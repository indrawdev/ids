<?php

class InfoKasir extends CI_Controller
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
			$this->load->view('vinfokasir');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function printkasir()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tinfokasir';
		$nmfile = 'infokasir-'.$jamskg;
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
		
		$oExcel->getActiveSheet()->setTitle('InfoKasir');
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
		$tgl = trim($this->input->post('fd_tgl'));
		$tgl2 = trim($this->input->post('fd_tgl2'));
		$refnodt = trim($this->input->post('fd_refno'));
		$refnodt2 = trim($this->input->post('fd_refno2'));
		$cekin = trim($this->input->post('fb_cekin'));
		$cekout = trim($this->input->post('fb_cekout'));
		$cekbank = trim($this->input->post('fb_setor'));
		$cust = trim($this->input->post('fs_cust'));
		
		if (trim($cekin) == 'true')
		{
			$cekin = 1;
		}
		else
		{
			$cekin = 0;
		}
		
		if (trim($cekout) == 'true')
		{
			$cekout = 1;
		}
		else
		{
			$cekout = 0;
		}
		
		if (trim($cekbank) == 'true')
		{
			$cekbank = 1;
		}
		else
		{
			$cekbank = 0;
		}
		
		$this->load->model('mInfoKasir','',true);
		$ssql = $this->mInfoKasir->kasir($refnodt,$refnodt2,$refno,$cust,$cekin,$cekout,$cekbank);
		
		$nmwh = '';
		$nmprod0 = '';
		$nmprod = '';
		$pjghal = 57;
		$i = 1;
		$l = 0; // no urut
		
		$k = 1; // pjg hal
		$p = 1; // awal hal
		$sel->setCellValue('A'.$i, 'LAPORAN KASIR');
		$oExcel->getActiveSheet()->mergeCells('A'.$i.':F'.$i);
		$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$i++;
		$k++;
		
		if (trim($tgl) == trim($tgl2))
		{
			$sel->setCellValue('A'.$i, 'Tanggal : '.$tgl);
		}
		else
		{
			$sel->setCellValue('A'.$i, 'Tanggal : '.$tgl.' s/d '.$tgl2);
		}
		$oExcel->getActiveSheet()->mergeCells('A'.$i.':F'.$i);
		$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$i++;
		$k++;
		
		$sel->setCellValue('A'.$i, ' ');
		$i++;
		$k++;
		
		if ($ssql->num_rows() > 0)
		{
			$l++;
			$sel->setCellValue('A'.$i, 'No.');
			$sel->setCellValue('B'.$i, 'Referensi');
			$sel->setCellValue('C'.$i, 'Customer');
			$sel->setCellValue('D'.$i, 'Keterangan');
			$sel->setCellValue('E'.$i, 'IN');
			$sel->setCellValue('F'.$i, 'OUT');
			$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$oExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray(
				array(
					'borders' => array(
						'top' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$oExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$p = $i;
			$i++;
			$k++;
			
			
			//loop
			$totalIn = 0;
			$totalOut = 0;
			foreach ($ssql->result() as $row)
			{
				if ($k > $pjghal)
				{
					$j = $i - 1;
					$oExcel->getActiveSheet()->setBreak('A'.$j, PHPExcel_Worksheet::BREAK_ROW);
					
					$oExcel->getActiveSheet()->getStyle('A'.$j.':F'.$j)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					
					$k = 1;
					$sel->setCellValue('A'.$i, 'No.');
					$sel->setCellValue('B'.$i, 'Referensi');
					$sel->setCellValue('C'.$i, 'Customer');
					$sel->setCellValue('D'.$i, 'Keterangan');
					$sel->setCellValue('E'.$i, 'IN');
					$sel->setCellValue('F'.$i, 'OUT');
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					$oExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray(
						array(
							'borders' => array(
								'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$oExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					
					$q = $p;
					$arr = array(
						'borders' => array(
							'left' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					);
					$oExcel->getActiveSheet()->getStyle('A'.$q.':A'.$j)->applyFromArray($arr);
					$oExcel->getActiveSheet()->getStyle('B'.$q.':B'.$j)->applyFromArray($arr);
					$oExcel->getActiveSheet()->getStyle('C'.$q.':C'.$j)->applyFromArray($arr);
					$oExcel->getActiveSheet()->getStyle('D'.$q.':D'.$j)->applyFromArray($arr);
					$oExcel->getActiveSheet()->getStyle('E'.$q.':E'.$j)->applyFromArray($arr);
					$oExcel->getActiveSheet()->getStyle('F'.$q.':F'.$j)->applyFromArray($arr);
					$oExcel->getActiveSheet()->getStyle('F'.$q.':F'.$j)->applyFromArray(
						array(
							'borders' => array(
								'right' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$p = $i;
					$i++;
					$k++;
				}
				
				$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$oExcel->getActiveSheet()->getStyle('A'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				$sel->setCellValue('A'.$i, $l.'.');
				
				$sel->setCellValue('B'.$i, trim($row->fs_refno));
				$sel->setCellValue('C'.$i, trim($row->fs_cust));
				$sel->setCellValue('D'.$i, trim($row->fs_note));
				
				if (trim($row->fb_in) == '1')
				{
					$sel->setCellValue('E'.$i, number_format($row->fn_total,2,',','.'));
					$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$totalIn = $totalIn + $row->fn_total;
				}
				else
				{
					$sel->setCellValue('F'.$i, number_format($row->fn_total,2,',','.'));
					$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$totalOut = $totalOut + $row->fn_total;
				}
				$i++;
				$k++;
				$l++;
			}
			//eof loop
			
			$j = $i - 1;
			$oExcel->getActiveSheet()->getStyle('A'.$j.':F'.$j)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			
			$q = $p;
			$arr = array(
				'borders' => array(
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$oExcel->getActiveSheet()->getStyle('A'.$q.':A'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('B'.$q.':B'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('C'.$q.':C'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('D'.$q.':D'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('E'.$q.':E'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('F'.$q.':F'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('F'.$q.':F'.$j)->applyFromArray(
				array(
					'borders' => array(
						'right' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			
			$sel->setCellValue('D'.$i, 'Total');
			$sel->setCellValue('E'.$i, number_format($totalIn,2,',','.'));
			$sel->setCellValue('F'.$i, number_format($totalOut,2,',','.'));
			$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$arr = array(
				'borders' => array(
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$oExcel->getActiveSheet()->getStyle('E'.$q.':E'.$i)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('F'.$q.':F'.$i)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('F'.$q.':F'.$i)->applyFromArray(
				array(
					'borders' => array(
						'right' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			
			$oExcel->getActiveSheet()->getStyle('E'.$i.':F'.$i)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
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