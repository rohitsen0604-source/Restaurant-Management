<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['dsn']      The full DSN string describe a connection to the database.
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database driver. e.g.: mysqli.
|			Currently supported:
|				 cubrid, ibase, mssql, mysql, mysqli, oci8,
|				 odbc, pdo, postgre, sqlite, sqlite3, sqlsrv
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Query Builder class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['encrypt']  Whether or not to use an encrypted connection.
|
|			'mysql' (deprecated), 'sqlsrv' and 'pdo/sqlsrv' drivers accept TRUE/FALSE
|			'mysqli' and 'pdo/mysql' drivers accept an array with the following options:
|
|				'ssl_key'    - Path to the private key file
|				'ssl_cert'   - Path to the public key certificate file
|				'ssl_ca'     - Path to the certificate authority file
|				'ssl_capath' - Path to a directory containing trusted CA certificates in PEM format
|				'ssl_cipher' - List of *allowed* ciphers to be used for the encryption, separated by colons (':')
|				'ssl_verify' - TRUE/FALSE; Whether verify the server certificate or not
|
|	['compress'] Whether or not to use client compression (MySQL only)
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|	['ssl_options']	Used to set various SSL options that can be used when making SSL connections.
|	['failover'] array - A array with 0 or more data for connections if the main should fail.
|	['save_queries'] TRUE/FALSE - Whether to "save" all executed queries.
| 				NOTE: Disabling this will also effectively disable both
| 				$this->db->last_query() and profiling of DB queries.
| 				When you run a query, with this setting set to TRUE (default),
| 				CodeIgniter will store the SQL statement for debugging purposes.
| 				However, this may cause high memory usage, especially if you run
| 				a lot of SQL queries ... disable this to avoid that problem.
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $query_builder variables lets you determine whether or not to load
| the query builder class.
*/
$active_group = 'default';
$query_builder = TRUE;

if (!function_exists('ci_getenv')) {
	function ci_getenv($key, $default = null) {
		if (isset($_ENV[$key]) && $_ENV[$key] !== '') return $_ENV[$key];
		if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') return $_SERVER[$key];
		$val = getenv($key);
		if ($val !== false && $val !== '') return $val;
		return $default;
	}
}

// Support individual environment variables from $_ENV, $_SERVER or getenv
$db_host = ci_getenv('DB_HOST', 'localhost');
$db_user = ci_getenv('DB_USER', 'root');
$db_pass = ci_getenv('DB_PASS', ci_getenv('DB_PASSWORD', ''));
$db_name = ci_getenv('DB_NAME', ci_getenv('DB_DATABASE', 'db_reservation'));
$db_port = (int)ci_getenv('DB_PORT', 3306);
$db_ssl  = (ci_getenv('DB_SSL') === 'true' || ci_getenv('DB_SSL') === '1');

// Support full URL like DATABASE_URL or MYSQL_URL (mysql://user:pass@host:port/dbname)
$db_url = ci_getenv('DATABASE_URL', ci_getenv('MYSQL_URL'));
if (!empty($db_url)) {
	$parsed = parse_url($db_url);
	if ($parsed) {
		if (!empty($parsed['host'])) $db_host = $parsed['host'];
		if (!empty($parsed['user'])) $db_user = urldecode($parsed['user']);
		if (isset($parsed['pass'])) $db_pass = urldecode($parsed['pass']);
		if (!empty($parsed['port'])) $db_port = (int)$parsed['port'];
		if (!empty($parsed['path'])) $db_name = ltrim($parsed['path'], '/');
		if (!empty($parsed['query'])) {
			parse_str($parsed['query'], $query_params);
			if (isset($query_params['ssl-mode']) && $query_params['ssl-mode'] !== 'DISABLED') {
				$db_ssl = true;
			}
		}
	}
}

$encrypt = $db_ssl ? array('ssl_verify' => FALSE) : FALSE;
$db_debug = (ci_getenv('DB_DEBUG') === 'false' || ci_getenv('DB_DEBUG') === '0') ? FALSE : TRUE;

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => $db_host,
	'username' => $db_user,
	'password' => $db_pass,
	'database' => $db_name,
	'port'     => $db_port,
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => $db_debug,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => $encrypt,
	'compress' => FALSE,
	'stricton' => NULL,
	'failover' => array(),
	'save_queries' => TRUE
);