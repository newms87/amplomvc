<?php
/**
 * Mail Messages
 *
 * Version: 0.1
 * Title: Mail Messages
 * Description: This is a work in progress... doesn't do much right now.
 * Author: Daniel Newman
 * Date: 3/15/2013
 * Link: http://www.amplocart.com/plugins/mail
 *
 */

class Mail_Setup extends PluginSetup
{

	public function install()
	{
		//TODO: Need to find a good way to adapt to the controller!
		$controller_adapters[] = array(
			'for'         => 'mail/messages',
			'admin'       => true,
			'plugin_file' => 'admin/mail',
			'callback'    => 'mail_settings',
			'priority'    => 0
		);
	}

	public function uninstall($keep_data = true)
	{
		//Nothing to do
	}
}