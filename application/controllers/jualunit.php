<?php

class JualUnit extends CI_Controller
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
		$this->load->view('vjualunit');
	}
	
	function color()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdwarna = trim($this->input->post('fs_kd_vareable'));
		$nmwarna = trim($this->input->post('fs_nm_vareable'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->warna_all($kdwarna,$nmwarna);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->warna($kdwarna,$nmwarna,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function cust_kode()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdcust = trim($this->input->post('fs_code'));
		$nmcust = trim($this->input->post('fs_nm_code'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->cust_jual_all($kdcust,$nmcust);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->cust_jual($kdcust,$nmcust,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function cust_ceksave()
	{
		$kddept = trim($this->session->userdata('gDept'));
		$ldept = strlen($kddept);
		$ldept = $ldept - 2;
		$kdcount = trim($this->session->userdata('gCount'));
		$lcount = strlen($kdcount);
		$lcount = $lcount - 2;
		$kdcust = substr(trim($kddept), $ldept, 2).substr(trim($kdcount), $lcount, 2);
		$kdcount = '000001';
		
		$this->load->model('mMainModul','',true);
		$nmcust = $this->mMainModul->clean_input(trim($this->input->post('fs_nm_cust')));
		$this->load->model('mMainModul','',true);
		$almt = $this->mMainModul->clean_input(trim($this->input->post('fs_addr')));
		$this->load->model('mMainModul','',true);
		$tlp = $this->mMainModul->clean_input(trim($this->input->post('fs_tlp')));
		$kdid = trim($this->input->post('fs_kd_id'));
		
		if (trim($nmcust) == '' or trim($kdid) == '' or trim($almt) == '' or trim($tlp) == '')
		{
			$hasil = array('sukses' => false, 'hasil' => 'One of the names, id card, address, or phone is still empty!! <br>
						Please fill in advance!!');
			echo json_encode($hasil);
		}
		else
		{
			$kdid = str_ireplace('.','',$kdid);
			$kdid = str_ireplace('/','',$kdid);
			$kdid = str_ireplace('-','',$kdid);
			$kdid = str_ireplace('*','',$kdid);
			$kdid = str_ireplace(' ','',$kdid);
			
			$this->load->model('mCust','',true);
			$ssql = $this->mCust->cek_idcard($kdid);
			
			if ($ssql->num_rows() > 0)
			{
				$hasil = array('sukses' => true, 'hasil' => 'Customer already exists, do you want to update it?');
				echo json_encode($hasil);
			}
			else
			{
				$this->load->model('mCust','',true);
				$ssql = $this->mCust->cek_nama($nmcust,$almt);
				
				if ($ssql->num_rows() > 0)
				{
					$hasil = array('sukses' => true, 'hasil' => 'Customer already exists, do you want to update it?');
					echo json_encode($hasil);
				}
				else
				{
					$hasil = array('sukses' => true, 'hasil' => 'lanjut');
					echo json_encode($hasil);
				}
			}
		}
	}
	
	function cust_save()
	{
		$kddept = trim($this->session->userdata('gDept'));
		$ldept = strlen($kddept);
		$ldept = $ldept - 2;
		$kdcount = trim($this->session->userdata('gCount'));
		$lcount = strlen($kdcount);
		$lcount = $lcount - 2;
		$kdcust = substr(trim($kddept), $ldept, 2).substr(trim($kdcount), $lcount, 2);
		$kdcount = '000001';
		
		$this->load->model('mMainModul','',true);
		$nmcust = $this->mMainModul->clean_input(trim($this->input->post('fs_nm_cust')));
		$this->load->model('mMainModul','',true);
		$almt = $this->mMainModul->clean_input(trim($this->input->post('fs_addr')));
		$this->load->model('mMainModul','',true);
		$tlp = $this->mMainModul->clean_input(trim($this->input->post('fs_tlp')));
		
		$kdid = trim($this->input->post('fs_kd_id'));
		$kdid = str_ireplace('.','',$kdid);
		$kdid = str_ireplace('/','',$kdid);
		$kdid = str_ireplace('-','',$kdid);
		$kdid = str_ireplace('*','',$kdid);
		$kdid = str_ireplace(' ','',$kdid);
		
		$this->load->model('mCust','',true);
		$ssql = $this->mCust->cek_idcard($kdid);
		
		$xupdate = false;
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$kdcust = $ssql->fs_code;
			$kdcount = $ssql->fs_count;
			$almt = $ssql->fs_addr;
			
			$xupdate = true;
		}
		else
		{
			$this->load->model('mCust','',true);
			$ssql = $this->mCust->cek_nama($nmcust,$almt);
			
			if ($ssql->num_rows() > 0)
			{
				$ssql = $ssql->row();
				$kdcust = $ssql->fs_code;
				$kdcount = $ssql->fs_count;
				$almt = $ssql->fs_addr;
				
				$xupdate = true;
			}
			else
			{
				$this->load->model('mCust','',true);
				$ssql = $this->mCust->ambil_kode();
				
				if ($ssql->num_rows() > 0)
				{
					$ssql = $ssql->row();
					
					$xupdate = false;
					
					if (trim($ssql->fs_count) == 'KOSONG' or trim($ssql->fs_count) == '000000')
					{
						$kdcount = '000001';
						
						$xupdate = false;
					}
				}
			}
		}
		
		//hapus tm_customer
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_customer = '".trim($kdcust)."'
					AND	fs_count = '".trim($kdcount)."'";
		
		$this->db->where($where);
		$this->db->delete('tm_customer');
		//eof hapus tm_customer
		
		//save tm_customer
		$data = array(
			'fs_kd_comp' => trim($this->session->userdata('gComp')),
			'fs_kd_customer' => trim($kdcust),
			'fs_count' => trim($kdcount),
			'fs_kd_custtype' => '0',
			'fs_kd_currency' => 'IDR',
			'fb_active' => '1',
			
			'fs_usrcrt' => trim($this->session->userdata('gUser')),
			'fd_usrcrt' => trim(date('Y-m-d H:i:s')),
			'fs_upddt' => trim($this->session->userdata('gUser')),
			'fd_upddt' => trim(date('Y-m-d H:i:s'))
		);
		$this->db->insert('tm_customer', $data);
		//eof save tm_customer
		
		
		//save
		$dt = array(
			'fs_kd_comp' => trim($this->session->userdata('gComp')),
			'fs_cdtyp' => '02',
			'fs_code' => trim($kdcust),
			'fs_count' => trim($kdcount),
			'fs_nm_code' => trim($nmcust),
			
			'fs_idcard' => trim($kdid),
			'fs_addr' => trim($almt),
			'fs_phone1' => trim($tlp)
		);
		
		if ($xupdate == false)
		{
			if (trim($kdcust) <> '' and trim($kdcount) <> '')
			{
				$dt2 = array(
					'fs_usrcrt' => trim($this->session->userdata('gUser')),
					'fd_usrcrt' => trim(date('Y-m-d H:i:s')),
					'fs_upddt' => trim($this->session->userdata('gUser')),
					'fd_upddt' => trim(date('Y-m-d H:i:s'))
				);
				$data = array_merge($dt,$dt2);
				
				$this->db->insert('tm_addr', $data);
				
				$hasil = array('sukses' => true, 'hasil' => 'Saving Customer Success',
							'kdcust' => trim($kdcust), 'kdcount' => trim($kdcount), 'nmcust' => trim($nmcust),
							'kdid' => trim($kdid), 'almt' => trim($almt), 'tlp' => $tlp);
				echo json_encode($hasil);
			}
			else
			{
				$hasil = array('sukses' => false, 'hasil' => 'Saving Customer Failed, generate customer code failed!! <br>
						Please try again letter...');
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
						AND fs_code = '".trim($kdcust)."'
						AND	fs_count = '".trim($kdcount)."'
						AND	fs_cdtyp = '02'";
			
			$this->db->where($where);
			$this->db->update('tm_addr', $data);
			
			$hasil = array('sukses' => true, 'hasil' => 'Saving Update Customer Success',
						'kdcust' => trim($kdcust), 'kdcount' => trim($kdcount), 'nmcust' => trim($nmcust),
						'kdid' => trim($kdid), 'almt' => trim($almt), 'tlp' => $tlp);
			echo json_encode($hasil);
		}
		//eof save
	}
	
	function kodedp()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdcode = trim($this->input->post('fs_code'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->dp_all($kdcode);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->dp($kdcode,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function dp_trs()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdcust = trim($this->input->post('fs_kd_cust'));
		$nmcust = trim($this->input->post('fs_docno'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->dp_trs_all($kdcust);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->dp_trs($kdcust,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function dp_ceksave()
	{
		$refno = trim($this->input->post('fs_refno'));
		$dp = trim($this->input->post('fn_dp'));
		
		if (trim($dp) <= 0)
		{
			$hasil = array('sukses' => false, 'hasil' => 'DP values is less than or equal to zero!! <br>
						Please fill in advance!!');
			echo json_encode($hasil);
		}
		else
		{
			if (trim($refno) == '' or trim($refno) == 'AUTOMATIC')
			{
				$hasil = array('sukses' => true, 'hasil' => 'lanjut');
				echo json_encode($hasil);
			}
			else
			{
				$this->load->model('mJualUnit','',true);
				$ssql = $this->mJualUnit->cek_kodedp($refno);
				
				if ($ssql->num_rows() > 0)
				{
					$hasil = array('sukses' => true, 'hasil' => 'DP already exists, do you want to update it?');
					echo json_encode($hasil);
				}
				else
				{
					$hasil = array('sukses' => false, 'hasil' => 'Saving Failed, Reference number unknown!!');
					echo json_encode($hasil);
				}
			}
		}
	}
	
	function dp_save()
	{
		$kddept = trim($this->session->userdata('gDept'));
		$kdcount = trim($this->session->userdata('gCount'));
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
		$this->load->model('mJualUnit','',true);
		$ssql = $this->mJualUnit->cek_kodedp($refno);
		
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
		
		$this->load->model('mCBReceive','',true);
		$this->mCBReceive->update_dp($kdtrx,$kdstrx,$refno);
		
		$dt = array(
			'fs_kd_comp' => trim($this->session->userdata('gComp')),
			'fs_kd_dept' => trim($kddept),
			'fs_count' => trim($kdcount),
			'fs_kd_trx' => trim($kdtrx),
			'fs_kd_strx' => trim($kdstrx),
			
			'fs_refno' => trim($refno),
			'fd_refno' => trim($tglrefno),
			'fs_docno' => trim($nmcust),
			'fd_docno' => trim($tglrefno),
			'fs_acno' => trim($kdacno),
			
			'fd_periode' => trim($periode),
			'fn_amount' => trim($nilaidp),
			'fs_note' => trim($note),
			'fb_delete' => '0',
			'fb_draft' => trim($kddraft),
			
			'fs_trsta' => trim($kdtrsta),
			'fs_status' => trim($kdstatus),
			'fs_kd_cussup' => trim($kdcust),
			'fs_countcussup' => trim($kdcustcount),
			
		);
		
		if ($xupdate == false)
		{
			if (trim($refno) <> '')
			{
				$dt2 = array(
					'fs_usrcrt' => trim($this->session->userdata('gUser')),
					'fd_usrcrt' => trim(date('Y-m-d H:i:s')),
					'fs_upddt' => trim($this->session->userdata('gUser')),
					'fd_upddt' => trim(date('Y-m-d H:i:s'))
				);
				$data = array_merge($dt,$dt2);
				
				$this->db->insert('tx_cbheader', $data);
			}
			else
			{
				$hasil = array('sukses' => true, 'hasil' => 'Saving Failed, generate reference number failed!! <br>
						Please try again letter...', 'refno' => $refno);
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
			$this->db->update('tx_cbheader', $data);
		}
        
		$this->load->model('mCBReceive','',true);
		$this->mCBReceive->update_dp2($kdtrx,$kdstrx,$kdtrsta,$periode,$refno);
		
		if ($xupdate == false)
		{
			$hasil = array('sukses' => true, 'hasil' => 'Saving DP Success',
							'kdcust' => trim($kdcust).trim($kdcustcount), 'nmcust' => trim($nmcust),
							'nilaidp' => trim($nilaidp));
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array('sukses' => true, 'hasil' => 'Saving Update DP Success',
							'kdcust' => trim($kdcust).trim($kdcustcount), 'nmcust' => trim($nmcust),
							'nilaidp' => trim($nilaidp));
			echo json_encode($hasil);
		}
	}
	
	function dp_cekremove()
	{
		$refno = trim($this->input->post('fs_refno'));
		
		if (trim($refno) == '' or trim($refno) == 'AUTOMATIC')
		{
			$hasil = array('sukses' => false, 'hasil' => 'Remove Failed, Reference number empty!!');
			echo json_encode($hasil);
		}
		else
		{
			$refno = trim($this->input->post('fs_refno'));
			
			$this->load->model('mJualUnit','',true);
			$ssql = $this->mJualUnit->cek_kodedp($refno);
			
			if ($ssql->num_rows() > 0)
			{
				$hasil = array('sukses' => true, 'hasil' => 'Are you sure you want to delete this reference number?');
				echo json_encode($hasil);
			}
			else
			{
				$hasil = array('sukses' => false, 'hasil' => 'Remove Failed, Reference number unknown!!');
				echo json_encode($hasil);
			}
		}
	}
	
	function dp_remove()
	{
		$refno = trim($this->input->post('fs_refno'));
		$kddept = trim($this->session->userdata('gDept'));
		$kdcount = trim($this->session->userdata('gCount'));
		
		$kdtrx = '5300';
		$kdstrx = '0100';
		
		$this->load->model('mCBReceive','',true);
		$this->mCBReceive->hapus_dp($kdtrx,$kdstrx,$refno);
		
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($kddept)."'
					AND	fs_count = '".trim($kdcount)."'
					AND	fs_kd_trx = '".trim($kdtrx)."'
					AND	fs_kd_strx = '".trim($kdstrx)."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actheaderdt');
		
		
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($kddept)."'
					AND	fs_count = '".trim($kdcount)."'
					AND	fs_kd_trx = '".trim($kdtrx)."'
					AND	fs_kd_strx = '".trim($kdstrx)."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_cbdetail');
		
		
		$this->load->model('mCBReceive','',true);
		$this->mCBReceive->hapus_dp2($kdtrx,$kdstrx,$refno);
		
		
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($kddept)."'
					AND	fs_count = '".trim($kdcount)."'
					AND	fs_kd_trx = '".trim($kdtrx)."'
					AND	fs_kd_strx = '".trim($kdstrx)."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actdetail');
		
		$hasil = array('sukses' => true, 'hasil' => 'Remove DP reference number: "'.$refno.'" success');
		echo json_encode($hasil);
	}
	
	function product()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdprod = trim($this->input->post('fs_kd_product'));
		$nmprod = trim($this->input->post('fs_nm_product'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->product_all($kdprod,$nmprod);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->product($kdprod,$nmprod,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function payment()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdpay = trim($this->input->post('fs_code'));
		$nmpay = trim($this->input->post('fs_nm_code'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->payment_all($kdpay,$nmpay);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->payment($kdpay,$nmpay,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function refno()
	{
		$nstart = trim($this->input->post('start'));
		
		$refno = trim($this->input->post('fs_refno'));
		$docno = trim($this->input->post('fs_docno'));
		$cust = trim($this->input->post('fs_nm_cussup'));
		$trx = 'JL';
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->jual_unit_all($trx,$refno,$docno,$cust);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->jual_unit($trx,$refno,$docno,$cust,$nstart,$total);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function sales()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdsales = trim($this->input->post('fs_code'));
		$nmsales = trim($this->input->post('fs_nm_code'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->sales_all($kdsales,$nmsales);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->sales($kdsales,$nmsales,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function sales_mtd()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdsalesmtd = trim($this->input->post('fs_kd_vareable'));
		$nmsalesmtd = trim($this->input->post('fs_nm_vareable'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->sales_mtd_all($kdsalesmtd,$nmsalesmtd);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->sales_mtd($kdsalesmtd,$nmsalesmtd,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function survey()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdsurvey = trim($this->input->post('fs_code'));
		$nmsurvey = trim($this->input->post('fs_nm_code'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->survey_all($kdsurvey,$nmsurvey);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->survey($kdsurvey,$nmsurvey,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function term()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdterm = trim($this->input->post('fs_kd_term'));
		$nmterm = trim($this->input->post('fs_nm_term'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->term_all($kdterm,$nmterm);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->term($kdterm,$nmterm,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function wh()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdwh = trim($this->input->post('fs_code'));
		$nmwh = trim($this->input->post('fs_nm_code'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->wh_all($kdwh,$nmwh);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->wh($kdwh,$nmwh,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function rangka()
	{
		$nstart = trim($this->input->post('start'));
		
		$refno = trim($this->input->post('fs_refno'));
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		$kdprod = trim($this->input->post('fs_kd_product'));
		$wh = trim($this->input->post('fs_kd_wh'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->rangka_jual_all($refno,$rangka,$mesin,$kdprod,$wh);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->rangka_jual($refno,$rangka,$mesin,$kdprod,$wh,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function jual_ceksave()
	{
		$refno = trim($this->input->post('fs_refno'));
		$kdtrx = 'JL';//ubah jd JL klo beli udah bs simpan
		$nmcust = trim($this->input->post('fs_nm_cust'));
		$kdid = trim($this->input->post('fs_idcard'));
		$almt = trim($this->input->post('fs_addr'));
		$tlp = trim($this->input->post('fs_tlp'));
		
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		
		if (trim($nmcust) == '' or trim($kdid) == '' or trim($almt) == '' or trim($tlp) == '')
		{
			$hasil = array('sukses' => false, 'hasil' => 'One of the names, id card, address, or phone is still empty!! <br>
						Please fill in advance!!');
			echo json_encode($hasil);
			return;
		}
		
		if (trim($refno) == '' or trim($refno) == 'AUTOMATIC')
		{
			$this->load->model('mJualUnit','',true);
			$ssql = $this->mJualUnit->cek_rangka($rangka);
			if ($ssql->num_rows() > 0)
			{
				$ssql = $ssql->row();
				$refnojual = $ssql->fs_refnoinv;
				$hasil = array('sukses' => false, 'hasil' => 'Saving Failed,  <br> Chasis already exists and already sold with reference: '.trim($refnojual).' !!');
				echo json_encode($hasil);
			}
			else
			{
				$this->load->model('mJualUnit','',true);
				$ssql = $this->mJualUnit->cek_mesin($mesin);
				if ($ssql->num_rows() > 0)
				{
					$ssql = $ssql->row();
					$refnojual = $ssql->fs_refnoinv;
					$hasil = array('sukses' => false, 'hasil' => 'Saving Failed, <br> Machine already exists and already sold with reference: '.trim($refnojual).' !!');
					echo json_encode($hasil);
				}
				else
				{
					$hasil = array('sukses' => true, 'hasil' => 'lanjut');
					echo json_encode($hasil);
				}
			}
		}
		else
		{
			$this->load->model('mJualUnit','',true);
			$ssql = $this->mJualUnit->cek_refno($kdtrx,$refno);
			
			if ($ssql->num_rows() > 0)
			{
				$this->load->model('mJualUnit','',true);
				$ssql = $this->mJualUnit->cek_rangka($rangka);
				if ($ssql->num_rows() > 0)
				{
					$ssql = $ssql->row();
					$refnojual = $ssql->fs_refnoinv;
					
					if (trim($refnojual) == trim($refno))
					{
						// $hasil = array('sukses' => true, 'hasil' => 'Reference number already exists, do you want to update it?');
						// echo json_encode($hasil);
						// return;
					}
					else
					{
						$hasil = array('sukses' => false, 'hasil' => 'Saving Failed,  <br> Chasis already exists and already sold with reference: '.trim($refnojual).' !!');
						echo json_encode($hasil);
						return;
					}
				}
				else
				{
					$hasil = array('sukses' => true, 'hasil' => 'Reference number already exists, do you want to update it?');
					echo json_encode($hasil);
					return;
				}
				
				$this->load->model('mJualUnit','',true);
				$ssql = $this->mJualUnit->cek_mesin($mesin);
				if ($ssql->num_rows() > 0)
				{
					$ssql = $ssql->row();
					$refnojual = $ssql->fs_refnoinv;
					
					if (trim($refnojual) == trim($refno))
					{
						$hasil = array('sukses' => true, 'hasil' => 'Reference number already exists, do you want to update it?');
						echo json_encode($hasil);
						return;
					}
					else
					{
						$hasil = array('sukses' => false, 'hasil' => 'Saving Failed, <br> Machine already exists and already sold with reference: '.trim($refnojual).' !!');
						echo json_encode($hasil);
						return;
					}
				}
				else
				{
					$hasil = array('sukses' => true, 'hasil' => 'Reference number already exists, do you want to update it?');
					echo json_encode($hasil);
				}
			}
			else
			{
				$hasil = array('sukses' => false, 'hasil' => 'Saving Failed, Reference number unknown!!');
				echo json_encode($hasil);
			}
		}
	}
	
	function beli_save()
	{
		$kdcust = trim($this->input->post('fs_kd_cust'));
		$kdcustcount = trim($this->input->post('fs_count_cust'));
		$nmcust = trim($this->input->post('fs_nm_cust'));
		$idcard = trim($this->input->post('fs_idcard'));
		$addr = trim($this->input->post('fs_addr'));
		
		$refno = trim($this->input->post('fs_refno'));
		$refnodt = trim($this->input->post('fd_refno'));
		$docno = trim($this->input->post('fs_docno'));
		$docnodt = trim($this->input->post('fd_docno'));
		$kdsales = trim($this->input->post('fs_kd_sales'));
		
		$nmsales = trim($this->input->post('fs_nm_sales'));
		$kdsurvey = trim($this->input->post('fs_kd_survey'));
		$nmsurvey = trim($this->input->post('fs_nm_survey'));
		$kdterm = trim($this->input->post('fs_kd_term'));
		$nmterm = trim($this->input->post('fs_nm_term'));
		
		$kdprod = trim($this->input->post('fs_kd_product'));
		$nmprod = trim($this->input->post('fs_nm_product'));
		$register = trim($this->input->post('fs_register'));
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		
		$kdsalesmtd = trim($this->input->post('fs_kd_salesmtd'));
		$nmsalesmtd = trim($this->input->post('fs_nm_salesmtd'));
		$kdpay = trim($this->input->post('fs_kd_payment'));
		$nmpay = trim($this->input->post('fs_nm_payment'));
		$kdacno = trim($this->input->post('fs_kd_acno'));
		
		$nmacno = trim($this->input->post('fs_nm_acno'));
		$cc = trim($this->input->post('fn_cc'));
		$thn = trim($this->input->post('fn_thn'));
		$kdwarna = trim($this->input->post('fs_kd_color'));
		$nmwarna = trim($this->input->post('fs_nm_color'));
		
		$kdwh = trim($this->input->post('fs_kd_wh'));
		$nmwh = trim($this->input->post('fs_nm_wh'));
		$hargaunit = trim($this->input->post('fn_harga'));
		$dpmax = trim($this->input->post('fn_dpmax'));
		$disc = trim($this->input->post('fn_disc'));
		
		$kddp = trim($this->input->post('fs_kd_dp'));
		$nmdp = trim($this->input->post('fs_nm_dp'));
		$nilaidp = trim($this->input->post('fn_dp'));
		$discl = trim($this->input->post('fn_discl'));
		$install = trim($this->input->post('fn_instal'));
		
		$total = trim($this->input->post('fn_total'));
		
		$kdtrx = 'BL';
		$kdsalesmtdbeli = '00';
		$nmsalesmtdbeli = 'STOCK';
		
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
		$this->load->model('mJualUnit','',true);
		$ssql = $this->mJualUnit->cek_refno($kdtrx,$refno);
		
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
		
		
		$this->load->model('mJualUnit','',true);
		$ssql = $this->mJualUnit->cek_param();
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$kdsupp = $ssql->fs_kd_cussup;
			$kdsuppcount = $ssql->fs_countcussup;
			$kdtax = $ssql->fb_otax;
		}
		else
		{
			$kdsupp = '';
			$kdsuppcount = '';
			$kdtax = '0';
			
			$hasil = array('sukses' => false, 'hasil' => 'Saving Failed, Supplier code has not been set!!');
			echo json_encode($hasil);
			return;
		}
		
		
		$this->load->model('mJualUnit','',true);
		$ssql = $this->mJualUnit->cek_acno($kdsupp,$kdsuppcount);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$kdacno = $ssql->fs_kd_acno;
		}
		else
		{
			$kdacno = '';
			
			$hasil = array('sukses' => false, 'hasil' => 'Saving Failed, Supplier account number must be filled!!');
			echo json_encode($hasil);
			return;
		}
		
		
		$this->load->model('mJualUnit','',true);
		$ssql = $this->mJualUnit->cek_hargabeli($refnodt,$kdprod);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$harga = $ssql->price;
		}
		else
		{
			$harga = '0';
		}
		
		if ($harga == '0')
		{
			$harga = (float)(round($hargaunit / 1.1, 6));
		}
		else
		{
			if ($kdtax == '0')
			{
				$harga = (float)(round($harga / 1.1, 6));
			}
			else
			{
				$harga = $harga;
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
			'fs_kd_comp' => trim($this->session->userdata('gComp')),
			'fs_kd_dept' => trim($this->session->userdata('gDept')),
			'fs_count' => trim($this->session->userdata('gCount')),
			'fs_kd_trx' => trim($kdtrx),
			'fs_refno' => trim($refno),
			
			'fd_refno' => trim($refnodt),
			'fd_periode' => trim($periode),
			'fs_kd_cussup' => trim($kdsupp),
			'fs_countcussup' => trim($kdsuppcount),
			'fs_docno' => trim($docno),
			
			'fd_docno' => trim($docnodt),
			'fs_kd_term' => '00',
			'fn_grsamt' => trim($harga),
			'fn_dscamt' => '0',
			'fn_sdscamt' => '0',
			
			'fn_netbframt' => trim($harga),
			'fn_taxamt' => (float)(round($harga * 0.1, 6)),
			'fs_kd_otax' => '0',
			'fs_nm_otax' => 'NONE',
			'fn_otaxpct' => '0',
			
			'fn_otaxamt' => '0',
			'fn_netaftramt' => (float)(round($harga * 1.1, 6)),
			'fn_addonamt' => '0',
			'fn_deducamt' => '0',
			'fn_trxamt' => (float)(round($harga * 1.1, 6)),
			
			'fn_rmnamt' => (float)(round($harga * 1.1, 6)),
			'fn_bbn' => (float)(round($harga * 1.1, 6)),
			'fn_subsidi' => '0',
			'fb_spearpart' => trim($this->session->userdata('gSparePart')),
			'fn_installment' => '0',
			
			'fs_kd_payment' => '',
			'fs_nm_payment' => '',
			'fs_acno' => '',
			'fs_kd_wh' => '',
			'fs_nm_wh' => '',
			
			'fs_kd_salesman' => '',
			'fs_nm_salesman' => '',
			'fs_kd_salesmtd' => trim($kdsalesmtdbeli),
			'fs_nm_salesmtd' => trim($nmsalesmtdbeli),
			
			'fs_kd_dp' => '',
			'fs_nm_dp' => '',
			'fn_dpamt' => '0',
			'fs_invno' => '',
			'fd_invno' => trim($refnodt)
		);
		
		if ($xupdate == false)
		{
			$dt2 = array(
				'fs_usrcrt' => trim($this->session->userdata('gUser')),
				'fd_usrcrt' => trim(date('Y-m-d H:i:s')),
				'fs_upddt' => trim($this->session->userdata('gUser')),
				'fd_upddt' => trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			
			$this->db->insert('tx_posheader', $data);
		}
		else
		{
			$dt2 = array(
				'fs_upddt' => trim($this->session->userdata('gUser')),
				'fd_upddt' => trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
						AND	fs_refno = '".trim($refno)."'";
			
			$this->db->where($where);
			$this->db->update('tx_posheader', $data);
			
			$this->load->model('mJualUnit','',true);
			$ssql = $this->mJualUnit->unit_stokupdate($refno,'DEL');
		}
		
		$where = "fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tm_icregister');
		
		
		//hapus detail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_posdetail');
		//eof hapus detail
		
		//simpan detail
		$data = array(
			'fs_kd_comp' => trim($this->session->userdata('gComp')),
			'fs_kd_dept' => trim($this->session->userdata('gDept')),
			'fs_count' => trim($this->session->userdata('gCount')),
			'fs_kd_trx' => trim($kdtrx),
			'fs_refno' => trim($refno),
			
			'fd_refno' => trim($refnodt),
			'fd_periode' => trim($periode),
			'fs_seqno' => trim(sprintf("%06d",'1')),
			'fs_kd_product' => trim($kdprod),
			'fs_nm_product' => trim($nmprod),
			
			'fn_qtytr' => '1',
			'fn_qtytr1' => '1',
			'fs_unitbill' => 'UNIT',
			'fn_unitprc' => trim($harga),
			'fn_dscprc' => '0',
			
			'fn_lineamt' => trim($harga),
			'fn_ldscamt' => '0',
			'fn_sdscamt' => '0',
			'fn_addonamt' => '0',
			'fn_deducamt' => '0',
			
			'fn_ftrxamt' => trim($harga),
			'fs_kd_wh' => '',
			'fs_countwh' => '',
			'fs_kd_bin' => '',
			'fs_luxcd' => '',
			
			'fs_luxnm' => '',
			'fn_luxpct' => '0',
			'fn_luxamt' => '0',
			'fs_kd_ppnbm' => '',
			'fs_nm_ppnbm' => '',
			
			'fn_ppnbm' => '0',
			'fn_ppnbmpct' => '0',
			
			'fs_usrcrt' => trim($this->session->userdata('gUser')),
			'fd_usrcrt' => trim(date('Y-m-d H:i:s')),
			'fs_upddt' => trim($this->session->userdata('gUser')),
			'fd_upddt' => trim(date('Y-m-d H:i:s'))
			);
		
		$this->db->insert('tx_posdetail', $data);
		//eof simpan detail
		
		//hapus reg
		$where = "fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tm_icregister');
		//eof hapus reg
		
		
		//simpan reg
		
		//generate reg product
		$xprefix = trim($this->session->userdata('gDept')).trim($kdprod).trim($refnodt);
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
		
		$data = array(
			'fs_kd_comp' => trim($this->session->userdata('gComp')),
			'fs_kd_dept' => trim($this->session->userdata('gDept')),
			'fs_count' => trim($this->session->userdata('gCount')),
			'fs_kd_trx' => trim($kdtrx),
			'fs_kd_strx' => '0100',
			
			'fs_refno' => trim($refno),
			'fd_periode' => trim($periode),
			'fs_seqnoRegister' => trim(sprintf("%06d",'1')),
			'fs_seqno' => trim(sprintf("%06d",'1')),
			'fs_register' => trim($register),
			
			'fs_kd_product' => trim($kdprod),
			'fs_kd_type' => '0',
			'fn_hpp' => trim($harga),
			'fb_delete' => '0',
			'fn_silinder' => trim($cc),
			
			'fs_rangka' => trim($rangka),
			'fs_machine' => trim($mesin),
			'fs_kd_warna' => trim($kdwarna),
			'fd_thnpembuatan' => trim($thn),
			'fd_thnperakitan' => trim($thn),
			
			'fn_qty' => '1',
			'fs_kd_wh' => trim($kdwh),
			'fs_nm_wh' => trim($nmwh),
			
			'fs_usrcrt' => trim($this->session->userdata('gUser')),
			'fd_usrcrt' => trim(date('Y-m-d H:i:s')),
			'fs_upddt' => trim($this->session->userdata('gUser')),
			'fd_upddt' => trim(date('Y-m-d H:i:s'))
		);
		
		$this->db->insert('tm_icregister', $data);
		//eof simpan reg
		
		$this->load->model('mJualUnit','',true);
		$ssql = $this->mJualUnit->pos_beli($refno,$register,$kdtrx,$kdsalesmtdbeli);
		
		if ($xupdate == false)
		{
			$hasil = array('sukses' => true, 'hasil' => 'Saving Purchase Success', 'refno' => $refno, 'reg' => $register);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array('sukses' => true, 'hasil' => 'Saving Purchase Update Success', 'refno' => $refno, 'reg' => $register);
			echo json_encode($hasil);
		}
	}
	
	function jual_save()
	{
		$kdcust = trim($this->input->post('fs_kd_cust'));
		$kdcustcount = trim($this->input->post('fs_count_cust'));
		$nmcust = trim($this->input->post('fs_nm_cust'));
		$idcard = trim($this->input->post('fs_idcard'));
		$addr = trim($this->input->post('fs_addr'));
		
		$refno = trim($this->input->post('fs_refno'));
		$refnodt = trim($this->input->post('fd_refno'));
		$docno = trim($this->input->post('fs_docno'));
		$docnodt = trim($this->input->post('fd_docno'));
		$kdsales = trim($this->input->post('fs_kd_sales'));
		
		$nmsales = trim($this->input->post('fs_nm_sales'));
		$kdsurvey = trim($this->input->post('fs_kd_survey'));
		$nmsurvey = trim($this->input->post('fs_nm_survey'));
		$kdterm = trim($this->input->post('fs_kd_term'));
		$nmterm = trim($this->input->post('fs_nm_term'));
		
		$kdprod = trim($this->input->post('fs_kd_product'));
		$nmprod = trim($this->input->post('fs_nm_product'));
		$register = trim($this->input->post('fs_register'));
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		
		$kdsalesmtd = trim($this->input->post('fs_kd_salesmtd'));
		$nmsalesmtd = trim($this->input->post('fs_nm_salesmtd'));
		$kdpay = trim($this->input->post('fs_kd_payment'));
		$nmpay = trim($this->input->post('fs_nm_payment'));
		$kdacno = trim($this->input->post('fs_kd_acno'));
		
		$nmacno = trim($this->input->post('fs_nm_acno'));
		$cc = trim($this->input->post('fn_cc'));
		$thn = trim($this->input->post('fn_thn'));
		$kdwarna = trim($this->input->post('fs_kd_color'));
		$nmwarna = trim($this->input->post('fs_nm_color'));
		
		$kdwh = trim($this->input->post('fs_kd_wh'));
		$nmwh = trim($this->input->post('fs_nm_wh'));
		$hargaunit = trim($this->input->post('fn_harga'));
		$dpmax = trim($this->input->post('fn_dpmax'));
		$disc = trim($this->input->post('fn_disc'));
		
		$kddp = trim($this->input->post('fs_kd_dp'));
		$nmdp = trim($this->input->post('fs_nm_dp'));
		$nilaidp = trim($this->input->post('fn_dp'));
		$discl = trim($this->input->post('fn_discl'));
		$install = trim($this->input->post('fn_instal'));
		
		$total = trim($this->input->post('fn_total'));
		$refnobeli = trim($this->input->post('fs_refnobeli'));
		$nmqq = trim($this->input->post('fs_nm_qq'));
		$almtqq = trim($this->input->post('fs_addr_qq'));
		
		$kdtrx = 'JL';
		
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
		$this->load->model('mJualUnit','',true);
		$ssql = $this->mJualUnit->cek_refno($kdtrx,$refno);
		
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
		
		
		$this->load->model('mJualUnit','',true);
		$ssql = $this->mJualUnit->cek_param();
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$kdtax = $ssql->fb_otax;
		}
		else
		{
			$kdtax = '0';
		}
		
		$this->load->model('mJualUnit','',true);
		$ssql = $this->mJualUnit->cek_hargabeli($refnodt,$kdprod);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$hpp = $ssql->price;
		}
		else
		{
			$hpp = '0';
		}
		
		if ($hpp == '0')
		{
			$hpp = round($hargaunit / 1.1, 6);
		}
		else
		{
			if ($kdtax == '0')
			{
				$hpp = round($hpp / 1.1, 6);
			}
			else
			{
				$hpp = $hpp;
			}
		}
		
		$this->load->model('mJualUnit','',true);
		$ssql = $this->mJualUnit->cek_hargabeli2($refnodt,$kdprod);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$bbn = $ssql->price;
		}
		else
		{
			$bbn = '0';
		}
		
		if ($bbn == '0')
		{
			$bbn = $hargaunit * 0.1;
		}
		
		$harga = round(($hargaunit - $disc) / 1.1, 6);
		
		
		$this->load->model('mJualUnit','',true);
		$ssql = $this->mJualUnit->disc_max($refno,$kdprod);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			$discmax = $ssql->fn_grsmargin;
			$dpmaks = $ssql->fn_jualksi;
		}
		else
		{
			$discmax = '0';
			$dpmaks = '0';
		}
		
		if ($dpmax == '0')
		{
			$dpmax = $dpmaks;
		}
		else
		{
			$dpmax = $dpmax;
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
		
		//hapus tm_posregsold
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	fs_kd_trx = '".trim($kdtrx)."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tm_posregsold');
		//eof hapus tm_posregsold
		
		//simpan tm_posregsold
		$data = array(
			'fs_kd_comp' => trim($this->session->userdata('gComp')),
			'fs_kd_dept' => trim($this->session->userdata('gDept')),
			'fs_count' => trim($this->session->userdata('gCount')),
			'fs_kd_trx' => trim($kdtrx),
			'fs_refno' => trim($refno),
			
			'fd_refno' => trim($refnodt),
			'fs_kd_cussup' => trim($kdcust),
			'fs_countcussup' => trim($kdcustcount),
			'fs_kd_dp' => trim($kddp),
			'fs_nm_dp' => trim($nmdp),
			
			'fs_kd_product' => trim($kdprod),
			'fs_nm_product' => trim($nmprod),
			'fs_chasis' => trim($rangka),
			'fs_machine' => trim($mesin),
			'fn_hpp' => trim($hpp),
			
			'fn_otr' => trim($hargaunit),
			'fn_discount' => trim($disc),
			'fn_ftrxamt' => (float)(round(((($hargaunit - $disc) * 1.1) - $nilaidp), 6)),
			'fn_installment' => trim($kdterm),
			'fn_instamt' => trim($install),
			
			'fn_taxamt' => (float)(round((($hargaunit / 1.1) * 0.1), 6)),
			'fn_otaxamt' => '0',
			'fn_dpamt' => trim($nilaidp),
			'fn_addonamt' => '0',
			'fn_deducamt' => '0',
			
			'fn_bbn' => trim($bbn),
			'fn_dpp' => (float)($hargaunit / 1.1),
			'fn_subsidi' => trim($discl),
			'fn_discmax' => trim($discmax),
			'fn_dpmax' => trim($dpmax),
			
			'fs_kd_pay' => trim($kdpay),
			'fs_nm_pay' => trim($nmpay),
			'fs_acno' => trim($kdacno),
			'fs_kd_sales' => trim($kdsales),
			'fs_nm_sales' => trim($nmsales),
			
			'fs_kd_surveyor' => trim($kdsurvey),
			'fs_nm_surveyor' => trim($nmsurvey),
			'fs_seqno' => '000001',
			
			'fs_usrcrt' => trim($this->session->userdata('gUser')),
			'fd_usrcrt' => trim(date('Y-m-d H:i:s')),
			'fs_upddt' => trim($this->session->userdata('gUser')),
			'fd_upddt' => trim(date('Y-m-d H:i:s'))
		);
		
		$this->db->insert('tm_posregsold', $data);
		//eof simpan tm_posregsold
        
		
		$dt = array(
			'fs_kd_comp' => trim($this->session->userdata('gComp')),
			'fs_kd_dept' => trim($this->session->userdata('gDept')),
			'fs_count' => trim($this->session->userdata('gCount')),
			'fs_kd_trx' => trim($kdtrx),
			'fs_refno' => trim($refno),
			
			'fd_refno' => trim($refnodt),
			'fd_periode' => trim($periode),
			'fs_kd_cussup' => trim($kdcust),
			'fs_countcussup' => trim($kdcustcount),
			'fs_docno' => trim($docno),
			
			'fd_docno' => trim($docnodt),
			'fs_kd_term' => trim($kdterm),
			'fn_grsamt' => trim($hargaunit),
			'fn_dscamt' => trim($disc),
			'fn_sdscamt' => '0',
			
			'fn_netbframt' => trim($harga),
			'fn_taxamt' => trim($hargaunit - $disc - $harga),
			'fs_kd_otax' => '',
			'fs_nm_otax' => '',
			'fn_otaxpct' => '0',
			
			'fn_otaxamt' => '0',
			'fn_netaftramt' => trim($hargaunit - $disc),
			'fn_addonamt' => '0',
			'fn_deducamt' => '0',
			'fn_trxamt' => trim($hargaunit - $disc),
			
			'fn_rmnamt' => trim($hargaunit - $disc),
			'fn_bbn' => trim($bbn),
			'fn_subsidi' => trim($discl),
			'fb_spearpart' => trim($this->session->userdata('gSparePart')),
			'fn_installment' => trim($install),
			
			'fs_kd_payment' => trim($kdpay),
			'fs_nm_payment' => trim($nmpay),
			'fs_acno' => trim($kdacno),
			'fs_kd_wh' => trim($kdwh),
			'fs_nm_wh' => trim($nmwh),
			
			'fs_kd_salesman' => trim($kdsales),
			'fs_nm_salesman' => trim($nmsales),
			'fs_kd_salesmtd' => trim($kdsalesmtd),
			'fs_nm_salesmtd' => trim($nmsalesmtd),
			
			'fs_kd_dp' => trim($kddp),
			'fs_nm_dp' => trim($nmdp),
			'fn_dpamt' => trim($nilaidp),
			'fs_invno' => '',
			'fd_invno' => '',
			
			'fs_qqname' => trim($nmqq),
			'fs_qqaddr' => trim($almtqq)
		);
		
		if ($xupdate == false)
		{
			$dt2 = array(
				'fs_usrcrt' => trim($this->session->userdata('gUser')),
				'fd_usrcrt' => trim(date('Y-m-d H:i:s')),
				'fs_upddt' => trim($this->session->userdata('gUser')),
				'fd_upddt' => trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			
			$this->db->insert('tx_posheader', $data);
		}
		else
		{
			$dt2 = array(
				'fs_upddt' => trim($this->session->userdata('gUser')),
				'fd_upddt' => trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
						AND	fs_refno = '".trim($refno)."'";
			
			$this->db->where($where);
			$this->db->update('tx_posheader', $data);
			
			$this->load->model('mJualUnit','',true);
			$ssql = $this->mJualUnit->unit_stokupdate($refno,'INS');
		}
		
		
		//hapus detail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_posdetail');
		//eof hapus detail
		
		//simpan detail
		$data = array(
			'fs_kd_comp' => trim($this->session->userdata('gComp')),
			'fs_kd_dept' => trim($this->session->userdata('gDept')),
			'fs_count' => trim($this->session->userdata('gCount')),
			'fs_kd_trx' => trim($kdtrx),
			'fs_refno' => trim($refno),
			
			'fd_refno' => trim($refnodt),
			'fd_periode' => trim($periode),
			'fs_seqno' => trim(sprintf("%06d",'1')),
			'fs_kd_product' => trim($kdprod),
			'fs_nm_product' => trim($nmprod),
			
			'fn_qtytr' => '1',
			'fn_qtytr1' => '1',
			'fs_unitbill' => 'UNIT',
			'fn_unitprc' => trim($hargaunit),
			'fn_dscprc' => trim($disc),
			
			'fn_lineamt' => trim($hargaunit),
			'fn_ldscamt' => trim($disc),
			'fn_sdscamt' => '0',
			'fn_addonamt' => '0',
			'fn_deducamt' => '0',
			
			'fn_ftrxamt' => trim($hargaunit),
			'fs_kd_wh' => '',
			'fs_countwh' => '',
			'fs_kd_bin' => '',
			'fs_luxcd' => '',
			
			'fs_luxnm' => '',
			'fn_luxpct' => '0',
			'fn_luxamt' => '0',
			'fs_kd_ppnbm' => '',
			'fs_nm_ppnbm' => '',
			
			'fn_ppnbm' => '0',
			'fn_ppnbmpct' => '0',
			
			'fs_usrcrt' => trim($this->session->userdata('gUser')),
			'fd_usrcrt' => trim(date('Y-m-d H:i:s')),
			'fs_upddt' => trim($this->session->userdata('gUser')),
			'fd_upddt' => trim(date('Y-m-d H:i:s'))
		);
		
		$this->db->insert('tx_posdetail', $data);
		//eof simpan detail
		
		
		$this->load->model('mJualUnit','',true);
		$ssql = $this->mJualUnit->pos_jual($refno,$kdtrx,$kdsalesmtd);
		
		if ($xupdate == false)
		{
			$hasil = array('sukses' => true, 'hasil' => 'Saving Sales Success', 'refno' => $refno, 'refnobeli' => $refnobeli);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array('sukses' => true, 'hasil' => 'Saving Sales Update Success', 'refno' => $refno, 'refnobeli' => $refnobeli);
			echo json_encode($hasil);
		}
	}
	
	function jual_cekremove()
	{
		$refno = trim($this->input->post('fs_refno'));
		
		if (trim($refno) == '' or trim($refno) == 'AUTOMATIC')
		{
			$hasil = array('sukses' => false, 'hasil' => 'Remove Failed, Reference number unknown!!');
			echo json_encode($hasil);
		}
		else
		{
			$this->load->model('mJualUnit','',true);
			$ssql = $this->mJualUnit->cek_refno('JL',$refno);
			
			if ($ssql->num_rows() > 0)
			{
				$hasil = array('sukses' => true, 'hasil' => 'Remove record?');
				echo json_encode($hasil);
			}
			else
			{
				$hasil = array('sukses' => false, 'hasil' => 'Remove Failed, Reference number unknown!!');
				echo json_encode($hasil);
			}
		}
	}
	
	function jual_remove()
	{
		$refno = trim($this->input->post('fs_refno'));
		$refnobeli = trim($this->input->post('fs_refnobeli'));
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		
		//hapus jual
		
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
		
		//hapus tm_posregsold
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tm_posregsold');
		//eof hapus tm_posregsold
		
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
		
		//eof hapus jual
		
		//hapus beli
		
		//hapus tx_actheaderdt
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refnobeli)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actheaderdt');
		//eof hapus tx_actheaderdt
		
		//hapus tx_actheader
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refnobeli)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actheader');
		//eof hapus tx_actheader
		
		//hapus tx_actdetail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refnobeli)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_actdetail');
		//eof hapus tx_actdetail
		
		//hapus tm_posregsold
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refnobeli)."'";
		
		$this->db->where($where);
		$this->db->delete('tm_posregsold');
		//eof hapus tm_posregsold
		
		//hapus tx_posheader
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refnobeli)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_posheader');
		//eof hapus tx_posheader
		
		//hapus tx_posdetail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refnobeli)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_posdetail');
		//eof hapus tx_posdetail
		
		//hapus tm_icregister
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refnobeli)."'
					AND	(fs_rangka = '".trim($rangka)."'
					OR	fs_machine = '".trim($mesin)."')";
		
		$this->db->where($where);
		$this->db->delete('tm_icregister');
		//eof hapus tm_icregister
		
		//eof hapus beli
		
		$hasil = array('sukses' => true, 'hasil' => 'Remove sales reference number: "'.$refno.'" success');
		echo json_encode($hasil);
	}
	
	function printso()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tsalesorder';
		$nmfile = 'salesorder-'.$jamskg;
		$xIP = trim($this->session->userdata('ip_address'));
		$gIP = trim($this->session->userdata('gIP'));
		
		$oReader = PHPExcel_IOFactory::createReader('Excel5');
		
		if (trim($xIP) == trim($gIP))
		{
			$xtPath = '/var/www/'.$this->config->item('base_folder').'/assets/docs/';
			$xPath = '/var/www/'.$this->config->item('base_folder').'/temp/';
		}
		else
		{
			$xtPath = APPPATH.'../assets/docs/';
			$xPath = APPPATH.'../temp/';
		}
		
		$oExcel = $oReader->load($xtPath.$nmfiletemp.'.xls');
		
		$oExcel->getActiveSheet()->setTitle('Sales Order');
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
		
		$trx = 'JL';
		$refno = trim($this->input->post('fs_refno'));
		$docno = trim($this->input->post('fs_refno'));
		$cust = trim($this->input->post('fs_refno'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->jual_unit_all($trx,$refno,$docno,$cust);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			
			$FakturNumber = $ssql->fs_refno;
			$PaymentName = $ssql->fs_nm_payment;
			$CustomerName = $ssql->fs_nm_cussup;
			$CustomerAddr = $ssql->fs_addr;
			$TypeName = $ssql->fs_nm_product;
			$Color = $ssql->fs_nm_warna;
			$Year = $ssql->fs_thn;
			$ChassisNumber = $ssql->fs_rangka;
			$MachineNumber = $ssql->fs_mesin;
			$UnitPrice = $ssql->fn_unitprice;
			$Discount = $ssql->fn_disc;
			$Term = $ssql->fs_nm_term;
			$InstallmentPerMonth = $ssql->fn_install;
			$Total = $ssql->fn_total;
			$DPValue = $ssql->fn_dp;
			$LeasingDisc = $ssql->fn_subsidi;
			
			$UserName = trim($this->session->userdata('gUser'));
			
			$pjghal = 57;
			$i = 1;
			$l = 0; // no urut
			
			$k = 1; // pjg hal
			$p = 1; // awal hal
			
			$i++;
			$k++;
			$sel->setCellValue('A'.$i, 'SURAT PEMESANAN KENDARAAN');
			$sel->mergeCells('A'.$i.':G'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$sel->getStyle('A'.$i)->getFont()->setSize(12);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'Nomor');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $FakturNumber);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Cara Pembayaran');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $PaymentName);
			$sel->mergeCells('C'.$i.':G'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Nama Pembeli');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $CustomerName);
			$sel->mergeCells('C'.$i.':G'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Alamat');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $CustomerAddr);
			$sel->getStyle('C'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$sel->getStyle('C'.$i)->getAlignment()->setWrapText(true);
			$j = $i + 1;
			$sel->mergeCells('C'.$i.':G'.$j);
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
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Tipe');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $TypeName);
			
			$sel->setCellValue('E'.$i, 'No. Rangka');
			$sel->setCellValue('F'.$i, ':');
			$sel->setCellValue('G'.$i, $ChassisNumber);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Warna');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $Color);
			
			$sel->setCellValue('E'.$i, 'No. Mesin');
			$sel->setCellValue('F'.$i, ':');
			$sel->setCellValue('G'.$i, $MachineNumber);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i,'Tahun');
			$sel->setCellValue('B'.$i,':');
			$sel->setCellValue('C'.$i, $Year);
			$sel->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
			
			$sel->setCellValue('A'.$i, 'Dengan keterangan tersebut di bawah ini :');
			$sel->mergeCells('A'.$i.':C'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i,'Harga Jual');
			$sel->setCellValue('B'.$i,':');
			$sel->setCellValue('C'.$i, number_format($UnitPrice));
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->setCellValue('E'.$i, 'Angsuran / Bln');
			$sel->setCellValue('F'.$i, ':');
			$sel->setCellValue('G'.$i, number_format($InstallmentPerMonth));
			$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i,'Potongan');
			$sel->setCellValue('B'.$i,':');
			$sel->setCellValue('C'.$i, number_format($Discount));
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$sel->setCellValue('E'.$i, 'Angsuran');
			$sel->setCellValue('F'.$i, ':');
			$sel->setCellValue('G'.$i, number_format($Term));
			$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i,'Total Subsidi');
			$sel->setCellValue('B'.$i,':');
			$sel->setCellValue('C'.$i, number_format($LeasingDisc));
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$sel->setCellValue('E'.$i, 'Total Uang Muka');
			$sel->setCellValue('F'.$i, ':');
			$sel->setCellValue('G'.$i, number_format($DPValue));
			$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('A'.$i.':G'.$i)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$i = $i + 10;
			$k = $k + 10;
			
			$this->load->model('mMainModul','',true);
			$xTglPrint = date('d-m-Y');
			$xTglPrint = substr($xTglPrint,0,2).' '.$this->mMainModul->ambilbulan(substr($xTglPrint,3,2)).' '.substr($xTglPrint,6,4);
			$sel->setCellValue('G'.$i,trim($this->session->userdata('gKota')).', '.trim($xTglPrint));
			$i++;
			$k++;
			
			$sel->setCellValue('G'.$i,'Petugas Counter,');
			$i = $i + 20;
			$k = $k + 20;
			
			$sel->setCellValue('A'.$i,$CustomerName);	
			$sel->setCellValue('G'.$i,'...............');
			
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
			$hasil = array('sukses' => false, 'hasil' => 'No record!!');
			echo json_encode($hasil);
		}
	}
	
	function printdo()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tdeliveryorder';
		$nmfile = 'deliveryorder-'.$jamskg;
		$xIP = trim($this->session->userdata('ip_address'));
		$gIP = trim($this->session->userdata('gIP'));
		
		$oReader = PHPExcel_IOFactory::createReader('Excel5');
		
		if (trim($xIP) == trim($gIP))
		{
			$xtPath = '/var/www/'.$this->config->item('base_folder').'/assets/docs/';
			$xPath = '/var/www/'.$this->config->item('base_folder').'/temp/';
		}
		else
		{
			$xtPath = APPPATH.'../assets/docs/';
			$xPath = APPPATH.'../temp/';
		}
		
		$oExcel = $oReader->load($xtPath.$nmfiletemp.'.xls');
		
		$oExcel->getActiveSheet()->setTitle('Delivery Order');
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
		
		$trx = 'JL';
		$refno = trim($this->input->post('fs_refno'));
		$docno = trim($this->input->post('fs_refno'));
		$cust = trim($this->input->post('fs_refno'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->jual_unit_all($trx,$refno,$docno,$cust);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			
			$FakturNumber = $ssql->fs_refno;
			$PaymentName = $ssql->fs_nm_payment;
			$CustomerName = $ssql->fs_nm_cussup;
			$CustomerAddr = $ssql->fs_addr;
			$TypeName = $ssql->fs_nm_product;
			$Color = $ssql->fs_nm_warna;
			$Year = $ssql->fs_thn;
			$ChassisNumber = $ssql->fs_rangka;
			$MachineNumber = $ssql->fs_mesin;
			$UnitPrice = $ssql->fn_unitprice;
			$Discount = $ssql->fn_disc;
			$Term = $ssql->fs_nm_term;
			$InstallmentPerMonth = $ssql->fn_install;
			$Total = $ssql->fn_total;
			$DPValue = $ssql->fn_dp;
			$LeasingDisc = $ssql->fn_subsidi;
			
			$UserName = trim($this->session->userdata('gUser'));
			
			$pjghal = 57;
			$i = 1;
			$l = 0; // no urut
			
			$k = 1; // pjg hal
			$p = 1; // awal hal
			
			$i++;
			$k++;
			$sel->setCellValue('A'.$i, 'SURAT PENGIRIMAN KENDARAAN');
			$sel->mergeCells('A'.$i.':F'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$sel->getStyle('A'.$i)->getFont()->setSize(12);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'Nomor Faktur');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $FakturNumber);
			
			$sel->setCellValue('D'.$i, 'Harga Jual');
			$sel->setCellValue('E'.$i, ':');
			$sel->setCellValue('F'.$i, number_format($UnitPrice));
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Nama Pembeli');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $CustomerName);
			
			$sel->setCellValue('D'.$i, 'Discount');
			$sel->setCellValue('E'.$i, ':');
			$sel->setCellValue('F'.$i, number_format($Discount));
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Alamat');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $CustomerAddr);
			$sel->getStyle('C'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$sel->getStyle('C'.$i)->getAlignment()->setWrapText(true);
			$j = $i + 1;
			$sel->mergeCells('C'.$i.':C'.$j);
			
			$sel->setCellValue('D'.$i, 'Uang Muka');
			$sel->setCellValue('E'.$i, ':');
			$sel->setCellValue('F'.$i, number_format($DPValue));
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$i++;
			$k++;
			
			$sel->setCellValue('D'.$i, 'Subsidi');
			$sel->setCellValue('E'.$i, ':');
			$sel->setCellValue('F'.$i, number_format($LeasingDisc));
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$i++;
			$k++;
			
			$sel->setCellValue('D'.$i, 'Angsuran');
			$sel->setCellValue('E'.$i, ':');
			$sel->setCellValue('F'.$i, number_format($InstallmentPerMonth));
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
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
			
			// $sel->setCellValue('E'.$i, 'Jenis Transaction');
			// $sel->setCellValue('F'.$i, ':');
			// $sel->setCellValue('G'.$i, ' ');
			// $i++;
			// $k++;
			
			$sel->setCellValue('A'.$i, 'Tipe');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $TypeName);
			
			$sel->setCellValue('D'.$i, 'No. Rangka');
			$sel->setCellValue('E'.$i, ':');
			$sel->setCellValue('F'.$i, $ChassisNumber);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Warna');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $Color);
			
			$sel->setCellValue('D'.$i, 'No. Mesin');
			$sel->setCellValue('E'.$i, ':');
			$sel->setCellValue('F'.$i, $MachineNumber);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i,'Tahun');
			$sel->setCellValue('B'.$i,':');
			$sel->setCellValue('C'.$i, $Year);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i,'Dengan unit sertaan berupa :');
			$sel->mergeCells('A'.$i.':C'.$i);
			$i++;
			$k++;
			$sel->setCellValue('A'.$i,'[  ] Karpet          [  ] Kunci Kontak          [  ] Tabung Pemadam          [  ] Buku Servis & Manual          [  ] Tool Box          [  ] Spare Tire          [  ] ...');
			$sel->mergeCells('A'.$i.':F'.$i);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i,'Keterangan :');
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i,'Barang yang telah diterima tidak dapat ditukar kembali');
			$sel->mergeCells('A'.$i.':C'.$i);
			$i = $i + 10;
			$k = $k + 10;
			
			$this->load->model('mMainModul','',true);
			$xTglPrint = date('d-m-Y');
			$xTglPrint = substr($xTglPrint,0,2).' '.$this->mMainModul->ambilbulan(substr($xTglPrint,3,2)).' '.substr($xTglPrint,6,4);
			$sel->setCellValue('E'.$i,trim($this->session->userdata('gKota')).', '.trim($xTglPrint));
			$sel->mergeCells('E'.$i.':F'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i,'Penerima');
			$sel->setCellValue('C'.$i,'Pengirim');
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->setCellValue('D'.$i,'PDI Oleh');
			$sel->setCellValue('E'.$i,'Yang Menyerahkan');
			$sel->mergeCells('E'.$i.':F'.$i);
			$i = $i + 20;
			$k = $k + 20;			
			
			$sel->setCellValue('A'.$i,$CustomerName);
			$sel->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$sel->getStyle('A'.$i)->getAlignment()->setWrapText(true);
			$j = $i + 1;
			$sel->mergeCells('A'.$i.':A'.$j);
			$sel->setCellValue('C'.$i,'...............');	
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->setCellValue('D'.$i,'...............');	
			$sel->setCellValue('E'.$i,'...............');
			$sel->mergeCells('E'.$i.':F'.$i);
			
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
			$hasil = array('sukses' => false, 'hasil' => 'No record!!');
			echo json_encode($hasil);
		}
	}
	
	function printinvoice()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tinvoice';
		$nmfile = 'invoice-'.$jamskg;
		$xIP = trim($this->session->userdata('ip_address'));
		$gIP = trim($this->session->userdata('gIP'));
		
		$oReader = PHPExcel_IOFactory::createReader('Excel5');
		
		if (trim($xIP) == trim($gIP))
		{
			$xtPath = '/var/www/'.$this->config->item('base_folder').'/assets/docs/';
			$xPath = '/var/www/'.$this->config->item('base_folder').'/temp/';
		}
		else
		{
			$xtPath = APPPATH.'../assets/docs/';
			$xPath = APPPATH.'../temp/';
		}
		
		$oExcel = $oReader->load($xtPath.$nmfiletemp.'.xls');
		
		$oExcel->getActiveSheet()->setTitle('Invoice');
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
		
		$trx = 'JL';
		$refno = trim($this->input->post('fs_refno'));
		$docno = trim($this->input->post('fs_refno'));
		$cust = trim($this->input->post('fs_refno'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->direct_sales_detail($trx,$refno,$docno);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			
			$FakturNumber = $ssql->fs_refno;
			$FakturDate = $ssql->fd_refno; 
			$DocumentNumber = $ssql->fs_docno;
			$CustomerName = $ssql->fs_nm_cussup;
			$PaymentName = $ssql->fs_nm_payment;
			$CustomerAddr = $ssql->fs_addr;
			$TypeName = $ssql->fs_nm_product;
			$ChassisNumber = $ssql->fs_rangka;
			$MachineNumber = $ssql->fs_mesin;
			$Color = $ssql->fs_nm_warna;
			$UnitPrice = $ssql->fn_unitprice;
			$Discount = $ssql->fn_disc;
			$DPValue = $ssql->fn_dp;
			$Total = $ssql->fn_total;
			
			//total bawah
			$HNetBfrtax = $ssql->HNetBfrtax;
			$HTaxamt = $ssql->HTaxamt;
			$HOtaxAmt = $ssql->HOtaxAmt;
			$HAddOn = $ssql->HAddOn;
			$GrandTotal = $HNetBfrtax + $HTaxamt + $HOtaxAmt + $HAddOn;
			$Company = $ssql->Company;
			
			$this->load->model('mMainModul','',true);
			$say = $this->mMainModul->terbilang($DPValue,4);
			
			$UserName = trim($this->session->userdata('gUser'));
			
			$pjghal = 57;
			$i = 1;
			$l = 0; // no urut
			
			$k = 1; // pjg hal
			$p = 1; // awal hal
			
			$i++;
			$k++;
			$sel->setCellValue('A'.$i, 'INVOICE');
			$sel->mergeCells('A'.$i.':I'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$sel->getStyle('A'.$i)->getFont()->setSize(12);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, $FakturNumber);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->mergeCells('A'.$i.':I'.$i);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'TO');
			$sel->setCellValue('B'.$i, ': '.$CustomerName);
			
			$sel->setCellValue('F'.$i, 'NO');
			$sel->setCellValue('G'.$i, ': '.$FakturNumber);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'ADD');
			$sel->setCellValue('B'.$i, ': '.$CustomerAddr);
			$sel->getStyle('B'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$sel->getStyle('B'.$i)->getAlignment()->setWrapText(true);
			$j = $i + 1;
			$sel->mergeCells('B'.$i.':D'.$j);
			
			$sel->setCellValue('F'.$i, 'DATE');
			$sel->setCellValue('G'.$i, ': '.$FakturDate);
			$i++;
			$k++;
			
			$sel->setCellValue('F'.$i, 'Order No');
			$sel->setCellValue('G'.$i, ':'.$DocumentNumber);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'UP');
			$sel->setCellValue('B'.$i, ':');
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'No');
			$sel->setCellValue('B'.$i, 'Model');
			$sel->mergeCells('B'.$i.':C'.$i);
			$sel->setCellValue('D'.$i, 'Color');
			$sel->setCellValue('E'.$i, 'Qty');
			$sel->setCellValue('F'.$i, 'Price');
			$sel->mergeCells('F'.$i.':G'.$i);
			$sel->setCellValue('H'.$i, 'Discount');
			$sel->setCellValue('I'.$i, 'Amount');
			$sel->getStyle('A'.$i.':I'.$i)->applyFromArray(
				array(
					'borders' => array(
						'top' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$sel->getStyle('A'.$i.':I'.$i)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$sel->getStyle('A'.$i.':I'.$i)->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('argb' => 'H0C0C0C0')
					)
				)
			);
			$q = $i;
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, '1');
			$sel->setCellValue('B'.$i, $TypeName);
			$sel->setCellValue('D'.$i, $Color);
			$sel->setCellValue('E'.$i, '1');
			$sel->setCellValue('F'.$i, number_format($UnitPrice));
			$sel->mergeCells('F'.$i.':G'.$i);
			$sel->setCellValue('H'.$i, number_format($Discount)); 
			$sel->setCellValue('I'.$i, number_format($UnitPrice - $Discount)); 
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('D'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$sel->getStyle('D'.$i)->getAlignment()->setWrapText(true);
			$j = $i + 1;
			$sel->mergeCells('D'.$i.':D'.$j);
			$i++;
			$k++;
			
			$sel->setCellValue('B'.$i, 'C = '.$ChassisNumber);
			$i++;
			$k++;
			
			$sel->setCellValue('B'.$i, 'M = '.$MachineNumber);
			$sel->getStyle('A'.$i.':I'.$i)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$arr = array(
				'borders' => array(
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$j = $q;
			$sel->getStyle('A'.$j.':A'.$i)->applyFromArray($arr);
			$sel->getStyle('B'.$j.':B'.$i)->applyFromArray($arr);
			$sel->getStyle('D'.$j.':D'.$i)->applyFromArray($arr);
			$sel->getStyle('E'.$j.':E'.$i)->applyFromArray($arr);
			$sel->getStyle('F'.$j.':F'.$i)->applyFromArray($arr);
			$sel->getStyle('H'.$j.':H'.$i)->applyFromArray($arr);
			$sel->getStyle('I'.$j.':I'.$i)->applyFromArray($arr);
			$sel->getStyle('I'.$j.':I'.$i)->applyFromArray(
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
			$q = $i;
			
			$sel->setCellValue('H'.$i, 'Total :');
			$sel->setCellValue('I'.$i, number_format($HNetBfrtax));
			$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('H'.$i.':I'.$i)->applyFromArray(
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
			
			$sel->setCellValue('H'.$i, 'PPN :');
			$sel->setCellValue('I'.$i, number_format($HTaxamt));
			$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('H'.$i.':I'.$i)->applyFromArray(
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
			
			$sel->setCellValue('H'.$i, 'PPH 22 :');
			$sel->setCellValue('I'.$i, number_format($HOtaxAmt));
			$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('H'.$i.':I'.$i)->applyFromArray(
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
			
			$sel->setCellValue('H'.$i, 'Materai :');
			$sel->setCellValue('I'.$i, number_format($HAddOn));
			$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('H'.$i.':I'.$i)->applyFromArray(
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
			
			$sel->setCellValue('H'.$i, 'Grand Total :');
			$sel->setCellValue('I'.$i, number_format($GrandTotal));
			$sel->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sel->getStyle('H'.$i.':I'.$i)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$j = $q;
			$sel->getStyle('H'.$j.':H'.$i)->applyFromArray($arr);
			$sel->getStyle('I'.$j.':I'.$i)->applyFromArray($arr);
			$sel->getStyle('I'.$j.':I'.$i)->applyFromArray(
				array(
					'borders' => array(
						'right' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$i = $i + 10;
			$k = $k + 10;
			
			$sel->setCellValue('G'.$i, $Company);
			$sel->mergeCells('G'.$i.':I'.$i);
			$i = $i + 20;
			$k = $k + 20;
			
			$sel->setCellValue('G'.$i, $UserName);
			$sel->mergeCells('G'.$i.':I'.$i);
			
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
			$hasil = array('sukses' => false, 'hasil' => 'No record!!');
			echo json_encode($hasil);
		}
	}
	
	function printkwitansidp()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tkwitansidp';
		$nmfile = 'kwitansidp-'.$jamskg;
		$xIP = trim($this->session->userdata('ip_address'));
		$gIP = trim($this->session->userdata('gIP'));
		
		$oReader = PHPExcel_IOFactory::createReader('Excel5');
		
		if (trim($xIP) == trim($gIP))
		{
			$xtPath = '/var/www/'.$this->config->item('base_folder').'/assets/docs/';
			$xPath = '/var/www/'.$this->config->item('base_folder').'/temp/';
		}
		else
		{
			$xtPath = APPPATH.'../assets/docs/';
			$xPath = APPPATH.'../temp/';
		}
		
		$oExcel = $oReader->load($xtPath.$nmfiletemp.'.xls');
		
		$oExcel->getActiveSheet()->setTitle('Kwitansi DP');
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
		
		$trx = 'JL';
		$refno = trim($this->input->post('fs_refno'));
		$docno = trim($this->input->post('fs_refno'));
		$cust = trim($this->input->post('fs_refno'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->jual_unit_all($trx,$refno,$docno,$cust);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			
			$FakturNumber = $ssql->fs_refno;
			$CustomerName = $ssql->fs_nm_cussup;
			$PaymentName = $ssql->fs_nm_payment;
			$CustomerAddr = $ssql->fs_addr;
			$TypeName = $ssql->fs_nm_product;
			$ChassisNumber = $ssql->fs_rangka;
			$MachineNumber = $ssql->fs_mesin;
			$UnitPrice = $ssql->fn_unitprice;
			$Discount = $ssql->fn_disc;
			$DPValue = $ssql->fn_dp;
			
			$this->load->model('mMainModul','',true);
			$say = $this->mMainModul->terbilang($DPValue,4);
			
			$UserName = trim($this->session->userdata('gUser'));
			
			$pjghal = 57;
			$i = 1;
			$l = 0; // no urut
			
			$k = 1; // pjg hal
			$p = 1; // awal hal
			
			$i++;
			$k++;
			$sel->setCellValue('A'.$i, 'KWITANSI');
			$sel->mergeCells('A'.$i.':H'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$sel->getStyle('A'.$i)->getFont()->setSize(12);
			$i++;
			$k++;
			
			$sel->mergeCells('A'.$i.':H'.$i);
			$sel->setCellValue('A'.$i, $FakturNumber);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i = $i + 5;
			$k = $k + 5;
			
			$sel->setCellValue('A'.$i, 'Telah diterima dari');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $CustomerName);
			$sel->mergeCells('C'.$i.':H'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Uang sejumlah');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $say);
			$sel->mergeCells('C'.$i.':H'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Untuk keperluan');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, 'Pembayaran DP 1 Unit kendaraan '.$TypeName);
			$sel->mergeCells('C'.$i.':H'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('C'.$i, 'a/n: '.$CustomerName);
			$sel->mergeCells('C'.$i.':H'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('C'.$i, 'No Rangka: '.$ChassisNumber);
			$sel->mergeCells('C'.$i.':D'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('C'.$i, 'No Mesin: '.$MachineNumber);
			$sel->mergeCells('C'.$i.':D'.$i);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i,'# Rp '.number_format($DPValue).',- #');
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
			$i = $i + 20;
			$k = $k + 20;			
				
			$sel->setCellValue('E'.$i,'...............');	
			$sel->mergeCells('E'.$i.':F'.$i);
			
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
			$hasil = array('sukses' => false, 'hasil' => 'No record!!');
			echo json_encode($hasil);
		}
	}
	
	function printkwitansitunai()
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
			$xtPath = '/var/www/'.$this->config->item('base_folder').'/assets/docs/';
			$xPath = '/var/www/'.$this->config->item('base_folder').'/temp/';
		}
		else
		{
			$xtPath = APPPATH.'../assets/docs/';
			$xPath = APPPATH.'../temp/';
		}
		
		$oExcel = $oReader->load($xtPath.$nmfiletemp.'.xls');
		
		$oExcel->getActiveSheet()->setTitle('Kwitansi DP');
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
		
		$trx = 'JL';
		$refno = trim($this->input->post('fs_refno'));
		$docno = trim($this->input->post('fs_refno'));
		$cust = trim($this->input->post('fs_refno'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->jual_unit_all($trx,$refno,$docno,$cust);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			
			$FakturNumber = $ssql->fs_refno;
			$CustomerName = $ssql->fs_nm_cussup;
			$PaymentName = $ssql->fs_nm_payment;
			$CustomerAddr = $ssql->fs_addr;
			$TypeName = $ssql->fs_nm_product;
			$ChassisNumber = $ssql->fs_rangka;
			$MachineNumber = $ssql->fs_mesin;
			$UnitPrice = $ssql->fn_unitprice;
			$Discount = $ssql->fn_disc;
			$DPValue = $ssql->fn_dp;
			$Total = $ssql->fn_total;
			
			$this->load->model('mMainModul','',true);
			$say = $this->mMainModul->terbilang($Total,4);
			
			$UserName = trim($this->session->userdata('gUser'));
			
			$pjghal = 57;
			$i = 1;
			$l = 0; // no urut
			
			$k = 1; // pjg hal
			$p = 1; // awal hal
			
			$i++;
			$k++;
			$sel->setCellValue('A'.$i, 'KWITANSI');
			$sel->mergeCells('A'.$i.':H'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$sel->getStyle('A'.$i)->getFont()->setSize(12);
			$i++;
			$k++;
			
			$sel->mergeCells('A'.$i.':H'.$i);
			$sel->setCellValue('A'.$i, $FakturNumber);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i = $i + 5;
			$k = $k + 5;
			
			$sel->setCellValue('A'.$i, 'Telah diterima dari');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $CustomerName);
			$sel->mergeCells('C'.$i.':H'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Uang sejumlah');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $say);
			$sel->mergeCells('C'.$i.':H'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Untuk keperluan');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, 'Pembayaran Tunai 1 Unit kendaraan '.$TypeName);
			$sel->mergeCells('C'.$i.':H'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('C'.$i, 'a/n: '.$CustomerName);
			$sel->mergeCells('C'.$i.':H'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('C'.$i, 'No Rangka: '.$ChassisNumber);
			$sel->mergeCells('C'.$i.':D'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('C'.$i, 'No Mesin: '.$MachineNumber);
			$sel->mergeCells('C'.$i.':D'.$i);
			$i = $i + 2;
			$k = $k + 2;
			
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
			$i = $i + 20;
			$k = $k + 20;			
				
			$sel->setCellValue('E'.$i,'...............');	
			$sel->mergeCells('E'.$i.':F'.$i);
			
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
			$hasil = array('sukses' => false, 'hasil' => 'No record!!');
			echo json_encode($hasil);
		}
	}
	
	function printfidusia2()
	{
		$xIP = trim($this->session->userdata('ip_address'));
		$gIP = trim($this->session->userdata('gIP'));
		if (trim($xIP) == trim($gIP))
		{
			$xtPath = '/var/www/'.$this->config->item('base_folder').'/assets/docs/';
			$xPath = '/var/www/'.$this->config->item('base_folder').'/temp/';
		}
		else
		{
			$xtPath = APPPATH.'../assets/docs/';
			$xPath = APPPATH.'../temp/';
		}
		$nmfile = 'rptKwitansi_fidusia';
		// $cr_report = 'C:\\xampp\\htdocs\\asterweb\\assets\\docs\\'.$nmfile.'.rpt';//"C:\\wamp\\www\\site\\Report1.rpt";
		// $exp_pdf = 'C:\\xampp\\htdocs\\asterweb\\assets\\docs\\'.$nmfile.'.pdf';
		
		// $ObjectFactory = New COM("CrystalRuntime.Application.8.5") or die("Unable to Create Object");;
		// $creport = $ObjectFactory->OpenReport($cr_report, 1);
		
		// $creport->Database->Tables(1)->SetLogOnInfo('aster-mayestik', 'aster-mayestik', 'sa', 'rgold');
		
		// //- field prompt or else report will hang - to get through
		// $creport->EnableParameterPrompting = 0;
		
		// //- DiscardSavedData - to refresh then read records
		// $creport->DiscardSavedData;
		// $creport->ReadRecords();
		
		// $creport->ExportOptions->DiskFileName = $exp_pdf;
		// $creport->ExportOptions->PDFExportAllPages = true;
		// $creport->ExportOptions->DestinationType = 1;
		// $creport->ExportOptions->FormatType = 31;
		// $creport->Export(false);
		
		// $len = filesize($exp_pdf);
		// header("Content-type: application/pdf");
		// header("Content-Length: $len");
		// header("Content-Disposition: inline; filename=$exp_pdf");
		// readfile($exp_pdf);
		
		$my_report = 'C:\\xampp\\htdocs\\asterweb\\assets\\docs\\'.$nmfile.'.rpt'; // rpt source file
		$my_pdf = 'C:\\xampp\\htdocs\\asterweb\\assets\\docs\\'.$nmfile.'.rpt'; // RPT export to pdf file
		//-Create new COM object-depends on your Crystal Report version
		$ObjectFactory = new COM("CrystalRuntime.Application.8.5") or die ("Error on load"); // call COM port
		// $crapp = $ObjectFactory-> CreateObject("CrystalDesignRunTime.Application"); // create an instance for Crystal
		$creport = $ObjectFactory->OpenReport($my_report, 1); // call rpt report
		
		// to refresh data before
		
		//- Set database logon info - must have
		// $creport->Database->Tables(1)->SetLogOnInfo("192.168.3.98", "aster-mayestik", "sa", "rgold");
		
		//- field prompt or else report will hang - to get through
		$creport->EnableParameterPrompting = 0;
		
		//- DiscardSavedData - to refresh then read records
		$creport->DiscardSavedData;
		$creport->ReadRecords();
		
		
		//export to PDF process
		$creport->ExportOptions->DiskFileName = $my_pdf; //export to pdf
		$creport->ExportOptions->PDFExportAllPages = true;
		$creport->ExportOptions->DestinationType = 1; // export to file
		$creport->ExportOptions->FormatType = 31; // PDF type
		$creport->Export(false);
		
		//------ Release the variables ------
		$creport = null;
		$crapp = null;
		$ObjectFactory = null;
	}
	
	function printfidusia()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tkwitansifidusia';
		$nmfile = 'kwitansifidusia-'.$jamskg;
		$xIP = trim($this->session->userdata('ip_address'));
		$gIP = trim($this->session->userdata('gIP'));
		
		$oReader = PHPExcel_IOFactory::createReader('Excel5');
		
		if (trim($xIP) == trim($gIP))
		{
			$xtPath = '/var/www/'.$this->config->item('base_folder').'/assets/docs/';
			$xPath = '/var/www/'.$this->config->item('base_folder').'/temp/';
		}
		else
		{
			$xtPath = APPPATH.'../assets/docs/';
			$xPath = APPPATH.'../temp/';
		}
		
		$oExcel = $oReader->load($xtPath.$nmfiletemp.'.xls');
		
		$oExcel->getActiveSheet()->setTitle('Kwitansi Fidusia');
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
		
		$trx = 'JL';
		$refno = trim($this->input->post('fs_refno'));
		$docno = trim($this->input->post('fs_refno'));
		$cust = trim($this->input->post('fs_refno'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->jual_unit_all($trx,$refno,$docno,$cust);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			
			$FakturNumber = $ssql->fs_refno;
			$CustomerName = $ssql->fs_nm_cussup;
			$PaymentName = $ssql->fs_nm_payment;
			$CustomerAddr = $ssql->fs_addr;
			$TypeName = $ssql->fs_nm_product;
			$ChassisNumber = $ssql->fs_rangka;
			$MachineNumber = $ssql->fs_mesin;
			$UnitPrice = $ssql->fn_unitprice;
			$Discount = $ssql->fn_disc;
			$Total = $ssql->fn_unitprice;
			
			$this->load->model('mMainModul','',true);
			$say = $this->mMainModul->terbilang($Total,4);
			
			$UserName = trim($this->session->userdata('gUser'));
			
			$pjghal = 57;
			$i = 1;
			$l = 0; // no urut
			
			$k = 1; // pjg hal
			$p = 1; // awal hal
			
			$i++;
			$k++;
			$sel->setCellValue('A'.$i, 'KWITANSI FIDUSIA');
			$sel->mergeCells('A'.$i.':H'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$sel->getStyle('A'.$i)->getFont()->setSize(12);
			$sel->getRowDimension($i)->setRowHeight(-1);
			$i++;
			$k++;
			
			$sel->mergeCells('A'.$i.':H'.$i);
			$sel->setCellValue('A'.$i, $FakturNumber);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i = $i + 5;
			$k = $k + 5;
			
			$sel = $oExcel->getActiveSheet();
			
			$sel->setCellValue('A'.$i, 'Telah diterima dari');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $PaymentName);
			$sel->mergeCells('C'.$i.':H'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Uang sejumlah');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $say);
			$sel->mergeCells('C'.$i.':H'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Untuk keperluan');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, 'Pembayaran pelunasan 1 Unit kendaraan '.$TypeName);
			$sel->mergeCells('C'.$i.':H'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('C'.$i, 'a/n: '.$CustomerName);
			$sel->mergeCells('C'.$i.':D'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('C'.$i, 'No Rangka: '.$ChassisNumber);
			$sel->mergeCells('C'.$i.':D'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('C'.$i, 'No Mesin: '.$MachineNumber);
			$sel->mergeCells('C'.$i.':H'.$i);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i,'# Rp '.number_format($Total).'.- #');
			// $sel->getRowDimension($i)->setRowHeight(-1);
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
			$i = $i + 20;
			$k = $k + 20;			
				
			$sel->setCellValue('E'.$i,'...............');	
			
			// $sel->getProtection()->setPassword('wismaamg3000');
			// $sel->getProtection()->setSheet(true);
			// $sel->getStyle('A1:H25')->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_PROTECTED);
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
			$hasil = array('sukses' => false, 'hasil' => 'No record!!');
			echo json_encode($hasil);
		}
	}
	
	function printsi()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tsalesinvoice';
		$nmfile = 'salesinvoice-'.$jamskg;
		$xIP = trim($this->session->userdata('ip_address'));
		$gIP = trim($this->session->userdata('gIP'));
		
		$oReader = PHPExcel_IOFactory::createReader('Excel5');
		
		if (trim($xIP) == trim($gIP))
		{
			$xtPath = '/var/www/'.$this->config->item('base_folder').'/assets/docs/';
			$xPath = '/var/www/'.$this->config->item('base_folder').'/temp/';
		}
		else
		{
			$xtPath = APPPATH.'../assets/docs/';
			$xPath = APPPATH.'../temp/';
		}
		
		$oExcel = $oReader->load($xtPath.$nmfiletemp.'.xls');
		
		$oExcel->getActiveSheet()->setTitle('Sales Invoice Leasing');
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
		
		$trx = 'JL';
		$refno = trim($this->input->post('fs_refno'));
		$docno = trim($this->input->post('fs_refno'));
		$cust = trim($this->input->post('fs_refno'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->jual_unit_all($trx,$refno,$docno,$cust);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			
			$FakturNumber = $ssql->fs_refno;
			$PaymentName = $ssql->fs_nm_payment;
			$CustomerName = $ssql->fs_nm_cussup;
			$CustomerAddr = $ssql->fs_addr;
			$TypeName = $ssql->fs_nm_product;
			$Color = $ssql->fs_nm_warna;
			$Year = $ssql->fs_thn;
			$ChassisNumber = $ssql->fs_rangka;
			$MachineNumber = $ssql->fs_mesin;
			$UnitPrice = $ssql->fn_unitprice;
			$Discount = $ssql->fn_disc;
			$Term = $ssql->fs_nm_term;
			$InstallmentPerMonth = $ssql->fn_install;
			$Total = $ssql->fn_total;
			$DPValue = $ssql->fn_dp;
			$DPMax = $ssql->fn_dpmax;
			$LeasingDisc = $ssql->fn_subsidi;
			
			$this->load->model('mMainModul','',true);
			$say = $this->mMainModul->terbilang($Total,4);
			
			$UserName = trim($this->session->userdata('gUser'));
			
			$pjghal = 57;
			$i = 1;
			$l = 0; // no urut
			
			$k = 1; // pjg hal
			$p = 1; // awal hal
			
			$i++;
			$k++;
			$sel->setCellValue('A'.$i, 'KWITANSI TAGIHAN LEASING');
			$sel->mergeCells('A'.$i.':G'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$sel->getStyle('A'.$i)->getFont()->setSize(12);
			$i++;
			$k++;
			
			$sel = $oExcel->getActiveSheet();
			$sel->setCellValue('A'.$i, $FakturNumber);
			$sel->mergeCells('A'.$i.':G'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i = $i + 5;
			$k = $k + 5;
			
			$sel->setCellValue('A'.$i, 'Telah diterima dari');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $CustomerName);
			$sel->mergeCells('C'.$i.':G'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Uang Sejumlah');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i,'# '.$say. ' #');
			$sel->mergeCells('C'.$i.':G'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Untuk Keperluan');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, 'Pencairan Leasing 1 Unit Kendaraan '.$TypeName.' a/n '.$CustomerName);
			$sel->getStyle('C'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$sel->getStyle('C'.$i)->getAlignment()->setWrapText(true);
			$j = $i + 2;
			$sel->mergeCells('C'.$i.':G'.$j);
			$i = $i + 3;
			$k = $k + 3;
			
			$sel->setCellValue('A'.$i, 'Rangka');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $ChassisNumber);
			
			$sel->setCellValue('E'.$i, 'Warna');
			$sel->setCellValue('F'.$i, ':');
			$sel->setCellValue('G'.$i, $Color);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Mesin');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $MachineNumber);
			
			$sel->setCellValue('E'.$i, 'Tahun');
			$sel->setCellValue('F'.$i, ':');
			$sel->setCellValue('G'.$i, $Year);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Harga');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, number_format($UnitPrice));
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$sel->setCellValue('E'.$i, 'DP Maximum');
			$sel->setCellValue('F'.$i, ':');
			$sel->setCellValue('G'.$i, number_format($DPMax));
			$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Discount');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, number_format($Discount));
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$sel->setCellValue('E'.$i, 'Subsidi');
			$sel->setCellValue('F'.$i, ':');
			$sel->setCellValue('G'.$i, number_format($LeasingDisc));
			$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'DP');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, number_format($DPValue));
			$sel->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$sel->setCellValue('E'.$i, 'Cicilan');
			$sel->setCellValue('F'.$i, ':');
			$sel->setCellValue('G'.$i, number_format($InstallmentPerMonth));
			$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$i++;
			$k++;
			
			$sel->setCellValue('E'.$i, 'Tenor');
			$sel->setCellValue('F'.$i, ':');
			$sel->setCellValue('G'.$i, $Term.' bulan');
			$sel->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$i = $i + 5;
			$k = $k + 5;
			
			$sel->setCellValue('A'.$i, '# Rp. '.number_format($Total).',- #');
			$sel->mergeCells('A'.$i.':C'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			// $sel->getStyle('A'.$i)->getFont()->setBold(true);
			// $sel->getStyle('A'.$i)->getFont()->setSize(12);
			$sel->mergeCells('A'.$i.':C'.$i);
			$sel->getStyle('A'.$i.':C'.$i)->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('argb' => 'H0C0C0C0')
					)
				)
			);
			$i = $i + 20;
			$k = $k + 20;
			
			$this->load->model('mMainModul','',true);
			$xTglPrint = date('d-m-Y');
			$xTglPrint = substr($xTglPrint,0,2).' '.$this->mMainModul->ambilbulan(substr($xTglPrint,3,2)).' '.substr($xTglPrint,6,4);
			$sel->setCellValue('G'.$i,trim($this->session->userdata('gKota')).', '.trim($xTglPrint));
			$i++;
			$k++;
			
			$sel->setCellValue('G'.$i,'Diterima Oleh');
			$i = $i + 20;
			$k = $k + 20;			
			
			$sel->setCellValue('G'.$i,'...............');	
			
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
			$hasil = array('sukses' => false, 'hasil' => 'No record!!');
			echo json_encode($hasil);
		}
	}
	
	function printsrtpernyataan()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'tsuratpernyataan';
		$nmfile = 'suratpernyataan-'.$jamskg;
		$xIP = trim($this->session->userdata('ip_address'));
		$gIP = trim($this->session->userdata('gIP'));
		
		$oReader = PHPExcel_IOFactory::createReader('Excel5');
		
		if (trim($xIP) == trim($gIP))
		{
			$xtPath = '/var/www/'.$this->config->item('base_folder').'/assets/docs/';
			$xPath = '/var/www/'.$this->config->item('base_folder').'/temp/';
		}
		else
		{
			$xtPath = APPPATH.'../assets/docs/';
			$xPath = APPPATH.'../temp/';
		}
		
		$oExcel = $oReader->load($xtPath.$nmfiletemp.'.xls');
		
		$oExcel->getActiveSheet()->setTitle('Surat Pernyataan');
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
		
		$trx = 'JL';
		$refno = trim($this->input->post('fs_refno'));
		$docno = trim($this->input->post('fs_refno'));
		$cust = trim($this->input->post('fs_refno'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->jual_unit_all($trx,$refno,$docno,$cust);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			
			$FakturNumber = $ssql->fs_refno;
			$CustomerName = $ssql->fs_nm_cussup;
			$PaymentName = $ssql->fs_nm_payment;
			$CustomerAddr = $ssql->fs_addr;
			$TypeName = $ssql->fs_nm_product;
			$ChassisNumber = $ssql->fs_rangka;
			$MachineNumber = $ssql->fs_mesin;
			$Color = $ssql->fs_nm_warna;
			$Year = $ssql->fs_thn;
			$Cabang = $ssql->Cabang;
			$AlamatCabang = $ssql->AlamatCabang;
			$sNamaSTNK = trim($this->input->post('fs_nama'));
			$sNamaMan = trim($this->input->post('fs_man'));
			
			$this->load->model('mJualUnit','',true);
			$this->mJualUnit->update_alias($ChassisNumber,$MachineNumber,$sNamaSTNK);
			
			$UserName = trim($this->session->userdata('gUser'));
			
			$pjghal = 57;
			$i = 1;
			$l = 0; // no urut
			
			$k = 1; // pjg hal
			$p = 1; // awal hal
			
			$i++;
			$k++;
			$sel->setCellValue('A'.$i, 'SURAT PERNYATAAN');
			$sel->mergeCells('A'.$i.':G'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$sel->getStyle('A'.$i)->getFont()->setSize(12);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'Yang telah bertanda tangan di bawah ini');
			$sel->mergeCells('A'.$i.':G'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Nama');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $sNamaMan);
			$sel->mergeCells('C'.$i.':G'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Alamat');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $AlamatCabang);
			$sel->mergeCells('C'.$i.':G'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i, 'Jabatan');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, 'Branch Manager');
			$sel->mergeCells('C'.$i.':G'.$i);
			$i++;
			$k++;
						
			$sel->setCellValue('A'.$i, 'Nama Dealer');
			$sel->setCellValue('B'.$i, ':');
			$sel->setCellValue('C'.$i, $Cabang);
			$sel->mergeCells('C'.$i.':G'.$i);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'Nama');
			$sel->setCellValue('B'.$i, 'Tipe');
			$sel->setCellValue('D'.$i, 'Tahun');
			$sel->setCellValue('E'.$i, 'Warna');
			$sel->setCellValue('F'.$i, 'No Rangka');
			$sel->setCellValue('G'.$i, 'No Mesin');
			$sel->getStyle('A'.$i.':G'.$i)->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('argb' => 'H0C0C0C0')
					)
				)
			);
			$sel->getStyle('A'.$i.':G'.$i)->applyFromArray(
				array(
					'borders' => array(
						'top' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$sel->getStyle('A'.$i.':G'.$i)->applyFromArray(
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
			
			if (trim($sNamaSTNK) <> '')
			{
				$sel->setCellValue('A'.$i, $sNamaSTNK );
			}
			else
			{
				$sel->setCellValue('A'.$i, $CustomerName );
			}
			$sel->setCellValue('B'.$i, $TypeName);
			$sel->setCellValue('D'.$i, $Year);
			$sel->setCellValue('E'.$i, $Color);
			$sel->setCellValue('F'.$i, $ChassisNumber);
			$sel->setCellValue('G'.$i, $MachineNumber);
			$sel->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$sel->getStyle('B'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$sel->getStyle('D'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$sel->getStyle('E'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$sel->getStyle('F'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$sel->getStyle('G'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$sel->getStyle('A'.$i)->getAlignment()->setWrapText(true);
			$sel->getStyle('B'.$i)->getAlignment()->setWrapText(true);
			$sel->getStyle('D'.$i)->getAlignment()->setWrapText(true);
			$sel->getStyle('E'.$i)->getAlignment()->setWrapText(true);
			$sel->getStyle('F'.$i)->getAlignment()->setWrapText(true);
			$sel->getStyle('G'.$i)->getAlignment()->setWrapText(true);
			$i++;
			$j = $i - 1;
			$sel->mergeCells('A'.$j.':A'.$i);
			$sel->mergeCells('B'.$j.':C'.$i);
			$sel->mergeCells('D'.$j.':D'.$i);
			$sel->mergeCells('E'.$j.':E'.$i);
			$sel->mergeCells('F'.$j.':F'.$i);
			$sel->mergeCells('G'.$j.':G'.$i);
			$sel->getStyle('A'.$i.':G'.$i)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$arr = array(
				'borders' => array(
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$j = $i - 2;
			$sel->getStyle('A'.$j.':A'.$i)->applyFromArray($arr);
			$sel->getStyle('B'.$j.':B'.$i)->applyFromArray($arr);
			$sel->getStyle('D'.$j.':D'.$i)->applyFromArray($arr);
			$sel->getStyle('E'.$j.':E'.$i)->applyFromArray($arr);
			$sel->getStyle('F'.$j.':F'.$i)->applyFromArray($arr);
			$sel->getStyle('G'.$j.':G'.$i)->applyFromArray($arr);
			$sel->getStyle('G'.$j.':G'.$i)->applyFromArray(
				array(
					'borders' => array(
						'right' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'BPKB tersebut diatas akan kami serahkan ke PT.Mandiri Finance Indonesia selambat-lambatnya dalam jangka waktu 3 (tiga) bulan dari tanggal tersebut di atas.');
			$sel->getStyle('A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$sel->getStyle('A'.$i)->getAlignment()->setWrapText(true);
			$j = $i + 1;
			$sel->mergeCells('A'.$i.':G'.$j);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i, 'Demikian surat pernyataan ini kami buat, atas perhatian dan kerjasamanya kami ucapkan terima kasih.');
			$sel->mergeCells('A'.$i.':G'.$i);
			$i = $i + 5;
			$k = $k + 5;
			
			$this->load->model('mMainModul','',true);
			$xTglPrint = date('d-m-Y');
			$xTglPrint = substr($xTglPrint,0,2).' '.$this->mMainModul->ambilbulan(substr($xTglPrint,3,2)).' '.substr($xTglPrint,6,4);
			$sel->setCellValue('F'.$i,trim($this->session->userdata('gKota')).', '.trim($xTglPrint));
			$sel->mergeCells('F'.$i.':G'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('F'.$i,'Diterima Oleh');
			$i = $i + 20;
			$k = $k + 20;			
				
			$sel->setCellValue('F'.$i,'...............');	
			
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
			$hasil = array('sukses' => false, 'hasil' => 'No record!!');
			echo json_encode($hasil);
		}
	}
	
	function printsrttandaterima()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'ttandaterima';
		$nmfile = 'tandaterima-'.$jamskg;
		$xIP = trim($this->session->userdata('ip_address'));
		$gIP = trim($this->session->userdata('gIP'));
		
		$oReader = PHPExcel_IOFactory::createReader('Excel5');
		
		if (trim($xIP) == trim($gIP))
		{
			$xtPath = '/var/www/'.$this->config->item('base_folder').'/assets/docs/';
			$xPath = '/var/www/'.$this->config->item('base_folder').'/temp/';
		}
		else
		{
			$xtPath = APPPATH.'../assets/docs/';
			$xPath = APPPATH.'../temp/';
		}
		
		$oExcel = $oReader->load($xtPath.$nmfiletemp.'.xls');
		
		$oExcel->getActiveSheet()->setTitle('Surat Tanda Terima');
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
		
		$trx = 'JL';
		$refno = trim($this->input->post('fs_refno'));
		$docno = trim($this->input->post('fs_refno'));
		$cust = trim($this->input->post('fs_refno'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->jual_unit_all($trx,$refno,$docno,$cust);
		
		if ($ssql->num_rows() > 0)
		{
			$ssql = $ssql->row();
			
			$FakturNumber = $ssql->fs_refno;
			$CustomerName = $ssql->fs_nm_cussup;
			$PaymentName = $ssql->fs_nm_payment;
			$CustomerAddr = $ssql->fs_addr;
			$TypeName = $ssql->fs_nm_product;
			$ChassisNumber = $ssql->fs_rangka;
			$MachineNumber = $ssql->fs_mesin;
			$Color = $ssql->fs_nm_warna;
			$Year = $ssql->fs_thn;
			$Cabang = $ssql->Cabang;
			$AlamatCabang = $ssql->AlamatCabang;
			
			$UserName = trim($this->session->userdata('gUser'));
			
			$pjghal = 57;
			$i = 1;
			$l = 0; // no urut
			
			$k = 1; // pjg hal
			$p = 1; // awal hal
			
			
			$i++;
			$k++;
			$sel->setCellValue('A'.$i,'Tanda Terima');
			$sel->mergeCells('A'.$i.':D'.$i);
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sel->getStyle('A'.$i)->getFont()->setBold(true);
			$sel->getStyle('A'.$i)->getFont()->setSize(12);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i,'Telah diterima dengan baik dari : '.$Cabang);
			$sel->mergeCells('A'.$i.':D'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('A'.$i,'sejumlah 1 berkas untuk diurus pencairan kredit yang didalamnya berisi antara lain :');
			$sel->mergeCells('A'.$i.':D'.$i);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('B'.$i,'1. Kwitansi Penagihan');
			$sel->mergeCells('B'.$i.':D'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('B'.$i,'2. Surat Pernyataan BPKB');
			$sel->mergeCells('B'.$i.':D'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('B'.$i,'3. Kertas Gesek No. Rangka dan No. Mesin');
			$sel->mergeCells('B'.$i.':D'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('B'.$i,'4. Surat Perintah Pengiriman');
			$sel->mergeCells('B'.$i.':D'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('B'.$i,'5. Surat Pemesanan Kendaraan');
			$sel->mergeCells('B'.$i.':D'.$i);
			$i++;
			$k++;
			
			$sel->setCellValue('B'.$i,'6. Lain - lain : Kunci Kontak');
			$sel->mergeCells('B'.$i.':D'.$i);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i,'Atas Nama :');
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i++;
			$k++;
			
			$sel->setCellValue('B'.$i,$CustomerName);
			$sel->mergeCells('B'.$i.':D'.$i);
			$i = $i + 2;
			$k = $k + 2;
			
			$sel->setCellValue('A'.$i,'Penerima');
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->mergeCells('A'.$i.':B'.$i);
			
			$this->load->model('mMainModul','',true);
			$xTglPrint = date('d-m-Y');
			$xTglPrint = substr($xTglPrint,0,2).' '.$this->mMainModul->ambilbulan(substr($xTglPrint,3,2)).' '.substr($xTglPrint,6,4);
			$sel->setCellValue('D'.$i,trim($this->session->userdata('gKota')).', '.trim($xTglPrint));
			$i++;
			$k++;
			
			$sel->setCellValue('D'.$i,'Yang menyerahkan');
			$i = $i + 20;
			$k = $k + 20;
				
			$sel->setCellValue('A'.$i,'...............');	
			$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sel->mergeCells('A'.$i.':B'.$i);
			$sel->setCellValue('D'.$i,'...............');	
			
			
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
			$hasil = array('sukses' => false, 'hasil' => 'No record!!');
			echo json_encode($hasil);
		}
	}
}
?>