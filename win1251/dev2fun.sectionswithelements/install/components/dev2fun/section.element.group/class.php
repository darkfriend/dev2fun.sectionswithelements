<?php
/**
* 
* @author dev2fun (darkfriend)
* @copyright darkfriend
* @version 0.1.1
* 
*/
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class Dev2funListElements extends CBitrixComponent {
	
	private $type;
	
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
	
	
	
	public function setType($type){
		$this->type = $type;
	}
	
	/**
	* ����� ���������� ������ � �������������� id
	* @param array $arraySections
	* 
	* @return array|false
	*/
	public final function GetArraySectionsID($arraySections){
		if(empty($arraySections)) return false;
		for($i=0,$cnt=count($arraySections);$i<$cnt;$i++){
			if(is_numeric($arraySections[$i])){
				$arraySections[$i] = intval($arraySections[$i]);
			} else {
				CIBlockFindTools::GetSectionID(
					false,
					$arraySections[$i],
					array(
						"GLOBAL_ACTIVE" => "Y",
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					)
				);
			}
		}
		return $arraySections;
	}
	
	public final function GetArraySectionInfo(){
		
	}
	
	/**
	* 
	* @param array $item ������ �������
	* @param string|integer $tools DEPTH_LEVEL(int, ������� �����������)|prev(string, ��������� ��������)|main_prev(string, ������� ��������)
	* 
	* @return array
	*/
	public final function getParentSectionArray($item,$tools='prev'){
		if(!$item['IBLOCK_SECTION_ID']) return false;
		$res = CIBlockSection::GetByID($item['IBLOCK_SECTION_ID']);
		if($ar_res = $res->GetNext()){
			switch($tools){
				case 'prev' : break;
				case 'main_prev' : $ar_res=$this->getParentSectionArray($ar_res['IBLOCK_SECTION_ID'], $tools); break;
				default : 
					if($ar_res['DEPTH_LEVEL']!==$tools){
						$ar_res=$this->getParentSectionArray($ar_res['IBLOCK_SECTION_ID'], $tools);
					}
			}
		}
		return $ar_res;
	}
	
	/*public final function conditionResultArray($arrItems, $groupKey='SECTION_RESULT'){
		$result = array();
		foreach($arrItems as $key=>$item){
			$thisKey = $key==0 ? 0 : count($result)-1;
			
//			var_dump($result[$thisKey][$groupKey]['ID']==$item[$groupKey]['ID']);
			var_dump($result[$thisKey][count($result[$thisKey])-1][$groupKey]['ID'].'=='.$item[$groupKey]['ID']);
			if($result[$thisKey][count($result[$thisKey])-1][$groupKey]['ID']==$item[$groupKey]['ID']){
				$result[$thisKey][] = $item;
			} else {
				$result[][] = $item;
			}
//			print_pre($result, true);
//			next($result[$thisKey]);
		}
		return $result;
	}*/
	/**
	* ������������ ������, ��������� �� ��������� ������
	* 
	* @param array $arrItems
	* @param string $pathGroupKeys ���� �� ������������� ��������
	* 
	* @return array ���������������
	*/
	public final function conditionResultArray($arrItems, $pathGroupKeys='SECTION_RESULT/ID'){
		
		$result = array();
		$groupPath = explode('/',$pathGroupKeys);
		
		foreach($arrItems as $key=>$item){
			$thisKey = $key==0 ? 0 : count($result)-1;

			$ifs = $this->recResultArray($item,$groupPath,$result[$thisKey][0]);
			
			if($ifs){
				$result[$thisKey][] = $item;
			} else {
				$result[][] = $item;
			}
		}
		return $result;
	}
	
	/**
	* ��������� ������� ������� �� ������� ������������� ����� � ���������� �������� � ��� ��� ��� �����������
	* 
	* @param array $item
	* @param array $groupPath
	* @param array|null $arrRes
	* @param integer|null $step
	* 
	* @return true|false
	*/
	private function recResultArray($item,$groupPath,$arrRes,$step){
		if(!$arrRes) return false;
		$result = false;

		if(is_array($item)){
			foreach($item as $key=>$val){
				
				if(!$step && $step!==0){
					$step = 0;
				}
				
				if($this->recReturnKeyGroup($key,$groupPath,$step)){
					if(is_array($val)){
						$ifs2 = $this->recResultArray($val,$groupPath,$arrRes,$step+1);
						if($ifs2){
							$result = true;
						}
					} else {
						if($this->recReturnValueArResult($arrRes,$groupPath)==$val){
							$result = true;
						}
					}
				}
			}
		}
		return $result;
	}
	
	/**
	* ��������� ������� ����� � ����� �� ������
	* 
	* @param string $key
	* @param array|string $groupPath
	* @param integer $step
	* 
	* @return string|false
	*/
	private function recReturnKeyGroup($key,$groupPath,$step=0){
		
		if(is_array($groupPath)){
			$groupPath=$this->recReturnKeyGroup($key,$groupPath[$step]);
		} 
		
		if($key==$groupPath){
			return $key;
		}
		
		return false;
	}
	
	/**
	* ���������� �������� ��������������� �������, �������� ���� �� ������
	* 
	* @param array $arrRes
	* @param array $groupPath
	* @param integer $key
	* 
	* @return string �������� � �������������� �������
	*/
	private function recReturnValueArResult($arrRes,$groupPath,$key=0){
		if(is_array($groupPath)){
			if(is_array($arrRes[$groupPath[$key]])){
				$res = $this->recReturnValueArResult($arrRes[$groupPath[$key]],$groupPath,$key+1);
			} else {
				$res = $arrRes[$groupPath[$key]];
			}
		} else {
			$res = $arrRes[$groupPath[$key]];
		}
		return $res;
	}
	
	/*public function rebResultArray($resultArray){
		$result = array();
		foreach($resultArray as $keyItem=>$valItem){
			if(is_numeric($keyItem)){
				$result[$keyItem] = $this->rebResultArray($valItem);
			} else {
		}
	}*/
}