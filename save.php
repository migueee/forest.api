<?php
//include_once('config.php');
include_once('db.php');

class save{
  private $db;
  private $user;

  public function __construct($user){
    $this->db = new db();
    $this->user=$user;
  }

  //estacion integer,tem integer,hum integer,hora integer,fecha date,presion integer,v integer,dir integer
  public function saveData($estacion, $tem, $hum, $hora, $fecha, $presion, $v, $dir){
    //$sql=" SELECT insert_data_WS(3,50,90,1,'21-08-2015',10,80,1) AS result";
    $sql=" SELECT insert_data_WS('".$estacion."', '".$tem."', '".$hum."', '".$hora."', '".$fecha."', '".$presion."', '".$v."', '".$dir."') AS result";
    
    $res = $this->db->exeQuery($sql);

    $rawdata = array(); //creamos un array

    while($row = pg_fetch_array($res)) $rawdata["result"] = $row["result"];

    return json_encode($rawdata); 
  }

}