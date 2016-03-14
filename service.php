<?php

/*
option siempre compuesto del objeto a crear y de el metodo a llamar,   ejemplo: temp_prom
indica crear el objeto temp y llamar al metodo prom que calcula temperaturas promedio....
asi se crearan clases temp, hum, map, point, pres, usu, hist, etc....
*/
if(isset($_GET['user']) && isset($_GET['option'])) {
	if(is_string($_GET['user']) && is_string($_GET['option'])){

		$js=json_encode('[{}]');

		$user=$_GET['user'];
		$op= array();
		$op=explode('_',$_GET['option']);
		
		switch ($op[0]) {

			case 'temp':
				include_once('temperatura.php');
				$temp = new temp($user);

				switch ($op[1]) {
					case 'prom':
						$dat=$temp->getTempProm();
						//echo $dat; //retorno json!
						echo $_GET['jsoncallback'] . '(' . $dat . ');';
						break;

					case 'weekmax':
						$dat=$temp->getTempMax();
						echo $dat; //retorno json!
						break;

					case 'actual':
						$dat=$temp->getActual();
						echo $_GET['jsoncallback'] . '(' . $dat . ');';
						break;

					case '24h':
						$dat=$temp->get24h();
						echo $_GET['jsoncallback'] . '(' . $dat . ');';
						break;

					default:
						echo $js;
						break;
				}
				break;

			case 'hum':
				include_once('humedad.php');
				$hum = new hum($user);

				switch ($op[1]) {
					case 'actual':
						$dat=$hum->getActual();
						echo $_GET['jsoncallback'] . '(' . $dat . ');';
						break;

					case '24h':
						$dat=$hum->get24h();
						echo $_GET['jsoncallback'] . '(' . $dat . ');';
						break;

				}
				break;

			case 'map':
				include_once('map.php');
				$map = new map($user);

				switch ($op[1]) {
					case 'point':
						$dat=$map->getPoint();
						echo $_GET['jsoncallback'] . '(' . $dat . ');';
						break;

					case 'polygon':
						$dat=$map->getPolygon();
						echo $_GET['jsoncallback'] . '(' . $dat . ');';
						break;

					case 'PP':
						$dat=$map->getPP();
						echo $_GET['jsoncallback'] . '(' . $dat . ');';
						break;

				}
				break;

			case 'img':
				include_once('image.php');
				$img = new img($user);

				switch ($op[1]) {
					case 'actual':
						$dat=$img->getActual();
						echo $_GET['jsoncallback'] . '(' . $dat . ');';
						break;

					case 'save':
						//save($direccion, $fecha, $hora,$estacion){
						//ej.. img_save_forestin_1_11-09-2015_13:07:35_1
						$dat=$img->save($op[2].'_'.$op[3],$op[4],$op[5],$op[6]);
						echo $_GET['jsoncallback'] . '(' . $dat . ');';
						break;
				}
				break;

			case 'wind':
				include_once('wind.php');
				$wind = new wind($user);
	
				switch ($op[1]) {
				 	case 'actual':
				 		$dat=$wind->getSpeedActual();
				 		echo $_GET['jsoncallback'] . '(' . $dat . ');';
				 		break;

				 	case '24h':
				 		$dat=$wind->get24h();
				 		echo $_GET['jsoncallback'] . '(' . $dat . ');';
				 		break;

				 	case 'direccionActual':
						$dat=$wind->getDirectionActual();
				 		echo $_GET['jsoncallback'] . '(' . $dat . ');';
				 		break;				 		
				 } 
				break;

			case 'save':
				include_once('save.php');
				$save = new save($user);

				//$estacion, $tem, $hum, $hora, $fecha, $presion, $v, $dir
				//url ejemplo
				//?user=migue&option=save_4_50_90_1_20-08-2015_10_81_1
				$dat=$save->saveData($op[1],$op[2],$op[3],$op[4],$op[5],$op[6],$op[7],$op[8]);
				echo $_GET['jsoncallback'] . '(' . $dat . ');';
				break;

			case 'access': 
				// proporciona datos basicos del usuario
				//url ?user=usuario&option=access_password
				// se reemplaza usuario y password! obvio 
				include_once('access.php');
				$access= new access();
				$dat=$access->getUser($user,$op[1]);
				echo $_GET['jsoncallback'] . '(' . $dat . ');';
				break;
					
			default:
				echo $js;
				break;
		}
		//echo json_encode($dat);
	}
}

?>