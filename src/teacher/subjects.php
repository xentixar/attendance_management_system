<?php
require './../layouts/essentials.php';
require './../../middleware/teacher.php';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Subjects | <?= getenv('APP_NAME') ?></title>
    <?php include './../layouts/header.php'; ?>
</head>

<body>
    <?php include './../layouts/navbar.php'; ?>
    <div class="content container py-5">
        <div class="mb-2 text-end">
            <a href="subject.php?action=create" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus"></i> Add Subject</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">NAME</th>
                        <th scope="col">SEMESTER</th>
                        <th scope="col">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $database = new Database();
                    $conn = $database->connect();
                    $user_id = $_SESSION['user']['id'];
                    $select_result = $conn->query("SELECT subjects.*, users.name AS teacher_name, semesters.name AS semester_name FROM subjects JOIN users ON subjects.teacher_id = users.id JOIN semesters ON subjects.semester_id = semesters.id WHERE subjects.teacher_id = $user_id ORDER BY subjects.id DESC");
                    $subjects = $select_result->fetch_all(MYSQLI_ASSOC);

                    $conn->close();
                    $paginator = new Pagination($subjects);
                    $paginated_data = $paginator->paginate();
                    foreach ($paginated_data as $subject) :
                    ?>
                        <tr>
                            <th scope="row"><?php echo $subject['id'] ?></th>
                            <td><?php echo $subject['name'] ?></td>
                            <td><?php echo $subject['semester_name'] ?></td>
                            <td>
                                <a href="attendance.php?subject=<?= $subject['id'] ?>" class="btn btn-sm btn-success"><i class="fa-solid fa-clipboard-user"></i></a>
                                <a href="old-attendance.php?subject=<?= $subject['id'] ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-folder"></i></a>
                            </td>
                        </tr>
                    <?php
                    endforeach;
                    if (count($paginated_data) === 0) {
                    ?>
                        <tr>
                            <td colspan="6" class="text-center">No Subjects Found...</td>
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