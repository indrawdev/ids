<?php

class MSearch extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function accgl_acno_all($sAcno,$sAcnoNm,$sCari)
	{
        $xSQL = ("
			SELECT  LTRIM(RTRIM(fs_kd_acno)) fs_kd_acno, ISNULL(fs_nm_acno, '') fs_nm_acno
            FROM 	tm_acno (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	LTRIM(RTRIM(fs_kd_acno)) <> ''
		");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_acno LIKE '%".trim($sCari)."%'
					OR fs_nm_acno LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sAcno) <> '' or trim($sAcnoNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_acno LIKE '%".trim($sAcno)."%'
					OR fs_nm_acno LIKE '%".trim($sAcnoNm)."%')
			");
		}
		$xSQL = $xSQL.("
			ORDER BY fs_kd_acno");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function accgl_acno($sAcno,$sAcnoNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
        $xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temp".$xUser.$xIP."%' )
					DROP TABLE #temp".$xUser.$xIP."
			
			SELECT  n = IDENTITY(INT, 1, 1), LTRIM(RTRIM(fs_kd_acno)) fs_kd_acno, ISNULL(fs_nm_acno, '') fs_nm_acno
			INTO	#temp".$xUser.$xIP."
            FROM 	tm_acno (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	LTRIM(RTRIM(fs_kd_acno)) <> ''
		");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_acno LIKE '%".trim($sCari)."%'
					OR fs_nm_acno LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sAcno) <> '' or trim($sAcnoNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_acno LIKE '%".trim($sAcno)."%'
					OR fs_nm_acno LIKE '%".trim($sAcnoNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_acno");
		
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temp".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temp".$xUser.$xIP);
			
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function accgl_acno2($sAcno,$sAcnoNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
        $xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temp2".$xUser.$xIP."%' )
					DROP TABLE #temp2".$xUser.$xIP."
			
			SELECT  n = IDENTITY(INT, 1, 1), LTRIM(RTRIM(fs_kd_acno)) fs_kd_acno, ISNULL(fs_nm_acno, '') fs_nm_acno
			INTO	#temp2".$xUser.$xIP."
            FROM 	tm_acno (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	LTRIM(RTRIM(fs_kd_acno)) <> ''
		");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_acno LIKE '%".trim($sCari)."%'
					OR fs_nm_acno LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sAcno) <> '' or trim($sAcnoNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_acno LIKE '%".trim($sAcno)."%'
					OR fs_nm_acno LIKE '%".trim($sAcnoNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_acno");
		
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temp2".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temp2".$xUser.$xIP);
			
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function acno_grup_all($sKel,$sKelNm,$sCari)
	{
        $xSQL = ("
			SELECT  LTRIM(RTRIM(a.fs_kd_group)) fs_kd_group, ISNULL(a.fs_nm_group, '') fs_nm_group,
					a.fs_kd_acno, ISNULL(b.fs_nm_acno, '') fs_nm_acno
            FROM 	tm_grpacno a (NOLOCK)
			LEFT JOIN tm_acno b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_acno = b.fs_kd_acno
            WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	LTRIM(RTRIM(a.fs_kd_group)) <> ''
		");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_group LIKE '%".trim($sCari)."%'
					OR a.fs_nm_group LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sKel) <> '' or trim($sKelNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_group LIKE '%".trim($sKel)."%'
					OR a.fs_nm_group LIKE '%".trim($sKelNm)."%')
			");
		}
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_group, a.fs_nm_group
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function acno_grup($sKel,$sKelNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
        $xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempkel".$xUser.$xIP."%' )
					DROP TABLE #tempkel".$xUser.$xIP."
			
			SELECT  n = IDENTITY(INT, 1, 1), LTRIM(RTRIM(a.fs_kd_group)) fs_kd_group, ISNULL(a.fs_nm_group, '') fs_nm_group,
					a.fs_kd_acno, ISNULL(b.fs_nm_acno, '') fs_nm_acno
			INTO	#tempkel".$xUser.$xIP."
            FROM 	tm_grpacno a (NOLOCK)
			LEFT JOIN tm_acno b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_acno = b.fs_kd_acno
            WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	LTRIM(RTRIM(a.fs_kd_group)) <> ''
		");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_group LIKE '%".trim($sCari)."%'
					OR a.fs_nm_group LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sKel) <> '' or trim($sKelNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_group LIKE '%".trim($sKel)."%'
					OR a.fs_nm_group LIKE '%".trim($sKelNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_group, a.fs_nm_group
		");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempkel".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempkel".$xUser.$xIP);
			
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function agen_all($sAgen,$sAgenNm,$sCari)
	{
        $xSQL = ("
			SELECT 	[fs_kd_agen] = fs_kd_birojs, [fs_nm_agen] = fs_nm_birojs,
					fs_alamat, fs_tlp
            FROM 	tm_masterbirojs (NOLOCK)
            WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fb_active = '1'
				AND	LTRIM(RTRIM(fs_kd_birojs)) <> ''
			");
		
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_birojs LIKE '%".trim($sCari)."%'
					OR fs_nm_birojs LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sAgen) <> '' or trim($sAgenNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_birojs LIKE '%".trim($sAgen)."%'
					OR fs_nm_birojs LIKE '%".trim($sAgenNm)."%')
			");
		}
		$xSQL = $xSQL.("
			ORDER BY fs_kd_birojs");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function agen($sAgen,$sAgenNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
        $xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempagen".$xUser.$xIP."%' )
					DROP TABLE #tempagen".$xUser.$xIP."
			
			SELECT  n = IDENTITY(INT, 1, 1), [fs_kd_agen] = fs_kd_birojs, [fs_nm_agen] = fs_nm_birojs,
					fs_alamat, fs_tlp
			INTO	#tempagen".$xUser.$xIP."
            FROM 	tm_masterbirojs (NOLOCK)
            WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fb_active = '1'
				AND	LTRIM(RTRIM(fs_kd_birojs)) <> ''
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_birojs LIKE '%".trim($sCari)."%'
					OR fs_nm_birojs LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sAgen) <> '' or trim($sAgenNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_birojs LIKE '%".trim($sAgen)."%'
					OR fs_nm_birojs LIKE '%".trim($sAgenNm)."%')
			");
		}
		$xSQL = $xSQL.("
			ORDER BY fs_kd_birojs");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempagen".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempagen".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function agen2_all($sAgen,$sAgenNm,$sCari)
	{
        $xSQL = ("
			SELECT 	[fs_kd_agen] = fs_kd_birojs, [fs_nm_agen] = fs_nm_birojs,
					fs_alamat, fs_tlp,
					fb_aktif = CASE fb_active WHEN '1' THEN 'true' ELSE 'false' END,
					fs_status = CASE fb_active WHEN '1' THEN 'ACTIVE' ELSE 'NON ACTIVE' END
            FROM 	tm_masterbirojs (NOLOCK)
            WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	LTRIM(RTRIM(fs_kd_birojs)) <> ''
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_birojs LIKE '%".trim($sCari)."%'
					OR fs_nm_birojs LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sAgen) <> '' or trim($sAgenNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_birojs LIKE '%".trim($sAgen)."%'
					OR fs_nm_birojs LIKE '%".trim($sAgenNm)."%')
			");
		}

		$xSQL = $xSQL.("
			ORDER BY fs_kd_birojs");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function agen2($sAgen,$sAgenNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
        $xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempagen".$xUser.$xIP."%' )
					DROP TABLE #tempagen".$xUser.$xIP."
			
			SELECT  n = IDENTITY(INT, 1, 1), [fs_kd_agen] = fs_kd_birojs, [fs_nm_agen] = fs_nm_birojs,
					fs_alamat, fs_tlp,
					fb_aktif = CASE fb_active WHEN '1' THEN 'true' ELSE 'false' END,
					fs_status = CASE fb_active WHEN '1' THEN 'ACTIVE' ELSE 'NON ACTIVE' END
			INTO	#tempagen".$xUser.$xIP."
            FROM 	tm_masterbirojs (NOLOCK)
            WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	LTRIM(RTRIM(fs_kd_birojs)) <> ''
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_birojs LIKE '%".trim($sCari)."%'
					OR fs_nm_birojs LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sAgen) <> '' or trim($sAgenNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_birojs LIKE '%".trim($sAgen)."%'
					OR fs_nm_birojs LIKE '%".trim($sAgenNm)."%')
			");
		}
		$xSQL = $xSQL.("
			ORDER BY fs_kd_birojs");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempagen".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempagen".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function agen_servis_all($sRefno,$sTrx,$ssTrx,$sCust,$sRangka,$sMesin,$sCari)
	{
        $xSQL = ("
			SELECT a.fs_refno, fd_refno = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105),
					[fs_nm_cust] = ISNULL(d.fs_nm_code, ' '), [fs_rangka] = ISNULL(b.fs_chasis, ' '),
					[fs_mesin] = b.fs_engine, a.fs_docno,
					[fd_docno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_docno, 105), 105),
					[fs_kd_agen] = a.fs_kd_agent, [fs_nm_agen] = ISNULL(fs_nm_birojs, ' '),
					[fs_alamat] = ISNULL(fs_alamat, ' '), [fs_tlp] = ISNULL(fs_tlp, ' ')
			FROM   tx_trxbirojs a (NOLOCK)
			LEFT JOIN  tx_trxbirojsd b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_dept = b.fs_kd_dept
				AND a.fs_count = b.fs_count
				AND a.fs_kd_trx = b.fs_kd_trx
				AND a.fs_kd_strx = b.fs_kd_strx
				AND a.fs_refno = b.fs_refno
			LEFT JOIN  tm_masterbirojs c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND a.fs_kd_agent = c.fs_kd_birojs
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
				AND c.fb_active = 1
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
			ORDER BY a.fs_refno DESC");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function agen_servis($sRefno,$sTrx,$ssTrx,$sCust,$sRangka,$sMesin,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
        $xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempagen".$xUser.$xIP."%' )
					DROP TABLE #tempagen".$xUser.$xIP."
			
			SELECT n = IDENTITY(INT, 1, 1), a.fs_refno, fd_refno = CONVERT(VARCHAR, CONVERT(DATETIME, a.fd_refno, 105), 105),
					[fs_nm_cust] = ISNULL(d.fs_nm_code, ' '), [fs_rangka] = ISNULL(b.fs_chasis, ' '),
					[fs_mesin] = b.fs_engine, a.fs_docno,
					[fd_docno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_docno, 105), 105),
					[fs_kd_agen] = a.fs_kd_agent, [fs_nm_agen] = ISNULL(fs_nm_birojs, ' '),
					[fs_alamat] = ISNULL(fs_alamat, ' '), [fs_tlp] = ISNULL(fs_tlp, ' ')
			INTO	#tempagen".$xUser.$xIP."
			FROM   tx_trxbirojs a (NOLOCK)
			LEFT JOIN  tx_trxbirojsd b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_dept = b.fs_kd_dept
				AND a.fs_count = b.fs_count
				AND a.fs_kd_trx = b.fs_kd_trx
				AND a.fs_kd_strx = b.fs_kd_strx
				AND a.fs_refno = b.fs_refno
			LEFT JOIN  tm_masterbirojs c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND a.fs_kd_agent = c.fs_kd_birojs
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
				AND c.fb_active = 1
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
			ORDER BY a.fs_refno DESC");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempagen".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempagen".$xUser.$xIP);
			
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function beli_do_all($sTrx,$sRefno,$sDocno,$sCari)
	{
		$xSQL = ("
			SELECT	fs_kd_suppcount = LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)),
					LTRIM(RTRIM(a.fs_kd_cussup)) fs_kd_supp, LTRIM(RTRIM(a.fs_countcussup)) fs_kd_count,
					ISNULL(b.fs_nm_code, '') fs_nm_supplier,
					a.fs_refno, CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105) fd_refno,
					a.fs_docno, CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_docno, 105), 105) fd_docno
			FROM	tx_posheader a
			LEFT JOIN tm_addr b ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_cussup = b.fs_code
				AND a.fs_countcussup = b.fs_count
				AND b.fs_cdtyp = '01'
			WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND ltrim(rtrim(a.fs_invno)) = ''
		");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sCari)."%'
					OR a.fs_docno LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sRefno) <> '' or trim($sDocno) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sRefno)."%'
					OR a.fs_docno LIKE '%".trim($sDocno)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_refno DESC");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function beli_do($sTrx,$sRefno,$sDocno,$nStart,$nTotal,$sCari)
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
					
			SELECT	n = IDENTITY(INT, 1, 1),
					fs_kd_suppcount = LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)),
					LTRIM(RTRIM(a.fs_kd_cussup)) fs_kd_supp, LTRIM(RTRIM(a.fs_countcussup)) fs_kd_count,
					ISNULL(b.fs_nm_code, '') fs_nm_supplier,
					a.fs_refno, CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105) fd_refno,
					a.fs_docno, CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_docno, 105), 105) fd_docno
			INTO 	#temprefno".$xUser.$xIP."
			FROM	tx_posheader a
			LEFT JOIN tm_addr b ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_cussup = b.fs_code
				AND a.fs_countcussup = b.fs_count
				AND b.fs_cdtyp = '01'
			WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND ltrim(rtrim(a.fs_invno)) = ''
		");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sCari)."%'
					OR a.fs_docno LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sRefno) <> '' or trim($sDocno) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sRefno)."%'
					OR a.fs_docno LIKE '%".trim($sDocno)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_refno");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temprefno".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			ORDER BY n DESC
			
			DROP TABLE #temprefno".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function beli_do2_all($sTrx,$sRefno,$sDocno,$sCari)
	{
		$xSQL = ("
			SELECT	fs_kd_suppcount = LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)),
					LTRIM(RTRIM(a.fs_kd_cussup)) fs_kd_supp, LTRIM(RTRIM(a.fs_countcussup)) fs_kd_count,
					ISNULL(b.fs_nm_code, '') fs_nm_supplier,
					a.fs_refno, CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105) fd_refno,
					a.fs_docno, CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_docno, 105), 105) fd_docno
			FROM	tx_posheader a (NOLOCK)
			LEFT JOIN tm_addr b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_cussup = b.fs_code
				AND a.fs_countcussup = b.fs_count
				AND b.fs_cdtyp = '01'
			WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fs_refno NOT IN (
					SELECT	x.fs_kd_order
					FROM	tx_posdetail x (NOLOCK)
				)
		");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sCari)."%'
					OR a.fs_docno LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sRefno) <> '' or trim($sDocno) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sRefno)."%'
					OR a.fs_docno LIKE '%".trim($sDocno)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_refno DESC");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function beli_do2($sTrx,$sRefno,$sDocno,$nStart,$nTotal,$sCari)
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
					
			SELECT	n = IDENTITY(INT, 1, 1),
					fs_kd_suppcount = LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)),
					LTRIM(RTRIM(a.fs_kd_cussup)) fs_kd_supp, LTRIM(RTRIM(a.fs_countcussup)) fs_kd_count,
					ISNULL(b.fs_nm_code, '') fs_nm_supplier,
					a.fs_refno, CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105) fd_refno,
					a.fs_docno, CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_docno, 105), 105) fd_docno
			INTO 	#temprefno".$xUser.$xIP."
			FROM	tx_posheader a (NOLOCK)
			LEFT JOIN tm_addr b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_cussup = b.fs_code
				AND a.fs_countcussup = b.fs_count
				AND b.fs_cdtyp = '01'
			WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fs_refno NOT IN (
					SELECT	x.fs_kd_order
					FROM	tx_posdetail x (NOLOCK)
				)
		");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sCari)."%'
					OR a.fs_docno LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sRefno) <> '' or trim($sDocno) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sRefno)."%'
					OR a.fs_docno LIKE '%".trim($sDocno)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_refno");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temprefno".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			ORDER BY n DESC
			
			DROP TABLE #temprefno".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function beli_mtd_all($sCode,$sName)
	{
		$xSQL = ("
			SELECT	fs_kd_vareable, fs_nm_vareable
            FROM 	tm_vareable (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_key = '96'
			");
			
		if (trim($sCode) <> '' or trim($sName) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sCode)."%'
					OR fs_nm_vareable LIKE '%".trim($sName)."%')
				");
		}
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function beli_mtd($sCode,$sName,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempbelimtd".$xUser.$xIP."%' )
					DROP TABLE #tempbelimtd".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_vareable, fs_nm_vareable
			INTO	#tempbelimtd".$xUser.$xIP."
            FROM 	tm_vareable (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_key = '96'
			");
			
		if (trim($sCode) <> '' or trim($sName) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sCode)."%'
					OR fs_nm_vareable LIKE '%".trim($sName)."%')
				");
		}
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempbelimtd".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempbelimtd".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function beli_unit_all($sTrx,$sRefno,$sDocno,$sInv,$bInv,$sCari)
	{
		$xSQL = ("
			SELECT	a.fs_refno,
					[fd_refno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105),
					a.fs_docno,
					[fd_docno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_docno, 105), 105),
					a.fs_invno,
					[fd_invno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_invno, 105), 105),
					[fs_kd_suppcount] = LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)),
					[fs_nm_supplier] = ISNULL(b.fs_nm_code, ''),
					[fs_kd_supp] = LTRIM(RTRIM(a.fs_kd_cussup)), [fs_count_supp] = LTRIM(RTRIM(a.fs_countcussup)),
					a.fs_kd_term, [fs_nm_term] = ISNULL(c.fs_nm_term, ''),
					a.fs_kd_salesmtd, a.fs_nm_salesmtd,
					[fn_total_price] = a.fn_grsamt,
					[fn_total_disc] = a.fn_dscamt,
					[fn_disc] = a.fn_sdscamt,
					[fn_total] = a.fn_netbframt,
					[fn_ppn] = a.fn_taxamt,
					[fs_kd_otax] = a.fs_kd_otax,
					[fs_nm_otax] = a.fs_nm_otax,
					[fn_otax_persen] = a.fn_otaxpct,
					[fn_otax_total] = a.fn_otaxamt,
					[fn_gtotal] = a.fn_trxamt
			FROM 	tx_posheader a (NOLOCK)
			LEFT JOIN 	tm_addr b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	b.fs_cdtyp = CASE a.fs_kd_trx WHEN 'BL' THEN '01' ELSE '02' END
				AND	a.fs_kd_cussup = b.fs_code
				AND	a.fs_countcussup = b.fs_count
			LEFT JOIN	tm_term c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND	a.fs_kd_term = c.fs_kd_term
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fb_spearpart = '".trim($this->session->userdata('gSparePart'))."'
		");
		
		if (trim($bInv) == '0')
		{
			$xSQL = $xSQL.("
					AND a.fs_refno IN (
						SELECT	DISTINCT x.fs_refno
						FROM	tx_posdetail x
						WHERE	x.fs_refno = a.fs_refno
							AND	LTRIM(RTRIM(x.fs_kd_order)) = ''
					)
			");
		}
		else
		{
			$xSQL = $xSQL.("
					AND a.fs_refno IN (
						SELECT	DISTINCT x.fs_refno
						FROM	tx_posdetail x
						WHERE	x.fs_refno = a.fs_refno
							AND	LTRIM(RTRIM(x.fs_kd_order)) <> ''
					)
			");
		}

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sCari)."%'
					OR a.fs_docno LIKE '%".trim($sCari)."%'
					OR a.fs_invno LIKE '%".trim($sCari)."%')");

		}

		if (trim($sRefno) <> '' or trim($sDocno) <> '' or trim($sInv))
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sRefno)."%'
					OR a.fs_docno LIKE '%".trim($sDocno)."%'
					OR a.fs_invno LIKE '%".trim($sInv)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_refno DESC");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function beli_unit($sTrx,$sRefno,$sDocno,$sInv,$bInv,$nStart,$nTotal,$sCari)
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
					
			SELECT	n = IDENTITY(INT, 1, 1), a.fs_refno,
					[fd_refno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105),
					a.fs_docno,
					[fd_docno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_docno, 105), 105),
					a.fs_invno,
					[fd_invno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_invno, 105), 105),
					[fs_kd_suppcount] = LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)),
					[fs_nm_supplier] = ISNULL(b.fs_nm_code, ''),
					[fs_kd_supp] = LTRIM(RTRIM(a.fs_kd_cussup)), [fs_count_supp] = LTRIM(RTRIM(a.fs_countcussup)),
					a.fs_kd_term, [fs_nm_term] = ISNULL(c.fs_nm_term, ''),
					a.fs_kd_salesmtd, a.fs_nm_salesmtd,
					[fn_total_price] = a.fn_grsamt,
					[fn_total_disc] = a.fn_dscamt,
					[fn_disc] = a.fn_sdscamt,
					[fn_total] = a.fn_netbframt,
					[fn_ppn] = a.fn_taxamt,
					[fs_kd_otax] = a.fs_kd_otax,
					[fs_nm_otax] = a.fs_nm_otax,
					[fn_otax_persen] = a.fn_otaxpct,
					[fn_otax_total] = a.fn_otaxamt,
					[fn_gtotal] = a.fn_trxamt
			INTO	#temprefno".$xUser.$xIP."
			FROM 	tx_posheader a (NOLOCK)
			LEFT JOIN 	tm_addr b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	b.fs_cdtyp = CASE a.fs_kd_trx WHEN 'BL' THEN '01' ELSE '02' END
				AND	a.fs_kd_cussup = b.fs_code
				AND	a.fs_countcussup = b.fs_count
			LEFT JOIN	tm_term c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND	a.fs_kd_term = c.fs_kd_term
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fb_spearpart = '".trim($this->session->userdata('gSparePart'))."'
		");
		
		if (trim($bInv) == '0')
		{
			$xSQL = $xSQL.("
					AND a.fs_refno IN (
						SELECT	DISTINCT x.fs_refno
						FROM	tx_posdetail x
						WHERE	x.fs_refno = a.fs_refno
							AND	LTRIM(RTRIM(x.fs_kd_order)) = ''
					)
			");
		}
		else
		{
			$xSQL = $xSQL.("
					AND a.fs_refno IN (
						SELECT	DISTINCT x.fs_refno
						FROM	tx_posdetail x
						WHERE	x.fs_refno = a.fs_refno
							AND	LTRIM(RTRIM(x.fs_kd_order)) <> ''
					)
			");
		}
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sCari)."%'
					OR a.fs_docno LIKE '%".trim($sCari)."%'
					OR a.fs_invno LIKE '%".trim($sCari)."%')");

		}

		if (trim($sRefno) <> '' or trim($sDocno) <> '' or trim($sInv))
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sRefno)."%'
					OR a.fs_docno LIKE '%".trim($sDocno)."%'
					OR a.fs_invno LIKE '%".trim($sInv)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_refno
		");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temprefno".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			ORDER BY n DESC
			
			DROP TABLE #temprefno".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function direct_sales_detail($sTrx,$sRefno,$sDocno)
	{
		$xSQL = ("
			SELECT	[fs_kd_cussup] = LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)),
					[fs_nm_cussup] = ISNULL(b.fs_nm_code, ''),
					[fs_addr] = ISNULL(b.fs_addr, ''),
					[fs_idcard] = ISNULL(b.fs_idcard, ''),
					a.fs_refno,
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
					HNetBfrtax = a.fn_netbframt,
					HTaxamt = a.fn_taxamt,
					HOtaxAmt = a.fn_otaxamt,
					HAddOn = a.fn_addonamt,
					Company=(SELECT TOP 1 xx.fs_nm_code FROM tm_addr xx(NOLOCK) 
						 WHERE xx.fs_kd_comp=a.fs_kd_comp 
						 AND xx.fs_cdtyp='00' 
						 AND xx.fs_code=a.fs_kd_comp)
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
					AND	f.fs_key = '08'
					AND	e.fs_kd_warna = f.fs_kd_vareable
			LEFT JOIN	tm_acno g (NOLOCK) ON c.fs_acno = g.fs_kd_acno
			LEFT JOIN	tx_posdetail h (NOLOCK) ON h.fs_refno = a.fs_refno
			
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fb_spearpart = '".trim($this->session->userdata('gSparePart'))."'
		");
		
		if (trim($sRefno) <> '' or trim($sDocno) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sRefno)."%'
					OR a.fs_docno LIKE '%".trim($sDocno)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_refno DESC");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cbin_acno_all($sAcno,$sAcnoNm)
	{
        $xSQL = ("
			SELECT 	DISTINCT a.fs_kd_acno, b.fs_nm_acno
			FROM 	tm_cbacno a(NOLOCK), tm_acno b(NOLOCK)
			WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_acno = b.fs_kd_acno
				AND a.fb_flag = '0'
			");
		
		if (trim($sAcno) <> '' or trim($sAcnoNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (b.fs_kd_acno LIKE '%".trim($sAcno)."%'
					OR b.fs_nm_acno LIKE '%".trim($sAcnoNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_acno");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cbin_acno($sAcno,$sAcnoNm,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
        $xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temp".$xUser.$xIP."%' )
					DROP TABLE #temp".$xUser.$xIP."
			
			SELECT 	DISTINCT n = IDENTITY(INT, 1, 1), a.fs_kd_acno, b.fs_nm_acno
			INTO	#temp".$xUser.$xIP."
			FROM 	tm_cbacno a(NOLOCK), tm_acno b(NOLOCK)
			WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_acno = b.fs_kd_acno
				AND a.fb_flag = '0'
			");
		
		if (trim($sAcno) <> '' or trim($sAcnoNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (b.fs_kd_acno LIKE '%".trim($sAcno)."%'
					OR b.fs_nm_acno LIKE '%".trim($sAcnoNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_acno");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temp".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temp".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cbin_refno_all($sDept,$sCount,$sTrx,$ssTrx,$sRefno)
	{
		$xSQL = ("
			SELECT	ta.fs_refno, ta.fs_kd_dept, ta.fs_count,
					[fs_nm_dept] = ba.fs_nm_code, fs_nm_strx = ISNULL(xa.fs_nm_strx, ''),
					ta.fs_doc, fd_refno = CONVERT(VARCHAR(10), CONVERT(DATETIME, ta.fd_refno, 105), 105),
					fs_kd_status = CASE WHEN ta.fs_trsta = '7' THEN '0' ELSE '1' END,
					fs_nm_status = CASE WHEN ta.fs_trsta = '7' THEN 'APPROVED' ELSE 'DRAFT' END,
					ta.fs_descrp, fd_doc = CONVERT(VARCHAR(10), CONVERT(DATETIME, ta.fd_doc, 105), 105),
					[fs_code] = LTRIM(RTRIM(ta.fs_kd_dept)) + LTRIM(RTRIM(ta.fs_count)),
					[fs_kd_strx] = LTRIM(RTRIM(ta.fs_kd_trx)) + LTRIM(RTRIM(ta.fs_kd_strx)),
					ta.fs_cekgiro
            FROM	tx_actheader ta(NOLOCK), tm_ADDr ba (NOLOCK), tm_strx xa (NOLOCK)
            WHERE 	ta.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND ta.fs_kd_trx = '".trim($sTrx)."'
				AND ta.fs_kd_strx = '".trim($ssTrx)."'
				AND ta.fs_kd_dept = ba.fs_code
				AND ba.fs_cdtyp = '03'
				AND ta.fs_count = ba.fs_count
				AND ba.fs_code = '".trim($sDept)."'
				AND ba.fs_count = '".trim($sCount)."'
				AND ta.fs_kd_trx = xa.fs_kd_trx
				AND ta.fs_kd_strx = xa.fs_kd_strx
			");
		
		if (trim($sRefno) <> '')
		{
			$xSQL = $xSQL.("
				AND	ta.fs_refno LIKE '%".trim($sRefno)."%'");
		}
		
		$xSQL =	$xSQL.("
			ORDER BY ta.fd_usrcrt DESC");
			
        $sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cbin_refno($sDept,$sCount,$sTrx,$ssTrx,$sRefno,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temp".$xUser.$xIP."%' )
					DROP TABLE #temp".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), ta.fs_refno, ta.fs_kd_dept, ta.fs_count,
					[fs_nm_dept] = ba.fs_nm_code, fs_nm_strx = ISNULL(xa.fs_nm_strx, ''),
					ta.fs_doc, fd_refno = CONVERT(VARCHAR(10), CONVERT(DATETIME, ta.fd_refno, 105), 105),
					fs_kd_status = CASE WHEN ta.fs_trsta = '7' THEN '0' ELSE '1' END,
					fs_nm_status = CASE WHEN ta.fs_trsta = '7' THEN 'APPROVED' ELSE 'DRAFT' END,
					ta.fs_descrp, fd_doc = CONVERT(VARCHAR(10), CONVERT(DATETIME, ta.fd_doc, 105), 105),
					[fs_code] = LTRIM(RTRIM(ta.fs_kd_dept)) + LTRIM(RTRIM(ta.fs_count)),
					[fs_kd_strx] = LTRIM(RTRIM(ta.fs_kd_trx)) + LTRIM(RTRIM(ta.fs_kd_strx)),
					ta.fs_cekgiro
			INTO	#temp".$xUser.$xIP."
            FROM	tx_actheader ta(NOLOCK), tm_ADDr ba (NOLOCK), tm_strx xa (NOLOCK)
            WHERE 	ta.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND ta.fs_kd_trx = '".trim($sTrx)."'
				AND ta.fs_kd_strx = '".trim($ssTrx)."'
				AND ta.fs_kd_dept = ba.fs_code
				AND ba.fs_cdtyp = '03'
				AND ta.fs_count = ba.fs_count
				AND ba.fs_code = '".trim($sDept)."'
				AND ba.fs_count = '".trim($sCount)."'
				AND ta.fs_kd_trx = xa.fs_kd_trx
				AND ta.fs_kd_strx = xa.fs_kd_strx
			");
		
		if (trim($sRefno) <> '')
		{
			$xSQL = $xSQL.("
				AND	ta.fs_refno LIKE '%".trim($sRefno)."%'");
		}
		
		$xSQL =	$xSQL.("
			ORDER BY ta.fd_usrcrt DESC");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temp".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temp".$xUser.$xIP);
			
        $sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cbtrs_type($sTrx)
	{
		$xSQL = ("
			SELECT	[fs_kd_strx] = ISNULL(LTRIM(RTRIM(fs_kd_trx)) + LTRIM(RTRIM(fs_kd_strx)), ''),
					[fs_nm_strx] = fs_nm_strx
			FROM 	tm_strx (NOLOCK)
			WHERE 	fs_kd_trx = '".trim($sTrx)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cbtrs_type2_all($sStrx,$sCari)
	{
		$xSQL = ("
			SELECT	fs_kd_strx = LTRIM(RTRIM(b.fs_kd_trx)) + LTRIM(RTRIM(b.fs_kd_strx)),
					a.fs_nm_trx fs_nm_strx, b.fs_kd_trx, b.fs_kd_strx fs_kd_sstrx
			FROM 	tm_trx a (NOLOCK)
			INNER JOIN tm_strx b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_trx = b.fs_kd_trx
			WHERE 	a.fs_kd_trx IN ('5000','5300')
		");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND	(a.fs_kd_trx LIKE '%".trim($sCari)."%' 
					OR a.fs_nm_trx LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sStrx) <> '')
		{
			$xSQL = $xSQL.("
				AND	a.fs_kd_trx = '".trim($sStrx)."'
			");
		}
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cbtrs_type2($sStrx,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
			@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temptrs".$xUser.$xIP."%' )
					DROP TABLE #temptrs".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_strx = LTRIM(RTRIM(b.fs_kd_trx)) + LTRIM(RTRIM(b.fs_kd_strx)),
					a.fs_nm_trx fs_nm_strx, b.fs_kd_trx, b.fs_kd_strx fs_kd_sstrx
			INTO	#temptrs".$xUser.$xIP."
			FROM 	tm_trx a (NOLOCK)
			INNER JOIN tm_strx b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_trx = b.fs_kd_trx
			WHERE 	a.fs_kd_trx IN ('5000','5300')
		");
		
		if (trim($sStrx) <> '')
		{
			$xSQL = $xSQL.("
				AND	a.fs_kd_trx = '".trim($sStrx)."'
			");
		}
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temptrs".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temptrs".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cb_trs_all($sTrx,$sStrx,$sCust,$sRefno,$bAll,$sCari)
	{
		$xSQL = ("
			SELECT	a.fs_refno, fd_refno = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105),
					b.fs_kd_refnot fs_refno_jual, a.fs_kd_cussup fs_kd_cust, a.fs_countcussup fs_cust_count,
					ISNULL(d.fs_nm_code, '') fs_nm_cust, ISNULL(c.fn_rmnamt, 0) + b.fn_trxamtt fn_sisa,
					b.fn_trxamtt fn_total, a.fs_note fs_ket,
					fb_edit = CASE e.fb_close WHEN 1 THEN 0 ELSE 1 END
			FROM	tx_cbheader a (NOLOCK)
			INNER JOIN tx_cbdetail b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_dept = b.fs_kd_dept
				AND	a.fs_count = b.fs_count
				AND	a.fs_kd_trx = b.fs_kd_trx
				AND	a.fs_refno = b.fs_refno
			INNER JOIN tx_posheader c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND	b.fs_kd_refnot = c.fs_refno
			LEFT JOIN tm_addr d (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_cussup = d.fs_code
				AND	a.fs_countcussup = d.fs_count
				AND	d.fs_cdtyp = '02'
			INNER JOIN tx_actheader e (NOLOCK) ON a.fs_kd_comp = e.fs_kd_comp
				AND	a.fs_kd_dept = e.fs_kd_dept
				AND	a.fs_count = e.fs_count
				AND	a.fs_kd_trx = e.fs_kd_trx
				AND	a.fs_refno = e.fs_refno
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fs_kd_strx = '".trim($sStrx)."'
				AND a.fb_delete = '0'
		");
		
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
					OR d.fs_nm_code LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sRefno) <> '' or trim($sCust) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sRefno)."%'
					OR d.fs_nm_code LIKE '%".trim($sCust)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_refno
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cb_trs($sTrx,$sStrx,$sCust,$sRefno,$bAll,$nStart,$nTotal,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nTotal." - ".$nStart." - 24
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temptrscb".$xUser.$xIP."%' )
					DROP TABLE #temptrscb".$xUser.$xIP."
					
			SELECT	n = IDENTITY(INT, 1, 1), a.fs_refno, fd_refno = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105),
					b.fs_kd_refnot fs_refno_jual, a.fs_kd_cussup fs_kd_cust, a.fs_countcussup fs_cust_count,
					ISNULL(d.fs_nm_code, '') fs_nm_cust, ISNULL(c.fn_rmnamt, 0) + b.fn_trxamtt fn_sisa,
					b.fn_trxamtt fn_total, a.fs_note fs_ket,
					fb_edit = CASE e.fb_close WHEN 1 THEN 0 ELSE 1 END
			INTO	#temptrscb".$xUser.$xIP."
			FROM	tx_cbheader a (NOLOCK)
			INNER JOIN tx_cbdetail b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_dept = b.fs_kd_dept
				AND	a.fs_count = b.fs_count
				AND	a.fs_kd_trx = b.fs_kd_trx
				AND	a.fs_refno = b.fs_refno
			INNER JOIN tx_posheader c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND	b.fs_kd_refnot = c.fs_refno
			LEFT JOIN tm_addr d (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_cussup = d.fs_code
				AND	a.fs_countcussup = d.fs_count
				AND	d.fs_cdtyp = '02'
			INNER JOIN tx_actheader e (NOLOCK) ON a.fs_kd_comp = e.fs_kd_comp
				AND	a.fs_kd_dept = e.fs_kd_dept
				AND	a.fs_count = e.fs_count
				AND	a.fs_kd_trx = e.fs_kd_trx
				AND	a.fs_refno = e.fs_refno
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fs_kd_strx = '".trim($sStrx)."'
				AND a.fb_delete = '0'
		");
		
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
					OR d.fs_nm_code LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sRefno) <> '' or trim($sCust) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sRefno)."%'
					OR d.fs_nm_code LIKE '%".trim($sCust)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_refno
		");
		
		$xSQL = $xSQL.("
			SELECT 	* FROM #temptrscb".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			ORDER BY n DESC
			
			DROP TABLE #temptrscb".$xUser.$xIP
		);
			
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function comp_impor_all()
	{
		$xSQL = ("
			SELECT	a.fs_kd_comp, a.fs_nm_db, a.fs_nm_comp
			FROM	tx_mutasidb a (NOLOCK)
			INNER JOIN tx_mutasidbd b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_refno = b.fs_refno
			WHERE	a.fs_kd_tcomp = '".trim($this->session->userdata('gComp'))."'
				AND	a.fs_kd_tdept = '".trim($this->session->userdata('gDept'))."'
				AND	a.fs_tcount = '".trim($this->session->userdata('gCount'))."'
				AND	LTRIM(RTRIM(b.fs_refnoin)) = ''
			GROUP BY a.fs_kd_comp, a.fs_nm_db, a.fs_nm_comp
			ORDER BY a.fs_kd_comp, a.fs_nm_db, a.fs_nm_comp
		");

		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function comp_impor($nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempcomp".$xUser.$xIP."%' )
					DROP TABLE #tempcomp".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), a.fs_kd_comp, a.fs_nm_db, a.fs_nm_comp
			INTO	#tempcomp".$xUser.$xIP."
			FROM	tx_mutasidb a (NOLOCK)
			INNER JOIN tx_mutasidbd b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_refno = b.fs_refno
			WHERE	a.fs_kd_tcomp = '".trim($this->session->userdata('gComp'))."'
				AND	a.fs_kd_tdept = '".trim($this->session->userdata('gDept'))."'
				AND	a.fs_tcount = '".trim($this->session->userdata('gCount'))."'
				AND	LTRIM(RTRIM(b.fs_refnoin)) = ''
			GROUP BY a.fs_kd_comp, a.fs_nm_db, a.fs_nm_comp
			ORDER BY a.fs_kd_comp, a.fs_nm_db, a.fs_nm_comp
		");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempcomp".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempcomp".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function change_comp_all($bFilter)
	{
		$xSQL = ("
			SELECT	fs_kd_comp = LTRIM(RTRIM(fs_kd_comp)), UPPER(LTRIM(RTRIM(fs_nm_comp))) fs_nm_comp,
					UPPER(LTRIM(RTRIM(fs_nm_db))) fs_nm_db
			FROM	tm_company (NOLOCK)
		");
		
		if (trim($bFilter) <> '')
		{
			$xSQL = $xSQL.("
				WHERE	LTRIM(RTRIM(fs_nm_db)) <> '".trim($this->session->userdata('gDatabase'))."'
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_db, fs_nm_comp
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function change_comp($bFilter,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temp".$xUser.$xIP."%' )
					DROP TABLE #temp".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_comp = LTRIM(RTRIM(fs_kd_comp)),
					UPPER(LTRIM(RTRIM(fs_nm_comp))) fs_nm_comp, UPPER(LTRIM(RTRIM(fs_nm_db))) fs_nm_db
			INTO	#temp".$xUser.$xIP."
			FROM	tm_company (NOLOCK)
		");
		
		if (trim($bFilter) <> '')
		{
			$xSQL = $xSQL.("
				WHERE	LTRIM(RTRIM(fs_nm_db)) <> '".trim($this->session->userdata('gDatabase'))."'
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_db, fs_nm_comp
		");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temp".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temp".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function color_all($sColorCd,$sColorNm)
	{
		$xSQL = ("
			SELECT 	fs_kd_vareable AS fs_kd_warna,
					fs_nm_vareable AS fs_nm_warna 
			FROM 	tm_vareable 
			WHERE 	fs_key='08'
            AND		fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");
		
		if (trim($sColorCd) <> '' or trim($sColorNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sColorCd)."%'
					OR fs_nm_vareable LIKE '%".trim($sColorNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_vareable");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function color($sProdCd,$sProdNm,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempcolor".$xUser.$xIP."%' )
					DROP TABLE #tempcolor".$xUser.$xIP."
			
			SELECT	DISTINCT n = IDENTITY(INT, 1, 1), fs_kd_vareable AS fs_kd_warna, fs_nm_vareable AS fs_nm_warna
			INTO	#tempcolor".$xUser.$xIP."
            FROM 	tm_vareable 
			WHERE 	fs_key='08'
            AND		fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");
		
		if (trim($sProdCd) <> '' or trim($sProdNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sProdCd)."%'
					OR fs_nm_vareable LIKE '%".trim($sProdNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_vareable");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempcolor".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempcolor".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cust_jual_all($sCustCd,$sCustNm,$sCari)
	{
		$xSQL = ("
			SELECT 	[fs_code] = ISNULL(LTRIM(RTRIM(a.fs_code)), '') + ISNULL(LTRIM(RTRIM(a.fs_count)), ''),
					[fs_kd_cust] = ISNULL(LTRIM(RTRIM(a.fs_code)), ''), [fs_count] = ISNULL(LTRIM(RTRIM(a.fs_count)), ''),
					[fs_nm_code] = a.fs_nm_code,
					a.fs_idcard, a.fs_addr, a.fs_phone1 fs_tlp
            FROM 	tm_addr a (NOLOCK)
			INNER JOIN tm_customer b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_cdtyp = '02'
                AND a.fs_code = b.fs_kd_customer
				AND a.fs_count = b.fs_count
			WHERE	b.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
                AND b.fb_active = '1'
				AND a.fs_code = '".trim($this->session->userdata('gGudang'))."'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(a.fs_code)) + LTRIM(RTRIM(a.fs_count)) LIKE '%".trim($sCari)."%'
					OR a.fs_nm_code LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sCustCd) <> '' or trim($sCustNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(a.fs_code)) + LTRIM(RTRIM(a.fs_count)) LIKE '%".trim($sCustCd)."%'
					OR a.fs_nm_code LIKE '%".trim($sCustNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_nm_code");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cust_jual($sCustCd,$sCustNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temp".$xUser.$xIP."%' )
					DROP TABLE #temp".$xUser.$xIP."
			
			SELECT 	n = IDENTITY(INT, 1, 1), [fs_code] = ISNULL(LTRIM(RTRIM(a.fs_code)), '') + ISNULL(LTRIM(RTRIM(a.fs_count)), ''),
					[fs_kd_cust] = ISNULL(LTRIM(RTRIM(a.fs_code)), ''), [fs_count] = ISNULL(LTRIM(RTRIM(a.fs_count)), ''),
					[fs_nm_code] = a.fs_nm_code,
					a.fs_idcard, a.fs_addr, a.fs_phone1 fs_tlp
			INTO	#temp".$xUser.$xIP."
			FROM 	tm_addr a (NOLOCK)
            INNER JOIN tm_customer b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_cdtyp = '02'
                AND a.fs_code = b.fs_kd_customer
				AND a.fs_count = b.fs_count
			WHERE	b.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
                AND b.fb_active = '1'
				AND a.fs_code = '".trim($this->session->userdata('gGudang'))."'
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(a.fs_code)) + LTRIM(RTRIM(a.fs_count)) LIKE '%".trim($sCari)."%'
					OR a.fs_nm_code LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sCustCd) <> '' or trim($sCustNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(a.fs_code)) + LTRIM(RTRIM(a.fs_count)) LIKE '%".trim($sCustCd)."%'
					OR a.fs_nm_code LIKE '%".trim($sCustNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_nm_code");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temp".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temp".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function dept_def($dept,$count)
	{
		$xSQL = ("
			SELECT 	TOP 1 a.fs_nm_code, ISNULL(fs_nm_vareable, '') fs_kota
			FROM 	tm_addr a (NOLOCK)
			LEFT JOIN tm_vareable b (NOLOCK) ON a.fs_kd_city = b.fs_kd_vareable
				AND b.fs_key = '14'
			WHERE 	a.fs_cdtyp = '03'
				AND	a.fs_code = '".$dept."'
				AND	a.fs_count = '".$count."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function dp_all($sCode,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		
		$xSQL = ("
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempdp".$xUser.$xIP."%' )
					DROP TABLE #tempdp".$xUser.$xIP."
					
			CREATE TABLE #tempdp".$xUser.$xIP."(
				fs_code		VARCHAR(25),
				fs_nm_code	VARCHAR(100),
				fn_dp		NUMERIC(35,0),
				fs_nm_dept	VARCHAR(50)
			)
			
			INSERT INTO	#tempdp".$xUser.$xIP."
			EXEC stp_DPBrow '".trim($this->session->userdata('gComp'))."', '".trim($sCode)."'
			SELECT 	* FROM #tempdp".$xUser.$xIP."
			WHERE	fn_dp > 0
				AND fs_code LIKE '".trim($this->session->userdata('gGudang'))."%'
		");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_code LIKE '%".trim($sCari)."%'
					OR fs_nm_code LIKE  '%".trim($sCari)."%')");
		}

		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function dp($sCode,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempdp".$xUser.$xIP."%' )
					DROP TABLE #tempdp".$xUser.$xIP."
					
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempdp2".$xUser.$xIP."%' )
					DROP TABLE #tempdp2".$xUser.$xIP."
					
			CREATE TABLE #tempdp".$xUser.$xIP."(
				fs_code		VARCHAR(25),
				fs_nm_code	VARCHAR(100),
				fn_dp		NUMERIC(35,0),
				fs_nm_dept	VARCHAR(50)
			)
			
			INSERT INTO	#tempdp".$xUser.$xIP."
			EXEC stp_DPBrow '".trim($this->session->userdata('gComp'))."', '".trim($sCode)."'
			
			SELECT 	n = IDENTITY(INT, 1, 1), fs_code, fs_nm_code, fn_dp, fs_nm_dept
			INTO	#tempdp2".$xUser.$xIP."
			FROM 	#tempdp".$xUser.$xIP."
			WHERE	fn_dp > 0
				AND fs_code LIKE '".trim($this->session->userdata('gGudang'))."%'
			
			SELECT 	* FROM #tempdp2".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempdp".$xUser.$xIP."
			DROP TABLE #tempdp2".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function dp_trs_all($sCustCd)
	{
		$xSQL = ("
			SELECT	[fs_refno] = tc.fs_refno, [fd_refno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, tc.fd_refno, 105), 105),
					[fs_kd_dept] = LTRIM(RTRIM(tc.fs_kd_dept)) + LTRIM(RTRIM(tc.fs_count)),
					[fs_nm_dept] = (	SELECT 	TOP 1 x.fs_nm_code
										FROM 	tm_addr x (NOLOCK)
										WHERE 	x.fs_kd_comp = tc.fs_kd_comp
											AND x.fS_cdtyp = '03'
											AND LTRIM(RTRIM(x.fs_code)) + LTRIM(RTRIM(x.fs_count)) = RTRIM(tc.fs_kd_dept) + LTRIM(RTRIM(tc.fs_count))),
					[fs_docno] = tc.fs_docno,
					[fs_kd_cust] = LTRIM(RTRIM(tc.fs_kd_cussup)) + LTRIM(RTRIM(tc.fs_countcussup)),
					tc.fs_note, [fn_amt] = tc.fn_amount
			FROM	tx_cbheader tc (NOLOCK)
            WHERE 	tc.fs_kd_comp='".trim($this->session->userdata('gComp'))."'
				AND tc.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND tc.fs_count='".trim($this->session->userdata('gCount'))."'
				AND tc.fs_kd_trx = '5300'
				AND tc.fs_kd_strx = '0100'
				AND tc.fb_delete = '0'
			");
		
		if (trim($sCustCd) <> '')
		{
			$xSQL = $xSQL.("
				AND LTRIM(RTRIM(tc.fs_kd_cussup)) + LTRIM(RTRIM(tc.fs_countcussup)) = '".trim($sCustCd)."'
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY tc.fs_refno
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function dp_trs($sCustCd,$nStart,$nTotal)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nTotal." - ".$nStart." - 24
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temptrsdp".$xUser.$xIP."%' )
					DROP TABLE #temptrsdp".$xUser.$xIP."
					
			SELECT	n = IDENTITY(INT, 1, 1), [fs_refno] = tc.fs_refno, [fd_refno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, tc.fd_refno, 105), 105),
					[fs_kd_dept] = LTRIM(RTRIM(tc.fs_kd_dept)) + LTRIM(RTRIM(tc.fs_count)),
					[fs_nm_dept] = (	SELECT 	TOP 1 x.fs_nm_code
										FROM 	tm_addr x (NOLOCK)
										WHERE 	x.fs_kd_comp = tc.fs_kd_comp
											AND x.fS_cdtyp = '03'
											AND LTRIM(RTRIM(x.fs_code)) + LTRIM(RTRIM(x.fs_count)) = RTRIM(tc.fs_kd_dept) + LTRIM(RTRIM(tc.fs_count))),
					[fs_docno] = tc.fs_docno,
					[fs_kd_cust] = LTRIM(RTRIM(tc.fs_kd_cussup)) + LTRIM(RTRIM(tc.fs_countcussup)),
					tc.fs_note, [fn_amt] = tc.fn_amount
			INTO	#temptrsdp".$xUser.$xIP."
			FROM	tx_cbheader tc (NOLOCK)
            WHERE 	tc.fs_kd_comp='".trim($this->session->userdata('gComp'))."'
				AND tc.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND tc.fs_count='".trim($this->session->userdata('gCount'))."'
				AND tc.fs_kd_trx = '5300'
				AND tc.fs_kd_strx = '0100'
				AND tc.fb_delete = '0'
			");
		
		if (trim($sCustCd) <> '')
		{
			$xSQL = $xSQL.("
				AND LTRIM(RTRIM(tc.fs_kd_cussup)) + LTRIM(RTRIM(tc.fs_countcussup)) = '".trim($sCustCd)."'
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY tc.fs_refno
		");
			
		$xSQL = $xSQL.("
			SELECT 	* FROM #temptrsdp".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			ORDER BY n DESC
			
			DROP TABLE #temptrsdp".$xUser.$xIP
		);
			
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function form_web($nm_form)
	{
		$sSQL = $this->db->query("
			SELECT	fs_nm_formweb
			FROM	tg_menu
			WHERE	fs_nm_menu = '".trim($nm_form)."'
			");
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
	
	function kasir_all($sRefno,$sCust)
	{
		$xSQL = ("
			SELECT	fs_refno, [fd_refno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, fd_refno, 105), 105),
					fn_pyamt fn_total, fs_descrp,
					fs_cust = CASE WHEN (SELECT CHARINDEX('\', fs_descrp, 1)) > 0 THEN SUBSTRING(fs_descrp, 1, CHARINDEX('\', fs_descrp, 0) - 1) ELSE '' END,
					fs_note = CASE WHEN (SELECT CHARINDEX('\', fs_descrp, 1)) > 0 THEN SUBSTRING(fs_descrp, CHARINDEX('\', fs_descrp, 0) + 1, LEN(fs_descrp) - 1) ELSE '' END,
					fb_in = CASE fb_in WHEN '1' THEN 'true' ELSE 'false' END,
					fs_status = CASE fb_in WHEN '1' THEN 'IN' ELSE 'OUT' END,
					fb_setor = CASE fb_setor WHEN '1' THEN 'true' ELSE 'false' END
			FROM 	tx_poskasir
			");
			
		if (trim($sRefno) <> '' or trim($sCust))
		{
			$xSQL = $xSQL.("
				WHERE (SELECT CHARINDEX('\', fs_descrp, 1)) > 0
				AND (fs_refno LIKE '%".trim($sRefno)."%'
					OR SUBSTRING(fs_descrp, 1, CHARINDEX('\', fs_descrp, 0) - 1) LIKE '%".trim($sCust)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_refno DESC");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kasir($sRefno,$sCust,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempkasir".$xUser.$xIP."%' )
					DROP TABLE #tempkasir".$xUser.$xIP."
					
			SELECT	n = IDENTITY(INT, 1, 1), fs_refno, [fd_refno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, fd_refno, 105), 105),
					fn_pyamt fn_total, fs_descrp,
					fs_cust = CASE WHEN (SELECT CHARINDEX('\', fs_descrp, 1)) > 0 THEN SUBSTRING(fs_descrp, 1, CHARINDEX('\', fs_descrp, 0) - 1) ELSE '' END,
					fs_note = CASE WHEN (SELECT CHARINDEX('\', fs_descrp, 1)) > 0 THEN SUBSTRING(fs_descrp, CHARINDEX('\', fs_descrp, 0) + 1, LEN(fs_descrp) - 1) ELSE '' END,
					fb_in = CASE fb_in WHEN '1' THEN 'true' ELSE 'false' END,
					fs_status = CASE fb_in WHEN '1' THEN 'IN' ELSE 'OUT' END,
					fb_setor = CASE fb_setor WHEN '1' THEN 'true' ELSE 'false' END
			INTO	#tempkasir".$xUser.$xIP."
			FROM 	tx_poskasir
			");
			
		if (trim($sRefno) <> '' or trim($sCust))
		{
			$xSQL = $xSQL.("
				WHERE (fs_refno LIKE '%".trim($sRefno)."%'
					OR fs_descrp LIKE '%".trim($sCust)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_refno DESC");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempkasir".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempkasir".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kendaraan($sKdKendaraan,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempkendaraan".$xUser.$xIP."%' )
					DROP TABLE #tempkendaraan".$xUser.$xIP."
			if (trim($xKdKendaraan) <> '') {
		
			
			SELECT 	n = IDENTITY(INT, 1, 1),aa.fs_kd_variable as fs_kd_kendaraan,aa.fs_nm_variable as fs_nm_kendaraan,bb.fn_harga,bb.fn_cc
				INTO #tempkendaraan".$xUser.$xIP."
				FROM tm_variableapk aa
				INNER JOIN tm_kendaraan bb ON bb.fs_kd_kendaraan = aa.fs_kd_variable
				
				");
			
		if (trim($sKdKendaraan) <> '')
		{
			$xSQL = $xSQL.("
				WHERE aa.fs_kd_variable LIKE '%" . $xKdKendaraan. "'%
				AND aa.fs_key='10'
				AND fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				");
		}else{
			$xSQL = $xSQL.("
				WHERE aa.fs_key='10'
				AND fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_kendaraan DESC");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempkendaraan".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempkendaraan".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodeacno_all($sAcno,$sAcnoNm,$sCari)
	{
        $xSQL = ("
			SELECT  LTRIM(RTRIM(fs_kd_acno)) fs_kd_acno, ISNULL(fs_nm_acno, '') fs_nm_acno,
					fb_aktif = CASE fb_active WHEN '1' THEN 'true' ELSE 'false' END,
					fs_status = CASE fb_active WHEN '1' THEN 'ACTIVE' ELSE 'NON ACTIVE' END
            FROM 	tm_acno (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	LTRIM(RTRIM(fs_kd_acno)) <> ''
		");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_acno LIKE '%".trim($sCari)."%'
					OR fs_nm_acno LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sAcno) <> '' or trim($sAcnoNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_acno LIKE '%".trim($sAcno)."%'
					OR fs_nm_acno LIKE '%".trim($sAcnoNm)."%')
			");
		}
		$xSQL = $xSQL.("
			ORDER BY fs_kd_acno
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodeacno($sAcno,$sAcnoNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
        $xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temp".$xUser.$xIP."%' )
					DROP TABLE #temp".$xUser.$xIP."
			
			SELECT  n = IDENTITY(INT, 1, 1), LTRIM(RTRIM(fs_kd_acno)) fs_kd_acno, ISNULL(fs_nm_acno, '') fs_nm_acno,
					fb_aktif = CASE fb_active WHEN '1' THEN 'true' ELSE 'false' END,
					fs_status = CASE fb_active WHEN '1' THEN 'ACTIVE' ELSE 'NON ACTIVE' END
			INTO	#temp".$xUser.$xIP."
            FROM 	tm_acno (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	LTRIM(RTRIM(fs_kd_acno)) <> ''
		");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_acno LIKE '%".trim($sCari)."%'
					OR fs_nm_acno LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sAcno) <> '' or trim($sAcnoNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_acno LIKE '%".trim($sAcno)."%'
					OR fs_nm_acno LIKE '%".trim($sAcnoNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_acno
		");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temp".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temp".$xUser.$xIP);
			
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodecode_all($sTipe,$sCode,$sCount,$sNama)
	{
		$xSQL = ("
			SELECT	LTRIM(RTRIM(a.fs_code)) fs_code, LTRIM(RTRIM(a.fs_count)) fs_count,
					LTRIM(RTRIM(a.fs_nm_code)) fs_nm_code, a.fs_addr fs_alamat,
					a.fs_cdtyp fs_kd_tipe, ISNULL(b.fs_nm_type, '') fs_nm_tipe,
					a.fs_kd_city fs_kd_kota, ISNULL(c.fs_nm_vareable, '') fs_nm_kota,
					a.fs_kd_district fs_kd_kab, ISNULL(d.fs_nm_vareable, '') fs_nm_kab,
					a.fs_kd_province fs_kd_prop, ISNULL(e.fs_nm_vareable, '') fs_nm_prop,
					a.fs_kd_country fs_kd_neg, ISNULL(f.fs_nm_vareable, '') fs_nm_neg,
					a.fs_kd_state fs_kd_wn, ISNULL(g.fs_nm_vareable, '') fs_nm_wn,
					a.fs_idcard fs_kd_id,
					fd_tgl_id = CASE a.fd_dateid
					WHEN '' THEN CONVERT(VARCHAR(10), CONVERT(DATETIME, GETDATE(), 105), 105)
					ELSE CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_dateid, 105), 105)
					END,
					fs_phone1 fs_tlp, fs_phone2 fs_tlp2
			FROM 	tm_addr a (NOLOCK)
			LEFT JOIN tm_codetype b (NOLOCK) ON a.fs_cdtyp = b.fs_kd_type
			LEFT JOIN tm_vareable c (NOLOCK) ON a.fs_kd_city = c.fs_kd_vareable
				AND	c.fs_key = '14'
			LEFT JOIN tm_vareable d (NOLOCK) ON a.fs_kd_district = d.fs_kd_vareable
				AND	d.fs_key = '05'
			LEFT JOIN tm_vareable e (NOLOCK) ON a.fs_kd_province = e.fs_kd_vareable
				AND	e.fs_key = '04'
			LEFT JOIN tm_vareable f (NOLOCK) ON a.fs_kd_country = f.fs_kd_vareable
				AND	f.fs_key = '02'
			LEFT JOIN tm_vareable g (NOLOCK) ON a.fs_kd_state = g.fs_kd_vareable
				AND	g.fs_key = '18'
            WHERE 	a.fs_cdtyp = '".trim($sTipe)."'
				AND	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
		");
		
		if (trim($sTipe) <> '03')
		{
			$xSQL = $xSQL.("
				AND a.fs_code = '".trim($this->session->userdata('gGudang'))."'
			");
		}
		
		if (trim($sCode) <> '')
		{
			$xSQL = $xSQL.("
				AND a.fs_code = '".trim($sCode)."'
			");
		}
		
		if (trim($sCount) <> '' or trim($sNama) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_count = '".trim($sCount)."'
				OR a.fs_nm_code = '".trim($sNama)."')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_code, a.fs_count, a.fs_nm_code
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodecode($sTipe,$sCode,$sCount,$sNama,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempcode".$xUser.$xIP."%' )
					DROP TABLE #tempcode".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), LTRIM(RTRIM(a.fs_code)) fs_code, LTRIM(RTRIM(a.fs_count)) fs_count,
					LTRIM(RTRIM(a.fs_nm_code)) fs_nm_code, a.fs_addr fs_alamat,
					a.fs_cdtyp fs_kd_tipe, ISNULL(b.fs_nm_type, '') fs_nm_tipe,
					a.fs_kd_city fs_kd_kota, ISNULL(c.fs_nm_vareable, '') fs_nm_kota,
					a.fs_kd_district fs_kd_kab, ISNULL(d.fs_nm_vareable, '') fs_nm_kab,
					a.fs_kd_province fs_kd_prop, ISNULL(e.fs_nm_vareable, '') fs_nm_prop,
					a.fs_kd_country fs_kd_neg, ISNULL(f.fs_nm_vareable, '') fs_nm_neg,
					a.fs_kd_state fs_kd_wn, ISNULL(g.fs_nm_vareable, '') fs_nm_wn,
					a.fs_idcard fs_kd_id,
					fd_tgl_id = CASE a.fd_dateid
					WHEN '' THEN CONVERT(VARCHAR(10), CONVERT(DATETIME, GETDATE(), 105), 105)
					ELSE CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_dateid, 105), 105)
					END,
					fs_phone1 fs_tlp, fs_phone2 fs_tlp2
			INTO	#tempcode".$xUser.$xIP."
			FROM 	tm_addr a (NOLOCK)
			LEFT JOIN tm_codetype b (NOLOCK) ON a.fs_cdtyp = b.fs_kd_type
			LEFT JOIN tm_vareable c (NOLOCK) ON a.fs_kd_city = c.fs_kd_vareable
				AND	c.fs_key = '14'
			LEFT JOIN tm_vareable d (NOLOCK) ON a.fs_kd_district = d.fs_kd_vareable
				AND	d.fs_key = '05'
			LEFT JOIN tm_vareable e (NOLOCK) ON a.fs_kd_province = e.fs_kd_vareable
				AND	e.fs_key = '04'
			LEFT JOIN tm_vareable f (NOLOCK) ON a.fs_kd_country = f.fs_kd_vareable
				AND	f.fs_key = '02'
			LEFT JOIN tm_vareable g (NOLOCK) ON a.fs_kd_state = g.fs_kd_vareable
				AND	g.fs_key = '18'
            WHERE 	a.fs_cdtyp = '".trim($sTipe)."'
				AND	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
		");
		
		if (trim($sTipe) <> '03')
		{
			$xSQL = $xSQL.("
				AND a.fs_code = '".trim($this->session->userdata('gGudang'))."'
			");
		}
		
		if (trim($sCode) <> '')
		{
			$xSQL = $xSQL.("
				AND a.fs_code = '".trim($sCode)."'
			");
		}
		
		if (trim($sCount) <> '' or trim($sNama) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_count = '".trim($sCount)."'
				OR a.fs_nm_code = '".trim($sNama)."')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_code, a.fs_count, a.fs_nm_code
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempcode".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempcode".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodegl_all($sGL,$sGLNm,$sCari)
	{
		$xSQL = ("
			SELECT	fs_kd_acno, fs_nm_acno
            FROM 	tm_acno (NOLOCK)
            WHERE 	fs_kd_comp = '".trim(trim($this->session->userdata('gComp')))."'
				AND fb_active = '1'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_acno LIKE '%".trim($sCari)."%'
					OR fs_nm_acno LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sGL) <> '' or trim($sGLNm))
		{
			$xSQL = $xSQL.("
				AND (fs_kd_acno LIKE '%".trim($sGL)."%'
					OR fs_nm_acno LIKE '%".trim($sGLNm)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_acno, fs_nm_acno
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodegl($sGL,$sGLNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempgl".$xUser.$xIP."%' )
					DROP TABLE #tempgl".$xUser.$xIP."
					
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_acno, fs_nm_acno
			INTO	#tempgl".$xUser.$xIP."
			FROM 	tm_acno (NOLOCK)
            WHERE 	fs_kd_comp = '".trim(trim($this->session->userdata('gComp')))."'
				AND fb_active = '1'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_acno LIKE '%".trim($sCari)."%'
					OR fs_nm_acno LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sGL) <> '' or trim($sGLNm))
		{
			$xSQL = $xSQL.("
				AND (fs_kd_acno LIKE '%".trim($sGL)."%'
					OR fs_nm_acno LIKE '%".trim($sGLNm)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_acno, fs_nm_acno
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempgl".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempgl".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodekab_all($sKab,$sKabNm,$sCari)
	{
		$xSQL = ("
			SELECT 	fs_kd_vareable fs_kd_kab, fs_nm_vareable fs_nm_kab
            FROM 	tm_vareable (NOLOCK)
            WHERE 	fs_key = '05'
				AND	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sCari)."%'
					OR fs_nm_vareable LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sKab) <> '' or trim($sKabNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sKab)."%'
					OR fs_nm_vareable LIKE '%".trim($sKabNm)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_vareable, fs_nm_vareable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodekab($sKab,$sKabNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempkab".$xUser.$xIP."%' )
					DROP TABLE #tempkab".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_vareable fs_kd_kab, fs_nm_vareable fs_nm_kab
			INTO	#tempkab".$xUser.$xIP."
			FROM 	tm_vareable (NOLOCK)
            WHERE 	fs_key = '05'
				AND	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sCari)."%'
					OR fs_nm_vareable LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sKab) <> '' or trim($sKabNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sKab)."%'
					OR fs_nm_vareable LIKE '%".trim($sKabNm)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_vareable, fs_nm_vareable
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempkab".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempkab".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodekey_all($sKey,$sKeyNm,$sCari)
	{
		$xSQL = ("
			SELECT 	fs_kd_key, fs_nm_key
            FROM 	tm_key (NOLOCK)
            WHERE 	LTRIM(RTRIM(fs_kd_key)) <> ''
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_key LIKE '%".trim($sCari)."%'
					OR fs_nm_key LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sKey) <> '' or trim($sKeyNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_key LIKE '%".trim($sKey)."%'
					OR fs_nm_key LIKE '%".trim($sKeyNm)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_key, fs_nm_key
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodekey($sKey,$sKeyNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempkey".$xUser.$xIP."%' )
					DROP TABLE #tempkey".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_key, fs_nm_key
			INTO	#tempkey".$xUser.$xIP."
			FROM 	tm_key (NOLOCK)
            WHERE 	LTRIM(RTRIM(fs_kd_key)) <> ''
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_key LIKE '%".trim($sCari)."%'
					OR fs_nm_key LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sKey) <> '' or trim($sKeyNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_key LIKE '%".trim($sKey)."%'
					OR fs_nm_key LIKE '%".trim($sKeyNm)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_key, fs_nm_key
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempkey".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempkey".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodekota_all($sKota,$sKotaNm,$sCari)
	{
		$xSQL = ("
			SELECT 	fs_kd_vareable fs_kd_kota, fs_nm_vareable fs_nm_kota
            FROM 	tm_vareable (NOLOCK)
            WHERE 	fs_key = '14'
				AND	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sCari)."%'
					OR fs_nm_vareable LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sKota) <> '' or trim($sKotaNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sKota)."%'
					OR fs_nm_vareable LIKE '%".trim($sKotaNm)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_vareable, fs_nm_vareable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodekota($sKota,$sKotaNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempkota".$xUser.$xIP."%' )
					DROP TABLE #tempkota".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_vareable fs_kd_kota, fs_nm_vareable fs_nm_kota
			INTO	#tempkota".$xUser.$xIP."
			FROM 	tm_vareable (NOLOCK)
            WHERE 	fs_key = '14'
				AND	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sCari)."%'
					OR fs_nm_vareable LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sKota) <> '' or trim($sKotaNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sKota)."%'
					OR fs_nm_vareable LIKE '%".trim($sKotaNm)."%')
				");
		}
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempkota".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempkota".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodelevel_all($slevel,$sCari)
	{
		$xSQL = ("
			SELECT 	DISTINCT fs_level
            FROM 	tm_parlevel (NOLOCK)
            WHERE 	LTRIM(RTRIM(fs_level)) <> ''
				AND	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_level LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($slevel) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_level LIKE '%".trim($slevel)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_level
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodelevel($slevel,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#templevel".$xUser.$xIP."%' )
					DROP TABLE #templevel".$xUser.$xIP."
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#templevel2".$xUser.$xIP."%' )
					DROP TABLE #templevel2".$xUser.$xIP."
			
			SELECT	DISTINCT fs_level
			INTO	#templevel".$xUser.$xIP."
			FROM 	tm_parlevel (NOLOCK)
            WHERE 	LTRIM(RTRIM(fs_level)) <> ''
				AND	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_level LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($slevel) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_level LIKE '%".trim($slevel)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_level
			");
		
		$xSQL =	$xSQL.("
			SELECT 	n = IDENTITY(INT, 1, 1), *
			INTO	#templevel2".$xUser.$xIP."
			FROM	#templevel".$xUser.$xIP."
			
			SELECT 	*
			FROM	#templevel2".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #templevel".$xUser.$xIP."
			DROP TABLE #templevel2".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodeneg_all($sNeg,$sNegNm,$sCari)
	{
		$xSQL = ("
			SELECT 	fs_kd_vareable fs_kd_neg, fs_nm_vareable fs_nm_neg
            FROM 	tm_vareable (NOLOCK)
            WHERE 	fs_key = '02'
				AND	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sCari)."%'
					OR fs_nm_vareable LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sNeg) <> '' or trim($sNegNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sNeg)."%'
					OR fs_nm_vareable LIKE '%".trim($sNegNm)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_vareable, fs_nm_vareable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodeneg($sNeg,$sNegNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempneg".$xUser.$xIP."%' )
					DROP TABLE #tempneg".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_vareable fs_kd_neg, fs_nm_vareable fs_nm_neg
			INTO	#tempneg".$xUser.$xIP."
			FROM 	tm_vareable (NOLOCK)
            WHERE 	fs_key = '02'
				AND	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sCari)."%'
					OR fs_nm_vareable LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sNeg) <> '' or trim($sNegNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sNeg)."%'
					OR fs_nm_vareable LIKE '%".trim($sNegNm)."%')
				");
		}
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempneg".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempneg".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodeprop_all($sProp,$sPropNm,$sCari)
	{
		$xSQL = ("
			SELECT 	fs_kd_vareable fs_kd_prop, fs_nm_vareable fs_nm_prop
            FROM 	tm_vareable (NOLOCK)
            WHERE 	fs_key = '04'
				AND	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sCari)."%'
					OR fs_nm_vareable LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sProp) <> '' or trim($sPropNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sProp)."%'
					OR fs_nm_vareable LIKE '%".trim($sPropNm)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_vareable, fs_nm_vareable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodeprop($sProp,$sPropNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempprop".$xUser.$xIP."%' )
					DROP TABLE #tempprop".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_vareable fs_kd_prop, fs_nm_vareable fs_nm_prop
			INTO	#tempprop".$xUser.$xIP."
			FROM 	tm_vareable (NOLOCK)
            WHERE 	fs_key = '04'
				AND	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sCari)."%'
					OR fs_nm_vareable LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sProp) <> '' or trim($sPropNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sProp)."%'
					OR fs_nm_vareable LIKE '%".trim($sPropNm)."%')
				");
		}
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempprop".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempprop".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodetipe_all($sTipe,$sTipeNm,$sCari)
	{
		$xSQL = ("
			SELECT	fs_kd_type fs_kd_tipe, fs_nm_type fs_nm_tipe
            FROM 	tm_codetype (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
		");
		
		if (trim($this->session->userdata('gUser')) <> 'MFI')
		{
			$xSQL = $xSQL.("
				AND	fs_kd_type NOT IN ('00','03','04','11','20','21','70')
			");
		}

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_type LIKE '%".trim($sCari)."%'
					OR fs_nm_type LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sTipe) <> '' or trim($sTipeNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_type LIKE '%".trim($sTipe)."%'
					OR fs_nm_type LIKE '%".trim($sTipeNm)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_type, fs_nm_type
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodetipe($sTipe,$sTipeNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temptipe".$xUser.$xIP."%' )
					DROP TABLE #temptipe".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_type fs_kd_tipe, fs_nm_type fs_nm_tipe
			INTO	#temptipe".$xUser.$xIP."
			FROM 	tm_codetype (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
		");
		
		if (trim($this->session->userdata('gUser')) <> 'MFI')
		{
			$xSQL = $xSQL.("
				AND	fs_kd_type NOT IN ('00','03','04','11','20','21','70')
			");
		}

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_type LIKE '%".trim($sCari)."%'
					OR fs_nm_type LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sTipe) <> '' or trim($sTipeNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_type LIKE '%".trim($sTipe)."%'
					OR fs_nm_type LIKE '%".trim($sTipeNm)."%')
				");
		}
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temptipe".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temptipe".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodetipecust_all($sTipe,$sTipeNm)
	{
		$xSQL = ("
			SELECT	fs_kd_vareable fs_kd_tipe, fs_nm_vareable fs_nm_tipe
            FROM 	tm_vareable (NOLOCK)
            WHERE 	fs_key = '61'
				AND	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");
		
		if (trim($sTipe) <> '' or trim($sTipeNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sTipe)."%'
					OR fs_nm_vareable LIKE '%".trim($sTipeNm)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_vareable, fs_nm_vareable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodetipecust($sTipe,$sTipeNm,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temptipe".$xUser.$xIP."%' )
					DROP TABLE #temptipe".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_vareable fs_kd_tipe, fs_nm_vareable fs_nm_tipe
			INTO	#temptipe".$xUser.$xIP."
			FROM 	tm_vareable (NOLOCK)
            WHERE 	fs_key = '61'
				AND	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");
		
		if (trim($sTipe) <> '' or trim($sTipeNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sTipe)."%'
					OR fs_nm_vareable LIKE '%".trim($sTipeNm)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_vareable, fs_nm_vareable
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temptipe".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temptipe".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodeuser_all($sUser,$sUserNm,$sCari)
	{
		$xSQL = ("
			SELECT 	fs_kd_user, fs_nm_user, fs_password,
					fs_level, fb_spearpart fb_part,
					fs_status = CASE fb_spearpart WHEN '0' THEN 'UNIT' ELSE 'PART' END
            FROM 	tm_user (NOLOCK)
            WHERE 	LTRIM(RTRIM(fs_kd_user)) <> ''
				AND	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_user LIKE '%".trim($sCari)."%'
					OR fs_nm_user LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sUser) <> '' or trim($sUserNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_user LIKE '%".trim($sUser)."%'
					OR fs_nm_user LIKE '%".trim($sUserNm)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_user, fs_nm_user
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodeuser($sUser,$sUserNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempuser".$xUser.$xIP."%' )
					DROP TABLE #tempuser".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_user, fs_nm_user, fs_password,
					fs_level, fb_spearpart fb_part,
					fs_status = CASE fb_spearpart WHEN '0' THEN 'UNIT' ELSE 'PART' END
			INTO	#tempuser".$xUser.$xIP."
			FROM 	tm_user (NOLOCK)
            WHERE 	LTRIM(RTRIM(fs_kd_user)) <> ''
				AND	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_user LIKE '%".trim($sCari)."%'
					OR fs_nm_user LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sUser) <> '' or trim($sUserNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_user LIKE '%".trim($sUser)."%'
					OR fs_nm_user LIKE '%".trim($sUserNm)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_user, fs_nm_user
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempuser".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempuser".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodevar_all($sKey,$sVar,$sVarNm,$sCari)
	{
		$xSQL = ("
			SELECT 	a.fs_kd_vareable fs_kd_var, a.fs_nm_vareable fs_nm_var,
					ISNULL(b.fs_kd_acno, '') fs_kd_acno, ISNULL(c.fs_nm_acno, '') fs_nm_acno,
					ISNULL(b.fn_subsidi, 0) fn_subsidi
            FROM 	tm_vareable a (NOLOCK)
			LEFT JOIN tm_acnopmtyp b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_key = b.fs_key
				AND	a.fs_kd_vareable = b.fs_kd_vareable
			LEFT JOIN tm_acno c (NOLOCK) ON b.fs_kd_acno = c.fs_kd_acno
            WHERE 	LTRIM(RTRIM(a.fs_kd_vareable)) <> ''
				AND	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	a.fs_key = '".trim($sKey)."'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_vareable LIKE '%".trim($sCari)."%'
					OR a.fs_nm_vareable LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sVar) <> '' or trim($sVarNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_vareable LIKE '%".trim($sVar)."%'
					OR a.fs_nm_vareable LIKE '%".trim($sVarNm)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_vareable, a.fs_nm_vareable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodevar($sKey,$sVar,$sVarNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempvar".$xUser.$xIP."%' )
					DROP TABLE #tempvar".$xUser.$xIP."
			
			SELECT	a.fs_kd_vareable fs_kd_var, a.fs_nm_vareable fs_nm_var,
					ISNULL(b.fs_kd_acno, '') fs_kd_acno, ISNULL(c.fs_nm_acno, '') fs_nm_acno,
					ISNULL(b.fn_subsidi, 0) fn_subsidi
			INTO	#tempvar".$xUser.$xIP."
			FROM 	tm_vareable a (NOLOCK)
			LEFT JOIN tm_acnopmtyp b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_key = b.fs_key
				AND	a.fs_kd_vareable = b.fs_kd_vareable
			LEFT JOIN tm_acno c (NOLOCK) ON b.fs_kd_acno = c.fs_kd_acno
            WHERE 	LTRIM(RTRIM(a.fs_kd_vareable)) <> ''
				AND	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	a.fs_key = '".trim($sKey)."'
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_vareable LIKE '%".trim($sCari)."%'
					OR a.fs_nm_vareable LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sVar) <> '' or trim($sVarNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_vareable LIKE '%".trim($sVar)."%'
					OR a.fs_nm_vareable LIKE '%".trim($sVarNm)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_vareable, a.fs_nm_vareable
		");
		
		$xSQL =	$xSQL.("
			SELECT 	n = IDENTITY(INT, 1, 1), *
			INTO	#tempvar2".$xUser.$xIP."
			FROM	#tempvar".$xUser.$xIP."
			
			SELECT	*
			FROM	#tempvar2".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			ORDER BY n
			
			DROP TABLE #tempvar".$xUser.$xIP."
			DROP TABLE #tempvar2".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodewn_all($sWN,$sWNNm,$sCari)
	{
		$xSQL = ("
			SELECT 	fs_kd_vareable fs_kd_wn, fs_nm_vareable fs_nm_wn
            FROM 	tm_vareable (NOLOCK)
            WHERE 	fs_key = '18'
				AND	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sCari)."%'
					OR fs_nm_vareable LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sWN) <> '' or trim($sWNNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sWN)."%'
					OR fs_nm_vareable LIKE '%".trim($sWNNm)."%')
				");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_vareable, fs_nm_vareable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodewn($sWN,$sWNNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempprop".$xUser.$xIP."%' )
					DROP TABLE #tempprop".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_vareable fs_kd_wn, fs_nm_vareable fs_nm_wn
			INTO	#tempprop".$xUser.$xIP."
			FROM 	tm_vareable (NOLOCK)
            WHERE 	fs_key = '18'
				AND	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sCari)."%'
					OR fs_nm_vareable LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sWN) <> '' or trim($sWNNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sWN)."%'
					OR fs_nm_vareable LIKE '%".trim($sWNNm)."%')
				");
		}
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempprop".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempprop".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function login_dept_all($sComp,$sCode,$sName)
	{
		$xSQL = ("
			SELECT 	[fs_code] = ISNULL(LTRIM(RTRIM(fs_code)), ''), [fs_count] = ISNULL(LTRIM(RTRIM(fs_count)), ''),
					[fs_nm_code] = ISNULL(fs_nm_code, '')
			FROM 	tm_addr(NOLOCK)
			WHERE 	fs_cdtyp = '03'
		");
		
		if (trim($sComp) <> '')
		{
			$xSQL = $xSQL.("
				AND	fs_kd_comp = '".trim($sComp)."'
			");
		}
		else
		{
			$xSQL = $xSQL.("
				AND	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");
		}
		

		if (trim($sCode) <> '' or trim($sName) <> '')
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)) LIKE '%".trim($sCode)."%'
					OR fs_nm_code LIKE '%".trim($sName)."%')
				");
		}
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function login_dept($sComp,$sCode,$sName,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempdept".$xUser.$xIP."%' )
					DROP TABLE #tempdept".$xUser.$xIP."
			
			SELECT 	n = IDENTITY(INT, 1, 1), [fs_code] = ISNULL(LTRIM(RTRIM(fs_code)), ''), [fs_count] = ISNULL(LTRIM(RTRIM(fs_count)), ''),
					[fs_nm_code] = ISNULL(fs_nm_code, '')
			INTO	#tempdept".$xUser.$xIP."
			FROM 	tm_addr(NOLOCK)
			WHERE 	fs_cdtyp = '03'
		");
		
		if (trim($sComp) <> '')
		{
			$xSQL = $xSQL.("
				AND	fs_kd_comp = '".trim($sComp)."'
			");
		}
		else
		{
			$xSQL = $xSQL.("
				AND	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");
		}
	

		if (trim($sCode) <> '' or trim($sName) <> '')
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)) LIKE '%".trim($sCode)."%'
					OR fs_nm_code LIKE '%".trim($sName)."%')
				");
		}
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempdept".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempdept".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function mutasi_all($sRefno,$sTrx,$ssTrx,$sCari)
	{
		$xSQL = ("
			SELECT	a.fs_refno, [fd_refno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105),
					a.fs_doc, [fd_doc] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_doc, 105), 105),
					a.fs_descrp, a.fs_kd_dept, a.fs_count, a.fs_trefno fs_refnot,
					[fs_dept] = LTRIM(RTRIM(a.fs_kd_dept)) + LTRIM(RTRIM(a.fs_count)),
					[fs_nm_dept] = ISNULL(b.fs_nm_code, ''),
					a.fs_kd_tdept, a.fs_tcount, [fs_tdept] = LTRIM(RTRIM(a.fs_kd_tdept)) + LTRIM(RTRIM(a.fs_tcount)),
					[fs_nm_tdept] = ISNULL(c.fs_nm_code, ''),
					a.fs_kd_wh, [fs_nm_wh] = ISNULL(d.fs_nm_code, ''),
					a.fs_kd_wht, [fs_nm_wht] = ISNULL(e.fs_nm_code, ''),
					[fs_status] = CASE WHEN a.fs_trsta = '5' THEN 'DRAFT' ELSE 'APPROVED' END
			FROM	tx_actheader a (NOLOCK)
			INNER JOIN tm_addr b (NOLOCK) ON a.fs_kd_dept = b.fs_code
				AND a.fs_count = b.fs_count
				AND	b.fs_cdtyp = '03'
			INNER JOIN tm_addr c (NOLOCK) ON a.fs_kd_tdept = c.fs_code
				AND a.fs_tcount = c.fs_count
				AND	c.fs_cdtyp = '03'
			INNER JOIN tm_addr d (NOLOCK) ON a.fs_kd_wh = LTRIM(RTRIM(d.fs_code)) + LTRIM(RTRIM(d.fs_count)) 
				AND d.fs_cdtyp = '11'
			INNER JOIN tm_addr e (NOLOCK) ON a.fs_kd_wht = LTRIM(RTRIM(e.fs_code)) + LTRIM(RTRIM(e.fs_count)) 
				AND e.fs_cdtyp = '11'
			WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fs_kd_strx = '".trim($ssTrx)."'
				AND a.fb_delete = 0
				AND a.fs_trsta IN ('5','7')
		");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sRefno) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sRefno)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_refno DESC");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function mutasi($sRefno,$sTrx,$ssTrx,$nStart,$nTotal,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nTotal." - ".$nStart." - 24
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempmutasi".$xUser.$xIP."%' )
					DROP TABLE #tempmutasi".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_refno, [fd_refno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, fd_refno, 105), 105),
					a.fs_doc, [fd_doc] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_doc, 105), 105),
					a.fs_descrp, a.fs_kd_dept, a.fs_count, a.fs_trefno fs_refnot,
					[fs_dept] = LTRIM(RTRIM(a.fs_kd_dept)) + LTRIM(RTRIM(a.fs_count)),
					[fs_nm_dept] = ISNULL(b.fs_nm_code, ''),
					a.fs_kd_tdept, a.fs_tcount, [fs_tdept] = LTRIM(RTRIM(a.fs_kd_tdept)) + LTRIM(RTRIM(a.fs_tcount)),
					[fs_nm_tdept] = ISNULL(c.fs_nm_code, ''),
					a.fs_kd_wh, [fs_nm_wh] = ISNULL(d.fs_nm_code, ''),
					a.fs_kd_wht, [fs_nm_wht] = ISNULL(e.fs_nm_code, ''),
					[fs_status] = CASE WHEN a.fs_trsta = '5' THEN 'DRAFT' ELSE 'APPROVED' END
			INTO	#tempmutasi".$xUser.$xIP."
			FROM	tx_actheader a (NOLOCK)
			INNER JOIN tm_addr b (NOLOCK) ON a.fs_kd_dept = b.fs_code
				AND a.fs_count = b.fs_count
				AND	b.fs_cdtyp = '03'
			INNER JOIN tm_addr c (NOLOCK) ON a.fs_kd_tdept = c.fs_code
				AND a.fs_tcount = c.fs_count
				AND	c.fs_cdtyp = '03'
			INNER JOIN tm_addr d (NOLOCK) ON a.fs_kd_wh = LTRIM(RTRIM(d.fs_code)) + LTRIM(RTRIM(d.fs_count)) 
				AND d.fs_cdtyp = '11'
			INNER JOIN tm_addr e (NOLOCK) ON a.fs_kd_wht = LTRIM(RTRIM(e.fs_code)) + LTRIM(RTRIM(e.fs_count)) 
				AND e.fs_cdtyp = '11'
			WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fs_kd_strx = '".trim($ssTrx)."'
				AND a.fb_delete = 0
				AND a.fs_trsta IN ('5','7')
		");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sRefno) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sRefno)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_refno");
		
		$xSQL =	$xSQL.("
			SELECT	* FROM #tempmutasi".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			ORDER BY n DESC
			
			DROP TABLE #tempmutasi".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function mutasi_out_all($sRefno,$sTrx,$ssTrx,$sCari)
	{
		$xSQL = ("
			SELECT	DISTINCT a.fs_refno, [fd_refno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105),
					a.fs_doc, [fd_doc] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_doc, 105), 105),
					a.fs_descrp, a.fs_kd_dept, a.fs_count,
					[fs_dept] = LTRIM(RTRIM(a.fs_kd_dept)) + LTRIM(RTRIM(a.fs_count)),
					[fs_nm_dept] = ISNULL(b.fs_nm_code, ''),
					a.fs_kd_tdept, a.fs_tcount, [fs_tdept] = LTRIM(RTRIM(a.fs_kd_tdept)) + LTRIM(RTRIM(a.fs_tcount)),
					[fs_nm_tdept] = ISNULL(c.fs_nm_code, ''),
					a.fs_kd_wh, [fs_nm_wh] = ISNULL(d.fs_nm_code, ''),
					a.fs_kd_wht, [fs_nm_wht] = ISNULL(e.fs_nm_code, ''),
					[fs_status] = CASE WHEN a.fs_trsta = '5' THEN 'DRAFT' ELSE 'APPROVED' END
			FROM	tx_actheader a (NOLOCK)
			INNER JOIN tm_addr b (NOLOCK) ON a.fs_kd_dept = b.fs_code
				AND a.fs_count = b.fs_count
				AND	b.fs_cdtyp = '03'
			INNER JOIN tm_addr c (NOLOCK) ON a.fs_kd_tdept = c.fs_code
				AND a.fs_tcount = c.fs_count
				AND	c.fs_cdtyp = '03'
			INNER JOIN tm_addr d (NOLOCK) ON a.fs_kd_wh = LTRIM(RTRIM(d.fs_code)) + LTRIM(RTRIM(d.fs_count)) 
				AND d.fs_cdtyp = '11'
			INNER JOIN tm_addr e (NOLOCK) ON a.fs_kd_wht = LTRIM(RTRIM(e.fs_code)) + LTRIM(RTRIM(e.fs_count)) 
				AND e.fs_cdtyp = '11'
			INNER JOIN tm_icregister f (NOLOCK) ON a.fs_refno = f.fs_refnoictout
				AND a.fs_kd_dept = f.fs_kd_deptictout
				AND a.fs_count = f.fs_countdeptictout
				AND a.fs_kd_trx = f.fs_kd_trxictout
				AND a.fs_kd_strx = f.fs_kd_strxictout
			WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_tdept = '".trim($this->session->userdata('gDept'))."'
				AND a.fs_tcount = '".trim($this->session->userdata('gCount'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fs_kd_strx = '".trim($ssTrx)."'
				AND a.fb_delete = 0
				AND a.fs_trsta IN ('5','7')
				AND f.fs_kd_deptictin = ''
				AND f.fs_countdeptictin = ''
				AND f.fs_kd_trxictin = ''
				AND f.fs_kd_strxictin = ''
				AND f.fs_refnoictin = ''
				AND f.fs_seqnoictin = ''
		");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND a.fs_refno LIKE '%".trim($sCari)."%'
			");
		}

		if (trim($sRefno) <> '')
		{
			$xSQL = $xSQL.("
				AND a.fs_refno LIKE '%".trim($sRefno)."%'
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_refno DESC");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function mutasi_out($sRefno,$sTrx,$ssTrx,$nStart,$nTotal,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nTotal." - ".$nStart." - 24
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempmutasiout".$xUser.$xIP."%' )
					DROP TABLE #tempmutasiout".$xUser.$xIP."
			
			SELECT	DISTINCT a.fs_refno, [fd_refno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, fd_refno, 105), 105),
					a.fs_doc, [fd_doc] = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_doc, 105), 105),
					a.fs_descrp, a.fs_kd_dept, a.fs_count,
					[fs_dept] = LTRIM(RTRIM(a.fs_kd_dept)) + LTRIM(RTRIM(a.fs_count)),
					[fs_nm_dept] = ISNULL(b.fs_nm_code, ''),
					a.fs_kd_tdept, a.fs_tcount, [fs_tdept] = LTRIM(RTRIM(a.fs_kd_tdept)) + LTRIM(RTRIM(a.fs_tcount)),
					[fs_nm_tdept] = ISNULL(c.fs_nm_code, ''),
					a.fs_kd_wh, [fs_nm_wh] = ISNULL(d.fs_nm_code, ''),
					a.fs_kd_wht, [fs_nm_wht] = ISNULL(e.fs_nm_code, ''),
					[fs_status] = CASE WHEN a.fs_trsta = '5' THEN 'DRAFT' ELSE 'APPROVED' END
			INTO	#tempmutasiout".$xUser.$xIP."
			FROM	tx_actheader a (NOLOCK)
			INNER JOIN tm_addr b (NOLOCK) ON a.fs_kd_dept = b.fs_code
				AND a.fs_count = b.fs_count
				AND	b.fs_cdtyp = '03'
			INNER JOIN tm_addr c (NOLOCK) ON a.fs_kd_tdept = c.fs_code
				AND a.fs_tcount = c.fs_count
				AND	c.fs_cdtyp = '03'
			INNER JOIN tm_addr d (NOLOCK) ON a.fs_kd_wh = LTRIM(RTRIM(d.fs_code)) + LTRIM(RTRIM(d.fs_count)) 
				AND d.fs_cdtyp = '11'
			INNER JOIN tm_addr e (NOLOCK) ON a.fs_kd_wht = LTRIM(RTRIM(e.fs_code)) + LTRIM(RTRIM(e.fs_count)) 
				AND e.fs_cdtyp = '11'
			INNER JOIN tm_icregister f (NOLOCK) ON a.fs_refno = f.fs_refnoictout
				AND a.fs_kd_dept = f.fs_kd_deptictout
				AND a.fs_count = f.fs_countdeptictout
				AND a.fs_kd_trx = f.fs_kd_trxictout
				AND a.fs_kd_strx = f.fs_kd_strxictout
			WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_tdept = '".trim($this->session->userdata('gDept'))."'
				AND a.fs_tcount = '".trim($this->session->userdata('gCount'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fs_kd_strx = '".trim($ssTrx)."'
				AND a.fb_delete = 0
				AND a.fs_trsta IN ('5','7')
				AND f.fs_kd_deptictin = ''
				AND f.fs_countdeptictin = ''
				AND f.fs_kd_trxictin = ''
				AND f.fs_kd_strxictin = ''
				AND f.fs_refnoictin = ''
				AND f.fs_seqnoictin = ''
		");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND a.fs_refno LIKE '%".trim($sCari)."%'
			");
		}

		if (trim($sRefno) <> '')
		{
			$xSQL = $xSQL.("
				AND a.fs_refno LIKE '%".trim($sRefno)."%'
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_refno");
		
		$xSQL =	$xSQL.("
			SELECT 	n = IDENTITY(INT, 1, 1), *
			INTO	#tempmutasiout2".$xUser.$xIP."
			FROM 	#tempmutasiout".$xUser.$xIP."
			ORDER BY fs_refno
			
			SELECT	* FROM #tempmutasiout2".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			ORDER BY n DESC
			
			DROP TABLE #tempmutasiout".$xUser.$xIP."
			DROP TABLE #tempmutasiout2".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function mutasi_out_db_all($sRefno,$sCust,$sCari)
	{
		$xSQL = ("
			SELECT	a.fs_refno, CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105) fd_refno,
					a.fs_ket, LTRIM(RTRIM(a.fs_kd_cust)) + LTRIM(RTRIM(a.fs_count_cust)) fs_kd_custcount,
					a.fs_kd_cust, a.fs_count_cust, ISNULL(b.fs_nm_code, '') fs_nm_cust,
					a.fs_nm_tcomp, a.fs_kd_tcomp, a.fs_nm_tdb,
					LTRIM(RTRIM(a.fs_kd_tdept)) + LTRIM(RTRIM(a.fs_tcount)) fs_kd_tdeptcount, a.fs_nm_tdept,
					a.fs_kd_tdept, a.fs_tcount, a.fs_kd_twh, a.fs_nm_twh
			FROM	tx_mutasidb a (NOLOCK)
			LEFT JOIN tm_addr b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_cust = b.fs_code
				AND	a.fs_count_cust = b.fs_count
				AND	b.fs_cdtyp = '02'
			WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
		");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sCari)."%'
					OR	b.fs_nm_code LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sRefno) <> '' or trim($sCust) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sRefno)."%'
					OR	b.fs_nm_code LIKE '%".trim($sCust)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_refno");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function mutasi_out_db($sRefno,$sCust,$nStart,$nTotal,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nTotal." - ".$nStart." - 24
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempmutasioutdb".$xUser.$xIP."%' )
					DROP TABLE #tempmutasioutdb".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), a.fs_refno, CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105) fd_refno,
					a.fs_ket, LTRIM(RTRIM(a.fs_kd_cust)) + LTRIM(RTRIM(a.fs_count_cust)) fs_kd_custcount,
					a.fs_kd_cust, a.fs_count_cust, ISNULL(b.fs_nm_code, '') fs_nm_cust,
					a.fs_nm_tcomp, a.fs_kd_tcomp, a.fs_nm_tdb,
					LTRIM(RTRIM(a.fs_kd_tdept)) + LTRIM(RTRIM(a.fs_tcount)) fs_kd_tdeptcount, a.fs_nm_tdept,
					a.fs_kd_tdept, a.fs_tcount, a.fs_kd_twh, a.fs_nm_twh
			INTO	#tempmutasioutdb".$xUser.$xIP."
			FROM	tx_mutasidb a (NOLOCK)
			LEFT JOIN tm_addr b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_cust = b.fs_code
				AND	a.fs_count_cust = b.fs_count
				AND	b.fs_cdtyp = '02'
			WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
		");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sCari)."%'
					OR	b.fs_nm_code LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sRefno) <> '' or trim($sCust) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sRefno)."%'
					OR	b.fs_nm_code LIKE '%".trim($sCust)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_refno");
		
		$xSQL =	$xSQL.("
			SELECT	* FROM #tempmutasioutdb".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			ORDER BY n DESC
			
			DROP TABLE #tempmutasioutdb".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function payment_all($sCode,$sName,$sCari)
	{
		$xSQL = ("
			SELECT	[fs_code] = a.fs_kd_vareable,
					[fs_nm_code] = b.fs_nm_vareable,
					[fs_kd_acno] = fs_kd_acno,
					[fs_nm_acno] = ISNULL((	SELECT	TOP 1 fs_nm_acno
									FROM 	tm_acno c (NOLOCK)
									WHERE 	c.fs_kd_acno = a.fs_kd_acno), ' ')
            FROM	tm_acnopmtyp a (NOLOCK), tm_vareable b (NOLOCK)
            WHERE 	a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_key = b.fs_key
				AND a.fs_kd_vareable = b.fs_kd_vareable
				AND b.fs_key = '17'
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (b.fs_kd_vareable LIKE '%".trim($sCari)."%'
					OR b.fs_nm_vareable LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sCode) <> '' or trim($sName) <> '')
		{
			$xSQL = $xSQL.("
				AND (b.fs_kd_vareable LIKE '%".trim($sCode)."%'
					OR b.fs_nm_vareable LIKE '%".trim($sName)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY b.fs_nm_vareable");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function payment($sCode,$sName,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temppay".$xUser.$xIP."%' )
					DROP TABLE #temppay".$xUser.$xIP."
			
			SELECT	DISTINCT n = IDENTITY(INT, 1, 1), [fs_code] = a.fs_kd_vareable,
					[fs_nm_code] = b.fs_nm_vareable,
					[fs_kd_acno] = fs_kd_acno,
					[fs_nm_acno] = ISNULL((	SELECT	TOP 1 fs_nm_acno
									FROM 	tm_acno c (NOLOCK)
									WHERE 	c.fs_kd_acno = a.fs_kd_acno), ' ')
			INTO	#temppay".$xUser.$xIP."
            FROM	tm_acnopmtyp a (NOLOCK), tm_vareable b (NOLOCK)
            WHERE 	a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_key = b.fs_key
				AND a.fs_kd_vareable = b.fs_kd_vareable
				AND b.fs_key = '17'
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (b.fs_kd_vareable LIKE '%".trim($sCari)."%'
					OR b.fs_nm_vareable LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sCode) <> '' or trim($sName) <> '')
		{
			$xSQL = $xSQL.("
				AND (b.fs_kd_vareable LIKE '%".trim($sCode)."%'
					OR b.fs_nm_vareable LIKE '%".trim($sName)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY b.fs_nm_vareable");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temppay".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temppay".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function product_all($sProdCd,$sProdNm,$sCari)
	{
		$xSQL = ("
			SELECT	DISTINCT fs_kd_product, fs_nm_product
            FROM 	tm_product (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fb_active = '1'
				AND	LTRIM(RTRIM(fs_kd_product)) <> ''
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_product LIKE '%".trim($sCari)."%'
					OR fs_nm_product LIKE  '%".trim($sCari)."%')
			");

		}	

		if (trim($sProdCd) <> '' or trim($sProdNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_product LIKE '%".trim($sProdCd)."%'
					OR fs_nm_product LIKE '%".trim($sProdNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_product");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function product($sProdCd,$sProdNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempprod".$xUser.$xIP."%' )
					DROP TABLE #tempprod".$xUser.$xIP."
			
			SELECT	DISTINCT n = IDENTITY(INT, 1, 1), fs_kd_product, fs_nm_product
			INTO	#tempprod".$xUser.$xIP."
            FROM 	tm_product (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fb_active = '1'
				AND	LTRIM(RTRIM(fs_kd_product)) <> ''
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_product LIKE '%".trim($sCari)."%'
					OR fs_nm_product LIKE  '%".trim($sCari)."%')
			");

		}

		if (trim($sProdCd) <> '' or trim($sProdNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_product LIKE '%".trim($sProdCd)."%'
					OR fs_nm_product LIKE '%".trim($sProdNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_product");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempprod".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempprod".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function product2($sProdCd,$sProdNm,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempprod2".$xUser.$xIP."%' )
					DROP TABLE #tempprod2".$xUser.$xIP."
			
			SELECT	DISTINCT n = IDENTITY(INT, 1, 1), fs_kd_product, fs_nm_product
			INTO	#tempprod2".$xUser.$xIP."
            FROM 	tm_product (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fb_active = '1'
				AND	LTRIM(RTRIM(fs_kd_product)) <> ''
			");
		
		if (trim($sProdCd) <> '' or trim($sProdNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_product LIKE '%".trim($sProdCd)."%'
					OR fs_nm_product LIKE '%".trim($sProdNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_product");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempprod2".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempprod2".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function product_mutasi($sProdCd,$sProdNm,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempprod3".$xUser.$xIP."%' )
					DROP TABLE #tempprod3".$xUser.$xIP."
			
			SELECT	a.fs_kd_product, a.fs_nm_product,
					ISNULL(b.fs_kd_unit, '') fs_kd_unit, ISNULL(c.fs_nm_unit, '') fs_nm_unit, ISNULL(c.fn_convertion, 0) fn_convertion
			INTO	#tempprod3".$xUser.$xIP."
            FROM 	tm_product a (NOLOCK)
			LEFT JOIN tx_unitproduct b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_product = b.fs_kd_product
			LEFT JOIN tm_unitconvertion c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND b.fs_kd_unit = c.fs_kd_unit
				AND c.fb_active = 1
            WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fb_active = '1'
			");
		
		if (trim($sProdCd) <> '' or trim($sProdNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_product LIKE '%".trim($sProdCd)."%'
					OR a.fs_nm_product LIKE '%".trim($sProdNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_product");
		
		$xSQL =	$xSQL.("
			SELECT 	n = IDENTITY(INT, 1, 1), *
			INTO	#tempprod31".$xUser.$xIP."
			FROM 	#tempprod3".$xUser.$xIP."
			
			SELECT	* FROM #tempprod31".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempprod3".$xUser.$xIP."
			DROP TABLE #tempprod31".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function product_beli($sProdCd,$sProdNm,$sTgl,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempprod4".$xUser.$xIP."%' )
					DROP TABLE #tempprod4".$xUser.$xIP."
			
			SELECT	DISTINCT n = IDENTITY(INT, 1, 1), a.fs_kd_product, a.fs_nm_product,
					ISNULL(b.fs_kd_unit, '') fs_kd_unit, ISNULL(c.fs_nm_unit, '') fs_nm_unit, ISNULL(c.fn_convertion, 0) fn_convertion,
					fn_harga = ISNULL((
						SELECT TOP 1 CASE WHEN (h.fd_sdatec <= '".trim($sTgl)."' AND h.fd_edatec >= '".trim($sTgl)."' AND h.fb_aktivec = '1') THEN h.fn_unitprcc
									ELSE h.fn_unitprcn END
						FROM	tm_unitprcpj h (NOLOCK)
						WHERE 	h.fs_kd_comp = a.fs_kd_comp
							AND h.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
							AND h.fs_count = '".trim($this->session->userdata('gCount'))."'
							AND h.fs_kd_product = a.fs_kd_product
							AND ((h.fd_sdatec <= '".trim($sTgl)."' AND h.fd_edatec >= '".trim($sTgl)."' AND h.fb_aktivec = '1')
							OR (h.fd_sdaten <= '".trim($sTgl)."' AND h.fd_edaten >= '".trim($sTgl)."' AND h.fb_aktiven = '1'))
					), 0),
					fn_diskon = ISNULL((
						SELECT TOP 1 CASE WHEN (i.fd_sdatec <= '".trim($sTgl)."' AND i.fd_edatec >= '".trim($sTgl)."' AND i.fb_aktivec = '1') THEN i.fn_discc
									ELSE i.fn_discn END
						FROM	tm_unitprcpj i (NOLOCK)
						WHERE 	i.fs_kd_comp = a.fs_kd_comp
							AND i.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
							AND i.fs_count = '".trim($this->session->userdata('gCount'))."'
							AND i.fs_kd_product = a.fs_kd_product
							AND ((i.fd_sdatec <= '".trim($sTgl)."' AND i.fd_edatec >= '".trim($sTgl)."' AND i.fb_aktivec = '1')
							OR (i.fd_sdaten <= '".trim($sTgl)."' AND i.fd_edaten >= '".trim($sTgl)."' AND i.fb_aktiven = '1'))
					), 0)
			INTO	#tempprod4".$xUser.$xIP."
            FROM 	tm_product a (NOLOCK)
			INNER JOIN tx_unitproduct b (NOLOCK) ON a.fs_kd_product = b.fs_kd_product
			LEFT JOIN tm_unitconvertion c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND b.fs_kd_unit = c.fs_kd_unit
				AND c.fb_active = 1
            WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fb_active = '1'
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_product LIKE '%".trim($sCari)."%'
					OR a.fs_nm_product LIKE  '%".trim($sCari)."%')
			");
		}

		if (trim($sProdCd) <> '' or trim($sProdNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_product LIKE '%".trim($sProdCd)."%'
					OR a.fs_nm_product LIKE '%".trim($sProdNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_product");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempprod4".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempprod4".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function product_master_all($sProdCd,$sProdNm,$sCari)
	{
		$xSQL = ("
			SELECT	a.fs_kd_product, a.fs_nm_product,
					ISNULL(b.fs_chasis, '') fs_rangka,
					ISNULL(b.fs_engine, '') fs_mesin,
					ISNULL(b.fs_cilinder, 0) fn_cc,
					a.fs_kd_group, ISNULL(c.fs_nm_group, '') fs_nm_group,
					a.fs_kd_acno, ISNULL(d.fs_nm_acno, '') fs_nm_acno,
					a.fs_kd_acnox, ISNULL(e.fs_nm_acno, '') fs_nm_acnox,
					a.fb_active, fs_status = CASE a.fb_active WHEN '1' THEN 'ACTIVE' ELSE 'NON ACTIVE' END
            FROM 	tm_product a (NOLOCK)
			LEFT JOIN tm_motortp b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_product = b.fs_kd_product
			LEFT JOIN tm_grpacno c (NOLOCK) ON a.fs_kd_group = c.fs_kd_group
			LEFT JOIN tm_acno d (NOLOCK) ON a.fs_kd_acno = d.fs_kd_acno
			LEFT JOIN tm_acno e (NOLOCK) ON a.fs_kd_acnox = e.fs_kd_acno
            WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	LTRIM(RTRIM(a.fs_kd_product)) <> ''
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_product LIKE '%".trim($sCari)."%'
					OR a.fs_nm_product LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sProdCd) <> '' or trim($sProdNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_product LIKE '%".trim($sProdCd)."%'
					OR a.fs_nm_product LIKE '%".trim($sProdNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_product, a.fs_nm_product");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function product_master($sProdCd,$sProdNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempprod".$xUser.$xIP."%' )
					DROP TABLE #tempprod".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), a.fs_kd_product, a.fs_nm_product,
					ISNULL(b.fs_chasis, '') fs_rangka,
					ISNULL(b.fs_engine, '') fs_mesin,
					ISNULL(b.fs_cilinder, 0) fn_cc,
					a.fs_kd_group, ISNULL(c.fs_nm_group, '') fs_nm_group,
					a.fs_kd_acno, ISNULL(d.fs_nm_acno, '') fs_nm_acno,
					a.fs_kd_acnox, ISNULL(e.fs_nm_acno, '') fs_nm_acnox,
					a.fb_active, fs_status = CASE a.fb_active WHEN '1' THEN 'ACTIVE' ELSE 'NON ACTIVE' END
			INTO	#tempprod".$xUser.$xIP."
            FROM 	tm_product a (NOLOCK)
			LEFT JOIN tm_motortp b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_product = b.fs_kd_product
			LEFT JOIN tm_grpacno c (NOLOCK) ON a.fs_kd_group = c.fs_kd_group
			LEFT JOIN tm_acno d (NOLOCK) ON a.fs_kd_acno = d.fs_kd_acno
			LEFT JOIN tm_acno e (NOLOCK) ON a.fs_kd_acnox = e.fs_kd_acno
            WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	LTRIM(RTRIM(a.fs_kd_product)) <> ''
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_product LIKE '%".trim($sCari)."%'
					OR a.fs_nm_product LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sProdCd) <> '' or trim($sProdNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_product LIKE '%".trim($sProdCd)."%'
					OR a.fs_nm_product LIKE '%".trim($sProdNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_product, a.fs_nm_product");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempprod".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempprod".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function product_surat_all($sCust,$sCount,$sCari)
	{
		$xSQL = ("
			SELECT	DISTINCT ISNULL(c.fs_nm_product, '') fs_nm_product,
					LTRIM(RTRIM(c.fs_chasis)) + ' / ' + LTRIM(RTRIM(c.fs_machine)) fs_rangka,
					LTRIM(RTRIM(a.fs_nm_code)) fs_nama, c.fs_chasis, c.fs_refno
			FROM	tm_addr a (NOLOCK)
			INNER JOIN tm_customer b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_code = b.fs_kd_customer
				AND a.fs_count = b.fs_count
			INNER JOIN tm_posregsold c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND a.fs_code = c.fs_kd_cussup
				AND a.fs_count = c.fs_countcussup
			WHERE	a.fs_cdtyp = '02'
				AND b.fb_active = '1'
				AND a.fs_code = '".trim($sCust)."'
				AND a.fs_count = '".trim($sCount)."'
				AND c.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND c.fs_count = '".trim($this->session->userdata('gCount'))."'
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (c.fs_nm_product LIKE '%".trim($sCari)."%'
					OR a.fs_nm_code LIKE  '%".trim($sCari)."%')"
			);
		}

		$xSQL = $xSQL.("
			ORDER BY c.fs_nm_product, c.fs_chasis");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function product_surat($sCust,$sCount,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempprod5".$xUser.$xIP."%' )
					DROP TABLE #tempprod5".$xUser.$xIP."
			
			SELECT	DISTINCT n = IDENTITY(INT, 1, 1), ISNULL(c.fs_nm_product, '') fs_nm_product,
					LTRIM(RTRIM(c.fs_chasis)) + ' / ' + LTRIM(RTRIM(c.fs_machine)) fs_rangka,
					LTRIM(RTRIM(a.fs_nm_code)) fs_nama, c.fs_chasis, c.fs_refno
			INTO	#tempprod5".$xUser.$xIP."
			FROM	tm_addr a (NOLOCK)
			INNER JOIN tm_customer b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_code = b.fs_kd_customer
				AND a.fs_count = b.fs_count
			INNER JOIN tm_posregsold c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND a.fs_code = c.fs_kd_cussup
				AND a.fs_count = c.fs_countcussup
			WHERE	a.fs_cdtyp = '02'
				AND b.fb_active = '1'
				AND a.fs_code = '".trim($sCust)."'
				AND a.fs_count = '".trim($sCount)."'
				AND c.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND c.fs_count = '".trim($this->session->userdata('gCount'))."'
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (c.fs_nm_product LIKE '%".trim($sCari)."%'
					OR a.fs_nm_code LIKE  '%".trim($sCari)."%')"
			);
		}

		$xSQL = $xSQL.("
			ORDER BY c.fs_nm_product, c.fs_chasis");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempprod5".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempprod5".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function rangka_beli_all($sRangka,$sMesin,$sProdCd,$sCari)
	{
		$xSQL = ("
			SELECT	a.fs_kd_product, ISNULL(b.fs_nm_product, '') fs_nm_product,
					a.fs_rangka, a.fs_machine fs_mesin, a.fs_register,
					fn_silinder fs_cc, fd_thnpembuatan fs_thn,
					a.fs_kd_warna, ISNULL(c.fs_nm_vareable, '') fs_nm_warna,
					a.fs_kd_wh, ISNULL(d.fs_nm_code, '') fs_nm_wh
			FROM	tm_icregister a (NOLOCK)
			LEFT JOIN tm_product b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_type = b.fs_kd_type
				AND a.fs_kd_product = b.fs_kd_product
			LEFT JOIN tm_vareable c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND	a.fs_kd_warna = c.fs_kd_vareable
				AND	c.fs_key = '08'
			LEFT JOIN tm_addr d (NOLOCK) ON a.fs_kd_comp = d.fs_kd_comp
				AND	a.fs_kd_wh = LTRIM(RTRIM(d.fs_code)) + LTRIM(RTRIM(d.fs_count))
				AND	d.fs_cdtyp = '11'
            WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
		");
		
		if (trim($sProdCd) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_product = '".trim($sProdCd)."')
			");
		}

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_rangka LIKE '%".trim($sCari)."%'
					OR a.fs_machine LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sRangka) <> '' or trim($sMesin) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_rangka LIKE '%".trim($sRangka)."%'
					OR a.fs_machine LIKE '%".trim($sMesin)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_product, a.fs_rangka, a.fs_machine");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function rangka_beli($sRangka,$sMesin,$sProdCd,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temprangka".$xUser.$xIP."%' )
					DROP TABLE #temprangka".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), a.fs_kd_product, ISNULL(b.fs_nm_product, '') fs_nm_product,
					a.fs_rangka, a.fs_machine fs_mesin, a.fs_register,
					fn_silinder fs_cc, fd_thnpembuatan fs_thn,
					a.fs_kd_warna, ISNULL(c.fs_nm_vareable, '') fs_nm_warna,
					a.fs_kd_wh, ISNULL(d.fs_nm_code, '') fs_nm_wh
			INTO	#temprangka".$xUser.$xIP."
			FROM	tm_icregister a (NOLOCK)
			LEFT JOIN tm_product b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_type = b.fs_kd_type
				AND a.fs_kd_product = b.fs_kd_product
			LEFT JOIN tm_vareable c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND	a.fs_kd_warna = c.fs_kd_vareable
				AND	c.fs_key = '08'
			LEFT JOIN tm_addr d (NOLOCK) ON a.fs_kd_comp = d.fs_kd_comp
				AND	a.fs_kd_wh = LTRIM(RTRIM(d.fs_code)) + LTRIM(RTRIM(d.fs_count))
				AND	d.fs_cdtyp = '11'
            WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
		");
		
		if (trim($sProdCd) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_product = '".trim($sProdCd)."')
			");
		}
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_rangka LIKE '%".trim($sCari)."%'
					OR a.fs_machine LIKE '%".trim($sCari)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_product, a.fs_rangka, a.fs_machine");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temprangka".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temprangka".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function rangka_jual_all($sRefno,$sRangka,$sMesin,$sProdCd,$sWh,$sCari)
	{
		$xSQL = ("
			SELECT	a.fs_rangka, fs_mesin = a.fs_machine,
					fs_thn = a.fd_thnpembuatan, fs_cc = CONVERT(NUMERIC(5,0),a.fn_silinder), a.fs_kd_warna,
					fs_nm_warna = b.fs_nm_vareable, a.fs_register
			FROM	tm_icregister a (NOLOCK)
			INNER JOIN	tm_vareable b (NOLOCK) ON a.fs_kd_warna = b.fs_kd_vareable
					AND	b.fs_key = '08'
            WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	a.fs_kd_product = '".trim($sProdCd)."'
		");
		
		if (trim($sRefno) <> '' and trim($sRangka) <> '')
		{
		}
		else
		{
			$xSQL = $xSQL.("
				AND	LTRIM(RTRIM(a.fs_refnoinv)) = ''
				AND	a.fs_kd_trxl <> '3400'
			");
		}
		
		if (trim($sWh) <> '')
		{
			$xSQL = $xSQL.("
				AND	a.fs_kd_wh = '".trim($sWh)."'
			");
		}
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_rangka LIKE '%".trim($sCari)."%'
					OR a.fs_machine LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sRangka) <> '' or trim($sMesin) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_rangka LIKE '%".trim($sRangka)."%'
					OR a.fs_machine LIKE '%".trim($sMesin)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_rangka, fs_machine
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function rangka_jual($sRefno,$sRangka,$sMesin,$sProdCd,$sWh,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temprangka".$xUser.$xIP."%' )
					DROP TABLE #temprangka".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), a.fs_rangka, fs_mesin = a.fs_machine,
					fs_thn = a.fd_thnpembuatan, fs_cc = CONVERT(NUMERIC(5,0),a.fn_silinder), a.fs_kd_warna,
					fs_nm_warna = b.fs_nm_vareable, a.fs_register
			INTO	#temprangka".$xUser.$xIP."
			FROM	tm_icregister a (NOLOCK)
			INNER JOIN	tm_vareable b (NOLOCK) ON a.fs_kd_warna = b.fs_kd_vareable
					AND	b.fs_key = '08'
            WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	a.fs_kd_product = '".trim($sProdCd)."'
		");
		
		if (trim($sRefno) <> '' and trim($sRangka) <> '')
		{
		}
		else
		{
			$xSQL = $xSQL.("
				AND	LTRIM(RTRIM(a.fs_refnoinv)) = ''
				AND	a.fs_kd_trxl <> '3400'
			");
		}
		
		if (trim($sWh) <> '')
		{
			$xSQL = $xSQL.("
				AND	a.fs_kd_wh = '".trim($sWh)."'
			");
		}
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_rangka LIKE '%".trim($sCari)."%'
					OR a.fs_machine LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sRangka) <> '' or trim($sMesin) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_rangka LIKE '%".trim($sRangka)."%'
					OR a.fs_machine LIKE '%".trim($sMesin)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_rangka, fs_machine
		");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temprangka".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temprangka".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function rangka_jual2_all($sRangka,$sMesin,$sCari)
	{
		$xSQL = ("
			SELECT	a.fs_rangka, fs_mesin = a.fs_machine,
					fs_thn = a.fd_thnpembuatan, fs_cc = CONVERT(NUMERIC(5,0),a.fn_silinder), a.fs_kd_warna,
					fs_nm_warna = b.fs_nm_vareable, a.fs_register,
					a.fs_kd_product, ISNULL(c.fs_nm_product, '') fs_nm_product
			FROM	tm_icregister a (NOLOCK)
			INNER JOIN	tm_vareable b (NOLOCK) ON a.fs_kd_warna = b.fs_kd_vareable
					AND	b.fs_key = '08'
			LEFT JOIN	tm_product c (NOLOCK) ON a.fs_kd_product = c.fs_kd_product
            WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	LTRIM(RTRIM(a.fs_refnoinv)) <> ''
		");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_rangka LIKE '%".trim($sCari)."%'
					OR a.fs_machine LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sRangka) <> '' or trim($sMesin) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_rangka LIKE '%".trim($sRangka)."%'
					OR a.fs_machine LIKE '%".trim($sMesin)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_rangka, fs_machine");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function rangka_jual2($sRangka,$sMesin,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temprangka".$xUser.$xIP."%' )
					DROP TABLE #temprangka".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), a.fs_rangka, fs_mesin = a.fs_machine,
					fs_thn = a.fd_thnpembuatan, fs_cc = CONVERT(NUMERIC(5,0),a.fn_silinder), a.fs_kd_warna,
					fs_nm_warna = b.fs_nm_vareable, a.fs_register,
					a.fs_kd_product, ISNULL(c.fs_nm_product, '') fs_nm_product
			INTO	#temprangka".$xUser.$xIP."
			FROM	tm_icregister a (NOLOCK)
			INNER JOIN	tm_vareable b (NOLOCK) ON a.fs_kd_warna = b.fs_kd_vareable
					AND	b.fs_key = '08'
			LEFT JOIN	tm_product c (NOLOCK) ON a.fs_kd_product = c.fs_kd_product
            WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	LTRIM(RTRIM(a.fs_refnoinv)) <> ''
		");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_rangka LIKE '%".trim($sCari)."%'
					OR a.fs_machine LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sRangka) <> '' or trim($sMesin) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_rangka LIKE '%".trim($sRangka)."%'
					OR a.fs_machine LIKE '%".trim($sMesin)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_rangka, fs_machine");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temprangka".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temprangka".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function sales_all($sCode,$sName,$sCari)
	{
		$xSQL = ("
			SELECT 	[fs_code] = ISNULL(LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)), ''),
					[fs_nm_code] = ISNULL(fs_nm_code, ''),[fs_kd_sales]=ISNULL(LTRIM(RTRIM(fs_code)),''),[fs_count]=ISNULL(LTRIM(RTRIM(fs_count)),'')
			FROM 	tm_addr(NOLOCK)
			WHERE 	fs_cdtyp = '05'
				AND	fs_code = '".trim($this->session->userdata('gGudang'))."'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)) LIKE '%".trim($sCari)."%'
					OR fs_nm_code LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sCode) <> '' or trim($sName) <> '')
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)) LIKE '%".trim($sCode)."%'
					OR fs_nm_code LIKE '%".trim($sName)."%')
				");
		}
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function sales($sCode,$sName,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempsales".$xUser.$xIP."%' )
					DROP TABLE #tempsales".$xUser.$xIP."
			
			SELECT 	n = IDENTITY(INT, 1, 1), [fs_code] = ISNULL(LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)), ''),
					[fs_nm_code] = ISNULL(fs_nm_code, ''),[fs_kd_sales]=ISNULL(LTRIM(RTRIM(fs_code)),''),[fs_count]=ISNULL(LTRIM(RTRIM(fs_count)),'')
			INTO	#tempsales".$xUser.$xIP."
			FROM 	tm_addr(NOLOCK)
			WHERE 	fs_cdtyp = '05'
				AND	fs_code = '".trim($this->session->userdata('gGudang'))."'
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)) LIKE '%".trim($sCari)."%'
					OR fs_nm_code LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sCode) <> '' or trim($sName) <> '')
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)) LIKE '%".trim($sCode)."%'
					OR fs_nm_code LIKE '%".trim($sName)."%')
				");
		}
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempsales".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempsales".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function sales_mtd_all($sCode,$sName,$sCari)
	{
		$xSQL = ("
			SELECT	fs_kd_vareable, fs_nm_vareable
            FROM 	tm_vareable (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_key = '98'
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sCari)."%'
					OR fs_nm_vareable LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sCode) <> '' or trim($sName) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sCode)."%'
					OR fs_nm_vareable LIKE '%".trim($sName)."%')
				");
		}
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function sales_mtd($sCode,$sName,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempsalesmtd".$xUser.$xIP."%' )
					DROP TABLE #tempsalesmtd".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_vareable, fs_nm_vareable
			INTO	#tempsalesmtd".$xUser.$xIP."
            FROM 	tm_vareable (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_key = '98'
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sCari)."%'
					OR fs_nm_vareable LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sCode) <> '' or trim($sName) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sCode)."%'
					OR fs_nm_vareable LIKE '%".trim($sName)."%')
				");
		}
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempsalesmtd".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempsalesmtd".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function status_pelihara_all($sRefno)
	{
        $xSQL = ("
			SELECT 	fs_refno, fd_refno = CONVERT(VARCHAR(10), CONVERT(DATETIME, fd_refno, 105), 105),
					fs_docno, fd_docno = CONVERT(VARCHAR(10), CONVERT(DATETIME, fd_docno, 105), 105)
			FROM   	tx_unitstatus (NOLOCK)
			WHERE  	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");
		
		if (trim($sRefno) <> '')
		{
			$xSQL = $xSQL.("
				AND	fs_refno LIKE '%".trim($sRefno)."%'
			");
		}
		$xSQL = $xSQL.("
			ORDER BY fs_refno DESC");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function status_pelihara($sRefno,$nStart)
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
			
			SELECT 	n = IDENTITY(INT, 1, 1), fs_refno, fd_refno = CONVERT(VARCHAR(10), CONVERT(DATETIME, fd_refno, 105), 105),
					fs_docno, fd_docno = CONVERT(VARCHAR(10), CONVERT(DATETIME, fd_docno, 105), 105)
			INTO	#tempstatus".$xUser.$xIP."
			FROM   	tx_unitstatus (NOLOCK)
			WHERE  	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");
		
		if (trim($sRefno) <> '')
		{
			$xSQL = $xSQL.("
				AND	fs_refno LIKE '%".trim($sRefno)."%'
			");
		}
		$xSQL = $xSQL.("
			ORDER BY fs_refno DESC");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempstatus".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempstatus".$xUser.$xIP);
			
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function survey_all($sCode,$sName,$sCari)
	{
		$xSQL = ("
			SELECT 	[fs_code] = ISNULL(LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)), ''),
					[fs_nm_code] = ISNULL(fs_nm_code, '')
			FROM 	tm_addr(NOLOCK)
			WHERE 	fs_cdtyp = '80'
				AND	fs_code = '".trim($this->session->userdata('gGudang'))."'
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)) LIKE '%".trim($sCari)."%'
					OR fs_nm_code LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sCode) <> '' or trim($sName) <> '')
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)) LIKE '%".trim($sCode)."%'
					OR fs_nm_code LIKE '%".trim($sName)."%')
				");
		}
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function survey($sCode,$sName,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempsurvey".$xUser.$xIP."%' )
					DROP TABLE #tempsurvey".$xUser.$xIP."
			
			SELECT 	n = IDENTITY(INT, 1, 1), [fs_code] = ISNULL(LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)), ''),
					[fs_nm_code] = ISNULL(fs_nm_code, '')
			INTO	#tempsurvey".$xUser.$xIP."
			FROM 	tm_addr(NOLOCK)
			WHERE 	fs_cdtyp = '80'
				AND	fs_code = '".trim($this->session->userdata('gGudang'))."'
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)) LIKE '%".trim($sCari)."%'
					OR fs_nm_code LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sCode) <> '' or trim($sName) <> '')
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)) LIKE '%".trim($sCode)."%'
					OR fs_nm_code LIKE '%".trim($sName)."%')
				");
		}
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempsurvey".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempsurvey".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function supp_beli_all($sSuppCd,$sSuppNm, $sCari)
	{
		$xSQL = ("
			SELECT 	[fs_code] = ISNULL(LTRIM(RTRIM(a.fs_code)), '') + ISNULL(LTRIM(RTRIM(a.fs_count)), ''),
					[fs_kd_supp] = ISNULL(LTRIM(RTRIM(a.fs_code)), ''), [fs_count] = ISNULL(LTRIM(RTRIM(a.fs_count)), ''),
					[fs_nm_code] = a.fs_nm_code,
					a.fs_idcard, a.fs_addr
            FROM 	tm_addr a (NOLOCK), tm_supplier b (NOLOCK)
            WHERE 	a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_cdtyp = '01'
                AND a.fs_code = b.fs_kd_supplier
				AND a.fs_count = b.fs_count
				AND b.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
                AND b.fb_active = '1'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_code LIKE '%".trim($sCari)."%'
					OR a.fs_nm_code LIKE  '%".trim($sCari)."%')");

		}

		if (trim($sSuppCd) <> '' or trim($sSuppNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(a.fs_code)) + LTRIM(RTRIM(a.fs_count)) LIKE '%".trim($sSuppCd)."%'
					OR a.fs_nm_code LIKE '%".trim($sSuppNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_nm_code");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function supp_beli($sSuppCd,$sSuppNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temp".$xUser.$xIP."%' )
					DROP TABLE #temp".$xUser.$xIP."
			
			SELECT 	n = IDENTITY(INT, 1, 1), [fs_code] = ISNULL(LTRIM(RTRIM(a.fs_code)), '') + ISNULL(LTRIM(RTRIM(a.fs_count)), ''),
					[fs_kd_supp] = ISNULL(LTRIM(RTRIM(a.fs_code)), ''), [fs_count] = ISNULL(LTRIM(RTRIM(a.fs_count)), ''),
					[fs_nm_code] = a.fs_nm_code,
					a.fs_idcard, a.fs_addr
			INTO	#temp".$xUser.$xIP."
            FROM 	tm_addr a (NOLOCK), tm_supplier b (NOLOCK)
            WHERE 	a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_cdtyp = '01'
                AND a.fs_code = b.fs_kd_supplier
				AND a.fs_count = b.fs_count
				AND b.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
                AND b.fb_active = '1'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_code LIKE '%".trim($sCari)."%'
					OR a.fs_nm_code LIKE  '%".trim($sCari)."%')");

		}

		if (trim($sSuppCd) <> '' or trim($sSuppNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(a.fs_code)) + LTRIM(RTRIM(a.fs_count)) LIKE '%".trim($sSuppCd)."%'
					OR a.fs_nm_code LIKE '%".trim($sSuppNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_nm_code");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temp".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temp".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function tax_all($sCode,$sName,$sCari)
	{
		$xSQL = ("
			SELECT	fs_kd_tax, fs_nm_tax, fn_persen = fn_percent, fn_round
            FROM 	tm_tax (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_tax LIKE '%".trim($sCari)."%'
					OR fs_nm_tax LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sCode) <> '' or trim($sName) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_tax LIKE '%".trim($sCode)."%'
					OR fs_nm_tax LIKE '%".trim($sName)."%')
				");
		}
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function tax($sCode,$sName,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temptax".$xUser.$xIP."%' )
					DROP TABLE #temptax".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_tax, fs_nm_tax, fn_persen = CONVERT(NUMERIC(5,2), fn_percent), fn_round
			INTO	#temptax".$xUser.$xIP."
            FROM 	tm_tax (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_tax LIKE '%".trim($sCari)."%'
					OR fs_nm_tax LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sCode) <> '' or trim($sName) <> '')
		{
			$xSQL = $xSQL.("
				AND ((fs_kd_tax) LIKE '%".trim($sCode)."%'
					OR fs_nm_tax LIKE '%".trim($sName)."%')
				");
		}
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temptax".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temptax".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function term_all($sCode,$sName,$sCari)
	{
		$xSQL = ("
			SELECT	fs_kd_term, fs_nm_term
            FROM 	tm_term (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fb_active = '1'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_term LIKE '%".trim($sCari)."%'
					OR fs_nm_term LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sCode) <> '' or trim($sName) <> '')
		{
			$xSQL = $xSQL.("
				AND ((fs_kd_term) LIKE '%".trim($sCode)."%'
					OR fs_nm_term LIKE '%".trim($sName)."%')
				");
		}
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function term($sCode,$sName,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempterm".$xUser.$xIP."%' )
					DROP TABLE #tempterm".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_term, fs_nm_term
			INTO	#tempterm".$xUser.$xIP."
            FROM 	tm_term (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fb_active = '1'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_term LIKE '%".trim($sCari)."%'
					OR fs_nm_term LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sCode) <> '' or trim($sName) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_term LIKE '%".trim($sCode)."%'
					OR fs_nm_term LIKE '%".trim($sName)."%')
				");
		}
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempterm".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempterm".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function unit_all($sProdCd)
	{
		// $xSQL = ("
			// SELECT	a.fs_kd_unit, ISNULL(b.fs_nm_unit, '') fs_nm_unit, ISNULL(b.fn_convertion, 0) fn_convertion
			// FROM 	tx_unitproduct a (NOLOCK)
			// LEFT JOIN tm_unitconvertion b (NOLOCK) ON a.fs_kd_unit = b.fs_kd_unit
				// AND b.fb_active = 1
			// WHERE 	a.fs_kd_product = '".trim($sProdCd)."'
			// ");
		$xSQL = ("
			SELECT	'UNIT' fs_kd_unit, 'UNIT' fs_nm_unit
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function unit($sProdCd,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempunit".$xUser.$xIP."%' )
					DROP TABLE #tempunit".$xUser.$xIP."
			
			SELECT	DISTINCT n = IDENTITY(INT, 1, 1), a.fs_kd_unit, ISNULL(b.fs_nm_unit, '') fs_nm_unit, ISNULL(b.fn_convertion, 0) fn_convertion
			INTO	#tempunit".$xUser.$xIP."
            FROM 	tx_unitproduct a (NOLOCK)
			LEFT JOIN tm_unitconvertion b (NOLOCK) ON a.fs_kd_unit = b.fs_kd_unit
				AND b.fb_active = 1
            WHERE 	a.fs_kd_product = '".trim($sProdCd)."'
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempunit".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempunit".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function unit2_all($sProdCd)
	{
		$xSQL = ("
			SELECT	fs_kd_unit
			FROM 	tx_unitproduct (NOLOCK)
			WHERE 	fs_kd_product = '".trim($sProdCd)."'
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function warna_all($sCode,$sName,$sCari)
	{
		$xSQL = ("
			SELECT	fs_kd_vareable, fs_nm_vareable
            FROM 	tm_vareable (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_key = '08'
			");

		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sCari)."%'
					OR fs_nm_vareable LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sCode) <> '' or trim($sName) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sCode)."%'
					OR fs_nm_vareable LIKE '%".trim($sName)."%')
				");
		}
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function warna($sCode,$sName,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempwarna".$xUser.$xIP."%' )
					DROP TABLE #tempwarna".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_vareable, fs_nm_vareable
			INTO	#tempwarna".$xUser.$xIP."
            FROM 	tm_vareable (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_key = '08'
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sCari)."%'
					OR fs_nm_vareable LIKE '%".trim($sCari)."%')
				");
		}

		if (trim($sCode) <> '' or trim($sName) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_vareable LIKE '%".trim($sCode)."%'
					OR fs_nm_vareable LIKE '%".trim($sName)."%')
				");
		}
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempwarna".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempwarna".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function wh_all($sComp,$sDeptCd,$sCount,$sWHCd,$sWHNm,$sCari)
	{
		$xSQL = ("
			SELECT	DISTINCT [fs_code] = ISNULL(LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)), ''),
					[fs_nm_code] = ISNULL(UPPER(fs_nm_code), '')
            FROM	tm_addr (NOLOCK)
            WHERE 	fs_cdtyp = '11'
		");
		
		if (trim($sComp) <> '')
		{
			$xSQL = $xSQL.("
				AND fs_kd_comp = '".trim($sComp)."'
			");
		}
		else
		{
			$xSQL = $xSQL.("
				AND fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");
		}
		
		if (trim($sDeptCd) == '' and $sCount == '')
		{
			$xSQL = $xSQL.("
				AND fs_code = '".trim($this->session->userdata('gGudang'))."'
			");
		}
		else
		{
			$xSQL = $xSQL.("
				AND fs_code = '".$sDeptCd.$sCount."'
			");
		}
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)) LIKE '%".trim($sCari)."%'
					OR fs_nm_code LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sWHCd) <> '' or trim($sWHNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)) LIKE '%".trim($sWHCd)."%'
					OR fs_nm_code LIKE '%".trim($sWHNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_code
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function wh($sComp,$sDeptCd,$sCount,$sWHCd,$sWHNm,$nStart,$sCari)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempwh".$xUser.$xIP."%' )
					DROP TABLE #tempwh".$xUser.$xIP."
			
			SELECT	DISTINCT n = IDENTITY(INT, 1, 1), [fs_code] = ISNULL(LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)), ''),
					[fs_nm_code] = ISNULL(UPPER(fs_nm_code), '')
			INTO	#tempwh".$xUser.$xIP."
            FROM	tm_addr (NOLOCK)
            WHERE 	fs_cdtyp = '11'
		");
		
		if (trim($sComp) <> '')
		{
			$xSQL = $xSQL.("
				AND fs_kd_comp = '".trim($sComp)."'
			");
		}
		else
		{
			$xSQL = $xSQL.("
				AND fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");
		}
		
		if (trim($sDeptCd) == '' and $sCount == '')
		{
			$xSQL = $xSQL.("
				AND fs_code = '".trim($this->session->userdata('gGudang'))."'
			");
		}
		else
		{
			$xSQL = $xSQL.("
				AND fs_code = '".$sDeptCd.$sCount."'
			");
		}
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)) LIKE '%".trim($sCari)."%'
					OR fs_nm_code LIKE '%".trim($sCari)."%')
			");
		}

		if (trim($sWHCd) <> '' or trim($sWHNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)) LIKE '%".trim($sWHCd)."%'
					OR fs_nm_code LIKE '%".trim($sWHNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_code
		");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempwh".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempwh".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function valid_userpass($KdComp,$KdUser,$Pass)
	{
		$sSQL = $this->db->query("
			SELECT	ISNULL(fs_kd_user, '') fs_kd_user, ISNULL(fs_nm_user, '') fs_nm_user, ISNULL(fs_password, '') fs_password
			FROM	tm_user (NOLOCK)
			WHERE	fs_kd_comp = '".$KdComp."'
				AND	fs_kd_user = '".$KdUser."'
				AND	fs_password = '".$Pass."'
			");
		return $sSQL;
	}
	
	/* apk	*/
	function kodeapk_all($sRefno,$sNmKons)
	{
		$xSQL = ("
			SELECT	a.fs_kd_comp, a.fs_kd_dept, a.fs_count,
					a.fs_refno, fd_refno = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105),
					a.fs_kd_jns_konsumen, ISNULL(b.fs_nm_variable, '') AS fs_nm_jns_konsumen,
					a.fs_kd_jns_piutang, ISNULL(c.fs_nm_variable, '') AS fs_nm_jns_piutang,
					a.fs_kd_pola_trs, ISNULL(d.fs_nm_variable, '') AS fs_nm_pola_trs,
					a.fs_kd_jns_paket, ISNULL(e.fs_nm_variable, '') AS fs_nm_jns_paket,
					a.fs_nm_konsumen, a.fs_no_ktp, fd_ktp = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_ktp, 105), 105),
					a.fs_no_telp, a.fs_no_hp, a.fs_alamat,
					a.fs_kelurahan, a.fs_kecamatan, a.fs_kd_kabupaten, ISNULL(f.fs_nm_variable, '') AS fs_nm_kabupaten,
					a.fs_kota, a.fs_kd_pos, a.fs_no_npwp,
					fs_kd_spt = CASE a.fb_status_spt WHEN 0 THEN '0' ELSE '1' END,
					fs_nm_spt = CASE a.fb_status_spt WHEN 0 THEN 'TIDAK' ELSE 'YA' END,
					g.fs_nm_pt, g.fs_alamat_pt, g.fs_tlp_pt,
					g.fs_kd_kat_usaha AS fs_kd_katusaha, ISNULL(h.fs_nm_variable, '') AS fs_nm_katusaha,
					fs_kd_katktr = CASE g.fn_kategori_pt WHEN '0' THEN '0' WHEN '1' THEN '1' WHEN '2' THEN '2' END,
					fs_nm_katktr = CASE g.fn_kategori_pt WHEN '0' THEN 'KECIL' WHEN '1' THEN 'MENENGAH' WHEN '2' THEN 'BESAR' END,
					fs_kd_kondisiktr = CASE g.fn_kondisi_ktr WHEN '0' THEN '0' WHEN '1' THEN '1' WHEN '2' THEN '2' END,
					fs_nm_kondisiktr = CASE g.fn_kondisi_ktr WHEN '0' THEN 'KECIL' WHEN '1' THEN 'MENENGAH' WHEN '2' THEN 'LUX' END,
					g.fs_kd_usaha AS fs_kd_kerja, ISNULL(i.fs_nm_variable, '') AS fs_nm_kerja,
					g.fs_ket_usaha, fd_kerja = RIGHT(g.fd_kerja, 2) +'-'+ LEFT(g.fd_kerja, 4),
					g.fn_pendapatan, g.fs_tempat_lahir, fd_tgllahir = CONVERT(VARCHAR(10), CONVERT(DATETIME, g.fd_tgl_lahir, 105), 105),
					fs_kd_jk = CASE g.fn_jns_kelamin WHEN '0' THEN '0' WHEN '1' THEN '1' END,
					fs_nm_jk = CASE g.fn_jns_kelamin WHEN '0' THEN 'LAKI - LAKI' WHEN '1' THEN 'PEREMPUAN' END,
					g.fs_kd_agama, ISNULL(j.fs_nm_variable, '') AS fs_nm_agama,
					g.fs_nm_ibu, g.fs_kd_pendidikan, ISNULL(k.fs_nm_variable, '') AS fs_nm_pendidikan,
					fs_kd_kawin = CASE g.fn_status_kawin WHEN '0' THEN '0' WHEN '1' THEN '1' WHEN '2' THEN '2' WHEN '3' THEN '3' END,
					fs_nm_kawin = CASE g.fn_status_kawin WHEN '0' THEN 'BELUM / TIDAK KAWIN' WHEN '1' THEN 'KAWIN' WHEN '2' THEN 'JANDA / DUDA' WHEN '3' THEN 'CERAI' END,
					g.fn_tanggungan, g.fs_status_rumah AS fs_kd_rumah, ISNULL(l.fs_nm_variable, '') AS fs_nm_rumah,
					fd_tinggal = RIGHT(g.fd_tinggal, 2) +'-'+ LEFT(g.fd_tinggal, 4),
					g.fn_biaya_bulan, g.fs_alamat_surat, g.fs_kota AS fs_kotakons,
					g.fs_kd_pos AS fs_kd_poskons, g.fn_jml_kendaraan, g.fn_kredit_ke,
					fs_kd_garasi = CASE g.fb_garasi_rumah WHEN '0' THEN '0' WHEN '1' THEN '1' END,
					fs_nm_garasi = CASE g.fb_garasi_rumah WHEN '0' THEN 'TIDAK' WHEN '1' THEN 'YA' END,
					fs_kd_kalikredit = CASE g.fn_kali_kredit WHEN '0' THEN '0' WHEN '1' THEN '1'  WHEN '2' THEN '2' WHEN '3' THEN '3' END,
					fs_nm_kalikredit = CASE g.fn_kali_kredit WHEN '0' THEN 'TIDAK, TIDAK ADA BUKTI' WHEN '1' THEN 'TIDAK, ADA BUKTI' WHEN '2' THEN 'YA' WHEN '3' THEN 'REPEAT' END,
					fs_kd_kondisilingk = CASE g.fn_kondisi_lingk WHEN '0' THEN '0' WHEN '1' THEN '1'  WHEN '2' THEN '2' END,
					fs_nm_kondisilingk = CASE g.fn_kondisi_lingk WHEN '0' THEN 'SEDERHANA' WHEN '1' THEN 'MENENGAH'  WHEN '2' THEN 'LUX' END,
					m.fs_kd_jns_kend AS fs_kd_kend, ISNULL(n.fs_nm_variable, '') AS fs_nm_kend,
					m.fs_rangka, m.fs_mesin, m.fn_silinder,
					m.fn_tahun, m.fs_warna,
					fs_kd_komersial = CASE m.fb_komersial WHEN '0' THEN '0' WHEN '1' THEN '1' END,
					fs_nm_komersial = CASE m.fb_komersial WHEN '0' THEN 'TIDAK' WHEN '1' THEN 'YA' END,
					fs_kd_namabpkb = CASE m.fb_nm_bpkb WHEN '0' THEN '0' WHEN '1' THEN '1' END,
					fs_nm_namabpkb = CASE m.fb_nm_bpkb WHEN '0' THEN 'TIDAK' WHEN '1' THEN 'YA' END,
					m.fs_no_bpkb, m.fs_nopol, m.fs_nm_bpkb,
					m.fs_alm_bpkb, m.fs_kd_kota_bpkb, ISNULL(o.fs_nm_variable, '') AS fs_nm_kota_bpkb,
					m.fs_kd_lbg_keu AS fs_kd_lemkeu, ISNULL(p.fs_nm_variable, '') AS fs_nm_lemkeu,
					fs_kd_ass = CASE m.fs_kd_jns_ass WHEN '0' THEN '0' WHEN '1' THEN '1' END,
					fs_nm_ass = CASE m.fs_kd_jns_ass WHEN '0' THEN 'TOTAL LOSS ONLY' WHEN '1' THEN 'ALL RISK' END,
					m.fs_kd_supplier +'-'+ m.fs_kd_supplier2 AS fs_kd_supp, ISNULL(q.fs_nm_dealer, '') AS fs_nm_supp,
					m.fs_kd_cabang, ISNULL(r.fs_nm_cabang, '') AS fs_nm_cabang,
					m.fs_sales
			FROM	tx_apkheader a
			LEFT JOIN tm_variableapk b ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_jns_konsumen = b.fs_kd_variable
				AND b.fs_key = '02'
			LEFT JOIN tm_variableapk c ON a.fs_kd_comp = c.fs_kd_comp
				AND a.fs_kd_jns_piutang = c.fs_kd_variable
				AND c.fs_key = '03'
			LEFT JOIN tm_variableapk d ON a.fs_kd_comp = d.fs_kd_comp
				AND a.fs_kd_pola_trs = d.fs_kd_variable
				AND d.fs_key = '04'
			LEFT JOIN tm_variableapk e ON a.fs_kd_comp = e.fs_kd_comp
				AND a.fs_kd_jns_paket = e.fs_kd_variable
				AND e.fs_key = '05'
			LEFT JOIN tm_variableapk f ON a.fs_kd_comp = f.fs_kd_comp
				AND a.fs_kd_kabupaten = f.fs_kd_variable
				AND f.fs_key = '09'
			INNER JOIN tx_apkkonsumen g ON a.fs_refno = g.fs_refno
			LEFT JOIN tm_variableapk h ON a.fs_kd_comp = h.fs_kd_comp
				AND g.fs_kd_kat_usaha = h.fs_kd_variable
				AND h.fs_key = '16'
			LEFT JOIN tm_variableapk i ON a.fs_kd_comp = i.fs_kd_comp
				AND g.fs_kd_usaha = i.fs_kd_variable
				AND i.fs_key = '15'
			LEFT JOIN tm_variableapk j ON a.fs_kd_comp = j.fs_kd_comp
				AND g.fs_kd_agama = j.fs_kd_variable
				AND j.fs_key = '06'
			LEFT JOIN tm_variableapk k ON a.fs_kd_comp = k.fs_kd_comp
				AND g.fs_kd_pendidikan = k.fs_kd_variable
				AND k.fs_key = '07'
			LEFT JOIN tm_variableapk l ON a.fs_kd_comp = l.fs_kd_comp
				AND g.fs_status_rumah = l.fs_kd_variable
				AND l.fs_key = '08'
			INNER JOIN tx_apkkendaraan m ON a.fs_refno = m.fs_refno
			LEFT JOIN tm_variableapk n ON  a.fs_kd_comp = n.fs_kd_comp
				AND	m.fs_kd_jns_kend = n.fs_kd_variable
				AND n.fs_key = '10'
			LEFT JOIN tm_variableapk o ON a.fs_kd_comp = o.fs_kd_comp
				AND	m.fs_kd_kota_bpkb = o.fs_kd_variable
				AND o.fs_key = '11'
			LEFT JOIN tm_variableapk p ON a.fs_kd_comp = p.fs_kd_comp
				AND	m.fs_kd_lbg_keu = p.fs_kd_variable
				AND p.fs_key = '12'
			LEFT JOIN tm_dealer q ON a.fs_kd_comp = q.fs_kd_comp
				AND	m.fs_kd_supplier = q.fs_kd_dealer
				AND	m.fs_kd_supplier2 = q.fs_kd_dealer2
			LEFT JOIN tm_dealer2 r ON q.fs_kd_dealer = r.fs_kd_dealer
				AND	q.fs_kd_dealer2 = r.fs_kd_dealer2
				AND	m.fs_kd_cabang = r.fs_kd_cabang
			");
		
		if (trim($sRefno) <> '' or trim($sNmKons) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sRefno)."%'
					OR a.fs_nm_konsumen LIKE '%".trim($sNmKons)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_refno DESC
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodeapk($sRefno,$sNmKons,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempapk".$xUser.$xIP."%' )
					DROP TABLE #tempapk".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), a.fs_kd_comp, a.fs_kd_dept, a.fs_count,
					a.fs_refno, fd_refno = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105),
					a.fs_kd_jns_konsumen, ISNULL(b.fs_nm_variable, '') AS fs_nm_jns_konsumen,
					a.fs_kd_jns_piutang, ISNULL(c.fs_nm_variable, '') AS fs_nm_jns_piutang,
					a.fs_kd_pola_trs, ISNULL(d.fs_nm_variable, '') AS fs_nm_pola_trs,
					a.fs_kd_jns_paket, ISNULL(e.fs_nm_variable, '') AS fs_nm_jns_paket,
					a.fs_nm_konsumen, a.fs_no_ktp, fd_ktp = CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_ktp, 105), 105),
					a.fs_no_telp, a.fs_no_hp, a.fs_alamat,
					a.fs_kelurahan, a.fs_kecamatan, a.fs_kd_kabupaten, ISNULL(f.fs_nm_variable, '') AS fs_nm_kabupaten,
					a.fs_kota, a.fs_kd_pos, a.fs_no_npwp,
					fs_kd_spt = CASE a.fb_status_spt WHEN 0 THEN '0' ELSE '1' END,
					fs_nm_spt = CASE a.fb_status_spt WHEN 0 THEN 'TIDAK' ELSE 'YA' END,
					g.fs_nm_pt, g.fs_alamat_pt, g.fs_tlp_pt,
					g.fs_kd_kat_usaha AS fs_kd_katusaha, ISNULL(h.fs_nm_variable, '') AS fs_nm_katusaha,
					fs_kd_katktr = CASE g.fn_kategori_pt WHEN '0' THEN '0' WHEN '1' THEN '1' WHEN '2' THEN '2' END,
					fs_nm_katktr = CASE g.fn_kategori_pt WHEN '0' THEN 'KECIL' WHEN '1' THEN 'MENENGAH' WHEN '2' THEN 'BESAR' END,
					fs_kd_kondisiktr = CASE g.fn_kondisi_ktr WHEN '0' THEN '0' WHEN '1' THEN '1' WHEN '2' THEN '2' END,
					fs_nm_kondisiktr = CASE g.fn_kondisi_ktr WHEN '0' THEN 'KECIL' WHEN '1' THEN 'MENENGAH' WHEN '2' THEN 'LUX' END,
					g.fs_kd_usaha AS fs_kd_kerja, ISNULL(i.fs_nm_variable, '') AS fs_nm_kerja,
					g.fs_ket_usaha, fd_kerja = RIGHT(g.fd_kerja, 2) +'-'+ LEFT(g.fd_kerja, 4),
					g.fn_pendapatan, g.fs_tempat_lahir, fd_tgllahir = CONVERT(VARCHAR(10), CONVERT(DATETIME, g.fd_tgl_lahir, 105), 105),
					fs_kd_jk = CASE g.fn_jns_kelamin WHEN '0' THEN '0' WHEN '1' THEN '1' END,
					fs_nm_jk = CASE g.fn_jns_kelamin WHEN '0' THEN 'LAKI - LAKI' WHEN '1' THEN 'PEREMPUAN' END,
					g.fs_kd_agama, ISNULL(j.fs_nm_variable, '') AS fs_nm_agama,
					g.fs_nm_ibu, g.fs_kd_pendidikan, ISNULL(k.fs_nm_variable, '') AS fs_nm_pendidikan,
					fs_kd_kawin = CASE g.fn_status_kawin WHEN '0' THEN '0' WHEN '1' THEN '1' WHEN '2' THEN '2' WHEN '3' THEN '3' END,
					fs_nm_kawin = CASE g.fn_status_kawin WHEN '0' THEN 'BELUM / TIDAK KAWIN' WHEN '1' THEN 'KAWIN' WHEN '2' THEN 'JANDA / DUDA' WHEN '3' THEN 'CERAI' END,
					g.fn_tanggungan, g.fs_status_rumah AS fs_kd_rumah, ISNULL(l.fs_nm_variable, '') AS fs_nm_rumah,
					fd_tinggal = RIGHT(g.fd_tinggal, 2) +'-'+ LEFT(g.fd_tinggal, 4),
					g.fn_biaya_bulan, g.fs_alamat_surat, g.fs_kota AS fs_kotakons,
					g.fs_kd_pos AS fs_kd_poskons, g.fn_jml_kendaraan, g.fn_kredit_ke,
					fs_kd_garasi = CASE g.fb_garasi_rumah WHEN '0' THEN '0' WHEN '1' THEN '1' END,
					fs_nm_garasi = CASE g.fb_garasi_rumah WHEN '0' THEN 'TIDAK' WHEN '1' THEN 'YA' END,
					fs_kd_kalikredit = CASE g.fn_kali_kredit WHEN '0' THEN '0' WHEN '1' THEN '1'  WHEN '2' THEN '2' WHEN '3' THEN '3' END,
					fs_nm_kalikredit = CASE g.fn_kali_kredit WHEN '0' THEN 'TIDAK, TIDAK ADA BUKTI' WHEN '1' THEN 'TIDAK, ADA BUKTI' WHEN '2' THEN 'YA' WHEN '3' THEN 'REPEAT' END,
					fs_kd_kondisilingk = CASE g.fn_kondisi_lingk WHEN '0' THEN '0' WHEN '1' THEN '1'  WHEN '2' THEN '2' END,
					fs_nm_kondisilingk = CASE g.fn_kondisi_lingk WHEN '0' THEN 'SEDERHANA' WHEN '1' THEN 'MENENGAH'  WHEN '2' THEN 'LUX' END,
					m.fs_kd_jns_kend AS fs_kd_kend, ISNULL(n.fs_nm_variable, '') AS fs_nm_kend,
					m.fs_rangka, m.fs_mesin, m.fn_silinder,
					m.fn_tahun, m.fs_warna,
					fs_kd_komersial = CASE m.fb_komersial WHEN '0' THEN '0' WHEN '1' THEN '1' END,
					fs_nm_komersial = CASE m.fb_komersial WHEN '0' THEN 'TIDAK' WHEN '1' THEN 'YA' END,
					fs_kd_namabpkb = CASE m.fb_nm_bpkb WHEN '0' THEN '0' WHEN '1' THEN '1' END,
					fs_nm_namabpkb = CASE m.fb_nm_bpkb WHEN '0' THEN 'TIDAK' WHEN '1' THEN 'YA' END,
					m.fs_no_bpkb, m.fs_nopol, m.fs_nm_bpkb,
					m.fs_alm_bpkb, m.fs_kd_kota_bpkb, ISNULL(o.fs_nm_variable, '') AS fs_nm_kota_bpkb,
					m.fs_kd_lbg_keu AS fs_kd_lemkeu, ISNULL(p.fs_nm_variable, '') AS fs_nm_lemkeu,
					fs_kd_ass = CASE m.fs_kd_jns_ass WHEN '0' THEN '0' WHEN '1' THEN '1' END,
					fs_nm_ass = CASE m.fs_kd_jns_ass WHEN '0' THEN 'TOTAL LOSS ONLY' WHEN '1' THEN 'ALL RISK' END,
					m.fs_kd_supplier +'-'+ m.fs_kd_supplier2 AS fs_kd_supp, ISNULL(q.fs_nm_dealer, '') AS fs_nm_supp,
					m.fs_kd_cabang, ISNULL(r.fs_nm_cabang, '') AS fs_nm_cabang,
					m.fs_sales
			INTO	#tempapk".$xUser.$xIP."
			FROM	tx_apkheader a
			LEFT JOIN tm_variableapk b ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_jns_konsumen = b.fs_kd_variable
				AND b.fs_key = '02'
			LEFT JOIN tm_variableapk c ON a.fs_kd_comp = c.fs_kd_comp
				AND a.fs_kd_jns_piutang = c.fs_kd_variable
				AND c.fs_key = '03'
			LEFT JOIN tm_variableapk d ON a.fs_kd_comp = d.fs_kd_comp
				AND a.fs_kd_pola_trs = d.fs_kd_variable
				AND d.fs_key = '04'
			LEFT JOIN tm_variableapk e ON a.fs_kd_comp = e.fs_kd_comp
				AND a.fs_kd_jns_paket = e.fs_kd_variable
				AND e.fs_key = '05'
			LEFT JOIN tm_variableapk f ON a.fs_kd_comp = f.fs_kd_comp
				AND a.fs_kd_kabupaten = f.fs_kd_variable
				AND f.fs_key = '09'
			INNER JOIN tx_apkkonsumen g ON a.fs_refno = g.fs_refno
			LEFT JOIN tm_variableapk h ON a.fs_kd_comp = h.fs_kd_comp
				AND g.fs_kd_kat_usaha = h.fs_kd_variable
				AND h.fs_key = '16'
			LEFT JOIN tm_variableapk i ON a.fs_kd_comp = i.fs_kd_comp
				AND g.fs_kd_usaha = i.fs_kd_variable
				AND i.fs_key = '15'
			LEFT JOIN tm_variableapk j ON a.fs_kd_comp = j.fs_kd_comp
				AND g.fs_kd_agama = j.fs_kd_variable
				AND j.fs_key = '06'
			LEFT JOIN tm_variableapk k ON a.fs_kd_comp = k.fs_kd_comp
				AND g.fs_kd_pendidikan = k.fs_kd_variable
				AND k.fs_key = '07'
			LEFT JOIN tm_variableapk l ON a.fs_kd_comp = l.fs_kd_comp
				AND g.fs_status_rumah = l.fs_kd_variable
				AND l.fs_key = '08'
			INNER JOIN tx_apkkendaraan m ON a.fs_refno = m.fs_refno
			LEFT JOIN tm_variableapk n ON  a.fs_kd_comp = n.fs_kd_comp
				AND	m.fs_kd_jns_kend = n.fs_kd_variable
				AND n.fs_key = '10'
			LEFT JOIN tm_variableapk o ON a.fs_kd_comp = o.fs_kd_comp
				AND	m.fs_kd_kota_bpkb = o.fs_kd_variable
				AND o.fs_key = '11'
			LEFT JOIN tm_variableapk p ON a.fs_kd_comp = p.fs_kd_comp
				AND	m.fs_kd_lbg_keu = p.fs_kd_variable
				AND p.fs_key = '12'
			LEFT JOIN tm_dealer q ON a.fs_kd_comp = q.fs_kd_comp
				AND	m.fs_kd_supplier = q.fs_kd_dealer
				AND	m.fs_kd_supplier2 = q.fs_kd_dealer2
			LEFT JOIN tm_dealer2 r ON q.fs_kd_dealer = r.fs_kd_dealer
				AND	q.fs_kd_dealer2 = r.fs_kd_dealer2
				AND	m.fs_kd_cabang = r.fs_kd_cabang
			");
		
		if (trim($sRefno) <> '' or trim($sNmKons) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_refno LIKE '%".trim($sRefno)."%'
					OR a.fs_nm_konsumen LIKE '%".trim($sNmKons)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_refno DESC
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempapk".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempapk".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodejenis($sJenisCd,$sJenisNm)
	{
		$xSQL = ("
			SELECT	fs_kd_variable fs_kd_jenis, fs_nm_variable fs_nm_jenis
			FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '02'
			");
		
		if (trim($sJenisCd) <> '' or trim($sJenisNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sJenisCd)."%'
					OR fs_nm_variable LIKE '%".trim($sJenisNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_variable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodejenisp($sJenisCd,$sJenisNm)
	{
		$xSQL = ("
			SELECT	fs_kd_variable fs_kd_jenisp, fs_nm_variable fs_nm_jenisp
			FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '03'
			");
		
		if (trim($sJenisCd) <> '' or trim($sJenisNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sJenisCd)."%'
					OR fs_nm_variable LIKE '%".trim($sJenisNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_variable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodepola($sPolaCd,$sPolaNm)
	{
		$xSQL = ("
			SELECT	fs_kd_variable fs_kd_pola, fs_nm_variable fs_nm_pola
			FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '04'
			");
		
		if (trim($sPolaCd) <> '' or trim($sPolaNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sPolaCd)."%'
					OR fs_nm_variable LIKE '%".trim($sPolaNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_variable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodejenispaket_all($sJenisCd,$sJenisNm)
	{
		$xSQL = ("
			SELECT	fs_kd_variable fs_kd_jenisp, fs_nm_variable fs_nm_jenisp
			FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '05'
			");
		
		if (trim($sJenisCd) <> '' or trim($sJenisNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sJenisCd)."%'
					OR fs_nm_variable LIKE '%".trim($sJenisNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_variable, fs_kd_variable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodejenispaket($sJenisCd,$sJenisNm,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempjenisp".$xUser.$xIP."%' )
					DROP TABLE #tempjenisp".$xUser.$xIP."
			
			SELECT	DISTINCT n = IDENTITY(INT, 1, 1), fs_kd_variable fs_kd_jenisp, fs_nm_variable fs_nm_jenisp
			INTO	#tempjenisp".$xUser.$xIP."
            FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '05'
			");
		
		if (trim($sJenisCd) <> '' or trim($sJenisNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sJenisCd)."%'
					OR fs_nm_variable LIKE '%".trim($sJenisNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_variable, fs_kd_variable
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempjenisp".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempjenisp".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodekab2_all($sKabCd,$sKabNm)
	{
		$xSQL = ("
			SELECT	fs_kd_variable fs_kd_kab, fs_nm_variable fs_nm_kab
			FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '09'
			");
		
		if (trim($sKabCd) <> '' or trim($sKabNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sKabCd)."%'
					OR fs_nm_variable LIKE '%".trim($sKabNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_variable, fs_kd_variable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodekab2($sKabCd,$sKabNm,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempkab".$xUser.$xIP."%' )
					DROP TABLE #tempkab".$xUser.$xIP."
			
			SELECT	DISTINCT n = IDENTITY(INT, 1, 1), fs_kd_variable fs_kd_kab, fs_nm_variable fs_nm_kab
			INTO	#tempkab".$xUser.$xIP."
            FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '09'
			");
		
		if (trim($sKabCd) <> '' or trim($sKabNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sKabCd)."%'
					OR fs_nm_variable LIKE '%".trim($sKabNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_variable, fs_kd_variable
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempkab".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempkab".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodekatusaha_all($sKatCd,$sKatNm)
	{
		$xSQL = ("
			SELECT	fs_kd_variable fs_kd_katusaha, fs_nm_variable fs_nm_katusaha
			FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '16'
			");
		
		if (trim($sKatCd) <> '' or trim($sKatNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sKatCd)."%'
					OR fs_nm_variable LIKE '%".trim($sKatNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_variable, fs_kd_variable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodekatusaha($sKatCd,$sKatNm,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempkatusaha".$xUser.$xIP."%' )
					DROP TABLE #tempkatusaha".$xUser.$xIP."
			
			SELECT	DISTINCT n = IDENTITY(INT, 1, 1), fs_kd_variable fs_kd_katusaha, fs_nm_variable fs_nm_katusaha
			INTO	#tempkatusaha".$xUser.$xIP."
            FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '16'
			");
		
		if (trim($sKatCd) <> '' or trim($sKatNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sKatCd)."%'
					OR fs_nm_variable LIKE '%".trim($sKatNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_variable, fs_kd_variable
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempkatusaha".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempkatusaha".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodekerja_all($sKerjaCd,$sKerjaNm)
	{
		$xSQL = ("
			SELECT	fs_kd_variable fs_kd_kerja, fs_nm_variable fs_nm_kerja
			FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '15'
			");
		
		if (trim($sKerjaCd) <> '' or trim($sKerjaNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sKerjaCd)."%'
					OR fs_nm_variable LIKE '%".trim($sKerjaNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_variable, fs_kd_variable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodekerja($sKerjaCd,$sKerjaNm,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempkerja".$xUser.$xIP."%' )
					DROP TABLE #tempkerja".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_variable fs_kd_kerja, fs_nm_variable fs_nm_kerja
			INTO	#tempkerja".$xUser.$xIP."
            FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '15'
			");
		
		if (trim($sKerjaCd) <> '' or trim($sKerjaNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sKerjaCd)."%'
					OR fs_nm_variable LIKE '%".trim($sKerjaNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_variable, fs_kd_variable
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempkerja".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempkerja".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodeagama($sAgamaCd,$sAgamaNm)
	{
		$xSQL = ("
			SELECT	fs_kd_variable fs_kd_agama, fs_nm_variable fs_nm_agama
			FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '06'
			");
		
		if (trim($sAgamaCd) <> '' or trim($sAgamaNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sAgamaCd)."%'
					OR fs_nm_variable LIKE '%".trim($sAgamaNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_variable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodependidikan_all($sPddkanCd,$sPddkanNm)
	{
		$xSQL = ("
			SELECT	fs_kd_variable fs_kd_pendidikan, fs_nm_variable fs_nm_pendidikan
			FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '07'
			");
		
		if (trim($sPddkanCd) <> '' or trim($sPddkanNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sPddkanCd)."%'
					OR fs_nm_variable LIKE '%".trim($sPddkanNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_variable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodependidikan($sPddkanCd,$sPddkanNm,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temppddkan".$xUser.$xIP."%' )
					DROP TABLE #temppddkan".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_variable fs_kd_pendidikan, fs_nm_variable fs_nm_pendidikan
			INTO	#temppddkan".$xUser.$xIP."
            FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '07'
			");
		
		if (trim($sPddkanCd) <> '' or trim($sPddkanNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sPddkanCd)."%'
					OR fs_nm_variable LIKE '%".trim($sPddkanNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_variable
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temppddkan".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temppddkan".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function koderumah_all($sRumahCd,$sRumahNm)
	{
		$xSQL = ("
			SELECT	fs_kd_variable fs_kd_rumah, fs_nm_variable fs_nm_rumah
			FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '08'
			");
		
		if (trim($sRumahCd) <> '' or trim($sRumahNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sRumahCd)."%'
					OR fs_nm_variable LIKE '%".trim($sRumahNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_variable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function koderumah($sRumahCd,$sRumahNm,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temprmh".$xUser.$xIP."%' )
					DROP TABLE #temprmh".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_variable fs_kd_rumah, fs_nm_variable fs_nm_rumah
			INTO	#temprmh".$xUser.$xIP."
            FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '08'
			");
		
		if (trim($sRumahCd) <> '' or trim($sRumahNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sRumahCd)."%'
					OR fs_nm_variable LIKE '%".trim($sRumahNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_variable
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temprmh".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temprmh".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodekend_all($sKendCd,$sKendNm)
	{
		$xSQL = ("
			SELECT	fs_kd_variable fs_kd_kend, fs_nm_variable fs_nm_kend
			FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '10'
			");
		
		if (trim($sKendCd) <> '' or trim($sKendNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sKendCd)."%'
					OR fs_nm_variable LIKE '%".trim($sKendNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_variable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodekend($sKendCd,$sKendNm,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempkend".$xUser.$xIP."%' )
					DROP TABLE #tempkend".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_variable fs_kd_kend, fs_nm_variable fs_nm_kend
			INTO	#tempkend".$xUser.$xIP."
            FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '10'
			");
		
		if (trim($sKendCd) <> '' or trim($sKendNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sKendCd)."%'
					OR fs_nm_variable LIKE '%".trim($sKendNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_variable
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempkend".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempkend".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodekota2_all($sKotaCd,$sKotaNm)
	{
		$xSQL = ("
			SELECT	fs_kd_variable fs_kd_kota, fs_nm_variable fs_nm_kota
			FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '11'
			");
		
		if (trim($sKotaCd) <> '' or trim($sKotaNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sKotaCd)."%'
					OR fs_nm_variable LIKE '%".trim($sKotaNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_variable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodekota2($sKotaCd,$sKotaNm,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempkota".$xUser.$xIP."%' )
					DROP TABLE #tempkota".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_variable fs_kd_kota, fs_nm_variable fs_nm_kota
			INTO	#tempkota".$xUser.$xIP."
            FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '11'
			");
		
		if (trim($sKotaCd) <> '' or trim($sKotaNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sKotaCd)."%'
					OR fs_nm_variable LIKE '%".trim($sKotaNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_variable
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempkota".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempkota".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodelemkeu_all($sLemKeuCd,$sLemKeuNm)
	{
		$xSQL = ("
			SELECT	fs_kd_variable fs_kd_lemkeu, fs_nm_variable fs_nm_lemkeu
			FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '12'
			");
		
		if (trim($sLemKeuCd) <> '' or trim($sLemKeuNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sLemKeuCd)."%'
					OR fs_nm_variable LIKE '%".trim($sLemKeuNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_variable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodelemkeu($sLemKeuCd,$sLemKeuNm,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempkota".$xUser.$xIP."%' )
					DROP TABLE #tempkota".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_variable fs_kd_lemkeu, fs_nm_variable fs_nm_lemkeu
			INTO	#tempkota".$xUser.$xIP."
            FROM	tm_variableapk (NOLOCK)
			WHERE	fs_key = '12'
			");
		
		if (trim($sLemKeuCd) <> '' or trim($sLemKeuNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_variable LIKE '%".trim($sLemKeuCd)."%'
					OR fs_nm_variable LIKE '%".trim($sLemKeuNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_variable
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempkota".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempkota".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodesupp_all($sSuppCd,$sSuppNm)
	{
		$xSQL = ("
			SELECT	fs_kd_dealer +'-'+ fs_kd_dealer2 fs_kd_supp, fs_nm_dealer fs_nm_supp
			FROM	tm_dealer (NOLOCK)
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");
		
		if (trim($sSuppCd) <> '' or trim($sSuppNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_dealer +'-'+ fs_kd_dealer2 LIKE '%".trim($sSuppCd)."%'
					OR fs_nm_dealer LIKE '%".trim($sSuppNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_dealer
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodesupp($sSuppCd,$sSuppNm,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempsupp".$xUser.$xIP."%' )
					DROP TABLE #tempsupp".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_dealer +'-'+ fs_kd_dealer2 fs_kd_supp, fs_nm_dealer fs_nm_supp
			INTO	#tempsupp".$xUser.$xIP."
            FROM	tm_dealer (NOLOCK)
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			");
		
		if (trim($sSuppCd) <> '' or trim($sSuppNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_dealer +'-'+ fs_kd_dealer2 LIKE '%".trim($sSuppCd)."%'
					OR fs_nm_dealer LIKE '%".trim($sSuppNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_dealer
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempsupp".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempsupp".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodecabang_all($sSuppCd,$sCabCd,$sCabNm)
	{
		$xSQL = ("
			SELECT	fs_kd_cabang, fs_nm_cabang
			FROM	tm_dealer2 (NOLOCK)
			WHERE 	fs_kd_dealer +'-'+ fs_kd_dealer2 = '".trim($sSuppCd)."'
			");
		
		if (trim($sCabCd) <> '' or trim($sCabNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_cabang LIKE '%".trim($sCabCd)."%'
					OR fs_nm_cabang LIKE '%".trim($sCabNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_cabang
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kodecabang($sSuppCd,$sCabCd,$sCabNm,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempcabang".$xUser.$xIP."%' )
					DROP TABLE #tempcabang".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_cabang, fs_nm_cabang
			INTO	#tempcabang".$xUser.$xIP."
            FROM	tm_dealer2 (NOLOCK)
			WHERE 	fs_kd_dealer +'-'+ fs_kd_dealer2 = '".trim($sSuppCd)."'
			");
		
		if (trim($sCabCd) <> '' or trim($sCabNm) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_cabang LIKE '%".trim($sCabCd)."%'
					OR fs_nm_cabang LIKE '%".trim($sCabNm)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_cabang
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempcabang".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempcabang".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	/* Eof apk	*/
}

?>