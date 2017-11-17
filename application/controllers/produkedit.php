<?php

class ProdukEdit extends CI_Controller
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
			$this->load->view('vprodukedit');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function ceksave()
	{
		$kdproduk = trim($this->input->post('fs_kd_product'));
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		$rangka2 = trim($this->input->post('fs_rangka2'));
		$mesin2 = trim($this->input->post('fs_mesin2'));
		
		if (trim($kdproduk) == '' or trim($rangka) == '' or trim($mesin) == '')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, Product Code, Chassis or Machine unknown!!'
			);
			echo json_encode($hasil);
		}
		else
		{
			$this->load->model('mProdukEdit','',true);
			$ssql = $this->mProdukEdit->cek_rangkamesin2($kdproduk,$rangka,$mesin,$rangka2,$mesin2);
			
			if ($ssql->num_rows() > 0)
			{
				$hasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Saving Canceled, New Chassis and New Machine already exists!!'
				);
				echo json_encode($hasil);
				return;
			}
			
			$this->load->model('mProdukEdit','',true);
			$ssql = $this->mProdukEdit->cek_rangkamesin($kdproduk,$rangka,$mesin);
			
			if ($ssql->num_rows() > 0)
			{
				$hasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Product Code already exists, do you want to update it?'
				);
				echo json_encode($hasil);
			}
			else
			{
				$hasil = array(
					'sukses'	=> true,
					'hasil'		=> 'lanjut'
				);
				echo json_encode($hasil);
			}
		}
	}
	
	function save()
	{
		$kdproduk = trim($this->input->post('fs_kd_product'));
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		$reg = trim($this->input->post('fs_register'));
		$cc = trim($this->input->post('fs_cc'));
		$tahun = trim($this->input->post('fs_tahun'));
		$kdwarna = trim($this->input->post('fs_kd_warna'));
		
		$rangka2 = trim($this->input->post('fs_rangka2'));
		$mesin2 = trim($this->input->post('fs_mesin2'));
		$cc2 = trim($this->input->post('fs_cc2'));
		$tahun2 = trim($this->input->post('fs_tahun2'));
		$kdwarna2 = trim($this->input->post('fs_kd_warna2'));
		$nmwarna2 = trim($this->input->post('fs_nm_warna2'));
		
		
		//update tm_icregister
		$data = array(
			'fs_rangka'			=> trim($rangka2),
			'fs_machine'		=> trim($mesin2),
			'fn_silinder'		=> trim($cc2),
			
			'fd_thnpembuatan'	=> trim($tahun2),
			'fd_thnperakitan'	=> trim($tahun2),
			'fs_kd_warna'		=> trim($kdwarna2)
		);
		
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_kd_product = '".trim($kdproduk)."'
					AND	fs_rangka = '".trim($rangka)."'
					AND	fs_machine = '".trim($mesin)."'
					AND	fs_kd_warna = '".trim($kdwarna)."'";
		
		$this->db->where($where);
		$this->db->update('tm_icregister', $data);
		//eof update tm_icregister
		
		
		//update tm_posregsold
		$data = array(
			'fs_chasis'		=> trim($rangka2),
			'fs_machine'	=> trim($mesin2)
		);
		
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_kd_product = '".trim($kdproduk)."'
					AND	fs_chasis = '".trim($rangka)."'
					AND	fs_machine = '".trim($mesin)."'";
		
		$this->db->where($where);
		$this->db->update('tm_posregsold', $data);
		//eof update tm_posregsold
		
		
		//update tx_icmoveregsr
		$data = array(
			'fs_rangka'	=> trim($rangka2),
			'fs_mesin'	=> trim($mesin2)
		);
		
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_register = '".trim($reg)."'
					AND	fs_rangka = '".trim($rangka)."'
					AND	fs_mesin = '".trim($mesin)."'";
		
		$this->db->where($where);
		$this->db->update('tx_icmoveregsr', $data);
		//eof update tx_icmoveregsr
		
		//update tx_mutasidbd
		$data = array(
			'fs_rangka'		=> trim($rangka2),
			'fs_mesin'		=> trim($mesin2),
			'fn_cc'			=> trim($cc2),
			
			'fn_thn'		=> trim($tahun2),
			'fs_kd_warna'	=> trim($kdwarna2),
			'fs_nm_warna'	=> trim($nmwarna2)
		);
		
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_kd_product = '".trim($kdproduk)."'
					AND	fs_rangka = '".trim($rangka)."'
					AND	fs_mesin = '".trim($mesin)."'
					AND	fs_kd_warna = '".trim($kdwarna)."'";
		
		$this->db->where($where);
		$this->db->update('tx_mutasidbd', $data);
		//eof update tx_mutasidbd
		
		//simpan log
		$data = array(
			'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
			'fs_kd_dept'	=> trim($this->session->userdata('gDept')),
			'fs_count'		=> trim($this->session->userdata('gCount')),
			'fs_kd_product'	=> trim($kdproduk),
			
			'fs_rangka'		=> trim($rangka),
			'fs_mesin'		=> trim($mesin),
			'fs_silinder'	=> trim($cc),
			'fs_tahun'		=> trim($tahun),
			'fs_kd_warna'	=> trim($kdwarna),
			
			'fs_rangka2'	=> trim($rangka2),
			'fs_mesin2'		=> trim($mesin2),
			'fs_silinder2'	=> trim($cc2),
			'fs_tahun2'		=> trim($tahun2),
			'fs_kd_warna2'	=> trim($kdwarna2),
			
			'fs_usrcrt'		=> trim($this->session->userdata('gUser')),
			'fd_usrcrt'		=> trim(date('Y-m-d H:i:s'))
		);
		$this->db->insert('tx_produk_ed', $data);
		
		//eof simpan log
		$hasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Saving Product Update Success'
		);
		echo json_encode($hasil);
	}
	
	function excel_histori()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tprodukedit';
		$nmfile = 'produkedit-'.$jamskg;
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
		$oExcel->getActiveSheet()->setTitle('Histori');
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
		
		$this->load->model('mProdukEdit','',true);
		$ssql = $this->mProdukEdit->produk_edit_histori($xtgl,$xtgl2);
		
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
			$sel->setCellValue('A'.$i, 'Histori Edit Produk');
			$sel->mergeCells('A'.$i.':N'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$sel->getStyle('A'.$i)->getFont()->setSize(12);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Periode: '.trim($tgl).' s/d '.trim($tgl2));
			$sel->mergeCells('A'.$i.':N'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'No');
			$sel->setCellValue('B'.$i, 'Dept');
			$sel->setCellValue('C'.$i, 'Kode');
			$sel->setCellValue('D'.$i, 'Tipe');
			$sel->setCellValue('E'.$i, 'Rangka Lama');
			$sel->setCellValue('F'.$i, 'Rangka Baru');
			$sel->setCellValue('G'.$i, 'Mesin Lama');
			$sel->setCellValue('H'.$i, 'Mesin Baru');
			$sel->setCellValue('I'.$i, 'CC Lama');
			$sel->setCellValue('J'.$i, 'CC Baru');
			$sel->setCellValue('K'.$i, 'Thn Lama');
			$sel->setCellValue('L'.$i, 'Thn Baru');
			$sel->setCellValue('M'.$i, 'Warna Lama');
			$sel->setCellValue('N'.$i, 'Warna Baru');
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
			
			$sel->getStyle('A'.$i.':N'.$i)->applyFromArray(
				array(
					'borders' => array(
						'top' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$sel->getStyle('A'.$i.':N'.$i)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$sel->getStyle('A'.$i.':'.'N'.$i)->applyFromArray($warnakolom);
			$i++;
			$k++;
			$l++;
			
			foreach ($ssql->result() as $row)
			{
				if ($k > $pjghal)
				{
					$j = $i - 1;
					$sel->setBreak('A'.$j, PHPExcel_Worksheet::BREAK_ROW);
					$sel->getStyle('A'.$j.':N'.$j)->applyFromArray(
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
					$sel->getStyle('N'.$q.':N'.$j)->applyFromArray(
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
					$sel->setCellValue('A'.$i, 'Histori Edit Produk');
					$sel->mergeCells('A'.$i.':N'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
					$sel->getStyle('A'.$i)->getFont()->setBold(true);
					$sel->getStyle('A'.$i)->getFont()->setSize(12);
					$i++;
					$k++;
					
					$sel->setCellValue('A'.$i, 'Periode: '.trim($tgl).' s/d '.trim($tgl2));
					$sel->mergeCells('A'.$i.':N'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$i = $i + 2;
					$k = $k + 2;
					
					$sel->setCellValue('A'.$i, 'No');
					$sel->setCellValue('B'.$i, 'Dept');
					$sel->setCellValue('C'.$i, 'Kode');
					$sel->setCellValue('D'.$i, 'Tipe');
					$sel->setCellValue('E'.$i, 'Rangka Lama');
					$sel->setCellValue('F'.$i, 'Rangka Baru');
					$sel->setCellValue('G'.$i, 'Mesin Lama');
					$sel->setCellValue('H'.$i, 'Mesin Baru');
					$sel->setCellValue('I'.$i, 'CC Lama');
					$sel->setCellValue('J'.$i, 'CC Baru');
					$sel->setCellValue('K'.$i, 'Thn Lama');
					$sel->setCellValue('L'.$i, 'Thn Baru');
					$sel->setCellValue('M'.$i, 'Warna Lama');
					$sel->setCellValue('N'.$i, 'Warna Baru');
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
					
					$sel->getStyle('A'.$i.':N'.$i)->applyFromArray(
						array(
							'borders' => array(
								'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$sel->getStyle('A'.$i.':N'.$i)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$sel->getStyle('A'.$i.':'.'N'.$i)->applyFromArray($warnakolom);
					$q = $i;
					$i++;
					$k++;
				}
				
				$sel->setCellValue('A'.$i, trim($l));
				$sel->setCellValue('B'.$i, trim($row->fs_nm_dept));
				$sel->setCellValue('C'.$i, trim($row->fs_kd_product));
				$sel->setCellValue('D'.$i, trim($row->fs_nm_product));
				$sel->setCellValue('E'.$i, trim($row->fs_rangka));
				$sel->setCellValue('F'.$i, trim($row->fs_rangka2));
				$sel->setCellValue('G'.$i, trim($row->fs_mesin));
				$sel->setCellValue('H'.$i, trim($row->fs_mesin2));
				$sel->setCellValue('I'.$i, trim($row->fs_cc));
				$sel->setCellValue('J'.$i, trim($row->fs_cc2));
				$sel->setCellValue('K'.$i, trim($row->fs_tahun));
				$sel->setCellValue('L'.$i, trim($row->fs_tahun2));
				$sel->setCellValue('M'.$i, trim($row->fs_nm_warna));
				$sel->setCellValue('N'.$i, trim($row->fs_nm_warna2));
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->getStyle('K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->getStyle('L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				if ($k % 2 <> 0) {
					$sel->getStyle('A'.$i.':'.'N'.$i)->applyFromArray($warnakolom);
				}
				$i++;
				$k++;
				$l++;
			}
			$j = $i - 1;
			$sel->getStyle('A'.$j.':N'.$j)->applyFromArray(
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
			$sel->getStyle('N'.$q.':N'.$j)->applyFromArray(
				array(
					'borders' => array(
						'right' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			foreach(range('A','N') as $idkolom)
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