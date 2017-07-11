<?php 
/**
* Clase wrapper para xpandreport y fpdf
*/

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
	}

	private function MsgError($text)
	{
		throw new Exception("XpandReport: " . $text, 1);
	}

	private function paramGlobal($param)
	{
		$globasStr = array(
						'systemTime' => date("h:i:s A"),
						'systemDate' => date("d/m/Y")
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

	private function drawStaticNodes(){
		$statics = $this->_reportStruct->getStaticNodes();
		foreach ($statics as $name => $com) {
			switch ($name) {
				case 'textField':
					foreach ($com as $txt) {
						$x = $txt['attr']['x'] - ($txt['attr']['x'] * .28);
						$y = $txt['attr']['y'] - ($txt['attr']['y'] * .2754);
						$this->SetXY($x, $y);
						$this->Cell($txt['attr']['width'], 
									$txt['attr']['height'], 
									$txt['prop']['text']['content']);
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
						$this->SetXY($x, $y);
						$this->Cell($txt['attr']['width'], 
									$txt['attr']['height'], 
									$this->resolveParams(
										$txt['prop']['text']['content']
									));
					}
					break;
				
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
	 * Crear el reporte con todos los componentes estaticos y dinamicos.
	 */
	public function Run(){
		$this->AddPage();
		$this->SetFont('Arial');
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