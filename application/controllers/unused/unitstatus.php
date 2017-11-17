<?php

class Unitstatus extends CI_Controller
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
			$this->load->view('vunitstatus');
		}
		else
		{
			redirect('login','site_url');
		}
	}
	
	function warna()
	{
		$nstart = trim($this->input->post('start'));
		$kdwarna = trim($this->input->post('fs_nm_warna'));
		$nmwarna = trim($this->input->post('fs_nm_warna'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->color_all($kdwarna,$nmwarna);
		$total = $ssql->num_rows();
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->color_all($kdwarna,$nmwarna,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function gudang()
	{
		$nstart = trim($this->input->post('start'));
		$kdwh = trim($this->input->post('fs_code'));
		$nmwh = trim($this->input->post('fs_nm_code'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->wh_all($kdwh,$nmwh);
		$total = $ssql->num_rows();
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->wh($kdwh,$nmwh,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function product()
	{
		$nstart = trim($this->input->post('start'));
		$kdproduct = trim($this->input->post('fs_kd_product'));
		$nmproduct = trim($this->input->post('fs_nm_product'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->product_all($kdproduct,$nmproduct);
		$total = $ssql->num_rows();
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->product($kdproduct,$nmproduct,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function product2()
	{
		$nstart = trim($this->input->post('start'));
		$kdproduct = trim($this->input->post('fs_kd_product'));
		$nmproduct = trim($this->input->post('fs_nm_product'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->product_all($kdproduct,$nmproduct);
		$total = $ssql->num_rows();
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->product2($kdproduct,$nmproduct,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function casis()
	{
		$nstart = trim($this->input->post('start'));
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		$kdprod = trim($this->input->post('fs_kd_product'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->rangka_beli_all($rangka,$mesin,$kdprod);
		$total = $ssql->num_rows();
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->rangka_beli($rangka,$mesin,$kdprod,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function dept()
	{
		$nstart = trim($this->input->post('start'));
		$kddept = trim($this->input->post('fs_code'));
		$nmdept = trim($this->input->post('fs_nm_code'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->login_dept_all('','');
		$total = $ssql->num_rows();
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->login_dept($kddept,$nmdept,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function printstatus2()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tunitstatus';
		$nmfile = 'unitstatus-'.$jamskg;
		$xIP = trim($this->session->userdata('ip_address'));
		$gIP = trim($this->session->userdata('gIP'));
		
		$oReader = PHPExcel_IOFactory::createReader('Excel5');
		
		if (trim($xIP) == trim($gIP))
		{
			$xtPath = '/var/www/'.$this->config->item('base_folder').'/assets/docs/';
			$xPath = '/var/www/'.$this->config->item('base_folder').'/temp/';
		}
		else
		{
			$xtPath = APPPATH.'../assets/docs/';
			$xPath = APPPATH.'../temp/';
		}
		
		$oExcel = $oReader->load($xtPath.$nmfiletemp.'.xls');
		
		$oExcel->getActiveSheet()->setTitle('UnitStatus');
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
		
		$kdwh = trim($this->input->post('fs_kd_wh'));
		$kdprod = trim($this->input->post('fs_kd_product'));
		$kdcolor = trim($this->input->post('fs_kd_warna'));
		$tgl = trim($this->input->post('fd_tgl'));
		
		$this->load->model('MUnitStatus','',true);
		$ssql = $this->MUnitStatus->unitstatus($kdwh,$kdprod,$kdcolor);
		
		$total = $ssql->num_rows();
		
		$nmwh = '';
		$nmprod0 = '';
		$nmprod = '';
		$pjghal = 41;
		$i = 1;
		$l = 0; // no urut
		
		$k = 1; // pjg hal
		$p = 1; // awal hal
		
		$i=$i+2;
		$k=$k+2;
		
		$sel->setCellValue('A'.$i, 'NATIONAL STOCK MANAGEMENT REPORT');
		$oExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
		$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i++;
		$k++;
		
		$sel->setCellValue('A'.$i, 'WISMA MOTORAVE');
		$oExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
		$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i++;
		$k++;
		
		$sel->setCellValue('A'.$i, 'JL. KH Hasyim Ashari No 1, Jakarta Pusat');
		$oExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
		$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i++;
		$k++;
		
		$sel->setCellValue('A'.$i,'Print Date : '.$tgl);
		$oExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
		$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i++;
		$k++;
		
		$sel->setCellValue('A'.$i, ' ');
		
		$i++;
		$k++;
		
		$sel->setCellValue('A'.$i, 'NO');
		$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$sel->setCellValue('B'.$i, 'LOKASI');
		$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$oExcel->getActiveSheet()->getStyle('B'.$i.':B'.$i)->applyFromArray(
			array(
				'borders' => array(
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					),
					'right' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			)
		);
		
		$sel->setCellValue('C'.$i, 'PRODUK');
		$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$oExcel->getActiveSheet()->getStyle('C'.$i.':C'.$i)->applyFromArray(
			array(
				'borders' => array(
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					),
					'right' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			)
		);
					
		$sel->setCellValue('D'.$i, 'RANGKA');
		$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$sel->setCellValue('E'.$i, 'MESIN');
		$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$oExcel->getActiveSheet()->getStyle('E'.$i.':E'.$i)->applyFromArray(
			array(
				'borders' => array(
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					),
					'right' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			)
		);
		
		$sel->setCellValue('F'.$i, 'CC');
		$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$sel->setCellValue('G'.$i, 'TAHUN');
		$oExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$oExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$oExcel->getActiveSheet()->getStyle('G'.$i.':G'.$i)->applyFromArray(
			array(
				'borders' => array(
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					),
					'right' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			)
		);
		
		$sel->setCellValue('H'.$i, 'STATUS');
		$oExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$oExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$sel->setCellValue('I'.$i, 'KETERANGAN');
		$oExcel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$oExcel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$oExcel->getActiveSheet()->getStyle('I'.$i.':I'.$i)->applyFromArray(
			array(
				'borders' => array(
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					),
					'right' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			)
		);
		
		$oExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray(
			array(
				'borders' => array(
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					),
					'top' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					),
					'bottom' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					),
					'right' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			)
		);
		$i++;
		$k++;
		
		
		if ($ssql->num_rows() > 0)
		{
			//loop 
			$urut = 1;
			$lastwh = '';
			$lastcount = 0 ;
			
			foreach ($ssql->result() as $row)
			{
				if ($k > $pjghal)
				{
					$j = $i - 1;
					$oExcel->getActiveSheet()->setBreak('A'.$j, PHPExcel_Worksheet::BREAK_ROW);
					
					$oExcel->getActiveSheet()->getStyle('A'.$j.':I'.$j)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					
					$oExcel->getActiveSheet()->getStyle('A'.$j.':A'.$j)->applyFromArray(
						array(
							'borders' => array(
								'left' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					
					$k = 1;
					$sel->setCellValue('A'.$i, 'NO');
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('B'.$i, 'LOKASI');
					$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('B'.$i.':B'.$i)->applyFromArray(
						array(
							'borders' => array(
								'left' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								),
								'right' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					
					$sel->setCellValue('C'.$i, 'PRODUK');
					$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					
					$oExcel->getActiveSheet()->getStyle('C'.$i.':C'.$i)->applyFromArray(
						array(
							'borders' => array(
								'left' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								),
								'right' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
								
					$sel->setCellValue('D'.$i, 'RANGKA');
					$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('E'.$i, 'MESIN');
					$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					
					$oExcel->getActiveSheet()->getStyle('E'.$i.':E'.$i)->applyFromArray(
						array(
							'borders' => array(
								'left' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								),
								'right' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					
					$sel->setCellValue('F'.$i, 'CC');
					$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('G'.$i, 'TAHUN');
					$oExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					
					$oExcel->getActiveSheet()->getStyle('G'.$i.':G'.$i)->applyFromArray(
						array(
							'borders' => array(
								'left' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								),
								'right' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					
					$sel->setCellValue('H'.$i, 'STATUS');
					$oExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('I'.$i, 'KETERANGAN');
					$oExcel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					
					$oExcel->getActiveSheet()->getStyle('I'.$i.':I'.$i)->applyFromArray(
						array(
							'borders' => array(
								'left' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								),
								'right' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					
					$oExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray(
						array(
							'borders' => array(
								'left' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								),
								'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								),
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								),
								'right' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$i++;
					$k++;
				}
				
				if (($urut == 0 AND trim($lastwh) <> '') OR (trim($lastwh) <> trim($row->fs_nm_wh)))
				{
					$urut = 1;
					
					if (trim($lastwh) == '' and trim($lastcount) > 0)
					{
						$sel->setCellValue('A'.$i, 'Total Unit :  '. $lastcount);
						$oExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
						$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$oExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray(
							array(
								'borders' => array(
									'top' => array(
										'style' => PHPExcel_Style_Border::BORDER_THIN
									),'bottom' => array(
										'style' => PHPExcel_Style_Border::BORDER_THIN
									),'left' => array(
										'style' => PHPExcel_Style_Border::BORDER_THIN
									),
									'right' => array(
										'style' => PHPExcel_Style_Border::BORDER_THIN
									)
								)
							)
						);
						$i++;
						$k++;
					}
					else if (trim($lastwh) <> '')
					{
						$sel->setCellValue('A'.$i, 'Total Unit di lokasi :  '. $lastcount);
						$oExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
						$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$oExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray(
							array(
								'borders' => array(
									'top' => array(
										'style' => PHPExcel_Style_Border::BORDER_THIN
									),'bottom' => array(
										'style' => PHPExcel_Style_Border::BORDER_THIN
									),'left' => array(
										'style' => PHPExcel_Style_Border::BORDER_THIN
									),
									'right' => array(
										'style' => PHPExcel_Style_Border::BORDER_THIN
									)
								)
							)
						);
						$i++;
						$k++;
					}
					
					$sel->setCellValue('A'.$i, $urut);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray(
						array(
							'borders' => array(
								'left' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								),
								'right' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
				}
				else
				{
					$sel->setCellValue('A'.$i, $urut);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray(
						array(
							'borders' => array(
								'left' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								),
								'right' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
				}
				
				$sel->setCellValue('A'.$i, $urut);
				
				$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$oExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray(
					array(
						'borders' => array(
							'left' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							),
							'right' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					)
				);
				$sel->setCellValue('B'.$i, trim($row->fs_nm_wh));
				$lastwh = trim($row->fs_nm_wh);
				$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$oExcel->getActiveSheet()->getStyle('B'.$i.':H'.$i)->applyFromArray(
					array(
						'borders' => array(
							'left' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							),
							'right' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					)
				);
				$sel->setCellValue('C'.$i, trim($row->fs_nm_product));
				$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$oExcel->getActiveSheet()->getStyle('C'.$i.':C'.$i)->applyFromArray(
					array(
						'borders' => array(
							'left' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							),
							'right' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					)
				);
				$sel->setCellValue('D'.$i, trim($row->fs_rangka));
				$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$sel->setCellValue('E'.$i, trim($row->fs_mesin));
				$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$oExcel->getActiveSheet()->getStyle('E'.$i.':E'.$i)->applyFromArray(
					array(
						'borders' => array(
							'left' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							),
							'right' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					)
				);
				$sel->setCellValue('F'.$i, trim($row->fn_silinder));
				$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$sel->setCellValue('G'.$i, trim($row->fd_thn));
				$oExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$oExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$oExcel->getActiveSheet()->getStyle('G'.$i.':G'.$i)->applyFromArray(
					array(
						'borders' => array(
							'left' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							),
							'right' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					)
				);
				$sel->setCellValue('H'.$i, trim($row->fs_status));
				$oExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$oExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$sel->setCellValue('I'.$i, trim($row->fs_ket));
				$oExcel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$oExcel->getActiveSheet()->getStyle('I'.$i.':I'.$i)->applyFromArray(
					array(
						'borders' => array(
							'left' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							),
							'right' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					)
				);
				
				$lastcount = $urut;
				$urut++;
				
				$i++;
				$k++;
			}
			
			$sel->setCellValue('A'.$i, 'Total Unit di lokasi :  '. $lastcount);
			$oExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
			$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$oExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray(
				array(
					'borders' => array(
						'top' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						),'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						),'left' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						),'right' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			
			$i = $i+3;
			$k = $k+3;
			
			$sel->setCellValue('B'.$i, 'Prepared By');
			$sel->setCellValue('D'.$i, 'Checked By');
			$sel->setCellValue('F'.$i, 'Approved By');
			
			$i = $i+3;
			$k = $k+3;
			
			$sel->setCellValue('B'.$i, '(...............)');
			$sel->setCellValue('D'.$i, '(...............)');
			$sel->setCellValue('F'.$i, '(...............)');
			
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
			
			$hasil = array('sukses' => true, 'nmfile' => $nmfile.'.xls');
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array('sukses' => false, 'hasil' => 'No record!!');
			echo json_encode($hasil);
		}
	}
	
	function printstatus()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tunitstatus';
		$nmfile = 'unitstatus-'.$jamskg;
		$xIP = trim($this->session->userdata('ip_address'));
		$gIP = trim($this->session->userdata('gIP'));
		
		$oReader = PHPExcel_IOFactory::createReader('Excel5');
		
		if (trim($xIP) == trim($gIP))
		{
			$xtPath = '/var/www/'.$this->config->item('base_folder').'/assets/docs/';
			$xPath = '/var/www/'.$this->config->item('base_folder').'/temp/';
		}
		else
		{
			$xtPath = APPPATH.'../assets/docs/';
			$xPath = APPPATH.'../temp/';
		}
		
		$oExcel = $oReader->load($xtPath.$nmfiletemp.'.xls');
		
		$oExcel->getActiveSheet()->setTitle('UnitStatus');
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
		
		$kdwh = trim($this->input->post('fs_kd_wh'));
		$kdprod = trim($this->input->post('fs_kd_product'));
		$kdcolor = trim($this->input->post('fs_kd_warna'));
		$tgl = trim($this->input->post('fd_tgl'));
		
		$this->load->model('MUnitStatus','',true);
		$ssql = $this->MUnitStatus->unitstatus($kdwh,$kdprod,$kdcolor);
		
		$total = $ssql->num_rows();
		
		$pjghal = 41;
		$i = 1;
		$l = 0; // no urut
		
		$k = 1; // pjg hal
		$p = 1; // awal hal
		
		if ($ssql->num_rows() > 0)
		{
			$i=$i+2;
			$k=$k+2;
			
			$sel->setCellValue('A'.$i, 'NATIONAL STOCK MANAGEMENT REPORT');
			$sel->mergeCells('A'.$i.':I'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'WISMA MOTORAVE');
			$sel->mergeCells('A'.$i.':I'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'JL. KH Hasyim Ashari No 1, Jakarta Pusat');
			$sel->mergeCells('A'.$i.':I'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i,'Print Date : '.$tgl);
			$sel->mergeCells('A'.$i.':I'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'NO');
			$sel->setCellValue('B'.$i, 'PRODUK');
			$sel->setCellValue('C'.$i, 'RANGKA');
			$sel->setCellValue('D'.$i, 'MESIN');
			$sel->setCellValue('E'.$i, 'CC');
			$sel->setCellValue('F'.$i, 'TAHUN');
			$sel->setCellValue('G'.$i, 'WARNA');
			$sel->setCellValue('H'.$i, 'STATUS');
			$sel->setCellValue('I'.$i, 'KETERANGAN');
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$sel->getStyle('A'.$i.':I'.$i)->applyFromArray(
				array(
					'borders' => array(
						'top' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$sel->getStyle('A'.$i.':I'.$i)->applyFromArray(
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
			
			$urut = 1;
			$lastwh = '';
			$lastcount = 0 ;
			$l = 1;
			
			foreach ($ssql->result() as $row)
			{
				if (trim($lastwh) == '')
				{
				}
				
				if (trim($row->fs_nm_wh) <> '' and trim($lastwh) <> trim($row->fs_nm_wh))
				{
					if ($k > $pjghal)
					{
						$j = $i - 1;
						$sel->setBreak('A'.$j, PHPExcel_Worksheet::BREAK_ROW);
						$sel->getStyle('A'.$j.':I'.$j)->applyFromArray(
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
						$sel->getStyle('A'.$q.':A'.$j)->applyFromArray($arr);
						$sel->getStyle('B'.$q.':B'.$j)->applyFromArray($arr);
						$sel->getStyle('C'.$q.':C'.$j)->applyFromArray($arr);
						$sel->getStyle('D'.$q.':D'.$j)->applyFromArray($arr);
						$sel->getStyle('E'.$q.':E'.$j)->applyFromArray($arr);
						$sel->getStyle('F'.$q.':F'.$j)->applyFromArray($arr);
						$sel->getStyle('G'.$q.':G'.$j)->applyFromArray($arr);
						$sel->getStyle('H'.$q.':H'.$j)->applyFromArray($arr);
						$sel->getStyle('I'.$q.':I'.$j)->applyFromArray($arr);
						$sel->getStyle('I'.$q.':I'.$j)->applyFromArray(
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
						// $sel->setCellValue('A'.$i, 'LAPORAN DAFTAR STOCK');
						// $sel->mergeCells('A'.$i.':D'.$i);
						// $sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						// $p = $i;
						// $i = $i + 2;
						// $k = $k + 2;
						
						$sel->setCellValue('A'.$i, 'NO');
						$sel->setCellValue('B'.$i, 'PRODUK');
						$sel->setCellValue('C'.$i, 'RANGKA');
						$sel->setCellValue('D'.$i, 'MESIN');
						$sel->setCellValue('E'.$i, 'CC');
						$sel->setCellValue('F'.$i, 'TAHUN');
						$sel->setCellValue('G'.$i, 'WARNA');
						$sel->setCellValue('H'.$i, 'STATUS');
						$sel->setCellValue('I'.$i, 'KETERANGAN');
						$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sel->getStyle('A'.$i.':I'.$i)->applyFromArray(
							array(
								'borders' => array(
									'top' => array(
										'style' => PHPExcel_Style_Border::BORDER_THIN
									)
								)
							)
						);
						$sel->getStyle('A'.$i.':I'.$i)->applyFromArray(
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
						
					}
					
					if (trim($lastwh) <> '')
					{
						$n = $l - 1;
						$sel->setCellValue('A'.$i, 'Total '.trim($lastwh).' : '.$n);
						$sel->mergeCells('A'.$i.':I'.$i);
						$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$sel->getStyle('A'.$i.':I'.$i)->applyFromArray(
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
					}
					
					$sel->setCellValue('A'.$i, 'Lokasi : '.trim($row->fs_nm_wh));
					$sel->mergeCells('A'.$i.':I'.$i);
					
					$sel->getStyle('A'.$i.':I'.$i)->applyFromArray(
						array(
							'borders' => array(
								'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$sel->getStyle('A'.$i.':I'.$i)->applyFromArray(
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
					$sel->getStyle('A'.$j.':I'.$j)->applyFromArray(
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
					$sel->getStyle('A'.$q.':A'.$j)->applyFromArray($arr);
					$sel->getStyle('B'.$q.':B'.$j)->applyFromArray($arr);
					$sel->getStyle('C'.$q.':C'.$j)->applyFromArray($arr);
					$sel->getStyle('D'.$q.':D'.$j)->applyFromArray($arr);
					$sel->getStyle('E'.$q.':E'.$j)->applyFromArray($arr);
					$sel->getStyle('F'.$q.':F'.$j)->applyFromArray($arr);
					$sel->getStyle('G'.$q.':G'.$j)->applyFromArray($arr);
					$sel->getStyle('H'.$q.':H'.$j)->applyFromArray($arr);
					$sel->getStyle('I'.$q.':I'.$j)->applyFromArray($arr);
					$sel->getStyle('I'.$q.':I'.$j)->applyFromArray(
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
					// $sel->setCellValue('A'.$i, 'LAPORAN DAFTAR STOCK');
					// $sel->mergeCells('A'.$i.':I'.$i);
					// $sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					// $p = $i;
					// $i = $i + 2;
					// $k = $k + 2;
					
					$sel->setCellValue('A'.$i, 'NO');
					$sel->setCellValue('B'.$i, 'PRODUK');
					$sel->setCellValue('C'.$i, 'RANGKA');
					$sel->setCellValue('D'.$i, 'MESIN');
					$sel->setCellValue('E'.$i, 'CC');
					$sel->setCellValue('F'.$i, 'TAHUN');
					$sel->setCellValue('G'.$i, 'WARNA');
					$sel->setCellValue('H'.$i, 'STATUS');
					$sel->setCellValue('I'.$i, 'KETERANGAN');
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('A'.$i.':I'.$i)->applyFromArray(
						array(
							'borders' => array(
								'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$sel->getStyle('A'.$i.':I'.$i)->applyFromArray(
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
				
				$sel->setCellValue('A'.$i, $l);
				$sel->setCellValue('B'.$i, trim($row->fs_kd_product));
				$sel->setCellValue('C'.$i, trim($row->fs_rangka));
				$sel->setCellValue('D'.$i, trim($row->fs_mesin));
				$sel->setCellValue('E'.$i, trim($row->fn_silinder));
				$sel->setCellValue('F'.$i, trim($row->fd_thn));
				$sel->setCellValue('G'.$i, trim($row->fs_nm_warna));
				$sel->setCellValue('H'.$i, trim($row->fs_status));
				$sel->setCellValue('I'.$i, trim($row->fs_ket));
				$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				// $total = $total + trim($row->fn_total);
				// $gtotal = $gtotal + trim($row->fn_total);
				$i++;
				$k++;
				$l++;
				
				$lastwh = trim($row->fs_nm_wh);
			}
			
			$n = $l - 1;
			$sel->setCellValue('A'.$i, 'Total '.trim($lastwh).' : '.$n);
			$sel->mergeCells('A'.$i.':I'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('A'.$i.':I'.$i)->applyFromArray(
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
			
			$j = $i -1;
			$oExcel->getActiveSheet()->getStyle('A'.$j.':I'.$j)->applyFromArray(
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
			$oExcel->getActiveSheet()->getStyle('G'.$q.':G'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('H'.$q.':H'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('I'.$q.':I'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('I'.$q.':I'.$j)->applyFromArray(
				array(
					'borders' => array(
						'right' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			// $sel->setCellValue('A'.$i, 'Total Unit di lokasi :  '. $lastcount);
			// $oExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
			// $oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			// $oExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray(
				// array(
					// 'borders' => array(
						// 'top' => array(
							// 'style' => PHPExcel_Style_Border::BORDER_THIN
						// ),'bottom' => array(
							// 'style' => PHPExcel_Style_Border::BORDER_THIN
						// ),'left' => array(
							// 'style' => PHPExcel_Style_Border::BORDER_THIN
						// ),'right' => array(
							// 'style' => PHPExcel_Style_Border::BORDER_THIN
						// )
					// )
				// )
			// );
			
			$i = $i+3;
			$k = $k+3;
			
			$sel->setCellValue('B'.$i, 'Prepared By');
			$sel->setCellValue('D'.$i, 'Checked By');
			$sel->setCellValue('F'.$i, 'Approved By');
			
			$i = $i+3;
			$k = $k+3;
			
			$sel->setCellValue('B'.$i, '(...............)');
			$sel->setCellValue('D'.$i, '(...............)');
			$sel->setCellValue('F'.$i, '(...............)');
			
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
			
			$hasil = array('sukses' => true, 'nmfile' => $nmfile.'.xls');
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array('sukses' => false, 'hasil' => 'No record!!');
			echo json_encode($hasil);
		}
	}
	
	function printhistory()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tunithistory';
		$nmfile = 'unithistory-'.$jamskg;
		$xIP = trim($this->session->userdata('ip_address'));
		$gIP = trim($this->session->userdata('gIP'));
		
		$oReader = PHPExcel_IOFactory::createReader('Excel5');
		
		if (trim($xIP) == trim($gIP))
		{
			$xtPath = '/var/www/'.$this->config->item('base_folder').'/assets/docs/';
			$xPath = '/var/www/'.$this->config->item('base_folder').'/temp/';
		}
		else
		{
			$xtPath = APPPATH.'../assets/docs/';
			$xPath = APPPATH.'../temp/';
		}
		
		$oExcel = $oReader->load($xtPath.$nmfiletemp.'.xls');
		
		$oExcel->getActiveSheet()->setTitle('UnitStatus');
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
		
		$kdwh = trim($this->input->post('fs_kd_wh'));
		$kdprod = trim($this->input->post('fs_kd_product'));
		$tgl = trim($this->input->post('fd_tgl'));
		
		$this->load->model('MUnitStatus','',true);
		$ssql = $this->MUnitStatus->unithistory($kdwh,$kdprod);
		
		$total = $ssql->num_rows();
		
		$refno = '';
		$nmprod0 = '';
		$nmprod = '';
		$pjghal = 57;
		$i = 1;
		$l = 0; // no urut
		
		$k = 1; // pjg hal
		$p = 1; // awal hal
		
		if ($ssql->num_rows() > 0)
		{
			$i = 1;
			$k = 1;
			$sel->setCellValue('A'.$i, 'LAPORAN UNIT HISTORI STATUS DETAIL');
			$sel->mergeCells('A'.$i.':G'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$p = $i;
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'No.');
			$sel->setCellValue('B'.$i, 'Nama Produk');
			$sel->setCellValue('C'.$i, 'Rangka');
			$sel->setCellValue('D'.$i, 'Mesin');
			$sel->setCellValue('E'.$i, 'Status');
			$sel->setCellValue('F'.$i, 'Tgl Status');
			$sel->setCellValue('G'.$i, 'Keterangan');
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
			$i = $i + 2;
			$k = $k + 2;
			
			//loop
			foreach ($ssql->result() as $row)
			{
				if (trim($refno) == '')
				{
					$j = $i - 1;
					$sel->setCellValue('A'.$j, trim($row->fs_refno).' - '.trim($row->fd_refno));
					$sel->mergeCells('A'.$j.':G'.$i);
					
					$sel->getStyle('A'.$j.':G'.$j)->applyFromArray(
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
					$l = 1;
				}
				
				if (trim($row->fs_refno) <> '' and trim($refno) <> trim($row->fs_refno))
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
						
						$q = $p + 2;
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
						$sel->setCellValue('A'.$i, 'LAPORAN UNIT HISTORI STATUS DETAIL');
						$sel->mergeCells('A'.$i.':G'.$i);
						$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$p = $i;
						$i = $i + 2;
						$k = $k + 2;
						
						$sel->setCellValue('A'.$i, 'No.');
						$sel->setCellValue('B'.$i, 'Nama Produk');
						$sel->setCellValue('C'.$i, 'Rangka');
						$sel->setCellValue('D'.$i, 'Mesin');
						$sel->setCellValue('E'.$i, 'Status');
						$sel->setCellValue('F'.$i, 'Tgl Status');
						$sel->setCellValue('G'.$i, 'Keterangan');
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
					}
					$i++;
					$k++;
					$j = $i - 1;
					$sel->setCellValue('A'.$j, trim($row->fs_refno).' - '.trim($row->fd_refno));
					$sel->mergeCells('A'.$j.':G'.$i);
					
					$sel->getStyle('A'.$j.':G'.$j)->applyFromArray(
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
					$l=1;
				}
				
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
					
					$q = $p + 2;
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
					$sel->setCellValue('A'.$i, 'LAPORAN UNIT HISTORI STATUS DETAIL');
					$sel->mergeCells('A'.$i.':G'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$p = $i;
					$i = $i + 2;
					$k = $k + 2;
					
					$sel->setCellValue('A'.$i, 'No.');
					$sel->setCellValue('B'.$i, 'Nama Produk');
					$sel->setCellValue('C'.$i, 'Rangka');
					$sel->setCellValue('D'.$i, 'Mesin');
					$sel->setCellValue('E'.$i, 'Status');
					$sel->setCellValue('F'.$i, 'Tgl Status');
					$sel->setCellValue('G'.$i, 'Keterangan');
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
				}
				
				$sel->setCellValue('A'.$i, trim($l));
				$sel->setCellValue('B'.$i, trim($row->fs_nm_product));
				$sel->setCellValue('C'.$i, trim($row->fs_rangka));
				$sel->setCellValue('D'.$i, trim($row->fs_mesin));
				$sel->setCellValue('E'.$i, trim($row->fs_status));
				$sel->setCellValue('F'.$i, trim($row->fd_tgl_pelihara));
				$sel->setCellValue('G'.$i, trim($row->fs_ket));
				$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$i++;
				$k++;
				$l++;
				
				$refno = trim($row->fs_refno);
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
			
			$q = $p + 2;
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
			
			$hasil = array('sukses' => true, 'hasil' => '"'.$nmfile.'.pdf" has been created!!', 'nmfile' => $nmfile.'.pdf');
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array('sukses' => false, 'hasil' => 'No record!!');
			echo json_encode($hasil);
		}
	}
}
?>
