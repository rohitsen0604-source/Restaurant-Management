<?php 
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : (getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '');
$db_name = getenv('DB_NAME') ?: (getenv('DB_DATABASE') ?: 'db_reservation');
$db_port = getenv('DB_PORT') ? (int)getenv('DB_PORT') : 3306;

$db_url = getenv('DATABASE_URL') ?: getenv('MYSQL_URL');
if (!empty($db_url)) {
	$parsed = parse_url($db_url);
	if ($parsed) {
		if (!empty($parsed['host'])) $db_host = $parsed['host'];
		if (!empty($parsed['user'])) $db_user = urldecode($parsed['user']);
		if (isset($parsed['pass'])) $db_pass = urldecode($parsed['pass']);
		if (!empty($parsed['port'])) $db_port = (int)$parsed['port'];
		if (!empty($parsed['path'])) $db_name = ltrim($parsed['path'], '/');
	}
}

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} 
?>