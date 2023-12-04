<nav class="bg-primary d-flex p-3 justify-content-between text-light">
    <span data-bs-toggle="offcanvas" href="#sidebar" aria-controls="sidebar"><i class="fa-solid fa-bars"></i></span>
    <div><?= getenv('APP_NAME') ?></div>
</nav>

<div class="offcanvas offcanvas-start bg-dark text-light" data-bs-scroll="true" data-bs-backdrop="true" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sidebarLabel"><?= getenv('APP_NAME') ?></h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <?php
        if (isset($_SESSION['user'])) {
            if ($_SESSION['user']['role'] === 'admin') {
        ?>
                <a class="side-link" href="./index.php">Dashboard</a>
                <a class="side-link" href="./users.php">All Users</a>
                <a class="side-link" href="./../auth/logout.php">Logout</a>
            <?php
            }
        } else {
            ?>
            <a class="side-link" href="/<?php echo getenv('APP_URI') ? getenv('APP_URI') . "/" : ''  . "index.php" ?>">Home</a>
            <a class="side-link" href="/<?php echo getenv('APP_URI') ? getenv('APP_URI') . "/" : '' . "auth/login.php" ?>">Login</a>
            <a class="side-link" href="/<?php echo getenv('APP_URI') ? getenv('APP_URI') . "/" : '' . "auth/register.php" ?>">Register</a>
        <?php
        }
        ?>
    </div>
</div>