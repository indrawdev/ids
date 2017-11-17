<?php

class CBReceive extends CI_Controller
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
			$this->load->view('vcbreceive');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function cb_trs()
	{
		$strx = trim($this->input->post('fs_kd_strx'));
		$cari = trim($this->input->post('fs_cari'));
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->cbtrs_type2_all($strx,$cari);
		
		echo json_encode($ssql->result());
	}
	
	function refno()
	{
		$lstrx = strlen(trim($this->input->post('fs_kd_strx')));
		$lstrx = $lstrx - 4;
		
		$kdtrx = substr(trim($this->input->post('fs_kd_strx')), 0, $lstrx);
		$kdstrx = substr(trim($this->input->post('fs_kd_strx')), $lstrx, 4);
		
		$refno = trim($this->input->post('fs_refno'));
		$nmcust = trim($this->input->post('fs_nm_cust'));
		$nstart = trim($this->input->post('start'));
		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->cb_trs_all($kdtrx,$kdstrx,$nmcust,$refno,'0',$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->cb_trs($kdtrx,$kdstrx,$nmcust,$refno,'0',$nstart,$total,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function refnojual()
	{
		$nstart = trim($this->input->post('start'));
		
		$refno = trim($this->input->post('fs_refno'));
		$docno = trim($this->input->post('fs_docno'));
		$cust = trim($this->input->post('fs_nm_cussup'));
		$trx = 'JL';
		$cari = trim($this->input->post('fs_cari'));
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->jual_unit_all($trx,$refno,$docno,$cust,'1','0',$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->jual_unit($trx,$refno,$docno,$cust,'1','0',$nstart,$total,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function hitungsisa()
	{
		$refnojual = trim($this->input->post('fs_refno_jual'));
		$jmlsisa = 0;
		
		$this->load->model('mCBReceive','',true);
		$ssql = $this->mCBReceive->hitungsisa($refnojual);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$jmlsisa = trim($ssql->fn_sisa);
		}
		
		$hasil = array(
			'sukses'	=> true,
			'jmlsisa'	=> $jmlsisa
		);
		echo json_encode($hasil);
	}
	
	function dp_trs()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdcust = trim($this->input->post('fs_kd_cust'));
		$nmcust = trim($this->input->post('fs_docno'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->dp_trs_all($kdcust);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->dp_trs($kdcust,$nstart,$total);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function ceksave()
	{
		$refno = trim($this->input->post('fs_refno'));
		$total = trim($this->input->post('fn_total'));
		
		$lstrx = strlen(trim($this->input->post('fs_kd_strx')));
		$lstrx = $lstrx - 4;
		
		$kdtrx = substr(trim($this->input->post('fs_kd_strx')), 0, $lstrx);
		$kdstrx = substr(trim($this->input->post('fs_kd_strx')), $lstrx, 4);
		
		if (trim($total) <= 0)
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Amount values is less than or equal to zero!!</br>Please fill in advance!!'
			);
			echo json_encode($hasil);
		}
		else
		{
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
				$this->load->model('mCBReceive','',true);
				$ssql = $this->mCBReceive->cek_kode($kdtrx,$kdstrx,$refno);
				
				if ($ssql->num_rows() > 0)
				{
					$ssql = $ssql->row();
					$kdedit = $ssql->fb_edit;
					
					if (trim($kdedit) == 0)
					{
						$hasil = array(
							'sukses'	=> false,
							'hasil'		=> 'Saving Failed, transaction can not be edit!!</br>Please contact your administrator first to unlock transaction!!'
						);
						echo json_encode($hasil);
						return;
					}
					
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
	}
	
	function save()
	{
		$lstrx = strlen(trim($this->input->post('fs_kd_strx')));
		$lstrx = $lstrx - 4;
		
		$kdtrx = substr(trim($this->input->post('fs_kd_strx')), 0, $lstrx);
		$kdstrx = substr(trim($this->input->post('fs_kd_strx')), $lstrx, 4);
		
		$refno = trim($this->input->post('fs_refno'));
		$tglrefno = trim($this->input->post('fd_refno'));
		$refnojual = trim($this->input->post('fs_refno_jual'));
		$kdcust = trim($this->input->post('fs_kd_cust'));
		$kdcustcount = trim($this->input->post('fs_cust_count'));
		$nmcust = trim($this->input->post('fs_nm_cust'));
		
		$ket = trim($this->input->post('fs_ket'));
		$total = trim($this->input->post('fn_total'));
		$kdacno = '';
		
		$kddraft = '0';
		$kdtrsta = '7';
		$kdstatus = '11';
		
		//get kas usaha
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->getkasusaha();
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$kdacno = $ssql->fs_kd_acno;
		}
		//eof get kas usaha
		
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
		$this->load->model('mCBReceive','',true);
		$ssql = $this->mCBReceive->cek_kode($kdtrx,$kdstrx,$refno);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$refno = $ssql->fs_refno;
			$xupdate = true;
		}
		else
		{
			//generate refno
			$this->load->model('mMainModul','',true);
			$ssql = $this->mMainModul->get_refno($this->session->userdata('gDept'),$this->session->userdata('gCount'),$kdtrx,$kdstrx,$tglrefno);
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
			'fs_kd_comp'		=> trim($this->session->userdata('gComp')),
			'fs_kd_dept'		=> trim($this->session->userdata('gDept')),
			'fs_count'			=> trim($this->session->userdata('gCount')),
			'fs_kd_trx'			=> trim($kdtrx),
			'fs_kd_strx'		=> trim($kdstrx),
			
			'fs_refno'			=> trim($refno),
			'fd_refno'			=> trim($tglrefno),
			'fs_docno'			=> trim($nmcust),
			'fd_docno'			=> trim($tglrefno),
			'fs_acno'			=> trim($kdacno),
			
			'fd_periode'		=> trim($periode),
			'fn_amount'			=> trim($total),
			'fs_note'			=> trim($ket),
			'fb_delete'			=> '0',
			'fb_draft'			=> trim($kddraft),
			
			'fs_trsta'			=> trim($kdtrsta),
			'fs_status'			=> trim($kdstatus),
			'fs_kd_cussup'		=> trim($kdcust),
			'fs_countcussup'	=> trim($kdcustcount)
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
				
				$this->db->insert('tx_cbheader', $data);
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
						AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
						AND	fs_count = '".trim($this->session->userdata('gCount'))."'
						AND	fs_kd_trx = '".trim($kdtrx)."'
						AND	fs_kd_strx = '".trim($kdstrx)."'
						AND	fs_refno = '".trim($refno)."'";
			
			$this->db->where($where);
			$this->db->update('tx_cbheader', $data);
		}
        
		$this->load->model('mCBReceive','',true);
		$this->mCBReceive->update_trs($kdtrx,$kdstrx,$kdtrsta,$periode,$refno);
		
		
		//simpan detil
		$data = array(
			'fs_kd_comp'		=> trim($this->session->userdata('gComp')),
			'fs_kd_dept'		=> trim($this->session->userdata('gDept')),
			'fs_count'			=> trim($this->session->userdata('gCount')),
			'fs_kd_trx'			=> trim($kdtrx),
			'fs_kd_strx'		=> trim($kdstrx),
			
			'fd_refno'			=> trim($tglrefno),
			'fs_refno'			=> trim($refno),
			'fs_seqno'			=> '000001',
			'fn_trxamtt'		=> trim($total),
			'fs_kd_refnot'		=> trim($refnojual),
			
			'fs_kd_cussup'		=> trim($kdcust),
			'fs_countcussup'	=> trim($kdcustcount),
			'fs_trsta'			=> trim($kdtrsta),
			
			'fs_usrcrt'			=> trim($this->session->userdata('gUser')),
			'fd_usrcrt'			=> trim(date('Y-m-d H:i:s')),
			'fs_upddt'			=> trim($this->session->userdata('gUser')),
			'fd_upddt'			=> trim(date('Y-m-d H:i:s'))
		);
		$this->db->insert('tx_cbdetail', $data);
		
		$this->load->model('mCBReceive','',true);
		$this->mCBReceive->update_trs2($kdtrx,$kdstrx,$kdtrsta,$periode,$refno,$refnojual);
		//eof simpan detil
		
		
		if ($xupdate == false)
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Success',
				'refno'		=> trim($refno)
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Update Success',
				'refno'		=> trim($refno)
			);
			echo json_encode($hasil);
		}
	}
	
	function cekremove()
	{
		$refno = trim($this->input->post('fs_refno'));
		
		$lstrx = strlen(trim($this->input->post('fs_kd_strx')));
		$lstrx = $lstrx - 4;
		
		$kdtrx = substr(trim($this->input->post('fs_kd_strx')), 0, $lstrx);
		$kdstrx = substr(trim($this->input->post('fs_kd_strx')), $lstrx, 4);
		
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
			$this->load->model('mCBReceive','',true);
			$ssql = $this->mCBReceive->cek_kode($kdtrx,$kdstrx,$refno);
			
			if ($ssql->num_rows() > 0)
			{
				$ssql = $ssql->row();
				$kdedit = $ssql->fb_edit;
				
				if (trim($kdedit) == 0)
				{
					$hasil = array(
						'sukses'	=> false,
						'hasil'		=> 'Remove Failed, transaction can not be remove!!</br>Please contact your administrator first to unlock transaction!!'
					);
					echo json_encode($hasil);
					return;
				}
				
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
		$refno = trim($this->input->post('fs_refno'));
		
		$lstrx = strlen(trim($this->input->post('fs_kd_strx')));
		$lstrx = $lstrx - 4;
		
		$kdtrx = substr(trim($this->input->post('fs_kd_strx')), 0, $lstrx);
		$kdstrx = substr(trim($this->input->post('fs_kd_strx')), $lstrx, 4);
		
		$this->load->model('mCBReceive','',true);
		$this->mCBReceive->hapus_trs($kdtrx,$kdstrx,$refno);
		
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	fs_kd_trx = '".trim($kdtrx)."'
					AND	fs_kd_strx = '".trim($kdstrx)."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actheaderdt');
		
		
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	fs_kd_trx = '".trim($kdtrx)."'
					AND	fs_kd_strx = '".trim($kdstrx)."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_cbdetail');
		
		
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	fs_kd_trx = '".trim($kdtrx)."'
					AND	fs_kd_strx = '".trim($kdstrx)."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actdetail');
		
		$hasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Remove reference number: "'.$refno.'" success'
		);
		echo json_encode($hasil);
	}
	
	function dp_ceksave()
	{
		$refno = trim($this->input->post('fs_refno'));
		$dp = trim($this->input->post('fn_dp'));
		$refnojual = trim($this->input->post('fs_refno_jual'));
		$kdtrx = 'JL';
		
		$this->load->model('mJual','',true);
		$ssql = $this->mJual->cek_refno($kdtrx,$refnojual);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$kdedit = $ssql->fb_edit;
			
			if (trim($kdedit) == 0)
			{
				$hasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Saving Failed, transaction can not be edit!!</br>Please contact your administrator first to unlock transaction!!'
				);
				echo json_encode($hasil);
				return;
			}
		}
		
		if (trim($dp) <= 0)
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'DP values is less than or equal to zero!!</br>Please fill in advance!!'
			);
			echo json_encode($hasil);
		}
		else
		{
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
				$this->load->model('mJual','',true);
				$ssql = $this->mJual->cek_kodedp($refno);
				
				if ($ssql->num_rows() > 0)
				{
					$hasil = array(
						'sukses'	=> true,
						'hasil'		=> 'DP already exists, do you want to update it?'
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
	}
	
	function dp_save()
	{
		$kdtrx = '5300';
		$kdstrx = '0100';
		
		$lcust = strlen(trim($this->input->post('fs_kd_cust')));
		$lcust = $lcust - 6;
		
		$kdcust = substr(trim($this->input->post('fs_kd_cust')), 0, $lcust);
		$kdcustcount = substr(trim($this->input->post('fs_kd_cust')), $lcust, 6);
		$nmcust = trim($this->input->post('fs_nm_cust'));
		
		$refno = trim($this->input->post('fs_refno'));
		$tglrefno = trim($this->input->post('fd_refno'));
		$note = trim($this->input->post('fs_note'));
		$nilaidp = trim($this->input->post('fn_dp'));
		$kdacno = '';
		
		$kddraft = '0';
		$kdtrsta = '7';
		$kdstatus = '11';
		
		//get kas usaha
		$this->load->model('mMainModul','',true);
		$ssql = $this->mMainModul->getkasusaha();
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$kdacno = $ssql->fs_kd_acno;
		}
		//eof get kas usaha
		
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
		$this->load->model('mJual','',true);
		$ssql = $this->mJual->cek_kodedp($refno);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$refno = $ssql->fs_refno;
			$xupdate = true;
		}
		else
		{
			//generate refno
			$this->load->model('mMainModul','',true);
			$ssql = $this->mMainModul->get_refno($this->session->userdata('gDept'),$this->session->userdata('gCount'),$kdtrx,$kdstrx,$tglrefno);
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
			'fs_kd_comp'		=> trim($this->session->userdata('gComp')),
			'fs_kd_dept'		=> trim($this->session->userdata('gDept')),
			'fs_count'			=> trim($this->session->userdata('gCount')),
			'fs_kd_trx'			=> trim($kdtrx),
			'fs_kd_strx'		=> trim($kdstrx),
			
			'fs_refno'			=> trim($refno),
			'fd_refno'			=> trim($tglrefno),
			'fs_docno'			=> trim($nmcust),
			'fd_docno'			=> trim($tglrefno),
			'fs_acno'			=> trim($kdacno),
			
			'fd_periode'		=> trim($periode),
			'fn_amount'			=> trim($nilaidp),
			'fs_note'			=> trim($note),
			'fb_delete'			=> '0',
			'fb_draft'			=> trim($kddraft),
			
			'fs_trsta'			=> trim($kdtrsta),
			'fs_status'			=> trim($kdstatus),
			'fs_kd_cussup'		=> trim($kdcust),
			'fs_countcussup'	=> trim($kdcustcount)
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
				
				$this->db->insert('tx_cbheader', $data);
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
						AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
						AND	fs_count = '".trim($this->session->userdata('gCount'))."'
						AND	fs_kd_trx = '".trim($kdtrx)."'
						AND	fs_kd_strx = '".trim($kdstrx)."'
						AND	fs_refno = '".trim($refno)."'";
			
			$this->db->where($where);
			$this->db->update('tx_cbheader', $data);
		}
        
		$this->load->model('mCBReceive','',true);
		$this->mCBReceive->update_trs($kdtrx,$kdstrx,$kdtrsta,$periode,$refno);
		
		if ($xupdate == false)
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving DP Success',
				'refnodp'	=> trim($refno),
				'kdcust'	=> trim($kdcust).trim($kdcustcount),
				'nmcust'	=> trim($nmcust),
				'nilaidp'	=> trim($nilaidp)
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Update DP Success',
				'refnodp'	=> trim($refno),
				'kdcust'	=> trim($kdcust).trim($kdcustcount),
				'nmcust'	=> trim($nmcust),
				'nilaidp'	=> trim($nilaidp)
			);
			echo json_encode($hasil);
		}
	}
	
	function dp_cekremove()
	{
		$refno = trim($this->input->post('fs_refno'));
		$refnojual = trim($this->input->post('fs_refno_jual'));
		$kdtrx = 'JL';
		
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
			$this->load->model('mJual','',true);
			$ssql = $this->mJual->cek_refno($kdtrx,$refnojual);
			
			if ($ssql->num_rows() > 0)
			{
				$ssql = $ssql->row();
				$kdedit = $ssql->fb_edit;
				
				if (trim($kdedit) == 0)
				{
					$hasil = array(
						'sukses'	=> false,
						'hasil'		=> 'Remove Failed, transaction can not be remove!!</br>Please contact your administrator first to unlock transaction!!'
					);
					echo json_encode($hasil);
					return;
				}
			}
			
			$this->load->model('mJual','',true);
			$ssql = $this->mJual->cek_kodedp($refno);
			
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
	
	function dp_remove()
	{
		$refno = trim($this->input->post('fs_refno'));
		
		$kdtrx = '5300';
		$kdstrx = '0100';
		
		$this->load->model('mCBReceive','',true);
		$this->mCBReceive->hapus_trs($kdtrx,$kdstrx,$refno);
		
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	fs_kd_trx = '".trim($kdtrx)."'
					AND	fs_kd_strx = '".trim($kdstrx)."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actheaderdt');
		
		
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	fs_kd_trx = '".trim($kdtrx)."'
					AND	fs_kd_strx = '".trim($kdstrx)."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_cbdetail');
		
		
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	fs_kd_trx = '".trim($kdtrx)."'
					AND	fs_kd_strx = '".trim($kdstrx)."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actdetail');
		
		$hasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Remove DP reference number: "'.$refno.'" success'
		);
		echo json_encode($hasil);
	}
	
	function printkwitansi()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tkwitansitunai';
		$nmfile = 'kwitansitunai-'.$jamskg;
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
		
		$oExcel->getActiveSheet()->setTitle('Kwitansi');
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
		
		$trx = '5000';
		$strx = '0100';
		$refno = trim($this->input->post('fs_refno'));
		$cust = trim($this->input->post('fs_refno'));
		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->cb_trs_all($trx,$strx,$cust,$refno,'0',$cari);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			
			$Refno = $ssql->fs_refno;
			$NmCust = $ssql->fs_nm_cust;
			$Total = $ssql->fn_total;
			$Ket = $ssql->fs_ket;
			
			$this->load->model('mMainModul','',true);
			$say = $this->mMainModul->terbilang($Total,4);
			
			$UserName = trim($this->session->userdata('gUser'));
			
			$pjghal = 57;
			$i = 1;
			$l = 0; // no urut
			
			$k = 1; // pjg hal
			$p = 1; // awal hal
			
			for($z = 1; $z <= 3; $z++) {
				$i = $i + 2;
				$k = $k + 2;	
				$sel->setCellValue('A'.$i, 'KWITANSI');
				$sel->mergeCells('A'.$i.':H'.$i);
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
				$sel->getStyle('A'.$i)->getFont()->setBold(true);
				$sel->getStyle('A'.$i)->getFont()->setSize(12);
				$i++;
				$k++;
				
				$sel->mergeCells('A'.$i.':H'.$i);
				$sel->setCellValue('A'.$i, $Refno);
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$i = $i + 5;
				$k = $k + 5;
				
				$sel->setCellValue('A'.$i, 'Telah diterima dari');
				$sel->setCellValue('B'.$i, ':');
				$sel->setCellValue('C'.$i, $NmCust);
				$sel->mergeCells('C'.$i.':H'.$i);
				$i++;
				$k++;
				
				$sel->setCellValue('A'.$i, 'Uang sejumlah');
				$sel->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				$sel->getStyle('A'.$i)->getAlignment()->setWrapText(true);
				$j = $i + 1;
				$sel->mergeCells('A'.$i.':A'.$j);
				
				$sel->setCellValue('B'.$i, ':');
				$sel->getStyle('B'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				$sel->getStyle('B'.$i)->getAlignment()->setWrapText(true);
				$j = $i + 1;
				$sel->mergeCells('B'.$i.':B'.$j);
				
				$sel->setCellValue('C'.$i, $say);
				$sel->getStyle('C'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				$sel->getStyle('C'.$i)->getAlignment()->setWrapText(true);
				$j = $i + 1;
				$sel->mergeCells('C'.$i.':H'.$j);
				$i = $i + 2;
				$k = $k + 2;
				
				$sel->setCellValue('A'.$i, 'Untuk keperluan');
				$sel->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				$sel->getStyle('A'.$i)->getAlignment()->setWrapText(true);
				$j = $i + 4;
				$sel->mergeCells('A'.$i.':A'.$j);
				
				$sel->setCellValue('B'.$i, ':');
				$sel->getStyle('B'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				$sel->getStyle('B'.$i)->getAlignment()->setWrapText(true);
				$j = $i + 4;
				$sel->mergeCells('B'.$i.':B'.$j);
				
				$sel->setCellValue('C'.$i, $Ket);
				$sel->getStyle('C'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				$sel->getStyle('C'.$i)->getAlignment()->setWrapText(true);
				$j = $i + 4;
				$sel->mergeCells('C'.$i.':H'.$j);
				$i = $i + 5;
				$k = $k + 5;
				
				
				$sel->setCellValue('A'.$i,'# Rp '.number_format($Total).',- #');
				$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sel->mergeCells('A'.$i.':C'.$i);
				$sel->getStyle('A'.$i.':C'.$i)->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('argb' => 'H0C0C0C0')
						)
					)
				);
				$i = $i + 5;
				$k = $k + 5;
				
				$this->load->model('mMainModul','',true);
				$xTglPrint = date('d-m-Y');
				$xTglPrint = substr($xTglPrint,0,2).' '.$this->mMainModul->ambilbulan(substr($xTglPrint,3,2)).' '.substr($xTglPrint,6,4);
				$sel->setCellValue('E'.$i,trim($this->session->userdata('gKota')).', '.trim($xTglPrint));
				$sel->mergeCells('E'.$i.':H'.$i);
				$i++;
				$k++;
				
				$sel->setCellValue('E'.$i,'Diterima Oleh');
				$sel->mergeCells('E'.$i.':H'.$i);
				$i = $i + 35;
				$k = $k + 35;			
					
				$sel->setCellValue('E'.$i,'...............');	
				$sel->mergeCells('E'.$i.':F'.$i);
				$i = $i + 2;
				$k = $k + 2;
				$sel->getStyle('A'.$i.':H'.$i)->applyFromArray(
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
				'nmfile'	=> $xPath.$nmfile.'.pdf',
				'nmfilexls'	=> $xPath.$nmfile.'.xls'
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