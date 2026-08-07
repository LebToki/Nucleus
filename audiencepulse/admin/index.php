<?php
require_once __DIR__ . '/../includes/config.php';

// Handle locale switch
if (isset($_GET['locale'])) {
    setAppLocale($_GET['locale']);
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

$title = t('dashboard.title');
$subTitle = t('dashboard.subtitle');
$stats = getDashboardStats();
$recentVotes = getRecentVotes(10);
$liveShows = dbAll("SELECT * FROM shows WHERE status = 'live'");
$openPolls = dbAll("SELECT p.*, s.name as show_name, s.name_ar as show_name_ar FROM polls p JOIN shows s ON s.id = p.show_id WHERE p.status = 'open'");
?>

<?php include __DIR__ . '/partials/layouts/layoutTop.php'; ?>

<!-- Summary Cards -->
<div class="row row-cols-xxxl-5 row-cols-lg-4 row-cols-sm-2 row-cols-1 gy-4">
	<div class="col">
		<div class="card shadow-none border bg-gradient-start-1 h-100 stat-card">
			<div class="card-body p-20">
				<div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
					<div>
						<p class="fw-medium text-primary-light mb-1"><?= e(t('dashboard.total_shows')) ?></p>
						<h6 class="mb-0"><?= fmt($stats['total_shows']) ?></h6>
					</div>
					<div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
						<iconify-icon icon="ri:tv-line" class="text-white text-2xl mb-0"></iconify-icon>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card shadow-none border bg-gradient-start-2 h-100 stat-card">
			<div class="card-body p-20">
				<div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
					<div>
						<p class="fw-medium text-primary-light mb-1"><?= e(t('dashboard.live_shows')) ?></p>
						<h6 class="mb-0 text-success"><?= fmt($stats['live_shows']) ?></h6>
					</div>
					<div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
						<iconify-icon icon="ri:live-line" class="text-white text-2xl mb-0"></iconify-icon>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card shadow-none border bg-gradient-start-3 h-100 stat-card">
			<div class="card-body p-20">
				<div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
					<div>
						<p class="fw-medium text-primary-light mb-1"><?= e(t('dashboard.total_polls')) ?></p>
						<h6 class="mb-0"><?= fmt($stats['total_polls']) ?></h6>
					</div>
					<div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
						<iconify-icon icon="ri:bar-chart-line" class="text-white text-2xl mb-0"></iconify-icon>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card shadow-none border bg-gradient-start-4 h-100 stat-card">
			<div class="card-body p-20">
				<div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
					<div>
						<p class="fw-medium text-primary-light mb-1"><?= e(t('dashboard.total_votes')) ?></p>
						<h6 class="mb-0"><?= fmt($stats['total_votes']) ?></h6>
					</div>
					<div class="w-50-px h-50-px bg-warning rounded-circle d-flex justify-content-center align-items-center">
						<iconify-icon icon="ri:vote-line" class="text-white text-2xl mb-0"></iconify-icon>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card shadow-none border bg-gradient-start-5 h-100 stat-card">
			<div class="card-body p-20">
				<div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
					<div>
						<p class="fw-medium text-primary-light mb-1"><?= e(t('dashboard.total_voters')) ?></p>
						<h6 class="mb-0"><?= fmt($stats['total_voters']) ?></h6>
					</div>
					<div class="w-50-px h-50-px bg-danger rounded-circle d-flex justify-content-center align-items-center">
						<iconify-icon icon="ri:user-heart-line" class="text-white text-2xl mb-0"></iconify-icon>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Moderation Stats -->
<div class="row mt-4">
	<div class="col-md-6">
		<div class="card shadow-none border">
			<div class="card-body p-20">
				<div class="d-flex align-items-center justify-content-between">
					<div>
						<p class="fw-medium text-primary-light mb-1"><?= e(t('dashboard.pending_messages')) ?></p>
						<h6 class="mb-0 text-warning"><?= fmt($stats['pending_messages']) ?></h6>
					</div>
					<div class="w-50-px h-50-px bg-warning-subtle rounded-circle d-flex justify-content-center align-items-center">
						<iconify-icon icon="ri:time-line" class="text-warning text-2xl mb-0"></iconify-icon>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="card shadow-none border">
			<div class="card-body p-20">
				<div class="d-flex align-items-center justify-content-between">
					<div>
						<p class="fw-medium text-primary-light mb-1"><?= e(t('dashboard.approved_messages')) ?></p>
						<h6 class="mb-0 text-success"><?= fmt($stats['approved_messages']) ?></h6>
					</div>
					<div class="w-50-px h-50-px bg-success-subtle rounded-circle d-flex justify-content-center align-items-center">
						<iconify-icon icon="ri:chat-check-line" class="text-success text-2xl mb-0"></iconify-icon>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Live Shows & Open Polls -->
<div class="row mt-4">
	<div class="col-lg-6">
		<div class="card shadow-none border">
			<div class="card-header">
				<h6 class="mb-0"><?= e(t('dashboard.live_shows')) ?></h6>
			</div>
			<div class="card-body">
				<?php if (empty($liveShows)): ?>
					<p class="text-muted mb-0"><?= e(t('shows.no_shows')) ?></p>
				<?php else: ?>
					<?php foreach ($liveShows as $show): ?>
						<div class="d-flex align-items-center justify-content-between py-2 border-bottom">
							<div class="d-flex align-items-center gap-2">
								<img src="<?= e(tvShowImage($show)) ?>" alt="<?= e(l10n($show, 'name')) ?>" class="w-40-px h-40-px rounded-circle object-fit-cover flex-shrink-0">
								<div>
									<h6 class="mb-0"><?= e(l10n($show, 'name')) ?></h6>
									<small class="text-muted"><?= e($show['whatsapp_number']) ?></small>
								</div>
							</div>
							<span class="badge bg-success live-badge"><?= e(t('status.live')) ?></span>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="card shadow-none border">
			<div class="card-header">
				<h6 class="mb-0"><?= e(t('dashboard.open_polls')) ?></h6>
			</div>
			<div class="card-body">
				<?php if (empty($openPolls)): ?>
					<p class="text-muted mb-0"><?= e(t('polls.title')) ?> — <?= e(t('status.closed')) ?></p>
				<?php else: ?>
					<?php foreach ($openPolls as $poll): ?>
						<div class="d-flex align-items-center justify-content-between py-2 border-bottom">
							<div>
								<h6 class="mb-0"><?= e(l10n($poll, 'question')) ?></h6>
								<small class="text-muted"><?= e(l10n($poll, 'show_name')) ?></small>
							</div>
							<span class="badge bg-success"><?= e(t('status.open')) ?></span>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<!-- Recent Votes -->
<div class="row mt-4">
	<div class="col-12">
		<div class="card shadow-none border">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h6 class="mb-0"><?= e(t('dashboard.recent_votes')) ?></h6>
				<span class="badge bg-primary live-badge"><?= e(t('dashboard.live_feed')) ?></span>
			</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table basic-table mb-0">
						<thead>
							<tr>
								<th><?= e(t('dashboard.voter')) ?></th>
								<th><?= e(t('dashboard.option')) ?></th>
								<th><?= e(t('dashboard.time')) ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($recentVotes as $vote): ?>
								<tr>
									<td class="avatar-container">
										<?php
											// Determine avatar path based on available IDs (Category > Viewer > Default)
											$category_id = $vote['category_id'] ?? null;
											$viewer_id = $vote['viewer_id'] ?? ($vote['voter_id'] ?? null);

											if ($category_id) {
												$avatar_path = "admin/assets/images/categories/{$category_id}.jpg";
											} elseif ($viewer_id) {
												$avatar_path = "admin/assets/images/viewers/{$viewer_id}.jpg";
											} else {
												// Fallback if no specific ID is available for the voter
												$avatar_path = "admin/assets/images/default.jpg"; 
											}
										?>
										<img src="<?= e($avatar_path) ?>" alt="Avatar" style="width: 30px; height: 30px; object-fit: cover;">
									</td>
									<td><?= e(l10n($vote, 'option_text')) ?></td>
									<td><small class="text-muted"><?= e(timeAgo($vote['created_at'])) ?></small></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include __DIR__ . '/partials/layouts/layoutBottom.php'; ?>