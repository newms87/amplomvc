<div id="db_profile_box">
	<style>
		#db_profile_box {
			position: fixed;
			bottom: 15px;
			left: 15px;
			width: auto;
			height: 20px;
			box-shadow: 4px 4px 20px rgba(0,0,0,.7);
			overflow: hidden;
			background: white;
			padding: 15px;
			border-radius: 8px;
			z-index: 1000;
			-webkit-transition: all .2s;
			transition: all .2s;
			cursor: pointer;
		}

		#db_profile_box.show {
			bottom: 20%;
			left: 30%;
			width: 40%;
			height: 60%;
			box-shadow: 4px 4px 20px rgba(0,0,0,.7);
			overflow: auto;
			background: white;
			padding: 25px 15px 15px;
			cursor: default;
		}

		#db_profile_box h2 {
			font-size: 1.5em;
			margin: 0 auto 15px auto;
			text-align: center;
		}
		#db_profile_box .close {
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
		#db_profile{
			display: none;
		}
		#db_profile .profile_list{
			position:relative;
		}
		#db_profile .profile_list div{
			position:relative;
			margin: 10px 0 10px 15px;
			cursor: pointer;
			padding: 5px 10px;
			background: #38B0E3;
			border-radius: 5px;
			width: 200px;
		}
		#db_profile .profile_list div span {
			position:absolute;
			width: 400px;
			top:0;
			left: 50%;
			display:none;
			background:white;
			padding: 10px 20px;
			border-radius: 10px;
			box-shadow: 5px 5px 20px rgba(0,0,0,.6);
			z-index: 10;
		}
		#db_profile .profile_list div:hover span{
			display:block;
		}

		#amplo-profile {
			display: none;
		}

		#db_profile_box.show .close, #db_profile_box.show #db_profile, #db_profile_box.show #amplo-profile {
			display: block;
		}
	</style>

	<h2>Amplo DB / Performance Profile</h2>

	<div id="amplo-profile">
		<div class="total"><?= _l("Run Time: %s seconds", $run_time); ?></div>
		<div class="memory"><?= _l("Max Memory / Allocated: %s / %s", $memory, $real_memory); ?></div>
		<div class="total-files"><?= _l("Total Files Loaded: %s", $total_files); ?></div>
		<div class="file-lists"><?php html_dump($file_list, 'file list'); ?></div>
	</div>

	<?php if (!empty($profile)) { ?>
		<div id="system-profile">
			<?php html_dump($profile, 'system profile'); ?>
		</div>
	<?php } ?>

	<div class="close">X</div>

	<?php if (DB_PROFILE) { ?>
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
	$('#db_profile_box').click(function() {
		if (!$(this).hasClass('closing')) {
			$(this).addClass('show');
		}
	});

	$('#db_profile_box .close').click(function(){
		$('#db_profile_box').removeClass('show').addClass('closing');
		setTimeout(function(){$('#db_profile_box').removeClass('closing')}, 200);
	});
</script>
