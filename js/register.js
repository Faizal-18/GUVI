$(function () {
    $("#register-form").on("submit", function (e) {
        e.preventDefault();

        const data = {
            name: $("#name").val(),
            email: $("#email").val(),
            password: $("#password").val(),
            age: $("#age").val(),
            dob: $("#dob").val(),
            contact: $("#contact").val()
        };

        $("#register-alert").addClass("d-none");

        $.ajax({
            url: "php/register.php",
            method: "POST",
            data: JSON.stringify(data),
            contentType: "application/json",
            dataType: "json",        // <-- IMPORTANT
            processData: false,      // <-- SUPER IMPORTANT (prevents jQuery from breaking JSON)
            success: function (res) {
                if (res.success) {
                    $("#register-alert")
                        .removeClass("d-none alert-danger")
                        .addClass("alert-success")
                        .text(res.message);

                    setTimeout(() => {
                        window.location.href = "login.html";
                    }, 900);
                } else {
                    $("#register-alert")
                        .removeClass("d-none alert-success")
                        .addClass("alert-danger")
                        .text(res.message);
                }
            }
        });
    });
});
