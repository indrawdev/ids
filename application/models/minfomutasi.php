<?php

class MInfoMutasi extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function mutasi_rangka($sRangka)
	{
		$xSQL = ("
			SELECT	b.fs_kd_product, ISNULL(c.fs_nm_product, '') fs_nm_product,
					b.fs_rangka, b.fs_machine fs_mesin,
					CASE UPPER(LTRIM(RTRIM(a.fs_kd_trx)))
					WHEN '3400' THEN ISNULL(d.fs_nm_code, '')
					WHEN '3300' THEN ISNULL(e.fs_nm_code, '')
					WHEN 'JL' THEN ISNULL(d.fs_nm_code, '')
					ELSE ''
					END fs_nm_asal,
					CASE UPPER(LTRIM(RTRIM(a.fs_kd_trx)))
					WHEN '3400' THEN ISNULL(e.fs_nm_code, '')
					WHEN '3300' THEN ISNULL(d.fs_nm_code, '')
					WHEN 'JL' THEN ''
					ELSE ''
					END fs_nm_tujuan, a.fs_refno,
					CASE UPPER(LTRIM(RTRIM(a.fs_kd_trx)))
					WHEN '3400' THEN 'MOVE OUT'
					WHEN '3300' THEN 'MOVE IN'
					WHEN 'JL' THEN 'SOLD'
					ELSE ''
					END fs_status
			FROM	tx_icmoveregsr a
			INNER JOIN tm_icregister b ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_rangka = b.fs_rangka
				AND	a.fs_mesin = b.fs_machine
			LEFT JOIN tm_product c ON a.fs_kd_comp = c.fs_kd_comp
				AND	b.fs_kd_product = c.fs_kd_product
			LEFT JOIN tm_addr d ON a.fs_kd_comp = d.fs_kd_comp
				AND	d.fs_cdtyp = '03'
				AND	a.fs_kd_dept = d.fs_code
				AND	a.fs_count = d.fs_count
			LEFT JOIN tm_addr e ON a.fs_kd_comp = e.fs_kd_comp
				AND	e.fs_cdtyp = '03'
				AND	a.fs_kd_deptt = e.fs_code
				AND	a.fs_countt = e.fs_count
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
		");
		
		if (trim($sRangka) <> '')
		{
			$xSQL = $xSQL.("
				AND	a.fs_rangka = '".trim($sRangka)."'
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY c.fs_nm_product, b.fs_rangka, b.fs_machine, a.fd_usrcrt
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>