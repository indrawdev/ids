<?php

class MKasir extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function cek_refno($sTrx,$sRefno)
	{
		$sSQL = $this->db->query("
			SELECT	TOP 1 fs_refno
			FROM 	tx_poskasir (NOLOCK)
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND fs_count = '".trim($this->session->userdata('gCount'))."'
				AND fs_trx = '".trim($sTrx)."'
				AND fs_refno = '".trim($sRefno)."'
		   ");
		return $sSQL;
	}
	
	function cek_acno()
	{
		$sSQL = $this->db->query("
			SELECT	a.fs_kd_vareable, b.fs_nm_vareable, a.fs_kd_acno,
					fs_nm_acno = ISNULL((
						SELECT	TOP 1 fs_nm_acno
						FROM 	tm_acno h (NOLOCK)
						WHERE 	h.fs_kd_acno = a.fs_kd_acno
						), '')
			FROM	tm_acnopmtyp a (NOLOCK)
			INNER JOIN tm_vareable b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_key = b.fs_key
				AND a.fs_kd_vareable = b.fs_kd_vareable
			WHERE 	b.fs_key = '17'
				AND a.fs_kd_vareable = 'CASH'
			");
		return $sSQL;
	}
	
	function kasir($sRefno)
	{
		$xSQL = ("
			SELECT	fs_refno, [fd_refno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, fd_refno, 105), 105),
					fn_pyamt fn_total, fs_descrp,
					fs_cust = CASE WHEN (SELECT CHARINDEX('\', fs_descrp, 1)) > 0 THEN SUBSTRING(fs_descrp, 1, CHARINDEX('\', fs_descrp, 0) - 1) ELSE '' END,
					fs_note = CASE WHEN (SELECT CHARINDEX('\', fs_descrp, 1)) > 0 THEN SUBSTRING(fs_descrp, CHARINDEX('\', fs_descrp, 0) + 1, LEN(fs_descrp) - 1) ELSE '' END
			FROM 	tx_poskasir
			");
			
		if (trim($sRefno) <> '')
		{
			$xSQL = $xSQL.("
				WHERE (fs_refno LIKE '%".trim($sRefno)."%')
				");
		}
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>