<?php
require './../layouts/essentials.php';
require './../../middleware/admin.php';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];

    if ($action && $id && is_numeric($id)) {
        $database = new Database();
        $conn = $database->connect();
        if ($action === 'delete') {
            $delete_query = $conn->query("DELETE FROM students WHERE id=$id");
            if ($delete_query) {
                echo header('Location:students.php?msg=deleted');
            }
        } elseif ($action === 'edit') {
            $select_result = $conn->query("SELECT * FROM students WHERE id=$id");
            $student = $select_result->fetch_assoc();
            if (!$student) {
                echo header('Location:students.php');
            }
        } else {
            echo header('Location:students.php');
        }
    } else {
        echo header('Location:students.php');
    }
} else {
    $student['name'] = "";
    $student['semester_id'] = "";
    $student['teacher_id'] = "";
    $action = 'create';
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manage Student | <?= getenv('APP_NAME') ?></title>
    <?php include './../layouts/header.php'; ?>
</head>

<body>
    <?php include './../layouts/navbar.php'; ?>
    <div class="content container py-5">
        <?php
        if (isset($_POST['save']) && isset($_POST['student']) && isset($_POST['subject'])) {
            $data = $_POST;
            $validator = new Validation($data);
            $validated = $validator->validate([
                'student' => 'required|number|exists:users,id',
                'subject' => 'required|number|exists:subjects,id'
            ]);

            if ($validated) {
                $student = $data['student'];
                $subject = $data['subject'];

                $database = new Database();
                $conn = $database->connect();

                if ($action === "edit") {
                    $update_query = "UPDATE students SET subject_id=?,subject_id=? WHERE id=$id";
                    $stmt = $conn->prepare($update_query);
                } else {
                    $select_query = "SELECT * FROM students WHERE subject_id=? AND student_id=?";
                    $stmt = $conn->prepare($select_query);
                    $stmt->bind_param("ii", $subject, $student);
                    $result = $stmt->execute();
                    if ($result) {
                        $result = $stmt->get_result();
                        $student = $result->fetch_assoc();
                        print_r($student);
                        if ($student) {
                            $_SESSION['error']['student'] = "Selected student already exists in the subject.";
                            $stmt->close();
                            $conn->close();
                            echo header('Location:./student.php?action=create');
                        } else {
                            $insert_query = "INSERT INTO students(student_id,subject_id) VALUES(?,?)";
                            $stmt = $conn->prepare($insert_query);
                        }
                    } else {
                        echo "Error:" . $stmt->error;
                    }
                }
                if ($stmt) {
                    $stmt->bind_param("ii", $student, $subject);
                    $result = $stmt->execute();
                    if ($result) {
                        $stmt->close();
                        $conn->close();
                        echo header('Location:./students.php?msg=update_success');
                    } else {
                        echo "Error:" . $stmt->error;
                    }
                } else {
                    echo "Error:" . $conn->error;
                }
                $stmt->close();
                $conn->close();
            }
        }
        $database = new Database();
        $conn = $database->connect();

        $students_result = $conn->query("SELECT * FROM users WHERE role='student'");
        $students = $students_result->fetch_all(MYSQLI_ASSOC);

        $subjects_result = $conn->query("SELECT * FROM subjects");
        $subjects = $subjects_result->fetch_all(MYSQLI_ASSOC);
        ?>
        <form action="#" method="post">
            <div class="form-group mb-3">
                <label for="subject" class="form-label">Subject <b>*</b></label>
                <select name="subject" id="subject" class="form-control">
                    <option value="">Select a subject</option>
                    <?php
                    foreach ($subjects as $subject) :
                    ?>
                        <option value="<?= $subject['id'] ?>"><?= $subject['name'] ?></option>
                    <?php
                    endforeach
                    ?>
                </select>
                <small class="text-danger"><?php echo isset($_SESSION['error']['subject']) ? $_SESSION['error']['subject'] ?? '' : '' ?></small>
                <script>
                    const subject = "<?= $student['subject_id'] ?>"
                    document.getElementById('subject').value = `${subject}`
                </script>
            </div>
            <div class="form-group mb-3">
                <label for="student" class="form-label">Student <b>*</b></label>
                <select name="student" id="student" class="form-control">
                    <option value="">Select a student</option>
                    <?php
                    foreach ($students as $std) :
                    ?>
                        <option value="<?= $std['id'] ?>"><?= $std['name'] ?></option>
                    <?php
                    endforeach
                    ?>
                </select>
                <small class="text-danger"><?php echo isset($_SESSION['error']['student']) ? $_SESSION['error']['student'] ?? '' : '' ?></small>
                <script>
                    const student = "<?= $student['student_id'] ?>"
                    document.getElementById('student').value = `${student}`
                </script>
            </div>
            <button type="submit" name="save" class="btn btn-success">Save student</button>
        </form>
    </div>
    <?php include './../layouts/footer.php'; ?>
</body>

</html>

<?php unset($_SESSION['error']) ?>