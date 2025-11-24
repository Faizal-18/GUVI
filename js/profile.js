$(function () {
    const token = localStorage.getItem("sessionToken");

    if (!token) {
        window.location.href = "login.html";
        return;
    }

    function showAlert(type, msg) {
        $("#profile-alert")
            .removeClass("d-none alert-success alert-danger")
            .addClass(type === "success" ? "alert-success" : "alert-danger")
            .text(msg);
    }

    // Fetch profile
    $.ajax({
        url: "php/profile.php",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({ action: "get", token }),
        success: function (res) {
            if (res.success) {
                $("#name").val(res.user.name);
                $("#email").val(res.user.email);
                $("#age").val(res.user.age);
                $("#dob").val(res.user.dob);
                $("#contact").val(res.user.contact);
            } else {
                showAlert("error", res.message);
                setTimeout(() => window.location.href = "login.html", 1200);
            }
        }
    });

    // Save changes
    $("#profile-form").on("submit", function (e) {
        e.preventDefault();

        const data = {
            action: "update",
            token,
            name: $("#name").val(),
            age: $("#age").val(),
            dob: $("#dob").val(),
            contact: $("#contact").val()
        };

        $.ajax({
            url: "php/profile.php",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify(data),
            success: function (res) {
                if (res.success) showAlert("success", res.message);
                else showAlert("error", res.message);
            }
        });
    });

    // Logout
    $("#logout-btn").on("click", function () {
        $.ajax({
            url: "php/profile.php",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({ action: "logout", token }),
            complete: function () {
                localStorage.removeItem("sessionToken");
                window.location.href = "login.html";
            }
        });
    });
});
