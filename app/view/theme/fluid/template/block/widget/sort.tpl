<span class="sort_text"><?= _l("Sort"); ?></span>
<?= $this->builder->build('select', $sorts, 'sort_list', $sort_select); ?>