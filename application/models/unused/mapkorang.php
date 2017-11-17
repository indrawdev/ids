<?php

class MAPKOrang extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function cek_kode($sKode)
	{
		$xSQL = ("
			SELECT 	fs_refno
            FROM 	tx_apkheader (NOLOCK)
            WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_refno = '".trim($sKode)."'
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>