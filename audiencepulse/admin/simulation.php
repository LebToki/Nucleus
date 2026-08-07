<?php
require_once __DIR__ . '/../includes/config.php';

if (isset($_GET['locale'])) { setAppLocale($_GET['locale']); header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?')); exit; }

$title = t('simulation.title');
$subTitle = t('simulation.subtitle');
$shows = getShows();
$polls = dbAll("SELECT p.*, s.name as show_name, s.name_ar as show_name_ar FROM polls p JOIN shows s ON s.id = p.show_id WHERE p.status = 'open' ORDER BY p.created_at DESC");
$whatsappNumber = getSetting('whatsapp_number', '201000000000');
?>

<?php include __DIR__ . '/partials/layouts/layoutTop.php'; ?>

<div class="row">
	<!-- WhatsApp Message Simulator (uses 2TInteractive jQuery WhatsApp library) -->
	<div class="col-lg-6">
		<div class="card shadow-none border">
			<div class="card-header">
				<h6 class="mb-0"><iconify-icon icon="ri:whatsapp-line" class="text-success"></iconify-icon> <?= e(t('simulation.send')) ?></h6>
			</div>
			<div class="card-body">
				<form id="whatsappForm" data-title="<?= e(t('app.name')) ?>">
					<div class="mb-3">
						<label class="form-label"><?= e(t('simulation.name')) ?></label>
						<input type="text" class="form-control" id="viewerName" name="viewer_name" placeholder="Ahmed" value="Ahmed" data-label="<?= e(t('simulation.name')) ?>">
					</div>
					<div class="mb-3">
						<label class="form-label"><?= e(t('simulation.phone')) ?></label>
						<input type="text" class="form-control" id="viewerPhone" name="viewer_phone" placeholder="201000000001" value="201000000001" data-label="<?= e(t('simulation.phone')) ?>">
					</div>
					<div class="mb-3">
						<label class="form-label"><?= e(t('simulation.message')) ?></label>
						<textarea class="form-control" id="viewerMessage" name="viewer_message" rows="3" placeholder="Great show!" data-label="<?= e(t('simulation.message')) ?>">Great show today!</textarea>
					</div>
					<button type="submit" class="btn btn-success w-100">
						<iconify-icon icon="ri:whatsapp-line"></iconify-icon> <?= e(t('simulation.send')) ?>
					</button>
				</form>
				<div id="whatsappResult" class="mt-3"></div>
			</div>
		</div>
	</div>

	<!-- Vote Simulator -->
	<div class="col-lg-6">
		<div class="card shadow-none border">
			<div class="card-header">
				<h6 class="mb-0"><iconify-icon icon="ri:vote-line" class="text-primary"></iconify-icon> <?= e(t('simulation.vote')) ?></h6>
			</div>
			<div class="card-body">
				<form id="voteForm">
					<div class="mb-3">
						<label class="form-label"><?= e(t('simulation.select_poll')) ?></label>
						<select class="form-select" id="votePoll" required>
							<option value=""><?= e(t('simulation.select_poll')) ?></option>
							<?php foreach ($polls as $poll): ?>
								<option value="<?= $poll['id'] ?>"><?= e(l10n($poll, 'question')) ?> (<?= e(l10n($poll, 'show_name')) ?>)</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="mb-3">
						<label class="form-label"><?= e(t('simulation.select_option')) ?></label>
						<select class="form-select" id="voteOption" required>
							<option value=""><?= e(t('simulation.select_option')) ?></option>
						</select>
					</div>
					<div class="mb-3">
						<label class="form-label"><?= e(t('simulation.phone')) ?></label>
						<input type="text" class="form-control" id="votePhone" placeholder="201000000001" value="201000000001">
					</div>
					<button type="submit" class="btn btn-primary w-100">
						<iconify-icon icon="ri:vote-line"></iconify-icon> <?= e(t('simulation.cast_vote')) ?>
					</button>
				</form>
				<div id="voteResult" class="mt-3"></div>
			</div>
		</div>
	</div>
</div>

<!-- 2TInteractive jQuery WhatsApp Library -->
<script src="../assets/whatsapp/js/jquery-whatsapp.min.js"></script>
<script>
// Load poll options when poll changes
document.getElementById('votePoll')?.addEventListener('change', function() {
	var pollId = this.value;
	var optionSelect = document.getElementById('voteOption');
	optionSelect.innerHTML = '<option value=""><?= e(t('simulation.select_option')) ?></option>';
	if (!pollId) return;
	fetch('api/poll_options.php?poll_id=' + pollId)
		.then(r => r.json())
		.then(data => {
			data.forEach(function(opt) {
				var el = document.createElement('option');
				el.value = opt.id;
				el.textContent = opt.option_text;
				optionSelect.appendChild(el);
			});
		});
});

// WhatsApp form submission — uses the 2TInteractive jQuery WhatsApp plugin
$(document).ready(function() {
	$('#whatsappForm').whatsAppSenderForm({
		whatsAppNumber: '<?= e($whatsappNumber) ?>',
		openInNewWindow: true,
		submissionMessage: '<?= e(t('simulation.message_sent')) ?>'
	});

	// Also save to moderation queue via API before opening WhatsApp
	$('#whatsappForm').on('submit', function() {
		var name = document.getElementById('viewerName').value;
		var phone = document.getElementById('viewerPhone').value;
		var message = document.getElementById('viewerMessage').value;

		fetch('api/send_message.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ show_id: 1, phone: phone, name: name, content: message })
		})
		.then(r => r.json())
		.then(data => {
			var result = document.getElementById('whatsappResult');
			if (data.success) {
				result.innerHTML = '<div class="alert alert-success"><?= e(t('simulation.message_sent')) ?></div>';
			} else {
				result.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Error') + '</div>';
			}
		});
	});
});

// Vote form submission
document.getElementById('voteForm')?.addEventListener('submit', function(e) {
	e.preventDefault();
	var pollId = document.getElementById('votePoll').value;
	var optionId = document.getElementById('voteOption').value;
	var phone = document.getElementById('votePhone').value;

	fetch('api/cast_vote.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ poll_id: pollId, option_id: optionId, phone: phone })
	})
	.then(r => r.json())
	.then(data => {
		var result = document.getElementById('voteResult');
		if (data.success) {
			result.innerHTML = '<div class="alert alert-success"><?= e(t('simulation.vote_cast')) ?></div>';
		} else {
			result.innerHTML = '<div class="alert alert-warning"><?= e(t('simulation.duplicate_vote')) ?></div>';
		}
	});
});
</script>

<?php include __DIR__ . '/partials/layouts/layoutBottom.php'; ?>
