<?php
//script para guardar predicciones de clima bajados desde la weather channel

include 'base.php';

$key_ID = 'a0c04db7ae160e62';
$coord = '-26.84086037,-65.10494232'; //aeropuerto
$est = 1; //estacion
$db_params = array(
	'host' => "localhost",
	'user' => "root",
	'pass' => "123456",
	'name' => "clima_wu",
);

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
cerrarMySQL($db);

