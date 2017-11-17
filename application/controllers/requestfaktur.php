<?php

class RequestFaktur extends CI_Controller
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
			$this->load->view('vrequestfaktur');
		}
		else
		{
			redirect('','refresh');
		}
	}

	function grid_detail()
	{
		$nstart = trim($this->input->post('start'));
		$refno = trim($this->input->post('fs_refno'));
		$kdtrx = '5800';
		$kdstrx = '0100';
		
		$this->load->model('mRequestFaktur','',true);
		$ssql = $this->mRequestFaktur->grid($refno,$kdtrx,$kdstrx);
		
		echo json_encode($ssql->result());
	}

	function grid_detail2()
	{
		$nstart = trim($this->input->post('start'));
		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mRequestFaktur','',true);
		$ssql = $this->mRequestFaktur->grid2_all($cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mRequestFaktur->grid2($cari,$nstart);
		
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
		$kdtrx = '5800';
		$kdstrx = '0100';
		
		$this->load->model('mRequestFaktur','',true);
		$ssql = $this->mRequestFaktur->faktur_request_all($refno,$kdtrx,$kdstrx,$cust,$rangka,$mesin,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mRequestFaktur->faktur_request($refno,$kdtrx,$kdstrx,$cust,$rangka,$mesin,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}

	function griddetil()
	{
		$nstart = trim($this->input->post('start'));
		$refno = trim($this->input->post('fs_refno'));
		$cust = trim($this->input->post('fs_nm_cust'));
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		$cari = trim($this->input->post('fs_cari'));
		$kdtrx = '5800';
		$kdstrx = '0100';
		
		$this->load->model('mRequestFaktur','',true);
		$ssql = $this->mRequestFaktur->faktur_request_all($refno,$kdtrx,$kdstrx,$cust,$rangka,$mesin,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mRequestFaktur->faktur_request($refno,$kdtrx,$kdstrx,$cust,$rangka,$mesin,$nstart,$cari);
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}


	function ceksave()
	{
		$refno = trim($this->input->post('fs_refno'));
		$kdtrx = '5800';
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
			$this->load->model('mRequestFaktur','',true);
			$ssql = $this->mRequestFaktur->cek_refno($kdtrx,$kdstrx,$refno);
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

		$kddept = $this->session->userdata('gDept');
		$kdcount = $this->session->userdata('gCount');
		$kdtrx = '5800';
		$kdstrx = '0100';
		
		$xupdate = false;
		$this->load->model('mRequestFaktur','',true);
		$ssql = $this->mRequestFaktur->cek_refno($kdtrx,$kdstrx,$refno);

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
				//var_dump($refno);
			}
			else
			{
				$refno = '';
			}
		}
		//var_dump($xupdate);
		$dt1 = array(
			'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
			'fs_kd_trx'		=> trim($kdtrx),
			'fs_kd_strx'	=> trim($kdstrx),
			'fs_refno'		=> trim($refno),
			
			'fd_refno'		=> trim($refnodt),
			'fs_docno'		=> trim($docno),
			'fd_docno'		=> trim($docnodt),
			'fb_delete'		=> '0'
		);
		
		if ($xupdate == false)
		{
			$dt2 = array(
				'fs_kd_dept'	=> trim($this->session->userdata('gDept')),
				//taruh disini krn jika simpan ulang & trs tdk di load sesuai dept ketika simpan pertama
				'fs_count'		=> trim($this->session->userdata('gCount')),
				'fs_usrcrt'		=> trim($this->session->userdata('gUser')),
				'fd_usrcrt'		=> trim(date('Y-m-d H:i:s')),
				'fs_upddt'		=> trim($this->session->userdata('gUser')),
				'fd_upddt'		=> trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt1, $dt2);
			$this->db->insert('tx_trxrequestfaktur', $data);
		}
		else 
		{
			$dt2 = array(
				'fs_upddt'	=> trim($this->session->userdata('gUser')),
				'fd_upddt'	=> trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt1, $dt2);
			$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
						AND	fs_refno = '".trim($refno)."'";
			$this->db->where($where);
			$this->db->update('tx_trxrequestfaktur', $data);
		}

		$this->load->model('mRequestFaktur','',true);
		$ssql = $this->mRequestFaktur->hapus_isi($kdtrx,$kdstrx,$refno);

		//hapus detail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND	fs_refno = '".trim($refno)."'";

		$this->db->where($where);
		$this->db->delete('tx_trxrequestfakturd');
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

		$jml = count($rangka) - 1;
		if ($jml != 0)
		{
			for ($i=1; $i<=$jml; $i++)
			{
				/*if (trim($stnkdt[$i]) <> '')
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
				}*/
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
					//'fs_flag2'			=> trim($xflag2),
					//'fs_flag3'			=> trim($xflag3),
					
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
				$this->db->insert('tx_trxrequestfakturd', $data);
			}
		}
		//eof simpan detail

		$this->load->model('mRequestFaktur','',true);
		$ssql = $this->mRequestFaktur->update_isi($kdtrx,$kdstrx,$refno);
		//dept tx_trxbirojsd perlu diupdate krn klo simpan ulang, dept ikut dept login

		if ($xupdate == false)
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Request Faktur Success',
				'refno'		=> $refno
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Request Faktur Update Success',
				'refno'		=> $refno
			);
			echo json_encode($hasil);
		}
	}


	function cekremove()
	{
		$refno = trim($this->input->post('fs_refno'));
		$kdtrx = '5800';
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
			$this->load->model('mRequestFaktur','',true);
			$ssql = $this->mRequestFaktur->cek_refno($kdtrx,$kdstrx,$refno);

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
		$kdtrx = '5800';
		$kdstrx = '0100';

		$this->load->model('mRequestFaktur','',true);
		$ssql = $this->mRequestFaktur->hapus_isi($kdtrx,$kdstrx,$refno);
		//hapus detail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_TrxRequestFakturD');
		//eof hapus detail
		
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_kd_dept = '".trim($this->session->userdata('gDept'))."'
					AND	fs_count = '".trim($this->session->userdata('gCount'))."'
					AND	fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_TrxRequestFaktur');
		
		$hasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Remove Request Faktur reference number: "'.$refno.'" success'
		);
		echo json_encode($hasil);
	}

}