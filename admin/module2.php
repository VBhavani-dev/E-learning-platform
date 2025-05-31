<?php
session_start();
if (!isset($_SESSION['adminid'])) header("Location: ../index.php");

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = new mysqli("localhost", "root", "", "edu_db");
    
    // Handle file upload for documents
    $file_path = '';
    if ($_POST['type'] == 'material' && isset($_FILES['material_file']) && $_FILES['material_file']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/materials/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = basename($_FILES['material_file']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt', 'zip'];
        
        if (in_array($file_ext, $allowed_ext)) {
            $new_file_name = uniqid() . '.' . $file_ext;
            $target_path = $upload_dir . $new_file_name;
            
            if (move_uploaded_file($_FILES['material_file']['tmp_name'], $target_path)) {
                $file_path = $target_path;
            } else {
                $error_message = "Failed to upload file.";
            }
        } else {
            $error_message = "Invalid file type. Allowed types: PDF, DOC, DOCX, PPT, PPTX, TXT, ZIP";
        }
    }
    
    // Validate URL for YouTube or external links
    $link = '';
    if (in_array($_POST['type'], ['youtube', 'link']) && !empty($_POST['link'])) {
        $link = filter_var($_POST['link'], FILTER_VALIDATE_URL);
        if ($link === false) {
            $error_message = "Invalid URL provided.";
        } elseif ($_POST['type'] == 'youtube') {
            // Basic YouTube URL validation
            if (!preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $link)) {
                $error_message = "Invalid YouTube URL.";
            }
        }
    }
    
    // Proceed with database insertion if no errors
    if (empty($error_message)) {
        $course = $_POST['course'];
        $regulation = $_POST['regulation'];
        $department = $_POST['department'];
        
        if ($_POST['type'] == 'material' && !empty($file_path)) {
            // Insert into documents table
            $stmt = $conn->prepare("INSERT INTO documents (course, regulation, department, file_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $course, $regulation, $department, $file_path);
        } elseif ($_POST['type'] == 'youtube' && !empty($link)) {
            // Insert into youtube_videos table
            $stmt = $conn->prepare("INSERT INTO youtube_videos (course, regulation, department, link) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $course, $regulation, $department, $link);
        } elseif ($_POST['type'] == 'link' && !empty($link)) {
            // Insert into external_links table
            $stmt = $conn->prepare("INSERT INTO external_links (course, regulation, department, link) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $course, $regulation, $department, $link);
        } else {
            $error_message = "Missing required data for the selected material type.";
        }
        
        if (empty($error_message) && $stmt->execute()) {
            $success_message = "Material added successfully!";
        } elseif (empty($error_message)) {
            $error_message = "Error adding material: " . $conn->error;
        }
        
        if (isset($stmt)) {
            $stmt->close();
        }
    }
    
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Material/Video</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styl.css">
    <style>
        .file-upload-container {
            display: none;
        }
        .link-upload-container {
            display: none;
        }
        .active-upload {
            display: block;
        }
    </style>
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
                            <li class="nav-item"><a class="nav-link text-white slide-in active" href="module2.php"><i class="fas fa-plus-circle me-2"></i> Add Material</a></li>
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
                    <h1 class="h2 fade-in"><i class="fas fa-plus-circle me-2"></i> Add Material/Video</h1>
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
                
                <div class="card neumorphic text-dark fade-in">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="course" class="form-label">Course</label>
                                <input type="text" class="form-control" id="course" name="course" required>
                            </div>
                            <div class="mb-3">
                                <label for="regulation" class="form-label">Regulation</label>
                                <input type="text" class="form-control" id="regulation" name="regulation" required>
                            </div>
                            <div class="mb-3">
                                <label for="department" class="form-label">Department</label>
                                <input type="text" class="form-control" id="department" name="department" required>
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-select" id="type" name="type" required onchange="toggleUploadFields()">
                                    <option value="">Select Material Type</option>
                                    <option value="material">Document (PDF, DOC, PPT, etc.)</option>
                                    <option value="youtube">YouTube Video</option>
                                    <option value="link">External Link</option>
                                </select>
                            </div>
                            
                            <!-- File Upload Container -->
                            <div id="fileUploadContainer" class="file-upload-container mb-3">
                                <label for="material_file" class="form-label">Upload File</label>
                                <input type="file" class="form-control" id="material_file" name="material_file" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.zip" required>
                                <small class="text-muted">Allowed formats: PDF, DOC, DOCX, PPT, PPTX, TXT, ZIP (Max 25MB)</small>
                            </div>
                            
                            <!-- YouTube/Link Container -->
                            <div id="linkUploadContainer" class="link-upload-container mb-3">
                                <label for="link" class="form-label">URL</label>
                                <input type="url" class="form-control" id="link" name="link" placeholder="Enter YouTube URL or external link" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary"><i class="fas fa-plus me-2"></i> Add Material</button>
                        </form>
                    </div>
                </div>
                <a href="module1.php" class="btn btn-outline-light mt-3 bounce"><i class="fas fa-arrow-left me-2"></i> Back to Materials</a>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/script.js"></script>
    <script>
        function toggleUploadFields() {
            const type = document.getElementById('type').value;
            const fileUploadContainer = document.getElementById('fileUploadContainer');
            const linkUploadContainer = document.getElementById('linkUploadContainer');
            
            // Hide both containers first
            fileUploadContainer.classList.remove('active-upload');
            linkUploadContainer.classList.remove('active-upload');
            
            // Show appropriate container based on selection
            if (type === 'material') {
                fileUploadContainer.classList.add('active-upload');
                document.getElementById('material_file').setAttribute('required', 'required');
                document.getElementById('link').removeAttribute('required');
            } else if (type === 'youtube' || type === 'link') {
                linkUploadContainer.classList.add('active-upload');
                document.getElementById('link').setAttribute('required', 'required');
                document.getElementById('material_file').removeAttribute('required');
            } else {
                document.getElementById('material_file').removeAttribute('required');
                document.getElementById('link').removeAttribute('required');
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleUploadFields();
        });
    </script>
</body>
</html>