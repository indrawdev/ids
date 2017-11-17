<?php

class InfoJual extends CI_Controller
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
			$this->load->view('vinfojual');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function rangka()
	{
		$nstart = trim($this->input->post('start'));
		
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->rangka_jual2_all($rangka,$mesin,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->rangka_jual2($rangka,$mesin,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function printinfo()
	{
		ini_set('memory_limit', '-1');
		
		$TglAwal = trim($this->input->post('fd_tgl'));
		$TglAkhir = trim($this->input->post('fd_tgl2'));
		$sCekWH = trim($this->input->post('fs_kd_wh'));
		$sCustomerCode = trim($this->input->post('fs_kd_cust')) . trim($this->input->post('fs_custcount'));
		$sCustomerCount = trim($this->input->post('fs_custcount'));
		$sChassisNumber = trim($this->input->post('fs_rangka'));
		$sPilih = trim($this->input->post('fs_pilih'));
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tdailysales';
		$nmfile = 'dailysales-'.$jamskg;
		
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
		
		$oExcel->getActiveSheet()->setTitle('Daily Sales');
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
		
		$this->load->model('mInfoJual','',true);
		$ssql = $this->mInfoJual->CetakDailySales($TglAwal,$TglAkhir,$sCekWH,$sCustomerCode,$sChassisNumber,$sPilih);
		
		if ($ssql->num_rows() > 0)
		{
			$i = 1;
			$k = 1;
			$sel->setCellValue('A'.$i, 'Laporan Penjualan');
			$sel->mergeCells('A'.$i.':J'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$sel->getStyle('A'.$i)->getFont()->setSize(12);
			$p = $i;
			$i++;
			$k++;
			if ($sPilih == '0')
			{
				$sel->setCellValue('A'.$i, 'Periode: '. substr($TglAwal,6,2) .'-'. substr($TglAwal,4,2) .'-'. substr($TglAwal,0,4) .' s/d '. substr($TglAkhir,6,2) .'-'. substr($TglAkhir,4,2) .'-'. substr($TglAkhir,0,4));
			}
			$sel->mergeCells('A'.$i.':J'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'No');
			$sel->setCellValue('B'.$i, 'TGL');
			$sel->setCellValue('C'.$i, 'Nama');
			$sel->setCellValue('D'.$i, 'Tipe');
			$sel->setCellValue('E'.$i, 'No.Rangka');
			$sel->setCellValue('F'.$i, 'Harga Jual');
			$sel->setCellValue('G'.$i, 'Uang Muka');
			$sel->setCellValue('H'.$i, 'Subsidi');
			$sel->setCellValue('I'.$i, 'Lama Angs');
			$sel->setCellValue('J'.$i, 'Keterangan');
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			
			$sel->getStyle('A'.$i.':J'.$i)->applyFromArray(
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
			$sel->setCellValue('B'.$i, 'Faktur');
			$sel->setCellValue('C'.$i, 'Alamat');
			$sel->setCellValue('D'.$i, 'Warna');
			$sel->setCellValue('E'.$i, 'No.Mesin');
			$sel->setCellValue('F'.$i, 'Potongan');
			$sel->setCellValue('H'.$i, 'Piutang');
			$sel->setCellValue('I'.$i, 'Angs/Bulan');
			$sel->setCellValue('J'.$i, 'Sales');
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$sel->getStyle('A'.$i.':J'.$i)->applyFromArray(
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
					$sel->getStyle('A'.$j.':J'.$j)->applyFromArray(
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
					$sel->getStyle('J'.$q.':J'.$j)->applyFromArray(
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
					$sel->setCellValue('A'.$i, 'Laporan Penjualan');
					$sel->mergeCells('A'.$i.':J'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
					$sel->getStyle('A'.$i)->getFont()->setBold(true);
					$sel->getStyle('A'.$i)->getFont()->setSize(12);
					$i++;
					$k++;
					if ($sPilih == '0')
					{
						$sel->setCellValue('A'.$i, 'Periode: '. substr($TglAwal,6,2) .'-'. substr($TglAwal,4,2) .'-'. substr($TglAwal,0,4) .' s/d '. substr($TglAkhir,6,2) .'-'. substr($TglAkhir,4,2) .'-'. substr($TglAkhir,0,4));
					}
					$sel->mergeCells('A'.$i.':J'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$i = $i + 2;
					$k = $k + 2;
					
					$sel->setCellValue('A'.$i, 'No');
					$sel->setCellValue('B'.$i, 'TGL');
					$sel->setCellValue('C'.$i, 'Nama');
					$sel->setCellValue('D'.$i, 'Tipe');
					$sel->setCellValue('E'.$i, 'No.Rangka');
					$sel->setCellValue('F'.$i, 'Harga Jual');
					$sel->setCellValue('G'.$i, 'Uang Muka');
					$sel->setCellValue('H'.$i, 'Subsidi');
					$sel->setCellValue('I'.$i, 'Lama Angs');
					$sel->setCellValue('J'.$i, 'Keterangan');
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$sel->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					
					$sel->getStyle('A'.$i.':J'.$i)->applyFromArray(
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
					$sel->setCellValue('B'.$i, 'Faktur');
					$sel->setCellValue('C'.$i, 'Alamat');
					$sel->setCellValue('D'.$i, 'Warna');
					$sel->setCellValue('E'.$i, 'No.Mesin');
					$sel->setCellValue('F'.$i, 'Potongan');
					$sel->setCellValue('H'.$i, 'Piutang');
					$sel->setCellValue('I'.$i, 'Angs/Bulan');
					$sel->setCellValue('J'.$i, 'Sales');
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					
					$sel->getStyle('A'.$i.':J'.$i)->applyFromArray(
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
				
				$sel->setCellValue('A'.$i, $l);
				$sel->setCellValue('B'.$i, trim($row->refdt));
				$sel->setCellValue('C'.$i, trim($row->custnm));
				$sel->setCellValue('D'.$i, trim($row->product));
				$sel->setCellValue('E'.$i, trim($row->chasis));
				$sel->setCellValue('F'.$i, number_format(trim($row->unit_price), 2, '.', ','));
				$sel->setCellValue('G'.$i, number_format(trim($row->dp_amount), 2, '.', ','));
				$sel->setCellValue('H'.$i, number_format(trim($row->subsidi), 2, '.', ','));
				$sel->setCellValue('I'.$i, trim($row->nmterm));
				$sel->setCellValue('J'.$i, trim($row->keterangan));
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$i++;
				$k++;
				$sel->setCellValue('B'.$i, trim($row->refno));
				$sel->setCellValue('C'.$i, trim($row->custaddr));
				$sel->setCellValue('D'.$i, trim($row->colour));
				$sel->setCellValue('E'.$i, trim($row->machine));
				$sel->setCellValue('F'.$i, number_format(trim($row->discount), 2, '.', ','));
				$sel->setCellValue('H'.$i, number_format(trim($row->piutang), 2, '.', ','));
				$sel->setCellValue('I'.$i, number_format(trim($row->angsuran), 2, '.', ','));
				$sel->setCellValue('J'.$i, trim($row->sales));
				$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				
				$sel->getStyle('A'.$i.':J'.$i)->applyFromArray(
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
			$sel->getStyle('A'.$j.':J'.$j)->applyFromArray(
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
			$sel->getStyle('J'.$q.':J'.$j)->applyFromArray(
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
	
	function printinfo2()
	{
		ini_set('memory_limit', '-1');
		
		$TglAwal = trim($this->input->post('fd_tgl3'));
		$TglAkhir = trim($this->input->post('fd_tgl4'));
		$sSalesCode = trim($this->input->post('fs_kd_sales')) . trim($this->input->post('fs_salescount'));
		$sSalesCode2 = trim($this->input->post('fs_kd_sales2')) . trim($this->input->post('fs_salescount2'));
		$sSalesName = trim($this->input->post('fs_nm_sales'));
		$sSalesName2 = trim($this->input->post('fs_nm_sales2'));
		
		//jika nama sales 2 kosong, maka samakan dengan nama sales pertama
		if ($sSalesName2 == '') {
			$sSalesName = $sSalesName2;
		}
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tdailysales';
		$nmfile = 'dailysales-'.$jamskg;
		
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
		
		$oExcel->getActiveSheet()->setTitle('Daily Sales');
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
		
		$this->load->model('mInfoJual','',true);
		$ssql = $this->mInfoJual->CetakDailySalesBySales($TglAwal,$TglAkhir,$sSalesCode,$sSalesCode2);
		
		if ($ssql->num_rows() > 0)
		{
			$i = 1;
			$k = 1;
			$sel->setCellValue('A'.$i, 'Laporan Penjualan Per Sales');
			$sel->mergeCells('A'.$i.':i'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$sel->getStyle('A'.$i)->getFont()->setSize(12);
			$p = $i;
			$i++;
			$k++;
			$sel->setCellValue('A'.$i, 'Periode: '. substr($TglAwal,6,2) .'-'. substr($TglAwal,4,2) .'-'. substr($TglAwal,0,4) .' s/d '. substr($TglAkhir,6,2) .'-'. substr($TglAkhir,4,2) .'-'. substr($TglAkhir,0,4));
			$sel->mergeCells('A'.$i.':I'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'No. / TGL');
			$sel->setCellValue('B'.$i, 'Nama');
			$sel->setCellValue('C'.$i, 'Tipe');
			$sel->setCellValue('D'.$i, 'No.Rangka');
			$sel->setCellValue('E'.$i, 'Harga Jual');
			$sel->setCellValue('F'.$i, 'Uang Muka');
			$sel->setCellValue('G'.$i, 'Subsidi');
			$sel->setCellValue('H'.$i, 'Lama Angs');
			$sel->setCellValue('I'.$i, 'Keterangan');
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			
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
			$sel->setCellValue('A'.$i, 'Faktur');
			$sel->setCellValue('B'.$i, 'Alamat');
			$sel->setCellValue('C'.$i, 'Warna');
			$sel->setCellValue('D'.$i, 'No.Mesin');
			$sel->setCellValue('E'.$i, 'Potongan');
			$sel->setCellValue('G'.$i, 'Piutang');
			$sel->setCellValue('H'.$i, 'Angs/Bulan');
			$sel->setCellValue('I'.$i, 'Sales');
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
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
			$l++;
			
			foreach ($ssql->result() as $row)
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
					$sel->setCellValue('A'.$i, 'Laporan Penjualan Per Sales');
					$sel->mergeCells('A'.$i.':i'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
					$sel->getStyle('A'.$i)->getFont()->setBold(true);
					$sel->getStyle('A'.$i)->getFont()->setSize(12);
					$i++;
					$k++;
					$sel->setCellValue('A'.$i, 'Periode: '. substr($TglAwal,6,2) .'-'. substr($TglAwal,4,2) .'-'. substr($TglAwal,0,4) .' s/d '. substr($TglAkhir,6,2) .'-'. substr($TglAkhir,4,2) .'-'. substr($TglAkhir,0,4));
					$sel->mergeCells('A'.$i.':I'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$i = $i + 2;
					$k = $k + 2;
					
					$sel->setCellValue('A'.$i, 'No. / TGL');
					$sel->setCellValue('B'.$i, 'Nama');
					$sel->setCellValue('C'.$i, 'Tipe');
					$sel->setCellValue('D'.$i, 'No.Rangka');
					$sel->setCellValue('E'.$i, 'Harga Jual');
					$sel->setCellValue('F'.$i, 'Uang Muka');
					$sel->setCellValue('G'.$i, 'Subsidi');
					$sel->setCellValue('H'.$i, 'Lama Angs');
					$sel->setCellValue('I'.$i, 'Keterangan');
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					
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
					$sel->setCellValue('A'.$i, 'Faktur');
					$sel->setCellValue('B'.$i, 'Alamat');
					$sel->setCellValue('C'.$i, 'Warna');
					$sel->setCellValue('D'.$i, 'No.Mesin');
					$sel->setCellValue('E'.$i, 'Potongan');
					$sel->setCellValue('G'.$i, 'Piutang');
					$sel->setCellValue('H'.$i, 'Angs/Bulan');
					$sel->setCellValue('I'.$i, 'Sales');
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					
					$sel->getStyle('A'.$i.':I'.$i)->applyFromArray(
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
				
				$sel->setCellValue('A'.$i, $l.'. '.trim($row->refdt));
				$sel->setCellValue('B'.$i, trim($row->custnm));
				$sel->setCellValue('C'.$i, trim($row->product));
				$sel->setCellValue('D'.$i, trim($row->chasis));
				$sel->setCellValue('E'.$i, number_format(trim($row->unit_price), 2, '.', ','));
				$sel->setCellValue('F'.$i, number_format(trim($row->dp_amount), 2, '.', ','));
				$sel->setCellValue('G'.$i, number_format(trim($row->subsidi), 2, '.', ','));
				$sel->setCellValue('H'.$i, trim($row->nmterm));
				$sel->setCellValue('I'.$i, trim($row->keterangan));
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$i++;
				$k++;
				$sel->setCellValue('A'.$i, trim($row->refno));
				$sel->setCellValue('B'.$i, trim($row->custaddr));
				$sel->setCellValue('C'.$i, trim($row->colour));
				$sel->setCellValue('D'.$i, trim($row->machine));
				$sel->setCellValue('E'.$i, number_format(trim($row->discount), 2, '.', ','));
				$sel->setCellValue('G'.$i, number_format(trim($row->piutang), 2, '.', ','));
				$sel->setCellValue('H'.$i, number_format(trim($row->angsuran), 2, '.', ','));
				$sel->setCellValue('I'.$i, trim($row->sales));
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				
				$sel->getStyle('A'.$i.':I'.$i)->applyFromArray(
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
			$sel->getStyle('A'.$j.':I'.$j)->applyFromArray(
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
			$sel->getStyle('I'.$q.':I'.$j)->applyFromArray(
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
	
	function printinfo3()
	{
		ini_set('memory_limit', '-1');
		
		$TglAwal = trim($this->input->post('fd_tgl5'));
		$TglAkhir = trim($this->input->post('fd_tgl6'));
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tdailysalesrekap';
		$nmfile = 'dailysalesrekap-'.$jamskg;
		
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
		
		$oExcel->getActiveSheet()->setTitle('Daily Sales');
		$oExcel->getActiveSheet()->setShowGridlines(false);
		
		$oExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		// $oExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		
		$oExcel->getActiveSheet()->getPageMargins()->setTop(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setBottom(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setLeft(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setRight(0);
		
		$oExcel->getDefaultStyle()->getFont()->setName('Calibri');
		$oExcel->getDefaultStyle()->getFont()->setSize(10);
		$sel = $oExcel->getActiveSheet();
		
		$this->load->model('mInfoJual','',true);
		$ssql = $this->mInfoJual->CetakDailySalesRekap($TglAwal,$TglAkhir);
		
		$data = array();
		$data = $ssql;
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			
			$UserName = trim($this->session->userdata('gUser'));
			
			$pjghal = 57;
			
			$i = 1;
			$l = 0; // no urut
			
			$k = 1; // pjg hal
			$p = 1; // awal hal
			
			$sel->setCellValue('A'.$i, 'Laporan Penjualan Rekap');
			$sel->mergeCells('A'.$i.':D'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$sel->getStyle('A'.$i)->getFont()->setSize(12);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Periode: '. substr($TglAwal,6,2) .'-'. substr($TglAwal,4,2) .'-'. substr($TglAwal,0,4) .' s/d '. substr($TglAkhir,6,2) .'-'. substr($TglAkhir,4,2) .'-'. substr($TglAkhir,0,4));
			$sel->mergeCells('A'.$i.':D'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'CABANG');
			$sel->setCellValue('B'.$i, 'TIPE');
			$sel->setCellValue('C'.$i, 'WARNA');
			$sel->setCellValue('D'.$i, 'QTY');
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i.':D'.$i)->applyFromArray(
				array(
					'borders' => array(
						'top' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						),
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$sel->getStyle('C'.$i.':C'.$i)->applyFromArray(
				array(
					'borders' => array(
						'left' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$i++;
			$k++;
			$n = $i;
			
			$NamaDept = '';
			$Total = 0;
			
			foreach ($data->result() as $row)
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
					
					$r = $i - 1;
					$sel->getStyle('C'.$n.':C'.$r)->applyFromArray(
						array(
							'borders' => array(
								'left' => array(
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
					$n = $i + 3;
					$sel->setCellValue('A'.$i, 'Laporan Penjualan Rekap');
					$sel->mergeCells('A'.$i.':D'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
					$sel->getStyle('A'.$i)->getFont()->setBold(true);
					$sel->getStyle('A'.$i)->getFont()->setSize(12);
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i, 'Periode: '. substr($TglAwal,6,2) .'-'. substr($TglAwal,4,2) .'-'. substr($TglAwal,0,4) .' s/d '. substr($TglAkhir,6,2) .'-'. substr($TglAkhir,4,2) .'-'. substr($TglAkhir,0,4));
					$sel->mergeCells('A'.$i.':D'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$i = $i + 2;
					$k = $k + 2;
					
					$sel->setCellValue('A'.$i, 'CABANG');
					$sel->setCellValue('B'.$i, 'TIPE');
					$sel->setCellValue('C'.$i, 'WARNA');
					$sel->setCellValue('D'.$i, 'QTY');
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('A'.$i.':D'.$i)->applyFromArray(
						array(
							'borders' => array(
								'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								),
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$sel->getStyle('C'.$i.':C'.$i)->applyFromArray(
						array(
							'borders' => array(
								'left' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$i++;
					$k++;
					$sel->setCellValue('A'.$i, trim($row->deptnm));
				}
				
				if (trim($NamaDept) == '' or trim($NamaDept) <> trim($row->deptnm))
				{
					if (trim($NamaDept) <> '' and trim($NamaDept) <> trim($row->deptnm))
					{
						$r = $i - 1;
						$sel->getStyle('C'.$n.':C'.$r)->applyFromArray(
							array(
								'borders' => array(
									'left' => array(
										'style' => PHPExcel_Style_Border::BORDER_THIN
									)
								)
							)
						);
						$sel->setCellValue('B'.$i, 'TOTAL '.trim($NamaDept).' :');
						$sel->setCellValue('D'.$i, $Total);
						$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$sel->getStyle('B'.$i.':D'.$i)->applyFromArray(
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
						$Total = 0;
						$i++;
						$k++;
						$n = $i;
					}
					$sel->setCellValue('A'.$i, trim($row->deptnm));
				}
				
				$sel->setCellValue('B'.$i, trim($row->product));
				$sel->setCellValue('C'.$i, trim($row->colour));
				$sel->setCellValue('D'.$i, trim($row->qty));
				$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$i++;
				$k++;
				
				$Total = $Total + trim($row->qty);
				$NamaDept = trim($row->deptnm);
			}
			
			$r = $i - 1;
			$sel->getStyle('C'.$n.':C'.$r)->applyFromArray(
				array(
					'borders' => array(
						'left' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$sel->setCellValue('B'.$i, 'TOTAL '.trim($NamaDept).' :');
			$sel->setCellValue('D'.$i, $Total);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('B'.$i.':D'.$i)->applyFromArray(
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
			
			$q = $p + 3;
			$arr = array(
				'borders' => array(
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$sel->getStyle('A'.$q.':A'.$i)->applyFromArray($arr);
			$sel->getStyle('B'.$q.':B'.$i)->applyFromArray($arr);
			$sel->getStyle('D'.$q.':D'.$i)->applyFromArray($arr);
			$sel->getStyle('D'.$q.':D'.$i)->applyFromArray(
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