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

$programs = $conn->query("
    SELECT 
        p.ID,
        p.TITLE,
        p.START_TIME,
        p.END_TIME,
        p.DESCRIPTION,
        p.TYPE,
        p.ADMIN_ID,
        GROUP_CONCAT(
            DISTINCT COALESCE(d.STAGE_NAME, UPPER(d.REAL_NAME))
            SEPARATOR ', '
        ) AS HOSTS,
        GROUP_CONCAT(DISTINCT dt.DAY_TYPE ORDER BY dt.ID SEPARATOR ', ') AS DAY_TYPES
    FROM Program p
    LEFT JOIN Program_Anchor_Assignment paa
        ON p.ID = paa.PROGRAM_ID
    LEFT JOIN DJ_Profile d
        ON paa.DJ_ID = d.ID
    LEFT JOIN Program_Day_Type pdt
        ON p.ID = pdt.PROGRAM_ID
    LEFT JOIN Day_Type dt
        ON pdt.DAY_TYPE_ID = dt.ID
    GROUP BY p.ID
    ORDER BY p.TITLE ASC
");

$dj_list = $conn->query("SELECT * FROM DJ_Profile ORDER BY STAGE_NAME ASC");

if (!$dj_list) {
    die("Query failed: " . $conn->error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = mb_strtoupper(trim($_POST['title']), 'UTF-8');
    $type  = $_POST['type'];
    $start = $_POST['start_time'];
    $end   = $_POST['end_time'];
    $desc  = $_POST['description'];
    $day_types = $_POST['day_types'] ?? [];
    $djs = $_POST['djs'] ?? [];

    if ($type === "WITH DJ/HOST" && empty($djs)) {
        die("Please select at least one DJ/Host.");
    }

    if (!empty($_POST['program_id'])) {
        // update
        $program_id = (int)$_POST['program_id'];

        $stmt = $conn->prepare("
            UPDATE Program
            SET TITLE=?, TYPE=?, START_TIME=?, END_TIME=?, DESCRIPTION=?
            WHERE ID=?
            AND ADMIN_ID=?
        ");
        $stmt->bind_param(
            "sssssis",
            $title,
            $type,
            $start,
            $end,
            $desc,
            $program_id,
            $_SESSION['admin_id']
        );
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            header("Location: admin-add-programs.php?error=unauthorized");
            exit;
        }
        $stmt->close();

        $conn->query("DELETE FROM Program_Day_Type WHERE PROGRAM_ID=$program_id");
        $conn->query("DELETE FROM Program_Anchor_Assignment WHERE PROGRAM_ID=$program_id");

    } else {
        // insert
        $admin_id = $_SESSION['admin_id'];
        $stmt = $conn->prepare("
            INSERT INTO Program (TITLE, TYPE, START_TIME, END_TIME, DESCRIPTION, ADMIN_ID)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssi", $title, $type, $start, $end, $desc, $admin_id);
        $stmt->execute();
        $program_id = $stmt->insert_id;
        $stmt->close();
    }

    foreach ($day_types as $day_id) {
        $conn->query("
            INSERT INTO Program_Day_Type (PROGRAM_ID, DAY_TYPE_ID)
            VALUES ($program_id, $day_id)
        ");
    }

    foreach ($djs as $dj_id) {
        $conn->query("
            INSERT INTO Program_Anchor_Assignment (PROGRAM_ID, DJ_ID)
            VALUES ($program_id, $dj_id)
        ");
    }

    header("Location: admin-add-programs.php?" . (isset($_POST['program_id']) ? "updated=1" : "added=1"));
    exit;
}

$edit_mode = false;
$edit_program = null;
$assigned_djs = [];
$assigned_days = [];

if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_id = (int)$_GET['edit'];

    $admin_id = $_SESSION['admin_id'];

    $edit_program = $conn->query("
        SELECT * FROM Program
        WHERE ID = $edit_id
        AND ADMIN_ID = $admin_id
    ")->fetch_assoc();

    if (!$edit_program) {
        header("Location: admin-add-programs.php?error=unauthorized");
        exit;
    }

    if ($edit_program) {

        $res = $conn->query("
            SELECT DJ_ID FROM Program_Anchor_Assignment
            WHERE PROGRAM_ID = $edit_id
        ");
        while ($r = $res->fetch_assoc()) {
            $assigned_djs[] = $r['DJ_ID'];
        }

        $res = $conn->query("
            SELECT DAY_TYPE_ID FROM Program_Day_Type
            WHERE PROGRAM_ID = $edit_id
        ");
        while ($r = $res->fetch_assoc()) {
            $assigned_days[] = $r['DAY_TYPE_ID'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="frontend/css/admin-add-programs.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src = "frontend/js/admin-add-programs.js"></script>
</head>

<body class="admin-home">

    <?php if (isset($_GET['added'])): ?>
    <script>
        alert("Program added successfully.");
        window.history.replaceState({}, document.title, window.location.pathname);
    </script>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
    <script>
        alert("Program deleted successfully.");
        window.history.replaceState({}, document.title, window.location.pathname);
    </script>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
    <script>
        alert("Program updated successfully.");
        window.history.replaceState({}, document.title, window.location.pathname);
    </script>
    <?php endif; ?>

    <header>
        <div class="header-content">
            <div class="header-left">
                <a href="admin-home.php" class="logo-link">
                    <img src="frontend/images/logo.png" class="logo" alt="Energy FM 106.3 Naga Logo">
                </a>
            </div>

            <input type="checkbox" id="menu-toggle">
            <label for="menu-toggle" class="menu-icon">&#9776;</label>

            <div class="dropdown-menu">
                <a href="backend/logout.php">Logout</a>
            </div>
        </div>
    </header>

    <main class="dashboard-content">
        <h1>ADMIN DASHBOARD</h1>
        
        <div class="admin-buttons">
            <a href="admin-attach-news.php" class="btn attach-news-button">
                <i class="fas fa-paperclip"></i> Attach News </a>
                
            <a href="admin-add-programs.php" class="btn add-programs-button">
                <i class="fas fa-plus"></i> Add Programs </a>
        </div>
        
        <hr>

        <section class="program-list-section">
            <div class="program-list">

            <?php while ($row = $programs->fetch_assoc()): ?>

                <div class="program-card">
                    <div class="card-details">
                        <div class="card-left-info">
                            <h3 class="program-title"><?= htmlspecialchars($row['TITLE']) ?></h3>
                            <p class="schedule">
                                <?= date("g:i A", strtotime($row['START_TIME'])) ?>
                                –
                                <?= date("g:i A", strtotime($row['END_TIME'])) ?>
                            </p>
                                <p class="hosts">
                                    <?= $row['HOSTS'] ? htmlspecialchars($row['HOSTS']) : 'MUSIC ONLY' ?>
                                </p>
                                <?php if (!empty($row['DAY_TYPES'])): ?>
                                    <p class="day-type"><?= htmlspecialchars($row['DAY_TYPES']) ?></p>
                                <?php endif; ?>
                        </div>

                        <div class="card-description">
                            <p><?= htmlspecialchars($row['DESCRIPTION']) ?></p>
                        </div>
                    </div>

                    <div class="card-actions card-actions-news">
                        <?php if ($row['ADMIN_ID'] == $_SESSION['admin_id']): ?>

                            <a href="admin-add-programs.php?edit=<?= $row['ID'] ?>" class="edit-icon">
                                <i class="fas fa-pencil-alt"></i>
                            </a>

                            <a href="admin-delete-program.php?id=<?= $row['ID'] ?>"
                                onclick="return confirm('Are you sure you want to delete this program?');"
                                class="delete-icon">
                                <i class="fas fa-trash-alt"></i>
                            </a>

                        <?php endif; ?>
                    </div>
                </div>

            <?php endwhile; ?>

            </div>
        </section>

        <section class="add-item-form">
            <form method="POST">
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="program_id" value="<?= $edit_program['ID'] ?>">
                <?php endif; ?>

                <div class="form-row form-top-row">

                <input type="text" id="headline" name="title" placeholder="Title"
                    value="<?= $edit_mode ? htmlspecialchars($edit_program['TITLE']) : '' ?>"
                    required>
                    
                    <div class="input-group-left">
                        <div class="horizontal-align-row">
                            <textarea id="description" name="description" placeholder="Description"
                                      required><?= $edit_mode ? htmlspecialchars($edit_program['DESCRIPTION']) : '' ?></textarea>
                            <div class="checkbox-container">
                                <div class="checkbox-group">
                                    <label>
                                        <input type="checkbox" name="day_types[]" value="1"
                                            <?= in_array(1, $assigned_days) ? 'checked' : '' ?>> WEEKDAYS
                                    </label>

                                    <label>
                                        <input type="checkbox" name="day_types[]" value="2"
                                            <?= in_array(2, $assigned_days) ? 'checked' : '' ?>> SAT
                                    </label>

                                    <label>
                                        <input type="checkbox" name="day_types[]" value="3"
                                            <?= in_array(3, $assigned_days) ? 'checked' : '' ?>> SUN
                                    </label>

                                </div>
                            </div>
                        </div>

                        <div class="horizontal-align-row">
                            <div class="form-bottom-row">
                                <input type="time" name="start_time"
                                    value="<?= $edit_mode ? $edit_program['START_TIME'] : '' ?>" required>

                                <p class="to">-</p>
                                <input type="time" name="end_time"
                                    value="<?= $edit_mode ? $edit_program['END_TIME'] : '' ?>" required>
                                <select id="type-selector" name="type" onchange="toggleDJFields()" required>
                                    <option value="" disabled hidden>Type</option>
                                    <option value="MUSIC ONLY"
                                        <?= $edit_mode && $edit_program['TYPE'] === 'MUSIC ONLY' ? 'selected' : '' ?>>
                                        MUSIC ONLY
                                    </option>
                                    <option value="WITH DJ/HOST"
                                        <?= $edit_mode && $edit_program['TYPE'] === 'WITH DJ/HOST' ? 'selected' : '' ?>>
                                        WITH DJ/HOST
                                    </option>
                                </select>

                            </div>

                            <div id="dj-selection-container" class="checkbox-container disabled-dj">
                                <div class="checkbox-group dj-list-scroll">
                                    <?php while($dj = $dj_list->fetch_assoc()): ?>
                                        <label>
                                            <input type="checkbox"
                                                class="dj-checkbox"
                                                name="djs[]"
                                                value="<?= $dj['ID'] ?>"
                                                <?= in_array($dj['ID'], $assigned_djs) ? 'checked' : '' ?>>

                                            <?= htmlspecialchars($dj['STAGE_NAME'] ?: strtoupper($dj['REAL_NAME'])) ?>
                                        </label>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="add-button">
                        <i class="fas <?= $edit_mode ? 'fa-save' : 'fa-plus' ?>"></i>
                        <?= $edit_mode ? 'Update' : 'Add' ?>
                    </button>

                    <?php if (isset($_GET['error']) && $_GET['error'] === 'unauthorized'): ?>
                    <script>
                        alert("You are not allowed to edit or delete this program because you did not create it.");
                        window.history.replaceState({}, document.title, window.location.pathname);
                    </script>
                    <?php endif; ?>

                </div>
            </form>
        </section>
    </main>
    
    <footer>
        Privacy Policy | Energy FM © 2025
    </footer>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        toggleDJFields();
    });
    </script>

</body>
</html> 
