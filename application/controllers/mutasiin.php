<?php

class MutasiIn extends CI_Controller
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
			$this->load->view('vmutasiin');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function grid_prod()
	{
		if (trim($this->input->post('fs_refno')) <> '')
		{
			$kddept = trim($this->input->post('fs_kd_dept'));
			$kdcount = trim($this->input->post('fs_count'));
			$refno = trim($this->input->post('fs_refno'));
		}
		else
		{
			$kddept = trim($this->input->post('fs_kd_tdept'));
			$kdcount = trim($this->input->post('fs_tcount'));
			$refno = trim($this->input->post('fs_kd_out'));
		}
		
		$this->load->model('mMutasiIn','',true);
		$ssql = $this->mMutasiIn->grid_prod($kddept,$kdcount,$refno);
		
		echo json_encode($ssql->result());
	}
	
	function grid_reg()
	{
		if (trim($this->input->post('fs_refno')) <> '')
		{
			$kddept = trim($this->input->post('fs_kd_tdept'));
			$kdcount = trim($this->input->post('fs_tcount'));
			$kdtrx = '3300';
			$kdstrx = '0100';
			$refno = trim($this->input->post('fs_refno'));
			
			$this->load->model('mMutasiIn','',true);
			$ssql = $this->mMutasiIn->grid_reg($kddept,$kdcount,$kdtrx,$kdstrx,$refno);
		}
		else
		{
			$kddept = trim($this->input->post('fs_kd_dept'));
			$kdcount = trim($this->input->post('fs_count'));
			$kdtrx = '3400';
			$kdstrx = '0100';
			$refno = trim($this->input->post('fs_kd_out'));
			
			$this->load->model('mMutasiOut','',true);
			$ssql = $this->mMutasiOut->grid_reg($kddept,$kdcount,$kdtrx,$kdstrx,$refno);
		}
		
		echo json_encode($ssql->result());
	}
	
	function refnoout()
	{
		$nstart = trim($this->input->post('start'));
		
		$refno = trim($this->input->post('fs_refno'));
		$cari = trim($this->input->post('fs_cari'));
		$kdtrx = '3400';
		$kdstrx = '0100';
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->mutasi_out_all($refno,$kdtrx,$kdstrx,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->mutasi_out($refno,$kdtrx,$kdstrx,$nstart,$total,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function refno()
	{
		$nstart = trim($this->input->post('start'));
		
		$refno = trim($this->input->post('fs_refno'));
		$cari = trim($this->input->post('fs_cari'));
		$kdtrx = '3300';
		$kdstrx = '0100';
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->mutasi_all($refno,$kdtrx,$kdstrx,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->mutasi($refno,$kdtrx,$kdstrx,$nstart,$total,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function print_mutasiin()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tmutasiout';
		$nmfile = 'mutasiout-'.$jamskg;
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
		
		$oExcel->getActiveSheet()->setTitle('Surat Pengiriman Unit');
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
		
		//query dg nomor refno
		
		$refno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mMutasiIn','',true);
		$ssql = $this->mMutasiIn->mutasiin_cetak($refno);
		
		$data = array();
		$data = $ssql;
		$jumlah_unit = $ssql->num_rows();
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			
			$NoMutasi = $ssql->fs_refno;
			$NamaCabangFrom = $ssql->fs_nm_deptt;
			$AlamatCabangFrom = $ssql->fs_addrt;
			$KotaFrom = $ssql->fs_nm_kotat;
			
			$NamaCabangTo = $ssql->fs_nm_deptf;
			$AlamatCabangTo = $ssql->fs_addrf;
			$KotaTo = $ssql->fs_nm_kotaf;
			
			$UserName = trim($this->session->userdata('gUser'));
			
			$pjghal = 57;
			$i = 1;
			$l = 0; // no urut
			
			$k = 1; // pjg hal
			$p = 1; // awal hal
			
			$sel->setCellValue('A'.$i, 'SURAT PENGIRIMAN UNIT');
			
			$sel->mergeCells('A'.$i.':F'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i,'No : '.$NoMutasi );
			$sel->mergeCells('A'.$i.':F'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('E'.$i, 'Kepada Yth.');
			$i++;
			$k++;
			
			$sel->setCellValue('B'.$i, trim($NamaCabangFrom));
			$sel->mergeCells('B'.$i.':D'.$i);
			$sel->setCellValue('E'.$i, trim($NamaCabangTo));
			$sel->mergeCells('E'.$i.':F'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('B'.$i, trim($AlamatCabangFrom));
			$sel->mergeCells('B'.$i.':D'.$i);
			$sel->setCellValue('E'.$i, trim($AlamatCabangTo));
			$sel->mergeCells('E'.$i.':F'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('B'.$i, trim($KotaFrom));
			$sel->mergeCells('B'.$i.':C'.$i);
			$sel->setCellValue('E'.$i, trim($KotaTo));
			$sel->mergeCells('E'.$i.':F'.$i);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('B'.$i, 'Asal Cabang');
			$sel->setCellValue('C'.$i, ': '.trim($NamaCabangFrom));
			$sel->mergeCells('C'.$i.':D'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('B'.$i, 'Tujuan Cabang');
			$sel->setCellValue('C'.$i, ': '.trim($NamaCabangTo));
			$sel->mergeCells('C'.$i.':D'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('B'.$i, 'Mohon diterima barang-barang di bawah ini berupa '.trim($jumlah_unit).' UNIT kendaraan beserta kelengkapannya sebagai berikut:');
			$sel->mergeCells('B'.$i.':F'.$i);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'No');
			$sel->setCellValue('B'.$i, 'Produk');
			$sel->setCellValue('C'.$i, 'Tahun');
			$sel->setCellValue('D'.$i, 'Warna');
			$sel->setCellValue('E'.$i, 'No Rangka');
			$sel->setCellValue('F'.$i, 'No Mesin');
			
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
			
			//liat detail
			foreach ($data->result() as $row) 
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
					$q = $p + 12;
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
					$sel->setCellValue('A'.$i, 'SURAT PENGIRIMAN UNIT');
					
					$sel->mergeCells('A'.$i.':F'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
					$sel->getStyle('A'.$i)->getFont()->setBold(true);
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i,'No : '.$NoMutasi );
					$sel->mergeCells('A'.$i.':F'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$i = $i + 2;
					$k = $k + 2;
					
					$sel->setCellValue('E'.$i, 'Kepada Yth.');
					$i++;
					$k++;
					
					$sel->setCellValue('B'.$i, trim($NamaCabangFrom));
					$sel->mergeCells('B'.$i.':D'.$i);
					$sel->setCellValue('E'.$i, trim($NamaCabangTo));
					$sel->mergeCells('E'.$i.':F'.$i);
					$i++;
					$k++;
					
					$sel->setCellValue('B'.$i, trim($AlamatCabangFrom));
					$sel->mergeCells('B'.$i.':D'.$i);
					$sel->setCellValue('E'.$i, trim($AlamatCabangTo));
					$sel->mergeCells('E'.$i.':F'.$i);
					$i++;
					$k++;
					
					$sel->setCellValue('B'.$i, trim($KotaFrom));
					$sel->mergeCells('B'.$i.':C'.$i);
					$sel->setCellValue('E'.$i, trim($KotaTo));
					$sel->mergeCells('E'.$i.':F'.$i);
					$i = $i + 2;
					$k = $k + 2;
					
					$sel->setCellValue('B'.$i, 'Asal Cabang');
					$sel->setCellValue('C'.$i, ': '.trim($NamaCabangFrom));
					$sel->mergeCells('C'.$i.':D'.$i);
					$i++;
					$k++;
					
					$sel->setCellValue('B'.$i, 'Tujuan Cabang');
					$sel->setCellValue('C'.$i, ': '.trim($NamaCabangTo));
					$sel->mergeCells('C'.$i.':D'.$i);
					$i++;
					$k++;
					
					$sel->setCellValue('B'.$i, 'Mohon diterima barang-barang di bawah ini berupa '.trim($jumlah_unit).' UNIT kendaraan beserta kelengkapannya sebagai berikut:');
					$sel->mergeCells('B'.$i.':F'.$i);
					$i = $i + 2;
					$k = $k + 2;
					
					$sel->setCellValue('A'.$i, 'No');
					$sel->setCellValue('B'.$i, 'Produk');
					$sel->setCellValue('C'.$i, 'Tahun');
					$sel->setCellValue('D'.$i, 'Warna');
					$sel->setCellValue('E'.$i, 'No Rangka');
					$sel->setCellValue('F'.$i, 'No Mesin');
					
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
				}
				
				$sel->setCellValue('A'.$i, trim($l));
				$sel->setCellValue('B'.$i, trim($row->fs_nm_product));
				$sel->setCellValue('C'.$i, trim($row->fs_year));
				$sel->setCellValue('D'.$i, trim($row->fs_nm_warna));
				$sel->setCellValue('E'.$i, trim($row->fs_rangka));
				$sel->setCellValue('F'.$i, trim($row->fs_machine));
				
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
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
			
			$q = $p + 12;
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
			$i++;
			$k++;	
			
			$sel->setCellValue('B'.$i,'Kelengkapan Unit Berupa :');
			$sel->mergeCells('B'.$i.':D'.$i);
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->getStyle('B'.$i,$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			// $sel->getStyle('B'.$i,$i)->getFont()->setBold(true);
			$i++;
			$k++;
			
			$sel->setCellValue('B'.$i,'[ ] Helm');
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->setCellValue('C'.$i,'[ ] Toolkit');
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->setCellValue('D'.$i,'[ ] Buku Service');
			$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->setCellValue('E'.$i,'[ ] Kunci Kontak');
			$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$i++;
			$k++;
			
			$sel->setCellValue('B'.$i,'[ ] Kaca Spion');
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->setCellValue('C'.$i,'[ ] Jaket');
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->setCellValue('D'.$i,'[ ] Buku Manual');
			$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sel->setCellValue('E'.$i,'[ ] Lain-Lain...');
			$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$i =$i + 10;
			$k = $k + 10;
			
			$sel->setCellValue('B'.$i,'Dibuat');
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->setCellValue('C'.$i,'Diketahui');
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->setCellValue('D'.$i,'Pengirim');
			$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->setCellValue('E'.$i,'Penerima');
			$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$i = $i + 30;
			$k = $k + 30;
			
			$sel->setCellValue('B'.$i,'(.................)');
			$sel->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->setCellValue('C'.$i,'(.................)');
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->setCellValue('D'.$i,'(.................)');
			$sel->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->setCellValue('E'.$i,'(.................)');
			$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			
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