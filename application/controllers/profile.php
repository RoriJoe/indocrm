<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

class Profile extends CI_Controller
{
    public function index()
    {
        $this->orca_auth->login_required();
        $this->load->view('profile');
    }
    
    public function changepass()
    {
        $this->orca_auth->login_required();
        $result = array('success' => false);
        header('Content-type: application/json; charset=UTF-8');
        
        $oldpass = $this->orca_auth->make_hash($this->input->post('oldpass'), '', true);
        $newpass = $this->input->post('newpass');
        $confirm = $this->input->post('c');
        
        if ($oldpass == $this->orca_auth->user->password)
        {
            if ($newpass == $confirm)
            {
                $newpass = $this->orca_auth->make_hash($this->input->post('newpass'), '', true);
                $this->db->where('id',$this->orca_auth->user->id)->update('users', array('password' => $newpass));
                $this->orca_auth->user->password=$newpass;
                $result['success'] = true;
            }
        }
        
        echo json_encode($result);
    }
    
    public function info()
    {
        $this->orca_auth->login_required();
        $result = array('success' => true, 'data' => $this->orca_auth->user);
        header('Content-type: application/json; charset=UTF-8');
        echo json_encode($result);
    }
    
    public function save()
    {
        $this->orca_auth->login_required();
        $result = array('success' => true, 'data' => array());
        
        $timeView= $this->input->post('timeview');
        $tmp = explode(";", $timeView);
        $timezone = $tmp[0];
        $timezone_id = $tmp[1];
        
        $data = array(
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'timezone' => $timezone,
            'timezone_id' => $timezone_id,
            'bdate' => $this->input->post('bdate'),
            'mobile' => $this->input->post('mobile')
        );
        
        if ( isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name']) )
        {
            $config = array();
            $config['upload_path'] = './u/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['overwrite'] = false;
            $this->load->library('upload', $config);

            if ($this->upload->do_upload('file'))
            {
                $img = $this->upload->data();
                $config2 = array();
                $config2['image_library'] = 'gd2';
                $config2['source_image'] = $img['full_path'];
                $config2['maintain_ratio'] = FALSE;
                $config2['width'] = 64;
                $config2['height'] = 64;
                $this->load->library('image_lib', $config2);
                if($this->image_lib->resize())
                {
                    $data['photo'] = $img['file_name'];
                }
                else
                {
                    $result['success'] = false;
                    $result['error'] = 'Resize gagal';
                    echo json_encode($result);
                    return;
                }
            }
            else
            {
                $result['success'] = false;
                $result['error'] = $this->upload->display_errors('','');
                echo json_encode($result);
                return;
            }
        }
        
        $this->db->where('id', $this->orca_auth->user->id)->update( 'users', $data );
        
        $query = $this->db->get_where('users', array('id'=> $this->orca_auth->user->id), 1);
        if ($query->num_rows())
        {
            $this->orca_auth->user = $query->row();
            $result['data'] = $this->orca_auth->user;
            $result['success'] = true;
        }

        echo json_encode($result);
    }
    
       public function timezone(){
		
		$this->orca_auth->login_required();
        $result = array('success' => true, 'rows'=>array());
		
		$list = DateTimeZone::listAbbreviations();
		$idents = DateTimeZone::listIdentifiers();
		$arrTimezone = array();

		$data = $offset = $added = array();
		foreach ($list as $abbr => $info) {
			foreach ($info as $zone) {
				if ( ! empty($zone['timezone_id'])
					AND
					! in_array($zone['timezone_id'], $added)
					AND 
					  in_array($zone['timezone_id'], $idents)) {
					$z = new DateTimeZone($zone['timezone_id']);
					$c = new DateTime(null, $z);
					$zone['time'] = $c->format('H:i a');
					$data[] = $zone;
					$offset[] = $z->getOffset($c);
					$added[] = $zone['timezone_id'];
				}
			}
		}

		array_multisort($offset, SORT_ASC, $data);
		
		$arrTemp = array();
		
		foreach ($data as $key => $row) {
			$arrTimezone[] = array('timezone' => $this->_formatOffset($row['offset']).';'.$row['timezone_id'], 'country' => 'GMT: '.$this->_formatOffset($row['offset']).' - Benua/Kota : '.$row['timezone_id']);
		}

		/*
		asort($arrTimezone);
		echo '<pre>';
		print_r($arrTimezone);
		echo '</pre>';
		*/
		echo json_encode((array)$arrTimezone);
		
	}
	
	function _formatOffset($offset) {
		$hours = $offset / 3600;
		$remainder = $offset % 3600;
		$sign = $hours > 0 ? '+' : '-';
		$hour = (int) $hours;
		return $hour;

	}
}
