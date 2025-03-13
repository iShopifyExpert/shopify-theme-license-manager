document.addEventListener("DOMContentLoaded", function () {
    const LICENSE_KEY = "ABC123-XYZ789"; // Change this based on the store
    const API_URL = "/license-system/check_license.php"; // Adjust path as needed

    fetch(API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ license: LICENSE_KEY })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status !== "success") {
            alert("License Error: " + data.message);
            console.error("License Error:", data.message);
        } else {
            console.log("License Valid: ", data.message);
        }
    })
    .catch(error => console.error("License Check Failed:", error));
});
