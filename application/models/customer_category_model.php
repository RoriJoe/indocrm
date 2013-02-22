<?php
class Customer_Category_model extends CI_Model {
	function __construct() {
        parent::__construct();
		
		$this->Table = CUSTOMER_CATEGORY;
		$this->Field = array( 'category_id', 'client_id', 'category' );
    }
	
	function Update($Param) {
		$Result = array();
		
		if (empty($Param['category_id'])) {
			$InsertQuery  = GenerateInsertQuery($this->Field, $Param, $this->Table);
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['category_id'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
		} else {
			$UpdateQuery  = GenerateUpdateQuery($this->Field, $Param, $this->Table);
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['category_id'] = $Param['category_id'];
			$Result['QueryStatus'] = '1';
		}
		
		return $Result;
	}
	
	function GetByID($Param) {
		$Param['ForceInsert'] = (empty($Param['ForceInsert'])) ? 0 : 1;
		
		if (isset($Param['category_id'])) {
			$SelectQuery  = "
				SELECT CustomerCategory.*
				FROM ".CUSTOMER_CATEGORY." CustomerCategory
				WHERE CustomerCategory.category_id = '".$Param['category_id']."' LIMIT 1
			";
		} else if (!empty($Param['category']) && !empty($Param['client_id'])) {
			$SelectQuery  = "
				SELECT CustomerCategory.*
				FROM ".CUSTOMER_CATEGORY." CustomerCategory
				WHERE CustomerCategory.client_id = '".$Param['client_id']."' AND CustomerCategory.category = '".$Param['category']."' LIMIT 1
			";
		}
		
		$Array = array();
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Array = StripArray($Row);
		}
		
		if (count($Array) == 0 && $Param['ForceInsert'] == 1) {
			$Result = $this->Update($Param);
			$Array = $this->GetByID(array('category_id' => $Result['category_id']));
		}
		
		return $Array;
	}
	
	function GetArray($Param = array()) {
		$Array = array();
		
		$StringClient = (isset($Param['client_id']) && !empty($Param['client_id'])) ? "AND CustomerCategory.client_id = '" . $Param['client_id'] . "'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
        $StringSorting = (isset($Param['sort'])) ? GetStringSorting($Param['sort']) : 'CustomerCategory.category ASC';
		
		$SelectQuery = "
			SELECT CustomerCategory.*
			FROM ".CUSTOMER_CATEGORY." CustomerCategory
			WHERE 1 $StringClient $StringFilter
			ORDER BY $StringSorting
			LIMIT $PageOffset, $PageLimit
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Row = StripArray($Row);
			$Array[] = $Row;
		}
		
		return $Array;
	}
	
	function GetCount($Param = array()) {
		$TotalRecord = 0;
		
		$StringClient = (isset($Param['client_id']) && !empty($Param['client_id'])) ? "AND CustomerCategory.client_id = '" . $Param['client_id'] . "'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".CUSTOMER_CATEGORY." CustomerCategory
			WHERE 1 $StringClient $StringFilter
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$TotalRecord = $Row['TotalRecord'];
		}
		
		return $TotalRecord;
	}
	
	function Delete($Param) {
		$DeleteQuery  = "DELETE FROM ".CUSTOMER_CATEGORY." WHERE category_id = '".$Param['category_id']."' LIMIT 1";
		$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
		
		$Result['QueryStatus'] = '1';
		$Result['Message'] = 'Data berhasil dihapus.';
		
		return $Result;
	}
}
?>