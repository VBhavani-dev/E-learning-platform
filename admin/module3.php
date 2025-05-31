<?php
session_start();
if (!isset($_SESSION['adminid'])) header("Location: ../index.php");

$conn = new mysqli("localhost", "root", "", "edu_db");

// Handle form submissions
$success_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    // First check if student ID already exists
    $check_stmt = $conn->prepare("SELECT studentid FROM students WHERE studentid = ?");
    $check_stmt->bind_param("s", $_POST['studentid']);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows > 0) {
        $error_message = "Student ID already exists!";
        $check_stmt->close();
    } else {
        $check_stmt->close();
        // If doesn't exist, proceed with insertion
        $stmt = $conn->prepare("INSERT INTO students (studentid, department, regulation, year, added_by_admin) VALUES (?, ?, ?, ?, TRUE)");
        $stmt->bind_param("sssi", $_POST['studentid'], $_POST['department'], $_POST['regulation'], $_POST['year']);
        if ($stmt->execute()) {
            $success_message = "Student added successfully!";
        } else {
            $error_message = "Error adding student: " . $stmt->error;
        }
        $stmt->close();
    }
}

if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM students WHERE studentid = '{$_GET['delete']}'");
    header("Location: module3.php");
    exit();
}

$search_result = null;
if (isset($_POST['search'])) {
    $stmt = $conn->prepare("SELECT * FROM students WHERE studentid = ?");
    $stmt->bind_param("s", $_POST['searchid']);
    $stmt->execute();
    $search_result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styl.css">
</head>
<body class="bg-dark text-white">
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar" id="sidebar">
                <div class="position-sticky pt-3">
                    <h4 class="text-center pulse"><i class="fas fa-user-shield me-2"></i><a href="dashboard.php" style="border-color: #fff;
    color: #fff; text-decoration: none;"> Admin Panel</a></h4>
                    <button class="btn btn-outline-light d-md-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-expanded="false">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="collapse d-md-block" id="sidebarMenu">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module1.php"><i class="fas fa-video me-2"></i> Materials & Videos</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module2.php"><i class="fas fa-plus-circle me-2"></i> Add Material</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in active" href="module3.php"><i class="fas fa-users me-2"></i> Manage Students</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module4.php"><i class="fas fa-chart-pie me-2"></i> Analytics</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module5.php"><i class="fas fa-key me-2"></i> Reset Requests</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2 fade-in"><i class="fas fa-users me-2"></i> Manage Students</h1>
                </div>

                <?php if (!empty($success_message)): ?>
                <div class="alert alert-success fade-in">
                    <?php echo $success_message; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger fade-in">
                    <?php echo $error_message; ?>
                </div>
                <?php endif; ?>

                <div class="row g-4">
                    <!-- Add Student Card -->
                    <div class="col-md-6">
                        <div class="card neumorphic text-dark fade-in">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-user-plus me-2"></i> Add Student</h5>
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="studentid" class="form-label">Student ID</label>
                                        <input type="text" class="form-control" id="studentid" name="studentid" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="department" class="form-label">Department</label>
                                        <input type="text" class="form-control" id="department" name="department" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="regulation" class="form-label">Regulation</label>
                                        <input type="text" class="form-control" id="regulation" name="regulation" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="year" class="form-label">Year</label>
                                        <input type="number" class="form-control" id="year" name="year" required>
                                    </div>
                                    <button type="submit" name="add" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i> Add Student
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Search Student Card -->
                    <div class="col-md-6">
                        <div class="card neumorphic text-dark fade-in">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-search me-2"></i> Search Student</h5>
                                <form method="POST" class="mb-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="searchid" placeholder="Student ID" required>
                                        <button type="submit" name="search" class="btn btn-outline-primary">
                                            <i class="fas fa-search me-2"></i> Search
                                        </button>
                                    </div>
                                </form>
                                
                                <?php if ($search_result && $search_result->num_rows > 0): 
                                    $student = $search_result->fetch_assoc(); ?>
                                    <div class="alert alert-info">
                                        <h6>Student Found:</h6>
                                        <p class="mb-1"><strong>ID:</strong> <?php echo $student['studentid']; ?></p>
                                        <p class="mb-1"><strong>Department:</strong> <?php echo $student['department']; ?></p>
                                        <p class="mb-1"><strong>Regulation:</strong> <?php echo $student['regulation']; ?></p>
                                        <p class="mb-1"><strong>Year:</strong> <?php echo $student['year']; ?></p>
                                    </div>
                                <?php elseif (isset($_POST['search'])): ?>
                                    <div class="alert alert-warning">
                                        No student found with that ID
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Students List Card -->
                    <div class="col-12">
                        <div class="card neumorphic text-dark fade-in">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-list me-2"></i> Students List</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>ID</th>
                                                <th>Department</th>
                                                <th>Regulation</th>
                                                <th>Year</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $result = $conn->query("SELECT * FROM students");
                                            while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $row['studentid']; ?></td>
                                                    <td><?php echo $row['department']; ?></td>
                                                    <td><?php echo $row['regulation']; ?></td>
                                                    <td><?php echo $row['year']; ?></td>
                                                    <td>
                                                        <a href="?delete=<?php echo $row['studentid']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this student?')">
                                                            <i class="fas fa-trash-alt me-1"></i> Delete
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/script.js"></script>
</body>
</html>
<?php $conn->close(); ?>