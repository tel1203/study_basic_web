<?php
// --- 1. Database Connection Configuration (RDS Endpoint) ---
// IMPORTANT: These must be replaced by the actual values from the RDS Console
$servername = "your-rds-endpoint.xxxxxxxx.ap-northeast-1.rds.amazonaws.com"; // <-- THIS IS THE MAJOR CHANGE!
$username = "bbs_user";    // Database user created for the application
$password = "your_strong_password"; // IMPORTANT: Use the password set during RDS creation
$dbname = "simple_bbs";    // Database name (created during RDS setup)

// --- 2. Establish Connection ---
// The connection logic (using mysqli) remains identical to the local MariaDB version.
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // RDS connection errors usually relate to Security Group or Endpoint issues.
    die("RDS Connection failed! Check Security Group & Endpoint: " . $conn->connect_error);
}
// Set character set to UTF-8
$conn->set_charset("utf8mb4");


// --- 3. Handle Form Submission (INSERT Operation) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $message = trim($_POST['message']);

    if (!empty($name) && !empty($message)) {
        // Use prepared statements (Security Best Practice)
        $stmt = $conn->prepare("INSERT INTO posts (name, message) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $message); 

        if (!$stmt->execute()) {
            $error_message = "Error posting message to RDS: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error_message = "Name and Message cannot be empty.";
    }
}


// --- 4. Read Data for Display (SELECT Operation) ---
$posts = [];
$sql = "SELECT id, name, message, created_at FROM posts ORDER BY id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
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
    <title>Simple RDS BBS</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 0 20px; background-color: #f7fff7; }
        h1 { color: #008000; border-bottom: 3px solid #008000; padding-bottom: 10px; }
        .form-container { background: #e6ffe6; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 10px; border: 1px solid #008000; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #008000; color: white; border: none; padding: 12px 25px; border-radius: 4px; cursor: pointer; font-size: 16px; transition: background-color 0.3s; }
        button:hover { background-color: #006600; }
        .post { background: #ffffff; border-left: 6px solid #ff9900; /* AWS Gold */ padding: 15px; margin-bottom: 15px; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .post-meta { color: #666; font-size: 0.9em; margin-bottom: 5px; }
        .post-content { font-size: 1.1em; }
        .error-message { color: red; padding: 10px; background: #ffdddd; border: 1px solid red; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>

    <h1>⭐ Cloud Native BBS (AWS RDS)</h1>
    <p>This version uses **Amazon RDS**, a fully managed, separate database service, demonstrating a true **Decoupled Architecture**.</p>
    
    <?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <!-- Input Form -->
    <div class="form-container">
        <form action="bbs_rds.php" method="post">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Your Name" required>
            </div>
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="3" placeholder="What's on your mind?" required></textarea>
            </div>
            <button type="submit">Post Message to RDS</button>
        </form>
    </div>

    <!-- Display Area -->
    <h2>Recent Posts from RDS Database</h2>
    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <div class="post-meta">
                    #<?php echo htmlspecialchars($post['id']); ?> Posted by 
                    <strong><?php echo htmlspecialchars($post['name']); ?></strong> on 
                    <?php echo htmlspecialchars($post['created_at']); ?>
                </div>
                <div class="post-content"><?php echo nl2br(htmlspecialchars($post['message'])); ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No posts found in the RDS database. Post the first message!</p>
    <?php endif; ?>

</body>
</html>

