<?php

class MMutasiOutDB extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function cek_refno($sRefno)
	{
		$xSQL = ("
			SELECT	TOP 1 fs_refno, fs_nm_tdb
			FROM   	tx_mutasidb (NOLOCK)
			WHERE  	fs_refno = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid_prod($sRefno,$sCust)
	{
		if (trim($sRefno) <> '')
		{
			$xSQL = ("
				SELECT	a.fs_refno, a.fs_kd_product, ISNULL(c.fs_nm_product, '') fs_nm_product,
						a.fs_rangka, a.fs_mesin, CONVERT(NUMERIC(35,0), a.fn_cc) fs_cc,
						CONVERT(NUMERIC(35,0), a.fn_thn) fs_thn, a.fs_kd_warna, a.fs_nm_warna
				FROM	tx_mutasidbd a (NOLOCK)
				INNER JOIN tx_mutasidb b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
					AND	a.fs_refno = b.fs_refno
				LEFT JOIN tm_product c (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
					AND	a.fs_kd_product = c.fs_kd_product
				WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	a.fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	a.fs_refno = '".trim($sRefno)."'
			");
		}
		else
		{
			$xSQL = ("
				SELECT	a.fs_refno, b.fs_kd_product, ISNULL(d.fs_nm_product, '') fs_nm_product,
						c.fs_rangka, c.fs_machine fs_mesin, CONVERT(NUMERIC(4,0), c.fn_silinder) fs_cc,
						c.fd_thnpembuatan fs_thn, c.fs_kd_warna, ISNULL(e.fs_nm_vareable, '') fs_nm_warna
				FROM 	tx_posheader a (NOLOCK)
				INNER JOIN tx_posdetail b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
					AND	a.fs_refno = b.fs_refno
				INNER JOIN tm_icregister c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
					AND	a.fs_refno = c.fs_refnoinv
					AND	b.fs_kd_product = c.fs_kd_product
				LEFT JOIN tm_product d (NOLOCK) ON a.fs_kd_comp = d.fs_kd_comp
					AND	c.fs_kd_product = d.fs_kd_product
				LEFT JOIN tm_vareable e (NOLOCK) ON c.fs_kd_warna = e.fs_kd_vareable
					AND	e.fs_key = '08'
				WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	a.fs_kd_trx = 'JL'
					AND	LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(fs_countcussup)) = '".trim($sCust)."'
					AND c.fs_rangka NOT IN (
						SELECT 	x.fs_rangka
						FROM 	tx_mutasidbd x (NOLOCK)
					)
					AND c.fs_machine NOT IN (
						SELECT 	x.fs_mesin
						FROM 	tx_mutasidbd x (NOLOCK)
					)
				ORDER BY a.fs_refno, b.fs_kd_product, d.fs_nm_product, c.fs_rangka, c.fs_machine
			");
		}
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_rangka($sRangka,$sMesin)
	{
		$xSQL = ("
			SELECT	fs_kd_product, fs_rangka, fs_mesin
			FROM   	tx_mutasidbd (NOLOCK)
			WHERE  	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_rangka IN (".trim($sRangka).")
				AND	fs_mesin IN (".trim($sMesin).")
				AND fs_rangka IN (
					SELECT 	fs_rangka
					FROM 	tx_mutasidbd (NOLOCK)
					WHERE	LTRIM(RTRIM(fs_refnoin)) <> ''
				)
				AND fs_mesin IN (
					SELECT 	fs_mesin
					FROM 	tx_mutasidbd (NOLOCK)
					WHERE	LTRIM(RTRIM(fs_refnoin)) <> ''
				)
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function hapustodb($sNmDB,$sRefno)
	{
		$xSQL = ("
			DELETE	FROM [".trim($sNmDB)."]..tx_mutasidb
			WHERE	[".trim($sNmDB)."]..tx_mutasidb.fs_refno = '".trim($sRefno)."'
			
			DELETE	FROM [".trim($sNmDB)."]..tx_mutasidbd
			WHERE	[".trim($sNmDB)."]..tx_mutasidbd.fs_refno = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function simpantodb($sNmDB,$sRefno)
	{
		$xSQL = ("
			INSERT INTO [".trim($sNmDB)."]..tx_mutasidb(
					fs_kd_comp, fs_nm_db, fs_nm_comp,
					fs_kd_dept, fs_count, fs_refno,
					fd_refno, fs_ket, fs_kd_cust,
					fs_count_cust, fs_kd_tcomp, fs_nm_tdb,
					fs_nm_tcomp, fs_kd_tdept, fs_tcount,
					fs_nm_tdept, fs_kd_twh, fs_nm_twh,
					fs_usrcrt, fd_usrcrt, fs_upddt, fd_upddt
			)
			SELECT	fs_kd_comp, fs_nm_db, fs_nm_comp,
					fs_kd_dept, fs_count, fs_refno,
					fd_refno, fs_ket, fs_kd_cust,
					fs_count_cust, fs_kd_tcomp, fs_nm_tdb,
					fs_nm_tcomp, fs_kd_tdept, fs_tcount,
					fs_nm_tdept, fs_kd_twh, fs_nm_twh,
					fs_usrcrt, fd_usrcrt, fs_upddt, fd_upddt
			FROM	tx_mutasidb
			WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND	fs_count = '".trim($this->session->userdata('gCount'))."'
				AND	fs_refno = '".trim($sRefno)."'
			
			INSERT INTO [".trim($sNmDB)."]..tx_mutasidbd(
					fs_kd_comp, fs_kd_dept, fs_count,
					fs_refno, fs_kd_product, fs_rangka,
					fs_mesin, fn_cc, fn_thn, fs_kd_warna,
					fs_nm_warna, fs_seqno, fs_refnoin
			)
			SELECT	fs_kd_comp, fs_kd_dept, fs_count,
					fs_refno, fs_kd_product, fs_rangka,
					fs_mesin, fn_cc, fn_thn, fs_kd_warna,
					fs_nm_warna, fs_seqno, fs_refnoin
			FROM 	tx_mutasidbd
			WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND	fs_count = '".trim($this->session->userdata('gCount'))."'
				AND	fs_refno = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function mutasioutdb_cetak($sNmDB,$sRefno)
	{
		$xSQL = ("
			SELECT	a.fs_kd_comp, a.fs_refno, b.fs_kd_product,
					ISNULL(c.fs_nm_product, '') fs_nm_product,
					b.fn_thn fs_year, b.fs_rangka, b.fs_mesin fs_machine,
					b.fs_kd_warna, b.fs_nm_warna, a.fs_nm_comp fs_nm_compf, a.fs_kd_dept fs_kd_deptf,
					a.fs_count fs_countf, ISNULL(d.fs_nm_code, '') fs_nm_deptf,
					ISNULL(d.fs_addr, '') fs_addrf, ISNULL(d.fs_phone1, '') telp_depf,
					ISNULL(d.fs_kd_city, '') fs_kd_kotaf, ISNULL(e.fs_nm_vareable, '') fs_nm_kotaf,
					a.fs_nm_tcomp fs_nm_compt, a.fs_kd_tdept fs_kd_deptt, a.fs_tcount fs_countt, a.fs_nm_tdept fs_nm_deptt,
					ISNULL(f.fs_addr, '') fs_addrt, ISNULL(f.fs_kd_city, '') fs_kd_kotat,
					ISNULL(g.fs_nm_vareable, '') fs_nm_kotat
			FROM	tx_mutasidb a (NOLOCK)
			INNER JOIN tx_mutasidbd b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_refno = b.fs_refno
			LEFT JOIN tm_product c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND	b.fs_kd_product = c.fs_kd_product
			LEFT JOIN tm_addr d (NOLOCK) ON a.fs_kd_comp = d.fs_kd_comp
				AND	a.fs_kd_dept = d.fs_code
				AND	a.fs_count = d.fs_count
				AND	d.fs_cdtyp = '03'
			LEFT JOIN tm_vareable e ON d.fs_kd_city = e.fs_kd_vareable
				AND	e.fs_key = '14'
			LEFT JOIN [".trim($sNmDB)."]..tm_addr f (NOLOCK) ON a.fs_kd_comp = f.fs_kd_comp
				AND	a.fs_kd_dept = f.fs_code
				AND	a.fs_count = f.fs_count
				AND	f.fs_cdtyp = '03'
			LEFT JOIN [".trim($sNmDB)."]..tm_vareable g (NOLOCK) ON f.fs_kd_city = g.fs_kd_vareable
				AND	g.fs_key = '14'
			WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	a.fs_refno = '".trim($sRefno)."'
			ORDER BY c.fs_nm_product, b.fs_rangka, b.fs_mesin
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>