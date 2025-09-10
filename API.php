<?php
header("Content-Type: application/json; charset=utf-8");

// Kết nối MySQL
$conn = new mysqli("localhost", "root", "", "database");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Kết nối thất bại: " . $conn->connect_error], JSON_UNESCAPED_UNICODE);
    exit();
}
$conn->set_charset("utf8mb4");

// Lấy từ khóa từ GET hoặc POST
$keyword = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : '';

if ($keyword != "") {
    $sql = "SELECT article_id, article_link, article_title, article_author, article_date 
            FROM article 
            WHERE (article_link LIKE ? OR article_title LIKE ?)
            AND article_status = 1
            ORDER BY article_date DESC";

    $stmt = $conn->prepare($sql);s

    $like = "%".$keyword."%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();

    $articles = [];
    while ($row = $result->fetch_assoc()) {
        $articles[] = $row;
    }

    echo json_encode([
        "keyword" => $keyword,
        "count"   => count($articles),
        "results" => $articles
    ], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
} else {
    echo json_encode(["error" => "Vui lòng nhập từ khóa"], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>
