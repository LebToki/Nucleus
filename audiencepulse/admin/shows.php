what<?php
require_once __DIR__ . '/../includes/config.php';

if (isset($_GET['locale'])) { setAppLocale($_GET['locale']); header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?')); exit; }

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $name = $_POST['name'] ?? '';
        $nameAr = $_POST['name_ar'] ?? '';
        $description = $_POST['description'] ?? '';
        $whatsapp = $_POST['whatsapp_number'] ?? '';
        $status = $_POST['status'] ?? 'draft';
        if ($id > 0) {
            dbExec('UPDATE shows SET name=?, name_ar=?, description=?, whatsapp_number=?, status=? WHERE id=?', [$name, $nameAr, $description, $whatsapp, $status, $id]);
        } else {
            dbInsert('INSERT INTO shows (name, name_ar, description, whatsapp_number, status) VALUES (?,?,?,?,?)', [$name, $nameAr, $description, $whatsapp, $status]);
        }
        header('Location: shows.php'); exit;
    }
    if ($action === 'delete') {
        dbExec('DELETE FROM shows WHERE id=?', [(int)$_POST['id']]);
        header('Location: shows.php'); exit;
    }
}

$title = t('shows.title');
$subTitle = t('app.tagline');
$shows = getShows();
$editShow = null;
if (isset($_GET['edit'])) { $editShow = getShow((int)$_GET['edit']); }
?>

<?php include __DIR__ . '/partials/layouts/layoutTop.php'; ?>

<div class="row">
	<div class="col-12">
		<div class="card shadow-none border">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h6 class="mb-0"><?= e(t('shows.title')) ?></h6>
				<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showModal"><?= e(t('shows.add')) ?></button>
			</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table basic-table mb-0">
						<thead>
							<tr>
								<th>#</th>
								<th><?= e(t('shows.name')) ?></th>
								<th><?= e(t('shows.whatsapp')) ?></th>
								<th><?= e(t('shows.status')) ?></th>
								<th><?= e(t('shows.actions')) ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($shows as $show): ?>
								<tr>
									<td><?= $show['id'] ?></td>
									<td>
										<div class="d-flex align-items-center gap-2">
											<img src="<?= e(tvShowImage($show)) ?>" alt="<?= e(l10n($show, 'name')) ?>" class="w-44-px h-44-px rounded-circle object-fit-cover flex-shrink-0">
											<div>
												<strong class="d-block"><?= e(l10n($show, 'name')) ?></strong>
												<small class="text-muted"><?= e($show['description']) ?></small>
											</div>
										</div>
									</td>
									<td><?= e($show['whatsapp_number']) ?></td>
									<td><span class="badge bg-<?= statusBadge($show['status']) ?>"><?= e(statusLabel($show['status'])) ?></span></td>
									<td>
										<a href="?edit=<?= $show['id'] ?>" class="btn btn-sm btn-outline-primary"><iconify-icon icon="ri:edit-line"></iconify-icon></a>
										<form method="POST" class="d-inline" onsubmit="return confirm('<?= e(t('common.confirm')) ?>')">
											<input type="hidden" name="action" value="delete">
											<input type="hidden" name="id" value="<?= $show['id'] ?>">
											<button type="submit" class="btn btn-sm btn-outline-danger"><iconify-icon icon="ri:delete-bin-line"></iconify-icon></button>
										</form>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Show Modal -->
<div class="modal fade" id="showModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="POST">
				<input type="hidden" name="action" value="save">
				<input type="hidden" name="id" value="<?= $editShow['id'] ?? 0 ?>">
				<div class="modal-header">
					<h5 class="modal-title"><?= e($editShow ? t('shows.edit') : t('shows.add')) ?></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<div class="mb-3">
						<label class="form-label"><?= e(t('shows.name')) ?></label>
						<input type="text" class="form-control" name="name" value="<?= e($editShow['name'] ?? '') ?>" required>
					</div>
					<div class="mb-3">
						<label class="form-label"><?= e(t('shows.name_ar')) ?></label>
						<input type="text" class="form-control" name="name_ar" value="<?= e($editShow['name_ar'] ?? '') ?>" dir="rtl">
					</div>
					<div class="mb-3">
						<label class="form-label"><?= e(t('shows.description')) ?></label>
						<textarea class="form-control" name="description" rows="2"><?= e($editShow['description'] ?? '') ?></textarea>
					</div>
					<div class="mb-3">
						<label class="form-label"><?= e(t('shows.whatsapp')) ?></label>
						<input type="text" class="form-control" name="whatsapp_number" value="<?= e($editShow['whatsapp_number'] ?? '') ?>">
					</div>
					<div class="mb-3">
						<label class="form-label"><?= e(t('shows.status')) ?></label>
						<select class="form-select" name="status">
							<option value="draft" <?= ($editShow['status'] ?? '') == 'draft' ? 'selected' : '' ?>><?= e(t('status.draft')) ?></option>
							<option value="live" <?= ($editShow['status'] ?? '') == 'live' ? 'selected' : '' ?>><?= e(t('status.live')) ?></option>
							<option value="ended" <?= ($editShow['status'] ?? '') == 'ended' ? 'selected' : '' ?>><?= e(t('status.ended')) ?></option>
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= e(t('common.cancel')) ?></button>
					<button type="submit" class="btn btn-primary"><?= e(t('shows.save')) ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php if ($editShow): ?>
<script>document.addEventListener('DOMContentLoaded', function() { new bootstrap.Modal(document.getElementById('showModal')).show(); });</script>
<?php endif; ?>

<?php include __DIR__ . '/partials/layouts/layoutBottom.php'; ?>
