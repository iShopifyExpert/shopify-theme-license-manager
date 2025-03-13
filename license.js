document.addEventListener("DOMContentLoaded", async function () {
    const LICENSE_KEY = "ABC123-XYZ789"; // Change this based on the store
    const API_URL = "/license-system/check_license.php"; // Adjust path as needed
    const TIMEOUT_MS = 5000; // 5 seconds timeout
    const fetchWithTimeout = (url, options, timeout = TIMEOUT_MS) => {
        return Promise.race([
            fetch(url, options),
            new Promise((_, reject) => setTimeout(() => reject(new Error("Request Timeout")), timeout))
        ]);
    };
    try {
        const response = await fetchWithTimeout(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ license: LICENSE_KEY })
        });
        if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
        const data = await response.json();
        if (data.status !== "success") {
            console.warn("License Invalid:", data.message);
            showLicenseError();
        } else {
            console.log("License Valid:", data.message);
        }
    } catch (error) {
        console.error("License Check Failed:", error.message);
        showLicenseError();
    }
    function showLicenseError() {
        document.body.innerHTML = `
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
                        text-align: center; font-family: Arial, sans-serif; color: red;">
                Please go to the theme editor. You must perform an action there.
            </div>
        `;
    }
});

// document.addEventListener("DOMContentLoaded", function () {
//     const LICENSE_KEY = "ABC123-XYZ789"; // Change this based on the store
//     const API_URL = "/license-system/check_license.php"; // Adjust path as needed

//     fetch(API_URL, {
//         method: "POST",
//         headers: { "Content-Type": "application/json" },
//         body: JSON.stringify({ license: LICENSE_KEY })
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.status !== "success") {
//             alert("License Error: " + data.message);
//             console.error("License Error:", data.message);
//             document.body.innerHTML = '<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">Please go to the theme editor. You must perform an action there.</div>';
//         } else {
//             console.log("License Valid: ", data.message);
//         }
//     })
//     .catch(error => console.error("License Check Failed:", error));
// });
