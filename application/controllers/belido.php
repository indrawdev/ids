<?php

class BeliDO extends CI_Controller
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
			$this->load->view('vbelido');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function refno()
	{
		$nstart = trim($this->input->post('start'));
		
		$refno = trim($this->input->post('fs_refno'));
		$docno = trim($this->input->post('fs_docno'));
		$trx = 'DO';
		
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->beli_do_all($trx,$refno,$docno,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->beli_do($trx,$refno,$docno,$nstart,$total,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function refnodo()
	{
		$nstart = trim($this->input->post('start'));
		
		$refno = trim($this->input->post('fs_refno'));
		$docno = trim($this->input->post('fs_docno'));
		$trx = 'DO';

		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->beli_do2_all($trx,$refno,$docno,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->beli_do2($trx,$refno,$docno,$nstart,$total,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function ambil_comp()
	{
		$nstart = trim($this->input->post('start'));
		
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->comp_impor_all();
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->comp_impor($nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function grid_prod2()
	{
		$sComp = trim($this->input->post('fs_kd_comp'));
		$sDB = trim($this->input->post('fs_nm_db'));
		
		$this->load->model('mBeliDO','',true);
		$ssql = $this->mBeliDO->grid_prod2($sComp,$sDB);
		
		$xArr = array();
		if ($ssql->num_rows() > 0)
		{
			foreach ($ssql->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_product'	=> trim($xRow->fs_kd_product),
					'fs_nm_product'	=> trim($xRow->fs_nm_product),
					'fn_qty'		=> trim($xRow->fn_qty),
					'fs_kd_unit'	=> 'UNIT',
					'fs_seqno'		=> trim($xRow->fs_seqno)
				);
			}
		}
		echo json_encode($xArr);
	}
	
	function grid_reg2()
	{
		$sComp = trim($this->input->post('fs_kd_comp'));
		$sDB = trim($this->input->post('fs_nm_db'));
		$xprod = explode('|', trim($this->input->post('fs_kd_product')));
		$xseqno = explode('|', trim($this->input->post('fs_seqno')));
		$jml = count($xprod) - 1;
		
		$this->load->model('mBeliDO','',true);
		$ssql = $this->mBeliDO->grid_reg2($sComp,$sDB);
		
		$xArr = array();
		if ($ssql->num_rows() > 0)
		{
			foreach ($ssql->result() as $xRow)
			{
				if ($jml != 0)
				{
					for ($i=1; $i<=$jml; $i++)
					{
						if (trim($xprod[$i]) == trim($xRow->fs_kd_product))
						{
							$xseq = trim($xseqno[$i]);
							break;
						}
					}
				}
				
				$xArr[] = array(
					'fs_rangka'		=> trim($xRow->fs_rangka),
					'fs_mesin'		=> trim($xRow->fs_mesin),
					'fs_cc'			=> trim($xRow->fs_cc),
					'fs_thn'		=> trim($xRow->fs_thn),
					'fs_kd_color'	=> trim($xRow->fs_kd_color),
					
					'fs_nm_color'	=> trim($xRow->fs_nm_color),
					'fs_kd_wh'		=> trim($xRow->fs_kd_wh),
					'fs_nm_wh'		=> trim($xRow->fs_nm_wh),
					'fs_seqno'		=> trim($xseq),
					'fs_kd_product'	=> trim($xRow->fs_kd_product)
				);
			}
		}
		echo json_encode($xArr);
	}
	
	function ceksave()
	{
		$refno = trim($this->input->post('fs_refno'));
		$kdtrx = 'BL';
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		
		$prod = explode('|', trim($this->input->post('fs_kd_product')));
		$qty = explode('|', trim($this->input->post('fn_qty')));
		
		$jml = count($prod) - 1;
		if ($jml != 0)
		{
			for ($i=1; $i<=$jml; $i++)
			{
				if (trim($qty[$i]) == '0')
				{
					$hasil = array(
						'sukses'	=> false,
						'hasil'		=> 'Saving Failed, </br> Chassis is empty for product "'.trim($prod[$i]).'"!! </br> Please fill it first'
					);
					echo json_encode($hasil);
					return;
				}
			}
		}
		else
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, </br> Product is empty, please fill in advance!!'
			);
			echo json_encode($hasil);
			return;
		}
		
		$this->load->model('mBeli','',true);
		$ssql = $this->mBeli->cek_mutasi($rangka,$mesin);
		
		if ($ssql->num_rows() > 0)
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Canceled, There are product which spesific chasis and macines already transfer out!!</br>Please transfer in first!!'
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
			$this->load->model('mBeliDO','',true);
			$ssql = $this->mBeliDO->cek_inv($rangka,$mesin);
			
			if ($ssql->num_rows() > 0)
			{
				$hasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Saving Canceled, There are product which spesific chasis and macines already have invoice!!'
				);
				echo json_encode($hasil);
				return;
			}
			
			$this->load->model('mBeli','',true);
			$ssql = $this->mBeli->cek_refno('DO',$refno);
			
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
		
		$kdsupp = trim($this->input->post('fs_kd_sup'));
		$count = trim($this->input->post('fs_count'));
		$kdterm = trim($this->input->post('fs_kd_term'));
		$kdbelimtd = trim($this->input->post('fs_kd_belimtd'));
		
		$nmbelimtd = trim($this->input->post('fs_nm_belimtd'));
		$kdtrx = 'BL';
		
		//get periode
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->getperiode($refnodt);
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
		$this->load->model('mBeli','',true);
		$ssql = $this->mBeli->cek_refno('DO',$refno);
		
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
			$xdate = substr(trim($refnodt), 2, 6);
			
			$xprefix = trim($this->session->userdata('gSparePart')).trim($kdtrx).'/'.trim($xdept).'/'.trim($xdate).'/';
			$this->load->model('mMainModul','',true);
			$ssql = $this->mMainModul->get_fakturbeli($xprefix);
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
		
		$dt = array(
			'fs_kd_comp'		=> trim($this->session->userdata('gComp')),
			'fs_kd_dept'		=> trim($this->session->userdata('gDept')),
			'fs_count'			=> trim($this->session->userdata('gCount')),
			'fs_kd_trx'			=> 'DO',
			'fs_refno'			=> trim($refno),
			
			'fd_refno'			=> trim($refnodt),
			'fd_periode'		=> trim($periode),
			'fs_kd_cussup'		=> trim($kdsupp),
			'fs_countcussup'	=> trim($count),
			'fs_docno'			=> trim($docno),
			
			'fd_docno'			=> trim($docnodt),
			'fs_kd_salesmtd'	=> trim($kdbelimtd),
			'fs_nm_salesmtd'	=> trim($nmbelimtd)
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
			
			$this->db->insert('tx_posheader', $data);
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
			$this->db->update('tx_posheader', $data);
		}
		
		//hapus stok
		$this->load->model('mJual','',true);
		$ssql = $this->mJual->unit_stokupdate($refno,'DEL');
		//eof hapus stok
		
		//hapus reg di mutasi
		$this->load->model('mBeliDO','',true);
		$ssql = $this->mBeliDO->cek_rangkadb2($refno);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$xnmdb = trim($ssql->fs_nm_db);
			
			$this->load->model('mBeliDO','',true);
			$ssql = $this->mBeliDO->kosongtodb($xnmdb,$refno);
		}
		
		$data = array(
			'fs_refnoin'	=> ''
		);
		$where = "fs_refnoin = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->update('tx_mutasidbd', $data);
		//eof hapus reg di mutasi
		
		//hapus reg
		$where = "fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tm_icregister');
		//eof hapus reg
		
		
		//hapus detail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_posdetail');
		//eof hapus detail
		
		//simpan detail
		$kdprod = explode('|', trim($this->input->post('fs_kd_product')));
		$nmprod = explode('|', trim($this->input->post('fs_nm_product')));
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
					'fs_kd_dept'	=> trim($this->session->userdata('gDept')),
					'fs_count'		=> trim($this->session->userdata('gCount')),
					'fs_kd_trx'		=> 'DO',
					'fs_refno'		=> trim($refno),
					
					'fd_refno'		=> trim($refnodt),
					'fd_periode'	=> trim($periode),
					'fs_seqno'		=> trim($seqno[$i]),
					'fs_kd_product'	=> trim($kdprod[$i]),
					'fs_nm_product'	=> trim($nmprod[$i]),
					
					'fn_qtytr'		=> trim($qty[$i]),
					'fn_qtytr1'		=> trim($qty[$i]),
					'fs_unitbill'	=> trim($kdunit[$i]),
					'fn_unitprc'	=> '0',//trim($harga[$i]),//
					'fn_dscprc'		=> '0',//trim($disc[$i]),//
					
					'fn_lineamt'	=> '0',//$jmlharga,//
					'fn_ldscamt'	=> '0',//$jmldiskon,//
					'fn_sdscamt'	=> '0',//$diskon,//
					'fn_addonamt'	=> '0',
					'fn_deducamt'	=> '0',
					
					'fn_ftrxamt'	=> '0',//(float)(round($jmlharga - $jmldiskon - $diskon, 6)),//
					'fs_kd_wh'		=> '',
					'fs_countwh'	=> '',
					'fs_kd_bin'		=> '',
					'fs_luxcd'		=> '',//$kdlux[$i],//
					
					'fs_luxnm'		=> '',//$nmlux[$i],//
					'fn_luxpct'		=> '0',//$luxpersen[$i],//
					'fn_luxamt'		=> '0',//$lux[$i],//
					'fs_kd_ppnbm'	=> '',
					'fs_nm_ppnbm'	=> '',
					
					'fn_ppnbm'		=> '0',
					'fn_ppnbmpct'	=> '0',
					
					'fs_usrcrt'		=> trim($this->session->userdata('gUser')),
					'fd_usrcrt'		=> trim(date('Y-m-d H:i:s')),
					'fs_upddt'		=> trim($this->session->userdata('gUser')),
					'fd_upddt'		=> trim(date('Y-m-d H:i:s'))
				);
				
				$this->db->insert('tx_posdetail', $data);
			}
		}
		//eof simpan detail
		
		
		//simpan reg
		$kdprodreg = explode('|', trim($this->input->post('fs_kd_prodreg')));
		$rangka = explode('|', trim($this->input->post('fs_rangka')));
		$mesin = explode('|', trim($this->input->post('fs_mesin')));
		$cc = explode('|', trim($this->input->post('fs_cc')));
		$thn = explode('|', trim($this->input->post('fs_thn')));
		
		$kdwarna = explode('|', trim($this->input->post('fs_kd_color')));
		$kdwh = explode('|', trim($this->input->post('fs_kd_wh')));
		$nmwh = explode('|', trim($this->input->post('fs_nm_wh')));
		
		$seqno = explode('|', trim($this->input->post('fs_seqnoreg')));
		$nmseqno = '';
		$seqnoreg = 0;
		
		$jml = count($kdprodreg) - 1;
		if ($jml != 0)
		{
			for ($i=1; $i<=$jml; $i++)
			{
				//generate reg product
				$xprefix = trim($this->session->userdata('gDept')).trim($kdprodreg[$i]).trim($refnodt);
				$this->load->model('mMainModul','',true);
				$ssql = $this->mMainModul->get_regproduct($xprefix);
				//eof generate reg product
				
				if ($ssql->num_rows() > 0)
				{
					$ssql = $ssql->row();
					
					$register = $xprefix.sprintf("%06d",trim($ssql->fs_seqno) + 1);
				}
				else
				{
					$register = $xprefix.'000001';
				}
				
				if (trim($nmseqno) == '' or trim($nmseqno) <> trim($seqno[$i]))
				{
					$seqnoreg = 1;
				}
				else
				{
					++$seqnoreg;
				}
				
				$data = array(
					'fs_kd_comp'		=> trim($this->session->userdata('gComp')),
					'fs_kd_dept'		=> trim($this->session->userdata('gDept')),
					'fs_count'			=> trim($this->session->userdata('gCount')),
					'fs_kd_trx'			=> trim($kdtrx),
					'fs_kd_strx'		=> '0100',
					
					'fs_refno'			=> trim($refno),
					'fd_periode'		=> trim($periode),
					'fs_seqnoRegister'	=> trim(sprintf("%06d",$seqnoreg)),
					'fs_seqno'			=> trim($seqno[$i]),
					'fs_register'		=> trim($register),
					
					'fs_kd_product'		=> trim($kdprodreg[$i]),
					'fs_kd_type'		=> '0',
					'fn_hpp'			=> '0',//trim($hpp[$i]),//
					'fb_delete'			=> '0',
					'fn_silinder'		=> trim($cc[$i]),
					
					'fs_rangka'			=> trim($rangka[$i]),
					'fs_machine'		=> trim($mesin[$i]),
					'fs_kd_warna'		=> trim($kdwarna[$i]),
					'fd_thnpembuatan'	=> trim($thn[$i]),
					'fd_thnperakitan'	=> trim($thn[$i]),
					
					'fn_qty'			=> '1',
					'fs_kd_wh'			=> trim($kdwh[$i]),
					'fs_nm_wh'			=> trim($nmwh[$i]),
					
					'fs_usrcrt'			=> trim($this->session->userdata('gUser')),
					'fd_usrcrt'			=> trim(date('Y-m-d H:i:s')),
					'fs_upddt'			=> trim($this->session->userdata('gUser')),
					'fd_upddt'			=> trim(date('Y-m-d H:i:s'))
				);
				
				$this->db->insert('tm_icregister', $data);
				
				$this->load->model('mBeli','',true);
				$ssql = $this->mBeli->reg_last($refno,$rangka[$i],$mesin[$i]);
				
				$nmseqno = trim($seqno[$i]);
				
				
				//simpan di mutasi
				$data = array(
					'fs_refnoin'	=> trim($refno)
				);
				$where = "fs_kd_product = '".trim($kdprodreg[$i])."'
							AND	fs_rangka = '".trim($rangka[$i])."'
							AND	fs_mesin = '".trim($mesin[$i])."'
							AND	fs_kd_warna = '".trim($kdwarna[$i])."'";
				
				$this->db->where($where);
				$this->db->update('tx_mutasidbd', $data);
				
				
				$this->load->model('mBeliDO','',true);
				$ssql = $this->mBeliDO->cek_rangkadb($kdprodreg[$i],$rangka[$i],$mesin[$i],$kdwarna[$i]);
				
				if ($ssql->num_rows() > 0)
				{
					$ssql = $ssql->row();
					$xnmdb = trim($ssql->fs_nm_db);
					
					$this->load->model('mBeliDO','',true);
					$ssql = $this->mBeliDO->simpantodb2($xnmdb,$refno,$kdprodreg[$i],$rangka[$i],$mesin[$i],$kdwarna[$i]);
				}
				//eof simpan di mutasi
			}
		}
		//eof simpan reg

		//masuk stok
		$this->load->model('mJual','',true);
		$ssql = $this->mJual->unit_stokupdate($refno,'INS');
		//eof masuk stok
		
		// delete all record tx_tempexcel
		$dept = trim($this->session->userdata('gDept'));
		$where = "fs_kd_dept = '".trim($dept)."'";
		$this->db->where($where);
		$this->db->delete('tx_tempexcel');

		if ($xupdate == false)
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving DO Success',
				'refno'		=> $refno
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving DO Update Success',
				'refno'		=> $refno
			);
			echo json_encode($hasil);
		}
	}
	
	function cekremove()
	{
		$refno = trim($this->input->post('fs_refno'));
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		
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
			$this->load->model('mBeli','',true);
			$ssql = $this->mBeli->cek_refno('DO',$refno);
			
			if ($ssql->num_rows() > 0)
			{
				$this->load->model('mBeli','',true);
				$ssql = $this->mBeli->cek_mutasi($rangka,$mesin);
				
				if ($ssql->num_rows() > 0)
				{
					$hasil = array(
						'sukses'	=> false,
						'hasil'		=> 'Remove Failed, There are product which spesific chasis and macines already transfer out!!</br>Please transfer in first!!'
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
						'hasil'		=> 'Remove Canceled, There are product which spesific chasis and macines already sold!!'
					);
					echo json_encode($hasil);
					return;
				}
				
				$this->load->model('mBeliDO','',true);
				$ssql = $this->mBeliDO->cek_inv($rangka,$mesin);
				
				if ($ssql->num_rows() > 0)
				{
					$hasil = array(
						'sukses'	=> false,
						'hasil'		=> 'Remove Canceled, There are product which spesific chasis and macines already have invoice!!'
					);
					echo json_encode($hasil);
				}
				else
				{
					$hasil = array(
						'sukses'	=> true,
						'hasil'		=> 'Remove record?'
					);
					echo json_encode($hasil);
				}
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
		
		//hapus beli
		//hapus tx_posheader
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_posheader');
		//eof hapus tx_posheader
		
		//hapus tx_posdetail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_posdetail');
		//eof hapus tx_posdetail
		
		//hapus reg di mutasi
		$this->load->model('mBeliDO','',true);
		$ssql = $this->mBeliDO->cek_rangkadb2($refno);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$xnmdb = trim($ssql->fs_nm_db);
			
			$this->load->model('mBeliDO','',true);
			$ssql = $this->mBeliDO->kosongtodb($xnmdb,$refno);
		}
		
		$data = array(
			'fs_refnoin'	=> ''
		);
		$where = "fs_refnoin = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->update('tx_mutasidbd', $data);
		//eof hapus reg di mutasi
		
		//hapus tm_icregister
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tm_icregister');
		//eof hapus tm_icregister
		
		//eof hapus beli
		
		$hasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Remove DO reference number: "'.$refno.'" success'
		);
		echo json_encode($hasil);
	}

	// FUNCTION IMPORT EXCEL
	public function gridimport() {
		$this->load->model('mBeliDO','',true);
		$ssql = $this->mBeliDO->listImport();
		$xArr = array();
		if ($ssql->num_rows() > 0)
		{
			$seqno = 1;
			foreach ($ssql->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_wh'		=> trim($xRow->fs_kd_wh),
					'fs_kd_product'	=> trim($xRow->fs_kd_product),
					'fs_nm_product'	=> trim($xRow->fs_nm_product),
					'fs_thn'		=> trim($xRow->fs_thn),
					'fn_qty'		=> trim($xRow->fn_qty),
					'fs_unit'		=> 'UNIT',
					'fs_seqno'		=> trim($seqno)
				);

				$seqno = $seqno + 1;
			}
		}
		echo json_encode($xArr);
	}

	public function griddetail() {
		$this->load->model('mBeliDO','',true);
		$ssql = $this->mBeliDO->listImportDetail();
		$xArr = array();
		if ($ssql->num_rows() > 0)
		{
			foreach ($ssql->result() as $xRow)
			{
				$xArr[] = array(
					'fs_rangka'		=> trim($xRow->fs_rangka),
					'fs_mesin'		=> trim($xRow->fs_mesin),
					'fs_cc'			=> trim($xRow->fs_cc),
					'fs_thn'		=> trim($xRow->fs_thn),
					'fs_kd_color'	=> trim($xRow->fs_kd_color),
					'fs_nm_color'	=> trim($xRow->fs_nm_color),
					'fs_kd_wh'		=> trim($xRow->fs_kd_wh),
					'fs_nm_wh'		=> trim($xRow->fs_nm_wh),
					'fs_kd_product'	=> trim($xRow->fs_kd_product)
				);
			}
		}
		echo json_encode($xArr);
	}

	public function uploadfile() {
		$user = trim($this->session->userdata('gUser'));
		$dept = trim($this->session->userdata('gDept'));
		// variable
		$nmWH = trim($this->input->post('cboWH2'));
		$kdWH = trim($this->input->post('txtWH2'));
		$thn = trim($this->input->post('txtThnKend'));

		if (!empty($_FILES['txtFileExcel']['name'])) {
			$name = $_FILES['txtFileExcel']['name'];
			$tmp = explode('.', $name);
			$file_extension = end($tmp);

			if ($file_extension <> 'xlsx') {
				$response = array(
					'success' => false, 
					'msg' => 'Jenis file ini bukan (.XLSX)'
				);
				echo json_encode($response);
			} else {
				$config['upload_path'] = './temp/excel/';
				$config['max_size'] = 10000;
				$config['allowed_types'] = 'xlsx';
				$config['file_name'] = $_FILES['txtFileExcel']['name'];
				$config['encrypt_name'] = TRUE;
				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if ($this->upload->do_upload('txtFileExcel')) {
					$uploadData = $this->upload->data();
					$file = $uploadData['file_name'];
					$size = $uploadData['file_size'];

					$this->load->library('PHPExcel/PHPExcel');
					$xpath = APPPATH.'../temp/excel/';
					$excelfile = $xpath . $file;

					// DELETE ALL RECORD
					$where = "fs_kd_dept = '".trim($dept)."'";
					$this->db->where($where);
					$this->db->delete('tx_tempexcel');

					$spreadsheet = PHPExcel_IOFactory::load($excelfile);
					$worksheet = $spreadsheet->getActiveSheet();
					$rows = array();

					foreach ($worksheet->getRowIterator(2, null) AS $row) {
						$cellIterator = $row->getCellIterator();
						$cellIterator->setIterateOnlyExistingCells(FALSE);
						$cells = array();

						foreach ($cellIterator as $cell) {
							$cells[] = $cell->getValue();
						}

						$rows = $cells;
						$kdproduk = substr($rows['1'], 0, 6);
						$xkdproduk = substr($kdproduk, 0, 2) .' '. substr($kdproduk, 2, 3) .' '. substr($kdproduk, 5, 6);
						$xwarna = substr($rows['1'], 13, 17);

						$data = array(
							'fs_kd_product' => trim($xkdproduk),
							'fs_kd_warna' => trim($xwarna),
							'fs_rangka' => trim($rows['2']),
							'fs_machine' => trim($rows['3']),
							'fs_kd_dept' => trim($dept),
							'fs_kd_wh' => trim($kdWH),
							'fs_nm_wh' => trim($nmWH),
							'fs_thn' => trim($thn)
						);
						$this->db->insert('tx_tempexcel', $data);
					}
					// delete file EXCEL after import
					unlink($excelfile);
					$response = array(
						'success' => true, 
						'data' => array('name' => $file),
						'msg' => 'File Uploaded Successfully.'
					);
					echo json_encode($response);
				} else {
					$response = array(
						'success' => false, 
						'msg' => $this->upload->display_errors()
					);
					echo json_encode($response);
				}
			}
		}
	}


}