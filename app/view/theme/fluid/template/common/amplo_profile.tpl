<div id="db_profile_box">
	<style>
		#db_profile_box {
			position: absolute;
			top: 20%;
			left: 30%;
			width: 40%;
			height: 60%;
			box-shadow: 4px 4px 20px rgba(0,0,0,.7);
			overflow: auto;
			background: white;
			padding: 15px;
		}

		#db_profile_box h2 {
			font-size: 1.5em;
			margin: 15px auto;
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
		}
		#db_profile{
			clear:both;
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
			left: 10%;
			display:none;
			background:white;
			padding: 10px 20px;
			border-radius: 10px;
			box-shadow: 5px 5px 5px rgba(0,0,0,.6);
			z-index: 10;
		}
		#db_profile .profile_list div:hover span{
			display:block;
		}
	</style>

	<h2>Amplo DB / Performance Profile</h2>
	<div id="amplo_profile">
		<div class="total"><?= _l("Run Time: %s seconds", $run_time); ?></div>
		<div class="memory"><?= _l("Max Memory / Allocated: %s / %s", $memory, $real_memory); ?></div>
		<div class="total-files"><?= _l("Total Files Loaded: %s", $total_files); ?></div>
		<div class="file-lists"><?php html_dump($file_list, 'file list'); ?></div>
	</div>

	<div id="db_profile">
		<div class="total"><?= _l("Database Total Time: %s in %s transactions", $db_time, count($profile)); ?></div>
		<div class="profile_list">
			<?php foreach ($profile as $p) { ?>
				<div><?= $p['time']; ?><span><?= $p['query']; ?></span></div>
			<?php } ?>
		</div>
	</div>
	<div class="close">X</div>

	<script>
		$('#db_profile_box .close').click(function(){
			$('#db_profile_box').remove();
		});
	</script>
</div>