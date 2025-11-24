$(function () {
    $("#login-form").on("submit", function (e) {
        e.preventDefault();

        const data = {
            email: $("#email").val(),
            password: $("#password").val()
        };

        $("#login-alert").addClass("d-none");

        $.ajax({
            url: "php/login.php",
            method: "POST",
            data: JSON.stringify(data),
            contentType: "application/json",
            dataType: "json",
            processData: false,
            success: function (res) {
                if (res.success) {
                    localStorage.setItem("sessionToken", res.token);
                    window.location.href = "profile.html";
                } else {
                    $("#login-alert")
                        .removeClass("d-none alert-success")
                        .addClass("alert-danger")
                        .text(res.message);
                }
            }
        });
    });
});
