<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

/**
 * Description of users
 *
 * @author ferdhie
 */
class Users extends CI_Controller 
{
	function index()
	{
		$this->orca_auth->login_required();
		$this->load->view('users');
	}
	
	function update_users($data)
	{

		$error = false;
		if ( $this->orca_auth->user->client_id )
		{
			$data['client_id'] = $this->orca_auth->user->client_id;
		}

		if (preg_match('/[^a-z0-9_]/i',$data['username']) )
		{
			$error = 'Username hanya boleh karakter, angka dan garis bawah';
			return $error;
		}
		
		if (!$data['id'])
		{
			if (!$data['password'] || !$data['email'])
			{
				$error = 'Password dan email tidak boleh kosong';
				return $error;
			}

			if (!preg_match('/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/i',$data['email']))
			{
				$error = 'Format email kurang valid';
				return $error;
			}
		}
		else
		{
			if (!$data['password']) unset($data['password']);
			if (!$data['email']) unset($data['email']);
			else if (!preg_match('/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/i',$data['email']))
			{
				$error = 'Format email kurang valid';
				return $error;
			}
			if (!$data['name']) unset($data['name']);
		}
		
		$where = '';
		if ($data['id'])
		{
			$where .= "AND id <> ".$data['id'];
		}

		$row = $this->db->query("SELECT COUNT(*) AS cnt FROM users WHERE (username = ? OR email = ?) $where",array($data['username'], $data['email']))->row();
		if ($row->cnt > 0)
		{
			$error = 'Username atau email sudah terpakai';
			return $error;
		}
		
		if ($data['id'])
		{
			//cek before update
			
			$beforeupdate = $this->_cek_is_confirm($data['id']);
			
			$this->db->where('id', $data['id'])->update('users', $data);
			
			$afterupdate = $this->_cek_is_confirm($data['id']);
			
			$q = $this->db->query("SELECT client_type FROM clients WHERE client_id = '".$data['client_id']."'");
			$res = $q->result();
			
			$client_type = $res[0]->client_type;
			
			$strclient = "";
			switch ($client_type){
				case 0:
					$strclient = "Anda telah mendaftar untuk paket Personal dengan biaya Rp. 100.000,- per bulan. Maka Anda akan mendapatkan quota sebesar 1.000 SMS dan 100 Email.";
					break;
				case 1:
					$strclient = "Anda telah mendaftar untuk paket Professional dengan biaya Rp. 250.000,- per bulan. Maka Anda akan mendapatkan quota sebesar 1.000 SMS dan 100 Email perbulan. Plus Anda bisa mengatur signature Anda sendiri.";
					break;
				case 2:
					$strclient = "Anda telah mendaftar untuk paket Corporate dengan biaya Rp. 500.000,- per bulan. Maka Anda akan mendapatkan quota SMS tak terbatas. Email 500 Email perbulan. Anda juga bisa mengatur signature pada setiap pesan SMS Anda. Dan Anda pun bisa menggunakan nomor Anda sendiri";
					break;
			}
	
			if ($beforeupdate == 0 && $afterupdate == 1){
				 @mail( $data['email'], "IndoCRM: Konfirmasi Pendaftaran", "Salam dari Simetri CRM,\n\n".
					"Selamat, akun Anda telah aktif. Kami telah mengkonfirmasi Anda sebagai user untuk mengunakan layanan SMS IndoCRM.\n\n".
					$strclient."\n\n".
					"Terima kasih atas partisipasi anda\n\n".
					"Hormat kami,\n\n".
					"Simetri CRM\n\n".
					"--\n".
					"Pesan ini telah diproduksi dan didistribusikan oleh PT Sinar Media Tiga, Raya Sulfat 96c, Malang, Indonesia 65123 - 0341-406633",
					"From: info@indocrm.com\r\n".
					"To: ".$data['username']." <{".$data['email']."}>");
				 
				list($y,$m,$d) = explode('-', date('Y-m-d'));
				$tgltenggat = date('Y-m-d', mktime(0,0,0,$m,$d+7,$y));
				
				$this->db->insert('tagihan_client', array(
								'client_id' => $data['client_id'],
								'paid' => 1
								 )
								);
			}
		}
		else
		{
			$this->db->insert('users', $data);
		}
		//echo $this->db->last_query();

		return $error;
	}
	
	function update()
	{
		$this->orca_auth->login_required();
		$this->load->library('form_validation');

		$error = '';

		if ( is_post_request() )
		{
			if ( p('delete') )
			{
				$selected = p('selected');
				if ( is_array($selected) && $selected )
				{
					$selected = implode(", ", array_map( 'intval', $selected));
					if ($selected)
					{
						$this->db->query("DELETE FROM users WHERE id in ( $selected )");
						flashmsg_set("Delete user success");
					}
				}
			}
			else
			{
				$arrusername = p('username');
				$arrpassword = p('password');
				$arremail = p('email');
				$arrname = p('name');
				$arrconfirm = p('is_confirmed');
				$arrid = p('selected');
				$arrclient_id = p('client_id');
				
				do
				{
					
					if ( isset($arrusername['new']) && $arrusername['new'] )
					{
					
					/*	$this->db->where('username',$arrusername['new']);
						$q = $this->db->get('users');
						if ($q->num_rows()==0){*/
							$data = array(
								'username' => $arrusername['new'],
								'password' => isset($arrpassword['new']) ? $this->orca_auth->make_hash($arrpassword['new'], '', true) : '',
								'email' => isset($arremail['new']) ? $arremail['new'] : '',
								'name' => isset($arrname['new']) ? $arrname['new'] : '',
								'client_id' => isset($arrclient_id['new']) ? $arrclient_id['new'] : '',
								'confirm_hash' =>  $this->orca_auth->make_hash( uniqid(mt_rand()), '', true ),
								'id' => null,
							);
					
						$error = $this->update_users($data);
						if ($error)
						{
							flashmsg_set($error);
							break;
						}
					}
					
					foreach( $arrid as $k => $id )
					{
					
						
						$this->db->where('id',$id);
						$q = $this->db->get('users');
						$res = $q->result();
						$data = array(
							'username' => isset($arrusername[$id]) && $arrusername[$id] ? $arrusername[$id] : $res[0]->username,
							'password' => isset($arrpassword[$id]) && $arrpassword[$id] ? $arrpassword[$id] : '',
							'email' => isset($arremail[$id]) && $arremail[$id] ? $arremail[$id] : $res[0]->email,
							'name' => isset($arrname[$id]) && $arrname[$id] ? $arrname[$id] : $res[0]->name,
							'is_confirmed' => isset($arrconfirm[$id]) ? $arrconfirm[$id]: $res[0]->is_confirmed,
							'client_id' => isset($arrclient_id[$id]) && $arrclient_id[$id] ? $arrclient_id[$id] : $res[0]->client_id,
							'id' => $id,
						);

						if (!$data['password'])
							unset($data['password']);
						else $data['password'] = $this->orca_auth->make_hash($data['password'], '', true);

						$error = $this->update_users($data);
						if ($error)
						{
							flashmsg_set($error);
							break 2;
						}
					}

					flashmsg_set("Update user success");
				}
				while(0);
			}
		}

		redirect( site_url('users?update=1') );
	}
	
	function groups()
	{
		$user_id = $this->input->get('id');
		if ( !$user_id )
		{
			show_404();
		}

		if ( is_post_request() )
		{
			$selected = p('selected');
			if ($selected && is_array($selected))
			{
				$selected = array_filter(array_map('intval', $selected));
				if ( $selected )
				{
					$sql = "DELETE FROM user_groups WHERE user_id = ?";
					$this->db->query($sql, array($user_id));

					$sql = "INSERT INTO user_groups (user_id, group_id) VALUES (?,?)";
					foreach( $selected as $sel )
					{
						$this->db->query($sql, array($user_id, $sel));
					}

					flashmsg_set('Success, user groups updated');
				}
			}

			redirect(site_url('users/groups?id='.$user_id.'&ok=1'));
			exit;
		}

		$rows = $this->db->query( "SELECT group_id FROM user_groups WHERE user_id = ?", array($user_id) )->result_array();
		$selected = array();
		foreach( $rows as $row )
		{
			$selected[$row['group_id']] = 1;
		}

		$sql = "SELECT * FROM groups";
		if ( $this->orca_auth->user->client_id )
			$sql .= " WHERE admin_group = 0";
		$groups = $this->db->query( $sql )->result_array();

		$this->load->view( 'user_groups', array('groups' => $groups, 'selected' => $selected, 'user_id' => $user_id) );
	}
	
	private function _cek_is_confirm($id){
		$this->db->select('is_confirmed');
		$this->db->where('id', $id);
		$q = $this->db->get('users');
		$res = $q->result();
		
		return $res[0]->is_confirmed;
	}
}

