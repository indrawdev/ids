<?php

class MainMenu extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		//change db
		$this->load->model('mMainModul','',true);
		$this->mMainModul->change_db($this->session->userdata('gServer'),$this->session->userdata('gDatabase'));
		//end of change db
		if (!$this->session->userdata('logged')) {
			redirect('login');
		}
	}
	
	function index()
	{
		if (trim($this->session->userdata('gDatabase')) <> '')
		{
			$this->load->view('vmainmenu');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function ambildefa()
	{
		$hasil = array(
			'sukses'	=> true,
			'kduser'	=> trim($this->session->userdata('gUser')),
			'kddept'	=> trim($this->session->userdata('gDept')),
			'count'		=> trim($this->session->userdata('gCount')),
			'nmdept'	=> trim($this->session->userdata('gDeptName')),
			
			'nmdb'		=> trim($this->session->userdata('gDatabase')),
			'nmserver'	=> trim($this->session->userdata('gKonek'))
		);
		echo json_encode($hasil);
	}
	
	function ambil_nodes()
	{
		$this->load->model('mMainMenu','',true);
		$ssql = $this->mMainMenu->load_menu();
		
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
								'id'	=> $row0->fs_kd_parent.'|'.$row0->fs_nm_formweb,
								'text'	=> $row0->fs_nm_menu,
								// 'href'	=> $row0->fs_nm_formweb,
								'leaf'	=> true
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
											'id'	=> $row1->fs_kd_child.'|'.$row1->fs_nm_formweb,
											'text'	=> $row1->fs_nm_menu,
											// 'href'	=> $row1->fs_nm_formweb,
											'leaf'	=> true
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
														'id'	=> $row2->fs_kd_child.'|'.$row2->fs_nm_formweb,
														'text'	=> $row2->fs_nm_menu,
														// 'href'	=> $row2->fs_nm_formweb,
														'leaf'	=> true
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
																	'id'	=> $row3->fs_kd_child.'|'.$row3->fs_nm_formweb,
																	'text'	=> $row3->fs_nm_menu,
																	// 'href'	=> $row3->fs_nm_formweb,
																	'leaf'	=> true
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
																			'id'	=> $row4->fs_kd_child.'|'.$row4->fs_nm_formweb,
																			'text'	=> $row4->fs_nm_menu,
																			// 'href'	=> $row4->fs_nm_formweb,
																			'leaf'	=> true
																		);
																	}
																}
															}
															$arr3[] = array(
																'id'		=> $row3->fs_kd_child,
																'text'		=> $row3->fs_nm_menu,
																'expanded'	=> true,
																'leaf'		=> false,
																'children'	=> $arr4
															);
														}
													}
												}
												$arr2[] = array(
													'id'		=> $row2->fs_kd_child,
													'text'		=> $row2->fs_nm_menu,
													'expanded'	=> true,
													'leaf'		=> false,
													'children'	=> $arr3
												);
											}
										}
									}
									$arr1[] = array(
										'id'		=> $row1->fs_kd_child,
										'text'		=> $row1->fs_nm_menu,
										'expanded'	=> true,
										'leaf'		=> false,
										'children'	=> $arr2
									);
								}
							}
						}
						$arr0[] = array(
							'id'		=> $row0->fs_kd_parent,
							'text'		=> $row0->fs_nm_menu,
							'expanded'	=> true,
							'leaf'		=> false,
							'children'	=> $arr1
						);
					}
				}
			}
		}
		echo json_encode($arr0);
	}
}
?>