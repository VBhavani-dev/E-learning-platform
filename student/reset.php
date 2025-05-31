<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = new mysqli("localhost", "root", "", "edu_db");
    if ($conn->connect_error) die("Connection failed");
    
    $stmt = $conn->prepare("INSERT INTO reset_requests (studentid, new_password) VALUES (?, ?)");
    $stmt->bind_param("ss", $_POST['studentid'], $_POST['new_password']);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo '<div class="alert alert-success mt-3 mx-4">Reset request submitted successfully!</div>';
    } else {
        echo '<div class="alert alert-danger mt-3 mx-4">Failed to submit reset request. Please try again.</div>';
    }
    
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background: linear-gradient(45deg,#007BFF,aliceblue,#60B5FF,#AFDDFF,#3159D1);
            min-height: 100vh;
            background-size: 300% 300%;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 10px;
            transition: background-color 1.5s ease-in-out;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
        }
        .input-group-text {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card neumorphic shadow-lg fade-in">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4"><i class="fas fa-key me-2"></i> Reset Password</h2>
                        <form method="POST">
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" name="studentid" class="form-control" placeholder="Student ID" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="new_password" class="form-control" placeholder="New Password" required>
                                </div>
                                <div class="form-text">Your password will be updated after admin approval</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane me-2"></i> Submit Request
                            </button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="../index.php" class="btn btn-link text-info">
                                <i class="fas fa-sign-in-alt me-1"></i> Back to Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/script.js"></script>
</body>
</html>