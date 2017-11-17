<?php

class MCrud extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	function cek_kode($sKdCrud)
	{
		$xSQL = ("
			SELECT 	fs_kd_crud
            FROM 	tm_crud (NOLOCK)
            WHERE	fs_kd_crud = '".trim($sKdCrud)."'
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}

	function crud_all() 
	{
		$xSQL = ("
				SELECT * FROM tm_crud ORDER BY fs_kd_crud
			");
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}

	function city_all() 
	{
		$xSQL = ("
				SELECT fn_city_id, fs_city_name FROM tm_city ORDER BY fn_city_id
			");
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}

	function city($nStart) 
	{
		$xUser = trim($this->session->userdata('gUser'));
		$xIP = str_replace(".","",trim($this->session->userdata('ip_address')));
		$xSQL = ("
			DECLARE @Start	NUMERIC(35,0),
					@Limit	NUMERIC(35,0)
			
			SET	@Start 	= ".$nStart." + 1
			SET @Limit	= @Start + 24
			
			IF EXISTS (	SELECT NAME FROM tempdb..sysobjects WHERE NAME LIKE '#temp".$xUser.$xIP."%' )
					DROP TABLE #temp".$xUser.$xIP."
			
			SELECT	n = IDENTITY(INT, 1, 1), fn_city_id = LTRIM(RTRIM(fn_city_id)),
					UPPER(LTRIM(RTRIM(fs_city_name))) fs_city_name
			INTO	#temp".$xUser.$xIP."
			FROM	tm_city (nolock)
			ORDER BY fn_city_id
			");
		
		$xSQL =	$xSQL.("
			SELECT 	* FROM #temp".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temp".$xUser.$xIP);
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
    
	function griddetil_all()
	{
		$xSQL = ("
			SELECT	a.fs_kd_crud, a.fs_fname, a.fs_lname,
					a.fs_address, ISNULL(b.fs_city_name, '') fs_city_name,
					a.fb_active, fs_status = CASE a.fb_active WHEN '1' THEN 'ACTIVE' ELSE 'NON ACTIVE' END
            FROM 	tm_crud a (NOLOCK)
			LEFT JOIN tm_city b (NOLOCK) ON a.fn_city_id = b.fn_city_id
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}