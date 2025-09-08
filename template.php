
<title>Tin Tức</title>
    <!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/./">TRANG CHỦ</a></li>
        <li class="breadcrumb-item"><a href="../product.php">TIN TỨC </a></li>
    </ol>
</nav>
<?php
// Bật hiển thị lỗi
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

<?php
    // Thông tin kết nối
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'database';
    
   $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    

    // Lấy danh sách bài viết
    $sql = "SELECT * FROM article WHERE article_status = 1 ORDER BY article_date DESC"; // Sắp xếp theo ngày
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $news = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $news = [];
    }

    // Lấy bài viết nổi bật (bài viết mới nhất)

    $sqlFeatured = "SELECT * FROM article WHERE article_featured = 1 ORDER BY article_date DESC LIMIT 2";
    // $resultFeatured = $conn->query($sqlFeatured);

    // if ($resultFeatured->num_rows > 0) {
    //     $featuredNews = $resultFeatured->fetch_assoc();
    // } else {
    //     $featuredNews = null;
    // }
    
    // Lấy trang hiện tại từ URL
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    // Số bài viết mỗi trang
    $limit = ($page == 1) ? 4 : 9;

    // Tính toán vị trí bắt đầu
    $start = ($page - 1) * $limit;

    // Tính tổng số bài viết và trang
    $totalItems = count($news);
    $totalPages = ceil($totalItems / $limit);

    // Lấy danh sách bài viết cho trang hiện tại
    $currentPageItems = array_slice($news, $start, $limit);
    
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 9; // Hiển thị 9 bài viết mỗi trang, ngoại trừ trang đầu tiên
    $start = ($page - 2) * $limit;

    // Đối với trang đầu tiên, chỉ lấy các bài viết cũ (bỏ qua bài viết mới nhất)
    if ($page == 1) {
        $sql = "SELECT * FROM article WHERE article_status = 1 ORDER BY article_date ASC LIMIT $limit";
        $result = $conn->query($sql);
        $currentPageItems = ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    } else {
        // Đối với trang thứ hai trở đi, lấy bài viết mới nhất và tiếp tục với các bài viết khác
        $start = ($page - 2) * $limit; // Điều chỉnh vị trí bắt đầu
        $sql = "SELECT * FROM article WHERE article_status = 1 ORDER BY article_date DESC LIMIT $start, $limit";
        $result = $conn->query($sql);
        $currentPageItems = ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Tính tổng số bài viết và trang
    $totalItems = count($news);
    $totalPages = ceil(($totalItems - 9) / 9) + 1; // Trừ 9 bài đầu tiên cho trang đầu

    // Phân trang bài viết 
    


    // Xử lý khi bấm vào link bài viết
    if (isset($_GET['link'])) {
        $articleLink = $_GET['link'];
        $sql = "SELECT * FROM article WHERE article_link = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $articleLink);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $article = $result->fetch_assoc();
            // Hiển thị thông tin bài viết
        } else {
            echo "Bài viết không tồn tại.";
        }
    }
?>

<div class="featured-news">
    <h1>Tin tức nổi bật</h1>
    <div class="featured-container">
        <?php foreach ($featuredNews as $newsItem): ?>
            <div class="news-item">
                <div class="news-image">
                    <?php
                        $img = isset($newsItem['article_image']) ? trim($newsItem['article_image']) : '';
                        $imgSrc = ($img !== '' && preg_match('/^https?:\/\//i', $img))
                            ? $img
                            : '../tintuc_test/admin/modules/blog/uploads/' . $img;
                    ?>
                    <img src="<?= htmlspecialchars($imgSrc); ?>" alt="Featured Image">
                </div>
                <div class="news-content">
                    <h2>
                        <a href="tintuc/<?= htmlspecialchars($newsItem['article_link']); ?>">
                            <?= htmlspecialchars($newsItem['article_title']); ?>
                        </a>
                    </h2>
                    <small>Ngày: <?= date("d/m/Y", strtotime(htmlspecialchars($featuredNews['article_date']))); ?></small>
                    <small>Người viết: <?= htmlspecialchars($newsItem['article_author']); ?></small>
                    <p class="description"><?= htmlspecialchars_decode($newsItem['article_content']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="news-container">
    <?php foreach ($currentPageItems as $item): ?>
        <div class="news-item">
            <div class="news-image">
                <?php
                    $img = isset($item['article_image']) ? trim($item['article_image']) : '';
                    $imgSrc = ($img !== '' && preg_match('/^https?:\/\//i', $img))
                        ? $img
                        : '../tintuc_test/admin/modules/blog/uploads/' . $img;
                ?>
                <img src="<?= htmlspecialchars($imgSrc); ?>" alt="Image">
            </div>
            <div class="news-content">
                <h2>
                    <a href="tintuc/<?= htmlspecialchars($item['article_link']); ?>">
                        <?= htmlspecialchars($item['article_title']); ?>
                    </a>
                </h2>
                <small>Ngày: <?= date("d/m/Y", strtotime(htmlspecialchars($featuredNews['article_date']))); ?></small>
                <small>Người viết: <?= htmlspecialchars($item['article_author']); ?></small>
                <p class="description"><?= htmlspecialchars_decode($item['article_content']); ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i; ?>" <?= $i == $page ? "class='active'" : ""; ?>><?= $i; ?></a>
    <?php endfor; ?>
</div>
    <?php $conn->close(); ?>
<style>
 

/* Global Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
    color: #333;
    line-height: 1.8;
    margin: 0;
    padding: 0;
}

/* Featured News Section */
.featured-news {
    background-color: #fff;
    padding: 120px 15%;
    margin-bottom: 40px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.featured-news h1 {
    font-size: 2.5rem;
    text-align: center;
    margin-bottom: 30px;
    color: #333;
}

.featured-news .news-item {
    display: flex;
    flex-direction: row;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.featured-news .news-item:hover {
    transform: translateY(-10px);
}

.featured-news .news-image {
    flex: 1;
    height: 250px;
    overflow: hidden;
}

.featured-news .news-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.featured-news .news-content {
    flex: 2;
    padding: 20px;
}

.featured-news .news-content h2 {
    font-size: 1.8rem;
    color: #333;
    margin-bottom: 10px;
}

.featured-news .news-content small {
    display: block;
    color: #666;
    margin-bottom: 10px;
    font-size: 0.9rem;
}

.featured-news .news-content p {
    font-size: 1rem;
    color: #555;
    line-height: 1.6;
}

/* News List Section */
.news-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    padding: 0 15%;
}

.news-item {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.news-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.news-image {
    height: 200px;
    overflow: hidden;
}

.news-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.news-content {
    padding: 20px;
}

.news-content h2 {
    font-size: 1.4rem;
    margin-bottom: 10px;
}

.news-content small {
    display: block;
    color: #666;
    margin-bottom: 10px;
}

.news-content p {
    font-size: 0.95rem;
    color: #555;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    margin-top: 40px;
}

.pagination a {
    padding: 10px 20px;
    margin: 0 5px;
    background-color: #0066cc;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

.pagination a:hover {
    background-color: #004d99;
}

.pagination a.active {
    background-color: #003366;
}

/* Responsive Design */
@media (max-width: 768px) {
    .featured-news {
        padding: 20px 5%;
    }

    .featured-news h1 {
        font-size: 2rem;
    }

    .featured-news .news-item {
        flex-direction: column;
    }

    .news-container {
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 0 5%;
    }

    .news-item {
        flex-direction: column;
    }

    .news-image {
        height: 150px;
    }
}

@media (max-width: 480px) {
    body {
        font-size: 14px;
    }

    .featured-news h1 {
        font-size: 1.5rem;
    }

    .featured-news .news-item {
        flex-direction: column;
    }

    .featured-news .news-image {
        height: 150px;
    }

    .news-container {
        grid-template-columns: 1fr;
    }

    .pagination a {
        padding: 8px 15px;
    }
}



</style>
