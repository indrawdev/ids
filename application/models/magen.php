<?php

class MAgen extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function cek_refno($sTrx,$ssTrx,$sRefno)
	{
		$xSQL = ("
			SELECT	TOP 1 fs_refno
			FROM 	tx_trxbirojs (NOLOCK)
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_trx = '".trim($sTrx)."'
				AND fs_kd_strx = '".trim($ssTrx)."'
				AND fs_refno = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid($sRefno,$sTrx,$ssTrx)
	{
		$xSQL = ("
			SELECT	a.fs_refnosi fs_refnojual, CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105) fd_refno,
					a.fs_kd_cussup fs_kd_cust,
					a.fs_countcussup fs_count,
					ISNULL(c.fs_nm_code, '') fs_nm_cust,
					a.fs_chasis fs_rangka, a.fs_engine fs_mesin,
					CASE LTRIM(RTRIM(d.fd_stnk)) WHEN '' THEN '01-01-3000' ELSE CONVERT(VARCHAR, CONVERT(DATETIME, d.fd_stnk, 105), 105) END fd_stnk, a.fs_stnk,
					CASE d.fd_stnk WHEN '' THEN 'Belum Diserahkan Ke CUST' ELSE 'Sudah Diserahkan Ke CUST' END fs_stnk_status,
					CASE LTRIM(RTRIM(d.fd_bpkb)) WHEN '' THEN '01-01-3000' ELSE CONVERT(VARCHAR, CONVERT(DATETIME, d.fd_bpkb, 105), 105) END fd_bpkb, a.fs_bpkb,
					CASE d.fd_bpkb WHEN '' THEN 'Belum Diserahkan Ke CUST' ELSE 'Sudah Diserahkan Ke CUST' END fs_bpkb_status,
					a.fn_bbn, a.fn_jasa fn_servis, a.fs_seqno,
					ISNULL(b.fs_nm_pay, '') fs_note,
					a.fs_nm_stnk_qq, a.fs_almt_stnk_qq,
					a.fs_nm_bpkb_qq, a.fs_almt_bpkb_qq
			FROM	tx_trxbirojsd a (NOLOCK)
			INNER JOIN tm_posregsold b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_refnoSI = b.fs_refno
				AND a.fs_chasis = b.fs_chasis
				AND	a.fs_engine = b.fs_machine
			INNER JOIN tm_addr c (NOLOCK) ON c.fs_kd_comp = a.fs_kd_comp
				AND c.fs_cdtyp = '02'
				AND c.fs_code = a.fs_kd_cussup
				AND	c.fs_count = a.fs_countcussup
			INNER JOIN tm_icregister d (NOLOCK) ON a.fs_kd_comp = d.fs_kd_comp
				AND a.fs_chasis = d.fs_rangka
				AND	a.fs_engine = d.fs_machine
			WHERE a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				-- AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				-- AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fs_kd_strx = '".trim($ssTrx)."'
				AND a.fs_refno = '".trim($sRefno)."'
			ORDER BY a.fs_seqno
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid2_all($sCari)
	{
		$xSQL = ("
			SELECT	CONVERT(BIT, 0) fb_cek, ISNULL(a.fs_refno, '') fs_refnojual,
					CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105) fd_refno,
					a.fs_kd_cussup fs_kd_cust,
					a.fs_countcussup fs_count,
					ISNULL(d.fs_nm_code, '') fs_nm_cust,
					a.fs_chasis fs_rangka, a.fs_machine fs_mesin,
					'01-01-3000' fd_stnk, ' ' fs_stnk, ' ' fs_stnk_status,
					'01-01-3000' fd_bpkb, ' ' fs_bpkb, ' ' fs_bpkb_status,
					'0' fn_bbn, '0' fn_servis, ISNULL(a.fs_nm_pay, '') fs_note,
					' ' AS fs_seqno
			FROM	tm_posregsold a (NOLOCK)
			INNER JOIN tx_posheader b (NOLOCK) ON a.fs_kd_dept = b.fs_kd_dept
				AND a.fs_count = b.fs_count
				AND a.fs_kd_trx = b.fs_kd_trx
				AND a.fs_refno = b.fs_refno
			INNER JOIN tm_icregister c (NOLOCK) ON a.fs_chasis = c.fs_rangka
				AND a.fs_machine = c.fs_machine
				AND a.fs_refno = c.fs_refnoINV
				AND a.fs_kd_dept = c.fs_kd_DeptINV
				AND a.fs_count = c.fs_countDeptINV
				AND a.fs_kd_trx = c.fs_kd_trxINV
			INNER JOIN tm_addr d (NOLOCK) ON d.fs_kd_comp = a.fs_kd_comp
				AND d.fs_cdtyp = '02'
				AND d.fs_code = a.fs_kd_cussup
				AND d.fs_count = a.fs_countcussup
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND b.fs_kd_comp = b.fs_kd_comp
				AND b.fs_kd_salesmtd <> '01'
				AND LEFT(d.fs_code, 2) = '".trim($this->session->userdata('gWilayah'))."'
				AND a.fs_chasis NOT IN (
					SELECT	x.fs_chasis
					FROM	tx_trxbirojsd x (NOLOCK)
					WHERE	x.fs_kd_strx = '0100' )
		");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (d.fs_nm_code LIKE '%".trim($sCari)."%'
					OR a.fs_chasis LIKE '%".trim($sCari)."%'
					OR a.fs_machine LIKE '%".trim($sCari)."%'
					OR a.fs_nm_pay LIKE  '%".trim($sCari)."%')
			");
		}
		
		$xSQL =	$xSQL.("
			ORDER BY d.fs_nm_code
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function grid2($sCari,$nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
        $xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempdet".$xUser.$xIP."%' )
					DROP TABLE #tempdet".$xUser.$xIP."
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempdet2".$xUser.$xIP."%' )
					DROP TABLE #tempdet2".$xUser.$xIP."
			
			SELECT	CONVERT(BIT, 0) fb_cek, ISNULL(a.fs_refno, '') fs_refnojual,
					CONVERT(VARCHAR(10), CONVERT(DATETIME, a.fd_refno, 105), 105) fd_refno,
					a.fs_kd_cussup fs_kd_cust,
					a.fs_countcussup fs_count,
					ISNULL(d.fs_nm_code, '') fs_nm_cust,
					a.fs_chasis fs_rangka, a.fs_machine fs_mesin,
					'01-01-3000' fd_stnk, ' ' fs_stnk, ' ' fs_stnk_status,
					'01-01-3000' fd_bpkb, ' ' fs_bpkb, ' ' fs_bpkb_status,
					'0' fn_bbn, '0' fn_servis, ISNULL(a.fs_nm_pay, '') fs_note,
					' ' AS fs_seqno
			INTO	#tempdet".$xUser.$xIP."
			FROM	tm_posregsold a (NOLOCK)
			INNER JOIN tx_posheader b (NOLOCK) ON a.fs_kd_dept = b.fs_kd_dept
				AND a.fs_count = b.fs_count
				AND a.fs_kd_trx = b.fs_kd_trx
				AND a.fs_refno = b.fs_refno
			INNER JOIN tm_icregister c (NOLOCK) ON a.fs_chasis = c.fs_rangka
				AND a.fs_machine = c.fs_machine
				AND a.fs_refno = c.fs_refnoINV
				AND a.fs_kd_dept = c.fs_kd_DeptINV
				AND a.fs_count = c.fs_countDeptINV
				AND a.fs_kd_trx = c.fs_kd_trxINV
			INNER JOIN tm_addr d (NOLOCK) ON d.fs_kd_comp = a.fs_kd_comp
				AND d.fs_cdtyp = '02'
				AND d.fs_code = a.fs_kd_cussup
				AND d.fs_count = a.fs_countcussup
			WHERE a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND b.fs_kd_comp = b.fs_kd_comp
				AND b.fs_kd_salesmtd <> '01'
				AND LEFT(d.fs_code, 2) = '".trim($this->session->userdata('gWilayah'))."'
				AND a.fs_chasis NOT IN (
					SELECT	x.fs_chasis
					FROM	tx_trxbirojsd x (NOLOCK)
					WHERE	x.fs_kd_strx = '0100' )
		");
		
		if (trim($sCari) <> '')
		{
			$xSQL = $xSQL.("
				AND (d.fs_nm_code LIKE '%".trim($sCari)."%'
					OR a.fs_chasis LIKE '%".trim($sCari)."%'
					OR a.fs_machine LIKE '%".trim($sCari)."%'
					OR a.fs_nm_pay LIKE  '%".trim($sCari)."%')
			");
		}
		
		$xSQL =	$xSQL.("
			ORDER BY d.fs_nm_code
		");
		
		$xSQL =	$xSQL.("
			SELECT	n = IDENTITY(INT, 1, 1), *
			INTO	#tempdet2".$xUser.$xIP."
			FROM	#tempdet".$xUser.$xIP."
			
			SELECT 	* FROM #tempdet2".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempdet".$xUser.$xIP."
			DROP TABLE #tempdet2".$xUser.$xIP
		);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function hapus_isi($sTrx,$ssTrx,$sRefno)
	{
		$xSQL = ("
			UPDATE	tm_icregister SET
					fs_stnk = '', fd_stnk = '',
					fs_bpkb = '', fd_bpkb = ''
			FROM	tm_icregister a (NOLOCK)
			INNER JOIN tx_trxbirojsd b (NOLOCK) ON a.fs_rangka = b.fs_chasis
				AND	a.fs_machine = b.fs_engine
			WHERE	b.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	b.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND	b.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND	b.fs_kd_trx = '".trim($sTrx)."'
				AND	b.fs_kd_strx = '".trim($ssTrx)."'
				AND	b.fs_refno = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function update_isi($sTrx,$ssTrx,$sRefno)
	{
		$xSQL = ("
			UPDATE	tx_trxbirojsd SET
					fs_kd_dept = a.fs_kd_dept,
					fs_count = a.fs_count
			FROM	tx_trxbirojs a (NOLOCK)
			INNER JOIN tx_trxbirojsd b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_refno = b.fs_refno
			WHERE	a.fs_refno = '".trim($sRefno)."'
			
			UPDATE	tm_icregister SET
					fs_stnk = b.fs_stnk, fd_stnk = b.fd_rcvdt,
					fs_bpkb = b.fs_bpkb, fd_bpkb = b.fd_rcvdt2
			FROM	tm_icregister a (NOLOCK)
			INNER JOIN tx_trxbirojsd b (NOLOCK) ON a.fs_rangka = b.fs_chasis
				AND	a.fs_machine = b.fs_engine
			WHERE	b.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	b.fs_kd_trx = '".trim($sTrx)."'
				AND	b.fs_kd_strx = '".trim($ssTrx)."'
				AND	b.fs_refno = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function agen_service($sRefno)
	{
		$xSQL = ("
			SELECT  aa.fs_refno, cc.fs_nm_birojs, cc.fs_alamat,
					dd.fs_nm_strx, fs_stnk,
					CASE LTRIM(RTRIM(fs_stnk)) WHEN '' THEN fs_bpkb ELSE fs_stnk END fs_no_faktur,
					[fd_refno] = CONVERT(VARCHAR(10), CONVERT(DATETIME, bb.fd_refno, 105), 105),
					CASE LTRIM(RTRIM(bb.fd_rcvdt)) WHEN '' THEN '' ELSE CONVERT(VARCHAR, CONVERT(DATETIME, bb.fd_rcvdt, 105), 105) END fd_rcvdt
			FROM 	tx_trxbirojs aa (NOLOCK)
			INNER JOIN tx_trxbirojsd bb ON bb.fs_refno = aa.fs_refno
			INNER JOIN tm_masterbirojs cc ON cc.fs_kd_birojs = aa.fs_kd_agent
			INNER JOIN tm_strx dd ON LTRIM(RTRIM(dd.fs_kd_trx)) + LTRIM(RTRIM(dd.fs_kd_strx)) = LTRIM(RTRIM(aa.fs_kd_trx)) + LTRIM(RTRIM(aa.fs_kd_strx))
			WHERE 	bb.fs_refno = '".trim($sRefno)."'
				AND fb_delete = 0
			");
			
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}	
}
?>