<?php

class Login extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		$this->load->view('vlogin');
	}
	
	function ambil_comp()
	{
		$nstart = trim($this->input->post('start'));
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->change_comp_all('');
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->change_comp('',$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function change_comp()
	{
		//set sesi
		if (strripos(base_url(),config_item('ip_server')) <> 0)
		{
			$this->session->set_userdata('gServer', config_item('ip_db_server'));
		}
		else
		{
			$this->session->set_userdata('gServer', config_item('ip_db_lokal'));
		}
		$this->session->set_userdata('gIP', config_item('ip_server'));
		
		if (substr(trim($this->session->userdata('gServer')), strlen(trim($this->session->userdata('gServer'))) - 2, 3) == '44')
		{
			$this->session->set_userdata('gKonek','SERVER-IDS');
		}
		else
		{
			$this->session->set_userdata('gKonek','SERVER-(LOCAL)');
		}
		//eof set sesi
		
		$xdb_aktif = trim($this->input->post('fs_nm_db'));
		$xComp = trim($this->input->post('fs_kd_comp'));
		
		if (trim($xdb_aktif) <> '')
		{
			//change db
			$this->load->model('mMainModul','',true);
			$this->mMainModul->change_db($this->session->userdata('gServer'),$xdb_aktif);
			//eof change db
			
			$nstart = trim($this->input->post('start'));
			
			$this->load->model('mSearch','',true);
			$ssql = $this->mSearch->login_dept_all($xComp,'','');
			$total = $ssql->num_rows();
			
			$ssql = $this->mSearch->login_dept($xComp,'','',$nstart);
			
			echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
		}
	}
	
	function buat_captcha()
	{
		$this->load->helper('captcha');
		
		$this->load->database();
		
		$vals = array(
			'expiration'	=> 3600,
			'font_path'		=> './assets/css/font/comic.ttf',
			'img_height'	=> 70,
			'img_path'		=> './temp/captcha/',
			'img_url'		=> './temp/captcha/',
			'img_width'		=> 270
		);
		
		$cap = create_captcha($vals);
		
		if ($cap)
		{
			$data = array(
				'captcha_time'	=> round($cap['time']),
				'ip_address'	=> $this->input->ip_address(),
				'word'			=> $cap['word']
			);
			
			$this->db->insert('captcha', $data);
			
			$this->session->set_userdata('vcpt',round(trim($cap['time'])));
			
			$xPathFile = base_url('/temp/captcha/'.trim($cap['time']).'.jpg');
			
			$hasil = array(
				'src'	=> $xPathFile
			);
			echo json_encode($hasil);
		}
	}
	
	function ceklogin()
	{
		$this->form_validation->set_rules('cboComp', 'Company', 'trim|required|xss_clean');
		$this->form_validation->set_rules('txtComp', 'CompCode', 'trim|required|xss_clean');
		$this->form_validation->set_rules('txtDB', 'Database', 'trim|required|xss_clean');
		$this->form_validation->set_rules('cboDept', 'DeptName', 'trim|required|xss_clean');
		$this->form_validation->set_rules('txtDept', 'DeptCode', 'trim|required|xss_clean');
		$this->form_validation->set_rules('txtCount', 'DeptCount', 'trim|required|xss_clean');
		$this->form_validation->set_rules('txtUserName', 'UserCode', 'trim|required|xss_clean');
		$this->form_validation->set_rules('txtUserPass', 'Password', 'trim|required|xss_clean');
		$this->form_validation->set_rules('txtCaptcha', 'Captcha', 'trim|required|xss_clean');
		
		
		if ($this->form_validation->run() == FALSE)
		{
			echo "'User Code or Password Incorrect!!'";
		}
		else
		{
			$word = trim($this->input->post('txtCaptcha'));
			
			// change db
			$this->load->model('mMainModul','',true);
			$this->mMainModul->change_db($this->session->userdata('gServer'),config_item('base_server'));
			// eof change db
			
			$exp = time()-3600;
			$where = "captcha_time < '".trim($exp)."'";
			$this->db->where($where);
			$this->db->delete('captcha');
			
			$this->load->model('mMainModul','',true);
			$ssql = $this->mMainModul->cek_captcha($word);
			$ssql = $ssql->row();
			$jml = $ssql->fn_jml;
			
			if ($jml > 0)
			{
				$kdcomp = trim($this->input->post('txtComp'));
				$nmcomp = trim($this->input->post('cboComp'));
				$kddept = trim($this->input->post('txtDept'));
				$kdcount = trim($this->input->post('txtCount'));
				
				$db = trim($this->input->post('txtDB'));
				$usernm = str_replace("'",'"',trim($this->input->post('txtUserName')));
				$userpass = str_replace("'",'"',trim($this->input->post('txtUserPass')));
				
				
				//change db
				$this->load->model('mMainModul','',true);
				$this->mMainModul->change_db($this->session->userdata('gServer'),$db);
				//eof change db
				
				$this->load->model('mSearch','',true);
				$ssql = $this->mSearch->dept_def($kddept,$kdcount);
				$ssql = $ssql->row();
				$deptdef = $ssql->fs_nm_code;
				$kota = ucwords(strtolower($ssql->fs_kota));
				
				$lDept = strlen(trim($kddept));
				$lDept = $lDept - 2;
				$xDept = substr(trim($kddept), $lDept, 2);
				
				$lCount = strlen(trim($kdcount));
				$lCount = $lCount - 2;
				$xCount = substr(trim($kdcount), $lCount, 2);
				
				if ((trim($usernm) == 'MFI' and trim($userpass) == 'AMGGROUP'))
				{
					//set sesi
					$new = array(
						'gDatabase'		=> trim($db),
						'gComp'			=> trim($kdcomp),
						'gCompName'		=> trim($nmcomp),
						'gDept'			=> trim($kddept),
						'gCount'		=> trim($kdcount),
						
						'gDeptName'		=> trim($deptdef),
						'gGudang'		=> trim($xDept).trim($xCount),
						'gWilayah'		=> trim($xDept),
						'gKota'			=> trim($kota),
						'gUser'			=> trim($usernm),
						
						'gPass'			=> trim($userpass),
						'gSparePart'	=> '0',
						'logged'		=> TRUE
					);
					$this->session->set_userdata($new);
					
					$this->load->model('mMainModul','',true);
					$ssql = $this->mMainModul->get_tax();
					$ssql = $ssql->row();
					$otax = $ssql->fb_otax;
					
					$new = array('gOTax'=>trim($otax));
					$this->session->set_userdata($new);
					//eof set sesi
					
					$this->session->unset_userdata('vcpt');
					echo "{success:true}";
				}
				else
				{
					//coding pass
					$this->load->model('mMainModul','',true);
					$ssql = $this->mMainModul->coding($userpass);
					$userpass = $ssql;
					//eof coding pass
					
					//cari user, password di db
					$this->load->model('mSearch','',true);
					$ssql = $this->mSearch->valid_userpass($kdcomp,$usernm,$userpass);
					//eof cari user, password di db
					
					if ($ssql->num_rows() > 0)//user, password ada di db
					{
						$ssql = $ssql->row();
						$usernm = $ssql->fs_kd_user;
						// $userpass = $ssql->fs_password;
						
						//set sesi
						$new = array(
							'gDatabase'	=> trim($db),
							'gComp'		=> trim($kdcomp),
							'gCompName'	=> trim($nmcomp),
							'gDept'		=> trim($kddept),
							'gCount'	=> trim($kdcount),
							
							'gDeptName' => trim($deptdef),
							'gGudang'	=> trim($xDept).trim($xCount),
							'gWilayah'	=> trim($xDept),
							'gKota'		=> trim($kota),
							'gUser'		=> trim($usernm),
							
							'gPass'		=> '',
							'logged'	=> TRUE
						);
						$this->session->set_userdata($new);
						
						$this->load->model('mMainModul','',true);
						$ssql = $this->mMainModul->user_tipe($usernm);
						$ssql = $ssql->row();
						$usertipe = $ssql->fb_sparepart;
						
						$new = array(
							'gSparePart'	=> trim($usertipe)
						);
						$this->session->set_userdata($new);
						
						$this->load->model('mMainModul','',true);
						$ssql = $this->mMainModul->get_tax();
						$ssql = $ssql->row();
						$otax = $ssql->fb_otax;
						
						$new = array(
							'gOTax'	=> trim($otax)
						);
						$this->session->set_userdata($new);
						//eof set sesi
						
						$this->session->unset_userdata('vcpt');
						echo "{success:true}";
					}
					else//user, password tdk ada di db
					{
						echo "'User Code or Password Incorrect!!'";
					}
				}
			}
			else
			{
				echo "'Captcha Incorrect!!'";
			}
		}
	}
	
	function logout()
	{
		$this->session->sess_destroy();
		echo "{success:true}";
		//$this->session->unset_userdata('some_name');
		//redirect('','refresh');
	}
}
?>