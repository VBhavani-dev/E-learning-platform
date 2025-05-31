<?php
session_start();
if (!isset($_SESSION['adminid'])) {
    header("Location: ../index.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "elearn_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Search functionality
$searchTerm = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '';

// Filter by content type
$contentType = isset($_GET['content_type']) && in_array($_GET['content_type'], ['all', 'videos', 'documents', 'external_links']) 
    ? $_GET['content_type'] 
    : 'all';

// Valid tables
$tables = ['youtube_videos', 'documents', 'external_links'];

// Initialize queries and counts
$queries = [];
$counts = ['youtube_videos' => 0, 'documents' => 0, 'external_links' => 0];

// Build queries based on content type
$whereClause = !empty($searchTerm) 
    ? " WHERE (course LIKE '%$searchTerm%' OR regulation LIKE '%$searchTerm%' OR department LIKE '%$searchTerm%')" 
    : "";

if ($contentType == 'all') {
    foreach ($tables as $table) {
        $query = "SELECT *, '$table' as source FROM $table $whereClause";
        $result = $conn->query($query);
        if ($result) {
            $queries[$table] = $result;
            $countQuery = "SELECT COUNT(*) as count FROM $table $whereClause";
            $countResult = $conn->query($countQuery);
            $counts[$table] = $countResult ? $countResult->fetch_assoc()['count'] : 0;
        } else {
            $counts[$table] = 0;
        }
    }
} else {
    $table = $contentType == 'videos' ? 'youtube_videos' : ($contentType == 'documents' ? 'documents' : 'external_links');
    $query = "SELECT *, '$table' as source FROM $table $whereClause";
    $result = $conn->query($query);
    if ($result) {
        $queries[$table] = $result;
        $countQuery = "SELECT COUNT(*) as count FROM $table $whereClause";
        $countResult = $conn->query($countQuery);
        $counts[$table] = $countResult ? $countResult->fetch_assoc()['count'] : 0;
    }
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
    <link rel="stylesheet" href="../css/styl.css">
    <style>
        .content-type-tabs {
            margin-bottom: 20px;
            border-radius: 8px;
            overflow: hidden;
        }
        .content-type-tabs .nav-link {
            border-radius: 0;
            padding: 10px 15px;
            font-weight: 500;
            color: #adb5bd;
        }
        .content-type-tabs .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }
        .content-badge {
            font-size: 0.8rem;
            margin-left: 5px;
            padding: 3px 8px;
        }
        .section-title {
            margin-top: 30px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #495057;
        }
        .youtube-preview {
            width: 160px;
            height: 90px;
            border: none;
            border-radius: 4px;
        }
        .file-icon {
            font-size: 1.2rem;
            margin-right: 5px;
        }
        .search-container {
            margin-bottom: 20px;
        }
        .search-input {
            background-color: whitesmoke;
            border: 1px solid white;
            color: #fff;
        }
        .search-input:focus {
            background-color: #eff0f0;
            border-color: #0d6efd;
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .search-btn {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .search-clear {
            background-color: #6c757d;
            border-color: #6c757d;
        }
    </style>
</head>
<body class="bg-dark text-white">
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar" id="sidebar">
                <div class="position-sticky pt-3">
                    <h4 class="text-center pulse">
                        <i class="fas fa-user-shield me-2"></i>
                        <a href="dashboard.php" style="border-color: #fff; color: #fff; text-decoration: none;">Admin Panel</a>
                    </h4>
                    <button class="btn btn-outline-light d-md-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-expanded="false">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="collapse d-md-block" id="sidebarMenu">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link text-white slide-in active" href="module1.php">
                                    <i class="fas fa-book-open me-2"></i> Materials & Videos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white slide-in" href="module2.php">
                                    <i class="fas fa-plus-circle me-2"></i> Add Material
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white slide-in" href="module3.php">
                                    <i class="fas fa-users me-2"></i> Manage Students
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white slide-in" href="module4.php">
                                    <i class="fas fa-chart-pie me-2"></i> Analytics
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white slide-in" href="module5.php">
                                    <i class="fas fa-key me-2"></i> Reset Requests
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white slide-in" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
                <h2 class="mb-4 fade-in"><i class="fas fa-book-open me-2"></i> Materials & Videos</h2>
                
                <!-- Search Bar -->
                <div class="search-container fade-in">
                    <form method="GET" action="" class="d-flex">
                        <input type="hidden" name="content_type" value="<?php echo htmlspecialchars($contentType); ?>">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control search-input" placeholder="Search by course, regulation, or department..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                            <button type="submit" class="btn search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                            <?php if (!empty($searchTerm)): ?>
                                <a href="?content_type=<?php echo htmlspecialchars($contentType); ?>" class="btn search-clear">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                
                <!-- Search Results Information -->
                <?php if (!empty($searchTerm)): ?>
                    <div class="alert alert-info fade-in">
                        <i class="fas fa-info-circle me-2"></i> 
                        Search results for: <strong><?php echo htmlspecialchars($searchTerm); ?></strong> 
                        (Found: <?php echo $totalCount; ?> items)
                    </div>
                <?php endif; ?>
                
                <!-- Content type tabs -->
                <ul class="nav nav-pills content-type-tabs">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $contentType == 'all' ? 'active' : ''; ?>" href="?content_type=all<?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>">
                            <i class="fas fa-th-large me-1"></i> All
                            <span class="badge bg-secondary content-badge"><?php echo $totalCount; ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $contentType == 'videos' ? 'active' : ''; ?>" href="?content_type=videos<?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>">
                            <i class="fas fa-video me-1"></i> Videos
                            <span class="badge bg-danger content-badge"><?php echo $counts['youtube_videos']; ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $contentType == 'documents' ? 'active' : ''; ?>" href="?content_type=documents<?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>">
                            <i class="fas fa-file-alt me-1"></i> Documents
                            <span class="badge bg-primary content-badge"><?php echo $counts['documents']; ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $contentType == 'external_links' ? 'active' : ''; ?>" href="?content_type=external_links<?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>">
                            <i class="fas fa-link me-1"></i> External Links
                            <span class="badge bg-success content-badge"><?php echo $counts['external_links']; ?></span>
                        </a>
                    </li>
                </ul>
                
                <?php if ($contentType == 'all'): ?>
                    <!-- Display all content types in separate sections -->
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
                            <div class="table-responsive">
                                <table class="table table-dark table-hover slide-in">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Course</th>
                                            <th>Regulation</th>
                                            <th>Department</th>
                                            <th><?php echo $table == 'youtube_videos' ? 'Preview' : ($table == 'documents' ? 'File' : 'Link'); ?></th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $queries[$table]->fetch_assoc()): ?>
                                            <tr class="pulse-hover">
                                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                                <td><?php echo htmlspecialchars($row['course']); ?></td>
                                                <td><?php echo htmlspecialchars($row['regulation']); ?></td>
                                                <td><?php echo htmlspecialchars($row['department']); ?></td>
                                                <td>
                                                    <?php if ($table == 'youtube_videos'): ?>
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
                                                        <iframe src="<?php echo htmlspecialchars($youtube_url); ?>" class="youtube-preview"></iframe>
                                                    <?php elseif ($table == 'documents'): ?>
                                                        <?php
                                                        $file_path = $row['file_path'];
                                                        $file_ext = pathinfo($file_path, PATHINFO_EXTENSION);
                                                        $file_name = basename($file_path);
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
                                                        <i class="fas fa-file-<?php echo $icon_class; ?> file-icon text-primary"></i>
                                                        <?php echo htmlspecialchars($file_name); ?>
                                                    <?php else: ?>
                                                        <i class="fas fa-link file-icon text-success"></i>
                                                        <?php echo htmlspecialchars(basename($row['link'])); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="<?php echo $table == 'documents' ? htmlspecialchars($row['file_path']) : htmlspecialchars($row['link']); ?>" target="_blank" class="btn btn-info btn-sm">
                                                            <i class="fas fa-external-link-alt"></i>
                                                        </a>
                                                        <a href="?delete=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&content_type=<?php echo htmlspecialchars($contentType); ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>" class="btn btn-danger btn-sm rotate-hover" onclick="return confirm('Are you sure you want to delete this item?');">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    
                    <?php if ($totalCount == 0): ?>
                        <div class="alert alert-dark fade-in">
                            <?php if (!empty($searchTerm)): ?>
                                No materials found matching your search.
                            <?php else: ?>
                                No materials found.
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Single content type display -->
                    <?php $table = $contentType == 'videos' ? 'youtube_videos' : ($contentType == 'documents' ? 'documents' : 'external_links'); ?>
                    <?php if (isset($queries[$table]) && $queries[$table]->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover slide-in">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Course</th>
                                        <th>Regulation</th>
                                        <th>Department</th>
                                        <th><?php echo $contentType == 'videos' ? 'Preview' : ($contentType == 'documents' ? 'File' : 'Link'); ?></th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $queries[$table]->fetch_assoc()): ?>
                                        <tr class="pulse-hover">
                                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['course']); ?></td>
                                            <td><?php echo htmlspecialchars($row['regulation']); ?></td>
                                            <td><?php echo htmlspecialchars($row['department']); ?></td>
                                            <td>
                                                <?php if ($table == 'youtube_videos'): ?>
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
                                                    <iframe src="<?php echo htmlspecialchars($youtube_url); ?>" class="youtube-preview"></iframe>
                                                <?php elseif ($table == 'documents'): ?>
                                                    <?php
                                                    $file_path = $row['file_path'];
                                                    $file_ext = pathinfo($file_path, PATHINFO_EXTENSION);
                                                    $file_name = basename($file_path);
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
                                                    <i class="fas fa-file-<?php echo $icon_class; ?> file-icon text-primary"></i>
                                                    <?php echo htmlspecialchars($file_name); ?>
                                                <?php else: ?>
                                                    <i class="fas fa-link file-icon text-success"></i>
                                                    <?php echo htmlspecialchars(basename($row['link'])); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?php echo $table == 'documents' ? htmlspecialchars($row['file_path']) : htmlspecialchars($row['link']); ?>" target="_blank" class="btn btn-info btn-sm">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                    <a href="?delete=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&content_type=<?php echo htmlspecialchars($contentType); ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>" class="btn btn-danger btn-sm rotate-hover" onclick="return confirm('Are you sure you want to delete this item?');">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-dark fade-in">
                            <?php if (!empty($searchTerm)): ?>
                                No materials found matching your search.
                            <?php else: ?>
                                No materials found in this category.
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <a href="dashboard.php" class="btn btn-outline-light mt-3 bounce">
                    <i class="fas fa-arrow-left me-2"></i> Back
                </a>
            </main>
        </div>
    </div>
    
    <?php
    if (isset($_GET['delete']) && isset($_GET['table'])) {
        $id = (int)$_GET['delete'];
        $table = $conn->real_escape_string($_GET['table']);
        
        if (in_array($table, $tables)) {
            $conn->query("DELETE FROM $table WHERE id = $id");
        }
        
        $redirect = "module1.php";
        $params = [];
        
        if (isset($_GET['content_type']) && in_array($_GET['content_type'], ['all', 'videos', 'documents', 'external_links'])) {
            $params[] = "content_type=" . urlencode($_GET['content_type']);
        }
        
        if (!empty($searchTerm)) {
            $params[] = "search=" . urlencode($searchTerm);
        }
        
        if (!empty($params)) {
            $redirect .= "?" . implode("&", $params);
        }
        
        header("Location: $redirect");
        exit;
    }
    $conn->close();
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/script.js"></script>
</body>
</html>