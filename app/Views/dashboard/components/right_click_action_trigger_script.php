<script>
    document.addEventListener("contextmenu", function (e) {
        const row = e.target.closest(".item-row");
        if (!row) return;

        e.preventDefault();

        // Get the dropdown toggle button in this row
        const dropdownToggle = row.querySelector('.dropdown-toggle');

        if (dropdownToggle) {
            // Close any other open dropdown
            document.querySelectorAll(".dropdown-menu.show").forEach(menu => {
                menu.classList.remove("show");
            });

            // Force toggle its dropdown
            const dropdown = bootstrap.Dropdown.getOrCreateInstance(dropdownToggle);
            dropdown.show();

            // Position the dropdown at the cursor
            const menu = row.querySelector(".dropdown-menu");
            menu.style.position = "fixed";
            menu.style.top = e.clientY + "px";
            menu.style.left = e.clientX + "px";
        }
    });

</script>