<?php

date_default_timezone_set('America/Argentina/Tucuman');

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

// conectar a una base de datos MySQL
function cerrarMySQL($db){
	$db->close();
}

// insertar una prediccion
function insertarForecast($db, $est, $obs, $idCapture){
	$date = sprintf("'%s-%s-%s %s:%s:00'", $obs["FCTTIME"]["year"], $obs["FCTTIME"]["mon_padded"], $obs["FCTTIME"]["mday_padded"], $obs["FCTTIME"]["hour_padded"], $obs["FCTTIME"]["min"]);

	$temp =      ($obs['temp']['metric']      && $obs['temp']['metric']>-999)      ? ("'".$obs['temp']['metric']."'")      : 'NULL';
	$wspd =      ($obs['wspd']['metric']      && $obs['wspd']['metric']>-999)      ? ("'".$obs['wspd']['metric']."'")      : 'NULL';
	$wdir =      ($obs['wdir']['degrees']     && $obs['wdir']['degrees']>-999)     ? ("'".$obs['wdir']['degrees']."'")     : 'NULL';
	$humidity =  ($obs['humidity']            && $obs['humidity']>-999)            ? ("'".$obs['humidity']."'")            : 'NULL';
	$windchill = ($obs['windchill']['metric'] && $obs['windchill']['metric']>-999) ? ("'".$obs['windchill']['metric']."'") : 'NULL';
	$heatindex = ($obs['heatindex']['metric'] && $obs['heatindex']['metric']>-999) ? ("'".$obs['heatindex']['metric']."'") : 'NULL';
	$pop =       ($obs['pop']                 && $obs['pop'] > -999)               ? ("'".$obs['pop']."'")                 : 'NULL';
	$mslp =      ($obs['mslp']['metric']      && $obs['mslp']['metric']>-999)      ? ("'".$obs['mslp']['metric']."'")      : 'NULL';
	
	$fields = "idCapture, idStation, dates, temp, wspd, wdir, humidity, windchill, heatindex, pop, mslp";
	$query = sprintf("INSERT INTO forecasts (%s) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)", 
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
			$r->free_result();
			return $fila['id'];
		}
	}
	return false;
}
//verifica si existe una captura actual en la base de datos
function existeCaptura($db){
	//comparo año, mes, dia y hora
	$query = $db->query("SELECT SUBSTR(MAX(dates),1,13) AS 'fecha' FROM captures LIMIT 1");
	$r = $query->fetch_assoc();
	mysql_free_result($query);
	$query->free_result();
	return (strcmp($r['fecha'], date("Y-m-d H")) == 0);
}

function guardar_pronosticos($db_params, $key_ID, $coord, $est, $intentos = 100){
	$db = conectarMySQL($db_params);

	while ($intentos > 0){
		// pedir datos del dia
		$parsed_json = prediccion10DiasHorario($key_ID, $coord);
		sleep( 6.5 ); // LIMITACION POR USO GRATUITO
		if ($parsed_json){
			if (existeCaptura($db)){return;}
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
	cerrarMySQL($db);
}
