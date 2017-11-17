<?php

class User extends CI_Controller
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
			$this->load->view('vuser');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function kodeuser()
	{
		$nstart = trim($this->input->post('start'));
		$kduser = trim($this->input->post('fs_kd_user'));
		$nmuser = trim($this->input->post('fs_nm_user'));

		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodeuser_all($kduser,$nmuser,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->kodeuser($kduser,$nmuser,$nstart,$cari);
		
		$arr = array();
		if ($ssql->num_rows() > 0)
		{
			foreach ($ssql->result() as $row)
			{
				$this->load->model('mMainModul','',true);
				$xpass = $this->mMainModul->decoding(trim($row->fs_password));
				
				$arr[] = array(
					'fs_kd_user'	=> trim($row->fs_kd_user),
					'fs_nm_user'	=> trim($row->fs_nm_user),
					'fs_password'	=> trim($xpass),
					'fs_level'		=> trim($row->fs_level),
					'fb_part'		=> trim($row->fb_part),
					'fs_status'		=> trim($row->fs_status)
				);
			}
		}
		echo '({"total":"'.$total.'","hasil":'.json_encode($arr).'})';
	}
	
	function kodelevel()
	{
		$nstart = trim($this->input->post('start'));
		$kdlevel = trim($this->input->post('fs_level'));

		$cari = trim($this->input->post('fs_cari'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodelevel_all($kdlevel,$cari);
		$total = $ssql->num_rows();
		
		$ssql = $this->mSearch->kodelevel($kdlevel,$nstart,$cari);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function ambil_nodes()
	{
		$level = trim($this->input->post('fs_level'));
		
		$this->load->model('mUser','',true);
		$ssql = $this->mUser->load_menu($level);
		
		$arr0 = array();
		$arr1 = array();
		$arr2 = array();
		$arr3 = array();
		$arr4 = array();
		if ($ssql->num_rows() > 0)
		{
			foreach ($ssql->result() as $row0)
			{
				if (trim($row0->fs_kd_child) == '')//strlen(trim($row0->fs_kd_parent)) == 2 and 
				{
					
					$i = 0;
					foreach ($ssql->result() as $row1)
					{
						if (strlen(trim($row1->fs_kd_parent)) == strlen(trim($row0->fs_kd_parent))
							and trim($row1->fs_kd_parent) == trim($row0->fs_kd_parent)
							and trim($row1->fs_kd_child) <> '')
						{
							++$i;
						}
					}
					
					if ($i == 0)
					{
						if (trim($row0->fs_nm_formweb) <> '')
						{
							$arr0[] = array(
								'fs_kd_induk'	=> $row0->fs_kd_parent,
								'fs_kd_menu'	=> $row0->fs_kd_child,
								'fs_nm_menu'	=> $row0->fs_nm_menu,
								'fb_tambah'		=> $row0->fb_tambah,
								'expanded'		=> true,
								'leaf'			=> true
							);
						}
					}
					else
					{
						$arr1 = array();
						foreach ($ssql->result() as $row1)
						{
							if ((strlen(trim($row1->fs_kd_parent)) == strlen(trim($row0->fs_kd_parent)))
								and (trim($row1->fs_kd_parent) == trim($row0->fs_kd_parent))
								and trim($row1->fs_kd_child) <> '')
							{
								
								$i = 0;
								foreach ($ssql->result() as $row2)
								{
									if (strlen(trim($row2->fs_kd_parent)) == strlen(trim($row1->fs_kd_child))
										and trim($row2->fs_kd_parent) == trim($row1->fs_kd_child))
									{
										++$i;
									}
								}
								
								if ($i == 0)
								{
									if (trim($row1->fs_nm_formweb) <> '')
									{
										$arr1[] = array(
											'fs_kd_induk'	=> $row1->fs_kd_parent,
											'fs_kd_menu'	=> $row1->fs_kd_child,
											'fs_nm_menu'	=> $row1->fs_nm_menu,
											'fb_tambah'		=> $row1->fb_tambah,
											'expanded'		=> true,
											'leaf'			=> true
										);
									}
								}
								else
								{
									$arr2 = array();
									foreach ($ssql->result() as $row2)
									{
										if (strlen(trim($row2->fs_kd_parent)) == strlen(trim($row1->fs_kd_child))
											and trim($row2->fs_kd_parent) == trim($row1->fs_kd_child))
										{
											
											$i = 0;
											foreach ($ssql->result() as $row3)
											{
												if (strlen(trim($row3->fs_kd_parent)) == strlen(trim($row2->fs_kd_child))
													and trim($row3->fs_kd_parent) == trim($row2->fs_kd_child))
												{
													++$i;
												}
											}
											
											if ($i == 0)
											{
												if (trim($row2->fs_nm_formweb) <> '')
												{
													$arr2[] = array(
														'fs_kd_induk'	=> $row2->fs_kd_parent,
														'fs_kd_menu'	=> $row2->fs_kd_child,
														'fs_nm_menu'	=> $row2->fs_nm_menu,
														'fb_tambah'		=> $row2->fb_tambah,
														'expanded'		=> true,
														'leaf'			=> true
													);
												}
											}
											else
											{
												$arr3 = array();
												foreach ($ssql->result() as $row3)
												{
													if (strlen(trim($row3->fs_kd_parent)) == strlen(trim($row2->fs_kd_child))
														and trim($row3->fs_kd_parent) == trim($row2->fs_kd_child))
													{
														
														$i = 0;
														foreach ($ssql->result() as $row4)
														{
															if (strlen(trim($row4->fs_kd_parent)) == strlen(trim($row3->fs_kd_child))
																and trim($row4->fs_kd_parent) == trim($row3->fs_kd_child))
															{
																++$i;
															}
														}
														
														if ($i == 0)
														{
															if (trim($row3->fs_nm_formweb) <> '')
															{
																$arr3[] = array(
																	'fs_kd_induk'	=> $row3->fs_kd_parent,
																	'fs_kd_menu'	=> $row3->fs_kd_child,
																	'fs_nm_menu'	=> $row3->fs_nm_menu,
																	'fb_tambah'		=> $row3->fb_tambah,
																	'expanded'		=> true,
																	'leaf'			=> true
																);
															}
														}
														else
														{
															$arr4 = array();
															foreach ($ssql->result() as $row4)
															{
																if (strlen(trim($row4->fs_kd_parent)) == strlen(trim($row3->fs_kd_child))
																	and trim($row4->fs_kd_parent) == trim($row3->fs_kd_child))
																{
																	if (trim($row4->fs_nm_formweb) <> '')
																	{
																		$arr4[] = array(
																			'fs_kd_induk'	=> $row4->fs_kd_parent,
																			'fs_kd_menu'	=> $row4->fs_kd_child,
																			'fs_nm_menu'	=> $row4->fs_nm_menu,
																			'fb_tambah'		=> $row4->fb_tambah,
																			'expanded'		=> true,
																			'leaf'			=> true
																		);
																	}
																}
															}
															$arr3[] = array(
																'fs_kd_induk'	=> $row3->fs_kd_parent,
																'fs_kd_menu'	=> $row3->fs_kd_child,
																'fs_nm_menu'	=> $row3->fs_nm_menu,
																'fb_tambah'		=> $row3->fb_tambah,
																'expanded'		=> true,
																'leaf'			=> false,
																'children'		=> $arr4
															);
														}
													}
												}
												$arr2[] = array(
													'fs_kd_induk'	=> $row2->fs_kd_parent,
													'fs_kd_menu'	=> $row2->fs_kd_child,
													'fs_nm_menu'	=> $row2->fs_nm_menu,
													'fb_tambah'		=> $row2->fb_tambah,
													'expanded'		=> true,
													'leaf'			=> false,
													'children'		=> $arr3
												);
											}
										}
									}
									$arr1[] = array(
										'fs_kd_induk'	=> $row1->fs_kd_parent,
										'fs_kd_menu'	=> $row1->fs_kd_child,
										'fs_nm_menu'	=> $row1->fs_nm_menu,
										'fb_tambah'		=> $row1->fb_tambah,
										'expanded'		=> true,
										'leaf'			=> false,
										'children'		=> $arr2
									);
								}
							}
						}
						$arr0[] = array(
							'fs_kd_induk'	=> $row0->fs_kd_parent,
							'fs_kd_menu'	=> $row0->fs_kd_child,
							'fs_nm_menu'	=> $row0->fs_nm_menu,
							'fb_tambah'		=> $row0->fb_tambah,
							'expanded'		=> true,
							'leaf'			=> false,
							'children'		=> $arr1
						);
					}
				}
			}
		}
		echo json_encode($arr0);
	}
	
	function ceksave()
	{
		$kduser = trim($this->input->post('fs_kd_user'));
		$kdpass = trim($this->input->post('fs_password'));
		$kdpass2 = trim($this->input->post('fs_password2'));
		
		if (trim($kdpass) <> trim($kdpass2))
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, Password is not same!!'
			);
			echo json_encode($hasil);
			return;
		}
		
		if (trim($kduser) == '')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, Reference number unknown!!'
			);
			echo json_encode($hasil);
		}
		else
		{
			$this->load->model('mUser','',true);
			$ssql = $this->mUser->cek_kode($kduser);
			
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
					'sukses'	=> true,
					'hasil'		=> 'lanjut'
				);
				echo json_encode($hasil);
			}
		}
	}
	
	function save()
	{
		$kduser = trim($this->input->post('fs_kd_user'));
		$nmuser = trim($this->input->post('fs_nm_user'));
		$kdpart = trim($this->input->post('fb_part'));
		$password = trim($this->input->post('fs_password'));
		$level = trim($this->input->post('fs_level'));
		
		if (trim($kdpart) == 'true')
		{
			$kdpart = 1;
		}
		else
		{
			$kdpart = 0;
		}
		
		$this->load->model('mMainModul','',true);
		$password = $this->mMainModul->coding(trim($password));
		
		$xupdate = false;
		$this->load->model('mUser','',true);
		$ssql = $this->mUser->cek_kode($kduser);
		
		if ($ssql->num_rows() > 0)
		{
			$xupdate = true;
		}
		
		$dt = array(
			'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
			'fs_kd_user'	=> trim($kduser),
			'fs_nm_user'	=> trim($nmuser),
			'fs_password'	=> trim($password),
			'fs_level'		=> trim($level),
			'fb_spearpart'	=> trim($kdpart)
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
			
			$this->db->insert('tm_user', $data);
		}
		else
		{
			$dt2 = array(
				'fs_upddt' => trim($this->session->userdata('gUser')),
				'fd_upddt' => trim(date('Y-m-d H:i:s'))
			);
			$data = array_merge($dt,$dt2);
			$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
						AND	fs_kd_user = '".trim($kduser)."'";
			
			$this->db->where($where);
			$this->db->update('tm_user', $data);
		}
		
		if ($xupdate == false)
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving User Success'
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving User Update Success'
			);
			echo json_encode($hasil);
		}
	}
	
	function ceksave2()
	{
		$level = trim($this->input->post('fs_level'));
		
		if (trim($level) == '')
		{
			$hasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Saving Failed, Level unknown!!'
			);
			echo json_encode($hasil);
		}
		else
		{
			$this->load->model('mUser','',true);
			$ssql = $this->mUser->cek_level($level);
			
			if ($ssql->num_rows() > 0)
			{
				$hasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Level already exists, do you want to update it?'
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
	}
	
	function save2()
	{
		$level = trim($this->input->post('fs_level'));
		
		$this->load->model('mUser','',true);
		$ssql = $this->mUser->cek_level($level);
		
		$xupdate = false;
		if ($ssql->num_rows() > 0)
		{
			$xupdate == true;
		}
		
		//hapus detail
		$where = "fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
					AND fs_level = '".trim($level)."'";
		
		$this->db->where($where);
		$this->db->delete('tm_parlevel');
		//eof hapus detail
		
		//simpan detail
		$kdinduk = explode('|', trim($this->input->post('fs_kd_induk')));
		$kdmenu = explode('|', trim($this->input->post('fs_kd_menu')));
		
		$jml = count($kdinduk) - 1;
		if ($jml != 0)
		{
			for ($i=1; $i<=$jml; $i++)
			{
				if (strlen(trim($kdinduk[$i])) == 2 and trim($kdmenu[$i]) == '')
				{
					$kdroot = '1';
				}
				else
				{
					$kdroot = '0';
				}
				
				$data = array(
					'fs_kd_comp'	=> trim($this->session->userdata('gComp')),
					'fs_kd_parent'	=> trim($kdinduk[$i]),
					'fs_kd_child'	=> trim($kdmenu[$i]),
					'fs_level'		=> trim($level),
					'fs_index'		=> '1',
					'fb_root'		=> trim($kdroot),
					
					'fs_usrcrt'		=> trim($this->session->userdata('gUser')),
					'fd_usrcrt'		=> trim(date('Y-m-d H:i:s')),
					'fs_upddt'		=> trim($this->session->userdata('gUser')),
					'fd_upddt'		=> trim(date('Y-m-d H:i:s'))
				);
				$this->db->insert('tm_parlevel', $data);
			}
		}
		//eof simpan detail
		
		if ($xupdate == false)
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Level Success'
			);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Saving Level Update Success'
			);
			echo json_encode($hasil);
		}
	}
}
?>