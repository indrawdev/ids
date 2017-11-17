<?php

class InfoAgingCust extends CI_Controller
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
			$this->load->view('vinfoagingcust');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function excel_aging()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tagingcust';
		$nmfile = 'agingcust-'.$jamskg;
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
		$oExcel->getActiveSheet()->setTitle('AgingCustomer');
		$oExcel->getActiveSheet()->setShowGridlines(false);
		
		$oExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$oExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$oExcel->getActiveSheet()->getPageMargins()->setTop(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setBottom(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setLeft(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setRight(0);
		// $oExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . '&RPage &P of &N');
		$oExcel->getDefaultStyle()->getFont()->setName('Calibri');
		$oExcel->getDefaultStyle()->getFont()->setSize(10);
		
		$sel = $oExcel->getActiveSheet();
		
		$xtgl = trim($this->input->post('fd_refno'));
		$xtgl2 = trim($this->input->post('fd_refno2'));
		$tgl = trim($this->input->post('fd_tgl'));
		$tgl2 = trim($this->input->post('fd_tgl2'));
		
		$this->load->model('mInfoAgingCust','',true);
		$ssql = $this->mInfoAgingCust->aging_cust($xtgl,$xtgl2);
		
		if ($ssql->num_rows() > 0)
		{
			$warnakolom = array(
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'fff8dc')
				)
			);
			
			$i = 1;
			$k = 1;
			$p = $i;
			$sel->setCellValue('A'.$i, 'Aging Customer');
			$sel->mergeCells('A'.$i.':T'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$sel->getStyle('A'.$i)->getFont()->setSize(12);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Periode Penjualan: '.trim($tgl).' s/d '.trim($tgl2));
			$sel->mergeCells('A'.$i.':T'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'No');
			$sel->setCellValue('B'.$i, 'Ref. No');
			$sel->setCellValue('C'.$i, 'Konsumen');
			$sel->setCellValue('D'.$i, 'Tgl Jual');
			$sel->setCellValue('E'.$i, 'Total');
			$sel->setCellValue('F'.$i, 'Sisa Bayar');
			$sel->setCellValue('G'.$i, 'Tempo');
			$sel->setCellValue('H'.$i, 'Jatuh Tempo');
			$sel->setCellValue('I'.$i, 'Bulan Ke-1');
			$sel->setCellValue('J'.$i, 'Bulan Ke-2');
			$sel->setCellValue('K'.$i, 'Bulan Ke-3');
			$sel->setCellValue('L'.$i, 'Bulan Ke-4');
			$sel->setCellValue('M'.$i, 'Bulan Ke-5');
			$sel->setCellValue('N'.$i, 'Bulan Ke-6');
			$sel->setCellValue('O'.$i, 'Bulan Ke-7');
			$sel->setCellValue('P'.$i, 'Bulan Ke-8');
			$sel->setCellValue('Q'.$i, 'Bulan Ke-9');
			$sel->setCellValue('R'.$i, 'Bulan Ke-10');
			$sel->setCellValue('S'.$i, 'Bulan Ke-11');
			$sel->setCellValue('T'.$i, 'Bulan Ke-12');
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('M'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('N'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('P'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('Q'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('R'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('S'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('T'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$sel->getStyle('A'.$i.':T'.$i)->applyFromArray(
				array(
					'borders' => array(
						'top' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$sel->getStyle('A'.$i.':T'.$i)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$sel->getStyle('A'.$i.':'.'T'.$i)->applyFromArray($warnakolom);
			$i++;
			$k++;
			$l++;
			
			foreach ($ssql->result() as $row)
			{/*
				if ($k > $pjghal)
				{
					$j = $i - 1;
					$sel->setBreak('A'.$j, PHPExcel_Worksheet::BREAK_ROW);
					$sel->getStyle('A'.$j.':T'.$j)->applyFromArray(
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
					$sel->getStyle('G'.$q.':G'.$j)->applyFromArray($arr);
					$sel->getStyle('H'.$q.':H'.$j)->applyFromArray($arr);
					$sel->getStyle('I'.$q.':I'.$j)->applyFromArray($arr);
					$sel->getStyle('J'.$q.':J'.$j)->applyFromArray($arr);
					$sel->getStyle('K'.$q.':K'.$j)->applyFromArray($arr);
					$sel->getStyle('L'.$q.':L'.$j)->applyFromArray($arr);
					$sel->getStyle('M'.$q.':M'.$j)->applyFromArray($arr);
					$sel->getStyle('N'.$q.':N'.$j)->applyFromArray($arr);
					$sel->getStyle('O'.$q.':O'.$j)->applyFromArray($arr);
					$sel->getStyle('P'.$q.':P'.$j)->applyFromArray($arr);
					$sel->getStyle('Q'.$q.':Q'.$j)->applyFromArray($arr);
					$sel->getStyle('R'.$q.':R'.$j)->applyFromArray($arr);
					$sel->getStyle('S'.$q.':S'.$j)->applyFromArray($arr);
					$sel->getStyle('T'.$q.':T'.$j)->applyFromArray($arr);
					$sel->getStyle('T'.$q.':T'.$j)->applyFromArray(
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
					$sel->setCellValue('A'.$i, 'Aging Customer');
					$sel->mergeCells('A'.$i.':T'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
					$sel->getStyle('A'.$i)->getFont()->setBold(true);
					$sel->getStyle('A'.$i)->getFont()->setSize(12);
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i, 'Periode Penjualan: '.trim($tgl).' s/d '.trim($tgl2));
					$sel->mergeCells('A'.$i.':T'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$i = $i + 2;
					$k = $k + 2;
					
					$sel->setCellValue('A'.$i, 'No');
					$sel->setCellValue('B'.$i, 'Ref. No');
					$sel->setCellValue('C'.$i, 'Konsumen');
					$sel->setCellValue('D'.$i, 'Tgl Jual');
					$sel->setCellValue('E'.$i, 'Total');
					$sel->setCellValue('F'.$i, 'Sisa Bayar');
					$sel->setCellValue('G'.$i, 'Tempo');
					$sel->setCellValue('H'.$i, 'Jatuh Tempo');
					$sel->setCellValue('I'.$i, 'Bulan Ke-1');
					$sel->setCellValue('J'.$i, 'Bulan Ke-2');
					$sel->setCellValue('K'.$i, 'Bulan Ke-3');
					$sel->setCellValue('L'.$i, 'Bulan Ke-4');
					$sel->setCellValue('M'.$i, 'Bulan Ke-5');
					$sel->setCellValue('N'.$i, 'Bulan Ke-6');
					$sel->setCellValue('O'.$i, 'Bulan Ke-7');
					$sel->setCellValue('P'.$i, 'Bulan Ke-8');
					$sel->setCellValue('Q'.$i, 'Bulan Ke-9');
					$sel->setCellValue('R'.$i, 'Bulan Ke-10');
					$sel->setCellValue('S'.$i, 'Bulan Ke-11');
					$sel->setCellValue('T'.$i, 'Bulan Ke-12');
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('M'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('N'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('P'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('Q'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('R'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('S'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('T'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					$sel->getStyle('A'.$i.':T'.$i)->applyFromArray(
						array(
							'borders' => array(
								'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$sel->getStyle('A'.$i.':T'.$i)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$sel->getStyle('A'.$i.':'.'T'.$i)->applyFromArray($warnakolom);
					$q = $i;
					$i++;
					$k++;
				}*/
				
				$sel->setCellValue('A'.$i, trim($l));
				$sel->setCellValue('B'.$i, trim($row->fs_refno));
				$sel->setCellValue('C'.$i, trim($row->fs_nm_cust));
				$sel->setCellValue('D'.$i, trim($row->fd_refno));
				$sel->setCellValue('E'.$i, number_format(trim($row->fn_total)));
				$sel->setCellValue('F'.$i, number_format(trim($row->fn_sisa)));
				$sel->setCellValue('G'.$i, trim($row->fs_nm_term).' Bulan');
				$sel->setCellValue('H'.$i, trim($row->fd_tgl_bayar));
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$sel->setCellValue('I'.$i, trim($row->fd_bulan1));
				$sel->setCellValue('J'.$i, trim($row->fd_bulan2));
				$sel->setCellValue('K'.$i, trim($row->fd_bulan3));
				$sel->setCellValue('L'.$i, trim($row->fd_bulan4));
				$sel->setCellValue('M'.$i, trim($row->fd_bulan5));
				$sel->setCellValue('N'.$i, trim($row->fd_bulan6));
				$sel->setCellValue('O'.$i, trim($row->fd_bulan7));
				$sel->setCellValue('P'.$i, trim($row->fd_bulan8));
				$sel->setCellValue('Q'.$i, trim($row->fd_bulan9));
				$sel->setCellValue('R'.$i, trim($row->fd_bulan10));
				$sel->setCellValue('S'.$i, trim($row->fd_bulan11));
				$sel->setCellValue('T'.$i, trim($row->fd_bulan12));
				$i++;
				$k++;
				
				$xnilai = '';
				if (trim($row->fn_bayar1) <> 0)
				{
					$xnilai = number_format(trim($row->fn_bayar1));
				}
				$sel->setCellValue('I'.$i, $xnilai);
				
				$xnilai = '';
				if (trim($row->fn_bayar2) <> 0)
				{
					$xnilai = number_format(trim($row->fn_bayar2));
				}
				$sel->setCellValue('J'.$i, $xnilai);
				
				$xnilai = '';
				if (trim($row->fn_bayar3) <> 0)
				{
					$xnilai = number_format(trim($row->fn_bayar3));
				}
				$sel->setCellValue('K'.$i, $xnilai);
				
				$xnilai = '';
				if (trim($row->fn_bayar4) <> 0)
				{
					$xnilai = number_format(trim($row->fn_bayar4));
				}
				$sel->setCellValue('L'.$i, $xnilai);
				
				$xnilai = '';
				if (trim($row->fn_bayar5) <> 0)
				{
					$xnilai = number_format(trim($row->fn_bayar5));
				}
				$sel->setCellValue('M'.$i, $xnilai);
				
				$xnilai = '';
				if (trim($row->fn_bayar6) <> 0)
				{
					$xnilai = number_format(trim($row->fn_bayar6));
				}
				$sel->setCellValue('N'.$i, $xnilai);
				
				$xnilai = '';
				if (trim($row->fn_bayar7) <> 0)
				{
					$xnilai = number_format(trim($row->fn_bayar7));
				}
				$sel->setCellValue('O'.$i, $xnilai);
				
				$xnilai = '';
				if (trim($row->fn_bayar8) <> 0)
				{
					$xnilai = number_format(trim($row->fn_bayar8));
				}
				$sel->setCellValue('P'.$i, $xnilai);
				
				$xnilai = '';
				if (trim($row->fn_bayar9) <> 0)
				{
					$xnilai = number_format(trim($row->fn_bayar9));
				}
				$sel->setCellValue('Q'.$i, $xnilai);
				
				$xnilai = '';
				if (trim($row->fn_bayar10) <> 0)
				{
					$xnilai = number_format(trim($row->fn_bayar10));
				}
				$sel->setCellValue('R'.$i, $xnilai);
				
				$xnilai = '';
				if (trim($row->fn_bayar11) <> 0)
				{
					$xnilai = number_format(trim($row->fn_bayar11));
				}
				$sel->setCellValue('S'.$i, $xnilai);
				
				$xnilai = '';
				if (trim($row->fn_bayar12) <> 0)
				{
					$xnilai = number_format(trim($row->fn_bayar12));
				}
				$sel->setCellValue('T'.$i, $xnilai);
				$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('M'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('N'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('P'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('Q'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('R'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('S'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('T'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				
				$sel->getStyle('A'.$i.':T'.$i)->applyFromArray(
					array(
						'borders' => array(
							'bottom' => array(
								'style' => PHPExcel_Style_Border::BORDER_DOTTED
							)
						)
					)
				);
				
				$j = $i - 1;
				if ($k % 4 == 0)
				{
					$sel->getStyle('A'.$j.':'.'T'.$i)->applyFromArray($warnakolom);
				}
				$i++;
				$k++;
				$l++;
			}
			$j = $i - 1;
			$sel->getStyle('A'.$j.':T'.$j)->applyFromArray(
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
			$sel->getStyle('G'.$q.':G'.$j)->applyFromArray($arr);
			$sel->getStyle('H'.$q.':H'.$j)->applyFromArray($arr);
			$sel->getStyle('I'.$q.':I'.$j)->applyFromArray($arr);
			$sel->getStyle('J'.$q.':J'.$j)->applyFromArray($arr);
			$sel->getStyle('K'.$q.':K'.$j)->applyFromArray($arr);
			$sel->getStyle('L'.$q.':L'.$j)->applyFromArray($arr);
			$sel->getStyle('M'.$q.':M'.$j)->applyFromArray($arr);
			$sel->getStyle('N'.$q.':N'.$j)->applyFromArray($arr);
			$sel->getStyle('O'.$q.':O'.$j)->applyFromArray($arr);
			$sel->getStyle('P'.$q.':P'.$j)->applyFromArray($arr);
			$sel->getStyle('Q'.$q.':Q'.$j)->applyFromArray($arr);
			$sel->getStyle('R'.$q.':R'.$j)->applyFromArray($arr);
			$sel->getStyle('S'.$q.':S'.$j)->applyFromArray($arr);
			$sel->getStyle('T'.$q.':T'.$j)->applyFromArray($arr);
			$sel->getStyle('T'.$q.':T'.$j)->applyFromArray(
				array(
					'borders' => array(
						'right' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			foreach(range('A','T') as $idkolom)
			{
				$sel->getColumnDimension($idkolom)->setAutoSize(true);
			}
			
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