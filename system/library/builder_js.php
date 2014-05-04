<?php

switch ($js) {

	case 'ckeditor':
		?>
		<? if (!isset($js_loaded_files['ckeditor'])) { ?>
		<script type="text/javascript" src="<?= URL_RESOURCES . 'js/ckeditor/ckeditor.js'; ?>"></script>
		<script type="text/javascript">
			var ckedit_index = 0;

			function init_ckeditor_for(context) {
				context.each(function (i, e) {
					if (!$(e).attr('id') || CKEDITOR.instances[$(e).attr('id')]) {
						$(e).attr('id', 'ckedit_' + ckedit_index++);
					}

					CKEDITOR.replace($(e).attr('id'), {
						filebrowserBrowseUrl: "<?= URL_AJAX . 'common/filemanager/ckeditor'; ?>",
						filebrowserImageBrowseUrl: "<?= URL_AJAX . 'common/filemanager/ckeditor'; ?>",
						filebrowserFlashBrowseUrl: "<?= URL_AJAX . 'common/filemanager/ckeditor'; ?>",
						filebrowserUploadUrl: "<?= URL_AJAX . 'common/filemanager/ckeditor'; ?>",
						filebrowserImageUploadUrl: "<?= URL_AJAX . 'common/filemanager/ckeditor'; ?>",
						filebrowserFlashUploadUrl: "<?= URL_AJAX . 'common/filemanager/ckeditor'; ?>"
					});
				});
			}

			init_ckeditor_for($('.ckedit').not('.template'));

			function remove_ckeditor_for(context) {
				context.each(function (i, e) {
					CKEDITOR.instances[$(e).attr('id')].destroy();
				});
			}
		</script>
	<? } ?>
		<?php break;

	case 'translations':
		if (empty($args[0])) {
			$args[0] = array();
		}

		if (!empty($args[1])) {
			$name_format = $args[1];
		} else {
			$name_format = false;
		}

		$languages = $this->System_Model_Language->getEnabledLanguages();

		$default_language = option('config_language_id');

		$translations = json_encode($args[0]); ?>

		<div id="language_menu_template">
			<div class="language_menu">
				<? foreach ($languages as $language) { ?>
					<div class="language_item <?= $language['language_id'] == $default_language ? 'active' : ''; ?>"
						title="<?= $language['name']; ?>" lang_id="<?= $language['language_id']; ?>">
						<img alt="<?= $language['name']; ?>" src="<?= theme_url("image/flags/$language[image]"); ?>"/>
					</div>
				<? } ?>
			</div>
		</div>

		<script type="text/javascript">
			$('.language_menu .language_item').click(function () {
				lang_id = $(this).attr('lang_id');
				$('.translation').hide();
				$('.translation.' + lang_id).show();
				$('.language_menu .language_item.active').removeClass('active');
				$('.language_menu [lang_id=' + lang_id + ']').addClass('active');
			});

			language_menu = $('#language_menu_template .language_menu').clone(true);
			$('#language_menu_template').remove();

			var translations = <?= $translations; ?>;
			var default_language = "<?= $default_language ?>";

			for (var t in translations) {
				<? if ($name_format) { ?>
				context = $('[name="<?= $name_format; ?>"]'.replace(/%name%/, t));
				<? } else { ?>
				context = $('[name="' + t + '"]');
				<? } ?>

				if (!context.length) {
					context = $(t);
				}

				if (!context.length) break;

				box = $('<div class ="translation_box" />');

				context.before(box);

				box.append(context);

				for (var lang in translations[t]) {
					<? if ($name_format) { ?>
					t_name = "<?= $name_format; ?>".replace(/%name%/, "translations][" + t + "][" + lang + "");
					<? } else { ?>
					t_name = "translations[" + t + "][" + lang + "]";
					<? } ?>

					t_input = context.clone();
					t_input.attr('name', t_name);
					t_input.val(translations[t][lang]);

					if (t_input.hasClass('ckedit')) {
						t_input.attr('id', 'translation_' + t + '_' + lang);

						box.append($('<div class ="translation ' + lang + '" />').append(t_input));

						<? if (isset($js_loaded_files['ckeditor'])) { ?>
						init_ckeditor_for($('#translation_' + t + '_' + lang));
						<? } ?>
					}
					else {
						t_input.addClass('translation ' + lang);
						box.append(t_input);
					}
				}

				box.append(language_menu.clone(true));

				if (context.hasClass('ckedit')) {
					ckedit_box = $('<div class ="translation ' + default_language + '" />');

					context.before(ckedit_box);
					ckedit_box.append(context);

					ckedit_box.show();
				}
				else {
					context.addClass('translation ' + default_language);
					context.show();
				}
			}
		</script>

		<?php break;


	default:
		break;
}

$js_loaded_files[$js] = true;
