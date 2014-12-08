<?php
/**
* 
* @author dev2fun (darkfriend)
* @copyright darkfriend
* @version 0.1.2
* 
*/
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(class_exists('Dev2funListElements')) return;

class Dev2funListElements extends CBitrixComponent {
	
	/**
	* 
	* @param array $arrSections
	* 
	* @return array is not section empty
	*/
	public final function checkSectionOnEmpty($arrSections){
		if(empty($arrSections)) return false;
		foreach($arrSections as $keySection=>$arrSection){
			if($arrSection['ELEMENT_CNT']<=0){
				unset($arrSections[$keySection]);
			}
		}
		return $arrSections;
	}
}