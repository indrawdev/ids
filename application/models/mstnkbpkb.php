<?php

class MStnkBpkb extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function cek_tglstnk($sRangka,$sMesin)
	{
		$xSQL = ("
			SELECT	ISNULL(fd_stnk, '') fd_stnk,
					ISNULL(fd_bpkb, '') fd_bpkb
			FROM	tm_icregister
			WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_rangka = '".trim($sRangka)."'
				AND	fs_machine = '".trim($sMesin)."'
		");
			
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function custlist_all($sCari)
	{
		$xSQL = ("
			SELECT	DISTINCT c.fs_kd_cussup fs_kd_cust, c.fs_countcussup fs_count, f.fs_nm_code fs_nm_cust,
					f.fs_addr fs_alamat, ISNULL(e.fs_nm_product, '') fs_nm_product,
					a.fs_rangka, a.fs_machine fs_mesin,
					a.fs_stnk,
					CASE LEN(LTRIM(RTRIM(a.fd_stnk))) WHEN 0 THEN '' WHEN 8 THEN CONVERT(VARCHAR, CONVERT(DATETIME, a.fd_stnk, 105), 105) ELSE '' END fd_stnk,
					a.fs_bpkb,
					CASE LEN(LTRIM(RTRIM(a.fd_bpkb))) WHEN 0 THEN '' WHEN 8 THEN CONVERT(VARCHAR, CONVERT(DATETIME, a.fd_bpkb, 105), 105) ELSE '' END fd_bpkb,
					a.fs_nopol, c.fs_nm_pay fs_note,
					b.fs_nm_stnk_qq, b.fs_almt_stnk_qq,
					b.fs_nm_bpkb_qq, b.fs_almt_bpkb_qq
			FROM	tm_icregister a (NOLOCK)
			INNER JOIN tx_trxbirojsd b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_rangka = b.fs_chasis
				AND a.fs_machine = b.fs_engine
			INNER JOIN tm_posregsold c (NOLOCK) ON b.fs_chasis = c.fs_chasis
				AND b.fs_engine = c.fs_machine
				AND a.fs_kd_comp = c.fs_kd_comp
				AND	a.fs_kd_deptinv = c.fs_kd_dept
				AND a.fs_countdeptinv = c.fs_count
				AND a.fs_kd_trxinv = c.fs_kd_trx
				AND a.fs_refnoinv = c.fs_refno
			LEFT JOIN tm_product e (NOLOCK) ON c.fs_kd_comp = e.fs_kd_comp
				AND c.fs_kd_product = e.fs_kd_product
			LEFT JOIN tm_addr f (NOLOCK) ON c.fs_kd_comp = f.fs_kd_comp
				AND c.fs_kd_cussup = f.fs_code
				AND c.fs_countcussup = f.fs_count
				AND	f.fs_cdtyp = '02'
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND b.fd_rcvdt <> ''
		");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (f.fs_nm_code LIKE '%".trim($sCari)."%'
					OR a.fs_rangka LIKE '%".trim($sCari)."%'
					OR a.fs_machine LIKE '%".trim($sCari)."%')
			");
		}
		$xSQL = $xSQL.("
			ORDER BY f.fs_nm_code
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
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempcust".$xUser.$xIP."%' )
					DROP TABLE #tempcust".$xUser.$xIP."
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempcust2".$xUser.$xIP."%' )
					DROP TABLE #tempcust2".$xUser.$xIP."
			
			SELECT  DISTINCT c.fs_kd_cussup fs_kd_cust, c.fs_countcussup fs_count, f.fs_nm_code fs_nm_cust,
					f.fs_addr fs_alamat, ISNULL(e.fs_nm_product, '') fs_nm_product,
					a.fs_rangka, a.fs_machine fs_mesin,
					a.fs_stnk,
					CASE LEN(LTRIM(RTRIM(a.fd_stnk))) WHEN 0 THEN '' WHEN 8 THEN CONVERT(VARCHAR, CONVERT(DATETIME, a.fd_stnk, 105), 105) ELSE '' END fd_stnk,
					a.fs_bpkb,
					CASE LEN(LTRIM(RTRIM(a.fd_bpkb))) WHEN 0 THEN '' WHEN 8 THEN CONVERT(VARCHAR, CONVERT(DATETIME, a.fd_bpkb, 105), 105) ELSE '' END fd_bpkb,
					a.fs_nopol, c.fs_nm_pay fs_note,
					b.fs_nm_stnk_qq, b.fs_almt_stnk_qq,
					b.fs_nm_bpkb_qq, b.fs_almt_bpkb_qq
			INTO	#tempcust".$xUser.$xIP."
			FROM	tm_icregister a (NOLOCK)
			INNER JOIN tx_trxbirojsd b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_rangka = b.fs_chasis
				AND a.fs_machine = b.fs_engine
			INNER JOIN tm_posregsold c (NOLOCK) ON b.fs_chasis = c.fs_chasis
				AND b.fs_engine = c.fs_machine
				AND a.fs_kd_comp = c.fs_kd_comp
				AND	a.fs_kd_deptinv = c.fs_kd_dept
				AND a.fs_countdeptinv = c.fs_count
				AND a.fs_kd_trxinv = c.fs_kd_trx
				AND a.fs_refnoinv = c.fs_refno
			LEFT JOIN tm_product e (NOLOCK) ON c.fs_kd_comp = e.fs_kd_comp
				AND c.fs_kd_product = e.fs_kd_product
			LEFT JOIN tm_addr f (NOLOCK) ON c.fs_kd_comp = f.fs_kd_comp
				AND c.fs_kd_cussup = f.fs_code
				AND c.fs_countcussup = f.fs_count
				AND	f.fs_cdtyp = '02'
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND b.fd_rcvdt <> ''
		");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (f.fs_nm_code LIKE '%".trim($sCari)."%'
					OR a.fs_rangka LIKE '%".trim($sCari)."%'
					OR a.fs_machine LIKE '%".trim($sCari)."%')
			");
		}
		$xSQL = $xSQL.("
			ORDER BY f.fs_nm_code
		");
		
		$xSQL =	$xSQL.("
			SELECT 	n = IDENTITY(INT, 1, 1), *
			INTO	#tempcust2".$xUser.$xIP."
			FROM #tempcust".$xUser.$xIP."
			
			SELECT	* FROM #tempcust2".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempcust".$xUser.$xIP."
			DROP TABLE #tempcust2".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cetak_stnk($sRangka,$sMesin)
	{
		$xSQL = ("
			SELECT  dd.fs_nm_code as fs_nm_cabang,
					fs_nm_customer = cc.fs_nm_code, fs_almt_customer = cc.fs_addr,
					fs_nm_stnk_qq = CASE ISNULL(aa.fs_nm_stnk_qq, '') WHEN '' THEN cc.fs_nm_code ELSE ISNULL(aa.fs_nm_stnk_qq, '') END,
					fs_almt_stnk_qq = CASE ISNULL(aa.fs_almt_stnk_qq, '') WHEN '' THEN cc.fs_addr ELSE ISNULL(aa.fs_almt_stnk_qq, '') END,
					fs_addr = cc.fs_addr,
					aa.fs_stnk, ee.fs_nm_product,
					bb.fs_nopol, fs_chasis as fs_rangka, fs_engine as fs_mesin,
					fs_nm_vareable as fs_warna, fd_thnpembuatan as fs_tahunbuat
			FROM 	tx_trxbirojsd aa
			INNER JOIN tm_icRegister bb ON aa.fs_kd_comp = bb.fs_kd_comp
				AND	bb.fs_rangka = aa.fs_chasis
				AND	aa.fs_engine = bb.fs_machine
			LEFT JOIN tm_addr cc ON aa.fs_kd_comp = cc.fs_kd_comp
				AND	LTRIM(RTRIM(cc.fs_code)) = RTRIM(LTRIM(aa.fs_kd_cussup))
				AND	LTRIM(RTRIM(cc.fs_count)) = RTRIM(LTRIM(aa.fs_countcussup))
				AND	cc.fs_cdtyp = '02'
			LEFT JOIN tm_addr dd ON aa.fs_kd_comp = dd.fs_kd_comp
				AND LTRIM(RTRIM(dd.fs_code)) = RTRIM(LTRIM(aa.fs_kd_dept))
				AND LTRIM(RTRIM(dd.fs_count)) = RTRIM(LTRIM(aa.fs_count))
				AND dd.fs_cdtyp = '03'
			LEFT JOIN tm_product ee ON aa.fs_kd_comp = ee.fs_kd_comp
				AND	ee.fs_kd_product = bb.fs_kd_product
			LEFT JOIN tm_vareable ff ON aa.fs_kd_comp = ff.fs_kd_comp
				AND	ff.fs_kd_vareable = bb.fs_kd_warna
				AND ff.fs_key = '08'
			WHERE 	aa.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				and	bb.fs_rangka = '".trim($sRangka)."'
				AND	bb.fs_machine = '".trim($sMesin)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cetak_bpkb($sRangka,$sMesin)
	{
		$xSQL = ("
			SELECT  dd.fs_nm_code as fs_nm_cabang,
					fs_nm_customer = cc.fs_nm_code, fs_almt_customer = cc.fs_addr,
					fs_nm_bpkb_qq = CASE ISNULL(aa.fs_nm_bpkb_qq, '') WHEN '' THEN cc.fs_nm_code ELSE ISNULL(aa.fs_nm_bpkb_qq, '') END,
					fs_almt_bpkb_qq = CASE ISNULL(aa.fs_almt_bpkb_qq, '') WHEN '' THEN cc.fs_addr ELSE ISNULL(aa.fs_almt_bpkb_qq, '') END,
					fs_addr = cc.fs_addr,
					aa.fs_bpkb, ee.fs_nm_product,
					bb.fs_nopol, fs_chasis as fs_rangka, fs_engine as fs_mesin,
					fs_nm_vareable as fs_warna, fd_thnpembuatan as fs_tahunbuat
			FROM 	tx_trxbirojsd aa
			INNER JOIN tm_icRegister bb ON aa.fs_kd_comp = bb.fs_kd_comp
				AND	bb.fs_rangka = aa.fs_chasis
				AND	aa.fs_engine = bb.fs_machine
			LEFT JOIN tm_addr cc ON aa.fs_kd_comp = cc.fs_kd_comp
				AND	LTRIM(RTRIM(cc.fs_code)) = RTRIM(LTRIM(aa.fs_kd_cussup))
				AND	LTRIM(RTRIM(cc.fs_count)) = RTRIM(LTRIM(aa.fs_countcussup))
				AND	cc.fs_cdtyp = '02'
			LEFT JOIN tm_addr dd ON aa.fs_kd_comp = dd.fs_kd_comp
				AND LTRIM(RTRIM(dd.fs_code)) = RTRIM(LTRIM(aa.fs_kd_dept))
				AND LTRIM(RTRIM(dd.fs_count)) = RTRIM(LTRIM(aa.fs_count))
				AND dd.fs_cdtyp = '03'
			LEFT JOIN tm_product ee ON aa.fs_kd_comp = ee.fs_kd_comp
				AND	ee.fs_kd_product = bb.fs_kd_product
			LEFT JOIN tm_vareable ff ON aa.fs_kd_comp = ff.fs_kd_comp
				AND	ff.fs_kd_vareable = bb.fs_kd_warna
				AND ff.fs_key = '08'
			WHERE 	aa.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				and	bb.fs_rangka = '".trim($sRangka)."'
				AND	bb.fs_machine = '".trim($sMesin)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>