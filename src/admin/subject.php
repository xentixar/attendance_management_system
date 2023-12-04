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
            $delete_query = $conn->query("DELETE FROM subjects WHERE id=$id");
            if ($delete_query) {
                echo header('Location:subjects.php?msg=deleted');
            }
        } elseif ($action === 'edit') {
            $select_result = $conn->query("SELECT * FROM subjects WHERE id=$id");
            $subject = $select_result->fetch_assoc();
            if (!$subject) {
                echo header('Location:subjects.php');
            }
        } else {
            echo header('Location:subjects.php');
        }
    } else {
        echo header('Location:subjects.php');
    }
} else {
    $subject['name'] = "";
    $subject['semester_id'] = "";
    $subject['teacher_id'] = "";
    $action = 'create';
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manage Subject | <?= getenv('APP_NAME') ?></title>
    <?php include './../layouts/header.php'; ?>
</head>

<body>
    <?php include './../layouts/navbar.php'; ?>
    <div class="content container py-5">
        <?php
        if (isset($_POST['save']) && isset($_POST['name']) && isset($_POST['semester']) && isset($_POST['teacher'])) {
            $data = $_POST;
            $validator = new Validation($data);
            $validated = $validator->validate([
                'name' => 'required|max:100',
                'semester' => 'required|number|exists:semesters,id',
                'teacher' => 'required|number|exists:users,id'
            ]);

            if ($validated) {
                $name = $data['name'];
                $semester = $data['semester'];
                $teacher = $data['teacher'];

                $database = new Database();
                $conn = $database->connect();

                if ($action === "edit") {
                    $update_query = "UPDATE subjects SET name=?,semester_id=?,teacher_id=? WHERE id=$id";
                    $stmt = $conn->prepare($update_query);
                } else {
                    $insert_query = "INSERT INTO subjects(name,semester_id,teacher_id) VALUES(?,?,?)";
                    $stmt = $conn->prepare($insert_query);
                }
                if ($stmt) {
                    $stmt->bind_param("sii", $name, $semester, $teacher);
                    $result = $stmt->execute();
                    if ($result) {
                        $stmt->close();
                        $conn->close();
                        echo header('Location:./subjects.php?msg=update_success');
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

        $teachers_result = $conn->query("SELECT * FROM users WHERE role='teacher'");
        $teachers = $teachers_result->fetch_all(MYSQLI_ASSOC);

        $semesters_result = $conn->query("SELECT * FROM semesters");
        $semesters = $semesters_result->fetch_all(MYSQLI_ASSOC);
        ?>
        <form action="#" method="post">
            <div class="form-group mb-3">
                <label for="name" class="form-label">Name <b>*</b></label>
                <input type="text" class="form-control" value="<?= $subject['name'] ?>" name="name" id="name" placeholder="Enter the name...">
                <small class="text-danger"><?php echo isset($_SESSION['error']['name']) ? $_SESSION['error']['name'] ?? '' : '' ?></small>
            </div>
            <div class="form-group mb-3">
                <label for="semester" class="form-label">Semester <b>*</b></label>
                <select name="semester" id="semester" class="form-control">
                    <option value="">Select a semester</option>
                    <?php
                    foreach ($semesters as $semester) :
                    ?>
                        <option value="<?= $semester['id'] ?>"><?= $semester['name'] ?></option>
                    <?php
                    endforeach
                    ?>
                </select>
                <small class="text-danger"><?php echo isset($_SESSION['error']['semester']) ? $_SESSION['error']['semester'] ?? '' : '' ?></small>
                <script>
                    const semester = "<?= $subject['semester_id'] ?>"
                    document.getElementById('semester').value = `${semester}`
                </script>
            </div>
            <div class="form-group mb-3">
                <label for="teacher" class="form-label">Teacher <b>*</b></label>
                <select name="teacher" id="teacher" class="form-control">
                    <option value="">Select a teacher</option>
                    <?php
                    foreach ($teachers as $teacher) :
                    ?>
                        <option value="<?= $teacher['id'] ?>"><?= $teacher['name'] ?></option>
                    <?php
                    endforeach
                    ?>
                </select>
                <small class="text-danger"><?php echo isset($_SESSION['error']['teacher']) ? $_SESSION['error']['teacher'] ?? '' : '' ?></small>
                <script>
                    const teacher = "<?= $subject['teacher_id'] ?>"
                    document.getElementById('teacher').value = `${teacher}`
                </script>
            </div>
            <button type="submit" name="save" class="btn btn-success">Save subject</button>
        </form>
    </div>
    <?php include './../layouts/footer.php'; ?>
</body>

</html>

<?php unset($_SESSION['error']) ?>