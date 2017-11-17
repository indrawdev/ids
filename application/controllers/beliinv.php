<?php

class BeliInv extends CI_Controller
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
			$this->load->view('vbeliinv');
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
		$docno = '';
		$invno = trim($this->input->post('fs_invno'));
		$trx = 'BL';
		
		$cari = trim($this->input->post('fs_cari'));

		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->beli_unit_all($trx,$refno,$docno,$invno,'1',$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->beli_unit($trx,$refno,$docno,$invno,'1',$nstart,$total,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function grid_do()
	{
		$refno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mBeliInv','',true);
		$ssql = $this->mBeliInv->grid_do($refno);
		
		echo json_encode($ssql->result());
	}
	
	function grid_prod()
	{
		$refno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mBeliInv','',true);
		$ssql = $this->mBeliInv->grid_prod($refno);
		
		echo json_encode($ssql->result());
	}
	
	function grid_prod2()
	{
		$refno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mBeliInv','',true);
		$ssql = $this->mBeliInv->grid_prod2($refno);
		
		echo json_encode($ssql->result());
	}
	
	function grid_reg()
	{
		$refno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mBeliInv','',true);
		$ssql = $this->mBeliInv->grid_reg($refno);
		
		echo json_encode($ssql->result());
	}
	
	function grid_reg2()
	{
		$refno = trim($this->input->post('fs_refno'));
		
		$this->load->model('mBeliInv','',true);
		$ssql = $this->mBeliInv->grid_reg2($refno);
		
		echo json_encode($ssql->result());
	}
	
	function ceksave()
	{
		$refno = trim($this->input->post('fs_refno'));
		$kdtrx = 'BL';
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		$ppn = trim($this->input->post('fn_ppn'));
		
		if (trim($ppn) <= 0)
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Canceled, Total PPN less than or equal to zero (0)!!'
			);
			echo json_encode($hasil);
			return;
		}
		
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
			$ssql = $this->mBeli->cek_refno($kdtrx,$refno);
			
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
		$invno = trim($this->input->post('fs_invno'));
		
		$invnodt = trim($this->input->post('fd_invno'));
		$kdsupp = trim($this->input->post('fs_kd_sup'));
		$count = trim($this->input->post('fs_count'));
		$kdterm = trim($this->input->post('fs_kd_term'));
		$kdbelimtd = trim($this->input->post('fs_kd_belimtd'));
		
		$nmbelimtd = trim($this->input->post('fs_nm_belimtd'));
		$sumtotalharga = trim($this->input->post('fn_sumtotal_harga'));
		$sumtotaldisc = trim($this->input->post('fn_sumtotal_disc'));
		$sumdisc = '0';
		$total = trim($this->input->post('fn_total'));
		
		$ppn = trim($this->input->post('fn_ppn'));
		$kdtax = trim($this->input->post('fs_kd_tax'));
		$nmtax = trim($this->input->post('fs_nm_tax'));
		$taxpersen = trim($this->input->post('fn_taxpersen'));
		$taxtotal = trim($this->input->post('fn_taxtotal'));
		
		$gtotal = trim($this->input->post('fn_gtotal'));
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
		$ssql = $this->mBeli->cek_refno($kdtrx,$refno);
		
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
		
		//hapus tx_actheaderdt
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	fs_kd_trx = '".trim($kdtrx)."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actheaderdt');
		//eof hapus tx_actheaderdt
		
		$dt = array(
			'fs_kd_comp'		=> trim($this->session->userdata('gComp')),
			'fs_kd_dept'		=> trim($this->session->userdata('gDept')),
			'fs_count'			=> trim($this->session->userdata('gCount')),
			'fs_kd_trx'			=> trim($kdtrx),
			'fs_refno'			=> trim($refno),
			
			'fd_refno'			=> trim($refnodt),
			'fd_periode'		=> trim($periode),
			'fs_kd_cussup'		=> trim($kdsupp),
			'fs_countcussup'	=> trim($count),
			'fs_docno'			=> trim($docno),
			
			'fd_docno'			=> trim($docnodt),
			'fs_kd_term'		=> trim($kdterm),
			'fn_grsamt'			=> trim($sumtotalharga),
			'fn_dscamt'			=> trim($sumtotaldisc),
			'fn_sdscamt'		=> trim($sumdisc),
			
			'fn_netbframt'		=> trim($total),
			'fn_taxamt'			=> trim($ppn),
			'fs_kd_otax'		=> trim($kdtax),
			'fs_nm_otax'		=> trim($nmtax),
			'fn_otaxpct'		=> trim($taxpersen),
			
			'fn_otaxamt'		=> trim($taxtotal),
			'fn_netaftramt'		=> trim($gtotal),
			'fn_addonamt'		=> '0',
			'fn_deducamt'		=> '0',
			'fn_trxamt'			=> trim($gtotal),
			
			'fn_rmnamt'			=> trim($gtotal),
			'fn_bbn'			=> '0',
			'fn_subsidi'		=> '0',
			'fb_spearpart'		=> trim($this->session->userdata('gSparePart')),
			'fn_installment'	=> '0',
			
			'fs_kd_payment'		=> '',
			'fs_nm_payment'		=> '',
			'fs_acno'			=> '',
			'fs_kd_wh'			=> '',
			'fs_nm_wh'			=> '',
			
			'fs_kd_salesman'	=> '',
			'fs_nm_salesman'	=> '',
			'fs_kd_salesmtd'	=> trim($kdbelimtd),
			'fs_nm_salesmtd'	=> trim($nmbelimtd),
			
			'fs_kd_dp'			=> '',
			'fs_nm_dp'			=> '',
			'fn_dpamt'			=> '0',
			'fs_invno'			=> trim($invno),
			'fd_invno'			=> trim($invnodt)
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
		
		//hapus detail
		$this->load->model('mBeliInv','',true);
		$ssql = $this->mBeliInv->reset_hpp($refno);
		
		
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_posdetail');
		//eof hapus detail
		
		//simpan detail
		$kddo = explode('|', trim($this->input->post('fs_kd_do')));
		$kdprod = explode('|', trim($this->input->post('fs_kd_product')));
		$nmprod = explode('|', trim($this->input->post('fs_nm_product')));
		$qty = explode('|', trim($this->input->post('fn_qty')));
		$kdunit = explode('|', trim($this->input->post('fs_kd_unit')));
		
		$kdwarna = explode('|', trim($this->input->post('fs_kd_warna')));
		$harga = explode('|', trim($this->input->post('fn_harga')));
		$disc = explode('|', trim($this->input->post('fn_disc')));
		
		$jml = count($kdprod) - 1;
		if ($jml != 0)
		{
			for ($i=1; $i<=$jml; $i++)
			{
				$jmlharga = (float)(round($qty[$i] * $harga[$i], 6));
				$jmldiskon = (float)(round($qty[$i] * $disc[$i], 6));
				$diskon = (float)(round($qty[$i] * $harga[$i] / $sumtotalharga * $sumdisc, 6));
				
				$data = array(
					'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
					'fs_kd_dept'	=> trim($this->session->userdata('gDept')),
					'fs_count'		=> trim($this->session->userdata('gCount')),
					'fs_kd_trx'		=> trim($kdtrx),
					'fs_refno'		=> trim($refno),
					
					'fd_refno'		=> trim($refnodt),
					'fd_periode'	=> trim($periode),
					'fs_seqno'		=> trim(sprintf("%06d",$i)),
					'fs_kd_product'	=> trim($kdprod[$i]),
					'fs_nm_product'	=> trim($nmprod[$i]),
					
					'fn_qtytr'		=> trim($qty[$i]),
					'fn_qtytr1'		=> trim($qty[$i]),
					'fs_unitbill'	=> trim($kdunit[$i]),
					'fn_unitprc'	=> trim($harga[$i]),
					'fn_dscprc'		=> trim($disc[$i]),
					
					'fn_lineamt'	=> $jmlharga,
					'fn_ldscamt'	=> $jmldiskon,
					'fn_sdscamt'	=> $diskon,
					'fn_addonamt'	=> '0',
					'fn_deducamt'	=> '0',
					
					'fn_ftrxamt'	=> (float)(round($jmlharga - $jmldiskon - $diskon, 6)),
					'fs_kd_wh'		=> '',
					'fs_countwh'	=> '',
					'fs_kd_bin'		=> '',
					'fs_luxcd'		=> '',
					
					'fs_luxnm'		=> '',
					'fn_luxpct'		=> '0',
					'fn_luxamt'		=> '0',
					'fs_kd_ppnbm'	=> '',
					'fs_nm_ppnbm'	=> '',
					
					'fn_ppnbm'		=> '0',
					'fn_ppnbmpct'	=> '0',
					'fs_kd_order'	=> trim($kddo[$i]),
					'fs_kd_warna'	=> trim($kdwarna[$i]),
					
					'fs_usrcrt'		=> trim($this->session->userdata('gUser')),
					'fd_usrcrt'		=> trim(date('Y-m-d H:i:s')),
					'fs_upddt'		=> trim($this->session->userdata('gUser')),
					'fd_upddt'		=> trim(date('Y-m-d H:i:s'))
				);
				
				$this->db->insert('tx_posdetail', $data);
				
				
				$data = array(
					'fn_hpp'	=> trim($harga[$i])
				);
				$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
							AND	fs_refno = '".trim($kddo[$i])."'
							AND	fs_kd_product = '".trim($kdprod[$i])."'
							AND	fs_kd_warna = '".trim($kdwarna[$i])."'";
				
				$this->db->where($where);
				$this->db->update('tm_icregister', $data);
			}
		}
		//eof simpan detail
		
		
		$this->load->model('mBeli','',true);
		$ssql = $this->mBeli->pos_beli($refno,$kdtrx,$kdbelimtd);
		
		if ($xupdate == false)
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Invoice Success',
				'refno'		=> $refno
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Invoice Update Success',
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
			$ssql = $this->mBeli->cek_refno('BL',$refno);
			
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
		$this->load->model('mBeliInv','',true);
		$ssql = $this->mBeliInv->reset_hpp($refno);
		
		
		//hapus tx_actheaderdt
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actheaderdt');
		//eof hapus tx_actheaderdt
		
		//hapus tx_actheader
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actheader');
		//eof hapus tx_actheader
		
		//hapus tx_actdetail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actdetail');
		//eof hapus tx_actdetail
		
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
		
		//eof hapus beli
		
		$hasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Remove invoice reference number: "'.$refno.'" success'
		);
		echo json_encode($hasil);
	}
}?>