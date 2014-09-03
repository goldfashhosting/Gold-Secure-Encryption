<?php
/*
 *    Updates Plugin
 */
function my_github_plugin_updater() {

	if ( ! function_exists( 'github_plugin_updater_register' ) )
		return false;

	github_plugin_updater_register( array(
		'owner'	=> 'goldfashhosting',
		'repo'	=> 'GoldFash-Secure-Encryption',
		'slug'	=> 'GoldFash-Secure-Encryption/goldsecure.php', // defaults to the repo value ('repo/repo.php')
	) );
}

