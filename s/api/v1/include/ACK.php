<?php
require_once 'APIHeader.php';
require_once 'Error.php';

class ACK extends APIHeader
{
	public $Status = '';
	public $Message = '';
	public $Error;
	public function __construct($status, $message, $error = null){
		parent::__construct();
		if($error === null){
			$error = new Error();
		}
		$this->Error = $error;
		$this->Status = $status;
		$this->Message = $message;
	}
}
?>
