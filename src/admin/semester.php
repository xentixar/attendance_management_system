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
            $delete_query = $conn->query("DELETE FROM semesters WHERE id=$id");
            if ($delete_query) {
                echo header('Location:semesters.php?msg=deleted');
            }
        } elseif ($action === 'edit') {
            $select_result = $conn->query("SELECT * FROM semesters WHERE id=$id");
            $semester = $select_result->fetch_assoc();
            if (!$semester) {
                echo header('Location:semesters.php');
            }
        } else {
            echo header('Location:semesters.php');
        }
    } else {
        echo header('Location:semesters.php');
    }
} else {
    $semester['name'] = "";
    $action = 'create';
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manage Semester | <?= getenv('APP_NAME') ?></title>
    <?php include './../layouts/header.php'; ?>
</head>

<body>
    <?php include './../layouts/navbar.php'; ?>
    <div class="content container py-5">
        <?php
        if (isset($_POST['save']) && isset($_POST['name'])) {
            $data = $_POST;
            $validator = new Validation($data);
            $validated = $validator->validate([
                'name' => 'required|max:100',
            ]);

            if ($validated) {
                $name = $data['name'];

                $database = new Database();
                $conn = $database->connect();

                if ($action === "edit") {
                    $update_query = "UPDATE semesters SET name=? WHERE id=$id";
                    $stmt = $conn->prepare($update_query);
                } else {
                    $insert_query = "INSERT INTO semesters(name) VALUES(?)";
                    $stmt = $conn->prepare($insert_query);
                }
                if ($stmt) {
                    $stmt->bind_param("s", $name);
                    $result = $stmt->execute();
                    if ($result) {
                        $stmt->close();
                        $conn->close();
                        echo header('Location:./semesters.php?msg=update_success');
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
        ?>
        <form action="#" method="post">
            <div class="form-group mb-3">
                <label for="name" class="form-label">Name <b>*</b></label>
                <input type="text" class="form-control" value="<?= $semester['name'] ?>" name="name" id="name" placeholder="Enter the name...">
                <small class="text-danger"><?php echo isset($_SESSION['error']['name']) ? $_SESSION['error']['name'] ?? '' : '' ?></small>
            </div>
            <button type="submit" name="save" class="btn btn-success">Save Semester</button>
        </form>
    </div>
    <?php include './../layouts/footer.php'; ?>
</body>

</html>

<?php unset($_SESSION['error']) ?>