<?php

class MUnitStatus extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function unitstatus($sWH,$sProd,$sColor)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
				SELECT 	fs_nm_wh = CASE aa.fs_nm_wh WHEN '' THEN '-' ELSE aa.fs_nm_wh END,
						ISNULL(aa.fs_kd_product,'') fs_kd_product, ISNULL(fs_nm_vareable, '') fs_nm_warna,
						ISNULL(aa.fs_rangka,'') fs_rangka, ISNULL(aa.fs_machine,'') fs_mesin,
						CONVERT(NUMERIC(35,0),ISNULL(aa.fn_silinder, 0)) fn_silinder,
						fs_status = CASE CONVERT(NUMERIC(35,0),ISNULL(aa.fn_status, 0))
							WHEN 0 THEN 'READY'
							WHEN 1 THEN 'MAINTENANCE'
							WHEN 2 THEN 'REPAIR'
						END,
				ISNULL(fd_thnperakitan, '') fd_thn,aa.fd_tgl_pelihara,aa.fs_ket
				FROM tm_icregister aa (NOLOCK)
				INNER JOIN tm_product bb ON bb.fs_kd_product = aa.fs_kd_product
				INNER JOIN tm_vareable cc ON cc.fs_kd_vareable = aa.fs_kd_warna
				WHERE	cc.fs_key = '08'
					AND RTRIM(LTRIM(aa.fs_refnoinv)) = '' and RTRIM(LTRIM(fs_kd_trxl)) <> '3400'
				");
			
			if (trim($sWH) <> '')
			{
				$xSQL = $xSQL.("
					AND aa.fs_kd_wh = '".trim($sWH)."'
				");
			}
			
			if (trim($sProd) <> '')
			{
				$xSQL = $xSQL.("
					AND aa.fs_kd_product = '".trim($sProd)."'
				");
			}
			
			if (trim($sColor) <> '')
			{
				$xSQL = $xSQL.("
					AND aa.fs_kd_warna = '".trim($sColor)."'
				");
			}
		
			$xSQL = $xSQL.("
					ORDER BY aa.fs_nm_wh, aa.fs_kd_product
				");
				
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function unithistory($sWH,$sProd)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
				SELECT 	aa.fs_refno, CONVERT(VARCHAR(10), CONVERT(DATETIME, aa.fd_refno, 105), 105) fd_refno,
						ISNULL(dd.fs_nm_product, '') fs_nm_product,
						bb.fs_rangka, bb.fs_mesin,
						fs_status = CASE CONVERT(NUMERIC(35,0),ISNULL(bb.fn_status, 0))
						WHEN 0 THEN 'READY'
						WHEN 1 THEN 'MAINTENANCE'
						WHEN 2 THEN 'REPAIR'
						END,
						fd_tgl_pelihara = CASE cc.fd_tgl_pelihara WHEN '' THEN '' ELSE CONVERT(VARCHAR(10), CONVERT(DATETIME, cc.fd_tgl_pelihara, 105), 105) END,
						bb.fs_ket
				FROM 	tx_unitstatus aa
				INNER JOIN tx_unitstatus_detil bb ON bb.fs_refno = aa.fs_refno
				INNER JOIN tm_icregister cc ON RTRIM(LTRIM(cc.fs_rangka)) = RTRIM(LTRIM(bb.fs_rangka))
				INNER JOIN tm_product dd ON dd.fs_kd_product = cc.fs_kd_product
				WHERE 	RTRIM(LTRIM(cc.fs_refnoinv)) = '' 
					AND RTRIM(LTRIM(cc.fs_kd_trxl)) <> '3400'
				");
			
			if (trim($sWH) <> '')
			{
				$xSQL = $xSQL.("
					AND cc.fs_kd_wh = '".trim($sWH)."'
				");
			}
			
			if (trim($sProd) <> '')
			{
				$xSQL = $xSQL.("
					AND cc.fs_kd_product = '".trim($sProd)."'
				");
			}
			
			$xSQL = $xSQL.("
				ORDER BY aa.fs_refno, dd.fs_nm_product, bb.fs_rangka, bb.fs_mesin
				");
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}

?>