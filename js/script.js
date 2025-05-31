document.addEventListener("DOMContentLoaded", function() {
    // Sidebar toggle for mobile
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.querySelector(".d-md-none");
    if (toggleBtn) {
        toggleBtn.addEventListener("click", () => {
            sidebar.classList.toggle("active");
        });
    }

    // Button animations
    const buttons = document.querySelectorAll(".btn");
    buttons.forEach(button => {
        button.addEventListener("mouseenter", () => {
            button.classList.add("bounce");
        });
        button.addEventListener("mouseleave", () => {
            button.classList.remove("bounce");
        });
    });

    // Smooth scroll for sidebar links
    const links = document.querySelectorAll(".nav-link");
    links.forEach(link => {
        link.addEventListener("click", function(e) {
            e.preventDefault();
            const href = this.getAttribute("href");
            window.location.href = href;
        });
    });

    // Table row hover effect
    const rows = document.querySelectorAll(".table-hover tbody tr");
    rows.forEach(row => {
        row.addEventListener("mouseenter", () => {
            row.classList.add("pulse-hover");
        });
        row.addEventListener("mouseleave", () => {
            row.classList.remove("pulse-hover");
        });
    });

    console.log("Enhanced UI/UX loaded");
});
document.addEventListener("DOMContentLoaded", function() {
    // Sidebar toggle for mobile
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.querySelector(".d-md-none");
    if (toggleBtn) {
        toggleBtn.addEventListener("click", () => {
            sidebar.classList.toggle("active");
        });
    }

    // Button animations
    const buttons = document.querySelectorAll(".btn");
    buttons.forEach(button => {
        button.addEventListener("mouseenter", () => {
            button.classList.add("bounce");
        });
        button.addEventListener("mouseleave", () => {
            button.classList.remove("bounce");
        });
    });

    // Table row hover effect (for pages with tables)
    const rows = document.querySelectorAll(".table-hover tbody tr");
    rows.forEach(row => {
        row.addEventListener("mouseenter", () => {
            row.classList.add("pulse-hover");
        });
        row.addEventListener("mouseleave", () => {
            row.classList.remove("pulse-hover");
        });
    });

    // Remove navigation interference for sidebar links
    // Previously, this prevented default behavior; now it’s removed
    console.log("Student Dashboard UI loaded");
});