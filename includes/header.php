<?php
$page_title = $page_title ?? 'AdsMarket.nl - Google Ads Specialist';
$page_description = $page_description ?? 'AdsMarket - Specialist in Google Ads campagnes.';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="premium-effects.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container nav-container">
            <div class="logo">
                <a href="index.php" class="logo-text">Ads<span class="logo-highlight">Market</span></a>
            </div>
            <ul class="nav-menu" id="navMenu">
                <li><a href="index.php" class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a></li>
                <li><a href="diensten.php" class="nav-link <?php echo ($current_page == 'diensten.php') ? 'active' : ''; ?>">Diensten</a></li>
                <li><a href="over-ons.php" class="nav-link <?php echo ($current_page == 'over-ons.php') ? 'active' : ''; ?>">Over Ons</a></li>
                <li><a href="resultaten.php" class="nav-link <?php echo ($current_page == 'resultaten.php') ? 'active' : ''; ?>">Resultaten</a></li>
                <li><a href="contact.php" class="nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">Contact</a></li>
            </ul>
            <a href="contact.php" class="btn btn-primary nav-cta">Gratis Consult</a>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>
