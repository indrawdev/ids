<?php

class MStatus extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function cek_refno($sRefno)
	{
		$xSQL = ("
			SELECT	TOP 1 fs_refno
			FROM 	tx_unitstatus (NOLOCK)
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_refno = '".trim($sRefno)."'
			");
			
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function update_status($sRangka,$sMesin,$sRefno)
	{
		$xSQL = ("
			UPDATE	tm_icregister SET
					fn_status = b.fn_status, fd_tgl_pelihara = b.fd_tgl_pelihara,
					fs_ket = b.fs_ket
			FROM	tm_icregister a (NOLOCK)
			INNER JOIN tx_unitstatus_detil b (NOLOCK) ON a.fs_rangka = b.fs_rangka
				AND	a.fs_machine = b.fs_mesin
			WHERE	b.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	b.fs_refno = '".trim($sRefno)."'
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid($sRefno)
	{
		$xSQL = ("
			SELECT 	b.fs_kd_product, ISNULL(c.fs_nm_product, '') fs_nm_product,
					a.fs_rangka, a.fs_mesin, fn_status = CONVERT(NUMERIC(35,0),ISNULL(a.fn_status, 0)),
					fd_tgl_pelihara = CASE a.fd_tgl_pelihara WHEN '' THEN '' ELSE CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_tgl_pelihara, 105), 105) END,
					a.fs_ket
			FROM 	tx_unitstatus_detil a (NOLOCK)
			INNER JOIN tm_icregister b (NOLOCK) ON a.fs_rangka = b.fs_rangka 
					AND a.fs_mesin = b.fs_machine 
			LEFT JOIN tm_product c (NOLOCK) ON b.fs_kd_product = c.fs_kd_product 
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	a.fs_refno = '".trim($sRefno)."'
			ORDER BY b.fs_kd_product, c.fs_nm_product, a.fs_rangka, a.fs_mesin
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid2_all($sCari)
	{
		$xSQL = ("
			SELECT	CONVERT(BIT, 0) fb_cek, a.fs_kd_product,
					ISNULL(b.fs_nm_product, '') fs_nm_product,
					a.fs_rangka, a.fs_machine fs_mesin,
					a.fs_kd_wh, a.fs_nm_wh,
					'0' fn_status, ' ' fd_tgl_pelihara
			FROM	tm_icregister a (NOLOCK)
			LEFT JOIN tm_product b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_product = b.fs_kd_product
			WHERE a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND LTRIM(RTRIM(a.fs_refnoinv)) = ''
				AND a.fs_kd_trxl <> '3400'
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_product LIKE '%".trim($sCari)."%'
					OR b.fs_nm_product LIKE '%".trim($sCari)."%'
					OR a.fs_rangka LIKE '%".trim($sCari)."%'
					OR a.fs_machine LIKE '%".trim($sCari)."%'
					OR a.fs_nm_wh LIKE '%".trim($sCari)."%')
			");
		}
		
		$xSQL =	$xSQL.("
			ORDER BY a.fs_nm_wh, a.fs_kd_product, b.fs_nm_product, a.fs_rangka, a.fs_machine
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
			
			SELECT	CONVERT(BIT, 0) fb_cek, a.fs_kd_product,
					ISNULL(b.fs_nm_product, '') fs_nm_product,
					a.fs_rangka, a.fs_machine fs_mesin,
					a.fs_kd_wh, a.fs_nm_wh,
					'0' fn_status, ' ' fd_tgl_pelihara
			INTO	#tempdet".$xUser.$xIP."
			FROM	tm_icregister a (NOLOCK)
			LEFT JOIN tm_product b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_product = b.fs_kd_product
			WHERE a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND LTRIM(RTRIM(a.fs_refnoinv)) = ''
				AND a.fs_kd_trxl <> '3400'
			");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_product LIKE '%".trim($sCari)."%'
					OR b.fs_nm_product LIKE '%".trim($sCari)."%'
					OR a.fs_rangka LIKE '%".trim($sCari)."%'
					OR a.fs_machine LIKE '%".trim($sCari)."%'
					OR a.fs_nm_wh LIKE '%".trim($sCari)."%')
			");
		}
		
		$xSQL =	$xSQL.("
			ORDER BY a.fs_nm_wh, a.fs_kd_product, b.fs_nm_product, a.fs_rangka, a.fs_machine
			");
		
		$xSQL =	$xSQL.("
			SELECT 	n = IDENTITY(INT, 1, 1), *
			INTO	#tempdet2".$xUser.$xIP."
			FROM 	#tempdet".$xUser.$xIP."
			ORDER BY fs_nm_wh, fs_kd_product, fs_nm_product, fs_rangka, fs_mesin
			
			SELECT 	* FROM #tempdet2".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempdet".$xUser.$xIP."
			DROP TABLE #tempdet2".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>