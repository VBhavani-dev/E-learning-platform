<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $conn = new mysqli("localhost", "root", "", "edu_db");
    if ($conn->connect_error) die("Connection failed");

    if ($role == "admin") {
        $stmt = $conn->prepare("SELECT * FROM admins WHERE adminid = ? AND password = ?");
        $stmt->bind_param("ss", $id, $password);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $_SESSION['adminid'] = $id;
            header("Location: admin/dashboard.php");
        } else {
            echo '<div class="alert alert-danger mt-3 mx-4">Invalid admin credentials</div>';
        }
    } else {
        $stmt = $conn->prepare("SELECT * FROM students WHERE studentid = ? AND password = ? AND added_by_admin = TRUE");
        $stmt->bind_param("ss", $id, $password);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $_SESSION['studentid'] = $id;
            header("Location: student/dashboard.php");
        } else {
            echo '<div class="alert alert-danger mt-3 mx-4">Invalid student credentials or not approved</div>';
        }
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
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
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
            <div class="col-md-5 col-lg-4">
                <div class="card neumorphic shadow-lg">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4"><i class="fas fa-sign-in-alt me-2"></i> Login</h2>
                        <form action="index.php" method="POST">
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="id" class="form-control" placeholder="AdminID/StudentID" required>
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
                                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                    <select name="role" class="form-select">
                                        <option value="admin">Admin</option>
                                        <option value="student">Student</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-arrow-right me-2"></i> Login
                            </button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="student/register.php" class="btn btn-link text-info">Register</a> | 
                            <a href="student/reset.php" class="btn btn-link text-warning">Reset Password</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>