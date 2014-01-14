<?= $header; ?>
<div class="section">
	<div class="box"
		style="width: 400px; min-height: 300px; margin-top: 40px; margin-left: auto; margin-right: auto;">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'lockscreen.png'; ?>" alt=""/> <?= _l("Please enter your login details."); ?></h1>
		</div>
		<div class="section" style="min-height: 150px; overflow: hidden;">
			<?= $this->builder->displayMessages($messages); ?>
			<form action="<?= $action ?>" method="post" enctype="multipart/form-data" id="form">
				<table style="width: 100%;">
					<tr>
						<td style="text-align: center;" rowspan="4"><img src="<?= HTTP_THEME_IMAGE . 'login.png'; ?>"
								alt="<?= _l("Please enter your login details."); ?>"/></td>
					</tr>
					<tr>
						<td><?= _l("Username:"); ?><br/>
							<input type="text" name="username" value="<?= $username; ?>" style="margin-top: 4px;"/>
							<br/>
							<br/>
							<?= _l("Password:"); ?><br/>
							<input type="password" autocomplete="off" name="password" value="<?= $password; ?>"
								style="margin-top: 4px;"/>
							<br/>
							<a href="<?= $forgotten; ?>"><?= _l("Forgotten Password"); ?></a></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="text-align: right;"><input type="submit" class="button" value="<?= _l("Login"); ?>"/>
						</td>
					</tr>
				</table>
			</form>
		</div>
		<div class="section" style="min-height:80px">
			<div class="help"><?= _l("Not sure how you got here? Please"); ?> <a href="<?= $to_front; ?>"><?= _l("return to the shop!"); ?></a></div>
		</div>
	</div>
</div>

<?= $footer; ?>
