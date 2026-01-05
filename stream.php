<?php
include 'backend/db.php';
include 'backend/auto_ingest.php';

$sql = "
    SELECT 
        abl.ID,
        abl.DATE,
        abl.START_TIME,
        abl.END_TIME,
        abl.AUDIO_FILE_PATH,
        p.TITLE
    FROM Audio_Broadcast_Log abl
    LEFT JOIN Program p ON abl.PROGRAM_ID = p.id
    ORDER BY abl.DATE DESC, abl.START_TIME DESC
";

$result = $conn->query($sql);

$baseUrl = "http://localhost:8000/";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Energy Streams</title>
    <link rel="stylesheet" href="frontend/css/stream.css">
    <script src = "frontend/js/stream.js"></script>
</head>
<body>

    <header class="header">
        <a href = "home.php">
            <img src = "frontend/images/logo.png" alt = "Energy FM 106.3 Naga Logo" class = "logo">
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
            <h1>ENERGY STREAMS</h1>
            <p class = "intro">Welcome to ENERGY Streams! Explore latest  live broadcasts and past audio highlights. 
                Replay your favorite moments, search for the songs you love, and vibe anytime you want. Tune in, pangga!</p>
        </div>
    </header>

    <div class = "break-box"></div>

    <div class="stream-section">
        <div class="harambogan-content">
            <div class="harambogan-text">
                <h2>HARAMBOGAN SA RADYO</h2>
                <p>The station is well-known in the city for its popular catchphrases, such as "Pangga, may Energy ka pa ba?" and "Basta Energy, Number 1 pirmi!". 
                    It has also been recognized several times as the Number 1 radio station in Nago.</p>
            </div>
            <div class="harambogan-video">
                <div class="video-placeholder">
                </div>
            </div>
        </div>
    </div>

    <div class = "break-box"></div>

    <div class="stream-section">
        <h2 class="section-heading">STREAM THE LATEST VIDEO LIVESTREAMS!</h2>
        <div class="carousel">
            <div class="player-card"></div>
            <div class="player-card"></div>
            <div class="player-card"></div>
            <div class="player-card"></div>
            <div class="player-card"></div>
            <div class="player-card"></div>
            <div class="player-card"></div>
            <div class="player-card"></div>
            <div class="player-card"></div>
            <div class="player-card"></div>
        </div>
    </div>

    <div class="stream-section">
        <h2 class="section-heading">AUDIO BROADCASTS</h2>
        <div class="table-container">
            <div class="search-box">
                <span class="search-icon">🔍︎</span>
                <input type="text" placeholder="Search">
            </div>
            <div class="table-wrapper">
                <table class="stream-table">
                    <thead>
                        <tr>
                            <th class="col-name">Name <span class="sort-icon">▼</span></th>
                            <th class="col-date">Date <span class="sort-icon">▼</span></th>
                            <th class="col-time">Time <span class="sort-icon">▼</span></th>
                            <th class="col-action"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <audio id="audio-<?= $row['ID'] ?>" preload="none">
                                        <source src="<?= $baseUrl . htmlspecialchars($row['AUDIO_FILE_PATH']) ?>" type="audio/mpeg">
                                    </audio>

                                    <div class="broadcast-title">
                                        <span class="broadcast-icon"
                                            id="icon-<?= $row['ID'] ?>"
                                            onclick="togglePlay(<?= $row['ID'] ?>)">
                                            ▶
                                        </span>

                                        <span class="broadcast-text">
                                            <?= htmlspecialchars($row['TITLE'] ?? 'No Specific Program') ?>
                                        </span>
                                    </div>
                                </td>

                                <td class="center-align">
                                    <?= date("M d, Y", strtotime($row['DATE'])) ?>
                                </td>

                                <td class="center-align">
                                    <?= date("g:i A", strtotime($row['START_TIME'])) ?>
                                    –
                                    <?= date("g:i A", strtotime($row['END_TIME'])) ?>
                                </td>

                                <td>
                                    <span class="heart-icon">♡</span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="center-align">
                                No audio broadcasts available.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class = "footer">
        <footer>Privacy Policy | Energy FM © 2025</footer>
    </div>

</body>
</html>
