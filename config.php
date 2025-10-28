<?php
// config.php on Railway
// Use the exact variable names provided by Railway
define('DB_SERVER', $_ENV['mysql.railway.internal']); 
define('DB_USERNAME', $_ENV['root']);
define('DB_PASSWORD', $_ENV['BvYRwYAjDWbKdnnXJjdaCNqDfMdZmidR]);
define('DB_NAME', $_ENV['railway']); // This should be 'railway' based on your last variable check

// Example of establishing the connection:
// $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
// ...
?>