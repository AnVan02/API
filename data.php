<?php
// DB connection settings — change these to match your MySQL
$DB_HOST = '127.0.0.1';
$DB_NAME = 'website'; // TODO: change to your database name
$DB_USER = 'root';    // XAMPP default is 'root' with empty password
$DB_PASS = '';
$DB_CHARSET = 'utf8mb4';

/**
 * Returns a singleton PDO connection.
 */
function db(): PDO {
	static $pdo = null;
	global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS, $DB_CHARSET;
	if ($pdo instanceof PDO) {
		return $pdo;
	}
	$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}";
	$options = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => false,
	];
	$pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
	return $pdo;
}

/** Sends a JSON response and exits. */
function json_ok(array $data = [], int $status = 200): void {
	http_response_code($status);
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode(['ok' => true] + $data, JSON_UNESCAPED_UNICODE);
	exit;
}

/** Sends a JSON error and exits. */
function json_error(string $message, int $status = 400, array $extra = []): void {
	http_response_code($status);
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode(['ok' => false, 'error' => $message] + $extra, JSON_UNESCAPED_UNICODE);
	exit;
}
