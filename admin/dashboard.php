<?php
session_start();
if (!isset($_SESSION['adminid'])) header("Location: ../index.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styl.css">
</head>
<body class="bg-dark text-white">
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar" id="sidebar">
                <div class="position-sticky pt-3">
                    <h4 class="text-center pulse"><i class="fas fa-user-shield me-2"></i> Admin Panel</h4>
                    <button class="btn btn-outline-light d-md-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-expanded="false">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="collapse d-md-block" id="sidebarMenu">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module1.php"><i class="fas fa-video me-2"></i> Materials & Videos</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module2.php"><i class="fas fa-plus-circle me-2"></i> Add Material</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module3.php"><i class="fas fa-users me-2"></i> Manage Students</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module4.php"><i class="fas fa-chart-pie me-2"></i> Analytics</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module5.php"><i class="fas fa-key me-2"></i> Reset Requests</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2 fade-in"><i class="fas fa-tachometer-alt me-2"></i> Welcome, <?php echo $_SESSION['adminid']; ?></h1>
                </div>
                <div class="row g-4">
                    <div class="col-md-4"><div class="card neumorphic text-dark fade-in"><a href="module1.php" style="text-decoration :none;"><div class="card-body"><h5><i class="fas fa-video"></i> Materials</h5><p>Manage your resources</p></div></a></div></div>
                    <div class="col-md-4"><div class="card neumorphic text-dark fade-in"><a href="module3.php" style="text-decoration :none;"><div class="card-body"><h5><i class="fas fa-users"></i> Students</h5><p>Control student access</p></div></a></div></div>
                    <div class="col-md-4"><div class="card neumorphic text-dark fade-in"><a href="module4.php" style="text-decoration :none;"><div class="card-body"><h5><i class="fas fa-chart-bar"></i> Analytics</h5><p>Track usage</p></div></div></a></div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/script.js"></script>
</body>
</html>