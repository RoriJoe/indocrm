<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

$page_header = '';
$page_title = 'Upload Data Pelanggan';
$this->load->view('header', array('page_title' => $page_title, 'page_header' => $page_header));

function select_kolom($name, $header, $fields)
{
	$idx = substr($name,6,-1);
    $str = '<select name="'.$name.'">';
    //$str .= '<option value="">Pilih kolom</option>';
    foreach($fields as $k => $arrfield)
    {
		foreach($arrfield as $tblfield => $field){
			$selected = ($k == $idx) ? 'selected' : '';
			$str .= '<option value="'.htmlentities($tblfield).'" '.$selected.'>'.htmlentities($field).'</option>';
		}
    }
    $str .= '</select>';
    return $str;
}

$table_fields = array(
    array('first_name' => 'Nama Depan'),
    array('last_name' => 'Nama Belakang'),
    array('email' => 'E-Mail'),
    array('mobile' => 'Handphone'),
    array('phone' => 'Telepon'),
    array('address' => 'Alamat'),
    array('city' => 'Kota'),
    array('state' => 'Propinsi'),
    array('country' => 'Negara'),
    array('zip_code' => 'Kode Pos'),
    array('bb_pin' => 'PIN BB'),
    array('website' => 'Website'),
    array('facebook' => 'Facebook'),
    array('twitter' => 'Twitter'),
);

?>

                <div id="content">
                    <h1><?php echo $page_title; ?></h1>
                    
                    <div class="dialog">
                        <p>
                            Tinggal satu langkah lagi, silahkan tentukan pasangan nama kolom file upload anda dengan nama kolom data pelanggan SimetriCRM,
                        </p>
                    </div>
                    
                    <form method="post" action="<?php echo site_url('customers/uploadresult'); ?>">
                        
                        <input type="hidden" name="id" value="<?php echo $this->input->get_post('id'); ?>" />
                        <input type="hidden" name="campaign" value="<?php echo isset($campaign_id) ? $campaign_id : ''; ?>" />
                        
                        <table id="mapping-table" style="width:90%">
                            <tr>
                                <th>Nama Kolom</th>
                                <th>Data Pelanggan</th>
                            </tr>
                            <?php
                            if ( isset($headers[0]) && is_array($headers[0]) )
                                $headers = array_shift($headers);
                            
                            $sampledata = array();
                            $found = 0;
                            foreach($data as $row)
                            {
                                foreach($headers as $k => $v)
                                {
                                    if ((!isset($sampledata[$k]) || !$sampledata[$k]) && isset($row[$k]) && $row[$k])
                                    {
                                        $sampledata[$k] = $row[$k];
                                    }
                                    else
                                    {
                                        $found++;
                                    }
                                }
                                
                                if ($found == count($headers))
                                    break;
                            }
                            
                            foreach( $headers as $k => $header ): ?>
                            <tr>
                                <th>
                                    <?php echo htmlentities($header); ?><input type="hidden" name="key[<?php echo $k; ?>]" value="<?php echo htmlentities($header); ?>" />
                                    <?php echo isset($sampledata[$k]) ? htmlentities(" ({$sampledata[$k]})") : ""; ?>
                                </th>
                                <td><?php echo select_kolom('field['.$k.']', $header, $table_fields); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                        
                        <div class="formfield">
                            <label for="id_category">Tambahkan di kategori:</label>
                            <select name="category" id="id_category">
                                <?php
                                $query = $this->db->get_where('customer_categories',array('client_id'=> $this->orca_auth->user->client_id));
                                if ($query->num_rows())
                                {
                                    foreach($query->result() as $row)
                                        echo '<option value="'.h($row->category).'">'.h($row->category).'</option>';
                                }
                                echo '<option value="">buat kategori baru</option>';
                                ?>
                            </select>
                        </div>
                    
                        <div class="formfield">
                            <label for="id_category">Nama Kategori:</label>
                            <input name="new_category" id="id_new_category" type="text" placeholder="kategori baru" size="20"/>
                        </div>
                        
                        <p>
                            <input class="btn primary-btn" value="Update" type="submit" />
                        </p>
                    </form>
                    
                </div>
		<div id="sidebar">
                    <!-- menu admin -->
                    <?php $this->load->view('dashboard_menu'); ?>
                </div>

                <div class="cl"></div>

<?php $this->load->view('footer');
