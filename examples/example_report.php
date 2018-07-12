<?php
// Asignar la zona horaria para tomar tanto los valores de fecha y hora.
date_default_timezone_set('America/Merida');
// Llamar a la libreria
require_once "../vendor/autoload.php";
require_once "../src/xpandreport.php";
// Crear un objeto de la libreria y pasarle como parametro el nombre del reporte
$report = new XpandReport('reports/reporte_bien_entrega.xrs');
$params = array(
			"pvobo" => "ING. ALONSO CASTILLO GAMBOA",
			"pentrega" => "ING. BEDER DEL CARMEN RAMON CHAN",
			"precibe" => "ING. ALEXIS ANTONIO FUENTES CHE",
			"ptitulo" => "ENTREGA DE BIEN",
			"fecha" => "11/07/2018"
		);

$report->setParams($params);
$report->Run();
// $report->Demo();
