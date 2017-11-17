<?php

class InfoTerimaKas extends CI_Controller
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
			$this->load->view('vinfoterimakas');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function cb_trs()
	{
		$strx = trim($this->input->post('fs_kd_strx'));
		$nstart = trim($this->input->post('start'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->cbtrs_type2_all($strx);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->cbtrs_type2($strx,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function printkas()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tinfoterimakas';
		$nmfile = 'infoterimakas-'.$jamskg;
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
		$oExcel->getActiveSheet()->setTitle('InfoTerimaKas');
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
		
		$kdtrx = trim($this->input->post('fs_kd_trx'));
		$kdstrx = trim($this->input->post('fs_kd_strx'));
		$xtgl = trim($this->input->post('fd_refno'));
		$xtgl2 = trim($this->input->post('fd_refno2'));
		$tgl = trim($this->input->post('fd_tgl'));
		$tgl2 = trim($this->input->post('fd_tgl2'));
		
		$this->load->model('mInfoTerimaKas','',true);
		$ssql = $this->mInfoTerimaKas->terima_kas($kdtrx,$kdstrx,$xtgl,$xtgl2);
		
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
			$sel->setCellValue('A'.$i, 'Penerimaan Kas');
			$sel->mergeCells('A'.$i.':E'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$sel->getStyle('A'.$i)->getFont()->setSize(12);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Periode: '.trim($tgl).' s/d '.trim($tgl2));
			$sel->mergeCells('A'.$i.':E'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'No');
			$sel->setCellValue('B'.$i, 'Ref. No');
			$sel->setCellValue('C'.$i, 'Tanggal');
			$sel->setCellValue('D'.$i, 'Keterangan');
			$sel->setCellValue('E'.$i, 'Nilai');
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$sel->getStyle('A'.$i.':E'.$i)->applyFromArray(
				array(
					'borders' => array(
						'top' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$sel->getStyle('A'.$i.':E'.$i)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			// $sel->getStyle('A'.$i.':'.'N'.$i)->applyFromArray($warnakolom);
			$i++;
			$k++;
			$l++;
			
			$NmTrx = '';
			$total = 0;
			$gtotal = 0;
			foreach ($ssql->result() as $row)
			{
				if (trim($row->fs_nm_trx) <> '' and trim($NmTrx) <> trim($row->fs_nm_trx))
				{
					if ($k > $pjghal)
					{
						$j = $i - 1;
						$sel->setBreak('A'.$j, PHPExcel_Worksheet::BREAK_ROW);
						$sel->getStyle('A'.$j.':E'.$j)->applyFromArray(
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
						$sel->getStyle('E'.$q.':E'.$j)->applyFromArray(
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
						$sel->setCellValue('A'.$i, 'Penerimaan Kas');
						$sel->mergeCells('A'.$i.':D'.$i);
						$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
						$sel->getStyle('A'.$i)->getFont()->setBold(true);
						$sel->getStyle('A'.$i)->getFont()->setSize(12);
						$i++;
						$k++;
						
						$sel->setCellValue('A'.$i, 'Periode: '.trim($tgl).' s/d '.trim($tgl2));
						$sel->mergeCells('A'.$i.':D'.$i);
						$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$i = $i + 2;
						$k = $k + 2;
						
						$sel->setCellValue('A'.$i, 'No');
						$sel->setCellValue('B'.$i, 'Ref. No');
						$sel->setCellValue('C'.$i, 'Tanggal');
						$sel->setCellValue('D'.$i, 'Keterangan');
						$sel->setCellValue('E'.$i, 'Nilai');
						$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						
						$sel->getStyle('A'.$i.':E'.$i)->applyFromArray(
							array(
								'borders' => array(
									'top' => array(
										'style' => PHPExcel_Style_Border::BORDER_THIN
									)
								)
							)
						);
						$sel->getStyle('A'.$i.':E'.$i)->applyFromArray(
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
					
					if (trim($NmTrx) <> '')
					{
						$sel->setCellValue('A'.$i, 'Total '.trim($NmTrx).' : '.number_format($total));
						$sel->mergeCells('A'.$i.':E'.$i);
						$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$sel->getStyle('A'.$i.':E'.$i)->applyFromArray(
							array(
								'borders' => array(
									'top' => array(
										'style' => PHPExcel_Style_Border::BORDER_THIN
									)
								)
							)
						);
						$total = 0;
						$i++;
						$k++;
					}
					
					$sel->setCellValue('A'.$i, 'Tipe Transaksi : '.trim($row->fs_nm_trx));
					$sel->mergeCells('A'.$i.':E'.$i);
					
					$sel->getStyle('A'.$i.':E'.$i)->applyFromArray(
						array(
							'borders' => array(
								'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$sel->getStyle('A'.$i.':E'.$i)->applyFromArray(
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
					$l = 1;
				}
				
				if ($k > $pjghal)
				{
					$j = $i - 1;
					$sel->setBreak('A'.$j, PHPExcel_Worksheet::BREAK_ROW);
					$sel->getStyle('A'.$j.':E'.$j)->applyFromArray(
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
					$sel->getStyle('E'.$q.':E'.$j)->applyFromArray(
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
					$sel->setCellValue('A'.$i, 'Penerimaan Kas');
					$sel->mergeCells('A'.$i.':D'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
					$sel->getStyle('A'.$i)->getFont()->setBold(true);
					$sel->getStyle('A'.$i)->getFont()->setSize(12);
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i, 'Periode: '.trim($tgl).' s/d '.trim($tgl2));
					$sel->mergeCells('A'.$i.':D'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$i = $i + 2;
					$k = $k + 2;
					
					$sel->setCellValue('A'.$i, 'No');
					$sel->setCellValue('B'.$i, 'Ref. No');
					$sel->setCellValue('C'.$i, 'Tanggal');
					$sel->setCellValue('D'.$i, 'Keterangan');
					$sel->setCellValue('E'.$i, 'Nilai');
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					
					$sel->getStyle('A'.$i.':E'.$i)->applyFromArray(
						array(
							'borders' => array(
								'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$sel->getStyle('A'.$i.':E'.$i)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					// $sel->getStyle('A'.$i.':'.'N'.$i)->applyFromArray($warnakolom);
					$q = $i;
					$i++;
					$k++;
				}
				
				$sel->setCellValue('A'.$i, trim($l));
				$sel->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				// $sel->getStyle('A'.$i)->getAlignment()->setWrapText(true);
				
				$sel->setCellValue('B'.$i, trim($row->fs_refno));
				$sel->getStyle('B'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				// $sel->getStyle('B'.$i)->getAlignment()->setWrapText(true);
				
				$sel->setCellValue('C'.$i, trim($row->fd_refno));
				$sel->getStyle('C'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				// $sel->getStyle('C'.$i)->getAlignment()->setWrapText(true);
				
				$sel->setCellValue('D'.$i, trim($row->fs_ket));
				$sel->getStyle('D'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				// $sel->getStyle('D'.$i)->getAlignment()->setWrapText(true);
				
				$sel->setCellValue('E'.$i, number_format(trim($row->fn_total)));
				$sel->getStyle('E'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				// $sel->getStyle('E'.$i)->getAlignment()->setWrapText(true);
				
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$total = $total + trim($row->fn_total);
				$gtotal = $gtotal + trim($row->fn_total);
				
				if ($k % 2 <> 0) {
					// $sel->getStyle('A'.$i.':'.'E'.$i)->applyFromArray($warnakolom);
				}
				$i++;
				$k++;
				$l++;
				
				$NmTrx = trim($row->fs_nm_trx);
			}
			
			$sel->setCellValue('A'.$i, 'Total '.trim($NmTrx).' : '.number_format($total));
			$sel->mergeCells('A'.$i.':E'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('A'.$i.':E'.$i)->applyFromArray(
				array(
					'borders' => array(
						'top' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Grand Total : '.number_format($gtotal));
			$sel->mergeCells('A'.$i.':E'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('A'.$i.':E'.$i)->applyFromArray(
				array(
					'borders' => array(
						'top' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$i++;
			$k++;
			
			$j = $i - 1;
			$sel->getStyle('A'.$j.':E'.$j)->applyFromArray(
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
			$sel->getStyle('E'.$q.':E'.$j)->applyFromArray(
				array(
					'borders' => array(
						'right' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			// foreach(range('A','E') as $idkolom)
			// {
				// $sel->getColumnDimension($idkolom)->setAutoSize(true);
			// }
			
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