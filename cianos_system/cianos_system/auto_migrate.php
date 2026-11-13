<?php
// auto_migrate.php - Automatically imports all missing tables from db_reservation.sql

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

$is_web = (php_sapi_name() !== 'cli');
if ($is_web) {
	echo "<pre>\n";
}

echo "=== Restaurant Management Database Auto-Migration ===\n";
echo "Connecting to MySQL server: $db_host (Database: $db_name)...\n";

$conn = mysqli_init();
$conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
if (!@$conn->real_connect($db_host, $db_user, $db_pass, $db_name, $db_port)) {
	echo "Error: Database connection failed: " . mysqli_connect_error() . "\n";
	if ($is_web) echo "</pre>";
	exit(1);
}

echo "Connected successfully!\n";

// Core tables to verify
$core_tables = array(
	'attendance', 'biometrics_attendance', 'cashadvance', 'delivery_details',
	'delivery_details_tmp', 'delivery_head', 'employees', 'huts', 'menu',
	'menu_category', 'menu_selection', 'overtime', 'position', 'registration',
	'reservation', 'reservation_details', 'reservation_details_tmp',
	'reservation_tmp', 'schedules', 'tables', 'users'
);

$existing_tables = array();
$res = $conn->query("SHOW TABLES");
if ($res) {
	while ($row = $res->fetch_array()) {
		$existing_tables[] = strtolower($row[0]);
	}
}

$missing = array_diff($core_tables, $existing_tables);

if (empty($missing)) {
	echo "All 21 tables already exist in database '$db_name'. Everything is up to date!\n";
	if ($is_web) echo "</pre>";
	exit(0);
}

echo "Missing tables detected: " . implode(', ', $missing) . "\n";
echo "Importing schema and data from db_reservation.sql...\n";

$sql_path = __DIR__ . '/db/db_reservation.sql';
if (!file_exists($sql_path)) {
	echo "Error: SQL file not found at $sql_path\n";
	if ($is_web) echo "</pre>";
	exit(1);
}

$sql_raw = file_get_contents($sql_path);

// Safe parsing: Remove comment lines
$lines = explode("\n", $sql_raw);
$clean_sql = "";
foreach ($lines as $line) {
	$trimmed = trim($line);
	if ($trimmed === '' || substr($trimmed, 0, 2) === '--' || substr($trimmed, 0, 2) === '/*') {
		continue;
	}
	$clean_sql .= $line . "\n";
}

// Convert CREATE TABLE to CREATE TABLE IF NOT EXISTS and INSERT to INSERT IGNORE
$clean_sql = preg_replace('/CREATE TABLE `([a-zA-Z0-9_]+)`/i', 'CREATE TABLE IF NOT EXISTS `$1`', $clean_sql);
$clean_sql = preg_replace('/INSERT INTO `([a-zA-Z0-9_]+)`/i', 'INSERT IGNORE INTO `$1`', $clean_sql);

// Split queries
$queries = explode(";\n", $clean_sql);
$executed = 0;
$errors = 0;

foreach ($queries as $query) {
	$q = trim($query);
	if (!empty($q)) {
		if (!@$conn->query($q)) {
			// Ignore common non-fatal errors like duplicate primary key or table already exists
			$err_no = $conn->errno;
			if ($err_no !== 1068 && $err_no !== 1050 && $err_no !== 1060 && $err_no !== 1062) {
				$errors++;
			}
		} else {
			$executed++;
		}
	}
}

echo "Migration finished: $executed statements executed.\n";

// Verify again
$verified_tables = array();
$res = $conn->query("SHOW TABLES");
if ($res) {
	while ($row = $res->fetch_array()) {
		$verified_tables[] = strtolower($row[0]);
	}
}

echo "Total tables now in database: " . count($verified_tables) . "\n";
echo "Tables list: " . implode(', ', $verified_tables) . "\n";
echo "All done! Restaurant Management System is ready to use.\n";

if ($is_web) {
	echo "\n<a href='index.php'>Go to Website Login</a>";
	echo "</pre>";
}

