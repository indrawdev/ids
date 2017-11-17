<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
	<link rel="stylesheet" href="<?php echo base_url();?>assets/js/themes/base/jquery.ui.all.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/themes/default/easyui.css">
	<!--link rel="stylesheet" type="text/css" href="<!?php echo base_url();?>assets/themes/icon.css"-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/aster.css">
	<script src="<?php echo base_url();?>assets/js/jquery-1.7.2.min.js"></script>
	<script src="<?php echo base_url();?>assets/js/jquery.easyui.min.js"></script>
	<!--script src="<!?php echo base_url();?>assets/js/jquery-ui-1.8.21.custom.min.js"></script-->
	<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.core.js"></script>
	<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.widget.js"></script>
	<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.button.js"></script>
	<script type="text/javascript">
	function fs_comp(nilai){
		$.ajax({
			type: "POST",
			url: "<?php echo site_url('login/change_comp');?>",
			data: "xcomp="+nilai,
			success: function(data){
				$("#dept").html(data);
			},
			error: function(XMLHttpRequest){
				alert(XMLHttpRequest.responseText);
			}
		})
	};
	
	$(function() {
		$( "input:submit, input:reset", ".tombol").button();
	});
	</script>
</head>

<body>
<center>

<div id="content" class="easyui-panel" style="width:550px;height:230px;padding:10px 30px;" title="Login">  	
	<font color="#FF0000"><?php echo $this->session->flashdata('pesan');?></font>
	<?php echo form_open('login/ceklogin');?>
	<table width="500">
	  <tr>
		<td width="80">Company</td>
		<td width="10">:</td>
		<td width="400">
			<?php 
				$js = 'onChange="fs_comp(this.value);"';
				echo form_dropdown('cbocomp', $comp, 'id="cbocomp"', $js);
			?>
		</td>
	  </tr>
	  <tr>
		<td>Departement</td>
		<td>:</td>
		<td id="dept">
			<?php
				$dept = array('- Select a Departement -');
				echo form_dropdown('cbodept', $dept, 'id="cbodept"');
			?>
		</td>
	  </tr>
	  <tr>
		<td>User Code </td>
		<td>:</td>
		<td><?php echo form_input('login_username', 'SASAGALA');?></td>
	  </tr>
	  <tr>
		<td>Password</td>
		<td>:</td>
		<td><?php echo form_password('login_userpass', 'JESUS IS MY SAVIOR');?></td>
	  </tr>
	  <tr>
	  	<td colspan="2">
		</td>
		<td>
		<div class="tombol">
			<input type="submit" value="Login" name="cmdlogin">
			<input type="reset" value="Reset" name="cmdReset">
			<?php echo form_close();?>
		</div>
		</td>
	  </tr>
  	</table>

	Copyright &copy; 2012 ASTER Team
</center>
</div>

</body>
</html>
