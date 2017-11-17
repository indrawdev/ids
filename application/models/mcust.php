<?php

class MCust extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function cek_idcard($sIdCard,$sNmCust)
	{
		$xSQL = ("
			SELECT	TOP 1  fs_code, fs_count, fs_addr, fs_nm_code
			FROM 	tm_addr (NOLOCK)
			WHERE 	REPLACE(REPLACE(fs_idcard, ' ', ''), '.', '') = '".trim($sIdCard)."'
				AND	fs_nm_code LIKE '%".trim($sNmCust)."%'
			ORDER BY fs_count DESC
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_nama($sNmCust,$sCustAddr)
	{
		$xSQL = ("
			SELECT	TOP 1 fs_code, fs_count, fs_addr
			FROM 	tm_addr (NOLOCK)
			WHERE 	REPLACE(REPLACE(fs_nm_code ,' ', ''), '.', '') = REPLACE(REPLACE('".trim($sNmCust)."', ' ', ''), '.', '')
				AND REPLACE(REPLACE(REPLACE(fs_addr, ' ', ''), '.', ''), '-', '') = REPLACE(REPLACE(REPLACE('".trim($sCustAddr)."', ' ', ''), '.', ''), '-', '')
			ORDER BY fs_count DESC
		   ");
		
		$sSQL = $this->db->query($xSQL);   
		return $sSQL;
	}
	
	function ambil_kode()
	{
		$kddept = trim($this->session->userdata('gDept'));
		$ldept = strlen($kddept);
		$ldept = $ldept - 2;
		$kdcount = trim($this->session->userdata('gCount'));
		$lcount = strlen($kdcount);
		$lcount = $lcount - 2;
		
		$xSQL = ("
			SELECT	TOP 1 fs_count = ISNULL(RIGHT('000000' + CONVERT(VARCHAR(6), CONVERT(INT, MAX(fs_count)) + 1), 6), 'KOSONG')
			FROM 	tm_addr (NOLOCK)
			WHERE 	fs_code = '".substr(trim($kddept), $ldept, 2).substr(trim($kdcount), $lcount, 2)."'
			ORDER BY fs_count DESC
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>