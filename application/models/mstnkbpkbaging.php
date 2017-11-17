<?php

class MStnkBpkbAging extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function grid_all($sTgl,$sTgl2,$sCustCd,$sCount,$sCustCd2,$sCount2)
	{
        $xSQL = ("
			SELECT	fs_kd_dept = CONVERT(VARCHAR(15), LTRIM(RTRIM(aa.fs_kd_dept)) + LTRIM(RTRIM(aa.fs_count))),
					fs_nm_dept = ISNULL(cc.fs_nm_code, ''),
					fs_kd_cust = bb.fs_kd_cussup, fs_count = bb.fs_countcussup,
					fs_nm_cust = ISNULL(dd.fs_nm_code, ''),
					fd_serah = CONVERT(VARCHAR(10), CONVERT(DATETIME, aa.fd_refno, 105), 105),
					fn_jumlah = CONVERT(VARCHAR(20), DATEDIFF(month, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE())) + ' Month(s)',
					[hr0_7] = ISNULL((    SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 0
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 7
								), ''),
					[hr8_14] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 7
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 14
								), ''),
					[hr15_21] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 15
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 21
								), ''),
					[hr22_28] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 22
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 28
								), ''),
					[hr29_35] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 29
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 35
								), ''),
					[hr36_42] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 36
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 42
								), ''),
					[hr43_49] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 43
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 49
								), ''),
					[hr50_56] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 50
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 56
								), ''),
					[hr57_63] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + ltrim(rtrim(hh.fs_count)) = ltrim(rtrim(bb.fs_kd_cussup)) + ltrim(rtrim(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 57
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 63
								), ''),
					[hr63] = ISNULL((    SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) > 63
								), '')
			FROM	tx_trxbirojs aa (NOLOCK)
			INNER JOIN tx_trxbirojsd bb (NOLOCK) ON aa.fs_kd_comp = bb.fs_kd_comp
				AND aa.fs_refno = bb.fs_refno
			LEFT JOIN  tm_addr cc (NOLOCK) ON aa.fs_kd_comp = cc.fs_kd_comp
				AND LTRIM(RTRIM(aa.fs_kd_dept)) +  LTRIM(RTRIM(aa.fs_count)) = LTRIM(RTRIM(cc.fs_code)) + LTRIM(RTRIM(cc.fs_count))
				AND cc.fs_cdtyp = '03'
			LEFT JOIN  tm_addr dd (NOLOCK) ON bb.fs_kd_comp = dd.fs_kd_comp
				AND LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup)) = LTRIM(RTRIM(dd.fs_code)) + LTRIM(RTRIM(dd.fs_count))
				AND dd.fs_cdtyp = '02'
			WHERE      aa.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND LTRIM(RTRIM(bb.fd_rcvdt)) = ''
				AND aa.fs_kd_strx = '0100'
				AND aa.fd_refno >= '".trim($sTgl)."'
				AND aa.fd_refno <= '".trim($sTgl2)."'
				AND	dd.fs_nm_code <> ''
			");
		
		if (trim($sCustCd) <> '' and trim($sCustCd2) == '')
		{
			$xSQL = $xSQL.("
				AND bb.fs_kd_cussup = '".trim($sCustCd)."'
				AND	bb.fs_countcussup = '".trim($sCount)."'
			");
		}
		
		if (trim($sCustCd) <> '' and trim($sCustCd2) <> '')
		{
			$xSQL = $xSQL.("
				AND LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup)) IN ('".trim($sCustCd).trim($sCount)."','".trim($sCustCd2).trim($sCount2)."')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY dd.fs_nm_code");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid($sTgl,$sTgl2,$sCustCd,$sCount,$sCustCd2,$sCount2,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
        $xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempstnk".$xUser.$xIP."%' )
					DROP TABLE #tempstnk".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_dept = CONVERT(VARCHAR(15), LTRIM(RTRIM(aa.fs_kd_dept)) + LTRIM(RTRIM(aa.fs_count))),
					fs_nm_dept = ISNULL(cc.fs_nm_code, ''),
					fs_kd_cust = bb.fs_kd_cussup, fs_count = bb.fs_countcussup,
					fs_nm_cust = ISNULL(dd.fs_nm_code, ''),
					fd_serah = CONVERT(VARCHAR(10), CONVERT(DATETIME, aa.fd_refno, 105), 105),
					fs_jumlah = CONVERT(VARCHAR(20), DATEDIFF(month, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE())) + ' Month(s)',
					[0-7] = ISNULL((    SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 0
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 7
								), ''),
					[8-14] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 7
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 14
								), ''),
					[15-21] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 15
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 21
								), ''),
					[22-28] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 22
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 28
								), ''),
					[29-35] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 29
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 35
								), ''),
					[36-42] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 36
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 42
								), ''),
					[43-49] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 43
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 49
								), ''),
					[50-56] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 50
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 56
								), ''),
					[57-63] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + ltrim(rtrim(hh.fs_count)) = ltrim(rtrim(bb.fs_kd_cussup)) + ltrim(rtrim(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 57
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 63
								), ''),
					[>63] = ISNULL((    SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) > 63
								), '')
			INTO	#tempstnk".$xUser.$xIP."
			FROM	tx_trxbirojs aa (NOLOCK)
			INNER JOIN tx_trxbirojsd bb (NOLOCK) ON aa.fs_kd_comp = bb.fs_kd_comp
				AND aa.fs_refno = bb.fs_refno
			LEFT JOIN  tm_addr cc (NOLOCK) ON aa.fs_kd_comp = cc.fs_kd_comp
				AND LTRIM(RTRIM(aa.fs_kd_dept)) +  LTRIM(RTRIM(aa.fs_count)) = LTRIM(RTRIM(cc.fs_code)) + LTRIM(RTRIM(cc.fs_count))
				AND cc.fs_cdtyp = '03'
			LEFT JOIN  tm_addr dd (NOLOCK) ON bb.fs_kd_comp = dd.fs_kd_comp
				AND LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup)) = LTRIM(RTRIM(dd.fs_code)) + LTRIM(RTRIM(dd.fs_count))
				AND dd.fs_cdtyp = '02'
			WHERE      aa.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND LTRIM(RTRIM(bb.fd_rcvdt)) = ''
				AND aa.fs_kd_strx = '0100'
				AND aa.fd_refno >= '".trim($sTgl)."'
				AND aa.fd_refno <= '".trim($sTgl2)."'
				AND	dd.fs_nm_code <> ''
			");
		
		if (trim($sCustCd) <> '' and trim($sCustCd2) == '')
		{
			$xSQL = $xSQL.("
				AND bb.fs_kd_cussup = '".trim($sCustCd)."'
				AND	bb.fs_countcussup = '".trim($sCount)."'
			");
		}
		
		if (trim($sCustCd) <> '' and trim($sCustCd2) <> '')
		{
			$xSQL = $xSQL.("
				AND LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup)) IN ('".trim($sCustCd).trim($sCount)."','".trim($sCustCd2).trim($sCount2)."')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY dd.fs_nm_code");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempstnk".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempstnk".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid2_all($sTgl,$sTgl2,$sCustCd,$sCount,$sCustCd2,$sCount2)
	{
        $xSQL = ("
			SELECT	fs_kd_dept = CONVERT(VARCHAR(15), LTRIM(RTRIM(aa.fs_kd_dept)) + LTRIM(RTRIM(aa.fs_count))),
					fs_nm_dept = ISNULL(cc.fs_nm_code, ''),
					fs_kd_cust = bb.fs_kd_cussup, fs_count = bb.fs_countcussup,
					fs_nm_cust = ISNULL(dd.fs_nm_code, ''),
					fd_serah = CONVERT(VARCHAR(10), CONVERT(DATETIME, aa.fd_refno, 105), 105),
					fn_jumlah = CONVERT(VARCHAR(20), DATEDIFF(month, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE())) + ' Month(s)',
					[hr0_30] = ISNULL((    SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 0
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 30
								), ''),
					[hr31_60] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 31
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 60
								), ''),
					[hr61_90] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 61
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 90
								), ''),
					[hr91_120] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 91
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 120
								), ''),
					[hr120] = ISNULL((    SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) > 120
								), '')
			FROM	tx_trxbirojs aa (NOLOCK)
			INNER JOIN tx_trxbirojsd bb (NOLOCK) ON aa.fs_kd_comp = bb.fs_kd_comp
				AND aa.fs_refno = bb.fs_refno
			LEFT JOIN  tm_addr cc (NOLOCK) ON aa.fs_kd_comp = cc.fs_kd_comp
				AND LTRIM(RTRIM(aa.fs_kd_dept)) +  LTRIM(RTRIM(aa.fs_count)) = LTRIM(RTRIM(cc.fs_code)) + LTRIM(RTRIM(cc.fs_count))
				AND cc.fs_cdtyp = '03'
			LEFT JOIN  tm_addr dd (NOLOCK) ON bb.fs_kd_comp = dd.fs_kd_comp
				AND LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup)) = LTRIM(RTRIM(dd.fs_code)) + LTRIM(RTRIM(dd.fs_count))
				AND dd.fs_cdtyp = '02'
			WHERE      aa.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND LTRIM(RTRIM(bb.fd_rcvdt2)) = ''
				AND aa.fs_kd_strx = '0100'
				AND aa.fd_refno >= '".trim($sTgl)."'
				AND aa.fd_refno <= '".trim($sTgl2)."'
				AND	dd.fs_nm_code <> ''
			");
		
		if (trim($sCustCd) <> '' and trim($sCustCd2) == '')
		{
			$xSQL = $xSQL.("
				AND bb.fs_kd_cussup = '".trim($sCustCd)."'
				AND	bb.fs_countcussup = '".trim($sCount)."'
			");
		}
		
		if (trim($sCustCd) <> '' and trim($sCustCd2) <> '')
		{
			$xSQL = $xSQL.("
				AND LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup)) IN ('".trim($sCustCd).trim($sCount)."','".trim($sCustCd2).trim($sCount2)."')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY dd.fs_nm_code");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid2($sTgl,$sTgl2,$sCustCd,$sCount,$sCustCd2,$sCount2,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
        $xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempstnk".$xUser.$xIP."%' )
					DROP TABLE #tempstnk".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_dept = CONVERT(VARCHAR(15), LTRIM(RTRIM(aa.fs_kd_dept)) + LTRIM(RTRIM(aa.fs_count))),
					fs_nm_dept = ISNULL(cc.fs_nm_code, ''),
					fs_kd_cust = bb.fs_kd_cussup, fs_count = bb.fs_countcussup,
					fs_nm_cust = ISNULL(dd.fs_nm_code, ''),
					fd_serah = CONVERT(VARCHAR(10), CONVERT(DATETIME, aa.fd_refno, 105), 105),
					fs_jumlah = CONVERT(VARCHAR(20), DATEDIFF(month, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE())) + ' Month(s)',
					[0-30] = ISNULL((    SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 0
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 30
								), ''),
					[31-60] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 31
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 60
								), ''),
					[61-90] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 61
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 90
								), ''),
					[91-120] = ISNULL((   SELECT  '*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) >= 91
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) <= 120
								), ''),
					[>120] = ISNULL((	SELECT	'*'
									FROM    tm_addr hh
									WHERE   LTRIM(RTRIM(hh.fs_code)) + LTRIM(RTRIM(hh.fs_count)) = LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup))
										AND hh.fs_cdtyp = '02'
										AND DATEDIFF(day, CONVERT(DATETIME, aa.fd_refno, 105), GETDATE()) > 120
								), '')
			INTO	#tempstnk".$xUser.$xIP."
			FROM	tx_trxbirojs aa (NOLOCK)
			INNER JOIN tx_trxbirojsd bb (NOLOCK) ON aa.fs_kd_comp = bb.fs_kd_comp
				AND aa.fs_refno = bb.fs_refno
			LEFT JOIN  tm_addr cc (NOLOCK) ON aa.fs_kd_comp = cc.fs_kd_comp
				AND LTRIM(RTRIM(aa.fs_kd_dept)) +  LTRIM(RTRIM(aa.fs_count)) = LTRIM(RTRIM(cc.fs_code)) + LTRIM(RTRIM(cc.fs_count))
				AND cc.fs_cdtyp = '03'
			LEFT JOIN  tm_addr dd (NOLOCK) ON bb.fs_kd_comp = dd.fs_kd_comp
				AND LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup)) = LTRIM(RTRIM(dd.fs_code)) + LTRIM(RTRIM(dd.fs_count))
				AND dd.fs_cdtyp = '02'
			WHERE      aa.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND LTRIM(RTRIM(bb.fd_rcvdt2)) = ''
				AND aa.fs_kd_strx = '0100'
				AND aa.fd_refno >= '".trim($sTgl)."'
				AND aa.fd_refno <= '".trim($sTgl2)."'
				AND	dd.fs_nm_code <> ''
			");
		
		if (trim($sCustCd) <> '' and trim($sCustCd2) == '')
		{
			$xSQL = $xSQL.("
				AND bb.fs_kd_cussup = '".trim($sCustCd)."'
				AND	bb.fs_countcussup = '".trim($sCount)."'
			");
		}
		
		if (trim($sCustCd) <> '' and trim($sCustCd2) <> '')
		{
			$xSQL = $xSQL.("
				AND LTRIM(RTRIM(bb.fs_kd_cussup)) + LTRIM(RTRIM(bb.fs_countcussup)) IN ('".trim($sCustCd).trim($sCount)."','".trim($sCustCd2).trim($sCount2)."')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY dd.fs_nm_code");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempstnk".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempstnk".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>