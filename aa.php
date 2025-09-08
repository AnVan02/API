<?php
// Thiết lập header để trả về JSON và hỗ trợ CORS
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *"); // Cho phép mọi domain truy cập
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // Cho phép các phương thức
header("Access-Control-Allow-Headers: Content-Type"); // Cho phép các header

// Xử lý request OPTIONS cho CORS (Cross-Origin Resource Sharing)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Kiểm tra phương thức request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ request body
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Kiểm tra xem có trường 'name' trong dữ liệu không
    if (!isset($input['name'])) {
        http_response_code(400); // Bad Request
        echo json_encode(array("error" => "Thiếu trường name trong dữ liệu"));
        exit();
    }
    
    $name = $input['name'];
    
    // In tên ra log (có thể xem trong file error_log của XAMPP/MAMP)
    error_log("Tên nhận được: " . $name);
    
    // Trả về phản hồi thành công
    echo json_encode(array("message" => "Đã nhận và in tên: " . $name));
    
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Xử lý request GET (tùy chọn)
    $name = isset($_GET['name']) ? $_GET['name'] : '';
    
    if (!empty($name)) {
        error_log("Tên nhận được (GET): " . $name);
        echo json_encode(array("message" => "Đã nhận và in tên (GET): " . $name));
    } else {
        // Hướng dẫn sử dụng nếu không có name
        echo json_encode(array("instruction" => "Gửi POST request với JSON: {'name': 'Tên của bạn'}"));
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("error" => "Phương thức không được hỗ trợ"));
}
?>