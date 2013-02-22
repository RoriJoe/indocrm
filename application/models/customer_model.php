<?php
class Customer_model extends CI_Model {
	function __construct() {
        parent::__construct();
		
		$this->Table = CUSTOMER;
		$this->Field = array(
			'customer_id', 'first_name', 'last_name', 'address', 'city', 'state', 'zip_code', 'phone', 'mobile', 'photo', 'email', 'facebook',
			'twitter', 'bb_pin', 'website', 'client_id', 'create_date', 'is_delete', 'country', 'category'
		);
    }
	
	function Update($Param) {
		$Result = array();
		
		if (empty($Param['customer_id'])) {
			$Param['create_date'] = $this->config->item('time_current');
			$InsertQuery  = GenerateInsertQuery($this->Field, $Param, $this->Table);
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['customer_id'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
		} else {
			$UpdateQuery  = GenerateUpdateQuery($this->Field, $Param, $this->Table);
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['customer_id'] = $Param['customer_id'];
			$Result['QueryStatus'] = '1';
		}
		
		return $Result;
	}
	
	function UpdateDetail($Param) {
		$Count = 0;
		$SelectQuery  = "SELECT COUNT(*) Count FROM " . CUSTOMER_DETAIL . " WHERE customer_id = '" . $Param['customer_id'] . "'";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Count = $Row['Count'];
		}
		
		if ($Count == 0) {
			$InsertQuery = "
				INSERT INTO " . CUSTOMER_DETAIL . " (customer_id, category_id)
				VALUES ('" . $Param['customer_id'] . "', '" . $Param['category_id'] . "')
			";
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
		} else {
			$UpdateQuery = "
				UPDATE " . CUSTOMER_DETAIL . "
				SET category_id = '" . $Param['category_id'] . "'
				WHERE customer_id = '" . $Param['customer_id'] . "'
				LIMIT 1
			";
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
		}
		
		$Result['customer_id'] = $Param['customer_id'];
		$Result['QueryStatus'] = '1';
	}
	
	function GetByID($Param) {
		$Array = array();
		
		if (isset($Param['customer_id'])) {
			$SelectQuery  = "
				SELECT Customer.*
				FROM ".CUSTOMER." Customer
				WHERE Customer.customer_id = '".$Param['customer_id']."' LIMIT 1
			";
		}
		
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Array = StripArray($Row);
		}
		
		return $Array;
	}
	
	function GetArray($Param = array()) {
		$Array = array();
		
		$StringClient = (isset($Param['client_id']) && !empty($Param['client_id'])) ? "AND Customer.client_id = '" . $Param['client_id'] . "'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
        $StringSorting = (isset($Param['sort'])) ? GetStringSorting($Param['sort']) : 'Customer.first_name ASC';
		
		$SelectQuery = "
			SELECT Customer.*
			FROM ".CUSTOMER." Customer
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
		
		$StringClient = (isset($Param['client_id']) && !empty($Param['client_id'])) ? "AND Customer.client_id = '" . $Param['client_id'] . "'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".CUSTOMER." Customer
			WHERE 1 $StringClient $StringFilter
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$TotalRecord = $Row['TotalRecord'];
		}
		
		return $TotalRecord;
	}
	
	function Delete($Param) {
		$DeleteQuery  = "DELETE FROM ".CUSTOMER." WHERE customer_id = '".$Param['customer_id']."' LIMIT 1";
		$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
		
		$Result['QueryStatus'] = '1';
		$Result['Message'] = 'Data berhasil dihapus.';
		
		return $Result;
	}
}
?>