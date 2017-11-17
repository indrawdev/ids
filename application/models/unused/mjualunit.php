<?php

class MJual extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function cek_acno($sSupp,$sCount)
	{
		$sSQL = $this->db->query("
			SELECT	TOP 1 fs_kd_acno
			FROM 	tm_supplier (nolock)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_supplier = '".trim($sSupp)."'
				AND fs_count = '".trim($sCount)."'
			");
		return $sSQL;
	}
	
	function cek_custid($sTrx)
	{
		$sSQL = $this->db->query("
			SELECT	TOP 1 fs_idcard
			FROM 	tm_addr (nolock)
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_trx = '".trim($sTrx)."'
			");
		return $sSQL;
	}
	
	function cek_hargabeli($sTrxDt,$sProd)
	{
		$sSQL = $this->db->query("
			SELECT	TOP 1 price = CASE WHEN (fd_sdateC <= '".trim($sTrxDt)."' AND fd_edatec > = '".trim($sTrxDt)."' AND fb_aktiveC = 1) THEN
					fn_unitprcC ELSE fn_unitprcN END,
					fd_edatec, fd_edateN
			FROM	tm_unitprcPj (nolock)
			WHERE ((fd_sdateC <= '".trim($sTrxDt)."' AND fd_edatec >= '".trim($sTrxDt)."' AND fb_aktiveC = 1)
				OR (fd_sdateN <= '".trim($sTrxDt)."' AND fd_edateN >= '".trim($sTrxDt)."' AND fb_aktiveN = 1))
				AND fs_kd_product = '".trim($sProd)."'
				AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND fs_count = '".trim($this->session->userdata('gCount'))."'
			ORDER BY fd_edatec DESC, fd_edateN DESC	
			");
		return $sSQL;
	}
	
	function cek_hargabeli2($sTrxDt,$sProd)
	{
		$sSQL = $this->db->query("
			SELECT	TOP 1 price = ISNULL((CASE WHEN (fd_sdateC <= GETDATE() AND fd_edatec >= GETDATE() AND fb_aktiveC = 1) THEN
					fn_bbn ELSE fn_bbn END), 0),
					fd_edatec , fd_edateN
			FROM	tm_unitprcOE (nolock)
			WHERE ((fd_sdateC <= '".trim($sTrxDt)."' AND fd_edatec >= '".trim($sTrxDt)."' AND fb_aktiveC = 1)
				OR (fd_sdateN <= '".trim($sTrxDt)."' AND fd_edateN >= '".trim($sTrxDt)."' AND fb_aktiveN = 1))
				AND fs_kd_product = '".trim($sProd)."'
				AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND fs_count = '".trim($this->session->userdata('gCount'))."'
			ORDER BY fd_edatec DESC, fd_edateN DESC
			");
		return $sSQL;
	}
	
	function cek_kodebeli($sRefnoJual)
	{
		$sSQL = $this->db->query("
			SELECT	fs_refno
			FROM	tm_icregister
			WHERE	fs_refnoinv = '".trim($sRefnoJual)."'
			");
		return $sSQL;
	}
	
	function cek_kodedp($sRefno)
	{
		$sSQL = $this->db->query("
			SELECT	fs_refno
			FROM 	tx_cbheader (nolock)
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND fs_count = '".trim($this->session->userdata('gCount'))."'
				AND fs_kd_trx = '5300'
				AND fs_kd_strx = '0100'
				AND fs_refno = '".trim($sRefno)."'
			");
		return $sSQL;
	}
	
	function cek_mesin($sMesin)
	{
		$sSQL = $this->db->query("
			SELECT	fs_machine fs_mesin, fs_refnoinv
			FROM	tm_icregister
			WHERE	fs_machine LIKE '%".trim($sMesin)."%'
				AND	fs_refnoinv <> ''
			");
		return $sSQL;
	}
	
	function cek_param()
	{
		$sSQL = $this->db->query("
			SELECT	TOP 1 fs_kd_cussup, fs_countcussup, fs_nm_cussup,
					fs_kd_deptf, fs_countdeptf, fs_nm_deptf,
					fs_kd_whf, fs_nm_whf, fs_kd_depttf, fs_countdepttf, fs_nm_depttf,
					fs_kd_whtf, fs_nm_whtf, fb_otax
			FROM 	tm_company (nolock)
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");
		return $sSQL;
	}
	
	function cek_rangka($sRangka)
	{
		$sSQL = $this->db->query("
			SELECT	fs_rangka, fs_refnoinv
			FROM	tm_icregister
			WHERE	fs_rangka LIKE '%".trim($sRangka)."%'
				AND	fs_refnoinv <> ''
			");
		return $sSQL;
	}
	
	function cek_refno($sTrx,$sRefno)
	{
		$sSQL = $this->db->query("
			SELECT	TOP 1 fs_refno
			FROM 	tx_posheader (NOLOCK)
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND fs_count = '".trim($this->session->userdata('gCount'))."'
				AND fs_kd_trx = '".trim($sTrx)."'
				AND fs_refno = '".trim($sRefno)."'
		   ");
		return $sSQL;
	}
	
	function disc_max($sRefno,$sProd)
	{
		$sSQL = $this->db->query("
			SELECT	TOP 1 fn_grsmargin, fn_jualksi
            FROM   	tm_unitprcoe aa (nolock)
            INNER JOIN tx_posdetail bb (nolock) ON aa.fs_kd_comp = bb.fs_kd_comp
				AND aa.fs_kd_dept = bb.fs_kd_dept
				AND aa.fs_count = bb.fs_count
				AND aa.fs_kd_product = bb.fs_kd_product
				AND bb.fs_refno = '".trim($sRefno)."'
				AND bb.fs_kd_product = '".trim($sProd)."'
			");
		return $sSQL;
	}
	
	function pos_beli($sRefno,$sReg,$sTrx,$sSalesMtdBeli)
	{
		$xSQL = ("
			UPDATE	tm_icregister
				SET	fs_kd_ldept = '".trim($this->session->userdata('gDept'))."',
					fs_countdept = '".trim($this->session->userdata('gCount'))."',
                    fs_kd_trxl = 'BL',
					fs_kd_strxl = '0100',
					fs_refnol = '".trim($sRefno)."',
					fs_seqnol = '000001'
			WHERE	fs_register = '".trim($sReg)."'
			
			EXEC stp_posbeli '".trim($this->session->userdata('gComp'))."','".trim($this->session->userdata('gDept'))."',
                    '".trim($this->session->userdata('gCount'))."','".trim($sTrx)."',' ','".trim($sRefno)."'
					
			EXEC stp_stock_update '".trim($this->session->userdata('gComp'))."',
				'".trim($this->session->userdata('gDept'))."','".trim($this->session->userdata('gCount'))."',
				'".trim($sRefno)."','INS','".trim($this->session->userdata('gSparePart'))."'
			");
			
		if (trim($sTrx) == 'BL' and trim($sSalesMtdBeli) == '01')
		{
		}
		else
		{
			$xSQL = $xSQL.("
				EXEC stp_jurnalposjual 'POS',' AND fs_refno=''".trim($sRefno)."'''
			   
				EXEC stp_jurnalposjual 'JURNAL_UNIT_SALES',' AND fs_refno = ''".trim($sRefno)."'''
				");
		}
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function pos_jual($sRefno,$sTrx,$sSalesMtd)
	{
		$xSQL = ("
            UPDATE tm_icregister SET
				fs_kd_deptpt = ' ',
				fs_countdeptpt = ' ',
				fs_kd_trxpt = ' ',
				fs_refnopt = ' ',
				fs_seqnopt = ' ',
				fs_kd_deptso = ' ',
				fs_countdeptso = ' ',
				fs_refnoso = ' ',
				fs_seqnoso = ' ',
				fs_kd_trxso = ' ',
				fs_kd_deptinv = ' ',
				fs_countdeptinv = ' ',
				fs_refnoinv = ' ',
				fs_seqnoinv = ' ',
				fs_kd_trxinv = ' ',
				fs_kd_deptdo = ' ',
				fs_countdeptdo = ' ',
				fs_refnodo = ' ',
				fs_seqnodo = ' ',
				fs_kd_trxdo = ' ',
				fs_kd_ldept = ' ',
				fs_countdept = ' ',
				fs_kd_trxl = ' ',
				fs_kd_strxl = ' ',
				fs_refnol = ' ',
				fs_seqnol = ' '
            WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_refnoinv = '".trim($sRefno)."'
			
            UPDATE tm_icregister SET
				fs_kd_ldept = a.fs_kd_dept,
				fs_countdept = a.fs_count,
				fs_kd_trxl = a.fs_kd_trx,
				fs_kd_strxl = ' ',
				fs_refnol = a.fs_refno,
				fs_seqnol = a.fs_seqno,
				fs_kd_deptso = a.fs_kd_dept,
				fs_countdeptso = a.fs_count,
				fs_kd_trxso = a.fs_kd_trx,
				fs_kd_strxso = ' ',
				fs_refnoso = a.fs_refno,
				fs_seqnoso = a.fs_seqno,
				fs_kd_deptpt = a.fs_kd_dept,
				fs_countdeptpt = a.fs_count,
				fs_kd_trxpt = a.fs_kd_trx,
				fs_kd_strxpt = ' ',
				fs_refnopt = a.fs_refno,
				fs_seqnopt = a.fs_seqno,
				fs_kd_deptdo = a.fs_kd_dept,
				fs_countdeptdo = a.fs_count,
				fs_kd_trxdo = a.fs_kd_trx,
				fs_kd_strxdo = ' ',
				fs_refnodo = a.fs_refno,
				fs_seqnodo = a.fs_seqno,
				fs_kd_deptinv = a.fs_kd_dept,
				fs_countdeptinv = a.fs_count,
				fs_kd_trxinv = a.fs_kd_trx,
				fs_kd_strxinv = ' ',
				fs_refnoinv = a.fs_refno,
				fs_seqnoinv = a.fs_seqno
			FROM	tm_posregsold a (nolock), tm_icregister b (nolock)
			WHERE 	LTRIM(RTRIM(a.fs_chasis)) = LTRIM(RTRIM(b.fs_rangka))
				AND	a.fs_refno = '".trim($sRefno)."'
			
            UPDATE tm_icregister SET
				fs_kd_deptpt = ' ',
				fs_countdeptpt = ' ',
				fs_kd_trxpt = ' ',
				fs_refnopt = ' ',
				fs_seqnopt = ' ',
				fs_kd_deptso = ' ',
				fs_countdeptso = ' ',
				fs_refnoso = ' ',
				fs_seqnoso = ' ',
				fs_kd_trxso = ' ',
				fs_kd_deptinv = ' ',
				fs_countdeptinv = ' ',
				fs_refnoinv = ' ',
				fs_seqnoinv = ' ',
				fs_kd_trxinv = ' ',
				fs_kd_deptdo = ' ',
				fs_countdeptdo = ' ',
				fs_refnodo = ' ',
				fs_seqnodo = ' ',
				fs_kd_trxdo = ' ',
				fs_kd_ldept = ' ',
				fs_countdept = ' ',
				fs_kd_trxl = ' ',
				fs_kd_strxl = ' ',
				fs_refnol = ' ',
				fs_seqnol = ' '
            WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_refnoinv = '".trim($sRefno)."'
				AND fs_seqnoinv NOT IN (
					SELECT	DISTINCT fs_seqno
					FROM    tx_posdetail
					WHERE   fs_refno = '".trim($sRefno)."')
			
            DELETE	FROM tm_posregsold
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND fs_count = '".trim($this->session->userdata('gCount'))."'
				AND	fs_refno = '".trim($sRefno)."'
				AND fs_chasis NOT IN (
					SELECT  fs_rangka
					FROM    tm_icregister
					WHERE   fs_refnoinv = '".trim($sRefno)."')
					
			EXEC stp_stock_update '".trim($this->session->userdata('gComp'))."',
				'".trim($this->session->userdata('gDept'))."','".trim($this->session->userdata('gCount'))."',
				'".trim($sRefno)."','DEL','".trim($this->session->userdata('gSparePart'))."'
				
			EXEC stp_moveregsr '".trim($this->session->userdata('gComp'))."', '".trim($this->session->userdata('gSparePart'))."', '".trim($sRefno)."'
			");
			
		if (trim($sTrx) == 'BL' and trim($sSalesMtd) == '01')
		{
		}
		else
		{
			$xSQL = $xSQL.("
				EXEC stp_jurnalposjual 'POS',' AND fs_refno=''".trim($sRefno)."'''
			   
				EXEC stp_jurnalposjual 'JURNAL_UNIT_SALES',' AND fs_refno = ''".trim($sRefno)."'''
				");
		}
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function unit_stokupdate($sRefno,$sStatus)
	{
		//$sStatus = 'INS','DEL'
		$sSQL = $this->db->query("EXEC stp_stock_update '".trim($this->session->userdata('gComp'))."',
				'".trim($this->session->userdata('gDept'))."','".trim($this->session->userdata('gCount'))."',
				'".trim($sRefno)."','".trim($sStatus)."','".trim($this->session->userdata('gSparePart'))."'
			");
		return $sSQL;
	}
	
	function update_alias($sRangka,$sMesin,$sNamaSTNK) //update alias
	{
		$this->db->query("
			UPDATE	tm_icRegister SET fs_pemilik = '".trim($sNamaSTNK)."'
			WHERE	fs_rangka = '".trim($sRangka)."'
				AND	fs_machine = '".trim($sMesin)."'
		");
	}
}
?>