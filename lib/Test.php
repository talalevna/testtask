<?php

class Test {
	private $db;
	private $page = 1;
	private $pagenumMax = 1;
	private $clientsPerPage = 10;
	private $action = "clients";
	private $actions = ["clients", "edit", "delete", "register"];
	private $statuses = array();

	public function __construct() {
		$page = (isset($_GET['page']) && is_numeric($_GET['page']))?$_GET['page']:NULL;
		$action = (isset($_GET['action']))?$_GET['action']:NULL;
		if($page) $this->page = $page;
		if($action && in_array($action, $this->actions)) $this->action = $action;
		require "/lib/DB.php";
		$this->db = new DB();
		// $lp = getLastPage();
		// var_dump($this->pagenumMax); die;
		if(!isset($_SESSION['pagenum'])) {
			$_SESSION['pagenum'] = 1;
		}
	}

	private function getLastPage() {

	}

	private function init() {
		$statusResult=$this->db->link->query("SELECT * FROM `client_statuses`");
		if(!$statusResult)
		{
			$this->renderPage("error", null, null, "Load statuses error");
			die;
		}
		$statuses=$statusResult->fetch_all(MYSQLI_ASSOC);
		foreach($statuses as $key => $value) {
			$this->statuses[$value['id']] = $value['name'];
		}
	}

	private function refValues($arr) { 
		if (strnatcmp(phpversion(),'5.3') >= 0)
		{ 
			$refs = array(); 
			foreach($arr as $key => $value) 
				$refs[$key] = &$arr[$key]; 
			return $refs; 
		} 
		return $arr; 
	}

	private function query()
	{
		try
		{
		if(!$this->db->link)
		{
			return false;
		}
		$NumArgs=func_num_args();
		if($NumArgs<3)
		{
			return false;
		}
		$sql=func_get_arg(0);
		$pattern=func_get_arg(1);
		if(!$stmt=$this->db->link->prepare($sql))
		{
			return false;
		}
		$params=array();
		for($i=2;$i<$NumArgs;$i++)
		{
			$params[]=func_get_arg($i);
		}
		if($stmt->param_count!=strlen($pattern)||sizeof($params)!=strlen($pattern))
		{
			return false;
		}
		if(!call_user_func_array('mysqli_stmt_bind_param', array_merge (array($stmt, $pattern), $this->refValues($params))))
		{
			return false;
		}
		if(!$stmt->execute())
		{
			return false;
		}
		$result=$stmt->get_result();
		if(!$result)
		{
			$result=$stmt;
		}
		return $result;
		}
		catch(Exception $e)
		{
			return false;
		}
	}

	private function CheckID()
	{
		$result=true;
		$NumArgs=func_num_args();
		for($i=0;$i<$NumArgs;$i++)
		{
			$tmp=func_get_arg($i);
			if(!(isset($tmp)&&strlen($tmp)>0))
			{
				$result=false;
			}
			if(!is_int((int)$tmp))
			{
				return false;
			}
			$tmp2=(int)$tmp;
			if($tmp!=$tmp2)
			{
				return false;
			}
		}
		return true;
	}

	private function GOOUT($adress="/")
	{
		try {
			session_destroy();
			if(headers_sent())
			{
				throw new Exception();
			}
			header('Location: '.$adress);
		} catch(Exception $e) {
			echo '<script type="text/javascript">';
			echo 'window.location.href="'.$adress."\";";
			echo '</script>';
		}
		exit();
	}
	
	private function renderPage($tpl=null, $clients=array(), $statuses=array(), $message='', $continue=false) {
		if($tpl) {
			require "/lib/".$tpl.".php";
		} else {
			$message = "Template error";
			require "/lib/error.php";
		}
		if(!$continue) die;
	}

	private function getMaxNumPage() {
		if(!$clientsResult=$this->db->link->query("SELECT COUNT(*) as `num` FROM `client`")) {
			$this->renderPage("error", null, null, "Не удалось получить список клиентов".$this->db->link->error);
		}
		$clientsCountArr = $clientsResult->fetch_assoc();
		if(($clientsCountArr['num'] % $this->clientsPerPage) > 0) {
			$this->pagenumMax = (int)floor($clientsCountArr['num']/$this->clientsPerPage) + 1;
		} else {
			$this->pagenumMax = (int)$clientsCountArr['num']/$this->clientsPerPage;
		}
	}

	private function clients() {
		if(isset($_GET["pagenum"]) && $this->CheckID($_GET["pagenum"]) && $_GET["pagenum"] >= 1) {
			$_SESSION['pagenum'] = $_GET["pagenum"];
		}
		if(!$clientsResult=$this->db->link->query("SELECT * FROM `client` ORDER BY `cID` DESC LIMIT ".($this->clientsPerPage*($_SESSION['pagenum']-1)).",".$this->clientsPerPage)) {
			$this->renderPage("error", null, null, "Внутренняя ошибка системы. Не удалось получить список клиентов".$this->db->link->error);
		}
		$clients=array();
		while($tmp=$clientsResult->fetch_assoc()) {
			$clients[$tmp["cID"]]=$tmp;
		}
		$this->getMaxNumPage();
		$this->renderPage("page1", $clients, $this->statuses);
	}

	private function edit() {
		if(!isset($_GET['id']) || !is_int((int)$_GET['id'])) {
			$this->renderPage("error", null, null, "No ID");
		}
		if( isset($_POST['cStatus']) ) {
			$stmt = $this->db->link->prepare("UPDATE `client` SET `cStatus`=? WHERE `cID`=?");
			if(!$stmt) {
				$this->renderPage("error", null, null, "DB error");
			}
			$stmt->bind_param("ii", $_POST['cStatus'], $_GET['id']);
			if(!$stmt->execute()) {
				$this->renderPage("error", null, null, "Update error", true);
			}
			// echo "fukk"; die;
			header("Location: ?page=1&pagenum=".$_SESSION['pagenum']);
		}
		$clientResult=$this->db->link->query("SELECT * FROM `client` WHERE `cID`=".$_GET["id"]);
		if(!$clientResult || $clientResult->num_rows!=1)
		{
			$this->renderPage("error", null, null, "No clients");
		}
		$clientInfo=$clientResult->fetch_assoc();
		$this->renderPage("edit", $clientInfo, $this->statuses);
	}

	private function delete() {
		if(!isset($_GET['id']) || !is_int((int)$_GET['id'])) {
			$this->renderPage("error", null, null, "No ID");
		}
		$stmt = $this->db->link->prepare("DELETE FROM `client` WHERE `cID`=?");
		if(!$stmt) {
			$this->renderPage("error", null, null, "DB error");
		}
		$stmt->bind_param("i", $_GET['id']);
		if(!$stmt->execute()) {
			$this->renderPage("error", null, null, "Update error", true);
		}
		header("Location: ?page=1&pagenum=".$_SESSION['pagenum']);
	}

	private function register() {
		if(isset($_POST['cFIO']) && isset($_POST['cPhone']) && isset($_POST['cStatus'])) {
			// if(!isset($_POST["registered"])||strlen(trim($_POST["registered"]))==0||!strtotime($_POST["registered"])||strtotime($_POST["registered"])>time()) {
			// 	$registered=date("Y-m-d H:i:s");
			// } else {
			// 	$registered=date("Y-m-d H:i:s",strtotime($_POST["registered"]));
			// }
			$stmt = $this->db->link->prepare("INSERT INTO `client` (`cFIO`,`cPhone`,`cStatus`,`cRegistered`) VALUES(?,?,?,?)");
			if(!$stmt) {
				$this->renderPage("error", null, null, "DB error");
			}
			$stmt->bind_param("ssis", $_POST["cFIO"], $_POST["cPhone"], $_POST["cStatus"], $registered);
			if(!$stmt->execute()) {
				$this->renderPage("error", null, null, "Update error");
			}
			header("Location: ?page=1&pagenum=".$_SESSION['pagenum']);
		}
		$this->renderPage("register", null, $this->statuses);
	}

	public function go() {
		$this->init();
		if($this->page == 1) {
			if($this->action == "clients") {
				$this->clients();
			}
			if($this->action == "edit") {
				$this->edit();
			}
			if($this->action == "delete") {
				$this->delete();
			}
			if($this->action == "register") {
				$this->register();
			}
		}
		if($this->page == 2) {
			$this->renderPage("page2");
		}
	}
}

?>