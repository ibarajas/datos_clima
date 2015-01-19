<?php
//script para guardar predicciones de clima bajados desde la weather channel

include 'base.php';

$key_ID = 'a0c04db7ae160e62';
$coord = '-26.84086037,-65.10494232'; //aeropuerto
$est = 1; //estacion
$db_params = array(
	'host' => "mysql.1freehosting.com",
	'user' => "u105124561_clima",
	'pass' => "u105124561",
	'name' => "u105124561_clima",
);

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
cerrarMySQL($db);
echo 'Finalizado.';

