<?php
session_start();
if (!isset($_SESSION['studentid'])) header("Location: ../index.php");

$conn = new mysqli("localhost", "root", "", "edu_db");
$studentid = $_SESSION['studentid'];

// Handle bookmark removal
if (isset($_GET['remove']) && isset($_GET['table'])) {
    $material_id = (int)$_GET['remove'];
    $table_name = $conn->real_escape_string($_GET['table']);
    if (in_array($table_name, ['youtube_videos', 'documents', 'external_links'])) {
        $stmt = $conn->prepare("DELETE FROM bookmarks WHERE studentid = ? AND material_id = ? AND table_name = ?");
        $stmt->bind_param("sis", $studentid, $material_id, $table_name);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: module3.php");
    exit();
}

// Get bookmarks with material details (one per course per content type)
$stmt = $conn->prepare("
    SELECT m.*, b.table_name
    FROM (
        SELECT id, course, link, created_at, 'youtube_videos' AS table_name FROM youtube_videos
        UNION ALL
        SELECT id, course, file_path AS link, created_at, 'documents' AS table_name FROM documents
        UNION ALL
        SELECT id, course, link, created_at, 'external_links' AS table_name FROM external_links
    ) m
    JOIN (
        SELECT b.material_id, b.table_name, MIN(b.id) AS min_id
        FROM bookmarks b
        JOIN (
            SELECT id, course, 'youtube_videos' AS table_name FROM youtube_videos
            UNION ALL
            SELECT id, course, 'documents' AS table_name FROM documents
            UNION ALL
            SELECT id, course, 'external_links' AS table_name FROM external_links
        ) m ON b.material_id = m.id AND b.table_name = m.table_name
        WHERE b.studentid = ?
        GROUP BY m.course, b.table_name
    ) b ON m.id = b.material_id AND m.table_name = b.table_name
    ORDER BY m.course
");
$stmt->bind_param("s", $studentid);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookmarks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .material-card {
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
        }
        .material-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .material-icon {
            font-size: 2rem;
            margin-bottom: 15px;
        }
        .youtube-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            margin-bottom: 15px;
        }
        .youtube-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 8px;
        }
        .file-preview {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            text-align: center;
        }
        .no-bookmarks {
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
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
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module2.php"><i class="fas fa-eye me-2"></i> Viewed Courses</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in active" href="module3.php"><i class="fas fa-bookmark me-2"></i> Bookmarks</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module4.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
                <h2 class="mb-4 fade-in text-dark"><i class="fas fa-bookmark me-2"></i> Bookmarks</h2>
                
                <?php if ($result->num_rows == 0): ?>
                    <div class="card no-bookmarks">
                        <div class="card-body text-center">
                            <i class="fas fa-bookmark fa-4x text-muted mb-3"></i>
                            <h4>No bookmarks found</h4>
                            <p class="text-muted">You haven't bookmarked any materials yet.</p>
                            <a href="module1.php" class="btn btn-primary">Browse Materials</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card material-card fade-in">
                                    <div class="card-body">
                                        <div class="text-center">
                                            <?php if ($row['table_name'] == 'youtube_videos'): ?>
                                                <i class="fab fa-youtube material-icon text-danger"></i>
                                            <?php elseif ($row['table_name'] == 'documents'): ?>
                                                <?php
                                                $file_ext = pathinfo($row['link'], PATHINFO_EXTENSION);
                                                $icon_class = 'alt';
                                                if ($file_ext == 'pdf') {
                                                    $icon_class = 'pdf';
                                                } elseif (in_array($file_ext, ['doc', 'docx'])) {
                                                    $icon_class = 'word';
                                                } elseif (in_array($file_ext, ['ppt', 'pptx'])) {
                                                    $icon_class = 'powerpoint';
                                                } elseif (in_array($file_ext, ['xls', 'xlsx'])) {
                                                    $icon_class = 'excel';
                                                }
                                                ?>
                                                <i class="fas fa-file-<?php echo $icon_class; ?> material-icon text-primary"></i>
                                            <?php else: ?>
                                                <i class="fas fa-link material-icon text-success"></i>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <h5 class="card-title"><?php echo htmlspecialchars($row['course']); ?></h5>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <?php
                                                if ($row['table_name'] == 'youtube_videos') {
                                                    echo 'YouTube';
                                                } elseif ($row['table_name'] == 'documents') {
                                                    echo 'Document';
                                                } else {
                                                    echo 'External Link';
                                                }
                                                ?> • 
                                                <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                            </small>
                                        </p>
                                        
                                        <?php if ($row['table_name'] == 'youtube_videos'): ?>
                                            <div class="youtube-container">
                                                <?php 
                                                $youtube_url = $row['link'];
                                                if (strpos($youtube_url, 'embed') === false) {
                                                    $video_id = '';
                                                    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $youtube_url, $matches)) {
                                                        $video_id = $matches[1];
                                                    }
                                                    $youtube_url = "https://www.youtube.com/embed/$video_id";
                                                }
                                                ?>
                                                <iframe src="<?php echo htmlspecialchars($youtube_url); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                            </div>
                                        <?php elseif ($row['table_name'] == 'documents'): ?>
                                            <div class="file-preview">
                                                <p class="mb-2"><?php echo htmlspecialchars(basename($row['link'])); ?></p>
                                            </div>
                                        <?php else: ?>
                                            <div class="file-preview">
                                                <p class="mb-2"><?php echo htmlspecialchars($row['link']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="d-flex justify-content-between">
                                            <?php if ($row['table_name'] == 'youtube_videos'): ?>
                                                <a href="<?php echo htmlspecialchars($row['link']); ?>" target="_blank" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-external-link-alt me-1"></i> Watch
                                                </a>
                                            <?php elseif ($row['table_name'] == 'documents'): ?>
                                                <a href="module1.php?download=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-download me-1"></i> Download
                                                </a>
                                            <?php else: ?>
                                                <a href="<?php echo htmlspecialchars($row['link']); ?>" target="_blank" class="btn btn-success btn-sm">
                                                    <i class="fas fa-external-link-alt me-1"></i> Visit
                                                </a>
                                            <?php endif; ?>
                                            <a href="?remove=<?php echo $row['id']; ?>&table=<?php echo $row['table_name']; ?>" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash-alt me-1"></i> Remove
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
                
                <a href="dashboard.php" class="btn btn-outline-dark mt-3 bounce"><i class="fas fa-arrow-left me-2"></i> Back</a>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/script.js"></script>
</body>
</html>
<?php $conn->close(); ?>