<?php
//include_once('config.php');
include_once('db.php');

class access{
  private $db;
  
  public function __construct(){
    $this->db = new db();
  }

  public function getUser($user,$pass){
      $sql = "select usu_nombre,usu_correo,usu_fono from lemu_usuario where usu_nombre='".$user."' and usu_pass='".$pass."' ";
      //var_dump($sql);
      $res = $this->db->exeQuery($sql);

      $rawdata = array(); //creamos un array
      $add=array(); // sub array para agregar a rawdata

      //{"hora":1435637379,"generador":"jonas","validacion":"ok"}

      while($row = pg_fetch_array($res)){

            $add["hora"] = date("H:i:s");
            $add["generador"] = $row["usu_nombre"];
            $add["validacion"]= "ok";
            $add["correo"]= $row["usu_correo"];
            $add["fono"]= $row["usu_fono"];
            
            //array_push ( $rawdata , $add );
          }
      return json_encode($add); //genero json con el array ya asociado
    }
}