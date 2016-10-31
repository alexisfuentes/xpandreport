<?php 
require_once "../src/xpandreport.php";

$report = new XpandReport('reports/report1.xrs');
$report->Run();