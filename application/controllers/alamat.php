<?php

class Alamat extends CI_Controller
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
			$this->load->view('valamat');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function nilaidefa()
	{
		$hasil = array(
			'sukses'	=> true,
			'kddept'	=> trim($this->session->userdata('gDept')).trim($this->session->userdata('gCount')),
			'deptcd'	=> trim($this->session->userdata('gDept')),
			'count'		=> trim($this->session->userdata('gCount')),
			'nmdept'	=> trim($this->session->userdata('gDeptName'))
		);
		echo json_encode($hasil);
	}
	
	function ambilkode()
	{
		$nstart = trim($this->input->post('start'));
		$kdtipe = trim($this->input->post('fs_kd_tipe'));
		$kodecode = '';
		$kodecount = '';
		
		if ($kdtipe <> '00' and $kdtipe <> '03')
		{
			$this->load->model('mMainModul','',true);
			$ssql = $this->mMainModul->ambil_kode($kdtipe);
			
			if ($ssql->num_rows() > 0)
			{
				$ssql = $ssql->row();
				$kodecode = trim($ssql->fs_code);
				$kodecount = sprintf('%06d', trim($ssql->fs_count) + 1);
			}
			else
			{
				$ldept = strlen(trim($this->session->userdata('gDept')));
				$ldept = $ldept - 2;
				$lcount = strlen(trim($this->session->userdata('gCount')));
				$lcount = $lcount - 2;
				
				$xdept = substr(trim($this->session->userdata('gDept')), $ldept, 2).substr(trim($this->session->userdata('gCount')), $lcount, 2);
				$kodecode = $xdept;
				$kodecount = sprintf('%06d', 1);
			}
		}
		
		$hasil = array(
			'sukses'	=> true,
			'txtcode'	=> $kodecode,
			'txtcount'	=> $kodecount
		);
		echo json_encode($hasil);
	}
	
	function detil()
	{
		$kdtipe = trim($this->input->post('fs_kd_tipe'));
		$kdcode = trim($this->input->post('fs_code'));
		$kdcount = trim($this->input->post('fs_count'));
		$hasil = array();
		
		if ($kdtipe == '01')
		{
			$ceksupp = '1';
			$this->load->model('mAlamat','',true);
			$ssql = $this->mAlamat->detil($kdtipe,$kdcode,$kdcount);
			
			if ($ssql->num_rows() > 0)
			{
				$ssql = $ssql->row();
				$kdacno = trim($ssql->fs_kd_acno);
				$nmacno = trim($ssql->fs_nm_acno);
				$ceksupp = trim($ssql->fb_active);
			}
			
			$hasil = array(
				'sukses'	=> true,
				'kdacno'	=> $kdacno,
				'nmacno'	=> $nmacno,
				'cek'		=> $ceksupp
			);
		}
		elseif ($kdtipe == '02')
		{
			$cekcust = '1';
			$this->load->model('mAlamat','',true);
			$ssql = $this->mAlamat->detil($kdtipe,$kdcode,$kdcount);
			
			if ($ssql->num_rows() > 0)
			{
				$ssql = $ssql->row();
				$kdtipe = trim($ssql->fs_kd_tipe);
				$nmtipe = trim($ssql->fs_nm_tipe);
				$cekcust = trim($ssql->fb_active);
			}
			
			$hasil = array(
				'sukses'	=> true,
				'kdtipe'	=> $kdtipe,
				'nmtipe'	=> $nmtipe,
				'cek'		=> $cekcust
			);
		}
		
		echo json_encode($hasil);
	}
	
	function kodetipe()
	{
		$nstart = trim($this->input->post('start'));
		$kdtipe = trim($this->input->post('fs_kd_tipe'));
		$nmtipe = trim($this->input->post('fs_nm_tipe'));
		
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodetipe_all($kdtipe,$nmtipe,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->kodetipe($kdtipe,$nmtipe,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kodecode()
	{
		$nstart = trim($this->input->post('start'));
		$kdtipe = trim($this->input->post('fs_kd_tipe'));
		$kdcode = trim($this->input->post('fs_code'));
		$kdcount = trim($this->input->post('fs_count'));
		$nmcode = trim($this->input->post('fs_nm_code'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodecode_all($kdtipe,$kdcode,$kdcount,$nmcode);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->kodecode($kdtipe,$kdcode,$kdcount,$nmcode,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kodekota()
	{
		$nstart = trim($this->input->post('start'));
		$kdkota = trim($this->input->post('fs_kd_kota'));
		$nmkota = trim($this->input->post('fs_nm_kota'));
		
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodekota_all($kdkota,$nmkota,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->kodekota($kdkota,$nmkota,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kodekab()
	{
		$nstart = trim($this->input->post('start'));
		$kdkab = trim($this->input->post('fs_kd_kota'));
		$nmkab = trim($this->input->post('fs_nm_kota'));

		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodekab_all($kdkab,$nmkab,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->kodekab($kdkab,$nmkab,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kodeprop()
	{
		$nstart = trim($this->input->post('start'));
		$kdprop = trim($this->input->post('fs_kd_prop'));
		$nmprop = trim($this->input->post('fs_nm_prop'));

		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodeprop_all($kdprop,$nmprop,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->kodeprop($kdprop,$nmprop,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kodeneg()
	{
		$nstart = trim($this->input->post('start'));
		$kdneg = trim($this->input->post('fs_kd_neg'));
		$nmneg = trim($this->input->post('fs_nm_neg'));

		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodeneg_all($kdneg,$nmneg,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->kodeneg($kdneg,$nmneg,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kodewn()
	{
		$nstart = trim($this->input->post('start'));
		$kdwn = trim($this->input->post('fs_kd_wn'));
		$nmwn = trim($this->input->post('fs_nm_wn'));
		
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodewn_all($kdwn,$nmwn,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->kodewn($kdwn,$nmwn,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kodetipecust()
	{
		$nstart = trim($this->input->post('start'));
		$kdtipe = trim($this->input->post('fs_kd_tipe'));
		$nmtipe = trim($this->input->post('fs_nm_tipe'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodetipecust_all($kdtipe,$nmtipe);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->kodetipecust($kdtipe,$nmtipe,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kodegl()
	{
		$nstart = trim($this->input->post('start'));
		$kdgl = trim($this->input->post('fs_kd_acno'));
		$nmgl = trim($this->input->post('fs_nm_acno'));
		
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodegl_all($kdgl,$nmgl,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->kodegl($kdgl,$nmgl,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function ceksave()
	{
		$kdtipe = trim($this->input->post('fs_kd_tipe'));
		$kdcode = trim($this->input->post('fs_code'));
		$kdcount = trim($this->input->post('fs_count'));
		
		if (trim($kdcode) == '' or trim($kdcount) == '')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, Code or Count number unknown!!'
			);
			echo json_encode($hasil);
			return;
		}
		else
		{
			$this->load->model('mAlamat','',true);
			$ssql = $this->mAlamat->cek_kode($kdtipe,$kdcode,$kdcount);
			
			if ($ssql->num_rows() > 0)
			{
				$hasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Reference number already exists, do you want to update it?'
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
		$kdtipe = trim($this->input->post('fs_kd_tipe'));
		$kdcode = trim($this->input->post('fs_code'));
		$kdcount = trim($this->input->post('fs_count'));
		$nama = trim($this->input->post('fs_nama'));
		$alamat = trim($this->input->post('fs_alamat'));
		
		$kdkota = trim($this->input->post('fs_kd_kota'));
		$nmkota = trim($this->input->post('fs_nm_kota'));
		$kdkab = trim($this->input->post('fs_kd_kab'));
		$kdprop = trim($this->input->post('fs_kd_prop'));
		$kdneg = trim($this->input->post('fs_kd_neg'));
		
		$kdwn = trim($this->input->post('fs_kd_wn'));
		$kdid = trim($this->input->post('fs_kd_id'));
		$tglid = trim($this->input->post('fd_tgl_id'));
		$tlp = trim($this->input->post('fs_tlp'));
		$tlp2 = trim($this->input->post('fs_tlp2'));
		
		$kdacno = trim($this->input->post('fs_kd_acno'));
		$ceksupp = trim($this->input->post('fb_supp'));
		$kdtipecust = trim($this->input->post('fs_kd_tipecust'));
		$cekcust = trim($this->input->post('fb_cust'));
		
		// if (trim($ceksupp) == 'true')
		// {
			$ceksupp = 1;
		// }
		// else
		// {
			// $ceksupp = 0;
		// }
		
		// if (trim($cekcust) == 'true')
		// {
			$cekcust = 1;
		// }
		// else
		// {
			// $cekcust = 0;
		// }
		
		$xupdate = false;
		$this->load->model('mAlamat','',true);
		$ssql = $this->mAlamat->cek_kode($kdtipe,$kdcode,$kdcount);
		
		if ($ssql->num_rows() > 0)
		{
			$xupdate = true;
		}
		else
		{
			$xupdate = false;
		}
		
		$dt = array(
			'fs_kd_comp'		=> trim($this->session->userdata('gComp')),
			'fs_cdtyp'			=> trim($kdtipe),
			'fs_code'			=> trim($kdcode),
			'fs_count'			=> trim($kdcount),
			'fs_nm_code'		=> trim($nama),
			
			'fs_addr'			=> trim($alamat),
			'fs_kd_city'		=> trim($kdkota),
			'fs_kd_district'	=> trim($kdkab),
			'fs_kd_province'	=> trim($kdprop),
			'fs_kd_country'		=> trim($kdneg),
			
			'fs_kd_state'		=> trim($kdwn),
			'fs_idcard'			=> trim($kdid),
			'fd_dateID'			=> trim($tglid),
			'fs_phone1'			=> trim($tlp),
			'fs_phone2'			=> trim($tlp2)
		);
		
		if ($xupdate == false)
		{
			$dt2 = array(
				'fs_usrcrt'	=> trim($this->session->userdata('gUser')),
				'fd_usrcrt'	=> trim(date('Y-m-d H:i:s')),
				'fs_upddt'	=> trim($this->session->userdata('gUser')),
				'fd_upddt'	=> trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			
			$this->db->insert('tm_addr', $data);
		}
		else
		{
			$dt2 = array(
				'fs_upddt'	=> trim($this->session->userdata('gUser')),
				'fd_upddt'	=> trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
						AND	fs_cdtyp = '".trim($kdtipe)."'
						AND	fs_code = '".trim($kdcode)."'
						AND	fs_count = '".trim($kdcount)."'";
			
			$this->db->where($where);
			$this->db->update('tm_addr', $data);
		}
		
		
		if (trim($kdtipe) == '00')
		{
			//hapus detail
			$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'";
			
			$this->db->where($where);
			$this->db->delete('tm_company');
			//eof hapus detail
			
			$dt = array(
				'fs_kd_comp' => trim($this->session->userdata('gComp'))
			);
			
			$dt2 = array(
				'fs_usrcrt'	=> trim($this->session->userdata('gUser')),
				'fd_usrcrt'	=> trim(date('Y-m-d H:i:s')),
				'fs_upddt'	=> trim($this->session->userdata('gUser')),
				'fd_upddt'	=> trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			
			$this->db->insert('tm_company', $data);
		}
		else if (trim($kdtipe) == '01'
				or trim($kdtipe) == '02'
				or trim($kdtipe) == '03'
				or trim($kdtipe) == '04')
		{
			//hapus detail
			if (trim($kdtipe) == '01')
			{
				$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
							AND	fs_kd_supplier = '".trim($kdcode)."'
							AND	fs_count = '".trim($kdcount)."'";
				$tabel = 'tm_supplier';
			}
			elseif (trim($kdtipe) == '02')
			{
				$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
							AND	fs_kd_customer = '".trim($kdcode)."'
							AND	fs_count = '".trim($kdcount)."'";
				$tabel = 'tm_customer';
			}
			elseif (trim($kdtipe) == '03')
			{
				$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
							AND	fs_kd_dept = '".trim($kdcode)."'
							AND	fs_count = '".trim($kdcount)."'";
				$tabel = 'tm_dept';
			}
			elseif (trim($kdtipe) == '04')
			{
				$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
							AND	fs_kd_bank = '".trim($kdcode)."'
							AND	fs_count = '".trim($kdcount)."'";
				$tabel = 'tm_bank';
			}
			
			$this->db->where($where);
			$this->db->delete($tabel);
			//eof hapus detail
			
			if (trim($kdtipe) == '01')
			{
				$dt = array(
					'fs_kd_comp'		=> trim($this->session->userdata('gComp')),
					'fs_kd_supplier'	=> trim($kdcode),
					'fs_count'			=> trim($kdcount),
					'fs_kd_acno'		=> trim($kdacno),
					'fb_active'			=> trim($ceksupp)
				);
			}
			elseif (trim($kdtipe) == '02')
			{
				$dt = array(
					'fs_kd_comp'		=> trim($this->session->userdata('gComp')),
					'fs_kd_customer'	=> trim($kdcode),
					'fs_count'			=> trim($kdcount),
					'fs_kd_custtype'	=> trim($kdtipecust),
					'fb_active'			=> trim($cekcust)
				);
			}
			elseif (trim($kdtipe) == '03')
			{
				$dt = array(
					'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
					'fs_kd_dept'	=> trim($kdcode),
					'fs_count'		=> trim($kdcount),
					'fb_active'		=> '1'
				);
			}
			elseif (trim($kdtipe) == '04')
			{
				$dt = array(
					'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
					'fs_kd_bank'	=> trim($kdcode),
					'fs_count'		=> trim($kdcount),
					'fb_active'		=> '1'
				);
			}
			
			$dt2 = array(
				'fs_usrcrt'	=> trim($this->session->userdata('gUser')),
				'fd_usrcrt'	=> trim(date('Y-m-d H:i:s')),
				'fs_upddt'	=> trim($this->session->userdata('gUser')),
				'fd_upddt'	=> trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			
			$this->db->insert($tabel, $data);
		}
		
		
		if (trim($kdtipe) == '03')
		{
			if (trim($kdcode) == trim($this->session->userdata('gDept')) and
				trim($kdcount) == trim($this->session->userdata('gCount')))
			{
				$new = array(
					'gKota'	=> trim($nmkota)
				);
				$this->session->set_userdata($new);
			}
		}
		
		if ($xupdate == false)
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Address Success'
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Address Update Success'
			);
			echo json_encode($hasil);
		}
	}
	
	function excel_cust()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tkonsumen';
		$nmfile = 'konsumen-'.$jamskg;
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
		$oExcel->getActiveSheet()->setTitle('DaftarKonsumen');
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
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodecode_all('02',trim($this->session->userdata('gGudang')),'','');
		$total = $ssql->num_rows();
		
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
			$sel->setCellValue('A'.$i, 'Daftar Konsumen');
			$sel->mergeCells('A'.$i.':E'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$sel->getStyle('A'.$i)->getFont()->setSize(12);
			$p = $i;
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'No');
			$sel->setCellValue('B'.$i, 'Kode');
			$sel->setCellValue('C'.$i, 'Nama');
			$sel->setCellValue('D'.$i, 'Telp');
			$sel->setCellValue('E'.$i, 'Alamat');
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
			$sel->getStyle('A'.$i.':'.'E'.$i)->applyFromArray($warnakolom);
			$i++;
			$k++;
			$l++;
			
			foreach ($ssql->result() as $row)
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
					$sel->setCellValue('A'.$i, 'Daftar Konsumen');
					$sel->mergeCells('A'.$i.':E'.$i);
					$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
					$sel->getStyle('A'.$i)->getFont()->setBold(true);
					$sel->getStyle('A'.$i)->getFont()->setSize(12);
					$i = $i + 2;
					$k = $k + 2;
					
					$sel->setCellValue('A'.$i, 'No');
					$sel->setCellValue('B'.$i, 'Kode');
					$sel->setCellValue('C'.$i, 'Nama');
					$sel->setCellValue('D'.$i, 'Telp');
					$sel->setCellValue('E'.$i, 'Alamat');
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
					$sel->getStyle('A'.$i.':'.'E'.$i)->applyFromArray($warnakolom);
					$q = $i;
					$i++;
					$k++;
				}
				
				$sel->setCellValue('A'.$i, trim($l));
				$sel->setCellValue('B'.$i, trim($row->fs_code).trim($row->fs_count));
				$sel->setCellValue('C'.$i, trim($row->fs_nm_code));
				$sel->setCellValue('D'.$i, "'".trim($row->fs_tlp));
				$sel->setCellValue('E'.$i, trim($row->fs_alamat));
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				
				if ($k % 2 <> 0) {
					$sel->getStyle('A'.$i.':'.'E'.$i)->applyFromArray($warnakolom);
				}
				$i++;
				$k++;
				$l++;
			}
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
			$sel->getStyle('E'.$q.':E'.$j)->applyFromArray(
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