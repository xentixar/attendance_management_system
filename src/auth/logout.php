<?php
include './../layouts/essentials.php';
if (isset($_SESSION['user']['username'])) {
    unset($_SESSION['user']);
    echo header("Location:./login.php?msg=logout_success");
} else {
    echo header("Location:./login.php");
}
