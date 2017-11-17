<?php

class MJual extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function cek_acno($sSupp,$sCount)
	{
		$xSQL = ("
			SELECT	TOP 1 fs_kd_acno
			FROM 	tm_supplier (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_supplier = '".trim($sSupp)."'
				AND fs_count = '".trim($sCount)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_custid($sTrx)
	{
		$xSQL = ("
			SELECT	TOP 1 fs_idcard
			FROM 	tm_addr (NOLOCK)
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_trx = '".trim($sTrx)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_hargabeli($sTrxDt,$sProd)
	{
		$xSQL = ("
			SELECT	TOP 1 price = CASE WHEN (fd_sdateC <= '".trim($sTrxDt)."' AND fd_edatec > = '".trim($sTrxDt)."' AND fb_aktiveC = 1) THEN
					fn_unitprcC ELSE fn_unitprcN END,
					fd_edatec, fd_edateN
			FROM	tm_unitprcPj (NOLOCK)
			WHERE ((fd_sdateC <= '".trim($sTrxDt)."' AND fd_edatec >= '".trim($sTrxDt)."' AND fb_aktiveC = 1)
				OR (fd_sdateN <= '".trim($sTrxDt)."' AND fd_edateN >= '".trim($sTrxDt)."' AND fb_aktiveN = 1))
				AND fs_kd_product = '".trim($sProd)."'
				AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND fs_count = '".trim($this->session->userdata('gCount'))."'
			ORDER BY fd_edatec DESC, fd_edateN DESC	
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_hargabeli2($sTrxDt,$sProd)
	{
		$xSQL = ("
			SELECT	TOP 1 price = ISNULL((CASE WHEN (fd_sdateC <= GETDATE() AND fd_edatec >= GETDATE() AND fb_aktiveC = 1) THEN
					fn_bbn ELSE fn_bbn END), 0),
					fd_edatec , fd_edateN
			FROM	tm_unitprcOE (NOLOCK)
			WHERE ((fd_sdateC <= '".trim($sTrxDt)."' AND fd_edatec >= '".trim($sTrxDt)."' AND fb_aktiveC = 1)
				OR (fd_sdateN <= '".trim($sTrxDt)."' AND fd_edateN >= '".trim($sTrxDt)."' AND fb_aktiveN = 1))
				AND fs_kd_product = '".trim($sProd)."'
				AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND fs_count = '".trim($this->session->userdata('gCount'))."'
			ORDER BY fd_edatec DESC, fd_edateN DESC
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_kodebeli($sRefnoJual)
	{
		$xSQL = ("
			SELECT	fs_refno
			FROM	tm_icregister
			WHERE	fs_refnoinv = '".trim($sRefnoJual)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_kodedp($sRefno)
	{
		$xSQL = ("
			SELECT	fs_refno
			FROM 	tx_cbheader (NOLOCK)
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND fs_count = '".trim($this->session->userdata('gCount'))."'
				AND fs_kd_trx = '5300'
				AND fs_kd_strx = '0100'
				AND fs_refno = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_mesin($sMesin)
	{
		$xSQL = ("
			SELECT	fs_machine fs_mesin, fs_refnoinv
			FROM	tm_icregister
			WHERE	fs_machine LIKE '%".trim($sMesin)."%'
				AND	fs_refnoinv <> ''
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_param()
	{
		$xSQL = ("
			SELECT	TOP 1 fs_kd_cussup, fs_countcussup, fs_nm_cussup,
					fs_kd_deptf, fs_countdeptf, fs_nm_deptf,
					fs_kd_whf, fs_nm_whf, fs_kd_depttf, fs_countdepttf, fs_nm_depttf,
					fs_kd_whtf, fs_nm_whtf, fb_otax
			FROM 	tm_company (NOLOCK)
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_rangka($sRangka)
	{
		$xSQL = ("
			SELECT	fs_rangka, fs_refnoinv
			FROM	tm_icregister
			WHERE	fs_rangka LIKE '%".trim($sRangka)."%'
				AND	fs_refnoinv <> ''
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_refno($sTrx,$sRefno)
	{
		$xSQL = ("
			SELECT	TOP 1 fs_refno, fb_edit
			FROM 	tx_posheader (NOLOCK)
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_trx = '".trim($sTrx)."'
				AND fs_refno = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function disc_max($sRefno,$sProd)
	{
		$xSQL = ("
			SELECT	TOP 1 fn_grsmargin, fn_jualksi
            FROM   	tm_unitprcoe aa (NOLOCK)
            INNER JOIN tx_posdetail bb (NOLOCK) ON aa.fs_kd_comp = bb.fs_kd_comp
				AND aa.fs_kd_dept = bb.fs_kd_dept
				AND aa.fs_count = bb.fs_count
				AND aa.fs_kd_product = bb.fs_kd_product
				AND bb.fs_refno = '".trim($sRefno)."'
				AND bb.fs_kd_product = '".trim($sProd)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function pos_jual($sRefno,$sRangka,$sMesin,$sTrx,$sSalesMtdBeli)
	{
		$xSQL = ("
			UPDATE	tm_icregister
				SET	fs_kd_ldept = '".trim($this->session->userdata('gDept'))."',
					fs_countdept = '".trim($this->session->userdata('gCount'))."',
                    fs_kd_trxl = 'BL',
					fs_kd_strxl = '0100',
					fs_refnol = '".trim($sRefno)."',
					fs_seqnol = '000001'
			WHERE	fs_rangka = '".trim($sRangka)."'
				AND	fs_machine = '".trim($sMesin)."'
			
			EXEC stp_stock_update '".trim($this->session->userdata('gComp'))."',
				'".trim($this->session->userdata('gDept'))."','".trim($this->session->userdata('gCount'))."',
				'".trim($sRefno)."','DEL','".trim($this->session->userdata('gSparePart'))."'
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
	
	function pos_jual2($sRefno,$sTrx,$sSalesMtd)
	{
		$xSQL = ("
            UPDATE	tm_icregister SET
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
			
            UPDATE	tm_icregister SET
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
			FROM	tm_posregsold a (NOLOCK), tm_icregister b (NOLOCK)
			WHERE 	LTRIM(RTRIM(a.fs_chasis)) = LTRIM(RTRIM(b.fs_rangka))
				AND	LTRIM(RTRIM(a.fs_machine)) = LTRIM(RTRIM(b.fs_machine))
				AND	a.fs_refno = '".trim($sRefno)."'
			
            UPDATE	tm_icregister SET
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
	
	function reset_reg($sRefno)
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
		");
		
		$sSQL = $this->db->query($xSQL);
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

	function data_refno($sRefno)
	{
		$xSQL = ("
			SELECT TOP 1 *
			FROM 	tx_posheader (NOLOCK)
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_refno = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}


	function jual_unit_all($sTrx,$sRefno,$sDocno,$sCust,$bCash,$bAll,$sCari)
	{
		$xSQL = ("
			SELECT	[fs_kd_cussup] = LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)),
					[fs_nm_cussup] = ISNULL(b.fs_nm_code, ''),
					[fs_addr] = ISNULL(b.fs_addr, ''),
					[fs_idcard] = ISNULL(b.fs_idcard, ''), fs_phone1 fs_tlp,
					a.fs_refno, a.fb_edit,
					[fd_refno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105),
					a.fs_docno,
					[fd_docno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_docno, 105), 105),
					[fs_kd_sales] = ISNULL(c.fs_kd_sales, ''),
					[fs_nm_sales] = ISNULL(c.fs_nm_sales, ''),
					[fs_kd_surveyor] = ISNULL(c.fs_kd_surveyor, ''),
					[fs_nm_surveyor] = ISNULL(c.fs_nm_surveyor, ''),
					a.fs_kd_term, [fs_nm_term] = ISNULL(d.fs_nm_term, ''),
					[fs_kd_product] = ISNULL(c.fs_kd_product, ''),
					[fs_nm_product] = ISNULL(c.fs_nm_product, ''),
					[fs_rangka] = ISNULL(c.fs_chasis, ''),
					[fs_mesin] = ISNULL(c.fs_machine, ''),
					[fs_cc] = CONVERT(NUMERIC(35,0),ISNULL(e.fn_silinder, 0)),
					[fs_thn] = ISNULL(e.fd_thnpembuatan, 0),
					[fs_kd_warna] = ISNULL(e.fs_kd_warna, ''),
					[fs_nm_warna] = ISNULL(f.fs_nm_vareable, ''),
					a.fs_kd_wh, a.fs_nm_wh,
					a.fs_kd_salesmtd, a.fs_nm_salesmtd,
					a.fs_kd_payment, a.fs_nm_payment,
					[fs_kd_acno] = ISNULL(c.fs_acno, ''),
					[fs_nm_acno] = ISNULL(g.fs_nm_acno, ''),
					[fn_unitprice] = ISNULL(c.fn_otr, 0),
					[fn_dpmax] = ISNULL(c.fn_dpmax, 0),
					[fn_disc] = ISNULL(c.fn_discount, 0),
					a.fs_kd_dp, a.fs_nm_dp, [fn_dp] = a.fn_dpamt,
					a.fn_subsidi, [fn_install] = ISNULL(c.fn_instamt, 0),
					fn_total = ISNULL(c.fn_otr, 0) - ISNULL(c.fn_discount, 0) - a.fn_dpamt,
					[fs_refnobeli] = ISNULL(e.fs_refno, ''),
					[fs_register] = ISNULL(e.fs_register, ''),
					[fs_kd_cust] = LTRIM(RTRIM(a.fs_kd_cussup)), [fs_count_cust] = LTRIM(RTRIM(a.fs_countcussup)),
					[fs_nm_qq] = a.fs_qqname, [fs_addr_qq] = a.fs_qqaddr,
					[fn_dpp] = a.fn_netbframt, [fn_dpptax] = a.fn_netaftramt,
					Cabang = ISNULL((	SELECT	TOP 1 x.fs_nm_code
										FROM 	tm_addr x (NOLOCK)
										WHERE 	x.fs_kd_comp = a.fs_kd_comp
											AND x.fs_cdtyp = '03'
											AND x.fs_code = a.fs_kd_dept
											AND x.fs_count = a.fs_count
						), ''),
					AlamatCabang = ISNULL((	SELECT	TOP 1 x.fs_addr
											FROM 	tm_addr x (NOLOCK)
											WHERE	x.fs_kd_comp = a.fs_kd_comp
												AND x.fs_cdtyp = '03'
												AND x.fs_code = a.fs_kd_dept
												AND x.fs_count = a.fs_count
						), ''),
					fs_refno_dp = ISNULL((	SELECT	TOP 1 x.fs_refno
											FROM	tx_cbdetail x (NOLOCK)
											WHERE	x.fs_kd_comp = a.fs_kd_comp
												AND	x.fs_kd_refnot = a.fs_refno
												AND	x.fs_kd_trx = '5300'
											ORDER BY x.fs_refno DESC
						), '')
			FROM	tx_posheader a (NOLOCK)
			LEFT JOIN 	tm_addr b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
					AND	b.fs_cdtyp = CASE a.fs_kd_trx WHEN 'BL' THEN '01' ELSE '02' END
					AND	a.fs_kd_cussup = b.fs_code
					AND	a.fs_countcussup = b.fs_count
			LEFT JOIN	tm_posregsold c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
					AND	a.fs_kd_dept = c.fs_kd_dept
					AND	a.fs_count = c.fs_count
					AND	a.fs_refno = c.fs_refno
					AND	a.fs_kd_trx = c.fs_kd_trx
			LEFT JOIN	tm_term d (NOLOCK) ON a.fs_kd_comp = d.fs_kd_comp
					AND	a.fs_kd_term = d.fs_kd_term
			LEFT JOIN	tm_icregister e (NOLOCK) ON c.fs_chasis = e.fs_rangka
					AND	c.fs_machine = e.fs_machine
			LEFT JOIN	tm_vareable f (NOLOCK) ON e.fs_kd_comp = f.fs_kd_comp
					AND f.fs_key = '08'
					AND	e.fs_kd_warna = f.fs_kd_vareable
			LEFT JOIN	tm_acno g (NOLOCK) ON c.fs_acno = g.fs_kd_acno
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fb_spearpart = '".trim($this->session->userdata('gSparePart'))."'
				
		");
		// AND MONTH(a.fd_refno) <= MONTH(GETDATE())

		if (trim($bCash) == '1')
		{
			$xSQL = $xSQL.("
				AND a.fs_kd_payment IN ('0','1')
			");
		}
		
		if (trim($bAll) == '0')
		{
			$xSQL = $xSQL.("
				AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
			");
		}
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sCari)."%'
					OR a.fs_docno LIKE '%".trim($sCari)."%'
					OR b.fs_nm_code LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sRefno) <> '' or trim($sDocno) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sRefno)."%'
					OR a.fs_docno LIKE '%".trim($sDocno)."%'
					OR b.fs_nm_code LIKE '%".trim($sCust)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fd_usrcrt DESC
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function jual_unit($sTrx,$sRefno,$sDocno,$sCust,$bCash,$bAll,$nStart,$nTotal,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nTotal." - ".$nStart." - 24
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temprefno".$xUser.$xIP."%' )
					DROP TABLE #temprefno".$xUser.$xIP."
					
			SELECT	n = IDENTITY(INT, 1, 1), [fs_kd_cussup] = LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)),
					[fs_nm_cussup] = ISNULL(b.fs_nm_code, ''),
					[fs_addr] = ISNULL(b.fs_addr, ''),
					[fs_idcard] = ISNULL(b.fs_idcard, ''), fs_phone1 fs_tlp,
					a.fs_refno,
					[fd_refno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105),
					a.fs_docno, a.fb_edit,
					[fd_docno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_docno, 105), 105),
					[fs_kd_sales] = ISNULL(c.fs_kd_sales, ''),
					[fs_nm_sales] = ISNULL(c.fs_nm_sales, ''),
					[fs_kd_surveyor] = ISNULL(c.fs_kd_surveyor, ''),
					[fs_nm_surveyor] = ISNULL(c.fs_nm_surveyor, ''),
					a.fs_kd_term, [fs_nm_term] = ISNULL(d.fs_nm_term, ''),
					[fs_kd_product] = ISNULL(c.fs_kd_product, ''),
					[fs_nm_product] = ISNULL(c.fs_nm_product, ''),
					[fs_rangka] = ISNULL(c.fs_chasis, ''),
					[fs_mesin] = ISNULL(c.fs_machine, ''),
					[fs_cc] = CONVERT(NUMERIC(35,0),ISNULL(e.fn_silinder, 0)),
					[fs_thn] = ISNULL(e.fd_thnpembuatan, 0),
					[fs_kd_warna] = ISNULL(e.fs_kd_warna, ''),
					[fs_nm_warna] = ISNULL(f.fs_nm_vareable, ''),
					a.fs_kd_wh, a.fs_nm_wh,
					a.fs_kd_salesmtd, a.fs_nm_salesmtd,
					a.fs_kd_payment, a.fs_nm_payment,
					[fs_kd_acno] = ISNULL(c.fs_acno, ''),
					[fs_nm_acno] = ISNULL(g.fs_nm_acno, ''),
					[fn_unitprice] = ISNULL(c.fn_otr, 0),
					[fn_dpmax] = ISNULL(c.fn_dpmax, 0),
					[fn_disc] = ISNULL(c.fn_discount, 0),
					a.fs_kd_dp, a.fs_nm_dp, [fn_dp] = a.fn_dpamt,
					a.fn_subsidi, [fn_install] = ISNULL(c.fn_instamt, 0),
					fn_total = ISNULL(c.fn_otr, 0) - ISNULL(c.fn_discount, 0) - a.fn_dpamt,
					[fs_refnobeli] = ISNULL(e.fs_refno, ''),
					[fs_register] = ISNULL(e.fs_register, ''),
					[fs_kd_cust] = LTRIM(RTRIM(a.fs_kd_cussup)), [fs_count_cust] = LTRIM(RTRIM(a.fs_countcussup)),
					[fs_nm_qq] = a.fs_qqname, [fs_addr_qq] = a.fs_qqaddr,
					[fn_dpp] = a.fn_netbframt, [fn_dpptax] = a.fn_netaftramt,
					Cabang = ISNULL((	SELECT	TOP 1 x.fs_nm_code
										FROM 	tm_addr x (NOLOCK)
										WHERE 	x.fs_kd_comp = a.fs_kd_comp
											AND x.fs_cdtyp = '03'
											AND x.fs_code = a.fs_kd_dept
											AND x.fs_count = a.fs_count
						), ''),
					AlamatCabang = ISNULL((	SELECT	TOP 1 x.fs_addr
											FROM 	tm_addr x (NOLOCK)
											WHERE	x.fs_kd_comp = a.fs_kd_comp
												AND x.fs_cdtyp = '03'
												AND x.fs_code = a.fs_kd_dept
												AND x.fs_count = a.fs_count
						), ''),
					fs_refno_dp = ISNULL((	SELECT	TOP 1 x.fs_refno
											FROM	tx_cbdetail x (NOLOCK)
											WHERE	x.fs_kd_comp = a.fs_kd_comp
												AND	x.fs_kd_refnot = a.fs_refno
												AND	x.fs_kd_trx = '5300'
											ORDER BY x.fs_refno DESC
						), '')
			INTO	#temprefno".$xUser.$xIP."
			FROM 	tx_posheader a (NOLOCK)
			LEFT JOIN 	tm_addr b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
					AND	b.fs_cdtyp = CASE a.fs_kd_trx WHEN 'BL' THEN '01' ELSE '02' END
					AND	a.fs_kd_cussup = b.fs_code
					AND	a.fs_countcussup = b.fs_count
			LEFT JOIN	tm_posregsold c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
					AND	a.fs_kd_dept = c.fs_kd_dept
					AND	a.fs_count = c.fs_count
					AND	a.fs_refno = c.fs_refno
					AND	a.fs_kd_trx = c.fs_kd_trx
			LEFT JOIN	tm_term d (NOLOCK) ON a.fs_kd_comp = d.fs_kd_comp
					AND	a.fs_kd_term = d.fs_kd_term
			LEFT JOIN	tm_icregister e (NOLOCK) ON c.fs_chasis = e.fs_rangka
					AND	c.fs_machine = e.fs_machine
			LEFT JOIN	tm_vareable f (NOLOCK) ON e.fs_kd_comp = f.fs_kd_comp
					AND f.fs_key = '08'
					AND	e.fs_kd_warna = f.fs_kd_vareable
			LEFT JOIN	tm_acno g (NOLOCK) ON c.fs_acno = g.fs_kd_acno
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fb_spearpart = '".trim($this->session->userdata('gSparePart'))."'
				
		");
		//AND MONTH(a.fd_refno) = MONTH(GETDATE())
		
		if (trim($bCash) == '1')
		{
			$xSQL = $xSQL.("
				AND a.fs_kd_payment IN ('0','1')
			");
		}
		
		if (trim($bAll) == '0')
		{
			$xSQL = $xSQL.("
				AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
			");
		}
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sCari)."%'
					OR a.fs_docno LIKE '%".trim($sCari)."%'
					OR b.fs_nm_code LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sRefno) <> '' or trim($sDocno) <> '' or trim($sCust))
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sRefno)."%'
					OR a.fs_docno LIKE '%".trim($sDocno)."%'
					OR b.fs_nm_code LIKE '%".trim($sCust)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fd_usrcrt DESC
		");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temprefno".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			ORDER BY n DESC
			
			DROP TABLE #temprefno".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}

	function jurnal($sRefno)
	{
		$xSQL = ("
			SELECT b.fs_acno,
			d = CASE b.fs_dbcr WHEN 'D' THEN b.fn_ftrxamt ELSE 0 END,
			c = CASE b.fs_dbcr WHEN 'C' THEN b.fn_ftrxamt ELSE 0 END
			FROM tx_posheader a 
			LEFT JOIN tx_actheaderdt b ON b.fs_refno = a.fs_refno
			WHERE a.fs_refno = '".trim($sRefno)."'
		");

		$xSQL = $xSQL.("
			ORDER BY b.fs_dbcr DESC
		");

		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}

	function total($sRefno)
	{
		$xSQL = ("
			SELECT
			d = SUM(CASE b.fs_dbcr WHEN 'D' THEN b.fn_ftrxamt ELSE 0 END),
			c = SUM(CASE b.fs_dbcr WHEN 'C' THEN b.fn_ftrxamt ELSE 0 END)
			FROM tx_posheader a 
			LEFT JOIN tx_actheaderdt b ON b.fs_refno = a.fs_refno
			WHERE a.fs_refno = '".trim($sRefno)."'
		");

		$sSQL = $this->db->query($xSQL);
		return $sSQL->row();
	}

}
?>