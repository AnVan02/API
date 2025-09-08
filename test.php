<?php
// Cho phép CORS (nếu cần gọi từ frontend khác domain)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type");

// Lấy method request
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    // Đọc dữ liệu JSON từ request body
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Lấy tên từ request
    $name = isset($input['name']) ? $input['name'] : '';
    
    // Kiểm tra nếu không có tên
    if (empty($name)) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Vui lòng gửi tên!'
        ]);
        exit;
    }
    
    // In tên ra (sẽ hiện trong log server)
    error_log("Tên nhận được: " . $name);
    
    // Trả về response
    $response = [
        'message' => 'Xin chào ' . $name . '!',
        'receivedName' => $name,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} else if ($method == 'GET') {
    // API GET với tên trong URL parameter
    $name = isset($_GET['name']) ? $_GET['name'] : '';
    
    if (empty($name)) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Vui lòng gửi tham số name!'
        ]);
        exit;
    }
    
    // In tên ra log
    error_log("Hello " . $name . "!");
    
    // Trả về response
    $response = [
        'message' => 'Hello ' . $name . '!',
        'name' => $name
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} else {
    // Method không được hỗ trợ
    http_response_code(405);
    echo json_encode([
        'error' => 'Method không được hỗ trợ!'
    ]);
}
?>