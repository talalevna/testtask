<?php 

class DB {
	private $host="localhost";
	private $login="test";
	private $pass="testpass";
	private $base="test";
	public $link;

	//$_POST["FIO"]="Даня";$_POST["Phone"]="+79";$_POST["registered"]="10.07.2016";$_GET["regUser"]=true;

	public function __construct() {
		if(!$this->link=new mysqli($this->host,$this->login,$this->pass,$this->base))
		{
			throw new Exception("Не удалось подключиться к базе данных");
		}
		$this->link->set_charset("utf8");
	}

	public function save($cFIO, $cPhone, $cStatus, $cRegistered) {
		$stmt = $this->link->prepare("INSERT INTO `client` (`cFIO`,`cPhone`,`cStatus`,`cRegistered`) VALUES(?,?,?,?)");
		if(!$stmt) {
			return false;
		}
		$stmt->bind_param("ssis", $cFIO, $cPhone, $cStatus, $cRegistered);
		if(!$stmt->execute()) {
			return false;
		}
		return true;
	}

	public function update() {
	}

}

?>