<?php

class Kasir extends CI_Controller
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
			$this->load->view('vkasir');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function terbilang()
	{
		$xtotal = trim($this->input->post('fn_total'));
		$this->load->model('mMainmodul','',true);
		$word = $this->mMainmodul->terbilang($xtotal,4);
		
		$hasil = array(
			'sukses'	=> true,
			'word'		=> $word
		);
		echo json_encode($hasil);
	}
	
	function addfield()
	{
		$nmtabel = 'tx_poskasir';
		$nmkolom = 'fb_in';
		$pjgkolom = '1';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		
		$nmtabel = 'tx_poskasir';
		$nmkolom = 'fb_setor';
		$pjgkolom = '1';
		
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->addfield($nmtabel,$nmkolom,$pjgkolom);
		
		$hasil = array(
			'sukses'	=> true
		);
		echo json_encode($hasil);
	}
	
	function refno()
	{
		$nstart = trim($this->input->post('start'));
		
		$refno = trim($this->input->post('fs_refno'));
		$cust = trim($this->input->post('fs_cust'));
		$trx = 'KSR';
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kasir_all($refno,$cust);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->kasir($refno,$cust,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kasir_ceksave()
	{
		$refno = trim($this->input->post('fs_refno'));
		$kdtrx = 'KSR';
		
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
			$this->load->model('mKasir','',true);
			$ssql = $this->mKasir->cek_refno($kdtrx,$refno);
			
			if ($ssql->num_rows() > 0)
			{
				$hasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Receipt already exists, do you want to update it?'
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
	
	function kasir_save()
	{
		$refno = trim($this->input->post('fs_refno'));
		$refnodt = trim($this->input->post('fd_refno'));
		$cust = trim($this->input->post('fs_cust'));
		$total = trim($this->input->post('fn_total'));
		$note = trim($this->input->post('fs_note'));
		
		$tbilang = trim($this->input->post('fs_terbilang'));
		$kdin = trim($this->input->post('fb_in'));
		$kdsetor = trim($this->input->post('fb_setor'));
		$kddept = trim($this->session->userdata('gDept'));
		$kdcount = trim($this->session->userdata('gCount'));
		
		$kdtrx = 'KSR';
		$kdstrx = '';
		
		if (trim($kdin) == 'true')
		{
			$kdin = 1;
		}
		else
		{
			$kdin = 0;
		}
		
		if (trim($kdsetor) == 'true')
		{
			$kdsetor = 1;
		}
		else
		{
			$kdsetor = 0;
		}
		
		$this->load->model('mKasir','',true);
		$ssql = $this->mKasir->cek_acno();
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$kdacno = $ssql->fs_kd_acno;
		}
		else
		{
			$kdacno = '';
		}
		
		$xupdate = false;
		$this->load->model('mKasir','',true);
		$ssql = $this->mKasir->cek_refno($kdtrx,$refno);
		
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
			$ldept = strlen(trim($this->session->userdata('gDept')));
			$ldept = $ldept - 2;
			$lcount = strlen(trim($this->session->userdata('gCount')));
			$lcount = $lcount - 2;
			
			$xdept = substr(trim($this->session->userdata('gDept')), $ldept, 2).substr(trim($this->session->userdata('gCount')), $lcount, 2);
			$xdate = substr(trim($refnodt), 2, 6);
			
			$xprefix = trim($this->session->userdata('gSparePart')).trim($kdtrx).'/'.trim($xdept).'/'.trim($xdate).'/';
			$this->load->model('mMainModul','',true);
			$ssql = $this->mMainModul->get_nokasir($xprefix);
			//eof generate refno
			
			if ($ssql->num_rows() > 0)
			{
				$ssql = $ssql->row();
				
				$lrefno = strlen(trim($ssql->fs_refno));
				$lrefno = $lrefno - 5;
				
				$refno = $xprefix.sprintf("%05d",(substr(trim($ssql->fs_refno), $lrefno, 5) + 1));
			}
			else
			{
				$refno = $xprefix.'00001';
			}
		}
		
		$dt = array(
			'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
			'fs_kd_dept'	=> trim($this->session->userdata('gDept')),
			'fs_count'		=> trim($this->session->userdata('gCount')),
			'fs_trx'		=> trim($kdtrx),
			'fs_refno'		=> trim($refno),
			
			'fd_refno'		=> trim($refnodt),
			'fs_trxrefno'	=> trim($refno),
			'fd_trxrefno'	=> trim($kdtrx),
			'fs_acno'		=> trim($kdacno),
			'fs_card_num'	=> '',
			
			'fs_card_type'	=> '00',
			'fs_kd_bank'	=> '',
			'fs_count_bank'	=> '',
			'fn_rmamt'		=> '0',
			'fn_pyamt'		=> trim($total),
			
			'fs_descrp'		=> trim($cust).'\\'.trim($note),
			'fb_in'			=> trim($kdin),
			'fb_setor'		=> trim($kdsetor)
			);
		
		if ($xupdate == false)
		{
			$dt2 = array(
				'fs_usrcrt'	=> trim($this->session->userdata('gUser')),
				'fd_usrcrt'	=> trim(date('Y-m-d H:i:s')),
				'fs_upddt'	=> trim($this->session->userdata('gUser'))
			);
			$data = array_merge($dt,$dt2);
			
			$this->db->insert('tx_poskasir', $data);
		}
		else
		{
			$dt2 = array(
				'fs_upddt' => trim($this->session->userdata('gUser'))
			);
			$data = array_merge($dt,$dt2);
			$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
						AND	fs_refno = '".trim($refno)."'";
			
			$this->db->where($where);
			$this->db->update('tx_poskasir', $data);
		}
		$this->session->set_userdata('vtbilang', $tbilang);
		
		if ($xupdate == false)
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Cashier Success',
				'refno'		=> $refno
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Cashier Update Success',
				'refno'		=> $refno
			);
			echo json_encode($hasil);
		}
	}
	
	function print_kuitansi()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tkasir';
		$nmfile = 'kasir-'.$jamskg;
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
		
		$oExcel->getActiveSheet()->setTitle('Kasir');
		$oExcel->getActiveSheet()->setShowGridlines(false);
		
		$oExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$oExcel->getActiveSheet()->getPageMargins()->setTop(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setBottom(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setLeft(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setRight(0);
		// $oExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . '&RPage &P of &N');
		$oExcel->getDefaultStyle()->getFont()->setName('Calibri');
		$oExcel->getDefaultStyle()->getFont()->setSize(10);
		
		$sel = $oExcel->getActiveSheet();
		
		$refno = trim($this->input->post('fs_refno'));
		
		if (trim($refno)  == '')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'No record!!'
			);
			echo json_encode($hasil);
			return;
		}
		
		$this->load->model('mKasir','',true);
		$ssql = $this->mKasir->kasir($refno,'');
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$cust = $ssql->fs_cust;
			$total = $ssql->fn_total;
			$note = $ssql->fs_note;
			
			$this->load->model('mMainModul','',true);
			$bilang = $this->mMainModul->terbilang($total,4);
			
			$i = 1;
			$sel->setCellValue('A'.$i, 'K W I T A N S I');
			$oExcel->getActiveSheet()->mergeCells('A'.$i.':E'.$i);
			$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$oExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$oExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
			$oExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setSize(14);
			$i=$i+2;
			
			$sel = $oExcel->getActiveSheet();
			
			$sel->setCellValue('A'.$i, 'No. Kwitansi');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $refno);
			$i++;
			
			$sel->setCellValue('A'.$i, 'Telah diterima dari');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $cust);
			$oExcel->getActiveSheet()->mergeCells('C'.$i.':E'.$i);
			$i++;
			
			$sel->setCellValue('A'.$i, 'Uang sejumlah');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $bilang);
			$j=$i+2;
			$oExcel->getActiveSheet()->mergeCells('A'.$i.':A'.$j);
			$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$oExcel->getActiveSheet()->mergeCells('B'.$i.':B'.$j);
			$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$oExcel->getActiveSheet()->mergeCells('C'.$i.':E'.$j);
			$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setWrapText(true);
			$i=$i+3;
			
			$sel->setCellValue('A'.$i, 'Untuk Pembayaran');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $note);
			$j=$i+2;
			$oExcel->getActiveSheet()->mergeCells('A'.$i.':A'.$j);
			$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$oExcel->getActiveSheet()->mergeCells('B'.$i.':B'.$j);
			$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$oExcel->getActiveSheet()->mergeCells('C'.$i.':E'.$j);
			$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$i=$i+4;
			
			$sel->setCellValue('A'.$i,'Rp. '.number_format($total,2,',','.'));
			$oExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
			$oExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
			$oExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setSize(12);
			$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$oExcel->getActiveSheet()->getStyle('A'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFA9A9A9');
			$i++;
			
			$sel->setCellValue('E'.$i,trim($this->session->userdata('gKota')).',   '.trim(date('d-m-Y')));
			$i++;
			
			// $sel->setCellValue('C'.$i,'Customer,');
			$sel->setCellValue('E'.$i,'Diterima Oleh,');
			$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i=$i+8;
			
			$sel->setCellValue('C'.$i,$cust);
			$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$oExcel->getActiveSheet()->getStyle('C'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
			$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$oExcel->getActiveSheet()->getStyle('E'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
			
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
				'nmfile'	=> $nmfile.'.pdf'
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