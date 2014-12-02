<?php
/**
* 
* @author dev2fun (darkfriend)
* @copyright darkfriend
* @version 0.1.1
* 
*/

if(class_exists('CCheckPermForComponents')) return;

class CCheckPermForComponents{
	
	/** 
	* 
	* @param string $path - abs. path to file||dir
	* 
	* @return key_msg||true
	*/
	public static function checkPermissions($path){
		if(!is_readable($path)){
			return 'MSG_NO_READABLE';
		}
		if(!is_writable($path)){
			return 'MSG_NO_WRITABLE';
		}
		return true;
	}
	
}