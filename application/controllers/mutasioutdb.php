<?php

class MutasiOutDB extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		//change db
		// $this->load->model('mMainModul','',true);
		// $this->mMainModul->change_db($this->session->userdata('gServer'),$this->session->userdata('gDatabase'));
		//eof change db
	}
	
	function index()
	{
		if (trim($this->session->userdata('gDatabase')) <> '')
		{
			$this->load->view('vmutasioutdb');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function refno()
	{
		//change db
		$this->load->model('mMainModul','',true);
		$this->mMainModul->change_db($this->session->userdata('gServer'),$this->session->userdata('gDatabase'));
		//eof change db
		
		$refno = trim($this->input->post('fs_refno'));
		$nmcust = trim($this->input->post('fs_nm_cust'));
		
		$nstart = trim($this->input->post('start'));
		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->mutasi_out_db_all($refno,$nmcust,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->mutasi_out_db($refno,$nmcust,$nstart,$total,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function ambil_comp()
	{
		$nstart = trim($this->input->post('start'));
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->change_comp_all('1');
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->change_comp('1',$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function ambil_comp2()
	{
		$nstart = trim($this->input->post('start'));
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->change_comp_all('1');
		
		echo json_encode($ssql->result());
	}
	
	function kodedeptke()
	{
		$xDB = trim($this->input->post('fs_nm_db'));
		$xKdComp = trim($this->input->post('fs_kd_comp'));
		
		if (trim($xDB) <> '')
		{
			//change db
			$this->load->model('mMainModul','',true);
			$this->mMainModul->change_db($this->session->userdata('gServer'),$xDB);
			//eof change db
			
			$nstart = trim($this->input->post('start'));
			
			$this->load->model('mSearch','',true);
			$ssql = $this->mSearch->login_dept_all($xKdComp,'','');
			$total = $ssql->num_rows();
			
			$ssql = $this->mSearch->login_dept($xKdComp,'','',$nstart);
			
			echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
		}
	}
	
	function gudang()
	{
		$nstart = trim($this->input->post('start'));
		$xDB = trim($this->input->post('fs_nm_db'));
		$lDept = strlen(trim($this->input->post('fs_kd_dept')));
		$lDept = $lDept - 2;
		$kddept = substr(trim($this->input->post('fs_kd_dept')), $lDept, 2);
		
		$lCount = strlen(trim($this->input->post('fs_count')));
		$lCount = $lCount - 2;
		$kdcount = substr(trim($this->input->post('fs_count')), $lCount, 2);
		
		$KdComp = trim($this->input->post('fs_kd_comp'));
		$kdwh = trim($this->input->post('fs_code'));
		$nmwh = trim($this->input->post('fs_nm_code'));

		$cari = trim($this->input->post('fs_cari'));
		
		if (trim($xDB) <> '')
		{
			//change db
			$this->load->model('mMainModul','',true);
			$this->mMainModul->change_db($this->session->userdata('gServer'),$xDB);
			//eof change db
			
			$this->load->model('mSearch','',true);
			$ssql = $this->mSearch->wh_all($KdComp,$kddept,$kdcount,$kdwh,$nmwh,$cari);
			$total = $ssql->num_rows();
			
			$ssql = $this->mSearch->wh($KdComp,$kddept,$kdcount,$kdwh,$nmwh,$nstart,$cari);
			
			echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
		}
	}
	
	function grid_prod()
	{
		//change db
		$this->load->model('mMainModul','',true);
		$this->mMainModul->change_db($this->session->userdata('gServer'),$this->session->userdata('gDatabase'));
		//eof change db
		
		$nstart = trim($this->input->post('start'));
		
		$refno = trim($this->input->post('fs_refno'));
		$kdcust = trim($this->input->post('fs_kd_cust'));
		
		$this->load->model('mMutasiOutDB','',true);
		$ssql = $this->mMutasiOutDB->grid_prod($refno,$kdcust);
		
		echo json_encode($ssql->result());
	}
	
	function ceksave()
	{
		//change db
		$this->load->model('mMainModul','',true);
		$this->mMainModul->change_db($this->session->userdata('gServer'),$this->session->userdata('gDatabase'));
		//eof change db
		
		$refno = trim($this->input->post('fs_refno'));
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		
		if (trim($refno) == '' or trim($refno) == 'AUTOMATIC')
		{
			$this->load->model('mMutasiOutDB','',true);
			$ssql = $this->mMutasiOutDB->cek_rangka($rangka,$mesin);
			
			if ($ssql->num_rows() > 0)
			{
				$hasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Saving Canceled, There are product which spesific chasis and macines already In!!'
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
			$this->load->model('mMutasiOutDB','',true);
			$ssql = $this->mMutasiOutDB->cek_refno($refno);
			
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
					'sukses'	=> false,
					'hasil'		=> 'Saving Failed, Reference number unknown!!'
				);
				echo json_encode($hasil);
			}
		}
	}
	
	function save()
	{
		//change db
		$this->load->model('mMainModul','',true);
		$this->mMainModul->change_db($this->session->userdata('gServer'),$this->session->userdata('gDatabase'));
		//eof change db
		
		$refno = trim($this->input->post('fs_refno'));
		$tglrefno = trim($this->input->post('fd_refno'));
		$ket = trim($this->input->post('fs_ket'));
		$kdcust = trim($this->input->post('fs_kd_cust'));
		
		$kdcustcount = trim($this->input->post('fs_count_cust'));
		$kdtcomp = trim($this->input->post('fs_kd_tcomp'));
		$tnmdb = trim($this->input->post('fs_nm_tdb'));
		$nmtcomp = trim($this->input->post('fs_nm_tcomp'));
		$kdtdept = trim($this->input->post('fs_kd_tdept'));
		$kdtcount = trim($this->input->post('fs_tcount'));
		$nmtdept = trim($this->input->post('fs_nm_tdept'));
		$kdtwh = trim($this->input->post('fs_kd_twh'));
		$nmtwh = trim($this->input->post('fs_nm_twh'));
		
		$xupdate = false;
		$this->load->model('mMutasiOutDB','',true);
		$ssql = $this->mMutasiOutDB->cek_refno($refno);
		
		if ($ssql->num_rows() > 0)
		{
			//refno ada
			$ssql = $ssql->row();
			$refno = $ssql->fs_refno;
			$xupdate = true;
			//eof refno ada
		}
		else
		{
			//generate refno
			$ldept = strlen(trim($this->session->userdata('gDept')));
			$ldept = $ldept - 2;
			$lcount = strlen(trim($this->session->userdata('gCount')));
			$lcount = $lcount - 2;
			
			$xdept = substr(trim($this->session->userdata('gDept')), $ldept, 2).substr(trim($this->session->userdata('gCount')), $lcount, 2);
			$xdate = trim($tglrefno);
			
			$xprefix = trim($this->session->userdata('gComp')).'/TRO/'.trim($xdept).'/'.trim($xdate).'-';
			$this->load->model('mMainModul','',true);
			$ssql = $this->mMainModul->get_mutasi($xprefix);
			//eof generate refno
			
			if ($ssql->num_rows() > 0)
			{
				$ssql = $ssql->row();
				
				$lrefno = strlen(trim($ssql->fs_refno));
				$lrefno = $lrefno - 5;
				
				$refno = $xprefix.sprintf("%05d",(substr(trim($ssql->fs_refno), $lrefno, 5) + 1));
			}
			else
			{
				$refno = $xprefix.'00001';
			}
		}
		
		//save header
		$dt = array(
			'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
			'fs_nm_db'		=> trim($this->session->userdata('gDatabase')),
			'fs_nm_comp'	=> trim($this->session->userdata('gCompName')),
			'fs_kd_dept'	=> trim($this->session->userdata('gDept')),
			'fs_count'		=> trim($this->session->userdata('gCount')),
			'fs_refno'		=> trim($refno),
			
			'fd_refno'		=> trim($tglrefno),
			'fs_ket'		=> trim($ket),
			'fs_kd_cust'	=> trim($kdcust),
			'fs_count_cust'	=> trim($kdcustcount),
			'fs_kd_tcomp'	=> trim($kdtcomp),
			
			'fs_nm_tdb'		=> trim($tnmdb),
			'fs_nm_tcomp'	=> trim($nmtcomp),
			'fs_kd_tdept'	=> trim($kdtdept),
			'fs_tcount'		=> trim($kdtcount),
			'fs_nm_tdept'	=> trim($nmtdept),
			
			'fs_kd_twh'		=> trim($kdtwh),
			'fs_nm_twh'		=> trim($nmtwh)
		);
		
		
		if ($xupdate == false)
		{
			if (trim($refno) <> '')
			{
				$dt2 = array(
					'fs_usrcrt'	=> trim($this->session->userdata('gUser')),
					'fd_usrcrt'	=> trim(date('Y-m-d H:i:s')),
					'fs_upddt'	=> trim($this->session->userdata('gUser')),
					'fd_upddt'	=> trim(date('Y-m-d H:i:s'))
				);
				$data = array_merge($dt,$dt2);
				
				$this->db->insert('tx_mutasidb', $data);
			}
			else
			{
				$hasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Saving Failed, generate reference number failed!!</br>Please try again letter...',
					'refno'		=> $refno
				);
				echo json_encode($hasil);
			}
		}
		else
		{
			$dt2 = array(
				'fs_upddt'	=> trim($this->session->userdata('gUser')),
				'fd_upddt'	=> trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
						AND	fs_refno = '".trim($refno)."'";
			
			$this->db->where($where);
			$this->db->update('tx_mutasidb', $data);
		}
		//eof save header
		
		//save detail
		//hapus detail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_mutasidbd');
		//eof hapus detail
		
		
		$kdprod = explode('|', trim($this->input->post('fs_kd_product')));
		$rangka = explode('|', trim($this->input->post('fs_rangka')));
		$mesin = explode('|', trim($this->input->post('fs_mesin')));
		$cc = explode('|', trim($this->input->post('fs_cc')));
		$thn = explode('|', trim($this->input->post('fs_thn')));
		$kdwarna = explode('|', trim($this->input->post('fs_kd_warna')));
		$nmwarna = explode('|', trim($this->input->post('fs_nm_warna')));
		
		$jml = count($kdprod) - 1;
		if ($jml != 0)
		{
			for ($i=1; $i<=$jml; $i++)
			{
				$data = array(
					'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
					'fs_kd_dept'	=> trim($this->session->userdata('gDept')),
					'fs_count'		=> trim($this->session->userdata('gCount')),
					'fs_refno'		=> trim($refno),
					'fs_kd_product'	=> trim($kdprod[$i]),
					
					'fs_rangka'		=> trim($rangka[$i]),
					'fs_mesin'		=> trim($mesin[$i]),
					'fn_cc'			=> trim($cc[$i]),
					'fn_thn'		=> trim($thn[$i]),
					
					'fs_kd_warna'	=> trim($kdwarna[$i]),
					'fs_nm_warna'	=> trim($nmwarna[$i]),
					'fs_seqno'		=> trim(sprintf("%06d",$i))
				);
				
				$this->db->insert('tx_mutasidbd', $data);
			}
		}
		
		$this->load->model('mMutasiOutDB','',true);
		$ssql = $this->mMutasiOutDB->hapustodb($tnmdb,$refno);
		
		$this->load->model('mMutasiOutDB','',true);
		$ssql = $this->mMutasiOutDB->simpantodb($tnmdb,$refno);
		
		if ($xupdate == false)
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Success',
				'refno'		=> $refno
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Update Success',
				'refno'		=> $refno
			);
			echo json_encode($hasil);
		}
		//eof save detail
	}
	
	function cekremove()
	{
		//change db
		$this->load->model('mMainModul','',true);
		$this->mMainModul->change_db($this->session->userdata('gServer'),$this->session->userdata('gDatabase'));
		//eof change db
		
		$refno = trim($this->input->post('fs_refno'));
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		
		if (trim($refno) == '' or trim($refno) == 'AUTOMATIC')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Remove Failed, Reference number empty!!'
			);
			echo json_encode($hasil);
		}
		else
		{
			$this->load->model('mMutasiOutDB','',true);
			$ssql = $this->mMutasiOutDB->cek_rangka($rangka,$mesin);
			
			if ($ssql->num_rows() > 0)
			{
				$hasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Remove Canceled, There are product which spesific chasis and macines already In!!'
				);
				echo json_encode($hasil);
				return;
			}
			
			$this->load->model('mMutasiOutDB','',true);
			$ssql = $this->mMutasiOutDB->cek_refno($refno);
			
			if ($ssql->num_rows() > 0)
			{
				$hasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Are you sure you want to delete this reference number?'
				);
				echo json_encode($hasil);
			}
			else
			{
				$hasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Remove Failed, Reference number unknown!!'
				);
				echo json_encode($hasil);
			}
		}
	}
	
	function remove()
	{
		//change db
		$this->load->model('mMainModul','',true);
		$this->mMainModul->change_db($this->session->userdata('gServer'),$this->session->userdata('gDatabase'));
		//eof change db
		
		$refno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mMutasiOutDB','',true);
		$ssql = $this->mMutasiOutDB->cek_refno($refno);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$tnmdb = $ssql->fs_nm_tdb;
			
			$this->load->model('mMutasiOutDB','',true);
			$ssql = $this->mMutasiOutDB->hapustodb($tnmdb,$refno);
		}
		
		//remove header
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_mutasidb');
		//eof remove header
		
		//remove detail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_mutasidbd');
		//eof remove detail
		
		$hasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Remove reference number: "'.$refno.'" success'
		);
		echo json_encode($hasil);
	}
	
	function print_mutasioutdb()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tmutasioutdb';
		$nmfile = 'mutasioutdb-'.$jamskg;
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
		
		//change db
		$this->load->model('mMainModul','',true);
		$this->mMainModul->change_db($this->session->userdata('gServer'),$this->session->userdata('gDatabase'));
		//eof change db
		
		$refno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mMutasiOutDB','',true);
		$ssql = $this->mMutasiOutDB->cek_refno($refno);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$tnmdb = $ssql->fs_nm_tdb;
		}
		else
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'No record!!'
			);
			echo json_encode($hasil);
			return;
		}
		
		$this->load->model('mMutasiOut','',true);
		$ssql = $this->mMutasiOutDB->mutasioutdb_cetak($tnmdb,$refno);
		
		$data = array();
		$data = $ssql;
		$jumlah_unit = $ssql->num_rows();
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			
			$NoMutasi = $ssql->fs_refno;
			$NamaPTFrom = $ssql->fs_nm_compf;
			$NamaCabangFrom = $ssql->fs_nm_deptf;
			$AlamatCabangFrom = $ssql->fs_addrf;
			$KotaFrom = $ssql->fs_nm_kotaf;
			
			$NamaPTTo = $ssql->fs_nm_compt;
			$NamaCabangTo = $ssql->fs_nm_deptt;
			$AlamatCabangTo = $ssql->fs_addrt;
			$KotaTo = $ssql->fs_nm_kotat;
			
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
			
			$sel->setCellValue('B'.$i, trim($NamaPTFrom));
			$sel->mergeCells('B'.$i.':D'.$i);
			$sel->setCellValue('E'.$i, trim($NamaPTTo));
			$sel->mergeCells('E'.$i.':F'.$i);
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
			
			$sel->setCellValue('B'.$i, 'Asal Perusahaan');
			$sel->setCellValue('C'.$i, ': '.trim($NamaPTFrom));
			$sel->mergeCells('C'.$i.':D'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('B'.$i, 'Tujuan Perusahaan');
			$sel->setCellValue('C'.$i, ': '.trim($NamaPTTo));
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
					$q = $p + 13;
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
			
			$q = $p + 13;
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