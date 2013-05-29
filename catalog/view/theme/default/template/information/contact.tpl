<?= $header; ?><?= $column_left; ?><?= $column_right; ?>
<div id="content"><?= $content_top; ?>
	<?= $this->builder->display_breadcrumbs(); ?>
	<?= $this->builder->display_errors($errors); ?>
	<h1><?= $heading_title; ?></h1>
	<form id='contact_form' action="<?= $action; ?>" method="post" enctype="multipart/form-data">
		<h3><?= $text_contact_us; ?></h3>
		<h3><?= $text_contact_info; ?></h3>
		<h2><?= $text_contact; ?></h2>
		<div class="content">
		<b><?= $entry_name; ?></b><br />
		<input type="text" name="name" value="<?= $name; ?>" />
		<br />
		<br />
		<b><?= $entry_email; ?></b><br />
		<input type="text" name="email" value="<?= $email; ?>" />
		<br />
		<br />
		<b><?= $entry_enquiry; ?></b><br />
		<textarea name="enquiry" cols="40" rows="10" style="width: 99%;"><?= $enquiry; ?></textarea>
		<br />
		<br />
		<b><?= $entry_captcha; ?></b><br />
		<input type="text" name="captcha" value="<?= $captcha; ?>" />
		<br />
		<img src="<?= $captcha_url; ?>" alt="" />
		</div>
		<div class="buttons">
			<div class="right"><input type="submit" value="<?= $button_submit; ?>" class="button" /></div>
		</div>
	</form>
	
	<h3>You can also follow us on:</h3>
		<table border="0">
	<tbody>
		<tr>
		<td><a title="Members Newsletter" href="http://www.bettyconfidential.com/ci/membership.html"><img title="Members Newsletter" src="http://images.bettyconfidential.com/img/icons/email_icon_2.jpg" alt="Members Newsletter" height="51"></a></td>
		<td><a title="FaceBook" href="http://www.facebook.com/bettyconfidential" target="_blank"><img title="FaceBook" src="http://images.bettyconfidential.com/img/icons/facebook_52x51.gif" alt="FaceBook" width="52" height="51"></a></td>
		<td><a title="Twitter" href="http://twitter.com/bettybuzz" target="_blank"><img title="Twitter" src="http://images.bettyconfidential.com/img/icons/twitter_52x51.gif" alt="Twitter" width="52" height="51"></a></td>
		<td><a title="YouTube" href="http://www.youtube.com/bettyconfidential" target="_blank"><img title="YouTube" src="http://images.bettyconfidential.com/img/icons/youtube_52x51.gif" alt="YouTube" width="52" height="51"></a></td>
		<td><a title="MySpace" href="http://www.myspace.com/bettyconfidential" target="_blank"><img title="MySpace" src="http://images.bettyconfidential.com/img/icons/myspace_52x51.gif" alt="MySpace" width="52" height="51"></a></td>
		<td><a title="RSS" href="http://www.bettyconfidential.com/cbc/rss.html"><img title="RSS" src="http://images.bettyconfidential.com/img/icons/rss_52x51.gif" alt="RSS" width="52" height="51"></a></td>
		<td><a title="Apple App Store" href="http://itunes.apple.com/app/bettyconfidential/id342264269?mt=8" target="_blank"><img title="Apple App Store" src="http://s2.bettyconfidential.com/img/icons/iphone_icon_1.gif" alt="Apple App Store" height="51"></a></td>
		<td>&nbsp;</td>
		</tr>
		<tr>
		<td style="text-align: center;"><span style="font-size: x-small;"><a title="Members Newsletter" href="http://www.bettyconfidential.com/ci/membership.html">Email</a></span></td>
		<td style="text-align: center;"><span style="font-size: x-small;"><a title="FaceBook" href="http://www.bettyconfidential.com/facebook" target="_blank">FaceBook</a></span></td>
		<td style="text-align: center;"><span style="font-size: x-small;"><a title="Twitter" href="http://www.bettyconfidential.com/twitter/bettybuzz" target="_blank">Twitter</a></span></td>
		<td style="text-align: center;"><span style="font-size: x-small;"><a title="YouTube" href="http://www.youtube.com/bettyconfidential" target="_blank">YouTube</a></span></td>
		<td style="text-align: center;"><span style="font-size: x-small;"><a title="MySpace" href="http://www.bettyconfidential.com/myspace" target="_blank">MySpace</a></span></td>
		<td style="text-align: center;"><span style="font-size: x-small;"><a title="RSS" href="/cbc/rss.html">RSS</a></span></td>
		<td style="text-align: center;"><span style="font-size: x-small;"><a title="Apple App Store" href="http://itunes.apple.com/app/bettyconfidential/id342264269?mt=8" target="_blank">App </a></span></td>
		</tr>
	</tbody>
	</table>
		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
		</div>
	
	<?= $content_bottom; ?></div>
	
	<?= $this->builder->js('errors',$errors); ?>
<?= $footer; ?>