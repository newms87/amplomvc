<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section plugin-settings">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>
		</div>

		<div class="section">
			<div class="filter-team col xs-12">
				<form action="<?= site_url('admin/plugin/find'); ?>" method="post">
					<input type="text" name="team" value="<?= $team; ?>"/>
					<button>{{Search By Team}}</button>
				</form>
			</div>

			<div class="plugin-list col xs-12">
				<? foreach ($plugins as $plugin) { ?>
					<div class="plugin col xs-12 sm-6 md-4 lg-3">
						<div class="details col xs-12 sm-10">
							<a class="name col xs-12" href="<?= site_url('admin/plugin/download', 'name=' . $plugin['full_name']); ?>">
								<h2><?= $plugin['name']; ?></h2>
							</a>

							<p class="description col xs-12 left"><?= $plugin['description']; ?></p>

							<div class="buttons col xs-12">
								<div class="install col xs-6 left">
									<a href="<?= site_url('admin/plugin/download', 'name=' . $plugin['full_name']); ?>">{{Install}}</a>
								</div>
								<div class="download col xs-6 right">
									<a href="<?= $plugin['download']; ?>">{{Download Zip}}</a>
								</div>
							</div>
						</div>
					</div>
				<? } ?>
			</div>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
