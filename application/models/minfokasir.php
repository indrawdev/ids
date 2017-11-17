<?php

class MInfoKasir extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function kasir($sTgl,$sTgl2,$sRefno,$sCust,$bCekIn,$bCekOut,$bCekBank)
	{
		$xSQL = ("
			SELECT	fs_refno, [fd_refno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, fd_refno, 105), 105),
					fn_pyamt fn_total, fs_descrp,
					fs_cust = CASE WHEN (SELECT CHARINDEX('\', fs_descrp, 1)) > 0 THEN SUBSTRING(fs_descrp, 1, CHARINDEX('\', fs_descrp, 0) - 1) ELSE '' END,
					fs_note = CASE WHEN (SELECT CHARINDEX('\', fs_descrp, 1)) > 0 THEN SUBSTRING(fs_descrp, CHARINDEX('\', fs_descrp, 0) + 1, LEN(fs_descrp) - 1) ELSE '' END,
					fb_in, fb_setor
			FROM 	tx_poskasir
			WHERE	fd_refno BETWEEN '".trim($sTgl)."' AND '".trim($sTgl2)."'
			");
			
		if (trim($sRefno) <> '' or trim($sCust) <> '')
		{
			$xSQL = $xSQL.("
				AND (SELECT CHARINDEX('\', fs_descrp, 1)) > 0
				AND (fs_refno like '%".trim($sRefno)."%'
					OR SUBSTRING(fs_descrp, 1, CHARINDEX('\', fs_descrp, 0) - 1) LIKE '%".trim($sCust)."%')
				");
		}
		
		if (trim($bCekIn) == '1' and trim($bCekOut) == '0')
		{
			$xSQL = $xSQL.("
				AND (fb_in = '1')
				");
		}
		
		if (trim($bCekIn) == '0' and trim($bCekOut) == '1')
		{
			$xSQL = $xSQL.("
				AND (fb_in = '0')
				");
		}
		
		if (trim($bCekIn) == '1' and trim($bCekOut) == '1')
		{
			$xSQL = $xSQL.("
				AND (fb_in = '1' OR fb_in = '0')
				");
		}
		
		if (trim($bCekBank) == '1')
		{
			$xSQL = $xSQL.("
				AND (fb_setor = '1')
				");
		}
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>