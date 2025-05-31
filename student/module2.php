<?php
session_start();
if (!isset($_SESSION['studentid'])) header("Location: ../index.php");

$conn = new mysqli("localhost", "root", "", "edu_db");
$studentid = $_SESSION['studentid'];

// Query to get unique courses with view count across all content types
$stmt = $conn->prepare("
    SELECT 
        m.id,
        m.course,
        m.table_name AS type,
        m.link,
        COUNT(v.id) AS view_count
    FROM (
        SELECT id, course, link, 'youtube_videos' AS table_name FROM youtube_videos
        UNION ALL
        SELECT id, course, file_path AS link, 'documents' AS table_name FROM documents
        UNION ALL
        SELECT id, course, link, 'external_links' AS table_name FROM external_links
    ) m
    JOIN views v ON m.id = v.material_id AND m.table_name = v.table_name
    WHERE v.studentid = ?
    GROUP BY m.id, m.course, m.table_name, m.link
");
$stmt->bind_param("s", $studentid);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Function to estimate completion percentage based on view count
function getEstimatedCompletion($viewCount) {
    // First view = 25%, second = 50%, third = 75%, fourth or more = 100%
    $completion = min(25 * $viewCount, 100);
    return $completion;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viewed Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-gradient">
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar text-white" id="sidebar">
                <div class="position-sticky pt-3">
                    <h4 class="text-center pulse"><i class="fas fa-user-graduate me-2"></i><a href="dashboard.php" style="border-color: #fff; color: #fff; text-decoration: none;"> Student Panel</a></h4>
                    <button class="btn btn-outline-light d-md-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-expanded="false">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="collapse d-md-block" id="sidebarMenu">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module1.php"><i class="fas fa-book-open me-2"></i> Materials & Videos</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in active" href="module2.php"><i class="fas fa-eye me-2"></i> Viewed Courses</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module3.php"><i class="fas fa-bookmark me-2"></i> Bookmarks</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module4.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
                <h2 class="mb-4 fade-in text-dark"><i class="fas fa-eye me-2"></i> Viewed Courses</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-hover slide-in">
                        <thead class="table-dark">
                            <tr>
                                <th>Course</th>
                                <th>Type</th>
                                <th>Completion</th>
                                <th>Views</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): 
                                // Calculate estimated completion based on view count
                                $completion = getEstimatedCompletion($row['view_count']);
                                
                                // Determine progress bar color based on completion percentage
                                $progressColor = "bg-danger";
                                if ($completion >= 75) {
                                    $progressColor = "bg-success";
                                } elseif ($completion >= 50) {
                                    $progressColor = "bg-info";
                                } elseif ($completion >= 25) {
                                    $progressColor = "bg-warning";
                                }
                            ?>
                                <tr class="pulse-hover">
                                    <td><?php echo htmlspecialchars($row['course']); ?></td>
                                    <td>
                                        <?php
                                        if ($row['type'] == 'youtube_videos') {
                                            echo 'YouTube';
                                        } elseif ($row['type'] == 'documents') {
                                            echo 'Document';
                                        } else {
                                            echo 'External Link';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated <?php echo $progressColor; ?>" 
                                                 role="progressbar" 
                                                 style="width: <?php echo $completion; ?>%" 
                                                 aria-valuenow="<?php echo $completion; ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                <?php echo $completion; ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-secondary"><?php echo $row['view_count']; ?></span></td>
                                    <td>
                                        <?php 
                                        if ($row['type'] == 'youtube_videos') {
                                            echo "<a href='" . htmlspecialchars($row['link']) . "' class='btn btn-sm btn-danger' target='_blank'>
                                                    <i class='fab fa-youtube me-1'></i> Watch
                                                  </a>";
                                        } elseif ($row['type'] == 'documents') {
                                            echo "<a href='module1.php?download=" . $row['id'] . "' class='btn btn-sm btn-primary'>
                                                    <i class='fas fa-download me-1'></i> Download
                                                  </a>";
                                        } else {
                                            echo "<a href='" . htmlspecialchars($row['link']) . "' class='btn btn-sm btn-success' target='_blank'>
                                                    <i class='fas fa-link me-1'></i> Visit
                                                  </a>";
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if ($result->num_rows == 0): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i> You haven't viewed any courses yet.
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <a href="dashboard.php" class="btn btn-outline-dark mt-3 bounce"><i class="fas fa-arrow-left me-2"></i> Back</a>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/script.js"></script>
</body>
</html>
<?php $conn->close(); ?>