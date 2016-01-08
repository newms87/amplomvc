<?= $is_ajax ? '' : call(IS_ADMIN ? 'admin/header' : 'header'); ?>

<? if (!$is_ajax) { ?>
<div class="amp-manager-page">
	<div class="row header-row">
		<div class="wrap">
			<h1><?= page_info('title'); ?></h1>
		</div>
	</div>

	<div class="amp-manager" class="row">
		<div class="wrap">
			<? } ?>

			<div class="am-search">
				<div class="row left amp-nested-form am-search-form">
					<div class="help">{{Search For:}}</div>

					<div class="col left xs-11 form-input">
						<input type="text" name="filter[keywords]" value="" placeholder="{{Search by any field}}"/>
					</div>

					<div class="col right xs-1 form-submit">
						<button class="submit-search">
							<i class="fa fa-search color-main-dark"></i>
						</button>
					</div>
				</div>

				<div class="row am-results">
					<div class="row left am-record-list">
						<div class="am-record row left" data-row="__ac_template__" data-template-root="true">
							<div class="am-fields row left">
								<div class="am-checked col auto"><i class="on-selected fa fa-check"></i></div>

								<label for="record-cb-__ac_template__" class="am-record-info col auto left">
									<div class="am-field <?= slug($label); ?>" data-name="<?= $label; ?>"></div>
								</label>
							</div>

							<a data-confirm-modal="{{Are you sure you want to remove this <?= $title; ?>?}}" href="<?= site_url('manager/remove', $value . '=__ac_template__'); ?>" class="am-remove-record">
								<i class="fa fa-trash-o"></i>
							</a>
						</div>
					</div>

					<div class="no-results">{{There are no records matching your search.}}</div>
				</div>
			</div>

			<? if (!$is_ajax) { ?>
		</div>
	</div>
</div>

	<script type="text/javascript">
		$('.amp-manager').ampManager();
	</script>
<? } ?>

<?= $is_ajax ? '' : call(IS_ADMIN ? 'admin/footer' : 'footer'); ?>
