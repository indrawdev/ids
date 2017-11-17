<?php

class MTBRekon extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function gridheader()
	{
		$xSQL =	("
			SELECT	fs_nm_code AS fs_nm_department
			FROM 	tm_addr
			WHERE 	fs_cdtyp = '03'
		");
		
	    $sSQL = $this->db->query($xSQL);
		
		return $sSQL;
	}
	
	function loadrekening_I($xPeriodeAwal,$xPeriodeAkhir,$xLevel,$gComp)
	{
		$xSQL = "";	 
		
		if (trim($xPeriodeAwal) == '') {
			$xPeriodeAwal = '201201';
		}
		
		if (trim($xPeriodeAkhir) == '') {
			$xPeriodeAkhir = '201212';
		}
		
		$xWhere = (" AND a.fd_periode >= ''".$xPeriodeAwal."'' AND a.fd_periode <= ''".$xPeriodeAkhir ."''");
		
		if ($xLevel <> "" AND trim($xLevel) <> 0 ) {
			$xWhere = $xWhere  & "|" & sprintf("%02d",$xLevel);
		}
		
		$xSQL = ("EXEC Stp_TBRecon 'TBRecon','".trim($xWhere)."'");
		
		$this->db->query($xSQL);
	}
	
	function loadrekening_II($gComp)
	{
		$xSQL = ("
			SELECT	DISTINCT fs_kd_acno=LTRIM(RTRIM(a.fs_kd_acno)),
					fs_nm_acno = CASE WHEN LEN(a.fs_kd_acno) = 5 THEN '   ' + LTRIM(RTRIM(a.fs_nm_acno))
						WHEN LEN(a.fs_kd_acno) = 9 THEN '         ' + LTRIM(RTRIM(a.fs_nm_acno))
						WHEN LEN(a.fs_kd_acno) = 13 THEN '              ' + LTRIM(RTRIM(a.fs_nm_acno))
						WHEN LEN(a.fs_kd_acno) = 17 THEN '                       ' + LTRIM(RTRIM(a.fs_nm_acno))
						ELSE LTRIM(RTRIM(a.fs_nm_acno)) END,
					fs_nm_dept = (	SELECT 	TOP 1 LTRIM(RTRIM(x.fs_nm_code))
									FROM 	tm_addr x(NOLOCK)
									WHERE 	x.fs_kd_comp = a.fs_kd_comp
										AND x.fs_cdtyp = '03'
										AND LTRIM(RTRIM(x.fs_code)) + LTRIM(RTRIM(x.fs_count)) = LTRIM(RTRIM(b.DEPTCD))
								),
					Periode = ISNULL((b.Periode), 0),
					BDebet = ISNULL((b.BDebet), 0),
					BCredit = ISNULL((b.BCredit), 0),
					Debet = ISNULL((b.Debet), 0),
					Credit = ISNULL((b.Credit), 0),
					fn_balance = ISNULL((ISNULL(b.BDebet,0) - b.BCredit) + (b.Debet - b.Credit), 0),
					DEPTCD = ISNULL((b.DEPTCD), 0)
			INTO #TMPDT
			FROM	tm_acno a (NOLOCK), table2 b (NOLOCK)
			WHERE 	a.fs_kd_comp = '".trim($gComp)."'
				AND LTRIM(RTRIM(a.fs_kd_acno)) = RTRIM(LTRIM(b.fs_kd_acno)) 
				AND	LTRIM(RTRIM(a.fs_kd_acno)) <> ''
			ORDER BY b.Periode, b.DEPTCD, a.fs_kd_acno ASC
			
			IF EXISTS(SELECT name FROM sysobjects WHERE name = N't_tempdt' AND type = 'U') DROP TABLE t_tempdt
			
			CREATE TABLE  t_tempdt(
					fs_kd_acno	VARCHAR(30)NOT NULL DEFAULT (' '),
					fs_nm_acno	VARCHAR(100)NOT NULL DEFAULT (' '),
					fs_nm_dept	VARCHAR(100)NOT NULL DEFAULT (' '),
					Periode		VARCHAR(12)NOT NULL DEFAULT (' ') ,
					BDebet  	NUMERIC(35,13),
					BCredit 	NUMERIC(35,13),
					Debet   	NUMERIC(35,13),
					Credit  	NUMERIC(35,13),
					fn_balance	NUMERIC(35,13),
					DEPTCD 		VARCHAR(15)NOT NULL DEFAULT (' '))                  
			
			INSERT INTO t_tempdt
			SELECT * FROM #TMPDT
		");
		
		$this->db->query($xSQL);
	}
	
	function loadrekening_III($xPeriodeAwal,$xPeriodeAkhir,$xLevel,$gComp)
	{
		$arr0 = array();
		
		$isSQL ='';
		$jsSQL ='';
		$xHead ='';
		
		$xSQL = ("
			SELECT	DISTINCT [DEPTCD] = LTRIM(RTRIM(fs_code)) + LTRIM(RTRIM(fs_count)),
					[DEPTNM] = LTRIM(RTRIM(fs_nm_code))
			FROM 	tm_addr (NOLOCK)
			WHERE 	fs_kd_comp = '".trim($gComp)."'
				AND fs_cdtyp = '03'
		");
		
		$sSQL = $this->db->query($xSQL);
		
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $row0)
			{
				$isSQL = $isSQL.("
					[".trim($row0->DEPTNM)."] = ISNULL((
													SELECT	SUM(c.Bdebet)
													FROM 	t_tempdt c (NOLOCK)  
													WHERE 	RTRIM(LTRIM(c.fs_nm_dept)) = '".trim($row0->DEPTNM)."'
														AND a.fs_kd_acno = c.fs_kd_acno 
													GROUP BY c.fs_kd_acno, c.fs_nm_acno, c.fs_nm_dept
												), 0),
				"); 
				$isSQL = $isSQL.("
					[".trim($row0->DEPTNM)."] = ISNULL((
													SELECT	SUM(c.BCredit)
													FROM 	t_tempdt c (NOLOCK)  
													WHERE 	RTRIM(LTRIM(c.fs_nm_dept)) = '".trim($row0->DEPTNM)."'
														AND a.fs_kd_acno = c.fs_kd_acno 
													GROUP BY c.fs_kd_acno, c.fs_nm_acno, c.fs_nm_dept
												), 0),
					");
				$isSQL = $isSQL.("
					[".trim($row0->DEPTNM)."] = ISNULL((
													SELECT	SUM(c.Debet)
													FROM	t_tempdt c (NOLOCK)
													WHERE 	RTRIM(LTRIM(c.fs_nm_dept)) = '".trim($row0->DEPTNM)."'
														AND a.fs_kd_acno = c.fs_kd_acno
													GROUP BY c.fs_kd_acno, c.fs_nm_acno, c.fs_nm_dept
												), 0),
				");
				$isSQL = $isSQL.("
					[".trim($row0->DEPTNM)."] = ISNULL((
													SELECT	SUM(c.Credit)
													FROM 	t_tempdt c (NOLOCK)
													WHERE 	RTRIM(LTRIM(c.fs_nm_dept)) = '".trim($row0->DEPTNM)."'
														AND a.fs_kd_acno = c.fs_kd_acno 
													GROUP BY c.fs_kd_acno, c.fs_nm_acno, c.fs_nm_dept
												), 0),
				");
				$isSQL = $isSQL.("
					[".trim($row0->DEPTNM)."] = ISNULL((
													SELECT	SUM(c.fn_balance)
													FROM 	t_tempdt c (NOLOCK)
													WHERE 	RTRIM(LTRIM(c.fs_nm_dept)) = '".trim($row0->DEPTNM)."'
														AND a.fs_kd_acno = c.fs_kd_acno
													GROUP BY c.fs_kd_acno, c.fs_nm_acno, c.fs_nm_dept
												), 0),
					");
				$jsSQL = $jsSQL.("
					[".trim($row0->DEPTNM)."-A"."] = ISNULL((
														SELECT	SUM(c.Bdebet) - SUM(c.BCredit)
														FROM	t_tempdt c (NOLOCK)
														WHERE 	RTRIM(LTRIM(c.fs_nm_dept)) = '".trim($row0->DEPTNM)."'
															AND a.fs_kd_acno = c.fs_kd_acno
														GROUP BY c.fs_kd_acno, c.fs_nm_acno, c.fs_nm_dept
													), 0),
				");
				//ISNULL((SELECT CAST(SUM(c.Debet) AS VARCHAR) 
				$jsSQL = $jsSQL.("
					[".trim($row0->DEPTNM)."-B"."] = ISNULL((
														SELECT	SUM(c.Debet)
														FROM 	t_tempdt c (NOLOCK)
														WHERE 	RTRIM(LTRIM(c.fs_nm_dept)) = '".trim($row0->DEPTNM)."'
															AND a.fs_kd_acno = c.fs_kd_acno
														GROUP BY c.fs_kd_acno, c.fs_nm_acno, c.fs_nm_dept
													),0),
				");
				//ISNULL((SELECT CAST(SUM(c.Credit) AS VARCHAR) 
				$jsSQL = $jsSQL.("
					[".trim($row0->DEPTNM)."-C"."] = ISNULL((
														SELECT	SUM(c.Credit)
														FROM 	t_tempdt c (NOLOCK)
														WHERE 	RTRIM(LTRIM(c.fs_nm_dept)) = '".trim($row0->DEPTNM)."'
															AND a.fs_kd_acno = c.fs_kd_acno
														GROUP BY c.fs_kd_acno, c.fs_nm_acno, c.fs_nm_dept
													), 0),
				");
				//ISNULL((SELECT CAST(SUM(c.fn_balance)AS VARCHAR)
				$jsSQL = $jsSQL.("
					[".trim($row0->DEPTNM)."] = ISNULL((
													SELECT	SUM(c.fn_balance)
													FROM	t_tempdt c (NOLOCK)
													WHERE 	RTRIM(LTRIM(c.fs_nm_dept)) = '".trim($row0->DEPTNM)."' 
														AND a.fs_kd_acno = c.fs_kd_acno
													GROUP BY c.fs_kd_acno, c.fs_nm_acno, c.fs_nm_dept
												), 0),
				"); 
			}
			
			$isSQL = ("
				SELECT	DISTINCT fs_kd_acno,fs_nm_acno,
						".$isSQL.
						"[TOTAL] = ISNULL((
									SELECT	SUM(c.fn_balance)
									FROM 	#TMPDT c (NOLOCK)
									WHERE 	LTRIM(RTRIM(a.fs_kd_acno)) = LTRIM(RTRIM(c.fs_kd_acno)) 
									GROUP BY c.fs_kd_acno
								), 0)
				FROM	tm_acno a(NOLOCK)
				WHERE	LTRIM(RTRIM(a.fs_kd_acno)) <> ''
				ORDER BY a.fs_kd_acno
			");
			
			$jsSQL = ("
				SELECT	DISTINCT [NO] = IDENTITY(INT, 1, 1), [ REKENING] = a.fs_kd_acno, KETERANGAN = a.fs_nm_acno,
						".$jsSQL.
						"[TOTAL] = ISNULL((
									SELECT	SUM(c.fn_balance)
									FROM 	t_tempdt c (NOLOCK)
									WHERE 	LTRIM(RTRIM(a.fs_kd_acno)) = LTRIM(RTRIM(c.fs_kd_acno))
									GROUP BY c.fs_kd_acno
								), 0)
				INTO #TMPTBREKON
				FROM	tm_acno a(NOLOCK)
				WHERE	LTRIM(RTRIM(a.fs_kd_acno)) <> ''
			");
			
			if (trim($xLevel) == 1)
			{
				$jsSQL = $jsSQL.("
					AND	LEN(a.fs_kd_acno) = 1
				");
			}
			else if (trim($xLevel) > 1)
			{
				$jsSQL = $jsSQL.("
					AND	LEN(a.fs_kd_acno) - LEN(REPLACE(a.fs_kd_acno,'.','')) = ".($xLevel - 1)."
				");
			}
			
			$jsSQL = $jsSQL.("
				SELECT	*
				FROM 	#TMPTBREKON
				ORDER BY [NO], [ REKENING]
				
				DROP TABLE #TMPDT
				DROP TABLE #TMPTBREKON
			");
		}
		
		$sSQL2 = $this->db->query($jsSQL);
		return $sSQL2;
	}
	
	function grid_header_index()
	{
		$return = array();
		
		$xSQL =	("
			SELECT	fs_nm_code AS fs_nm_department
			FROM 	tm_addr
			WHERE 	fs_cdtyp = '03'
		");
		
		$query = $this->db->query($xSQL);
		
		foreach ($query->result_array() as $row)
		{
			$return[] = $row;
		}
		return $return;  
	}
}
?>