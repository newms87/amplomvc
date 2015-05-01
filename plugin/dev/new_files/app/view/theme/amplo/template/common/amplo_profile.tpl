<div id="amplo-profile-box">
	<style>
		#amplo-profile-box {
			position: fixed;
			bottom: 15px;
			left: 15px;
			width: auto;
			height: 20px;
			box-shadow: 4px 4px 20px rgba(0, 0, 0, .7);
			overflow: hidden;
			background: white;
			padding: 15px;
			border-radius: 8px;
			z-index: 1000;
			-webkit-transition: all .2s;
			transition: all .2s;
			cursor: pointer;
		}

		#amplo-profile-box.show {
			bottom: 20%;
			left: 30%;
			width: 40%;
			height: 60%;
			box-shadow: 4px 4px 20px rgba(0, 0, 0, .7);
			overflow: auto;
			background: white;
			padding: 25px 15px 15px;
			cursor: default;
		}

		#amplo-profile-box h2 {
			font-size: 1.5em;
			margin: 0 auto 15px auto;
			text-align: center;
		}

		#amplo-profile-box .close {
			position: absolute;
			top: 8px;
			right: 8px;
			background: #B6CEED;
			color: gray;
			padding: 4px 8px;
			cursor: pointer;
			border-radius: 4px;
			display: none;
		}

		#db_profile {
			display: none;
		}

		#db_profile .profile_list {
			position: relative;
		}

		#db_profile .profile_list div {
			position: relative;
			margin: 10px 0 10px 15px;
			cursor: pointer;
			padding: 5px 10px;
			background: #38B0E3;
			border-radius: 5px;
			width: 200px;
		}

		#db_profile .profile_list div span {
			position: absolute;
			width: 400px;
			top: 0;
			left: 50%;
			display: none;
			background: white;
			padding: 10px 20px;
			border-radius: 10px;
			box-shadow: 5px 5px 20px rgba(0, 0, 0, .6);
			z-index: 10;
		}

		#db_profile .profile_list div:hover span {
			display: block;
		}

		#amplo-profile {
			display: none;
		}

		#amplo-profile-box.show .close, #amplo-profile-box.show #db_profile, #amplo-profile-box.show #amplo-profile {
			display: block;
		}
	</style>

	<h2>Amplo DB / Performance Profile</h2>

	<div id="amplo-profile">
		<div class="total"><?= _l("Run Time: %s seconds", $run_time); ?></div>
		<div class="memory"><?= _l("Max Memory / Allocated: %s / %s", $memory, $real_memory); ?></div>
		<div class="total-files"><?= _l("Total Files Loaded: %s", count($file_list)); ?></div>
		<div class="total-file-size"><?= _l("Total File Size: %s", $total_file_size); ?></div>
		<div class="file-lists"><?php html_dump($file_list, 'file list'); ?></div>

		<div class="total-files"><?= _l("Total Cache Files: %s", count($cache_files)); ?></div>
		<div class="total-file-size"><?= _l("Total Cache File Size: %s", $total_cache_size); ?></div>
		<div class="file-lists"><?php html_dump($cache_files, 'cache files'); ?></div>
	</div>

	<?php if (!empty($profile)) { ?>
		<div id="system-profile">
			<?php html_dump($profile, 'system profile'); ?>
		</div>
	<?php } ?>

	<div class="close">X</div>

	<?php if (AMPLO_PROFILE) { ?>
		<div id="db_profile">
			<div class="total"><?= _l("Database Total Time: %s in %s transactions", $db_time, count($db_profile)); ?></div>
			<div class="profile_list">
				<?php foreach ($db_profile as $p) { ?>
					<div><?= $p['time']; ?><span><?= $p['query']; ?></span></div>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
</div>

<script>
	$('#amplo-profile-box').click(function () {
		if (!$(this).hasClass('closing')) {
			$(this).addClass('show');
		}
	});

	$('#amplo-profile-box .close').click(function () {
		$('#amplo-profile-box').removeClass('show').addClass('closing');
		setTimeout(function () {
			$('#amplo-profile-box').removeClass('closing')
		}, 200);
	});
</script>
