<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<form action="<?= site_url('admin/category/save', 'category_id=' . $record_id); ?>" method="post" enctype="multipart/form-data" class="box ctrl-save">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<button>{{Save}}</button>
				<? if ($record_id && user_can('w', 'admin/category/remove')) { ?>
					<a href="<?= site_url('admin/category/remove', 'category_id=' . $record_id); ?>" class="button remove" data-confirm="{{Confirm Delete}}" data-confirm-modal="{{Are you sure you want to delete this API User?}}">{{Delete}}</a>
				<? } ?>
			</div>
		</div>

		<div class="section">
			<div id="tab-general">
				<table class="form">
					<tr>
						<td class="required"> {{Type:}}</td>
						<td>
							<input type="text" name="type" value="<?= $record['type']; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="required"> {{Name:}}</td>
						<td>
							<input type="text" name="name" value="<?= $record['name']; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="required"> {{Title:}}</td>
						<td>
							<input type="text" name="title" value="<?= $record['title']; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="required"> {{Parent Category:}}</td>
						<td>
							<?= build(array(
								'type'   => 'select',
								'name'   => 'parent_id',
								'data'   => $data['parents'],
								'label'  => 'name',
								'value'  => 'category_id',
								'select' => $record['parent_id'],
							)); ?>
						</td>
					</tr>
					<tr>
						<td class="required sort-order"> {{Sort Order:}}</td>
						<td>
							<input type="text" name="sort_order" value="<?= $record['sort_order']; ?>"/>
						</td>
					</tr>
					<tr>
						<td>{{Status:}}</td>
						<td>
							<?= build(array(
								'type'   => 'select',
								'name'   => 'status',
								'data'   => array(
									0 => '{{Deactivated}}',
									1 => '{{Active}}',
								),
								'select' => $record['status'],
							)); ?>
						</td>
					</tr>

				</table>
			</div>
		</div>
	</form>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
