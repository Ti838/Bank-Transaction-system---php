<?php
// Test file to check navbar script
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Theme Test</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            transition: all 0.3s;
            padding: 20px;
        }

        body.light-theme {
            background: white;
            color: black;
        }

        .theme-btn {
            padding: 10px 20px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <h1>Theme Toggle Test</h1>
    <button id="theme-toggle" class="theme-btn">
        <i class="fas fa-moon"></i> Toggle Theme
    </button>

    <script>
        const themeToggle = document.getElementById('theme-toggle');

        if (themeToggle) {
            const icon = themeToggle.querySelector('i');

            // Load saved theme
            if (localStorage.getItem('theme') === 'light') {
                document.body.classList.add('light-theme');
                if (icon) icon.classList.replace('fa-moon', 'fa-sun');
            }

            // Toggle on click
            themeToggle.addEventListener('click', () => {
                document.body.classList.toggle('light-theme');

                if (document.body.classList.contains('light-theme')) {
                    localStorage.setItem('theme', 'light');
                    if (icon) icon.classList.replace('fa-moon', 'fa-sun');
                } else {
                    localStorage.setItem('theme', 'dark');
                    if (icon) icon.classList.replace('fa-sun', 'fa-moon');
                }
            });
        }
    </script>
</body>

</html>