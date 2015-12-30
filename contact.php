<?php
class contact
{
	public $FirstName;
	public $LastName;
	public $address;
	public $email;
	public $phone;
	
	public function __construct($FirstName, $LastName, $address, $email, $phone){
		$this->FirstName = $FirstName;
		$this->LastName = $LastName;
		$this->address = $address;
		$this->email = $email;
		$this->phone = $phone;
	}
	
	public function getFirstName(){return $this->FirstName;}
	public function getLastName(){return $this->LastName;}
	public function getaddress(){return $this->address;}
	public function getemail(){return $this->email;}
	public function getphone(){return $this->phone;}

	
}
?>