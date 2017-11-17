<?php

class MProdukEdit extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function cek_rangkamesin($sKdProduk,$sRangka,$sMesin)
	{
		$xSQL = ("
			SELECT 	fs_kd_product, fs_rangka, fs_machine
            FROM 	tm_icregister (NOLOCK)
            WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_product = '".trim($sKdProduk)."'
				AND	fs_rangka = '".trim($sRangka)."'
				AND	fs_machine = '".trim($sMesin)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_rangkamesin2($sKdProduk,$sRangka,$sMesin,$sRangka2,$sMesin2)
	{
		$xSQL = ("
			SELECT 	fs_kd_product, fs_rangka, fs_machine
            FROM 	tm_icregister (NOLOCK)
            WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_product = '".trim($sKdProduk)."'
				AND	fs_rangka = '".trim($sRangka2)."'
				AND	fs_machine = '".trim($sMesin2)."'
				AND	fs_rangka <> '".trim($sRangka)."'
				AND	fs_machine <> '".trim($sMesin)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function produk_edit_histori($sTgl,$sTgl2)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		
		$xSQL = ("
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempeditprod".$xUser.$xIP."%' )
					DROP TABLE #tempeditprod".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), a.fs_kd_dept, a.fs_count, ISNULL(b.fs_nm_code, '') fs_nm_dept,
					a.fs_kd_product, ISNULL(c.fs_nm_product, '') fs_nm_product,
					a.fs_rangka, a.fs_mesin, fs_silinder fs_cc,
					a.fs_tahun, a.fs_kd_warna, ISNULL(d.fs_nm_vareable, '') fs_nm_warna,
					a.fs_rangka2, a.fs_mesin2, fs_silinder2 fs_cc2,
					a.fs_tahun2, a.fs_kd_warna2, ISNULL(e.fs_nm_vareable, '') fs_nm_warna2,
					a.fs_usrcrt fs_kd_user, ISNULL(f.fs_nm_user, '') fs_nm_user,
					CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_usrcrt, 105), 105) fd_tgl
			INTO	#tempeditprod".$xUser.$xIP."
			FROM	tx_produk_ed a (NOLOCK)
			LEFT JOIN tm_addr b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_dept = b.fs_code
				AND	a.fs_count = b.fs_count
				AND	b.fs_cdtyp = '03'
			LEFT JOIN tm_product c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND	a.fs_kd_product = c.fs_kd_product
			LEFT JOIN tm_vareable d (NOLOCK) ON a.fs_kd_comp = d.fs_kd_comp
				AND	a.fs_kd_warna = d.fs_kd_vareable
				AND	d.fs_key = '08'
			LEFT JOIN tm_vareable e (NOLOCK) ON a.fs_kd_comp = e.fs_kd_comp
				AND	a.fs_kd_warna = e.fs_kd_vareable
				AND	e.fs_key = '08'
			LEFT JOIN tm_user f (NOLOCK) ON a.fs_kd_comp = f.fs_kd_comp
				AND	a.fs_usrcrt = f.fs_kd_user
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	REPLACE(CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_usrcrt, 105), 20), '-', '') BETWEEN '".trim($sTgl)."' AND '".trim($sTgl2)."'
			ORDER BY b.fs_nm_code
			
			SELECT 	* FROM #tempeditprod".$xUser.$xIP."
			
			DROP TABLE #tempeditprod".$xUser.$xIP
		);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>