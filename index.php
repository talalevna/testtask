<?php 
//$_POST["FIO"]="Даня";$_POST["Phone"]="+79";$_POST["registered"]="10.07.2016";$_GET["regUser"]=true;

session_start();

require "/lib/Test.php";

$test = new Test();

$test->go();

?>