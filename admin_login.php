<?php
session_start();
require_once "config.php";

// Get database connection using the singleton pattern
$conn = Database::getInstance()->getConnection();

$login_err = "";

// Limit login attempts
if (!isset($_SESSION['attempt_count'])) {
    $_SESSION['attempt_count'] = 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION['attempt_count'] < 5) {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $sql = "SELECT id, username, password FROM admin_users WHERE username = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        $param_username = $username;

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);

                if (mysqli_stmt_fetch($stmt)) {
                    if (password_verify($password, $hashed_password)) {
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;
                        $_SESSION['attempt_count'] = 0; // Reset attempts on successful login
                        header("location: admin_dashboard.php");
                        exit;
                    } else {
                        $login_err = "Invalid username or password.";
                        $_SESSION['attempt_count']++;
                    }
                }
            } else {
                $login_err = "Invalid username or password.";
                $_SESSION['attempt_count']++;
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
} elseif ($_SESSION['attempt_count'] >= 5) {
    $login_err = "Too many failed login attempts. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Girdonawah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #003D23;
            /* Base color */
            --primary-color-light: #005A36;
            /* Lighter shade of the base color */
        }

        body {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-color-light));
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 400px;
            width: 100%;
            transition: transform 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-10px);
        }

        .login-header {
            background: linear-gradient(to right, var(--primary-color), var(--primary-color-light));
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            margin: -30px -30px 30px;
        }

        .btn-login {
            background: var(--primary-color);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: var(--primary-color-light);
            transform: scale(1.05);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 100, 0, 0.25);
        }

        .form-label {
            color: var(--primary-color);
            font-weight: 600;
        }

        .bi {
            color: var(--primary-color);
            margin-right: 10px;
        }

        .alert-danger {
            background-color: #ffdddd;
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h2 class="mb-0">Admin Login</h2>
        </div>

        <?php if (!empty($login_err)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($login_err); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">
                    <i class="bi bi-person-fill"></i>Username
                </label>
                <input type="text" name="username" class="form-control" required
                    placeholder="Enter your username" aria-label="Username">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="bi bi-lock-fill"></i>Password
                </label>
                <input type="password" name="password" class="form-control" required
                    placeholder="Enter your password" aria-label="Password">
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-login btn-primary">
                    Login
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>