<?php
// ================== DATABASE CONFIG ==================
$host = "localhost";
$db   = "message_board";
$user = "root";      // change if needed
$pass = "root";          // change if needed

$conn = new mysqli($host, $user, $pass, $db, 8889);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ================== ADD MESSAGE ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['message'])) {
    $name = trim($_POST['name']);
    $message = trim($_POST['message']);

    if ($name !== "" && $message !== "") {
        $stmt = $conn->prepare("INSERT INTO messages (name, message) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $message);
        $stmt->execute();
        $stmt->close();
    }

    // Prevent form resubmission
    header("Location: index.php");
    exit;
}

// ================== DELETE MESSAGE ==================
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}

// ================== FETCH MESSAGES ==================
$result = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP Message Board</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }
        form {
            background: #ffffff;
            padding: 15px;
            margin-bottom: 20px;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        button {
            padding: 8px 12px;
        }
        .message {
            background: #ffffff;
            padding: 10px;
            margin-bottom: 10px;
        }
        .meta {
            font-size: 0.9em;
            color: #555;
        }
        .delete {
            color: red;
            text-decoration: none;
        }
    </style>
</head>
<body>

<h2>Post a Message</h2>
<form method="post">
    <input type="text" name="name" placeholder="Your name" required>
    <textarea name="message" placeholder="Your message" required></textarea>
    <button type="submit">Send</button>
</form>

<h2>Messages</h2>

<?php while ($row = $result->fetch_assoc()): ?>
    <div class="message">
        <div class="meta">
            <strong><?= htmlspecialchars($row['name']) ?></strong>
            | <?= $row['created_at'] ?>
            | <a class="delete" href="?delete=<?= $row['id'] ?>"
                 onclick="return confirm('Delete this message?');">
                 Delete
              </a>
        </div>
        <p><?= nl2br(htmlspecialchars($row['message'])) ?></p>
    </div>
<?php endwhile; ?>

</body>
</html>

<?php
$conn->close();
?>
