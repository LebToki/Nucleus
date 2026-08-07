<?php
require_once __DIR__ . '/../includes/config.php';

if (isset($_GET['locale'])) { setAppLocale($_GET['locale']); header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?')); exit; }

// Handle moderation actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $messageId = (int)($_POST['id'] ?? 0);
    $notes = $_POST['notes'] ?? '';
    if ($action === 'approve') { updateMessageStatus($messageId, 'approved', $notes); }
    if ($action === 'reject') { updateMessageStatus($messageId, 'rejected', $notes); }
    if ($action === 'hold') { updateMessageStatus($messageId, 'held', $notes); }
    header('Location: moderation.php'); exit;
}

$title = t('moderation.title');
$subTitle = t('app.tagline');
$shows = getShows();
$selectedShow = (int)($_GET['show'] ?? ($shows[0]['id'] ?? 0));
$filter = $_GET['filter'] ?? 'pending';
$messages = $selectedShow ? getMessages($selectedShow, $filter) : [];
?>

<?php include __DIR__ . '/partials/layouts/layoutTop.php'; ?>

<div class="row mb-3">
	<div class="col-md-6">
		<select class="form-select" onchange="location.href='?show='+this.value+'&filter=<?= e($filter) ?>'">
			<?php foreach ($shows as $show): ?>
				<option value="<?= $show['id'] ?>" <?= $selectedShow == $show['id'] ? 'selected' : '' ?>><?= e(l10n($show, 'name')) ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="col-md-6">
		<div class="btn-group w-100">
			<a href="?show=<?= $selectedShow ?>&filter=pending" class="btn btn-sm <?= $filter == 'pending' ? 'btn-warning' : 'btn-outline-warning' ?>"><?= e(t('moderation.pending')) ?></a>
			<a href="?show=<?= $selectedShow ?>&filter=approved" class="btn btn-sm <?= $filter == 'approved' ? 'btn-success' : 'btn-outline-success' ?>"><?= e(t('moderation.approved')) ?></a>
			<a href="?show=<?= $selectedShow ?>&filter=rejected" class="btn btn-sm <?= $filter == 'rejected' ? 'btn-danger' : 'btn-outline-danger' ?>"><?= e(t('moderation.rejected')) ?></a>
			<a href="?show=<?= $selectedShow ?>&filter=held" class="btn btn-sm <?= $filter == 'held' ? 'btn-info' : 'btn-outline-info' ?>"><?= e(t('moderation.held')) ?></a>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card shadow-none border">
			<div class="card-header">
				<h6 class="mb-0"><?= e(t('moderation.title')) ?> — <?= e(statusLabel($filter)) ?></h6>
			</div>
			<div class="card-body p-0">
				<?php if (empty($messages)): ?>
					<p class="text-muted p-3 mb-0"><?= e(t('moderation.no_messages')) ?></p>
				<?php else: ?>
					<div class="table-responsive">
						<table class="table basic-table mb-0">
							<thead>
								<tr>
									<th><?= e(t('moderation.content')) ?></th>
									<th><?= e(t('moderation.voter')) ?></th>
									<th><?= e(t('moderation.time')) ?></th>
									<th><?= e(t('shows.actions')) ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($messages as $msg): ?>
									<tr>
										<td>
											<strong><?= e(l10n($msg, 'content')) ?></strong>
											<?php if ($msg['moderator_notes']): ?>
												<small class="d-block text-muted"><?= e($msg['moderator_notes']) ?></small>
											<?php endif; ?>
										</td>
										<td>
											<div class="d-flex align-items-center gap-2">
												<img src="<?= e(viewerAvatar(['avatar' => $msg['voter_avatar'] ?? ''])) ?>" alt="<?= e($msg['voter_name'] ?: 'voter') ?>" class="w-40-px h-40-px rounded-circle object-fit-cover flex-shrink-0">
												<span><?= e($msg['voter_name'] ?: $msg['voter_phone']) ?></span>
											</div>
										</td>
										<td><small class="text-muted"><?= e(timeAgo($msg['created_at'])) ?></small></td>
										<td>
											<?php if ($msg['status'] !== 'approved'): ?>
												<form method="POST" class="d-inline">
													<input type="hidden" name="action" value="approve">
													<input type="hidden" name="id" value="<?= $msg['id'] ?>">
													<button type="submit" class="btn btn-sm btn-success"><iconify-icon icon="ri:check-line"></iconify-icon></button>
												</form>
											<?php endif; ?>
											<?php if ($msg['status'] !== 'rejected'): ?>
												<form method="POST" class="d-inline">
													<input type="hidden" name="action" value="reject">
													<input type="hidden" name="id" value="<?= $msg['id'] ?>">
													<button type="submit" class="btn btn-sm btn-danger"><iconify-icon icon="ri:close-line"></iconify-icon></button>
												</form>
											<?php endif; ?>
											<?php if ($msg['status'] !== 'held'): ?>
												<form method="POST" class="d-inline">
													<input type="hidden" name="action" value="hold">
													<input type="hidden" name="id" value="<?= $msg['id'] ?>">
													<button type="submit" class="btn btn-sm btn-info"><iconify-icon icon="ri:pause-line"></iconify-icon></button>
												</form>
											<?php endif; ?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php include __DIR__ . '/partials/layouts/layoutBottom.php'; ?>
