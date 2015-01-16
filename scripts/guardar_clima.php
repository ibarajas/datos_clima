<?php
//script para guardar predicciones de clima bajados desde la weather channel

$key_ID = 'a0c04db7ae160e62';
$coord_aeropuerto = '-26.84086037,-65.10494232';
$db_params_local = array(
	'host' => "localhost",
	'user' => "root",
	'pass' => "123456",
	'name' => "clima_wu",
);
$db_params_5jelly = array(
	'host' => "mysql.5jelly.com",
	'user' => "u690801609_clima",
	'pass' => "u690801609_clima",
	'name' => "u690801609_clima",
);

// pedir predicciones a 10 dias a nivel horario
function prediccion10DiasHorario($key_ID, $coord){
	$cWU = curl_init('http://api.wunderground.com/api/'.$key_ID.'/hourly10day/q/'.$coord.'.json');
	if (!$cWU){return false;}
	curl_setopt($cWU, CURLOPT_RETURNTRANSFER, TRUE);
	$json_string = curl_exec($cWU);
	curl_close($cWU);
	if (!$json_string){return false;}
	$parsed_json = json_decode($json_string, true);
	return $parsed_json;
}

// conectar a una base de datos MySQL
function conectarMySQL($params){
	$mysqli = new mysqli($params['host'], $params['user'], $params['pass'], $params['name']);
	return $mysqli;
}

// insertar una prediccion
function insertarForecast($db, $est, $obs, $idCapture){
	$date =      "'".date("Y-m-d H:i:s")."'";

	$temp =      ($obs['temp']['metric']      && $obs['temp']['metric']>-999)      ? ("'".$obs['temp']['metric']."'")      : 'NULL';
	$wspd =      ($obs['wspd']['metric']      && $obs['wspd']['metric']>-999)      ? ("'".$obs['wspd']['metric']."'")      : 'NULL';
	$wdir =      ($obs['wdir']['degrees']     && $obs['wdir']['degrees']>-999)     ? ("'".$obs['wdir']['degrees']."'")     : 'NULL';
	$humidity =  ($obs['humidity']            && $obs['humidity']>-999)            ? ("'".$obs['humidity']."'")            : 'NULL';
	$windchill = ($obs['windchill']['metric'] && $obs['windchill']['metric']>-999) ? ("'".$obs['windchill']['metric']."'") : 'NULL';
	$heatindex = ($obs['heatindex']['metric'] && $obs['heatindex']['metric']>-999) ? ("'".$obs['heatindex']['metric']."'") : 'NULL';
	$pop =       ($obs['pop']                 && $obs['pop'] > -999)               ? ("'".$obs['pop']."'")                 : 'NULL';
	$mslp =      ($obs['mslp']['metric']      && $obs['mslp']['metric']>-999)      ? ("'".$obs['mslp']['metric']."'")      : 'NULL';
	
	$fields = "idCapture, idStation, dates, temp, wspd, wdir, humidity, windchill, heatindex, pop, mslp";
	$query = sprintf("INSERT INTO clima_wu.forecasts (%s) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)", 
		$fields, $idCapture, $est, $date, $temp, $wspd, $wdir, $humidity, $windchill, $heatindex, $pop, $mslp);

	return $db->query($query);
}

// insertar una captura para predicciones
function insertarCaptura($db, $est){
	$date = "'".date("Y-m-d H:i:s")."'";
	$fields = "idStation, dates";
	$query = sprintf("INSERT INTO captures (%s) VALUES (%s,%s)", 
		$fields, $est, $date);

	if ($db->query($query)){
		$r = $db->query("SELECT MAX(idCapture) AS 'id' FROM captures");
		if ($r->num_rows > 0){
			$fila = $r->fetch_assoc();
			return $fila['id'];
		}
	}
	return false;
}

function test($db_params, $key_ID, $coord, $est=0){
	echo "Iniciando pruebas...";
	echo '<br/>';
		$db = conectarMySQL($db_params);
		echo ($db->connect_errno) ? "Fallo al contenctar a MySQL: (" . $db->connect_errno . ") " . $db->connect_error : "Conexion establecida con exito.";
	echo '<br/>';
		$parsed_json = prediccion10DiasHorario($key_ID, $coord);
		echo ($parsed_json) ? 'Pedido realizado con exito.' : 'No se pudo hacer la peticion JSON.';
	echo '<br/>';
		$idCapture = insertarCaptura($db, $est);
		echo 'Resultado de guardar captura: '.(($idCapture)?$idCapture:'falso');
	echo '<br/>';
		echo 'Resultados de guardar pronosticos: <br/>';
		foreach($parsed_json['hourly_forecast'] as $obs){
			// insertarla en la base de datos
			$res = insertarForecast($db, $est, $obs, $idCapture);
			echo "  ".(($res)?"Correcto ":"Error ")."<br/>";
			break; //para prueba es suficiente una vez
		}
	echo '<br/>';
	mysql_close($db);
	sleep( 6.5 );
	echo 'Finalizado.';
}

//bajar datos de internet y guardarlos en la base de datos
function procesarPredicciones($db_params, $key_ID, $coord, $est=1){
	$db = conectarMySQL($db_params);
	$intentos = 100;
	while ($intentos > 0){
		// pedir datos del dia
		$parsed_json = prediccion10DiasHorario($key_ID, $coord);
		sleep( 6.5 ); // LIMITACION POR USO GRATUITO
		if ($parsed_json){
			$idCapture = insertarCaptura($db, $est);
			if ($idCapture){
				$intentos = -1; //para terminar bucle
				foreach($parsed_json['hourly_forecast'] as $obs){
					$res = insertarForecast($db, $est, $obs, $idCapture);
				}
				break;
			}
			else{ // quito un intento y espero para el proximo
				$intentos--;
			}
		}
		else{ // quito un intento y espero para el proximo
			$intentos--;
		}
		echo intentos."<br/>";
	}
	mysql_close($db);
	return ($intentos<0);	
}

procesarPredicciones($db_params_local, $key_ID, $coord_aeropuerto);

