<?php

class APKOrang extends CI_Controller
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
		$this->load->view('vapkorang');
	}
	
	function kodeapk()
	{
		$nstart = trim($this->input->post('start'));
		
		$refno = trim($this->input->post('fs_refno'));
		$nmkons = trim($this->input->post('fs_nm_konsumen'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodeapk_all($refno,$nmkons);
		$total = $ssql->num_rows();
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodeapk($refno,$nmkons,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kodejenis()
	{
		$kdjenis = trim($this->input->post('fs_kd_jenis'));
		$nmjenis = trim($this->input->post('fs_nm_jenis'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodejenis($kdjenis,$nmjenis);
		
		echo json_encode($ssql->result());
	}
	
	function kodejenisp()
	{
		$kdjenisp = trim($this->input->post('fs_kd_jenis'));
		$nmjenisp = trim($this->input->post('fs_nm_jenis'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodejenisp($kdjenisp,$nmjenisp);
		
		echo json_encode($ssql->result());
	}
	
	function kodepola()
	{
		$kdpola = trim($this->input->post('fs_kd_pola'));
		$nmpola = trim($this->input->post('fs_nm_pola'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodepola($kdpola,$nmpola);
		
		echo json_encode($ssql->result());
	}
	
	function kodejenispaket()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdjenisp = trim($this->input->post('fs_kd_jenisp'));
		$nmjenisp = trim($this->input->post('fs_nm_jenisp'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodejenispaket_all($kdjenisp,$nmjenisp);
		$total = $ssql->num_rows();
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodejenispaket($kdjenisp,$nmjenisp,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kodekab()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdkab = trim($this->input->post('fs_kd_kab'));
		$nmkab = trim($this->input->post('fs_nm_kab'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodekab_all($kdkab,$nmkab);
		$total = $ssql->num_rows();
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodekab($kdkab,$nmkab,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kodekatusaha()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdkat = trim($this->input->post('fs_kd_katusaha'));
		$nmkat = trim($this->input->post('fs_nm_katusaha'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodekatusaha_all($kdkat,$nmkat);
		$total = $ssql->num_rows();
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodekatusaha($kdkat,$nmkat,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kodekerja()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdkerja = trim($this->input->post('fs_kd_kerja'));
		$nmkerja = trim($this->input->post('fs_nm_kerja'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodekerja_all($kdkerja,$nmkerja);
		$total = $ssql->num_rows();
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodekerja($kdkerja,$nmkerja,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kodeagama()
	{
		$kdagama = trim($this->input->post('fs_kd_agama'));
		$nmagama = trim($this->input->post('fs_nm_agama'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodeagama($kdagama,$nmagama);
		
		echo json_encode($ssql->result());
	}
	
	function kodependidikan()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdpendidikan = trim($this->input->post('fs_kd_pendidikan'));
		$nmpendidikan = trim($this->input->post('fs_nm_pendidikan'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodependidikan_all($kdpendidikan,$nmpendidikan);
		$total = $ssql->num_rows();
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodependidikan($kdpendidikan,$nmpendidikan,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function koderumah()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdrumah = trim($this->input->post('fs_kd_rumah'));
		$nmrumah = trim($this->input->post('fs_nm_rumah'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->koderumah_all($kdrumah,$nmrumah);
		$total = $ssql->num_rows();
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->koderumah($kdrumah,$nmrumah,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kodekend()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdkend = trim($this->input->post('fs_kd_kend'));
		$nmkend = trim($this->input->post('fs_nm_kend'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodekend_all($kdkend,$nmkend);
		$total = $ssql->num_rows();
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodekend($kdkend,$nmkend,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kodekota()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdkota = trim($this->input->post('fs_kd_kota'));
		$nmkota = trim($this->input->post('fs_nm_kota'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodekota_all($kdkota,$nmkota);
		$total = $ssql->num_rows();
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodekota($kdkota,$nmkota,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kodelemkeu()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdlemkeu = trim($this->session->userdata('gCabang'));
		$nmlemkeu = trim($this->session->userdata('gCabang'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodelemkeu_all($kdlemkeu,$nmlemkeu);
		$total = $ssql->num_rows();
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodelemkeu($kdlemkeu,$nmlemkeu,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kodesupp()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdsupp = trim($this->input->post('fs_kd_supp'));
		$nmsupp = trim($this->input->post('fs_nm_supp'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodesupp_all($kdsupp,$nmsupp);
		$total = $ssql->num_rows();
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodesupp($kdsupp,$nmsupp,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function kodecabang()
	{
		$nstart = trim($this->input->post('start'));
		
		$kdsupp = trim($this->input->post('fs_kd_supp'));
		$kdcab = trim($this->input->post('fs_kd_cabang'));
		$nmcab = trim($this->input->post('fs_kd_cabang'));
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodecabang_all($kdsupp,$kdcab,$nmcab);
		$total = $ssql->num_rows();
		
		$this->load->model('mSearch','',true);
		$ssql = $this->mSearch->kodecabang($kdsupp,$kdcab,$nmcab,$nstart);
		
		echo '({"total":"'.$total.'","hasil":'.json_encode($ssql->result()).'})';
	}
	
	function rate()
	{
		$nper = trim($this->input->post('fn_nper'));
		$pmt = trim($this->input->post('fn_pmt')) * -1;
		$pv = trim($this->input->post('fn_pv'));
		$fv = trim($this->input->post('fn_fv'));
		$tipe = trim($this->input->post('fn_tipe'));
		$ges = trim($this->input->post('fn_guess'));
		
		$this->load->model('mMainModul','',true);
		$nilairate = $this->mMainModul->RATE($nper,$pmt,$pv);
		$nilairate = round($nilairate * 12 * 100, 2);
		
		$hasil = array('sukses' => true, 'nilairate' => $nilairate);
		echo json_encode($hasil);
	}
	
	function ceksave()
	{
		$refno = trim($this->input->post('fs_refno'));
		
		if (trim($refno) == '' or trim($refno) == 'AUTOMATIC')
		{
			$hasil = array('sukses' => true, 'hasil' => 'lanjut');
			echo json_encode($hasil);
		}
		else
		{
			$this->load->model('mAPKorang','',true);
			$ssql = $this->mAPKorang->cek_kode($refno);
			
			if ($ssql->num_rows() > 0)
			{
				$hasil = array('sukses' => true, 'hasil' => 'Reference number already exists, do you want to update it?');
				echo json_encode($hasil);
			}
			else
			{
				$hasil = array('sukses' => false, 'hasil' => 'Saving Failed, Reference number unknown!!');
				echo json_encode($hasil);
			}
		}
	}
	
	function save()
	{
		$refno = trim($this->input->post('fs_refno'));
		$refnodt = trim($this->input->post('fd_refno'));
		$jeniskons = trim($this->input->post('fs_kd_jns_konsumen'));
		$jenispiu = trim($this->input->post('fs_kd_jns_piutang'));
		$polatrs = trim($this->input->post('fs_kd_pola_trs'));
		
		$jenispaket = trim($this->input->post('fs_kd_jns_paket'));
		$nmkons = trim($this->input->post('fs_nm_konsumen'));
		$noktp = trim($this->input->post('fs_no_ktp'));
		$tglktp = trim($this->input->post('fd_ktp'));
		$tlp = trim($this->input->post('fs_no_telp'));
		
		$hp = trim($this->input->post('fs_no_hp'));
		$alamat = trim($this->input->post('fs_alamat'));
		$kel = trim($this->input->post('fs_kelurahan'));
		$kec = trim($this->input->post('fs_kecamatan'));
		$kdkab = trim($this->input->post('fs_kd_kabupaten'));
		
		$kota = trim($this->input->post('fs_kota'));
		$kodepos = trim($this->input->post('fs_kd_pos'));
		$npwp = trim($this->input->post('fs_no_npwp'));
		$spt = trim($this->input->post('fb_status_spt'));
		
		
		$nmpt = trim($this->input->post('fs_nm_pt'));
		$alamatpt = trim($this->input->post('fs_alamat_pt'));
		$tlppt = trim($this->input->post('fs_tlp_pt'));
		$kdkatusaha = trim($this->input->post('fs_kd_kat_usaha'));
		$kdkatpt = trim($this->input->post('fn_kategori_pt'));
		
		$kdkondisiktr = trim($this->input->post('fn_kondisi_ktr'));
		$kdusaha = trim($this->input->post('fs_kd_usaha'));
		$ketusaha = trim($this->input->post('fs_ket_usaha'));
		$tglkerja = trim($this->input->post('fd_kerja'));
		$gaji = trim($this->input->post('fn_pendapatan'));
		
		$tmplahir = trim($this->input->post('fs_tempat_lahir'));
		$tgllahir = trim($this->input->post('fd_tgl_lahir'));
		$jk = trim($this->input->post('fn_jns_kelamin'));
		$kdagama = trim($this->input->post('fs_kd_agama'));
		$nmibu = trim($this->input->post('fs_nm_ibu'));
		
		$kdpddkan = trim($this->input->post('fs_kd_pendidikan'));
		$statuskawin = trim($this->input->post('fn_status_kawin'));
		$tanggungan = trim($this->input->post('fn_tanggungan'));
		$statusrmh = trim($this->input->post('fs_status_rumah'));
		$tgltinggal = trim($this->input->post('fd_tinggal'));
		
		$biaya = trim($this->input->post('fn_biaya_bulan'));
		$alamatsurat = trim($this->input->post('fs_alamat_surat'));
		$kotakons = trim($this->input->post('fs_kotakons'));
		$kodeposkons = trim($this->input->post('fs_kd_poskons'));
		$garasi = trim($this->input->post('fb_garasi_rumah'));
		
		$jmlkend = trim($this->input->post('fn_jml_kendaraan'));
		$kalikredit = trim($this->input->post('fn_kali_kredit'));
		$kreditke = trim($this->input->post('fn_kredit_ke'));
		$kondisilingk = trim($this->input->post('fn_kondisi_lingk'));
		
		
		$jnskend = trim($this->input->post('fs_kd_jns_kend'));
		$rangka = trim($this->input->post('fs_rangka'));
		$mesin = trim($this->input->post('fs_mesin'));
		$silinder = trim($this->input->post('fn_silinder'));
		$thn = trim($this->input->post('fn_tahun'));
		
		$warna = trim($this->input->post('fs_warna'));
		$komersial = trim($this->input->post('fb_komersial'));
		$kdbpkb = trim($this->input->post('fb_nm_bpkb'));
		$nobpkb = trim($this->input->post('fs_no_bpkb'));
		$nopol = trim($this->input->post('fs_nopol'));
		
		$nmbpkb = trim($this->input->post('fs_nm_bpkb'));
		$almbpkb = trim($this->input->post('fs_alm_bpkb'));
		$kotabpkb = trim($this->input->post('fs_kd_kota_bpkb'));
		$kdlemleu = trim($this->input->post('fs_kd_lbg_keu'));
		$kdjnsass = trim($this->input->post('fs_kd_jns_ass'));
		
		$kdcab = trim($this->input->post('fs_kd_cabang'));
		$sales = trim($this->input->post('fs_sales'));
		
		$jml = 0;
		$kdsupp[0] = '';
		$kdsupp[1] = '';
		$kdsupp = explode('-', trim($this->input->post('fs_kd_supplier')));
		$nmsupp = trim($this->input->post('fs_nm_supp'));
		
		$jml = count($kdsupp) - 1;
		if ($jml == 0)
		{
			$kdsupp[0] = trim($this->input->post('fs_kd_supplier'));
			$kdsupp[1] = trim($this->input->post('fs_kd_supplier'));
		}
		
		
		$xupdate = false;
		$this->load->model('mAPKOrang','',true);
		$ssql = $this->mAPKOrang->cek_kode($refno);
		
		if ($ssql->num_rows() > 0)
		{
			$xupdate = true;
		}
		else
		{
			$this->db->trans_begin();
			$xPrefix = date('y');
			$xcabang = trim($this->session->userdata('gCabang'));
			
			$this->load->model('mMainModul','',true);
			$ssql = $this->mMainModul->ambil_kodeapk($xPrefix);
			
			if ($ssql->num_rows() > 0)
			{
				$ssql = $ssql->row();
				$refno = $ssql->$xcabang;
				$data = array(
					$xcabang => $refno
				);
				
				$this->db->update('tm_parameter', $data);
			}
			else
			{
				$refno = $xPrefix.'0001';
				$data = array(
					$xcabang => $refno
				);
				
				$this->db->update('tm_parameter', $data);
			}
			
			$xupdate = false;
			$this->db->trans_commit();
		}
		
		$dt = array(
			'fs_kd_comp' => trim($this->session->userdata('gComp')),
			'fs_kd_dept' => trim($this->session->userdata('gDept')),
			'fs_count' => trim($this->session->userdata('gCount')),
			'fs_refno' => trim($refno),
			'fd_refno' => trim($refnodt),
			
			'fs_kd_jns_konsumen' => trim($jeniskons),
			'fs_kd_jns_piutang' => trim($jenispiu),
			'fs_kd_pola_trs' => trim($polatrs),
			'fs_kd_jns_paket' => trim($jenispaket),
			'fs_nm_konsumen' => trim($nmkons),
			
			'fs_no_ktp' => trim($noktp),
			'fd_ktp' => trim($tglktp),
			'fs_no_telp' => trim($tlp),
			'fs_no_hp' => trim($hp),
			'fs_alamat' => trim($alamat),
			
			'fs_kelurahan' => trim($kel),
			'fs_kecamatan' => trim($kec),
			'fs_kd_kabupaten' => trim($kdkab),
			'fs_kota' => trim($kota),
			'fs_kd_pos' => trim($kodepos),
			
			'fs_no_npwp' => trim($npwp),
			'fb_status_spt' => trim($spt)
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
			
			$this->db->insert('tx_apkheader', $data);
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
			$this->db->update('tx_apkheader', $data);
		}
		
		
		//hapus tx_apkkonsumen
		$where = "fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_apkkonsumen');
		//eof hapus tx_apkkonsumen
		
		$data = array(
			'fs_refno' => trim($refno),
			'fs_nm_pt' => trim($nmpt),
			'fs_alamat_pt' => trim($alamatpt),
			'fs_tlp_pt' => trim($tlppt),
			'fs_kd_kat_usaha' => trim($kdkatusaha),
			
			'fn_kategori_pt' => trim($kdkatpt),
			'fn_kondisi_ktr' => trim($kdkondisiktr),
			'fs_kd_usaha' => trim($kdusaha),
			'fs_ket_usaha' => trim($ketusaha),
			'fd_kerja' => trim($tglkerja),
			
			'fn_pendapatan' => trim($gaji),
			'fs_tempat_lahir' => trim($tmplahir),
			'fd_tgl_lahir' => trim($tgllahir),
			'fn_jns_kelamin' => trim($jk),
			'fs_kd_agama' => trim($kdagama),
			
			'fs_nm_ibu' => trim($nmibu),
			'fs_kd_pendidikan' => trim($kdpddkan),
			'fn_status_kawin' => trim($statuskawin),
			'fn_tanggungan' => trim($tanggungan),
			'fs_status_rumah' => trim($statusrmh),
			
			'fd_tinggal' => trim($tgltinggal),
			'fn_biaya_bulan' => trim($biaya),
			'fs_alamat_surat' => trim($alamatsurat),
			'fs_kota' => trim($kotakons),
			'fs_kd_pos' => trim($kodeposkons),
			
			'fb_garasi_rumah' => trim($garasi),
			'fn_jml_kendaraan' => trim($jmlkend),
			'fn_kali_kredit' => trim($kalikredit),
			'fn_kredit_ke' => trim($kreditke),
			'fn_kondisi_lingk' => trim($kondisilingk)
		);
		$this->db->insert('tx_apkkonsumen', $data);
		
		
		//hapus tx_apkkendaraan
		$where = "fs_refno = '".trim($refno)."'";
		
		$this->db->where($where);
		$this->db->delete('tx_apkkendaraan');
		//eof hapus tx_apkkendaraan
		
		$data = array(
			'fs_refno' => trim($refno),
			'fs_kd_jns_kend' => trim($jnskend),
			'fs_rangka' => trim($rangka),
			'fs_mesin' => trim($mesin),
			'fn_silinder' => trim($silinder),
			'fn_tahun' => trim($thn),
			
			'fs_warna' => trim($warna),
			'fb_komersial' => trim($komersial),
			'fb_nm_bpkb' => trim($kdbpkb),
			'fs_no_bpkb' => trim($nobpkb),
			'fs_nopol' => trim($nopol),
			
			'fs_nm_bpkb' => trim($nmbpkb),
			'fs_alm_bpkb' => trim($almbpkb),
			'fs_kd_kota_bpkb' => trim($kotabpkb),
			'fs_kd_lbg_keu' => trim($kdlemleu),
			'fs_kd_jns_ass' => trim($kdjnsass),
			
			'fs_kd_supplier' => trim($kdsupp[0]),
			'fs_kd_supplier2' => trim($kdsupp[1]),
			'fs_kd_cabang' => trim($kdcab),
			'fs_sales' => trim($sales)
		);
		$this->db->insert('tx_apkkendaraan', $data);
		
		
		if ($xupdate == false)
		{
			$hasil = array('sukses' => true, 'hasil' => 'Simpan APK Sukses', 'refno' => $refno);
			echo json_encode($hasil);
		}
		else
		{
			$hasil = array('sukses' => true, 'hasil' => 'Simpan APK Update Sukses', 'refno' => $refno);
			echo json_encode($hasil);
		}
	}
}
?>