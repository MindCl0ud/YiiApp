<?php

session_start();
// Database connection
include 'connection.php';

function check_login() {
    if(!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
}
check_login();

// Check if user is Admin
function check_admin() {
    if($_SESSION['role'] !== 'admin') {
        header("Location: index.php");
        exit();
    }
}
check_admin();


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Function to create new account
function create_account($conn, $username, $password, $name, $role) {
    // Check if username already exists
    $sql_check = "SELECT * FROM account WHERE username='$username'";
    $result_check = $conn->query($sql_check);
    if ($result_check->num_rows > 0) {
        echo "Username already exists!";
        return;
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new account into database
    $sql_insert = "INSERT INTO account (username, password, name, role) VALUES ('$username', '$hashed_password', '$name', '$role')";
    if ($conn->query($sql_insert) === TRUE) {
        echo "New account created successfully";
    } else {
        echo "Error: " . $sql_insert . "<br>" . $conn->error;
    }
}

// Function to edit account
function edit_account($conn, $username, $new_username, $password, $name, $role) {
    // Check if new username already exists
    $sql_check = "SELECT * FROM account WHERE username='$new_username' AND username<>'$username'";
    $result_check = $conn->query($sql_check);
    if ($result_check->num_rows > 0) {
        echo "New username already exists!";
        return;
    }

    // Hash the password for security if password is provided
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_password = ", password='$hashed_password'";
    } else {
        $update_password = "";
    }

    // Update account in the database
    $sql_update = "UPDATE account SET username='$new_username', name='$name', role='$role' $update_password WHERE username='$username'";
    if ($conn->query($sql_update) === TRUE) {
        echo "Account updated successfully";
    } else {
        echo "Error: " . $sql_update . "<br>" . $conn->error;
    }
}


// CRUD logic for account (Create, Read, Update, Delete)
if($_SESSION['role'] === 'admin') {
    // Admin can create new account
    if (isset($_POST['create_account'])) {
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $role = isset($_POST['role']) ? $_POST['role'] : '';
        create_account($conn, $username, $password, $name, $role);
    }
}


// Fetch all accounts if user is admin
$accounts = [];
if($_SESSION['role'] === 'admin') {
    $sql = "SELECT * FROM account";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $accounts[] = $row;
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Account</title>
</head>
<body>
<div class="back-button">
    <a href="index.php">Back</a>
</div>
<?php if($_SESSION['role'] === 'admin'): ?>
    <h2>Create New Account</h2>
    <form method="post">
        <div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div>
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="admin">Admin</option>
                <option value="author">Author</option>
            </select>
        </div>
        <button type="submit" name="create_account">Create Account</button>
    </form>
    <br>
    <h2>Edit Account</h2>
    <form method="post">
        <div>
            <label for="old_username">Old Username:</label>
            <input type="text" id="old_username" name="old_username" required>
        </div>
        <div>
            <label for="new_username">New Username:</label>
            <input type="text" id="new_username" name="new_username">
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password">
        </div>
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name">
        </div>
        <div>
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="admin">Admin</option>
                <option value="author">Author</option>
            </select>
        </div>
        <button type="submit" name="edit_account">Edit Account</button>
    </form>
    <h2>All Accounts</h2>
    <!-- Display all accounts -->
    <table border="1">
        <tr>
            <th>Username</th>
            <th>Name</th>
            <th>Role</th>
        </tr>
        <?php foreach ($accounts as $account): ?>
            <tr>
                <td><?php echo $account['username']; ?></td>
                <td><?php echo $account['name']; ?></td>
                <td><?php echo $account['role']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

</body>
</html>