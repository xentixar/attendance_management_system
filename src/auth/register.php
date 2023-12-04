<?php
require './../layouts/essentials.php';
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
    <title>Register | <?= getenv('APP_NAME') ?></title>
    <?php include './../layouts/header.php'; ?>
</head>

<body>
    <?php include './../layouts/navbar.php'; ?>
    <div class="content container my-5 py-4">
        <div class="login-form">
            <h3 class="text-danger text-center">Register your account</h3>
            <hr>
            <?php
            if (isset($_POST['register']) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password'])) {
                $data = $_POST;
                $validator = new Validation($data);
                $validated = $validator->validate([
                    'name' => 'required|max:100',
                    'email' => 'required|email|unique:users,email',
                    'username' => 'required|alpha_dash|min:5|unique:users,username',
                    'password' => 'required|min:8|max:30'
                ]);

                if ($validated) {
                    $name = $data['name'];
                    $email = $data['email'];
                    $username = $data['username'];
                    $password = $data['password'];

                    $password = password_hash($password, PASSWORD_BCRYPT);

                    $database = new Database();
                    $conn = $database->connect();

                    $insert_query = "INSERT INTO users(name,email,username,password) VALUES(?,?,?,?)";
                    $stmt = $conn->prepare($insert_query);
                    if ($stmt) {
                        $stmt->bind_param("ssss", $name, $email, $username, $password);
                        $insert_result = $stmt->execute();
                        if ($insert_result) {
                            $stmt->close();
                            $conn->close();
                            echo header('Location:./login.php?rdc=register.php&status=1');
                        } else {
                            echo "Error:" . $stmt->error;
                        }
                    } else {
                        echo "Error:" . $conn->error;
                    }
                    $stmt->close();
                    $conn->close();
                }
            }

            ?>
            <form action="#" method="post">
                <div class="form-group mb-3">
                    <label for="name" class="form-label">Name <b>*</b></label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter your name...">
                    <small class="text-danger"><?php echo isset($_SESSION['error']['name']) ? $_SESSION['error']['name'] ?? '' : '' ?></small>
                </div>
                <div class="form-group mb-3">
                    <label for="email" class="form-label">Email <b>*</b></label>
                    <input type="text" class="form-control" name="email" id="email" placeholder="Enter your email...">
                    <small class="text-danger"><?php echo isset($_SESSION['error']['email']) ? $_SESSION['error']['email'] ?? '' : '' ?></small>
                </div>
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
                <button type="submit" name="register" class="btn btn-success">Register</button>
            </form>
            <div class="text-center my-3">
                <strong>Already have an account? </strong><a href="./login.php" class="text-decoration-none">Login</a>
            </div>
        </div>
    </div>
    <?php include './../layouts/footer.php'; ?>
</body>

</html>

<?php unset($_SESSION['error']) ?>