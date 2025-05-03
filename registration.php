<?php
    session_start();
    if (isset($_SESSION["user"])) {
        header("Location: index.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php
            if (isset($_POST["submit"])) {
                $fullname = trim(htmlspecialchars($_POST["fullname"]));
                $email = trim(htmlspecialchars($_POST["email"]));
                $password = $_POST["password"];
                $passwordRepeat = $_POST["repeat_Password"];

                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $errors = array();
                if (empty($fullname) OR empty($email) OR empty($password) OR empty($passwordRepeat)) {
                    array_push($errors, "All fields are required");
                }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    array_push($errors, "Email is not valid");
                }
                if (strlen($password)<8) {
                    array_push($errors, "Password must be at least 8 characters long");
                }
                if ($password !== $passwordRepeat) {
                    array_push($errors, "Password does not match");
                }

                require_once "database.php";
                $sql = "SELECT * FROM users WHERE email = '$email'";
                $result = mysqli_query($conn, $sql);
                $rowCount = mysqli_num_rows($result);
                if ($rowCount>0) {
                    array_push($errors, "Email already exists!");
                }

                if (count($errors)>0) {
                    foreach ($errors as $error) {
                        echo "<div class='alert alert-danger'>$error</div>";
                    }
                } else {
                    $sql = "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)";
                    $stmt = mysqli_stmt_init($conn);
                    $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                    if ($prepareStmt) {
                        mysqli_stmt_bind_param($stmt, "sss", $fullname, $email, $passwordHash);
                        mysqli_stmt_execute($stmt);
                        $_SESSION["user"] = $email;
                        $_SESSION["user_name"] = $fullname;
                        header("Location: index.php");
                        exit();
                    } else {
                        die("Something went wrong");
                    }
                }
            }
        ?>
        <form action="registration.php" method="POST">
            <div class="form-group">
                <input type="text" name="fullname" placeholder="Full name:" class="form-control">
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email:" class="form-control">
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password:" class="form-control">
            </div>
            <div class="form-group">
                <input type="password" name="repeat_Password" placeholder="Repeat Password:" class="form-control">
            </div>
            <div class="form-btn">
                <input type="submit" name="submit" value="Register" class="btn btn-primary">
            </div>
        </form>
        <div>
            <p>Already Registered <a href="login.php">Login Here</a></p>
        </div>
    </div>
</body>
</html>