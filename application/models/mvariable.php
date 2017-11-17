<?php

class MVariable extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function cek_kode($sKey,$sVar)
	{
		$xSQL = ("
			SELECT 	fs_key, fs_kd_vareable
            FROM 	tm_vareable (NOLOCK)
            WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_key = '".trim($sKey)."'
				AND	fs_kd_vareable = '".trim($sVar)."'
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>