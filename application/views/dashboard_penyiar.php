<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

$company = false;
$client_id = $this->orca_auth->user->client_id;
$client_key = $this->orca_auth->user->confirm_hash;

if (!$client_id) 
{
	$r = $this->User_model->get_company($client_id);
	if ($r) $company = $r->name;
} 

if (!$company) $company = 'SIMETRI';

$page_title = 'List SMS Masuk';

$judul='SMS Masuk';
$width=800;
$sizefont=11;
$colorsms='#000000';
$colormobile='#FF0080';
$jml = 0;

$this->db->where('client_id', $client_id);
$query = $this->db->get('log_mo_widgets_options');

if ($query->num_rows()>0)
{
    $jml = $query->num_rows();
    $rs = $query->result_array();
    
    //echo print_r($rs);die;
    if (!empty($rs[0]['judul']))
        $judul = $rs[0]['judul'];
    if (!empty($rs[0]['height']))
        $height = $rs[0]['height'];
    if (!empty($rs[0]['sizefont']))
        $sizefont = $rs[0]['sizefont'];
    if (!empty($rs[0]['colorsms']))
        $colorsms = $rs[0]['colorsms'];
    if (!empty($rs[0]['colormobile']))
        $colormobile = $rs[0]['colormobile'];
}

$t = strtotime('+7 hour');
$jamsebelum = date('H:i', $t-86400);

if (!$tanggal)
    $tanggal = date('Y-m-d', $t);

if (!$jam)
    $jam = date('H:i', $t);
    
if (!$jamto)
    $jamto = date('H:i', $t);

$dateDisabled = json_encode($dofiltertgl ? false : true);
$timeDisabled = json_encode($dofilter ? false : true);
$base_url = json_encode( site_url( 'dashboard' ) );

$page_header =<<<EOT
<style type="text/css" media="all">
#widget-content { text-align:left; font-family:arial,helvetica; color:#333; }
.sender {color:$colormobile;}
.sms {color:$colorsms;}
</style>

<script type="text/javascript">

CHECKED = {};

function update_checked(value, add) {
    if (add && typeof value != 'undefined' && value) {
        CHECKED[value] = value;
    } else if ( typeof CHECKED[value] != 'undefined' ){
        delete CHECKED[value];
    }
    
    var count = 0;
    for (var k in CHECKED) {
       ++count;
    }
    
    Ext.getCmp('cekInfo').setValue( count + ' SMS terpilih' );
}

function reset_cek() {
    Ext.each(Ext.query("input.cek"), function(elem, index, array) {
        var el = Ext.get( "li"+elem.value );
        if (elem.checked) {
            el.removeCls('kepilih');
        }
        elem.checked = false;
        CHECKED = {}
    });
}

function init_cek() {
    Ext.each(Ext.query("input.cek"), function(elem, index, array) {
        elem.onclick = function() {
            var el = Ext.get( "li"+this.value );
            if (elem.checked) {
                el.addCls('kepilih');
            } else {
                Ext.getCmp('selectAll').setValue(false);
                el.removeCls('kepilih');
            }
            update_checked( this.value, this.checked );
        }
        
        update_checked( elem.value, elem.checked );
    });
    
    Ext.each(Ext.query(".pagination a"), function(elem, index, array) {
        if (window.console)
            console.log( elem );
        elem.onclick = function() {
            ext_ajax({
                url: this.href,
                method: 'POST',
                params: {isread:Ext.JSON.encode(CHECKED)},
                success: function(resp, opts) {
                    Ext.get('wg').update(resp.responseText);
                    init_cek();
                },
                failure: function(resp, opts) {
                    Ext.Msg.alert('Error', 'Pengambilan data error, coba beberapa saat lagi');
                }
            }, true);
            return false;
        };
    });
}

function cek_all() {
    var checked = Ext.getCmp('selectAll').getValue();
    Ext.each(Ext.query("input.cek"), function(elem, index, array) {
        elem.checked = checked;
        var el = Ext.get( "li"+elem.value );
        if (elem.checked) {
            el.addCls('kepilih');
        } else {
            el.removeCls('kepilih');
        }
        update_checked( elem.value, elem.checked );
    });
    
    if (!checked) {
        CHECKED = {}
        Ext.getCmp('cekInfo').setValue( '0 SMS terpilih' );
    }
}

LAST_ID = 0;
NEW_LAST_ID = 0;
SERVER_TIME = 0;

function reload() {
    var params = {}
    params['isread'] = Ext.JSON.encode(CHECKED)
    LAST_ID = NEW_LAST_ID;
    ext_ajax({
        url: $base_url + '/wgpenyiar',
        method: 'POST',
        params: params,
        success: function(resp, opts) {
            if (window.console)
                console.log( ['ajax success', resp, opts] );
            Ext.get('wg').update(resp.responseText);
            init_cek();
            Ext.get("wgcount").update( "" );
        },
        failure: function(resp, opts) {
            Ext.Msg.alert('Error', 'Pengambilan data error, coba beberapa saat lagi');
        }
    }, true);
}

function check_new_sms() {
    ext_ajax( {
        url: $base_url + '/wg_load_cnt/$client_id/-/' + LAST_ID,
        success: function(resp, opts) {
            var data = Ext.JSON.decode(resp.responseText);
            if (window.console)
                console.log(['check', data]);
            
            if ( LAST_ID == 0 ) {
                LAST_ID = data.last_id;
                SERVER_TIME = data.server_time;
            } else {
                data.count = parseInt(data.count);
                NEW_LAST_ID = data.last_id;
                SERVER_TIME = data.server_time;
                if (data.count) {
                    Ext.get("wgcount").update( "<h3><strong>"+data.count+"</strong> Pesan Baru. <a href='"+$base_url+"/wgpenyiar' onclick='reload(); return false;'>Klik Untuk Refresh</a>" );
                }
            }
            next_check();
        },
        failure: function(resp, opts) {
            next_check();
        }
    } );
}

function next_check() {
    setTimeout( check_new_sms, 1000*20 );
}

Ext.onReady(function() {
    Ext.create('Ext.container.Container', {
        renderTo: Ext.get('toolbarSMS'),
        width: '100%',
        items: [
            {
                xtype: 'toolbar',
                width: '100%',
                margin: '0 0 2 0',
                items: [
                    {
                        xtype:'checkbox',
                        boxLabel: 'Pilih tanggal',
                        id: 'checkTanggal',
                        margin:'0 5 0 5',
                        value:1,
                        checked: !$dateDisabled,
                        listeners: {
                            change: function(chk, value, oldvalue, eOpts) {
                                Ext.getCmp('filterTgl').setDisabled(!value);
                            }
                        }
                    },
                    {
                        disabled: $dateDisabled,
                        xtype:'datefield',
                        id: 'filterTgl',
                        format: 'd/m/Y',
                        width: 100,
                        value: new Date("$tanggal")  // limited to the current date or prior
                    },
                    '-',
                    {
                        xtype:'checkbox',
                        boxLabel: 'Jam',
                        margin:'0 5 0 0',
                        id: 'checkJam',
                        value: 1,
                        checked: !$timeDisabled,
                        listeners: {
                            change: function(chk, value, oldvalue, eOpts) {
                                Ext.getCmp('filterJam').setDisabled(!value);
                                Ext.getCmp('filterJamNext').setDisabled(!value);
                            }
                        }
                    },
                    {
                        xtype: 'timefield',
                        disabled: $timeDisabled,
                        format: 'H:i',
                        increment: 15,
                        id: 'filterJam',
                        width: 60,
                        value: new Date("$tanggal $jamsebelum")
                    },
                    {
                        xtype: 'timefield',
                        disabled: $timeDisabled,
                        format: 'H:i',
                        increment: 15,
                        id: 'filterJamNext',
                        fieldLabel: 's/d',
                        labelWidth: 30,
                        width: 100,
                        value: new Date("$tanggal $jamto")
                    },
                    '-',
                    {
                        text: 'Tampilkan Data',
                        cls: 'btn btn.small',
                        handler: function(){
                            var params = {}
                            if ( Ext.getCmp('checkTanggal').getValue() ) {
                                params['dofiltertgl'] = true;
                                params['tanggal'] = Ext.util.Format.date( Ext.getCmp('filterTgl').getValue(), 'Y-m-d' );
                            }
                        
                            if ( Ext.getCmp('checkJam').getValue() ) {
                                params['dofilter'] = true;
                                params['jam'] = Ext.util.Format.date( Ext.getCmp('filterJam').getValue(), 'H:i:00' );
                                params['jamto'] = Ext.util.Format.date( Ext.getCmp('filterJamNext').getValue(), 'H:i:59' );
                            }
                            
                            params['isread'] = Ext.JSON.encode(CHECKED)
                            
                            ext_ajax({
                                url: $base_url + '/wgpenyiar',
                                method: 'POST',
                                params: params,
                                success: function(resp, opts) {
                                    if (window.console)
                                        console.log( ['ajax success', resp, opts] );
                                    Ext.get('wg').update(resp.responseText);
                                    init_cek();
                                },
                                failure: function(resp, opts) {
                                    Ext.Msg.alert('Error', 'Pengambilan data error, coba beberapa saat lagi');
                                }
                            }, true);
                        }
                    }
                ]
            },
            {
                xtype: 'toolbar',
                width: '100%',
                items: [
                    {
                        xtype: 'displayfield',
                        id: 'cekInfo',
                        value: CHECKED.length + ' sms terpilih',
                        width: 150
                    },
                    '->',
                    {
                        xtype:'checkbox',
                        boxLabel: 'Semua',
                        id: 'selectAll',
                        margin:'0 5 0 0',
                        listeners: {
                            change: function(chk, value, oldvalue, eOpts) {
                                cek_all();
                            }
                        }
                    },
                    {
                        xtype: 'combo',
                        id: 'selaction',
                        forceSelection: true,
                        queryMode: 'local',
                        store: [
                            ['Mark as read','Mark as read'],
                            ['Mark as unread','Mark as unread'],
                            ['Delete','Delete']
                        ],
                        value: 'Mark as read'
                    },'-',
                    {
                        text: 'Update!',
                        cls: 'btn btn.small',
                        handler: function(){
                            var params = {}
                            if ( Ext.getCmp('checkTanggal').getValue() ) {
                                params['dofiltertgl'] = true;
                                params['tanggal'] = Ext.util.Format.date( Ext.getCmp('filterTgl').getValue(), 'Y-m-d' );
                            }
                        
                            if ( Ext.getCmp('checkJam').getValue() ) {
                                params['dofilter'] = true;
                                params['jam'] = Ext.util.Format.date( Ext.getCmp('filterJam').getValue(), 'H:i:00' );
                                params['jamto'] = Ext.util.Format.date( Ext.getCmp('filterJamNext').getValue(), 'H:i:59' );
                            }
                            
                            params['isread'] = Ext.JSON.encode(CHECKED)
                            params['do'] = true;
                            params['selaction'] = Ext.getCmp('selaction').getValue();
                            
                            ext_ajax({
                                url: $base_url + '/wgpenyiar',
                                method: 'POST',
                                params: params,
                                success: function(resp, opts) {
                                    if (window.console)
                                        console.log( ['ajax success', resp, opts] );
                                    Ext.get('wg').update(resp.responseText);
                                    init_cek();
                                    reset_cek();
                                },
                                failure: function(resp, opts) {
                                    Ext.Msg.alert('Error', 'Pengambilan data error, coba beberapa saat lagi');
                                }
                            }, true);
                        }
                    }
                ]
            }
        ]
    });
    
    
    init_cek();
    check_new_sms();

});

</script>

EOT;

$query = $this->db->query("SELECT mobile FROM clients WHERE client_id = ? LIMIT 1", array($client_id));
$resgw = $query->result_array();
$mob_gw = isset($resgw[0]['mobile']) ? $resgw[0]['mobile'] : '';

$str = "";
if (!empty($mob_gw))
{
	$str = " dari $mob_gw";
}

$page_title .= $str;

$this->load->view('header', array('page_title' => $page_title, 'page_header' => $page_header));

?>
<div class="card">
    <h2 class="judul"><?php echo $page_title; ?></h2>
    <div id="widget-content">
        <div id="toolbarSMS" style="width:100%;"></div>
        <div id="wgcount"></div>
        <div id="wg">
            <?php include 'wgview.php'; ?>
        </div> <!-- #wg -->
    </div>
</div>

<form id="history-form" class="x-hide-display">
    <input type="hidden" id="x-history-field" />
    <iframe id="x-history-frame"></iframe>
</form>

<?php $this->load->view('footer');
