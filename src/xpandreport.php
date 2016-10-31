<?php 
/**
* Clase wrapper para xpandreport y fpdf
*/

require_once "parserxr.php";

class XpandReport
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

		// Obtener los parametros a reemplazar;
		$this->_strParams = $this->_reportStruct->getParams();
	}

	/**
	 * Buscamos todos los parametros dentro de la cadena y los reemplazamos
	 * con los valores pasados para el reporte
	 * @param  string $param Cadena con el parametro a reemplazar.
	 * @return string        Cadena con los valores reemplazados.
	 */
	private function resolveParams($param){
		while(stristr($param, '$P', true)) {
			if (preg_match('/\$P\{([a-zA-Z0-9]*)\}/', $param, $str)){
				if (array_key_exists($str[1], $this->_params))
					$param = str_replace($str[0], $this->_params[$str[1]], $param);
				else
					$param = str_replace($str[0], '', $param);
			}
		}

		return $param;
	}

	/**
	 * Pasar los parametros a reemplazar en el reporte
	 * @param Array $paramsArray Arreglo con los valores finales.
	 */
	public function setParams($paramsArray)
	{
		$this->_params = $paramsArray;
	}

	public function Demo()
	{
		echo "<pre>";
		$prop = $this->_reportStruct->getDynamicNodes();
		print_r($prop);
	}
}