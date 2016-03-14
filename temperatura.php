<?php
//include_once('config.php');
include_once('db.php');

class temp{
  private $db;
  private $user;
  
  public function __construct($user){
    $this->db = new db();
    $this->user=$user;
  }

  public function getTempProm(){
      $sql = "SELECT AVG(dat_temp) AS prom, est_id 
              FROM lemu_data ld
              GROUP BY est_id 
              ORDER BY est_id";

      $res = $this->db->exeQuery($sql);

      $rawdata = array(); //creamos un array
      $add=array(); // sub array para agregar a rawdata

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

  public function getWeek(){
    $sql="SELECT  max(ld.dat_temp)AS MAX,min(ld.dat_temp)AS MIN,ld.est_id,lf.fec_date AS fecha
          from  lemu_data ld, lemu_estacion le, lemu_usuario lu, lemu_fecha lf
                where  lu.usu_nombre='migue' and le.usu_id=lu.usu_id  
            and ld.est_id=le.est_id  and ld.fec_id=lf.fec_id 
                  and (lf.fec_date=(select current_date-6) or lf.fec_date=(select current_date-5)
            or lf.fec_date=(select current_date-4) or lf.fec_date=(select current_date-3) or lf.fec_date=(select current_date-2) 
            or lf.fec_date=(select current_date-1) or lf.fec_date=(select current_date) )
                  and ld.hor_id in(
                    (0*240),(1*240),(2*240),(3*240),(4*240),(5*240),(6*240),(7*240),(8*240),(9*240),(10*240),(11*240),(12*240),
                    (13*240),(14*240),(15*240),(16*240),(17*240),(18*240),(19*240),(20*240),(21*240),(22*240),(23*240),(24*240)
                    )
        group by ld.est_id,ld.est_id,lf.fec_date
        order by lf.fec_date,ld.est_id";
    $res = $this->db->exeQuery($sql);

    return json_encode($js); 
  }

  public function getActual(){
    $sql="SELECT ld.dat_temp, ld.est_id 
          FROM lemu_data ld, lemu_estacion le, lemu_usuario lu, ID_MAYOR idm
          where ld.est_id=le.est_id and le.usu_id=lu.usu_id ";
    if($this->user!='admin') $sql=$sql." and lu.usu_nombre='".$this->user."' ";
      
    $sql=$sql." and idm.id_mayor=ld.dat_id
          GROUP BY ld.est_id,ld.dat_temp
          ORDER BY ld.est_id,ld.dat_temp";
    
    $res = $this->db->exeQuery($sql);

    $rawdata = array(); //creamos un array
    $add=array(); // sub array para agregar a rawdata

    while($row = pg_fetch_array($res)){
        
            $add["y"] = round($row["dat_temp"],2);
            $add["label"] = "Estacion ".$row["est_id"];
            
            array_push ( $rawdata , $add );
          }

    return json_encode($rawdata); 
  }

  public function get24h(){
    $sql="SELECT  ld.dat_temp, (ld.hor_id/240) as hora,ld.est_id
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
            $add["y"] = round($row["dat_temp"],2);
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