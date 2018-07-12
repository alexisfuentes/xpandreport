<?php
// Asignar la zona horaria para tomar tanto los valores de fecha y hora.
date_default_timezone_set('America/Merida');
// Llamar a la libreria
require_once "../src/xpandreport.php";
// Crear un objeto de la libreria y pasarle como parametro el nombre del reporte
$report = new XpandReport('reports/report1.xrs');
$params = array(
			"strParam" => "AyD",
			"dtParam" => "27/07/2017",
			"txtNuevo" => "Alexis Fuentes"
		);
$valuesT = array();
for ($i=0; $i < 400; $i++) { 
	$row = array();
	$row = array(
			"Nombre de una persona completo" . rand(1,1000),
			rand(1, 40), rand(1, 40),
		);
	$valuesT[] = $row;
}
$report->setParams($params);
$report->setTable('dinamicatb', $valuesT);
$report->Run();