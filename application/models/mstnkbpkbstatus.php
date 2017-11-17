<?php

class MStnkBpkbStatus extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function grid_all($sTgl,$sTgl2,$sCustCd,$sCount,$sCustCd2,$sCount2)
	{
		$xSQL = ("
			SELECT	fs_kd_dept = LTRIM(RTRIM(a.fs_kd_dept)) + LTRIM(RTRIM(a.fs_count)),
					fs_nm_dept = ISNULL(b.fs_nm_code, ' '),
					fs_kd_cust = LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)),
					fs_nm_cust = ISNULL(c.fs_nm_code, ' '),
					fs_status = CASE
						WHEN ISNULL((
								SELECT	TOP 1 h.fd_refno
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') = '' THEN 'BELUM PROSES STNK'
						WHEN
							 ISNULL((
								SELECT	TOP 1 h.fd_refno
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') <> ''
							AND 
							ISNULL((
								SELECT  TOP 1 i.fd_rcvdt
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') = ''
						THEN 'PROSES STNK BELUM SELESAI'
						WHEN
							 ISNULL((
								SELECT	TOP 1 h.fd_refno
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') <> ''
							AND 
							ISNULL((
								SELECT  TOP 1 i.fd_rcvdt
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') <> ''
							AND ISNULL(d.fd_stnk, '') = ''
						THEN 'STNK JADI TP BELUM DISERAHKAN KE CUST'
						WHEN
							 ISNULL((
								SELECT	TOP 1 h.fd_refno
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') <> ''
							AND 
							ISNULL((
								SELECT  TOP 1 i.fd_rcvdt
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') <> ''
							AND ISNULL(d.fd_stnk, '') <> ''
						THEN 'STNK JADI & SUDAH DISERAHKAN KE CUST'
					END,
					fd_serah_ke_agen = ISNULL((
							SELECT	TOP 1 CASE LEN(LTRIM(RTRIM(h.fd_refno))) WHEN 8 THEN CONVERT(VARCHAR(10), CONVERT(DATETIME, h.fd_refno, 105), 105) ELSE ' ' END
							FROM    tx_trxbirojs h (NOLOCK)
							INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
									AND h.fs_kd_dept = i.fs_kd_dept
									AND h.fs_count = i.fs_count
									AND h.fs_kd_trx = i.fs_kd_trx
									AND h.fs_kd_strx = i.fs_kd_strx
									AND h.fs_refno = i.fs_refno
									AND i.fs_kd_comp = a.fs_kd_comp
									AND i.fs_kd_cussup = a.fs_kd_cussup
									AND i.fs_countcussup = a.fs_countcussup
									AND i.fs_chasis = a.fs_chasis
									AND i.fs_engine = a.fs_machine
									AND h.fs_kd_strx = '0100'
						), ' '),
					fd_terima_dr_agen = ISNULL((
							SELECT  TOP 1 CASE LEN(LTRIM(RTRIM(i.fd_rcvdt))) WHEN 8 THEN CONVERT(VARCHAR(10), CONVERT(DATETIME, i.fd_rcvdt, 105), 105) ELSE ' ' END
							FROM    tx_trxbirojs h (NOLOCK)
							INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
									AND h.fs_kd_dept = i.fs_kd_dept
									AND h.fs_count = i.fs_count
									AND h.fs_kd_trx = i.fs_kd_trx
									AND h.fs_kd_strx = i.fs_kd_strx
									AND h.fs_refno = i.fs_refno
									AND i.fs_kd_comp = a.fs_kd_comp
									AND i.fs_kd_cussup = a.fs_kd_cussup
									AND i.fs_countcussup = a.fs_countcussup
									AND i.fs_chasis = a.fs_chasis
									AND i.fs_engine = a.fs_machine
									AND h.fs_kd_strx = '0100'
						), ' '),
					fs_stnk = ISNULL(d.fs_stnk, ' '),
					fd_serah_stnk_ke_cust = CASE LEN(LTRIM(RTRIM(d.fd_stnk))) WHEN 8 THEN CONVERT(VARCHAR(10), CONVERT(DATETIME, d.fd_stnk, 105), 105) ELSE ' ' END,
					fs_nopol = ISNULL(d.fs_nopol, ' '),
					fn_bbn = ISNULL((
							SELECT  TOP 1 ISNULL(i.fn_bbn,0)
							FROM    tx_trxbirojs h (NOLOCK)
							INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
									AND h.fs_kd_dept = i.fs_kd_dept
									AND h.fs_count = i.fs_count
									AND h.fs_kd_trx = i.fs_kd_trx
									AND h.fs_kd_strx = i.fs_kd_strx
									AND h.fs_refno = i.fs_refno
									AND i.fs_kd_comp = a.fs_kd_comp
									AND i.fs_kd_cussup = a.fs_kd_cussup
									AND i.fs_countcussup = a.fs_countcussup
									AND i.fs_chasis = a.fs_chasis
									AND i.fs_engine = a.fs_machine
									AND h.fs_kd_strx = '0100'
						), 0), 
					fn_jasa = ISNULL((
							SELECT  TOP 1 ISNULL(i.fn_jasa, 0)
							FROM    tx_trxbirojs h (NOLOCK)
							INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
									AND h.fs_kd_dept = i.fs_kd_dept
									AND h.fs_count = i.fs_count
									AND h.fs_kd_trx = i.fs_kd_trx
									AND h.fs_kd_strx = i.fs_kd_strx
									AND h.fs_refno = i.fs_refno
									AND i.fs_kd_comp = a.fs_kd_comp
									AND i.fs_kd_cussup = a.fs_kd_cussup
									AND i.fs_countcussup = a.fs_countcussup
									AND i.fs_chasis = a.fs_chasis
									AND i.fs_engine = a.fs_machine
									AND h.fs_kd_strx = '0100'
						), 0),
					fs_rangka = a.fs_chasis, fs_mesin = a.fs_machine,
					fs_biro_jasa = ISNULL((
							SELECT  TOP 1 (	SELECT 	TOP 1 x.fs_nm_birojs
											FROM 	tm_masterbirojs x (NOLOCK)
											WHERE 	x.fs_kd_comp = h.fs_kd_comp
												AND x.fs_kd_birojs = h.fs_kd_agent
											)
							FROM    tx_trxbirojs h (NOLOCK)
							INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
									AND h.fs_kd_dept = i.fs_kd_dept
									AND h.fs_count = i.fs_count
									AND h.fs_kd_trx = i.fs_kd_trx
									AND h.fs_kd_strx = i.fs_kd_strx
									AND h.fs_refno = i.fs_refno
									AND i.fs_kd_comp = a.fs_kd_comp
									AND i.fs_kd_cussup = a.fs_kd_cussup
									AND i.fs_countcussup = a.fs_countcussup
									AND i.fs_chasis = a.fs_chasis
									AND i.fs_engine = a.fs_machine
									AND h.fs_kd_strx = '0100'
						), ' ')
			FROM   tm_posregsold a (NOLOCK)
			LEFT JOIN  tm_addr b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_dept = b.fs_code
				AND a.fs_count = b.fs_count
				AND b.fs_cdtyp = '03'
			LEFT JOIN  tm_addr c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND a.fs_kd_cussup = c.fs_code
				AND a.fs_countcussup = c.fs_count
				AND c.fs_cdtyp = '02'
			INNER JOIN tm_icregister d (NOLOCK) ON a.fs_kd_comp = d.fs_kd_comp
				AND a.fs_kd_dept = d.fs_kd_deptinv
				AND a.fs_count = d.fs_countdeptinv
				AND a.fs_kd_trx = d.fs_kd_trxinv
				AND a.fs_refno = d.fs_refnoinv
				AND a.fs_chasis = d.fs_rangka
				AND a.fs_machine = d.fs_machine
			");
		
		if (trim($sCustCd) <> '' and trim($sCustCd2) == '')
		{
			$xSQL = $xSQL.("
				AND LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)) = '".trim($sCustCd).trim($sCount)."'
			");
		}
		if (trim($sCustCd) <> '' and trim($sCustCd2) <> '')
		{
			$xSQL = $xSQL.("
				AND LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)) IN ('".trim($sCustCd).trim($sCount)."','".trim($sCustCd2).trim($sCount2)."')
			");
		}
		
		if (trim($sCustCd) == '' and trim($sCustCd2) == '')
		{
			if (trim($sTgl) <> '' and trim($sTgl2) <> '')
			{
				$xSQL = $xSQL.("
					AND a.fd_refno >= '".trim($sTgl)."'
					AND a.fd_refno <= '".trim($sTgl2)."'
				");
			}
		}
		
		$xSQL = $xSQL.("
			ORDER BY   b.fs_nm_code, c.fs_nm_code
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid($sTgl,$sTgl2,$sCustCd,$sCount,$sCustCd2,$sCount2,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
        $xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempstnk".$xUser.$xIP."%' )
					DROP TABLE #tempstnk".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_dept = LTRIM(RTRIM(a.fs_kd_dept)) + LTRIM(RTRIM(a.fs_count)),
					fs_nm_dept = ISNULL(b.fs_nm_code, ' '),
					fs_kd_cust = LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)),
					fs_nm_cust = ISNULL(c.fs_nm_code, ' '),
					fs_status = CASE
						WHEN ISNULL((
								SELECT	TOP 1 h.fd_refno
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') = '' THEN 'BELUM PROSES STNK'
						WHEN
							 ISNULL((
								SELECT	TOP 1 h.fd_refno
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') <> ''
							AND 
							ISNULL((
								SELECT  TOP 1 i.fd_rcvdt
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') = ''
						THEN 'PROSES STNK BELUM SELESAI'
						WHEN
							 ISNULL((
								SELECT	TOP 1 h.fd_refno
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') <> ''
							AND 
							ISNULL((
								SELECT  TOP 1 i.fd_rcvdt
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') <> ''
							AND ISNULL(d.fd_stnk, '') = ''
						THEN 'STNK JADI TP BELUM DISERAHKAN KE CUST'
						WHEN
							 ISNULL((
								SELECT	TOP 1 h.fd_refno
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') <> ''
							AND 
							ISNULL((
								SELECT  TOP 1 i.fd_rcvdt
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') <> ''
							AND ISNULL(d.fd_stnk, '') <> ''
						THEN 'STNK JADI & SUDAH DISERAHKAN KE CUST'
					END,
					fd_serah_ke_agen = ISNULL((
							SELECT	TOP 1 CASE LEN(LTRIM(RTRIM(h.fd_refno))) WHEN 8 THEN CONVERT(VARCHAR(10), CONVERT(DATETIME, h.fd_refno, 105), 105) ELSE ' ' END
							FROM    tx_trxbirojs h (NOLOCK)
							INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
									AND h.fs_kd_dept = i.fs_kd_dept
									AND h.fs_count = i.fs_count
									AND h.fs_kd_trx = i.fs_kd_trx
									AND h.fs_kd_strx = i.fs_kd_strx
									AND h.fs_refno = i.fs_refno
									AND i.fs_kd_comp = a.fs_kd_comp
									AND i.fs_kd_cussup = a.fs_kd_cussup
									AND i.fs_countcussup = a.fs_countcussup
									AND i.fs_chasis = a.fs_chasis
									AND i.fs_engine = a.fs_machine
									AND h.fs_kd_strx = '0100'
						), ' '),
					fd_terima_dr_agen = ISNULL((
							SELECT  TOP 1 CASE LEN(LTRIM(RTRIM(i.fd_rcvdt))) WHEN 8 THEN CONVERT(VARCHAR(10), CONVERT(DATETIME, i.fd_rcvdt, 105), 105) ELSE ' ' END
							FROM    tx_trxbirojs h (NOLOCK)
							INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
									AND h.fs_kd_dept = i.fs_kd_dept
									AND h.fs_count = i.fs_count
									AND h.fs_kd_trx = i.fs_kd_trx
									AND h.fs_kd_strx = i.fs_kd_strx
									AND h.fs_refno = i.fs_refno
									AND i.fs_kd_comp = a.fs_kd_comp
									AND i.fs_kd_cussup = a.fs_kd_cussup
									AND i.fs_countcussup = a.fs_countcussup
									AND i.fs_chasis = a.fs_chasis
									AND i.fs_engine = a.fs_machine
									AND h.fs_kd_strx = '0100'
						), ' '),
					fs_stnk = ISNULL(d.fs_stnk, ' '),
					fd_serah_stnk_ke_cust = CASE LEN(LTRIM(RTRIM(d.fd_stnk))) WHEN 8 THEN CONVERT(VARCHAR(10), CONVERT(DATETIME, d.fd_stnk, 105), 105) ELSE ' ' END,
					fs_nopol = ISNULL(d.fs_nopol, ' '),
					fn_bbn = ISNULL((
							SELECT  TOP 1 ISNULL(i.fn_bbn,0)
							FROM    tx_trxbirojs h (NOLOCK)
							INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
									AND h.fs_kd_dept = i.fs_kd_dept
									AND h.fs_count = i.fs_count
									AND h.fs_kd_trx = i.fs_kd_trx
									AND h.fs_kd_strx = i.fs_kd_strx
									AND h.fs_refno = i.fs_refno
									AND i.fs_kd_comp = a.fs_kd_comp
									AND i.fs_kd_cussup = a.fs_kd_cussup
									AND i.fs_countcussup = a.fs_countcussup
									AND i.fs_chasis = a.fs_chasis
									AND i.fs_engine = a.fs_machine
									AND h.fs_kd_strx = '0100'
						), 0), 
					fn_jasa = ISNULL((
							SELECT  TOP 1 ISNULL(i.fn_jasa, 0)
							FROM    tx_trxbirojs h (NOLOCK)
							INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
									AND h.fs_kd_dept = i.fs_kd_dept
									AND h.fs_count = i.fs_count
									AND h.fs_kd_trx = i.fs_kd_trx
									AND h.fs_kd_strx = i.fs_kd_strx
									AND h.fs_refno = i.fs_refno
									AND i.fs_kd_comp = a.fs_kd_comp
									AND i.fs_kd_cussup = a.fs_kd_cussup
									AND i.fs_countcussup = a.fs_countcussup
									AND i.fs_chasis = a.fs_chasis
									AND i.fs_engine = a.fs_machine
									AND h.fs_kd_strx = '0100'
						), 0),
					fs_rangka = a.fs_chasis, fs_mesin = a.fs_machine,
					fs_biro_jasa = ISNULL((
							SELECT  TOP 1 (	SELECT 	TOP 1 x.fs_nm_birojs
											FROM 	tm_masterbirojs x (NOLOCK)
											WHERE 	x.fs_kd_comp = h.fs_kd_comp
												AND x.fs_kd_birojs = h.fs_kd_agent
											)
							FROM    tx_trxbirojs h (NOLOCK)
							INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
									AND h.fs_kd_dept = i.fs_kd_dept
									AND h.fs_count = i.fs_count
									AND h.fs_kd_trx = i.fs_kd_trx
									AND h.fs_kd_strx = i.fs_kd_strx
									AND h.fs_refno = i.fs_refno
									AND i.fs_kd_comp = a.fs_kd_comp
									AND i.fs_kd_cussup = a.fs_kd_cussup
									AND i.fs_countcussup = a.fs_countcussup
									AND i.fs_chasis = a.fs_chasis
									AND i.fs_engine = a.fs_machine
									AND h.fs_kd_strx = '0100'
						), ' ')
			INTO	#tempstnk".$xUser.$xIP."
			FROM   	tm_posregsold a (NOLOCK)
			LEFT JOIN  tm_addr b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_dept = b.fs_code
				AND a.fs_count = b.fs_count
				AND b.fs_cdtyp = '03'
			LEFT JOIN  tm_addr c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND a.fs_kd_cussup = c.fs_code
				AND a.fs_countcussup = c.fs_count
				AND c.fs_cdtyp = '02'
			INNER JOIN tm_icregister d (NOLOCK) ON a.fs_kd_comp = d.fs_kd_comp
				AND a.fs_kd_dept = d.fs_kd_deptinv
				AND a.fs_count = d.fs_countdeptinv
				AND a.fs_kd_trx = d.fs_kd_trxinv
				AND a.fs_refno = d.fs_refnoinv
				AND a.fs_chasis = d.fs_rangka
				AND a.fs_machine = d.fs_machine
			");
		
		if (trim($sCustCd) <> '' and trim($sCustCd2) == '')
		{
			$xSQL = $xSQL.("
				AND LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)) = '".trim($sCustCd).trim($sCount)."'
			");
		}
		if (trim($sCustCd) <> '' and trim($sCustCd2) <> '')
		{
			$xSQL = $xSQL.("
				AND LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)) IN ('".trim($sCustCd).trim($sCount)."','".trim($sCustCd2).trim($sCount2)."')
			");
		}
		
		if (trim($sCustCd) == '' and trim($sCustCd2) == '')
		{
			if (trim($sTgl) <> '' and trim($sTgl2) <> '')
			{
				$xSQL = $xSQL.("
					AND a.fd_refno >= '".trim($sTgl)."'
					AND a.fd_refno <= '".trim($sTgl2)."'
				");
			}
		}
		
		$xSQL = $xSQL.("
			ORDER BY   b.fs_nm_code, c.fs_nm_code
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempstnk".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempstnk".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid2_all($sTgl,$sTgl2,$sCustCd,$sCount,$sCustCd2,$sCount2)
	{
		$xSQL = ("
			SELECT	fs_kd_dept = LTRIM(RTRIM(a.fs_kd_dept)) + LTRIM(RTRIM(a.fs_count)),
					fs_nm_dept = ISNULL(b.fs_nm_code, ' '),
					fs_kd_cust = LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)),
					fs_nm_cust = ISNULL(c.fs_nm_code, ' '),
					fs_status = CASE
						WHEN ISNULL((
								SELECT	TOP 1 h.fd_refno
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') = '' THEN 'BELUM PROSES BPKB'
						WHEN
							 ISNULL((
								SELECT	TOP 1 h.fd_refno
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') <> ''
							AND 
							ISNULL((
								SELECT  TOP 1 i.fd_rcvdt2
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') = ''
						THEN 'PROSES BPKB BELUM SELESAI'
						WHEN
							 ISNULL((
								SELECT	TOP 1 h.fd_refno
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') <> ''
							AND 
							ISNULL((
								SELECT  TOP 1 i.fd_rcvdt2
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') <> ''
							AND ISNULL(d.fd_bpkb, '') = ''
						THEN 'BPKB JADI TP BELUM DISERAHKAN KE CUST'
						WHEN
							 ISNULL((
								SELECT	TOP 1 h.fd_refno
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') <> ''
							AND 
							ISNULL((
								SELECT  TOP 1 i.fd_rcvdt
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') <> ''
							AND ISNULL(d.fd_bpkb, '') <> ''
						THEN 'BPKB JADI & SUDAH DISERAHKAN KE CUST'
					END,
					fd_serah_ke_agen = ISNULL((
							SELECT	TOP 1 CASE LEN(LTRIM(RTRIM(h.fd_refno))) WHEN 8 THEN CONVERT(VARCHAR(10), CONVERT(DATETIME, h.fd_refno, 105), 105) ELSE ' ' END
							FROM    tx_trxbirojs h (NOLOCK)
							INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
									AND h.fs_kd_dept = i.fs_kd_dept
									AND h.fs_count = i.fs_count
									AND h.fs_kd_trx = i.fs_kd_trx
									AND h.fs_kd_strx = i.fs_kd_strx
									AND h.fs_refno = i.fs_refno
									AND i.fs_kd_comp = a.fs_kd_comp
									AND i.fs_kd_cussup = a.fs_kd_cussup
									AND i.fs_countcussup = a.fs_countcussup
									AND i.fs_chasis = a.fs_chasis
									AND i.fs_engine = a.fs_machine
									AND h.fs_kd_strx = '0100'
						), ' '),
					fd_terima_dr_agen = ISNULL((
							SELECT  TOP 1 CASE LEN(LTRIM(RTRIM(i.fd_rcvdt2))) WHEN 8 THEN CONVERT(VARCHAR(10), CONVERT(DATETIME, i.fd_rcvdt2, 105), 105) ELSE ' ' END
							FROM    tx_trxbirojs h (NOLOCK)
							INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
									AND h.fs_kd_dept = i.fs_kd_dept
									AND h.fs_count = i.fs_count
									AND h.fs_kd_trx = i.fs_kd_trx
									AND h.fs_kd_strx = i.fs_kd_strx
									AND h.fs_refno = i.fs_refno
									AND i.fs_kd_comp = a.fs_kd_comp
									AND i.fs_kd_cussup = a.fs_kd_cussup
									AND i.fs_countcussup = a.fs_countcussup
									AND i.fs_chasis = a.fs_chasis
									AND i.fs_engine = a.fs_machine
									AND h.fs_kd_strx = '0100'
						), ' '),
					fs_bpkb = ISNULL(d.fs_bpkb, ' '),
					fd_serah_bpkb_ke_cust = CASE LEN(LTRIM(RTRIM(d.fd_bpkb))) WHEN 8 THEN CONVERT(VARCHAR(10), CONVERT(DATETIME, d.fd_bpkb, 105), 105) ELSE ' ' END,
					fs_nopol = ISNULL(d.fs_nopol, ' '),
					fn_bbn = ISNULL((
							SELECT  TOP 1 ISNULL(i.fn_bbn,0)
							FROM    tx_trxbirojs h (NOLOCK)
							INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
									AND h.fs_kd_dept = i.fs_kd_dept
									AND h.fs_count = i.fs_count
									AND h.fs_kd_trx = i.fs_kd_trx
									AND h.fs_kd_strx = i.fs_kd_strx
									AND h.fs_refno = i.fs_refno
									AND i.fs_kd_comp = a.fs_kd_comp
									AND i.fs_kd_cussup = a.fs_kd_cussup
									AND i.fs_countcussup = a.fs_countcussup
									AND i.fs_chasis = a.fs_chasis
									AND i.fs_engine = a.fs_machine
									AND h.fs_kd_strx = '0100'
						), 0), 
					fn_jasa = ISNULL((
							SELECT  TOP 1 ISNULL(i.fn_jasa, 0)
							FROM    tx_trxbirojs h (NOLOCK)
							INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
									AND h.fs_kd_dept = i.fs_kd_dept
									AND h.fs_count = i.fs_count
									AND h.fs_kd_trx = i.fs_kd_trx
									AND h.fs_kd_strx = i.fs_kd_strx
									AND h.fs_refno = i.fs_refno
									AND i.fs_kd_comp = a.fs_kd_comp
									AND i.fs_kd_cussup = a.fs_kd_cussup
									AND i.fs_countcussup = a.fs_countcussup
									AND i.fs_chasis = a.fs_chasis
									AND i.fs_engine = a.fs_machine
									AND h.fs_kd_strx = '0100'
						), 0),
					fs_rangka = a.fs_chasis, fs_mesin = a.fs_machine,
					fs_biro_jasa = ISNULL((
							SELECT  TOP 1 (	SELECT 	TOP 1 x.fs_nm_birojs
											FROM 	tm_masterbirojs x (NOLOCK)
											WHERE 	x.fs_kd_comp = h.fs_kd_comp
												AND x.fs_kd_birojs = h.fs_kd_agent
											)
							FROM    tx_trxbirojs h (NOLOCK)
							INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
									AND h.fs_kd_dept = i.fs_kd_dept
									AND h.fs_count = i.fs_count
									AND h.fs_kd_trx = i.fs_kd_trx
									AND h.fs_kd_strx = i.fs_kd_strx
									AND h.fs_refno = i.fs_refno
									AND i.fs_kd_comp = a.fs_kd_comp
									AND i.fs_kd_cussup = a.fs_kd_cussup
									AND i.fs_countcussup = a.fs_countcussup
									AND i.fs_chasis = a.fs_chasis
									AND i.fs_engine = a.fs_machine
									AND h.fs_kd_strx = '0100'
						), ' ')
			FROM   tm_posregsold a (NOLOCK)
			LEFT JOIN  tm_addr b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_dept = b.fs_code
				AND a.fs_count = b.fs_count
				AND b.fs_cdtyp = '03'
			LEFT JOIN  tm_addr c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND a.fs_kd_cussup = c.fs_code
				AND a.fs_countcussup = c.fs_count
				AND c.fs_cdtyp = '02'
			INNER JOIN tm_icregister d (NOLOCK) ON a.fs_kd_comp = d.fs_kd_comp
				AND a.fs_kd_dept = d.fs_kd_deptinv
				AND a.fs_count = d.fs_countdeptinv
				AND a.fs_kd_trx = d.fs_kd_trxinv
				AND a.fs_refno = d.fs_refnoinv
				AND a.fs_chasis = d.fs_rangka
				AND a.fs_machine = d.fs_machine
			");
		
		if (trim($sCustCd) <> '' and trim($sCustCd2) == '')
		{
			$xSQL = $xSQL.("
				AND LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)) = '".trim($sCustCd).trim($sCount)."'
			");
		}
		if (trim($sCustCd) <> '' and trim($sCustCd2) <> '')
		{
			$xSQL = $xSQL.("
				AND LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)) IN ('".trim($sCustCd).trim($sCount)."','".trim($sCustCd2).trim($sCount2)."')
			");
		}
		
		if (trim($sCustCd) == '' and trim($sCustCd2) == '')
		{
			if (trim($sTgl) <> '' and trim($sTgl2) <> '')
			{
				$xSQL = $xSQL.("
					AND a.fd_refno >= '".trim($sTgl)."'
					AND a.fd_refno <= '".trim($sTgl2)."'
				");
			}
		}
		
		$xSQL = $xSQL.("
			ORDER BY   b.fs_nm_code, c.fs_nm_code
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid2($sTgl,$sTgl2,$sCustCd,$sCount,$sCustCd2,$sCount2,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
        $xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempstnk".$xUser.$xIP."%' )
					DROP TABLE #tempstnk".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_dept = LTRIM(RTRIM(a.fs_kd_dept)) + LTRIM(RTRIM(a.fs_count)),
					fs_nm_dept = ISNULL(b.fs_nm_code, ' '),
					fs_kd_cust = LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)),
					fs_nm_cust = ISNULL(c.fs_nm_code, ' '),
					fs_status = CASE
						WHEN ISNULL((
								SELECT	TOP 1 h.fd_refno
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') = '' THEN 'BELUM PROSES BPKB'
						WHEN
							 ISNULL((
								SELECT	TOP 1 h.fd_refno
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') <> ''
							AND 
							ISNULL((
								SELECT  TOP 1 i.fd_rcvdt2
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') = ''
						THEN 'PROSES BPKB BELUM SELESAI'
						WHEN
							 ISNULL((
								SELECT	TOP 1 h.fd_refno
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') <> ''
							AND 
							ISNULL((
								SELECT  TOP 1 i.fd_rcvdt2
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') <> ''
							AND ISNULL(d.fd_bpkb, '') = ''
						THEN 'BPKB JADI TP BELUM DISERAHKAN KE CUST'
						WHEN
							 ISNULL((
								SELECT	TOP 1 h.fd_refno
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') <> ''
							AND 
							ISNULL((
								SELECT  TOP 1 i.fd_rcvdt
								FROM    tx_trxbirojs h (NOLOCK)
								INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
										AND h.fs_kd_dept = i.fs_kd_dept
										AND h.fs_count = i.fs_count
										AND h.fs_kd_trx = i.fs_kd_trx
										AND h.fs_kd_strx = i.fs_kd_strx
										AND h.fs_refno = i.fs_refno
										AND i.fs_kd_comp = a.fs_kd_comp
										AND i.fs_kd_cussup = a.fs_kd_cussup
										AND i.fs_countcussup = a.fs_countcussup
										AND i.fs_chasis = a.fs_chasis
										AND i.fs_engine = a.fs_machine
										AND h.fs_kd_strx = '0100'
							), '') <> ''
							AND ISNULL(d.fd_bpkb, '') <> ''
						THEN 'BPKB JADI & SUDAH DISERAHKAN KE CUST'
					END,
					fd_serah_ke_agen = ISNULL((
							SELECT	TOP 1 CASE LEN(LTRIM(RTRIM(h.fd_refno))) WHEN 8 THEN CONVERT(VARCHAR(10), CONVERT(DATETIME, h.fd_refno, 105), 105) ELSE ' ' END
							FROM    tx_trxbirojs h (NOLOCK)
							INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
									AND h.fs_kd_dept = i.fs_kd_dept
									AND h.fs_count = i.fs_count
									AND h.fs_kd_trx = i.fs_kd_trx
									AND h.fs_kd_strx = i.fs_kd_strx
									AND h.fs_refno = i.fs_refno
									AND i.fs_kd_comp = a.fs_kd_comp
									AND i.fs_kd_cussup = a.fs_kd_cussup
									AND i.fs_countcussup = a.fs_countcussup
									AND i.fs_chasis = a.fs_chasis
									AND i.fs_engine = a.fs_machine
									AND h.fs_kd_strx = '0100'
						), ' '),
					fd_terima_dr_agen = ISNULL((
							SELECT  TOP 1 CASE LEN(LTRIM(RTRIM(i.fd_rcvdt2))) WHEN 8 THEN CONVERT(VARCHAR(10), CONVERT(DATETIME, i.fd_rcvdt2, 105), 105) ELSE ' ' END
							FROM    tx_trxbirojs h (NOLOCK)
							INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
									AND h.fs_kd_dept = i.fs_kd_dept
									AND h.fs_count = i.fs_count
									AND h.fs_kd_trx = i.fs_kd_trx
									AND h.fs_kd_strx = i.fs_kd_strx
									AND h.fs_refno = i.fs_refno
									AND i.fs_kd_comp = a.fs_kd_comp
									AND i.fs_kd_cussup = a.fs_kd_cussup
									AND i.fs_countcussup = a.fs_countcussup
									AND i.fs_chasis = a.fs_chasis
									AND i.fs_engine = a.fs_machine
									AND h.fs_kd_strx = '0100'
						), ' '),
					fs_bpkb = ISNULL(d.fs_bpkb, ' '),
					fd_serah_bpkb_ke_cust = CASE LEN(LTRIM(RTRIM(d.fd_bpkb))) WHEN 8 THEN CONVERT(VARCHAR(10), CONVERT(DATETIME, d.fd_bpkb, 105), 105) ELSE ' ' END,
					fs_nopol = ISNULL(d.fs_nopol, ' '),
					fn_bbn = ISNULL((
							SELECT  TOP 1 ISNULL(i.fn_bbn,0)
							FROM    tx_trxbirojs h (NOLOCK)
							INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
									AND h.fs_kd_dept = i.fs_kd_dept
									AND h.fs_count = i.fs_count
									AND h.fs_kd_trx = i.fs_kd_trx
									AND h.fs_kd_strx = i.fs_kd_strx
									AND h.fs_refno = i.fs_refno
									AND i.fs_kd_comp = a.fs_kd_comp
									AND i.fs_kd_cussup = a.fs_kd_cussup
									AND i.fs_countcussup = a.fs_countcussup
									AND i.fs_chasis = a.fs_chasis
									AND i.fs_engine = a.fs_machine
									AND h.fs_kd_strx = '0100'
						), 0), 
					fn_jasa = ISNULL((
							SELECT  TOP 1 ISNULL(i.fn_jasa, 0)
							FROM    tx_trxbirojs h (NOLOCK)
							INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
									AND h.fs_kd_dept = i.fs_kd_dept
									AND h.fs_count = i.fs_count
									AND h.fs_kd_trx = i.fs_kd_trx
									AND h.fs_kd_strx = i.fs_kd_strx
									AND h.fs_refno = i.fs_refno
									AND i.fs_kd_comp = a.fs_kd_comp
									AND i.fs_kd_cussup = a.fs_kd_cussup
									AND i.fs_countcussup = a.fs_countcussup
									AND i.fs_chasis = a.fs_chasis
									AND i.fs_engine = a.fs_machine
									AND h.fs_kd_strx = '0100'
						), 0),
					fs_rangka = a.fs_chasis, fs_mesin = a.fs_machine,
					fs_biro_jasa = ISNULL((
							SELECT  TOP 1 (	SELECT 	TOP 1 x.fs_nm_birojs
											FROM 	tm_masterbirojs x (NOLOCK)
											WHERE 	x.fs_kd_comp = h.fs_kd_comp
												AND x.fs_kd_birojs = h.fs_kd_agent
											)
							FROM    tx_trxbirojs h (NOLOCK)
							INNER JOIN  tx_trxbirojsd i (NOLOCK) ON h.fs_kd_comp = i.fs_kd_comp
									AND h.fs_kd_dept = i.fs_kd_dept
									AND h.fs_count = i.fs_count
									AND h.fs_kd_trx = i.fs_kd_trx
									AND h.fs_kd_strx = i.fs_kd_strx
									AND h.fs_refno = i.fs_refno
									AND i.fs_kd_comp = a.fs_kd_comp
									AND i.fs_kd_cussup = a.fs_kd_cussup
									AND i.fs_countcussup = a.fs_countcussup
									AND i.fs_chasis = a.fs_chasis
									AND i.fs_engine = a.fs_machine
									AND h.fs_kd_strx = '0100'
						), ' ')
			INTO	#tempstnk".$xUser.$xIP."
			FROM   	tm_posregsold a (NOLOCK)
			LEFT JOIN  tm_addr b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_dept = b.fs_code
				AND a.fs_count = b.fs_count
				AND b.fs_cdtyp = '03'
			LEFT JOIN  tm_addr c (NOLOCK) ON a.fs_kd_comp = c.fs_kd_comp
				AND a.fs_kd_cussup = c.fs_code
				AND a.fs_countcussup = c.fs_count
				AND c.fs_cdtyp = '02'
			INNER JOIN tm_icregister d (NOLOCK) ON a.fs_kd_comp = d.fs_kd_comp
				AND a.fs_kd_dept = d.fs_kd_deptinv
				AND a.fs_count = d.fs_countdeptinv
				AND a.fs_kd_trx = d.fs_kd_trxinv
				AND a.fs_refno = d.fs_refnoinv
				AND a.fs_chasis = d.fs_rangka
				AND a.fs_machine = d.fs_machine
			");
		
		if (trim($sCustCd) <> '' and trim($sCustCd2) == '')
		{
			$xSQL = $xSQL.("
				AND LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)) = '".trim($sCustCd).trim($sCount)."'
			");
		}
		if (trim($sCustCd) <> '' and trim($sCustCd2) <> '')
		{
			$xSQL = $xSQL.("
				AND LTRIM(RTRIM(a.fs_kd_cussup)) + LTRIM(RTRIM(a.fs_countcussup)) IN ('".trim($sCustCd).trim($sCount)."','".trim($sCustCd2).trim($sCount2)."')
			");
		}
		
		if (trim($sCustCd) == '' and trim($sCustCd2) == '')
		{
			if (trim($sTgl) <> '' and trim($sTgl2) <> '')
			{
				$xSQL = $xSQL.("
					AND a.fd_refno >= '".trim($sTgl)."'
					AND a.fd_refno <= '".trim($sTgl2)."'
				");
			}
		}
		
		$xSQL = $xSQL.("
			ORDER BY   b.fs_nm_code, c.fs_nm_code
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempstnk".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempstnk".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>