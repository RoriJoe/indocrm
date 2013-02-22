<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */
//cheating, pake jquery!!
$page_header = '<script type="text/javascript" src="'.base_url('static/jquery.js').'"></script>';
$page_header .= '<script type="text/javascript" src="'.base_url('static/admin.js').'"></script>';

$page_title = 'User Management';
$this->load->view('header', array('page_title' => $page_title, 'page_header' => $page_header));
?>

                <div id="usermanager" class="card">
                    <h1><?php echo $page_title; ?></h1>
                    
                    <div style="margin:10px 0;">
                    
                    <?php
                    
                    $msg = flashmsg_get();
                    if ($msg)
                    {
                        echo '<div class="msgbox warning">'.htmlentities($msg).'</div>';
                    }
                    
                    ?>
                        
                    <div class="dialog">
                        <p>Jika anda punya pegawai, silahkan tambahkan account pegawai anda disini untuk bisa membantu anda mengatur IndoCRM.</p>
                    </div>
                        
                    <?php
                    
                    $allgroups = $this->db->get('groups')->result();
                    $allclients = $this->db->get('clients')->result();
                    
                    $admin = new stdClass();
                    $admin->name = 'Admin';
                    $admin->client_id = 0;
                    
                    $allclients[] = $admin;
                    
                    function select_groups( $name, $value, $array, $class='' )
                    {
                        $str = '<select name="'.$name.'" multiple="multiple" class="'.$class.'">';
                        foreach($array as $row)
                        {
                            $str .= '<option value="'.$row->group_id.'"'.(in_array($value, $array)?" selected=\"selected\"":"").'>'.$row->group_name.'</option>';
                        }
                        $str .= '</select>';
                        return $str;
                    }

                    function select_clients( $name, $value, $array, $class='' )
                    {
                        $str = '<select name="'.$name.'" class="'.$class.'">';
                        foreach($array as $row)
                        {
                            $str .= '<option value="'.$row->client_id.'"'.($value == $row->client_id?" selected=\"selected\"":"").'>'.$row->name.'</option>';
                        }
                        $str .= '</select>';
                        return $str;
                    }
                    
                    ?>

                    <form method="post" action="<?php echo site_url('users/update'); ?>">
                    <table class="list-table">
                    <tr>
                        <th><input type="checkbox" id="id_checkall" /></th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>E-mail</th>
                        <th>Name</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><input type="text" name="username[new]" value="<?php echo isset($username['new']) ? h($username['new']) : ''; ?>" size="10" /></td>
                        <td><input type="text" name="password[new]" value="<?php echo isset($password['new']) ? h($password['new']) : ''; ?>" size="10" /></td>
                        <td><input type="text" name="email[new]" value="<?php echo isset($email['new']) ? h($email['new']) : ''; ?>" size="20" /></td>
                        <td><input type="text" name="name[new]" value="<?php echo isset($name['new']) ? h($name['new']) : ''; ?>" size="20" /></td>
                        <?php if (!$this->orca_auth->user->client_id): ?>
                        <td><?php 
                        $arrConfirmed = array('0' => 'Belum konfirmasi', '1' => 'Sudah Konfirmasi');
                        echo '<select name="konfirmasi">';
                        foreach($arrConfirmed as $key => $konf){
							echo '<option value="'.$key.'">'.$konf.'</option>';
						}
						echo '</select>';
                        ?></td>
                        <td><?php echo select_groups('groups[new][]', isset($groups['new']) ? $groups['new'] : array(), $allgroups); ?></td>
                        <td><?php echo select_clients('client_id[new]', isset($client_id['new']) ? $client_id['new'] : 0, $allclients); ?></td>
                        <td>&nbsp;</td>
                        <?php else:?>
                        <td colspan="2">&nbsp;</td>
                        <?php endif; ?>
                    </tr>

                    <?php
                    
                    $where = '';
                    
                    if ( $this->orca_auth->user->client_id )
                    {
                        $where = "WHERE users.client_id = {$this->orca_auth->user->client_id}";
                    }
                        
                    $users = $this->db->query("SELECT users.*, clients.name AS client_name FROM users LEFT JOIN clients ON users.client_id = clients.client_id $where")->result_array();

                    foreach( $users as $row )
                    {
                        echo '
                        <tr>
                            <td><input type="checkbox" class="chkrow" name="selected[]" value="'.h($row['id']).'" /></td>
                            <td><span rel="'.h($row['id']).'" class="inline_text">'.h($row['username']?$row['username']:'empty').'</span><input class="inline_edit" type="text" name="username['.h($row['id']).']" value="'.h($row['username']).'" size="20" /></a></td>
                            <td><span rel="'.h($row['id']).'" class="inline_text">***</span><input class="inline_edit" type="text" name="password['.h($row['id']).']" value="" size="20" /></a></td>
                            <td><span rel="'.h($row['id']).'" class="inline_text">'.h($row['email']?$row['email']:'empty').'</span><input class="inline_edit" type="text" name="email['.h($row['id']).']" value="'.h($row['email']).'" size="20" /></td>
                            <td><span rel="'.h($row['id']).'" class="inline_text">'.h($row['name']?$row['name']:'empty').'</span><input class="inline_edit" type="text" name="name['.h($row['id']).']" value="'.h($row['name']).'" size="20" /></td>';
                        if (!$this->orca_auth->user->client_id){
							echo '<td><span rel="'.h($row['is_confirmed']).'" class="inline_text">'.h($row['is_confirmed']?'Sudah Confirm':'Belum Confirm').'</span>';
							echo '<select class="inline_edit" name="is_confirmed['.h($row['id']).']">';
							foreach($arrConfirmed as $key => $konf){
								echo '<option value="'.$key.'">'.$konf.'</option>';
							}
							echo '</select>';
							echo '</td>';
						}
                       echo '<td><a class="editgroups" href="'.site_url('users/groups?id='.$row['id']).'">groups</a></td>';
                        
                        if ( $this->orca_auth->user->client_id )
                        {
                            echo '<td>&nbsp;</td>';
                        }
                        else
                        {
                            echo '<td><span rel="'.h($row['id']).'" class="inline_text">'.h($row['client_name']?$row['client_name']:'empty').'</span>'.  select_clients('client_id['.$row['id'].']', $row['client_id'], $allclients, 'inline_edit').'</td>';
                        }
                        
                        echo '</tr>';
                    }

                    ?>
                    <tr>
                        <th colspan="8">
                            <input class="btn" type="submit" value="Save" />
                            <input class="btn" type="submit" id="delete_button" name="delete" value="Delete" />
                            <a href="<?php echo site_url('dashboard'); ?>">dashboard</a>
                        </th>
                    </tr>
                    </table>

                    <div id="inline_holder"></div>

                    </form>
                        
                    </div>

                </div>
                <div class="cl"></div>


<?php $this->load->view('footer');
