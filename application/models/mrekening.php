<?php

class MRekening extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function cek_kode($sKode)
	{
		$xSQL = ("
			SELECT 	fs_kd_acno
            FROM 	tm_acno (NOLOCK)
            WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_acno = '".trim($sKode)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_kodegrup($sKode)
	{
		$xSQL = ("
			SELECT 	fs_kd_group
            FROM 	tm_grpacno (NOLOCK)
            WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_group = '".trim($sKode)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function griddetil_all()
	{
		$xSQL = ("
			SELECT	fs_kd_acno, fs_nm_acno, fb_active
            FROM 	tm_acno (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	LTRIM(RTRIM(fs_kd_acno)) <> ''
			ORDER BY fs_kd_acno, fs_nm_acno
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
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempacno".$xUser.$xIP."%' )
					DROP TABLE #tempacno".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fs_kd_acno, fs_nm_acno, fb_active
			INTO	#tempacno".$xUser.$xIP."
            FROM 	tm_acno (NOLOCK)
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	LTRIM(RTRIM(fs_kd_acno)) <> ''
			ORDER BY fs_kd_acno, fs_nm_acno
		");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempacno".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempacno".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function griddetil2_all()
	{
		$xSQL = ("
			SELECT	a.fs_kd_group, a.fs_nm_group, a.fs_kd_acno, ISNULL(b.fs_nm_acno, '') fs_nm_acno
            FROM 	tm_grpacno a (NOLOCK)
			LEFT JOIN tm_acno b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_acno = b.fs_kd_acno
            WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	LTRIM(RTRIM(a.fs_kd_group)) <> ''
			ORDER BY a.fs_kd_group, a.fs_nm_group, a.fs_kd_acno, b.fs_nm_acno
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function griddetil2($nStart)
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#tempkelacno".$xUser.$xIP."%' )
					DROP TABLE #tempkelacno".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), a.fs_kd_group, a.fs_nm_group, a.fs_kd_acno, ISNULL(b.fs_nm_acno, '') fs_nm_acno
			INTO	#tempkelacno".$xUser.$xIP."
            FROM 	tm_grpacno a (NOLOCK)
			LEFT JOIN tm_acno b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_acno = b.fs_kd_acno
            WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	LTRIM(RTRIM(a.fs_kd_group)) <> ''
			ORDER BY a.fs_kd_group, a.fs_nm_group, a.fs_kd_acno, b.fs_nm_acno
		");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #tempkelacno".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #tempkelacno".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>