<?php
require './../layouts/essentials.php';
require './../../middleware/student.php';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Old Attendances | <?= getenv('APP_NAME') ?></title>
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
                        <th scope="col">SUBJECT</th>
                        <th scope="col">TEACHER</th>
                        <th scope="col">DATE</th>
                        <th scope="col">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $user_id = $_SESSION['user']['id'];
                    $date = date('Y-m-d');
                    $database = new Database();
                    $conn = $database->connect();
                    $select_result = $conn->query("SELECT attendances.*, users.name AS student_name,subjects.name AS subject_name,teachers.name AS teacher_name FROM attendances JOIN students ON attendances.student_id = students.id JOIN users ON students.student_id = users.id JOIN subjects on students.subject_id=subjects.id JOIN users AS teachers on subjects.teacher_id=teachers.id WHERE users.id=$user_id ORDER BY date DESC");
                    $attendances = $select_result->fetch_all(MYSQLI_ASSOC);
                    $conn->close();
                    $paginator = new Pagination($attendances);
                    $attendances = $paginator->paginate();
                    foreach ($attendances as $attendance) :
                    ?>
                        <tr>
                            <th scope="row"><?php echo $attendance['id'] ?></th>
                            <td><?php echo $attendance['student_name'] ?></td>
                            <td><?php echo $attendance['subject_name'] ?></td>
                            <td><?php echo $attendance['teacher_name'] ?></td>
                            <td><?php echo date('Y-m-d') ?></td>
                            <td>
                                <input class="form-check-input attendance-checkbox" type="checkbox" <?php echo $attendance ? ($attendance['status'] ? 'checked' : '') : '' ?> value="1">
                            </td>
                        </tr>
                    <?php
                    endforeach;
                    if (count($attendances) === 0) {
                    ?>
                        <tr>
                            <td colspan="6" class="text-center">No Records Found...</td>
                        </tr>
                    <?php
                    }
                    ?>

                </tbody>
            </table>
            <?= $paginator->links() ?>
        </div>
    </div>
    <?php include './../layouts/footer.php'; ?>
</body>

</html>

<?php unset($_SESSION['error']) ?>