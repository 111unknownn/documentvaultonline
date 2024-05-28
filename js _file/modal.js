// Open the modal when clicking the About button
document.addEventListener("DOMContentLoaded", function () {
    var aboutButton = document.querySelector("[data-target='#aboutModal']");
    var modal = document.getElementById("aboutModal");
    var closeButton = document.querySelector(".close");

    aboutButton.addEventListener("click", function () {
        modal.style.display = "block";
    });

    closeButton.addEventListener("click", function () {
        modal.style.display = "none";
    });

    window.addEventListener("click", function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });
});
