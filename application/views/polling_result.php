<?php
$page_header = '
 <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">';

$judul = '';
$text1 = '';$pilihan1 = 0;
$text2 = '';$pilihan2 = 0;
$text3 = '';$pilihan3 = 0;
$text4 = '';$pilihan4 = 0;
$text5 = '';$pilihan5 = 0;
$array = array();

if ($result){
	foreach($result as $row){
		$judul = $row->judul_polling;
		$text1 = $row->text1; $pilihan1 = $row->pilihan1;
		$text2 = $row->text2; $pilihan2 = $row->pilihan2;
		$text3 = $row->text3; $pilihan3 = $row->pilihan3;
		$text4 = $row->text4; $pilihan4 = $row->pilihan4;
		$text5 = $row->text5; $pilihan5 = $row->pilihan5;
		$array[$text1] = $pilihan1;
		$array[$text2] = $pilihan2;
		if (!empty($text3)) $array[$text3] = $pilihan3;
		if (!empty($text4)) $array[$text4] = $pilihan4;
		if (!empty($text5)) $array[$text5] = $pilihan5;
	}
}

$page_header .= '
      google.load(\'visualization\', \'1.0\', {\'packages\':[\'corechart\']});
      
      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);


      // Callback that creates and populates a data table, 
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {

      // Create the data table.
      var data = new google.visualization.DataTable();
      
      data.addColumn(\'string\', \'Topping\');
      data.addColumn(\'number\', \'Slices\');
      data.addRows([';
	foreach($array as $key => $val){
		$page_header .= '[\''.$key.'\', '.$val.'],';
	}
$page_header .='
      ]);

      // Set chart options
      var options = {\'title\':\''.$judul.'\',
                     \'width\':600,
                     \'height\':400};

      // Instantiate and draw our chart, passing in some options.
      var chart = new google.visualization.PieChart(document.getElementById(\'chart_div\'));
      chart.draw(data, options);
    }
    </script>';
$page_title = 'POLLING RESULT';
$this->load->view('header', array('page_title' => $page_title, 'page_header' => $page_header));
?>
	<div id="content">
		<h1><?php echo $page_title; ?></h1>
		<div class="dialog">
			<p>Hasil Polling </p>
			<p style="color:red;font-weight:bold;"><?php echo $err; ?></p>
		</div>
		<?php if (count($array) > 0){ ?>
		<div id="chart_div" style="width:400; height:300"></div>
		<?php } else { ?>
		 <p>Belum ada data</p>
		<?php } ?>
		<?php echo anchor('polling', 'back to Polling'); ?>
	</div>
	<div id="sidebar">
		<!-- menu admin -->
		<?php $this->load->view('dashboard_menu'); ?>
	</div>

	<div class="cl"></div>
<?php $this->load->view('footer');
