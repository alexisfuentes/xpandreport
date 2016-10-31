<?php 
/**
* Clase wrapper para xpandreport y fpdf
*/

require_once "parserxr.php";

class XpandReport
{
	protected $_components;

	public function __construct($file)
	{
		try {
			$this->_components = new ParserXR($file);
		} catch (Exception $e) {
			echo $e;
			exit();
		}
	}

	public function Demo()
	{
		echo "<pre>";
		print_r($this->_components->getReportProperties());
	}
}