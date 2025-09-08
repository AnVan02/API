<?php

/**
 * 1. Thiết lập Header để đảm bảo phản hồi luôn là JSON
 */
header('Content-Type: application/json');
// Thiết lập thêm header CORS nếu bạn muốn gọi API này từ một domain khác (optional)
// header('Access-Control-Allow-Origin: *'); 
// header('Access-Control-Allow-Methods: GET, POST'); 
// header('Access-Control-Allow-Headers: Content-Type');


// Khởi tạo tên mặc định
$name = 'bạn';
$status = 200; // Mã trạng thái HTTP


/**
 * 2. Lấy dữ liệu dựa trên phương thức yêu cầu (GET hoặc POST)
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Xử lý phương thức POST
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if ($data !== null && isset($data['name'])) {
        $name = trim($data['name']);
        $message_type = 'Chào mừng (POST)';
    } else {
        // Xử lý lỗi nếu JSON không hợp lệ hoặc thiếu trường 'name'
        $status = 400; // Bad Request
        $response = [
            'error' => 'Dữ liệu POST không hợp lệ hoặc thiếu trường "name".',
            'method' => 'POST'
        ];
        // Trả về lỗi và kết thúc chương trình
        http_response_code($status);
        echo json_encode($response);
        exit(); 
    }

} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Xử lý phương thức GET
    if (isset($_GET['name']) && !empty($_GET['name'])) {
        $name = trim($_GET['name']);
        $message_type = 'Xin chào (GET)';
    } else {
        $message_type = 'Xin chào (GET - mặc định)';
    }

} else {
    // Xử lý các phương thức khác (như PUT, DELETE,...) - Không hỗ trợ trong ví dụ này
    $status = 405; // Method Not Allowed
    $response = [
        'error' => 'Phương thức yêu cầu không được hỗ trợ.',
        'method' => $_SERVER['REQUEST_METHOD']
    ];
    http_response_code($status);
    echo json_encode($response);
    exit();
}


/**
 * 3. Tạo phản hồi thành công và trả về
 */

// Đảm bảo tên không rỗng sau khi trim (nếu rỗng, dùng mặc định)
$final_name = !empty($name) ? htmlspecialchars($name) : 'bạn';

$response = [
    'status' => 'success',
    'code' => $status,
    'message' => "{$message_type}, {$final_name}!",
    'your_name' => $final_name
];

// Thiết lập mã trạng thái HTTP
http_response_code($status);

// Chuyển đổi mảng thành JSON và in ra
echo json_encode($response);

?>