<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        $this->load->view('welcome_message');
    }
    
    /*
    public function path()
    {
        echo FCPATH;
    }

    function template()
    {
        $s = '<table style="width: 100%;" border="0" cellspacing="0" cellpadding="0">
            <tbody><tr><td style="background-color: #ababab;" align="center" valign="top" bgcolor="#ababab"><br /> <br />
            <table style="width: 600px;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="background-color: #ffffff;" align="left" valign="top" bgcolor="#ffffff"><table style="margin-bottom: 15px; width: 570px;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding: 10px;" align="left" valign="middle">
            <img style="display: block;" title="SIMETRI_LOGO_21.png" src="http://localhost/ferdhie/crm/u/SIMETRI_LOGO_21.png" alt="SIMETRI_LOGO_21.png" width="300" height="97" /></td></tr></tbody></table><table style="margin-bottom: 15px; width: 570px;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr>
            <td style="font-family: Arial, Helvetica, sans-serif; color: #4e4e4e; font-size: 13px; padding-right: 10px;" align="left" valign="middle" width="360"><div style="font-size: 24px;">{%CAMPAIGN_TITLE%}.</div>lorem eu luctus cursus, sapien justo auctor ligula, id bibendum lorem leo quis leo. Suspendisse sit amet aliquam orci. Aliquam erat volutpat. Aliquam erat volutpat. Nunc ac justo enim. Morbi eleifend feugiat turpis non placerat. Etiam</td><td align="right" valign="middle"><table style="width: 210px;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="background-color: #f73f9b;" align="center" valign="top" bgcolor="#f73f9b"><table style="width: 184px;" border="0" cellspacing="0" cellpadding="4"><tbody><tr><td style="font-family: Arial, Helvetica, sans-serif; font-size: 20px; color: #ffffff;" align="left" valign="top"><strong>Quick Links</strong></td></tr><tr><td style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #ffffff;" align="left" valign="top"><a style="color: #ffffff; text-decoration: none;" href="#">About Our Company</a><br /> <a style="color: #ffffff; text-decoration: none;" href="#">Products &amp; Services</a><br /> <a style="color: #ffffff; text-decoration: none;" href="#">News Room</a><br /> <a style="color: #ffffff; text-decoration: none;" href="#">Online Catelogue</a><br /> <a style="color: #ffffff; text-decoration: none;" href="#">Contact Details</a></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table style="margin-bottom: 15px; width: 570px;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td align="left" valign="middle"><img style="display: block;" src="images/top.png" alt="" width="570" height="16" /></td></tr><tr><td style="padding: 0px 20px 0px 20px; font-family: Arial, Helvetica, sans-serif; background-color: #1ba5db; color: #ffffff;" align="left" valign="middle" bgcolor="#1ba5db"><div style="font-size: 20px;">Lorem ipsum Dollar tempor venenatis eros.</div><div style="font-size: 13px;">Lorem ipsum dolor sit amet, consectetur tempor venenatis eros. Aliquam sed velit vitae nibh pulvinar iaculis. Aenean hendrerit, lorem eu luctus cursus, sapien justo auctor ligula, id bibendum lorem leo quis leo. Suspendisse sit amet aliquam orci. Aliquam erat volutpat. Aliquam erat volutpat. Nunc ac justo enim. Morbi eleifend feugiat turpis non placerat. Etiam sed tellus ac lectus lacinia molestie nec eu nisl. Pellentesque mattis luctus ultrices. Suspendisse pretium feugiat ipsum nec dapibus. Aenean bibendum vestibulum scelerisque.</div></td></tr></tbody></table><table style="margin-bottom: 15px; width: 570px;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding: 0px 20px 0px 20px; font-family: Arial, Helvetica, sans-serif; background-color: #1ba5db; color: #ffffff;" align="left" valign="middle" bgcolor="#1ba5db"><div style="font-size: 20px;">Lorem ipsum Dollar tempor venenatis eros.</div><div style="font-size: 13px;">Lorem ipsum dolor sit amet, consectetur tempor venenatis eros. Aliquam sed velit vitae nibh pulvinar iaculis. Aenean hendrerit, lorem eu luctus cursus, sapien justo auctor ligula, id bibendum lorem leo quis leo. Suspendisse sit amet aliquam orci. Aliquam erat volutpat. Aliquam erat volutpat. Nunc ac justo enim. Morbi eleifend feugiat turpis non placerat. Etiam sed tellus ac lectus lacinia molestie nec eu nisl. Pellentesque mattis luctus ultrices. Suspendisse pretium feugiat ipsum nec dapibus. Aenean bibendum vestibulum scelerisque.</div></td></tr></tbody></table><table style="margin-bottom: 15px; width: 570px;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding: 0px 20px 0px 20px; font-family: Arial, Helvetica, sans-serif; background-color: #1ba5db; color: #ffffff;" align="left" valign="middle" bgcolor="#1ba5db"><div style="font-size: 20px;">Lorem ipsum Dollar tempor venenatis eros. Aliquam sed velit vitae nibh pulvinar iaculis.</div><div style="font-size: 13px;">lorem eu luctus cursus, sapien justo auctor ligula, id bibendum lorem leo quis leo. Suspendisse sit amet aliquam orci. Aliquam erat volutpat. Aliquam erat volutpat. Nunc ac justo enim. Morbi eleifend feugiat turpis non placerat. Etiam sed tellus ac lectus lacinia molestie nec eu nisl.</div></td></tr></tbody></table><table style="margin-bottom: 20px; width: 95%;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding: 10px;" align="left" valign="middle" width="50%"><table style="width: 75%;" border="0" cellspacing="0" cellpadding="4"><tbody><tr><td style="font-family: Verdana, Geneva, sans-serif; font-size: 14px; color: #000000;" align="left" valign="top"><strong>Follow Us On</strong></td></tr><tr><td style="font-family: Verdana, Geneva, sans-serif; font-size: 12px; color: #000000;" align="left" valign="top"><table style="width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td align="left" valign="middle" width="33%"><img title="tweet48.png" src="http://localhost/ferdhie/crm/u/tweet48.png" alt="tweet48.png" width="64" height="64" /></td><td align="left" valign="middle" width="34%"><img title="in481.png" src="http://localhost/ferdhie/crm/u/in481.png" alt="in481.png" width="64" height="64" /></td><td align="left" valign="middle" width="33%"><img title="face481.png" src="http://localhost/ferdhie/crm/u/face481.png" alt="face481.png" width="64" height="64" /></td></tr></tbody></table></td></tr></tbody></table></td><td style="color: #595959; font-size: 11px; font-family: Arial, Helvetica, sans-serif; padding: 10px;" align="left" valign="middle" width="50%"><strong>Company Address</strong><br /> Company URL: <a style="color: #595959; text-decoration: none;" href="http://www.yourcompanyname.com" target="_blank">http://www.yourcompanyname.com</a><br /> <br /> <strong>Hours:</strong> Mon-Fri 9:30-5:30, Sat. 9:30-3:00, Sun. Closed <br /> <strong>Customer Support:</strong> <a style="color: #595959; text-decoration: none;" href="mailto:name@yourcompanyname.com">name@yourcompanyname.com</a></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table>';
        
        $campaign = $this->db->get_where('campaign', array('campaign_id' => 45), 1)->row();
        $cl = $this->db->get_where('clients', array('client_id' => 1), 1)->row();
        $customer = new stdClass();
        $customer->first_name = $cl->name;
        $customer->last_name = '';
        $customer->email = 'test@gppg;';
        $customer->phone = $cl->phone;
        $customer->website = $cl->website;
        $customer->mobile = $cl->phone;
        
        $parser = new template_parser();
        $context = $parser->make_context($customer, $campaign, $cl);
        echo '<pre>',$parser->parse(html2plain($campaign->template), $context);
    }
    
    function uniquenumber()
    {
        $max = $this->input->get_post('i');
        $i = 1;
        for($x = 0; $x< $max; $x++)
        {
            $i = ((16807 * $i) % 2147483647);
            //echo "$i<br />\n";
            //$s = substr(base64_encode(pack("l", $i)), 0, 6);
            $s = substr(base64_encode(pack("l", $i)), 0, 6);
            echo "$s<br />\n";
        }
    }
    
    public function createuser()
    {
        $username = $this->input->get('username');
        $password = $this->input->get('password');
        echo $password . '|' . $this->orca_auth->make_hash( $password, '', TRUE );
    }
    
    private function _select_type($name,$options,$value='')
    {
        $str = '<select name="'.$name.'">';
        foreach($options as $opt)
        {
            $str .= '<option value="'.$opt.'"'.($opt == $value ? ' selected="selected"':'').'>'.$opt.'</option>';
        }
        $str .= '</select>';
        return $str;
    }
    
    private function _checkbox( $name, $label, $value, $id )
    {
        return '<label for="'.$id.'"><input type="checkbox" id="'.$id.'" name="'.$name.'" value="1" '.($value?'checked="checked"':'').'/>&nbsp;'.$label.'</label>';
    }
    
    public function formview()
    {
        $table = $this->input->get('table');
        if (!$table) $table = 'stores';
        
        $form_type = array('', 'passwordfield', 'datefield', 'radiofield', 'checkboxfield', 'combobox', 'hiddenfield', 'timefield', 'textareafield', 'numberfield');

        $query = $this->db->query("DESCRIBE $table");
        $rows = $query->result();
        
        echo '<form method="POST" action="'.current_url().'?table='.rawurlencode($table).'">
        <table>
        <tr>
            <th>Name</th>
            <th>Label</th>
            <th>Type</th>
            <th>Parameters</th>
        </tr>
        ';

        $names = isset($_POST['names']) ? $_POST['names'] : array();
        $labels = isset($_POST['labels']) ? $_POST['labels'] : array();
        $types = isset($_POST['types']) ? $_POST['types'] : array();
        $params = isset($_POST['params']) ? $_POST['params'] : array();

        foreach( $rows as $row )
        {
            $type = isset($types[$row->Field]) && in_array($types[$row->Field], $form_type) ? $types[$row->Field] : '';
            $param = isset($params[$row->Field]) ? $params[$row->Field] : '';
            $help = '';
            if ( $type == 'datefield' && !$param)
            {
                $help = 'format|value|maxValue';
                $param = 'Y-m-d|new Date()|';
            }
            else if ( $type == 'textfield' )
            {
                $help = 'minLength|maxLength';
                $param = '';
            }
            else if ( $type == 'numberfield' )
            {
                $help = 'minValue|maxValue|value';
                $param = '';
            }
            else if ( $type == 'textareafield' )
            {
                $help = 'minLength|maxLength|grow';
                $param = '';
            }

            echo '
            <tr>
                <td><input type="text" name="names['.$row->Field.']" value="'.htmlentities(isset($names[$row->Field])?$names[$row->Field]:'').'" size="20" /></td>
                <td><input type="text" name="labels['.$row->Field.']" value="'.htmlentities(isset($labels[$row->Field])?$labels[$row->Field]:'').'" size="20" /></td>
                <td>'.$this->_select_type( 'types['.$row->Field.']', $form_type, (isset($types[$row->Field])?$types[$row->Field]:'') ).'</td>
                <td>'.$help.'<br /><input type="text" name="params['.$row->Field.']" value="'.htmlentities($param).'" size="20" /></td>
            </tr>';
        }
        
        echo '
        </table>
        <p><input type="submit" value="Generate" /></p>
        </form>';
    }
    
    public function gridview()
    {
        $table = $this->input->get('table');
        if (!$table) $table = 'stores';

        $query = $this->db->query("DESCRIBE $table");
        $rows = $query->result();
        $extfields = array();
        $extheaders = array();
        $options = array('', 'text' => 'booleancolumn', 'format' => 'datecolumn', 'format' => 'numbercolumn', 'tpl' => 'templatecolumn');
        $headerFields = array('sortable', 'hideable', 'draggable', 'groupable', 'hidden', 'filter');
        
        $names = isset($_POST['names']) ? $_POST['names'] : array();
        $widths = isset($_POST['widths']) ? $_POST['widths'] : array();
        $types = isset($_POST['types']) ? $_POST['types'] : array();
        $params = isset($_POST['params']) ? $_POST['params'] : array();

        foreach($headerFields as $hf)
        {
            $$hf = isset($_POST[$hf]) ? $_POST[$hf] : array();
        }
        
        $columns = array();
        $column_names = array();
        
        echo '<form method="POST" action="'.current_url().'?table='.rawurlencode($table).'">
        <table>
            <tr>
                <th>DataIndex</th>
                <th>Label</th>
                <th>Width</th>
                <th>Type</th>
                <th>Parameters</th>
            </tr>';
            
        foreach($rows as $row)
        {
            $column_names[] = $row->Field;

            $checkboxes = '';
            foreach($headerFields as $hf)
            {
                $array = $$hf;
                $checkboxes.= $this->_checkbox( $hf.'['.$row->Field.']', $hf, isset($array[$row->Field]) && $array[$row->Field] ? $array[$row->Field] : FALSE , 'id_'.$row->Field.'_'.$hf  ) . ' ';
            }

            echo '
            <tr>
                <td>'.$row->Field.'</td>
                <td><input type="text" name="names['.$row->Field.']" value="'.htmlentities(isset($names[$row->Field])?$names[$row->Field]:'').'" size="20" /></td>
                <td><input type="text" name="widths['.$row->Field.']" value="'.htmlentities(isset($widths[$row->Field])?$widths[$row->Field]:'').'" size="3" /></td>
                <td>'.$this->_select_type( 'types['.$row->Field.']', $options, (isset($types[$row->Field])?$types[$row->Field]:'') ).'</td>
                <td>
                    <label for="id_param_'.$row->Field.'">Format/Tpl/Text</label>
                    <input type="text" id="id_param_'.$row->Field.'" name="params['.$row->Field.']" value="'.htmlentities(isset($params[$row->Field])?$params[$row->Field]:'').'" size="10" /><br />
                    '.$checkboxes.'
                </td>
            </tr>';
            
            $field = array( 'name' => $row->Field );
            $column = array('text' => isset($names[$row->Field]) ? $names[$row->Field] : $row->Field,
                             'dataIndex' => $row->Field);
            
            if ( isset($widths[$row->Field]) && is_numeric($widths[$row->Field]) )
            {
                $column['width'] = intval($widths[$row->Field]);
            }
            
            if (isset($types[$row->Field]) && $types[$row->Field])
            {
                $type = $types[$row->Field];
                if ( $type == 'booleancolumn' )
                {
                    $param = isset($params[$row->Field]) ? explode('|',$params[$row->Field]) : array();
                    $column['xtype'] = $type;
                    $column['trueText'] = isset($param[0]) ? $param[0] : 'TRUE';
                    $column['falseText'] = isset($param[1]) ? $param[1] : 'FALSE';
                }
                else if ( $type == 'datecolumn' )
                {
                    $column['xtype'] = $type;
                    if (isset($params[$row->Field]))
                    {
                        $column['format'] = $params[$row->Field] ;
                    }
                }
                else if ( $type == 'numbercolumn' )
                {
                    $column['xtype'] = $type;
                    if (isset($params[$row->Field]))
                    {
                        $column['format'] = $params[$row->Field] ;
                    }
                }
                else if ( $type == 'templatecolumn' )
                {
                    $column['xtype'] = $type;
                    if (isset($params[$row->Field]))
                    {
                        $column['tpl'] = $params[$row->Field] ;
                    }
                }
            }
            
            foreach($headerFields as $hf)
            {
                $array = $$hf;
                $default = $hf == 'sortable' || $hf == 'hideable' || $hf == 'draggable' || $hf == 'filter' ? TRUE : FALSE;
                $column[$hf] = isset($array[$row->Field]) && $array[$row->Field] ? TRUE : $default;
            }
            
            if ( preg_match('~^(int|integer|bigint|mediumint|money)~i', $row->Type) )
            {
                $field['type'] = 'int';
            }
            else if ( preg_match('~^(tinyint\(1\)|boolean|bool)~i', $row->Type) )
            {
                $field['type'] = 'boolean';
            }
            else if ( preg_match('~^(decimal|float|double|numeric)~i', $row->Type) )
            {
                $field['type'] = 'float';
            }
            else if ( preg_match('~^(char|varchar|binary|varbinary|blob)~i', $row->Type) )
            {
                $field['type'] = 'string';
            }
            else if ( preg_match('~^(date)~i', $row->Type) )
            {
                $field['type'] = 'date';
                $field['dateFormat'] = 'Y-m-d';
            }
            else if ( preg_match('~^(datetime|timestamp|time)~i', $row->Type) )
            {
                $field['type'] = 'date';
                $field['dateFormat'] = 'Y-m-d H:i:s';
            }
            
            $extfields[] = $field;
            $columns[] = $column;
        }
        
        echo '
        </table>
        <p><input type="submit" value="Generate" /></p>
        </form>';
        
        $result = array('fields' => $extfields, 'columns' => $columns);
        
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
        {
            echo '<pre>',json_encode($result ),'</pre>';
        }
        
        echo '<pre>',var_export($column_names, true),'</pre>';
    }
    */
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */