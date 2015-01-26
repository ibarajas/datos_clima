<?php
//script para guardar predicciones de clima bajados desde la weather channel

include 'base.php';

$key_ID = 'a0c04db7ae160e62';
$coord = '-26.84086037,-65.10494232'; //aeropuerto
$est = 1; //estacion
$db_params = array(
	'host' => "mysql3.000webhost.com",
	'user' => "a4332780_clima",
	'pass' => "a4332780",
	'name' => "a4332780_clima",
);
guardar_pronosticos($db_params, $key_ID, $coord, $est);
