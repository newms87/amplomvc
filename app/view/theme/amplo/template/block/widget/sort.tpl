<span class="sort_text"><?= _l("Sort"); ?></span><?= build('select', array(
	'name'   => 'sort_list',
	'data'   => $sorts,
	'select' => $sort_select
)); ?>
