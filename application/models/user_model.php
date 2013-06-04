<?php

class User_model extends CI_Model
{
	static $companies = array();
	private $table_name = 'users';			// user accounts
	private $perms = NULL;
	private $allperms = NULL;

	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_by_userpass( $username, $password=NULL )
	{
		/*$this->db->select('u.*,ug.group_id');
		$this->db->from($this->table_name.' as u');
		$this->db->join('user_groups as ug','u.id=ug.user_id');
		$this->db->where('username', $username);*/
		$strPassword ='';
		if ( !is_null($password) )
		{
			//$this->db->where('password', $password);
			
			$strPassword = "AND password = '$password'";
		}
		
		$sql = "SELECT u.*, CONCAT('GMT ', u.timezone, ' - ', u.timezone_id) as timeview, ug.group_id
		FROM ".$this->table_name." u JOIN user_groups ug ON u.id = ug.user_id WHERE u.username = '".$username."' $strPassword AND is_confirmed = 1";
		
		$query = $this->db->query($sql);
		//echo $this->db->last_query();
		return $query->num_rows() > 0 ? $query->row() : null;
	}
	
	public function get_company( $client_id )
	{
		if (!isset(self::$companies[$client_id]))
		{
			$r = $this->db->get_where('clients', array('client_id' => $client_id), 1);
			if ($r->num_rows() > 0)
			{
				self::$companies[$client_id] = $r->row();
			}
		}
		return isset(self::$companies[$client_id])?self::$companies[$client_id]:0;
	}
	
	public function check_perms($user_id, $uri)
	{
		$sql = "(SELECT t3.* FROM user_groups t1
				LEFT JOIN group_perms t2 ON t1.group_id = t2.group_id
				LEFT JOIN perms t3 ON t2.perm_id = t3.perm_id WHERE t1.user_id = ? AND t3.perm_path = ?
				ORDER BY t3.parent_id, t3.perm_order, t3.perm_id)
				UNION
				(SELECT t5.* FROM user_perms t4
				LEFT JOIN perms t5 ON t4.perm_id = t5.perm_id
				WHERE t4.user_id = ? AND t5.perm_path = ?
				ORDER BY t5.parent_id, t5.perm_order, t5.perm_id)";
		$query = $this->db->query($sql, array($user_id, $uri, $user_id, $uri));
		$this->perms = array();
		if ( $query->num_rows() > 0 )
		{
			$this->perms = $query->result();
		}
		return $this->perms;
	}
	
	public function my_perms($user_id)
	{
		if ( !$this->perms )
		{
			$sql = "(SELECT t3.* FROM user_groups t1
						LEFT JOIN group_perms t2 ON t1.group_id = t2.group_id
						LEFT JOIN perms t3 ON t2.perm_id = t3.perm_id WHERE t1.user_id = ?
						ORDER BY t3.parent_id, t3.perm_order, t3.perm_id)
					UNION
					(SELECT t5.* FROM user_perms t4
						LEFT JOIN perms t5 ON t4.perm_id = t5.perm_id
						WHERE t4.user_id = ?
						ORDER BY t5.parent_id, t5.perm_order, t5.perm_id)";
			$query = $this->db->query($sql, array($user_id, $user_id));
			$this->perms = array();
			if ( $query->num_rows() > 0 )
			{
				$this->perms = $query->result();
			}
		}
		return $this->perms;
	}
	
	public function print_menu($user_id, $parent_id = 0)
	{
		if ($this->orca_auth->user->group_id == 6){
		
			//$arrClient = array ('45' => '647ab965440cc681b4c196df5c9c19e9f83e621d', '46'=> 'a235a95d72bfc13ead5b9e43f2cb8ba15d6c0a8f','47' => '7d0d26f6992e8832d2dedb6b286fb0fa9a8cb2a8');
			
			/**
			 * this is for local use only
			 * $arrClient = array ('43' => '647ab965440cc681b4c196df5c9c19e9f83e621d', '44'=> 'a235a95d72bfc13ead5b9e43f2cb8ba15d6c0a8f','45' => '7d0d26f6992e8832d2dedb6b286fb0fa9a8cb2a8');
			 * 
			 * 
			*/

			if (in_array($this->orca_auth->user->client_id, array(45,46,47,70,139)))
            {
                /**
                 * this is for local use only
                    
                    //if (in_array($this->orca_auth->user->client_id, array(43,44,45))){
                * 
                */
				$path = site_url('dashboard/wgpenyiar/');
				echo '<li><a href="'.$path.'">SMS Masuk Penyiar</a></li>';
			}
		}else{
			$perms = $this->my_perms($user_id);
			
			$arrperms = array();

			foreach($perms as $perm)
			{
				if ($perm->parent_id != $parent_id)
					continue;
				$arrperms[] = $perm;
			}
			
			usort($arrperms, array($this, 'sort_order'));
			
			foreach($arrperms as $perm)
			{
				if ( !$perm->perm_path )
				{
					if ($this->orca_auth->user->client_id == 139 && in_array($perm->perm_name, array('Sales', 'Tagihan')))
						continue;
					if ($parent_id == 0)
					{
						echo '<li class="'.$perm->perm_class.'"><h2>'.$perm->perm_name.'</h2>';
					}
					else
					{
						echo '<li><span class="'.$perm->perm_class.'">'.$perm->perm_name.'</span>';
					}
				}
				else
				{
					$path = $perm->perm_path;
					if ($this->orca_auth->user->client_id == 139 && in_array($path, array('invoices', 'invoices/design', 'products')))
						continue;
					echo '<li><a class="'.$perm->perm_class.'" href="'.site_url($perm->perm_path).'" title="'.$perm->perm_name.'">'.$perm->perm_name.'</a>';
				}

				echo '<ul>';
				$this->print_menu( $user_id, $perm->perm_id );
				
				echo '</ul></li>';
			}
			
			if ($parent_id == 8 && in_array($this->orca_auth->user->client_id, array(45,46,47,70,139))){
				$path = site_url('/dashboard/wgpenyiar/');
				echo '<li><a class="'.$perm->perm_class.'" href="'.$path.'">SMS Masuk Penyiar</a></li>';
			}
		}
	}
	
	public function client_active($client_id){
		
		$active =0;
		
		if ($client_id == 0)
			return 1;
		
		$this->db->where('client_id', $client_id);
		$this->db->select('is_active');
		$q = $this->db->get('clients');
		// echo $this->db->last_query();
		if ($q->num_rows()>0){
			$res = $q->result();
			$active = $res[0]->is_active;
		}
		return $active;
	}
	
	function sort_order($a,$b)
	{
		return $a->perm_order - $b->perm_order;
	}
	
}
