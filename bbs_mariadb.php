<?php
// --- 1. Database Connection Configuration ---
// These credentials should ideally be stored outside the web root (e.g., in environment variables)
// but are kept here for simplicity in a single-file lecture example.
$servername = "localhost"; // Since MariaDB is on the same EC2 instance
$username = "bbs_user";    // Database user created for the application
$password = "your_strong_password"; // IMPORTANT: CHANGE THIS IN THE LECTURE!
$dbname = "simple_bbs";    // Database name

// --- 2. Establish Connection ---
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // In a real application, you would log this error and show a generic message to the user.
    die("Connection failed: " . $conn->connect_error);
}
// Set character set to UTF-8
$conn->set_charset("utf8mb4");


// --- 3. Handle Form Submission (INSERT Operation) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $message = trim($_POST['message']);

    if (!empty($name) && !empty($message)) {
        // Use prepared statements to prevent SQL Injection (Best Practice!)
        $stmt = $conn->prepare("INSERT INTO posts (name, message) VALUES (?, ?)");
        
        // 'ss' means two parameters, both are strings
        $stmt->bind_param("ss", $name, $message); 

        if ($stmt->execute()) {
            // Optional: Redirect to prevent form resubmission on refresh
            // header("Location: bbs_mariadb.php");
            // exit();
        } else {
            // Display error if insertion fails
            $error_message = "Error posting message: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error_message = "Name and Message cannot be empty.";
    }
}


// --- 4. Read Data for Display (SELECT Operation) ---
$posts = [];
// Select all posts, ordered by ID (newest post has highest ID)
$sql = "SELECT id, name, message, created_at FROM posts ORDER BY id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    // Fetch all results into an array
    while($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}

// --- 5. Close Connection ---
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple MariaDB BBS</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 0 20px; background-color: #f0f8ff; }
        h1 { color: #004d99; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
        .form-container { background: #e0f0ff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 10px; border: 1px solid #007bff; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #007bff; color: white; border: none; padding: 12px 25px; border-radius: 4px; cursor: pointer; font-size: 16px; transition: background-color 0.3s; }
        button:hover { background-color: #0056b3; }
        .post { background: #ffffff; border-left: 6px solid #28a745; padding: 15px; margin-bottom: 15px; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .post-meta { color: #666; font-size: 0.9em; margin-bottom: 5px; }
        .post-content { font-size: 1.1em; }
        .error-message { color: red; padding: 10px; background: #ffdddd; border: 1px solid red; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>

    <h1>💾 Simple MariaDB BBS</h1>
    <p>This version uses a **MariaDB Database** installed on the same server for structured data storage.</p>
    
    <?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <!-- Input Form -->
    <div class="form-container">
        <form action="bbs_mariadb.php" method="post">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Your Name" required>
            </div>
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="3" placeholder="What's on your mind?" required></textarea>
            </div>
            <button type="submit">Post Message to DB</button>
        </form>
    </div>

    <!-- Display Area -->
    <h2>Recent Posts from Database</h2>
    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <!-- Data is already secured by prepared statement before insertion -->
                <div class="post-meta">
                    #<?php echo htmlspecialchars($post['id']); ?> Posted by 
                    <strong><?php echo htmlspecialchars($post['name']); ?></strong> on 
                    <?php echo htmlspecialchars($post['created_at']); ?>
                </div>
                <div class="post-content"><?php echo nl2br(htmlspecialchars($post['message'])); ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No posts found in the database. Post the first message!</p>
    <?php endif; ?>

</body>
</html>

