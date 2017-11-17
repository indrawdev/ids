<?php

class MRegStatus extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function regstatus($sWH,$sProd,$sRangka,$bJual,$bAll)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempreg".$xUser.$xIP."%' )
					DROP TABLE #tempreg".$xUser.$xIP."
					
			CREATE TABLE #tempreg".$xUser.$xIP."(
				fs_kd_comp		VARCHAR(10),
				fd_thn			VARCHAR(5),
				fn_silinder		VARCHAR(5),
				fs_kd_product	VARCHAR(50),
				fs_kd_type		VARCHAR(1),
				fs_nm_product	VARCHAR(50),
				fs_register		VARCHAR(50),
				fs_rangka		VARCHAR(50),
				fs_mesin		VARCHAR(50),
				fs_status		VARCHAR(100),
				fs_kd_dept		VARCHAR(10),
				fs_count		VARCHAR(10),
				fs_nm_dept		VARCHAR(50),
				fs_refno		VARCHAR(50),
				fs_seqno		VARCHAR(10),
				fs_kd_wh		VARCHAR(25),
				fs_nm_wh		VARCHAR(50),
				fs_statusmv		VARCHAR(25),
				fs_refnomv		VARCHAR(50),
				fs_seqnomv		VARCHAR(10),
				fs_kd_wht		VARCHAR(25),
				fs_nm_wht		VARCHAR(50),
				fn_hpp			VARCHAR(50),
				fs_kd_tipe		VARCHAR(50),
				fs_kd_warna		VARCHAR(25),
				fs_nm_tipe		VARCHAR(50),
				fs_nm_warna		VARCHAR(50)
			)
		
		INSERT INTO #tempreg".$xUser.$xIP."
		EXEC stp_regstatus ' WHERE a.fs_kd_comp = ''".trim($this->session->userdata('gComp'))."''");
		
		if (trim($sWH) <> '')
		{
			$xSQL = $xSQL.("
				AND a.fs_kd_wh = ''".trim($sWH)."''
			");
		}
		else
		{
			if (trim($bAll) == '0')
			{
				$xSQL = $xSQL.("
					AND a.fs_kd_wh LIKE ''".trim($this->session->userdata('gGudang'))."%''
				");
			}
		}
		
		if (trim($sProd) <> '')
		{
			$xSQL = $xSQL.("
				AND a.fs_kd_product = ''".trim($sProd)."''
			");
		}
		
		if (trim($sRangka) <> '')
		{
			$xSQL = $xSQL.("
				AND a.fs_rangka = ''".trim($sRangka)."''
			");
		}
		
		if (trim($bJual) == '0')
		{
			$xSQL = $xSQL.("
				AND LTRIM(RTRIM(ISNULL(a.fs_refnoinv, ''''))) = ''''
			");
		}
		else
		{
			$xSQL = $xSQL.("
				AND (LTRIM(RTRIM(ISNULL(a.fs_refnoinv, ''''))) = '''' OR LTRIM(RTRIM(ISNULL(a.fs_refnoinv, ''''))) <> '''')
			");
		}
		
		$xSQL = $xSQL.("'");
		$xSQL = $xSQL.("
			
			SELECT	UPPER(ISNULL(fs_nm_wh, '')) fs_nm_wh, ISNULL(fs_nm_product, '') fs_nm_product, ISNULL(fs_nm_warna, '') fs_nm_warna,
					ISNULL(fs_rangka, '') fs_rangka, ISNULL(fs_mesin, '') fs_mesin, ISNULL(fn_silinder, 0) fn_silinder,
					LEFT(LTRIM(RTRIM(ISNULL(fs_status, ''))), 8) fs_status, ISNULL(fd_thn, '') fd_thn
			FROM 	#tempreg".$xUser.$xIP."
			ORDER BY fs_nm_wh, fs_kd_product, fs_nm_product, fs_rangka, fs_mesin
			
			DROP TABLE #tempreg".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function regstatusdate($sDept,$sCount,$sWH,$sProd,$sRangka,$sDate)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempreg2".$xUser.$xIP."%' )
					DROP TABLE #tempreg2".$xUser.$xIP."
					
			CREATE TABLE #tempreg2".$xUser.$xIP."(
				fs_kd_comp		VARCHAR(10),
				fd_thn			VARCHAR(5),
				fn_silinder		VARCHAR(5),
				fs_kd_type		VARCHAR(1),
				fs_kd_product	VARCHAR(50),
				fs_nm_product	VARCHAR(50),
				fs_register		VARCHAR(50),
				fs_rangka		VARCHAR(50),
				fs_mesin		VARCHAR(50),
				fs_kd_dept		VARCHAR(10),
				fs_count		VARCHAR(10),
				fs_nm_dept		VARCHAR(50),
				fs_refno		VARCHAR(50),
				fs_seqno		VARCHAR(10),
				fn_hpp			VARCHAR(50),
				fs_kd_tipe		VARCHAR(50),
				fs_nm_tipe		VARCHAR(50),
				fs_kd_warna		VARCHAR(25),
				fs_nm_warna		VARCHAR(50),
				fs_kd_wh		VARCHAR(25),
				fs_nm_wh		VARCHAR(50),
				fs_kd_wht		VARCHAR(25),
				fs_nm_wht		VARCHAR(50),
				fs_status		VARCHAR(100),
				fs_statusmv		VARCHAR(25),
				fs_refnomv		VARCHAR(50),
				fs_seqnomv		VARCHAR(10)
				)
			
			INSERT INTO #tempreg2".$xUser.$xIP."
			EXEC stp_regstatusdate ' AND a.fd_refno <= ''".trim($sDate)."'' ',' AND b.fs_kd_comp = ''".trim($this->session->userdata('gComp'))."''");
			
			if (trim($sDept) <> '')
			{
				$xSQL = $xSQL.("
					AND b.fs_kd_dept = ''".trim($sDept)."''
				");
			}
			
			if (trim($sCount) <> '')
			{
				$xSQL = $xSQL.("
					AND b.fs_count = ''".trim($sCount)."''
				");
			}
			
			if (trim($sWH) <> '')
			{
				$xSQL = $xSQL.("
					AND b.fs_kd_wh = ''".trim($sWH)."''
				");
			}
			else
			{
				$xSQL = $xSQL.("
					AND b.fs_kd_wh LIKE ''".trim($this->session->userdata('gGudang'))."%''
				");
			}
			
			if (trim($sProd) <> '')
			{
				$xSQL = $xSQL.("
					AND b.fs_kd_product = ''".trim($sProd)."''
				");
			}
			
			if (trim($sRangka) <> '')
			{
				$xSQL = $xSQL.("
					AND b.fs_rangka = ''".trim($sRangka)."''
				");
			}
			
		$xSQL = $xSQL.("
			AND LTRIM(RTRIM(b.fs_refnoinv)) = ''''
		");
		
		$xSQL = $xSQL.("'");
		$xSQL = $xSQL.("
			
			SELECT	UPPER(ISNULL(fs_nm_wh, '')) fs_nm_wh, ISNULL(fs_nm_product, '') fs_nm_product, ISNULL(fs_nm_warna, '') fs_nm_warna,
					ISNULL(fs_rangka, '') fs_rangka, ISNULL(fs_mesin, '') fs_mesin, ISNULL(fn_silinder, 0) fn_silinder,
					LEFT(LTRIM(RTRIM(ISNULL(fs_status, ''))), 8) fs_status, ISNULL(fd_thn, '') fd_thn
			FROM 	#tempreg2".$xUser.$xIP."
			ORDER BY fs_nm_wh, fs_kd_product, fs_nm_product, fs_rangka, fs_mesin
			
			DROP TABLE #tempreg2".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function stoksum($sKdWH,$sKdProd)
	{
		$xSQL = ("
			SELECT 	fs_nm_wh = CASE a.fs_nm_wh WHEN '' THEN '-' ELSE a.fs_nm_wh END,
					a.fs_kd_product +' -- '+ ISNULL(b.fs_nm_product, '') fs_nm_product,
					ISNULL(c.fs_nm_vareable, '') fs_nm_warna,
					ISNULL(COUNT(a.fs_rangka), 0) fn_total
			FROM	tm_icregister a (NOLOCK)
			LEFT JOIN tm_product b (NOLOCK) ON a.fs_kd_product = b.fs_kd_product
			LEFT JOIN tm_vareable c (NOLOCK) ON a.fs_kd_warna = c.fs_kd_vareable
				AND	c.fs_key = '08'
			WHERE	LTRIM(RTRIM(a.fs_refnoinv)) = ''
				AND	a.fs_kd_trxl <> '3400'
			");
		
		if (trim($sKdWH) <> '')
		{
			$xSQL = $xSQL.("
				AND	a.fs_kd_wh = '".trim($sKdWH)."'
				");
		}
		
		if (trim($sKdProd) <> '')
		{
			$xSQL = $xSQL.("
				AND	a.fs_kd_product = '".trim($sKdProd)."'
				");
		}
		
		$xSQL = $xSQL.("
			GROUP BY a.fs_nm_wh, a.fs_kd_product, b.fs_nm_product, c.fs_nm_vareable
			ORDER BY a.fs_nm_wh, a.fs_kd_product, b.fs_nm_product, c.fs_nm_vareable
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function stokjml()
	{
		$xSQL = ("
			SELECT 	a.fs_kd_product +' -- '+ ISNULL(b.fs_nm_product, '') fs_nm_product, count(a.fs_kd_product) fn_jml
			FROM	tm_icregister a (NOLOCK)
			LEFT JOIN tm_product b (NOLOCK) ON a.fs_kd_product = b.fs_kd_product
			WHERE	LTRIM(RTRIM(a.fs_refnoinv)) = ''
				AND	a.fs_kd_trxl <> '3400'
			GROUP BY a.fs_kd_product, b.fs_nm_product
			ORDER BY a.fs_kd_product, a.fs_nm_product
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}

?>