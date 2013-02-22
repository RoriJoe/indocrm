            <?php if (!$result): ?>
                <p>Maaf, data SMS kosong</p>
            <?php else: ?>
                <div class="pagination">
                    <div class="total" style="color:#000; font-weight:bold; ">Total: <?php echo $paging->numrows; ?></div>
                    <div class="thepage">
                        <?php if ( $paging->prev > 0 ) echo '<a title="Previous page" href="'.$paging->page_url( $paging->prev ).'">&lsaquo; Prev</a>'; ?>
                        <?php foreach( $paging->pages as $page ):
                            if ( $page == $paging->page )
                                echo ' <span class="page">'.$page.'</span> ';
                            else if ( $page === '' )
                                echo ' <span class="spacer">...</span> ';
                            else
                                echo '<a title="page '.$page.'" href="' . $paging->page_url( $page ) . '">'.$page.'</a>';
                        endforeach; ?>
                        <?php if ( $paging->next > 0 ) echo '<a title="Next page" href="'.$paging->page_url( $paging->next ).'">Next &rsaquo;</a>'; ?>
                        <div class="cl"></div>
                    </div>
                    <div class="cl"></div>
                </div>
                
                <ul id="list-content">
                <?php
                
                if (!isset($isread) || !is_array($isread)) 
                    $isread = array();
                $arrSelected = array_flip($isread);
                
                foreach($result as $i => $row)
                {
                    $mobile = $row['msisdn'];
                    
                    if (isset($arrSelected[$row['id']]))
                    {
                        $checked = "checked='checked'";
                        $class="kepilih";
                        if ($row['is_read'] == '1') 
                        { 
                            $classx = "kebaca";
                        } 
                        else 
                        {
                            $classx = "belumkebaca";
                        }
                    } 
                    else
                    {
                        $checked = "";
                        if ($row['is_read'] == '1') 
                        { 
                            $class = "kebaca";
                            $classx = "kebaca";
                        } 
                        else 
                        { 
                            $class = "belumkebaca"; 
                            $classx = "belumkebaca"; 
                        }
                    }
                    
                    if ($i & 1) 
                        $class .= " alt";
                    
                    $ln = strlen($mobile);
                    if (ctype_digit($mobile))
                    {
                        $mobile = substr($mobile,0,$ln-3)."XXX";
                    }
                    
                    $t = strtotime("$row[date] $row[time]");
                    //$time_since = time_since2( $row['timestamp'] );
                    
                    echo '<li id="li'.$row['id'].'" class="'.$class.'">
                        <div class="sender">'.htmlspecialchars($mobile).'</div>
                        <div class="date">'.date('l, d/m/Y H:i', $t).'</div>
                        <div class="sms">'.nl2br(htmlspecialchars($row['sms'])).'</div>
                        <div class="cmd"><input type="checkbox" class="cek" name="isread[]" value="'.$row['id'].'" '.$checked.' /></div>
                    </li>';
                    
                }

                ?>
                </ul>
                
                <div class="pagination">
                    <div class="total" style="color:#000; font-weight:bold; ">Total: <?php echo $paging->numrows; ?></div>
                    <div class="thepage">
                        <?php if ( $paging->prev > 0 ) echo '<a title="Previous page" href="'.$paging->page_url( $paging->prev ).'">&lsaquo; Prev</a>'; ?>
                        <?php foreach( $paging->pages as $page ):
                            if ( $page == $paging->page )
                                echo ' <span class="page">'.$page.'</span> ';
                            else if ( $page === '' )
                                echo ' <span class="spacer">...</span> ';
                            else
                                echo '<a title="page '.$page.'" href="' . $paging->page_url( $page ) . '">'.$page.'</a>';
                        endforeach; ?>
                        <?php if ( $paging->next > 0 ) echo '<a title="Next page" href="'.$paging->page_url( $paging->next ).'">Next &rsaquo;</a>'; ?>
                        <div class="cl"></div>
                    </div>
                    <div class="cl"></div>
                </div>
            <?php endif; ?>
