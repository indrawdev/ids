<?php

class MBeliDO extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function cek_inv($sRangka,$sMesin)
	{
		$xSQL = ("
			SELECT	fs_kd_product, fs_rangka, fs_machine
			FROM   	tm_icregister (NOLOCK)
			WHERE  	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_rangka IN (".trim($sRangka).")
				AND	fs_machine IN (".trim($sMesin).")
				AND fs_rangka IN (
						SELECT 	fs_rangka
						FROM 	tm_icregister
						WHERE	fn_hpp > 0
					)
				AND fs_machine IN (
						SELECT 	fs_machine
						FROM 	tm_icregister
						WHERE	fn_hpp > 0
					)
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid_prod2($sComp,$sNmDB)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temp".$xUser.$xIP."%' )
					DROP TABLE #temp".$xUser.$xIP."
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temp2".$xUser.$xIP."%' )
					DROP TABLE #temp2".$xUser.$xIP."
			
			SELECT	a.fs_kd_product, c.fs_nm_product, COUNT(a.fs_rangka) fn_qty,
					IDENTITY(INT, 1, 1) fs_seqno
			INTO	#temp".$xUser.$xIP."
			FROM	tx_mutasidbd a (NOLOCK)
			INNER JOIN tx_mutasidb b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_dept = b.fs_kd_dept
				AND	a.fs_count = b.fs_count
				AND	a.fs_refno = b.fs_refno
			INNER JOIN tm_product c (NOLOCK) ON a.fs_kd_product = c.fs_kd_product
			WHERE	b.fs_kd_tcomp = '".trim($this->session->userdata('gComp'))."'
				AND	b.fs_nm_tdb = '".trim($this->session->userdata('gDatabase'))."'
				AND	b.fs_kd_tdept = '".trim($this->session->userdata('gDept'))."'
				AND	b.fs_tcount = '".trim($this->session->userdata('gCount'))."'
				AND	b.fs_kd_comp = '".trim($sComp)."'
				AND	b.fs_nm_db = '".trim($sNmDB)."'
				AND	LTRIM(RTRIM(a.fs_refnoin)) = ''
			GROUP BY a.fs_kd_product, c.fs_nm_product
			ORDER BY a.fs_kd_product, c.fs_nm_product
			
			SELECT 	fs_kd_product, fs_nm_product, fn_qty,
					fs_seqno = LEFT('00000', 6 - LEN(fs_seqno)) + CONVERT(VARCHAR(5), fs_seqno)
			INTO	#temp2".$xUser.$xIP."
			FROM 	#temp".$xUser.$xIP."
			
			SELECT	fs_kd_product, fs_nm_product, fn_qty,
					fs_seqno
			FROM	#temp2".$xUser.$xIP."
			
			DROP TABLE #temp".$xUser.$xIP."
			DROP TABLE #temp2".$xUser.$xIP."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid_reg2($sComp,$sNmDB)
	{
		$xSQL = ("
			SELECT	a.fs_rangka, a.fs_mesin, CONVERT(NUMERIC(35,0), a.fn_cc) fs_cc,
					CONVERT(NUMERIC(35,0), a.fn_thn) fs_thn, a.fs_kd_warna fs_kd_color, a.fs_nm_warna fs_nm_color,
					b.fs_kd_twh fs_kd_wh, b.fs_nm_twh fs_nm_wh, a.fs_seqno,
					a.fs_kd_product
			FROM	tx_mutasidbd a (NOLOCK)
			INNER JOIN tx_mutasidb b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_dept = b.fs_kd_dept
				AND	a.fs_count = b.fs_count
				AND	a.fs_refno = b.fs_refno
			INNER JOIN tm_product c (NOLOCK) ON a.fs_kd_product = c.fs_kd_product
			WHERE	b.fs_kd_tcomp = '".trim($this->session->userdata('gComp'))."'
				AND	b.fs_nm_tdb = '".trim($this->session->userdata('gDatabase'))."'
				AND	b.fs_kd_tdept = '".trim($this->session->userdata('gDept'))."'
				AND	b.fs_tcount = '".trim($this->session->userdata('gCount'))."'
				AND	b.fs_kd_comp = '".trim($sComp)."'
				AND	b.fs_nm_db = '".trim($sNmDB)."'
				AND	LTRIM(RTRIM(a.fs_refnoin)) = ''
			ORDER BY a.fs_seqno
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_rangkadb($sProd,$sRangka,$sMesin,$sWarna)
	{
		$xSQL = ("
			SELECT	b.fs_nm_db
			FROM	tx_mutasidbd a (NOLOCK)
			INNER JOIN tx_mutasidb b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_refno = b.fs_refno
			WHERE	a.fs_kd_product = '".trim($sProd)."'
				AND	a.fs_rangka = '".trim($sRangka)."'
				AND	a.fs_mesin = '".trim($sMesin)."'
				AND	a.fs_kd_warna = '".trim($sWarna)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_rangkadb2($sRefno)
	{
		$xSQL = ("
			SELECT	b.fs_nm_db, a.fs_refnoin
			FROM	tx_mutasidbd a (NOLOCK)
			INNER JOIN tx_mutasidb b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_refno = b.fs_refno
			WHERE	a.fs_refnoin = '".trim($sRefno)."'
			GROUP BY b.fs_nm_db, a.fs_refnoin
			ORDER BY b.fs_nm_db
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function kosongtodb($sNmDB,$sRefno)
	{
		$xSQL = ("
			UPDATE	[".trim($sNmDB)."]..tx_mutasidbd
				SET [".trim($sNmDB)."]..tx_mutasidbd.fs_refnoin = ''
			WHERE	[".trim($sNmDB)."]..tx_mutasidbd.fs_refnoin = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function simpantodb2($sNmDB,$sRefno,$sProd,$sRangka,$sMesin,$sWarna)
	{
		$xSQL = ("
			UPDATE	[".trim($sNmDB)."]..tx_mutasidbd
				SET [".trim($sNmDB)."]..tx_mutasidbd.fs_refnoin = '".trim($sRefno)."'
			WHERE	[".trim($sNmDB)."]..tx_mutasidbd.fs_kd_product = '".trim($sProd)."'
				AND	[".trim($sNmDB)."]..tx_mutasidbd.fs_rangka = '".trim($sRangka)."'
				AND	[".trim($sNmDB)."]..tx_mutasidbd.fs_mesin = '".trim($sMesin)."'
				AND	[".trim($sNmDB)."]..tx_mutasidbd.fs_kd_warna = '".trim($sWarna)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}

	// FUNCTION IMPORT EXCEL
	function listImport()
	{
		$xSQL = ("
			SELECT a.fs_kd_wh, a.fs_kd_product, b.fs_nm_product, 
				CONVERT(NUMERIC(35,0), a.fs_thn) fs_thn,
				COUNT(a.fs_kd_product) as fn_qty, 'UNIT' as fs_unit,
				(select count(distinct x.fs_kd_product) from tx_tempexcel x where x.fs_kd_product < a.fs_kd_product ) + 1 as fs_seqno
			FROM tx_tempexcel a (NOLOCK)
			LEFT JOIN tm_product b (NOLOCK) ON b.fs_kd_product = a.fs_kd_product
			WHERE a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
		");

		$xSQL = $xSQL.("
			GROUP BY a.fs_kd_wh, a.fs_kd_product, b.fs_nm_product, a.fs_thn
			ORDER BY a.fs_kd_product ASC
		");

		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}

	function listImportDetail()
	{
		$xSQL = ("
			SELECT DISTINCT
				a.fs_rangka, a.fs_machine as fs_mesin, 
				CONVERT(NUMERIC(35,0), d.fn_silinder) fs_cc,
				CONVERT(NUMERIC(35,0), a.fs_thn) fs_thn, 
				a.fs_kd_warna as fs_kd_color, 
				c.fs_nm_vareable as fs_nm_color,
				a.fs_kd_wh, a.fs_nm_wh, a.fs_kd_product,
			(select count(distinct x.fs_kd_product) from tx_tempexcel x where x.fs_kd_product < a.fs_kd_product ) + 1 as fs_seqno
			FROM tx_tempexcel a (NOLOCK)
			LEFT JOIN tm_product b (NOLOCK) 
			ON b.fs_kd_product = a.fs_kd_product
			LEFT JOIN tm_vareable c (NOLOCK)
			ON c.fs_kd_vareable = a.fs_kd_warna AND c.fs_key = '08'
			LEFT JOIN tm_icregister d (NOLOCK)
			ON d.fs_rangka = a.fs_rangka AND d.fs_machine = a.fs_machine
			WHERE a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
		");

		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_product ASC
		");

		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}

}
?>