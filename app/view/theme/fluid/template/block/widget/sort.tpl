<span class="sort_text">{{Sort}}</span><?= build('select', array(
	'name'   => 'sort_list',
	'data'   => $sorts,
	'select' => $sort_select
)); ?>
