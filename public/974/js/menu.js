document.getElementById("lang-selector").addEventListener("click", function() {
    const dropdown = document.getElementById("lang-dropdown");
    if (dropdown.style.display === "none" || dropdown.style.display === "") {
        dropdown.style.display = "block";
    } else {
        dropdown.style.display = "none";
    }
});
