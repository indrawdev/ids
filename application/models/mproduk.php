<?php

class MProduk extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function cek_kode($sKdProduk)
	{
		$xSQL = ("
			SELECT 	fs_kd_product
            FROM 	tm_product (NOLOCK)
            WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_product = '".trim($sKdProduk)."'
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function norangka($sKdProduk)
	{
		$xSQL = ("
			SELECT 	ISNULL(b.fs_chasis, '') fs_rangka,
					ISNULL(b.fs_engine, '') fs_mesin,
					ISNULL(b.fs_cilinder, 0) fn_cc
            FROM 	tm_product a (NOLOCK)
			LEFT JOIN tm_motortp b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_product = b.fs_kd_product
            WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	a.fs_kd_product = '".trim($sKdProduk)."'
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function simpanprodtodb($sNmDB,$sKdComp,$sKdProduk)
	{
		$xSQL = ("
			DELETE	FROM [".trim($sNmDB)."]..tm_product
			WHERE	[".trim($sNmDB)."]..tm_product.fs_kd_product = '".trim($sKdProduk)."'
			
			INSERT INTO [".trim($sNmDB)."]..tm_product(
					fs_kd_comp, fs_kd_product, fs_kd_type,
					fs_nm_product, fb_active, fs_kd_cogs,
					fb_purchase, fb_sale, fb_transfer,
					fb_warning, fn_limitQuota, fs_kd_unit1,
					fb_ProdDept, fs_kd_group, fb_attribute,
					fn_balance, fn_valueBal, fs_kd_acno,
					fs_kd_acnoX, fs_kd_AcnoR, fs_kd_AcnoRx,
					fs_kd_component, fn_component, fs_UnitPurchase,
					fs_UnitSale, fs_kd_moving, fs_kd_jnsProd,
					fs_usrcrt, fd_usrcrt, fs_upddt,
					fd_upddt, fs_nm_faktur
			)
			SELECT 	'".trim($sKdComp)."', fs_kd_product, fs_kd_type,
					fs_nm_product, fb_active, fs_kd_cogs,
					fb_purchase, fb_sale, fb_transfer,
					fb_warning, fn_limitQuota, fs_kd_unit1,
					fb_ProdDept, fs_kd_group, fb_attribute,
					fn_balance, fn_valueBal, fs_kd_acno,
					fs_kd_acnoX, fs_kd_AcnoR, fs_kd_AcnoRx,
					fs_kd_component, fn_component, fs_UnitPurchase,
					fs_UnitSale, fs_kd_moving, fs_kd_jnsProd,
					fs_usrcrt, fd_usrcrt, fs_upddt,
					fd_upddt, fs_nm_faktur
            FROM 	tm_product (NOLOCK)
            WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_product = '".trim($sKdProduk)."'
			
			
			DELETE	FROM [".trim($sNmDB)."]..tx_unitproduct
			WHERE	[".trim($sNmDB)."]..tx_unitproduct.fs_kd_product = '".trim($sKdProduk)."'
			
			INSERT INTO [".trim($sNmDB)."]..tx_unitproduct(
					fs_kd_comp, fs_kd_product, fs_kd_unit,
					fs_kd_type, fb_active, fs_usrcrt,
					fd_usrcrt, fs_upddt, fd_upddt
			)
			SELECT	'".trim($sKdComp)."', fs_kd_product, fs_kd_unit,
					fs_kd_type, fb_active, fs_usrcrt,
					fd_usrcrt, fs_upddt, fd_upddt
			FROM 	tx_unitproduct (NOLOCK)
            WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_product = '".trim($sKdProduk)."'
			
			
			DELETE	FROM [".trim($sNmDB)."]..tm_motortp
			WHERE	[".trim($sNmDB)."]..tm_motortp.fs_kd_product = '".trim($sKdProduk)."'
			
			INSERT INTO [".trim($sNmDB)."]..tm_motortp(
					fs_kd_comp, fs_kd_product, fs_type,
					fs_cilinder, fs_chasis, fs_engine,
					fs_keyno, fs_grpMotor, fs_kd_componen,
					fn_componen, fn_discount, fb_active,
					fs_usrcrt, fd_usrcrt, fs_upddt, fd_upddt
			)
			SELECT	'".trim($sKdComp)."', fs_kd_product, fs_type,
					fs_cilinder, fs_chasis, fs_engine,
					fs_keyno, fs_grpMotor, fs_kd_componen,
					fn_componen, fn_discount, fb_active,
					fs_usrcrt, fd_usrcrt, fs_upddt, fd_upddt
			FROM 	tm_motortp (NOLOCK)
            WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_product = '".trim($sKdProduk)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function gridbeli()
	{
		$xSQL = ("
			SELECT	a.fs_code fs_kd_dept, a.fs_count,
					b.fs_kd_product, b.fs_nm_product,
					ISNULL(c.fn_unitprcc, 0) fn_harga,
					ISNULL(c.fn_discc, 0) fn_diskon,
					ISNULL(c.fn_depositc, 0) fn_deposit
			FROM	tm_addr a (NOLOCK)
			INNER JOIN tm_product b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	LTRIM(RTRIM(b.fs_kd_product)) <> ''
			LEFT JOIN tm_unitprcpj c (NOLOCK) ON b.fs_kd_comp = c.fs_kd_comp
				AND	a.fs_code = c.fs_kd_dept
				AND	a.fs_count = c.fs_count
				AND	b.fs_kd_product = c.fs_kd_product
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	a.fs_cdtyp = '03'
			ORDER BY a.fs_code, a.fs_count, b.fs_kd_product, b.fs_nm_product
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function gridjual()
	{
		$xSQL = ("
			SELECT	a.fs_code fs_kd_dept, a.fs_count,
					b.fs_kd_product, b.fs_nm_product,
					ISNULL(c.fn_unitprcc, 0) fn_harga,
					ISNULL(c.fn_grsmargin, 0) fn_diskon,
					ISNULL(c.fn_bbn, 0) fn_bbn,
					ISNULL(c.fn_jualksi, 0) fn_dp
			FROM	tm_addr a (NOLOCK)
			INNER JOIN tm_product b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	LTRIM(RTRIM(b.fs_kd_product)) <> ''
			LEFT JOIN tm_unitprcoe c (NOLOCK) ON b.fs_kd_comp = c.fs_kd_comp
				AND	a.fs_code = c.fs_kd_dept
				AND	a.fs_count = c.fs_count
				AND	b.fs_kd_product = c.fs_kd_product
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	a.fs_cdtyp = '03'
			ORDER BY a.fs_code, a.fs_count, b.fs_kd_product, b.fs_nm_product
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function griddetil_all()
	{
		$xSQL = ("
			SELECT	a.fs_kd_product, a.fs_nm_product,
					ISNULL(b.fs_chasis, '') fs_rangka,
					ISNULL(b.fs_engine, '') fs_mesin,
					ISNULL(b.fs_cilinder, 0) fn_cc,
					a.fs_kd_group, ISNULL(c.fs_nm_group, '') fs_nm_group,
					a.fs_kd_acno, ISNULL(d.fs_nm_acno, '') fs_nm_acno,
					a.fs_kd_acnox, ISNULL(e.fs_nm_acno, '') fs_nm_acnox,
					a.fb_active, fs_status = CASE a.fb_active WHEN '1' THEN 'ACTIVE' ELSE 'NON ACTIVE' END
            FROM 	tm_product a (NOLOCK)
			LEFT JOIN tm_motortp b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_product = b.fs_kd_product
			LEFT JOIN tm_grpacno c (NOLOCK) ON a.fs_kd_group = c.fs_kd_group
			LEFT JOIN tm_acno d (NOLOCK) ON a.fs_kd_acno = d.fs_kd_acno
			LEFT JOIN tm_acno e (NOLOCK) ON a.fs_kd_acnox = e.fs_kd_acno
            WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	LTRIM(RTRIM(a.fs_kd_product)) <> ''
			ORDER BY a.fs_kd_product, a.fs_nm_product
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function griddetil($nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempprod".$xUser.$xIP."%' )
					DROP TABLE #tempprod".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), a.fs_kd_product, a.fs_nm_product,
					ISNULL(b.fs_chasis, '') fs_rangka,
					ISNULL(b.fs_engine, '') fs_mesin,
					ISNULL(b.fs_cilinder, 0) fn_cc,
					a.fs_kd_group, ISNULL(c.fs_nm_group, '') fs_nm_group,
					a.fs_kd_acno, ISNULL(d.fs_nm_acno, '') fs_nm_acno,
					a.fs_kd_acnox, ISNULL(e.fs_nm_acno, '') fs_nm_acnox,
					a.fb_active, fs_status = CASE a.fb_active WHEN '1' THEN 'ACTIVE' ELSE 'NON ACTIVE' END
			INTO	#tempprod".$xUser.$xIP."
            FROM 	tm_product a (NOLOCK)
			LEFT JOIN tm_motortp b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_product = b.fs_kd_product
			LEFT JOIN tm_grpacno c (NOLOCK) ON a.fs_kd_group = c.fs_kd_group
			LEFT JOIN tm_acno d (NOLOCK) ON a.fs_kd_acno = d.fs_kd_acno
			LEFT JOIN tm_acno e (NOLOCK) ON a.fs_kd_acnox = e.fs_kd_acno
            WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	LTRIM(RTRIM(a.fs_kd_product)) <> ''
			ORDER BY a.fs_kd_product, a.fs_nm_product
		");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempprod".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempprod".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>