/* landing page and login functionality */
document.addEventListener("DOMContentLoaded", () => {
    const forgotWrapper = document.getElementById("forgot-wrapper");
    const form = document.getElementById("admin-form");
    const stepField = document.getElementById("step");
    const inputField = document.getElementById("email");
    const passwordField = document.getElementById("password");
    const button = document.getElementById("login-btn");
    const inputLabel = document.getElementById("input-label");
    const formTitle = document.getElementById("form-title");
    const requestAccessWrapper = document.querySelector(".request-access");

    forgotWrapper.style.display = "none";
    let step = "login";

    // show admin login (main)
    document.getElementById("show-admin").addEventListener("click", () => {
        document.getElementById("main-continue").style.display = "none";
        const adminBox = document.getElementById("admin-login");
        adminBox.style.display = "block";

        document.querySelector(".continue").style.height = "450px";
        adminBox.scrollIntoView({ behavior: "smooth" });

        step = "login";
        stepField.value = "login";

        passwordField.disabled = false;
        passwordField.style.display = "block";
        document.getElementById("password-label").style.display = "block";

        button.innerText = "Continue";
        forgotWrapper.style.display = "block";
        requestAccessWrapper.style.display = "block";
    });

    // form submission
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        let emailTab = null;
        if (step === "request" || step === "forgot") {
            emailTab = window.open("", "_blank");
        }

        // admin request access
        if (step === "request") {

            fetch("request-invite.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.text())
            .then(response => {

                if (!response.startsWith("http")) {
                    if (emailTab) emailTab.close();
                    alert(response);
                    return;
                }

                emailTab.document.write(`
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                    <meta charset="UTF-8">
                    <title>Admin Invite</title>
                    </head>
                    <body style="font-family:Arial;padding:40px;">
                        <h2>Admin Invite</h2>
                        <p>Click below:</p>
                        <a href="${response}">${response}</a>
                    </body>
                    </html>`);

                emailTab.document.close();
            });

            return;
        }

        // admin forgots password
        if (step === "forgot") {

            fetch("request-reset.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.text())
            .then(response => {

                if (!response.startsWith("http")) {
                    if (emailTab) emailTab.close();
                    alert(response);
                    return;
                }

                emailTab.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head><title>Password Reset</title></head>
                    <body style="font-family:Arial;padding:40px;">
                        <h2>Password Reset</h2>
                        <p>Click below:</p>
                        <a href="${response}">${response}</a>
                    </body>
                    </html>`);

                emailTab.document.close();
            });

            return;
        }

        // logging in / setting credentials
        fetch("backend/admin-auth.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(response => {
            if (response === "success") {
                window.location.href = "admin-home.php";
            }
            else if (response === "reset_success") {
                alert("Password reset successful.") 
                window.location.replace("index.php");
            }
            else if (response === "setup") {
                showSetCredentials();
            }
            else {
                alert(response);
            }
        });
    });

    // set credentials UI
    window.showSetCredentials = function () {
        step = "set";
        stepField.value = "set";

        formTitle.innerText = "Set Your Credentials";
        inputLabel.innerText = "Username";

        inputField.value = "";
        passwordField.value = "";
        button.innerText = "Save Credentials";

        document.querySelector(".continue").style.height = "410px";
        forgotWrapper.style.display = "none";
        requestAccessWrapper.style.display = "none";
    };

    // request access ui
    document.getElementById("request-access").addEventListener("click", (e) => {
        e.preventDefault();

        step = "request";
        stepField.value = "request";

        formTitle.innerText = "Admin Sign In";
        inputLabel.innerText = "Email Address";

        inputField.value = "";
        passwordField.value = "";

        passwordField.style.display = "none";
        passwordField.disabled = true;
        document.getElementById("password-label").style.display = "none";

        button.innerText = "Request Access Link";

        forgotWrapper.style.display = "none";
        requestAccessWrapper.style.display = "none";

        document.querySelector(".continue").style.height = "330px";
    });

    // reset password UI
    document.getElementById("forgot-password").addEventListener("click", (e) => {
        e.preventDefault();

        step = "forgot";
        stepField.value = "forgot";

        formTitle.innerText = "Reset Password";
        inputLabel.innerText = "Email / Username";

        inputField.value = "";
        passwordField.value = "";

        passwordField.style.display = "none";
        passwordField.disabled = true;
        document.getElementById("password-label").style.display = "none";

        button.innerText = "Send Reset Link";

        forgotWrapper.style.display = "none";
        requestAccessWrapper.style.display = "none";

        document.querySelector(".continue").style.height = "330px";
    });

    // set new password UI
    window.showResetPassword = function () {
        step = "reset";
        stepField.value = "reset";

        formTitle.innerText = "Reset Password";

        inputField.value = "";
        inputField.disabled = true;
        inputField.style.display = "none";
        inputLabel.style.display = "none";

        passwordField.value = "";
        passwordField.disabled = false;
        passwordField.style.display = "block";
        document.getElementById("password-label").style.display = "block";

        button.innerText = "Save New Password";

        forgotWrapper.style.display = "none";
        requestAccessWrapper.style.display = "none";

        document.querySelector(".continue").style.height = "330px";
    };
});
