<?php
require './../layouts/essentials.php';
require './../../middleware/admin.php';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard | <?= getenv('APP_NAME') ?></title>
    <?php include './../layouts/header.php'; ?>
</head>

<body>
    <?php include './../layouts/navbar.php'; ?>
    <div class="content container my-5 py-5">
    </div>
    <?php include './../layouts/footer.php'; ?>
</body>

</html>

<?php unset($_SESSION['error']) ?>