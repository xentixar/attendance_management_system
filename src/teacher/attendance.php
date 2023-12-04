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
    <title>Attendance | <?= getenv('APP_NAME') ?></title>
    <?php include './../layouts/header.php'; ?>
</head>

<body>
    <?php include './../layouts/navbar.php'; ?>
    <div class="content container py-5">
        <div class="mb-2 text-end">
            <label class="form-check-label" for="select_all">
                Select All
            </label>
            <input onchange="toggleCheck(this)" class="form-check-input" type="checkbox" value="1" id="select_all">
        </div>
        <div class="table-responsive">
            <?php
            if (isset($_POST['save']) && isset($_POST['attendance'])) {
                $attendances = $_POST['attendance'];
                $database = new Database();
                $conn = $database->connect();
                $subject_id = $subject['id'];
                $students_result = $conn->query("SELECT * FROM students WHERE subject_id = $subject_id");
                $students = $students_result->fetch_all(MYSQLI_ASSOC);

                foreach ($students as $student) {
                    $date = date('Y-m-d');
                    $student_id = $student['id'];
                    if (key_exists($student_id, $attendances)) {
                        $select_result = $conn->query("SELECT * FROM attendances WHERE student_id=$student_id AND date='$date'");
                        $attendance = $select_result->fetch_assoc();
                        $status = $attendances[$student_id];
                        if ($attendance) {
                            $update_result = $conn->query("UPDATE attendances SET status=1 WHERE student_id=$student_id AND date='$date'");
                        } else {
                            $insert_result = $conn->query("INSERT INTO attendances(student_id,date,status) VALUES($student_id,'$date',1)");
                        }
                    } else {
                        $update_result = $conn->query("UPDATE attendances SET status=0 WHERE student_id=$student_id AND date='$date'");
                    }
                }
                echo header('Refresh:3;url=attendance.php?subject=' . $subject_id);
            ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Updated Successfully!</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php
            } elseif (isset($_POST['save'])) {
                $subject_id = $subject['id'];
                $students_result = $conn->query("SELECT * FROM students WHERE subject_id = $subject_id");
                $students = $students_result->fetch_all(MYSQLI_ASSOC);

                foreach ($students as $student) {
                    $date = date('Y-m-d');
                    $student_id = $student['id'];
                    $update_result = $conn->query("UPDATE attendances SET status=0 WHERE student_id=$student_id AND date='$date'");
                }
                echo header('Refresh:3;url=attendance.php?subject=' . $subject_id);
            ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Updated Successfully!</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php
            }
            ?>
            <form action="#" method="post">
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
                        $select_result = $conn->query("SELECT students.*, users.name AS student_name, subjects.name AS subject_name, teachers.name AS teacher_name, semesters.name AS semester_name FROM students JOIN users ON students.student_id = users.id JOIN subjects ON students.subject_id = subjects.id JOIN users AS teachers ON subjects.teacher_id = teachers.id JOIN semesters ON subjects.semester_id = semesters.id WHERE subjects.id=$subject_id ORDER BY students.id DESC");
                        $students = $select_result->fetch_all(MYSQLI_ASSOC);
                        foreach ($students as $student) :
                            $student_id = $student['id'];
                            $date = date('Y-m-d');
                            $attendance_result = $conn->query("SELECT * FROM attendances WHERE student_id=$student_id AND date='$date'");
                            $attendance = $attendance_result->fetch_assoc();
                            $conn->close();

                        ?>
                            <tr>
                                <th scope="row"><?php echo $student['id'] ?></th>
                                <td><?php echo $student['student_name'] ?></td>
                                <td><?php echo date('Y-m-d') ?></td>
                                <td>
                                    <input class="form-check-input attendance-checkbox" type="checkbox" <?php echo $attendance ? ($attendance['status'] ? 'checked' : '') : '' ?> value="1" name="attendance[<?= $student['id'] ?>]">
                                </td>
                            </tr>
                        <?php
                        endforeach;
                        if (count($students) === 0) {
                        ?>
                            <tr>
                                <td colspan="6" class="text-center">No Students Found...</td>
                            </tr>
                        <?php
                        }
                        ?>

                    </tbody>
                </table>
                <button type="submit" name="save" class="btn btn-primary float-end">Save</button>
            </form>
        </div>
    </div>
    <?php include './../layouts/footer.php'; ?>
    <script>
        const toggleCheck = (e) => {
            const elements = document.querySelectorAll('.attendance-checkbox');
            elements.forEach(elem => {
                if (e.checked == true) {
                    elem.checked = true;
                } else {
                    elem.checked = false;
                }
            })
        }
    </script>
</body>

</html>

<?php unset($_SESSION['error']) ?>