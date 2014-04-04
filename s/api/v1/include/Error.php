<?php
class Error
{
	public $Error = '';
	public $Message = '';

	public function __construct($error = '', $message = ''){
		$this->Error = $error;
		$this->Message = $message;
	}
}
?>
