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

// Check if user is Author
function check_author() {
    if($_SESSION['role'] !== 'author') {
        header("Location: index.php");
        exit();
    }
}

check_author();


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// CRUD logic for post (Create, Read, Update, Delete)
if($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'author') {
    // Author can create new post
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_post'])) {
        // Process form submission for creating new post
        $title = $_POST['title'];
        $content = $_POST['content'];
        $username = $_SESSION['username'];
        $date = date("Y-m-d H:i:s"); // Get current date and time

        $sql = "INSERT INTO post (title, content, date, username) VALUES ('$title', '$content', '$date', '$username')";
        if ($conn->query($sql) === TRUE) {
            echo "New post created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Author can edit their own post
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_post'])) {
        // Process form submission for editing post
        $idpost = $_POST['idpost'];
        $title = $_POST['title'];
        $content = $_POST['content'];

        $sql_update = "UPDATE post SET title='$title', content='$content' WHERE idpost='$idpost'";
        if ($conn->query($sql_update) === TRUE) {
            echo "Post updated successfully";
        } else {
            echo "Error: " . $sql_update . "<br>" . $conn->error;
        }
    }

    // Author can delete their own post
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_post'])) {
        // Process form submission for deleting post
        $idpost = $_POST['idpost'];

        $sql_delete = "DELETE FROM post WHERE idpost='$idpost'";
        if ($conn->query($sql_delete) === TRUE) {
            echo "Post deleted successfully";
        } else {
            echo "Error: " . $sql_delete . "<br>" . $conn->error;
        }
    }
}

// Fetch all posts
$posts = [];
$sql_select_posts = "SELECT * FROM post ORDER BY date DESC";
$result_posts = $conn->query($sql_select_posts);
if ($result_posts->num_rows > 0) {
    while($row = $result_posts->fetch_assoc()) {
        $posts[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Post</title>
    <style>
        .post-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }
        .post-title {
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 5px;
        }
        .post-content {
            margin-bottom: 10px;
        }
        .post-date {
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
<div class="back-button">
    <a href="index.php">Back</a>
</div>
<?php if($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'author'): ?>
    <h2>Create New Post</h2>
    <form method="post">
        <div>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div>
            <label for="content">Content:</label>
            <textarea id="content" name="content" rows="4" required></textarea>
        </div>
        <button type="submit" name="create_post">Create Post</button>
    </form>
<?php endif; ?>

<h2>All Posts</h2>
<?php foreach ($posts as $post): ?>
    <div class="post-box">
        <div class="post-title"><?php echo $post['title']; ?></div>
        <div class="post-content"><?php echo $post['content']; ?></div>
        <div class="post-date">Posted on: <?php echo $post['date']; ?></div>
        <?php if($_SESSION['role'] === 'admin' || ($_SESSION['role'] === 'author' && $_SESSION['username'] === $post['username'])): ?>
            <form method="post">
                <input type="hidden" name="idpost" value="<?php echo $post['idpost']; ?>">
                <div>
                    <label for="edit_title">Edit Title:</label>
                    <input type="text" id="edit_title" name="title" value="<?php echo $post['title']; ?>" required>
                </div>
                <div>
                    <label for="edit_content">Edit Content:</label>
                    <textarea id="edit_content" name="content" rows="4" required><?php echo $post['content']; ?></textarea>
                </div>
                <button type="submit" name="edit_post">Edit Post</button>
            </form>
            <form method="post" style="display: inline;">
                    <input type="hidden" name="idpost" value="<?php echo $post['idpost']; ?>">
                    <button type="submit" name="delete_post">Delete</button>
                </form>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

</body>
</html>
