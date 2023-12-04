<?php
require './../layouts/essentials.php';
require './../../middleware/admin.php';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>All Students | <?= getenv('APP_NAME') ?></title>
    <?php include './../layouts/header.php'; ?>
</head>

<body>
    <?php include './../layouts/navbar.php'; ?>
    <div class="content container py-5">
        <div class="mb-2 text-end">
            <a href="student.php?action=create" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus"></i> Add Student</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">NAME</th>
                        <th scope="col">SUBJECT</th>
                        <th scope="col">TEACHER</th>
                        <th scope="col">SEMESTER</th>
                        <th scope="col">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $database = new Database();
                    $conn = $database->connect();

                    $select_result = $conn->query("SELECT students.*, users.name AS student_name, subjects.name AS subject_name, teachers.name AS teacher_name, semesters.name AS semester_name FROM students JOIN users ON students.student_id = users.id JOIN subjects ON students.subject_id = subjects.id JOIN users AS teachers ON subjects.teacher_id = teachers.id JOIN semesters ON subjects.semester_id = semesters.id ORDER BY students.id DESC");
                    $students = $select_result->fetch_all(MYSQLI_ASSOC);

                    $conn->close();
                    $paginator = new Pagination($students);
                    $paginated_data = $paginator->paginate();
                    foreach ($paginated_data as $student) :
                    ?>
                        <tr>
                            <th scope="row"><?php echo $student['id'] ?></th>
                            <td><?php echo $student['student_name'] ?></td>
                            <td><?php echo $student['subject_name'] ?></td>
                            <td><?php echo $student['teacher_name'] ?></td>
                            <td><?php echo $student['semester_name'] ?></td>
                            <td>
                                <a href="student.php?action=edit&id=<?= $student['id'] ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-pencil"></i></a>
                                <a onclick="return confirm('Are you sure you want to delete this student?')" href="student.php?action=delete&id=<?= $student['id'] ?>" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php
                    endforeach;
                    if (count($paginated_data) === 0) {
                    ?>
                        <tr>
                            <td colspan="6" class="text-center">No students Found...</td>
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