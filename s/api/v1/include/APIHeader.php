<?php

class APIHeader
{
	public $Secret = '';
	public $Timestamp = '';
	public function __construct(){
		$this->Secret = $this->_GUID();
		$_SESSION["Secret"] = $this->Secret;
		$this->Timestamp = microtime(true);
	}
	private function _GUID()
	{
	    if (function_exists('com_create_guid') === true)
	    {
	        return trim(com_create_guid(), '{}');
	    }

	    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}

}


?>