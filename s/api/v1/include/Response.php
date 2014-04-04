<?php
require_once 'ACK.php';

class Response extends ACK
{
	public $Data = '';
	public $Type = '';

	public function __construct($status='Success', $message='', $data = '', $type = 'json', $error = null){
		$this->Data = $data;
		$this->Type = $type;
		parent::__construct($status, $message, $error);
	}
}
?>