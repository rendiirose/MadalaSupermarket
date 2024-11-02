<?php
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'login');
    if ($conn->connect_error) {
        die('Connection Failed : ' . $conn->connect_error);
    } else {
        if (strpos($_SERVER['HTTP_REFERER'], 'register.html') !== false) {
            // Check if the username already exists during registration
            $checkStmt = $conn->prepare("SELECT * FROM registration WHERE username = ?");
            $checkStmt->bind_param("s", $username);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) {
                echo "<script>alert('Username already exists. Please choose another.');</script>";
                echo "<script>window.location.href='register.html';</script>";
                exit();
            }

            $checkStmt->close();

            // Proceed with registration
            $email = $_POST['email']; // Adjusted to match the input name in register.html
            $number = $_POST['cellphone']; // Adjusted to match the input name in register.html
            
            $stmt = $conn->prepare("INSERT INTO registration (username, Email, Number, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $number, $password);

            try {
                if ($stmt->execute()) {
                    echo "<script>alert('Registration successful!');</script>";
                    echo "<script>window.location.href='loginPage.html';</script>";
                    exit();
                } else {
                    echo "Error: " . $stmt->error;
                }
            } catch (Exception $e) {
                echo "<script>alert('Registration failed. Please check your details.');</script>";
                echo "<script>window.location.href='register.html';</script>";
                exit();
            }

            $stmt->close();
        } else {
            // Check if the username and password match in the database during login
            $stmt = $conn->prepare("SELECT * FROM registration WHERE username = ? AND password = ?");
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Login successful, redirect to home page
                echo "<script>window.location.href='index.html';</script>";
                exit();
            } else {
                echo "<script>alert('Invalid username or password.');</script>";
                echo "<script>window.location.href='loginPage.html';</script>";
                exit();
            }

            $stmt->close();
        }

        $conn->close();
    }
}
?>
