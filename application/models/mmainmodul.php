<?php

class MMainModul extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function addfield($xNmTabel,$xNmField,$xPjgField)
	{
		$sSQL = $this->db->query("EXEC stp_addfield '".$xNmTabel."', '".$xNmField."', ".$xPjgField."");
		return $sSQL;
	}
	
	function ambilbulan($xBulan){
		if ($xBulan == '01')
		{
			return 'Januari';
		}
		else if ($xBulan == '02')
		{
			return 'Februari';
		}
		else if ($xBulan == '03')
		{
			return 'Maret';
		}
		else if ($xBulan == '04')
		{
			return 'April';
		}
		else if ($xBulan == '05')
		{
			return 'Mei';
		}
		else if ($xBulan == '06')
		{
			return 'Juni';
		}
		else if ($xBulan == '07')
		{
			return 'Juli';
		}
		else if ($xBulan == '08')
		{
			return 'Agustus';
		}
		else if ($xBulan == '09')
		{
			return 'September';
		}
		else if ($xBulan == '10')
		{
			return 'Oktober';
		}
		else if ($xBulan == '11')
		{
			return 'Nopember';
		}
		else if ($xBulan == '12')
		{
			return 'Desember';
		}
	}
	
	function ambil_kode($sTipe)
	{
		$ldept = strlen(trim($this->session->userdata('gDept')));
		$ldept = $ldept - 2;
		$lcount = strlen(trim($this->session->userdata('gCount')));
		$lcount = $lcount - 2;
		
		$xdept = substr(trim($this->session->userdata('gDept')), $ldept, 2).substr(trim($this->session->userdata('gCount')), $lcount, 2);
		
		$xSQL = ("
			SELECT 	TOP 1 fs_code, fs_count
			FROM 	tm_addr
			WHERE 	fs_cdtyp = '".trim($sTipe)."'
				AND	fs_code = '".trim($xdept)."'
				AND	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
			ORDER BY fs_count DESC
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_captcha($sCaptcha)
	{
		$xSQL = ("
			SELECT 	COUNT(*) AS fn_jml
			FROM 	captcha
			WHERE 	word = '".trim($sCaptcha)."'
				AND ip_address = '".trim($this->input->ip_address())."'
				AND captcha_time >= '".trim($this->session->userdata('vcpt'))."'
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function change_db($nama_server,$nama_db)
	{
		$config['hostname'] = $nama_server;
		$config['username'] = "aster";
		$config['password'] = "aster3000";
		$config['database'] = $nama_db;
		$config['dbdriver'] = "mssql";
		$config['dbprefix'] = "";
		$config['pconnect'] = FALSE;
		$config['db_debug'] = TRUE;
		
		$this->load->database($config,TRUE);
	}
	
	function clean_input($text)
	{
		$text = str_ireplace('"',' ',trim($text));
		$text = str_ireplace("'",' ',trim($text));
		$text = str_ireplace(';',' ',trim($text));
		return $text;
	}
	
	function coding($xString)
	{
		$xlenstr = strlen(trim($xString));
		$xText = array();
		$hasil = '';
		for($i = 0; $i < $xlenstr; $i++)
		{
			$xText[$i] = chr(ord(substr(trim($xString),$i,1)) + 111);
			$hasil = $hasil.$xText[$i];
		}
		return $hasil;
	}
	
	function decoding($xString)
	{
		$xlenstr = strlen(trim($xString))-1;
		$xText = array();
		$hasil = '';
		for($i = 0; $i <= $xlenstr; $i++)
		{
			$xText[$i] = chr(ord(substr(trim($xString),$i,1)) - 111);
			$hasil = $hasil.$xText[$i];
		}
		return $hasil;
	}
	
	function getacnogrup($sTrx)
	{
		$xSQL = ("
			SELECT	TOP 1 fs_kd_group as acno
			FROM 	tm_trx (NOLOCK)
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_trx = '".trim($sTrx)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function getkasusaha()
	{
		$xSQL = ("
			SELECT	TOP 1 ISNULL(fs_kd_acno, '') fs_kd_acno
			FROM 	tm_grpacno (NOLOCK)
			WHERE 	fs_kd_group = 'CUSH'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function getperiode($sDate)
	{
		$xSQL = ("
			SELECT	TOP 1 [periode] = ltrim(rtrim(a.fs_year)) + ltrim(rtrim(a.fs_periode))
			FROM 	tm_perioded a(NOLOCK)
			WHERE 	a.fd_start <= '".trim($sDate)."'
				AND a.fd_end >= '".trim($sDate)."'
				AND a.fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function get_regproduct($xPrefix)
	{
		$xSQL = ("
			SELECT	TOP 1 fs_seqno = CONVERT(INT, RIGHT(LTRIM(RTRIM(fs_register)), 6))
			FROM 	tm_icregister (NOLOCK)
			WHERE 	fs_register LIKE '".trim($xPrefix)."%'
			ORDER BY fs_register DESC
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function get_fakturbeli($xPrefix)
	{
		$xSQL = ("
			SELECT	TOP 1 fs_refno
			FROM 	tx_posheader (NOLOCK)
			WHERE 	fs_refno LIKE '".trim($xPrefix)."%'
			ORDER BY fs_refno DESC
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function get_mutasi($xPrefix)
	{
		$xSQL = ("
			SELECT	TOP 1 fs_refno
			FROM 	tx_mutasidb (NOLOCK)
			WHERE 	fs_refno LIKE '".trim($xPrefix)."%'
			ORDER BY fs_refno DESC
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function get_nostatus($xPrefix)
	{
		$xSQL = ("
			SELECT	TOP 1 fs_refno
			FROM 	tx_unitstatus (NOLOCK)
			WHERE 	fs_refno LIKE '".trim($xPrefix)."%'
			ORDER BY fs_refno DESC
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function get_nokasir($xPrefix)
	{
		$xSQL = ("
			SELECT	TOP 1 fs_refno
			FROM 	tx_poskasir (NOLOCK)
			WHERE 	fs_refno LIKE '".trim($xPrefix)."%'
			ORDER BY fs_refno DESC
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function get_refno($sDept,$sCount,$sTrx,$ssTrx,$sDate)
	{
		$xSQL = ("
			EXEC stp_refno '".trim($this->session->userdata('gComp'))."',
				'".trim($sTrx)."','".trim($ssTrx)."','".trim($sDept)."','".trim($sCount)."','".trim($sDate)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function get_tax()
	{
		$xSQL = ("
			SELECT fb_otax
			FROM   tm_company
			WHERE  fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_cabang()
	{
		$xSQL = ("
			SELECT 	NAME
			FROM 	sysobjects
			WHERE 	NAME = 'tm_cabang'
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function get_cabang($sDept,$sCount)
	{
		$xSQL = ("
			SELECT 	fs_kd_cabang
			FROM 	tm_cabang
			WHERE 	fs_kd_dept = '".trim($sDept)."'
				AND	fs_count = '".trim($sCount)."'
			");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return round(((float)$usec + (float)$sec));
	}
	
	function pdf($nmfile)
	{
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);

		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
		
		$xIP = trim($this->session->userdata('ip_address'));
		$gIP = trim($this->session->userdata('gIP'));
		if (trim($xIP) == trim($gIP))
		{
			$xlibPath = '/var/www/'.config_item('base_folder').'/application/libraries/';
			$xPath = '/var/www/'.config_item('base_folder').'/temp/';
		}
		else
		{
			$xlibPath = APPPATH.'../application/libraries/';
			$xPath = APPPATH.'../temp/';
		}
		
		require_once $xlibPath.'PHPExcel/PHPExcel.php';
		
		$rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
		$rendererLibraryPath = $xlibPath.'MPDF57/';
		
		$oReader = PHPExcel_IOFactory::createReader('Excel5');
		$oExcel = $oReader->load($xPath.trim($nmfile).'.xls');
		
		try {
			if (!PHPExcel_Settings::setPdfRenderer(
				$rendererName,
				$rendererLibraryPath
			)) {
				echo (
					'NOTICE: Please set the $rendererName and $rendererLibraryPath values' .
					EOL .
					'at the top of this script as appropriate for your directory structure' .
					EOL
				);
			} else {
				$oWriter = PHPExcel_IOFactory::createWriter($oExcel, 'PDF');
				$oWriter->save(str_replace('.php', '.pdf', $xPath.trim($nmfile).'.pdf'));
			}
		} catch (Exception $e) {
		}
	}
	
	function tabel_sesi()
	{
		$sSQL = $this->db->query("
			IF NOT EXISTS (	SELECT	name 
							FROM   	sysobjects (NOLOCK)
							WHERE  	name = 'CI_Sessions' 
								AND	type = 'U')
			BEGIN
				CREATE TABLE CI_Sessions (
					session_id VARCHAR(40) DEFAULT '0' NOT NULL,
					ip_address VARCHAR(16) DEFAULT '0' NOT NULL,
					user_agent VARCHAR(120) NOT NULL,
					last_activity INT DEFAULT 0 NOT NULL,
					user_data VARCHAR(8000) NOT NULL,
					CONSTRAINT  PK_CI_Session PRIMARY KEY (session_id ASC)
				)
			
				CREATE NONCLUSTERED INDEX NCI_Session_Activity
				ON CI_Sessions(last_activity DESC)
			END 
		");
		return $sSQL;
	}
	
	function tabel_captcha()
	{
		$sSQL = $this->db->query("
			IF EXISTS (SELECT * FROM dbo.sysobjects WHERE id = object_id(N'[dbo].[captcha]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
			DROP TABLE [dbo].[captcha]
			
			CREATE TABLE [dbo].[captcha] (
				[captcha_id]	[BIGINT] IDENTITY (1, 1) NOT NULL,
				[captcha_time] 	[VARCHAR] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL DEFAULT ('0'),
				[ip_address] 	[VARCHAR] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL DEFAULT ('0'),
				[word] 			[VARCHAR] (20) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL DEFAULT (' ')
			) ON [PRIMARY]
		");
		return $sSQL;
	}
	
	function user_tipe($sUserCd)
	{
    	$xSQL = ("
			SELECT	TOP 1 fb_sparepart = CONVERT(INT, fb_spearpart), fs_level, fn_discmax
			FROM 	tm_user (NOLOCK)
			WHERE 	fs_kd_comp = '".trim($this->session->userdata('gComp'))."'
				AND fs_kd_user = '".trim($sUserCd)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function tkoma($x) 
	{
		$x = stristr($x,'.');
		$angka = array('', 'satu', 'dua', 'tiga', 'empat', 'lima','enam', 'tujuh', 'delapan', 'sembilan');
		
		$temp =' ';
		$pjg = strlen($x);
		$pos = 1;
		
		while ($pos < $pjg)
		{
			$char = substr($x,$pos,1);
			$pos++;
			$temp .= ' '.$angka[$char];
			
		}
		return $temp;
	}
	
	function kekata($x) 
	{
		$x = abs($x);
		$angka = array('', 'satu', 'dua', 'tiga', 'empat', 'lima','enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas');
		$temp = '';
		if ($x < 12)
		{
			$temp = ' '.$angka[$x];
		}
		else if ($x < 20)
		{
			$temp = $this->kekata($x - 10).' belas';
		}
		else if ($x < 100)
		{
			$temp = $this->kekata($x / 10).' puluh'.$this->kekata($x % 10);
		}
		else if ($x < 200)
		{
			$temp = ' seratus'.$this->kekata($x - 100);
		}
		else if ($x < 1000)
		{
			$temp = $this->kekata($x / 100).' ratus'.$this->kekata($x % 100);
		}
		else if ($x < 2000)
		{
			$temp = ' seribu'.$this->kekata($x - 1000);
		}
		else if ($x < 1000000)
		{
			$temp = $this->kekata($x / 1000).' ribu'.$this->kekata($x % 1000);
		}
		else if ($x < 1000000000)
		{
			$temp = $this->kekata($x / 1000000).' juta'.$this->kekata($x % 1000000);
		}
		else if ($x < 1000000000000)
		{
			$temp = $this->kekata($x / 1000000000).' milyar'.$this->kekata(fmod($x,1000000000));
		}
		else if ($x < 1000000000000000)
		{
			$temp = $this->kekata($x / 1000000000000).' trilyun'.$this->kekata(fmod($x,1000000000000));
		}
		return $temp;
	}
	
	function terbilang($x,$style = 4) 
	{
		if ($x < 0)
		{
			$poin = trim($this->tkoma($x));
			$hasil = 'minus '. trim($this->kekata($x));
		}
		else
		{
			$poin = trim($this->tkoma($x));
			$hasil = trim($this->kekata($x));
		}
		
		switch ($style)
		{
			case 1:
				if ($poin)
				{
					$hasil = strtoupper($hasil) . ' KOMA '. strtoupper($poin);
				}
				else
				{
					$hasil = strtoupper($hasil);
				}
				$hasil = $hasil . ' Rupiah ';
				break;
			
			case 2:
				if ($poin)
				{
					$hasil = strtolower($hasil) . ' koma '. strtolower($poin);
				}
				else
				{
					$hasil = strtolower($hasil);
				}
				$hasil = $hasil . ' Rupiah ';
				break;
			
			case 3:
				if ($poin)
				{
					$hasil = ucwords($hasil) . ' Koma '. ucwords($poin);
				}
				else
				{
					$hasil = ucwords($hasil);
				}
				$hasil = $hasil . ' Rupiah ';
				break;
			
			default:
				if ($poin)
				{
					$hasil = ucfirst($hasil) . ' koma ' . $poin;
				}
				else
				{
					$hasil = ucfirst($hasil);
				}
				$hasil = $hasil . ' Rupiah ';
				break;
		}
		return $hasil;
	}
	
	/**
	 * RATE
	 *
	 * Returns the interest rate per period of an annuity.
	 * RATE is calculated by iteration and can have zero or more solutions.
	 * If the successive results of RATE do not converge to within 0.0000001 after 20 iterations,
	 * RATE returns the #NUM! error value.
	 *
	 * Excel Function:
	 *		RATE(nper,pmt,pv[,fv[,type[,guess]]])
	 *
	 * @access	public
	 * @category Financial Functions
	 * @param	float	nper		The total number of payment periods in an annuity.
	 * @param	float	pmt			The payment made each period and cannot change over the life
	 *									of the annuity.
	 *								Typically, pmt includes principal and interest but no other
	 *									fees or taxes.
	 * @param	float	pv			The present value - the total amount that a series of future
	 *									payments is worth now.
	 * @param	float	fv			The future value, or a cash balance you want to attain after
	 *									the last payment is made. If fv is omitted, it is assumed
	 *									to be 0 (the future value of a loan, for example, is 0).
	 * @param	integer	type		A number 0 or 1 and indicates when payments are due:
	 *										0 or omitted	At the end of the period.
	 *										1				At the beginning of the period.
	 * @param	float	guess		Your guess for what the rate will be.
	 *									If you omit guess, it is assumed to be 10 percent.
	 * @return	float
	 **/
	function RATE($nper, $pmt, $pv, $fv = 0.0, $type = 0, $guess = 0.1) {
		$nper	= (int) PHPExcel_Calculation_Functions::flattenSingleValue($nper);
		$pmt	= PHPExcel_Calculation_Functions::flattenSingleValue($pmt);
		$pv		= PHPExcel_Calculation_Functions::flattenSingleValue($pv);
		$fv		= (is_null($fv))	? 0.0	:	PHPExcel_Calculation_Functions::flattenSingleValue($fv);
		$type	= (is_null($type))	? 0		:	(int) PHPExcel_Calculation_Functions::flattenSingleValue($type);
		$guess	= (is_null($guess))	? 0.1	:	PHPExcel_Calculation_Functions::flattenSingleValue($guess);
		
		/** FINANCIAL_MAX_ITERATIONS */
		define('FINANCIAL_MAX_ITERATIONS', 20);//128);
		
		/** FINANCIAL_PRECISION */
		define('FINANCIAL_PRECISION', 0.0000001);//1.0e-08);
		
		$rate = $guess;
		if (abs($rate) < FINANCIAL_PRECISION) {
			$y = $pv * (1 + $nper * $rate) + $pmt * (1 + $rate * $type) * $nper + $fv;
		} else {
			$f = exp($nper * log(1 + $rate));
			$y = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
		}
		$y0 = $pv + $pmt * $nper + $fv;
		$y1 = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;

		// find root by secant method
		$i  = $x0 = 0.0;
		$x1 = $rate;
		while ((abs($y0 - $y1) > FINANCIAL_PRECISION) && ($i < FINANCIAL_MAX_ITERATIONS)) {
			$rate = ($y1 * $x0 - $y0 * $x1) / ($y1 - $y0);
			$x0 = $x1;
			$x1 = $rate;
			if (($nper * abs($pmt)) > ($pv - $fv))
				$x1 = abs($x1);

			if (abs($rate) < FINANCIAL_PRECISION) {
				$y = $pv * (1 + $nper * $rate) + $pmt * (1 + $rate * $type) * $nper + $fv;
			} else {
				$f = exp($nper * log(1 + $rate));
				$y = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
			}

			$y0 = $y1;
			$y1 = $y;
			++$i;
		}
		return $rate;
	}	//	function RATE()
}

?>
