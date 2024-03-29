<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>
		</div>
		<div class="section">
			<form id="form_version" action="<?= $action; ?>" method="post">
				<table class="form">
					<tr>
						<td class="required"> {{Update system to Version:}}</td>
						<td><?=
							build(array(
								'type'   => 'select',
								'name'   => 'version',
								'data'   => $data_versions,
								'select' => $version
							)); ?></td>
					</tr>
					<tr>
						<td></td>
						<td><input type="submit" class="button" value="{{Update Version}}"/></td>
					</tr>
				</table>
			</form>
			<form id="form_auto_update" action="<?= $action; ?>" method="post">
				<table class="form">
					<tr>
						<td> {{Activate / Deactivate System Automatic Updates}}</td>
						<td>
							<? if ($auto_update) { ?>
								<input type="hidden" name="auto_update" value="0"/>
								<input type="submit" class="button" value="{{Deactivate Automatic Updates}}"/>
							<? } else { ?>
								<input type="hidden" name="auto_update" value="1"/>
								<input type="submit" class="button" value="{{Activate Automatic Updates}}"/>
							<? } ?>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>


<?= $is_ajax ? '' : call('admin/footer'); ?>
