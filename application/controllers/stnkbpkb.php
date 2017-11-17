<?php

class StnkBpkb extends CI_Controller
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
			$this->load->view('vstnkbpkb');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function cust_list()
	{
		$nstart = trim($this->input->post('start'));
		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mStnkBpkb','',true);
		$ssql = $this->mStnkBpkb->custlist_all($cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mStnkBpkb->custlist($cari,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function ceksave()
	{
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		$tglstnk = trim($this->input->post('fd_stnk'));
		
		if ($rangka == '' or $mesin == '')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, Chassis or Machine unknown!!'
			);
			echo json_encode($hasil);
			return;
		}
		else
		{
			$this->load->model('mStnkBpkb','',true);
			$ssql = $this->mStnkBpkb->cek_tglstnk($rangka,$mesin);
			
			if ($ssql->num_rows() > 0)
			{
				$ssql = $ssql->row();
				if (trim($ssql->fd_stnk) <> '' or trim($ssql->fd_bpkb) <> '')
				{
					$hasil = array(
						'sukses'	=> true,
						'hasil'		=> 'Update record?'
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
			else
			{
				$hasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Saving Failed, Record unknown!!'
				);
				echo json_encode($hasil);
			}
		}
	}
	
	function save()
	{
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		$tglstnk = trim($this->input->post('fd_stnk'));
		$tglbpkb = trim($this->input->post('fd_bpkb'));
		$nopol = trim($this->input->post('fs_nopol'));
		
		$this->load->model('mStnkBpkb','',true);
		$ssql = $this->mStnkBpkb->cek_tglstnk($rangka,$mesin);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			if (trim($ssql->fd_stnk) <> '' or trim($ssql->fd_bpkb) <> '')
			{
				$xupdate = true;
			}
			else
			{
				$xupdate = false;
			}
		}
		
		$data = array(
			'fd_stnk'	=> trim($tglstnk),
			'fd_bpkb'	=> trim($tglbpkb),
			'fs_nopol'	=> trim($nopol)
		);
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_rangka = '".trim($rangka)."'
					AND	fs_machine = '".trim($mesin)."'";
		
		$this->db->where($where);
		$this->db->update('tm_icregister', $data);
		
		if ($xupdate == false)
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving STNK-BPKB Success'
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving STNK-BPKB Update Success'
			);
			echo json_encode($hasil);
		}
	}
	
	function cekremove()
	{
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		
		if ($rangka == '' or $mesin == '')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, Chassis or Machine unknown!!'
			);
			echo json_encode($hasil);
			return;
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Remove STNK & BPKB Info?'
			);
			echo json_encode($hasil);
		}
	}
	
	function remove()
	{
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		
		$data = array(
			'fs_stnk'	=> '',
			'fd_stnk'	=> '',
			'fs_bpkb'	=> '',
			'fd_bpkb'	=> '',
			'fs_nopol'	=> ''
		);
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_rangka = '".trim($rangka)."'
					AND	fs_machine = '".trim($mesin)."'";
		
		$this->db->where($where);
		$this->db->update('tm_icregister', $data);
		
		$hasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Remove STNK & BPKB Info Success'
		);
		echo json_encode($hasil);
	}
	
	function print_stnk()
	{ 
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tterima_stnk';
		$nmfile = 'terima_stnk-'.$jamskg;
		
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
		
		$oExcel->getActiveSheet()->setTitle('Terima STNK');
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
		
		$sKdComp = trim($this->session->userdata('gComp'));
		$sKdDept = trim($this->session->userdata('gDept'));
		$sKdCount = trim($this->session->userdata('gCount'));
		
		$xRangka = trim($this->input->post('fs_rangka'));
		$xMesin = trim($this->input->post('fs_mesin'));
		
		$this->load->model('mStnkBpkb','',true);
		$ssql = $this->mStnkBpkb->cetak_stnk($xRangka,$xMesin);
		
		if ($ssql->num_rows() > 0)
		{
			$pjghal = 57;
			$i = 1;
			$l = 0; // no urut
			
			$k = 1; // pjg hal
			$p = 1; // awal hal
			
			// date_default_timezone_set("Asia/Bangkok");
			// $sel->setCellValue('B'.$i,'Print : '.date('d-m-Y H:i:s'));
			// $sel->setCellValue('F'.$i,'User :'.trim($this->session->userdata('gUser')));
			
			$ssql = $ssql->row();
			
			for ($z = 1; $z <= 2; $z++) {
				$i = $i + 2;
				$k = $k + 2;
				$sel->setCellValue('A'.$i,'TANDA TERIMA');
				$sel->mergeCells('A'.$i.':G'.$i);
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
				$sel->getStyle('A'.$i)->getFont()->setBold(true);
				$sel->getStyle('A'.$i)->getFont()->setSize(12);
				$i = $i + 2;
				$k = $k + 2;
				
				$sel->setCellValue('A'.$i, 'Telah diterima dari '.trim($ssql->fs_nm_cabang). ' berupa 1 (satu) buah STNK dengan keterangan sebagai berikut :');
				$sel->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				$sel->getStyle('A'.$i)->getAlignment()->setWrapText(true);
				$j = $i + 1;
				$sel->mergeCells('A'.$i.':G'.$j);
				$i = $i + 2;
				$k = $k + 2;
				
				$sel->setCellValue('A'.$i, 'Nama');
				$sel->setCellValue('B'.$i, ':');
				$sel->setCellValue('C'.$i, trim($ssql->fs_nm_customer));
				$sel->mergeCells('C'.$i.':G'.$i);
				$i++;
				$k++;
				
				$sel->setCellValue('A'.$i, 'Alamat');
				$sel->setCellValue('B'.$i, ':');
				$sel->setCellValue('C'.$i, trim($ssql->fs_almt_customer));
				$sel->getStyle('C'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				$sel->getStyle('C'.$i)->getAlignment()->setWrapText(true);
				$j = $i + 1;
				$sel->mergeCells('C'.$i.':G'.$j);
				$i = $i + 2;
				$k = $k + 2;
				
				$sel->setCellValue('A'.$i, 'No STNK');
				$sel->setCellValue('B'.$i, ':');
				$sel->setCellValue('C'.$i, trim($ssql->fs_stnk));
				$sel->mergeCells('C'.$i.':G'.$i);
				$i = $i + 2;
				$k = $k + 2;
				
				$sel->setCellValue('A'.$i,'Kendaraan Tipe '.trim($ssql->fs_nm_product).' dengan keterangan sebagai berikut :');
				$sel->mergeCells('A'.$i.':G'.$i);
				$i++;
				$k++;
				
				$sel->setCellValue('A'.$i, 'No Polisi');
				$sel->setCellValue('B'.$i, ':');
				$sel->setCellValue('C'.$i, trim($ssql->fs_nopol));
				$sel->mergeCells('C'.$i.':G'.$i);
				$i++;
				$k++;
				
				$sel->setCellValue('A'.$i, 'Tipe');
				$sel->setCellValue('B'.$i, ':');
				$sel->setCellValue('C'.$i, trim($ssql->fs_nm_product));
				$sel->mergeCells('C'.$i.':G'.$i);
				$i++;
				$k++;
				
				$sel->setCellValue('A'.$i, 'No Rangka / No Mesin');
				$sel->setCellValue('B'.$i, ':');
				$sel->setCellValue('C'.$i, trim($ssql->fs_rangka). ' / '.trim($ssql->fs_mesin));
				$sel->mergeCells('C'.$i.':G'.$i);
				$i++;
				$k++;
				
				$sel->setCellValue('A'.$i, 'Tahun / Warna');
				$sel->setCellValue('B'.$i, ':');
				$sel->setCellValue('C'.$i, trim($ssql->fs_tahunbuat). ' / '.trim($ssql->fs_warna));
				$sel->mergeCells('C'.$i.':G'.$i);
				$i = $i + 2;
				$k = $k + 2;
				
				$this->load->model('mMainModul','',true);
				$xTglPrint = date('d-m-Y');
				$xTglPrint = substr($xTglPrint,0,2).' '.$this->mMainModul->ambilbulan(substr($xTglPrint,3,2)).' '.substr($xTglPrint,6,4);
				$sel->setCellValue('E'.$i,trim($this->session->userdata('gKota')).', '.trim($xTglPrint));
				$sel->mergeCells('E'.$i.':G'.$i);
				$i = $i + 2;
				$k = $k + 2;
				
				$sel->setCellValue('A'.$i,'Penerima');
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->mergeCells('A'.$i.':C'.$i);
				$sel->setCellValue('E'.$i,'Yang Menyerahkan');
				$sel->mergeCells('E'.$i.':G'.$i);
				$i = $i + 40;
				$k = $k + 40;
				
				$sel->setCellValue('A'.$i, '( '.$ssql->fs_nm_stnk_qq.' )');
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->mergeCells('A'.$i.':C'.$i);
				$sel->setCellValue('E'.$i, '( '.trim($this->session->userdata('gUser')).' )');
				$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->mergeCells('E'.$i.':G'.$i);
				$i = $i + 2;
				$k = $k + 2;
				
				$sel->getStyle('A'.$i.':G'.$i)->applyFromArray(
					array(
						'borders' => array(
							'bottom' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					)
				);
				$i = $i + 20;
				$k = $k + 20;
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
			
			$this->load->model('mMainModul','',true);
			$this->mMainModul->pdf($nmfile);
			
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> '"'.$nmfile.'.pdf" has been created!!',
				'nmfile'	=> $xPath.$nmfile.'.pdf'
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
	
	function print_bpkb()
	{ 
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tterima_bpkb';
		$nmfile = 'terima_bpkb-'.$jamskg;
		
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
		
		$oExcel->getActiveSheet()->setTitle('Terima BPKB');
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
		
		$sKdComp = trim($this->session->userdata('gComp'));
		$sKdDept = trim($this->session->userdata('gDept'));
		$sKdCount = trim($this->session->userdata('gCount'));
		
		$xRangka = trim($this->input->post('fs_rangka'));
		$xMesin = trim($this->input->post('fs_mesin'));
		
		$this->load->model('mStnkBpkb','',true);
		$ssql = $this->mStnkBpkb->cetak_bpkb($xRangka,$xMesin);
		
		if ($ssql->num_rows() > 0)
		{
			$pjghal = 57;
			$i = 1;
			$l = 0; // no urut
			
			$k = 1; // pjg hal
			$p = 1; // awal hal
			
			// date_default_timezone_set("Asia/Bangkok");
			// $sel->setCellValue('B'.$i,'Print : '.date('d-m-Y H:i:s'));
			// $sel->setCellValue('F'.$i,'User :'.trim($this->session->userdata('gUser')));
			
			$ssql = $ssql->row();
			
			for ($z = 1; $z <= 2; $z++) {
				$i = $i + 2;
				$k = $k + 2;
				$sel->setCellValue('A'.$i,'TANDA TERIMA');
				$sel->mergeCells('A'.$i.':G'.$i);
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
				$sel->getStyle('A'.$i)->getFont()->setBold(true);
				$sel->getStyle('A'.$i)->getFont()->setSize(12);
				$i = $i + 2;
				$k = $k + 2;
				
				$sel->setCellValue('A'.$i, 'Telah diterima dari '.trim($ssql->fs_nm_cabang). ' berupa 1 (satu) buah BPKB dengan keterangan sebagai berikut :');
				$sel->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				$sel->getStyle('A'.$i)->getAlignment()->setWrapText(true);
				$j = $i + 1;
				$sel->mergeCells('A'.$i.':G'.$j);
				$i = $i + 2;
				$k = $k + 2;
				
				$sel->setCellValue('A'.$i, 'Nama');
				$sel->setCellValue('B'.$i, ':');
				$sel->setCellValue('C'.$i, trim($ssql->fs_nm_customer));
				$sel->mergeCells('C'.$i.':G'.$i);
				$i++;
				$k++;
				
				$sel->setCellValue('A'.$i, 'Alamat');
				$sel->setCellValue('B'.$i, ':');
				$sel->setCellValue('C'.$i, trim($ssql->fs_almt_customer));
				$sel->getStyle('C'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				$sel->getStyle('C'.$i)->getAlignment()->setWrapText(true);
				$j = $i + 1;
				$sel->mergeCells('C'.$i.':G'.$j);
				$i = $i + 2;
				$k = $k + 2;
				
				$sel->setCellValue('A'.$i, 'No BPKB');
				$sel->setCellValue('B'.$i, ':');
				$sel->setCellValue('C'.$i, trim($ssql->fs_bpkb));
				$sel->mergeCells('C'.$i.':G'.$i);
				$i = $i + 2;
				$k = $k + 2;
				
				$sel->setCellValue('A'.$i,'Kendaraan Tipe '.trim($ssql->fs_nm_product).' dengan keterangan sebagai berikut :');
				$sel->mergeCells('A'.$i.':G'.$i);
				$i++;
				$k++;
				
				$sel->setCellValue('A'.$i, 'No Polisi');
				$sel->setCellValue('B'.$i, ':');
				$sel->setCellValue('C'.$i, trim($ssql->fs_nopol));
				$sel->mergeCells('C'.$i.':G'.$i);
				$i++;
				$k++;
				
				$sel->setCellValue('A'.$i, 'Tipe');
				$sel->setCellValue('B'.$i, ':');
				$sel->setCellValue('C'.$i, trim($ssql->fs_nm_product));
				$sel->mergeCells('C'.$i.':G'.$i);
				$i++;
				$k++;
				
				$sel->setCellValue('A'.$i, 'No Rangka / No Mesin');
				$sel->setCellValue('B'.$i, ':');
				$sel->setCellValue('C'.$i, trim($ssql->fs_rangka). ' / '.trim($ssql->fs_mesin));
				$sel->mergeCells('C'.$i.':G'.$i);
				$i++;
				$k++;
				
				$sel->setCellValue('A'.$i, 'Tahun / Warna');
				$sel->setCellValue('B'.$i, ':');
				$sel->setCellValue('C'.$i, trim($ssql->fs_tahunbuat). ' / '.trim($ssql->fs_warna));
				$sel->mergeCells('C'.$i.':G'.$i);
				$i = $i + 2;
				$k = $k + 2;
				
				$this->load->model('mMainModul','',true);
				$xTglPrint = date('d-m-Y');
				$xTglPrint = substr($xTglPrint,0,2).' '.$this->mMainModul->ambilbulan(substr($xTglPrint,3,2)).' '.substr($xTglPrint,6,4);
				$sel->setCellValue('E'.$i,trim($this->session->userdata('gKota')).', '.trim($xTglPrint));
				$sel->mergeCells('E'.$i.':G'.$i);
				$i = $i + 2;
				$k = $k + 2;
				
				$sel->setCellValue('A'.$i,'Penerima');
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->mergeCells('A'.$i.':C'.$i);
				$sel->setCellValue('E'.$i,'Yang Menyerahkan');
				$sel->mergeCells('E'.$i.':G'.$i);
				$i = $i + 40;
				$k = $k + 40;
				
				$sel->setCellValue('A'.$i, '( '.$ssql->fs_nm_bpkb_qq.' )');
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->mergeCells('A'.$i.':C'.$i);
				$sel->setCellValue('E'.$i, '( '.trim($this->session->userdata('gUser')).' )');
				$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->mergeCells('E'.$i.':G'.$i);
				$i = $i + 2;
				$k = $k + 2;
				
				$sel->getStyle('A'.$i.':G'.$i)->applyFromArray(
					array(
						'borders' => array(
							'bottom' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					)
				);
				$i = $i + 20;
				$k = $k + 20;
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
			
			$this->load->model('mMainModul','',true);
			$this->mMainModul->pdf($nmfile);
			
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> '"'.$nmfile.'.pdf" has been created!!',
				'nmfile'	=> $xPath.$nmfile.'.pdf'
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