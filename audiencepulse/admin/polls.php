<?php
require_once __DIR__ . '/../includes/config.php';

if (isset($_GET['locale'])) { setAppLocale($_GET['locale']); header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?')); exit; }

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $showId = (int)$_POST['show_id'];
        $question = $_POST['question'] ?? '';
        $questionAr = $_POST['question_ar'] ?? '';
        $status = $_POST['status'] ?? 'draft';
        $opensAt = $_POST['opens_at'] ?: null;
        $closesAt = $_POST['closes_at'] ?: null;
        $options = $_POST['options'] ?? [];
        $optionsAr = $_POST['options_ar'] ?? [];

        if ($id > 0) {
            dbExec('UPDATE polls SET show_id=?, question=?, question_ar=?, status=?, opens_at=?, closes_at=? WHERE id=?', [$showId, $question, $questionAr, $status, $opensAt, $closesAt, $id]);
            dbExec('DELETE FROM poll_options WHERE poll_id=?', [$id]);
        } else {
            $id = dbInsert('INSERT INTO polls (show_id, question, question_ar, status, opens_at, closes_at) VALUES (?,?,?,?,?,?)', [$showId, $question, $questionAr, $status, $opensAt, $closesAt]);
        }
        foreach ($options as $i => $opt) {
            if (trim($opt) !== '') {
                dbInsert('INSERT INTO poll_options (poll_id, option_text, option_text_ar, sort_order) VALUES (?,?,?,?)', [$id, $opt, $optionsAr[$i] ?? '', $i + 1]);
            }
        }
        header('Location: polls.php'); exit;
    }
    if ($action === 'delete') {
        dbExec('DELETE FROM polls WHERE id=?', [(int)$_POST['id']]);
        header('Location: polls.php'); exit;
    }
}

$title = t('polls.title');
$subTitle = t('app.tagline');
$shows = getShows();
$polls = dbAll('SELECT p.*, s.name as show_name, s.name_ar as show_name_ar FROM polls p JOIN shows s ON s.id = p.show_id ORDER BY p.created_at DESC');
$editPoll = null;
if (isset($_GET['edit'])) { $editPoll = getPoll((int)$_GET['edit']); }
?>

<?php include __DIR__ . '/partials/layouts/layoutTop.php'; ?>

<div class="row">
	<div class="col-12">
		<div class="card shadow-none border">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h6 class="mb-0"><?= e(t('polls.title')) ?></h6>
				<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#pollModal"><?= e(t('polls.add')) ?></button>
			</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table basic-table mb-0">
						<thead>
							<tr>
								<th>#</th>
								<th><?= e(t('polls.question')) ?></th>
								<th><?= e(t('polls.show')) ?></th>
								<th><?= e(t('polls.status')) ?></th>
								<th><?= e(t('polls.total_votes')) ?></th>
								<th><?= e(t('shows.actions')) ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($polls as $poll): ?>
								<tr>
									<td><?= $poll['id'] ?></td>
									<td>
										<strong><?= e(l10n($poll, 'question')) ?></strong>
										<small class="d-block text-muted"><?= e($poll['opens_at'] ?? '') ?> → <?= e($poll['closes_at'] ?? '') ?></small>
									</td>
									<td><?= e(l10n($poll, 'show_name')) ?></td>
									<td><span class="badge bg-<?= statusBadge($poll['status']) ?>"><?= e(statusLabel($poll['status'])) ?></span></td>
									<td><?= fmt((int)dbOne('SELECT COUNT(*) as cnt FROM votes WHERE poll_id=?', [$poll['id']])['cnt']) ?></td>
									<td>
										<a href="?edit=<?= $poll['id'] ?>" class="btn btn-sm btn-outline-primary"><iconify-icon icon="ri:edit-line"></iconify-icon></a>
										<form method="POST" class="d-inline" onsubmit="return confirm('<?= e(t('common.confirm')) ?>')">
											<input type="hidden" name="action" value="delete">
											<input type="hidden" name="id" value="<?= $poll['id'] ?>">
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

<!-- Poll Modal -->
<div class="modal fade" id="pollModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form method="POST">
				<input type="hidden" name="action" value="save">
				<input type="hidden" name="id" value="<?= $editPoll['id'] ?? 0 ?>">
				<div class="modal-header">
					<h5 class="modal-title"><?= e($editPoll ? t('polls.edit') : t('polls.add')) ?></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<div class="mb-3">
						<label class="form-label"><?= e(t('polls.show')) ?></label>
						<select class="form-select" name="show_id" required>
							<?php foreach ($shows as $show): ?>
								<option value="<?= $show['id'] ?>" <?= ($editPoll['show_id'] ?? '') == $show['id'] ? 'selected' : '' ?>><?= e(l10n($show, 'name')) ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="mb-3">
						<label class="form-label"><?= e(t('polls.question')) ?></label>
						<input type="text" class="form-control" name="question" value="<?= e($editPoll['question'] ?? '') ?>" required>
					</div>
					<div class="mb-3">
						<label class="form-label"><?= e(t('polls.question_ar')) ?></label>
						<input type="text" class="form-control" name="question_ar" value="<?= e($editPoll['question_ar'] ?? '') ?>" dir="rtl">
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label"><?= e(t('polls.opens_at')) ?></label>
							<input type="datetime-local" class="form-control" name="opens_at" value="<?= e($editPoll['opens_at'] ?? '') ?>">
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label"><?= e(t('polls.closes_at')) ?></label>
							<input type="datetime-local" class="form-control" name="closes_at" value="<?= e($editPoll['closes_at'] ?? '') ?>">
						</div>
					</div>
					<div class="mb-3">
						<label class="form-label"><?= e(t('polls.status')) ?></label>
						<select class="form-select" name="status">
							<option value="draft" <?= ($editPoll['status'] ?? '') == 'draft' ? 'selected' : '' ?>><?= e(t('status.draft')) ?></option>
							<option value="open" <?= ($editPoll['status'] ?? '') == 'open' ? 'selected' : '' ?>><?= e(t('status.open')) ?></option>
							<option value="closed" <?= ($editPoll['status'] ?? '') == 'closed' ? 'selected' : '' ?>><?= e(t('status.closed')) ?></option>
						</select>
					</div>
					<hr>
					<h6 class="mb-3"><?= e(t('polls.options')) ?></h6>
					<div id="optionsContainer">
						<?php $opts = $editPoll['options'] ?? [['option_text' => '', 'option_text_ar' => '']]; ?>
						<?php foreach ($opts as $i => $opt): ?>
							<div class="row g-2 mb-2 option-row">
								<div class="col-md-5">
									<input type="text" class="form-control" name="options[]" placeholder="<?= e(t('polls.option_text')) ?>" value="<?= e($opt['option_text'] ?? '') ?>">
								</div>
								<div class="col-md-5">
									<input type="text" class="form-control" name="options_ar[]" placeholder="<?= e(t('polls.option_text_ar')) ?>" value="<?= e($opt['option_text_ar'] ?? '') ?>" dir="rtl">
								</div>
								<div class="col-md-2">
									<button type="button" class="btn btn-outline-danger btn-sm remove-option"><iconify-icon icon="ri:close-line"></iconify-icon></button>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
					<button type="button" class="btn btn-sm btn-outline-primary" id="addOption"><iconify-icon icon="ri:add-line"></iconify-icon> <?= e(t('polls.add_option')) ?></button>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= e(t('common.cancel')) ?></button>
					<button type="submit" class="btn btn-primary"><?= e(t('polls.save')) ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php if ($editPoll): ?>
<script>document.addEventListener('DOMContentLoaded', function() { new bootstrap.Modal(document.getElementById('pollModal')).show(); });</script>
<?php endif; ?>

<script>
document.getElementById('addOption')?.addEventListener('click', function() {
	var container = document.getElementById('optionsContainer');
	var row = document.createElement('div');
	row.className = 'row g-2 mb-2 option-row';
	row.innerHTML = '<div class="col-md-5"><input type="text" class="form-control" name="options[]" placeholder="<?= e(t('polls.option_text')) ?>"></div><div class="col-md-5"><input type="text" class="form-control" name="options_ar[]" placeholder="<?= e(t('polls.option_text_ar')) ?>" dir="rtl"></div><div class="col-md-2"><button type="button" class="btn btn-outline-danger btn-sm remove-option"><iconify-icon icon="ri:close-line"></iconify-icon></button></div>';
	container.appendChild(row);
});
document.addEventListener('click', function(e) {
	if (e.target.closest('.remove-option')) {
		e.target.closest('.option-row').remove();
	}
});
</script>

<?php include __DIR__ . '/partials/layouts/layoutBottom.php'; ?>
