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

$page_title = 'Set Group Permission';
$this->load->view('header', array('page_title' => $page_title, 'page_header' => $page_header));
?>

                <div id="content">
                    <h1><?php echo $page_title; ?></h1>
                    
                    <div id="group-perms" style="margin:10px;">
                    
                    <?php
                    
                    $msg = flashmsg_get();
                    if ($msg)
                    {
                        echo '<div class="msgbox warning">'.htmlentities($msg).'</div>';
                    }

                    function print_tree( $rows, $selected, $parent_id=0 )
                    {
                        echo '<ul id="perm-'.$parent_id.'" class="perm">';

                        $maps = array('protected', 'public', 'hidden');

                        foreach( $rows as $perm_id => $perm )
                        {
                            if ( $perm['parent_id'] != $parent_id )
                                continue;

                            echo '<li class="lineborder">
                            <label for="id_check_'.$perm['perm_id'].'">
                            <input class="block checkthis" id="id_check_'.$perm['perm_id'].'" type="checkbox" name="selected[]" value="'.$perm['perm_id'].'" '.(isset($selected[$perm_id]) ? 'checked="checked"' : '').' />
                            <span class="block">'.h($perm['perm_name']?$perm['perm_name']:'empty').'</span>
                            <span class="block">'.h($perm['perm_path']?$perm['perm_path']:'empty').'</span>
                            <span class="block">'.h($maps[$perm['public']]).'</span>
                            </label>
                            <br class="clear" />
                            ';

                            print_tree( $rows, $selected, $perm['perm_id'] );
                            echo '</li>';

                        }
                        echo '</ul>';
                    }

                    ?>

                    <form method="POST" action="<?php echo site_url('groups/perms?id='.$group_id); ?>">
                    <?php print_tree($perms,$selected); ?>
                    <p>
                        <input type="submit" class="btn" value="Save" />
                        <a href="<?php echo site_url('groups'); ?>">Back to Groups</a>
                    </p>
                    </form>

                    <script type="text/javascript">
                    $(function() {
                        $(".checkthis").click(function() {
                            var checked = this.checked;
                            var li = $(this).parent().parent();
                            if (checked)
                                li.find('input.checkthis').attr( 'checked', true );
                            else
                                li.find('input.checkthis').removeAttr('checked');
                        });
                    });
                    </script>
                    
                    </div>

                </div>

                <div id="sidebar">
                    <?php $this->load->view('dashboard_menu'); ?>
                </div>

                <div class="cl"></div>


<?php $this->load->view('footer');
