<?php
// Ensure constants are defined - use absolute paths from web root
if (!defined('ASSETS_URL')) {
    // Fallback calculation if not defined
    $baseUrl = defined('BASE_URL') ? BASE_URL : '';
    $assetsUrl = $baseUrl === '' ? '/assets' : $baseUrl . '/assets';
} else {
    $assetsUrl = ASSETS_URL;
}

// Ensure ASSETS_URL is always absolute (starts with /)
if (substr($assetsUrl, 0, 1) !== '/') {
    $assetsUrl = '/' . $assetsUrl;
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Primary Meta Tags -->
<title>Nucleus — The Missing Dashboard for Linux Developers</title>
<meta name="title" content="Nucleus — The Missing Dashboard for Linux Developers">
<meta name="description" content="Lightweight central control panel for Linux development environments. Service management, project detection, database tools, and server monitoring for ZorinOS, Ubuntu, and Mint.">
<meta name="keywords" content="nucleus, dashboard, linux, development environment, zorinos, ubuntu, mint, php, apache, nginx, mysql, systemd, web development, local server">
<meta name="author" content="Tarek Tarabichi, 2TInteractive">
<meta name="robots" content="index, follow">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="https://github.com/LebToki/Nucleus/">
<meta property="og:title" content="Nucleus — The Missing Dashboard for Linux Developers">
<meta property="og:description" content="Lightweight central control panel for Linux development environments. Service management, project detection, database tools, and server monitoring for ZorinOS, Ubuntu, and Mint.">
<meta property="og:image" content="/assets/images/nucleus-og.png">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="https://github.com/LebToki/Nucleus/">
<meta property="twitter:title" content="Nucleus — The Missing Dashboard for Linux Developers">
<meta property="twitter:description" content="Lightweight central control panel for Linux development environments. Service management, project detection, database tools, and server monitoring for ZorinOS, Ubuntu, and Mint.">
<meta property="twitter:image" content="/assets/images/nucleus-og.png">

<!-- Additional Meta Tags -->
<meta name="theme-color" content="#4f46e5">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">

<!-- Favicons -->
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $assetsUrl; ?>/images/favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $assetsUrl; ?>/images/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $assetsUrl; ?>/images/favicon/favicon-16x16.png">
<link rel="manifest" href="<?php echo $assetsUrl; ?>/images/favicon/site.webmanifest">
<link rel="shortcut icon" href="<?php echo $assetsUrl; ?>/images/favicon/favicon.ico">
    <!-- remix icon font css  -->
    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>/css/remixicon.css">
    <!-- BootStrap css -->
    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>/css/lib/bootstrap.min.css">
    <!-- Apex Chart css -->
    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>/css/lib/apexcharts.css">
    <!-- Data Table css -->
    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>/css/lib/dataTables.min.css">
    <!-- Text Editor css -->
    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>/css/lib/editor-katex.min.css">
    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>/css/lib/editor.atom-one-dark.min.css">
    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>/css/lib/editor.quill.snow.css">
    <!-- Date picker css -->
    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>/css/lib/flatpickr.min.css">
    <!-- Calendar css -->
    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>/css/lib/full-calendar.css">
    <!-- Vector Map css -->
    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>/css/lib/jquery-jvectormap-2.0.5.css">
    <!-- Popup css -->
    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>/css/lib/magnific-popup.css">
    <!-- Slick Slider css -->
    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>/css/lib/slick.css">
    <!-- prism css -->
    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>/css/lib/prism.css">
    <!-- CodeMirror css -->
    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>/css/lib/codemirror.min.css">
    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>/css/lib/theme/monokai.min.css">
    <!-- file upload css -->
    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>/css/lib/file-upload.css">

    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>/css/lib/audioplayer.css">
    <!-- Monochrome Mode css -->
    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>/css/lib/monochrome-mode.css">
    <!-- main css -->
    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>/css/style.css">
</head>