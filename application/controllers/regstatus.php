<?php

class RegStatus extends CI_Controller
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
			$this->load->view('vregstatus');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function gudang()
	{
		$nstart = trim($this->input->post('start'));
		$lDept = strlen(trim($this->input->post('fs_kd_dept')));
		$lDept = $lDept - 2;
		$kddept = substr(trim($this->input->post('fs_kd_dept')), $lDept, 2);
		
		$lCount = strlen(trim($this->input->post('fs_count')));
		$lCount = $lCount - 2;
		$kdcount = substr(trim($this->input->post('fs_count')), $lCount, 2);
		
		$kdwh = trim($this->input->post('fs_code'));
		$nmwh = trim($this->input->post('fs_nm_code'));
		
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->wh_all('',$kddept,$kdcount,$kdwh,$nmwh,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->wh('',$kddept,$kdcount,$kdwh,$nmwh,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function product()
	{
		$nstart = trim($this->input->post('start'));
		$kdproduct = trim($this->input->post('fs_kd_product'));
		$nmproduct = trim($this->input->post('fs_nm_product'));
		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->product_all($kdproduct,$nmproduct,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->product($kdproduct,$nmproduct,$nstart,$cari);
		
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
		
		$ssql = $this->mSearch->product2($kdproduct,$nmproduct,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function rangka()
	{
		$nstart = trim($this->input->post('start'));
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		$kdprod = trim($this->input->post('fs_kd_product'));
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->rangka_beli_all($rangka,$mesin,$kdprod,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->rangka_beli($rangka,$mesin,$kdprod,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function dept()
	{
		$nstart = trim($this->input->post('start'));
		$kddept = trim($this->input->post('fs_code'));
		$nmdept = trim($this->input->post('fs_nm_code'));

		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->login_dept_all('',$kddept,$nmdept,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->login_dept('',$kddept,$nmdept,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function printreg()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tregstatus';
		$nmfile = 'regstatus-'.$jamskg;
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
		
		$oExcel->getActiveSheet()->setTitle('RegStatus');
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
		$kdrangka = trim($this->input->post('fs_rangka'));
		$kdjual = trim($this->input->post('fb_jual'));
		$kdall = trim($this->input->post('fb_all'));
		$tgl = trim($this->input->post('fd_tgl'));
		
		if (trim($kdjual) == 'true')
		{
			$kdjual = 1;
		}
		else
		{
			$kdjual = 0;
		}
		
		if (trim($kdall) == 'true')
		{
			$kdall = 1;
		}
		else
		{
			$kdall = 0;
		}
		
		$this->load->model('mRegStatus','',true);
		$ssql = $this->mRegStatus->regstatus($kdwh,$kdprod,$kdrangka,$kdjual,$kdall);
		
		$nmwh = '';
		$nmprod0 = '';
		$nmprod = '';
		$pjghal = 57;
		$i = 1;
		$l = 0; // no urut
		
		$k = 1; // pjg hal
		$p = 1; // awal hal
		$sel->setCellValue('A'.$i, 'LAPORAN DAFTAR STOCK');
		$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
		$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$i++;
		$k++;
		
		$sel->setCellValue('A'.$i, 'Sampai Tanggal : '.$tgl);
		$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
		$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$i++;
		$k++;
		
		$sel->setCellValue('A'.$i, ' ');
		$i++;
		$k++;
		
		if ($ssql->num_rows() > 0)
		{
			//loop
			foreach ($ssql->result() as $row)
			{
				if (trim($nmwh) == '')
				{
					$sel->setCellValue('A'.$i, trim($row->fs_nm_wh));
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i, 'GUDANG');
					$j = $i + 1;
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':A'.$j);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('B'.$i, 'TIPE');
					$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->setCellValue('C'.$i, 'RANGKA');
					$oExcel->getActiveSheet()->mergeCells('C'.$i.':C'.$j);
					$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('D'.$i, 'MESIN');
					$oExcel->getActiveSheet()->mergeCells('D'.$i.':D'.$j);
					$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('E'.$i, 'CC');
					$oExcel->getActiveSheet()->mergeCells('E'.$i.':E'.$j);
					$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('F'.$i, 'STATUS');
					$oExcel->getActiveSheet()->mergeCells('F'.$i.':F'.$j);
					$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('G'.$i, 'TH');
					$oExcel->getActiveSheet()->mergeCells('G'.$i.':G'.$j);
					$oExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					
					$oExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
					
					$sel->setCellValue('B'.$i, 'WARNA');
					$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					$oExcel->getActiveSheet()->getStyle('B'.$i.':B'.$i)->applyFromArray(
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
					
					$sel->setCellValue('A'.$i, ' ');
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					
					$oExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
				
				if (trim($nmwh) <> '' and trim($nmwh) <> trim($row->fs_nm_wh))
				{
					$j = $i - 1;
					$oExcel->getActiveSheet()->setBreak('A'.$j, PHPExcel_Worksheet::BREAK_ROW);
					
					$oExcel->getActiveSheet()->getStyle('A'.$j.':G'.$j)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					
					$k = 1;
					$sel->setCellValue('A'.$i, 'LAPORAN DAFTAR STOCK');
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					$q = $p + 4;
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
					$oExcel->getActiveSheet()->getStyle('G'.$q.':G'.$j)->applyFromArray(
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
					
					$sel->setCellValue('A'.$i, 'Sampai Tanggal : '.$tgl);
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$i++;
					$k++;
					$sel->setCellValue('A'.$i, ' ');
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i, trim($row->fs_nm_wh));
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i, 'GUDANG');
					$j = $i + 1;
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':A'.$j);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('B'.$i, 'TIPE');
					$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->setCellValue('C'.$i, 'RANGKA');
					$oExcel->getActiveSheet()->mergeCells('C'.$i.':C'.$j);
					$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('D'.$i, 'MESIN');
					$oExcel->getActiveSheet()->mergeCells('D'.$i.':D'.$j);
					$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('E'.$i, 'CC');
					$oExcel->getActiveSheet()->mergeCells('E'.$i.':E'.$j);
					$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('F'.$i, 'STATUS');
					$oExcel->getActiveSheet()->mergeCells('F'.$i.':F'.$j);
					$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('G'.$i, 'TH');
					$oExcel->getActiveSheet()->mergeCells('G'.$i.':G'.$j);
					$oExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					
					$oExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
					
					$sel->setCellValue('B'.$i, 'WARNA');
					$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					$oExcel->getActiveSheet()->getStyle('B'.$i.':B'.$i)->applyFromArray(
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
					
					$sel->setCellValue('A'.$i, ' ');
					
					$oExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
				
				if ($k > $pjghal)
				{
					$j = $i - 1;
					$oExcel->getActiveSheet()->setBreak('A'.$j, PHPExcel_Worksheet::BREAK_ROW);
					
					$oExcel->getActiveSheet()->getStyle('A'.$j.':G'.$j)->applyFromArray(
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
					$sel->setCellValue('A'.$i, 'LAPORAN DAFTAR STOCK');
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					$q = $p + 4;
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
					$oExcel->getActiveSheet()->getStyle('G'.$q.':G'.$j)->applyFromArray(
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
					
					$sel->setCellValue('A'.$i, 'Sampai Tanggal : '.$tgl);
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$i++;
					$k++;
					$sel->setCellValue('A'.$i, ' ');
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i, trim($row->fs_nm_wh));
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i, 'GUDANG');
					$j = $i + 1;
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':A'.$j);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('B'.$i, 'TIPE');
					$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->setCellValue('C'.$i, 'RANGKA');
					$oExcel->getActiveSheet()->mergeCells('C'.$i.':C'.$j);
					$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('D'.$i, 'MESIN');
					$oExcel->getActiveSheet()->mergeCells('D'.$i.':D'.$j);
					$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('E'.$i, 'CC');
					$oExcel->getActiveSheet()->mergeCells('E'.$i.':E'.$j);
					$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('F'.$i, 'STATUS');
					$oExcel->getActiveSheet()->mergeCells('F'.$i.':F'.$j);
					$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('G'.$i, 'TH');
					$oExcel->getActiveSheet()->mergeCells('G'.$i.':G'.$j);
					$oExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					
					$oExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
					
					$sel->setCellValue('B'.$i, 'WARNA');
					$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					$oExcel->getActiveSheet()->getStyle('B'.$i.':B'.$i)->applyFromArray(
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
					
					$sel->setCellValue('A'.$i, ' ');
					
					$oExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
				
				
				$nmprod0 = trim($row->fs_nm_product);
				if (trim($nmprod0) <> '' and trim($nmprod) <> '' and trim($nmprod0) <> trim($nmprod))
				{
					$n = $l - 1;
					$sel->setCellValue('A'.$i, 'Total '.trim($nmprod).' : '.$n);
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					
					$oExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
				
				if (trim($nmprod) == '')
				{
					$sel->setCellValue('A'.$i, trim($row->fs_nm_product));
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					
					$oExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
				
				if (trim($nmprod) <> '' and trim($nmprod) <> trim($row->fs_nm_product))
				{
					$sel->setCellValue('A'.$i, ' ');
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					$i++;
					$k++;
					$sel->setCellValue('A'.$i, trim($row->fs_nm_product));
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					
					$oExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
				
				$sel->setCellValue('A'.$i, $l);
				$sel->setCellValue('B'.$i, trim($row->fs_nm_warna));
				$sel->setCellValue('C'.$i, trim($row->fs_rangka));
				$sel->setCellValue('D'.$i, trim($row->fs_mesin));
				$sel->setCellValue('E'.$i, trim($row->fn_silinder));
				$sel->setCellValue('F'.$i, trim($row->fs_status));
				$sel->setCellValue('G'.$i, trim($row->fd_thn));
				$i++;
				$k++;
				$l++;
				
				$nmwh = trim($row->fs_nm_wh);
				$nmprod = trim($row->fs_nm_product);
			}
			//eof loop
			
			$n = $l - 1;
			$sel->setCellValue('A'.$i, 'Total '.trim($nmprod).' : '.$n);
			$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
			
			$oExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
			$oExcel->getActiveSheet()->getStyle('A'.$j.':G'.$j)->applyFromArray(
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
			$oExcel->getActiveSheet()->getStyle('A'.$q.':A'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('B'.$q.':B'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('C'.$q.':C'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('D'.$q.':D'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('E'.$q.':E'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('F'.$q.':F'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('G'.$q.':G'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('G'.$q.':G'.$j)->applyFromArray(
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
	
	function printreg2()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tregstatus';
		$nmfile = 'regstatusdate-'.$jamskg;
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
		
		$oExcel->getActiveSheet()->setTitle('RegStatus');
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
		
		// $ldept = strlen(trim($this->input->post('fs_kd_dept')));
		// $ldept = $ldept - 6;
		$lstrx = strlen(trim($this->input->post('fs_kd_strx')));
		$lstrx = $lstrx - 4;
		
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		$kdwh = trim($this->input->post('fs_kd_wh'));
		$kdprod = trim($this->input->post('fs_kd_product'));
		$rangka = trim($this->input->post('fs_rangka'));
		$tgl = trim($this->input->post('fd_tgl'));
		$tgl2 = trim($this->input->post('fd_tgl2'));
		
		$this->load->model('mRegStatus','',true);
		$ssql = $this->mRegStatus->regstatusdate($kddept,$kdcount,$kdwh,$kdprod,$rangka,$tgl2);
		
		$nmwh = '';
		$nmprod0 = '';
		$nmprod = '';
		$pjghal = 57;
		$i = 1;
		$l = 0; // no urut
		
		$k = 1; // pjg hal
		$p = 1; // awal hal
		$sel->setCellValue('A'.$i, 'LAPORAN DAFTAR STOCK');
		$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
		$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$i++;
		$k++;
		
		$sel->setCellValue('A'.$i, 'Sampai Tanggal : '.$tgl);
		$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
		$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$i++;
		$k++;
		
		$sel->setCellValue('A'.$i, ' ');
		$i++;
		$k++;
		
		if ($ssql->num_rows() > 0)
		{
			//loop
			foreach ($ssql->result() as $row)
			{
				if (trim($nmwh) == '')
				{
					$sel->setCellValue('A'.$i, trim($row->fs_nm_wh));
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i, 'GUDANG');
					$j = $i + 1;
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':A'.$j);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('B'.$i, 'TIPE');
					$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->setCellValue('C'.$i, 'RANGKA');
					$oExcel->getActiveSheet()->mergeCells('C'.$i.':C'.$j);
					$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('D'.$i, 'MESIN');
					$oExcel->getActiveSheet()->mergeCells('D'.$i.':D'.$j);
					$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('E'.$i, 'CC');
					$oExcel->getActiveSheet()->mergeCells('E'.$i.':E'.$j);
					$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('F'.$i, 'STATUS');
					$oExcel->getActiveSheet()->mergeCells('F'.$i.':F'.$j);
					$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('G'.$i, 'TH');
					$oExcel->getActiveSheet()->mergeCells('G'.$i.':G'.$j);
					$oExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					
					$oExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
					
					$sel->setCellValue('B'.$i, 'WARNA');
					$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					$oExcel->getActiveSheet()->getStyle('B'.$i.':B'.$i)->applyFromArray(
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
					
					$sel->setCellValue('A'.$i, ' ');
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					
					$oExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
				
				if (trim($nmwh) <> '' and trim($nmwh) <> trim($row->fs_nm_wh))
				{
					$j = $i - 1;
					$oExcel->getActiveSheet()->setBreak('A'.$j, PHPExcel_Worksheet::BREAK_ROW);
					
					$oExcel->getActiveSheet()->getStyle('A'.$j.':G'.$j)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					
					$k = 1;
					$sel->setCellValue('A'.$i, 'LAPORAN DAFTAR STOCK');
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					$q = $p + 4;
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
					$oExcel->getActiveSheet()->getStyle('G'.$q.':G'.$j)->applyFromArray(
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
					
					$sel->setCellValue('A'.$i, 'Sampai Tanggal : '.$tgl);
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$i++;
					$k++;
					$sel->setCellValue('A'.$i, ' ');
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i, trim($row->fs_nm_wh));
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i, 'GUDANG');
					$j = $i + 1;
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':A'.$j);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('B'.$i, 'TIPE');
					$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->setCellValue('C'.$i, 'RANGKA');
					$oExcel->getActiveSheet()->mergeCells('C'.$i.':C'.$j);
					$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('D'.$i, 'MESIN');
					$oExcel->getActiveSheet()->mergeCells('D'.$i.':D'.$j);
					$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('E'.$i, 'CC');
					$oExcel->getActiveSheet()->mergeCells('E'.$i.':E'.$j);
					$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('F'.$i, 'STATUS');
					$oExcel->getActiveSheet()->mergeCells('F'.$i.':F'.$j);
					$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('G'.$i, 'TH');
					$oExcel->getActiveSheet()->mergeCells('G'.$i.':G'.$j);
					$oExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					
					$oExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
					
					$sel->setCellValue('B'.$i, 'WARNA');
					$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					$oExcel->getActiveSheet()->getStyle('B'.$i.':B'.$i)->applyFromArray(
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
					
					$sel->setCellValue('A'.$i, ' ');
					
					$oExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
				
				if ($k > $pjghal)
				{
					$j = $i - 1;
					$oExcel->getActiveSheet()->setBreak('A'.$j, PHPExcel_Worksheet::BREAK_ROW);
					
					$oExcel->getActiveSheet()->getStyle('A'.$j.':G'.$j)->applyFromArray(
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
					$sel->setCellValue('A'.$i, 'LAPORAN DAFTAR STOCK');
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					$q = $p + 4;
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
					$oExcel->getActiveSheet()->getStyle('G'.$q.':G'.$j)->applyFromArray(
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
					
					$sel->setCellValue('A'.$i, 'Sampai Tanggal : '.$tgl);
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$i++;
					$k++;
					$sel->setCellValue('A'.$i, ' ');
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i, trim($row->fs_nm_wh));
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i, 'GUDANG');
					$j = $i + 1;
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':A'.$j);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('B'.$i, 'TIPE');
					$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->setCellValue('C'.$i, 'RANGKA');
					$oExcel->getActiveSheet()->mergeCells('C'.$i.':C'.$j);
					$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('D'.$i, 'MESIN');
					$oExcel->getActiveSheet()->mergeCells('D'.$i.':D'.$j);
					$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('E'.$i, 'CC');
					$oExcel->getActiveSheet()->mergeCells('E'.$i.':E'.$j);
					$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('F'.$i, 'STATUS');
					$oExcel->getActiveSheet()->mergeCells('F'.$i.':F'.$j);
					$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sel->setCellValue('G'.$i, 'TH');
					$oExcel->getActiveSheet()->mergeCells('G'.$i.':G'.$j);
					$oExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$oExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					
					$oExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
					
					$sel->setCellValue('B'.$i, 'WARNA');
					$oExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					$oExcel->getActiveSheet()->getStyle('B'.$i.':B'.$i)->applyFromArray(
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
					
					$sel->setCellValue('A'.$i, ' ');
					
					$oExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
				
				
				$nmprod0 = trim($row->fs_nm_product);
				if (trim($nmprod0) <> '' and trim($nmprod) <> '' and trim($nmprod0) <> trim($nmprod))
				{
					$n = $l - 1;
					$sel->setCellValue('A'.$i, 'Total '.trim($nmprod).' : '.$n);
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					
					$oExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
				
				if (trim($nmprod) == '')
				{
					$sel->setCellValue('A'.$i, trim($row->fs_nm_product));
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					
					$oExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
				
				if (trim($nmprod) <> '' and trim($nmprod) <> trim($row->fs_nm_product))
				{
					$sel->setCellValue('A'.$i, ' ');
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					$i++;
					$k++;
					$sel->setCellValue('A'.$i, trim($row->fs_nm_product));
					$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
					
					$oExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
				
				$sel->setCellValue('A'.$i, $l);
				$sel->setCellValue('B'.$i, trim($row->fs_nm_warna));
				$sel->setCellValue('C'.$i, trim($row->fs_rangka));
				$sel->setCellValue('D'.$i, trim($row->fs_mesin));
				$sel->setCellValue('E'.$i, trim($row->fn_silinder));
				$sel->setCellValue('F'.$i, trim($row->fs_status));
				$sel->setCellValue('G'.$i, trim($row->fd_thn));
				$i++;
				$k++;
				$l++;
				
				$nmwh = trim($row->fs_nm_wh);
				$nmprod = trim($row->fs_nm_product);
			}
			//eof loop
			
			$n = $l - 1;
			$sel->setCellValue('A'.$i, 'Total '.trim($nmprod).' : '.$n);
			$oExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
			
			$oExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
			$oExcel->getActiveSheet()->getStyle('A'.$j.':G'.$j)->applyFromArray(
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
			$oExcel->getActiveSheet()->getStyle('A'.$q.':A'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('B'.$q.':B'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('C'.$q.':C'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('D'.$q.':D'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('E'.$q.':E'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('F'.$q.':F'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('G'.$q.':G'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('G'.$q.':G'.$j)->applyFromArray(
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
	
	function printsum()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tregsum';
		$nmfile = 'regsum-'.$jamskg;
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
		
		$oExcel->getActiveSheet()->setTitle('RegStatus');
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
		
		$this->load->model('mRegStatus','',true);
		$ssql = $this->mRegStatus->stoksum($kdwh,$kdprod);
		
		$nmwh = '';
		$nmprod = '';
		$total = 0;
		$gtotal = 0;
		$pjghal = 55;
		$i = 1;
		$l = 0; // no urut
		
		$k = 1; // pjg hal
		$p = 1; // awal hal
		
		if ($ssql->num_rows() > 0)
		{
			$sel->setCellValue('A'.$i, 'LAPORAN DAFTAR STOCK');
			$sel->mergeCells('A'.$i.':D'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i = $i + 2;
			$k = $k + 2;
			
			
			$sel->setCellValue('A'.$i, 'No.');
			$sel->setCellValue('B'.$i, 'Tipe');
			$sel->setCellValue('C'.$i, 'Warna');
			$sel->setCellValue('D'.$i, 'Jumlah');
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$sel->getStyle('A'.$i.':D'.$i)->applyFromArray(
				array(
					'borders' => array(
						'top' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$sel->getStyle('A'.$i.':D'.$i)->applyFromArray(
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
			
			//loop
			foreach ($ssql->result() as $row)
			{
				if (trim($nmwh) == '')
				{
				}
				
				if (trim($row->fs_nm_wh) <> '' and trim($nmwh) <> trim($row->fs_nm_wh))
				{
					if ($k > $pjghal)
					{
						$j = $i - 1;
						$sel->setBreak('A'.$j, PHPExcel_Worksheet::BREAK_ROW);
						$sel->getStyle('A'.$j.':D'.$j)->applyFromArray(
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
						$sel->getStyle('D'.$q.':D'.$j)->applyFromArray(
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
						$sel->setCellValue('A'.$i, 'LAPORAN DAFTAR STOCK');
						$sel->mergeCells('A'.$i.':D'.$i);
						$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$p = $i;
						$i = $i + 2;
						$k = $k + 2;
						
						$sel->setCellValue('A'.$i, 'No.');
						$sel->setCellValue('B'.$i, 'Tipe');
						$sel->setCellValue('C'.$i, 'Warna');
						$sel->setCellValue('D'.$i, 'Jumlah');
						$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sel->getStyle('A'.$i.':D'.$i)->applyFromArray(
							array(
								'borders' => array(
									'top' => array(
										'style' => PHPExcel_Style_Border::BORDER_THIN
									)
								)
							)
						);
						$sel->getStyle('A'.$i.':D'.$i)->applyFromArray(
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
					
					if (trim($nmwh) <> '')
					{
						$sel->setCellValue('A'.$i, 'Total '.trim($nmwh).' : '.$total);
						$sel->mergeCells('A'.$i.':D'.$i);
						$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$sel->getStyle('A'.$i.':D'.$i)->applyFromArray(
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
					
					$sel->setCellValue('A'.$i, 'Lokasi : '.trim($row->fs_nm_wh));
					$sel->mergeCells('A'.$i.':D'.$i);
					
					$sel->getStyle('A'.$i.':D'.$i)->applyFromArray(
						array(
							'borders' => array(
								'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$sel->getStyle('A'.$i.':D'.$i)->applyFromArray(
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
					$sel->getStyle('A'.$j.':D'.$j)->applyFromArray(
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
					$sel->getStyle('D'.$q.':D'.$j)->applyFromArray(
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
					$sel->setCellValue('A'.$i, 'LAPORAN DAFTAR STOCK');
					$sel->mergeCells('A'.$i.':D'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$p = $i;
					$i = $i + 2;
					$k = $k + 2;
					
					$sel->setCellValue('A'.$i, 'No.');
					$sel->setCellValue('B'.$i, 'Tipe');
					$sel->setCellValue('C'.$i, 'Warna');
					$sel->setCellValue('D'.$i, 'Jumlah');
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('A'.$i.':D'.$i)->applyFromArray(
						array(
							'borders' => array(
								'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$sel->getStyle('A'.$i.':D'.$i)->applyFromArray(
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
				$sel->setCellValue('B'.$i, trim($row->fs_nm_product));
				$sel->setCellValue('C'.$i, trim($row->fs_nm_warna));
				$sel->setCellValue('D'.$i, trim($row->fn_total));
				$total = $total + trim($row->fn_total);
				$gtotal = $gtotal + trim($row->fn_total);
				$i++;
				$k++;
				$l++;
				
				$nmwh = trim($row->fs_nm_wh);
				$nmprod = trim($row->fs_nm_product);
			}
			//eof loop
			
			$sel->setCellValue('A'.$i, 'Total '.trim($nmwh).' : '.$total);
			$sel->mergeCells('A'.$i.':D'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('A'.$i.':D'.$i)->applyFromArray(
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
			
			$sel->setCellValue('A'.$i, 'Grand Total : '.$gtotal);
			$sel->mergeCells('A'.$i.':D'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('A'.$i.':D'.$i)->applyFromArray(
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
			$oExcel->getActiveSheet()->getStyle('A'.$j.':D'.$j)->applyFromArray(
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
			$oExcel->getActiveSheet()->getStyle('A'.$q.':A'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('B'.$q.':B'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('C'.$q.':C'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('D'.$q.':D'.$j)->applyFromArray($arr);
			$oExcel->getActiveSheet()->getStyle('D'.$q.':D'.$j)->applyFromArray(
				array(
					'borders' => array(
						'right' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$i = $i + 5;
			$k = $k + 5;
			$p = $i;
			$total = 0;
			
			$this->load->model('mRegStatus','',true);
			$ssql = $this->mRegStatus->stokjml();
			
			if ($ssql->num_rows() > 0)
			{
				$sel->setCellValue('A'.$i, 'No.');
				$sel->setCellValue('B'.$i, 'Tipe');
				$sel->setCellValue('C'.$i, 'Jumlah');
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$sel->getStyle('A'.$i.':C'.$i)->applyFromArray(
					array(
						'borders' => array(
							'top' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					)
				);
				$sel->getStyle('A'.$i.':C'.$i)->applyFromArray(
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
				$z = 1;
				
				foreach ($ssql->result() as $row)
				{
					if ($k > $pjghal)
					{
						$j = $i - 1;
						$sel->setBreak('A'.$j, PHPExcel_Worksheet::BREAK_ROW);
						$sel->getStyle('A'.$j.':C'.$j)->applyFromArray(
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
						$sel->getStyle('C'.$q.':C'.$j)->applyFromArray(
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
						$p = $i;
						$i = $i + 2;
						$k = $k + 2;
						
						$sel->setCellValue('A'.$i, 'No.');
						$sel->setCellValue('B'.$i, 'Tipe');
						$sel->setCellValue('C'.$i, 'Jumlah');
						$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sel->getStyle('A'.$i.':C'.$i)->applyFromArray(
							array(
								'borders' => array(
									'top' => array(
										'style' => PHPExcel_Style_Border::BORDER_THIN
									)
								)
							)
						);
						$sel->getStyle('A'.$i.':C'.$i)->applyFromArray(
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
					
					$sel->setCellValue('A'.$i, $z);
					$sel->setCellValue('B'.$i, trim($row->fs_nm_product));
					$sel->setCellValue('C'.$i, trim($row->fn_jml));
					$total = $total + trim($row->fn_jml);
					$i++;
					$k++;
					$z++;
				}
			}
			
			$sel->setCellValue('A'.$i, 'Grand Total : '.$total);
			$sel->mergeCells('A'.$i.':C'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('A'.$i.':C'.$i)->applyFromArray(
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
			$oExcel->getActiveSheet()->getStyle('A'.$j.':C'.$j)->applyFromArray(
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
			$oExcel->getActiveSheet()->getStyle('C'.$q.':C'.$j)->applyFromArray(
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
				'hasil'		=> 'No record!!'
			);
			echo json_encode($hasil);
		}
	}
}
?>