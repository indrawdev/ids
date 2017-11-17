<?php

class MCBReceive extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function cek_kode($sTrx,$sStrx,$sRefno)
	{
		$xSQL = ("
			SELECT	a.fs_refno, fb_edit = CASE b.fb_close WHEN 1 THEN 0 ELSE 1 END
			FROM 	tx_cbheader a (NOLOCK)
			INNER JOIN tx_actheader b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND	a.fs_kd_dept = b.fs_kd_dept
				AND	a.fs_count = b.fs_count
				AND	a.fs_kd_trx = b.fs_kd_trx
				AND	a.fs_kd_strx = b.fs_kd_strx
				AND	a.fs_refno = b.fs_refno
			WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fs_kd_strx = '".trim($sStrx)."'
				AND a.fs_refno = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_kodecash($sKode)
	{
		$xSQL = ("
			SELECT 	TOP 1 fs_refno
            FROM 	tx_cbdetail (NOLOCK)
            WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_refnot = '".trim($sKode)."'
			ORDER BY fs_refno DESC
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function hitungsisa($sRefno)
	{
		$xSQL = ("
			SELECT	fs_refno,
					fn_total = ROUND(CASE WHEN fn_rmnamt <= 0 THEN 0 ELSE fn_rmnamt END, -1),
					fn_sisa = ROUND(fn_rmnamt, -1), fs_docno,
					fs_ket = CASE WHEN ROUND(fn_rmnamt, -1) = 0 THEN '----LUNAS-----' WHEN ROUND(fn_rmnamt, -1) <0 THEN 'LEBIH BAYAR' ELSE 'KURANG BAYAR' END
			FROM	tx_posheader (NOLOCK)
			WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND	fs_kd_trx = 'JL'
				AND	fn_rmnamt > 0
				AND	fs_refno = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function update_dp($sTrx,$ssTrx,$sRefno)
	{
		$xSQL = ("
			UPDATE	tx_cbdetail SET fs_trsta = a.fs_trsta
            FROM 	tx_cbheader a (NOLOCK), tx_cbdetail b (NOLOCK)
            WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fs_kd_strx = '".trim($ssTrx)."'
				AND a.fs_refno = '".trim($sRefno)."'
				AND a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_dept = b.fs_kd_dept
				AND a.fs_count = b.fs_count
				AND a.fs_kd_trx = b.fs_kd_trx
				AND a.fs_kd_strx = b.fs_kd_strx
				AND a.fs_refno = b.fs_refno
            
			DELETE	tx_actheader
			FROM 	tx_cbheader a (NOLOCK), tx_actheader b (NOLOCK)
            WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fs_kd_strx = '".trim($ssTrx)."'
				AND a.fs_refno = '".trim($sRefno)."'
				AND b.fs_kd_comp = a.fs_kd_comp
				AND b.fs_kd_dept = a.fs_kd_dept
				AND b.fs_count = a.fs_count
				AND b.fs_kd_trx = a.fs_kd_trx
				AND b.fs_kd_strx = a.fs_kd_strx
				AND b.fs_refno = a.fs_refno
        
			INSERT INTO tx_actheader(fs_kd_comp, fs_kd_dept, fs_count, fs_kd_trx, fs_kd_strx,
					fs_refno, fd_refno, fs_doc, fd_doc, fd_periode,
					fs_kd_acno, fn_grsamt, fs_descrp, fb_draft, fs_trsta,
					fb_delete, fs_usrcrt, fd_usrcrt, fs_upddt, fd_upddt,
					fn_ftrxamt, fn_ltrxamt, fs_modul, fs_kd_cussup, fs_countcussup)
            SELECT	a.fs_kd_comp, a.fs_kd_dept, a.fs_count, a.fs_kd_trx, a.fs_kd_strx,
					a.fs_refno, a.fd_refno, a.fs_docno, a.fd_docno, a.fd_periode,
					a.fs_acno, a.fn_amount, a.fs_note, a.fb_draft, a.fs_trsta,
					a.fb_delete, a.fs_usrcrt, a.fd_usrcrt, a.fs_upddt, a.fd_upddt,
					a.fn_amount, a.fn_amount, 'CB', a.fs_kd_cussup, a.fs_countcussup
            FROM	tx_cbheader a (NOLOCK)
            WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fs_kd_strx = '".trim($ssTrx)."'
				AND a.fs_refno = '".trim($sRefno)."'
		
			EXEC STP_ACTHEADER '".trim($this->session->userdata('gComp'))."','".trim($this->session->userdata('gDept'))."',
							'".trim($this->session->userdata('gCount'))."','".trim($sTrx)."','".trim($ssTrx)."','".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function update_dp2($sTrx,$ssTrx,$sTrsta,$sPeriode,$sRefno)
	{
		$xSQL = ("
			DELETE	FROM tx_actdetail
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND fs_count = '".trim($this->session->userdata('gCount'))."'
				AND fs_kd_trx = '".trim($sTrx)."'
				AND fs_kd_strx = '".trim($ssTrx)."'
				AND fs_refno = '".trim($sRefno)."'
			
			INSERT INTO tx_actdetail(fs_kd_comp, fs_kd_dept, fs_count, fs_kd_trx, fs_kd_strx,
					fs_refno, fs_seqno, fd_refno, fs_modul, fs_descp,
					fn_lineamt, fs_trsta, fs_usrcrt, fd_usrcrt, fs_upddt,
					fd_upddt, fn_ftrxamt, fn_ltrxamt, fs_kd_cussup, fs_countcussup,
					fd_periode)
			SELECT  fs_kd_comp, fs_kd_dept, fs_count, fs_kd_trx, fs_kd_strx,
					fs_refno, fs_seqno, fd_refno, modul = 'CB', fs_kd_refnot,
					fn_trxamtt, '".trim($sTrsta)."', fs_usrcrt, fd_usrcrt, fs_upddt,
					fd_upddt, fn_trxamtt, fn_trxamtt, fs_kd_cussup, fs_countcussup,
					'".trim($sPeriode)."'
			FROM	tx_cbdetail (NOLOCK)
			WHERE  	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND fs_count = '".trim($this->session->userdata('gCount'))."'
				AND fs_kd_trx = '".trim($sTrx)."'
				AND fs_kd_strx = '".trim($ssTrx)."'
				AND fs_refno = '".trim($sRefno)."'
			
			EXEC stp_JurnalCB '".trim($this->session->userdata('gComp'))."','".trim($this->session->userdata('gDept'))."',
							'".trim($this->session->userdata('gCount'))."','".trim($sTrx)."','".trim($ssTrx)."','".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function update_trs($sTrx,$ssTrx,$sTrsta,$sPeriode,$sRefno)
	{
		$xSQL = ("
			DELETE	tx_actheaderdt
			WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND	fs_count = '".trim($this->session->userdata('gCount'))."'
				AND	fs_kd_trx = '".trim($sTrx)."'
				AND	fs_refno = '".trim($sRefno)."'
			
			DELETE	tx_cbdetail
			WHERE	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND	fs_count = '".trim($this->session->userdata('gCount'))."'
				AND	fs_kd_trx = '".trim($sTrx)."'
				AND	fs_refno = '".trim($sRefno)."'
			
			DELETE	tx_actheader
			FROM 	tx_cbheader a (NOLOCK)
			INNER JOIN tx_actheader b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
				AND a.fs_kd_dept = b.fs_kd_dept
				AND a.fs_count = b.fs_count
				AND a.fs_kd_trx = b.fs_kd_trx
				AND a.fs_kd_strx = b.fs_kd_strx
				AND a.fs_refno = b.fs_refno
			WHERE	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fs_kd_strx = '".trim($ssTrx)."'
				AND a.fs_refno = '".trim($sRefno)."'
        
			INSERT INTO tx_actheader(
					fs_kd_comp, fs_kd_dept, fs_count,
					fs_kd_trx, fs_kd_strx, fs_refno,
					fd_refno, fs_doc, fd_doc,
					fd_periode, fs_kd_acno, fn_grsamt,
					fs_descrp, fb_draft, fs_trsta,
					fb_delete, fs_usrcrt, fd_usrcrt,
					fs_upddt, fd_upddt, fn_ftrxamt,
					fn_ltrxamt, fs_modul,
					fs_kd_cussup, fs_countcussup,
					fb_close
			)
            SELECT	a.fs_kd_comp, a.fs_kd_dept, a.fs_count,
					a.fs_kd_trx, a.fs_kd_strx, a.fs_refno,
					a.fd_refno, a.fs_docno, a.fd_docno,
					a.fd_periode, a.fs_acno, a.fn_amount,
					a.fs_note, a.fb_draft, a.fs_trsta,
					a.fb_delete, a.fs_usrcrt, a.fd_usrcrt,
					a.fs_upddt, a.fd_upddt, a.fn_amount,
					a.fn_amount, 'CB',
					a.fs_kd_cussup, a.fs_countcussup,
					'1'
            FROM	tx_cbheader a (NOLOCK)
            WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
				AND a.fs_kd_trx = '".trim($sTrx)."'
				AND a.fs_kd_strx = '".trim($ssTrx)."'
				AND a.fs_refno = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function update_trs2($sTrx,$ssTrx,$sTrsta,$sPeriode,$sRefno,$sRefnoJual)
	{
		$xSQL = ("
			EXEC STP_ACTHEADER '".trim($this->session->userdata('gComp'))."','".trim($this->session->userdata('gDept'))."',
							'".trim($this->session->userdata('gCount'))."','".trim($sTrx)."','".trim($ssTrx)."','".trim($sRefno)."'
			
			EXEC stp_JurnalCB '".trim($this->session->userdata('gComp'))."','".trim($this->session->userdata('gDept'))."',
							'".trim($this->session->userdata('gCount'))."','".trim($sTrx)."','".trim($ssTrx)."','".trim($sRefno)."'
		");
		
		if (trim($sTrx) == '5000')
		{
			$xSQL = $xSQL.("
				DECLARE @Sisa NUMERIC(35,0)
				SET	@Sisa = 0
				
				SELECT	@Sisa = b.fn_trxamt - SUM(a.fn_trxamtt)
				FROM 	tx_cbdetail a (NOLOCK)
				INNER JOIN tx_posheader b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
					AND a.fs_kd_refnot = b.fs_refno
					AND b.fs_kd_trx = 'JL'
				WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
					AND a.fs_kd_trx = '".trim($sTrx)."'
					AND a.fs_kd_strx = '".trim($ssTrx)."'
					AND	b.fs_refno = '".trim($sRefnoJual)."'
				GROUP BY b.fn_trxamt
				
				UPDATE	tx_posheader SET fn_rmnamt = @Sisa
				FROM 	tx_cbdetail a (NOLOCK)
				INNER JOIN tx_posheader b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
					AND a.fs_kd_refnot = b.fs_refno
					AND b.fs_kd_trx = 'JL'
				WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
					AND a.fs_kd_trx = '".trim($sTrx)."'
					AND a.fs_kd_strx = '".trim($ssTrx)."'
					AND	b.fs_refno = '".trim($sRefnoJual)."'
					
				UPDATE	tx_actheader SET fn_rmnamt = @Sisa
				FROM 	tx_cbdetail a (NOLOCK)
				INNER JOIN tx_actheader b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
					AND a.fs_kd_refnot = b.fs_refno
					AND b.fs_kd_trx = 'JL'
				WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
					AND a.fs_kd_trx = '".trim($sTrx)."'
					AND a.fs_kd_strx = '".trim($ssTrx)."'
					AND	b.fs_refno = '".trim($sRefnoJual)."'
			");
		}
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function hapus_trs($sTrx,$ssTrx,$sRefno)
	{
		$xSQL = '';
		
		if (trim($sTrx) == '5000')
		{
			$xSQL = $xSQL.("
				UPDATE	tx_posheader SET fn_rmnamt = b.fn_rmnamt + a.fn_trxamtt
				FROM 	tx_cbdetail a (NOLOCK)
				INNER JOIN tx_posheader b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
					AND a.fs_kd_refnot = b.fs_refno
					AND b.fs_kd_trx = 'JL'
				WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
					AND a.fs_kd_trx = '".trim($sTrx)."'
					AND a.fs_kd_strx = '".trim($ssTrx)."'
					AND a.fs_refno = '".trim($sRefno)."'
					
				UPDATE	tx_actheader SET fn_rmnamt = b.fn_rmnamt + a.fn_trxamtt
				FROM 	tx_cbdetail a (NOLOCK)
				INNER JOIN tx_actheader b (NOLOCK) ON a.fs_kd_comp = b.fs_kd_comp
					AND a.fs_kd_refnot = b.fs_refno
					AND b.fs_kd_trx = 'JL'
				WHERE 	a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND a.fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND a.fs_count = '".trim($this->session->userdata('gCount'))."'
					AND a.fs_kd_trx = '".trim($sTrx)."'
					AND a.fs_kd_strx = '".trim($ssTrx)."'
					AND a.fs_refno = '".trim($sRefno)."'
			");
		}
		
		$xSQL = $xSQL.("
			UPDATE	tx_cbheader SET fb_delete = 1
            WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND fs_count = '".trim($this->session->userdata('gCount'))."'
				AND fs_kd_trx = '".trim($sTrx)."'
				AND fs_kd_strx = '".trim($ssTrx)."'
				AND fs_refno = '".trim($sRefno)."'
				
			UPDATE	tx_actheader SET fb_delete = 1
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
				AND fs_count = '".trim($this->session->userdata('gCount'))."'
				AND fs_kd_trx = '".trim($sTrx)."'
				AND fs_kd_strx = '".trim($ssTrx)."'
				AND fs_refno = '".trim($sRefno)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>