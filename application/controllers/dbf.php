<?php

class Dbf extends CI_Controller
{

	function createAPK()
	{
		// field 99 in DTAPK.DBF
		$cols = array(
					array("RECOID", "C", 1),
					array("NOMAPK", "N", 6, 0),
					array("JNSFRM", "C", 1),
					array("TGLAPK", "D"),
					array("KODELK", "N", 2, 0),
					array("NOMDEL", "N", 2, 0),
					array("JENPIU", "C", 1),
					array("POLPEN", "N", 2, 0),
					array("NOMPJB", "N", 6, 0),
					array("TGLPJB", "D"),
					array("NAMPEM", "C", 30),
					array("ALPEM1", "C", 30),
					array("ALPEM2", "C", 30),
					array("NAMKOT", "C", 30),
					array("KODEPS", "C", 6),
					array("NOTELP", "C", 15),
					array("USHPEM", "C", 6),
					array("KUSPEM", "C", 30),
					array("ALUPE1", "C", 30),
					array("ALUPE2", "C", 30),
					array("TLUPEM", "C", 15),
					array("GAJPEM", "N", 18, 0),
					array("NAMPHL", "C", 30),
					array("USHPHL", "C", 6),
					array("KUSPHL", "C", 30),
					array("ALUPH1", "C", 30),
					array("ALUPH2", "C", 30),
					array("TLUPHL", "C", 15),
					array("GAJPHL", "N", 12, 0),
					array("NAMPJM", "C", 30),
					array("ALPJM1", "C", 30),
					array("ALPJM2", "C", 30),
					array("KOTPJM", "C", 30),
					array("KDPPJM", "C", 6),
					array("TLPPJM", "C", 15),
					array("USHPJM", "C", 6),
					array("KUSPJM", "C", 30),
					array("STAPJM", "C", 20),
					array("ALUPJ1", "C", 30),
					array("ALUPJ2", "C", 30),
					array("TLUPJM", "C", 15),
					array("GAJPJM", "N", 12, 0),
					array("ALKOR1", "C", 30),
					array("ALKOR2", "C", 30),
					array("KOTKOR", "C", 30),
					array("POSKOR", "C", 6),
					array("STSRMH", "C", 1),
					array("BLNTGL", "N", 2, 0),
					array("THNTGL", "N", 2, 0),
					array("JNSKLM", "C", 1),
					array("TPTLHR", "C", 30),
					array("AGAMA", "C", 1),
					array("PENDDK", "N", 2, 0),
					array("STSKWN", "C", 1),
					array("TGNGAN", "N", 5, 0),
					array("BIAYAH", "N", 18, 0),
					array("BLNKER", "N", 2, 0),
					array("THNKER", "N", 2, 0),
					array("TGLSVY", "D"),
					array("LAMSVY", "N", 2, 0),
					array("PTGSVY", "C", 3),
					array("KPTSAN", "C", 1),
					array("TGLKEP", "D"),
					array("KETKEP", "C", 50),
					array("TGLSPP", "D"),
					array("NAMSLS", "C", 15),
					array("FLAGCT", "C", 13),
					array("TGLINP", "D"),
					array("SJKKER", "C", 6),
					array("SJKTGL", "C", 6),
					array("NAMINP", "C", 30),
					array("RESIKO", "C", 1),
					array("SRTSTJ", "C", 1),
					array("HPHONE", "C", 15),
					array("TPAGER", "C", 15),
					array("TGJTKH", "D"),
					array("TARIKW", "C", 1),
					array("FLCTDP", "N", 1, 0),
					array("UPDTKE", "N", 1, 0),
					array("USERID", "C", 3),
					array("FLCTSP", "N", 1, 0),
					array("UPDTSP", "N", 1, 0),
					array("USERSP", "C", 3),
					array("BATAPK", "N", 7, 0),
					array("BATSPP", "N", 7, 0),
					array("NOMLKS", "C", 6),
					array("NOAPKE", "N", 6, 0),
					array("NOMKTP", "C", 30),
					array("NKTPSD", "D"),
					array("NONPWP", "C", 20),
					array("SPTTHN", "C", 1),
					array("NAMIBU", "C", 30),
					array("JOBKON", "C", 5),
					array("KELURA", "C", 30),
					array("KECAMA", "C", 30),
					array("KDDATI", "C", 4),
					array("TGLSPK", "D"),
					array("TGCTPO", "D")
		);

		// creation
		if (!dbase_create('./temp/DTAPK.dbf', $cols)) {
		  echo "Error, can't create the database\n";
		}

		/*
		// open in read-write mode
		$db = dbase_open('./temp/DTAPK.dbf', 2);

		$insert = array();

		if ($db) {
			dbase_add_record($db, $insert);
			dbase_close($db);
		}
		*/
		$file = './temp/DTAPK.dbf';
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
    		header('Content-Type: application/octet-stream');
   			header('Content-Disposition: attachment; filename="'.basename($file).'"');
   			header('Expires: 0');
   			header('Cache-Control: must-revalidate');
   			header('Pragma: public');
   			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}
	}

	function createAPKJ()
	{
		// field 8 in DTAPKJ.DBF
		$cols = array(
					array("RECOID", "C", 1),
					array("NOMAPK", "N", 6, 0),
					array("KODTRN", "C", 3),
					array("NAMTRN", "C", 30),
					array("NILTRN", "N", 10, 0),
					array("FLTAGI", "C", 1),
					array("FLCAIR", "C", 1),
					array("XLTAGI", "C", 1)
		);

		// creation
		if (!dbase_create('./temp/DTAPKJ.dbf', $cols)) {
		  echo "Error, can't create the database\n";
		}

		// open in read-write mode
		$db = dbase_open('./temp/DTAPKJ.dbf', 2);

		$insert = array('N',223,'IND','ANDRI',8089,'N','N','N');

		if ($db) {

			for ($i = 1; $i <= 4; $i++)
			{
				dbase_add_record($db, $insert);
			}

			dbase_close($db);
		}

		$file = './temp/DTAPKJ.dbf';
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
    		header('Content-Type: application/octet-stream');
   			header('Content-Disposition: attachment; filename="'.basename($file).'"');
   			header('Expires: 0');
   			header('Cache-Control: must-revalidate');
   			header('Pragma: public');
   			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}
	}

	function createAPKK()
	{
		// field 28 in DTAPKK.DBF
		$cols = array(
					array("RECOID", "C", 1),
					array("NOMAPK", "N", 6, 0),
					array("NOMRUT", "N", 4, 0),
					array("WARKEN", "C", 15),
					array("THNKEN", "N", 4, 0),
					array("NOMSIS", "N", 8, 0),
					array("NOMMES", "N", 12, 0),
					array("NAMKOT", "C", 12),
					array("NMBPKB", "C", 30),
					array("ABPKB1", "C", 30),
					array("ABPKB2", "C", 30),
					array("TLBPKB", "C", 15),
					array("HRBPKB", "C", 1),
					array("NOBPKB", "N", 8, 0),
					array("KODEWL", "C", 2),
					array("NOMPOL", "N", 4, 0),
					array("HRNOPO", "C", 3),
					array("KOWIBK", "N", 2, 0),
					array("KODEBK", "N", 2, 0),
					array("FLAGNP", "N", 1, 0),
					array("FLAGKP", "N", 1, 0),
					array("FLAGKD", "C", 1),
					array("TGLINP", "D"),
					array("NKBPKB", "C", 20),
					array("KTBPKB", "D"),
					array("NORANG", "C", 25),
					array("NOMESN", "C", 25),
					array("NRBPKB", "C", 15)
		);

		// creation
		if (!dbase_create('./temp/DTAPKK.dbf', $cols)) {
		  echo "Error, can't create the database\n";
		}

		$file = './temp/DTAPKK.dbf';
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
    		header('Content-Type: application/octet-stream');
   			header('Content-Disposition: attachment; filename="'.basename($file).'"');
   			header('Expires: 0');
   			header('Cache-Control: must-revalidate');
   			header('Pragma: public');
   			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}
	}

	function createAPKP()
	{
		// field 13 in DTAPKP.DBF
		$cols = array(
					array("RECOID", "C", 1),
					array("NOMAPK", "N", 6, 0),
					array("NAMPGS", "C", 30),
					array("KLMPGS", "C", 1),
					array("ALMPGS", "C", 100),
					array("KOTPGS", "C", 30),
					array("KELPGS", "C", 30),
					array("KECPGS", "C", 30),
					array("KABPGS", "C", 4),
					array("KTPPGS", "C", 20),
					array("JBTPGS", "C", 5),
					array("MILPGS", "N", 6, 2),
					array("NPWPGS", "C", 20)
		);
		// creation
		if (!dbase_create('./temp/DTAPKP.dbf', $cols)) {
		  echo "Error, can't create the database\n";
		}

		$file = './temp/DTAPKP.dbf';
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
    		header('Content-Type: application/octet-stream');
   			header('Content-Disposition: attachment; filename="'.basename($file).'"');
   			header('Expires: 0');
   			header('Cache-Control: must-revalidate');
   			header('Pragma: public');
   			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}
	}

	function createAPKS()
	{
		// field 40 in DTAPKS.DBF
		$cols = array(
					array("RECOID", "C", 1),
					array("NOMAPK", "N", 6, 0),
					array("JNSFRM", "C", 1),
					array("SALVAG", "N", 12, 0),
					array("SALWAL", "N", 12, 0),
					array("SALAKH", "N", 12, 0),
					array("JUMLIN", "N", 12, 0),
					array("JUMOUT", "N", 12, 0),
					array("JUMTRN", "N", 4, 0),
					array("SAAVGP", "N", 12, 0),
					array("SAWALP", "N", 12, 0),
					array("SAAKHP", "N", 12, 0),
					array("JUMINP", "N", 12, 0),
					array("JLOUTP", "N", 12, 0),
					array("JLTRNP", "N", 4, 0),
					array("JLKENM", "N", 3, 0),
					array("GARASI", "C", 1),
					array("HUBFIN", "C", 1),
					array("KONLIN", "C", 1),
					array("KONRMH", "C", 1),
					array("KATPRS", "C", 1),
					array("OMZBLN", "N", 15, 0),
					array("JUMKYW", "N", 5, 0),
					array("BIABLN", "N", 15, 0),
					array("TMPUSH", "C", 1),
					array("SCORES", "N", 4, 0),
					array("GRADES", "C", 1),
					array("JNSPKT", "C", 7),
					array("TGDPAS", "D"),
					array("TGDKTK", "D"),
					array("PRMASK", "N", 8, 0),
					array("HUBFKE", "N", 2, 0),
					array("PTMKRD", "N", 1, 0),
					array("CDOC01", "N", 2, 0),
					array("CDOC02", "C", 1),
					array("CDOC03", "C", 1),
					array("CDOC04", "C", 1),
					array("CDOC05", "C", 1),
					array("FLCOMV", "C", 1),
					array("NMPRSK", "C", 50)
		);
		// creation
		if (!dbase_create('./temp/DTAPKS.dbf', $cols)) {
		  echo "Error, can't create the database\n";
		}

		$file = './temp/DTAPKP.dbf';
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
    		header('Content-Type: application/octet-stream');
   			header('Content-Disposition: attachment; filename="'.basename($file).'"');
   			header('Expires: 0');
   			header('Cache-Control: must-revalidate');
   			header('Pragma: public');
   			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}
	}

	function createAPKT()
	{
		// field 69 in DTAPKT.DBF
		$cols = array(
					array("RECOID", "C", 1),
					array("NOMAPK", "N", 6, 0),
					array("KODCAB", "C", 3),
					array("KODEKR", "N", 2, 0),
					array("TGLBYR", "D"),
					array("NABANK", "C", 30),
					array("ACBANK", "C", 25),
					array("KODEBK", "N", 2, 0),
					array("KOWIBK", "N", 2, 0),
					array("NOACBK", "C", 25),
					array("NOMBBK", "N", 2, 0),
					array("NOMBBT", "N", 2, 0),
					array("MASAFL", "N", 2, 0),
					array("JENKEN", "C", 6),
					array("JUMKEN", "N", 4, 0),
					array("THNKEN", "N", 4, 0),
					array("HRGOTR", "N", 11, 0),
					array("SILIND", "N", 4, 0),
					array("UMPMLK", "N", 11, 0),
					array("PREMPL", "N", 8, 0),
					array("PERBAP", "N", 5, 2),
					array("PERBEP", "N", 5, 2),
					array("BIANGP", "N", 10, 0),
					array("JLANGP", "N", 10, 0),
					array("JAYTSP", "N", 10, 0),
					array("ANYTSP", "C", 1),
					array("UMDEAL", "N", 10, 0),
					array("PREMDL", "N", 8, 0),
					array("PERBAD", "N", 5, 2),
					array("PERBED", "N", 5, 2),
					array("BIANGD", "N", 10, 0),
					array("JLANGD", "N", 10, 0),
					array("JLAYTS", "N", 10, 0),
					array("ANGYTS", "C", 1),
					array("POLANG", "C", 1),
					array("CARBAR", "C", 1),
					array("ANGDEL", "C", 1),
					array("LAMANG", "N", 2, 0),
					array("MASANG", "N", 2, 0),
					array("BEKANG", "N", 2, 0),
					array("BEKANP", "N", 2, 0),
					array("ANGPER", "D"),
					array("TGANGS", "N", 2, 0),
					array("APBYRM", "C", 1),
					array("APBYRK", "N", 2, 0),
					array("APBYRV", "N", 9, 0),
					array("APBPPD", "C", 1),
					array("DEPPPD", "C", 1),
					array("BIAADM", "N", 9, 0),
					array("DENDAD", "N", 8, 0),
					array("BINORI", "N", 8, 0),
					array("BIASUR", "N", 8, 0),
					array("POTANG", "N", 9, 0),
					array("PDTLIN", "N", 9, 0),
					array("KODSUP", "N", 2, 0),
					array("NOMSUP", "N", 2, 0),
					array("NAMSUP", "C", 30),
					array("ALSUP1", "C", 30),
					array("ALSUP2", "C", 30),
					array("KOTSUP", "C", 30),
					array("ANBANK", "C", 30),
					array("FLAGCR", "C", 1),
					array("FLAGUM", "C", 1),
					array("TGLSTD", "D"),
					array("TGSRPY", "D"),
					array("APBYDK", "N", 9, 0),
					array("DNDPHR", "N", 5, 2),
					array("KOWIAS", "N", 2, 0),
					array("KODEAS", "N", 2, 0)
		);

		// creation
		if (!dbase_create('./temp/DTAPKT.dbf', $cols)) {
		  echo "Error, can't create the database\n";
		}

		$file = './temp/DTAPKT.dbf';
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
    		header('Content-Type: application/octet-stream');
   			header('Content-Disposition: attachment; filename="'.basename($file).'"');
   			header('Expires: 0');
   			header('Cache-Control: must-revalidate');
   			header('Pragma: public');
   			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}
	}

	function createCAIR()
	{
		// field 2 in DTCAIR.DBF
		$cols = array(
					array("NOMAPK", "N", 6, 0),
					array("NILPCR", "N", 11, 0)
		);
		// creation
		if (!dbase_create('./temp/DTCAIR.dbf', $cols)) {
		  echo "Error, can't create the database\n";
		}
		$file = './temp/DTCAIR.dbf';
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
    		header('Content-Type: application/octet-stream');
   			header('Content-Disposition: attachment; filename="'.basename($file).'"');
   			header('Expires: 0');
   			header('Cache-Control: must-revalidate');
   			header('Pragma: public');
   			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}
	}
}