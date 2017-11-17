<?php

class MMutasiIn extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function grid_prod($sDept,$sCount,$sRefno)
	{
		$xSQL = ("
			SELECT	a.fs_kd_product, [fs_nm_product] = ISNULL(b.fs_nm_product, ''), a.fn_qtytr fn_qty,
					[fs_kd_unit] = a.fs_unitbill, [fs_nm_unit] = ISNULL(c.fs_nm_unit, ''),
					a.fs_seqno
			FROM 	tx_actdetail a (NOLOCK)
			INNER JOIN tm_product b (NOLOCK) ON a.fs_kd_product = b.fs_kd_product
			INNER JOIN tm_unitconvertion c (NOLOCK) ON a.fs_unitbill = c.fs_kd_unit
			WHERE 	a.fs_kd_dept = '".trim($sDept)."'
				AND a.fs_count = '".trim($sCount)."'
				AND a.fs_refno = '".trim($sRefno)."'
				AND a.fs_refno <> ''
			ORDER BY a.fs_seqno
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid_reg($sDept,$sCount,$sTrx,$ssTrx,$sRefno)
	{
		$xSQL = ("
			SELECT	a.fs_rangka, a.fs_machine fs_mesin, a.fs_seqnoictin fs_seqno,
					a.fs_kd_product, a.fd_thnpembuatan fd_thn,
					ISNULL(b.fs_nm_vareable, '') fs_nm_warna
			FROM	tm_icregister a (NOLOCK)
			LEFT JOIN tm_vareable b (NOLOCK) ON a.fs_kd_warna = b.fs_kd_vareable
				AND	b.fs_key = '08'
			WHERE	a.fs_kd_deptictin = '".trim($sDept)."'
				AND a.fs_countdeptictin = '".trim($sCount)."'
				AND a.fs_kd_trxictin = '".trim($sTrx)."'
				AND a.fs_kd_strxictin = '".trim($ssTrx)."'
				AND a.fs_refnoictin = '".trim($sRefno)."'
			ORDER BY a.fs_seqnoictin, a.fs_rangka, a.fs_machine
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function mutasi_header($sRefnoOut,$sRefnoIn)
	{
		$xSQL = ("
			UPDATE tm_icregister
			SET fs_kd_deptictout = a.fs_kd_dept,
				fs_countdeptictout = a.fs_count,
				fs_kd_trxictout = a.fs_kd_trx,
				fs_kd_strxictout = a.fs_kd_strx,
				fs_refnoictout = a.fs_refno,
				fs_seqnoictout = a.fs_seqno,
				fs_kd_deptictin = '',
				fs_countdeptictin = '',
				fs_kd_trxictin = '',
				fs_kd_strxictin = '',
				fs_refnoictin = '',
				fs_seqnoictin = '',
				fs_kd_ldept = a.fs_kd_dept,
				fs_countdept = a.fs_count,
				fs_kd_trxl = a.fs_kd_trx,
				fs_kd_strxl = a.fs_kd_strx,
				fs_refnol = a.fs_refno,
				fs_seqnol = a.fs_seqno,
				fb_delete = 0
			FROM	tx_icmoveregsr a (NOLOCK)
			INNER JOIN tm_icregister b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_register = b.fs_register
				AND	a.fs_rangka = b.fs_rangka
				AND	a.fs_mesin = b.fs_machine
			WHERE	a.fs_refno = '".trim($sRefnoOut)."'
			
			DELETE FROM tx_icmoveregsr WHERE fs_refno = '".trim($sRefnoIn)."'
			
			DELETE FROM tx_icmove WHERE fs_kd_refno = '".trim($sRefnoIn)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function mutasi_reg($sDeptCd,$sCount,$sTrx,$ssTrx,$sRefno,$sProd,$sSeqno,$sTipe,$sRangka,$sMesin,$sWHCd,$sWHNm)
	{
		$xSQL = ("
			UPDATE	tm_icregister
				SET fs_kd_deptictout = '',
					fs_countdeptictout = '',
					fs_kd_trxictout = '',
					fs_kd_strxictout = '',
					fs_refnoictout = '',
					fs_seqnoictout = '',
					fs_kd_deptictin = '".trim($sDeptCd)."',
                    fs_countdeptictin = '".trim($sCount)."',
					fs_kd_trxictin = '".trim($sTrx)."',
					fs_kd_strxictin = '".trim($ssTrx)."',
					fs_refnoictin = '".trim($sRefno)."',
					fs_seqnoictin = '".trim($sSeqno)."',
					fs_kd_ldept = '".trim($sDeptCd)."',
					fs_countdept = '".trim($sCount)."',
					fs_kd_trxl = '".trim($sTrx)."',
					fs_kd_strxl = '".trim($ssTrx)."',
					fs_refnol = '".trim($sRefno)."',
					fs_seqnol = '".trim($sSeqno)."',
					fs_kd_wh = '".trim($sWHCd)."',
					fs_nm_wh = '".trim($sWHNm)."'
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_product = '".trim($sProd)."'
				AND	fs_kd_type = '".trim($sTipe)."'
				AND	fs_rangka = '".trim($sRangka)."'
				AND	fs_machine = '".trim($sMesin)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function mutasiin_cetak($sRefno)
	{
		$xSQL = ("
			SELECT	aa.fs_refnoictin fs_refno, ISNULL(ff.fs_nm_product, '') fs_nm_product,
					aa.fd_thnpembuatan as fs_year,
					aa.fs_rangka, aa.fs_machine, ee.fs_nm_vareable as fs_nm_warna,
					fs_register, bb.fs_kd_dept, bb.fs_count,
					cc.fs_nm_code as fs_nm_deptf, cc.fs_addr as fs_addrf, cc.fs_phone1 as telp_depf,
					cc.fs_kd_city as fs_kd_kotaf, ISNULL(gg.fs_nm_vareable, '') fs_nm_kotaf,
					bb.fs_kd_Tdept, bb.fs_TCount, dd.fs_nm_code as fs_nm_deptt, dd.fs_addr as fs_addrt,
					dd.fs_kd_city as fs_kd_kotat, ISNULL(hh.fs_nm_vareable, '') fs_nm_kotat
			FROM 	tm_icregister aa
			INNER JOIN tx_actheader bb ON bb.fs_refno = aa.fs_refnoictin
			INNER JOIN tm_addr cc ON cc.fs_code = bb.fs_kd_dept and cc.fs_count = bb.fs_count
			INNER JOIN tm_addr dd ON dd.fs_code = bb.fs_kd_Tdept and dd.fs_count = bb.fs_TCount
			LEFT JOIN tm_vareable ee ON ee.fs_kd_vareable = aa.fs_kd_warna
				AND	ee.fs_key = '08'
			LEFT JOIN tm_product ff ON ff.fs_kd_product = aa.fs_kd_product
			LEFT JOIN tm_vareable gg ON cc.fs_kd_city = gg.fs_kd_vareable
				AND	gg.fs_key = '14'
			LEFT JOIN tm_vareable hh ON dd.fs_kd_city = hh.fs_kd_vareable
				AND	hh.fs_key = '14'
			WHERE bb.fs_refno = '".trim($sRefno)."'
			ORDER BY fs_nm_product
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>