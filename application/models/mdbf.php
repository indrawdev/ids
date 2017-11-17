<?php

class MDbf extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	function getAPK()
	{
		$xSQL = ("
			SELECT * FROM tx_apk
		");

		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}

	function getAPKJ()
	{
		$xSQL = ("
			SELECT * FROM tx_apk
		");

		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}

	function getAPKK()
	{
		$xSQL = ("
			SELECT * FROM tx_apk
		");

		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}

	function getAPKP()
	{
		$xSQL = ("
			SELECT * FROM tx_apk
		");

		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}

	function getAPKS ()
	{
		$xSQL = ("
			SELECT * FROM tx_apk
		");

		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	} 

}