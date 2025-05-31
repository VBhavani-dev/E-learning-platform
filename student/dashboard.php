<?php
session_start();
if (!isset($_SESSION['studentid'])) header("Location: ../index.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-gradient">
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar text-white" id="sidebar">
                <div class="position-sticky pt-3">
                    <h4 class="text-center pulse"><i class="fas fa-user-graduate me-2"></i> Student Panel</h4>
                    <button class="btn btn-outline-light d-md-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-expanded="false">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="collapse d-md-block" id="sidebarMenu">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module1.php"><i class="fas fa-video me-2"></i> Materials & Videos</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module2.php"><i class="fas fa-eye me-2"></i> Viewed Courses</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module3.php"><i class="fas fa-bookmark me-2"></i> Bookmarks</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module4.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2 fade-in text-dark"><i class="fas fa-tachometer-alt me-2"></i> Welcome, <?php echo $_SESSION['studentid']; ?></h1>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card neumorphic text-dark fade-in">
                            <div class="card-body">
                                <h5><i class="fas fa-video me-2"></i> Materials</h5>
                                <p>Access your course resources</p>
                                <a href="module1.php" class="btn btn-primary btn-sm bounce"><i class="fas fa-arrow-right me-1"></i> Go</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card neumorphic text-dark fade-in">
                            <div class="card-body">
                                <h5><i class="fas fa-eye me-2"></i> Viewed Courses</h5>
                                <p>Track what you’ve seen</p>
                                <a href="module2.php" class="btn btn-primary btn-sm bounce"><i class="fas fa-arrow-right me-1"></i> Go</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card neumorphic text-dark fade-in">
                            <div class="card-body">
                                <h5><i class="fas fa-bookmark me-2"></i> Bookmarks</h5>
                                <p>Your favorite materials</p>
                                <a href="module3.php" class="btn btn-primary btn-sm bounce"><i class="fas fa-arrow-right me-1"></i> Go</a>
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