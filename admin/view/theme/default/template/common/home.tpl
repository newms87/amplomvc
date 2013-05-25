<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <?= $this->builder->display_errors($errors);?>
  <div class="box">
    <div class="heading">
      <h1><img src="<?= HTTP_THEME_IMAGE . 'home.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
    </div>
    <div class="content">
      <div class="overview">
        <div class="dashboard-heading"><?= $text_overview; ?></div>
        <div class="dashboard-content">
          <table>
            <tr>
              <td><?= $text_total_sale; ?></td>
              <td><?= $total_sale; ?></td>
            </tr>
            <tr>
              <td><?= $text_total_sale_year; ?></td>
              <td><?= $total_sale_year; ?></td>
            </tr>
            <tr>
              <td><?= $text_total_order; ?></td>
              <td><?= $total_order; ?></td>
            </tr>
            <tr>
              <td><?= $text_total_customer; ?></td>
              <td><?= $total_customer; ?></td>
            </tr>
            <tr>
              <td><?= $text_total_customer_approval; ?></td>
              <td><?= $total_customer_approval; ?></td>
            </tr>
            <tr>
              <td><?= $text_total_review_approval; ?></td>
              <td><?= $total_review_approval; ?></td>
            </tr>
            <tr>
              <td><?= $text_total_affiliate; ?></td>
              <td><?= $total_affiliate; ?></td>
            </tr>
            <tr>
              <td><?= $text_total_affiliate_approval; ?></td>
              <td><?= $total_affiliate_approval; ?></td>
            </tr>
          </table>
        </div>
      </div>
      <div class="statistic">
        <div class="range"><?= $entry_range; ?>
          <select id="range" onchange="getSalesChart(this.value)">
            <option value="day"><?= $text_day; ?></option>
            <option value="week"><?= $text_week; ?></option>
            <option value="month"><?= $text_month; ?></option>
            <option value="year"><?= $text_year; ?></option>
          </select>
        </div>
        <div class="dashboard-heading"><?= $text_statistics; ?></div>
        <div class="dashboard-content">
          <div id="report" style="width: 390px; height: 170px; margin: auto;"></div>
        </div>
      </div>
      <div class="latest">
        <div class="dashboard-heading"><?= $text_latest_10_orders; ?></div>
        <div class="dashboard-content">
          <table class="list">
            <thead>
              <tr>
                <td class="right"><?= $column_order; ?></td>
                <td class="left"><?= $column_customer; ?></td>
                <td class="left"><?= $column_status; ?></td>
                <td class="left"><?= $column_date_added; ?></td>
                <td class="right"><?= $column_total; ?></td>
                <td class="right"><?= $column_action; ?></td>
              </tr>
            </thead>
            <tbody>
              <? if ($orders) { ?>
              <? foreach ($orders as $order) { ?>
              <tr>
                <td class="right"><?= $order['order_id']; ?></td>
                <td class="left"><?= $order['customer']; ?></td>
                <td class="left"><?= $order['status']; ?></td>
                <td class="left"><?= $order['date_added']; ?></td>
                <td class="right"><?= $order['total']; ?></td>
                <td class="right"><? foreach ($order['action'] as $action) { ?>
                  [ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
                  <? } ?></td>
              </tr>
              <? } ?>
              <? } else { ?>
              <tr>
                <td class="center" colspan="6"><?= $text_no_results; ?></td>
              </tr>
              <? } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!--[if IE]>
<script type="text/javascript" src="<?= HTTP_JS . 'jquery/flot/excanvas.js'; ?>"></script>
<![endif]--> 
<script type="text/javascript" src="<?= HTTP_JS . 'jquery/flot/jquery.flot.js'; ?>"></script> 
<script type="text/javascript"><!--
function getSalesChart(range) {
	$.ajax({
		type: 'GET',
		url: "<?= HTTP_ADMIN . "index.php?route=common/home/chart"; ?>" + '&range=' + range,
		dataType: 'json',
		async: false,
		success: function(json) {
			var option = {	
				shadowSize: 0,
				lines: { 
					show: true,
					fill: true,
					lineWidth: 1
				},
				grid: {
					backgroundColor: '#FFFFFF'
				},	
				xaxis: {
            		ticks: json.xaxis
				}
			}

			$.plot($('#report'), [json.order, json.customer], option);
		}
	});
}

getSalesChart($('#range').val());
//--></script> 
<?= $footer; ?>