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

$page_title = 'Set User Groups';
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
                        
                        <div class="dialog">
                            <p>Pengesetan group untuk anda dan pegawai anda</p>
                        </div>
                        
                        <form method="post" action="<?php echo site_url('users/groups?id='.$user_id); ?>">
                        <table class="list-table">
                        <tr>
                            <th><input type="checkbox" id="id_checkall" /></th>
                            <th>Group Name</th>
                        </tr>
                        <?php

						if (!in_array($this->orca_auth->user->client_id, array(45,46,47,70)))
						//if (!in_array($this->orca_auth->user->client_id, array(43,44,45,70)))
						{
							foreach( $groups as $row )
							{
								if ($row['group_id'] == 6) continue;
								echo '
								<tr>
									<td><input type="checkbox" class="chkrow" name="selected[]" value="'.h($row['group_id']).'" '.(isset($selected[$row['group_id']]) ? ' checked="checked"':'').' /></td>
									<td><span rel="'.h($row['group_id']).'">'.h($row['group_name']?$row['group_name']:'empty').'</span></td>
								</tr>';
							}
						}else{
							if (in_array($this->orca_auth->user->client_id, array(45,46,47,70))){
							//if (in_array($this->orca_auth->user->client_id, array(43,44,45,70))){
								foreach( $groups as $row )
								{
									if($row['group_id'] == 6){
										echo '
										<tr>
											<td><input type="checkbox" class="chkrow" name="selected[]" value="'.h($row['group_id']).'" '.(isset($selected[$row['group_id']]) ? ' checked="checked"':'').' /></td>
											<td><span rel="'.h($row['group_id']).'">'.h($row['group_name']?$row['group_name']:'empty').'</span></td>
										</tr>';
									}
								}
							}
						}

                        ?>
                        <tr>
                            <th colspan="6">
                                <input class="btn" type="submit" value="Save" />
                                <a href="<?php echo site_url('users'); ?>">back to users</a>
                            </th>
                        </tr>
                        </table>
                        </form>

                        
                    </div>

                </div>

                <div id="sidebar">
                    <?php $this->load->view('dashboard_menu'); ?>
                </div>

                <div class="cl"></div>


<?php $this->load->view('footer');
