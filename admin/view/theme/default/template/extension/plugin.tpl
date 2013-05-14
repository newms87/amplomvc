<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <?= $this->builder->display_errors($errors);?>
  <div class="box">
    <div class="heading">
      <h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
    </div>
    <div class="content">
      <table class="list">
        <thead>
          <tr>
            <td class="left"><?= $column_name; ?></td>
            <td class="right"><?= $column_action; ?></td>
          </tr>
        </thead>
        <tbody>
          <? if ($plugins) { ?>
          <? foreach ($plugins as $plugin) { ?>
          <tr>
            <td class="left"><?= $plugin['name']; ?></td>
            <td class="right"><? foreach ($plugin['action'] as $action) { ?>
              [ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
              <? } ?></td>
          </tr>
          <? } ?>
          <? } else { ?>
          <tr>
            <td class="center" colspan="8"><?= $text_no_results; ?></td>
          </tr>
          <? } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?= $footer; ?>

<?= $this->builder->js('errors', $errors);?>