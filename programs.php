<?php
include 'backend/db.php'; 

$programs = $conn->query("SELECT * FROM Program")->fetch_all(MYSQLI_ASSOC);
$programDayTypes = $conn->query("SELECT * FROM Program_Day_Type")->fetch_all(MYSQLI_ASSOC);
$dayTypes = $conn->query("SELECT * FROM Day_Type")->fetch_all(MYSQLI_ASSOC);
$assignments = $conn->query("SELECT * FROM Program_Anchor_Assignment")->fetch_all(MYSQLI_ASSOC);
$djs = $conn->query("SELECT * FROM DJ_Profile")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Energy Programs</title>
    <link rel="stylesheet" href="frontend/css/programs.css">
    <script src = "frontend/js/programs.js"></script>
</head>
<body>

<header class="header">
    <a href="home.php"><img src="frontend/images/logo.png" alt="Energy FM 106.3 Naga Logo" class="logo"></a>

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
        <h1>ENERGY PROGRAMS</h1>
        <p class="intro-programs">Pangga, may ENERGY ka pa ba? Tara na! Dive into the world of ENERGY FM where all your favorite programs are in ONE place!<br>
        From music that keeps you vibing to live broadcasts and talk shows that keep you company, everything is here. Check out each program's schedule and 
        anchors so you'll always know what's airing next!</p>
    </div>
</header>

<div class="break-box"></div>

<section class="content">

    <div class="top-bar">
        <h2>ALL PROGRAMS</h2>

        <select id="filter-select" class="filter-btn">
            <option value="title">TITLE (A–Z)</option>
            <option value="time">TIME (Earliest to Latest)</option>
            <option value="weekdays">WEEKDAYS</option>
            <option value="sat">SATURDAY</option>
            <option value="sun">SUNDAY</option>
        </select>
    </div>

    <div class="program-list">
        <?php
        function getDays($programId, $programDayTypes, $dayTypes) {
            $days = [];
            foreach ($programDayTypes as $p) {
                if ($p['PROGRAM_ID'] == $programId) {
                    foreach ($dayTypes as $d) {
                        if ($d['ID'] == $p['DAY_TYPE_ID']) {
                            $days[] = $d['DAY_TYPE'];
                        }
                    }
                }
            }
            return $days;
        }

        function getDJs($programId, $assignments, $djs) {
            $names = [];
            foreach ($assignments as $a) {
                if ($a['PROGRAM_ID'] == $programId) {
                    foreach ($djs as $dj) {
                        if ($dj['ID'] == $a['DJ_ID']) {
                            $names[] = $dj['STAGE_NAME'] ?: strtoupper($dj['REAL_NAME']);
                        }
                    }
                }
            }
            return implode(', ', $names);
        }

        foreach ($programs as $p):
            $days = getDays($p['ID'], $programDayTypes, $dayTypes);
            $djsList = getDJs($p['ID'], $assignments, $djs);
        ?>
        <div class="program-card" data-start="<?= $p['START_TIME'] ?>" data-end="<?= $p['END_TIME'] ?>" data-days="<?= implode(',', $days) ?>">
            <div class="card-left">
                <div class="title-status">
                    <h3><?= htmlspecialchars($p['TITLE']) ?></h3>
                    <span class="status offline">OFFLINE</span>
                </div>
                <p class="schedule"><?= date('g:i A', strtotime($p['START_TIME'])) ?>–<?= date('g:i A', strtotime($p['END_TIME'])) ?> | <?= implode(', ', $days) ?></p>
                <p class="hosts"><?= $djsList ?: htmlspecialchars($p['TYPE']) ?></p>
            </div>
            <div class="card-right">
                <p><?= htmlspecialchars($p['DESCRIPTION'] ?: 'Program description here') ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<div class="footer">
    <footer>Privacy Policy | Energy FM © 2025</footer>
</div>

</body>
</html>
