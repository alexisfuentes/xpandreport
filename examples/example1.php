<?php
date_default_timezone_set('America/Merida');
require_once "../src/xpandreport.php";

$report = new XpandReport('reports/report1.xrs');
$params = array(
			"strParam" => "AyD",
			"dtParam" => "27/07/2017",
			"txtNuevo" => "Alexis Fuentes"
		);
$valuesT = array(
			0 => array('Nombre Completo de Una Persona',48,'H'),
			1 => array('Nombre Completo de Una Persona',48,'H'),
			2 => array('Nombre Completo de Una Persona',48,'H'),
			3 => array('Nombre Completo de Una Persona',48,'H'),
			4 => array('Nombre Completo de Una Persona',48,'H'),
			5 => array('Nombre Completo de Una Persona',48,'H'),
			6 => array('Nombre Completo de Una Persona',48,'H'),
			7 => array('Nombre Completo de Una Persona',48,'H'),
			8 => array('Nombre Completo de Una Persona',48,'H'),
			9 => array('Nombre Completo de Una Persona',48,'H'),
			10 => array('Nombre Completo de Una Persona',48,'H'),
			11 => array('Nombre Completo de Una Persona',48,'H'),
			12 => array('Nombre Completo de Una Persona',48,'H'),
			13 => array('Nombre Completo de Una Persona',48,'H'),
			14 => array('Nombre Completo de Una Persona',48,'H'),
			15 => array('Nombre Completo de Una Persona',48,'H'),
			16 => array('Nombre Completo de Una Persona',48,'H'),
			17 => array('Nombre Completo de Una Persona',48,'H'),
			18 => array('Nombre Completo de Una Persona',48,'H'),
			19 => array('Nombre Completo de Una Persona',48,'H'),
			20 => array('Nombre Completo de Una Persona',48,'H'),
			21 => array('Nombre Completo de Una Persona',48,'H'),
			22 => array('Nombre Completo de Una Persona',48,'H'),
			23 => array('Nombre Completo de Una Persona',48,'H'),
			24 => array('Nombre Completo de Una Persona',48,'H'),
			25 => array('Nombre Completo de Una Persona',48,'H'),
			26 => array('Nombre Completo de Una Persona',48,'H'),
			27 => array('Nombre Completo de Una Persona',48,'H'),
			28 => array('Nombre Completo de Una Persona',48,'H'),
			29 => array('Nombre Completo de Una Persona',48,'H'),
			30 => array('Nombre Completo de Una Persona',48,'H'),
		);

$report->setParams($params);
$report->setTable('dinamicatb', $valuesT);
$report->Run();