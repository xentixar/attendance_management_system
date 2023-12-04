<?php
include './../layouts/essentials.php';
if (isset($_SESSION['user']['username'])) {
    $user_role = $_SESSION['user']['role'];
    echo header("Location:../$user_role/index.php");
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login | <?= getenv('APP_NAME') ?></title>
    <?php include './../layouts/header.php'; ?>
</head>

<body>
    <?php include './../layouts/navbar.php'; ?>
    <div class="content container my-5 py-5">
        <div class="login-form">
            <h3 class="text-danger text-center">Login to Continue</h3>
            <hr>
            <?php
            if (isset($_POST['login']) && isset($_POST['username']) && isset($_POST['password'])) {
                $data = $_POST;
                $validator = new Validation($data);
                $validated = $validator->validate([
                    'username' => 'required|alpha_dash|min:5',
                    'password' => 'required|min:8|max:30'
                ]);

                if ($validated) {
                    $username = $data['username'];
                    $password = $data['password'];

                    $database = new Database();
                    $conn = $database->connect();

                    $select_query = "SELECT * FROM users WHERE username=?";
                    $stmt = $conn->prepare($select_query);
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    $user = $result->fetch_assoc();

                    $stmt->close();
                    $conn->close();

                    if ($user && password_verify($password, $user['password'])) {
                        $_SESSION['user']['username'] = $username;
                        $_SESSION['user']['id'] = $user['id'];
                        $_SESSION['user']['role'] = $user['role'];
                        $user_role = $user['role'];
                        echo header("Location:../$user_role/index.php");
                    } else {
                        $_SESSION['error']['username'] = "You have entered incorrect credentials.";
                    }
                }
            }

            ?>
            <form action="#" method="post">
                <div class="form-group mb-3">
                    <label for="username" class="form-label">Username <b>*</b></label>
                    <input type="text" class="form-control" name="username" id="username" placeholder="Enter your username...">
                    <small class="text-danger"><?php echo isset($_SESSION['error']['username']) ? $_SESSION['error']['username'] ?? '' : '' ?></small>
                </div>
                <div class="form-group mb-3">
                    <label for="password" class="form-label">Password <b>*</b></label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password...">
                    <small class="text-danger"><?php echo isset($_SESSION['error']['password']) ? $_SESSION['error']['password'] ?? '' : '' ?></small>
                </div>
                <button type="submit" name="login" class="btn btn-success">Login</button>
            </form>
            <div class="text-center my-3">
                <strong>Don't have an account? </strong><a href="./register.php" class="text-decoration-none">Register</a>
            </div>
        </div>
    </div>
    <?php include './../layouts/footer.php'; ?>
</body>

</html>

<?php unset($_SESSION['error']) ?>