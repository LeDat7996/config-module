<?php

namespace Modules\Config\Helpers;

use Modules\Module\Helpers\Data as moduleData;
use Nwidart\Modules\Facades\Module;
use Illuminate\Support\Facades\Lang;

Class Data
{
	private $linkFileXml = [];

	function xml2array ( $xmlObject, $out = array () )
	{
	    foreach ( (array) $xmlObject as $index => $node )
	        $out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;

	    return $out;
	}

    public function __call($method, $parameters) {
    	if (!method_exists($this, $method)) {
	    	if (strpos($method, 'get') !== false) {
	            $attribute = substr($method, 3);
	            $this->getAllFileXML($attribute);
	            $xml = '';
	            if (count($parameters) == 2) {
	               	$xml = simplexml_load_file($this->getAllLinkFileXml()[$parameters[1]]); 
	               	$xml = $this->xml2array($xml->xpath('config/logo/link')[0]);
	            } else {
	            	foreach ($this->getAllLinkFileXml() as $link) {
		            	$xml=simplexml_load_file($this->getAllLinkFileXml());
		            }	 
	            }   
	     		return $xml;
	        } else {
	        	die(
	        		Lang::get(
	        			'config::messages.method_does_not_exist', 
	        			[
	        				'method' => $method.'()'
	        			]
	        		)
	        	);
	        }
    	}
    }

   	/**
   	 * @param Array $array
   	 */
	function setAllLinkFileXml(Array $array)
	{
		$this->linkFileXml = $array;
	}

	/**
	 * @return array
	 */
	public function getAllLinkFileXml()
	{
		return $this->linkFileXml;
	}

	function getAllFileXML($file) {
		$modules = Module::allEnabled();
		$array = [];
		foreach ($modules as $module) {
			$file = $module->getPath().'/xml/'.$file.'.xml';
			if (file_exists($file)) {
				$array[$module->getName()] = $file;				
			}

		}
		$this->setAllLinkFileXml($array);
	}
}
