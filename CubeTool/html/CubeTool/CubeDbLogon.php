<?php
$db_host = getenv("DB_HOST");
$db_port = getenv("DB_PORT"); 
$db_name = getenv("DB_NAME");
$db_user = getenv("DB_USER");
$db_password = getenv("DB_PASSWORD");

$dsn = "pgsql:host=".$db_host.";port=".$db_port.";dbname=".$db_name.";";
$conn = new PDO($dsn, $db_user, $db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
?>
