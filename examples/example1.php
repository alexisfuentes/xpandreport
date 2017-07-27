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
			0 => array('Alexis Fuentes',27,'H'),
			1 => array('Alexis Fuentes',27,'H'),
			2 => array('Alexis Fuentes',27,'H'),
			3 => array('Alexis Fuentes',27,'H'),
			4 => array('Alexis Fuentes',27,'H'),
			5 => array('Alexis Fuentes',27,'H'),
			6 => array('Alexis Fuentes',27,'H'),
			7 => array('Alexis Fuentes',27,'H'),
			8 => array('Alexis Fuentes',27,'H'),
			9 => array('Alexis Fuentes',27,'H'),
			10 => array('Alexis Fuentes',27,'H'),
			11 => array('Alexis Fuentes',27,'H'),
			12 => array('Alexis Fuentes',27,'H'),
			13 => array('Alexis Fuentes',27,'H'),
			14 => array('Alexis Fuentes',27,'H'),
			15 => array('Alexis Fuentes',27,'H'),
			16 => array('Alexis Fuentes',27,'H'),
			17 => array('Alexis Fuentes',27,'H'),
			18 => array('Alexis Fuentes',27,'H'),
			19 => array('Alexis Fuentes',27,'H'),
			20 => array('Alexis Fuentes',27,'H'),
			21 => array('Alexis Fuentes',27,'H'),
			22 => array('Alexis Fuentes',27,'H'),
			23 => array('Alexis Fuentes',27,'H'),
			24 => array('Alexis Fuentes',27,'H'),
			25 => array('Alexis Fuentes',27,'H'),
			26 => array('Alexis Fuentes',27,'H'),
			27 => array('Alexis Fuentes',27,'H'),
			28 => array('Alexis Fuentes',27,'H'),
			29 => array('Alexis Fuentes',27,'H'),
			30 => array('Alexis Fuentes',27,'H'),
		);

$report->setParams($params);
$report->setTable('dinamicatb', $valuesT);
$report->Run();