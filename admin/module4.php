<?php
session_start();
if (!isset($_SESSION['adminid'])) header("Location: ../index.php");

$conn = new mysqli("localhost", "root", "", "edu_db");

// Query to fetch analytics across all three tables
$query = "
    SELECT y.id, y.course, 'youtube_videos' AS source, y.link, COUNT(v.id) AS view_count
    FROM youtube_videos y
    LEFT JOIN views v ON y.id = v.material_id AND v.table_name = 'youtube_videos'
    GROUP BY y.id
    UNION ALL
    SELECT d.id, d.course, 'documents' AS source, d.file_path AS link, COUNT(v.id) AS view_count
    FROM documents d
    LEFT JOIN views v ON d.id = v.material_id AND v.table_name = 'documents'
    GROUP BY d.id
    UNION ALL
    SELECT e.id, e.course, 'external_links' AS source, e.link, COUNT(v.id) AS view_count
    FROM external_links e
    LEFT JOIN views v ON e.id = v.material_id AND v.table_name = 'external_links'
    GROUP BY e.id
";

$result = $conn->query($query);

// Calculate total materials and views
$total_materials = $result->num_rows;
$total_views = 0;
$result->data_seek(0); // Reset pointer
while ($row = $result->fetch_assoc()) {
    $total_views += $row['view_count'];
}
$result->data_seek(0); // Reset pointer again
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styl.css">
</head>
<body class="bg-dark text-white">
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar" id="sidebar">
                <div class="position-sticky pt-3">
                    <h4 class="text-center pulse"><i class="fas fa-user-shield me-2"></i><a href="dashboard.php" style="border-color: #fff; color: #fff; text-decoration: none;"> Admin Panel</a></h4>
                    <button class="btn btn-outline-light d-md-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-expanded="false">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="collapse d-md-block" id="sidebarMenu">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module1.php"><i class="fas fa-book-open me-2"></i> Materials & Videos</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module2.php"><i class="fas fa-plus-circle me-2"></i> Add Material</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module3.php"><i class="fas fa-users me-2"></i> Manage Students</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in active" href="module4.php"><i class="fas fa-chart-pie me-2"></i> Analytics</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module5.php"><i class="fas fa-key me-2"></i> Reset Requests</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2 fade-in"><i class="fas fa-chart-pie me-2"></i> Analytics Dashboard</h1>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-2"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i> Excel</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2"></i> PDF</a></li>
                        </ul>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card neumorphic text-dark fade-in">
                            <div class="card-body text-center">
                                <h5><i class="fas fa-file-alt me-2"></i> Total Materials</h5>
                                <h3 class="text-primary"><?php echo $total_materials; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card neumorphic text-dark fade-in">
                            <div class="card-body text-center">
                                <h5><i class="fas fa-eye me-2"></i> Total Views</h5>
                                <h3 class="text-success"><?php echo $total_views; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card neumorphic text-dark fade-in">
                            <div class="card-body text-center">
                                <h5><i class="fas fa-star me-2"></i> Avg. Views</h5>
                                <h3 class="text-warning">
                                    <?php echo $total_materials > 0 ? round($total_views / $total_materials, 1) : 0; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card neumorphic text-dark fade-in">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-table me-2"></i> Material Analytics</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-primary">
                                    <tr>
                                        <th><i class="fas fa-hashtag me-1"></i> ID</th>
                                        <th><i class="fas fa-book me-1"></i> Course</th>
                                        <th><i class="fas fa-tag me-1"></i> Type</th>
                                        <th><i class="fas fa-link me-1"></i> Link</th>
                                        <th><i class="fas fa-eye me-1"></i> Views</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['course']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php
                                                echo $row['source'] === 'youtube_videos' ? 'danger' : 
                                                     ($row['source'] === 'documents' ? 'primary' : 'success');
                                            ?>">
                                                <?php 
                                                echo $row['source'] === 'youtube_videos' ? 'YouTube' : 
                                                     ($row['source'] === 'documents' ? 'Document' : 'External Link');
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo htmlspecialchars($row['link']); ?>" target="_blank" class="text-truncate d-inline-block" style="max-width: 150px;">
                                                <?php echo htmlspecialchars($row['link']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: <?php echo min(100, ($row['view_count'] / max(1, $total_views)) * 100); ?>%" 
                                                     aria-valuenow="<?php echo $row['view_count']; ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="<?php echo $total_views; ?>">
                                                    <?php echo $row['view_count']; ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <a href="dashboard.php" class="btn btn-outline-light mt-3 bounce"><i class="fas fa-arrow-left me-2"></i> Back to Dashboard</a>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/script.js"></script>
</body>
</html>
<?php $conn->close(); ?>