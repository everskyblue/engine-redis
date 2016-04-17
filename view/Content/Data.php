<?php

namespace View\Content;

use ArrayObject;


/**
 * @license Apache
 * @author Nike Madrid
 * @copyright Nike Madrid
 */


class Data
{
	/**
	 * @const string iterar de derecha a izquierda
	 */
	 
	const RIGHT_TO_LEFT = 'RL';
	
	/**
	 * @const string iterar de izquiera a derecha
	 */
	 
	const LEFT_TO_RIGHT = 'LR';
	
	/**
	 * @var array
	 */
	 
	protected $data = [];
	
	/**
	 * @var array guarda los valores eliminado
	 */
	 
	protected $delData = [];
	
	/**
	 * @var int incrementa el valor de cada iteracion
	 */
	 
	protected $increment = 0;
	
	/**
	 * añade data
	 *
	 * @param array|null $data
	 * @throws Exception
	 */
	 
	public function __construct(array $data = [])
	{
		if(is_string($data)){
			throw new \Exception("valor esperardo a ser un array o objecto");
		}
		$this->setData($data);
	}
	
	/**
	 * añadir valor por llamado medio de propiedades
	 *
	 * @magic set
	 * @param string $name
	 * @param string $value
	 */
	 
	public function __set($name, $value)
	{
		$this->setData([$name => $value]);
	}
	
	/**
	 * obtiene el valor atraves de propiedad
	 * 
	 * @magic get
	 * @param string $name
	 * @return string
	 */
	 
	public function __get($key)
	{
		return $this->getDataValue($key);
	}
	
	/**
	 * eliminar un valor
	 * 
	 * @magic unset
	 * @param $name
	 */
	 
	public function __unset($key)
	{
		$this->delete($key);
	}
	
	/**
	 * añadir valores
	 *
	 * @access public
	 * @param array $val
	 */
	 
	public function setData(array $val)
	{
		$this->data = $val;
	}
	
	/**
	 * obtiene valor de la data o trae el valor del objeto pasado
	 *
	 * @access publc
	 * @param string $name
	 * @return string
	 */
	 
	public function getDataValue($name)
	{
		if(is_array($this->data) && isset($this->data[$name])){
			return $this->data[$name];
		}
		return $this->dataObject($name);
	}
	
	/**
	 * iterar sobre array sobre clave numerica
	 *
	 * este metodo permite limitar el numero de array que necesites
	 * si especifica el valor min max contara desde el valor minimo hasta el valor
	 * maximo dado devolviendo un array con su misma clave numerica
	 * 
	 * @param int $min valor minimo
	 * @param int|null $max valor maximo 
	 * @return $this
	 */
	 
	public function dataLimiter($min = 0, $max = null)
	{
		$func = is_string($max) || is_null($max) ?  self::LEFT_TO_RIGHT : self::RIGHT_TO_LEFT;
		if($func){
			$this->{'getData'.$func}($min, isset($max) ? $max : $func);
		}
		
		return $this;
	}
	
	/** 
	 * claves numericas 
	 *
	 * left to right [LR] devuelve la data comenzando desde la izquierda
	 * right to left [RL] devuelve la data derecha a izquierda
	 * elimina los datos de RL or LR y se alamacena en la propiedad delData
	 * con valor real de la clave y el valor del array
	 *
	 * @access protected
	 * @param int $position
	 * @param string $reverse RL or LR
	 */
	 
	protected function getDataLR($position, $reverse)
	{
		if(count($this->data) > 0){
			$i = $position;  
			$c = count($this->data);
			for($x = 0; $x < $c; $x++){
				switch($reverse){
					case 'LR':
						if($x < $position){
							$this->delData[$x] = $this->data[$x];
							unset($this->data[$x]);
						}
						break;
						
					case 'RL':
						if($x < $i + 1){
							if(isset($this->data[$i + 1])) {
								$this->delData[$i + 1] = $this->data[$i + 1];
								unset($this->data[$i + 1]);
							}
						}
						break;
				}
				$i++;
				$this->increment++;
			}
		}
	}
	
	/** 
	 * clave numericas
	 *
	 * elimina valores no necesarios especificando el comienzo del arrayby el fin
	 *
	 * @access public
	 * @param int $min valor a donde comenzar
	 * @param int $max valor fin del array
	 * @return void
	 */
	 
	protected function getDataRL($min, $max)
	{
		if(count($this->data)){
			$this->increment = $max;
			for($x = 0; $x < $max; $x++){
			
				if($x < $min){
					$this->delData[$x] = $this->data[$x];
					unset($this->data[$x]);
				}
				if($this->increment > $max){
					if(isset($this->data[$this->increment])){
						$this->delData[$this->increment] = $this->data[$this->increment];
						unset($this->data[$this->increment]);
					}
				}
				$this->increment++;
			} 
		}
	}
	
	/**
	 * elimina un valor de la data
	 *
	 * @access public 
	 * @return void
	 */
	
	public function delete($key)
	{
		$d = isset($this->data[$key]);
		if(!$d){
			throw new \Exception("valor no eliminado por que no existe, error en {__CLASS__}->{__FUNCTION__}");
		}
		unset($this->data[$key]);
	}
	
	/**
	 * comprueba si la data es un objecto pasadl
	 *
	 * @access protected
	 * @return string
	 */
	 
	protected function dataObject($data = null)
	{
		if($this->data instanceof ArrayObject){
			echo 'object';
		}
		
		if($this->data instanceof \stdClass && $this->data->{$data}){
			return $this->data->{$data};
		}
	}
	
	/**
	 * regresa la data entera o los datos del limite si se ah añadido
	 *
	 * @access public
	 * @return arrray
	 */
	 
	public function getData()
	{
		return $this->data;
	}	
	
	/**
	 * une los elementos eliminados
	 *
	 * @access public
	 * @return array
	 */
	 
	public function getAll()
	{
		$data = $this->getData() + $this->delData;
		
		return $data;
	}
}