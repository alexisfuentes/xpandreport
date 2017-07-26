<?php
date_default_timezone_set('America/Merida');
require_once "../src/xpandreport.php";

$report = new XpandReport('reports/report1.xrs');
$params = array(
			"strParam" => "AyD",
			"dtParam" => "x",
			"txtNuevo" => "Alexis Fuentes"
		);
$valuesT = array(
			0 => array(
				'Alexis Fuentes',
				'27',
				'H'
			),
			1 => array(
				'Antonio Fuentes',
				'57',
				'H'
			)
		);

$report->setParams($params);
$report->setTable('dinamicatb', $valuesT);
$report->Run();