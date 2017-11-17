<?php

class MCashBank extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function cbin_cek_refno($sDept,$sCount,$sTrx,$ssTrx,$sRefno)
	{
		$sSQL = $this->db->query("
			SELECT	TOP 1 fs_refno
			FROM	tx_actheader
			WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_dept = '".trim($sDept)."'
				AND	fs_count = '".trim($sCount)."'
				AND	fs_kd_trx = '".trim($sTrx)."'
				AND	fs_kd_strx = '".trim($ssTrx)."'
				AND	fs_refno = '".trim($sRefno)."'
			");
		return $sSQL;
	}
	
	function cbin_grid($sDept,$sCount,$sTrx,$sRefno)
	{
		$lstrx = strlen(trim($sTrx));
		$lstrx = $lstrx - 4;
		
		$kddept = trim($sDept);
		$kdcount = trim($sCount);
		$kdtrx = substr(trim($sTrx), 0, $lstrx);
		$kdstrx = substr(trim($sTrx), $lstrx, 4);
		
		$xSQL = ("
			SELECT	[fs_kd_acno] = ta.fs_acno, [fs_nm_acno] = ba.fs_nm_acno, [fs_note] = ta.fs_descrp,
					[fn_debet] = CASE WHEN ta.fs_dbcr = 'D' THEN ta.fn_ftrxamt ELSE 0 END,
					[fn_credit] = CASE WHEN ta.fs_dbcr = 'C' THEN ta.fn_ftrxamt ELSE 0 END,
					[fs_seqno] = ta.fs_seqno
			FROM 	tx_actheaderdt ta (NOLOCK), tm_acno ba (NOLOCK)
			WHERE 	ta.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND ta.fs_kd_dept = '".trim($kddept)."'
				AND ta.fs_count = '".trim($kdcount)."'
				AND ta.fs_kd_trx = '".trim($kdtrx)."'
				AND ta.fs_kd_strx = '".trim($kdstrx)."'
				AND ta.fs_refno = '".trim($sRefno)."'
				AND ta.fs_acno = ba.fs_kd_acno
			ORDER BY ta.fs_seqno
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function printcb($sDept,$sCount,$sTrx,$sRefno)
	{
		$lstrx = strlen(trim($sTrx));
		$lstrx = $lstrx - 4;
		
		$kddept = trim($sDept);
		$kdcount = trim($sCount);
		$kdtrx = substr(trim($sTrx), 0, $lstrx);
		$kdstrx = substr(trim($sTrx), $lstrx, 4);
		
		$xSQL = ("
			SELECT	[fs_refno] = ta.fs_refno, [fs_descrp] = ca.fs_descrp, [fs_kd_acno] = ta.fs_acno,
					[fs_nm_acno] = ba.fs_nm_acno, [fs_note] = ta.fs_descrp,
					[fn_debet] = CASE WHEN ta.fs_dbcr = 'D' THEN ta.fn_ftrxamt ELSE 0 END,
					[fn_credit] = CASE WHEN ta.fs_dbcr = 'C' THEN ta.fn_ftrxamt ELSE 0 END,
					[fs_seqno] = ta.fs_seqno,
					[fs_nm_cabang] = (SELECT TOP 1 xx.fs_nm_code FROM tm_addr xx(NOLOCK) 
						WHERE xx.fs_kd_comp = ta.fs_kd_comp 
							AND xx.fs_cdtyp = '03' 
							AND xx.fs_code = ta.fs_kd_dept
							AND xx.fs_count = ta.fs_count),
					[fs_nm_strx] = (SELECT xx.fs_nm_strx FROM tm_strx xx
						WHERE xx.fs_kd_trx = ta.fs_kd_trx
							AND xx.fs_kd_strx = ta.fs_kd_strx
					), [docno] = ca.fs_Doc
			FROM 	tx_actheaderdt ta (NOLOCK), tm_acno ba (NOLOCK), tx_actheader ca (NOLOCK)
			WHERE 	ta.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND ta.fs_kd_dept = '".trim($kddept)."'
				AND ta.fs_count = '".trim($kdcount)."'
				AND ta.fs_kd_trx = '".trim($kdtrx)."'
				AND ta.fs_kd_strx = '".trim($kdstrx)."'
				AND ta.fs_refno = '".trim($sRefno)."'
				AND ta.fs_acno = ba.fs_kd_acno
				AND ta.fs_refno = ca.fs_refno
			ORDER BY ta.fs_seqno
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>