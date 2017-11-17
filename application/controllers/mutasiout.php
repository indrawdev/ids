<?php

class MutasiOut extends CI_Controller
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
			$this->load->view('vmutasiout');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function cek_unit()
	{
		$kdprod = trim($this->input->post('fs_kd_product'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->unit2_all($kdprod);
		$jml = $ssql->num_rows();
		
		if ($jml < 1) {
			$data = array(
				'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
				'fs_kd_product'	=> trim($kdprod),
				'fs_kd_unit'	=> 'UNIT',
				'fs_kd_type'	=> '0',
				'fb_active'		=> '1',
				
				'fs_usrcrt'		=> trim($this->session->userdata('gUser')),
				'fd_usrcrt'		=> trim(date('Y-m-d H:i:s')),
				'fs_upddt'		=> trim($this->session->userdata('gUser')),
				'fd_upddt'		=> trim(date('Y-m-d H:i:s'))
			);
			
			$this->db->insert('tx_unitproduct', $data);
		}
		
		$hasil = array(
			'sukses'	=> true,
			'satuan'	=> 'UNIT'
		);
		echo json_encode($hasil);
	}
	
	function product()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdprod = trim($this->input->post('fs_kd_product'));
		$nmprod = trim($this->input->post('fs_nm_product'));
		
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->product_all($kdprod,$nmprod,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->product($kdprod,$nmprod,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function unit()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdprod = trim($this->input->post('fs_kd_product'));
		$kdunit = trim($this->input->post('fs_kd_unit'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->unit_all($kdprod);
		
		echo json_encode($ssql->result());
	}
	
	function reg()
	{
		$kdwh = trim($this->input->post('fs_kd_wh'));
		$kdprod = trim($this->input->post('fs_kd_product'));
		$seqno = trim($this->input->post('fs_seqno'));
		$refno = trim($this->input->post('fs_refno'));
		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mMutasiOut','',true);
		$ssql = $this->mMutasiOut->register($kdwh,$kdprod,$seqno,$refno,$cari);
		
		echo json_encode($ssql->result());
	}
	
	function grid_prod()
	{
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		$refno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mMutasiOut','',true);
		$ssql = $this->mMutasiOut->grid_prod($kddept,$kdcount,$refno);
		
		echo json_encode($ssql->result());
	}
	
	function grid_reg()
	{
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		$kdtrx = '3400';
		$kdstrx = '0100';
		$refno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mMutasiOut','',true);
		$ssql = $this->mMutasiOut->grid_reg($kddept,$kdcount,$kdtrx,$kdstrx,$refno);
		
		echo json_encode($ssql->result());
	}
	
	function refno()
	{
		$nstart = trim($this->input->post('start'));
		
		$refno = trim($this->input->post('fs_refno'));
		$cari = trim($this->input->post('fs_cari'));
		$kdtrx = '3400';
		$kdstrx = '0100';
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->mutasi_all($refno,$kdtrx,$kdstrx,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->mutasi($refno,$kdtrx,$kdstrx,$nstart,$total,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function mutasi_ceksave()
	{
		$refno = trim($this->input->post('fs_refno'));
		$kdtrx = trim($this->input->post('fs_kd_trx'));
		$refnoout = trim($this->input->post('fs_refnoout'));
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		
		if (trim($refno) == '' or trim($refno) == 'AUTOMATIC')
		{
			if ($kdtrx == '3300')
			{
				$this->load->model('mMutasiOut','',true);
				$ssql = $this->mMutasiOut->cek_refnoout($refnoout);
				
				if ($ssql->num_rows() > 0)
				{
					$hasil = array(
						'sukses'	=> false,
						'hasil'		=> 'Saving Failed, Transfer Out "'.trim($refnoout).'" already In!!'
					);
					echo json_encode($hasil);
					return;
				}
				
				$this->load->model('mBeli','',true);
				$ssql = $this->mBeli->cek_jual($rangka,$mesin);
				
				if ($ssql->num_rows() > 0)
				{
					$hasil = array(
						'sukses'	=> false,
						'hasil'		=> 'Saving Canceled, There are product which spesific chasis and macines already sold!!'
					);
					echo json_encode($hasil);
					return;
				}
			}
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'lanjut'
			);
			echo json_encode($hasil);
		}
		else
		{
			$kddept = trim($this->input->post('fs_kd_dept'));
			$kdcount = trim($this->input->post('fs_count'));
			$kdtrx = trim($this->input->post('fs_kd_trx'));
			$kdstrx = '0100';
			
			$this->load->model('mBeli','',true);
			$ssql = $this->mBeli->cek_jual($rangka,$mesin);
			
			if ($ssql->num_rows() > 0)
			{
				$hasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Saving Canceled, There are product which spesific chasis and macines already sold!!'
				);
				echo json_encode($hasil);
				return;
			}
			
			if ($kdtrx == '3400')
			{
				if (strtolower(trim($this->session->userdata('gDatabase'))) == 'aster-gitsunter')
				{
					// $this->load->model('mMutasiOut','',true);
					// $ssql = $this->mMutasiOut->cek_refnooutdb('aster-mayestik',$refno);
					
					// if ($ssql->num_rows() > 0)
					// {
						// $hasil = array(
							// 'sukses'	=> false,
							// 'hasil'		=> 'Saving Failed,</br>Transfer Out "'.trim($refno).'" in DB "ASTER-MAYESTIK" already In!!'
						// );
						// echo json_encode($hasil);
						// return;
					// }
				}
				else
				{
					$this->load->model('mMutasiOut','',true);
					$ssql = $this->mMutasiOut->cek_refnoout($refno);
					
					if ($ssql->num_rows() > 0)
					{
						$hasil = array(
							'sukses'	=> false,
							'hasil'		=> 'Saving Failed, Transfer Out "'.trim($refno).'" already In!!'
						);
						echo json_encode($hasil);
						return;
					}
				}
			}
			
			$this->load->model('mMutasiOut','',true);
			$ssql = $this->mMutasiOut->mutasi_cek_refno($kddept,$kdcount,$kdtrx,$kdstrx,$refno,$refnoout);
			
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
	
	function mutasi_save()
	{
		$refno = trim($this->input->post('fs_refno'));
		$tglrefno = trim($this->input->post('fd_refno'));
		$tgldocno = trim($this->input->post('fd_docno'));
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		
		$kdwh = trim($this->input->post('fs_kd_wh'));
		$nmwh = trim($this->input->post('fs_nm_wh'));
		$kddeptt = trim($this->input->post('fs_kd_deptt'));
		$kdcountt = trim($this->input->post('fs_countt'));
		$kdwht = trim($this->input->post('fs_kd_wht'));
		$nmwht = trim($this->input->post('fs_nm_wht'));
		
		$this->load->model('mMainModul','',true);
		$docno = $this->mMainModul->clean_input(trim($this->input->post('fs_docno')));
		$this->load->model('mMainModul','',true);
		$note = $this->mMainModul->clean_input(trim($this->input->post('fs_note')));
		$kdtrx = trim($this->input->post('fs_kd_trx'));
		$kdstrx = '0100';
		$kdtipe = '0';
		$status = '0';
		
		if  ($kdtrx == '3400')
		{
			$refnoout = '';
		}
		else if ($kdtrx == '3300')
		{
			$refnoout = trim($this->input->post('fs_refnoout'));
		}
		
		if ($status == '1')
		{
			$trsta = '5';
		}
		else
		{
			$trsta = '7';
		}
		
		//get periode
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->getperiode($tglrefno);
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$periode = $ssql->periode;
		}
		else
		{
			$periode = '';
		}
		//eof get periode
		
		$xupdate = false;
		$this->load->model('mMutasiOut','',true);
		$ssql = $this->mMutasiOut->mutasi_cek_refno($kddept,$kdcount,$kdtrx,$kdstrx,$refno,$refnoout);
		
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
			$ssql = $this->mMainModul->get_refno($kddept,$kdcount,$kdtrx,$kdstrx,$tglrefno);
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
		
		
		//save header
		$dt = array(
			'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
			'fs_kd_dept'	=> trim($kddept),
			'fs_count'		=> trim($kdcount),
			'fs_kd_trx'		=> trim($kdtrx),
			'fs_kd_strx'	=> trim($kdstrx),
			
			'fs_refno'		=> trim($refno),
			'fd_refno'		=> trim($tglrefno),
			'fd_periode'	=> trim($periode),
			'fs_doc'		=> trim($docno),
			'fd_doc'		=> trim($tgldocno),
			
			'fs_descrp'		=> trim($note),
			'fs_kd_wh'		=> trim($kdwh),
			'fs_kd_bin'		=> '',
			'fs_kd_alloc'	=> '',
			'fs_kd_tdept'	=> trim($kddeptt),
			
			'fs_tcount'		=> trim($kdcountt),
			'fs_trefno'		=> trim($refnoout),
			'fs_kd_wht'		=> trim($kdwht),
			'fs_kd_bint'	=> '',
			'fs_kd_alloct'	=> '',
			
			'fb_delete'		=> '0',
			'fb_draft'		=> trim($status),
			'fs_trsta'		=> trim($trsta),
			'fs_modul'		=> 'IC'
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
				
				$this->db->insert('tx_actheader', $data);
				
				// $hasil = array(
					// 'sukses'	=> true,
					// 'hasil'	=> 'Saving Success',
					// 'refno'	=> $refno
				// );
				// echo json_encode($hasil);
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
				'fs_upddt' => trim($this->session->userdata('gUser')),
				'fd_upddt' => trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
						AND fs_kd_dept = '".trim($kddept)."'
						AND	fs_count = '".trim($kdcount)."'
						AND	fs_kd_trx = '".trim($kdtrx)."'
						AND	fs_kd_strx = '".trim($kdstrx)."'
						AND	fs_refno = '".trim($refno)."'";
			
			$this->db->where($where);
			$this->db->update('tx_actheader', $data);
			
			// $hasil = array(
				// 'sukses'	=> true,
				// 'hasil'	=> 'Saving Update Success',
				// 'refno' 	=> $refno
			// );
			// echo json_encode($hasil);
		}
		//eof save header
		
		if ($kdtrx == '3400')
		{
			$this->load->model('mMutasiOut','',true);
			$ssql = $this->mMutasiOut->mutasi_header($kddept,$kdcount,$kdtrx,$kdstrx,$refno,$kdwh,$nmwh);
		}
		else if ($kdtrx == '3300')
		{
			$this->load->model('mMutasiIn','',true);
			$ssql = $this->mMutasiIn->mutasi_header($refnoout,$refno);
		}
		
		//save detail
		//hapus detail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($kddept)."'
					AND	fs_count = '".trim($kdcount)."'
					AND	fs_kd_trx = '".trim($kdtrx)."'
					AND	fs_kd_strx = '".trim($kdstrx)."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actdetail');
		//eof hapus detail
		
		
		$kdprod = explode('|', trim($this->input->post('fs_kd_product')));
		$qty = explode('|', trim($this->input->post('fn_qty')));
		$kdunit = explode('|', trim($this->input->post('fs_kd_unit')));
		$seqno = explode('|', trim($this->input->post('fs_seqno')));
		
		$jml = count($kdprod) - 1;
		if ($jml != 0)
		{
			for ($i=1; $i<=$jml; $i++)
			{
				$data = array(
					'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
					'fs_kd_dept'	=> trim($kddept),
					'fs_count'		=> trim($kdcount),
					'fs_kd_trx'		=> trim($kdtrx),
					'fs_kd_strx'	=> trim($kdstrx),
					
					'fs_refno'		=> trim($refno),
					'fs_seqno'		=> trim($seqno[$i]),
					'fd_periode'	=> trim($periode),
					'fd_refno'		=> trim($tglrefno),
					'fs_descp'		=> '',
					
					'fs_kd_product'	=> trim($kdprod[$i]),
					'fs_kd_type'	=> trim($kdtipe),
					'fn_qtytr'		=> trim($qty[$i]),
					'fn_qtytr1'		=> trim($qty[$i]),
					'fs_unitbill'	=> trim($kdunit[$i]),
					
					'fb_conv'		=> '0',
					'fn_conv'		=> '0',
					'fs_kd_wh'		=> trim($kdwh),
					'fs_kd_bin'		=> '',
					'fs_kd_alloc'	=> '',
					
					'fs_trsta'		=> trim($trsta),
					'fs_modul'		=> 'IC',
					
					'fs_usrcrt'		=> trim($this->session->userdata('gUser')),
					'fd_usrcrt'		=> trim(date('Y-m-d H:i:s')),
					'fs_upddt'		=> trim($this->session->userdata('gUser')),
					'fd_upddt'		=> trim(date('Y-m-d H:i:s'))
				);
				
				$this->db->insert('tx_actdetail', $data);
			}
		}
		
		$kdprodreg = explode('|', trim($this->input->post('fs_kd_prodreg')));
		$rangka = explode('|', trim($this->input->post('fs_rangka')));
		$mesin = explode('|', trim($this->input->post('fs_mesin')));
		$seqnoreg = explode('|', trim($this->input->post('fs_seqnoreg')));
		
		$jml = count($rangka) - 1;
		
		if ($jml != 0)
		{
			if ($kdtrx == '3400')
			{
				for ($i=1; $i<=$jml; $i++)
				{
					$this->load->model('mMutasiOut','',true);
					$ssql = $this->mMutasiOut->mutasi_reg($kddept,$kdcount,$kdtrx,$kdstrx,$refno,
									$kdprodreg[$i],$seqnoreg[$i],$kdtipe,$rangka[$i],$mesin[$i],$kdwht,$nmwht);
				}
				
				$this->load->model('mMutasiOut','',true);
				$ssql = $this->mMutasiOut->mutasi_move($refno);
			}
			else if ($kdtrx == '3300')
			{
				for ($i=1; $i<=$jml; $i++)
				{
					$this->load->model('mMutasiIn','',true);
					$ssql = $this->mMutasiIn->mutasi_reg($kddept,$kdcount,$kdtrx,$kdstrx,$refno,
									$kdprodreg[$i],$seqnoreg[$i],$kdtipe,$rangka[$i],$mesin[$i],$kdwh,$nmwh);
				}
				
				$this->load->model('mMutasiOut','',true);
				$ssql = $this->mMutasiOut->mutasi_move($refno);
			}
		}
		//eof save detail
		
		
		if ($kdtrx == '3400')
		{
			if (strtolower(trim($this->session->userdata('gDatabase'))) == 'aster-gitsunter')
			{
				// $this->load->model('mMutasiOut','',true);
				// $ssql = $this->mMutasiOut->eksportodb('aster-mayestik',$kdtrx,$kdstrx,$refno);
			}
		}
		
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
	}
	
	function mutasi_cekremove()
	{
		$refno = trim($this->input->post('fs_refno'));
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		$kdtrx = trim($this->input->post('fs_kd_trx'));
		$kdstrx = '0100';
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
			$refnoout = trim($this->input->post('fs_refnoout'));
			
			if (trim($kdtrx) == '3400')
			{
				$this->load->model('mMutasiOut','',true);
				$ssql = $this->mMutasiOut->cek_refnoout($refno);
				
				if ($ssql->num_rows() > 0)
				{
					$hasil = array(
						'sukses'	=> false,
						'hasil'		=> 'Saving Failed, Transfer Out "'.trim($refno).'" already In!!'
					);
					echo json_encode($hasil);
					return;
				}
			}
			
			// $this->load->model('mBeli','',true);
			// $ssql = $this->mBeli->cek_jual($rangka,$mesin);
			
			// if ($ssql->num_rows() > 0)
			// {
				// $hasil = array(
					// 'sukses'	=> false,
					// 'hasil'		=> 'Saving Canceled, There are product which spesific chasis and macines already sold!!'
				// );
				// echo json_encode($hasil);
				// return;
			// }
			
			$this->load->model('mMutasiOut','',true);
			$ssql = $this->mMutasiOut->mutasi_cek_refno($kddept,$kdcount,$kdtrx,$kdstrx,$refno,$refnoout);
			
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
	
	function mutasi_remove()
	{
		$refno = trim($this->input->post('fs_refno'));
		$kddept = trim($this->input->post('fs_kd_dept'));
		$kdcount = trim($this->input->post('fs_count'));
		$kdwh = trim($this->input->post('fs_kd_wh'));
		$nmwh = trim($this->input->post('fs_nm_wh'));
		$kdtrx = trim($this->input->post('fs_kd_trx'));
		$kdstrx = '0100';
		
		if  ($kdtrx == '3400')
		{
			$refnoout = '';
		}
		else if ($kdtrx == '3300')
		{
			$refnoout = trim($this->input->post('fs_refnoout'));
		}
		
		//remove header
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($kddept)."'
					AND	fs_count = '".trim($kdcount)."'
					AND	fs_kd_trx = '".trim($kdtrx)."'
					AND	fs_kd_strx = '".trim($kdstrx)."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actheader');
		//eof remove header
		
		if ($kdtrx == '3400')
		{
			$this->load->model('mMutasiOut','',true);
			$ssql = $this->mMutasiOut->mutasi_header($kddept,$kdcount,$kdtrx,$kdstrx,$refno,$kdwh,$nmwh);
		}
		else if ($kdtrx == '3300')
		{
			$this->load->model('mMutasiIn','',true);
			$ssql = $this->mMutasiIn->mutasi_header($refnoout,$refno);
		}
		
		
		//remove detail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($kddept)."'
					AND	fs_count = '".trim($kdcount)."'
					AND	fs_kd_trx = '".trim($kdtrx)."'
					AND	fs_kd_strx = '".trim($kdstrx)."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actdetail');
		//eof remove detail
		
		$hasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Remove reference number: "'.$refno.'" success'
		);
		echo json_encode($hasil);
	}
	
	function print_mutasiout()
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
		
		$this->load->model('mMutasiOut','',true);
		$ssql = $this->mMutasiOut->mutasiout_cetak($refno);
		
		$data = array();
		$data = $ssql;
		$jumlah_unit = $ssql->num_rows();
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			
			$NoMutasi = $ssql->fs_refno;
			$NamaCabangFrom = $ssql->fs_nm_deptf;
			$AlamatCabangFrom = $ssql->fs_addrf;
			$KotaFrom = $ssql->fs_nm_kotaf;
			
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