<?php
include_once('config.php');

class db{
 	
 	public function exeQuery($sql){
	
		  $strCnx = "host=".config::getHost()." dbname=".config::getDB()." user=".config::getUser()." password=".config::getPwd();
		  $con = pg_connect($strCnx);
		  if(!$con) die ("Error de conexion. ". pg_last_error());
		  
		  //$sql = "SELECT * FROM lemu_usuario";
		  $res = pg_query($con, $sql); 
		  
		  pg_close($con);
		  return $res;
 	}

}
?>