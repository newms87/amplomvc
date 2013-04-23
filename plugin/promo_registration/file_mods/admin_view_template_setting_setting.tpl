=====
       <div id="tab-option">
          <table class="form">
-----
>>>>>html
            <tr>
              <td><?= $entry_promo_reg; ?></td>
              <td><?=$this->builder->build('select',$yes_no, "config_promo_registration", $config_promo_registration);?></td>
            </tr>
            <tr>
              <td><?= $entry_promo_reg_title; ?></td>
              <td><input type='text' name='config_promo_registration_title' value='<?=$config_promo_registration_title;?>' /></td>
            </tr>
            <tr>
              <td><?= $entry_promo_reg_text; ?></td>
              <td><textarea name='config_promo_registration_text' id='promo_reg_text' class='ckedit'><?=$config_promo_registration_text;?></textarea></td>
            </tr>
            <tr>
              <td><?= $entry_promo_registration_coupon_id; ?></td>
              <td>
                 <? $this->builder->set_config('coupon_id','name');?>
                 <?=$this->builder->build('select',$coupons, "config_promo_registration_coupon_id", $config_promo_registration_coupon_id);?>
              </td>
            </tr>
-----