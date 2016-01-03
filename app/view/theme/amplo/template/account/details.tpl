<div class="account-info">
	<h1>{{My Details}}</h1>

	<div class="forms">
		<form action="<?= site_url('account/save'); ?>" method="post" class="section form-editor read col xs-12 left" autocomplete="off">
			<div class="heading">
				<h3>
					<span class="text">{{Email}}</span>
					<a class="edit-form reading"><i class="fa fa-pencil"></i></a>
					<a class="editing cancel-form"><i class="fa fa-close"></i></a>
				</h3>
			</div>

			<div class="field email reading"><?= $customer['email']; ?></div>

			<div class="input editing col xs-12 left">
				<div class="input">
					<input type="text" name="email" value="" autocomplete="off" placeholder="{{New Email}}"/>
				</div>
				<div class="input">
					<input type="text" name="confirm_email" value="" autocomplete="off" placeholder="{{Confirm New Email}}"/>

					<div class="help">{{Note: This will update your username email at log in and all future correspondence emails will be sent to your new email address.}}</div>
				</div>
				<div class="input current-password">
					<input type="password" name="current_password" autocomplete="new-password" value="" placeholder="{{Enter Password}}"/>
				</div>
			</div>

			<div class="buttons submit editing col no-whitespace-hack xs-12 left">
				<div class="col xs-12 md-4">
					<button data-loading="{{Saving...}}">{{Update My Email}}</button>
				</div>
			</div>
		</form>

		<form action="<?= site_url('account/save'); ?>" method="post" class="section form-editor read col xs-12 left">
			<div class="heading">
				<h3>
					<span class="text">{{Password}}</span>
					<a class="edit-form reading"><i class="fa fa-pencil"></i></a>
					<a class="editing cancel-form"><i class="fa fa-close"></i></a>
				</h3>
			</div>

			<div class="field password-field reading">XXXXXXXX</div>

			<div class="input editing col xs-12 left">
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

			<div class="buttons submit editing col no-whitespace-hack xs-12 left">
				<div class="col xs-12 md-4">
					<button data-loading="{{Saving...}}">{{Update My Password}}</button>
				</div>
			</div>
		</form>

		<form action="<?= site_url('account/save'); ?>" method="post" class="section form-editor read col xs-12 left">
			<div class="heading">
				<h3>
					<span class="text">{{Contact Info}}</span>
					<a class="edit-form reading"><i class="fa fa-pencil"></i></a>
					<a class="editing cancel-form"><i class="fa fa-close"></i></a>
				</h3>
			</div>

			<div class="fields reading">

				<div class="field name">
					<span class="field first_name" data-name="first_name"><?= $customer['first_name']; ?></span>
					<span class="field last_name" data-name="last_name"><?= $customer['last_name']; ?></span>
				</div>
				<div class="field phone" data-name="phone"><?= $customer['phone']; ?></div>
			</div>

			<div class="input editing col xs-12 left">
				<div class="input">
					<input type="text" name="first_name" value="<?= $customer['first_name']; ?>" placeholder="{{First Name}}"/>
				</div>
				<div class="input">
					<input type="text" name="last_name" value="<?= $customer['last_name']; ?>" placeholder="{{Last Name}}"/>
				</div>
				<div class="input">
					<input type="text" name="phone" value="<?= $customer['phone']; ?>" placeholder="{{Phone #}}"/>
				</div>
			</div>

			<div class="buttons submit editing col no-whitespace-hack xs-12 right">
				<div class="col xs-12 md-4">
					<button data-loading="{{Saving...}}">{{Save}}</button>
				</div>
			</div>
		</form>
	</div>
</div>

