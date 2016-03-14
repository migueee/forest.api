<?php

include_once('db.php');

class wind {
  private $db;
  private $user;

  public function __construct($user){
    $this->db = new db();
    $this->user=$user;
  }

  public function getSpeedProm($user){
    $sql = "SELECT AVG(dat_velocidad) AS prom, est_id
            FROM lemu_data ld
            GROUP BY est_id
            ORDER BY est_id";

    $res     = $this->db->exeQuery($sql);
    $rawdata = array(); //creamos un array
    $add     = array(); // sub array para agregar a rawdata

    while($row = pg_fetch_array($res)){

          $add["y"] = round($row["prom"],2);
          $add["label"] = "Estacion ".$row["est_id"];

          array_push ( $rawdata , $add );
            //$rawdata[$i] = $row;
            //$i++;
            //echo $row["usu_nombre"]." ";
        }
    return json_encode($rawdata); //genero json con el array ya asociado
  }

  public function getSpeedActual(){
    $sql="SELECT ld.dat_velocidad, ld.est_id
          FROM lemu_data ld, lemu_estacion le, lemu_usuario lu, ID_MAYOR idm
          where ld.est_id=le.est_id and le.usu_id=lu.usu_id ";

    if($this->user!='admin') $sql=$sql." and lu.usu_nombre='".$this->user."' ";

    $sql=$sql." and idm.id_mayor=ld.dat_id
          GROUP BY ld.est_id,ld.dat_velocidad
          ORDER BY ld.est_id,ld.dat_velocidad";

    $res = $this->db->exeQuery($sql);

    $rawdata = array(); //creamos un array
    $add=array(); // sub array para agregar a rawdata

    while($row = pg_fetch_array($res)){

            $add["y"] = round($row["dat_velocidad"],2);
            $add["label"] = "Estacion ".$row["est_id"];

            array_push ( $rawdata , $add );
          }
    return json_encode($rawdata);
  }


  public function get24h(){
    $sql="SELECT  ld.dat_velocidad, (ld.hor_id/240) as hora,ld.est_id
          from  lemu_data ld, lemu_estacion le, lemu_usuario lu, lemu_fecha lf
          where   ld.est_id=le.est_id and le.usu_id=lu.usu_id ";

    if($this->user!='admin') $sql=$sql." and lu.usu_nombre='".$this->user."' ";
    
    $sql=$sql."and ld.fec_id=lf.fec_id 
          and lf.fec_date=current_date and ld.hor_id in(
            (0*240),(1*240),(2*240),(3*240),(4*240),(5*240),(6*240),(7*240),(8*240),(9*240),(10*240),(11*240),(12*240),
            (13*240),(14*240),(15*240),(16*240),(17*240),(18*240),(19*240),(20*240),(21*240),(22*240),(23*240),(24*240)
            )
          order by ld.est_id,ld.hor_id";


    $res = $this->db->exeQuery($sql);

    $rawdata = array(); //creamos un array
    //$add=array(); // sub array para agregar a rawdata
    $dataPoints=array(); // {label:"hora",y:temp}
    $data=array();
    $i=0;

    while($row = pg_fetch_array($res)){
            
            if($row["hora"]==0 && $i==1){
              $rawdata["type"]="spline";
              $rawdata["showInLegend"]=true;
              $rawdata["name"]=$name;
              $rawdata["dataPoints"]=$dataPoints;
              array_push($data, $rawdata);
              
              unset($add);
              $add=array();
              unset($dataPoints);
              $dataPoints=array();
              //while(count($add))array_pop($add);
            }
            if($row["hora"]==0 && $i==0){
              $add=array();
              $i=1;
            }

            $name="Estacion ".$row["est_id"];
            $add["label"] = $row["hora"].":00";
            $add["y"] = round($row["dat_velocidad"],2);
            array_push ( $dataPoints , $add );
    }

    $rawdata["type"]="spline";
    $rawdata["showInLegend"]=true;
    $rawdata["name"]=$name;
    $rawdata["dataPoints"]=$dataPoints;
    array_push($data, $rawdata);

    //var_dump($rawdata);
    return json_encode($data); 
  }


//DIRECCION

  //corregir a grafico de multilineas
  public function getDirectionActual(){
    /*$sql="SELECT ld.dat_direccion, ld.est_id
          FROM lemu_data ld, lemu_estacion le, lemu_usuario lu, ID_MAYOR idm
          where ld.est_id=le.est_id and le.usu_id=lu.usu_id and lu.usu_nombre=".this->$user."
                and idm.id_mayor=ld.dat_id
          GROUP BY ld.est_id,ld.dat_direccion
          ORDER BY ld.est_id,ld.dat_direccion";*/

    $sql="SELECT ld.est_id,lh.hor_min as hora,ld.dat_direccion
          FROM lemu_data ld, lemu_estacion le, lemu_usuario lu, lemu_fecha lf, lemu_hora lh
          where ld.est_id=le.est_id and le.usu_id=lu.usu_id and lh.hor_id=ld.hor_id ";

    if($this->user!='admin') $sql=$sql." and lu.usu_nombre='".$this->user."' ";
      
    $sql=$sql." and lf.fec_id=ld.fec_id
                  and lf.fec_date=current_date-2
                  and (lh.hor_id%4)=0
                  and ld.hor_id>=(SELECT EXTRACT(HOUR FROM CURRENT_TIME)*240)
                  and ld.hor_id<((SELECT EXTRACT(HOUR FROM CURRENT_TIME)+1)*240)
          GROUP BY ld.est_id,lh.hor_id,ld.dat_direccion
          ORDER BY ld.est_id,lh.hor_id,ld.dat_direccion";

    $res = $this->db->exeQuery($sql);

    $rawdata = array(); //creamos un array
    //$add=array(); // sub array para agregar a rawdata
    $dataPoints=array(); // {label:"hora",y:temp}
    $data=array();
    $i=0;

    while($row = pg_fetch_array($res)){

            if($row["hora"]==0 && $i==1){
              $rawdata["type"]="spline";
              $rawdata["showInLegend"]=true;
              $rawdata["name"]=$name;
              $rawdata["dataPoints"]=$dataPoints;
              array_push($data, $rawdata);

              unset($add);
              $add=array();
              unset($dataPoints);
              $dataPoints=array();
              //while(count($add))array_pop($add);
            }
            if($row["hora"]==0 && $i==0){
              $add=array();
              $i=1;
            }

            $name="Estacion ".$row["est_id"];
            $add["label"] = $row["hora"].":00";
            $add["y"] = round($row["dat_direccion"],2);
            array_push ( $dataPoints , $add );
    }

    $rawdata["type"]="spline";
    $rawdata["showInLegend"]=true;
    $rawdata["name"]=$name;
    $rawdata["dataPoints"]=$dataPoints;
    array_push($data, $rawdata);

    //var_dump($rawdata);
    return json_encode($data);
  }

  //corregir
  public function getDirection24h($user){
    $sql="SELECT  ld.dat_direccion, (ld.hor_id/240) as hora,ld.est_id
          from  lemu_data ld, lemu_estacion le, lemu_usuario lu, lemu_fecha lf
          where   ld.est_id=le.est_id and le.usu_id=lu.usu_id and lu.usu_nombre=".this->$user." and ld.fec_id=lf.fec_id
          and lf.fec_date='2015-04-27' and ld.hor_id in(
            (0*240),(1*240),(2*240),(3*240),(4*240),(5*240),(6*240),(7*240),(8*240),(9*240),(10*240),(11*240),(12*240),
            (13*240),(14*240),(15*240),(16*240),(17*240),(18*240),(19*240),(20*240),(21*240),(22*240),(23*240),(24*240)
            )
          order by ld.est_id,ld.hor_id";

    $res = $this->db->exeQuery($sql);

    $rawdata = array(); //creamos un array
    //$add=array(); // sub array para agregar a rawdata
    $dataPoints=array(); // {label:"hora",y:temp}
    $data=array();
    $i=0;

    while($row = pg_fetch_array($res)){

            if($row["hora"]==0 && $i==1){
              $rawdata["type"]="spline";
              $rawdata["showInLegend"]=true;
              $rawdata["name"]=$name;
              $rawdata["dataPoints"]=$dataPoints;
              array_push($data, $rawdata);

              unset($add);
              $add=array();
              unset($dataPoints);
              $dataPoints=array();
              //while(count($add))array_pop($add);
            }
            if($row["hora"]==0 && $i==0){
              $add=array();
              $i=1;
            }

            $name="Estacion ".$row["est_id"];
            $add["label"] = $row["hora"].":00";
            $add["y"] = round($row["dat_direccion"],2);
            array_push ( $dataPoints , $add );
    }

    $rawdata["type"]="spline";
    $rawdata["showInLegend"]=true;
    $rawdata["name"]=$name;
    $rawdata["dataPoints"]=$dataPoints;
    array_push($data, $rawdata);

    //var_dump($rawdata);
    return json_encode($data);
  }


}
/*
 
  public function getDirectionProm($user){
    $sql = "SELECT AVG(dat_direccion) AS prom, est_id
            FROM lemu_data ld
            GROUP BY est_id
            ORDER BY est_id";

    $res     = $this->db->exeQuery($sql);
    $rawdata = array(); //creamos un array
    $add     = array(); // sub array para agregar a rawdata

    while($row = pg_fetch_array($res)){

          $add["y"] = round($row["prom"],2);
          $add["label"] = "Estacion ".$row["est_id"];

          array_push ( $rawdata , $add );
            //$rawdata[$i] = $row;
            //$i++;
            //echo $row["usu_nombre"]." ";
        }
    return json_encode($rawdata); //genero json con el array ya asociado
  }



}
?>
