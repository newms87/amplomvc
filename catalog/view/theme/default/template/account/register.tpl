<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>

		<div class="section">
			<h1><?= $head_title; ?></h1>

			<?= $content_top; ?>

			<p><?= $text_account_already; ?></p>

			<div class="section">
				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">

					<div class="customer section left">
						<h2><?= $text_your_details; ?></h2>

						<table class="form">
							<tr>
								<td class="required"> <?= $entry_firstname; ?></td>
								<td><input type="text" name="firstname" value="<?= $firstname; ?>"/></td>
							</tr>
							<tr>
								<td class="required"> <?= $entry_lastname; ?></td>
								<td><input type="text" name="lastname" value="<?= $lastname; ?>"/></td>
							</tr>
							<tr>
								<td class="required"> <?= $entry_email; ?></td>
								<td><input type="text" name="email" value="<?= $email; ?>"/></td>
							</tr>
						</table>

						<h2><?= $text_your_password; ?></h2>

						<table class="form">
							<tr>
								<td class="required"> <?= $entry_password; ?></td>
								<td><input type="password" autocomplete="off" name="password" value="<?= $password; ?>"/></td>
							</tr>
							<tr>
								<td class="required"> <?= $entry_confirm; ?></td>
								<td><input type="password" autocomplete="off" name="confirm" value="<?= $confirm; ?>"/></td>
							</tr>
						</table>

						<h2><?= $text_newsletter; ?></h2>

						<table class="form">
							<tr>
								<td><?= $entry_newsletter; ?></td>
								<td><?= $this->builder->build('radio', $data_yes_no, 'newsletter', $newsletter); ?></td>
							</tr>
						</table>
					</div>

					<div class="address section right">
						<h2><?= $text_your_address; ?> </h2>

						<table class="form">
							<tr>
								<td><?= $entry_company; ?></td>
								<td><input type="text" name="company" value="<?= $company; ?>"/></td>
							</tr>
							<tr>
								<td class="required"> <?= $entry_address_1; ?></td>
								<td><input type="text" name="address_1" value="<?= $address_1; ?>"/></td>
							</tr>
							<tr>
								<td><?= $entry_address_2; ?></td>
								<td><input type="text" name="address_2" value="<?= $address_2; ?>"/></td>
							</tr>
							<tr>
								<td class="required"> <?= $entry_city; ?></td>
								<td><input type="text" name="city" value="<?= $city; ?>"/></td>
							</tr>
							<tr>
								<td class="required"> <?= $entry_postcode; ?></td>
								<td><input type="text" name="postcode" value="<?= $postcode; ?>"/></td>
							</tr>
							<tr>
								<td class="required"> <?= $entry_country; ?></td>
								<td>
									<?= $this->builder->setConfig('country_id', 'name'); ?>
									<?= $this->builder->build('select', $countries, "country_id", $country_id, array('class' => "country_select")); ?>
								</td>
							</tr>
							<tr>
								<td class="required"> <?= $entry_zone; ?></td>
								<td><select name="zone_id" class="zone_select" zone_id="<?= $zone_id; ?>"></select></td>
							</tr>
						</table>
					</div>

					<div class="clear buttons">
						<div class="right">
							<? if ($text_agree) { ?>
								<?= $this->builder->build('checkbox', array(1 => $text_agree), 'agree', $agree); ?>
							<? } ?>

							<input type="submit" value="<?= $button_continue; ?>" class="button"/>
						</div>
					</div>
				</form>
			</div>

			<?= $content_bottom; ?>
		</div>
	</div>

<?= $this->builder->js('load_zones', 'table.form', '.country_select', '.zone_select'); ?>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
