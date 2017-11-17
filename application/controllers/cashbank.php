<?php

class CashBank extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		//change db
		$this->load->model('mMainModul','',true);
		$this->mMainModul->change_db($this->session->userdata('gServer'),$this->session->userdata('gDatabase'));
		//eof change db
	}
	
	function index()
	{
		if (trim($this->session->userdata('gDatabase')) <> '')
		{
			$this->load->view('vcashbank');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function dept_def()
	{
		$xdeptcd = trim($this->session->userdata('gDept'));
		$xcount = trim($this->session->userdata('gCount'));
		$xdept = trim($this->session->userdata('gDeptName'));
		$hasil = array(
			'sukses'	=> true,
			'deptcd'	=> $xdeptcd,
			'count'		=> $xcount,
			'dept'		=> $xdept
		);
		echo json_encode($hasil);
	}
	
	function cbin_dept()
	{
		$nstart = trim($this->input->post('start'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->login_dept_all('','','');
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->login_dept('','','',$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function cbout_dept()
	{
		$nstart = trim($this->input->post('start'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->login_dept_all('','','');
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->login_dept('','','',$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function cbin_trs()
	{
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->cbtrs_type('5500');
		echo json_encode($ssql->result());
	}
	
	function cbout_trs()
	{
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->cbtrs_type('4400');
		echo json_encode($ssql->result());
	}
	
	function cbin_refno()
	{
		$lstrx = strlen(trim($this->input->post('fs_kd_strx')));
		$lstrx = $lstrx - 4;
		
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		$kdtrx = substr(trim($this->input->post('fs_kd_strx')), 0, $lstrx);
		$kdstrx = substr(trim($this->input->post('fs_kd_strx')), $lstrx, 4);
		
		$nstart = trim($this->input->post('start'));
		$refno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->cbin_refno_all($kddept,$kdcount,$kdtrx,$kdstrx,$refno);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->cbin_refno($kddept,$kdcount,$kdtrx,$kdstrx,$refno,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function cbout_refno()
	{
		$lstrx = strlen(trim($this->input->post('fs_kd_strx')));
		$lstrx = $lstrx - 4;
		
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		$kdtrx = substr(trim($this->input->post('fs_kd_strx')), 0, $lstrx);
		$kdstrx = substr(trim($this->input->post('fs_kd_strx')), $lstrx, 4);
		
		$nstart = trim($this->input->post('start'));
		$refno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->cbin_refno_all($kddept,$kdcount,$kdtrx,$kdstrx,$refno);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->cbin_refno($kddept,$kdcount,$kdtrx,$kdstrx,$refno,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function cbin_acno()
	{
		$nstart = trim($this->input->post('start'));
		$kdacno = trim($this->input->post('fs_kd_acno'));
		$nmacno = trim($this->input->post('fs_nm_acno'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->cbin_acno_all($kdacno,$nmacno);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->cbin_acno($kdacno,$nmacno,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function cbout_acno()
	{
		$nstart = trim($this->input->post('start'));
		$kdacno = trim($this->input->post('fs_kd_acno'));
		$nmacno = trim($this->input->post('fs_nm_acno'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->cbin_acno_all($kdacno,$nmacno);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->cbin_acno($kdacno,$nmacno,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function cbin_isigrid()
	{
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		trim($this->input->post('fs_count'));
		$kdstrx = trim($this->input->post('fs_kd_strx'));
		$refno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mCashBank','',true);
		$ssql = $this->mCashBank->cbin_grid($kddept,$kdcount,$kdstrx,$refno);
		echo json_encode($ssql->result());
	}
	
	function cbout_isigrid()
	{
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		$kdstrx = trim($this->input->post('fs_kd_strx'));
		$refno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mCashBank','',true);
		$ssql = $this->mCashBank->cbin_grid($kddept,$kdcount,$kdstrx,$refno);
		echo json_encode($ssql->result());
	}
	
	function cbin_ceksave()
	{
		$refno = trim($this->input->post('fs_refno'));
		
		if (trim($refno) == '' or trim($refno) == 'AUTOMATIC')
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'lanjut'
			);
			echo json_encode($hasil);
		}
		else
		{
			$lstrx = strlen(trim($this->input->post('fs_kd_strx')));
			$lstrx = $lstrx - 4;
			
			$kddept = trim($this->input->post('fs_kd_dept'));
			$kdcount = trim($this->input->post('fs_count'));
			$kdtrx = substr(trim($this->input->post('fs_kd_strx')), 0, $lstrx);
			$kdstrx = substr(trim($this->input->post('fs_kd_strx')), $lstrx, 4);
			
			$this->load->model('mCashBank','',true);
			$ssql = $this->mCashBank->cbin_cek_refno($kddept,$kdcount,$kdtrx,$kdstrx,$refno);
			
			if ($ssql->num_rows() > 0)
			{
				$hasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Reference number already exists, do you want to update it?'
				);
				echo json_encode($hasil);
			}
			else
			{
				$hasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Saving Failed, Reference number unknown!!'
				);
				echo json_encode($hasil);
			}
		}
	}
	
	function cbin_save()
	{
		$refno = trim($this->input->post('fs_refno'));
		
		$lstrx = strlen(trim($this->input->post('fs_kd_strx')));
		$lstrx = $lstrx - 4;
		
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		$kdtrx = substr(trim($this->input->post('fs_kd_strx')), 0, $lstrx);
		$kdstrx = substr(trim($this->input->post('fs_kd_strx')), $lstrx, 4);
		$status = trim($this->input->post('fs_kd_status'));
		$cekgiro = trim($this->input->post('fs_cekgiro'));
		
		if ($status == '1')
		{
			$trsta = '5';
		}
		else
		{
			$trsta = '7';
		}
		
		$tglrefno = trim($this->input->post('fs_refnodt'));
		$this->load->model('mMainModul','',true);
		$docno = $this->mMainModul->clean_input(trim($this->input->post('fs_docno')));
		$tgldocno = trim($this->input->post('fs_docnodt'));
		
		$this->load->model('mMainModul','',true);
		$note = $this->mMainModul->clean_input(trim($this->input->post('fs_note')));
		$totalamt = trim($this->input->post('fs_total'));
		$modul = 'CB';
		$idr = 'IDR';
		
		
		//get periode
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->getperiode($tglrefno);
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$periode = $ssql->periode;
		}
		else
		{
			$periode = '';
		}
		//eof get periode
		
		
		$xupdate = false;
		$this->load->model('mCashBank','',true);
		$ssql = $this->mCashBank->cbin_cek_refno($kddept,$kdcount,$kdtrx,$kdstrx,$refno);
		
		if ($ssql->num_rows() > 0)
		{
			//refno ada
			$ssql = $ssql->row();
			$refno = $ssql->fs_refno;
			$xupdate = true;
			//eof refno ada
		}
		else
		{
			//generate refno
			$this->load->model('mMainModul','',true);
			$ssql = $this->mMainModul->get_refno($kddept,$kdcount,$kdtrx,$kdstrx,$tglrefno);
			//eof generate refno
			
			if ($ssql->num_rows() > 0)
			{
				$ssql = $ssql->row();
				$refno = $ssql->REFNO;// -> case sensitif <jgn diubah>
			}
			else
			{
				$refno = '';
			}
		}
		
		
		//save header
		$dt = array(
			'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
			'fs_kd_dept'	=> trim($kddept),
			'fs_count'		=> trim($kdcount),
			'fs_kd_trx'		=> trim($kdtrx),
			'fs_kd_strx'	=> trim($kdstrx),
			
			'fs_refno'		=> trim($refno),
			'fd_refno'		=> trim($tglrefno),
			'fd_periode'	=> trim($periode),
			'fs_doc'		=> trim($docno),
			'fd_doc'		=> trim($tgldocno),
			
			'fs_descrp'		=> trim($note),
			'fs_currency'	=> $idr,
			'fn_CurrRate'	=> '1',
			'fn_CurrBase'	=> '1',
			'fn_TaxRate'	=> '1',
			
			'fn_taxbase'	=> '1',
			'fs_kd_tax'		=> '0',
			'fs_kd_term'	=> '00',
			'fn_qty'		=> '1',
			'fn_grsamt'		=> trim($totalamt),
			
			'fs_modul'		=> $modul,
			'fb_draft'		=> trim($status),
			'fs_trsta'		=> trim($trsta),
			'fs_kd_grpacno'	=> '',
			'fs_cekgiro'	=> trim($cekgiro)
		);
		
		
		if ($xupdate == false)
		{
			if (trim($refno) <> '')
			{
				$dt2 = array(
					'fs_usrcrt'	=> trim($this->session->userdata('gUser')),
					'fd_usrcrt'	=> trim(date('Y-m-d H:i:s')),
					'fs_upddt'	=> trim($this->session->userdata('gUser')),
					'fd_upddt'	=> trim(date('Y-m-d H:i:s'))
				);
				$data = array_merge($dt,$dt2);
				
				$this->db->insert('tx_actheader', $data);
				
				// $hasil = array(
					// 'sukses'	=> true,
					// 'hasil'	=> 'Saving Success',
					// 'refno'	=> $refno
				// );
				// echo json_encode($hasil);
			}
			else
			{
				$hasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Saving Failed, generate reference number failed!!</br>Please try again letter...',
					'refno'		=> $refno
				);
				echo json_encode($hasil);
			}
		}
		else
		{
			$dt2 = array(
				'fs_upddt'	=> trim($this->session->userdata('gUser')),
				'fd_upddt'	=> trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
						AND fs_kd_dept = '".trim($kddept)."'
						AND	fs_count = '".trim($kdcount)."'
						AND	fs_kd_trx = '".trim($kdtrx)."'
						AND	fs_kd_strx = '".trim($kdstrx)."'
						AND	fs_refno = '".trim($refno)."'";
			
			$this->db->where($where);
			$this->db->update('tx_actheader', $data);
			
			// $hasil = array(
				// 'sukses'	=> true,
				// 'hasil'	=> 'Saving Update Success',
				// 'refno'	=> $refno
			// );
			// echo json_encode($hasil);
		}
		//eof save header
		
		
		//save detail
		//hapus detail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($kddept)."'
					AND	fs_count = '".trim($kdcount)."'
					AND	fs_kd_trx = '".trim($kdtrx)."'
					AND	fs_kd_strx = '".trim($kdstrx)."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actheaderdt');
		//eof hapus detail
		
		
		$kdacno = explode('|', trim($this->input->post('fs_kd_acno')));
		$notedetil = explode('|', trim($this->input->post('fs_notedetil')));
		$amt = explode('|', trim($this->input->post('fn_amt')));
		$seqno = explode('|', trim($this->input->post('fs_seqno')));
		$dc = explode('|', trim($this->input->post('fs_dc')));
		
		$jml = count($kdacno) - 1;
		if ($jml != 0)
		{
			for ($i=1; $i<=$jml; $i++)
			{
				$data = array(
					'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
					'fs_kd_dept'	=> trim($kddept),
					'fs_count'		=> trim($kdcount),
					'fs_kd_trx'		=> trim($kdtrx),
					'fs_kd_strx'	=> trim($kdstrx),
					
					'fs_refno'		=> trim($refno),
					'fn_ftrxamt'	=> trim($amt[$i]),
					'fd_periode'	=> trim($periode),
					'fn_ltrxamt'	=> trim($amt[$i]),
					'fs_acno'		=> trim($kdacno[$i]),
					
					'fs_docno'		=> trim($docno),
					'fd_docno'		=> trim($tgldocno),
					'fd_refno'		=> trim($tglrefno),
					'fs_modul'		=> $modul,
					'fs_dbcr'		=> trim($dc[$i]),
					
					'fs_currency'	=> $idr,
					'fs_descrp'		=> trim($notedetil[$i]),
					'fs_seqno'		=> trim(sprintf("%06d",$i)),
					'fn_CurrRate'	=> '1',
					'fn_CurrBase'	=> '1',
					
					'fs_usrcrt'		=> trim($this->session->userdata('gUser')),
					'fd_usrcrt'		=> trim(date('Y-m-d H:i:s')),
					'fs_upddt'		=> trim($this->session->userdata('gUser')),
					'fd_upddt'		=> trim(date('Y-m-d H:i:s'))
				);
				
				$this->db->insert('tx_actheaderdt', $data);
			}
		}
		
		if ($xupdate == false)
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Success',
				'refno'		=> $refno
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Update Success',
				'refno'		=> $refno
			);
			echo json_encode($hasil);
		}
		//eof save detail
	}
	
	function cbin_cekremove()
	{
		$refno = trim($this->input->post('fs_refno'));
		
		if (trim($refno) == '' or trim($refno) == 'AUTOMATIC')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Remove Failed, Reference number empty!!'
			);
			echo json_encode($hasil);
		}
		else
		{
			$lstrx = strlen(trim($this->input->post('fs_kd_strx')));
			$lstrx = $lstrx - 4;
			
			$kddept = trim($this->input->post('fs_kd_dept'));
			$kdcount = trim($this->input->post('fs_count'));
			$kdtrx = substr(trim($this->input->post('fs_kd_strx')), 0, $lstrx);
			$kdstrx = substr(trim($this->input->post('fs_kd_strx')), $lstrx, 4);
			
			$this->load->model('mCashBank','',true);
			$ssql = $this->mCashBank->cbin_cek_refno($kddept,$kdcount,$kdtrx,$kdstrx,$refno);
			
			if ($ssql->num_rows() > 0)
			{
				$hasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Are you sure you want to delete this reference number?'
				);
				echo json_encode($hasil);
			}
			else
			{
				$hasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Remove Failed, Reference number unknown!!'
				);
				echo json_encode($hasil);
			}
		}
	}
	
	function cbin_remove()
	{
		$refno = trim($this->input->post('fs_refno'));
		$lstrx = strlen(trim($this->input->post('fs_kd_strx')));
		$lstrx = $lstrx - 4;
		
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		$kdtrx = substr(trim($this->input->post('fs_kd_strx')), 0, $lstrx);
		$kdstrx = substr(trim($this->input->post('fs_kd_strx')), $lstrx, 4);
		
		//remove header
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($kddept)."'
					AND	fs_count = '".trim($kdcount)."'
					AND	fs_kd_trx = '".trim($kdtrx)."'
					AND	fs_kd_strx = '".trim($kdstrx)."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actheader');
		//eof remove header
		
		//remove detail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($kddept)."'
					AND	fs_count = '".trim($kdcount)."'
					AND	fs_kd_trx = '".trim($kdtrx)."'
					AND	fs_kd_strx = '".trim($kdstrx)."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actheaderdt');
		//eof remove detail
		
		$hasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Remove reference number: "'.$refno.'" success'
		);
		echo json_encode($hasil);
	}
	
	function printcb()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tcashbank';
		// $nmfile = 'cashbankin-'.$jamskg;
		$xIP = trim($this->session->userdata('ip_address'));
		$gIP = trim($this->session->userdata('gIP'));
		
		$oReader = PHPExcel_IOFactory::createReader('Excel5');
		
		if (trim($xIP) == trim($gIP))
		{
			$xtPath = '/var/www/'.config_item('base_folder').'/assets/docs/';
			$xPath = '/var/www/'.config_item('base_folder').'/temp/';
		}
		else
		{
			$xtPath = APPPATH.'../assets/docs/';
			$xPath = APPPATH.'../temp/';
		}
		
		$oExcel = $oReader->load($xtPath.$nmfiletemp.'.xls');
		
		$oExcel->getActiveSheet()->setTitle('Cash Bank');
		$oExcel->getActiveSheet()->setShowGridlines(false);
		
		$oExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$oExcel->getActiveSheet()->getPageMargins()->setTop(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setBottom(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setLeft(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setRight(0);
		$oExcel->getDefaultStyle()->getFont()->setName('Calibri');
		$oExcel->getDefaultStyle()->getFont()->setSize(10);
		$sel = $oExcel->getActiveSheet();
		
		$refno = trim($this->input->post('fs_refno'));
		$lstrx = strlen(trim($this->input->post('fs_kd_strx')));
		$lstrx = $lstrx - 4;
		
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		$kdtrx= trim($this->input->post('fs_kd_strx'));
		
		if (strripos($kdtrx,'5500') > -1)
		{
			$nmfile = 'cashbankin-'.$jamskg;
			$xJudul = 'BUKTI BANK MASUK';
		}
		else
		{
			$nmfile = 'cashbankout-'.$jamskg;
			$xJudul = 'BUKTI BANK KELUAR';
		}
		
		//query dg nomor refno
		
		$this->load->model('mCashBank','',true);
		$ssql = $this->mCashBank->printcb($kddept,$kdcount,$kdtrx,$refno);
		
		$data = array();
		$data = $ssql;
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
						
			$UserName = trim($this->session->userdata('gUser'));
			$Refno = $ssql->fs_refno;
			$Note = $ssql->fs_descrp;
			$namacabang = $ssql->fs_nm_cabang;
			$docno = $ssql->docno;
			$tipetrs = $ssql->fs_nm_strx;
			
			$pjghal = 57;
			$i = 1;
			$l = 0; // no urut
			
			$k = 1; // pjg hal
			$p = 1; // awal hal
			
			// $sel->setCellValue('A'.$i, 'Tanggal Cetak');
			// $sel->setCellValue('C'.$i, ':'.trim(date('d-m-Y')));
			
			// $sel->setCellValue('A'.$i, 'Waktu');
			// $sel->setCellValue('C'.$i, ':'.SUBSTR(date('Y-m-d H:i:s'),11,STRLEN(date('Y-m-d H:i:s'))));
			// $sel->setCellValue('F'.$i, 'Dicetak Oleh:'.$UserName);
			
			$sel->setCellValue('A'.$i, $xJudul);
			$sel->mergeCells('A'.$i.':H'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$sel->getStyle('A'.$i)->getFont()->setSize(12);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'Cabang');
			$sel->setCellValue('C'.$i, ':');
			$sel->setCellValue('D'.$i, $kddept.$kdcount.' - '.$namacabang);
			$sel->mergeCells('A'.$i.':B'.$i);
			$sel->mergeCells('D'.$i.':H'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Tipe');
			$sel->setCellValue('C'.$i, ':');
			$sel->setCellValue('D'.$i, $tipetrs);
			$sel->mergeCells('A'.$i.':B'.$i);
			$sel->mergeCells('D'.$i.':H'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Ref. No');
			$sel->setCellValue('C'.$i, ':');
			$sel->setCellValue('D'.$i, $Refno);
			$sel->mergeCells('A'.$i.':B'.$i);
			$sel->mergeCells('D'.$i.':H'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Doc. No');
			$sel->setCellValue('C'.$i, ':');
			$sel->setCellValue('D'.$i, $docno);
			$sel->mergeCells('A'.$i.':B'.$i);
			$sel->mergeCells('D'.$i.':H'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Keterangan');
			$sel->setCellValue('C'.$i, ':');
			$sel->setCellValue('D'.$i, $Note);
			$sel->mergeCells('A'.$i.':B'.$i);
			$sel->mergeCells('D'.$i.':H'.$i);
			$sel->getStyle('D'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$sel->getStyle('D'.$i)->getAlignment()->setWrapText(true);
			$j = $i + 1;
			$sel->mergeCells('D'.$i.':H'.$j);
			$i = $i + 3;
			$k = $k + 3;
			
			$sel->setCellValue('A'.$i, 'No');
			$sel->setCellValue('B'.$i, 'Kode Rekening');
			$sel->setCellValue('E'.$i, 'Nama Rekening');
			$sel->setCellValue('F'.$i, 'Keterangan');
			$sel->setCellValue('G'.$i, 'Debit');
			$sel->setCellValue('H'.$i, 'Kredit');
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->mergeCells('B'.$i.':D'.$i);
			$sel->getStyle('A'.$i.':H'.$i)->applyFromArray(
				array(
					'borders' => array(
						'top' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$sel->getStyle('A'.$i.':H'.$i)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$sel->getStyle('A'.$i.':H'.$i)->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('argb' => 'H0C0C0C0')
					)
				)
			);
			$i++;
			$k++;
			$l++;
			
			$ndebet = 0;
			$ncredit = 0;
			
			foreach ($data->result() as $row) 
			{
				if ($k > $pjghal)
				{
					$j = $i - 1;
					$sel->setBreak('A'.$j, PHPExcel_Worksheet::BREAK_ROW);
					$sel->getStyle('A'.$j.':H'.$j)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					
					$q = $p + 9;
					$arr = array(
						'borders' => array(
							'left' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					);
					$sel->getStyle('A'.$q.':A'.$j)->applyFromArray($arr);
					$sel->getStyle('B'.$q.':B'.$j)->applyFromArray($arr);
					$sel->getStyle('E'.$q.':E'.$j)->applyFromArray($arr);
					$sel->getStyle('F'.$q.':F'.$j)->applyFromArray($arr);
					$sel->getStyle('G'.$q.':G'.$j)->applyFromArray($arr);
					$sel->getStyle('H'.$q.':H'.$j)->applyFromArray($arr);
					$sel->getStyle('H'.$q.':H'.$j)->applyFromArray(
						array(
							'borders' => array(
								'right' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$p = $i;
					
					$k = 1;
					$sel->setCellValue('A'.$i, $xJudul);
					$sel->mergeCells('A'.$i.':H'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
					$sel->getStyle('A'.$i)->getFont()->setBold(true);
					$sel->getStyle('A'.$i)->getFont()->setSize(12);
					$i = $i + 2;
					$k = $k + 2;
					
					$sel->setCellValue('A'.$i, 'Cabang');
					$sel->setCellValue('C'.$i, ':');
					$sel->setCellValue('D'.$i, $kddept.$kdcount.' - '.$namacabang);
					$sel->mergeCells('A'.$i.':B'.$i);
					$sel->mergeCells('D'.$i.':H'.$i);
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i, 'Tipe');
					$sel->setCellValue('C'.$i, ':');
					$sel->setCellValue('D'.$i, $tipetrs);
					$sel->mergeCells('A'.$i.':B'.$i);
					$sel->mergeCells('D'.$i.':H'.$i);
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i, 'Ref. No');
					$sel->setCellValue('C'.$i, ':');
					$sel->setCellValue('D'.$i, $Refno);
					$sel->mergeCells('A'.$i.':B'.$i);
					$sel->mergeCells('D'.$i.':H'.$i);
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i, 'Doc. No');
					$sel->setCellValue('C'.$i, ':');
					$sel->setCellValue('D'.$i, $docno);
					$sel->mergeCells('A'.$i.':B'.$i);
					$sel->mergeCells('D'.$i.':H'.$i);
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i, 'Keterangan');
					$sel->setCellValue('C'.$i, ':');
					$sel->setCellValue('D'.$i, $Note);
					$sel->mergeCells('A'.$i.':B'.$i);
					$sel->mergeCells('D'.$i.':H'.$i);
					$sel->getStyle('D'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
					$sel->getStyle('D'.$i)->getAlignment()->setWrapText(true);
					$j = $i + 1;
					$sel->mergeCells('D'.$i.':H'.$j);
					$i = $i + 3;
					$k = $k + 3;
					
					$sel->setCellValue('A'.$i, 'No');
					$sel->setCellValue('B'.$i, 'Kode Rekening');
					$sel->setCellValue('E'.$i, 'Nama Rekening');
					$sel->setCellValue('F'.$i, 'Keterangan');
					$sel->setCellValue('G'.$i, 'Debit');
					$sel->setCellValue('H'.$i, 'Kredit');
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->mergeCells('B'.$i.':D'.$i);
					$sel->getStyle('A'.$i.':H'.$i)->applyFromArray(
						array(
							'borders' => array(
								'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$sel->getStyle('A'.$i.':H'.$i)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$sel->getStyle('A'.$i.':H'.$i)->applyFromArray(
						array(
							'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('argb' => 'H0C0C0C0')
							)
						)
					);
					$i++;
					$k++;
				}
				
				$sel->setCellValue('A'.$i, $l);
				$sel->setCellValue('B'.$i, $row->fs_kd_acno);
				$sel->setCellValue('E'.$i, $row->fs_nm_acno);
				$sel->setCellValue('F'.$i, $row->fs_note);
				$sel->setCellValue('G'.$i, number_format($row->fn_debet));
				$sel->setCellValue('H'.$i, number_format($row->fn_credit));
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->mergeCells('B'.$i.':D'.$i);
				
				$ndebet = $ndebet + $row->fn_debet;
				$ncredit = $ncredit + $row->fn_credit;
				
				$i++;
				$k++;
				$l++;
            }
			$j = $i - 1;
			$sel->getStyle('A'.$j.':H'.$j)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			
			$q = $p + 9;
			$arr = array(
				'borders' => array(
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$sel->getStyle('A'.$q.':A'.$j)->applyFromArray($arr);
			$sel->getStyle('B'.$q.':B'.$j)->applyFromArray($arr);
			$sel->getStyle('E'.$q.':E'.$j)->applyFromArray($arr);
			$sel->getStyle('F'.$q.':F'.$j)->applyFromArray($arr);
			$sel->getStyle('G'.$q.':G'.$j)->applyFromArray($arr);
			$sel->getStyle('H'.$q.':H'.$j)->applyFromArray($arr);
			$sel->getStyle('H'.$q.':H'.$j)->applyFromArray(
				array(
					'borders' => array(
						'right' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			
			$sel->setCellValue('G'.$i, number_format($ndebet));
			$sel->setCellValue('H'.$i, number_format($ncredit));
			$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$arr = array(
				'borders' => array(
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$sel->getStyle('G'.$i.':G'.$i)->applyFromArray($arr);
			$sel->getStyle('H'.$i.':H'.$i)->applyFromArray($arr);
			$sel->getStyle('H'.$i.':H'.$i)->applyFromArray(
				array(
					'borders' => array(
						'right' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$sel->getStyle('G'.$i.':H'.$i)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$i = $i + 5;
			$k = $k + 5;
			
			$sel->setCellValue('A'.$i, 'Membuat');
			$sel->setCellValue('F'.$i, 'Memeriksa');
			$sel->setCellValue('G'.$i, 'Menyetujui');
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->mergeCells('A'.$i.':D'.$i);
			$sel->mergeCells('G'.$i.':H'.$i);
			$i = $i + 20;
			$k = $k + 20;
			
			$sel->setCellValue('A'.$i, '( '.$UserName.' )');
			$sel->setCellValue('F'.$i, '(.......................)');
			$sel->setCellValue('G'.$i, '(.......................)');
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->mergeCells('A'.$i.':D'.$i);
			$sel->mergeCells('G'.$i.':H'.$i);
			
			$oWriter = PHPExcel_IOFactory::createWriter($oExcel, 'Excel5');
			$oWriter->save($xPath.$nmfile.'.xls');
			
			//hapus file lama
			$this->load->model('mMainModul','',true);
			$jamskg = $this->mMainModul->microtime_float();
			$exp = 1800;
			
			$current_dir = @opendir($xPath);
			while ($filename = @readdir($current_dir))
			{
				if ($filename != '.' and $filename != '..' and $filename != 'captcha')
				{
					$filename2 = str_replace('.xls', '', $filename);
					$filename2 = str_replace('.pdf', '', $filename2);
					
					$xlen = strlen($filename2);
					$xmulai = stripos($filename2,'-') + 1;
					$filename2 = substr($filename2,$xmulai,$xlen);
					
					if (($filename2 + $exp) < $jamskg)
					{
						@unlink($xPath.$filename);
					}
				}
			}
			@closedir($current_dir);
			//eof hapus file lama
			
			$this->load->model('mMainModul','',true);
			$this->mMainModul->pdf($nmfile);
			
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> '"'.$nmfile.'.pdf" has been created!!',
				'nmfile'	=> $xPath.$nmfile.'.pdf'
			);
			
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'No record!!'
			);
			echo json_encode($hasil);
		}
	}
	
	function printcbout()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tcashbank';
		$nmfile = 'cashbankout-'.$jamskg;
		$xIP = trim($this->session->userdata('ip_address'));
		$gIP = trim($this->session->userdata('gIP'));
		
		$oReader = PHPExcel_IOFactory::createReader('Excel5');
		
		if (trim($xIP) == trim($gIP))
		{
			$xtPath = '/var/www/'.config_item('base_folder').'/assets/docs/';
			$xPath = '/var/www/'.config_item('base_folder').'/temp/';
		}
		else
		{
			$xtPath = APPPATH.'../assets/docs/';
			$xPath = APPPATH.'../temp/';
		}
		
		$oExcel = $oReader->load($xtPath.$nmfiletemp.'.xls');
		
		$oExcel->getActiveSheet()->setTitle('Cash Bank');
		$oExcel->getActiveSheet()->setShowGridlines(false);
		
		$oExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$oExcel->getActiveSheet()->getPageMargins()->setTop(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setBottom(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setLeft(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setRight(0);
		$oExcel->getDefaultStyle()->getFont()->setName('Calibri');
		$oExcel->getDefaultStyle()->getFont()->setSize(10);
		$sel = $oExcel->getActiveSheet();
		
		$refno = trim($this->input->post('fs_refno'));
		$lstrx = strlen(trim($this->input->post('fs_kd_strx')));
		$lstrx = $lstrx - 4;
		
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		
		
		$kdtrx= trim($this->input->post('fs_kd_strx'));
		
		//query dg nomor refno
		
		$this->load->model('mCashBank','',true);
		$ssql = $this->mCashBank->printcb($kddept,$kdcount,$kdtrx,$refno);
		
		$data = array();
		$data = $ssql;
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
						
			$UserName = trim($this->session->userdata('gUser'));
			$Refno = $ssql->fs_refno;
			$Note = $ssql->fs_descrp;
			$namacabang = $ssql->fs_nm_cabang;
			
			$pjghal = 57;
			$i = 1;
			$l = 0; // no urut
			
			$k = 1; // pjg hal
			$p = 1; // awal hal
			$sel->setCellValue('A'.$i, 'BUKTI BANK KELUAR');
			
			$sel->mergeCells('A'.$i.':G'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			
			
			$i=$i+5;
			$k=$k+5;
			$sel = $oExcel->getActiveSheet();
			
			$sel->setCellValue('A'.$i, 'Tanggal Cetak');
			$sel->setCellValue('C'.$i, ':'.trim(date('d-m-Y')));
			
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Waktu');
			$sel->setCellValue('C'.$i, ':'.SUBSTR(date('Y-m-d H:i:s'),11,STRLEN(date('Y-m-d H:i:s'))));
			
			$sel->setCellValue('F'.$i, 'Dicetak Oleh:'.$UserName);
			
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'No Tanggal');
			$sel->setCellValue('C'.$i,':'. $Refno);
			
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Note');
			$sel->setCellValue('C'.$i, ':'.$Note);
			
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Cabang');
			$sel->setCellValue('C'.$i, ':'.'('.$kddept .'|'.$kdcount.')  '.$namacabang);
			
			$i=$i+4;
			$k=$k+4;
			
			$prev = null;
			
			$ndebet=0;
			$ncredit=0;
			$no=0;
			
			foreach ($data->result() as $row) 
			{

				$no = $no + 1;
				$sel->setCellValue('A'.$i, $no);
				$sel->setCellValue('B'.$i, $row->fs_kd_acno);
				$sel->setCellValue('D'.$i, $row->fs_nm_acno);
				$sel->setCellValue('E'.$i, $row->fs_note);
				$sel->setCellValue('F'.$i, number_format($row->fn_debet));
				$sel->setCellValue('G'.$i, number_format($row->fn_credit));
				
				$ndebet = $ndebet + $row->fn_debet;
				$ncredit = $ncredit + $row->fn_credit;
				
				$arr = array(
						'borders' => array(
							'left' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					);
					
				$sel->getStyle('A'.$i.':A'.$i)->applyFromArray($arr);
				$sel->getStyle('B'.$i.':B'.$i)->applyFromArray($arr);
				$sel->getStyle('D'.$i.':D'.$i)->applyFromArray($arr);
				$sel->getStyle('E'.$i.':E'.$i)->applyFromArray($arr);
				$sel->getStyle('F'.$i.':F'.$i)->applyFromArray($arr);
				$sel->getStyle('G'.$i.':G'.$i)->applyFromArray($arr);
				$sel->getStyle('H'.$i.':H'.$i)->applyFromArray($arr);
				
				$i++;
				$k++;

            }
			
			$arr = array(
						'borders' => array(
							'top' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					);
					
			$sel->getStyle('A'.$i.':G'.$i)->applyFromArray($arr);	
			
			//$sel->getStyle('A'.$i.':G'.$i)->applyFromArray($arr);	
			
			$sel->setCellValue('F'.$i, number_format($ndebet));
			$sel->setCellValue('G'.$i, number_format($ncredit));			
			
			$arr = array(
						'borders' => array(
							'left' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					);

			$sel->getStyle('F'.$i.':F'.$i)->applyFromArray($arr);	
			$sel->getStyle('G'.$i.':G'.$i)->applyFromArray($arr);	
			$sel->getStyle('H'.$i.':H'.$i)->applyFromArray($arr);	
			
			$arr = array(
						'borders' => array(
							'bottom' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					);
			
			$sel->getStyle('F'.$i.':G'.$i)->applyFromArray($arr);	
			
			$i=$i+34;
			$k=$k+34;
			
			$sel->setCellValue('B'.$i, 'Membuat');
			$sel->setCellValue('E'.$i, 'Memeriksa');
			$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->setCellValue('G'.$i, 'Menyetujui');
			
			$i=$i+20;
			$k=$k+20;
			
			$sel->setCellValue('B'.$i, '('. $UserName .')');
			$sel->setCellValue('E'.$i, '(.............)');
			$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->setCellValue('G'.$i, '(.............)');
			
			$oWriter = PHPExcel_IOFactory::createWriter($oExcel, 'Excel5');
			$oWriter->save($xPath.$nmfile.'.xls');
			
			//hapus file lama
			$this->load->model('mMainModul','',true);
			$jamskg = $this->mMainModul->microtime_float();
			$exp = 1800;
			
			$current_dir = @opendir($xPath);
			while ($filename = @readdir($current_dir))
			{
				if ($filename != '.' and $filename != '..' and $filename != 'captcha')
				{
					$filename2 = str_replace('.xls', '', $filename);
					$filename2 = str_replace('.pdf', '', $filename2);
					
					$xlen = strlen($filename2);
					$xmulai = stripos($filename2,'-') + 1;
					$filename2 = substr($filename2,$xmulai,$xlen);
					
					if (($filename2 + $exp) < $jamskg)
					{
						@unlink($xPath.$filename);
					}
				}
			}
			@closedir($current_dir);
			//eof hapus file lama
			
			$this->load->model('mMainModul','',true);
			$this->mMainModul->pdf($nmfile);
			
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> '"'.$nmfile.'.pdf" has been created!!',
				'nmfile'	=> $xPath.$nmfile.'.pdf'
			);
			
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'No record!!'
			);
			echo json_encode($hasil);
		}
	}
}
?>