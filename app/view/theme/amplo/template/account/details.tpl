<?= $is_ajax ? '' : call('header'); ?>

<section class="row account-page content">
	<div class="content-box col xs-12 lg-8 xl-7 top">
		<div class="content">

			<div class="account-info">
				<h1>{{My Details}}</h1>

				<div class="forms">
					<form action="<?= site_url('account/save'); ?>" method="post" class="section row left" autocomplete="off">
						<div class="heading">
							<h3 class="on-click is-dormant" data-amp-toggle=".form-change-email">
								<span class="text">{{Email}}</span>
								<a class="on-dormant"><i class="fa fa-pencil"></i></a>
								<a class="on-active"><i class="fa fa-close"></i></a>
							</h3>
						</div>

						<div class="form-fields form-change-email is-dormant on-always">
							<div class="row left on-dormant">
								<div class="field email"><?= $customer['email'] ?></div>
							</div>

							<div class="row left on-active">
								<div class="input">
									<input type="email" name="email" value="" autocomplete="off" placeholder="{{New Email}}"/>
								</div>
								<div class="input">
									<input type="email" name="confirm_email" value="" autocomplete="off" placeholder="{{Confirm New Email}}"/>

									<div class="help">{{Note: This will update your username email at log in and all future correspondence emails will be sent to your new email address.}}</div>
								</div>
								<div class="input current-password">
									<input type="password" name="current_password" autocomplete="new-password" value="" placeholder="{{Enter Password}}"/>
								</div>
							</div>

							<div class="row left buttons submit xs-12 left on-active">
								<button data-loading="{{Saving...}}">{{Update My Email}}</button>
							</div>
						</div>
					</form>

					<form action="<?= site_url('account/save'); ?>" method="post" class="section row left">
						<div class="heading">
							<h3 class="on-click is-dormant" data-amp-toggle=".form-change-password">
								<span class="text">{{Password}}</span>
								<a class="on-dormant"><i class="fa fa-pencil"></i></a>
								<a class="on-active"><i class="fa fa-close"></i></a>
							</h3>
						</div>

						<div class="form-fields form-change-password is-dormant on-always">
							<div class="row left on-dormant">
								<div class="field password-field">XXXXXXXX</div>
							</div>

							<div class="row left on-active">
								<div class="input">
									<input type="password" name="current_password" autocomplete="new-password" placeholder="{{Old Password}}"/>
								</div>
								<div class="input">
									<input type="password" name="password" autocomplete="new-password" placeholder="{{New Password}}"/>
								</div>
								<div class="input">
									<input type="password" name="confirm" autocomplete="new-password" placeholder="{{Confirm New Password}}"/>
								</div>
							</div>

							<div class="row left buttons submit on-active">
								<button data-loading="{{Saving...}}">{{Update My Password}}</button>
							</div>
						</div>
					</form>

					<form action="<?= site_url('account/save'); ?>" method="post" class="section row left">
						<div class="heading">
							<h3 class="on-click is-dormant" data-amp-toggle=".form-change-contact">
								<span class="text">{{Contact Info}}</span>
								<a class="on-dormant"><i class="fa fa-pencil"></i></a>
								<a class="on-active"><i class="fa fa-close"></i></a>
							</h3>
						</div>

						<div class="form-fields form-change-contact is-dormant on-always">
							<div class="row left on-dormant">
								<div class="field name">
									<span class="field first_name"><?= $customer['first_name']; ?></span>
									<span class="field last_name"><?= $customer['last_name']; ?></span>
								</div>
								<div class="field phone"><?= $customer['phone']; ?></div>
							</div>


							<div class="row left on-active">
								<div class="input">
									<input type="text" name="first_name" value="<?= $customer['first_name']; ?>" placeholder="{{First Name}}"/>
								</div>
								<div class="input">
									<input type="text" name="last_name" value="<?= $customer['last_name']; ?>" placeholder="{{Last Name}}"/>
								</div>
								<div class="input">
									<input type="tel" name="phone" value="<?= $customer['phone']; ?>" placeholder="{{Phone #}}"/>
								</div>
							</div>

							<div class="row left buttons submit on-active">
								<button data-loading="{{Saving...}}">{{Save}}</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>

<?= $is_ajax ? '' : call('footer'); ?>
