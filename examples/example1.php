<?php 
require_once "../src/xpandreport.php";

$report = new XpandReport('reports/report1.xrs');
$params = array(
			"strParam" => "AyD",
			"dtParam" => "x",
		);
$report->setParams($params);
$report->Run();