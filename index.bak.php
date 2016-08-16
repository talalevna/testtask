<?php 
$host="localhost";
$login="test";
$pass="testpass";
$base="test";

$_GET["id"]=1;
$_POST["newStatus"]=2;
$_GET["editUser"]=true;
//$_POST["FIO"]="Даня";$_POST["Phone"]="+79";$_POST["registered"]="10.07.2016";$_GET["editUser"]=true;

try{
if(!$link=new mysqli($host,$login,$pass,$base))
{
	throw new Exception("Не удалось подключиться к базе данных");
}
$link->set_charset("utf8");
function refValues($arr)
{ 
	if (strnatcmp(phpversion(),'5.3') >= 0)
	{ 
		$refs = array(); 
		foreach($arr as $key => $value) 
			$refs[$key] = &$arr[$key]; 
		return $refs; 
	} 
	return $arr; 
}
function query()
{
	global $link;
	try
	{
	if(!$link)
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
	if(!$stmt=$link->prepare($sql))
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
	if(!call_user_func_array('mysqli_stmt_bind_param', array_merge (array($stmt, $pattern), refValues($params))))
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
function CheckID()
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
		if(!is_numeric($tmp))
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
function GOOUT($adress="/")
{
	try
	{
		if(headers_sent())
		{
			throw new Exception();
		}
		header('Location: '.$adress);
	}
	catch(Exception $e)
	{
		echo '<script type="text/javascript">';
		echo 'window.location.href="'.$adress."\";";
		echo '</script>';
	}
	exit();
}
if(isset($_GET["id"]))
{
	if(!CheckID($_GET["id"]))
	{
		GOOUT();
	}
	if(isset($_GET["editUser"]))
	{
		// if(!$typeResult = $link->query( "SHOW COLUMNS FROM `client` WHERE Field = 'cStatus'" ))
		// {
		// 	throw new Exception("Не удалось получить список допустимых состояний клиента".$link->error);
		// }
		// $type=$typeResult->fetch_assoc()["Type"];
		// preg_match_all("/'(.*?)'/", $type, $matches);
		// $enum = $matches[1];
		// if(!in_array($_POST["newStatus"],$enum))
		// {
		// 	throw new Exception("Недопустимый статус ".$_POST["newStatus"]);
		// }
		if(!query("UPDATE `client` SET `cStatus`=? WHERE `cID`=?","si",$_POST["newStatus"],$_GET["id"]))
		{
			throw new Exception("Не удалось обновить статус");
		}
		//GOOUT("/?id=".$_GET["id"]);
	}
	if(!$clientResult=query("SELECT * FROM `client` WHERE `cID`=?","i",$_GET["id"]))
	{
		throw new Exception("Внутренняя ошибка системы");
	}
	if($clientResult->num_rows!=1)
	{
		throw new Exception("Клиент не найден");
	}
	$clientInfo=$clientResult->fetch_assoc();
	print_r($clientInfo);
	
}
else if(isset($_GET["regUser"]))
{
	if(!isset($_POST["registered"])||strlen(trim($_POST["registered"]))==0||!strtotime($_POST["registered"])||strtotime($_POST["registered"])>time())
	{
		$_POST["registered"]=date("Y-m-d H:i:s");
	}
	else
	{
		$_POST["registered"]=date("Y-m-d H:i:s",strtotime($_POST["registered"]));
	}
	if(!query("INSERT INTO `client` (`cFIO`,`cPhone`,`cStatus`,`cRegistered`) VALUES(?,?,'Новый',?)","sss",$_POST["FIO"],$_POST["Phone"],$_POST["registered"]))
	{
		throw new Exception("Внутренняя ошибка системы. Не удалось добавить клиента");
	}
	GOOUT("/?id=".$link->insert_id);
}
else
{
	if(!isset($_GET["page"])||!CheckID($_GET["page"])||$_GET["page"]<1)
	{
		$_GET["page"]=1;
	}
	$_GET["page"]--;
	if(!$clientsResult=$link->query("SELECT * FROM `client` LIMIT ".(20*$_GET["page"]).",20"))
	{
		throw new Exception("Внутренняя ошибка системы. Не удалось получить список клиентов".$link->error);
	}
	$clients=array();
	while($tmp=$clientsResult->fetch_assoc())
	{
		$clients[$tmp["cID"]]=$tmp;
	}
	echo "<pre>";
	print_r($clients);
}
}
catch(Exception $e)
{
	echo $e->getMessage();
	die();
}

?>