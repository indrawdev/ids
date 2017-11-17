<?php

class Barcode extends CI_Controller
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
			$this->load->view('vbarcode');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function gridrangka()
	{
		$kdproduk = trim($this->input->post('fs_kd_product'));
		
		$this->load->model('mBarcode','',true);
		$ssql = $this->mBarcode->gridrangka('',$kdproduk,'');
		
		echo json_encode($ssql->result());
	}
	
	function printbarcode()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tbarcode';
		$nmfile = 'barcode-'.$jamskg;
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
		
		$oExcel->getActiveSheet()->setTitle('Barcode');
		$oExcel->getActiveSheet()->setShowGridlines(false);
		
		$oExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$oExcel->getActiveSheet()->getPageMargins()->setTop(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setBottom(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setLeft(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setRight(0);
		// $oExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . '&RPage &P of &N');
		$oExcel->getDefaultStyle()->getFont()->setName('Calibri');
		$oExcel->getDefaultStyle()->getFont()->setSize(12);
		
		$sel = $oExcel->getActiveSheet();
		
		$kdwh = trim($this->input->post('fs_kd_wh'));
		$kdprod = trim($this->input->post('fs_kd_product'));
		$kdrangka = trim($this->input->post('fs_rangka'));
		
		$this->load->model('mBarcode','',true);
		$ssql = $this->mBarcode->gridrangka($kdwh,$kdprod,$kdrangka);
		
		$nmwh = '';
		$nmprod0 = '';
		$nmprod = '';
		$pjghal = 57;
		$i = 1;
		$l = 0; // no urut
		
		$k = 1; // pjg hal
		$p = 1; // awal hal
		
		$n = 1;
		$r = 1;
		if ($ssql->num_rows() > 0)
		{
			//loop
			foreach ($ssql->result() as $row)
			{
				if ($k > $pjghal)
				{
					if ($n % 2 == 0)
					{
						$r = $i;
						$j = $i - 1;
						$sel->setBreak('A'.$j, PHPExcel_Worksheet::BREAK_ROW);
					}
					$k = 1;
					$n++;
				}
				
				if ($n % 2 <> 0)
				{
					$sel->setCellValue('B'.$i, 'TYPE');
					$sel->setCellValue('C'.$i, ':');
					$sel->setCellValue('D'.$i, trim($row->fs_nm_product));
					$sel->getStyle('B'.$i)->getFont()->setBold(true);
					$sel->getStyle('C'.$i)->getFont()->setBold(true);
					$sel->getStyle('D'.$i)->getFont()->setBold(true);
					$sel->getStyle('A'.$i.':E'.$i)->applyFromArray(
						array(
							'borders' => array(
								'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THICK
								)
							)
						)
					);
					$i++;
					$k++;
					
					$sel->setCellValue('B'.$i, 'CYLINDER');
					$sel->setCellValue('C'.$i, ':');
					$sel->setCellValue('D'.$i, trim($row->fn_silinder));
					$sel->getStyle('B'.$i)->getFont()->setBold(true);
					$sel->getStyle('C'.$i)->getFont()->setBold(true);
					$sel->getStyle('D'.$i)->getFont()->setBold(true);
					$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$i++;
					$k++;
					
					$sel->setCellValue('B'.$i, 'COLOR');
					$sel->setCellValue('C'.$i, ':');
					$sel->setCellValue('D'.$i, trim($row->fs_nm_warna));
					$sel->getStyle('B'.$i)->getFont()->setBold(true);
					$sel->getStyle('C'.$i)->getFont()->setBold(true);
					$sel->getStyle('D'.$i)->getFont()->setBold(true);
					$i = $i + 2;
					$k = $k + 2;
					
					$sel->setCellValue('B'.$i, 'CHASSIS');
					$sel->getStyle('B'.$i)->getFont()->setBold(true);
					$i++;
					$k++;
					$sel->setCellValue('B'.$i, trim($row->fs_rangka));
					$sel->getStyle('B'.$i)->getFont()->setBold(true);
					$i = $i + 2;
					$k = $k + 2;
					
					$sel->setCellValue('B'.$i, 'MACHINE');
					$sel->getStyle('B'.$i)->getFont()->setBold(true);
					$i++;
					$k++;
					$sel->setCellValue('B'.$i, trim($row->fs_mesin));
					$sel->getStyle('B'.$i)->getFont()->setBold(true);
					$sel->getStyle('A'.$i.':E'.$i)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THICK
								)
							)
						)
					);
					
					$q = $i - 8;
					$sel->getStyle('A'.$q.':A'.$i)->applyFromArray(
						array(
							'borders' => array(
								'left' => array(
									'style' => PHPExcel_Style_Border::BORDER_THICK
								)
							)
						)
					);
					$sel->getStyle('E'.$q.':E'.$i)->applyFromArray(
						array(
							'borders' => array(
								'right' => array(
									'style' => PHPExcel_Style_Border::BORDER_THICK
								)
							)
						)
					);
					
					$i = $i + 2;
					$k = $k + 2;
				}
				else
				{
					$sel->setCellValue('H'.$r, 'TYPE');
					$sel->setCellValue('I'.$r, ':');
					$sel->setCellValue('J'.$r, trim($row->fs_nm_product));
					$sel->getStyle('H'.$r)->getFont()->setBold(true);
					$sel->getStyle('I'.$r)->getFont()->setBold(true);
					$sel->getStyle('J'.$r)->getFont()->setBold(true);
					$sel->getStyle('G'.$r.':K'.$r)->applyFromArray(
						array(
							'borders' => array(
								'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THICK
								)
							)
						)
					);
					$k++;
					$r++;
					
					$sel->setCellValue('H'.$r, 'CYLINDER');
					$sel->setCellValue('I'.$r, ':');
					$sel->setCellValue('J'.$r, trim($row->fn_silinder));
					$sel->getStyle('H'.$r)->getFont()->setBold(true);
					$sel->getStyle('I'.$r)->getFont()->setBold(true);
					$sel->getStyle('J'.$r)->getFont()->setBold(true);
					$sel->getStyle('J'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$k++;
					$r++;
					
					$sel->setCellValue('H'.$r, 'COLOR');
					$sel->setCellValue('I'.$r, ':');
					$sel->setCellValue('J'.$r, trim($row->fs_nm_warna));
					$sel->getStyle('H'.$r)->getFont()->setBold(true);
					$sel->getStyle('I'.$r)->getFont()->setBold(true);
					$sel->getStyle('J'.$r)->getFont()->setBold(true);
					$k = $k + 2;
					$r = $r + 2;
					
					$sel->setCellValue('H'.$r, 'CHASSIS');
					$sel->getStyle('H'.$r)->getFont()->setBold(true);
					$k++;
					$r++;
					$sel->setCellValue('H'.$r, trim($row->fs_rangka));
					$sel->getStyle('H'.$r)->getFont()->setBold(true);
					$k = $k + 2;
					$r = $r + 2;
					
					$sel->setCellValue('H'.$r, 'MACHINE');
					$sel->getStyle('H'.$r)->getFont()->setBold(true);
					$k++;
					$r++;
					$sel->setCellValue('H'.$r, trim($row->fs_mesin));
					$sel->getStyle('H'.$r)->getFont()->setBold(true);
					$sel->getStyle('G'.$r.':K'.$r)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THICK
								)
							)
						)
					);
					
					$q = $r - 8;
					$sel->getStyle('G'.$q.':G'.$r)->applyFromArray(
						array(
							'borders' => array(
								'left' => array(
									'style' => PHPExcel_Style_Border::BORDER_THICK
								)
							)
						)
					);
					$sel->getStyle('K'.$q.':K'.$r)->applyFromArray(
						array(
							'borders' => array(
								'right' => array(
									'style' => PHPExcel_Style_Border::BORDER_THICK
								)
							)
						)
					);
					
					$r = $r + 2;
					$k = $k + 2;
				}
			}
			//eof loop
			
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
				'nmfile'	=> $xPath.$nmfile.'.pdf',
				'nmfilexls'	=> $xPath.$nmfile.'.xls'
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