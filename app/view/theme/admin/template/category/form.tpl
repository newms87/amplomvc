<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<form action="<?= site_url('admin/category/save', 'category_id=' . $category_id); ?>" method="post" enctype="multipart/form-data" class="box ctrl-save">
		<div class="heading">
			<h1>
				<img src="<?= theme_url('image/user.png'); ?>" alt=""/>
				{{Category}}
			</h1>

			<div class="buttons">
				<button data-loading="{{Saving...}}">{{Save}}</button>
				<a href="<?= site_url('admin/category'); ?>" class="button cancel">{{Cancel}}</a>
				<? if ($category_id) { ?>
					<a href="<?= site_url('admin/category/remove', 'category_id=' . $category_id); ?>" class="button remove" data-confirm="{{Confirm Delete}}" data-confirm-text="{{Are you sure you want to delete this API User?}}">{{Delete}}</a>
				<? } ?>
			</div>
		</div>

		<div class="section">
			<div id="tab-general">
				<table class="form">
					<tr>
						<td class="required"> {{Name:}}</td>
						<td>
							<input type="text" name="name" value="<?= $name; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="required"> {{Type:}}</td>
						<td>
							<input type="text" name="type" value="<?= $type; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="required"> {{Title:}}</td>
						<td>
							<input type="text" name="title" value="<?= $title; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="required"> {{Parent Category:}}</td>
						<td>
							<?= build(array(
								'type'   => 'select',
								'name'   => 'parent_id',
								'data'   => $data_parents,
								'label'  => 'name',
								'value'  => 'category_id',
								'select' => $parent_id,
							)); ?>
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
								'select' => $status,
							)); ?>
						</td>
					</tr>

				</table>
			</div>
		</div>
	</form>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
