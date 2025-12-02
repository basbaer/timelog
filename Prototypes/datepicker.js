document.addEventListener("DOMContentLoaded", function () {
    const el = document.getElementById("date");
    if (el) {
        $(el).datepicker({
            format: "yyyy-mm-dd",
            autoclose: true,
            todayHighlight: true
        });
    }
});
