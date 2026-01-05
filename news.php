<?php
include 'backend/db.php';
date_default_timezone_set('Asia/Manila');

function formatNewsDate($datetimeString) {
    $posted = new DateTime($datetimeString);
    $now = new DateTime();

    $diff = $now->getTimestamp() - $posted->getTimestamp();

    if ($diff < 60) {
        return "Just now";
    }

    $minutes = floor($diff / 60);
    $hours = floor($diff / 3600);

    if ($minutes < 60) {
        return $minutes . " minute" . ($minutes == 1 ? "" : "s") . " ago";
    }

    if ($hours < 24) {
        return $hours . " hour" . ($hours == 1 ? "" : "s") . " ago";
    }

    return $posted->format("F j, Y");
}

// fetch the most latest news (one only) for featured section
$featuredSql = "SELECT * FROM News ORDER BY DATE_POSTED DESC, ID DESC LIMIT 1";
$featuredResult = $conn->query($featuredSql);
$featuredNews = $featuredResult->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title> Energy News </title>
  <link rel="stylesheet" href="frontend/css/news.css">
  <script src="frontend/js/news.js"></script>
</head>

<body>
  <div class="header">
      <a href="home.php">
        <img src="frontend/images/logo.png" alt="Energy FM 106.3 Naga Logo" class="logo">
      </a>

      <input type="checkbox" id="menu-toggle">
      <label for="menu-toggle" class="menu-icon">&#9776;</label>

      <div class="dropdown-menu">
        <a href="about.php">About</a>
        <a href="profiles.php">Profiles</a>
        <a href="programs.php">Programs</a>
        <a href="stream.php">Stream</a>
        <a href="news.php">News</a>
      </div>

      <div class="header-overlay">
        <h1>ENERGY FEATURED NEWS</h1>
        <p class="intro-news">
          Pangga, updated ka na ba? Stay in the loop with ENERGY FM's featured news! From latest happenings to must-know
          updates, we've gathered the top stories right here. Check out what's making headlines and stay informed
          anytime.
        </p>
      </div>
  </div>

  <div class="break-box"></div>

  <main>
    <div class="featurednews">
      <a href="<?= htmlspecialchars($featuredNews['SOURCE_URL']) ?>" target="_blank">
          <img src="<?= htmlspecialchars($featuredNews['HEADLINE_IMAGE_PATH']) ?>" 
               alt="<?= htmlspecialchars($featuredNews['HEADLINE']) ?>" 
                class="featurednews-image"> </a>

      <div class="news-content">
        <div class="featurednews-author">
          <div class="author-profile"></div>
          <p>
            <b> <a href="<?= htmlspecialchars($featuredNews['SOURCE_URL']) ?>" 
                    target="_blank" style="color: inherit; text-decoration: none;">
                <?= htmlspecialchars($featuredNews['AUTHOR']) ?></a>
            </b>
          </p>

          <div class="ellipse"></div>
          <p><?= formatNewsDate($featuredNews['DATE_POSTED']) ?></p>
        </div>

        <h1><?= htmlspecialchars($featuredNews['HEADLINE']) ?></h1>
        <h5>
          <?= nl2br(htmlspecialchars($featuredNews['SUMMARY'])) ?>
        </h5>
      </div>
    </div>

    <h4> Latest News </h4>

    <div class="search-filter-section">
      <div class="search">
        <img src="frontend/images/search_icon.png" alt="Search Icon">
        <input type="text" id="newsSearch" placeholder="Search news here...">
      </div>

      <select id="sortSelect" class="news-filter">
        <option value="newest"> Newest - Oldest </option>
        <option value="oldest"> Oldest - Newest </option>
        <option value="title-az"> Title (A-Z) </option>
        <option value="org-az"> Organization (A-Z) </option>
        <option value="author-az"> Author (A-Z) </option>
      </select>
    </div>

    <div class="news-section">

      <?php
      $sql = "
          SELECT 
              n.*,
              GROUP_CONCAT(c.NAME ORDER BY c.NAME SEPARATOR ', ') AS CATEGORIES
          FROM News n
          LEFT JOIN News_Category nc ON n.ID = nc.NEWS_ID
          LEFT JOIN Category c ON nc.CATEGORY_ID = c.ID
          GROUP BY n.ID
          ORDER BY n.DATE_POSTED DESC, n.ID DESC
      ";

      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
              if ($row['ID'] == $featuredNews['ID']) continue;

              $categories = $row['CATEGORIES'] ? strtolower($row['CATEGORIES']) : '';

          echo '
          <div class="news-card"
              data-date="' . htmlspecialchars($row['DATE_POSTED'], ENT_QUOTES, 'UTF-8') . '"
              data-title="' . htmlspecialchars($row['HEADLINE'], ENT_QUOTES, 'UTF-8') . '"
              data-org="' . htmlspecialchars($row['ORGANIZATION'], ENT_QUOTES, 'UTF-8') . '"
              data-author="'. htmlspecialchars(strtolower($row['AUTHOR']), ENT_QUOTES, 'UTF-8') .'"
              data-categories="' . htmlspecialchars($categories, ENT_QUOTES, 'UTF-8') . '">
                  <a href="' . $row["SOURCE_URL"] . '" target="_blank">
                      <img src="' . $row["HEADLINE_IMAGE_PATH"] . '" alt="News Image">
                  </a>
                  <h6>' . $row["HEADLINE"] . '</h6>
                  <div class="news-company">
                      <p><b>' . $row["ORGANIZATION"] . '</b></p>
                      <div class="ellipse"></div>
                      <p>' . formatNewsDate($row["DATE_POSTED"]) . '</p>
                  </div>
                  <p>' . $row["SUMMARY"] . '</p>
                  <div class="author-category-section">
                      <p>
                          By: <a href="' . $row["SOURCE_URL"] . '" target="_blank">
                              ' . htmlspecialchars($row["AUTHOR"]) . '
                          </a>
                      </p>

                  <div class="category-container">
                      ' . (
                          $row["CATEGORIES"]
                          ? implode('', array_map(
                              fn($cat) => '<span class="category-pill">' . htmlspecialchars(trim($cat)) . '</span>',
                              explode(',', $row["CATEGORIES"])
                          ))
                          : ''
                      ) . '
                  </div>
                  </div>
              </div>';
          }
      } else {
          echo "No news inserted yet.";
      }
      ?>
      
    </div>
    <br><br><br>
  </main>

  <div class="footer">
    <footer>Privacy Policy | Energy FM © 2010</footer>
  </div>

</body>

</html>
