<?php
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$name = null;

if ($method === 'GET') {
	$name = isset($_GET['name']) ? trim((string)$_GET['name']) : null;
} else {
	// Try POST form first
	if (isset($_POST['name'])) {
		$name = trim((string)$_POST['name']);
	} else {
		// Fallback to JSON body: { "name": "Your Name" }
		$rawInput = file_get_contents('php://input');
		if ($rawInput !== false && $rawInput !== '') {
			$decoded = json_decode($rawInput, true);
			if (json_last_error() === JSON_ERROR_NONE && isset($decoded['name'])) {
				$name = trim((string)$decoded['name']);
			}
		}
	}
}

if ($name === null || $name === '') {
	http_response_code(400);
	echo json_encode([
		'ok' => false,
		'error' => 'Missing "name" parameter',
	], JSON_UNESCAPED_UNICODE);
	exit;
}

echo json_encode([
	'ok' => true,
	'name' => $name,
	'message' => "Xin chào, $name",
], JSON_UNESCAPED_UNICODE);

// =====

// Thiết lập header để trả về JSON
header('Content-Type: application/json');

// Kiểm tra nếu request là POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ body
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Kiểm tra xem có trường 'name' không
    if (isset($input['name']) && !empty($input['name'])) {
        $name = $input['name'];
        echo json_encode(['message' => 'Tên của bạn là: ' . htmlspecialchars($name)]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Vui lòng cung cấp tên']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Phương thức không được hỗ trợ']);
}
?>


