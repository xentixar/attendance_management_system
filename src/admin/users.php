<?php
require './../layouts/essentials.php';
require './../../middleware/admin.php';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>All Users | <?= getenv('APP_NAME') ?></title>
    <?php include './../layouts/header.php'; ?>
</head>

<body>
    <?php include './../layouts/navbar.php'; ?>
    <div class="content container py-5">
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">NAME</th>
                        <th scope="col">EMAIL</th>
                        <th scope="col">USERNAME</th>
                        <th scope="col">ROLE</th>
                        <th scope="col">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $database = new Database();
                    $conn = $database->connect();

                    $select_result = $conn->query("SELECT * FROM users WHERE role != 'admin' ORDER BY id DESC");
                    $users = $select_result->fetch_all(MYSQLI_ASSOC);

                    $conn->close();
                    $paginator = new Pagination($users);
                    $paginated_data = $paginator->paginate();
                    foreach ($paginated_data as $user) :
                    ?>
                        <tr>
                            <th scope="row"><?php echo $user['id'] ?></th>
                            <td><?php echo $user['name'] ?></td>
                            <td><?php echo $user['email'] ?></td>
                            <td><?php echo $user['username'] ?></td>
                            <td><span class="badge bg-primary"><?php echo $user['role'] ?></span></td>
                            <td>
                                <a href="user.php?action=edit&id=<?= $user['id'] ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-pencil"></i></a>
                                <a onclick="return confirm('Are you sure you want to delete this user?')" href="user.php?action=delete&id=<?= $user['id'] ?>" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php
                    endforeach;
                    if (count($paginated_data) === 0) {
                    ?>
                        <tr>
                            <td colspan="6" class="text-center">No Users Found...</td>
                        </tr>
                    <?php
                    }
                    ?>

                </tbody>
            </table>
        </div>
        <?= $paginator->links() ?>
    </div>
    <?php include './../layouts/footer.php'; ?>
</body>

</html>

<?php unset($_SESSION['error']) ?>