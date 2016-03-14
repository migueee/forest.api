<?php
//include_once('config.php');
include_once('db.php');

class img{
  private $db;
  private $user;

  public function __construct($user){
    $this->db = new db();
    $this->user=$user;
  }
//lemu_data.hor_id / 240)::character varying)::text) || ':'::text) || (((lemu_data.hor_id::integer % 240 / 4)
  public function getActual(){
    $sql="SELECT ID.id_mayor, ID.img_direccion, LF.fec_date as fec_id,((((ID.hor_id / 240)::character varying)::text) || ':'::text) || (((ID.hor_id::integer % 240 / 4)::character varying)::text) AS hor_id,
       ID.img_estado, ID.est_id
        from ID_MAYOR_IMG ID, lemu_usuario LU, lemu_estacion LE, lemu_fecha LF
        where ID.est_id=LE.est_id and ID.fec_id=LF.fec_id and LE.usu_id=LU.usu_id ";

    if($this->user!='admin') $sql=$sql." and lu.usu_nombre='".$this->user."' ";

      $res = $this->db->exeQuery($sql);

      $rawdata = array(); //creamos un array
      $add=array(); // sub array para agregar a rawdata

      while($row = pg_fetch_array($res)){

            $add["id"] = $row["id_mayor"];
            $add["direccion"] = $row["img_direccion"];
            $add["fecha"]= $row["fec_id"];
            $add["hora"]= $row["hor_id"];
            $add["estado"]= $row["img_estado"];
            $add["estacion"]= $row["est_id"];

            array_push ( $rawdata , $add );
              //$rawdata[$i] = $row;
              //$i++;
              //echo $row["usu_nombre"]." ";
          }
      return json_encode($rawdata); //genero json con el array ya asociado
    }


   public function save($direccion, $fecha, $hora,$estacion){
      $h= array();
      //divide en sub string delimitado por ':' para separar horas de minutos
      $h= explode(':',$hora);
      $hora=($h[0]*240)+($h[1]*4);
      if($h[2]>=0 && $h[2]<15)$hora+=0;
      if($h[2]>=15 && $h[2]<30)$hora+=1;
      if($h[2]>=30 && $h[2]<45)$hora+=2;
      if($h[2]>=45 && $h[2]<60)$hora+=3;

      $sql="SELECT insert_imagen_WS('".$estacion."','".$hora."', '".$fecha."','".$direccion."') ";
      //(estacion integer,hora integer,fecha date,direccion varchar)
      //select insert_imagen_WS(1,1,'10-09-2015','forestin_1/10092015_123020.jpg');

      $res = $this->db->exeQuery($sql);
      $rawdata = array(); //creamos un array
      while($row = pg_fetch_array($res)) $rawdata["result"] = $row["result"];
      return json_encode($rawdata);
  }



}
