<link rel="stylesheet" type="text/css" href="<?php echo config_item('base_tema');?>">
<link rel="stylesheet" type="text/css" href="<?php echo config_item('base_img');?>Grid.css">
<link rel="stylesheet" type="text/css" href="<?php echo config_item('base_img');?>MainMenu.css">
<script type="text/javascript" src="<?php echo config_item('base_ext');?>ext-all.js"></script>
<script type="text/javascript">
	var vDeptCd = '<?php echo $this->session->userdata('gDept').$this->session->userdata('gCount')?>';
	var vDept = '<?php echo $this->session->userdata('gDeptName')?>';
</script>
<script type="text/javascript" src="<?php echo config_item('base_js');?>CashBank.js?cb=<?php echo time()?>"></script>