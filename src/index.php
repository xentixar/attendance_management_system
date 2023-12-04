<?php include './layouts/essentials.php'; ?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Welcome | <?= getenv('APP_NAME') ?></title>
    <?php include './layouts/header.php'; ?>
</head>

<body>
    <?php include './layouts/navbar.php'; ?>
    <div class="content container my-5 py-5 text-center">
        <h2> Welcome to Attendance Management System</h2>
        <a href="./auth/login.php" class="btn btn-primary">Enter System</a>
    </div>
    <?php include './layouts/footer.php'; ?>
</body>

</html>