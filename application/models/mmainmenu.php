<?php

class MMainMenu extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function load_menu()
	{
		$usernm = $this->session->userdata('gUser');
		$pass = $this->session->userdata('gPass');
		
		if ($usernm == 'MFI' and $pass == 'AMGGROUP')
		{
			$sSQL = $this->db->query("EXEC stp_menu 'MENU',' '");
		}
		else
		{
			$sSQL = $this->db->query("EXEC stp_menu 'MENU','where usercd = ''".$usernm."'' '");// and pwd = ''".$pass."''
		}
		return $sSQL;
	}
}

?>