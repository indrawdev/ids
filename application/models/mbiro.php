<?php

class MBiro extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function cek_kode($sKode)
	{
		$xSQL = ("
			SELECT 	fs_kd_birojs
            FROM 	tm_masterbirojs (NOLOCK)
            WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_birojs = '".trim($sKode)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>