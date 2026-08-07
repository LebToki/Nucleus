<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= e(t('app.name')) ?> - <?= e(t('app.tagline')) ?></title>
	<link rel="icon" type="image/png" href="assets/images/favicon.png" sizes="16x16">
	<meta name="author" content="<?= e(APP_COMPANY) ?>">
	<meta name="description" content="Live Audience Interaction System by <?= e(APP_COMPANY) ?>">
	<meta name="robots" content="noindex, nofollow">

	<!-- remix icon font css  -->
	<link rel="stylesheet" href="assets/css/remixicon.css">
	<!-- Cairo Arabic Font css -->
	<link rel="stylesheet" href="assets/css/cairo.css">
	<!-- Tajawal Arabic Font css -->
	<link rel="stylesheet" href="assets/css/tajawal.css">
	<!-- BootStrap css -->
	<link rel="stylesheet" href="assets/css/lib/bootstrap.min.css">
	<!-- Apex Chart css -->
	<link rel="stylesheet" href="assets/css/lib/apexcharts.css">
	<!-- Data Table css -->
	<link rel="stylesheet" href="assets/css/lib/dataTables.min.css">
	<!-- main css -->
	<link rel="stylesheet" href="assets/css/style.css">
	<!-- admin enhancements css -->
	<link rel="stylesheet" href="assets/css/admin-enhancements.css">
	<script>
		// Apply saved theme before page render to prevent FOUC
		(function () {
			var theme = localStorage.getItem('theme') || 'light';
			document.documentElement.setAttribute('data-theme', theme);
		})();
	</script>
	<style>
		body { font-family: 'Cairo', 'Tajawal', sans-serif; }
		.brand-logo { font-weight: 700; font-size: 1.4rem; }
		.brand-logo .pulse-dot { color: #e74c3c; }
		.stat-card { transition: transform 0.2s; }
		.stat-card:hover { transform: translateY(-3px); }
		.live-badge { animation: pulse 2s infinite; }
		@keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
	</style>
</head>
