<?php
session_start();
if (!isset($_SESSION['adminid'])) header("Location: ../index.php");

$conn = new mysqli("localhost", "root", "", "edu_db");

if (isset($_GET['approve'])) {
    $requestid = $_GET['approve'];
    $stmt = $conn->prepare("SELECT studentid, new_password FROM reset_requests WHERE requestid = ?");
    $stmt->bind_param("i", $requestid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $conn->query("UPDATE students SET password = '{$row['new_password']}' WHERE studentid = '{$row['studentid']}'");
    $conn->query("UPDATE reset_requests SET status = 'approved' WHERE requestid = $requestid");
    header("Location: module5.php");
}

if (isset($_GET['reject'])) {
    $conn->query("UPDATE reset_requests SET status = 'rejected' WHERE requestid = {$_GET['reject']}");
    header("Location: module5.php");
}

$result = $conn->query("SELECT * FROM reset_requests WHERE status = 'pending'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Requests</title>
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
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module3.php"><i class="fas fa-users me-2"></i> Manage Students</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module4.php"><i class="fas fa-chart-pie me-2"></i> Analytics</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in active" href="module5.php"><i class="fas fa-key me-2"></i> Reset Requests</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2 fade-in"><i class="fas fa-key me-2"></i> Password Reset Requests</h1>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-cog me-2"></i> Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="module3.php"><i class="fas fa-users me-2"></i> Manage Students</a></li>
                        </ul>
                    </div>
                </div>

                <div class="card neumorphic text-dark fade-in">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-clock me-2"></i> Pending Requests</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-primary">
                                    <tr>
                                        <th><i class="fas fa-hashtag me-1"></i> Request ID</th>
                                        <th><i class="fas fa-user-graduate me-1"></i> Student ID</th>
                                        <th><i class="fas fa-lock me-1"></i> New Password</th>
                                        <th><i class="fas fa-cogs me-1"></i> Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['requestid']; ?></td>
                                        <td><?php echo $row['studentid']; ?></td>
                                        <td><?php echo $row['new_password']; ?></td>
                                        <td>
                                            <a href="?approve=<?php echo $row['requestid']; ?>" class="btn btn-sm btn-success me-2">
                                                <i class="fas fa-check me-1"></i> Approve
                                            </a>
                                            <a href="?reject=<?php echo $row['requestid']; ?>" class="btn btn-sm btn-danger">
                                                <i class="fas fa-times me-1"></i> Reject
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if ($result->num_rows == 0): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No pending reset requests</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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