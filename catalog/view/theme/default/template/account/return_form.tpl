<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content"><?= $content_top; ?>
  <?= $this->builder->display_breadcrumbs();?>
  <?= $this->builder->display_errors($errors);?>
  <h1><?= $heading_title; ?></h1>
  <?= $text_description; ?>
  <form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
    <h2><?= $text_order; ?></h2>
    <div class="content">
      <div class="left"><span class="required">*</span> <?= $entry_firstname; ?><br />
        <input type="text" name="firstname" value="<?= $firstname; ?>" class="large-field" />
        <br />
        <br />
        <span class="required">*</span> <?= $entry_lastname; ?><br />
        <input type="text" name="lastname" value="<?= $lastname; ?>" class="large-field" />
        <br />
        <br />
        <span class="required">*</span> <?= $entry_email; ?><br />
        <input type="text" name="email" value="<?= $email; ?>" class="large-field" />
        <br />
        <br />
        <span class="required">*</span> <?= $entry_telephone; ?><br />
        <input type="text" name="telephone" value="<?= $telephone; ?>" class="large-field" />
        <br />
        <br />
      </div>
      <div class="right"><span class="required">*</span> <?= $entry_order_id; ?><br />
        <input type="text" name="order_id" value="<?= $order_id; ?>" class="large-field" />
        <br />
        <br />
        <?= $entry_date_ordered; ?><br />
        <input type="text" name="date_ordered" value="<?= $date_ordered; ?>" class="large-field date" />
        <br />
      </div>
    </div>
    <h2><?= $text_product; ?></h2>
    <div id="return-product">
      <div class="content">
        <div class="return-product">
          <div class="return-name"><span class="required">*</span> <b><?= $entry_product; ?></b><br />
            <input type="text" name="product" value="<?= $product; ?>" />
            <br />
          </div>
          <div class="return-model"><span class="required">*</span> <b><?= $entry_model; ?></b><br />
            <input type="text" name="model" value="<?= $model; ?>" />
            <br />
          </div>
          <div class="return-quantity"><b><?= $entry_quantity; ?></b><br />
            <input type="text" name="quantity" value="<?= $quantity; ?>" />
          </div>
        </div>
        <div class="return-detail">
          <div class="return-reason"><span class="required">*</span> <b><?= $entry_reason; ?></b><br />
            <table>
              <? foreach ($return_reasons as $return_reason) { ?>
              <? if ($return_reason['return_reason_id'] == $return_reason_id) { ?>
              <tr>
                <td width="1"><input type="radio" name="return_reason_id" value="<?= $return_reason['return_reason_id']; ?>" id="return-reason-id<?= $return_reason['return_reason_id']; ?>" checked="checked" /></td>
                <td><label for="return-reason-id<?= $return_reason['return_reason_id']; ?>"><?= $return_reason['name']; ?></label></td>
              </tr>
              <? } else { ?>
              <tr>
                <td width="1"><input type="radio" name="return_reason_id" value="<?= $return_reason['return_reason_id']; ?>" id="return-reason-id<?= $return_reason['return_reason_id']; ?>" /></td>
                <td><label for="return-reason-id<?= $return_reason['return_reason_id']; ?>"><?= $return_reason['name']; ?></label></td>
              </tr>
              <?  } ?>
              <?  } ?>
            </table>
          </div>
          <div class="return-opened"><b><?= $entry_opened; ?></b><br />
            <? if ($opened) { ?>
            <input type="radio" name="opened" value="1" id="opened" checked="checked" />
            <? } else { ?>
            <input type="radio" name="opened" value="1" id="opened" />
            <? } ?>
            <label for="opened"><?= $text_yes; ?></label>
            <? if (!$opened) { ?>
            <input type="radio" name="opened" value="0" id="unopened" checked="checked" />
            <? } else { ?>
            <input type="radio" name="opened" value="0" id="unopened" />
            <? } ?>
            <label for="unopened"><?= $text_no; ?></label>
            <br />
            <br />
            <?= $entry_fault_detail; ?><br />
            <textarea name="comment" cols="150" rows="6"><?= $comment; ?></textarea>
          </div>
          <div class="return-captcha"><b><?= $entry_captcha; ?></b><br />
            <input type="text" name="captcha" value="<?= $captcha; ?>" />
            <br />
            <img src="index.php?route=account/return/captcha" alt="" />
          </div>
        </div>
      </div>
    </div>
    <div class="buttons">
      <div class="left"><a href="<?= $back; ?>" class="button"><?= $button_back; ?></a></div>
      <div class="right">
        <input type="submit" value="<?= $button_continue; ?>" class="button" />
      </div>
    </div>
  </form>
  <?= $content_bottom; ?></div>
<script type="text/javascript">
//<!--
$(document).ready(function() {
	$('.date').datepicker({dateFormat: 'yy-mm-dd'});
});
//--></script> 

<?=$this->builder->js('errors',$errors);?>
<?= $footer; ?>