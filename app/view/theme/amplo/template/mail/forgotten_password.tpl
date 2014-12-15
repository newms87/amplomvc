<?= call('mail/header'); ?>

<p><?= _l("A new password was requested for your account with us at %s.", option('config_name')); ?></p>
<br/>
<br/>

<p>{{To reset your password please visit this link:}}</p>
<a href="<?= $reset; ?>"><?= $reset; ?></a>
<br/>
<br/>

<p>{{If you did not request for you password to be reset, please ignore this email.}}</p>

<?= call('mail/footer'); ?>
