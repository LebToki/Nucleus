<?php
require_once __DIR__ . '/../includes/config.php';

if (isset($_GET['locale'])) { setAppLocale($_GET['locale']); header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?')); exit; }

$title = t('voters.title');
$subTitle = t('app.tagline');
$voters = dbAll('SELECT v.*, COUNT(vt.id) as vote_count FROM voters v LEFT JOIN votes vt ON vt.voter_id = v.id GROUP BY v.id ORDER BY v.created_at DESC');
?>

<?php include __DIR__ . '/partials/layouts/layoutTop.php'; ?>

<div class="row">
	<div class="col-12">
		<div class="card shadow-none border">
			<div class="card-header">
				<h6 class="mb-0"><?= e(t('voters.title')) ?></h6>
			</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table basic-table mb-0">
						<thead>
							<tr>
								<th>#</th>
								<th><?= e(t('voters.name')) ?></th>
								<th><?= e(t('voters.phone')) ?></th>
								<th><?= e(t('voters.country')) ?></th>
								<th><?= e(t('voters.votes')) ?></th>
								<th><?= e(t('voters.joined')) ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($voters as $voter): ?>
								<tr>
									<td><?= $voter['id'] ?></td>
									<td>
										<div class="d-flex align-items-center gap-2">
											<img src="<?= e(viewerAvatar($voter)) ?>" alt="<?= e($voter['name'] ?: 'voter') ?>" class="w-40-px h-40-px rounded-circle object-fit-cover flex-shrink-0">
											<span><?= e($voter['name'] ?: '—') ?></span>
										</div>
									</td>
									<td><?= e($voter['phone']) ?></td>
									<td>
										<div class="d-flex align-items-center gap-2">
											<img src="<?= e(countryFlag($voter['country'])) ?>" alt="<?= e($voter['country']) ?>" class="w-24-px h-24-px object-fit-cover rounded-circle flex-shrink-0">
											<span><?= e($voter['country'] ?: '—') ?></span>
										</div>
									</td>
									<td><span class="badge bg-primary"><?= fmt((int)$voter['vote_count']) ?></span></td>
									<td><small class="text-muted"><?= e(timeAgo($voter['created_at'])) ?></small></td>
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