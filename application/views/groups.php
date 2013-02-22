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

$page_title = 'Groups Management';
$this->load->view('header', array('page_title' => $page_title, 'page_header' => $page_header));
?>

                <div id="content">
                    <h1><?php echo $page_title; ?></h1>
                    
                    <div style="margin:10px 0;">
                    
                    <?php
                    
                    $msg = flashmsg_get();
                    if ($msg)
                    {
                        echo '<div class="msgbox warning">'.htmlentities($msg).'</div>';
                    }

                    ?>

                    <form method="post" action="<?php echo site_url('groups/update'); ?>">
                    <table class="list-table">
                    <tr>
                        <th><input type="checkbox" id="id_checkall" /></th>
                        <th>Group Name</th>
                        <?php if (!$this->orca_auth->user->client_id): ?>
                        <th>Admin Group</th>
                        <?php endif; ?>
                        <th>&nbsp;</th>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><input type="text" name="group_name[new]" value="<?php echo isset($group_name['new']) ? h($group_name['new']) : ''; ?>" size="20" /></td>
                        <?php if (!$this->orca_auth->user->client_id): ?>
                        <td><label for="admin_groupnew"><input id="admin_groupnew" type="checkbox" name="admin_group[new]" value="1" <?php echo isset($admin_group['new']) ? 'checked="checked"' : ''; ?> />&nbsp;Admin</label></td>
                        <?php endif; ?>
                        <td>&nbsp;</td>
                    </tr>

                    <?php
                    
                    if ($this->orca_auth->user->client_id)
                    {
                        $this->db->where('admin_group', 0);
                    }
                    
                    $query = $this->db->get('groups');

                    foreach( $query->result_array() as $row )
                    {
                        echo '
                        <tr>
                            <td><input type="checkbox" class="chkrow" name="selected[]" value="'.h($row['group_id']).'" /></td>
                            <td><span rel="'.h($row['group_id']).'" class="inline_text">'.h($row['group_name']?$row['group_name']:'empty').'</span><input class="inline_edit" type="text" name="group_name['.h($row['group_id']).']" value="'.h($row['group_name']).'" size="20" /></td>';
                        
                        if (!$this->orca_auth->user->client_id)
                            echo '<td><span rel="'.h($row['group_id']).'" class="inline_text">'.h($row['admin_group']?'ADMIN':'ALL').'</span><label class="inline_edit" for="admin_group'.h($row['group_id']).'"><input type="checkbox" name="admin_group['.h($row['group_id']).']" value="1" id="admin_group'.h($row['group_id']).'" '.($row['admin_group']?' checked="checked"':'').' />&nbsp;ADMIN</label></td>';
                        echo '<td><a class="editperms" href="'.site_url('groups/perms?id='.$row['group_id']).'">permissions</a></td>
                        </tr>';
                    }

                    ?>
                    <tr>
                        <th colspan="6">
                            <input class="btn" type="submit" value="Save" />
                            <input class="btn" type="submit" id="delete_button" name="delete" value="Delete" />
                        </th>
                    </tr>
                    </table>

                    <div id="inline_holder"></div>

                    </form>
                        
                    </div>

                </div>

                <div id="sidebar">
                    <?php $this->load->view('dashboard_menu'); ?>
                </div>

                <div class="cl"></div>


<?php $this->load->view('footer');
