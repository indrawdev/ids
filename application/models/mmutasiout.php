<?php

class MMutasiOut extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function register($sWH,$sProdCd,$sSeqno,$sRefno,$sCari)
	{
		// SELECT	b.fs_rangka, b.fs_machine fs_mesin, '0' fb_cek,
				// a.fs_seqno, '".trim($sProdCd)."' fs_kd_product, a.fs_refno fs_refnoictout
		// FROM 	tx_icmoveregsr a (NOLOCK)
		// INNER JOIN tm_icregister b (NOLOCK) ON a.fs_register = b.fs_register
		// WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			// AND	a.fs_refno = '".trim($sRefno)."'
			// AND	a.fs_refno <> ''
			// AND	b.fs_kd_product = '".trim($sProdCd)."'
			// AND a.fs_seqnof = '".trim($sSeqno)."'
		$xSQL = ("
			SELECT	a.fs_rangka, a.fs_machine fs_mesin, '0' fb_cek,
					a.fd_thnpembuatan fd_thn, ISNULL(b.fs_nm_vareable, '') fs_nm_warna,
					a.fs_seqnoictout fs_seqno, a.fs_kd_product, a.fs_refnoictout
			FROM 	tm_icregister a (NOLOCK)
			LEFT JOIN tm_vareable b (NOLOCK) ON a.fs_kd_warna = b.fs_kd_vareable
				AND	b.fs_key = '08'
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_refnoictout = '".trim($sRefno)."'
				AND a.fs_kd_product = '".trim($sProdCd)."'
				AND a.fs_seqnoictout = '".trim($sSeqno)."'
			UNION
			SELECT	a.fs_rangka, a.fs_machine fs_mesin, '0' fb_cek,
					a.fd_thnpembuatan fd_thn, ISNULL(b.fs_nm_vareable, '') fs_nm_warna,
					'".trim($sSeqno)."' fs_seqno, a.fs_kd_product, a.fs_refnoictout
			FROM 	tm_icregister a (NOLOCK)
			LEFT JOIN tm_vareable b (NOLOCK) ON a.fs_kd_warna = b.fs_kd_vareable
				AND	b.fs_key = '08'
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND LTRIM(RTRIM(a.fs_refnoictout)) = ''
				AND LTRIM(RTRIM(a.fs_refnoinv)) = ''
				AND a.fs_kd_product = '".trim($sProdCd)."'
				AND a.fs_kd_wh = '".trim($sWH)."'
				AND a.fb_delete = '0'
				AND a.fs_kd_type = '0'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
			AND (a.fs_rangka LIKE '%".trim($sCari)."%'
					OR a.fs_machine LIKE '%".trim($sCari)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_refnoictout DESC, fs_rangka, fs_machine
		");

		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid_prod($sDept,$sCount,$sRefno)
	{
		$xSQL = ("
			SELECT	a.fs_kd_product, [fs_nm_product] = ISNULL(b.fs_nm_product, ''), '0' fn_qty,--a.fn_qtytr,
					[fs_kd_unit] = a.fs_unitbill, [fs_nm_unit] = ISNULL(c.fs_nm_unit, ''),
					a.fs_seqno
			FROM 	tx_actdetail a (NOLOCK)
			INNER JOIN tm_product b (NOLOCK) ON a.fs_kd_product = b.fs_kd_product
			INNER JOIN tm_unitconvertion c (NOLOCK) ON a.fs_unitbill = c.fs_kd_unit
			WHERE 	a.fs_kd_dept = '".trim($sDept)."'
				AND a.fs_count = '".trim($sCount)."'
				AND a.fs_refno = '".trim($sRefno)."'
				AND a.fs_refno <> ''
			ORDER BY a.fs_seqno
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid_reg($sDept,$sCount,$sTrx,$ssTrx,$sRefno)
	{
		$xSQL = ("
			SELECT	a.fs_rangka, a.fs_machine fs_mesin, a.fs_seqnoictout fs_seqno,
					a.fs_kd_product, a.fd_thnpembuatan fd_thn,
					ISNULL(b.fs_nm_vareable, '') fs_nm_warna
			FROM	tm_icregister a (NOLOCK)
			LEFT JOIN tm_vareable b (NOLOCK) ON a.fs_kd_warna = b.fs_kd_vareable
				AND	b.fs_key = '08'
			WHERE	a.fs_kd_deptictout = '".trim($sDept)."'
				AND	a.fs_countdeptictout = '".trim($sCount)."'
				AND	a.fs_kd_trxictout = '".trim($sTrx)."'
				AND	a.fs_kd_strxictout = '".trim($ssTrx)."'
				AND	a.fs_refnoictout = '".trim($sRefno)."'
			ORDER BY a.fs_seqnoictout
		");
		// $xSQL = ("
			// SELECT	a.fs_rangka, a.fs_machine fs_mesin,
					// b.fs_seqnof fs_seqno, a.fs_kd_product, a.fs_register fs_reg
			// FROM	tm_icregister a (NOLOCK)
			// INNER JOIN tx_icmoveregsr b (NOLOCK) ON a.fs_register = b.fs_register
			// WHERE	b.fs_kd_dept = '".trim($sDept)."'
				// AND b.fs_count = '".trim($sCount)."'
				// AND b.fs_kd_trx = '".trim($sTrx)."'
				// AND b.fs_kd_strx = '".trim($ssTrx)."'
				// AND b.fs_refno = '".trim($sRefno)."'
			// ORDER BY b.fs_seqnof, a.fs_rangka, a.fs_machine
		// ");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function mutasi_cek_refno($sDept,$sCount,$sTrx,$ssTrx,$sRefno,$sRefno2)
	{
		$xSQL = ("
			SELECT	TOP 1 fs_refno
			FROM   	tx_actheader (NOLOCK)
			WHERE  	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND	fs_count = '".trim($this->session->userdata('gCount'))."'
				AND	fs_kd_trx = '".trim($sTrx)."'
				AND	fs_kd_strx = '".trim($ssTrx)."'
				AND	fs_refno = '".trim($sRefno)."'
				AND	fs_trefno = '".trim($sRefno2)."'
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_refnoout($sRefno)
	{
		$xSQL = ("
			SELECT	TOP 1 ISNULL(fs_trefno, '') fs_trefno
			FROM   	tx_actheader (NOLOCK)
			WHERE  	fs_trefno = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_refnooutdb($sNmDB,$sRefno)
	{
		$xSQL = ("
			SELECT	TOP 1 ISNULL(a.fs_trefno, '') fs_trefno
			FROM   	[".trim($sNmDB)."]..tx_actheader a (NOLOCK)
			WHERE  	a.fs_trefno = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function mutasi_header($sDeptCd,$sCount,$sTrx,$ssTrx,$sRefno,$sWHCd,$sWHNm)
	{
		$xSQL = ("
			UPDATE	tm_icregister
				SET fs_kd_deptictout = '',
					fs_countdeptictout = '',
					fs_kd_trxictout = '',
					fs_kd_strxictout = '',
					fs_refnoictout = '',
					fs_seqnoictout = '',
					fs_kd_deptictin = '',
					fs_countdeptictin = '',
					fs_kd_trxictin = '',
					fs_kd_strxictin = '',
					fs_refnoictin = '',
					fs_seqnoictin = '',
					fs_kd_ldept = '',
					fs_countdept = '',
					fs_kd_trxl = '',
					fs_kd_strxl = '',
					fs_refnol = '',
					fs_seqnol = '',
					fs_kd_wh = '".trim($sWHCd)."',
					fs_nm_wh = '".trim($sWHNm)."',
					fb_delete = 0
			WHERE	fs_kd_deptictout = '".trim($sDeptCd)."'
				AND	fs_countdeptictout = '".trim($sCount)."'
				AND fs_kd_trxictout = '".trim($sTrx)."'
				AND fs_kd_strxictout = '".trim($ssTrx)."'
				AND fs_refnoictout = '".trim($sRefno)."'
			
			DELETE FROM tx_icmoveregsr WHERE fs_refno = '".trim($sRefno)."'
			
			DELETE FROM tx_icmove WHERE fs_kd_refno = '".trim($sRefno)."'
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function mutasi_reg($sDeptCd,$sCount,$sTrx,$ssTrx,$sRefno,$sProd,$sSeqno,$sTipe,$sRangka,$sMesin,$sWHtCd,$sWHtNm)
	{
		$xSQL = ("
			UPDATE	tm_icregister
				SET fs_kd_deptictout = '".trim($sDeptCd)."',
                    fs_countdeptictout = '".trim($sCount)."',
					fs_kd_trxictout = '".trim($sTrx)."',
					fs_kd_strxictout = '".trim($ssTrx)."',
					fs_refnoictout = '".trim($sRefno)."',
					fs_seqnoictout = '".trim($sSeqno)."',
					fs_kd_deptictin = '',
					fs_countdeptictin = '',
					fs_kd_trxictin = '',
					fs_kd_strxictin = '',
					fs_refnoictin = '',
					fs_seqnoictin = '',
					fs_kd_ldept = '".trim($sDeptCd)."',
					fs_countdept = '".trim($sCount)."',
					fs_kd_trxl = '".trim($sTrx)."',
					fs_kd_strxl = '".trim($ssTrx)."',
					fs_refnol = '".trim($sRefno)."',
					fs_seqnol = '".trim($sSeqno)."',
					fs_kd_wh = '".trim($sWHtCd)."',
					fs_nm_wh = '".trim($sWHtNm)."'
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_product = '".trim($sProd)."'
				AND	fs_kd_type = '".trim($sTipe)."'
				AND	fs_rangka = '".trim($sRangka)."'
				AND	fs_machine = '".trim($sMesin)."'
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function mutasi_move($sRefno)
	{
		$xSQL = ("
			EXEC stp_moveregsr '".trim($this->session->userdata('gComp'))."', '".trim($this->session->userdata('gSparePart'))."', '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function eksportodb($sNmDB,$sTrx,$sStrx,$sRefno)
	{
		$xSQL = ("
			DELETE
			FROM	[".trim($sNmDB)."]..tx_actheader
			WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND	fs_count = '".trim($this->session->userdata('gCount'))."'
				AND	fs_kd_trx = '".trim($sTrx)."'
				AND	fs_kd_strx = '".trim($sStrx)."'
				AND	fs_refno = '".trim($sRefno)."'
			
			DELETE
			FROM 	[".trim($sNmDB)."]..tx_actdetail
			WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND	fs_count = '".trim($this->session->userdata('gCount'))."'
				AND	fs_kd_trx = '".trim($sTrx)."'
				AND	fs_kd_strx = '".trim($sStrx)."'
				AND	fs_refno = '".trim($sRefno)."'
			
			DELETE	[".trim($sNmDB)."]..tm_icregister
			FROM 	[".trim($sNmDB)."]..tm_icregister z
			WHERE	EXISTS (
				SELECT	a.*
				FROM	tm_icregister a (NOLOCK)
				INNER JOIN tx_actdetail b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
					AND a.fs_kd_deptictout = b.fs_kd_dept
					AND a.fs_countdeptictout = b.fs_count
					AND a.fs_kd_trxictout = b.fs_kd_trx
					AND a.fs_refnoictout = b.fs_refno
					AND a.fs_seqnoictout = b.fs_seqno
				WHERE	a.fs_kd_comp = z.fs_kd_comp
					AND	a.fs_kd_product = z.fs_kd_product
					AND	a.fs_rangka = z.fs_rangka
					AND	a.fs_machine = z.fs_machine
					AND	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	a.fs_kd_deptictout = '".trim($this->session->userdata('gDept'))."'
					AND	a.fs_countdeptictout = '".trim($this->session->userdata('gCount'))."'
					AND	a.fs_kd_trxictout = '".trim($sTrx)."'
					AND	a.fs_refnoictout = '".trim($sRefno)."'
			)
			
			DELETE	[".trim($sNmDB)."]..tm_product
			FROM	[".trim($sNmDB)."]..tm_product z
			WHERE	EXISTS (
				SELECT	DISTINCT a.*
				FROM	tm_product a (NOLOCK)
				INNER JOIN tx_actdetail b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
					AND	a.fs_kd_product = b.fs_kd_product
				WHERE	a.fs_kd_comp = z.fs_kd_comp
					AND	a.fs_kd_product = z.fs_kd_product
					AND	b.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	b.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	b.fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	b.fs_kd_trx = '".trim($sTrx)."'
					AND	b.fs_kd_strx = '".trim($sStrx)."'
					AND	b.fs_refno = '".trim($sRefno)."'
			)
			
			DELETE	[".trim($sNmDB)."]..tm_unitprcoe
			FROM	[".trim($sNmDB)."]..tm_unitprcoe z
			WHERE	EXISTS (
				SELECT	DISTINCT a.*
				FROM	tm_unitprcoe a (NOLOCK)
				INNER JOIN tx_actheader b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
					AND	a.fs_kd_dept = b.fs_kd_tdept
					AND	a.fs_count = b.fs_tcount
				INNER JOIN tx_actdetail c (NOLOCK) ON b.fs_kd_comp = c.fs_kd_comp
					AND	b.fs_kd_dept = c.fs_kd_dept
					AND	b.fs_count = c.fs_count
					AND	b.fs_kd_trx = c.fs_kd_trx
					AND	b.fs_refno = c.fs_refno
					AND	a.fs_kd_product = c.fs_kd_product
				WHERE	a.fs_kd_comp = z.fs_kd_comp
					AND	a.fs_kd_dept = z.fs_kd_dept
					AND	a.fs_count = z.fs_count
					AND	a.fs_kd_product = z.fs_kd_product
					AND	b.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	b.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	b.fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	b.fs_kd_trx = '".trim($sTrx)."'
					AND	b.fs_kd_strx = '".trim($sStrx)."'
					AND	b.fs_refno = '".trim($sRefno)."'
			)
			
			DELETE	[".trim($sNmDB)."]..tm_vareable
			FROM	[".trim($sNmDB)."]..tm_vareable z
			WHERE	EXISTS (
				SELECT	DISTINCT a.*
				FROM 	tm_vareable a
				INNER JOIN tm_icregister b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
					AND	a.fs_kd_vareable = b.fs_kd_warna
					AND	a.fs_key = '08'
				INNER JOIN tx_actdetail c (NOLOCK) ON b.fs_kd_comp = c.fs_kd_comp
					AND b.fs_kd_deptictout = c.fs_kd_dept
					AND b.fs_countdeptictout = c.fs_count
					AND b.fs_kd_trxictout = c.fs_kd_trx
					AND b.fs_refnoictout = c.fs_refno
					AND b.fs_seqnoictout = c.fs_seqno
				WHERE	a.fs_kd_comp = z.fs_kd_comp
					AND	a.fs_key = z.fs_key
					AND	a.fs_kd_vareable = z.fs_kd_vareable
					AND	b.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	b.fs_kd_deptictout = '".trim($this->session->userdata('gDept'))."'
					AND	b.fs_countdeptictout = '".trim($this->session->userdata('gCount'))."'
					AND	b.fs_kd_trxictout = '".trim($sTrx)."'
					AND	b.fs_refnoictout = '".trim($sRefno)."'
			)
			
			
			INSERT INTO [".trim($sNmDB)."]..tx_actheader(
					fs_kd_comp, fs_kd_dept, fs_count,
					fs_kd_trx, fs_kd_strx, fs_refno,
					fd_refno, fs_kd_fdept, fs_fcount,
					fs_kd_ftrx, fs_kd_fstrx, fs_frefno,
					fd_frefno, fs_kd_tdept, fs_tcount,
					fs_kd_ttrx, fs_kd_tstrx, fs_trefno,
					fd_trefno, fd_periode, fs_doc,
					fd_doc, fs_kd_cussup, fs_countcussup,
					fs_descrp, fs_kd_term, fd_ovrdue,
					fs_currency, fn_currrate, fn_currbase,
					fn_taxrate, fn_taxbase, fb_prcinctax,
					fs_kd_tax, fn_taxpct, fn_ftaxamt,
					fn_ltaxamt, fs_kd_grpacno, fn_grsamt,
					fn_dscamt, fn_sdscamt, fn_netbfrtax,
					fs_kd_addon, fn_addonamt, fn_addamt,
					fs_kd_addin, fn_addinamt, fn_netaftrtax,
					fs_kd_deduc, fn_deducamt, fs_kd_dp,
					fs_nm_dp, fn_fdpamt, fn_ldpamt,
					fn_frmndpamt, fn_lrmndpamt, fn_rmnamt,
					fn_ftrxamt, fn_ltrxamt, fn_fftrxamt,
					fn_fltrxamt, fn_tftrxamt, fn_tltrxamt,
					fs_kd_payment, fb_delete, fb_draft,
					fs_kd_otax, fn_otaxpct, fn_otaxamtf,
					fn_otaxamtl, fn_printo, fn_printd,
					fn_printapv, fn_printr, fn_printship,
					fn_printb, fn_printcl, fn_printc,
					fn_qty, fs_kd_wh, fs_kd_bin,
					fs_kd_alloc, fs_kd_wht, fs_kd_bint,
					fs_kd_alloct, fs_trsta, fs_pmtyp,
					fs_modul, fs_kd_acnopmtyp, fs_kd_acno,
					fs_kd_sls, fs_countsls, fs_usrcrt,
					fd_usrcrt, fs_upddt, fd_upddt,
					fn_fluxtaxamt, fn_lluxtaxamt, fs_cekgiro,
					fb_close
			)
			SELECT	fs_kd_comp, fs_kd_dept, fs_count,
					fs_kd_trx, fs_kd_strx, fs_refno,
					fd_refno, fs_kd_fdept, fs_fcount,
					fs_kd_ftrx, fs_kd_fstrx, fs_frefno,
					fd_frefno, fs_kd_tdept, fs_tcount,
					fs_kd_ttrx, fs_kd_tstrx, fs_trefno,
					fd_trefno, fd_periode, fs_doc,
					fd_doc, fs_kd_cussup, fs_countcussup,
					fs_descrp, fs_kd_term, fd_ovrdue,
					fs_currency, fn_currrate, fn_currbase,
					fn_taxrate, fn_taxbase, fb_prcinctax,
					fs_kd_tax, fn_taxpct, fn_ftaxamt,
					fn_ltaxamt, fs_kd_grpacno, fn_grsamt,
					fn_dscamt, fn_sdscamt, fn_netbfrtax,
					fs_kd_addon, fn_addonamt, fn_addamt,
					fs_kd_addin, fn_addinamt, fn_netaftrtax,
					fs_kd_deduc, fn_deducamt, fs_kd_dp,
					fs_nm_dp, fn_fdpamt, fn_ldpamt,
					fn_frmndpamt, fn_lrmndpamt, fn_rmnamt,
					fn_ftrxamt, fn_ltrxamt, fn_fftrxamt,
					fn_fltrxamt, fn_tftrxamt, fn_tltrxamt,
					fs_kd_payment, fb_delete, fb_draft,
					fs_kd_otax, fn_otaxpct, fn_otaxamtf,
					fn_otaxamtl, fn_printo, fn_printd,
					fn_printapv, fn_printr, fn_printship,
					fn_printb, fn_printcl, fn_printc,
					fn_qty, fs_kd_wh, fs_kd_bin,
					fs_kd_alloc, fs_kd_wht, fs_kd_bint,
					fs_kd_alloct, fs_trsta, fs_pmtyp,
					fs_modul, fs_kd_acnopmtyp, fs_kd_acno,
					fs_kd_sls, fs_countsls, fs_usrcrt,
					fd_usrcrt, fs_upddt, fd_upddt,
					fn_fluxtaxamt, fn_lluxtaxamt, fs_cekgiro,
					fb_close
			FROM	tx_actheader (NOLOCK)
			WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND	fs_count = '".trim($this->session->userdata('gCount'))."'
				AND	fs_kd_trx = '".trim($sTrx)."'
				AND	fs_kd_strx = '".trim($sStrx)."'
				AND	fs_refno = '".trim($sRefno)."'
				AND	fb_delete = '0'
			
			INSERT INTO [".trim($sNmDB)."]..tx_actdetail(
					fs_kd_comp, fs_kd_dept, fs_count,
					fs_kd_trx, fs_kd_strx, fs_refno,
					fs_seqno, fd_refno, fs_modul,
					fs_descp, fd_periode, fs_kd_cussup,
					fs_kd_refnoso, fs_seqnoso, fs_countcussup,
					fs_kd_refnopr, fs_seqnopr, fs_kd_refnopo,
					fs_seqnopo, fs_kd_refnoco, fs_seqnoco,
					fs_kd_refnogr, fs_seqnogr, fs_kd_refnort,
					fs_seqnort, fs_kd_refnoinv, fs_seqnoinv,
					fs_kd_refnocm, fs_seqnocm, fn_qtypr,
					fn_qtypo, fn_qtyco, fn_qtygr,
					fn_qtyrt, fn_qtyinv, fn_qtyso,
					fn_qtycm, fs_kd_product, fs_kd_acno,
					fs_kd_type, fn_attribute, fs_kd_attribute,
					fs_kd_sproduct, fs_nm_sproduct, fn_qtytr,
					fs_unitbill, fn_qtytr1, fb_conv,
					fn_conv, fn_unitprc, fn_discprc,
					fn_lineamt, fn_ldscamt, fn_sdscamt,
					fs_kd_addin, fs_kd_addon, fn_addonamt,
					fn_addinamt, fs_kd_deduc, fn_deducamt,
					fs_kd_luxtax, fn_fluxtaxamt, fn_lluxtaxamt,
					fn_luxtaxpct, fs_currency, fn_currrate,
					fn_currbase, fn_taxrate, fn_netaftrtax,
					fn_netbfrtax, fn_ltrxamt, fn_ftrxamt,
					fn_taxbase, fs_kd_wh, fs_kd_bin,
					fs_kd_alloc, fs_trsta, fs_pmtyp,
					fn_unitprcotr, fn_dpamt, fs_apvkomisi,
					fs_penerima, fs_noktp, fs_alamat,
					fs_keterangan, fs_usrcrt, fd_usrcrt,
					fs_upddt, fd_upddt, fn_ftaxamt,
					fn_ltaxamt, fn_fotaxamt, fn_lotaxamt,
					fn_refundamt
			)
			SELECT	fs_kd_comp, fs_kd_dept, fs_count,
					fs_kd_trx, fs_kd_strx, fs_refno,
					fs_seqno, fd_refno, fs_modul,
					fs_descp, fd_periode, fs_kd_cussup,
					fs_kd_refnoso, fs_seqnoso, fs_countcussup,
					fs_kd_refnopr, fs_seqnopr, fs_kd_refnopo,
					fs_seqnopo, fs_kd_refnoco, fs_seqnoco,
					fs_kd_refnogr, fs_seqnogr, fs_kd_refnort,
					fs_seqnort, fs_kd_refnoinv, fs_seqnoinv,
					fs_kd_refnocm, fs_seqnocm, fn_qtypr,
					fn_qtypo, fn_qtyco, fn_qtygr,
					fn_qtyrt, fn_qtyinv, fn_qtyso,
					fn_qtycm, fs_kd_product, fs_kd_acno,
					fs_kd_type, fn_attribute, fs_kd_attribute,
					fs_kd_sproduct, fs_nm_sproduct, fn_qtytr,
					fs_unitbill, fn_qtytr1, fb_conv,
					fn_conv, fn_unitprc, fn_discprc,
					fn_lineamt, fn_ldscamt, fn_sdscamt,
					fs_kd_addin, fs_kd_addon, fn_addonamt,
					fn_addinamt, fs_kd_deduc, fn_deducamt,
					fs_kd_luxtax, fn_fluxtaxamt, fn_lluxtaxamt,
					fn_luxtaxpct, fs_currency, fn_currrate,
					fn_currbase, fn_taxrate, fn_netaftrtax,
					fn_netbfrtax, fn_ltrxamt, fn_ftrxamt,
					fn_taxbase, fs_kd_wh, fs_kd_bin,
					fs_kd_alloc, fs_trsta, fs_pmtyp,
					fn_unitprcotr, fn_dpamt, fs_apvkomisi,
					fs_penerima, fs_noktp, fs_alamat,
					fs_keterangan, fs_usrcrt, fd_usrcrt,
					fs_upddt, fd_upddt, fn_ftaxamt,
					fn_ltaxamt, fn_fotaxamt, fn_lotaxamt,
					fn_refundamt
			FROM 	tx_actdetail (NOLOCK)
			WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND	fs_count = '".trim($this->session->userdata('gCount'))."'
				AND	fs_kd_trx = '".trim($sTrx)."'
				AND	fs_kd_strx = '".trim($sStrx)."'
				AND	fs_refno = '".trim($sRefno)."'
			
			INSERT INTO [".trim($sNmDB)."]..tm_icregister(
					fs_kd_comp, fs_seqnoregister, fs_register,
					fs_kd_product, fs_kd_type, fs_kd_dept,
					fs_count, fs_kd_trx, fs_kd_strx,
					fs_refno, fs_seqno, fd_periode,
					fn_hpp, fs_bahanbakar, fs_descrp,
					fb_delete, fs_type, fn_silinder,
					fs_model, fs_rangka, fs_machine,
					fs_kd_warna, fs_keyno, fs_pemilik,
					fs_stnk, fd_stnk, fs_bpkb,
					fd_bpkb, fs_nopol, fd_thnpembuatan,
					fd_thnperakitan, fs_kd_warnatnkb, fs_kd_lokasi,
					fn_total_installment, fn_installment, fn_lastpayment,
					fs_kd_ldept, fs_countdept, fs_kd_trxl,
					fs_kd_strxl, fs_refnol, fs_seqnol,
					fs_kd_deptso, fs_countdeptso, fs_kd_trxso,
					fs_kd_strxso, fs_refnoso, fs_seqnoso,
					fs_kd_deptpt, fs_countdeptpt, fs_kd_trxpt,
					fs_kd_strxpt, fs_refnopt, fs_seqnopt,
					fs_kd_deptdo, fs_countdeptdo, fs_kd_trxdo,
					fs_kd_strxdo, fs_refnodo, fs_seqnodo,
					fs_kd_deptinv, fs_countdeptinv, fs_kd_trxinv,
					fs_kd_strxinv, fs_refnoinv, fs_seqnoinv,
					fs_kd_deptpinv, fs_countdeptpinv, fs_kd_trxpinv,
					fs_kd_strxpinv, fs_refnopinv, fs_seqnopinv,
					fs_kd_deptictin, fs_countdeptictin, fs_kd_trxictin,
					fs_kd_strxictin, fs_refnoictin, fs_seqnoictin,
					fs_kd_deptictout, fs_countdeptictout, fs_kd_trxictout,
					fs_kd_strxictout, fs_refnoictout, fs_seqnoictout,
					fs_kd_depticout, fs_countdepticout, fs_kd_trxicout,
					fs_kd_strxicout, fs_refnoicout, fs_seqnoicout,
					fs_regreplace, fn_qty, fs_kd_wh,
					fs_nm_wh, fs_refnogr, fd_refnogr,
					fd_refnopinv, fd_pencairan, fn_pencairan,
					fn_bbn, fs_kd_term, fs_usrcrt,
					fd_usrcrt, fs_upddt, fd_upddt
			)
			SELECT	a.fs_kd_comp, a.fs_seqnoregister, a.fs_register,
					a.fs_kd_product, a.fs_kd_type, a.fs_kd_dept,
					a.fs_count, a.fs_kd_trx, a.fs_kd_strx,
					a.fs_refno, a.fs_seqno, a.fd_periode,
					a.fn_hpp, a.fs_bahanbakar, a.fs_descrp,
					a.fb_delete, a.fs_type, a.fn_silinder,
					a.fs_model, a.fs_rangka, a.fs_machine,
					a.fs_kd_warna, a.fs_keyno, a.fs_pemilik,
					a.fs_stnk, a.fd_stnk, a.fs_bpkb,
					a.fd_bpkb, a.fs_nopol, a.fd_thnpembuatan,
					a.fd_thnperakitan, a.fs_kd_warnatnkb, a.fs_kd_lokasi,
					a.fn_total_installment, a.fn_installment, a.fn_lastpayment,
					a.fs_kd_ldept, a.fs_countdept, a.fs_kd_trxl,
					a.fs_kd_strxl, a.fs_refnol, a.fs_seqnol,
					a.fs_kd_deptso, a.fs_countdeptso, a.fs_kd_trxso,
					a.fs_kd_strxso, a.fs_refnoso, a.fs_seqnoso,
					a.fs_kd_deptpt, a.fs_countdeptpt, a.fs_kd_trxpt,
					a.fs_kd_strxpt, a.fs_refnopt, a.fs_seqnopt,
					a.fs_kd_deptdo, a.fs_countdeptdo, a.fs_kd_trxdo,
					a.fs_kd_strxdo, a.fs_refnodo, a.fs_seqnodo,
					a.fs_kd_deptinv, a.fs_countdeptinv, a.fs_kd_trxinv,
					a.fs_kd_strxinv, a.fs_refnoinv, a.fs_seqnoinv,
					a.fs_kd_deptpinv, a.fs_countdeptpinv, a.fs_kd_trxpinv,
					a.fs_kd_strxpinv, a.fs_refnopinv, a.fs_seqnopinv,
					a.fs_kd_deptictin, a.fs_countdeptictin, a.fs_kd_trxictin,
					a.fs_kd_strxictin, a.fs_refnoictin, a.fs_seqnoictin,
					a.fs_kd_deptictout, a.fs_countdeptictout, a.fs_kd_trxictout,
					a.fs_kd_strxictout, a.fs_refnoictout, a.fs_seqnoictout,
					a.fs_kd_depticout, a.fs_countdepticout, a.fs_kd_trxicout,
					a.fs_kd_strxicout, a.fs_refnoicout, a.fs_seqnoicout,
					a.fs_regreplace, a.fn_qty, a.fs_kd_wh,
					a.fs_nm_wh, a.fs_refnogr, a.fd_refnogr,
					a.fd_refnopinv, a.fd_pencairan, a.fn_pencairan,
					a.fn_bbn, a.fs_kd_term, a.fs_usrcrt,
					a.fd_usrcrt, a.fs_upddt, a.fd_upddt
			FROM 	tm_icregister a (NOLOCK)
			INNER JOIN tx_actdetail b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_deptictout = b.fs_kd_dept
				AND a.fs_countdeptictout = b.fs_count
				AND a.fs_kd_trxictout = b.fs_kd_trx
				AND a.fs_refnoictout = b.fs_refno
				AND a.fs_seqnoictout = b.fs_seqno
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	a.fs_kd_deptictout = '".trim($this->session->userdata('gDept'))."'
				AND	a.fs_countdeptictout = '".trim($this->session->userdata('gCount'))."'
				AND	a.fs_kd_trxictout = '".trim($sTrx)."'
				AND	a.fs_refnoictout = '".trim($sRefno)."'
			
			INSERT INTO [".trim($sNmDB)."]..tm_product(
					fs_kd_comp, fs_kd_product, fs_kd_type,
					fs_nm_product, fb_active, fs_kd_cogs,
					fb_purchase, fb_sale, fb_transfer,
					fb_warning, fn_limitquota, fs_kd_unit1,
					fb_proddept, fs_kd_group, fb_attribute,
					fn_balance, fn_valuebal, fs_kd_acno,
					fs_kd_acnox, fs_kd_acnor, fs_kd_acnorx,
					fs_kd_component, fn_component, fs_unitpurchase,
					fs_unitsale, fs_kd_moving, fs_kd_jnsprod,
					fs_usrcrt, fd_usrcrt, fs_upddt,
					fd_upddt, fs_nm_faktur
			)
			SELECT	DISTINCT a.fs_kd_comp, a.fs_kd_product, a.fs_kd_type,
					a.fs_nm_product, a.fb_active, a.fs_kd_cogs,
					a.fb_purchase, a.fb_sale, a.fb_transfer,
					a.fb_warning, a.fn_limitquota, a.fs_kd_unit1,
					a.fb_proddept, a.fs_kd_group, a.fb_attribute,
					a.fn_balance, a.fn_valuebal, a.fs_kd_acno,
					a.fs_kd_acnox, a.fs_kd_acnor, a.fs_kd_acnorx,
					a.fs_kd_component, a.fn_component, a.fs_unitpurchase,
					a.fs_unitsale, a.fs_kd_moving, a.fs_kd_jnsprod,
					a.fs_usrcrt, a.fd_usrcrt, a.fs_upddt,
					a.fd_upddt, a.fs_nm_faktur
			FROM	tm_product a (NOLOCK)
			INNER JOIN tx_actdetail b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_product = b.fs_kd_product
			WHERE	b.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	b.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND	b.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND	b.fs_kd_trx = '".trim($sTrx)."'
				AND	b.fs_kd_strx = '".trim($sStrx)."'
				AND	b.fs_refno = '".trim($sRefno)."'
			
			INSERT INTO [".trim($sNmDB)."]..tm_unitprcoe(
					fs_kd_comp, fs_kd_dept, fs_count,
					fn_unitprcpo, fs_refnopo, fd_refnopo,
					fs_kd_product, fs_kd_type, fs_kd_color,
					fn_unitprcc, fd_datec, fd_sdatec,
					fd_edatec, fb_aktivec, fd_setting,
					fn_unitprcn, fd_daten, fd_sdaten,
					fd_edaten, fb_aktiven, fn_grsmargin,
					fn_jualksi, fn_bbn, fs_usrcrt,
					fd_usrcrt, fs_upddt, fd_upddt
			)
			SELECT	DISTINCT a.fs_kd_comp, a.fs_kd_dept, a.fs_count,
					a.fn_unitprcpo, a.fs_refnopo, a.fd_refnopo,
					a.fs_kd_product, a.fs_kd_type, a.fs_kd_color,
					a.fn_unitprcc, a.fd_datec, a.fd_sdatec,
					'30000101', a.fb_aktivec, a.fd_setting,
					a.fn_unitprcn, a.fd_daten, a.fd_sdaten,
					a.fd_edaten, a.fb_aktiven, a.fn_grsmargin,
					a.fn_jualksi, a.fn_bbn, a.fs_usrcrt,
					a.fd_usrcrt, a.fs_upddt, a.fd_upddt
			FROM	tm_unitprcoe a (NOLOCK)
			INNER JOIN tx_actheader b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_dept = b.fs_kd_tdept
				AND	a.fs_count = b.fs_tcount
			INNER JOIN tx_actdetail c (NOLOCK) ON b.fs_kd_comp = c.fs_kd_comp
				AND	b.fs_kd_dept = c.fs_kd_dept
				AND	b.fs_count = c.fs_count
				AND	b.fs_kd_trx = c.fs_kd_trx
				AND	b.fs_refno = c.fs_refno
				AND	a.fs_kd_product = c.fs_kd_product
			WHERE	b.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	b.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND	b.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND	b.fs_kd_trx = '".trim($sTrx)."'
				AND	b.fs_kd_strx = '".trim($sStrx)."'
				AND	b.fs_refno = '".trim($sRefno)."'
			
			INSERT INTO [".trim($sNmDB)."]..tm_vareable(
					fs_kd_comp, fs_key,
					fs_kd_vareable, fs_nm_vareable
			)
			SELECT	DISTINCT a.fs_kd_comp, a.fs_key,
					a.fs_kd_vareable, a.fs_nm_vareable
			FROM 	tm_vareable a
			INNER JOIN tm_icregister b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_vareable = b.fs_kd_warna
				AND	a.fs_key = '08'
			INNER JOIN tx_actdetail c (NOLOCK) ON b.fs_kd_comp = c.fs_kd_comp
				AND b.fs_kd_deptictout = c.fs_kd_dept
				AND b.fs_countdeptictout = c.fs_count
				AND b.fs_kd_trxictout = c.fs_kd_trx
				AND b.fs_refnoictout = c.fs_refno
				AND b.fs_seqnoictout = c.fs_seqno
			WHERE	b.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	b.fs_kd_deptictout = '".trim($this->session->userdata('gDept'))."'
				AND	b.fs_countdeptictout = '".trim($this->session->userdata('gCount'))."'
				AND	b.fs_kd_trxictout = '".trim($sTrx)."'
				AND	b.fs_refnoictout = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function mutasiout_cetak($sRefno)
	{
		$xSQL = ("
			SELECT	aa.fs_refnoictout fs_refno, ISNULL(ff.fs_nm_product, '') fs_nm_product,
					aa.fd_thnpembuatan as fs_year,
					aa.fs_rangka, aa.fs_machine, ee.fs_nm_vareable as fs_nm_warna,
					fs_register, bb.fs_kd_dept, bb.fs_count,
					cc.fs_nm_code as fs_nm_deptf, cc.fs_addr as fs_addrf, cc.fs_phone1 as telp_depf,
					cc.fs_kd_city as fs_kd_kotaf, ISNULL(gg.fs_nm_vareable, '') fs_nm_kotaf,
					bb.fs_kd_Tdept, bb.fs_TCount, dd.fs_nm_code as fs_nm_deptt, dd.fs_addr as fs_addrt,
					dd.fs_kd_city as fs_kd_kotat, ISNULL(hh.fs_nm_vareable, '') fs_nm_kotat
			FROM 	tm_icregister aa
			INNER JOIN tx_actheader bb ON bb.fs_refno = aa.fs_refnoictout
			INNER JOIN tm_addr cc ON cc.fs_code = bb.fs_kd_dept and cc.fs_count = bb.fs_count
			INNER JOIN tm_addr dd ON dd.fs_code = bb.fs_kd_Tdept and dd.fs_count = bb.fs_TCount
			LEFT JOIN tm_vareable ee ON ee.fs_kd_vareable = aa.fs_kd_warna
				AND	ee.fs_key = '08'
			LEFT JOIN tm_product ff ON ff.fs_kd_product = aa.fs_kd_product
			LEFT JOIN tm_vareable gg ON cc.fs_kd_city = gg.fs_kd_vareable
				AND	gg.fs_key = '14'
			LEFT JOIN tm_vareable hh ON dd.fs_kd_city = hh.fs_kd_vareable
				AND	hh.fs_key = '14'
			WHERE aa.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	bb.fs_refno = '".trim($sRefno)."'
			ORDER BY fs_nm_product
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>