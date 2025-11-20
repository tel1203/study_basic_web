<?php
// Set the Content-Type header to ensure the browser renders HTML correctly
header('Content-Type: text/html; charset=UTF-8');

function get_ec2_metadata($path) {
    // EC2 Metadata Service IP address (Link-Local)
    $metadata_url = 'http://169.254.169.254/latest/meta-data/' . $path;

    // Use file_get_contents to fetch metadata (needs 'allow_url_fopen' enabled, which is common)
    // If not allowed, students must use cURL extension or command line execution.
    $data = @file_get_contents($metadata_url);
    
    // Check if data was retrieved successfully
    return $data !== FALSE ? $data : "N/A (Not on EC2 or Access Denied)";
}

// Get the Instance ID
$instance_id = get_ec2_metadata('instance-id');

// Get the Private IP Address (Alternative: using $_SERVER['SERVER_ADDR'] in some configurations)
$local_ip = get_ec2_metadata('local-ipv4');

// Get the Current Server Time
$server_time = date('Y-m-d H:i:s T');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dynamic Server Information</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #e6f7ff; }
        .info-box { border: 1px solid #007bff; padding: 15px; border-radius: 8px; background-color: #ffffff; }
        h2 { color: #007bff; }
        strong { color: #333; }
    </style>
</head>
<body>
    <div class="info-box">
        <h2>AWS EC2 Dynamic Info (PHP)</h2>
        <p>This page demonstrates **Server-Side Programming** by fetching live data from the EC2 instance and OS.</p>
        
        <p><strong>1. Current Server Time (PHP):</strong> <?php echo $server_time; ?></p>
        
        <p><strong>2. EC2 Instance ID:</strong> <?php echo $instance_id; ?></p>
        <p><strong>3. EC2 Private IP:</strong> <?php echo $local_ip; ?></p>

        <p><strong>4. Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
        <p><strong>5. PHP Version:</strong> <?php echo phpversion(); ?></p>
    </div>
</body>
</html>

