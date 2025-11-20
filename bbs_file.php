<?php
// Define the file path where messages will be stored locally
$data_file = 'bbs_data.txt';

// Check if the form was submitted (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Receive Input Data
    $name = $_POST['name'];
    $message = $_POST['message'];

    // Basic Validation
    if (!empty($name) && !empty($message)) {
        // 2. Prepare Data for Saving
        // Use htmlspecialchars to prevent XSS (security best practice)
        $clean_name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $clean_message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        $timestamp = date('Y-m-d H:i:s');

        // Format: Date | Name | Message (One line per post)
        $log_entry = "{$timestamp} | {$clean_name} | {$clean_message}\n";

        // 3. Write to Local File
        // 'a' mode: Open for writing only; place the file pointer at the end of the file.
        $fp = fopen($data_file, 'a');
        if ($fp) {
            fwrite($fp, $log_entry);
            fclose($fp);
        }
    }
}

// 4. Read Data from Local File for Display
$posts = [];
if (file_exists($data_file)) {
    // Read the file into an array (one line per array element)
    $posts = file($data_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    // Reverse the array to show the newest posts first
    $posts = array_reverse($posts);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple File BBS</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 0 20px; background-color: #f9f9f9; }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        
        /* Form Styles */
        .form-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #0056b3; }

        /* Post Styles */
        .post { background: #fff; border-left: 5px solid #28a745; padding: 15px; margin-bottom: 15px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .post-meta { color: #666; font-size: 0.9em; margin-bottom: 5px; }
        .post-content { font-size: 1.1em; }
    </style>
</head>
<body>

    <h1>📝 Simple Local File BBS</h1>

    <!-- Input Form -->
    <div class="form-container">
        <form action="bbs_file.php" method="post">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Your Name" required>
            </div>
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="3" placeholder="What's on your mind?" required></textarea>
            </div>
            <button type="submit">Post Message</button>
        </form>
    </div>

    <!-- Display Area -->
    <h2>Recent Posts</h2>
    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $line): ?>
            <?php
                // Explode the line by separator " | " to get parts
                $parts = explode(" | ", $line);
                if (count($parts) === 3):
                    $date = $parts[0];
                    $name = $parts[1];
                    $msg = $parts[2];
            ?>
            <div class="post">
                <div class="post-meta">Posted by <strong><?php echo $name; ?></strong> on <?php echo $date; ?></div>
                <div class="post-content"><?php echo $msg; ?></div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No posts yet. Be the first to write something!</p>
    <?php endif; ?>

</body>
</html>

