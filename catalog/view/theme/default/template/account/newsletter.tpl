<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<?= $content_top; ?>

		<h1><?= _l("Newsletter Subscription"); ?></h1>

		<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
			<div class="section">
				<table class="form">
					<tr>
						<td><?= _l("Subscribe:"); ?></td>
						<td><? if ($newsletter) { ?>
								<input type="radio" name="newsletter" value="1" checked="checked"/>
								<?= _l("Yes"); ?>&nbsp;
								<input type="radio" name="newsletter" value="0"/>
								<?= _l("No"); ?>
							<? } else { ?>
								<input type="radio" name="newsletter" value="1"/>
								<?= _l("Yes"); ?>&nbsp;
								<input type="radio" name="newsletter" value="0" checked="checked"/>
								<?= _l("No"); ?>
							<? } ?></td>
					</tr>
				</table>
			</div>
			<div class="buttons">
				<div class="left"><a href="<?= $back; ?>" class="button"><?= _l("Back"); ?></a></div>
				<div class="right"><input type="submit" value="<?= _l("Continue"); ?>" class="button"/></div>
			</div>
		</form>

		<?= $content_bottom; ?>
	</div>

<?= $footer; ?>