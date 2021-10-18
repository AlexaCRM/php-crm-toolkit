<?php


namespace AlexaCRM\CRMToolkit;

/**
 * Factory of SDK configuration classes, used to create instance of AlexaCRM\CRMToolkit\Client class
 * Class SettingsFactory
 * @package AlexaCRM\CRMToolkit
 */
class SettingsFactory {

	/**
	 * Returns proper type of settings based on $settingsOptions
	 *
	 * @param array $settingsOptions
	 *
	 * @return OnlineS2SSecretAuthenticationSettings|Settings
	 */
	public static function getSettings( array $settingsOptions ) {
		if ( isset( $settingsOptions['authMethod'] ) && $settingsOptions['authMethod'] === OnlineS2SSecretAuthenticationSettings::SETTINGS_TYPE ) {
			return new OnlineS2SSecretAuthenticationSettings( $settingsOptions );
		} else {
			return new Settings( $settingsOptions );
		}
	}

}
