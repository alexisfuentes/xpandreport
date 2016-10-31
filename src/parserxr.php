<?php 

/**
* Clase para reconocer y obtener los elementos que se integraron en el 
* reporte y convertirlos en entidades de PHP para reconocerlos y 
* crear el reporte en PDF
*/
class ParserXR
{
	// Contiene el objecto XML del reporte
	private $_xml;
	// Contendra los componentes estaticos del reporte
	private $_staticNodes;
	// Contendra los componentes dinamicos del reporte
	private $_dynamicNodes;

	public function __construct($file)
	{
		if (!file_exists($file))
			throw new Exception("ParserXR: File not found!", 1);
		
		$this->_xml = simplexml_load_file($file);

		// Obtener nodos estaticos
		$this->xStaticNodes();
		// Obtener nodos dinamicos
		$this->xDynamicNodes();
	}

	/**
	 * Obtiene las propiedades del reporte como son:
	 * - Tamaño del papel.
	 * - Orientación del papel.
	 * - Autor.
	 * - Fecha de creación
	 * @return Object
	 */
	public function getReportProperties(){
		$props = array();
		foreach ($this->_xml->attributes() as $name => $value)
			$props[$name] = strtolower((string)$value);

		return (object)$props;
	}

	/**
	 * Obtiene los margenes usados para el reporte
	 * @return Object
	 */
	public function getMarginsReport()
	{
		$margins = array();
		foreach ($this->_xml->margins->attributes() as $name => $value)
			$margins[$name] = (int)$value / 100;

		return (object)$margins;
	}

	/**
	 * Obtiene los parametros asociados al reporte para su posterior reemplazo
	 * @return Array Parametros con nombre y tipo
	 */
	public function getParams(){
		$params = array();
		foreach ($this->_xml->parameters->parameter as $param) {
			$dataParam = array();
			foreach ($param->attributes() as $name => $value)
				$dataParam[$name] = (string)$value;
			$params[] = $dataParam;
		}

		return $params;
	}

	/**
	 * Obtiene los componentes que son estaticos dentro del reporte
	 */
	private function xStaticNodes()
	{
		foreach ($this->_xml->content->staticContent->children() as $nodo) {
			$attr = array();
			$prop = array();
			switch ($nodo->getName()) {
				case 'textField':
					// Obtener los atributos
					foreach ($nodo->attributes() as $name => $value)
						$attr[$name] = (string)$value;

					foreach ($nodo->children() as $propiedades) {
						$at = array();
						if ($propiedades->getName() == 'text'){
							foreach ($propiedades->attributes() as $name => $value)
								$at[$name] = (string)$value;

							$prop['text'] = array(
												'content' => (string)$nodo->text,
												'attr' => $at
											);
						}else{
							foreach ($propiedades->attributes() as $name => $value)
								$at[$name] = (string)$value;
							$prop[$propiedades->getName()] = array(
																"attr" => $at
															);
						}
					}

					$this->_staticNodes[$nodo->getName()][] = array(
																'attr' => $attr,
																'prop' => $prop
															);
					break;
				case 'line':
					// Obtener los atributos
					foreach ($nodo->attributes() as $name => $value)
						$attr[$name] = (string)$value;

					$this->_staticNodes[$nodo->getName()][] = array(
																'attr' => $attr
															);
					break;
				case 'pictureBox':
					// Obtener los atributos
					foreach ($nodo->attributes() as $name => $value)
						$attr[$name] = (string)$value;

					foreach ($nodo->children() as $propiedades) {
						$at = array();
						if ($propiedades->getName() == 'image') {
							$prop['image'] = array(
												'content' => base64_decode((string)$nodo->image)
											);
						}else{
							foreach ($propiedades->attributes() as $name => $value)
								$at[$name] = (string)$value;

							$prop[$propiedades->getName()] = array(
																'attr' => $at
															);
						}
					}

					$this->_staticNodes[$nodo->getName()][] = array(
																	'attr' => $attr,
																	'prop' => $prop
																);
					break;
				case 'table':
					// Obtener los atributos
					break;
			}
		}
	}

	/**
	 * Obtiene los componentes dinamicos dentro del reporte
	 */
	private function xDynamicNodes()
	{
		foreach ($this->_xml->content->dynamicContent->children() as $nodo) {
			$attr = array();
			$prop = array();
			switch ($nodo->getName()) {
				case 'textField':
					// Obtener los atributos
					foreach ($nodo->attributes() as $name => $value)
						$attr[$name] = (string)$value;

					foreach ($nodo->children() as $propiedades) {
						$at = array();
						if ($propiedades->getName() == 'text'){
							foreach ($propiedades->attributes() as $name => $value)
								$at[$name] = (string)$value;

							$prop['text'] = array(
												'content' => (string)$nodo->text,
												'attr' => $at
											);
						}else{
							foreach ($propiedades->attributes() as $name => $value)
								$at[$name] = (string)$value;
							$prop[$propiedades->getName()] = array(
																"attr" => $at
															);
						}
					}

					$this->_dynamicNodes[$nodo->getName()][] = array(
																'attr' => $attr,
																'prop' => $prop
															);
					break;
				case 'pictureBox':
					// Obtener los atributos
					foreach ($nodo->attributes() as $name => $value)
						$attr[$name] = (string)$value;

					foreach ($nodo->children() as $propiedades) {
						$at = array();
						if ($propiedades->getName() == 'image') {
							$prop['image'] = array(
												'content' => base64_decode((string)$nodo->image)
											);
						}else{
							foreach ($propiedades->attributes() as $name => $value)
								$at[$name] = (string)$value;

							$prop[$propiedades->getName()] = array(
																'attr' => $at
															);
						}
					}

					$this->_dynamicNodes[$nodo->getName()][] = array(
																	'attr' => $attr,
																	'prop' => $prop
																);
					break;
				case 'table':
					// Obtener los atributos
					break;
			}
		}
	}

	public function getStaticNodes(){
		return $this->_staticNodes;
	}

	public function getDynamicNodes()
	{
		return $this->_dynamicNodes;
	}

}