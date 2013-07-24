<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>

	<h1><?= $heading_title; ?></h1>
	<p><?= $text_description; ?></p>
	<p><?= $text_code; ?><br />
		<textarea cols="40" rows="5"><?= $code; ?></textarea>
	</p>
	<p><?= $text_generator; ?><br />
		<input type="text" name="product" value="" />
	</p>
	<p><?= $text_link; ?><br />
		<textarea name="link" cols="40" rows="5"></textarea>
	</p>
	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
	</div>
	<?= $content_bottom; ?></div>
	

<? $autocomplete_data = array(
	'selector' => 'input[name=product]',
	'route' => 'affiliate/tracking/autocomplete',
	'filter' => 'name',
	'label' => 'name',
	'value' => 'link',
	'callback' => 'affiliate_autocomplete_callback'
); ?>

<?= $this->builder->js('autocomplete', $autocomplete_data); ?>

<script type="text/javascript">//<!--
function affiliate_autocomplete_callback(label, value) {
	$('input[name=product]').attr('value', label);
	$('textarea[name=link]').attr('value', value);
}
//--></script>
<?= $footer; ?>