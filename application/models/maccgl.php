<?php

class MAccGL extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function accgl_header($sDept,$sAcnoA,$sAcnoB,$sDateA,$sDateB,$nStart)
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
			
			CREATE TABLE #temp".$xUser.$xIP."(
					ACNO		VARCHAR(50),
					ACNM		VARCHAR(100),
					DEPTNM		VARCHAR(50),
					fs_kd_dept	VARCHAR(10),
					fs_count	VARCHAR(10),
					fs_kd_trx	VARCHAR(10),
					fs_kd_strx	VARCHAR(10),
					fs_refno	VARCHAR(50),
					fd_refno	VARCHAR(10),
					fs_Descrp	VARCHAR(500),
					fs_acno		VARCHAR(50),
					DEBET		NUMERIC(35,0),
					CREDIT		NUMERIC(35,0),
					BALANCE		NUMERIC(35,0)
			)
			
			INSERT INTO #temp".$xUser.$xIP."
			EXEC STP_JURNALDAILYBK '".trim($this->session->userdata('gComp'))."', 
					'".trim($sDateA)."', '".trim($sDateB)."', '".trim($sAcnoA)."', '".trim($sAcnoB)."', 'BUKUBESAR', '".trim($sDept)."'");
					
		$xSQL =	$xSQL.("
			SELECT n = IDENTITY(INT, 1, 1), * into #temp2".$xUser.$xIP." from #temp".$xUser.$xIP."
			
			SELECT 	* FROM #temp2".$xUser.$xIP."
			WHERE	n BETWEEN @Start AND @Limit
			
			DROP TABLE #temp".$xUser.$xIP."
			DROP TABLE #temp2".$xUser.$xIP);
		
        $sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function accgl_header_all($sDept,$sAcnoA,$sAcnoB,$sDateA,$sDateB)
	{
		$xSQL = ("
			EXEC STP_JURNALDAILYBK '".trim($this->session->userdata('gComp'))."', 
				'".trim($sDateA)."', '".trim($sDateB)."', '".trim($sAcnoA)."', '".trim($sAcnoB)."', 'BUKUBESAR', '".trim($sDept)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function accgl_detail($sDept,$sCount,$sTrx,$ssTrx,$sRefno)
	{
		$lrefno = strlen(trim($sRefno));
		$lrefno = $lrefno - 16;
		
		$xSQL = ("
			SELECT	[fs_kd_acc] = ta.fs_acno, [fs_nm_acc] = ba.fs_nm_acno, [fs_note] = ta.fs_descrp,
					[fn_debet] = CASE WHEN ta.fs_dbcr = 'D' THEN ta.fn_ftrxamt ELSE 0 END,
					[fn_credit] = CASE WHEN ta.fs_dbcr = 'C' THEN ta.fn_ftrxamt ELSE 0 END
			FROM 	tx_ACTHeaderdt ta (NOLOCK), tm_acno ba (NOLOCK)
			WHERE 	ta.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND ta.fs_kd_dept = '".trim($sDept)."'
				AND ta.fs_count = '".trim($sCount)."'
				AND ta.fs_kd_trx = '".trim($sTrx)."'
				AND ta.fs_kd_strx = '".trim($ssTrx)."'
		");
		
		if (trim($sTrx) == 'JL' or trim($sTrx) == 'BL' or trim($sTrx) == 'JLS')
		{
			$xSQL = $xSQL.("
				AND ta.fs_refno LIKE '%".trim($sTrx)."/%".substr(trim($sRefno), $lrefno, 16)."'
			");
		}
		else
		{
			$xSQL = $xSQL.("
				AND	ta.fs_refno = '".trim($sRefno)."'
			");
		}
		
		$xSQL = $xSQL.("
				AND ta.fs_acno = ba.fs_kd_acno
			ORDER BY ta.fs_seqno
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}

?>