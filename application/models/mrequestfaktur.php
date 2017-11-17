<?php

class MRequestFaktur extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	function cek_refno($sTrx,$ssTrx,$sRefno)
	{
		$xSQL = ("
			SELECT	TOP 1 fs_refno
			FROM 	tx_trxrequestfaktur (NOLOCK)
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_trx = '".trim($sTrx)."'
				AND fs_kd_strx = '".trim($ssTrx)."'
				AND fs_refno = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}

	function grid($sRefno,$sTrx,$ssTrx)
	{
		$xSQL = ("
			SELECT	a.fs_refnosi fs_refnojual, CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105) fd_refno,
					a.fs_kd_cussup fs_kd_cust,
					a.fs_countcussup fs_count,
					ISNULL(c.fs_nm_code, '') fs_nm_cust,
					a.fs_chasis fs_rangka, a.fs_engine fs_mesin,
					a.fn_bbn, a.fn_jasa fn_servis, a.fs_seqno,
					ISNULL(b.fs_nm_pay, '') fs_note,
					a.fs_nm_stnk_qq, a.fs_almt_stnk_qq,
					a.fs_nm_bpkb_qq, a.fs_almt_bpkb_qq
			FROM	tx_TrxRequestFakturD a (NOLOCK)
			INNER JOIN tm_posregsold b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_refnoSI = b.fs_refno
				AND a.fs_chasis = b.fs_chasis
				AND	a.fs_engine = b.fs_machine
			INNER JOIN tm_addr c (NOLOCK) ON c.fs_kd_comp = a.fs_kd_comp
				AND c.fs_cdtyp = '02'
				AND c.fs_code = a.fs_kd_cussup
				AND	c.fs_count = a.fs_countcussup
			INNER JOIN tm_icregister d (NOLOCK) ON a.fs_kd_comp = d.fs_kd_comp
				AND a.fs_chasis = d.fs_rangka
				AND	a.fs_engine = d.fs_machine
			WHERE a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				-- AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				-- AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fs_kd_strx = '".trim($ssTrx)."'
				AND a.fs_refno = '".trim($sRefno)."'
			ORDER BY a.fs_seqno
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}

	function faktur_request_all($sRefno,$sTrx,$ssTrx,$sCust,$sRangka,$sMesin,$sCari)
	{
		$xSQL = ("
			SELECT a.fs_refno, fd_refno = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105),
					[fs_nm_cust] = ISNULL(d.fs_nm_code, ' '), [fs_rangka] = ISNULL(b.fs_chasis, ' '),
					[fs_mesin] = b.fs_engine, a.fs_docno,
					[fd_docno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_docno, 105), 105),
					[fs_alamat] = ISNULL(fs_addr, ' '), [fs_tlp] = ISNULL(fs_phone1, ' '),
					b.fd_usrcrt fd_usrcrt,
			CASE b.fs_flag WHEN 1 THEN 'PENGAJUAN' WHEN 2 THEN 'MASIH PROSES' WHEN 3 THEN 'SUDAH SELESAI' ELSE '' END fs_status
			FROM   tx_trxrequestfaktur a (NOLOCK)
			LEFT JOIN  tx_trxrequestfakturd b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_dept = b.fs_kd_dept
				AND a.fs_count = b.fs_count
				AND a.fs_kd_trx = b.fs_kd_trx
				AND a.fs_kd_strx = b.fs_kd_strx
				AND a.fs_refno = b.fs_refno
			INNER JOIN tm_ADDr d (NOLOCK) ON b.fs_kd_comp = d.fs_kd_comp
				AND b.fs_kd_cussup = d.fs_code
				AND b.fs_countcussup = d.fs_count
				AND d.fs_cdtyp = '02'
			WHERE  a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				-- AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				-- AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fs_kd_strx = '".trim($ssTrx)."'
				AND a.fb_delete = '0'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sCari)."%'
					OR d.fs_nm_code LIKE '%".trim($sCari)."%'
					OR b.fs_chasis LIKE '%".trim($sCari)."%'
					OR b.fs_engine LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sRefno) <> '' or trim($sCust) <> '' or trim($sRangka) <> '' or trim($sMesin) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sRefno)."%'
					OR d.fs_nm_code LIKE '%".trim($sCust)."%'
					OR b.fs_chasis LIKE '%".trim($sRangka)."%'
					OR b.fs_engine LIKE '%".trim($sMesin)."%')
			");
		}
		$xSQL = $xSQL.("
			ORDER BY b.fd_usrcrt DESC");
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}


	function faktur_request($sRefno,$sTrx,$ssTrx,$sCust,$sRangka,$sMesin,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temprequest".$xUser.$xIP."%' )
					DROP TABLE #temprequest".$xUser.$xIP."
			
			SELECT n = IDENTITY(INT, 1, 1), a.fs_refno, fd_refno = CONVERT(VARCHAR, CONVERT(DATETIME, a.fd_refno, 105), 105),
					[fs_nm_cust] = ISNULL(d.fs_nm_code, ' '), [fs_rangka] = ISNULL(b.fs_chasis, ' '),
					[fs_mesin] = b.fs_engine, a.fs_docno,
					[fd_docno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_docno, 105), 105),
					[fs_alamat] = ISNULL(fs_addr, ' '), [fs_tlp] = ISNULL(fs_phone1, ' '),
					b.fd_usrcrt fd_usrcrt,
					--[fs_status] = ISNULL(fs_flag, ' '),
			CASE b.fs_flag WHEN '1' THEN 'PENGAJUAN' WHEN '2' THEN 'MASIH PROSES' WHEN '3' THEN 'SUDAH SELESAI' ELSE '' END fs_status
			INTO	#temprequest".$xUser.$xIP."
			FROM   tx_trxrequestfaktur a (NOLOCK)
			LEFT JOIN  tx_trxrequestfakturd b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_dept = b.fs_kd_dept
				AND a.fs_count = b.fs_count
				AND a.fs_kd_trx = b.fs_kd_trx
				AND a.fs_kd_strx = b.fs_kd_strx
				AND a.fs_refno = b.fs_refno
			INNER JOIN tm_ADDr d (NOLOCK) ON b.fs_kd_comp = d.fs_kd_comp
				AND b.fs_kd_cussup = d.fs_code
				AND b.fs_countcussup = d.fs_count
				AND d.fs_cdtyp = '02'
			WHERE  a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				-- AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				-- AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fs_kd_strx = '".trim($ssTrx)."'
				AND a.fb_delete = '0'
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sCari)."%'
					OR d.fs_nm_code LIKE '%".trim($sCari)."%'
					OR b.fs_chasis LIKE '%".trim($sCari)."%'
					OR b.fs_engine LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sRefno) <> '' or trim($sCust) <> '' or trim($sRangka) <> '' or trim($sMesin) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sRefno)."%'
					OR d.fs_nm_code LIKE '%".trim($sCust)."%'
					OR b.fs_chasis LIKE '%".trim($sRangka)."%'
					OR b.fs_engine LIKE '%".trim($sMesin)."%')
			");
		}
		$xSQL = $xSQL.("
			ORDER BY b.fd_usrcrt DESC");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temprequest".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temprequest".$xUser.$xIP);
			
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}

	function grid2_all($sCari)
	{
		$xSQL = ("
			SELECT	CONVERT(BIT, 0) fb_cek, ISNULL(a.fs_refno, '') fs_refnojual,
					CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105) fd_refno,
					a.fs_kd_cussup fs_kd_cust,
					a.fs_countcussup fs_count,
					ISNULL(d.fs_nm_code, '') fs_nm_cust,
					a.fs_chasis fs_rangka, a.fs_machine fs_mesin,
					'01-01-3000' fd_stnk, ' ' fs_stnk, ' ' fs_stnk_status,
					'01-01-3000' fd_bpkb, ' ' fs_bpkb, ' ' fs_bpkb_status,
					'0' fn_bbn, '0' fn_servis, ISNULL(a.fs_nm_pay, '') fs_note,
					' ' AS fs_seqno
			FROM	tm_posregsold a (NOLOCK)
			INNER JOIN tx_posheader b (NOLOCK) ON a.fs_kd_dept = b.fs_kd_dept
				AND a.fs_count = b.fs_count
				AND a.fs_kd_trx = b.fs_kd_trx
				AND a.fs_refno = b.fs_refno
			INNER JOIN tm_icregister c (NOLOCK) ON a.fs_chasis = c.fs_rangka
				AND a.fs_machine = c.fs_machine
				AND a.fs_refno = c.fs_refnoINV
				AND a.fs_kd_dept = c.fs_kd_DeptINV
				AND a.fs_count = c.fs_countDeptINV
				AND a.fs_kd_trx = c.fs_kd_trxINV
			INNER JOIN tm_addr d (NOLOCK) ON d.fs_kd_comp = a.fs_kd_comp
				AND d.fs_cdtyp = '02'
				AND d.fs_code = a.fs_kd_cussup
				AND d.fs_count = a.fs_countcussup
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND b.fs_kd_comp = b.fs_kd_comp
				AND b.fs_kd_salesmtd <> '01'
				AND LEFT(d.fs_code, 2) = '".trim($this->session->userdata('gWilayah'))."'
				AND a.fs_machine NOT IN (
					SELECT fs_engine
					FROM tx_TrxRequestFakturD
				)
		");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (d.fs_nm_code LIKE '%".trim($sCari)."%'
					OR a.fs_chasis LIKE '%".trim($sCari)."%'
					OR a.fs_machine LIKE '%".trim($sCari)."%'
					OR a.fs_nm_pay LIKE  '%".trim($sCari)."%')
			");
		}
		
		$xSQL =	$xSQL.("
			ORDER BY d.fs_nm_code
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid2($sCari,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
        $xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempdet".$xUser.$xIP."%' )
					DROP TABLE #tempdet".$xUser.$xIP."
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempdet2".$xUser.$xIP."%' )
					DROP TABLE #tempdet2".$xUser.$xIP."
			
			SELECT	CONVERT(BIT, 0) fb_cek, ISNULL(a.fs_refno, '') fs_refnojual,
					CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105) fd_refno,
					a.fs_kd_cussup fs_kd_cust,
					a.fs_countcussup fs_count,
					ISNULL(d.fs_nm_code, '') fs_nm_cust,
					a.fs_chasis fs_rangka, a.fs_machine fs_mesin,
					'01-01-3000' fd_stnk, ' ' fs_stnk, ' ' fs_stnk_status,
					'01-01-3000' fd_bpkb, ' ' fs_bpkb, ' ' fs_bpkb_status,
					'0' fn_bbn, '0' fn_servis, ISNULL(a.fs_nm_pay, '') fs_note,
					' ' AS fs_seqno
			INTO	#tempdet".$xUser.$xIP."
			FROM	tm_posregsold a (NOLOCK)
			INNER JOIN tx_posheader b (NOLOCK) ON a.fs_kd_dept = b.fs_kd_dept
				AND a.fs_count = b.fs_count
				AND a.fs_kd_trx = b.fs_kd_trx
				AND a.fs_refno = b.fs_refno
			INNER JOIN tm_icregister c (NOLOCK) ON a.fs_chasis = c.fs_rangka
				AND a.fs_machine = c.fs_machine
				AND a.fs_refno = c.fs_refnoINV
				AND a.fs_kd_dept = c.fs_kd_DeptINV
				AND a.fs_count = c.fs_countDeptINV
				AND a.fs_kd_trx = c.fs_kd_trxINV
			INNER JOIN tm_addr d (NOLOCK) ON d.fs_kd_comp = a.fs_kd_comp
				AND d.fs_cdtyp = '02'
				AND d.fs_code = a.fs_kd_cussup
				AND d.fs_count = a.fs_countcussup
			WHERE a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND b.fs_kd_comp = b.fs_kd_comp
				AND b.fs_kd_salesmtd <> '01'
				AND LEFT(d.fs_code, 2) = '".trim($this->session->userdata('gWilayah'))."'
				AND a.fs_machine NOT IN (
					SELECT fs_engine
					FROM tx_TrxRequestFakturD
				)
		");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (d.fs_nm_code LIKE '%".trim($sCari)."%'
					OR a.fs_chasis LIKE '%".trim($sCari)."%'
					OR a.fs_machine LIKE '%".trim($sCari)."%'
					OR a.fs_nm_pay LIKE  '%".trim($sCari)."%')
			");
		}
		
		$xSQL =	$xSQL.("
			ORDER BY d.fs_nm_code
		");
		
		$xSQL =	$xSQL.("
			SELECT	n = IDENTITY(INT, 1, 1), *
			INTO	#tempdet2".$xUser.$xIP."
			FROM	#tempdet".$xUser.$xIP."
			
			SELECT 	* FROM #tempdet2".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempdet".$xUser.$xIP."
			DROP TABLE #tempdet2".$xUser.$xIP
		);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}

	function hapus_isi($sTrx,$ssTrx,$sRefno)
	{
		$xSQL = ("
			UPDATE	tm_icregister SET
					fs_stnk = '', fd_stnk = '',
					fs_bpkb = '', fd_bpkb = ''
			FROM	tm_icregister a (NOLOCK)
			INNER JOIN tx_trxrequestfakturd b (NOLOCK) ON a.fs_rangka = b.fs_chasis
				AND	a.fs_machine = b.fs_engine
			WHERE	b.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	b.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND	b.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND	b.fs_kd_trx = '".trim($sTrx)."'
				AND	b.fs_kd_strx = '".trim($ssTrx)."'
				AND	b.fs_refno = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}

	function update_isi($sTrx,$ssTrx,$sRefno)
	{
		$xSQL = ("
			UPDATE	tx_trxrequestfakturd SET
					fs_kd_dept = a.fs_kd_dept,
					fs_count = a.fs_count
			FROM	tx_trxrequestfaktur a (NOLOCK)
			INNER JOIN tx_trxrequestfakturd b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_refno = b.fs_refno
			WHERE	a.fs_refno = '".trim($sRefno)."'
			
			UPDATE	tm_icregister SET
					fs_stnk = b.fs_stnk, fd_stnk = b.fd_rcvdt,
					fs_bpkb = b.fs_bpkb, fd_bpkb = b.fd_rcvdt2
			FROM	tm_icregister a (NOLOCK)
			INNER JOIN tx_trxrequestfakturd b (NOLOCK) ON a.fs_rangka = b.fs_chasis
				AND	a.fs_machine = b.fs_engine
			WHERE	b.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	b.fs_kd_trx = '".trim($sTrx)."'
				AND	b.fs_kd_strx = '".trim($ssTrx)."'
				AND	b.fs_refno = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}

}