<?= $header; ?>
<div class="content">
	<div class="box" style="width: 400px; min-height: 300px; margin-top: 40px; margin-left: auto; margin-right: auto;">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'lockscreen.png'; ?>" alt="" /> <?= $text_login; ?></h1>
		</div>
		<div class="content" style="min-height: 150px; overflow: hidden;">
			<?=$this->builder->display_messages($messages);?>
			<form action="<?= $action ?>" method="post" enctype="multipart/form-data" id="form">
				<table style="width: 100%;">
					<tr>
						<td style="text-align: center;" rowspan="4"><img src="<?= HTTP_THEME_IMAGE . 'login.png'; ?>" alt="<?= $text_login; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_username; ?><br />
							<input type="text" name="username" value="<?= $username; ?>" style="margin-top: 4px;" />
							<br />
							<br />
							<?= $entry_password; ?><br />
							<input type="password" autocomplete='off' name="password" value="<?= $password; ?>" style="margin-top: 4px;" />
							<br />
							<a href="<?= $forgotten; ?>"><?= $text_forgotten; ?></a></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="text-align: right;"><a onclick="$('#form').submit();" class="button"><?= $button_login; ?></a></td>
					</tr>
				</table>
			</form>
		</div>
		<div class="content" style="min-height:80px">
			<div class="help"><?=$text_lost;?></a>.</div>
			<br><br>
			<div class="help"><?=$text_are_you_a_designer;?></div>
		</div>
	</div>
</div>

<?= $footer; ?>