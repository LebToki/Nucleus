<aside class="sidebar">
	<button type="button" class="sidebar-close-btn">
		<iconify-icon icon="ri:close-line"></iconify-icon>
	</button>
	<div>
		<a href="index.php" class="sidebar-logo">
			<span class="brand-logo light-logo">
				<span class="pulse-dot">●</span> <?= e(t('app.name')) ?>
			</span>
			<span class="brand-logo dark-logo d-none">
				<span class="pulse-dot">●</span> <?= e(t('app.name')) ?>
			</span>
			<span class="brand-logo logo-icon">
				<span class="pulse-dot">●</span>
			</span>
		</a>
	</div>
	<div class="sidebar-menu-area">
		<ul class="sidebar-menu" id="sidebar-menu">
			<li class="sidebar-menu-group-title"><?= e(t('nav.dashboard')) ?></li>
			<li>
				<a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
					<iconify-icon icon="ri:dashboard-line" class="menu-icon"></iconify-icon>
					<span><?= e(t('nav.dashboard')) ?></span>
				</a>
			</li>

			<li class="sidebar-menu-group-title"><?= e(t('nav.shows')) ?></li>
			<li>
				<a href="shows.php" class="<?= basename($_SERVER['PHP_SELF']) == 'shows.php' ? 'active' : '' ?>">
					<iconify-icon icon="ri:tv-line" class="menu-icon"></iconify-icon>
					<span><?= e(t('nav.shows')) ?></span>
				</a>
			</li>
			<li>
				<a href="polls.php" class="<?= basename($_SERVER['PHP_SELF']) == 'polls.php' ? 'active' : '' ?>">
					<iconify-icon icon="ri:bar-chart-line" class="menu-icon"></iconify-icon>
					<span><?= e(t('nav.polls')) ?></span>
				</a>
			</li>
			<li>
				<a href="moderation.php" class="<?= basename($_SERVER['PHP_SELF']) == 'moderation.php' ? 'active' : '' ?>">
					<iconify-icon icon="ri:chat-check-line" class="menu-icon"></iconify-icon>
					<span><?= e(t('nav.moderation')) ?></span>
				</a>
			</li>
			<li>
				<a href="voters.php" class="<?= basename($_SERVER['PHP_SELF']) == 'voters.php' ? 'active' : '' ?>">
					<iconify-icon icon="ri:user-heart-line" class="menu-icon"></iconify-icon>
					<span><?= e(t('nav.voters')) ?></span>
				</a>
			</li>

			<li class="sidebar-menu-group-title"><?= e(t('nav.simulation')) ?></li>
			<li>
				<a href="simulation.php" class="<?= basename($_SERVER['PHP_SELF']) == 'simulation.php' ? 'active' : '' ?>">
					<iconify-icon icon="ri:smartphone-line" class="menu-icon"></iconify-icon>
					<span><?= e(t('nav.simulation')) ?></span>
				</a>
			</li>
			<li>
				<a href="graphics.php" class="<?= basename($_SERVER['PHP_SELF']) == 'graphics.php' ? 'active' : '' ?>">
					<iconify-icon icon="ri:live-line" class="menu-icon"></iconify-icon>
					<span><?= e(t('nav.live_graphics')) ?></span>
				</a>
			</li>

			<li class="sidebar-menu-group-title"><?= e(t('nav.settings')) ?></li>
			<li>
				<a href="settings.php" class="<?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>">
					<iconify-icon icon="ri:settings-3-line" class="menu-icon"></iconify-icon>
					<span><?= e(t('nav.settings')) ?></span>
				</a>
			</li>
		</ul>
	</div>
</aside>