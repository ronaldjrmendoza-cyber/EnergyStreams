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

$sql = "
SELECT 
    n.ID,
    n.HEADLINE,
    n.DATE_POSTED,
    n.AUTHOR,
    n.ORGANIZATION,
    n.SOURCE_URL,
    n.HEADLINE_IMAGE_PATH,
    n.ADMIN_ID,
    n.SUMMARY,
    GROUP_CONCAT(c.NAME SEPARATOR ', ') AS CATEGORIES
FROM News n
LEFT JOIN News_Category nc ON n.ID = nc.NEWS_ID
LEFT JOIN Category c ON nc.CATEGORY_ID = c.ID
GROUP BY n.ID
ORDER BY n.DATE_POSTED DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en" class="admin-attach-news">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="frontend/css/admin-home.css" rel="stylesheet">
</head>
<body class="admin-home">

    <?php if (isset($_GET['deleted'])): ?>
        <script>
            alert("News article deleted successfully.");
            window.history.replaceState({}, document.title, window.location.pathname);
        </script>
    <?php endif; ?>

    <header>
        <div class="header-content">
            <section class="header-image">
                <a href="admin-home.php" class="logo-link">
                    <img src="frontend/images/logo.png" class="logo" alt="Energy FM 106.3 Naga Logo">
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
        
        <div class="news-card-grid">
            <h3>Attached News</h3>

            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>

                    <div class="program-card-admin">
                        <div class="card-details-wrapper">

                            <div class="card-header-news">
                                <span class="program-title">
                                    <?= htmlspecialchars($row['ORGANIZATION']) ?>
                                </span>
                            </div>

                            <a href="<?= htmlspecialchars($row['SOURCE_URL']) ?>" target="_blank">
                                <img src="<?= htmlspecialchars($row['HEADLINE_IMAGE_PATH']) ?>" 
                                    alt="News Image" class="news-image">
                            </a>

                            <div class="news-info">
                                <p class="card-description">
                                    <?= htmlspecialchars($row['SUMMARY']) ?>
                                </p>

                                <p class="hosts">
                                    By:
                                    <?php if (!empty($row['SOURCE_URL'])): ?>
                                        <a href="<?= htmlspecialchars($row['SOURCE_URL']) ?>" target="_blank">
                                            <b><?= htmlspecialchars($row['AUTHOR']) ?></b>
                                        </a>
                                    <?php else: ?>
                                        <b><?= htmlspecialchars($row['AUTHOR']) ?></b>
                                    <?php endif; ?>
                                </p>

                                <div class="news-info">
                                    <p class="published-date"> Attached On:
                                        <b><?= date("F d, Y", strtotime($row['DATE_POSTED'])) ?></b>
                                        &nbsp; | &nbsp;
                                        Category/s:
                                        <b><?= $row['CATEGORIES'] ? htmlspecialchars($row['CATEGORIES']) : 'Uncategorized' ?></b>
                                    </p>
                                </div>
                            </div>

                            <div class="card-actions card-actions-news">
                                <?php if ($row['ADMIN_ID'] == $_SESSION['admin_id']): ?>
                                    <a href="admin-attach-news.php?edit=<?= $row['ID'] ?>" class="edit-icon">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>

                                    <a href="admin-delete-news.php?id=<?= $row['ID'] ?>"
                                    class="delete-icon"
                                    onclick="return confirm('Are you sure you want to delete this news?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                 <?php endif; ?>

                            </div>

                        </div>
                    </div>

            <?php endwhile; ?>
            <?php else: ?>
                <p>No news articles found.</p>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'unauthorized'): ?>
            <script>
                alert("You are not allowed to edit/delete this news item because you did not publish it.");
                window.history.replaceState({}, document.title, window.location.pathname);
            </script>
            <?php endif; ?>
        </div>
    </div>
    
    <footer>
        Privacy Policy | Energy FM © 2025
    </footer>

</body>
</html>
