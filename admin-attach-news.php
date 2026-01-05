<?php
session_start();

if (
    empty($_SESSION['logged_in']) ||
    empty($_SESSION['admin_id'])
) {
    header("Location: index.php");
    exit;
}

include 'backend/db.php';

$edit_mode = false;
$edit_news = null;
$selected_categories = [];

if (isset($_GET['edit'])) {
    $edit_mode = true;
    $newsId = (int)$_GET['edit'];

    $stmt = $conn->prepare("
        SELECT * FROM News
        WHERE ID = ?
        AND ADMIN_ID = ?
    ");
    $stmt->bind_param("ii", $newsId, $_SESSION['admin_id']);
    $stmt->execute();
    $edit_news = $stmt->get_result()->fetch_assoc();

    if (!$edit_news) {
        header("Location: admin-home.php?error=unauthorized");
        exit;
    }
    $stmt->close();

    // fetch selected categories
    $catStmt = $conn->prepare(
        "SELECT CATEGORY_ID FROM News_Category WHERE NEWS_ID = ?"
    );
    $catStmt->bind_param("i", $newsId);
    $catStmt->execute();
    $res = $catStmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $selected_categories[] = $row['CATEGORY_ID'];
    }
    $catStmt->close();
}

// fetch categories
$categories = [];
$catSql = "SELECT ID, NAME FROM Category ORDER BY NAME ASC";
$catResult = $conn->query($catSql);

if ($catResult && $catResult->num_rows > 0) {
    while ($cat = $catResult->fetch_assoc()) {
        $categories[] = $cat;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $source_url   = trim($_POST['news-link']);
    $headline     = trim($_POST['headline']);
    $author       = trim($_POST['author']);
    $organization = trim($_POST['newsorg']);
    $summary      = trim($_POST['description']);
    $imagePath    = trim($_POST['image_url']);
    $categoryIds  = $_POST['categories'] ?? [];
    $adminId = $_SESSION['admin_id'];

    if ($edit_mode) {
        $stmt = $conn->prepare("
            UPDATE News SET
                SOURCE_URL = ?,
                HEADLINE_IMAGE_PATH = ?,
                HEADLINE = ?,
                AUTHOR = ?,
                ORGANIZATION = ?,
                SUMMARY = ?
            WHERE ID = ?
            AND ADMIN_ID = ?
        ");

        $stmt->bind_param(
            "ssssssii",
            $source_url,
            $imagePath,
            $headline,
            $author,
            $organization,
            $summary,
            $newsId,
            $adminId
        );
        $stmt->execute();
        $stmt->close();

        // reset categories
        $catDel = $conn->prepare("
            DELETE nc FROM News_Category nc
            JOIN News n ON nc.NEWS_ID = n.ID
            WHERE nc.NEWS_ID = ?
            AND n.ADMIN_ID = ?
        ");
        $catDel->bind_param("ii", $newsId, $adminId);
        $catDel->execute();
        $catDel->close();

    } else {
        $stmt = $conn->prepare("
            INSERT INTO News
            (ADMIN_ID, SOURCE_URL, HEADLINE_IMAGE_PATH, HEADLINE, AUTHOR, ORGANIZATION, SUMMARY, DATE_POSTED)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $stmt->bind_param(
            "issssss",
            $adminId,
            $source_url,
            $imagePath,
            $headline,
            $author,
            $organization,
            $summary
        );
        $stmt->execute();
        $newsId = $conn->insert_id;
        $stmt->close();
    }

    // insert categories
    if (!empty($categoryIds)) {
        $catStmt = $conn->prepare(
            "INSERT INTO News_Category (NEWS_ID, CATEGORY_ID) VALUES (?, ?)"
        );
        foreach ($categoryIds as $catId) {
            $catStmt->bind_param("ii", $newsId, $catId);
            $catStmt->execute();
        }
        $catStmt->close();
    }

    header("Location: admin-home.php?" . ($edit_mode ? "updated=1" : "added=1"));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en" class="admin-attach-news">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="frontend/css/admin-attach-news.css" rel="stylesheet">
</head>

<body class="admin-home">

    <?php if (isset($_GET['added'])): ?>
    <script>
        alert("News article added successfully.");
        window.history.replaceState({}, document.title, window.location.pathname);
    </script>
    <?php endif; ?>

    <header>
        <div class="header-content">
            <section class="header-image">
                <a href="admin-home.php" class="logo-link">
                    <img src="frontend/images/logo.png" class="logo" alt="Energy FM Logo">
                </a>
            </section>
        </div>

        <input type="checkbox" id="menu-toggle">
        <label for="menu-toggle" class="menu-icon">&#9776;</label>

        <div class="dropdown-menu">
            <a href="backend/logout.php">Logout</a>
        </div>
    </header>


    <div class="dashboard-content">
        <h1>ADMIN DASHBOARD</h1>

        <div class="admin-buttons">
            <a href="admin-attach-news.php" class="btn attach-news-button">
                <i class="fas fa-paperclip"></i> Attach News
            </a>
            <a href="admin-add-programs.php" class="btn add-programs-button">
                <i class="fas fa-plus"></i> Add Programs
            </a>
        </div>

        <hr>

        <div class="form-container">
            <form method="POST">
                <div class="news-link-container">
                    <input type="text" id="news-link" name="news-link"
                    value="<?= $edit_mode ? htmlspecialchars($edit_news['SOURCE_URL']) : '' ?>"
                    placeholder="Attach News Link Here" required>
                </div>

                <div class="two-column-container">
                    <div class="left-column">
                        <span class="category-placeholder">Category/s:</span>
        
                        <div class="categories-checkboxes">
                            <?php foreach ($categories as $category): ?>
                                <label class="category-item">
                                    <input type="checkbox" name="categories[]" value="<?= $category['ID'] ?>"
                                        <?= in_array($category['ID'], $selected_categories) ? 'checked' : '' ?>>
                                        <?= htmlspecialchars($category['NAME']) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
    
                        <input type="text" id="image_url" name="image_url"
                            value="<?= $edit_mode ? htmlspecialchars($edit_news['HEADLINE_IMAGE_PATH']) : '' ?>"
                            placeholder="Image URL (https://...)">
                    </div>

                    <div class="right-column">
                        <input type="text" id="headline" name="headline"
                            value="<?= $edit_mode ? htmlspecialchars($edit_news['HEADLINE']) : '' ?>"
                            placeholder="Headline">
                        <input type="text" id="author" name="author"
                            value="<?= $edit_mode ? htmlspecialchars($edit_news['AUTHOR']) : '' ?>"
                            placeholder="Author/s Name">
                        <input type="text" id="newsorg" name="newsorg"
                            value="<?= $edit_mode ? htmlspecialchars($edit_news['ORGANIZATION']) : '' ?>"
                            placeholder="News Organization/Company" required>
                        <textarea id="description" name="description" placeholder="Description" required><?= $edit_mode ? htmlspecialchars($edit_news['SUMMARY']) : '' ?></textarea>

                        <button type="submit" class="add-button">
                            <i class="fas <?= $edit_mode ? 'fa-pen' : 'fa-plus' ?>"></i>
                            <?= $edit_mode ? 'Update' : 'Add' ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <footer>
        Privacy Policy | Energy FM © 2025
    </footer>

</body>
</html>
