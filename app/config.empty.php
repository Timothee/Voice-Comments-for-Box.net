<?php

class Config {
  
	const root_url = "FILL_ME_OUT"; // of the form 'http://www.example.com' without a trailing slash
	/**
	 * Returns true if localhost
	 */
  function isTestEnv() {
    return $_SERVER['SERVER_NAME'] == 'localhost';
  } // isTestEnv
  
	/**
	 * Returns true if in production environment
	 */
  function isProductionEnv() {
    return $_SERVER['SERVER_NAME'] == 'FILL_ME_OUT'; // of the form 'www.example.com'
  } // isProductionEnv
  
	/**
	 * Returns the API key for Box.net
	 */
  function boxDotNetApiKey() {
    if (Config::isProductionEnv()) {
      return 'FILL_ME_OUT'; // to get from your Box.net developer account
    } else {
      return 'FILL_ME_OUT'; // to get from your Box.net developer account
    }
  } // boxDotNetApiKey

	/**
	 * Returns an array with your Twilio credentials
	 * in order: AccountSid, token and ApplicationSid
	 */
	function twilioConfig() {
		return array('AC12345_FILL_ME_OUT', '12345_FILL_ME_OUT', 'AP12345_FILL_ME_OUT'); // to get from your Twilio account
	} // twilioConfig
  
} // Config

?>
