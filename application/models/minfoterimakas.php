<?php

class MInfoTerimaKas extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function terima_kas($sTrx,$sStrx,$sTgl,$sTgl2)
	{
		$xSQL = ("
			SELECT	a.fs_kd_dept, a.fs_count, ISNULL(d.fs_nm_code, '') fs_nm_dept,
					ISNULL(e.fs_nm_trx, '') fs_nm_trx, a.fs_refno, CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105) fd_refno,
					REPLACE(REPLACE(a.fs_descrp, CHAR(13), ' '), CHAR(10), ' ') fs_ket, a.fn_grsamt fn_total
			FROM	tx_actheader a (NOLOCK)
			INNER JOIN tx_cbheader b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_dept = b.fs_kd_dept
				AND	a.fs_count = b.fs_count
				AND	a.fs_kd_trx = b.fs_kd_trx
				AND	a.fs_kd_strx = b.fs_kd_strx
				AND	a.fs_refno = b.fs_refno
				AND	b.fb_delete = '0'
			INNER JOIN tx_cbdetail c (NOLOCK) ON b.fs_kd_comp = c.fs_kd_comp
				AND	b.fs_kd_dept = c.fs_kd_dept
				AND	b.fs_count = c.fs_count
				AND	b.fs_kd_trx = c.fs_kd_trx
				AND	b.fs_kd_strx = c.fs_kd_strx
				AND	b.fs_refno = c.fs_refno
			LEFT JOIN tm_addr d (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_dept = d.fs_code
				AND	a.fs_count = d.fs_count
				AND	d.fs_cdtyp = '03'
			LEFT JOIN tm_trx e (NOLOCK) ON a.fs_kd_comp = e.fs_kd_comp
				AND	a.fs_kd_trx = e.fs_kd_trx
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND	a.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND	a.fd_refno BETWEEN '".trim($sTgl)."' AND '".trim($sTgl2)."'
		");
		
		if (trim($sTrx) <> '' and trim($sStrx) <> '')
		{
			$xSQL = $xSQL.("
				AND	a.fs_kd_trx = '".trim($sTrx)."'
				AND	a.fs_kd_strx = '".trim($sStrx)."'
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_dept, a.fs_kd_trx, a.fs_refno
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>