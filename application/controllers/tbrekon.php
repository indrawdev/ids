<?php

class TBRekon extends CI_Controller
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
			$this->load->view('vtbrekon');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function data()
	{
		$hasil= array();
		$arrcolumns = array();
		$arrfieldata = array();
		$arrvalues = array();
		$arrcolumnsfinal = array();
		$arrfieldatafinal = array();
		$nama_dept = array();
		$arrvalues3 = array();
		
		$arrkolom1 = array();
		$arrkolom2 = array();
		$arrkolom = array();
		
		$arrcolumns	= array(
			array(
				'align'			=> 'right',
				'dataIndex'		=> 'id',
				'id'			=> 'id',
				'text'			=> 'No',
				'menuDisabled'	=> true,
				'width'			=> 30
			),
			array(
				'text'		=> 'Rekening',
				'width'			=> 100,
				'dataIndex'		=> 'Rekening',
				'menuDisabled'	=> true
			),
			array(
				'text'		=> 'Keterangan',
				'width'			=> 300,
				'dataIndex'		=> 'Keterangan',
				'menuDisabled'	=> true
			)
		);
		
		$arrfieldata = array(
			array(
				'name'	=> 'id',
				'type'	=> 'int'
			),
			array(
				'name'	=> 'Rekening'
			),
			array(
				'name'	=> 'Keterangan'
			)
		);
		
		$this->load->model('mTBRekon','',true);
		$ssql = $this->mTBRekon->gridheader();
		
		if ($ssql->num_rows() > 0)
		{
			$jumlah_dept = $ssql->num_rows();
			
			foreach ($ssql->result() as $row0)
			{
				$nama_dept[]= $row0->fs_nm_department;
				
				for ($i=1; $i<=3; $i++)
				{
					switch ($i)
					{
						case 1:
							$arrcolumnsadd[] = array(
								'dataIndex'		=> $row0->fs_nm_department."-A",
								'format'		=> '0,000.00',
								'text'			=> $row0->fs_nm_department."-A",
								'hidden'		=> true,
								'menuDisabled'	=> true,
								'width'			=> 200,
								'align'			=> 'right',
								'xtype'			=> 'numbercolumn'
							);
							
							$arrfieldataadd[] = array(
								'name'	=> $row0->fs_nm_department."-A",
								'type'	=> 'float'
							);
							break;
							
						case 2:
							$arrcolumnsadd[] = array(
								'dataIndex'		=> $row0->fs_nm_department."-B",
								'format'		=> '0,000.00',
								'text'		=> $row0->fs_nm_department."-B",
								'hidden'		=> true,
								'menuDisabled'	=> true,
								'width'			=> 200,
								'align'			=> 'right',
								'xtype'			=> 'numbercolumn'
							);
							
							$arrfieldataadd[] = array(
								'name'	=> $row0->fs_nm_department."-B",
								'type'	=> 'float'
							);
							break;
							
						case 3:
							$arrcolumnsadd[] = array(
								'dataIndex'		=> $row0->fs_nm_department."-C",
								'format'		=> '0,000.00', 
								'text'		=> $row0->fs_nm_department."-C",
								'hidden'		=> true,
								'menuDisabled'	=> true,
								'width'			=> 200,
								'align'			=> 'right',
								'xtype'			=> 'numbercolumn'
							);
										
							$arrfieldataadd[] = array(
								'name'	=> $row0->fs_nm_department. "-C",
								'type'	=> 'float'
							);
							break;
					}
		
				}			
				
				$arrcolumnsadd[] = array(
					'dataIndex'		=> $row0->fs_nm_department,
					'format'		=> '0,000.00',
					'text'		=> $row0->fs_nm_department,
					'hidden'		=> false,
					'menuDisabled'	=> true,
					'width'			=> 200,
					'align'			=> 'right',
					'xtype'			=> 'numbercolumn'
				);
									
				$arrfieldataadd[] = array(
					'name'	=> $row0->fs_nm_department ,
					'type'	=> 'float'
				);
			}
		}
		
		//tambah kolom TOTAL	
		$arrcolumnsadd[] = array(
			'dataIndex'		=> 'TOTAL',
			'format'		=> '0,000.00',
			'text'		=> 'TOTAL',
			'hidden'		=> false,
			'menuDisabled'	=> true,
			'width'			=> 200,
			'align'			=> 'right',
			'xtype'			=> 'numbercolumn'
		);
									
		$arrfieldataadd[] = array(
			'name'	=> 'TOTAL',
			'type'	=> 'float'
		);	
								
		$arrcolumnsfinal = array_merge($arrcolumns,$arrcolumnsadd);
		$arrfieldatafinal = array_merge($arrfieldata,$arrfieldataadd);
				
		$this->load->model('mTBRekon','',true);
		
		// $xPeriodeAwal = '201201';
		// $xPeriodeAkhir = '201212';
		
		$xPeriodeAwal = trim($this->input->post('fd_periodeawal'));
		$xPeriodeAkhir = trim($this->input->post('fd_periodeakhir'));
		$xLevel = trim($this->input->post('fs_level'));
		
		$gComp = $this->session->userdata('gComp');
		
		$this->mTBRekon->loadrekening_I($xPeriodeAwal,$xPeriodeAkhir,$xLevel,$gComp);
		$this->mTBRekon->loadrekening_II($gComp);
		
		$sSQL3 = $this->mTBRekon->loadrekening_III($xPeriodeAwal,$xPeriodeAkhir,$xLevel,$gComp);
		
				
		if ($sSQL3->num_rows() > 0)
		{
			$row = $sSQL3->result();
			
			$arrkolom[] = "NO";
			$arrkolom[] = " REKENING";
			$arrkolom[] = "KETERANGAN";
			
			
			for ($i=0; $i<= $jumlah_dept-1; $i++)
			{
				$arrkolom[] = $nama_dept[$i]."-A";
				$arrkolom[] = $nama_dept[$i]."-B";
				$arrkolom[] = $nama_dept[$i]."-C";
				$arrkolom[] = $nama_dept[$i];
			}
			$arrkolom[] = "TOTAL";
			
			$isiarray = "";
			$jumlah_kolom = count($arrkolom);
			$isi = array();
			
			foreach ($sSQL3->result() as $row)
			{
				for ($x=0; $x < $jumlah_kolom ; $x++)
				{
					$isiarray =  $isiarray."*".$row->{$arrkolom[$x]};
				}
				
				$arrvalues[] = explode("*",substr($isiarray, 1));
				$isiarray = "";
			}
			
			$sSQL3->free_result();
		}
		
		$hasil = array(
			'columndata'	=> $arrcolumnsfinal,
			'fielddata'		=> $arrfieldatafinal,
			'values'		=> $arrvalues
		);
		echo json_encode($hasil);
	}
	
	function data_kolom()
	{
		$hasil= array();
		$arrcolumns = array();
		$arrcolumnsfinal = array();
		
		$arrfieldata = array();
		$arrvalues = array();
				
		$arrcolumns	= array(
			array(
				'align'			=> 'right',
				'dataIndex'		=> 'id',
				'id'			=> 'id',
				'text'			=> 'No',
				'menuDisabled'	=> true,
				'width'			=> 30
			),
			array(
				'dataIndex'		=> 'Rekening',
				'menuDisabled'	=> true,
				'text'			=> 'Rekening',
				'width'			=> 100
			),
			array(
				'dataIndex'		=> 'Keterangan',
				'menuDisabled'	=> true,
				'text'			=> 'Keterangan',
				'width'			=> 300
			)
		);
		
		$arrfieldata = array(
			array(
				'name'	=> 'id',
				'type'	=> 'int'
			),
			array(
				'name'	=> 'Rekening'
			),
			array(
				'name'	=> 'Keterangan'
			)
		);
		
		$arrvalues	= array(
			array(
				'',
				'',
				'',
				'',
				'',
				'',
				''
			)
		);
		
		
		$this->load->model('mTBRekon','',true);
		$ssql = $this->mTBRekon->gridheader();
		
		if ($ssql->num_rows() > 0)
		{
			foreach ($ssql->result() as $row0)
			{
				for ($i=1; $i<=3; $i++)
				{
					switch ($i)
					{
						case 1:
							$arrcolumnsadd[] = array(
								'align'			=> 'right',
								'dataIndex'		=> $row0->fs_nm_department."-A",
								'format'		=> '0,000.00',
								'hidden'		=> true,
								'menuDisabled'	=> true,
								'text'			=> $row0->fs_nm_department."-A",
								'width'			=> 200,
								'xtype'			=> 'numbercolumn'
							);
							
							$arrfieldataadd[] = array(
								'name'	=> $row0->fs_nm_department."-A",
								'type'	=> 'float'
							);
							break;
						
						case 2:
							$arrcolumnsadd[] = array(
								'align'			=> 'right',
								'dataIndex'		=> $row0->fs_nm_department."-B",
								'format'		=> '0,000.00',
								'hidden'		=> true,
								'menuDisabled'	=> true,
								'text'			=> $row0->fs_nm_department."-B",
								'width'			=> 200,
								'xtype'			=> 'numbercolumn'
							);
							
							$arrfieldataadd[] = array(
								'name'	=> $row0->fs_nm_department."-B",
								'type'	=> 'float'
							);
							break;
						
						case 3:
							$arrcolumnsadd[] = array(
								'align'			=> 'right',
								'dataIndex'		=> $row0->fs_nm_department."-C",
								'format'		=> '0,000.00',
								'hidden'		=> true,
								'menuDisabled'	=> true,
								'text'			=> $row0->fs_nm_department."-C",
								'width'			=> 200,
								'xtype'			=> 'numbercolumn'
							);
							
							$arrfieldataadd[] = array(
								'name'	=> $row0->fs_nm_department. "-C",
								'type'	=> 'float'
							);
							break;
					}
				}			
				
				$arrcolumnsadd[] = array(
					'align'			=> 'right',
					'dataIndex'		=> $row0->fs_nm_department,
					'format'		=> '0,000.00',
					'hidden'		=> false,
					'menuDisabled'	=> true,
					'text'			=> $row0->fs_nm_department,
					'width'			=> 200,
					'xtype'			=> 'numbercolumn'
				);
				
				$arrfieldataadd[] = array(
					'name'	=> $row0->fs_nm_department,
					'type'	=> 'float'
				);
			}
		}
		
		$arrcolumnsfinal = array_merge($arrcolumns,$arrcolumnsadd);
		
		$hasil = array(
			'columndata'	=> $arrcolumnsfinal,
			'fielddata'		=> $arrfieldata,
			'values'		=> $arrvalues
		);
		echo json_encode($hasil);
	}
	
    function getColumnLetter($columnNumber)
	{
        if ($columnNumber > 26)
		{
			$columnLetter = Chr(intval(($columnNumber - 1) / 26) + 64).Chr((($columnNumber - 1) % 26) + 65);
        }
		else
		{
            $columnLetter = Chr($columnNumber + 64);
        }
		return $columnLetter;
    }
	
	function convert_toexcel()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul','',true);
		$jamskg = $this->mMainModul->microtime_float();
		
		$nmfiletemp = 'ttbrekonexcel';
		$nmfile = 'tbrekonexcel-'.$jamskg;
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
		
		$oExcel->getActiveSheet()->setTitle('TB Rekon');
		$oExcel->getActiveSheet()->setShowGridlines(false);
		
		$oExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$oExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$oExcel->getActiveSheet()->getPageMargins()->setTop(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setBottom(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setLeft(0.2);
		$oExcel->getActiveSheet()->getPageMargins()->setRight(0);
		
		$oExcel->getDefaultStyle()->getFont()->setName('Calibri');
		$oExcel->getDefaultStyle()->getFont()->setSize(10);
		$sel = $oExcel->getActiveSheet();
		
		$xPeriodeAwal = trim($this->input->post('fd_periodeawal'));
		$xPeriodeAkhir = trim($this->input->post('fd_periodeakhir'));
		$xLevel = trim($this->input->post('fs_level'));
		$gComp = $this->session->userdata('gComp');
		$UserName = trim($this->session->userdata('gUser'));
		
		$pjghal = 57;
		$i = 1;
		$l = 0; // no urut
		
		$k = 1; // pjg hal
		$p = 1; // awal hal
		
		$sel->setCellValue('A'.$i, 'Trial Balance (Rekon)');
		$sel->mergeCells('A'.$i.':H'.$i);
		$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sel->getStyle('A'.$i)->getFont()->SetUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
		$sel->getStyle('A'.$i)->getFont()->setBold(true);
		$sel->getStyle('A'.$i)->getFont()->setSize(12);
		$i++;
		$k++;
		
		$sel->setCellValue('A'.$i, 'Periode: '.substr($xPeriodeAwal,4,2).'-'.substr($xPeriodeAwal,0,4).' s/d '.substr($xPeriodeAwal,4,2).'-'.substr($xPeriodeAwal,0,4));
		$sel->mergeCells('A'.$i.':H'.$i);
		$sel->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$i = $i + 2;
		$k = $k + 2;
		
		$arrcolumnsexcelfinal = array();
		
		$arrcolumnsexcelfinal[] = 'NO';
		$arrcolumnsexcelfinal[] = ' REKENING';
		$arrcolumnsexcelfinal[] = 'KETERANGAN';
					
		$this->load->model('mTBRekon','',true);
		
		$ssql = $this->mTBRekon->gridheader();
		
		if ($ssql->num_rows() > 0)
		{
			foreach ($ssql->result() as $row0)
			{
				$arrcolumnsexcelfinal[] = $row0->fs_nm_department;
			}
			
			$arrcolumnsexcelfinal[] = 'TOTAL';	
		}
		
		$styleArray = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);
		
		$warnakolom = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'fff8dc')
			)
		);
		
		
		for ($x=0; $x<count($arrcolumnsexcelfinal); $x++)
		{
			$sel->setCellValueByColumnAndRow($x, $i ,$arrcolumnsexcelfinal[$x],false);
		}
		
		$sel->getStyle('A'.$i.':'.chr(65+count($arrcolumnsexcelfinal)-1).$i)->applyFromArray($styleArray);
		$sel->getStyle('A'.$i.':'.chr(65+count($arrcolumnsexcelfinal)-1).$i)->applyFromArray($warnakolom);
		$sel->getStyle('A'.$i.':'.chr(65+count($arrcolumnsexcelfinal)-1).$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$i++;
		$k++;
		
		$this->load->model('mTBRekon','',true);
		$this->mTBRekon->loadrekening_I($xPeriodeAwal,$xPeriodeAkhir,$xLevel,$gComp);
		$this->mTBRekon->loadrekening_II($gComp);
		
		$sSQL2 = $this->mTBRekon->loadrekening_III($xPeriodeAwal,$xPeriodeAkhir,$xLevel,$gComp);
		
		if ($sSQL2->num_rows() > 0)
		{
			foreach ($sSQL2->result() as $row)
			{
				for ($x=0; $x<count($arrcolumnsexcelfinal); $x++)
				{
					$sel->setCellValueByColumnAndRow($x, $i ,$row->{$arrcolumnsexcelfinal[$x]},false);
					if ($x > 2)
					{
						$h = $this->getColumnLetter($x+1);
						$sel->getStyle($h.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						
						$isi = $sel->getCellByColumnAndRow($x,$i)->getValue();
						if ($isi <> 0)
						{
							$sel->getStyle($h.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
						}
					}
					$sel->getColumnDimension(chr(65+$x))->setAutoSize(true);
					$sel->getStyle('A'.$i.':'.chr(65+count($arrcolumnsexcelfinal)-1).$i)->applyFromArray($styleArray);
				}
				$i++;
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
			
			$hasil = array(
				'sukses'	=> true,
				'nmfile'	=> $xPath.$nmfile.'.xls'
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