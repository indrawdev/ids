<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
	<link rel="stylesheet" href="<?php echo base_url();?>assets/tree/jquery.treeview.css">
	<link rel="stylesheet" href="<?php echo base_url();?>assets/tree/screen.css">
	<script src="<?php echo base_url();?>assets/tree/lib/jquery.js" type="text/javascript"></script>
	<script src="<?php echo base_url();?>assets/tree/lib/jquery.cookie.js" type="text/javascript"></script>
	<script src="<?php echo base_url();?>assets/tree/jquery.treeview.js" type="text/javascript"></script>
	
	<script type="text/javascript">
			$(function() {
				$("#tree").treeview({
					collapsed: true,
					animated: "medium",
					control:"#sidetreecontrol",
					persist: "location"
				});
			})
	</script>
</head>

<body>

<h1 id="banner">A S T E R</h1>
<div id="main"><?php echo anchor('login/logout','Logout Aster');?>

<div id="sidetree">
<div class="treeheader">&nbsp;</div>
<div id="sidetreecontrol"><a href="?#">Collapse All</a> | <a href="?#">Expand All</a></div>

<ul id="tree">	
	<?php	
		foreach ($data_menu->result() as $row0)
		{
			if (trim($row0->fs_kd_child) == "")
			{
	?>
				<li><span><strong><?=$row0->fs_nm_menu;?></strong></span>
				<ul>
	<?php
				foreach ($data_menu->result() as $row1)
				{
					if (trim($row1->fs_kd_parent) == trim($row0->fs_kd_parent) and trim($row1->fs_kd_child) <> "")
					{
	?>
						<li>
	<?php
							if (trim($row1->fs_nm_formweb) <> "")
							{
								echo anchor(trim($row1->fs_nm_formweb),trim($row1->fs_nm_menu));
							}
							else
							{
	?>
								<?=trim($row1->fs_nm_menu);?>
	<?php
							}
	?>
						<ul>
	<?php
						foreach ($data_menu->result() as $row2)
						{
							if (trim($row2->fs_kd_parent) == trim($row1->fs_kd_child))
							{
	?>
								<li>
	<?php
								if (trim($row2->fs_nm_formweb) <> "")
								{
									echo anchor(trim($row2->fs_nm_formweb),trim($row2->fs_nm_menu));
								}
								else
								{
	?>
									<?=trim($row2->fs_nm_menu);?>
	<?php
								}
	?>
								<ul>
	<?php
								foreach ($data_menu->result() as $row3)
								{
									if (trim($row3->fs_kd_parent) == trim($row2->fs_kd_child))
									{
	?>
										<li>
	<?php
										if (trim($row3->fs_nm_formweb) <> "")
										{
											echo anchor(trim($row3->fs_nm_formweb),trim($row3->fs_nm_menu));
										}
										else
										{
	?>
											<?=trim($row3->fs_nm_menu);?>
	<?php
										}
	?>
										<ul>
	<?php
										foreach ($data_menu->result() as $row4)
										{
											if (trim($row4->fs_kd_parent) == trim($row3->fs_kd_child))
											{
	?>
												<li>
	<?php
												if (trim($row4->fs_nm_formweb) <> "")
												{
													echo anchor(trim($row4->fs_nm_formweb),trim($row3->fs_nm_menu));
												}
												else
												{
	?>
													<?=trim($row4->fs_nm_menu);?>
	<?php
												}
	?>
												</li>
	<?php
											}
										}
	?>
										</ul>
										</li>
	<?php
									}
								}
	?>
								</ul>
								</li>
	<?php
							}
						}
	?>
						</ul>
						</li>
	<?php
					}
				}
	?>
				</li>
				</ul>
	<?php
			}
		}
	?>
</ul>
</div>
</div>

</body>
</html>
