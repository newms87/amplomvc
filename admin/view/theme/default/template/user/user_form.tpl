<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'user.png'; ?>" alt="" /> <?= $head_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<div id="tabs" class="htabs"><a href="#tab-general"><?= $tab_general; ?></a><a href="#tab-contact"><?= $tab_contact; ?></a></div>
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
			<div id='tab-general'>
				<table class="form">
					<tr>
						<td class="required"> <?= $entry_username; ?></td>
						<td><input type="text" name="username" value="<?= $username; ?>" /></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_firstname; ?></td>
						<td><input type="text" name="firstname" value="<?= $firstname; ?>" /></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_lastname; ?></td>
						<td><input type="text" name="lastname" value="<?= $lastname; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_email; ?></td>
						<td><input type="text" name="email" value="<?= $email; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_user_group; ?></td>
						<td>
							<? if($this->user->isTopAdmin()){?>
									<? $this->builder->set_config('user_group_id','name');?>
									<?= $this->builder->build('select',$user_groups, "user_group_id",(int)$user_group_id); ?>
							<? }else{?>
									<? foreach($user_groups as $ug){ if($ug['user_group_id'] == $user_group_id){?>
									<input type='hidden' name='user_group_id' value='<?= $user_group_id; ?>' />
									<div><?= $ug['name']; ?></div>
									<? }}?>
							<? }?>
						</td>
					</tr>
					<tr>
						<td><?= $entry_password; ?></td>
						<td><input type="password" autocomplete='off' name="password" value="<?= $password; ?>"	/></td>
					</tr>
					<tr>
						<td><?= $entry_confirm; ?></td>
						<td><input type="password" autocomplete='off' name="confirm" value="<?= $confirm; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_designers; ?></td>
						<td>
							<ul id='designer_list'><? if(isset($designers))
							foreach($designers as $d){
									echo "<li>";
									echo $this->builder->build('select',$manufacturers, 'designers[]',(int)$d);
									echo "<a onclick='$(this).parent().remove()'>remove</a>";
							}?>
							</ul>
							<a onclick="add_designer();"><?= $button_add_designer; ?></a>
						</td>
					</tr>
					<tr>
						<td><?= $entry_status; ?></td>
						<td><?= $this->builder->build('select',$statuses,'status',(int)$status); ?></td>
					</tr>
				</table>
			</div>
			<div id='tab-contact'>
					<?= $contact_template; ?>
				</div>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?>

<script type='text/javascript'><!--
function add_designer(){
	html = '<li>' + "<?= $this->builder->build('select',$manufacturers,'designers[]'); ?>" + '<a onclick="$(this).parent().remove();">remove</a></li>';
	$('#designer_list').append(html);
}
--></script>

<script type="text/javascript"><!--
$('#tabs a').tabs();
//--></script>

<?= $this->builder->js('errors', $errors); ?>