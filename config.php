<?php
// config.php on Render/Railway

// 1. Hostname/Server
// Railway provides the internal host in the MYSQL_HOST variable.
define('DB_SERVER', $_ENV['MYSQL_HOST']); 

// 2. Username
// Railway provides the username in the MYSQL_USER variable.
define('DB_USERNAME', $_ENV['MYSQL_USER']);

// 3. Password
// Railway provides the password in the MYSQL_PASSWORD variable.
define('DB_PASSWORD', $_ENV['MYSQL_PASSWORD']);

// 4. Database Name
// Railway provides the database name in the MYSQL_DATABASE variable.
define('DB_NAME', $_ENV['MYSQL_DATABASE']);

// Example of establishing the connection:
// $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
// ...

// Check if the connection constants were set correctly (optional debug)
// echo "Host: " . DB_SERVER . "<br>";
// echo "User: " . DB_USERNAME . "<br>";
// echo "DB: " . DB_NAME . "<br>"; 

?>