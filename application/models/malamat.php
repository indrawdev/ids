<?php

class MAlamat extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function cek_kode($sTipe,$sCode,$sCount)
	{
		$xSQL = ("
			SELECT	fs_cdtyp, fs_code, fs_count
            FROM   	tm_addr
            WHERE  	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_cdtyp = '".trim($sTipe)."'
				AND	fs_code = '".trim($sCode)."'
				AND	fs_count = '".trim($sCount)."'
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function detil($sTipe,$sCode,$sCount)
	{
		if ($sTipe == '01')
		{
			$xSQL = ("
				SELECT	ta.fs_code, ta.fs_count, ta.fs_nm_code,
				ts.fs_kd_grpacno, fs_nm_grpacno =
				ISNULL((SELECT 	TOP 1 tg.fs_nm_group
						FROM 	tm_grpacno tg (NOLOCK)
						WHERE 	ts.fs_kd_grpacno = tg.fs_kd_group
							AND ts.fs_kd_comp = tg.fs_kd_comp
				), ' '),
				ts.fs_kd_acno, fs_nm_acno =
				ISNULL((SELECT 	TOP 1 tn.fs_nm_acno
						FROM 	tm_acno tn (NOLOCK)
						WHERE 	ts.fs_kd_acno = tn.fs_kd_acno
							AND ts.fs_kd_comp = tn.fs_kd_comp
				), ' '),
				ts.fs_kd_currency fs_kd_curr, fs_nm_curr =
				ISNULL((SELECT TOP 1 tv.fs_nm_vareable
						FROM 	tm_vareable tv (NOLOCK)
						WHERE 	ts.fs_kd_currency = tv.fs_kd_vareable
							AND tv.fs_key = '10'
							AND ts.fs_kd_comp = tv.fs_kd_comp
				), ' '),
				ts.fs_kd_npwp, ts.fs_nm_npwp, ts.fd_npwp, ts.fs_addrnpwp,
				ts.fb_active, ts.fb_warning,
				ts.fb_limitproduct, fs_nm_limitproduct =
				ISNULL((SELECT 	TOP 1 tl.fs_nm_limitproduct
						FROM 	tm_limitproduct tl (NOLOCK)
						WHERE 	ts.fs_kd_limitproduct = tl.fs_kd_limitproduct
							AND ts.fs_kd_comp = tl.fs_kd_comp
				), 0),
				ts.fn_limit, ts.fn_bufferlimit, ts.fn_balance,
				ts.fs_kd_refnolpo, ts.fd_lpo, ts.fs_kd_deptlpo,
				fs_nm_dept = ISNULL((SELECT	TOP 1 ta2.fs_nm_code
									FROM 	tm_addr ta2 (NOLOCK)
									WHERE 	ta2.fs_kd_comp = ta.fs_kd_comp
										AND ta2.fs_cdtyp = '03'
										AND ta2.fs_code = ts.fs_kd_deptlpo
										AND ta2.fs_count = ts.fs_count_deptlpo
							), ' '),
				ts.fn_amountlpo, ts.fs_kd_limitproduct, ts.fs_kd_linkbank,
				ts.fs_count_linkbank, fs_nm_bank =
				ISNULL((SELECT	TOP 1ta3.fs_nm_code
						FROM 	tm_addr ta3 (NOLOCK)
						WHERE 	ts.fs_kd_comp = ta3.fs_kd_comp
							AND ts.fs_kd_linkbank = ta3.fs_code
							AND ts.fs_count_linkbank = ta3.fs_count
							AND ta3.fs_cdtyp = '04'
				), ' ')
				FROM 	tm_addr ta (NOLOCK), tm_supplier ts (NOLOCK)
				WHERE 	ta.fs_kd_comp = ts.fs_kd_comp
					AND ta.fs_cdtyp = '01'
					AND ta.fs_code = ts.fs_kd_supplier
					AND ta.fs_count = ts.fs_count
					AND ta.fs_code = '".trim($sCode)."'
					AND	ta.fs_count = '".trim($sCount)."'
			");
		}
		elseif ($sTipe == '02')
		{
			$xSQL = ("
				SELECT	ta.fs_code, ta.fs_count, ta.fs_nm_code,
						ts.fs_kd_grpacno, fs_nm_grpacno =
						ISNULL((SELECT 	TOP 1 tg.fs_nm_group
								FROM 	tm_grpacno tg (NOLOCK) 
								WHERE 	ts.fs_kd_grpacno = tg.fs_kd_group
									AND ts.fs_kd_comp = tg.fs_kd_comp
						), ' '),
						ts.fs_kd_acno, fs_nm_acno =
						ISNULL((SELECT	TOP 1 tn.fs_nm_acno
								FROM 	tm_acno tn (NOLOCK)
								WHERE 	ts.fs_kd_acno = tn.fs_kd_acno
									AND ts.fs_kd_comp = tn.fs_kd_comp
						), ' '),
						ts.fs_kd_currency fs_kd_curr, fs_nm_curr =
						ISNULL((SELECT	TOP 1 tv.fs_nm_vareable
								FROM 	tm_vareable tv (NOLOCK)
								WHERE 	ts.fs_kd_currency = tv.fs_kd_vareable
									AND tv.fs_key = '10'
									AND ts.fs_kd_comp = tv.fs_kd_comp
						), ' '),
						ts.fs_kd_npwp, ts.fs_nm_npwp, ts.fd_npwp,
						ts.fs_addrnpwp, ts.fs_score, ts.fb_active,
						ts.fb_locksale, ts.fb_warning, ts.fb_limitproduct,
						ts.fn_limit, ts.fn_bufferlimit, ts.fn_balance,
						ts.fs_kd_refnolso, ts.fd_lso, ts.fs_kd_deptlso,
						ISNULL((SELECT 	TOP 1 ta2.fs_nm_code
								FROM 	tm_addr ta2 (NOLOCK)
								WHERE 	ta2.fs_kd_comp = ta.fs_kd_comp
									AND ta2.fs_cdtyp = '03'
									AND ta2.fs_code = ts.fs_kd_deptlso
									AND ta2.fs_count = ts.fs_count_deptlso
						), ' '),
						ts.fn_amountlso, ts.fs_kd_limitproduct, fs_nm_limitproduct =
						ISNULL((SELECT 	TOP 1 tl.fs_nm_limitproduct
								FROM 	tm_limitproduct tl (NOLOCK)
								WHERE 	ts.fs_kd_limitproduct = tl.fs_kd_limitproduct
									AND ts.fs_kd_comp = tl.fs_kd_comp
						), 0),
						ts.fs_kd_linkbank, ts.fs_count_linkbank, fs_nm_bank =
						ISNULL((SELECT 	TOP 1ta3.fs_nm_code
								FROM 	tm_addr ta3 (NOLOCK)
								WHERE 	ts.fs_kd_comp = ta3.fs_kd_comp
									AND ts.fs_kd_linkbank = ta3.fs_code
									AND ts.fs_count_linkbank = ta3.fs_count
									AND ta3.fs_cdtyp = '04'
						), ' '),
						ta.fs_kewarganegaraan, ta.fs_idcard, ta.fs_pasport, fs_kd_tipe =
						ISNULL((SELECT 	TOP 1 x.fs_kd_custtype
								FROM 	tm_customer x (NOLOCK)
								WHERE 	x.fs_kd_comp = ta.fs_kd_comp
									AND ta.fs_cdtyp = '02'
									AND x.fs_kd_customer = ta.fs_code
									AND x.fs_count = ta.fs_count
						), ' '), 
						fs_nm_tipe =
						ISNULL((SELECT 	TOP 1 x.fs_nm_vareable
								FROM 	tm_vareable x (NOLOCK)
								INNER JOIN tm_customer y ON x.fs_kd_vareable = y.fs_kd_custtype
								WHERE x.fs_kd_comp = ta.fs_kd_comp
									AND ta.fs_cdtyp = '02'
									AND y.fs_kd_customer = ta.fs_code
									AND y.fs_count = ta.fs_count
									AND x.fs_key = '61'
						), ' ') 
				FROM 	tm_addr ta (NOLOCK), tm_customer ts (NOLOCK)
				WHERE 	ta.fs_kd_comp = ts.fs_kd_comp
					AND ta.fs_cdtyp = '02'
					AND ta.fs_code = ts.fs_kd_customer
					AND ta.fs_count = ts.fs_count
					AND ta.fs_code = '".trim($sCode)."'
					AND	ta.fs_count = '".trim($sCount)."'
			");
		}
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>