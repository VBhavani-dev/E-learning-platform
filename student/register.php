<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = new mysqli("localhost", "root", "", "edu_db");
    if ($conn->connect_error) die("Connection failed");
    
    // Check if student exists and is approved by admin
    $stmt = $conn->prepare("SELECT * FROM students WHERE studentid = ? AND added_by_admin = TRUE");
    $stmt->bind_param("s", $_POST['studentid']);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        $password = $_POST['password'];
        
        // Password validation rules
        $hasNumber = preg_match('/[0-9]/', $password);
        $hasSpecialChar = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);
        $isAtLeast7Chars = (strlen($password) >= 7);
        
        if (!$isAtLeast7Chars || !$hasNumber || !$hasSpecialChar) {
            echo '<div class="alert alert-danger mt-3 mx-4">Password must be at least 7 characters with at least 1 number and 1 special character.</div>';
        } else {
            // Update student record (without hashing)
            $stmt = $conn->prepare("UPDATE students SET email = ?, password = ?, department = ?, regulation = ? WHERE studentid = ?");
            $stmt->bind_param("sssss", $_POST['email'], $password, $_POST['department'], $_POST['regulation'], $_POST['studentid']);
            
            if ($stmt->execute()) {
                echo '<div class="alert alert-success mt-3 mx-4">Registration successful! Please log in.</div>';
            } else {
                echo '<div class="alert alert-danger mt-3 mx-4">Error updating record: ' . $conn->error . '</div>';
            }
        }
    } else {
        echo '<div class="alert alert-danger mt-3 mx-4">No student found with this ID or not approved by admin.</div>';
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
    <title>Student Register</title>
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
                <div class="card neumorphic shadow-lg">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4"><i class="fas fa-user-plus me-2"></i> Student Register</h2>
                        <form method="POST">
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" name="studentid" class="form-control" placeholder="Student ID" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    <input type="text" name="department" class="form-control" placeholder="Department" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                                    <input type="text" name="regulation" class="form-control" placeholder="Regulation" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-user-edit me-2"></i> Register
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
</body>
</html>