<?php
include 'backend/db.php';
date_default_timezone_set('Asia/Manila');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="frontend/css/home.css">
    <script src = "frontend/js/home.js"></script>
</head>
<body>
    <div class="header-home">
        <a href = "index.php">
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
    </div>

    <div class="banner">
        <h2>BASTA ENERGY, NUMBER 1 PIRMI!</h2>

        <div class="hosts-home">
            <img style="width: 850px; height: auto;" src="frontend/images/EFDJS.png" alt="Main DJs Photo">
        </div>

        <div class="listen">
            <button id="liveBtn" class="listen-button">
                <div class="listen-title"><img src = "frontend/images/play_button.svg" class = "play" alt="Play Button">LISTEN LIVE HERE   
                <img src = "frontend/images/listen_live.svg" alt="Listen Live Icon"></div>
                <div class="listen-subtitle">On Air: Loading...</div>
            </button>

            <audio id="liveAudio">
                <source src="https://stream.zeno.fm/klewk28qlqquv.m3u" type="audio/mpeg">
            </audio>
        </div>
    </div>

    <div class = "break-box"></div>
    <div class="section-title">FEATURED PROGRAMS</div>
    <div class="programs"></div>

    <div class="section-title-news">LATEST NEWS UPDATES</div>
    <div class="news">
        <?php
        $sql = "SELECT * FROM News ORDER BY DATE_POSTED DESC, ID DESC LIMIT 5";
        $result = $conn->query($sql);

        function formatNewsDate($datetimeString) {
            $posted = new DateTime($datetimeString);
            $now = new DateTime();

            $diff = $now->getTimestamp() - $posted->getTimestamp();

            if ($diff < 60) {
                return "Just Now";
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

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '
                <div class="news-card-home">
                    <a href="' . $row["SOURCE_URL"] . '" target="_blank">
                        <img src="' . $row["HEADLINE_IMAGE_PATH"] . '" width="325" height="300" alt="News Image" class="news-image">
                    </a>
                    <div class="news-header">' . $row["HEADLINE"] . '</div>
                    <div>' . $row["ORGANIZATION"] . ' | ' . formatNewsDate($row["DATE_POSTED"]) . '</div>
                    <div>By: ' . $row["AUTHOR"] . '</div>
                </div>';
            }
        } else {
            echo "No news inserted yet.";
        }

        $conn->close();
        ?>

    </div>

    <div class = "footer">
        <footer>Privacy Policy | Energy FM © 2025</footer>
    </div>

    </body>
</html>
