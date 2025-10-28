<?php
// config.php on Render connecting externally to Railway

// 1. Host/Server (Reads the custom variable defined on the Render dashboard)
// This must contain the full external address and port for cross-cloud connection.
define('DB_SERVER', $_ENV['RAILWAY_EXTERNAL_HOST']); 

// 2. Username (Reads the variable correctly mapped from Railway)
define('DB_USERNAME', $_ENV['MYSQL_USER']);

// 3. Password (Reads the variable correctly mapped from Railway)
define('DB_PASSWORD', $_ENV['MYSQL_PASSWORD']);

// 4. Database Name (Reads the variable correctly mapped from Railway)
define('DB_NAME', $_ENV['MYSQL_DATABASE']); 

// Example of connection using the four defined constants:
// $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
// ...
?>