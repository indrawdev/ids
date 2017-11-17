<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/themes/default/easyui.css">
	<script src="<?php echo base_url();?>assets/js/jquery-1.7.2.min.js"></script>
	<script src="<?php echo base_url();?>assets/js/jquery.easyui.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/aster.css">
</head>

<body>
	<p><?php echo anchor('MainMenu','Back To Main Menu');?></p>
	<div class="easyui-tabs" fit="true" plain="true" style="height:500px;width:500px;">
		<div title="Entry Cash Bank In" style="padding:10px;">
		
		<table width="500">
		  <tr>
			<td width="80">Departement</td>
			<td width="10">:</td>
			<td>
				<?php
					$dept_def = array($this->session->userdata('gDeptName'));
					echo form_dropdown('cbodept', $dept_def);
				?>
			</td>
		  </tr>
		  <tr>
			<td>Transaksi Type</td>
			<td>:</td>
			<td>
				<?php
					echo form_dropdown('cbotrs', $opttrs);
				?>
			</td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		  </tr>
		</table>
			
		</div>
		
		<div title="Entry Cash Bank Out" style="padding:10px;">
			Content 2
		</div>
	</div>
	</div>
</body>
</html>
