<?php
require_once __DIR__ . '/../includes/config.php';

if (isset($_GET['locale'])) { setAppLocale($_GET['locale']); header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?')); exit; }

$title = t('graphics.title');
$subTitle = t('graphics.subtitle');
$polls = dbAll("SELECT p.*, s.name as show_name, s.name_ar as show_name_ar FROM polls p JOIN shows s ON s.id = p.show_id WHERE p.status = 'open' ORDER BY p.created_at DESC");
$selectedPoll = isset($_GET['poll']) ? getPoll((int)$_GET['poll']) : (isset($polls[0]['id']) ? getPoll((int)$polls[0]['id']) : null);
$results = $selectedPoll ? getPollResults($selectedPoll['id']) : [];
$totalVotes = $selectedPoll ? $selectedPoll['total_votes'] : 0;
?>

<?php include __DIR__ . '/partials/layouts/layoutTop.php'; ?>

<div class="row mb-3">
	<div class="col-md-6">
		<select class="form-select" onchange="location.href='?poll='+this.value">
			<?php foreach ($polls as $poll): ?>
				<option value="<?= $poll['id'] ?>" <?= $selectedPoll && $selectedPoll['id'] == $poll['id'] ? 'selected' : '' ?>><?= e(l10n($poll, 'question')) ?></option>
			<?php endforeach; ?>
		</select>
	</div>
</div>

<?php if ($selectedPoll): ?>
<div class="row">
	<div class="col-12">
		<!-- Live Graphics Preview — WOWDASH-compatible -->
		<div class="card">
			<div class="card-body p-24">
				<div class="text-center mb-4">
					<h4 class="fw-semibold mb-1"><?= e(l10n($selectedPoll, 'question')) ?></h4>
					<small class="text-muted"><?= e(t('graphics.total_votes')) ?>: <?= fmt($totalVotes) ?></small>
				</div>
				<?php foreach ($results as $result): ?>
					<?php $pct = $totalVotes > 0 ? round(($result['votes'] / $totalVotes) * 100) : 0; ?>
					<div class="mb-4">
						<div class="d-flex justify-content-between mb-2">
							<span class="fw-medium"><?= e(l10n($result, 'option_text')) ?></span>
							<span class="badge bg-primary-main text-white"><?= fmt((int)$result['votes']) ?> (<?= $pct ?>%)</span>
						</div>
						<div class="progress progress-sm">
							<div style="width: <?= $pct ?>%; height: 100%; background-color: var(--primary-main); border-radius: 8px;"></div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>

<!-- Auto-refresh every 5 seconds -->
<script>
setInterval(function() {
	location.reload();
}, 5000);
</script>
<?php endif; ?>

<?php include __DIR__ . '/partials/layouts/layoutBottom.php'; ?>
