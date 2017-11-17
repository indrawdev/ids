<?php

class AccGL extends CI_Controller
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
			$this->load->view('vaccgl');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function acno()
	{
		$nstart = trim($this->input->post('start'));
		$kdacno = trim($this->input->post('fs_kd_acno'));
		$nmacno = trim($this->input->post('fs_nm_acno'));

		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->accgl_acno_all($kdacno,$nmacno,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->accgl_acno($kdacno,$nmacno,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function acno2()
	{
		$nstart = trim($this->input->post('start'));
		$kdacno = trim($this->input->post('fs_kd_acno'));
		$nmacno = trim($this->input->post('fs_nm_acno'));
		
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->accgl_acno_all($kdacno,$nmacno,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->accgl_acno2($kdacno,$nmacno,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function cek_show()
	{
		$kddept = trim($this->input->post('fs_kd_dept')).trim($this->input->post('fs_count'));
		$kdacnoa = trim($this->input->post('fs_kd_acnoa'));
		$kdacnob = trim($this->input->post('fs_kd_acnob'));
		$tgla = trim($this->input->post('fd_tgla'));
		$tglb = trim($this->input->post('fd_tglb'));
		
		if (trim($kdacnoa) <> '' and trim($kdacnob) <> '' and trim($tgla) <> '' and trim($tglb) <> '')
		{
			$hasil = array(
				'sukses'	=> true
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> false
			);
			echo json_encode($hasil);
		}
	}
	
	function grid_header()
	{
		$kddept = trim($this->input->post('fs_kd_dept')).trim($this->input->post('fs_count'));
		$kdacnoa = trim($this->input->post('fs_kd_acnoa'));
		$kdacnob = trim($this->input->post('fs_kd_acnob'));
		$tgla = trim($this->input->post('fd_tgla'));
		$tglb = trim($this->input->post('fd_tglb'));
		
		$nstart = trim($this->input->post('start'));
		
		$arr = array();
		if (trim($kdacnoa) <> '' and trim($kdacnob) <> '')
		{
			$this->load->model('mAccGL','',true);
			$ssql = $this->mAccGL->accgl_header_all($kddept,$kdacnoa,$kdacnob,$tgla,$tglb);
			$total = $ssql->num_rows();
			
			$ssql = $this->mAccGL->accgl_header($kddept,$kdacnoa,$kdacnob,$tgla,$tglb,$nstart);
			
			$bal = 0;
			
			if ($ssql->num_rows() > 0)
			{
				foreach ($ssql->result() as $row)
				{
					$bal = $bal + $row->BALANCE;
					$xTgl = trim($row->fd_refno);
					$xTgl = substr($xTgl,6,2).'-'.substr($xTgl,4,2).'-'.substr($xTgl,0,4);
					
					$arr[] = array(
						'fs_kd_dept'	=> trim($row->fs_kd_dept),
						'fs_count'		=> trim($row->fs_count),
						'DEPTNM'		=> trim($row->DEPTNM),
						'ACNO'			=> trim($row->ACNO),
						'ACNM'			=> trim($row->ACNM),
						
						'fs_kd_trx'		=> trim($row->fs_kd_trx),
						'fs_kd_strx'	=> trim($row->fs_kd_strx),
						'fs_refno'		=> trim($row->fs_refno),
						'fd_refno'		=> trim($xTgl),
						'fs_Descrp'		=> trim($row->fs_Descrp),
						
						'fs_acno'		=> trim($row->fs_acno),
						'DEBET'			=> trim($row->DEBET),
						'CREDIT'		=> trim($row->CREDIT),
						'BALANCE1'		=> trim($row->BALANCE),
						'BALANCE'		=> trim($bal)
					);

				}
			}
		}
		echo '({"total":"'.$total.'","hasil":'.json_encode($arr).'})';
	}
	
	function cek_detail()
	{
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		$kdtrx = trim($this->input->post('fs_kd_trx'));
		$kdstrx = trim($this->input->post('fs_kd_strx'));
		$refno = trim($this->input->post('fs_refno'));
		
		if (trim($kddept) <> '' and trim($kdcount) <> '' and trim($kdtrx) <> '' and trim($refno) <> '')
		{
			$hasil = array(
				'sukses'	=> true
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> false
			);
			echo json_encode($hasil);
		}
	}
	
	function grid_detail()
	{
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		$kdtrx = trim($this->input->post('fs_kd_trx'));
		$kdstrx = trim($this->input->post('fs_kd_strx'));
		$refno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mAccGL','',true);
		$ssql = $this->mAccGL->accgl_detail($kddept,$kdcount,$kdtrx,$kdstrx,$refno);
		echo json_encode($ssql->result());
	}
	
	function excel_header()
	{
		ini_set('memory_limit', '-1');
		
		$kddept = trim($this->input->post('fs_kd_dept')).trim($this->input->post('fs_count'));
		$kdacnoa = trim($this->input->post('fs_kd_acnoa'));
		$kdacnob = trim($this->input->post('fs_kd_acnob'));
		$nmacnoa = trim($this->input->post('fs_nm_acnoa'));
		$nmacnob = trim($this->input->post('fs_nm_acnob'));
		$tgla = trim($this->input->post('fd_tgla'));
		$tglb = trim($this->input->post('fd_tglb'));
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'taccgl';
		$nmfile = 'accgl-'.$jamskg;
		
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
		$pjghal = 38;
		$i = 1;
		$l = 0; // no urut
		
		$k = 1; // pjg hal
		$p = 1; // awal hal
		
		$oExcel->getActiveSheet()->setTitle('Buku Besar');
		$oExcel->getActiveSheet()->setShowGridlines(false);
		
		$oExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$oExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		
		$oExcel->getActiveSheet()->getPageMargins()->setTop(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setBottom(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setLeft(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setRight(0);
		
		$oExcel->getDefaultStyle()->getFont()->setName('Calibri');
		$oExcel->getDefaultStyle()->getFont()->setSize(10);
		$sel = $oExcel->getActiveSheet();
		
		$this->load->model('mAccGL','',true);
		$ssql = $this->mAccGL->accgl_header_all($kddept,$kdacnoa,$kdacnob,$tgla,$tglb);
		
		if ($ssql->num_rows() > 0)
		{
			$i = 1;
			$k = 1;
			$p = $i;
			$sel->setCellValue('A'.$i, 'Laporan Buku Besar');
			$sel->mergeCells('A'.$i.':G'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$sel->getStyle('A'.$i)->getFont()->setSize(12);
			$i++;
			$k++;
			$sel->mergeCells('A'.$i.':G'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->setCellValue('A'.$i, 'Periode: '.substr($tgla,6,2).'-'.substr($tgla,4,2).'-'.substr($tgla,0,4).' s/d '.substr($tglb,6,2).'-'.substr($tglb,4,2).'-'.substr($tglb,0,4));
			$i++;
			$k++;
			$sel->mergeCells('A'.$i.':G'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->setCellValue('A'.$i, 'Perkiraan antara: '.trim($nmacnoa).' s/d '.trim($nmacnob));
			$i++;
			$k++;
			$sel->mergeCells('A'.$i.':G'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'No');
			$sel->setCellValue('B'.$i, 'Faktur');
			$sel->setCellValue('C'.$i, 'Tanggal');
			$sel->setCellValue('D'.$i, 'Keterangan');
			$sel->setCellValue('E'.$i, 'Debet');
			$sel->setCellValue('F'.$i, 'Kredit');
			$sel->setCellValue('G'.$i, 'Saldo');
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$sel->getStyle('A'.$i.':G'.$i)->applyFromArray(
				array(
					'borders' => array(
						'top' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$sel->getStyle('A'.$i.':G'.$i)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$i++;
			$k++;
			$l++;
			
			$debet = 0;
			$kredit = 0;
			$kdacno = '';
			foreach ($ssql->result() as $row)
			{
				if ($k > $pjghal)
				{
					$j = $i - 1;
					$sel->setBreak('A'.$j, PHPExcel_Worksheet::BREAK_ROW);
					$sel->getStyle('A'.$j.':G'.$j)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					
					$q = $p + 4;
					$arr = array(
						'borders' => array(
							'left' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					);
					$sel->getStyle('A'.$q.':A'.$j)->applyFromArray($arr);
					$sel->getStyle('B'.$q.':B'.$j)->applyFromArray($arr);
					$sel->getStyle('C'.$q.':C'.$j)->applyFromArray($arr);
					$sel->getStyle('D'.$q.':D'.$j)->applyFromArray($arr);
					$sel->getStyle('E'.$q.':E'.$j)->applyFromArray($arr);
					$sel->getStyle('F'.$q.':F'.$j)->applyFromArray($arr);
					$sel->getStyle('G'.$q.':G'.$j)->applyFromArray($arr);
					$sel->getStyle('G'.$q.':G'.$j)->applyFromArray(
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
					$sel->setCellValue('A'.$i, 'Laporan Buku Besar');
					$sel->mergeCells('A'.$i.':G'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$i++;
					$k++;
					$sel->mergeCells('A'.$i.':G'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->setCellValue('A'.$i, 'Periode: '.substr($tgla,6,2).'-'.substr($tgla,4,2).'-'.substr($tgla,0,4).' s/d '.substr($tglb,6,2).'-'.substr($tglb,4,2).'-'.substr($tglb,0,4));
					$i++;
					$k++;
					$sel->mergeCells('A'.$i.':G'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->setCellValue('A'.$i, 'Perkiraan antara: '.trim($nmacnoa).' s/d '.trim($nmacnob));
					$i++;
					$k++;
					$sel->mergeCells('A'.$i.':G'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i, 'No');
					$sel->setCellValue('B'.$i, 'Faktur');
					$sel->setCellValue('C'.$i, 'Tanggal');
					$sel->setCellValue('D'.$i, 'Keterangan');
					$sel->setCellValue('E'.$i, 'Debet');
					$sel->setCellValue('F'.$i, 'Kredit');
					$sel->setCellValue('G'.$i, 'Saldo');
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					$sel->getStyle('A'.$i.':G'.$i)->applyFromArray(
						array(
							'borders' => array(
								'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$sel->getStyle('A'.$i.':G'.$i)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$q = $i;
					$i++;
					$k++;
				}
				
				$debet = $debet + trim($row->DEBET);
				$kredit = $kredit + trim($row->CREDIT);
				
				if (trim($kdacno) == '' or trim($kdacno <> trim($row->ACNO)))
				{
					$sel->mergeCells('A'.$i.':G'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$sel->setCellValue('A'.$i, trim($row->ACNM));
					$sel->getStyle('A'.$i.':G'.$i)->applyFromArray(
						array(
							'borders' => array(
								'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$sel->getStyle('A'.$i.':G'.$i)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$i++;
					$k++;
				}
				
				$sel->setCellValue('A'.$i, $l.'.');
				$sel->setCellValue('B'.$i, trim($row->fs_refno));
				$sel->setCellValue('C'.$i, substr(trim($row->fd_refno),6,2).'-'.substr(trim($row->fd_refno),4,2).'-'.substr(trim($row->fd_refno),0,4));
				$sel->setCellValue('D'.$i, trim($row->fs_Descrp));
				$sel->setCellValue('E'.$i, number_format(trim($row->DEBET), 2, '.', ','));
				$sel->setCellValue('F'.$i, number_format(trim($row->CREDIT), 2, '.', ','));
				$sel->setCellValue('G'.$i, number_format(trim($row->BALANCE), 2, '.', ','));
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				
				$kdacno = trim($row->ACNO);
				$sel->getStyle('A'.$i.':G'.$i)->applyFromArray(
					array(
						'borders' => array(
							'bottom' => array(
								'style' => PHPExcel_Style_Border::BORDER_DOTTED
							)
						)
					)
				);
				$i++;
				$k++;
				$l++;
			}
			$j = $i - 1;
			$sel->getStyle('A'.$j.':G'.$j)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			
			$q = $p + 4;
			$arr = array(
				'borders' => array(
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$sel->getStyle('A'.$q.':A'.$j)->applyFromArray($arr);
			$sel->getStyle('B'.$q.':B'.$j)->applyFromArray($arr);
			$sel->getStyle('C'.$q.':C'.$j)->applyFromArray($arr);
			$sel->getStyle('D'.$q.':D'.$j)->applyFromArray($arr);
			$sel->getStyle('E'.$q.':E'.$j)->applyFromArray($arr);
			$sel->getStyle('F'.$q.':F'.$j)->applyFromArray($arr);
			$sel->getStyle('G'.$q.':G'.$j)->applyFromArray($arr);
			$sel->getStyle('G'.$q.':G'.$j)->applyFromArray(
				array(
					'borders' => array(
						'right' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$sel->setCellValue('D'.$i, 'T O T A L : ');
			$sel->setCellValue('E'.$i, number_format($debet, 2, '.', ','));
			$sel->setCellValue('F'.$i, number_format($kredit, 2, '.', ','));
			$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
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
			
			$hasil = array(
				'sukses'	=> true,
				'nmfile'	=> $xPath.$nmfile.'.xls'
			);
			echo json_encode($hasil);
			
		}
		else
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'No record!!
			');
			echo json_encode($hasil);
		}
	}
	
	function excel_detail()
	{
		ini_set('memory_limit', '-1');
		
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		$kdtrx = trim($this->input->post('fs_kd_trx'));
		$kdstrx = trim($this->input->post('fs_kd_strx'));
		$refno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'taccgldet';
		$nmfile = 'accgldet-'.$jamskg;
		
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
		$pjghal = 38;
		$i = 1;
		$l = 0; // no urut
		
		$k = 1; // pjg hal
		$p = 1; // awal hal
		
		$oExcel->getActiveSheet()->setTitle('Jurnal Detail Buku Besar');
		$oExcel->getActiveSheet()->setShowGridlines(false);
		
		$oExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$oExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		
		$oExcel->getActiveSheet()->getPageMargins()->setTop(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setBottom(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setLeft(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setRight(0);
		
		$oExcel->getDefaultStyle()->getFont()->setName('Calibri');
		$oExcel->getDefaultStyle()->getFont()->setSize(10);
		$sel = $oExcel->getActiveSheet();
		
		$this->load->model('mAccGL','',true);
		$ssql = $this->mAccGL->accgl_detail($kddept,$kdcount,$kdtrx,$kdstrx,$refno);
		
		if ($ssql->num_rows() > 0)
		{
			$i = 1;
			$k = 1;
			$p = $i;
			$sel->setCellValue('A'.$i, 'Jurnal Detail Laporan Buku Besar');
			$sel->mergeCells('A'.$i.':F'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$sel->getStyle('A'.$i)->getFont()->setSize(12);
			$i++;
			$k++;
			$sel->mergeCells('A'.$i.':F'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->setCellValue('A'.$i, 'Reference: '.trim($refno));
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'No');
			$sel->setCellValue('B'.$i, 'Kode');
			$sel->setCellValue('C'.$i, 'Perkiraan');
			$sel->setCellValue('D'.$i, 'Keterangan');
			$sel->setCellValue('E'.$i, 'Debet');
			$sel->setCellValue('F'.$i, 'Kredit');
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$sel->getStyle('A'.$i.':F'.$i)->applyFromArray(
				array(
					'borders' => array(
						'top' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$sel->getStyle('A'.$i.':F'.$i)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$i++;
			$k++;
			$l++;
			
			foreach ($ssql->result() as $row)
			{
				if ($k > $pjghal)
				{
					$j = $i - 1;
					$sel->setBreak('A'.$j, PHPExcel_Worksheet::BREAK_ROW);
					$sel->getStyle('A'.$j.':F'.$j)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					
					$q = $p + 3;
					$arr = array(
						'borders' => array(
							'left' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					);
					$sel->getStyle('A'.$q.':A'.$j)->applyFromArray($arr);
					$sel->getStyle('B'.$q.':B'.$j)->applyFromArray($arr);
					$sel->getStyle('C'.$q.':C'.$j)->applyFromArray($arr);
					$sel->getStyle('D'.$q.':D'.$j)->applyFromArray($arr);
					$sel->getStyle('E'.$q.':E'.$j)->applyFromArray($arr);
					$sel->getStyle('F'.$q.':F'.$j)->applyFromArray($arr);
					$sel->getStyle('F'.$q.':F'.$j)->applyFromArray(
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
					$sel->setCellValue('A'.$i, 'Jurnal Detail Laporan Buku Besar');
					$sel->mergeCells('A'.$i.':F'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$i++;
					$k++;
					$sel->mergeCells('A'.$i.':F'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->setCellValue('A'.$i, 'Reference: '.trim($refno));
					$i = $i + 2;
					$k = $k + 2;
					
					$sel->setCellValue('A'.$i, 'No');
					$sel->setCellValue('B'.$i, 'Kode');
					$sel->setCellValue('C'.$i, 'Perkiraan');
					$sel->setCellValue('D'.$i, 'Keterangan');
					$sel->setCellValue('E'.$i, 'Debet');
					$sel->setCellValue('F'.$i, 'Kredit');
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					$sel->getStyle('A'.$i.':F'.$i)->applyFromArray(
						array(
							'borders' => array(
								'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$sel->getStyle('A'.$i.':F'.$i)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$q = $i;
					$i++;
					$k++;
				}
				
				$sel->setCellValue('A'.$i, $l.'.');
				$sel->setCellValue('B'.$i, trim($row->fs_kd_acc));
				$sel->setCellValue('C'.$i, trim($row->fs_nm_acc));
				$sel->setCellValue('D'.$i, trim($row->fs_note));
				$sel->setCellValue('E'.$i, number_format(trim($row->fn_debet), 2, '.', ','));
				$sel->setCellValue('F'.$i, number_format(trim($row->fn_credit), 2, '.', ','));
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$i++;
				$k++;
				$l++;
			}
			$j = $i - 1;
			$sel->getStyle('A'.$j.':F'.$j)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			
			$q = $p + 3;
			$arr = array(
				'borders' => array(
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$sel->getStyle('A'.$q.':A'.$j)->applyFromArray($arr);
			$sel->getStyle('B'.$q.':B'.$j)->applyFromArray($arr);
			$sel->getStyle('C'.$q.':C'.$j)->applyFromArray($arr);
			$sel->getStyle('D'.$q.':D'.$j)->applyFromArray($arr);
			$sel->getStyle('E'.$q.':E'.$j)->applyFromArray($arr);
			$sel->getStyle('F'.$q.':F'.$j)->applyFromArray($arr);
			$sel->getStyle('F'.$q.':F'.$j)->applyFromArray(
				array(
					'borders' => array(
						'right' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			
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
			
			$hasil = array(
				'sukses'	=> true,
				'nmfile'	=> $xPath.$nmfile.'.xls'
			);
			echo json_encode($hasil);
			
		}
		else
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'No record!!
			');
			echo json_encode($hasil);
		}
	}
}
?>