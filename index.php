<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carl Alfarero - Portfolio</title>
    <link rel="stylesheet" href="css/style.css?v=20261017-nav1">
    <link rel="shortcut icon" href="images/favicon/ca_logo.png" type="image/x-icon">
</head>
<body>
    <input type="checkbox" id="modal-toggle" class="modal-toggle-hidden">
    <header>
        <nav class="navbar">
            <div class="nav-container">
                <a href="index.php"><img src="images/carl_logo.png" alt="Carl Alfarero Logo"></a>
                <button class="nav-toggle" type="button" aria-label="Open navigation menu" aria-expanded="false" aria-controls="primary-navigation">
                    <span class="nav-toggle-icon" aria-hidden="true"></span>
                </button>
                <div class="nav-menu" id="primary-navigation">
                    <a href="index.php">Home</a>
                    <a href="about.php">About</a>
                    <a href="works.php">Works</a>
                    <a href="contact.php">Contact</a>
                </div>
            </div>
        </nav>
    </header>
    <main>
        <section class="home-text">
            <h1>Carl Alfarero</h1>
            <h2>Networking & IT Enthusiast</h2>
            <p>
                Passionate about creating network systems and exploring various aspects of IT.
            </p>
            <div>
                <div>
                    <label for="modal-toggle" class="primary-btn">Demo Reel</label>
                </div>
                <div>
                    <a href="contact.php" class="secondary-btn">Contact me</a>
                </div>
            </div>
        </section>
    </main>
    <label for="modal-toggle" class="modal">
        <video src="./videos/carl_demo_intro.mp4" controls class="modal-video"></video>
    </label>
    <footer>
        <p>&copy; 2026 Carl Alfarero. All rights reserved</p>
    </footer>
    <script src="js/navigation.js?v=20261017-nav1"></script>

    <!-- View Resume Button -->
    <a href="certificate/Carl_Alfarero_Resume.pdf" target="_blank" title="View Resume" class="resume-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/>
        </svg>
        View Resume
    </a>
</body>
</html>