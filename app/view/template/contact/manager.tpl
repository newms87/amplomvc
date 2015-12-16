<?= $is_ajax ? '' : call('header'); ?>

<? if (!$is_ajax) { ?>
<div class="contact-manager-page">
	<div class="row header-row">
		<div class="wrap">
			<h1>{{My Contacts}}</h1>
		</div>
	</div>

	<div class="amp-contact-manager" class="row">
		<div class="wrap">
			<? } ?>

			<div class="contact-search">
				<h3>{{Client:}}</h3>

				<div data-action="<?= site_url('contact/manager/listing'); ?>" class="row left amp-nested-form contact-search-form">
					<div class="help">{{Search for an existing client by company, name, email or phone}}</div>

					<div class="col left xs-11 form-input">
						<input type="text" name="filter[keywords]" value="" placeholder="{{Enter any client info to search}}"/>
					</div>

					<div class="col right xs-1 form-submit">
						<button class="submit-search">
							<i class="fa fa-search color-main-dark"></i>
						</button>
					</div>
				</div>

				<div class="row contact-results">
					<div class="row left add-contact">
						<div class="add">
							<i class="fa fa-plus"></i>
							<span class="text">{{Add New Client}}</span>
						</div>
						<div class="cancel">
							<i class="fa fa-close"></i>
							<span class="text">{{Cancel}}</span>
						</div>
					</div>

					<div class="row left contact-list">
						<div class="contact row left" data-row="__ac_template__" data-template-root="true">
							<input id="contact-cb-__ac_template__" type="radio" name="contact_id" class="contact-id-group" value="__ac_template__"/>

							<label for="contact-cb-__ac_template__" class="contact-info">
								<div class="field company" data-name="company"></div>

								<div class="field full-name">
									<span class="label">{{Name:}}</span>
									<span class="first-name value" data-name="first_name"></span>
									<span class="last-name value" data-name="last_name"></span>
								</div>
								<div class="field email">
									<span class="label">{{Email:}}</span>
									<span class="value" data-name="email"></span>
								</div>
								<div class="field phone">
									<span class="label">{{Phone:}}</span>
									<span class="value" data-name="phone"></span>
								</div>

								<div data-action="<?= site_url('contact/manager/save', 'contact_id=__ac_template__'); ?>" class="row left contact-form form amp-nested-form">
									<div class="col xs-12 sm-8 md-6 left">
										<div class="form-item company">
											<input type="text" name="company" value="" placeholder="{{Company Name}}"/>
										</div>
										<div class="form-item first-name">
											<input type="text" name="first_name" value="" placeholder="{{First Name}}"/>
										</div>
										<div class="form-item last-name">
											<input type="text" name="last_name" value="" placeholder="{{Last Name}}"/>
										</div>
										<div class="form-item email">
											<input type="text" name="email" value="" placeholder="{{Email}}"/>
										</div>
										<div class="form-item company">
											<input type="text" name="phone" value="" placeholder="{{Phone}}"/>
										</div>

										<div class="buttons">
											<button data-loading="{{Saving...}}">{{Save Client}}</button>
										</div>
									</div>
								</div>

								<div class="form-buttons">
									<a class="edit-contact edit-form">
										<i class="fa fa-pencil"></i>
										{{Edit Client Info}}
									</a>
									<a class="cancel-contact cancel-form">
										<i class="fa fa-close"></i>
										{{Cancel}}
									</a>
								</div>
							</label>

							<a data-confirm-modal="{{Are you sure you want to remove this client?}}" href="<?= site_url('contact/manager/remove', 'contact_id=__ac_template__'); ?>" class="remove-contact">
								<i class="fa fa-trash-o"></i>
							</a>
						</div>
					</div>

					<div class="no-results">{{There are no clients matching your search.}}</div>

					<div data-action="<?= site_url('contact/manager/save'); ?>" class="row left new-contact-form contact-form form amp-nested-form">
						<div class="col xs-12 sm-8 md-6 left">
							<div class="form-item company">
								<input type="text" name="company" value="" placeholder="{{Company Name}}"/>
							</div>
							<div class="form-item first-name">
								<input type="text" name="first_name" value="" placeholder="{{First Name}}"/>
							</div>
							<div class="form-item last-name">
								<input type="text" name="last_name" value="" placeholder="{{Last Name}}"/>
							</div>
							<div class="form-item email">
								<input type="text" name="email" value="" placeholder="{{Email}}"/>
							</div>
							<div class="form-item company">
								<input type="text" name="phone" value="" placeholder="{{Phone}}"/>
							</div>

							<div class="buttons">
								<button data-loading="{{Saving...}}">{{Save Client}}</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<? if (!$is_ajax) { ?>
		</div>
	</div>
</div>

	<script type="text/javascript">
		$('.amp-contact-manager').ampContactManager();
	</script>
<? } ?>

<?= $is_ajax ? '' : call('footer'); ?>
