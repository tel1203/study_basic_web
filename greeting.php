<?php
// Get the current hour in 24-hour format
$hour = (int)date('H');
$greeting_message = "";

// Conditional Branching based on time
if ($hour >= 5 && $hour < 12) {
    $greeting_message = "Good Morning!";
} elseif ($hour >= 12 && $hour < 18) {
    $greeting_message = "Good Afternoon!";
} else {
    $greeting_message = "Good Evening!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dynamic Greeting</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-align: center; margin-top: 50px; }
        h1 { color: #28a745; font-size: 3em; }
        .time { font-size: 1.5em; color: #555; }
    </style>
</head>
<body>
    <h1><?php echo $greeting_message; ?></h1>
    <p class="time">The current server time is: <?php echo date('H:i:s'); ?></p>
    <p>Refresh the page and watch the greeting change!</p>
</body>
</html>

