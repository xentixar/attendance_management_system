<?php
require './../layouts/essentials.php';
require './../../middleware/teacher.php';

if (isset($_GET['subject'])) {
    $subject = $_GET['subject'];
    if ($subject && is_numeric($subject)) {
        $database = new Database();
        $conn = $database->connect();

        $subject_result = $conn->query("SELECT * FROM subjects WHERE id=$subject");
        $subject = $subject_result->fetch_assoc();
    } else {
        echo header('Location:subjects.php');
    }
} else {
    echo header('Location:subjects.php');
}
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
                        <th scope="col">DATE</th>
                        <th scope="col">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $subject_id = $subject['id'];
                    $date = date('Y-m-d');
                    $select_result = $conn->query("SELECT attendances.*, users.name AS student_name FROM attendances JOIN users ON attendances.student_id = users.id WHERE date!='$date' ORDER BY date DESC");
                    $attendances = $select_result->fetch_all(MYSQLI_ASSOC);
                    $conn->close();
                    foreach ($attendances as $attendance) :
                    ?>
                        <tr>
                            <th scope="row"><?php echo $attendance['id'] ?></th>
                            <td><?php echo $attendance['student_name'] ?></td>
                            <td><?php echo date('Y-m-d') ?></td>
                            <td>
                                <input class="form-check-input attendance-checkbox" type="checkbox" <?php echo $attendance ? ($attendance['status'] ? 'checked' : '') : '' ?> value="1" name="attendance[<?= $student['id'] ?>]">
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
        </div>
    </div>
    <?php include './../layouts/footer.php'; ?>
</body>

</html>

<?php unset($_SESSION['error']) ?>