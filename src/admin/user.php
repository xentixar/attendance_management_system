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
            $delete_query = $conn->query("DELETE FROM users WHERE id=$id");
            if ($_SESSION['user']['id'] == $id) {
                echo header('Location:../auth/logout.php?msg=deleted');
            }
            if ($delete_query) {
                echo header('Location:users.php?msg=deleted');
            }
        } elseif ($action === 'edit') {
            $select_result = $conn->query("SELECT * FROM users WHERE id=$id");
            $user = $select_result->fetch_assoc();
            if (!$user) {
                echo header('Location:users.php');
            }
        } else {
            echo header('Location:users.php');
        }
    } else {
        echo header('Location:users.php');
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manage User | <?= getenv('APP_NAME') ?></title>
    <?php include './../layouts/header.php'; ?>
</head>

<body>
    <?php include './../layouts/navbar.php'; ?>
    <div class="content container py-5">
        <?php
        if (isset($_POST['save']) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['username']) && isset($_POST['role'])) {
            $data = $_POST;
            $validator = new Validation($data);
            $validated = $validator->validate([
                'name' => 'required|max:100',
                'email' => 'required|email|unique:users,email,' . $user['id'],
                'username' => 'required|alpha_dash|min:5|unique:users,username,' . $user['id'],
                'role' => 'required|in:admin,teacher,student'
            ]);

            if ($validated) {
                $name = $data['name'];
                $email = $data['email'];
                $username = $data['username'];
                $role = $data['role'];

                $database = new Database();
                $conn = $database->connect();

                $update_query = "UPDATE users SET name=?,email=?,username=?,role=? WHERE id=$id";
                $stmt = $conn->prepare($update_query);
                if ($stmt) {
                    $stmt->bind_param("ssss", $name, $email, $username, $role);
                    $insert_result = $stmt->execute();
                    if ($insert_result) {
                        $stmt->close();
                        $conn->close();
                        echo header('Location:./users.php?msg=update_success');
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
                <input type="text" class="form-control" value="<?= $user['name'] ?>" name="name" id="name" placeholder="Enter your name...">
                <small class="text-danger"><?php echo isset($_SESSION['error']['name']) ? $_SESSION['error']['name'] ?? '' : '' ?></small>
            </div>
            <div class="form-group mb-3">
                <label for="email" class="form-label">Email <b>*</b></label>
                <input type="text" class="form-control" value="<?= $user['email'] ?>" name="email" id="email" placeholder="Enter your email...">
                <small class="text-danger"><?php echo isset($_SESSION['error']['email']) ? $_SESSION['error']['email'] ?? '' : '' ?></small>
            </div>
            <div class="form-group mb-3">
                <label for="username" class="form-label">Username <b>*</b></label>
                <input type="text" class="form-control" value="<?= $user['username'] ?>" name="username" id="username" placeholder="Enter your username...">
                <small class="text-danger"><?php echo isset($_SESSION['error']['username']) ? $_SESSION['error']['username'] ?? '' : '' ?></small>
            </div>
            <div class="form-group mb-3">
                <label for="role" class="form-label">Role <b>*</b></label>
                <select name="role" id="role" class="form-control">
                    <option value="admin">Admin</option>
                    <option value="teacher">Teacher</option>
                    <option value="student">Student</option>
                </select>
                <small class="text-danger"><?php echo isset($_SESSION['error']['role']) ? $_SESSION['error']['role'] ?? '' : '' ?></small>
                <script>
                    const role = "<?= $user['role'] ?>"
                    document.getElementById('role').value = `${role}`
                </script>
            </div>
            <button type="submit" name="save" class="btn btn-success">Save User</button>
        </form>
    </div>
    <?php include './../layouts/footer.php'; ?>
</body>

</html>

<?php unset($_SESSION['error']) ?>