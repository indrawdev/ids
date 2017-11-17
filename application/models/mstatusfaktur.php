<?php

class MStatusFaktur extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	function custlist_all($sCari)
	{
		$xSQL = ("
			SELECT  DISTINCT
				c.fs_kd_cussup fs_kd_cust,
				c.fs_countCusSup fs_count,
				e.fs_nm_code fs_nm_cust,
				e.fs_addr fs_alamat,
				ISNULL(d.fs_nm_product, '') fs_nm_product,
				b.fs_chasis fs_rangka,
				b.fs_engine fs_mesin,
				b.fd_usrcrt fd_usrcrt

			FROM tx_TrxRequestFaktur a (NOLOCK)
			INNER JOIN tx_TrxRequestFakturD b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
						AND a.fs_kd_dept = b.fs_kd_dept
						AND a.fs_count = b.fs_count
						AND a.fs_kd_trx = b.fs_kd_trx
						AND a.fs_kd_strx = b.fs_kd_strx
						AND a.fs_refno = b.fs_refno
			INNER JOIN tm_posregsold c (NOLOCK) ON b.fs_chasis = c.fs_chasis
						AND b.fs_engine = c.fs_machine
						AND a.fs_kd_comp = c.fs_kd_comp
			LEFT JOIN tm_product d (NOLOCK) ON c.fs_kd_comp = d.fs_kd_comp
						AND c.fs_kd_product = d.fs_kd_product
			LEFT JOIN tm_addr e (NOLOCK) ON c.fs_kd_comp = e.fs_kd_comp
				AND b.fs_kd_cussup = e.fs_code
				AND b.fs_countcussup = e.fs_count
				AND e.fs_cdtyp = '02'
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			AND b.fs_flag IN('1','2')
		");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (e.fs_nm_code LIKE '%".trim($sCari)."%'
					OR b.fs_chasis LIKE '%".trim($sCari)."%'
					OR b.fs_engine LIKE '%".trim($sCari)."%')
			");
		}
		$xSQL = $xSQL.("
			ORDER BY fd_usrcrt DESC
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}

	function custlist($sCari,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempstatus".$xUser.$xIP."%' )
					DROP TABLE #tempstatus".$xUser.$xIP."
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempstatus2".$xUser.$xIP."%' )
					DROP TABLE #tempstatus2".$xUser.$xIP."
			
			SELECT  DISTINCT
				c.fs_kd_cussup fs_kd_cust,
				c.fs_countCusSup fs_count,
				e.fs_nm_code fs_nm_cust,
				e.fs_addr fs_alamat,
				ISNULL(d.fs_nm_product, '') fs_nm_product,
				b.fs_chasis fs_rangka,
				b.fs_engine fs_mesin,
				b.fd_usrcrt fd_usrcrt
			INTO	#tempstatus".$xUser.$xIP."
			FROM tx_TrxRequestFaktur a (NOLOCK)
			INNER JOIN tx_TrxRequestFakturD b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_dept = b.fs_kd_dept
				AND a.fs_count = b.fs_count
				AND a.fs_kd_trx = b.fs_kd_trx
				AND a.fs_kd_strx = b.fs_kd_strx
				AND a.fs_refno = b.fs_refno
			INNER JOIN tm_posregsold c (NOLOCK) ON b.fs_chasis = c.fs_chasis
				AND b.fs_engine = c.fs_machine
				AND a.fs_kd_comp = c.fs_kd_comp
			LEFT JOIN tm_product d (NOLOCK) ON c.fs_kd_comp = d.fs_kd_comp
				AND c.fs_kd_product = d.fs_kd_product
			LEFT JOIN tm_addr e (NOLOCK) ON c.fs_kd_comp = e.fs_kd_comp
				AND b.fs_kd_cussup = e.fs_code
				AND b.fs_countcussup = e.fs_count
				AND e.fs_cdtyp = '02'
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			AND b.fs_flag IN('1','2')
		");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (e.fs_nm_code LIKE '%".trim($sCari)."%'
					OR b.fs_chasis LIKE '%".trim($sCari)."%'
					OR b.fs_engine LIKE '%".trim($sCari)."%')
			");
		}
		$xSQL = $xSQL.("
			ORDER BY fd_usrcrt DESC
		");
		
		$xSQL =	$xSQL.("
			SELECT 	n = IDENTITY(INT, 1, 1), *
			INTO	#tempstatus2".$xUser.$xIP."
			FROM #tempstatus".$xUser.$xIP."
			
			SELECT	* FROM #tempstatus2".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempstatus".$xUser.$xIP."
			DROP TABLE #tempstatus2".$xUser.$xIP);
		
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
			WHERE a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."''
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fs_kd_strx = '".trim($ssTrx)."'
				AND a.fs_refno = '".trim($sRefno)."'
				AND a.fs_flag != '1'
			ORDER BY a.fs_seqno
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}


	function faktur_request_all($sRefno,$sTrx,$ssTrx,$sCust,$sRangka,$sMesin)
	{
		$xSQL = ("
			SELECT a.fs_refno, fd_refno = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105),
					[fs_nm_cust] = ISNULL(d.fs_nm_code, ' '), [fs_rangka] = ISNULL(b.fs_chasis, ' '),
					[fs_mesin] = b.fs_engine, a.fs_docno,
					[fd_docno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_docno, 105), 105),
					[fs_alamat] = ISNULL(fs_addr, ' '), [fs_tlp] = ISNULL(fs_phone1, ' '),
					b.fd_upddt fd_upddt,
			CASE b.fs_flag WHEN '1' THEN 'PENGAJUAN' WHEN '2' THEN 'MASIH PROSES' WHEN '3' THEN 'SUDAH SELESAI' ELSE '' END fs_status
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
				AND b.fs_flag IN('2','3')
				AND a.fb_delete = '0'
			");
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
			ORDER BY b.fd_upddt DESC");
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}


	function faktur_request($sRefno,$sTrx,$ssTrx,$sCust,$sRangka,$sMesin,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempstatus".$xUser.$xIP."%' )
					DROP TABLE #tempstatus".$xUser.$xIP."
			
			SELECT n = IDENTITY(INT, 1, 1), a.fs_refno, fd_refno = CONVERT(VARCHAR, CONVERT(DATETIME, a.fd_refno, 105), 105),
					[fs_nm_cust] = ISNULL(d.fs_nm_code, ' '), [fs_rangka] = ISNULL(b.fs_chasis, ' '),
					[fs_mesin] = b.fs_engine, a.fs_docno,
					[fd_docno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_docno, 105), 105),
					[fs_alamat] = ISNULL(fs_addr, ' '), [fs_tlp] = ISNULL(fs_phone1, ' '),
					b.fd_upddt fd_upddt,
			CASE b.fs_flag WHEN '1' THEN 'PENGAJUAN' WHEN '2' THEN 'MASIH PROSES' WHEN '3' THEN 'SUDAH SELESAI' ELSE '' END fs_status
			INTO	#tempstatus".$xUser.$xIP."
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
				AND b.fs_flag IN('2','3')
				AND a.fb_delete = '0'
			");
		
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
			ORDER BY b.fd_upddt DESC");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempstatus".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempstatus".$xUser.$xIP);
			
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}