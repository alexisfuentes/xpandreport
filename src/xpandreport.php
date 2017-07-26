<?php 
/**
* Clase wrapper para xpandreport y fpdf
*/
// define('FPDF_FONTPATH', '../src/fonts');
require_once "parserxr.php";
require_once "../vendor/autoload.php";

class XpandReport extends FPDF
{
	// Estructura del reporte ( Componentes estaticos y dinamicos )
	protected $_reportStruct;
	// Parametros ha reemplazar dentro del reporte
	protected $_params;
	// Cadenas de parametros obtenidas del reporte.
	protected $_strParams;
	// Contenedor de las tablas que se usaran en el reporte
	protected $_hTables;

	public function __construct($file)
	{
		try {
			$this->_reportStruct = new ParserXR($file);
		} catch (Exception $e) {
			echo $e;
		}

		// Obtener los parametros a reemplazar
		$this->_strParams = $this->_reportStruct->getParams();

		// Obtener las propiedades del reporte
		$props = $this->_reportStruct->getReportProperties();
		parent::__construct($props->layout[0], 'pt', $props->papersize);

		// Obtener los margenes del reporte y asignarlos al PDF
		$margins = $this->_reportStruct->getMarginsReport();
		$this->SetMargins($margins->left, $margins->top, $margins->right);

		// Agregar el autor del reporte
		$this->SetAuthor($props->author);
		$this->_hTables = array();
	}

	private function MsgError($text)
	{
		throw new Exception("XpandReport: " . $text, 1);
	}

	private function paramGlobal($param)
	{
		$globasStr = array(
						'systemTime' => date("h:i:s A"),
						'systemDate' => date("d/m/Y"),
						'pageNumber' => $this->PageNo()
					);
		if (array_key_exists($param, $globasStr)) {
			return $globasStr[$param];
		}
		return "";
	}

	private function findArrayParams()
	{
		$listParams = array();
		for ($i=0; $i < count($this->_strParams); $i++) { 
			$listParams[$this->_strParams[$i]['name']] = array(
															"type" => $this->_strParams[$i]['type']
														);
		}
		return $listParams;
	}

	/**
	 * Buscamos todos los parametros dentro de la cadena y los reemplazamos
	 * con los valores pasados para el reporte
	 * @param  string $param Cadena con el parametro a reemplazar.
	 * @return string        Cadena con los valores reemplazados.
	 */
	protected function resolveParams($param){
		$paramsExist = $this->findArrayParams();
		// Verificar si tiene el formato para reemplazar la cadena
		if(stripos($param, '$P') !== FALSE) {
			if (preg_match('/\$P\{([a-zA-Z0-9]*)\}/', $param, $str)){
				if (array_key_exists($str[1], $paramsExist)){
					if (array_key_exists($str[1], $this->_params))
						$param = str_replace($str[0], $this->_params[$str[1]], $param);
					else
						$param = str_replace($str[0], '', $param);
				}else{
					$param = $this->paramGlobal($str[1]);
				}
			}
		}
		return $param;
	}

	protected function resolveStyleText($param){
		return "";
		if ($param == 'regular')
			return '';

		return ($param == 'bold') ? 'B' : 'I';

	}

	protected function resolveDataTable($nameTable, $w, $h, $x, $y){
		// Obtener los datos de la tabla a agregar
		$rows = $this->_hTables[$nameTable];
		foreach ($rows as $row) {
			$i = 0;
			$y = $y + $h;
			$this->SetXY($x, $y);
			foreach ($row as $c){
				$this->Cell($w[$i], $h, $c, 1);
				$i++;
			}
			$this->Ln();
		}
	}

	private function drawStaticNodes(){
		$statics = $this->_reportStruct->getStaticNodes();
		foreach ($statics as $name => $com) {
			switch ($name) {
				case 'textField':
					foreach ($com as $txt) {
						/*$this->SetFont(
								$txt['prop']['font']['attr']['family'],
								$this->resolveStyleText($txt['prop']['font']['attr']['style']),
								$txt['prop']['font']['attr']['size']
							);*/
						$x = $txt['attr']['x'] - ($txt['attr']['x'] * .28);
						$y = $txt['attr']['y'] - ($txt['attr']['y'] * .2754);
						$this->SetXY($x, $y);
						$this->Cell($txt['attr']['width'], 
									$txt['attr']['height'], 
									$txt['prop']['text']['content']);
						$this->SetFont('Arial', '', 10);
					}
					break;
				case 'pictureBox':

					break;
				case 'line':
					foreach ($com as $line) {
						$x1 = $line['attr']['x'] - ($line['attr']['x'] * .28);
						$y1 = $line['attr']['y'] - ($line['attr']['y'] * .2754);

						$x2 = ($line['attr']['x'] + $line['attr']['width']) - (($line['attr']['x'] + $line['attr']['width']) * .2754);
						$y2 = $line['attr']['y'] - ($line['attr']['y'] * .2754);
						$this->SetXY($x, $y);
						$this->Line($x1, $y1, $x2, $y2);
					}
					break;
				default:
					# code...
					break;
			}
		}
	}

	private function drawDynamicNodes(){
		$statics = $this->_reportStruct->getDynamicNodes();
		foreach ($statics as $name => $com) {
			switch ($name) {
				case 'textField':
					foreach ($com as $txt) {
						$x = $txt['attr']['x'] - ($txt['attr']['x'] * .28);
						$y = $txt['attr']['y'] - ($txt['attr']['y'] * .2754);
						// $y -= 56.7; // 56.7
						$this->SetXY($x, $y);
						$this->Cell($txt['attr']['width'], 
									$txt['attr']['height'], 
									$this->resolveParams(
										$txt['prop']['text']['content']
									));
					}
					break;
				case 'table':
					foreach ($com as $table) {
						$x = $table['attr']['x'] - ($table['attr']['x'] * .28);
						$y = $table['attr']['y'] - ($table['attr']['y'] * .2754);
						$this->SetXY($x, $y);
						// Crear las celdas que funcionaran como titulo de la columna
						$wArray = array();
						$h = 0;
						foreach ($table['prop']['columns']['columns'] as $cell) {
							$w = $cell['width'] - ($cell['width'] * .28);
							$h = $table['attr']['cellHeight'] - ($table['attr']['cellHeight'] * .28);
							$this->Cell($w, $h, $cell['label'], 1);
							$wArray[] = $w;
						}
						$this->Ln();
						// Agregar los datos segun el nombre de la tabla
						$this->resolveDataTable($table['attr']['dataSource'], $wArray, $h, $x, $y);
					}
				default:
					# code...
					break;
			}
		}
	}

	/**
	 * Pasar los parametros a reemplazar en el reporte
	 * @param Array $paramsArray Arreglo con los valores finales.
	 */
	public function setParams($paramsArray)
	{
		$this->_params = $paramsArray;
	}

	/**
	 * Pasar los valores de las tablas para el reporte
	 * @param String $nameTable Nombre de la tabla donde se reemplazaran los datos
	 * @param Array $values    Datos de la tabla
	 */
	public function setTable($nameTable, $values)
	{
		if ($nameTable == "")
			throw new Exception("No se encontraron datos para la creación de la tabla", 1);

		$this->_hTables[$nameTable] = $values;
	}

	/**
	 * Crear el reporte con todos los componentes estaticos y dinamicos.
	 */
	public function Run(){
		$this->AddPage();
		$this->SetFont('Arial', '', 10);
		$this->SetAutoPageBreak(false);
		$this->AliasNbPages();
		// Agregar nodos estaticos
		$this->drawStaticNodes();
		// Agregar nodos dinamicos
		$this->drawDynamicNodes();
		$this->Output();
	}

	public function Demo()
	{
		echo "<pre>";
		$prop = $this->_reportStruct->getDynamicNodes();
		print_r($prop);
	}
}