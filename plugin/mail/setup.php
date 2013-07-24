<?php
class Mail_Setup extends PluginSetup
{

	public function install()
	{
		//TODO: Need to find a good way to adapt to the controller!
		$controller_adapters[] = array(
			'for' 			=> 'mail/messages',
			'admin'			=> true,
			'plugin_file'	=> 'admin/mail',
			'callback'		=> 'mail_settings',
			'priority'		=> 0
		);
	}
		
	public function uninstall($keep_data = true)
	{
		//Nothing to do
	}
}