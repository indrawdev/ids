<?php

class MBarcode extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function gridrangka($sWH,$sProduct,$sRangka)
	{
		$xSQL = ("
			SELECT	a.fs_kd_product, ISNULL(b.fs_nm_product, '') fs_nm_product,
					a.fs_rangka, a.fs_machine fs_mesin, CONVERT(NUMERIC(35,0), a.fn_silinder) fn_silinder,
					a.fs_kd_warna, ISNULL(c.fs_nm_vareable, '') fs_nm_warna,
					'0' fb_tambah
			FROM	tm_icregister a (NOLOCK)
			LEFT JOIN tm_product b (NOLOCK) ON a.fs_kd_product = b.fs_kd_product
			LEFT JOIN tm_vareable c (NOLOCK) ON a.fs_kd_warna = c.fs_kd_vareable
				AND	c.fs_key = '08'
			WHERE	LTRIM(RTRIM(a.fs_refnoinv)) = ''
				AND	LTRIM(RTRIM(a.fs_kd_trxl)) <> '3400'
				AND	a.fs_kd_product = '".trim($sProduct)."'
		");
		
		if (trim($sWH) <> '')
		{
			$xSQL = $xSQL.("
				AND	a.fs_kd_wh = '".trim($sWH)."'
			");
		}
		
		if (trim($sRangka) <> '')
		{
			$xSQL = $xSQL.("
				AND	a.fs_rangka IN (".trim($sRangka).")
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_rangka, a.fs_machine
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>