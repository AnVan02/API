<?php
// Kết nối MySQL
$conn = new mysqli("localhost", "root", "", "database");
$conn->set_charset("utf8mb4");

// Lấy từ khóa người dùng nhập
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($keyword != "") {
    $sql = "SELECT * FROM article 
            WHERE (article_link LIKE ? OR article_title LIKE ?)
            AND article_status = 1
            ORDER BY article_date DESC";

    $stmt = $conn->prepare($sql);

    $like = "%".$keyword."%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h2>Kết quả cho: <b>".htmlspecialchars($keyword)."</b></h2>";
        while ($row = $result->fetch_assoc()) {
            echo "<div style='margin-bottom:20px;'>";
            echo "<h3>".$row['article_title']."</h3>";
            echo "<p>Link: <a href='".$row['article_link']."' target='_blank'>".$row['article_link']."</a></p>";
            echo "</div>";
        }
    } else {
        echo "<p>Không tìm thấy bài viết nào!</p>";
    }
} else {
    echo "<p>Vui lòng nhập từ khóa!</p>";
}

$conn->close();
?>







