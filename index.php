<?php
session_start();
include 'backend/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Energy FM 106.3</title>
    <link href="frontend/css/landing-page.css" rel="stylesheet">
    <script src = "frontend/js/index.js"></script>
</head>
<body class="landing-page">

    <div class="landing-page-header">
        <div class="header-logo-row">
            <a href="index.php">
                <img src="frontend/images/logo.png" alt="Energy FM 106.3 Naga Logo" class="logo-landing-page">
            </a>
            <h2 class="station-title">Energy FM Naga</h2>
        </div>

        <div class="header-line"></div>
    </div>

    <h1 class="welcome-title">Welcome to <span>ENERGYCONNECT</span></h1>

    <div class="continue">

        <!-- shown first-->
        <section id="main-continue" class="continue-section">
            <h1>Continue as</h1>

            <div class="button-row">
                <a href="home.php" class="user-button">a User</a>
                <button id="show-admin" class="admin-button" type="button">an Admin</button>
            </div>
        </section>

        <!-- admin login (hidden at first) -->
        <section id="admin-login" class="admin-section" style="display:none;">
            <h1 id="form-title">Login</h1>

            <form id="admin-form" class="admin-form" method="POST" action="backend/admin-auth.php">
                <input type="hidden" name="step" id="step" value="login">

                <label id="input-label">Email / Username</label>
                <input type="text" id="email" name="email">

                <label id="password-label">Password</label>
                <input type="password" id="password" name="password">

                <div class="login">
                    <button type="submit" class="login-button" id="login-btn">Continue</button>
                </div>

                <p class="forgot-password" id="forgot-wrapper">
                    <a href="#" id="forgot-password">Forgot password?</a>
                </p>

                <?php if (!empty($_SESSION['show_reset_password'])): ?>
                <script>
                document.addEventListener("DOMContentLoaded", () => {
                    document.getElementById("show-admin").click();
                    showResetPassword();
                });
                </script>
                <?php unset($_SESSION['show_reset_password']); endif; ?>

                <p class="request-access">
                    <a href="#" id="request-access">First-time admin? Request access link.</a>
                </p>

            </form>

            <?php if (!empty($_SESSION['login_error'])): ?>
            <script>
                alert("<?= addslashes($_SESSION['login_error']) ?>");
            </script>
            <?php unset($_SESSION['login_error']); endif; ?>

        </section>

        <!-- shows the set credentials part -->
        <?php if (!empty($_SESSION['show_set_credentials'])): ?>
        <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById("show-admin").click();
            showSetCredentials();
        });
        </script>
        <?php unset($_SESSION['show_set_credentials']); endif; ?>

</body>
</html>
