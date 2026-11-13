<?php 
if (!function_exists('ci_getenv')) {
	function ci_getenv($key, $default = null) {
		if (isset($_ENV[$key]) && $_ENV[$key] !== '') return $_ENV[$key];
		if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') return $_SERVER[$key];
		$val = getenv($key);
		if ($val !== false && $val !== '') return $val;
		return $default;
	}
}

$db_host = ci_getenv('DB_HOST', 'localhost');
$db_user = ci_getenv('DB_USER', 'root');
$db_pass = ci_getenv('DB_PASS', ci_getenv('DB_PASSWORD', ''));
$db_name = ci_getenv('DB_NAME', ci_getenv('DB_DATABASE', 'db_reservation'));
$db_port = (int)ci_getenv('DB_PORT', 3306);

$db_url = ci_getenv('DATABASE_URL', ci_getenv('MYSQL_URL'));
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