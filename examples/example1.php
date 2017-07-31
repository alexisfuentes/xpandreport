<?php
date_default_timezone_set('America/Merida');
require_once "../src/xpandreport.php";

$report = new XpandReport('reports/rpGeneralMes.xrs');
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
			rand(1, 40), rand(1, 40),
			rand(1, 40), rand(1, 40),
			rand(1, 40), rand(1, 40),
			rand(1, 40), rand(1, 40),
			rand(1, 40), rand(1, 40),
			rand(60, 90)
		);
	$valuesT[] = $row;
}
$report->setParams($params);
$report->setTable('resumen_mensual', $valuesT);
$report->Run();