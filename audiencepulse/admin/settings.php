<?php
require_once __DIR__ . '/../includes/config.php';

if (isset($_GET['locale'])) { setAppLocale($_GET['locale']); header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?')); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = ['brand_name', 'brand_name_ar', 'whatsapp_number', 'default_locale'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            dbExec('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)', [$field, $_POST[$field]]);
        }
    }
    header('Location: settings.php?saved=1'); exit;
}

$title = t('settings.title');
$subTitle = t('app.tagline');
$saved = isset($_GET['saved']);
?>

<?php include __DIR__ . '/partials/layouts/layoutTop.php'; ?>

<?php if ($saved): ?>
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<?= e(t('settings.saved')) ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
<?php endif; ?>

<div class="row">
	<div class="col-lg-8">
		<div class="card shadow-none border">
			<div class="card-header">
				<h6 class="mb-0"><?= e(t('settings.title')) ?></h6>
			</div>
			<div class="card-body">
				<form method="POST">
					<div class="mb-3">
						<label class="form-label"><?= e(t('settings.brand_name')) ?></label>
						<input type="text" class="form-control" name="brand_name" value="<?= e(getSetting('brand_name', 'AudiencePulse')) ?>">
					</div>
					<div class="mb-3">
						<label class="form-label"><?= e(t('settings.brand_name_ar')) ?></label>
						<input type="text" class="form-control" name="brand_name_ar" value="<?= e(getSetting('brand_name_ar', 'نبض الجمهور')) ?>" dir="rtl">
					</div>
					<div class="mb-3">
						<label class="form-label"><?= e(t('settings.whatsapp_number')) ?></label>
						<input type="text" class="form-control" name="whatsapp_number" value="<?= e(getSetting('whatsapp_number', '')) ?>">
					</div>
					<div class="mb-3">
						<label class="form-label"><?= e(t('settings.default_locale')) ?></label>
						<select class="form-select" name="default_locale">
							<option value="en" <?= getSetting('default_locale', 'en') == 'en' ? 'selected' : '' ?>>English</option>
							<option value="ar" <?= getSetting('default_locale', 'en') == 'ar' ? 'selected' : '' ?>>العربية</option>
						</select>
					</div>
					<button type="submit" class="btn btn-primary"><?= e(t('settings.save')) ?></button>
				</form>
			</div>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="card shadow-none border">
			<div class="card-header">
				<h6 class="mb-0"><?= e(APP_COMPANY) ?></h6>
			</div>
			<div class="card-body">
				<p class="mb-1"><strong><?= e(t('app.name')) ?></strong> v<?= e(APP_VERSION) ?></p>
				<p class="mb-1"><?= e(t('app.tagline')) ?></p>
				<p class="mb-0"><a href="https://<?= e(APP_COMPANY_URL) ?>" target="_blank"><?= e(APP_COMPANY_URL) ?></a></p>
			</div>
		</div>
	</div>
</div>

<?php include __DIR__ . '/partials/layouts/layoutBottom.php'; ?>
