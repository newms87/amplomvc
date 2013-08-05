$.ac_vars = {};

$.ac_vars.site_url = "<?= SITE_URL; ?>";

<? if ($this->config->isAdmin()) { ?>
$.ac_vars.image_thumb_width = <?= $this->config->get('config_image_admin_thumb_width');?>;
$.ac_vars.image_thumb_height = <?= $this->config->get('config_image_admin_thumb_height');?>;
<? }
else { ?>
$.ac_vars.image_thumb_width = <?= $this->config->get('config_image_thumb_width');?>;
$.ac_vars.image_thumb_height = <?= $this->config->get('config_image_thumb_height');?>;
<? } ?>