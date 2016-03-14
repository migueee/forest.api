<?php
//include_once('config.php');
include_once('db.php');

class map{
  private $db;
  private $user;

  public function __construct($user){
    $this->db = new db();
    $this->user=$user;
  }

  public function getPoint(){
    $sql="SELECT le.est_id, le.est_estado, ST_X(est_ubicacion),ST_Y(est_ubicacion)
          FROM lemu_estacion le, lemu_usuario lu
          WHERE le.usu_id=lu.usu_id and lu.usu_nombre='".$this->user."' ";
    
    $res = $this->db->exeQuery($sql);

    $rawdata = array(); //creamos un array
    $add=array(); // sub array para agregar a rawdata

    while($row = pg_fetch_array($res)){
        
            $add["estacion"] = "Estacion ".$row["est_id"];
            $add["estado"] = $row["est_estado"];
            $add["x"] = $row["st_x"];
            $add["y"] = $row["st_y"];

            array_push ( $rawdata , $add );
          }

    return json_encode($rawdata); 
  }

  public function getPolygon(){
    //pasa el polygono a json
    $sql="SELECT le.est_id, le.est_estado, ST_AsGeoJSON(est_area) AS pol -- ST_AsText(est_area) as pol
          FROM lemu_estacion le, lemu_usuario lu
          WHERE le.usu_id=lu.usu_id and lu.usu_nombre='".$this->user."' ";
    
    $res = $this->db->exeQuery($sql);

    $rawdata = array(); //creamos un array
    $add=array(); // sub array para agregar a rawdata

    while($row = pg_fetch_array($res)){
        
            $add["estacion"] = "Estacion ".$row["est_id"];
            $add["estado"] = $row["est_estado"];
            // el json se decodifica para extraer solo el polygono 
            $add["polygon"] = json_decode($row["pol"])->coordinates[0];

            array_push ( $rawdata , $add );
          }

    return json_encode($rawdata); 
  }


  public function getPP(){
    //pasa el polygono a json
    $sql="SELECT le.est_id, le.est_estado,ST_X(est_ubicacion),ST_Y(est_ubicacion) , ST_AsGeoJSON(est_area) AS pol -- ST_AsText(est_area) as pol
          FROM lemu_estacion le, lemu_usuario lu
          WHERE le.usu_id=lu.usu_id and lu.usu_nombre='".$this->user."' ";
    
    $res = $this->db->exeQuery($sql);

    $rawdata = array(); //creamos un array
    $add=array(); // sub array para agregar a rawdata

    while($row = pg_fetch_array($res)){
        
            $add["estacion"] = "Estacion ".$row["est_id"];
            $add["estado"] = $row["est_estado"];

            $add["x"] = $row["st_x"];
            $add["y"] = $row["st_y"];

            // el json se decodifica para extraer solo el polygono 
            $add["polygon"] = json_decode($row["pol"])->coordinates[0];

            array_push ( $rawdata , $add );
          }

    return json_encode($rawdata); 
  }
  

}