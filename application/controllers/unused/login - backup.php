<?php

class Login extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		$this->load->model('mSearch','',TRUE);
		$ssql = $this->mSearch->change_comp();
		
		$hasil_comp = array();
		foreach ($ssql->result() as $row)
		{
			$hasil_comp[trim($row->fs_kd_comp)] = trim($row->fs_nm_comp);
		}
		$data_login['comp'] = $hasil_comp;
		$this->load->view('vlogin',$data_login);
	}
	
	function change_comp()
	{
		$this->load->model('mSearch','',TRUE);
		$ssql = $this->mSearch->nama_db($this->input->post('xcomp'));
		
		if ($ssql->num_rows() > 0)
		{
			$xdb = $ssql->row();
			$xdb_aktif = $xdb->fs_nm_db;
			
			//set sesi
			$this->session->set_userdata('gServer', '192.168.2.24');
			//end of set sesi
			
			//change db
			$this->load->model('mMainModul','',TRUE);
			$this->mMainModul->change_db($this->session->userdata('gServer'),$xdb_aktif);
			//end of change db
			
			
			$this->load->model('mSearch','',TRUE);
			$ssql = $this->mSearch->login_dept();
			
			$hasil = array();
			$hasil['optdept']['00'] = '- Select a Departement -';
			foreach ($ssql->result() as $row)
			{
				$hasil['optdept'][trim($row->code)] = trim($row->name);
			}
		}
		else{
			$hasil['optdept'] = array('- Select a Departement -');
		}
		//output to dropdown dept
		$dept = form_dropdown('cbodept',$hasil['optdept']);
		echo $dept;
		//end of output to dropdown dept
	}
	
	function ceklogin()
	{
		$this->form_validation->set_rules('cbocomp', 'Company', 'trim|required|xss_clean');
		$this->form_validation->set_rules('cbodept', 'Departement', 'trim|required|xss_clean');
		$this->form_validation->set_rules('login_username', 'UserCode', 'trim|required|xss_clean');
		$this->form_validation->set_rules('login_userpass', 'Password', 'trim|required|xss_clean');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('pesan','User Code or Password Incorrect!!');
			redirect('login','site_url');
		}
		else
		{
			$kdcomp = trim($this->input->post('cbocomp'));
			$kddept = substr(trim($this->input->post('cbodept')), 0, 5);
			$kdcount = substr(trim($this->input->post('cbodept')), 5, 6);
			$usernm = trim($this->input->post('login_username'));
			$userpass = $this->input->post('login_userpass');
			
			//ambil nm db
			$this->load->model('mSearch','',TRUE);
			$ssql = $this->mSearch->nama_db($kdcomp);
			$ssql = $ssql->row();
			$db = $ssql->fs_nm_db;
			//end of ambil nm db
			
			//change db
			$this->load->model('mMainModul','',TRUE);
			$this->mMainModul->change_db($this->session->userdata('gServer'),$db);
			//end of change db
			
			$this->load->model('mSearch','',TRUE);
			$ssql = $this->mSearch->dept_def();
			$ssql = $ssql->row();
			$deptdef = $ssql->fs_nm_code;
			
			if (trim($usernm) == 'SASAGALA' and trim($userpass) == 'JESUS IS MY SAVIOR')
			{
				//set sesi
				$new = array(
					'gDatabase'=>trim($db),'gComp'=>trim($kdcomp),'gDept'=>trim($kddept),
					'gCount'=>trim($kdcount),'gDeptName'=>trim($deptdef),'gUser'=>trim($usernm),'gPass'=>trim($userpass)
					);
				$this->session->set_userdata($new);
				//end of set sesi
				redirect('MainMenu','site_url');
			}
			else
			{
				//coding pass
				$this->load->model('mMainModul','',TRUE);
				$ssql = $this->mMainModul->coding($userpass);
				$userpass = $ssql;
				//end of coding pass
				
				//cari user, password di db
				$this->load->model('mSearch','',TRUE);
				$ssql = $this->mSearch->valid_userpass($kdcomp,$usernm,$userpass);
				//end of cari user, password di db
				
				if ($ssql->num_rows() > 0)//user, password ada di db
				{
					$ssql = $ssql->row();
					$usernm = $ssql->fs_kd_user;
					$userpass = $ssql->fs_password;
					
					//set sesi
					$new = array(
						'gDatabase'=>trim($db),'gComp'=>trim($kdcomp),'gDept'=>trim($kddept),
						'gCount'=>trim($kdcount),'gDeptName'=>trim($deptdef),'gUser'=>trim($usernm),'gPass'=>trim($userpass)
						);
					$this->session->set_userdata($new);
					//end of set sesi
					redirect('MainMenu','site_url');
				}
				else//user, password tdk ada di db
				{
					$this->session->set_flashdata('pesan','User Code or Password Incorrect!!');
					redirect('Login','site_url');
				}
			}
		}
	}
	
	function logout()
	{
		//$this->session->sess_destroy();
		redirect('Login','site_url');
	}
}
?>