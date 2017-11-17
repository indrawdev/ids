<?php

class MUser extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function cek_kode($sKode)
	{
		$xSQL = ("
			SELECT 	fs_kd_user
            FROM 	tm_user (NOLOCK)
            WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_user = '".trim($sKode)."'
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_level($sLevel)
	{
		$xSQL = ("
			SELECT 	DISTINCT fs_level
            FROM 	tm_parlevel (NOLOCK)
            WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_level = '".trim($sLevel)."'
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function load_menu($sLevel)
	{
		$xSQL = ("
			SELECT	a.fs_kd_parent, a.fs_kd_child,
					a.fs_nm_menu, a.fs_nm_form, a.fs_nm_formweb,
					CASE ISNULL((SELECT h.fs_kd_parent
						FROM	tm_parlevel h (NOLOCK)
						WHERE	h.fs_kd_comp = a.fs_kd_comp
							AND	h.fs_kd_parent = a.fs_kd_parent
							AND	h.fs_kd_child = a.fs_kd_child
							AND	h.fs_index = '1'
							AND	h.fs_level = '".trim($sLevel)."'
					), '') WHEN '' THEN 'false' ELSE 'true' END fb_tambah
			FROM	tg_menu a (NOLOCK)
			ORDER BY a.fs_kd_parent, a.fs_kd_child
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>