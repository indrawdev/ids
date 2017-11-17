<?php

class Agen extends CI_Controller
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
			$this->load->view('vagen');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function kodeagen()
	{
		$nstart = trim($this->input->post('start'));
		$kdagen = trim($this->input->post('fs_kd_agen'));
		$nmagen = trim($this->input->post('fs_nm_agen'));

		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->agen_all($kdagen,$nmagen,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->agen($kdagen,$nmagen,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function grid_detail()
	{
		$nstart = trim($this->input->post('start'));
		$refno = trim($this->input->post('fs_refno'));
		$kdtrx = '5600';
		$kdstrx = '0100';
		
		$this->load->model('mAgen','',true);
		$ssql = $this->mAgen->grid($refno,$kdtrx,$kdstrx);
		
		echo json_encode($ssql->result());
	}
	
	function grid_detail2()
	{
		$nstart = trim($this->input->post('start'));
		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mAgen','',true);
		$ssql = $this->mAgen->grid2_all($cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mAgen->grid2($cari,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function refno()
	{
		$nstart = trim($this->input->post('start'));
		$refno = trim($this->input->post('fs_refno'));
		$cust = trim($this->input->post('fs_nm_cust'));
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		$cari = trim($this->input->post('fs_cari'));
		$kdtrx = '5600';
		$kdstrx = '0100';
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->agen_servis_all($refno,$kdtrx,$kdstrx,$cust,$rangka,$mesin,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->agen_servis($refno,$kdtrx,$kdstrx,$cust,$rangka,$mesin,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function ceksave()
	{
		$refno = trim($this->input->post('fs_refno'));
		$kdtrx = '5600';
		$kdstrx = '0100';
		
		if (trim($refno) == '' or trim($refno) == 'AUTOMATIC')
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'lanjut'
			);
			echo json_encode($hasil);
		}
		else
		{
			$this->load->model('mAgen','',true);
			$ssql = $this->mAgen->cek_refno($kdtrx,$kdstrx,$refno);
			
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
		$refno = trim($this->input->post('fs_refno'));
		$refnodt = trim($this->input->post('fd_refno'));
		$docno = trim($this->input->post('fs_docno'));
		$docnodt = trim($this->input->post('fd_docno'));
		$kdagen = trim($this->input->post('fs_kd_agen'));
		
		$kddept = $this->session->userdata('gDept');
		$kdcount = $this->session->userdata('gCount');
		$kdtrx = '5600';
		$kdstrx = '0100';
		
		$xupdate = false;
		$this->load->model('mAgen','',true);
		$ssql = $this->mAgen->cek_refno($kdtrx,$kdstrx,$refno);
		
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
			$this->load->model('mMainModul','',true);
			$ssql = $this->mMainModul->get_refno($kddept,$kdcount,$kdtrx,$kdstrx,$refnodt);
			//eof generate refno
			
			if ($ssql->num_rows() > 0)
			{
				$ssql = $ssql->row();
				$refno = $ssql->REFNO;// -> case sensitif <jgn diubah>
			}
			else
			{
				$refno = '';
			}
		}
		
		
		$dt = array(
			'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
			'fs_kd_trx'		=> trim($kdtrx),
			'fs_kd_strx'	=> trim($kdstrx),
			'fs_refno'		=> trim($refno),
			
			'fd_refno'		=> trim($refnodt),
			'fs_docno'		=> trim($docno),
			'fd_docno'		=> trim($docnodt),
			'fs_kd_agent'	=> trim($kdagen),
			'fb_delete'		=> '0'
		);
		
		if ($xupdate == false)
		{
			$dt2 = array(
				'fs_kd_dept'	=> trim($this->session->userdata('gDept')),//taruh disini krn jika simpan ulang & trs tdk di load sesuai dept ketika simpan pertama
				'fs_count'		=> trim($this->session->userdata('gCount')),
				
				'fs_usrcrt'		=> trim($this->session->userdata('gUser')),
				'fd_usrcrt'		=> trim(date('Y-m-d H:i:s')),
				'fs_upddt'		=> trim($this->session->userdata('gUser')),
				'fd_upddt'		=> trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			
			$this->db->insert('tx_trxbirojs', $data);
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
			$this->db->update('tx_trxbirojs', $data);
		}
		
		
		$this->load->model('mAgen','',true);
		$ssql = $this->mAgen->hapus_isi($kdtrx,$kdstrx,$refno);
		
		//hapus detail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_trxbirojsd');
		//eof hapus detail
		
		//simpan detail
		$refnojual = explode('|', trim($this->input->post('fs_refnojual')));
		$kdcust = explode('|', trim($this->input->post('fs_kd_cust')));
		$count = explode('|', trim($this->input->post('fs_count')));
		$rangka = explode('|', trim($this->input->post('fs_rangka')));
		$mesin = explode('|', trim($this->input->post('fs_mesin')));
		
		$stnkdt = explode('|', trim($this->input->post('fd_stnk')));
		$stnk = explode('|', trim($this->input->post('fs_stnk')));
		$nmstnkqq = explode('|', trim($this->input->post('fs_nm_stnk_qq')));
		$almtstnkqq = explode('|', trim($this->input->post('fs_almt_stnk_qq')));
		
		$bpkbdt = explode('|', trim($this->input->post('fd_bpkb')));
		$bpkb = explode('|', trim($this->input->post('fs_bpkb')));
		$nmbpkbqq = explode('|', trim($this->input->post('fs_nm_bpkb_qq')));
		$almtbpkbqq = explode('|', trim($this->input->post('fs_almt_bpkb_qq')));
		
		$bbn = explode('|', trim($this->input->post('fn_bbn')));
		$servis = explode('|', trim($this->input->post('fn_servis')));
		$xflag2 = '0';
		$xflag3 = '0';
		
		$jml = count($rangka) - 1;
		if ($jml != 0)
		{
			for ($i=1; $i<=$jml; $i++)
			{
				if (trim($stnkdt[$i]) <> '')
				{
					$xflag2 = '1';
				}
				else
				{
					$xflag2 = '0';
				}
				
				if (trim($bpkbdt[$i]) <> '')
				{
					$xflag3 = '1';
				}
				else
				{
					$xflag3 = '0';
				}
				
				$data = array(
					'fs_kd_comp'		=> trim($this->session->userdata('gComp')),
					'fs_kd_dept'		=> trim($this->session->userdata('gDept')),
					'fs_count'			=> trim($this->session->userdata('gCount')),
					'fs_kd_trx'			=> trim($kdtrx),
					'fs_kd_strx'		=> trim($kdstrx),
					
					'fs_refno'			=> trim($refno),
					'fd_refno'			=> trim($refnodt),
					'fs_seqno'			=> trim(sprintf("%06d",$i)),
					'fs_kd_cussup'		=> trim($kdcust[$i]),
					'fs_countcussup'	=> trim($count[$i]),
					
					'fs_chasis'			=> trim($rangka[$i]),
					'fs_engine'			=> trim($mesin[$i]),
					'fs_flag'			=> '1',
					'fs_flag2'			=> trim($xflag2),
					'fs_flag3'			=> trim($xflag3),
					
					'fs_stnk'			=> trim($stnk[$i]),
					'fd_rcvdt'			=> trim($stnkdt[$i]),
					'fs_bpkb'			=> trim($bpkb[$i]),
					'fd_rcvdt2'			=> trim($bpkbdt[$i]),
					'fn_bbn'			=> trim($bbn[$i]),
					
					'fn_jasa'			=> trim($servis[$i]),
					'fs_refnosi'		=> trim($refnojual[$i]),
					
					'fs_nm_stnk_qq'		=> trim($nmstnkqq[$i]),
					'fs_almt_stnk_qq'	=> trim($almtstnkqq[$i]),
					'fs_nm_bpkb_qq'		=> trim($nmbpkbqq[$i]),
					'fs_almt_bpkb_qq'	=> trim($almtbpkbqq[$i]),
					
					'fs_usrcrt'			=> trim($this->session->userdata('gUser')),
					'fd_usrcrt'			=> trim(date('Y-m-d H:i:s')),
					'fs_upddt'			=> trim($this->session->userdata('gUser')),
					'fd_upddt'			=> trim(date('Y-m-d H:i:s'))
				);
				$this->db->insert('tx_trxbirojsd', $data);
			}
		}
		//eof simpan detail
		
		$this->load->model('mAgen','',true);
		$ssql = $this->mAgen->update_isi($kdtrx,$kdstrx,$refno);//dept tx_trxbirojsd perlu diupdate krn klo simpan ulang, dept ikut dept login
		
		if ($xupdate == false)
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Service Agent Success',
				'refno'		=> $refno
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Service Agent Update Success',
				'refno'		=> $refno
			);
			echo json_encode($hasil);
		}
	}
	
	function cekremove()
	{
		$refno = trim($this->input->post('fs_refno'));
		$kdtrx = '5600';
		$kdstrx = '0100';
		
		if (trim($refno) == '' or trim($refno) == 'AUTOMATIC')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Remove Failed, Reference number unknown!!'
			);
			echo json_encode($hasil);
		}
		else
		{
			$this->load->model('mAgen','',true);
			$ssql = $this->mAgen->cek_refno($kdtrx,$kdstrx,$refno);
			
			if ($ssql->num_rows() > 0)
			{
				$hasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Remove record?'
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
		$refno = trim($this->input->post('fs_refno'));
		$kdtrx = '5600';
		$kdstrx = '0100';
		
		$this->load->model('mAgen','',true);
		$ssql = $this->mAgen->hapus_isi($kdtrx,$kdstrx,$refno);
		
		//hapus detail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_trxbirojsd');
		//eof hapus detail
		
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_trxbirojs');
		
		$hasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Remove service agent reference number: "'.$refno.'" success'
		);
		echo json_encode($hasil);
	}
	
	function print_agenservice()
	{ 
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tagenservice';
		$nmfile = 'agenservice-'.$jamskg;
		
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
		
		$oExcel->getActiveSheet()->setTitle('Agen Service');
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
		$KdTrans = "BL";
		$sRefno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mAgen','',true);
		$ssql = $this->mAgen->agen_service($sRefno);
				
		$data = array();
		$data = $ssql;
		
		if ($ssql->num_rows() > 0)
		{
			
			$pjghal = 57;
			$i = 1;
			$l = 0; // no urut
			
			$k = 1; // pjg hal
			$p = 1; // awal hal
			
			$i=$i+3;
			$k=$k+3;
			
			date_default_timezone_set("Asia/Bangkok");
			$sel->setCellValue('B'.$i,'Print : '.date('d-m-Y H:i:s'));
			
			$sel->setCellValue('F'.$i,'User :'.trim($this->session->userdata('gUser')));
			
			$i=$i+2;
			$k=$k+2;
			
			foreach ($data->result() as $row) 
			{
				$sel->setCellValue('B'.$i, '..' . substr(trim($row->fs_refno),9));
				$sel->setCellValue('C'.$i, trim($row->fs_nm_birojs));
				$sel->setCellValue('D'.$i, substr(trim($row->fs_alamat),0,20).'..');
				$sel->setCellValue('E'.$i, trim($row->fs_nm_strx));
				$sel->setCellValue('F'.$i, trim($row->fs_no_faktur));
				$sel->setCellValue('G'.$i, trim($row->fd_refno));
				$sel->setCellValue('H'.$i, trim($row->fd_rcvdt));
				
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
					
				$oExcel->getActiveSheet()->getStyle('D'.$i.':D'.$i)->applyFromArray(
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
				
				$oExcel->getActiveSheet()->getStyle('F'.$i.':F'.$i)->applyFromArray(
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
				
				$oExcel->getActiveSheet()->getStyle('H'.$i.':H'.$i)->applyFromArray(
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
					
					
				$i++;
				$k++;
			}
			
			$i--;
			$k--;
			
			$oExcel->getActiveSheet()->getStyle('B'.$i.':H'.$i)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
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