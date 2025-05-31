<?php
session_start();
if (!isset($_SESSION['studentid'])) header("Location: ../index.php");

$conn = new mysqli("localhost", "root", "", "edu_db");
$studentid = $_SESSION['studentid'];
$student = $conn->query("SELECT department, regulation FROM students WHERE studentid = '$studentid'")->fetch_assoc();

// Handle view tracking
if (isset($_GET['view']) && isset($_GET['table'])) {
    $material_id = (int)$_GET['view'];
    $table_name = $conn->real_escape_string($_GET['table']);
    if (in_array($table_name, ['youtube_videos', 'documents', 'external_links'])) {
        $stmt = $conn->prepare("INSERT INTO views (material_id, table_name, studentid) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $material_id, $table_name, $studentid);
        $stmt->execute();
        $stmt->close();
    }
}

// Handle file download
if (isset($_GET['download'])) {
    $material_id = (int)$_GET['download'];
    $file_query = $conn->query("SELECT file_path FROM documents WHERE id = $material_id");
    if ($file_query->num_rows > 0) {
        $file = $file_query->fetch_assoc();
        $file_path = $file['file_path'];
        
        // Track the view
        $stmt = $conn->prepare("INSERT INTO views (material_id, table_name, studentid) VALUES (?, 'documents', ?)");
        $stmt->bind_param("is", $material_id, $studentid);
        $stmt->execute();
        $stmt->close();
        
        // Process the download
        if (file_exists($file_path)) {
            $file_name = basename($file_path);
            $file_size = filesize($file_path);
            $file_type = mime_content_type($file_path);
            
            header("Content-Type: $file_type");
            header("Content-Disposition: attachment; filename=\"$file_name\"");
            header("Content-Length: $file_size");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            readfile($file_path);
            exit;
        }
    }
}

// Handle bookmarking
if (isset($_GET['bookmark']) && isset($_GET['table'])) {
    $material_id = (int)$_GET['bookmark'];
    $table_name = $conn->real_escape_string($_GET['table']);
    if (in_array($table_name, ['youtube_videos', 'documents', 'external_links'])) {
        $stmt = $conn->prepare("INSERT INTO bookmarks (studentid, material_id, table_name) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $studentid, $material_id, $table_name);
        $stmt->execute();
        $stmt->close();
    }
}

// Build the base query for each table
$department = $conn->real_escape_string($student['department']);
$regulation = $conn->real_escape_string($student['regulation']);
$baseQuery = "WHERE department = '$department' AND regulation = '$regulation'";

// Add search condition if provided
$searchCondition = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $searchCondition = " AND course LIKE '%$search%'";
}

// Filter by content type if selected
$contentType = isset($_GET['content_type']) ? $_GET['content_type'] : 'all';
$queries = [];
$counts = ['youtube_videos' => 0, 'documents' => 0, 'external_links' => 0];

if ($contentType == 'all') {
    $tables = ['youtube_videos', 'documents', 'external_links'];
    foreach ($tables as $table) {
        $query = "SELECT *, '$table' AS source FROM $table $baseQuery $searchCondition";
        $queries[$table] = $conn->query($query);
        $counts[$table] = $queries[$table]->num_rows;
    }
} elseif ($contentType == 'videos') {
    $query = "SELECT *, 'youtube_videos' AS source FROM youtube_videos $baseQuery $searchCondition";
    $queries['youtube_videos'] = $conn->query($query);
    $counts['youtube_videos'] = $queries['youtube_videos']->num_rows;
} elseif ($contentType == 'documents') {
    $query = "SELECT *, 'documents' AS source FROM documents $baseQuery $searchCondition";
    $queries['documents'] = $conn->query($query);
    $counts['documents'] = $queries['documents']->num_rows;
} elseif ($contentType == 'external_links') {
    $query = "SELECT *, 'external_links' AS source FROM external_links $baseQuery $searchCondition";
    $queries['external_links'] = $conn->query($query);
    $counts['external_links'] = $queries['external_links']->num_rows;
}

$totalCount = $counts['youtube_videos'] + $counts['documents'] + $counts['external_links'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materials & Videos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .material-card { transition: transform 0.3s, box-shadow 0.3s; margin-bottom: 20px; }
        .material-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
        .material-icon { font-size: 2rem; margin-bottom: 15px; }
        .youtube-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; margin-bottom: 15px; }
        .youtube-container iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border-radius: 8px; }
        .file-preview { background: #f8f9fa; border-radius: 8px; padding: 15px; margin-bottom: 15px; text-align: center; }
        .content-type-tabs { margin-bottom: 20px; border-radius: 8px; overflow: hidden; }
        .content-type-tabs .nav-link { border-radius: 0; padding: 10px 15px; font-weight: 500; }
        .content-type-tabs .nav-link.active { background-color: #0d6efd; color: white; }
        .content-badge { font-size: 0.8rem; margin-left: 5px; padding: 3px 8px; }
        .section-title { margin-top: 30px; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #dee2e6; }
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
                            <li class="nav-item"><a class="nav-link text-white slide-in active" href="module1.php"><i class="fas fa-book-open me-2"></i> Materials & Videos</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module2.php"><i class="fas fa-eye me-2"></i> Viewed Courses</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module3.php"><i class="fas fa-bookmark me-2"></i> Bookmarks</a></li>
                            <li class="nav-item"><a class="nav-link text-white slide-in" href="module4.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
                <h2 class="mb-4 fade-in"><i class="fas fa-book-open me-2"></i> Materials & Videos</h2>
                
                <form method="GET" class="mb-4">
                    <div class="input-group neumorphic">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by course" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit" class="btn btn-primary bounce"><i class="fas fa-search"></i></button>
                    </div>
                    <?php if (isset($_GET['content_type'])): ?>
                        <input type="hidden" name="content_type" value="<?php echo htmlspecialchars($_GET['content_type']); ?>">
                    <?php endif; ?>
                </form>
                
                <!-- Content type tabs -->
                <ul class="nav nav-pills content-type-tabs">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $contentType == 'all' ? 'active' : ''; ?>" href="?<?php echo !empty($_GET['search']) ? 'search='.urlencode($_GET['search']).'&' : ''; ?>content_type=all">
                            <i class="fas fa-th-large me-1"></i> All
                            <span class="badge bg-secondary content-badge"><?php echo $totalCount; ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $contentType == 'videos' ? 'active' : ''; ?>" href="?<?php echo !empty($_GET['search']) ? 'search='.urlencode($_GET['search']).'&' : ''; ?>content_type=videos">
                            <i class="fas fa-video me-1"></i> Videos
                            <span class="badge bg-danger content-badge"><?php echo $counts['youtube_videos']; ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $contentType == 'documents' ? 'active' : ''; ?>" href="?<?php echo !empty($_GET['search']) ? 'search='.urlencode($_GET['search']).'&' : ''; ?>content_type=documents">
                            <i class="fas fa-file-alt me-1"></i> Documents
                            <span class="badge bg-primary content-badge"><?php echo $counts['documents']; ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $contentType == 'external_links' ? 'active' : ''; ?>" href="?<?php echo !empty($_GET['search']) ? 'search='.urlencode($_GET['search']).'&' : ''; ?>content_type=external_links">
                            <i class="fas fa-link me-1"></i> External Links
                            <span class="badge bg-success content-badge"><?php echo $counts['external_links']; ?></span>
                        </a>
                    </li>
                </ul>
                
                <!-- Content display -->
                <?php if ($totalCount == 0): ?>
                    <div class="alert alert-info fade-in">
                        No materials found for your department and regulation.
                    </div>
                <?php else: ?>
                    <?php if ($contentType == 'all'): ?>
                        <!-- Display all content types -->
                        <?php foreach (['youtube_videos' => 'Videos', 'documents' => 'Documents', 'external_links' => 'External Links'] as $table => $title): ?>
                            <?php if (isset($queries[$table]) && $queries[$table]->num_rows > 0): ?>
                                <div class="section-title">
                                    <h3>
                                        <i class="fas fa-<?php echo $table == 'youtube_videos' ? 'video' : ($table == 'documents' ? 'file-alt' : 'link'); ?> me-2"></i> 
                                        <?php echo $title; ?> 
                                        <span class="badge bg-<?php echo $table == 'youtube_videos' ? 'danger' : ($table == 'documents' ? 'primary' : 'success'); ?>">
                                            <?php echo $queries[$table]->num_rows; ?>
                                        </span>
                                    </h3>
                                </div>
                                <div class="row">
                                    <?php while ($row = $queries[$table]->fetch_assoc()): ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="card material-card fade-in">
                                                <div class="card-body">
                                                    <div class="text-center">
                                                        <?php if ($table == 'youtube_videos'): ?>
                                                            <i class="fab fa-youtube material-icon text-danger"></i>
                                                        <?php elseif ($table == 'documents'): ?>
                                                            <?php
                                                            $file_ext = pathinfo($row['file_path'], PATHINFO_EXTENSION);
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
                                                            <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                                            <?php if ($table == 'documents'): ?>
                                                                • <?php echo strtoupper($file_ext); ?>
                                                            <?php endif; ?>
                                                        </small>
                                                    </p>
                                                    <?php if ($table == 'youtube_videos'): ?>
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
                                                            <iframe src="<?php echo $youtube_url; ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                        </div>
                                                    <?php elseif ($table == 'documents'): ?>
                                                        <div class="file-preview">
                                                            <p class="mb-2"><?php echo htmlspecialchars(basename($row['file_path'])); ?></p>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="file-preview">
                                                            <p class="mb-2"><?php echo htmlspecialchars($row['link']); ?></p>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="d-flex justify-content-between">
                                                        <?php if ($table == 'youtube_videos'): ?>
                                                            <a href="<?php echo $row['link']; ?>" target="_blank" class="btn btn-danger btn-sm" onclick="window.location.href='?view=<?php echo $row['id']; ?>&table=youtube_videos<?php echo !empty($_GET['search']) ? '&search='.urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['content_type']) ? '&content_type='.urlencode($_GET['content_type']) : ''; ?>'; return false;">
                                                                <i class="fas fa-external-link-alt me-1"></i> Watch
                                                            </a>
                                                        <?php elseif ($table == 'documents'): ?>
                                                            <a href="?download=<?php echo $row['id']; ?><?php echo !empty($_GET['search']) ? '&search='.urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['content_type']) ? '&content_type='.urlencode($_GET['content_type']) : ''; ?>" class="btn btn-primary btn-sm">
                                                                <i class="fas fa-download me-1"></i> Download
                                                            </a>
                                                        <?php else: ?>
                                                            <a href="<?php echo $row['link']; ?>" target="_blank" class="btn btn-success btn-sm" onclick="window.location.href='?view=<?php echo $row['id']; ?>&table=external_links<?php echo !empty($_GET['search']) ? '&search='.urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['content_type']) ? '&content_type='.urlencode($_GET['content_type']) : ''; ?>'; return false;">
                                                                <i class="fas fa-external-link-alt me-1"></i> Visit
                                                            </a>
                                                        <?php endif; ?>
                                                        <a href="?bookmark=<?php echo $row['id']; ?>&table=<?php echo $table; ?><?php echo !empty($_GET['search']) ? '&search='.urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['content_type']) ? '&content_type='.urlencode($_GET['content_type']) : ''; ?>" class="btn btn-warning btn-sm">
                                                            <i class="fas fa-bookmark me-1"></i> Bookmark
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Single content type display -->
                        <?php $table = $contentType == 'videos' ? 'youtube_videos' : ($contentType == 'documents' ? 'documents' : 'external_links'); ?>
                        <?php if (isset($queries[$table]) && $queries[$table]->num_rows > 0): ?>
                            <div class="row">
                                <?php while ($row = $queries[$table]->fetch_assoc()): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card material-card fade-in">
                                            <div class="card-body">
                                                <div class="text-center">
                                                    <?php if ($table == 'youtube_videos'): ?>
                                                        <i class="fab fa-youtube material-icon text-danger"></i>
                                                    <?php elseif ($table == 'documents'): ?>
                                                        <?php
                                                        $file_ext = pathinfo($row['file_path'], PATHINFO_EXTENSION);
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
                                                        <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                                        <?php if ($table == 'documents'): ?>
                                                            • <?php echo strtoupper($file_ext); ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </p>
                                                <?php if ($table == 'youtube_videos'): ?>
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
                                                        <iframe src="<?php echo $youtube_url; ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                    </div>
                                                <?php elseif ($table == 'documents'): ?>
                                                    <div class="file-preview">
                                                        <p class="mb-2"><?php echo htmlspecialchars(basename($row['file_path'])); ?></p>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="file-preview">
                                                        <p class="mb-2"><?php echo htmlspecialchars($row['link']); ?></p>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="d-flex justify-content-between">
                                                    <?php if ($table == 'youtube_videos'): ?>
                                                        <a href="<?php echo $row['link']; ?>" target="_blank" class="btn btn-danger btn-sm" onclick="window.location.href='?view=<?php echo $row['id']; ?>&table=youtube_videos<?php echo !empty($_GET['search']) ? '&search='.urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['content_type']) ? '&content_type='.urlencode($_GET['content_type']) : ''; ?>'; return false;">
                                                            <i class="fas fa-external-link-alt me-1"></i> Watch
                                                        </a>
                                                    <?php elseif ($table == 'documents'): ?>
                                                        <a href="?download=<?php echo $row['id']; ?><?php echo !empty($_GET['search']) ? '&search='.urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['content_type']) ? '&content_type='.urlencode($_GET['content_type']) : ''; ?>" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-download me-1"></i> Download
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="<?php echo $row['link']; ?>" target="_blank" class="btn btn-success btn-sm" onclick="window.location.href='?view=<?php echo $row['id']; ?>&table=external_links<?php echo !empty($_GET['search']) ? '&search='.urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['content_type']) ? '&content_type='.urlencode($_GET['content_type']) : ''; ?>'; return false;">
                                                            <i class="fas fa-external-link-alt me-1"></i> Visit
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="?bookmark=<?php echo $row['id']; ?>&table=<?php echo $table; ?><?php echo !empty($_GET['search']) ? '&search='.urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['content_type']) ? '&content_type='.urlencode($_GET['content_type']) : ''; ?>" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-bookmark me-1"></i> Bookmark
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
                <!-- End of content display -->
                
                <a href="dashboard.php" class="btn btn-outline-dark mt-3 bounce"><i class="fas fa-arrow-left me-2"></i> Back</a>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/script.js"></script>
</body>
</html>
<?php $conn->close(); ?>