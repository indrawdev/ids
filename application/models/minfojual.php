<?php

class MInfoJual extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function CetakDailySales($TglAwal,$TglAkhir,$sCekWH,$sCustomerCode,$sChassisNumber,$sPilih)
	{
		if ( $sPilih == 0 ) {
		
		$xSQL = ("
			IF EXISTS(SELECT name FROM sysobjects WHERE name = N't_tempdt' AND type = 'U') DROP TABLE t_tempdt
			
			CREATE TABLE  t_tempdt(
				deptcd		VARCHAR(10)NOT NULL DEFAULT (' '),
				deptcount	VARCHAR(15)NOT NULL DEFAULT (' '),
				deptnm		VARCHAR(50)NOT NULL DEFAULT (' '),
				refno		VARCHAR(25)NOT NULL DEFAULT (' '),
				fd_tgl	  	VARCHAR(10)NOT NULL DEFAULT (' '),
				refdt	  	VARCHAR(10)NOT NULL DEFAULT (' '),
				custcd		VARCHAR(10)NOT NULL DEFAULT (' '),
				custcount	VARCHAR(10)NOT NULL DEFAULT (' '),
				custnm	 	VARCHAR(100)NOT NULL DEFAULT (' '),
				custaddr	VARCHAR(200)NOT NULL DEFAULT (' '),
				product		VARCHAR(25)NOT NULL DEFAULT (' '),
				colour		VARCHAR(15)NOT NULL DEFAULT (' '),
				chasis		VARCHAR(20)NOT NULL DEFAULT (' '),
				machine		VARCHAR(20)NOT NULL DEFAULT (' '),
				unit_price	NUMERIC(35,13),
				discount	NUMERIC(35,13),
				dp_amount	NUMERIC(35,13),
				piutang		NUMERIC(35,13),
				sales		VARCHAR(50)NOT NULL DEFAULT (' '),
				keterangan	VARCHAR(50)NOT NULL DEFAULT (' '),
				harga_ksi	NUMERIC(35,13),
				margin		NUMERIC(35,13),
				bbn			NUMERIC(35,13),
				dpp			NUMERIC(35,13),
				ppn			NUMERIC(35,13),
				rek_khusus	NUMERIC(35,13),
				hpp			NUMERIC(35,13),
				subsidi		NUMERIC(35,13),
				angsuran	VARCHAR(20)NOT NULL DEFAULT (' '),
				nmterm		VARCHAR(20)NOT NULL DEFAULT (' ')
			)
					
			INSERT INTO t_tempdt
			
			EXEC Stp_Keuangan 'PENJUALAN',' AND a.fd_refno >= ''". $TglAwal ."'' AND a.fd_refno <= ''". $TglAkhir ."'' 
		");
		
		if ($sCekWH == '0')
		{
			$xSQL = $xSQL.("
				 AND c.fs_kd_wh LIKE ''".trim($this->session->userdata('gGudang'))."%'' '
			");
			
			// $xSQL = $xSQL.("
				 // AND b.fs_kd_dept LIKE ''".trim($this->session->userdata('gDept'))."%'' '
			// ");
		}
		else
		{
			$xSQL = $xSQL.("
				'
			");
		}
		
		$xSQL = $xSQL.("
			SELECT * FROM t_tempdt
		");
		}
		
		if ( $sPilih == 1 ) {
		
		$xSQL = ("
			IF EXISTS(SELECT name FROM sysobjects WHERE name = N't_tempdt' AND type = 'U') DROP TABLE t_tempdt
			
			CREATE TABLE  t_tempdt(
				deptcd		VARCHAR(10)NOT NULL DEFAULT (' '),
				deptcount	VARCHAR(15)NOT NULL DEFAULT (' '),
				deptnm		VARCHAR(50)NOT NULL DEFAULT (' '),
				refno		VARCHAR(25)NOT NULL DEFAULT (' '),
				fd_tgl	  	VARCHAR(10)NOT NULL DEFAULT (' '),
				refdt	  	VARCHAR(10)NOT NULL DEFAULT (' '),
				custcd		VARCHAR(10)NOT NULL DEFAULT (' '),
				custcount	VARCHAR(10)NOT NULL DEFAULT (' '),
				custnm	 	VARCHAR(100)NOT NULL DEFAULT (' '),
				custaddr	VARCHAR(200)NOT NULL DEFAULT (' '),
				product		VARCHAR(25)NOT NULL DEFAULT (' '),
				colour		VARCHAR(15)NOT NULL DEFAULT (' '),
				chasis		VARCHAR(20)NOT NULL DEFAULT (' '),
				machine		VARCHAR(20)NOT NULL DEFAULT (' '),
				unit_price	NUMERIC(35,13),
				discount	NUMERIC(35,13),
				dp_amount	NUMERIC(35,13),
				piutang		NUMERIC(35,13),
				sales		VARCHAR(50)NOT NULL DEFAULT (' '),
				keterangan	VARCHAR(50)NOT NULL DEFAULT (' '),
				harga_ksi	NUMERIC(35,13),
				margin		NUMERIC(35,13),
				bbn			NUMERIC(35,13),
				dpp			NUMERIC(35,13),
				ppn			NUMERIC(35,13),
				rek_khusus	NUMERIC(35,13),
				hpp			NUMERIC(35,13),
				subsidi		NUMERIC(35,13),
				angsuran	VARCHAR(20)NOT NULL DEFAULT (' '),
				nmterm		VARCHAR(20)NOT NULL DEFAULT (' ')
			)
					
			INSERT INTO t_tempdt
			
			EXEC Stp_Keuangan 'PENJUALAN',' AND ltrim(rtrim(a.fs_kd_CusSup)) + ltrim(rtrim(a.fs_CountCusSup))  = ''". $sCustomerCode ."'''
			
			SELECT * FROM t_tempdt
			");
		}
		
		if ( $sPilih == 2 ) {
		
		$xSQL = ("
			IF EXISTS(SELECT name FROM sysobjects WHERE name = N't_tempdt' AND type = 'U') DROP TABLE t_tempdt
			
			CREATE TABLE  t_tempdt(
				deptcd		VARCHAR(10)NOT NULL DEFAULT (' '),
				deptcount	VARCHAR(15)NOT NULL DEFAULT (' '),
				deptnm		VARCHAR(50)NOT NULL DEFAULT (' '),
				refno		VARCHAR(25)NOT NULL DEFAULT (' '),
				fd_tgl	  	VARCHAR(10)NOT NULL DEFAULT (' '),
				refdt	  	VARCHAR(10)NOT NULL DEFAULT (' '),
				custcd		VARCHAR(10)NOT NULL DEFAULT (' '),
				custcount	VARCHAR(10)NOT NULL DEFAULT (' '),
				custnm	 	VARCHAR(100)NOT NULL DEFAULT (' '),
				custaddr	VARCHAR(200)NOT NULL DEFAULT (' '),
				product		VARCHAR(25)NOT NULL DEFAULT (' '),
				colour		VARCHAR(15)NOT NULL DEFAULT (' '),
				chasis		VARCHAR(20)NOT NULL DEFAULT (' '),
				machine		VARCHAR(20)NOT NULL DEFAULT (' '),
				unit_price	NUMERIC(35,13),
				discount	NUMERIC(35,13),
				dp_amount	NUMERIC(35,13),
				piutang		NUMERIC(35,13),
				sales		VARCHAR(50)NOT NULL DEFAULT (' '),
				keterangan	VARCHAR(50)NOT NULL DEFAULT (' '),
				harga_ksi	NUMERIC(35,13),
				margin		NUMERIC(35,13),
				bbn			NUMERIC(35,13),
				dpp			NUMERIC(35,13),
				ppn			NUMERIC(35,13),
				rek_khusus	NUMERIC(35,13),
				hpp			NUMERIC(35,13),
				subsidi		NUMERIC(35,13),
				angsuran	VARCHAR(20)NOT NULL DEFAULT (' '),
				nmterm		VARCHAR(20)NOT NULL DEFAULT (' ')
			)
					
			INSERT INTO t_tempdt
			
			EXEC Stp_Keuangan 'PENJUALAN',' AND c.fs_rangka LIKE ''%". $sChassisNumber ."%'''
			
			SELECT * FROM t_tempdt
			
			");
		}
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function CetakDailySalesBySales($TglAwal,$TglAkhir,$sSalesCode,$sSalesCode2)
	{
		$xSQL = ("
		IF EXISTS(SELECT name FROM sysobjects WHERE name = N't_tempdt' AND type = 'U') DROP TABLE t_tempdt
		
		CREATE TABLE  t_tempdt(
				deptcd		VARCHAR(10)NOT NULL DEFAULT (' '),
				deptcount	VARCHAR(15)NOT NULL DEFAULT (' '),
				deptnm		VARCHAR(50)NOT NULL DEFAULT (' '),
				refno		VARCHAR(25)NOT NULL DEFAULT (' '),
				refdt	  	VARCHAR(10)NOT NULL DEFAULT (' '),
				custcd		VARCHAR(10)NOT NULL DEFAULT (' '),
				custcount	VARCHAR(10)NOT NULL DEFAULT (' '),
				custnm	 	VARCHAR(100)NOT NULL DEFAULT (' '),
				custaddr	VARCHAR(200)NOT NULL DEFAULT (' '),
				product		VARCHAR(25)NOT NULL DEFAULT (' '),
				colour		VARCHAR(15)NOT NULL DEFAULT (' '),
				chasis		VARCHAR(20)NOT NULL DEFAULT (' '),
				machine		VARCHAR(20)NOT NULL DEFAULT (' '),
				unit_price	NUMERIC(35,13),
				discount	NUMERIC(35,13),
				dp_amount	NUMERIC(35,13),
				piutang		NUMERIC(35,13),
				sales		VARCHAR(50)NOT NULL DEFAULT (' '),
				keterangan	VARCHAR(50)NOT NULL DEFAULT (' '),
				hpp			NUMERIC(35,13),
				subsidi		NUMERIC(35,13),
				angsuran	VARCHAR(20)NOT NULL DEFAULT (' '),
				nmterm		VARCHAR(20)NOT NULL DEFAULT (' '))
				
		INSERT INTO t_tempdt
		");
		
		if (trim($sSalesCode) == '' and trim($sSalesCode2) == '')
		{
			$xSQL = $xSQL.("EXEC Stp_Keuangan 'PJLPERSLS',' AND (a.fd_refno >= ''".trim($TglAwal)."'' AND a.fd_refno <= ''".trim($TglAkhir)."'')'
						");
		}
		else if (trim($sSalesCode) <> '' and trim($sSalesCode2) == '')
		{
			$xSQL = $xSQL.("EXEC Stp_Keuangan 'PJLPERSLS',' AND (ltrim(rtrim(a.fs_kd_salesman)) = ''".trim($sSalesCode)."'' OR  ltrim(rtrim(a.fs_kd_salesman)) = ''".trim($sSalesCode)."'')
						AND a.fd_refno >= ''".trim($TglAwal)."'' AND a.fd_refno <= ''".trim($TglAkhir)."'''
						");
		}
		else if (trim($sSalesCode) == '' and trim($sSalesCode2) <> '')
		{
			$xSQL = $xSQL.("EXEC Stp_Keuangan 'PJLPERSLS',' AND (ltrim(rtrim(a.fs_kd_salesman)) = ''".trim($sSalesCode2)."'' OR  ltrim(rtrim(a.fs_kd_salesman)) = ''".trim($sSalesCode2)."'')
						AND a.fd_refno >= ''".trim($TglAwal)."'' AND a.fd_refno <= ''".trim($TglAkhir)."'''
						");
		}
		else if (trim($sSalesCode) <> '' and trim($sSalesCode2) <> '')
		{
			$xSQL = $xSQL.("EXEC Stp_Keuangan 'PJLPERSLS',' AND (ltrim(rtrim(a.fs_kd_salesman)) = ''".trim($sSalesCode)."'' OR  ltrim(rtrim(a.fs_kd_salesman)) = ''".trim($sSalesCode2)."'')
						AND a.fd_refno >= ''".trim($TglAwal)."'' AND a.fd_refno <= ''".trim($TglAkhir)."'''
						");
		}
		$xSQL = $xSQL.("
			SELECT * FROM t_tempdt
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function CetakDailySalesRekap($TglAwal,$TglAkhir)
	{
		$xSQL = ("
			IF EXISTS(SELECT name FROM sysobjects WHERE name = N't_tempdt' AND type = 'U') DROP TABLE t_tempdt
			
			CREATE TABLE  t_tempdt(
				deptcd		VARCHAR(10)NOT NULL DEFAULT (' '),
				deptcount	VARCHAR(15)NOT NULL DEFAULT (' '),
				deptnm		VARCHAR(50)NOT NULL DEFAULT (' '),
				refno		VARCHAR(25)NOT NULL DEFAULT (' '),
				fd_tgl	  	VARCHAR(10)NOT NULL DEFAULT (' '),
				refdt	  	VARCHAR(10)NOT NULL DEFAULT (' '),
				custcd		VARCHAR(10)NOT NULL DEFAULT (' '),
				custcount	VARCHAR(10)NOT NULL DEFAULT (' '),
				custnm	 	VARCHAR(100)NOT NULL DEFAULT (' '),
				custaddr	VARCHAR(200)NOT NULL DEFAULT (' '),
				product		VARCHAR(50)NOT NULL DEFAULT (' '),
				colour		VARCHAR(50)NOT NULL DEFAULT (' '),
				chasis		VARCHAR(20)NOT NULL DEFAULT (' '),
				machine		VARCHAR(20)NOT NULL DEFAULT (' '),
				unit_price	NUMERIC(35,13),
				discount	NUMERIC(35,13),
				dp_amount	NUMERIC(35,13),
				piutang		NUMERIC(35,13),
				sales		VARCHAR(50)NOT NULL DEFAULT (' '),
				keterangan	VARCHAR(100)NOT NULL DEFAULT (' '),
				harga_ksi	NUMERIC(35,13),
				margin		NUMERIC(35,13),
				bbn			NUMERIC(35,13),
				dpp			NUMERIC(35,13),
				ppn			NUMERIC(35,13),
				rek_khusus	NUMERIC(35,13),
				hpp			NUMERIC(35,13),
				subsidi		NUMERIC(35,13),
				angsuran	VARCHAR(20)NOT NULL DEFAULT (' '),
				nmterm		VARCHAR(20)NOT NULL DEFAULT (' '))
				
			INSERT INTO t_tempdt
			
			EXEC Stp_Keuangan 'PENJUALAN',' AND a.fd_refno >= ''". $TglAwal ."'' AND a.fd_refno <= ''". $TglAkhir ."'''
			
			SELECT	deptnm,product,colour,count(product+colour) AS qty FROM t_tempdt
			--WHERE	deptcd = '".trim($this->session->userdata('gDept'))."'
				--AND	deptcount = '".trim($this->session->userdata('gCount'))."'
			GROUP BY deptnm,product,colour
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>