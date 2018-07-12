<?php
/**
* Clase wrapper para xpandreport y fpdf
*/
// define('FPDF_FONTPATH', '../src/fonts');
require_once "parserxr.php";

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
	// Objetos que se repetiran a lo largo del reporte
	protected $_objsRepeat;
	// Array con las imagenes que se cargaran de forma dinamica
	protected $_listImgs;
	// Variable para tomar el ultimo componente de la parte inferior del documento

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
		$this->_objsRepeat = array();

		$this->AddFont('Tahoma','','tahoma.php');
		// $this->AddFont('Tahoma','B','tahomabd.php');
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
						'pageNumber' => $this->PageNo(),
						'totalPages' => '{nb}'
					);
		if (array_key_exists($param, $globasStr) && $param != "")
			return $globasStr[$param];
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
			if (preg_match_all('/\$P\{([a-zA-Z0-9]*)\}/', $param, $str, PREG_SET_ORDER)){
				foreach ($str as $p) {
					if (array_key_exists($p[1], $paramsExist)){
						if (array_key_exists($p[1], $this->_params))
							$param = str_replace($p[0], $this->_params[$p[1]], $param);
						else
							$param = str_replace($p[0], '', $param);
					}else
						$param = str_replace($p[0], $this->paramGlobal($p[1]), $param);
				}

			}
		}
		return utf8_decode($param);
	}

	protected function resolveStyleText($param){
		if ($param == 'regular')
			return '';

		return ($param == 'Bold') ? 'B' : '';
	}

	protected function resolveAlignment($param){
		if ($param == 'Right')
			return 'R';
		elseif ($param == 'Center')
			return 'C';

		return '';
	}

	protected function resolveBackground($param){
		if ($param == "Transparent") {
			return false;
		}
		return true;
	}

	protected function resolveAlignmentH($param){
		return mb_strtoupper($param)[0];
	}

	protected function resolveDataTable($nameTable, $w, $h, $x, $y){
		// Obtener los datos de la tabla a agregar
		// Verificar si se paso información de la tabla
		if (!array_key_exists($nameTable, $this->_hTables))
			return;

		$rows = $this->_hTables[$nameTable];
		foreach ($rows as $row) {
			$i = 0;
			$y = $y + $h;
			// Checar si ya estan cerca del margen para poder realizar el salto de página
			if ($this->h - 28.35/$this->k > $y){
				$this->SetXY($x, $y);
				foreach ($row as $c){
					$this->Cell($w[$i], $h, $c, 1);
					$i++;
				}
			}else{
				$this->AddPage();
				$y = 28.35 / $this->k;
				$this->SetXY($x, $y);
				foreach ($row as $c) {
					$this->Cell($w[$i], $h, $c, 1);
					$i++;
				}
			}
			$this->Ln();
		}
	}

	/**
	 * Asignar todos los componentes que se repetiran en las hojas.
	 * @param  Object $component Array con los datos del componente que se repetira
	 */
	protected function repeatComponent($component)
	{
		$this->_objsRepeat[] = $component;
	}

	/**
	 * Pinta los objectos que se repiten en las páginas.
	 */
	protected function paintRepeatObjs()
	{
		// Recorrer el arreglo de componentes y pintarlos segun sus necesidades
		foreach ($this->_objsRepeat as $component) {
			switch ($component['name']) {
				case 'textField':

					break;
				case 'pictureBox':
					break;
				case 'line':
					break;
				default:
					# code...
					break;
			}
		}
	}

	private function drawStaticNodes(){
		$statics = $this->_reportStruct->getStaticNodes();
		foreach ($statics as $name => $com) {
			switch ($name) {
				case 'textField':
					foreach ($com as $txt) {
						/*echo "<br />------------------" . $txt['prop']['text']['content'] . "-----------------";
						echo "<br />Family: " . $txt['prop']['font']['attr']['family'];
						echo "<br />Style: " . $this->resolveStyleText($txt['prop']['font']['attr']['style']);
						echo "<br />Size: " . $txt['prop']['font']['attr']['size'];*/
						$this->SetFont(
								$txt['prop']['font']['attr']['family'],
								$this->resolveStyleText($txt['prop']['font']['attr']['style']),
								$txt['prop']['font']['attr']['size']
							);
						$x = $txt['attr']['x'] - ($txt['attr']['x'] * .28);
						$y = $txt['attr']['y'] - ($txt['attr']['y'] * .2754);
						$this->SetXY($x, $y);
						$this->MultiCell(
								($txt['attr']['width'] - $txt['attr']['width'] * .28),
								($txt['attr']['height'] - $txt['attr']['height'] * .2754),
								utf8_decode($txt['prop']['text']['content']),
								($txt['prop']['border']['attr']['width'] > 0),
								$this->resolveAlignmentH(
									$txt['prop']['text']['attr']['horAlignment']
								),
								$this->resolveBackground(
									$txt['prop']['backgroundColor']['attr']['color']
								)
						);
						$this->SetFont('Arial', '', 10);
					}
					break;
				case 'pictureBox':
					foreach ($com as $imgs) {
						$x = $imgs['attr']['x'] - ($imgs['attr']['x'] * .28);
						$y = $imgs['attr']['y'] - ($imgs['attr']['y'] * .2754);
						$w = $imgs['attr']['width'] - ($imgs['attr']['width'] * .28);
						$h = $imgs['attr']['height'] - ($imgs['attr']['height'] * .2754);
						$dynamic = ($imgs['attr']['Dynamic'] == "True");
						if ($dynamic) {
							// Cargar imagen de la lista de imagenes pasadas.
							foreach ($images as $i)
								$this->Image($i, $x, $y, $w, $h);
						}else{
							$carpeta = 'temp';
							if (!file_exists($carpeta))
								mkdir($carpeta, 0777, true);
							file_put_contents('temp/'. $imgs['attr']['Name'] .'.png',
								$imgs['prop']['image']['content']);
							$dirImg = "temp/". $imgs['attr']['Name'] .".png";
							$this->Image($dirImg, $x, $y, $w, $h);
							unlink($dirImg);
						}
					}
					break;
				case 'line':
					foreach ($com as $line) {
						$x1 = $line['attr']['x'] - ($line['attr']['x'] * .28);
						$y1 = $line['attr']['y'] - ($line['attr']['y'] * .2754);

						$x2 = ($line['attr']['x'] + $line['attr']['width']) - (($line['attr']['x'] + $line['attr']['width']) * .2754);
						$y2 = $line['attr']['y'] - ($line['attr']['y'] * .2754);
						$this->SetXY($x1, $y1);
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
						$this->SetFont(
								$txt['prop']['font']['attr']['family'],
								$this->resolveStyleText($txt['prop']['font']['attr']['style']),
								$txt['prop']['font']['attr']['size']
							);
						$x = $txt['attr']['x'] - ($txt['attr']['x'] * .28);
						$y = $txt['attr']['y'] - ($txt['attr']['y'] * .2754);
						$this->SetXY($x, $y);
						$this->Cell(
								($txt['attr']['width'] - $txt['attr']['width'] * .28),
								($txt['attr']['height'] - $txt['attr']['height'] * .2754),
								$this->resolveParams(
									$txt['prop']['text']['content']
								),
								($txt['prop']['border']['attr']['width'] > 0),
								0,
								$this->resolveAlignmentH(
									$txt['prop']['text']['attr']['horAlignment']
								),
								$this->resolveBackground(
									$txt['prop']['backgroundColor']['attr']['color']
									)
						);
						$this->SetFont('Arial', '', 10);
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
			$this->MsgError("No se encontraron datos para la creación de la tabla");

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
		$prop = $this->_reportStruct->getStaticNodes();
		print_r($prop);
	}
}
