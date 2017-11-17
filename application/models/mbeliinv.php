<?php

class MBeliInv extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function reset_hpp($sRefno)
	{
		$xSQL = ("
			UPDATE	tm_icregister SET fn_hpp = '0'
			FROM	tx_posdetail a (NOLOCK)
			INNER JOIN tx_posheader b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_refno = b.fs_refno
			INNER JOIN tm_icregister c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND	a.fs_kd_order = c.fs_refno
				AND	a.fs_kd_warna = c.fs_kd_warna
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	a.fs_refno = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid_do($sRefno)
	{
		$xSQL = ("
			SELECT	a.fs_kd_order fs_kd_do, ISNULL(c.fs_docno, '') fs_docno
			FROM	tx_posdetail a (NOLOCK)
			INNER JOIN tx_posheader b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_refno = b.fs_refno
			INNER JOIN tx_posheader c (NOLOCK) ON b.fs_kd_comp = c.fs_kd_comp
				AND a.fs_kd_order = c.fs_refno
			WHERE a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_refno = '".trim($sRefno)."'
			GROUP BY a.fs_kd_order, c.fs_docno
			ORDER BY a.fs_kd_order, c.fs_docno
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid_prod($sRefno)
	{
		$xSQL = ("
			SELECT	a.fs_kd_order fs_kd_do, b.fs_docno, a.fs_kd_product,
					ISNULL(c.fs_nm_product, '') fs_nm_product, a.fs_unitbill fs_kd_unit,
					a.fn_qtytr fn_qty, a.fn_unitprc fn_harga, a.fn_dscprc fn_diskon,
					a.fs_kd_warna, ISNULL(d.fs_nm_vareable, '') fs_nm_warna
			FROM 	tx_posdetail a (NOLOCK)
			INNER JOIN tx_posheader b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_refno = b.fs_refno
			LEFT JOIN tm_product c (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp	
				AND	a.fs_kd_product = c.fs_kd_product
			LEFT JOIN tm_vareable d (NOLOCK) ON a.fs_kd_comp = d.fs_kd_comp
				AND	a.fs_kd_warna = d.fs_kd_vareable
				AND	d.fs_key = '08'
			WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_refno = '".trim($sRefno)."'
			ORDER BY a.fs_seqno
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid_prod2($sRefno)
	{
		$xSQL = ("
			SELECT	b.fs_refno fs_kd_do, b.fs_docno, c.fs_kd_product, ISNULL(d.fs_nm_product, '') fs_nm_product,
					c.fs_kd_warna, ISNULL(e.fs_nm_vareable, '') fs_nm_warna, COUNT(c.fs_kd_warna) fn_qty,
					a.fs_unitbill fs_kd_unit
			FROM	tx_posdetail a (NOLOCK)
			INNER JOIN tx_posheader b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_refno = b.fs_refno
			INNER JOIN tm_icregister c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND a.fs_refno = c.fs_refno
				AND a.fs_kd_product = c.fs_kd_product
			LEFT JOIN tm_product d (NOLOCK) ON a.fs_kd_comp = d.fs_kd_comp
				AND a.fs_kd_product = d.fs_kd_product
			LEFT JOIN  tm_vareable e (NOLOCK) ON c.fs_kd_comp = e.fs_kd_comp
				AND e.fs_key = '08'
				AND c.fs_kd_warna = e.fs_kd_vareable
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_refno = '".trim($sRefno)."'
			GROUP BY b.fs_refno, b.fs_docno, c.fs_kd_product,
					d.fs_nm_product, c.fs_kd_warna, e.fs_nm_vareable,
					a.fs_unitbill
			ORDER BY b.fs_refno, c.fs_kd_product, d.fs_nm_product, e.fs_nm_vareable
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid_reg($sRefno)
	{
		$xSQL = ("
			SELECT	a.fs_kd_order fs_kd_do, a.fs_kd_product,
					ISNULL(d.fs_nm_product, '') fs_nm_product, a.fs_kd_warna,
					ISNULL(e.fs_nm_vareable, '') fs_nm_warna, c.fs_rangka, c.fs_machine fs_mesin,
					c.fn_silinder fs_cc, c.fd_thnpembuatan fs_thn,
					c.fs_kd_wh, ISNULL(f.fs_nm_code, '') fs_nm_wh
			FROM	tx_posdetail a (NOLOCK)
			INNER JOIN tx_posheader b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_refno = b.fs_refno
			INNER JOIN tm_icregister c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND	a.fs_kd_order = c.fs_refno
				AND	a.fs_kd_warna = c.fs_kd_warna
			LEFT JOIN tm_product d (NOLOCK) ON a.fs_kd_comp = d.fs_kd_comp
				AND	a.fs_kd_product = d.fs_kd_product
			LEFT JOIN tm_vareable e (NOLOCK) ON a.fs_kd_comp = e.fs_kd_comp
				AND	a.fs_kd_warna = e.fs_kd_vareable
				AND	e.fs_key = '08'
			LEFT JOIN tm_addr f (NOLOCK) ON a.fs_kd_comp = f.fs_kd_comp
				AND c.fs_kd_wh = LTRIM(RTRIM(f.fs_code)) + LTRIM(RTRIM(f.fs_count))
				AND	f.fs_cdtyp = '11'
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	a.fs_refno = '".trim($sRefno)."'
			ORDER BY a.fs_refno, a.fs_kd_product, d.fs_nm_product, e.fs_nm_vareable
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid_reg2($sRefno)
	{
		$xSQL = ("
			SELECT	b.fs_refno fs_kd_do, b.fs_docno, c.fs_kd_product, ISNULL(d.fs_nm_product, '') fs_nm_product, 
					c.fs_kd_warna, ISNULL(e.fs_nm_vareable, '') fs_nm_warna, 
					a.fs_unitbill fs_kd_unit, a.fn_unitprc fn_harga,
					c.fs_rangka, c.fs_machine fs_mesin, c.fn_silinder fs_cc, c.fd_thnpembuatan fs_thn,
					c.fs_kd_wh, ISNULL(f.fs_nm_code, '') fs_nm_wh
			FROM	tx_posdetail a (NOLOCK)
			INNER JOIN tx_posheader b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_refno = b.fs_refno
			INNER JOIN tm_icregister c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND a.fs_refno = c.fs_refno
				AND a.fs_kd_product = c.fs_kd_product
			LEFT JOIN tm_product d (NOLOCK) ON a.fs_kd_comp = d.fs_kd_comp
				AND a.fs_kd_product = d.fs_kd_product
			LEFT JOIN tm_vareable e (NOLOCK) ON c.fs_kd_comp = e.fs_kd_comp
				AND e.fs_key = '08'
				AND c.fs_kd_warna = e.fs_kd_vareable
			LEFT JOIN tm_addr f (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND c.fs_kd_wh = LTRIM(RTRIM(f.fs_code)) + LTRIM(RTRIM(f.fs_count))
				AND	f.fs_cdtyp = '11'
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_refno = '".trim($sRefno)."'
			ORDER BY b.fs_refno, c.fs_kd_product, c.fs_kd_warna, e.fs_nm_vareable
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>