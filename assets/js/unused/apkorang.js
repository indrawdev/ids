Ext.Loader.setConfig({
	enabled: true
});

Ext.Loader.setPath('Ext.ux', gBaseUX);

Ext.require([
	'Ext.ux.form.NumericField',
	'Ext.ux.LiveSearchGridPanel',
	'Ext.ux.ProgressBarPager'
]);

var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';

function trim(text) {
    return text.replace(/^\s+|\s+$/g, '');
}

function tglDMY(text) {
	var x = '-';
	return text.substr(0,2).concat(x,text.substr(3,2),x,text.substr(6,4));
}

/*	APK	*/
var grupAPK = Ext.create('Ext.data.Store', {
	autoLoad: true,
	fields: [
		'fs_refno','fd_refno',
		'fs_kd_jns_konsumen','fs_nm_jns_konsumen',
		'fs_kd_jns_piutang','fs_nm_jns_piutang',
		'fs_kd_pola_trs','fs_nm_pola_trs',
		'fs_kd_jns_paket','fs_nm_jns_paket',
		'fs_nm_konsumen','fs_no_ktp','fd_ktp',
		'fs_no_telp','fs_no_hp','fs_alamat',
		'fs_kelurahan','fs_kecamatan',
		'fs_kd_kabupaten','fs_nm_kabupaten',
		'fs_kota','fs_kd_pos','fs_no_npwp',
		'fs_kd_spt','fs_nm_spt',
		
		'fs_nm_pt','fs_alamat_pt','fs_tlp_pt',
		'fs_kd_katusaha','fs_nm_katusaha','fs_kd_katktr',
		'fs_nm_katktr','fs_kd_kondisiktr','fs_nm_kondisiktr',
		'fs_kd_kerja','fs_nm_kerja','fs_ket_usaha',
		'fd_kerja','fn_pendapatan','fs_tempat_lahir',
		'fd_tgllahir','fs_kd_jk','fs_nm_jk',
		'fs_kd_agama','fs_nm_agama','fs_nm_ibu',
		'fs_kd_pendidikan','fs_nm_pendidikan','fs_kd_kawin',
		'fs_nm_kawin','fn_tanggungan','fs_kd_rumah',
		'fs_nm_rumah','fd_tinggal','fn_biaya_bulan',
		'fs_alamat_surat','fs_kotakons','fs_kd_poskons',
		'fn_jml_kendaraan','fn_kredit_ke','fs_kd_garasi',
		'fs_nm_garasi','fs_kd_kalikredit','fs_nm_kalikredit',
		'fs_kd_kondisilingk','fs_nm_kondisilingk',
		
		'fs_kd_kend','fs_nm_kend','fs_rangka',
		'fs_mesin','fn_silinder','fn_tahun',
		'fs_warna','fs_kd_komersial','fs_nm_komersial',
		'fs_kd_namabpkb','fs_nm_namabpkb','fs_no_bpkb',
		'fs_nopol','fs_nm_bpkb','fs_alm_bpkb',
		'fs_kd_kota_bpkb','fs_nm_kota_bpkb',
		'fs_kd_lemkeu','fs_nm_lemkeu','fs_kd_ass',
		'fs_nm_ass','fs_kd_supp','fs_nm_supp',
		'fs_kd_cabang','fs_nm_cabang','fs_sales'
		
	],
	pageSize: 25,
	proxy: {
		actionMethods: 'POST',
		reader: {
			root: 'hasil',
			totalProperty: 'total',
			type: 'json'
		},
		type: 'ajax',
		url: 'apkorang/kodeapk'
	}
});

grupAPK.on('beforeload', function(store, operation, eOpts) {
	operation.params = {
		'fs_refno': Ext.getCmp('0cboRefno').getValue(),
		'fs_nm_konsumen': Ext.getCmp('0cboRefno').getValue()
	};
}, this);

var popGrid = Ext.create('Ext.ux.LiveSearchGridPanel', {
	autoDestroy: true,
	height: 250,
	width: 550,
	store: grupAPK,
	bbar: Ext.create('Ext.PagingToolbar', {
		displayInfo: true,
		displayMsg: 'Displaying record {0} - {1} of {2}',
		emptyMsg: "No record to display",
		pageSize: 25,
		plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
		store: grupAPK,
		items:[
			'-', {
			text: 'Exit',
			handler: function() {
				helpGrid.hide();
			}
		}]
	}),
	columns: [
		{xtype: 'rownumberer', width: 45},
		{text: "Kode APK", dataIndex: 'fs_refno', width: 100},
		{text: "Tgl APK", dataIndex: 'fd_refno'},
		{text: "Jenis Kons Cd", dataIndex: 'fs_kd_jns_konsumen', hidden: true},
		{text: "Jenis Kons", dataIndex: 'fs_nm_jns_konsumen', hidden: true},
		{text: "Jenis Piutang Cd", dataIndex: 'fs_kd_jns_piutang', hidden: true},
		{text: "Jenis Piutang", dataIndex: 'fs_nm_jns_piutang', hidden: true},
		{text: "Pola Trs Cd", dataIndex: 'fs_kd_pola_trs', hidden: true},
		{text: "Pola Trs", dataIndex: 'fs_nm_pola_trs', hidden: true},
		{text: "Jenis Paket Cd", dataIndex: 'fs_kd_jns_paket', hidden: true},
		{text: "Jenis Paket", dataIndex: 'fs_nm_jns_paket', hidden: true},
		{text: "Nama Konsumen", dataIndex: 'fs_nm_konsumen'},
		{text: "No.KTP", dataIndex: 'fs_no_ktp', hidden: true},
		{text: "Tgl KTP", dataIndex: 'fd_ktp', hidden: true},
		{text: "Telp", dataIndex: 'fs_no_telp'},
		{text: "No.HP", dataIndex: 'fs_no_hp'},
		{text: "Alamat", dataIndex: 'fs_alamat', hidden: true},
		{text: "Kelurahan", dataIndex: 'fs_kelurahan', hidden: true},
		{text: "Kecamatan", dataIndex: 'fs_kecamatan', hidden: true},
		{text: "Kabupaten Cd", dataIndex: 'fs_kd_kabupaten', hidden: true},
		{text: "Kabupaten", dataIndex: 'fs_nm_kabupaten', hidden: true},
		{text: "Kota", dataIndex: 'fs_kota', hidden: true},
		{text: "Kode Pos", dataIndex: 'fs_kd_pos', hidden: true},
		{text: "NPWP", dataIndex: 'fs_no_npwp', hidden: true},
		{text: "Status SPT Cd", dataIndex: 'fs_kd_spt', hidden: true},
		{text: "Status SPT", dataIndex: 'fs_nm_spt', hidden: true},
		
		{text: "Nama PT", dataIndex: 'fs_nm_pt', hidden: true},
		{text: "Alamat PT", dataIndex: 'fs_alamat_pt', hidden: true},
		{text: "Tlp PT", dataIndex: 'fs_tlp_pt', hidden: true},
		{text: "Kode Kat Usaha", dataIndex: 'fs_kd_katusaha', hidden: true},
		{text: "Nama Kat Usaha", dataIndex: 'fs_nm_katusaha', hidden: true},
		{text: "Kode Kat Ktr", dataIndex: 'fs_kd_katktr', hidden: true},
		{text: "Nama Kat Ktr", dataIndex: 'fs_nm_katktr', hidden: true},
		{text: "Kode Kondisi Ktr", dataIndex: 'fs_kd_kondisiktr', hidden: true},
		{text: "Nama Kondisi Ktr", dataIndex: 'fs_kd_kondisiktr', hidden: true},
		{text: "Kode Kerja", dataIndex: 'fs_kd_kerja', hidden: true},
		{text: "Nama Kerja", dataIndex: 'fs_nm_kerja', hidden: true},
		{text: "Ket Kerja", dataIndex: 'fs_ket_usaha', hidden: true},
		{text: "Tgl Kerja", dataIndex: 'fd_kerja', hidden: true},
		{text: "Pendapatan", dataIndex: 'fn_pendapatan', hidden: true},
		{text: "Tempat Lahir", dataIndex: 'fs_tempat_lahir', hidden: true},
		{text: "Tgl Lahir", dataIndex: 'fd_tgllahir', hidden: true},
		{text: "Kode JK", dataIndex: 'fs_kd_jk', hidden: true},
		{text: "Nama JK", dataIndex: 'fs_nm_jk', hidden: true},
		{text: "Kode Agama", dataIndex: 'fs_kd_agama', hidden: true},
		{text: "Nama Agama", dataIndex: 'fs_nm_agama', hidden: true},
		{text: "Nama Ibu", dataIndex: 'fs_nm_ibu', hidden: true},
		{text: "Kode Pddkan", dataIndex: 'fs_kd_pendidikan', hidden: true},
		{text: "Nama Pddkan", dataIndex: 'fs_nm_pendidikan', hidden: true},
		{text: "Kode Kawin", dataIndex: 'fs_kd_kawin', hidden: true},
		{text: "Nama Kawin", dataIndex: 'fs_nm_kawin', hidden: true},
		{text: "Tanggungan", dataIndex: 'fn_tanggungan', hidden: true},
		{text: "Kode Rmh", dataIndex: 'fs_kd_rumah', hidden: true},
		{text: "Nama Rmh", dataIndex: 'fs_nm_rumah', hidden: true},
		{text: "Tgl Tinggal", dataIndex: 'fd_tinggal', hidden: true},
		{text: "Biaya", dataIndex: 'fn_biaya_bulan', hidden: true},
		{text: "Alamat Surat", dataIndex: 'fs_alamat_surat', hidden: true},
		{text: "Kota Kons", dataIndex: 'fs_kotakons', hidden: true},
		{text: "KodePos Kons", dataIndex: 'fs_kd_poskons', hidden: true},
		{text: "Jml Kend", dataIndex: 'fn_jml_kendaraan', hidden: true},
		{text: "Kredit Ke", dataIndex: 'fn_kredit_ke', hidden: true},
		{text: "Kode Garasi", dataIndex: 'fs_kd_garasi', hidden: true},
		{text: "Nama Garasi", dataIndex: 'fs_nm_garasi', hidden: true},
		{text: "Kode Kali Kredit", dataIndex: 'fs_kd_kalikredit', hidden: true},
		{text: "Nama Kali Kredit", dataIndex: 'fs_nm_kalikredit', hidden: true},
		{text: "Kode Kondisi Lingk", dataIndex: 'fs_kd_kondisilingk', hidden: true},
		{text: "Nama Kondisi Lingk", dataIndex: 'fs_nm_kondisilingk', hidden: true},
		
		{text: "Kode Kend", dataIndex: 'fs_kd_kend', hidden: true},
		{text: "Nama Kend", dataIndex: 'fs_nm_kend', hidden: true},
		{text: "Rangka", dataIndex: 'fs_rangka', hidden: true},
		{text: "Mesin", dataIndex: 'fs_mesin', hidden: true},
		{text: "Silinder", dataIndex: 'fn_silinder', hidden: true},
		{text: "Tahun", dataIndex: 'fn_tahun', hidden: true},
		{text: "Warna", dataIndex: 'fs_warna', hidden: true},
		{text: "Kode Komersial", dataIndex: 'fs_kd_komersial', hidden: true},
		{text: "Nama Komersial", dataIndex: 'fs_nm_komersial', hidden: true},
		{text: "Kode Nama Bpkb", dataIndex: 'fs_kd_namabpkb', hidden: true},
		{text: "Nama Nama Bpkb", dataIndex: 'fs_nm_namabpkb', hidden: true},
		{text: "No Bpkb", dataIndex: 'fs_no_bpkb', hidden: true},
		{text: "Nopol", dataIndex: 'fs_nopol', hidden: true},
		{text: "Nama Bpkb", dataIndex: 'fs_nm_bpkb', hidden: true},
		{text: "Alamat Bpkb", dataIndex: 'fs_alm_bpkb', hidden: true},
		{text: "Kode Kota", dataIndex: 'fs_kd_kota_bpkb', hidden: true},
		{text: "Nama Kota", dataIndex: 'fs_nm_kota_bpkb', hidden: true},
		{text: "Kode LemKeu", dataIndex: 'fs_kd_lemkeu', hidden: true},
		{text: "Nama LemKeu", dataIndex: 'fs_nm_lemkeu', hidden: true},
		{text: "Kode Ass", dataIndex: 'fs_kd_ass', hidden: true},
		{text: "Nama Ass", dataIndex: 'fs_nm_ass', hidden: true},
		{text: "Kode Supp", dataIndex: 'fs_kd_supp', hidden: true},
		{text: "Nama Supp", dataIndex: 'fs_nm_supp', hidden: true},
		{text: "Kode Cabang", dataIndex: 'fs_kd_cabang', hidden: true},
		{text: "Nama Cabang", dataIndex: 'fs_nm_cabang', hidden: true},
		{text: "Sales", dataIndex: 'fs_sales', hidden: true}
		
	],
    listeners: {
        itemclick: function(grid, record)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			record = grid.store.getAt(xpos.row);
			Ext.getCmp('0cboRefno').setValue(record.get('fs_refno'));
			Ext.getCmp('0txtTgl').setValue(record.get('fd_refno'));
			Ext.getCmp('0cboJenis').setValue(record.get('fs_kd_jns_konsumen'));
			Ext.getCmp('0txtJenis').setValue(record.get('fs_nm_jns_konsumen'));
			Ext.getCmp('0cboJenisPiutang').setValue(record.get('fs_kd_jns_piutang'));
			Ext.getCmp('0txtJenisPiutang').setValue(record.get('fs_nm_jns_piutang'));
			Ext.getCmp('0cboPolaTrs').setValue(record.get('fs_kd_pola_trs'));
			Ext.getCmp('0txtPolaTrs').setValue(record.get('fs_nm_pola_trs'));
			Ext.getCmp('0cboJenisPaket').setValue(record.get('fs_kd_jns_paket'));
			Ext.getCmp('0txtJenisPaket').setValue(record.get('fs_nm_jns_paket'));
			Ext.getCmp('0txtNama').setValue(record.get('fs_nm_konsumen'));
			Ext.getCmp('0txtKtp').setValue(record.get('fs_no_ktp'));
			Ext.getCmp('0txtTglKtp').setValue(record.get('fd_ktp'));
			Ext.getCmp('0txtTlp').setValue(record.get('fs_no_telp'));
			Ext.getCmp('0txtHp').setValue(record.get('fs_no_hp'));
			Ext.getCmp('0txtAlamat').setValue(record.get('fs_alamat'));
			Ext.getCmp('0txtKel').setValue(record.get('fs_kelurahan'));
			Ext.getCmp('0txtKec').setValue(record.get('fs_kecamatan'));
			Ext.getCmp('0cboKab').setValue(record.get('fs_kd_kabupaten'));
			Ext.getCmp('0txtKab').setValue(record.get('fs_nm_kabupaten'));
			Ext.getCmp('0txtKota').setValue(record.get('fs_kota'));
			Ext.getCmp('0txtKodePos').setValue(record.get('fs_kd_pos'));
			Ext.getCmp('0txtNpwp').setValue(record.get('fs_no_npwp'));
			Ext.getCmp('0cboSpt').setValue(record.get('fs_kd_spt'));
			
			Ext.getCmp('1txtNamaPt').setValue(record.get('fs_nm_pt'));
			Ext.getCmp('1txtAlamatPt').setValue(record.get('fs_alamat_pt'));
			Ext.getCmp('1txtTlpPt').setValue(record.get('fs_tlp_pt'));
			Ext.getCmp('1cboKategoriUsaha').setValue(record.get('fs_kd_katusaha'));
			Ext.getCmp('1txtKategoriUsaha').setValue(record.get('fs_nm_katusaha'));
			Ext.getCmp('1cboKategoriKtr').setValue(record.get('fs_kd_katktr'));
			Ext.getCmp('1cboKondisiKtr').setValue(record.get('fs_kd_kondisiktr'));
			Ext.getCmp('1cboKerja').setValue(record.get('fs_kd_kerja'));
			Ext.getCmp('1txtKerja').setValue(record.get('fs_nm_kerja'));
			Ext.getCmp('1txtKetKerja').setValue(record.get('fs_ket_usaha'));
			Ext.getCmp('1txtBlnKerja').setValue(record.get('fd_kerja'));
			Ext.getCmp('1txtGaji').setValue(record.get('fn_pendapatan'));
			Ext.getCmp('1txtTempatLahir').setValue(record.get('fs_tempat_lahir'));
			Ext.getCmp('1txtTglLahir').setValue(record.get('fd_tgllahir'));
			Ext.getCmp('1cboJK').setValue(record.get('fs_kd_jk'));
			Ext.getCmp('1cboAgama').setValue(record.get('fs_kd_agama'));
			Ext.getCmp('1txtNamaIbu').setValue(record.get('fs_nm_ibu'));
			Ext.getCmp('1cboPendidikan').setValue(record.get('fs_kd_pendidikan'));
			Ext.getCmp('1txtPendidikan').setValue(record.get('fs_nm_pendidikan'));
			Ext.getCmp('1cboStatusKawin').setValue(record.get('fs_kd_kawin'));
			Ext.getCmp('1txtTanggungan').setValue(record.get('fn_tanggungan'));
			Ext.getCmp('1cboStatusRmh').setValue(record.get('fs_kd_rumah'));
			Ext.getCmp('1txtStatusRmh').setValue(record.get('fs_nm_rumah'));
			Ext.getCmp('1txtBlnTinggal').setValue(record.get('fd_tinggal'));
			Ext.getCmp('1txtBiaya').setValue(record.get('fn_biaya_bulan'));
			Ext.getCmp('1txtAlamatSurat').setValue(record.get('fs_alamat_surat'));
			Ext.getCmp('1txtKota').setValue(record.get('fs_kotakons'));
			Ext.getCmp('1txtKodePos').setValue(record.get('fs_kd_poskons'));
			Ext.getCmp('1cboGarasi').setValue(record.get('fs_kd_garasi'));
			Ext.getCmp('1txtKend').setValue(record.get('fn_jml_kendaraan'));
			Ext.getCmp('1cboKaliKredit').setValue(record.get('fs_kd_kalikredit'));
			Ext.getCmp('1txtKreditKe').setValue(record.get('fn_kredit_ke'));
			Ext.getCmp('1cboKondisiLingk').setValue(record.get('fs_kd_kondisilingk'));
			
			Ext.getCmp('2cboJenisKend').setValue(record.get('fs_kd_kend'));
			Ext.getCmp('2txtJenisKend').setValue(record.get('fs_nm_kend'));
			Ext.getCmp('2txtRangka').setValue(record.get('fs_rangka'));
			Ext.getCmp('2txtMesin').setValue(record.get('fs_mesin'));
			Ext.getCmp('2txtSilinder').setValue(record.get('fn_silinder'));
			Ext.getCmp('2txtThn').setValue(record.get('fn_tahun'));
			Ext.getCmp('2txtWarna').setValue(record.get('fs_warna'));
			Ext.getCmp('2cboKomersial').setValue(record.get('fs_kd_komersial'));
			Ext.getCmp('2cboNamaBpkb').setValue(record.get('fs_kd_namabpkb'));
			Ext.getCmp('2txtBpkb').setValue(record.get('fs_no_bpkb'));
			Ext.getCmp('2txtNopol').setValue(record.get('fs_nopol'));
			Ext.getCmp('2txtNamaBpkb').setValue(record.get('fs_nm_bpkb'));
			Ext.getCmp('2txtAlamatBpkb').setValue(record.get('fs_alm_bpkb'));
			Ext.getCmp('2cboKota').setValue(record.get('fs_kd_kota_bpkb'));
			Ext.getCmp('2txtKota').setValue(record.get('fs_nm_kota_bpkb'));
			Ext.getCmp('2cboLembagaKeu').setValue(record.get('fs_kd_lemkeu'));
			Ext.getCmp('2txtLembagaKeu').setValue(record.get('fs_nm_lemkeu'));
			Ext.getCmp('2cboJenisAss').setValue(record.get('fs_kd_ass'));
			Ext.getCmp('2cboSupp').setValue(record.get('fs_kd_supp'));
			Ext.getCmp('2txtSupp').setValue(record.get('fs_nm_supp'));
			Ext.getCmp('2cboCab').setValue(record.get('fs_kd_cabang'));
			Ext.getCmp('2txtCab').setValue(record.get('fs_nm_cabang'));
			Ext.getCmp('2txtSales').setValue(record.get('fs_sales'));
		},
		itemdblclick: function(grid, record)
		{
			helpGrid.hide();
		}
    }
});

var helpGrid = Ext.create('Ext.menu.Menu', {
	plain: true
});
helpGrid.add(popGrid);

Ext.define('Ext.ux.SearchAPK', {
	alias: 'widget.searchAPK',
	extend: 'Ext.form.field.Trigger',
	initComponent: function() {
		this.callParent(arguments);
		this.on('specialkey', function(f, e) {
			if (e.getKey() == e.ENTER) {
				this.onTriggerClick();
			}
		}, this);
	},
	onTriggerClick: function() {
		grupAPK.load();
		popGrid.show();
		helpGrid.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
	}
});

var o0cboRefno = {
	anchor: '70%',
	emptyText: "OTOMATIS",
	fieldLabel: 'No. APK',
	fieldStyle: 'text-transform: uppercase',
	id: '0cboRefno',
	name: '0cboRefno',
	xtype: 'searchAPK',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o0txtTgl = {
	anchor: '38%',
	editable: true,
	fieldLabel: 'Tgl',
	format: 'd-m-Y',
	id: '0txtTgl',
	maskRe: /[0-9-]/,
	minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
	name: '0txtTgl',
	value: new Date(),
	xtype: 'datefield'
};

var grupJenis = Ext.create('Ext.data.Store', {
	autoLoad: true,
	fields: ['fs_kd_jenis','fs_nm_jenis'],
	pageSize: 25,
	proxy: {
		actionMethods: 'POST',
		reader: {
			root: 'hasil',
			totalProperty: 'total',
			type: 'json'
		},
		type: 'ajax',
		url: 'apkorang/kodejenis'
	}
});

grupJenis.on('beforeload', function(store, operation, eOpts) {
	operation.params = {
		// 'fs_kd_jenis': Ext.getCmp('0cboJenis').getValue(),
		// 'fs_nm_jenis': Ext.getCmp('0cboJenis').getValue()
	};
}, this);

var popGrid2 = Ext.create('Ext.ux.LiveSearchGridPanel', {
	autoDestroy: true,
	height: 250,
	width: 550,
	store: grupJenis,
	bbar: Ext.create('Ext.PagingToolbar', {
		displayInfo: true,
		displayMsg: 'Displaying record {0} - {1} of {2}',
		emptyMsg: "No record to display",
		pageSize: 25,
		plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
		store: grupJenis,
		items:[
			'-', {
			text: 'Exit',
			handler: function() {
				helpGrid2.hide();
			}
		}]
	}),
	columns: [
		{xtype: 'rownumberer', width: 45},
		{text: "Kode Jenis", dataIndex: 'fs_kd_jenis', width: 100},
		{text: "Nama Jenis", dataIndex: 'fs_nm_jenis', width: 380}
	],
    listeners: {
        itemclick: function(grid, record)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			record = grid.store.getAt(xpos.row);
			Ext.getCmp('0cboJenis').setValue(record.get('fs_kd_jenis'));
			Ext.getCmp('0txtJenis').setValue(record.get('fs_nm_jenis'));
		},
		itemdblclick: function(grid, record)
		{
			helpGrid2.hide();
		}
    }
});

var helpGrid2 = Ext.create('Ext.menu.Menu', {
	plain: true
});
helpGrid2.add(popGrid2);

Ext.define('Ext.ux.SearchJenis', {
	alias: 'widget.searchJenis',
	extend: 'Ext.form.field.Trigger',
	initComponent: function() {
		this.callParent(arguments);
		this.on('specialkey', function(f, e) {
			if (e.getKey() == e.ENTER) {
				this.onTriggerClick();
			}
		}, this);
	},
	onTriggerClick: function() {
		grupJenis.load();
		popGrid2.show();
		helpGrid2.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
	}
});

var o0cboJenis = {
	anchor: '98%',
	emptyText: "Pilih",
	fieldLabel: 'Jenis',
	fieldStyle: 'text-transform: uppercase',
	id: '0cboJenis',
	name: '0cboJenis',
	xtype: 'searchJenis',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
			Ext.getCmp('0txtJenis').setValue('');
		}
	}
};

var o0txtJenis = {
	anchor: '100%',
	emptyText: "Jenis",
	fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase;',
	id: '0txtJenis',
	name: '0txtJenis',
	readOnly: true,
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupJenisPiutang = Ext.create('Ext.data.Store', {
	autoLoad: true,
	fields: ['fs_kd_jenisp','fs_nm_jenisp'],
	pageSize: 25,
	proxy: {
		actionMethods: 'POST',
		reader: {
			root: 'hasil',
			totalProperty: 'total',
			type: 'json'
		},
		type: 'ajax',
		url: 'apkorang/kodejenisp'
	}
});

grupJenisPiutang.on('beforeload', function(store, operation, eOpts) {
	operation.params = {
		// 'fs_kd_jenisp': Ext.getCmp('0cboJenisPiutang').getValue(),
		// 'fs_nm_jenisp': Ext.getCmp('0cboJenisPiutang').getValue()
	};
}, this);

var popGrid3 = Ext.create('Ext.ux.LiveSearchGridPanel', {
	autoDestroy: true,
	height: 250,
	width: 550,
	store: grupJenisPiutang,
	bbar: Ext.create('Ext.PagingToolbar', {
		displayInfo: true,
		displayMsg: 'Displaying record {0} - {1} of {2}',
		emptyMsg: "No record to display",
		pageSize: 25,
		plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
		store: grupJenisPiutang,
		items:[
			'-', {
			text: 'Exit',
			handler: function() {
				helpGrid3.hide();
			}
		}]
	}),
	columns: [
		{xtype: 'rownumberer', width: 45},
		{text: "Kode Jenis Piutang", dataIndex: 'fs_kd_jenisp', width: 100},
		{text: "Nama Jenis Piutang", dataIndex: 'fs_nm_jenisp', width: 380}
	],
    listeners: {
        itemclick: function(grid, record)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			record = grid.store.getAt(xpos.row);
			Ext.getCmp('0cboJenisPiutang').setValue(record.get('fs_kd_jenisp'));
			Ext.getCmp('0txtJenisPiutang').setValue(record.get('fs_nm_jenisp'));
		},
		itemdblclick: function(grid, record)
		{
			helpGrid3.hide();
		}
    }
});

var helpGrid3 = Ext.create('Ext.menu.Menu', {
	plain: true
});
helpGrid3.add(popGrid3);

Ext.define('Ext.ux.SearchJenisP', {
	alias: 'widget.searchJenisP',
	extend: 'Ext.form.field.Trigger',
	initComponent: function() {
		this.callParent(arguments);
		this.on('specialkey', function(f, e) {
			if (e.getKey() == e.ENTER) {
				this.onTriggerClick();
			}
		}, this);
	},
	onTriggerClick: function() {
		grupJenisPiutang.load();
		popGrid3.show();
		helpGrid3.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
	}
});

var o0cboJenisPiutang = {
	anchor: '98%',
	emptyText: "Pilih",
	fieldLabel: 'Jenis Piutang',
	fieldStyle: 'text-transform: uppercase',
	id: '0cboJenisPiutang',
	name: '0cboJenisPiutang',
	xtype: 'searchJenisP',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
			Ext.getCmp('0txtJenisPiutang').setValue('');
		}
	}
};

var o0txtJenisPiutang = {
	anchor: '100%',
	emptyText: "Jenis Piutang",
	fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase;',
	id: '0txtJenisPiutang',
	name: '0txtJenisPiutang',
	readOnly: true,
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupPola = Ext.create('Ext.data.Store', {
	autoLoad: true,
	fields: ['fs_kd_pola','fs_nm_pola'],
	pageSize: 25,
	proxy: {
		actionMethods: 'POST',
		reader: {
			root: 'hasil',
			totalProperty: 'total',
			type: 'json'
		},
		type: 'ajax',
		url: 'apkorang/kodepola'
	}
});

grupPola.on('beforeload', function(store, operation, eOpts) {
	operation.params = {
		// 'fs_kd_pola': Ext.getCmp('0cboPolaTrs').getValue(),
		// 'fs_nm_pola': Ext.getCmp('0cboPolaTrs').getValue()
	};
}, this);

var popGrid4 = Ext.create('Ext.ux.LiveSearchGridPanel', {
	autoDestroy: true,
	height: 250,
	width: 550,
	store: grupPola,
	bbar: Ext.create('Ext.PagingToolbar', {
		displayInfo: true,
		displayMsg: 'Displaying record {0} - {1} of {2}',
		emptyMsg: "No record to display",
		pageSize: 25,
		plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
		store: grupPola,
		items:[
			'-', {
			text: 'Exit',
			handler: function() {
				helpGrid4.hide();
			}
		}]
	}),
	columns: [
		{xtype: 'rownumberer', width: 45},
		{text: "Kode Pola Trs", dataIndex: 'fs_kd_pola', width: 100},
		{text: "Nama Pola Trs", dataIndex: 'fs_nm_pola', width: 380}
	],
    listeners: {
        itemclick: function(grid, record)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			record = grid.store.getAt(xpos.row);
			Ext.getCmp('0cboPolaTrs').setValue(record.get('fs_kd_pola'));
			Ext.getCmp('0txtPolaTrs').setValue(record.get('fs_nm_pola'));
		},
		itemdblclick: function(grid, record)
		{
			helpGrid4.hide();
		}
    }
});

var helpGrid4 = Ext.create('Ext.menu.Menu', {
	plain: true
});
helpGrid4.add(popGrid4);

Ext.define('Ext.ux.SearchPola', {
	alias: 'widget.searchPola',
	extend: 'Ext.form.field.Trigger',
	initComponent: function() {
		this.callParent(arguments);
		this.on('specialkey', function(f, e) {
			if (e.getKey() == e.ENTER) {
				this.onTriggerClick();
			}
		}, this);
	},
	onTriggerClick: function() {
		grupPola.load();
		popGrid4.show();
		helpGrid4.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
	}
});

var o0cboPolaTrs = {
	anchor: '98%',
	emptyText: "Pilih",
	fieldLabel: 'Pola Transaksi',
	fieldStyle: 'text-transform: uppercase',
	id: '0cboPolaTrs',
	name: '0cboPolaTrs',
	xtype: 'searchPola',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
			Ext.getCmp('0txtPolaTrs').setValue('');
		}
	}
};

var o0txtPolaTrs = {
	anchor: '100%',
	emptyText: "Pola Transaksi",
	fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase;',
	id: '0txtPolaTrs',
	name: '0txtPolaTrs',
	readOnly: true,
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupJenisPaket = Ext.create('Ext.data.Store', {
	autoLoad: true,
	fields: ['fs_kd_jenisp','fs_nm_jenisp'],
	pageSize: 25,
	proxy: {
		actionMethods: 'POST',
		reader: {
			root: 'hasil',
			totalProperty: 'total',
			type: 'json'
		},
		type: 'ajax',
		url: 'apkorang/kodejenispaket'
	}
});

grupJenisPaket.on('beforeload', function(store, operation, eOpts) {
	operation.params = {
		'fs_kd_jenisp': Ext.getCmp('0cboJenisPaket').getValue(),
		'fs_nm_jenisp': Ext.getCmp('0cboJenisPaket').getValue()
	};
}, this);

var popGrid5 = Ext.create('Ext.ux.LiveSearchGridPanel', {
	autoDestroy: true,
	height: 250,
	width: 550,
	store: grupJenisPaket,
	bbar: Ext.create('Ext.PagingToolbar', {
		displayInfo: true,
		displayMsg: 'Displaying record {0} - {1} of {2}',
		emptyMsg: "No record to display",
		pageSize: 25,
		plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
		store: grupJenisPaket,
		items:[
			'-', {
			text: 'Exit',
			handler: function() {
				helpGrid5.hide();
			}
		}]
	}),
	columns: [
		{xtype: 'rownumberer', width: 45},
		{text: "Kode Pola Trs", dataIndex: 'fs_kd_jenisp', width: 100},
		{text: "Nama Pola Trs", dataIndex: 'fs_nm_jenisp', width: 380}
	],
    listeners: {
        itemclick: function(grid, record)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			record = grid.store.getAt(xpos.row);
			Ext.getCmp('0cboJenisPaket').setValue(record.get('fs_kd_jenisp'));
			Ext.getCmp('0txtJenisPaket').setValue(record.get('fs_nm_jenisp'));
		},
		itemdblclick: function(grid, record)
		{
			helpGrid5.hide();
		}
    }
});

var helpGrid5 = Ext.create('Ext.menu.Menu', {
	plain: true
});
helpGrid5.add(popGrid5);

Ext.define('Ext.ux.SearchJenisPaket', {
	alias: 'widget.searchJenisPaket',
	extend: 'Ext.form.field.Trigger',
	initComponent: function() {
		this.callParent(arguments);
		this.on('specialkey', function(f, e) {
			if (e.getKey() == e.ENTER) {
				this.onTriggerClick();
			}
		}, this);
	},
	onTriggerClick: function() {
		grupJenisPaket.load();
		popGrid5.show();
		helpGrid5.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
	}
});

var o0cboJenisPaket = {
	anchor: '98%',
	emptyText: "Pilih",
	fieldLabel: 'Jenis Paket',
	fieldStyle: 'text-transform: uppercase',
	id: '0cboJenisPaket',
	name: '0cboJenisPaket',
	xtype: 'searchJenisPaket',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
			Ext.getCmp('0txtJenisPaket').setValue('');
		}
	}
};

var o0txtJenisPaket = {
	anchor: '100%',
	emptyText: "Jenis Paket",
	fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase;',
	id: '0txtJenisPaket',
	name: '0txtJenisPaket',
	readOnly: true,
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o0txtNama = {
	afterLabelTextTpl: required,
	allowBlank: false,
	anchor: '100%',
	emptyText: "Nama Konsumen",
	fieldLabel: 'Nama',
	fieldStyle: 'text-transform: uppercase',
	id: '0txtNama',
	name: '0txtNama',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o0txtKtp = {
	anchor: '100%',
	emptyText: "No. KTP Konsumen",
	fieldLabel: 'No. KTP',
	fieldStyle: 'text-transform: uppercase',
	id: '0txtKtp',
	name: '0txtKtp',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o0txtTglKtp = {
	anchor: '100%',
	editable: true,
	fieldLabel: 's/d',
	format: 'd-m-Y',
	id: '0txtTglKtp',
	labelWidth: 30,
	maskRe: /[0-9-]/,
	minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
	name: '0txtTglKtp',
	value: new Date(),
	xtype: 'datefield'
};

var o0txtTlp = {
	anchor: '100%',
	emptyText: "Telp Konsumen",
	fieldLabel: 'Telp',
	fieldStyle: 'text-transform: uppercase',
	id: '0txtTlp',
	name: '0txtTlp',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o0txtHp = {
	anchor: '100%',
	emptyText: "HP Konsumen",
	fieldLabel: 'HP',
	fieldStyle: 'text-transform: uppercase',
	id: '0txtHp',
	name: '0txtHp',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o0txtAlamat = {
	anchor: '100%',
	emptyText: "Alamat Konsumen",
	fieldLabel: 'Alamat',
	fieldStyle: 'text-transform: uppercase',
	id: '0txtAlamat',
	name: '0txtAlamat',
	rows: 1,
	xtype: 'textarea',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o0txtKel = {
	anchor: '100%',
	emptyText: "Kelurahan Alamat Konsumen",
	fieldLabel: 'Kelurahan',
	fieldStyle: 'text-transform: uppercase',
	id: '0txtKel',
	name: '0txtKel',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o0txtKec = {
	anchor: '100%',
	emptyText: "Kecamatan Alamat Konsumen",
	fieldLabel: 'Kecamatan',
	fieldStyle: 'text-transform: uppercase',
	id: '0txtKec',
	name: '0txtKec',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o0txtKota = {
	anchor: '100%',
	emptyText: "Kota Alamat Konsumen",
	fieldLabel: 'Kota',
	fieldStyle: 'text-transform: uppercase',
	id: '0txtKota',
	name: '0txtKota',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupKab = Ext.create('Ext.data.Store', {
	autoLoad: true,
	fields: ['fs_kd_kab','fs_nm_kab'],
	pageSize: 25,
	proxy: {
		actionMethods: 'POST',
		reader: {
			root: 'hasil',
			totalProperty: 'total',
			type: 'json'
		},
		type: 'ajax',
		url: 'apkorang/kodekab'
	}
});

grupKab.on('beforeload', function(store, operation, eOpts) {
	operation.params = {
		'fs_kd_kab': Ext.getCmp('0cboKab').getValue(),
		'fs_nm_kab': Ext.getCmp('0cboKab').getValue()
	};
}, this);

var popGrid6 = Ext.create('Ext.ux.LiveSearchGridPanel', {
	autoDestroy: true,
	height: 250,
	width: 550,
	store: grupKab,
	bbar: Ext.create('Ext.PagingToolbar', {
		displayInfo: true,
		displayMsg: 'Displaying record {0} - {1} of {2}',
		emptyMsg: "No record to display",
		pageSize: 25,
		plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
		store: grupKab,
		items:[
			'-', {
			text: 'Exit',
			handler: function() {
				helpGrid6.hide();
			}
		}]
	}),
	columns: [
		{xtype: 'rownumberer', width: 45},
		{text: "Kode Kabupaten", dataIndex: 'fs_kd_kab', width: 100},
		{text: "Nama Kabupaten", dataIndex: 'fs_nm_kab', width: 380}
	],
    listeners: {
        itemclick: function(grid, record)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			record = grid.store.getAt(xpos.row);
			Ext.getCmp('0cboKab').setValue(record.get('fs_kd_kab'));
			Ext.getCmp('0txtKab').setValue(record.get('fs_nm_kab'));
		},
		itemdblclick: function(grid, record)
		{
			helpGrid6.hide();
		}
    }
});

var helpGrid6 = Ext.create('Ext.menu.Menu', {
	plain: true
});
helpGrid6.add(popGrid6);

Ext.define('Ext.ux.SearchKab', {
	alias: 'widget.searchKab',
	extend: 'Ext.form.field.Trigger',
	initComponent: function() {
		this.callParent(arguments);
		this.on('specialkey', function(f, e) {
			if (e.getKey() == e.ENTER) {
				this.onTriggerClick();
			}
		}, this);
	},
	onTriggerClick: function() {
		grupKab.load();
		popGrid6.show();
		helpGrid6.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
	}
});

var o0cboKab = {
	anchor: '98%',
	emptyText: "Pilih",
	fieldLabel: 'Kabupaten',
	fieldStyle: 'text-transform: uppercase',
	id: '0cboKab',
	name: '0cboKab',
	xtype: 'searchKab',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
			Ext.getCmp('0txtKab').setValue('');
		}
	}
};

var o0txtKab = {
	anchor: '100%',
	emptyText: "Kabupaten Alamat Konsumen",
	fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase;',
	id: '0txtKab',
	name: '0txtKab',
	readOnly: true,
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o0txtKodePos = {
	anchor: '100%',
	emptyText: "Kode Pos Alamat Konsumen",
	fieldLabel: 'Kode Pos',
	fieldStyle: 'text-transform: uppercase',
	id: '0txtKodePos',
	name: '0txtKodePos',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o0txtNpwp = {
	anchor: '98%',
	emptyText: "NPWP Konsumen",
	fieldLabel: 'NPWP',
	fieldStyle: 'text-transform: uppercase',
	id: '0txtNpwp',
	name: '0txtNpwp',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupSpt = Ext.create('Ext.data.ArrayStore', {
	autoLoad: true,
	data: [['1', 'YA'], ['0', 'TIDAK']],
	fields: ['fs_kd_spt', 'fs_nm_spt']
});

var o0cboSpt = {
	anchor: '100%',
	editable: false,
	emptyText: "SPT",
	displayField: 'fs_nm_spt',
	fieldLabel: 'SPT Tahunan',
	fieldStyle: 'text-transform: uppercase',
	id: '0cboSpt',
	name: '0cboSpt',
	store: grupSpt,
	value: '0',
	valueField: 'fs_kd_spt',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			// field.setValue(newValue.toUpperCase());
		}
	}
};
/*	Eof APK	*/

/*	Konsumen	*/
var o1txtNamaPt = {
	anchor: '100%',
	emptyText: "Nama Perusahaan",
	fieldLabel: 'Nama',
	fieldStyle: 'text-transform: uppercase',
	id: '1txtNamaPt',
	name: '1txtNamaPt',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o1txtTlpPt = {
	anchor: '100%',
	emptyText: "Telp Perusahaan",
	fieldLabel: 'Telp',
	fieldStyle: 'text-transform: uppercase',
	id: '1txtTlpPt',
	name: '1txtTlpPt',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o1txtAlamatPt = {
	anchor: '100%',
	emptyText: "Alamat Perusahaan",
	fieldLabel: 'Alamat',
	fieldStyle: 'text-transform: uppercase',
	id: '1txtAlamatPt',
	name: '1txtAlamatPt',
	rows: 1,
	xtype: 'textarea',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupKatUsaha = Ext.create('Ext.data.Store', {
	autoLoad: true,
	fields: ['fs_kd_katusaha','fs_nm_katusaha'],
	pageSize: 25,
	proxy: {
		actionMethods: 'POST',
		reader: {
			root: 'hasil',
			totalProperty: 'total',
			type: 'json'
		},
		type: 'ajax',
		url: 'apkorang/kodekatusaha'
	}
});

grupKatUsaha.on('beforeload', function(store, operation, eOpts) {
	operation.params = {
		// 'fs_kd_katusaha': Ext.getCmp('1cboKategoriUsaha').getValue(),
		// 'fs_nm_katusaha': Ext.getCmp('1cboKategoriUsaha').getValue()
	};
}, this);

var popGrid7 = Ext.create('Ext.ux.LiveSearchGridPanel', {
	autoDestroy: true,
	height: 250,
	width: 550,
	store: grupKatUsaha,
	bbar: Ext.create('Ext.PagingToolbar', {
		displayInfo: true,
		displayMsg: 'Displaying record {0} - {1} of {2}',
		emptyMsg: "No record to display",
		pageSize: 25,
		plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
		store: grupKatUsaha,
		items:[
			'-', {
			text: 'Exit',
			handler: function() {
				helpGrid7.hide();
			}
		}]
	}),
	columns: [
		{xtype: 'rownumberer', width: 45},
		{text: "Kode Kategori Usaha", dataIndex: 'fs_kd_katusaha', width: 100},
		{text: "Nama Kategori Usaha", dataIndex: 'fs_nm_katusaha', width: 380}
	],
    listeners: {
        itemclick: function(grid, record)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			record = grid.store.getAt(xpos.row);
			Ext.getCmp('1cboKategoriUsaha').setValue(record.get('fs_kd_katusaha'));
			Ext.getCmp('1txtKategoriUsaha').setValue(record.get('fs_nm_katusaha'));
		},
		itemdblclick: function(grid, record)
		{
			helpGrid7.hide();
		}
    }
});

var helpGrid7 = Ext.create('Ext.menu.Menu', {
	plain: true
});
helpGrid7.add(popGrid7);

Ext.define('Ext.ux.SearchKatUsaha', {
	alias: 'widget.searchKatUsaha',
	extend: 'Ext.form.field.Trigger',
	initComponent: function() {
		this.callParent(arguments);
		this.on('specialkey', function(f, e) {
			if (e.getKey() == e.ENTER) {
				this.onTriggerClick();
			}
		}, this);
	},
	onTriggerClick: function() {
		grupKatUsaha.load();
		popGrid7.show();
		helpGrid7.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
	}
});

var o1cboKategoriUsaha = {
	anchor: '98%',
	emptyText: "Pilih",
	fieldLabel: 'Kategori',
	fieldStyle: 'text-transform: uppercase',
	id: '1cboKategoriUsaha',
	name: '1cboKategoriUsaha',
	xtype: 'searchKatUsaha',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
			Ext.getCmp('1txtKategoriUsaha').setValue('');
		}
	}
};

var o1txtKategoriUsaha = {
	anchor: '100%',
	emptyText: "Kategori Usaha",
	fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase;',
	id: '1txtKategoriUsaha',
	name: '1txtKategoriUsaha',
	readOnly: true,
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupKerja = Ext.create('Ext.data.Store', {
	autoLoad: true,
	fields: ['fs_kd_kerja','fs_nm_kerja'],
	pageSize: 25,
	proxy: {
		actionMethods: 'POST',
		reader: {
			root: 'hasil',
			totalProperty: 'total',
			type: 'json'
		},
		type: 'ajax',
		url: 'apkorang/kodekerja'
	}
});

grupKerja.on('beforeload', function(store, operation, eOpts) {
	operation.params = {
		'fs_kd_kerja': Ext.getCmp('1cboKerja').getValue(),
		'fs_nm_kerja': Ext.getCmp('1cboKerja').getValue()
	};
}, this);

var popGrid8 = Ext.create('Ext.ux.LiveSearchGridPanel', {
	autoDestroy: true,
	height: 250,
	width: 550,
	store: grupKerja,
	bbar: Ext.create('Ext.PagingToolbar', {
		displayInfo: true,
		displayMsg: 'Displaying record {0} - {1} of {2}',
		emptyMsg: "No record to display",
		pageSize: 25,
		plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
		store: grupKerja,
		items:[
			'-', {
			text: 'Exit',
			handler: function() {
				helpGrid8.hide();
			}
		}]
	}),
	columns: [
		{xtype: 'rownumberer', width: 45},
		{text: "Kode Usaha/Pekerjaan", dataIndex: 'fs_kd_kerja', width: 100},
		{text: "Nama Usaha/Pekerjaan", dataIndex: 'fs_nm_kerja', width: 380}
	],
    listeners: {
        itemclick: function(grid, record)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			record = grid.store.getAt(xpos.row);
			Ext.getCmp('1cboKerja').setValue(record.get('fs_kd_kerja'));
			Ext.getCmp('1txtKerja').setValue(record.get('fs_nm_kerja'));
		},
		itemdblclick: function(grid, record)
		{
			helpGrid8.hide();
		}
    }
});

var helpGrid8 = Ext.create('Ext.menu.Menu', {
	plain: true
});
helpGrid8.add(popGrid8);

Ext.define('Ext.ux.SearchKerja', {
	alias: 'widget.searchKerja',
	extend: 'Ext.form.field.Trigger',
	initComponent: function() {
		this.callParent(arguments);
		this.on('specialkey', function(f, e) {
			if (e.getKey() == e.ENTER) {
				this.onTriggerClick();
			}
		}, this);
	},
	onTriggerClick: function() {
		grupKerja.load();
		popGrid8.show();
		helpGrid8.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
	}
});

var o1cboKerja = {
	anchor: '98%',
	emptyText: "Pilih",
	fieldLabel: 'Pekerjaan',
	fieldStyle: 'text-transform: uppercase',
	id: '1cboKerja',
	labelWidth: 100,
	name: '1cboKerja',
	xtype: 'searchKerja',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
			Ext.getCmp('1txtKerja').setValue('');
		}
	}
};

var o1txtKerja = {
	anchor: '100%',
	emptyText: "Usaha / Pekerjaan",
	fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase;',
	id: '1txtKerja',
	name: '1txtKerja',
	readOnly: true,
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o1txtKetKerja = {
	anchor: '100%',
	emptyText: "Keterangan Usaha / Pekerjaan",
	fieldLabel: 'Ket. Pekerjaan',
	fieldStyle: 'text-transform: uppercase',
	id: '1txtKetKerja',
	labelWidth: 100,
	name: '1txtKetKerja',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o1txtBlnKerja = {
	anchor: '100%',
	editable: true,
	fieldLabel: 'Kerja Sejak',
	format: 'm-Y',
	id: '1txtBlnKerja',
	labelWidth: 100,
	maskRe: /[0-9-]/,
	minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -100),
	name: '1txtBlnKerja',
	value: new Date(),
	xtype: 'datefield'
};

var o1txtGaji = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Pendapatan',
	hideTrigger: true,
	id: '1txtGaji',
	keyNavEnabled: false,
	labelWidth: 80,
	mouseWheelEnabled: false,
	name: '1txtGaji',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('1txtGaji').getValue())) {
				Ext.getCmp('1txtGaji').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var grupKondisiKtr = Ext.create('Ext.data.ArrayStore', {
	autoLoad: true,
	data: [['0', 'SEDERHANA'], ['1', 'MENENGAH'], ['2', 'LUX']],
	fields: ['fs_kd_kondisiktr', 'fs_nm_kondisiktr']
});

var o1cboKondisiKtr = {
	anchor: '100%',
	displayField: 'fs_nm_kondisiktr',
	editable: false,
	emptyText: "Pilih Kondisi Kantor",
	fieldLabel: 'Kondisi Kantor',
	fieldStyle: 'text-transform: uppercase',
	id: '1cboKondisiKtr',
	labelWidth: 100,
	name: '1cboKondisiKtr',
	store: grupKondisiKtr,
	value: '1',
	valueField: 'fs_kd_kondisiktr',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupKatKtr = Ext.create('Ext.data.ArrayStore', {
	autoLoad: true,
	data: [['0', 'KECIL'], ['1', 'MENENGAH'], ['2', 'BESAR']],
	fields: ['fs_kd_katktr', 'fs_nm_katktr']
});

var o1cboKategoriKtr = {
	anchor: '100%',
	displayField: 'fs_nm_katktr',
	editable: false,
	emptyText: "Pilih Kategori Perusahaan",
	fieldLabel: 'Kategori Perusahaan',
	fieldStyle: 'text-transform: uppercase',
	id: '1cboKategoriKtr',
	labelWidth: 125,
	name: '1cboKategoriKtr',
	store: grupKatKtr,
	value: '1',
	valueField: 'fs_kd_katktr',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupJK = Ext.create('Ext.data.ArrayStore', {
	autoLoad: true,
	data: [['0', 'LAKI - LAKI'], ['1', 'PEREMPUAN']],
	fields: ['fs_kd_jk', 'fs_nm_jk']
});

var o1cboJK = {
	anchor: '100%',
	displayField: 'fs_nm_jk',
	editable: false,
	emptyText: "Pilih Jenis Kelamin",
	fieldLabel: 'Jenis Kelamin',
	fieldStyle: 'text-transform: uppercase',
	id: '1cboJK',
	name: '1cboJK',
	store: grupJK,
	value: '0',
	valueField: 'fs_kd_jk',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupAgama = Ext.create('Ext.data.Store', {
	autoLoad: true,
	fields: ['fs_kd_agama','fs_nm_agama'],
	proxy: {
		actionMethods: 'POST',
		reader: {
			type: 'json'
		},
		type: 'ajax',
		url: 'apkorang/kodeagama'
	}
});

var o1cboAgama = {
	anchor: '100%',
	displayField: 'fs_nm_agama',
	editable: false,
	emptyText: "Pilih",
	fieldLabel: 'Agama',
	fieldStyle: 'text-transform: uppercase',
	id: '1cboAgama',
	labelWidth: 50,
	name: '1cboAgama',
	store: grupAgama,
	value: '01',
	valueField: 'fs_kd_agama',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupRumah = Ext.create('Ext.data.Store', {
	autoLoad: true,
	fields: ['fs_kd_rumah','fs_nm_rumah'],
	pageSize: 25,
	proxy: {
		actionMethods: 'POST',
		reader: {
			root: 'hasil',
			totalProperty: 'total',
			type: 'json'
		},
		type: 'ajax',
		url: 'apkorang/koderumah'
	}
});

grupRumah.on('beforeload', function(store, operation, eOpts) {
	operation.params = {
		// 'fs_kd_rumah': Ext.getCmp('1cboStatusRmh').getValue(),
		// 'fs_nm_rumah': Ext.getCmp('1cboStatusRmh').getValue()
	};
}, this);

var popGrid10 = Ext.create('Ext.ux.LiveSearchGridPanel', {
	autoDestroy: true,
	height: 250,
	width: 550,
	store: grupRumah,
	bbar: Ext.create('Ext.PagingToolbar', {
		displayInfo: true,
		displayMsg: 'Displaying record {0} - {1} of {2}',
		emptyMsg: "No record to display",
		pageSize: 25,
		plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
		store: grupRumah,
		items:[
			'-', {
			text: 'Exit',
			handler: function() {
				helpGrid10.hide();
			}
		}]
	}),
	columns: [
		{xtype: 'rownumberer', width: 45},
		{text: "Kode Status Rumah", dataIndex: 'fs_kd_rumah', width: 100},
		{text: "Nama Status Rumah", dataIndex: 'fs_nm_rumah', width: 380}
	],
    listeners: {
        itemclick: function(grid, record)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			record = grid.store.getAt(xpos.row);
			Ext.getCmp('1cboStatusRmh').setValue(record.get('fs_kd_rumah'));
			Ext.getCmp('1txtStatusRmh').setValue(record.get('fs_nm_rumah'));
		},
		itemdblclick: function(grid, record)
		{
			helpGrid10.hide();
		}
    }
});

var helpGrid10 = Ext.create('Ext.menu.Menu', {
	plain: true
});
helpGrid10.add(popGrid10);

Ext.define('Ext.ux.SearchRmh', {
	alias: 'widget.searchRmh',
	extend: 'Ext.form.field.Trigger',
	initComponent: function() {
		this.callParent(arguments);
		this.on('specialkey', function(f, e) {
			if (e.getKey() == e.ENTER) {
				this.onTriggerClick();
			}
		}, this);
	},
	onTriggerClick: function() {
		grupRumah.load();
		popGrid10.show();
		helpGrid10.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
	}
});

var o1cboStatusRmh = {
	anchor: '98%',
	emptyText: "Pilih",
	fieldLabel: 'Status Rumah',
	fieldStyle: 'text-transform: uppercase',
	id: '1cboStatusRmh',
	name: '1cboStatusRmh',
	xtype: 'searchRmh',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
			Ext.getCmp('1txtStatusRmh').setValue('');
		}
	}
};

var o1txtStatusRmh = {
	anchor: '100%',
	emptyText: "Status Rumah",
	fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase;',
	id: '1txtStatusRmh',
	name: '1txtStatusRmh',
	readOnly: true,
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o1txtBlnTinggal = {
	anchor: '100%',
	editable: true,
	fieldLabel: 'Tinggal Sejak',
	format: 'm-Y',
	id: '1txtBlnTinggal',
	labelWidth: 100,
	maskRe: /[0-9-]/,
	minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -100),
	name: '1txtBlnTinggal',
	value: new Date(),
	xtype: 'datefield'
};

var o1txtAlamatSurat = {
	anchor: '100%',
	emptyText: "Alamat Surat",
	fieldLabel: 'Alamat Surat',
	fieldStyle: 'text-transform: uppercase',
	id: '1txtAlamatSurat',
	labelWidth: 100,
	name: '1txtAlamatSurat',
	rows: 1,
	xtype: 'textarea',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o1txtKota = {
	anchor: '100%',
	emptyText: "Kota",
	fieldLabel: 'Kota',
	fieldStyle: 'text-transform: uppercase',
	id: '1txtKota',
	labelWidth: 100,
	name: '1txtKota',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o1txtKodePos = {
	anchor: '100%',
	emptyText: "Kode Pos",
	fieldLabel: 'Kode Pos',
	fieldStyle: 'text-transform: uppercase',
	id: '1txtKodePos',
	labelWidth: 60,
	name: '1txtKodePos',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o1txtTempatLahir = {
	anchor: '100%',
	emptyText: "Tempat Lahir",
	fieldLabel: 'Tempat / Tgl Lahir',
	fieldStyle: 'text-transform: uppercase',
	id: '1txtTempatLahir',
	name: '1txtTempatLahir',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o1txtTglLahir = {
	anchor: '100%',
	editable: true,
	fieldLabel: '/',
	format: 'd-m-Y',
	id: '1txtTglLahir',
	labelWidth: 10,
	maskRe: /[0-9-]/,
	minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -100),
	name: '1txtTglLahir',
	value: new Date(),
	xtype: 'datefield'
};

var grupPendidikan = Ext.create('Ext.data.Store', {
	autoLoad: true,
	fields: ['fs_kd_pendidikan','fs_nm_pendidikan'],
	pageSize: 25,
	proxy: {
		actionMethods: 'POST',
		reader: {
			root: 'hasil',
			totalProperty: 'total',
			type: 'json'
		},
		type: 'ajax',
		url: 'apkorang/kodependidikan'
	}
});

grupPendidikan.on('beforeload', function(store, operation, eOpts) {
	operation.params = {
		// 'fs_kd_pendidikan': Ext.getCmp('1cboPendidikan').getValue(),
		// 'fs_nm_pendidikan': Ext.getCmp('1cboPendidikan').getValue()
	};
}, this);

var popGrid9 = Ext.create('Ext.ux.LiveSearchGridPanel', {
	autoDestroy: true,
	height: 250,
	width: 550,
	store: grupPendidikan,
	bbar: Ext.create('Ext.PagingToolbar', {
		displayInfo: true,
		displayMsg: 'Displaying record {0} - {1} of {2}',
		emptyMsg: "No record to display",
		pageSize: 25,
		plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
		store: grupPendidikan,
		items:[
			'-', {
			text: 'Exit',
			handler: function() {
				helpGrid9.hide();
			}
		}]
	}),
	columns: [
		{xtype: 'rownumberer', width: 45},
		{text: "Kode Pendidikan", dataIndex: 'fs_kd_pendidikan', width: 100},
		{text: "Nama Pendidikan", dataIndex: 'fs_nm_pendidikan', width: 380}
	],
    listeners: {
        itemclick: function(grid, record)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			record = grid.store.getAt(xpos.row);
			Ext.getCmp('1cboPendidikan').setValue(record.get('fs_kd_pendidikan'));
			Ext.getCmp('1txtPendidikan').setValue(record.get('fs_nm_pendidikan'));
		},
		itemdblclick: function(grid, record)
		{
			helpGrid9.hide();
		}
    }
});

var helpGrid9 = Ext.create('Ext.menu.Menu', {
	plain: true
});
helpGrid9.add(popGrid9);

Ext.define('Ext.ux.SearchPddkan', {
	alias: 'widget.searchPddkan',
	extend: 'Ext.form.field.Trigger',
	initComponent: function() {
		this.callParent(arguments);
		this.on('specialkey', function(f, e) {
			if (e.getKey() == e.ENTER) {
				this.onTriggerClick();
			}
		}, this);
	},
	onTriggerClick: function() {
		grupPendidikan.load();
		popGrid9.show();
		helpGrid9.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
	}
});

var o1cboPendidikan = {
	anchor: '98%',
	emptyText: "Pilih",
	fieldLabel: 'Pendidikan',
	fieldStyle: 'text-transform: uppercase',
	id: '1cboPendidikan',
	name: '1cboPendidikan',
	xtype: 'searchPddkan',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
			Ext.getCmp('1txtPendidikan').setValue('');
		}
	}
};

var o1txtPendidikan = {
	anchor: '100%',
	emptyText: "Pendidikan",
	fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase;',
	id: '1txtPendidikan',
	name: '1txtPendidikan',
	readOnly: true,
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o1txtBiaya = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Biaya / Bulan',
	hideTrigger: true,
	id: '1txtBiaya',
	keyNavEnabled: false,
	labelWidth: 80,
	mouseWheelEnabled: false,
	name: '1txtBiaya',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('1txtBiaya').getValue())) {
				Ext.getCmp('1txtBiaya').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o1txtNamaIbu = {
	anchor: '100%',
	emptyText: "Nama Gadis Ibu Kandung",
	fieldLabel: 'Nama Ibu Kandung',
	fieldStyle: 'text-transform: uppercase',
	id: '1txtNamaIbu',
	name: '1txtNamaIbu',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupStatusKawin = Ext.create('Ext.data.ArrayStore', {
	autoLoad: true,
	data: [['0', 'BELUM / TIDAK KAWIN'], ['1', 'KAWIN'], ['2', 'JANDA / DUDA'], ['3', 'CERAI']],
	fields: ['fs_kd_kawin', 'fs_nm_kawin']
});

var o1cboStatusKawin = {
	anchor: '98%',
	displayField: 'fs_nm_kawin',
	editable: false,
	emptyText: "Pilih Status Kawin",
	fieldLabel: 'Status Kawin',
	fieldStyle: 'text-transform: uppercase',
	id: '1cboStatusKawin',
	name: '1cboStatusKawin',
	store: grupStatusKawin,
	value: '0',
	valueField: 'fs_kd_kawin',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o1txtTanggungan = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Tanggungan',
	hideTrigger: true,
	id: '1txtTanggungan',
	keyNavEnabled: false,
	labelWidth: 80,
	mouseWheelEnabled: false,
	name: '1txtTanggungan',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('1txtTanggungan').getValue())) {
				Ext.getCmp('1txtTanggungan').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var grupKondisiLingk = Ext.create('Ext.data.ArrayStore', {
	autoLoad: true,
	data: [['0', 'SEDERHANA'], ['1', 'MENENGAH'], ['2', 'LUX']],
	fields: ['fs_kd_kondisilingk', 'fs_nm_kondisilingk']
});

var o1cboKondisiLingk = {
	anchor: '64%',
	displayField: 'fs_nm_kondisilingk',
	editable: false,
	emptyText: "Pilih Kondisi Lingkungan Setempat",
	fieldLabel: 'Kondisi Lingkungan Setempat',
	fieldStyle: 'text-transform: uppercase',
	id: '1cboKondisiLingk',
	labelWidth: 170,
	name: '1cboKondisiLingk',
	store: grupKondisiLingk,
	value: '1',
	valueField: 'fs_kd_kondisilingk',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o1txtKend = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Jml Kend. Yg Dimiliki',
	hideTrigger: true,
	id: '1txtKend',
	keyNavEnabled: false,
	labelWidth: 120,
	mouseWheelEnabled: false,
	name: '1txtKend',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('1txtKend').getValue())) {
				Ext.getCmp('1txtKend').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var grupGarasi = Ext.create('Ext.data.ArrayStore', {
	autoLoad: true,
	data: [['0', 'TIDAK'], ['1', 'YA']],
	fields: ['fs_kd_garasi', 'fs_nm_garasi']
});

var o1cboGarasi = {
	anchor: '100%',
	displayField: 'fs_nm_garasi',
	editable: false,
	emptyText: "Pilih Rumah Memiliki Garasi",
	fieldLabel: 'Rumah Memiliki Garasi',
	fieldStyle: 'text-transform: uppercase',
	id: '1cboGarasi',
	labelWidth: 130,
	name: '1cboGarasi',
	store: grupGarasi,
	value: '0',
	valueField: 'fs_kd_garasi',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupKaliKredit = Ext.create('Ext.data.ArrayStore', {
	autoLoad: true,
	data: [['0', 'TIDAK, TIDAK ADA BUKTI'], ['1', 'TIDAK, ADA BUKTI'], ['2', 'YA'], ['2', 'REPEAT']],
	fields: ['fs_kd_kalikredit', 'fs_nm_kalikredit']
});

var o1cboKaliKredit = {
	anchor: '100%',
	displayField: 'fs_nm_kalikredit',
	editable: false,
	emptyText: "Pilih Pertama Kali Kredit",
	fieldLabel: 'Pertama Kali Kredit',
	fieldStyle: 'text-transform: uppercase',
	id: '1cboKaliKredit',
	labelWidth: 130,
	name: '1cboKaliKredit',
	store: grupKaliKredit,
	value: '2',
	valueField: 'fs_kd_kalikredit',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o1txtKreditKe = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Ke',
	hideTrigger: true,
	id: '1txtKreditKe',
	keyNavEnabled: false,
	labelWidth: 30,
	mouseWheelEnabled: false,
	name: '1txtKreditKe',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('1txtKreditKe').getValue())) {
				Ext.getCmp('1txtKreditKe').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});
/*	Eof Konsumen	*/

/*	Kendaraan	*/
var grupKend = Ext.create('Ext.data.Store', {
	autoLoad: true,
	fields: ['fs_kd_kend','fs_nm_kend'],
	pageSize: 25,
	proxy: {
		actionMethods: 'POST',
		reader: {
			root: 'hasil',
			totalProperty: 'total',
			type: 'json'
		},
		type: 'ajax',
		url: 'apkorang/kodekend'
	}
});

grupKend.on('beforeload', function(store, operation, eOpts) {
	operation.params = {
		'fs_kd_kend': Ext.getCmp('2cboJenisKend').getValue(),
		'fs_nm_kend': Ext.getCmp('2cboJenisKend').getValue()
	};
}, this);

var popGrid11 = Ext.create('Ext.ux.LiveSearchGridPanel', {
	autoDestroy: true,
	height: 250,
	width: 550,
	store: grupKend,
	bbar: Ext.create('Ext.PagingToolbar', {
		displayInfo: true,
		displayMsg: 'Displaying record {0} - {1} of {2}',
		emptyMsg: "No record to display",
		pageSize: 25,
		plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
		store: grupKend,
		items:[
			'-', {
			text: 'Exit',
			handler: function() {
				helpGrid11.hide();
			}
		}]
	}),
	columns: [
		{xtype: 'rownumberer', width: 45},
		{text: "Kode Kendaraan", dataIndex: 'fs_kd_kend', width: 100},
		{text: "Nama Kendaraan", dataIndex: 'fs_nm_kend', width: 380}
	],
    listeners: {
        itemclick: function(grid, record)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			record = grid.store.getAt(xpos.row);
			Ext.getCmp('2cboJenisKend').setValue(record.get('fs_kd_kend'));
			Ext.getCmp('2txtJenisKend').setValue(record.get('fs_nm_kend'));
		},
		itemdblclick: function(grid, record)
		{
			helpGrid11.hide();
		}
    }
});

var helpGrid11 = Ext.create('Ext.menu.Menu', {
	plain: true
});
helpGrid11.add(popGrid11);

Ext.define('Ext.ux.SearchKend', {
	alias: 'widget.searchKend',
	extend: 'Ext.form.field.Trigger',
	initComponent: function() {
		this.callParent(arguments);
		this.on('specialkey', function(f, e) {
			if (e.getKey() == e.ENTER) {
				this.onTriggerClick();
			}
		}, this);
	},
	onTriggerClick: function() {
		grupKend.load();
		popGrid11.show();
		helpGrid11.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
	}
});

var o2cboJenisKend = {
	anchor: '98%',
	emptyText: "Pilih",
	fieldLabel: 'Jenis Kend',
	fieldStyle: 'text-transform: uppercase',
	id: '2cboJenisKend',
	name: '2cboJenisKend',
	xtype: 'searchKend',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
			Ext.getCmp('2txtJenisKend').setValue('');
		}
	}
};

var o2txtJenisKend = {
	anchor: '100%',
	emptyText: "Jenis Kendaraan",
	fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase;',
	id: '2txtJenisKend',
	name: '2txtJenisKend',
	readOnly: true,
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o2txtRangka = {
	anchor: '100%',
	emptyText: "No. Rangka",
	fieldLabel: 'Rangka',
	fieldStyle: 'text-transform: uppercase;',
	id: '2txtRangka',
	name: '2txtRangka',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o2txtMesin = {
	anchor: '100%',
	emptyText: "No. Mesin",
	fieldLabel: 'Mesin',
	fieldStyle: 'text-transform: uppercase;',
	id: '2txtMesin',
	name: '2txtMesin',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o2txtSilinder = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Silinder',
	hideTrigger: true,
	id: '2txtSilinder',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '2txtSilinder',
	thousandSeparator: ',',
	useThousandSeparator: false,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('2txtSilinder').getValue())) {
				Ext.getCmp('2txtSilinder').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o2txtThn = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Tahun',
	hideTrigger: false,
	id: '2txtThn',
	keyNavEnabled: true,
	labelWidth: 50,
	maxLength: 4,
	mouseWheelEnabled: false,
	name: '2txtThn',
	thousandSeparator: ',',
	useThousandSeparator: false,
	value: Ext.Date.format(new Date(), 'Y'),
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('2txtThn').getValue())) {
				Ext.getCmp('2txtThn').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o2txtWarna = {
	anchor: '100%',
	emptyText: "Warna",
	fieldLabel: 'Warna',
	fieldStyle: 'text-transform: uppercase;',
	id: '2txtWarna',
	name: '2txtWarna',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupKomersial = Ext.create('Ext.data.ArrayStore', {
	autoLoad: true,
	data: [['0', 'TIDAK'], ['1', 'YA']],
	fields: ['fs_kd_komersial', 'fs_nm_komersial']
});

var o2cboKomersial = {
	anchor: '50%',
	displayField: 'fs_nm_komersial',
	editable: false,
	emptyText: "Pilih Komersial",
	fieldLabel: 'Komersial',
	fieldStyle: 'text-transform: uppercase',
	id: '2cboKomersial',
	name: '2cboKomersial',
	store: grupKomersial,
	value: '0',
	valueField: 'fs_kd_komersial',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupNamaBpkb = Ext.create('Ext.data.ArrayStore', {
	autoLoad: true,
	data: [['0', 'TIDAK'], ['1', 'YA']],
	fields: ['fs_kd_namabpkb', 'fs_nm_namabpkb']
});

var o2cboNamaBpkb = {
	anchor: '100%',
	displayField: 'fs_nm_namabpkb',
	emptyText: "Pilih Nama di BPKB Sama Dgn Kontrak",
	fieldLabel: 'Nama di BPKB Sama Dengan Kontrak',
	fieldStyle: 'text-transform: uppercase',
	id: '2cboNamaBpkb',
	labelWidth: 200,
	name: '2cboNamaBpkb',
	store: grupNamaBpkb,
	value: '0',
	valueField: 'fs_kd_namabpkb',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
			
			if (Ext.getCmp('2cboNamaBpkb').getValue() === '0') {
				Ext.getCmp('2txtNamaBpkb').setValue('');
				Ext.getCmp('2txtNamaBpkb').setReadOnly(false);
				Ext.getCmp('2txtNamaBpkb').setFieldStyle('background-color: white; background-image: url('.concat(gBaseIMG,'text-bg.gif);');
				Ext.getCmp('2txtAlamatBpkb').setValue('');
				Ext.getCmp('2txtAlamatBpkb').setReadOnly(false);
				Ext.getCmp('2txtAlamatBpkb').setFieldStyle('background-color: white; background-image: url('.concat(gBaseIMG,'text-bg.gif);');
			}
			else {
				Ext.getCmp('2txtNamaBpkb').setValue(Ext.getCmp('0txtNama').getValue());
				Ext.getCmp('2txtNamaBpkb').setReadOnly(true);
				Ext.getCmp('2txtNamaBpkb').setFieldStyle('background-color: #eee; background-image: none;');
				Ext.getCmp('2txtAlamatBpkb').setValue(Ext.getCmp('0txtAlamat').getValue());
				Ext.getCmp('2txtAlamatBpkb').setReadOnly(true);
				Ext.getCmp('2txtAlamatBpkb').setFieldStyle('background-color: #eee; background-image: none;');
			}
		}
	}
};

var o2txtBpkb = {
	anchor: '100%',
	emptyText: "Nomor BPKB",
	fieldLabel: 'Nomor BPKB',
	fieldStyle: 'text-transform: uppercase;',
	id: '2txtBpkb',
	name: '2txtBpkb',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o2txtNopol = {
	anchor: '100%',
	emptyText: "Nomor Polisi",
	fieldLabel: 'Nomor Polisi',
	fieldStyle: 'text-transform: uppercase;',
	id: '2txtNopol',
	name: '2txtNopol',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o2txtNamaBpkb = {
	anchor: '100%',
	emptyText: "Nama di BPKB",
	fieldLabel: 'Nama di BPKB',
	fieldStyle: 'text-transform: uppercase;',
	id: '2txtNamaBpkb',
	labelWidth: 100,
	name: '2txtNamaBpkb',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o2txtAlamatBpkb = {
	anchor: '100%',
	emptyText: "Alamat di BPKB",
	fieldLabel: 'Alamat di BPKB',
	fieldStyle: 'text-transform: uppercase;',
	id: '2txtAlamatBpkb',
	labelWidth: 100,
	name: '2txtAlamatBpkb',
	rows: 1,
	xtype: 'textarea',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupKota = Ext.create('Ext.data.Store', {
	autoLoad: true,
	fields: ['fs_kd_kota','fs_nm_kota'],
	pageSize: 25,
	proxy: {
		actionMethods: 'POST',
		reader: {
			root: 'hasil',
			totalProperty: 'total',
			type: 'json'
		},
		type: 'ajax',
		url: 'apkorang/kodekota'
	}
});

grupKota.on('beforeload', function(store, operation, eOpts) {
	operation.params = {
		'fs_kd_kota': Ext.getCmp('2cboKota').getValue(),
		'fs_nm_kota': Ext.getCmp('2cboKota').getValue()
	};
}, this);

var popGrid12 = Ext.create('Ext.ux.LiveSearchGridPanel', {
	autoDestroy: true,
	height: 250,
	width: 550,
	store: grupKota,
	bbar: Ext.create('Ext.PagingToolbar', {
		displayInfo: true,
		displayMsg: 'Displaying record {0} - {1} of {2}',
		emptyMsg: "No record to display",
		pageSize: 25,
		plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
		store: grupKota,
		items:[
			'-', {
			text: 'Exit',
			handler: function() {
				helpGrid12.hide();
			}
		}]
	}),
	columns: [
		{xtype: 'rownumberer', width: 45},
		{text: "Kode Kota", dataIndex: 'fs_kd_kota', width: 100},
		{text: "Nama Kota", dataIndex: 'fs_nm_kota', width: 380}
	],
    listeners: {
        itemclick: function(grid, record)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			record = grid.store.getAt(xpos.row);
			Ext.getCmp('2cboKota').setValue(record.get('fs_kd_kota'));
			Ext.getCmp('2txtKota').setValue(record.get('fs_nm_kota'));
		},
		itemdblclick: function(grid, record)
		{
			helpGrid12.hide();
		}
    }
});

var helpGrid12 = Ext.create('Ext.menu.Menu', {
	plain: true
});
helpGrid12.add(popGrid12);

Ext.define('Ext.ux.SearchKota', {
	alias: 'widget.searchKota',
	extend: 'Ext.form.field.Trigger',
	initComponent: function() {
		this.callParent(arguments);
		this.on('specialkey', function(f, e) {
			if (e.getKey() == e.ENTER) {
				this.onTriggerClick();
			}
		}, this);
	},
	onTriggerClick: function() {
		grupKota.load();
		popGrid12.show();
		helpGrid12.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
	}
});

var o2cboKota = {
	anchor: '98%',
	emptyText: "Pilih",
	fieldLabel: 'Kota di BPKB',
	fieldStyle: 'text-transform: uppercase',
	id: '2cboKota',
	labelWidth: 100,
	name: '2cboKota',
	xtype: 'searchKota',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
			Ext.getCmp('2txtKota').setValue('');
		}
	}
};

var o2txtKota = {
	anchor: '100%',
	emptyText: "Kota di BPKB",
	fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase;',
	id: '2txtKota',
	name: '2txtKota',
	readOnly: true,
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupLemKeu = Ext.create('Ext.data.Store', {
	autoLoad: true,
	fields: ['fs_kd_lemkeu','fs_nm_lemkeu'],
	pageSize: 25,
	proxy: {
		actionMethods: 'POST',
		reader: {
			root: 'hasil',
			totalProperty: 'total',
			type: 'json'
		},
		type: 'ajax',
		url: 'apkorang/kodelemkeu'
	}
});

grupLemKeu.on('beforeload', function(store, operation, eOpts) {
	operation.params = {
		'fs_kd_lemkeu': Ext.getCmp('2cboLembagaKeu').getValue(),
		'fs_nm_lemkeu': Ext.getCmp('2cboLembagaKeu').getValue()
	};
}, this);

var popGrid13 = Ext.create('Ext.ux.LiveSearchGridPanel', {
	autoDestroy: true,
	height: 250,
	width: 550,
	store: grupLemKeu,
	bbar: Ext.create('Ext.PagingToolbar', {
		displayInfo: true,
		displayMsg: 'Displaying record {0} - {1} of {2}',
		emptyMsg: "No record to display",
		pageSize: 25,
		plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
		store: grupLemKeu,
		items:[
			'-', {
			text: 'Exit',
			handler: function() {
				helpGrid13.hide();
			}
		}]
	}),
	columns: [
		{xtype: 'rownumberer', width: 45},
		{text: "Kode Lembaga Keuangan", dataIndex: 'fs_kd_lemkeu', width: 100},
		{text: "Nama Lembaga Keuangan", dataIndex: 'fs_nm_lemkeu', width: 380}
	],
    listeners: {
        itemclick: function(grid, record)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			record = grid.store.getAt(xpos.row);
			Ext.getCmp('2cboLembagaKeu').setValue(record.get('fs_kd_lemkeu'));
			Ext.getCmp('2txtLembagaKeu').setValue(record.get('fs_nm_lemkeu'));
		},
		itemdblclick: function(grid, record)
		{
			helpGrid13.hide();
		}
    }
});

var helpGrid13 = Ext.create('Ext.menu.Menu', {
	plain: true
});
helpGrid13.add(popGrid13);

Ext.define('Ext.ux.SearchLemKeu', {
	alias: 'widget.searchLemKeu',
	extend: 'Ext.form.field.Trigger',
	initComponent: function() {
		this.callParent(arguments);
		this.on('specialkey', function(f, e) {
			if (e.getKey() == e.ENTER) {
				this.onTriggerClick();
			}
		}, this);
	},
	onTriggerClick: function() {
		grupLemKeu.load();
		popGrid13.show();
		helpGrid13.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
	}
});

var o2cboLembagaKeu = {
	anchor: '98%',
	emptyText: "Pilih",
	fieldLabel: 'Lembaga Keuangan',
	fieldStyle: 'text-transform: uppercase',
	id: '2cboLembagaKeu',
	labelWidth: 110,
	name: '2cboLembagaKeu',
	xtype: 'searchLemKeu',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
			Ext.getCmp('2txtLembagaKeu').setValue('');
		}
	}
};

var o2txtLembagaKeu = {
	anchor: '100%',
	emptyText: "Lembaga Keuangan",
	fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase;',
	id: '2txtLembagaKeu',
	name: '2txtLembagaKeu',
	readOnly: true,
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupJenisAss = Ext.create('Ext.data.ArrayStore', {
	autoLoad: true,
	data: [['0', 'TOTAL LOSS ONLY'], ['1', 'ALL RISK']],
	fields: ['fs_kd_ass', 'fs_nm_ass']
});

var o2cboJenisAss = {
	anchor: '100%',
	displayField: 'fs_nm_ass',
	editable: false,
	emptyText: "Pilih Jenis Asuransi",
	fieldLabel: 'Jenis Asuransi',
	fieldStyle: 'text-transform: uppercase',
	id: '2cboJenisAss',
	labelWidth: 110,
	name: '2cboJenisAss',
	store: grupJenisAss,
	value: '0',
	valueField: 'fs_kd_ass',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o2txtSales = {
	anchor: '100%',
	emptyText: "Sales",
	fieldLabel: 'Sales',
	fieldStyle: 'text-transform: uppercase;',
	id: '2txtSales',
	name: '2txtSales',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupSupp = Ext.create('Ext.data.Store', {
	autoLoad: true,
	fields: ['fs_kd_supp','fs_nm_supp'],
	pageSize: 25,
	proxy: {
		actionMethods: 'POST',
		reader: {
			root: 'hasil',
			totalProperty: 'total',
			type: 'json'
		},
		type: 'ajax',
		url: 'apkorang/kodesupp'
	}
});

grupSupp.on('beforeload', function(store, operation, eOpts) {
	operation.params = {
		'fs_kd_supp': Ext.getCmp('2cboSupp').getValue(),
		'fs_nm_supp': Ext.getCmp('2cboSupp').getValue()
	};
}, this);

var popGrid14 = Ext.create('Ext.ux.LiveSearchGridPanel', {
	autoDestroy: true,
	height: 250,
	width: 550,
	store: grupSupp,
	bbar: Ext.create('Ext.PagingToolbar', {
		displayInfo: true,
		displayMsg: 'Displaying record {0} - {1} of {2}',
		emptyMsg: "No record to display",
		pageSize: 25,
		plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
		store: grupSupp,
		items:[
			'-', {
			text: 'Exit',
			handler: function() {
				helpGrid14.hide();
			}
		}]
	}),
	columns: [
		{xtype: 'rownumberer', width: 45},
		{text: "Kode Supplier", dataIndex: 'fs_kd_supp', width: 100},
		{text: "Nama Supplier", dataIndex: 'fs_nm_supp', width: 380}
	],
    listeners: {
        itemclick: function(grid, record)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			record = grid.store.getAt(xpos.row);
			Ext.getCmp('2cboSupp').setValue(record.get('fs_kd_supp'));
			Ext.getCmp('2txtSupp').setValue(record.get('fs_nm_supp'));
		},
		itemdblclick: function(grid, record)
		{
			helpGrid14.hide();
		}
    }
});

var helpGrid14 = Ext.create('Ext.menu.Menu', {
	plain: true
});
helpGrid14.add(popGrid14);

Ext.define('Ext.ux.SearchSupp', {
	alias: 'widget.searchSupp',
	extend: 'Ext.form.field.Trigger',
	initComponent: function() {
		this.callParent(arguments);
		this.on('specialkey', function(f, e) {
			if (e.getKey() == e.ENTER) {
				this.onTriggerClick();
			}
		}, this);
	},
	onTriggerClick: function() {
		grupSupp.load();
		popGrid14.show();
		helpGrid14.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
	}
});

var o2cboSupp = {
	anchor: '98%',
	emptyText: "Pilih",
	fieldLabel: 'Supplier',
	fieldStyle: 'text-transform: uppercase',
	id: '2cboSupp',
	name: '2cboSupp',
	xtype: 'searchSupp',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
			Ext.getCmp('2txtSupp').setValue('');
		}
	}
};

var o2txtSupp = {
	anchor: '100%',
	emptyText: "Supplier",
	fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase;',
	id: '2txtSupp',
	name: '2txtSupp',
	readOnly: true,
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupCabang = Ext.create('Ext.data.Store', {
	autoLoad: true,
	fields: ['fs_kd_cabang','fs_nm_cabang'],
	pageSize: 25,
	proxy: {
		actionMethods: 'POST',
		reader: {
			root: 'hasil',
			totalProperty: 'total',
			type: 'json'
		},
		type: 'ajax',
		url: 'apkorang/kodecabang'
	}
});

grupCabang.on('beforeload', function(store, operation, eOpts) {
	operation.params = {
		'fs_kd_supp': Ext.getCmp('2cboSupp').getValue(),
		'fs_kd_cabang': Ext.getCmp('2cboCab').getValue(),
		'fs_nm_cabang': Ext.getCmp('2cboCab').getValue()
	};
}, this);

var popGrid15 = Ext.create('Ext.ux.LiveSearchGridPanel', {
	autoDestroy: true,
	height: 250,
	width: 550,
	store: grupCabang,
	bbar: Ext.create('Ext.PagingToolbar', {
		displayInfo: true,
		displayMsg: 'Displaying record {0} - {1} of {2}',
		emptyMsg: "No record to display",
		pageSize: 25,
		plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
		store: grupCabang,
		items:[
			'-', {
			text: 'Exit',
			handler: function() {
				helpGrid15.hide();
			}
		}]
	}),
	columns: [
		{xtype: 'rownumberer', width: 45},
		{text: "Kode Cabang", dataIndex: 'fs_kd_cabang', width: 100},
		{text: "Nama Cabang", dataIndex: 'fs_nm_cabang', width: 380}
	],
    listeners: {
        itemclick: function(grid, record)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			record = grid.store.getAt(xpos.row);
			Ext.getCmp('2cboCab').setValue(record.get('fs_kd_cabang'));
			Ext.getCmp('2txtCab').setValue(record.get('fs_nm_cabang'));
		},
		itemdblclick: function(grid, record)
		{
			helpGrid15.hide();
		}
    }
});

var helpGrid15 = Ext.create('Ext.menu.Menu', {
	plain: true
});
helpGrid15.add(popGrid15);

Ext.define('Ext.ux.SearchCab', {
	alias: 'widget.searchCab',
	extend: 'Ext.form.field.Trigger',
	initComponent: function() {
		this.callParent(arguments);
		this.on('specialkey', function(f, e) {
			if (e.getKey() == e.ENTER) {
				this.onTriggerClick();
			}
		}, this);
	},
	onTriggerClick: function() {
		grupCabang.load();
		popGrid15.show();
		helpGrid15.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
	}
});

var o2cboCab = {
	anchor: '98%',
	emptyText: "Pilih",
	fieldLabel: 'Cabang',
	fieldStyle: 'text-transform: uppercase',
	id: '2cboCab',
	name: '2cboCab',
	xtype: 'searchCab',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
			Ext.getCmp('2txtCab').setValue('');
		}
	}
};

var o2txtCab = {
	anchor: '100%',
	emptyText: "Cabang",
	fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase;',
	id: '2txtCab',
	name: '2txtCab',
	readOnly: true,
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};
/* Eof Kendaraan	*/

/* Keuangan	*/
var grupPolaAngs = Ext.create('Ext.data.ArrayStore', {
	autoLoad: true,
	data: [['0', 'FDPK'], ['1', 'FLAT'], ['2','NOTARIIL']],
	fields: ['fs_kd_polaangs', 'fs_nm_polaangs']
});

var o3cboPola = {
	anchor: '100%',
	displayField: 'fs_nm_polaangs',
	editable: false,
	emptyText: "Pilih Pola Angsuran",
	fieldLabel: 'Pola Angsuran',
	fieldStyle: 'text-transform: uppercase',
	id: '3cboPola',
	name: '3cboPola',
	store: grupPolaAngs,
	value: '0',
	valueField: 'fs_kd_polaangs',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupBayar = Ext.create('Ext.data.ArrayStore', {
	autoLoad: true,
	data: [['0', 'CASH'], ['1', 'GIRO'], ['2','TRANSFER']],
	fields: ['fs_kd_bayar', 'fs_nm_bayar']
});

var o3cboCaraBayar = {
	anchor: '100%',
	displayField: 'fs_nm_bayar',
	editable: false,
	emptyText: "Pilih Cara Bayar",
	fieldLabel: 'Cara Bayar',
	fieldStyle: 'text-transform: uppercase',
	id: '3cboCaraBayar',
	name: '3cboCaraBayar',
	store: grupBayar,
	value: '0',
	valueField: 'fs_kd_bayar',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o3txtTdkAng = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Tdk. Ang',
	hideTrigger: true,
	id: '3txtTdkAng',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtTdkAng',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtTdkAng').getValue())) {
				Ext.getCmp('3txtTdkAng').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var grupDiMuka = Ext.create('Ext.data.ArrayStore', {
	autoLoad: true,
	data: [['0', 'TIDAK'], ['1', 'YA']],
	fields: ['fs_kd_dimuka', 'fs_nm_dimuka']
});

var o3cboDiMuka = {
	anchor: '100%',
	displayField: 'fs_nm_dimuka',
	editable: false,
	emptyText: "Pilih Di Muka",
	fieldLabel: 'Di Muka',
	fieldStyle: 'text-transform: uppercase',
	id: '3cboDiMuka',
	name: '3cboDiMuka',
	store: grupDiMuka,
	value: '0',
	valueField: 'fs_kd_dimuka',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupKali = Ext.create('Ext.data.ArrayStore', {
	autoLoad: true,
	data: [['0', '0'], ['1', '1'], ['2', '2'], ['3', '3'], ['4', '4']],
	fields: ['fs_kd_kali', 'fs_nm_kali']
});

var o3cboKali = {
	anchor: '100%',
	displayField: 'fs_nm_kali',
	editable: false,
	emptyText: "Pilih Kali",
	fieldLabel: 'Kali',
	fieldStyle: 'text-transform: uppercase',
	id: '3cboKali',
	labelWidth: 30,
	name: '3cboKali',
	store: grupKali,
	value: '0',
	valueField: 'fs_kd_kali',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupPtgDana = Ext.create('Ext.data.ArrayStore', {
	autoLoad: true,
	data: [['0', 'TIDAK'], ['1', 'YA']],
	fields: ['fs_kd_ptgdana', 'fs_nm_ptgdana']
});

var o3cboPotongDana = {
	anchor: '100%',
	displayField: 'fs_nm_ptgdana',
	editable: false,
	emptyText: "Pilih Potong Pencairan Dana",
	fieldLabel: 'Potong Pencairan Dana',
	fieldStyle: 'text-transform: uppercase',
	id: '3cboPotongDana',
	labelWidth: 130,
	name: '3cboPotongDana',
	store: grupPtgDana,
	value: '0',
	valueField: 'fs_kd_ptgdana',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var grupAngsByr = Ext.create('Ext.data.ArrayStore', {
	autoLoad: true,
	data: [['0', 'TIDAK'], ['1', 'YA']],
	fields: ['fs_kd_angsbyr', 'fs_nm_angsbyr']
});

var o3cboAngsByr = {
	anchor: '100%',
	displayField: 'fs_nm_angsbyr',
	editable: false,
	emptyText: "Pilih Angs. Dibyr Dealer",
	fieldLabel: 'Angs. Dibyr Dealer',
	fieldStyle: 'text-transform: uppercase',
	id: '3cboAngsByr',
	name: '3cboAngsByr',
	store: grupAngsByr,
	value: '0',
	valueField: 'fs_kd_angsbyr',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o3txtBiaya = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Biaya Administrasi',
	hideTrigger: true,
	id: '3txtBiaya',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtBiaya',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtBiaya').getValue())) {
				Ext.getCmp('3txtBiaya').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o3txtGros = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Premi As. Gross',
	hideTrigger: true,
	id: '3txtGros',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtGros',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtGros').getValue())) {
				Ext.getCmp('3txtGros').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o3txtNetto = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Premi As. Netto',
	hideTrigger: true,
	id: '3txtNetto',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtNetto',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtNetto').getValue())) {
				Ext.getCmp('3txtNetto').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o3txtDenda = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: true,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 1,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Denda / Hari (MIL)',
	hideTrigger: true,
	id: '3txtDenda',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtDenda',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtDenda').getValue())) {
				Ext.getCmp('3txtDenda').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

function fnRate(nper,pmt,pv,fv,type,guess) {
	Ext.Ajax.on('beforerequest', vMask.show, vMask);
	Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
	Ext.Ajax.on('requestexception', vMask.hide, vMask);
	
	Ext.Ajax.request({
		method: 'POST',
		url: 'apkorang/rate',
		params: {
			'fn_nper': nper,
			'fn_pmt': pmt,
			'fn_pv': pv,
			'fn_fv': fv,
			'fn_tipe': type,
			'fn_guess': guess
		},
		success: function(response, opts) {
			var xtext = Ext.decode(response.responseText);
			
			Ext.getCmp('3txtBungaED').setValue(xtext.nilairate);
		},
		failure: function(response, opts) {
			var xtext = Ext.decode(response.responseText);
			Ext.Msg.show({
				buttons: Ext.MessageBox.OK,
				icon: Ext.Msg.INFO,
				msg: 'Rate Failed, Connection Failed',
				title: 'ASTER'
			});
		}
	});
}

function fnRate2(nper,pmt,pv,fv,type,guess) {
	Ext.Ajax.on('beforerequest', vMask.show, vMask);
	Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
	Ext.Ajax.on('requestexception', vMask.hide, vMask);
	
	Ext.Ajax.request({
		method: 'POST',
		url: 'apkorang/rate',
		params: {
			'fn_nper': nper,
			'fn_pmt': pmt,
			'fn_pv': pv,
			'fn_fv': fv,
			'fn_tipe': type,
			'fn_guess': guess
		},
		success: function(response, opts) {
			var xtext = Ext.decode(response.responseText);
			
			Ext.getCmp('3txtBungaEK').setValue(xtext.nilairate);
		},
		failure: function(response, opts) {
			var xtext = Ext.decode(response.responseText);
			Ext.Msg.show({
				buttons: Ext.MessageBox.OK,
				icon: Ext.Msg.INFO,
				msg: 'Rate Failed, Connection Failed',
				title: 'ASTER'
			});
		}
	});
}

function hitung11() {
	var xum = Ext.getCmp('3txtUMD').getValue();
	Ext.getCmp('3txtUMK').setValue(xum);
	var xharga = Ext.getCmp('3txtOTRD').getValue();
	Ext.getCmp('3txtOTRK').setValue(xharga);
	var xpersen = xum * 100 / xharga;
	
	if (xpersen > 0 && xpersen <= 100) {
		Ext.getCmp('3txtUMPersenD').setValue(xpersen);
		Ext.getCmp('3txtUMPersenK').setValue(xpersen);
	}
	else {
		Ext.getCmp('3txtUMPersenD').setValue('0');
		Ext.getCmp('3txtUMPersenK').setValue('0');
	}
	
	var xjml = xharga - xum;
	Ext.getCmp('3txtJumlahD').setValue(xjml);
	Ext.getCmp('3txtJumlahK').setValue(xjml);
	
	
	var xbln = Ext.getCmp('3txtMasaBlnD').getValue();
	Ext.getCmp('3txtMasaBlnK').setValue(xbln);
	Ext.getCmp('3txtMasaKaliD').setValue(xbln);
	Ext.getCmp('3txtMasaKaliK').setValue(xbln);
	
	var xangs = Ext.getCmp('3txtAngsD').getValue();
	Ext.getCmp('3txtAngsK').setValue(xangs);
	
	var xkontrak = xbln * xangs;
	Ext.getCmp('3txtNilaiKontrakD').setValue(xkontrak);
	Ext.getCmp('3txtNilaiKontrakK').setValue(xkontrak);
	
	var xbunga = xkontrak - xjml;
	Ext.getCmp('3txtBungaD').setValue(xbunga);
	Ext.getCmp('3txtBungaK').setValue(xbunga);
	
	var xbungaf = ((xbunga / xjml) / xbln) * 12 * 100;
	if (xbungaf > 0 && xbungaf <= 100) {
		Ext.getCmp('3txtBungaFD').setValue(xbungaf);
		Ext.getCmp('3txtBungaFK').setValue(xbungaf);
	}
	else {
		Ext.getCmp('3txtBungaFD').setValue('0');
		Ext.getCmp('3txtBungaFK').setValue('0');
	}
	
	if (xbln > 0 && xangs > 0 && xjml > 0) {
		fnRate(xbln,xangs,xjml,0,0,10);
	}
}

function hitung21() {
	var xum = Ext.getCmp('3txtUMK').getValue();
	var xharga = Ext.getCmp('3txtOTRK').getValue();
	var xpersen = xum * 100 / xharga;
	
	if (xpersen > 0 && xpersen <= 100) {
		Ext.getCmp('3txtUMPersenK').setValue(xpersen);
	}
	else {
		Ext.getCmp('3txtUMPersenK').setValue('0');
	}
	
	var xjml = xharga - xum;
	Ext.getCmp('3txtJumlahK').setValue(xjml);
	
	
	var xbln = Ext.getCmp('3txtMasaBlnK').getValue();
	Ext.getCmp('3txtMasaKaliK').setValue(xbln);
	var xangs = Ext.getCmp('3txtAngsK').getValue();
	
	var xkontrak = xbln * xangs;
	Ext.getCmp('3txtNilaiKontrakK').setValue(xkontrak);
	
	var xbunga = xkontrak - xjml;
	Ext.getCmp('3txtBungaK').setValue(xbunga);
	
	var xbungaf = ((xbunga / xjml) / xbln) * 12 * 100;
	if (xbungaf > 0 && xbungaf <= 100) {
		Ext.getCmp('3txtBungaFK').setValue(xbungaf);
	}
	else {
		Ext.getCmp('3txtBungaFK').setValue('0');
	}
	
	if (xbln > 0 && xangs > 0 && xjml > 0) {
		fnRate2(xbln,xangs,xjml,0,0,10);
	}
}

var o3txtOTRD = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '98%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Harga OTR',
	hideTrigger: true,
	id: '3txtOTRD',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtOTRD',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtOTRD').getValue())) {
				Ext.getCmp('3txtOTRD').setValue(0);
			}
			else {
				hitung11();
				return value;
			}
		}
	}
});

var o3txtOTRK = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: '',
	hideTrigger: true,
	id: '3txtOTRK',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtOTRK',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtOTRK').getValue())) {
				Ext.getCmp('3txtOTRK').setValue(0);
			}
			else {
				hitung21();
				return value;
			}
		}
	}
});

var o3txtUMPersenD = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: true,
	anchor: '99%',
	currencySymbol: null,
	decimalPrecision: 2,
	decimalSeparator: '.',
	editable: false,
	fieldLabel: 'Uang Muka',
	fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
	hideTrigger: true,
	id: '3txtUMPersenD',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtUMPersenD',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtUMPersenD').getValue())) {
				Ext.getCmp('3txtUMPersenD').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o3txtUMPersenK = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: true,
	anchor: '95%',
	currencySymbol: null,
	decimalPrecision: 2,
	decimalSeparator: '.',
	editable: false,
	fieldLabel: '',
	fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
	hideTrigger: true,
	id: '3txtUMPersenK',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtUMKPersen',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtUMPersenK').getValue())) {
				Ext.getCmp('3txtUMPersenK').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o3txtUMD = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '96%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: '%',
	hideTrigger: true,
	id: '3txtUMD',
	keyNavEnabled: false,
	labelAlign: 'left',
	labelWidth: 15,
	mouseWheelEnabled: false,
	name: '3txtUMD',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtUMD').getValue())) {
				Ext.getCmp('3txtUMD').setValue(0);
			}
			else {
				hitung11();
				return value;
			}
		}
	}
});

var o3txtUMK = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: '%',
	hideTrigger: true,
	id: '3txtUMK',
	keyNavEnabled: false,
	labelAlign: 'left',
	labelWidth: 15,
	mouseWheelEnabled: false,
	name: '3txtUMK',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtUMK').getValue())) {
				Ext.getCmp('3txtUMK').setValue(0);
			}
			else {
				hitung21();
				return value;
			}
		}
	}
});

var o3txtAssD = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '98%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Asuransi',
	hideTrigger: true,
	id: '3txtAssD',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtAssD',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtAssD').getValue())) {
				Ext.getCmp('3txtAssD').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o3txtAssK = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: '',
	hideTrigger: true,
	id: '3txtAssK',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtAssK',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtAssK').getValue())) {
				Ext.getCmp('3txtAssK').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o3txtJumlahD = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '98%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: false,
	fieldLabel: 'Jumlah Pembiayaan',
	fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
	hideTrigger: true,
	id: '3txtJumlahD',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtJumlahD',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtJumlahD').getValue())) {
				Ext.getCmp('3txtJumlahD').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o3txtJumlahK = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: false,
	fieldLabel: '',
	fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
	hideTrigger: true,
	id: '3txtJumlahK',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtJumlahK',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtJumlahK').getValue())) {
				Ext.getCmp('3txtJumlahK').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o3txtBungaFD = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: true,
	anchor: '98%',
	currencySymbol: null,
	decimalPrecision: 2,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Bunga (% Flat / % Efektif)',
	hideTrigger: true,
	id: '3txtBungaFD',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtBungaFD',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtBungaFD').getValue())) {
				Ext.getCmp('3txtBungaFD').setValue(0);
			}
			else {
				hitung11();
				return value;
			}
		}
	}
});

var o3txtBungaED = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: true,
	anchor: '94%',
	currencySymbol: null,
	decimalPrecision: 2,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: '',
	hideTrigger: true,
	id: '3txtBungaED',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtBungaED',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtBungaED').getValue())) {
				Ext.getCmp('3txtBungaED').setValue(0);
			}
			else {
				hitung11();
				return value;
			}
		}
	}
});

var o3txtBungaFK = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: true,
	anchor: '95%',
	currencySymbol: null,
	decimalPrecision: 2,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: '',
	hideTrigger: true,
	id: '3txtBungaFK',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtBungaFK',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtBungaFK').getValue())) {
				Ext.getCmp('3txtBungaFK').setValue(0);
			}
			else {
				hitung21();
				return value;
			}
		}
	}
});

var o3txtBungaEK = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: true,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 2,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: '',
	hideTrigger: true,
	id: '3txtBungaEK',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtBungaEK',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtBungaEK').getValue())) {
				Ext.getCmp('3txtBungaEK').setValue(0);
			}
			else {
				hitung21();
				return value;
			}
		}
	}
});

var o3txtMasaBlnD = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '98%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Masa Angsuran (Bln / Kali)',
	hideTrigger: true,
	id: '3txtMasaBlnD',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtMasaBlnD',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtMasaBlnD').getValue())) {
				Ext.getCmp('3txtMasaBlnD').setValue(0);
			}
			else {
				hitung11();
				return value;
			}
		}
	}
});

var o3txtMasaBlnK = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '95%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: '',
	hideTrigger: true,
	id: '3txtMasaBlnK',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtMasaBlnK',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtMasaBlnK').getValue())) {
				Ext.getCmp('3txtMasaBlnK').setValue(0);
			}
			else {
				hitung21();
				return value;
			}
		}
	}
});

var o3txtMasaKaliD = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '94%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: '',
	hideTrigger: true,
	id: '3txtMasaKaliD',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtMasaKaliD',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtMasaKaliD').getValue())) {
				Ext.getCmp('3txtMasaKaliD').setValue(0);
			}
			else {
				hitung11();
				return value;
			}
		}
	}
});

var o3txtMasaKaliK = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: '',
	hideTrigger: true,
	id: '3txtMasaKaliK',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtMasaKaliK',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtMasaKaliK').getValue())) {
				Ext.getCmp('3txtMasaKaliK').setValue(0);
			}
			else {
				hitung21();
				return value;
			}
		}
	}
});

var o3txtBungaD = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '98%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Bunga',
	hideTrigger: true,
	id: '3txtBungaD',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtBungaD',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtBungaD').getValue())) {
				Ext.getCmp('3txtBungaD').setValue(0);
			}
			else {
				hitung11();
				return value;
			}
		}
	}
});

var o3txtBungaK = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: '',
	hideTrigger: true,
	id: '3txtBungaK',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtBungaK',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtBungaK').getValue())) {
				Ext.getCmp('3txtBungaK').setValue(0);
			}
			else {
				hitung21();
				return value;
			}
		}
	}
});

var o3txtNilaiKontrakD = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '98%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: false,
	fieldLabel: 'Nilai Kontrak',
	fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
	hideTrigger: true,
	id: '3txtNilaiKontrakD',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtNilaiKontrakD',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtNilaiKontrakD').getValue())) {
				Ext.getCmp('3txtNilaiKontrakD').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o3txtNilaiKontrakK = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: false,
	fieldLabel: '',
	fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
	hideTrigger: true,
	id: '3txtNilaiKontrakK',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtNilaiKontrakK',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtNilaiKontrakK').getValue())) {
				Ext.getCmp('3txtNilaiKontrakK').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o3txtAngsD = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '98%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Angsuran / Bln',
	hideTrigger: true,
	id: '3txtAngsD',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtAngsD',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtAngsD').getValue())) {
				Ext.getCmp('3txtAngsD').setValue(0);
			}
			else {
				hitung11();
				return value;
			}
		}
	}
});

var o3txtAngsK = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: '',
	hideTrigger: true,
	id: '3txtAngsK',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtAngsK',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtAngsK').getValue())) {
				Ext.getCmp('3txtAngsK').setValue(0);
			}
			else {
				hitung21();
				return value;
			}
		}
	}
});

var o3txtAngsTD = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '98%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Angsuran Tidak Sama',
	hideTrigger: true,
	id: '3txtAngsTD',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtAngsTD',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtAngsTD').getValue())) {
				Ext.getCmp('3txtAngsTD').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o3txtAngsTK = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: '',
	hideTrigger: true,
	id: '3txtAngsTK',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '3txtAngsTK',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('3txtAngsTK').getValue())) {
				Ext.getCmp('3txtAngsTK').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});
/* Eof Keuangan	*/

/* Detil Trs & Data Angsuran	*/
var grupTagih = Ext.create('Ext.data.ArrayStore', {
	autoLoad: true,
	data: [[''], ['+'], ['-']],
	fields: ['fn_tagih']
});

var grupCair = Ext.create('Ext.data.ArrayStore', {
	autoLoad: true,
	data: [[''], ['+'], ['-']],
	fields: ['fn_cair']
});

var cellEditingTrs = Ext.create('Ext.grid.plugin.CellEditing', {
	clicksToEdit: 2
});

cellEditingTrs.on({
	scope: this
});

Ext.define('DataGridTrs', {
	extend: 'Ext.data.Model',
	fields: [
		{name: 'fs_kd_trs', type: 'string'},
		{name: 'fs_nm_trs', type: 'string'},
		{name: 'fn_nilai', type: 'float'},
		{name: 'fn_tagih', type: 'string'},
		{name: 'fn_cair', type: 'string'}
	]
});

var grupGridTrs = Ext.create('Ext.data.Store', {
	autoLoad: true,
	model: 'DataGridTrs',
	proxy: {
		actionMethods: 'POST',
		reader: {
			type: 'json'
		},
		type: 'ajax',
		url: ''
	}
});

grupGridTrs.on('beforeload', function(store, operation, eOpts) {
	operation.params = {
		// 'fs_refno': Ext.getCmp('cboRefno').getValue()
	};
}, this);

var gridTrs = Ext.create('Ext.grid.Panel', {
	defaultType: 'textfield',
	height: 150,
	sortableColumns: false,
	store: grupGridTrs,
	columns: [{
		xtype: 'rownumberer'
	},{
		text: 'Kode',
		dataIndex: 'fs_kd_trs',
		width: 100
	},{
		text: 'Nama Trs',
		dataIndex: 'fs_nm_trs',
		width: 200
	},{
		align: 'right',
		text: 'Nilai Trs',
		dataIndex: 'fn_nilai',
		format: '0,000.00',
		width: 150,
		xtype: 'numbercolumn',
		editor: [
			Ext.create('Ext.ux.form.NumericField', {
				alwaysDisplayDecimals: true,
				currencySymbol: null,
				decimalPrecision: 2,
				decimalSeparator: '.',
				hideTrigger: true,
				keyNavEnabled: false,
				mouseWheelEnabled: false,
				thousandSeparator: ',',
				useThousandSeparator: true,
				xtype: 'numberfield'
			})
		]
	},{
		text: 'Ditagih Ke Kons',
		dataIndex: 'fn_tagih',
		width: 100
	},{
		text: 'Pencairan Ke Dlr',
		dataIndex: 'fn_cair',
		width: 100
	}],
	listeners: {
		'selectionchange': function(view, records) {
			gridTrs.down('#removeData').setDisabled(!records.length);
		}
	},
	plugins: [
		cellEditingTrs
	],
	tbar: [{
		flex: 2,
		layout: 'anchor',
		xtype: 'container',
		items: [{
			anchor: '95%',
			emptyText: 'Pilih Trs',
			fieldLabel: 'Kode Trs',
			fieldStyle: 'text-transform: uppercase',
			id: 'cboTrs',
			labelAlign: 'top',
			name: 'cboTrs',
			xtype: 'combobox',
			listeners: {
				change: function(field, newValue) {
					field.setValue(newValue.toUpperCase());
					Ext.getCmp('txtTrs').setValue('');
				}
			}
		},{
			anchor: '95%',
			emptyText: 'Input Nama Trs',
			fieldStyle: 'text-transform: uppercase',
			hidden: true,
			id: 'txtTrs',
			name: 'txtTrs',
			xtype: 'textfield',
			listeners: {
				change: function(field, newValue) {
					field.setValue(newValue.toUpperCase());
				}
			}
		}]
	},{
		flex: 1,
		layout: 'anchor',
		xtype: 'container',
		items: [
			Ext.create('Ext.ux.form.NumericField', {
				alwaysDisplayDecimals: true,
				anchor: '95%',
				currencySymbol: null,
				decimalPrecision: 2,
				decimalSeparator: '.',
				fieldLabel: 'Nilai Trs',
				hideTrigger: true,
				id: 'txtNilai',
				keyNavEnabled: false,
				labelAlign: 'top',
				mouseWheelEnabled: false,
				name: 'txtNilai',
				thousandSeparator: ',',
				useThousandSeparator: true,
				value: 0,
				listeners: {
					change: function(value) {
						if (Ext.isEmpty(Ext.getCmp('txtNilai').getValue())) {
							Ext.getCmp('txtNilai').setValue(0);
						}
						else {
							return value;
						}
					}
				}
			})
		]
	},{
		flex: 1,
		layout: 'anchor',
		xtype: 'container',
		items: [{
			anchor: '95%',
			editable: false,
			emptyText: 'Pilih Ditagih Ke Konsumen',
			displayField: 'fn_tagih',
			fieldLabel: 'Ditagih Ke Konsumen',
			id: 'cboTagih',
			labelAlign: 'top',
			name: 'cboTagih',
			store: grupTagih,
			valueField: 'fn_tagih',
			xtype: 'combobox'
		}]
	},{
		flex: 1,
		layout: 'anchor',
		xtype: 'container',
		items: [{
			anchor: '95%',
			editable: false,
			emptyText: 'Pilih Pencairan Ke Dealer',
			displayField: 'fn_cair',
			fieldLabel: 'Pencairan Ke Dealer',
			id: 'cboCair',
			labelAlign: 'top',
			name: 'cboCair',
			store: grupCair,
			valueField: 'fn_cair',
			xtype: 'combobox'
		}]
	},{
		xtype: 'buttongroup',
		columns: 2,
		defaults: {
			scale: 'small'
		},
		items: [{
			iconCls: 'icon-add',
			text: 'Add',
			handler: function() {
				var xtotal = grupGridTrs.getCount();
				var xdata = Ext.create('DataGridTrs', {
					fs_kd_trs: Ext.getCmp('cboTrs').getValue(),
					fs_nm_trs: Ext.getCmp('txtTrs').getValue(),
					fn_nilai: Ext.getCmp('txtNilai').getValue(),
					fn_tagih: Ext.getCmp('cboTagih').getValue(),
					fn_cair: Ext.getCmp('cboCair').getValue()
				});
				
				var xtrs = Ext.getCmp('txtTrs').getValue();
				var xtagih = Ext.getCmp('cboTagih').getValue();
				var xcair = Ext.getCmp('cboCair').getValue();
				if (trim(xtrs) === '') {
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						icon: Ext.Msg.WARNING,
						msg: 'Kode transaksi tdk ada dalam daftar!',
						title: 'ASTER'
					});
					return;
				}
				
				grupGridTrs.insert(xtotal, xdata);
				Ext.getCmp('cboTrs').setValue('');
				Ext.getCmp('txtTrs').setValue('');
				Ext.getCmp('txtNilai').setValue('0');
				Ext.getCmp('cboTagih').setValue('');
				Ext.getCmp('cboCair').setValue('');
			}
		},{
			iconCls: 'icon-delete',
			itemId: 'removeData',
			text: 'Delete',
			handler: function() {
				var sm = gridTrs.getSelectionModel();
				cellEditingTrs.cancelEdit();
				grupGridTrs.remove(sm.getSelection());
				gridTrs.getView().refresh();
				if (grupGridTrs.getCount() > 0) {
					sm.select(0);
				}
			},
			disabled: true
		}]
	}],
	viewConfig: {
		markDirty: false
	}
});

var cellEditingAngs = Ext.create('Ext.grid.plugin.CellEditing', {
	clicksToEdit: 2
});

cellEditingAngs.on({
	scope: this
});

Ext.define('DataGridAngs', {
	extend: 'Ext.data.Model',
	fields: [
		{name: 'fn_ke', type: 'string'},
		{name: 'fd_angs', type: 'date', dateFormat: 'd-m-Y'},
		{name: 'fn_pokok', type: 'string'},
		{name: 'fn_bunga', type: 'string'},
		{name: 'fn_angs', type: 'string'}
	]
});

var grupGridAngs = Ext.create('Ext.data.Store', {
	autoLoad: true,
	model: 'DataGridAngs',
	proxy: {
		actionMethods: 'POST',
		reader: {
			type: 'json'
		},
		type: 'ajax',
		url: ''
	}
});

grupGridAngs.on('beforeload', function(store, operation, eOpts) {
	operation.params = {
		// 'fs_refno': Ext.getCmp('cboRefno').getValue()
	};
}, this);

var gridAngs = Ext.create('Ext.grid.Panel', {
	defaultType: 'textfield',
	height: 150,
	sortableColumns: false,
	store: grupGridAngs,
	columns: [{
		xtype: 'rownumberer'
	},{
		align: 'right',
		text: 'Angsuran Ke',
		dataIndex: 'fn_ke',
		width: 80,
		renderer: function(value, metaData, record, rowIdx, colIdx, store) {
			var rowspan = this.rowspan,
				page = store.currentPage,
				result = record.index;
				
			if (rowspan) {
				metaData.tdAttr = 'rowspan="' + rowspan + '"';
			}

			if (result === null) {
				result = rowIdx;
				if (page > 1) {
					result += (page - 1) * store.pageSize; 
				}
			}
			return result + 1;
		}
	},{
		text: 'Tgl Jatuh Tempo',
		dataIndex: 'fd_angs',
		format: 'd-m-Y',
		renderer: Ext.util.Format.dateRenderer('d-m-Y'),
		width: 100,
		xtype: 'datecolumn',
		editor: {
			editable: true,
			format: 'd-m-Y',
			maskRe: /[0-9-]/,
			minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
			xtype: 'datefield'
		}
	},{
		align: 'right',
		text: 'Nilai Pokok',
		dataIndex: 'fn_pokok',
		format: '0,000.00',
		width: 150,
		xtype: 'numbercolumn',
		editor: [
			Ext.create('Ext.ux.form.NumericField', {
				alwaysDisplayDecimals: true,
				currencySymbol: null,
				decimalPrecision: 2,
				decimalSeparator: '.',
				hideTrigger: true,
				keyNavEnabled: false,
				mouseWheelEnabled: false,
				thousandSeparator: ',',
				useThousandSeparator: true,
				xtype: 'numberfield'
			})
		]
	},{
		align: 'right',
		text: 'Nilai Bunga',
		dataIndex: 'fn_bunga',
		format: '0,000.00',
		width: 150,
		xtype: 'numbercolumn',
		editor: [
			Ext.create('Ext.ux.form.NumericField', {
				alwaysDisplayDecimals: true,
				currencySymbol: null,
				decimalPrecision: 2,
				decimalSeparator: '.',
				hideTrigger: true,
				keyNavEnabled: false,
				mouseWheelEnabled: false,
				thousandSeparator: ',',
				useThousandSeparator: true,
				xtype: 'numberfield'
			})
		]
	},{
		align: 'right',
		text: 'Nilai Angsuran',
		dataIndex: 'fn_angs',
		format: '0,000.00',
		width: 150,
		xtype: 'numbercolumn',
		editor: [
			Ext.create('Ext.ux.form.NumericField', {
				alwaysDisplayDecimals: true,
				currencySymbol: null,
				decimalPrecision: 2,
				decimalSeparator: '.',
				hideTrigger: true,
				keyNavEnabled: false,
				mouseWheelEnabled: false,
				thousandSeparator: ',',
				useThousandSeparator: true,
				xtype: 'numberfield'
			})
		]
	}],
	listeners: {
		'selectionchange': function(view, records) {
			gridAngs.down('#removeData').setDisabled(!records.length);
		}
	},
	plugins: [
		cellEditingAngs
	],
	tbar: ['->',{
		xtype: 'buttongroup',
		columns: 2,
		defaults: {
			scale: 'small'
		},
		items: [{
			iconCls: 'icon-add',
			text: 'Add',
			handler: function() {
				var xtotal = grupGridAngs.getCount();
				var xdata = Ext.create('DataGridAngs', {
					fn_ke: '',
					fd_angs: '',
					fn_pokok: '0',
					fn_bunga: '0',
					fn_angs: '0'
				});
				
				grupGridAngs.insert(xtotal, xdata);
			}
		},{
			iconCls: 'icon-delete',
			itemId: 'removeData',
			text: 'Delete',
			handler: function() {
				var sm = gridAngs.getSelectionModel();
				cellEditingAngs.cancelEdit();
				grupGridAngs.remove(sm.getSelection());
				gridAngs.getView().refresh();
				if (grupGridAngs.getCount() > 0) {
					sm.select(0);
				}
			},
			disabled: true
		}]
	}],
	viewConfig: {
		markDirty: false
	}
});
/* Eof Detil Trs & Data Angsuran	*/

/* Tambahan	*/
var o5txtNama = {
	anchor: '100%',
	emptyText: "Nama Suami / Istri",
	fieldLabel: 'Nama',
	fieldStyle: 'text-transform: uppercase;',
	id: '5txtNama',
	name: '5txtNama',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o5txtAlamat = {
	fieldLabel: 'Alamat',
	id: '5txtAlamat',
	name: '5txtAlamat',
	value: '',
	xtype: 'displayfield'
};

var o5txtKota = {
	fieldLabel: 'Kota / Kode Pos',
	id: '5txtKota',
	name: '5txtKota',
	value: '',
	xtype: 'displayfield'
};

var o5txtTlp = {
	fieldLabel: 'Telp',
	id: '5txtTlp',
	name: '5txtTlp',
	value: '',
	xtype: 'displayfield'
};

var o5cboKerja = {
	anchor: '98%',
	emptyText: "Pilih",
	fieldLabel: 'Usaha / Pekerjaan',
	fieldStyle: 'text-transform: uppercase',
	id: '5cboKerja',
	name: '5cboKerja',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
			Ext.getCmp('5txtKerja').setValue('');
		}
	}
};

var o5txtKerja = {
	anchor: '100%',
	emptyText: "Usaha / Pekerjaan Suami / Istri",
	fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase;',
	id: '5txtKerja',
	name: '5txtKerja',
	readOnly: true,
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o5txtKetKerja = {
	anchor: '100%',
	emptyText: "Keterangan Usaha / Pekerjaan Suami / Istri",
	fieldLabel: 'Ket. Usaha',
	fieldStyle: 'text-transform: uppercase',
	id: '5txtKetKerja',
	labelWidth: 100,
	name: '5txtKetKerja',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o5txtAlamatPt = {
	anchor: '100%',
	emptyText: "Alamat Usaha / Pekerjaan Suami / Istri",
	fieldLabel: 'Alamat Usaha',
	fieldStyle: 'text-transform: uppercase',
	id: '5txtAlamatPt',
	name: '5txtAlamatPt',
	rows: 1,
	xtype: 'textarea',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o5txtTlpPt = {
	anchor: '100%',
	emptyText: "Telp Usaha / Pekerjaan Suami / Istri",
	fieldLabel: 'Telp Usaha',
	fieldStyle: 'text-transform: uppercase',
	id: '5txtTlpPt',
	name: '5txtTlpPt',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o5txtGaji = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '60%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Pendapatan',
	hideTrigger: true,
	id: '5txtGaji',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '5txtGaji',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('5txtGaji').getValue())) {
				Ext.getCmp('5txtGaji').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o5txtStatus = {
	fieldLabel: 'Status Penjamin',
	id: '5txtStatus',
	name: '5txtStatus',
	value: '',
	xtype: 'displayfield'
};

var o5txtNama2 = {
	anchor: '100%',
	emptyText: "Nama Penjamin",
	fieldStyle: 'text-transform: uppercase;',
	id: '5txtNama2',
	name: '5txtNama2',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o5txtAlamat2 = {
	anchor: '100%',
	emptyText: "Alamat Penjamin",
	fieldStyle: 'text-transform: uppercase',
	id: '5txtAlamat2',
	name: '5txtAlamat2',
	rows: 1,
	xtype: 'textarea',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o5txtKota2 = {
	anchor: '98%',
	emptyText: "Kota Penjamin",
	fieldStyle: 'text-transform: uppercase',
	id: '5txtKota2',
	name: '5txtKota2',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o5txtKodePos2 = {
	anchor: '100%',
	emptyText: "Kode Pos Penjamin",
	fieldStyle: 'text-transform: uppercase',
	id: '5txtKodePos2',
	name: '5txtKodePos2',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o5txtTlp2 = {
	anchor: '100%',
	emptyText: "Telp Penjamin",
	fieldStyle: 'text-transform: uppercase',
	id: '5txtTlp2',
	name: '5txtTlp2',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o5cboKerja2 = {
	anchor: '98%',
	emptyText: "Pilih",
	fieldStyle: 'text-transform: uppercase',
	id: '5cboKerja2',
	name: '5cboKerja2',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
			Ext.getCmp('5txtKerja2').setValue('');
		}
	}
};

var o5txtKerja2 = {
	anchor: '100%',
	emptyText: "Usaha / Pekerjaan Penjamin",
	fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase;',
	id: '5txtKerja2',
	name: '5txtKerja2',
	readOnly: true,
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o5txtKetKerja2 = {
	anchor: '100%',
	emptyText: "Keterangan Usaha / Pekerjaan Penjamin",
	fieldStyle: 'text-transform: uppercase',
	id: '5txtKetKerja2',
	name: '5txtKetKerja2',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o5txtAlamatPt2 = {
	anchor: '100%',
	emptyText: "Alamat Usaha / Pekerjaan Penjamin",
	fieldStyle: 'text-transform: uppercase',
	id: '5txtAlamatPt2',
	name: '5txtAlamatPt2',
	rows: 1,
	xtype: 'textarea',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o5txtTlpPt2 = {
	anchor: '100%',
	emptyText: "Telp Usaha / Pekerjaan Penjamin",
	fieldStyle: 'text-transform: uppercase',
	id: '5txtTlpPt2',
	name: '5txtTlpPt2',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o5txtGaji2 = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '50%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: '',
	hideTrigger: true,
	id: '5txtGaji2',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '5txtGaji2',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('5txtGaji2').getValue())) {
				Ext.getCmp('5txtGaji2').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o5txtStatus2 = {
	anchor: '100%',
	emptyText: "Status Penjamin",
	fieldStyle: 'text-transform: uppercase',
	id: '5txtStatus2',
	name: '5txtStatus2',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};
/* Eof Tambahan	*/

/* Survey	*/
var o6txtSaldoRata = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Saldo Rata-Rata',
	hideTrigger: true,
	id: '6txtSaldoRata',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '6txtSaldoRata',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('6txtSaldoRata').getValue())) {
				Ext.getCmp('6txtSaldoRata').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o6txtSaldoAwal = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Saldo Awal',
	hideTrigger: true,
	id: '6txtSaldoAwal',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '6txtSaldoAwal',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('6txtSaldoAwal').getValue())) {
				Ext.getCmp('6txtSaldoAwal').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o6txtSaldoAkhir = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Saldo Akhir',
	hideTrigger: true,
	id: '6txtSaldoAkhir',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '6txtSaldoAkhir',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('6txtSaldoAkhir').getValue())) {
				Ext.getCmp('6txtSaldoAkhir').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o6txtJmlMasuk = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Jml Pemasukan',
	hideTrigger: true,
	id: '6txtJmlMasuk',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '6txtJmlMasuk',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('6txtJmlMasuk').getValue())) {
				Ext.getCmp('6txtJmlMasuk').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o6txtJmlAmbil = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Jml Pengambilan',
	hideTrigger: true,
	id: '6txtJmlAmbil',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '6txtJmlAmbil',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('6txtJmlAmbil').getValue())) {
				Ext.getCmp('6txtJmlAmbil').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o6txtJmlHari = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Jml Hari Transaksi',
	hideTrigger: true,
	id: '6txtJmlHari',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '6txtJmlHari',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('6txtJmlHari').getValue())) {
				Ext.getCmp('6txtJmlHari').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o6txtSaldoRataPt = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Saldo Rata-Rata',
	hideTrigger: true,
	id: '6txtSaldoRataPt',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '6txtSaldoRataPt',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('6txtSaldoRataPt').getValue())) {
				Ext.getCmp('6txtSaldoRataPt').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o6txtSaldoAwalPt = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Saldo Awal',
	hideTrigger: true,
	id: '6txtSaldoAwalPt',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '6txtSaldoAwalPt',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('6txtSaldoAwalPt').getValue())) {
				Ext.getCmp('6txtSaldoAwalPt').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o6txtSaldoAkhirPt = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Saldo Akhir',
	hideTrigger: true,
	id: '6txtSaldoAkhirPt',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '6txtSaldoAkhirPt',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('6txtSaldoAkhirPt').getValue())) {
				Ext.getCmp('6txtSaldoAkhirPt').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o6txtJmlMasukPt = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Jml Pemasukan',
	hideTrigger: true,
	id: '6txtJmlMasukPt',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '6txtJmlMasukPt',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('6txtJmlMasukPt').getValue())) {
				Ext.getCmp('6txtJmlMasukPt').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o6txtJmlAmbilPt = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Jml Pengambilan',
	hideTrigger: true,
	id: '6txtJmlAmbilPt',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '6txtJmlAmbilPt',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('6txtJmlAmbilPt').getValue())) {
				Ext.getCmp('6txtJmlAmbilPt').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o6txtJmlHariPt = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Jml Hari Transaksi',
	hideTrigger: true,
	id: '6txtJmlHariPt',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '6txtJmlHariPt',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('6txtJmlHariPt').getValue())) {
				Ext.getCmp('6txtJmlHariPt').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o6txtTglSurvei = {
	anchor: '55%',
	editable: true,
	fieldLabel: 'Tanggal',
	format: 'd-m-Y',
	id: '6txtTglSurvei',
	maskRe: /[0-9-]/,
	minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
	name: '6txtTglSurvei',
	value: new Date(),
	xtype: 'datefield'
};

var o6cboPetugas = {
	anchor: '98%',
	emptyText: "Pilih",
	fieldLabel: 'Petugas',
	fieldStyle: 'text-transform: uppercase',
	id: '6cboPetugas',
	name: '6cboPetugas',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
			Ext.getCmp('6txtPetugas').setValue('');
		}
	}
};

var o6txtPetugas = {
	anchor: '100%',
	emptyText: "Petugas",
	fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase;',
	id: '6txtPetugas',
	name: '6txtPetugas',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o6txtLamaSurvei = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '55%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Lama Survey',
	hideTrigger: true,
	id: '6txtLamaSurvei',
	keyNavEnabled: false,
	labelWidth: 70,
	mouseWheelEnabled: false,
	name: '6txtLamaSurvei',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('6txtLamaSurvei').getValue())) {
				Ext.getCmp('6txtLamaSurvei').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o6cboKeputusan = {
	anchor: '100%',
	emptyText: "Pilih Keputusan",
	fieldLabel: 'Keputusan',
	fieldStyle: 'text-transform: uppercase',
	id: '6cboKeputusan',
	name: '6cboKeputusan',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o6txtTglKeputusan = {
	anchor: '60%',
	editable: true,
	fieldLabel: 'Tanggal',
	format: 'd-m-Y',
	id: '6txtTglKeputusan',
	maskRe: /[0-9-]/,
	minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
	name: '6txtTglKeputusan',
	value: new Date(),
	xtype: 'datefield'
};

var o6txtSkor = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '60%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Score',
	fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
	hideTrigger: true,
	id: '6txtSkor',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '6txtSkor',
	readOnly: true,
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('6txtSkor').getValue())) {
				Ext.getCmp('6txtSkor').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o6txtGrade = {
	anchor: '60%',
	emptyText: "Grade",
	fieldLabel: 'Grade',
	fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase;',
	id: '6txtGrade',
	name: '6txtGrade',
	readOnly: true,
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o6txtKet = {
	anchor: '100%',
	emptyText: "Keterangan",
	fieldLabel: 'Keterangan',
	fieldStyle: 'text-transform: uppercase;',
	id: '6txtKet',
	name: '6txtKet',
	rows: 1,
	xtype: 'textarea',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o6txtNama = {
	anchor: '100%',
	emptyText: "Nama",
	fieldLabel: 'Nama',
	fieldStyle: 'text-transform: uppercase;',
	id: '6txtNama',
	name: '6txtNama',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o6txtAlamat = {
	anchor: '100%',
	emptyText: "Alamat",
	fieldLabel: 'Alamat',
	fieldStyle: 'text-transform: uppercase;',
	id: '6txtAlamat',
	name: '6txtAlamat',
	rows: 1,
	xtype: 'textarea',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o6txtKota = {
	anchor: '100%',
	emptyText: "Kota",
	fieldLabel: 'Kota',
	fieldStyle: 'text-transform: uppercase;',
	id: '6txtKota',
	name: '6txtKota',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o6txtJumlah = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: false,
	anchor: '98%',
	currencySymbol: null,
	decimalPrecision: 0,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: 'Jumlah',
	hideTrigger: true,
	id: '6txtJumlah',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '6txtJumlah',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('6txtJumlah').getValue())) {
				Ext.getCmp('6txtJumlah').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o6txtJumlah2 = Ext.create('Ext.ux.form.NumericField', {
	alwaysDisplayDecimals: true,
	anchor: '100%',
	currencySymbol: null,
	decimalPrecision: 2,
	decimalSeparator: '.',
	editable: true,
	fieldLabel: '',
	hideTrigger: true,
	id: '6txtJumlah2',
	keyNavEnabled: false,
	mouseWheelEnabled: false,
	name: '6txtJumlah2',
	thousandSeparator: ',',
	useThousandSeparator: true,
	value: 0,
	listeners: {
		change: function(value) {
			if (Ext.isEmpty(Ext.getCmp('6txtJumlah2').getValue())) {
				Ext.getCmp('6txtJumlah2').setValue(0);
			}
			else {
				return value;
			}
		}
	}
});

var o6txtAnalisa = {
	anchor: '100%',
	emptyText: "Hasil Analisa Credit Analyst",
	fieldStyle: 'text-transform: uppercase;',
	id: '6txtAnalisa',
	name: '6txtAnalisa',
	rows: 8,
	xtype: 'textarea',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};
/* Eof Survey	*/

/* Pencairan	*/
var o7txtPJJ = {
	anchor: '100%',
	emptyText: "No. PJJ",
	fieldLabel: 'No. PJJ',
	fieldStyle: 'text-transform: uppercase;',
	id: '7txtPJJ',
	maskRe: /[0-9]/,
	name: '7txtPJJ',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o7txtTgl = {
	anchor: '100%',
	editable: true,
	fieldLabel: 'Tanggal',
	format: 'd-m-Y',
	id: '7txtTgl',
	maskRe: /[0-9-]/,
	minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
	name: '7txtTgl',
	value: new Date(),
	xtype: 'datefield'
};

var o7txtTglAngs = {
	anchor: '100%',
	editable: true,
	fieldLabel: 'Tgl Angsuran I',
	format: 'd-m-Y',
	id: '7txtTglAngs',
	labelWidth: 90,
	maskRe: /[0-9-]/,
	minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
	name: '7txtTglAngs',
	value: new Date(),
	xtype: 'datefield'
};

var o7txtTglAngsBln = {
	anchor: '100%',
	emptyText: "",
	fieldLabel: 'Tgl Angs./Bln',
	fieldStyle: 'text-transform: uppercase;',
	id: '7txtTglAngsBln',
	maskRe: /[0-9]/,
	labelWidth: 80,
	name: '7txtTglAngsBln',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o7cboCair = {
	anchor: '100%',
	emptyText: "Pilih Pencairan Ke",
	fieldLabel: 'Pencairan Ke',
	fieldStyle: 'text-transform: uppercase',
	id: '7cboCair',
	name: '7cboCair',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o7cboUM = {
	anchor: '100%',
	emptyText: "Pilih Uang Muka Bayar Di",
	fieldLabel: 'Uang Muka Bayar Di',
	fieldStyle: 'text-transform: uppercase',
	id: '7cboUM',
	labelWidth: 115,
	name: '7cboUM',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o7cboPotongCair = {
	anchor: '100%',
	emptyText: "Pilih Deposit Potong Pencairan",
	fieldLabel: 'Deposit Potong Pencairan',
	fieldStyle: 'text-transform: uppercase',
	id: '7cboPotongCair',
	labelWidth: 150,
	name: '7cboPotongCair',
	xtype: 'combobox',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o7txtAN = {
	anchor: '100%',
	emptyText: "Atas Nama",
	fieldLabel: 'A/N',
	fieldStyle: 'text-transform: uppercase;',
	id: '7txtAN',
	labelWidth: 30,
	name: '7txtAN',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o7txtBank = {
	anchor: '100%',
	emptyText: "Bank",
	fieldLabel: 'Bank',
	fieldStyle: 'text-transform: uppercase;',
	id: '7txtBank',
	labelWidth: 40,
	name: '7txtBank',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};

var o7txtAC = {
	anchor: '100%',
	emptyText: "No. AC",
	fieldLabel: 'AC',
	fieldStyle: 'text-transform: uppercase;',
	id: '7txtAC',
	labelWidth: 30,
	name: '7txtAC',
	xtype: 'textfield',
	listeners: {
		change: function(field, newValue) {
			field.setValue(newValue.toUpperCase());
		}
	}
};
/* Eof Pencairan	*/

function fnReset() {
	// Ext.getCmp('tabapk').setActiveTab(0);
	
	Ext.getCmp('0cboRefno').setValue('');
	Ext.getCmp('0txtTgl').setValue(new Date());
	Ext.getCmp('0cboJenis').setValue('');
	Ext.getCmp('0txtJenis').setValue('');
	Ext.getCmp('0cboJenisPiutang').setValue('');
	Ext.getCmp('0txtJenisPiutang').setValue('');
	Ext.getCmp('0cboPolaTrs').setValue('');
	Ext.getCmp('0txtPolaTrs').setValue('');
	Ext.getCmp('0cboJenisPaket').setValue('');
	Ext.getCmp('0txtJenisPaket').setValue('');
	Ext.getCmp('0txtNama').setValue('');
	Ext.getCmp('0txtKtp').setValue('');
	Ext.getCmp('0txtTglKtp').setValue(new Date());
	Ext.getCmp('0txtTlp').setValue('');
	Ext.getCmp('0txtHp').setValue('');
	Ext.getCmp('0txtAlamat').setValue('');
	Ext.getCmp('0txtKel').setValue('');
	Ext.getCmp('0txtKec').setValue('');
	Ext.getCmp('0cboKab').setValue('');
	Ext.getCmp('0txtKab').setValue('');
	Ext.getCmp('0txtKota').setValue('');
	Ext.getCmp('0txtKodePos').setValue('');
	Ext.getCmp('0txtNpwp').setValue('');
	Ext.getCmp('0cboSpt').setValue('0');
	
	Ext.getCmp('1txtNamaPt').setValue('');
	Ext.getCmp('1txtTlpPt').setValue('');
	Ext.getCmp('1txtAlamatPt').setValue('');
	Ext.getCmp('1cboKategoriUsaha').setValue('');
	Ext.getCmp('1txtKategoriUsaha').setValue('');
	Ext.getCmp('1cboKategoriKtr').setValue('1');
	Ext.getCmp('1cboKondisiKtr').setValue('1');
	Ext.getCmp('1cboKerja').setValue('');
	Ext.getCmp('1txtKerja').setValue('');
	Ext.getCmp('1txtKetKerja').setValue('');
	Ext.getCmp('1txtBlnKerja').setValue(Ext.Date.format(new Date(), 'm-Y'));
	Ext.getCmp('1txtGaji').setValue('0');
	
	Ext.getCmp('1txtTempatLahir').setValue('');
	Ext.getCmp('1txtTglLahir').setValue(new Date());
	Ext.getCmp('1cboJK').setValue('0');
	Ext.getCmp('1txtNamaIbu').setValue('');
	Ext.getCmp('1cboAgama').setValue('01');
	Ext.getCmp('1cboPendidikan').setValue('');
	Ext.getCmp('1txtPendidikan').setValue('');
	Ext.getCmp('1cboStatusKawin').setValue('0');
	Ext.getCmp('1txtTanggungan').setValue('0');
	Ext.getCmp('1cboStatusRmh').setValue('');
	Ext.getCmp('1txtStatusRmh').setValue('');
	Ext.getCmp('1txtBlnTinggal').setValue(Ext.Date.format(new Date(), 'm-Y'));
	Ext.getCmp('1txtBiaya').setValue('0');
	Ext.getCmp('1txtAlamatSurat').setValue('');
	Ext.getCmp('1txtKota').setValue('');
	Ext.getCmp('1txtKodePos').setValue('');
	Ext.getCmp('1cboGarasi').setValue('');
	Ext.getCmp('1txtKend').setValue('0');
	Ext.getCmp('1cboKaliKredit').setValue('2');
	Ext.getCmp('1txtKreditKe').setValue('0');
	Ext.getCmp('1cboKondisiLingk').setValue('1');
	
	Ext.getCmp('2cboJenisKend').setValue('');
	Ext.getCmp('2txtJenisKend').setValue('');
	Ext.getCmp('2txtRangka').setValue('');
	Ext.getCmp('2txtMesin').setValue('');
	Ext.getCmp('2txtSilinder').setValue('');
	Ext.getCmp('2txtThn').setValue(Ext.Date.format(new Date(), 'Y'));
	Ext.getCmp('2txtWarna').setValue('');
	Ext.getCmp('2cboKomersial').setValue('0');
	
	Ext.getCmp('2cboNamaBpkb').setValue('0');
	Ext.getCmp('2txtBpkb').setValue('');
	Ext.getCmp('2txtNopol').setValue('');
	Ext.getCmp('2txtNamaBpkb').setValue('');
	Ext.getCmp('2txtAlamatBpkb').setValue('');
	Ext.getCmp('2cboKota').setValue('');
	Ext.getCmp('2txtKota').setValue('');
	
	Ext.getCmp('2cboLembagaKeu').setValue('');
	Ext.getCmp('2txtLembagaKeu').setValue('');
	Ext.getCmp('2cboJenisAss').setValue('0');
	Ext.getCmp('2cboSupp').setValue('');
	Ext.getCmp('2txtSupp').setValue('');
	Ext.getCmp('2cboCab').setValue('');
	Ext.getCmp('2txtCab').setValue('');
	Ext.getCmp('2txtSales').setValue('');
	
	Ext.getCmp('3cboPola').setValue('0');
	Ext.getCmp('3cboCaraBayar').setValue('0');
	Ext.getCmp('3txtTdkAng').setValue('0');
	Ext.getCmp('3cboDiMuka').setValue('0');
	Ext.getCmp('3cboKali').setValue('0');
	Ext.getCmp('3cboPotongDana').setValue('0');
	Ext.getCmp('3cboAngsByr').setValue('0');
	Ext.getCmp('3txtBiaya').setValue('0');
	Ext.getCmp('3txtGros').setValue('0');
	Ext.getCmp('3txtNetto').setValue('0');
	Ext.getCmp('3txtDenda').setValue('0');
	
	Ext.getCmp('3txtOTRD').setValue('0');
	Ext.getCmp('3txtOTRK').setValue('0');
	Ext.getCmp('3txtUMPersenD').setValue('0');
	Ext.getCmp('3txtUMPersenK').setValue('0');
	Ext.getCmp('3txtUMD').setValue('0');
	Ext.getCmp('3txtUMK').setValue('0');
	Ext.getCmp('3txtAssD').setValue('0');
	Ext.getCmp('3txtAssK').setValue('0');
	Ext.getCmp('3txtJumlahD').setValue('0');
	Ext.getCmp('3txtJumlahK').setValue('0');
	Ext.getCmp('3txtBungaFD').setValue('0');
	Ext.getCmp('3txtBungaFK').setValue('0');
	Ext.getCmp('3txtBungaED').setValue('0');
	Ext.getCmp('3txtBungaEK').setValue('0');
	Ext.getCmp('3txtMasaBlnD').setValue('0');
	Ext.getCmp('3txtMasaBlnK').setValue('0');
	Ext.getCmp('3txtMasaKaliD').setValue('0');
	Ext.getCmp('3txtMasaKaliK').setValue('0');
	Ext.getCmp('3txtBungaD').setValue('0');
	Ext.getCmp('3txtBungaK').setValue('0');
	Ext.getCmp('3txtNilaiKontrakD').setValue('0');
	Ext.getCmp('3txtNilaiKontrakK').setValue('0');
	Ext.getCmp('3txtAngsD').setValue('0');
	Ext.getCmp('3txtAngsK').setValue('0');
	Ext.getCmp('3txtAngsTD').setValue('0');
	Ext.getCmp('3txtAngsTK').setValue('0');
	
	grupGridTrs.load();
	grupGridAngs.load();
	
	Ext.getCmp('5txtNama').setValue('');
	Ext.getCmp('5cboKerja').setValue('');
	Ext.getCmp('5txtKerja').setValue('');
	Ext.getCmp('5txtKetKerja').setValue('');
	Ext.getCmp('5txtAlamatPt').setValue('');
	Ext.getCmp('5txtTlpPt').setValue('');
	Ext.getCmp('5txtGaji').setValue('0');
	
	Ext.getCmp('5txtNama2').setValue('');
	Ext.getCmp('5txtAlamat2').setValue('');
	Ext.getCmp('5txtKota2').setValue('');
	Ext.getCmp('5txtKodePos2').setValue('');
	Ext.getCmp('5txtTlp2').setValue('');
	Ext.getCmp('5cboKerja2').setValue('');
	Ext.getCmp('5txtKerja2').setValue('');
	Ext.getCmp('5txtKetKerja2').setValue('');
	Ext.getCmp('5txtAlamatPt2').setValue('');
	Ext.getCmp('5txtTlpPt2').setValue('');
	Ext.getCmp('5txtGaji2').setValue('0');
	Ext.getCmp('5txtStatus2').setValue('');
	
	Ext.getCmp('6txtSaldoRata').setValue('0');
	Ext.getCmp('6txtSaldoAwal').setValue('0');
	Ext.getCmp('6txtSaldoAkhir').setValue('0');
	Ext.getCmp('6txtJmlMasuk').setValue('0');
	Ext.getCmp('6txtJmlAmbil').setValue('0');
	Ext.getCmp('6txtJmlHari').setValue('0');
	
	Ext.getCmp('6txtSaldoRataPt').setValue('0');
	Ext.getCmp('6txtSaldoAwalPt').setValue('0');
	Ext.getCmp('6txtSaldoAkhirPt').setValue('0');
	Ext.getCmp('6txtJmlMasukPt').setValue('0');
	Ext.getCmp('6txtJmlAmbilPt').setValue('0');
	Ext.getCmp('6txtJmlHariPt').setValue('0');
	
	Ext.getCmp('6txtTglSurvei').setValue(new Date());
	Ext.getCmp('6cboPetugas').setValue('');
	Ext.getCmp('6txtPetugas').setValue('');
	Ext.getCmp('6txtLamaSurvei').setValue('0');
	
	Ext.getCmp('6cboKeputusan').setValue('');
	Ext.getCmp('6txtTglKeputusan').setValue(new Date());
	Ext.getCmp('6txtSkor').setValue('0');
	Ext.getCmp('6txtGrade').setValue('');
	Ext.getCmp('6txtKet').setValue('');
	
	Ext.getCmp('6txtNama').setValue('');
	Ext.getCmp('6txtAlamat').setValue('');
	Ext.getCmp('6txtKota').setValue('');
	Ext.getCmp('6txtJumlah').setValue('0');
	Ext.getCmp('6txtJumlah2').setValue('0');
	Ext.getCmp('6txtAnalisa').setValue('');
	
	Ext.getCmp('7txtPJJ').setValue('');
	Ext.getCmp('7txtTgl').setValue(new Date());
	Ext.getCmp('7txtTglAngs').setValue(new Date());
	Ext.getCmp('7txtTglAngsBln').setValue('');
	
	Ext.getCmp('7cboCair').setValue('');
	Ext.getCmp('7cboUM').setValue('');
	Ext.getCmp('7cboPotongCair').setValue('');
	Ext.getCmp('7txtAN').setValue('');
	Ext.getCmp('7txtBank').setValue('');
	Ext.getCmp('7txtAC').setValue('');
	
}

function fnCekSave() {
	if (this.up('form').getForm().isValid()) {
		
		if (Ext.getCmp('0cboJenis').getValue() !== '' && Ext.getCmp('0txtJenis').getValue() === '') {
			Ext.Msg.show({
				buttons: Ext.MessageBox.OK,
				icon: Ext.Msg.INFO,
				msg: 'Jenis kosong tidak ada dalam daftar!!',
				title: 'ASTER'
			});
			return;
		}
		
		if (Ext.getCmp('0cboJenisPiutang').getValue() !== '' && Ext.getCmp('0txtJenisPiutang').getValue() === '') {
			Ext.Msg.show({
				buttons: Ext.MessageBox.OK,
				icon: Ext.Msg.INFO,
				msg: 'Jenis Piutang tidak ada dalam daftar!!',
				title: 'ASTER'
			});
			return;
		}
		
		if (Ext.getCmp('0cboPolaTrs').getValue() !== '' && Ext.getCmp('0txtPolaTrs').getValue() === '') {
			Ext.Msg.show({
				buttons: Ext.MessageBox.OK,
				icon: Ext.Msg.INFO,
				msg: 'Pola transaksi tidak ada dalam daftar!!',
				title: 'ASTER'
			});
			return;
		}
		
		if (Ext.getCmp('0cboJenisPaket').getValue() !== '' && Ext.getCmp('0txtJenisPaket').getValue() === '') {
			Ext.Msg.show({
				buttons: Ext.MessageBox.OK,
				icon: Ext.Msg.INFO,
				msg: 'Jenis Paket tidak ada dalam daftar!!',
				title: 'ASTER'
			});
			return;
		}
		
		if (Ext.getCmp('0cboKab').getValue() !== '' && Ext.getCmp('0txtKab').getValue() === '') {
			Ext.Msg.show({
				buttons: Ext.MessageBox.OK,
				icon: Ext.Msg.INFO,
				msg: 'Kabupaten tidak ada dalam daftar!!',
				title: 'ASTER'
			});
			return;
		}
		
		if (Ext.getCmp('1cboKategoriUsaha').getValue() !== '' && Ext.getCmp('1txtKategoriUsaha').getValue() === '') {
			Ext.Msg.show({
				buttons: Ext.MessageBox.OK,
				icon: Ext.Msg.INFO,
				msg: 'Kategori Usaha tidak ada dalam daftar!!',
				title: 'ASTER'
			});
			return;
		}
		
		if (Ext.getCmp('1cboKerja').getValue() !== '' && Ext.getCmp('1txtKerja').getValue() === '') {
			Ext.Msg.show({
				buttons: Ext.MessageBox.OK,
				icon: Ext.Msg.INFO,
				msg: 'Usaha / Pekerjaan tidak ada dalam daftar!!',
				title: 'ASTER'
			});
			return;
		}
		
		if (Ext.getCmp('1cboPendidikan').getValue() !== '' && Ext.getCmp('1txtPendidikan').getValue() === '') {
			Ext.Msg.show({
				buttons: Ext.MessageBox.OK,
				icon: Ext.Msg.INFO,
				msg: 'Pendidikan tidak ada dalam daftar!!',
				title: 'ASTER'
			});
			return;
		}
		
		if (Ext.getCmp('1cboStatusRmh').getValue() !== '' && Ext.getCmp('1txtStatusRmh').getValue() === '') {
			Ext.Msg.show({
				buttons: Ext.MessageBox.OK,
				icon: Ext.Msg.INFO,
				msg: 'Status Rumah tidak ada dalam daftar!!',
				title: 'ASTER'
			});
			return;
		}
		
		if (Ext.getCmp('2cboJenisKend').getValue() !== '' && Ext.getCmp('2txtJenisKend').getValue() === '') {
			Ext.Msg.show({
				buttons: Ext.MessageBox.OK,
				icon: Ext.Msg.INFO,
				msg: 'Jenis Kendaraan tidak ada dalam daftar!!',
				title: 'ASTER'
			});
			return;
		}
		
		if (Ext.getCmp('2cboKota').getValue() !== '' && Ext.getCmp('2txtKota').getValue() === '') {
			Ext.Msg.show({
				buttons: Ext.MessageBox.OK,
				icon: Ext.Msg.INFO,
				msg: 'Kota di BPKB tidak ada dalam daftar!!',
				title: 'ASTER'
			});
			return;
		}
		
		if (Ext.getCmp('2cboLembagaKeu').getValue() !== '' && Ext.getCmp('2txtLembagaKeu').getValue() === '') {
			Ext.Msg.show({
				buttons: Ext.MessageBox.OK,
				icon: Ext.Msg.INFO,
				msg: 'Lembaga Keuangan tidak ada dalam daftar!!',
				title: 'ASTER'
			});
			return;
		}
		
		if (Ext.getCmp('2cboSupp').getValue() !== '' && Ext.getCmp('2txtSupp').getValue() === '') {
			Ext.Msg.show({
				buttons: Ext.MessageBox.OK,
				icon: Ext.Msg.INFO,
				msg: 'Supplier tidak ada dalam daftar!!',
				title: 'ASTER'
			});
			return;
		}
		
		if (Ext.getCmp('2cboCab').getValue() !== '' && Ext.getCmp('2txtCab').getValue() === '') {
			Ext.Msg.show({
				buttons: Ext.MessageBox.OK,
				icon: Ext.Msg.INFO,
				msg: 'Cabang tidak ada dalam daftar!!',
				title: 'ASTER'
			});
			return;
		}
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'apkorang/ceksave',
			params: {
				'fs_refno': Ext.getCmp('0cboRefno').getValue()
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				
				if (xtext.sukses === false) {
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						icon: Ext.Msg.INFO,
						msg: xtext.hasil,
						title: 'ASTER'
					});
				}
				else {
					if (xtext.sukses === true && xtext.hasil == 'lanjut') {
						fnSave();
					}
					else {
						Ext.Msg.show({
							buttons: Ext.MessageBox.YESNO,
							icon: Ext.Msg.QUESTION,
							msg: xtext.hasil,
							title: 'ASTER',
							fn: function(btn) {
								if (btn == 'yes') {
									fnSave();
								}
							}
						});
					}
				}
			},
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					icon: Ext.Msg.INFO,
					msg: 'Saving Failed, Connection Failed',
					title: 'ASTER'
				});
			}
		});
	}
}

function fnSave() {
	Ext.Ajax.on('beforerequest', vMask.show, vMask);
	Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
	Ext.Ajax.on('requestexception', vMask.hide, vMask);
	
	Ext.Ajax.request({
		method: 'POST',
		url: 'apkorang/save',
		params: {
			'fs_refno': Ext.getCmp('0cboRefno').getValue(),
			'fd_refno': Ext.Date.format(Ext.getCmp('0txtTgl').getValue(), 'Ymd'),
			'fs_kd_jns_konsumen': Ext.getCmp('0cboJenis').getValue(),
			'fs_kd_jns_piutang': Ext.getCmp('0cboJenisPiutang').getValue(),
			'fs_kd_pola_trs': Ext.getCmp('0cboPolaTrs').getValue(),
			'fs_kd_jns_paket': Ext.getCmp('0cboJenisPaket').getValue(),
			'fs_nm_konsumen': Ext.getCmp('0txtNama').getValue(),
			'fs_no_ktp': Ext.getCmp('0txtKtp').getValue(),
			'fd_ktp': Ext.Date.format(Ext.getCmp('0txtTglKtp').getValue(), 'Ymd'),
			'fs_no_telp': Ext.getCmp('0txtTlp').getValue(),
			'fs_no_hp': Ext.getCmp('0txtHp').getValue(),
			'fs_alamat': Ext.getCmp('0txtAlamat').getValue(),
			'fs_kelurahan': Ext.getCmp('0txtKel').getValue(),
			'fs_kecamatan': Ext.getCmp('0txtKec').getValue(),
			'fs_kd_kabupaten': Ext.getCmp('0cboKab').getValue(),
			'fs_kota': Ext.getCmp('0txtKota').getValue(),
			'fs_kd_pos': Ext.getCmp('0txtKodePos').getValue(),
			'fs_no_npwp': Ext.getCmp('0txtNpwp').getValue(),
			'fb_status_spt': Ext.getCmp('0cboSpt').getValue(),
			
			'fs_nm_pt': Ext.getCmp('1txtNamaPt').getValue(),
			'fs_alamat_pt': Ext.getCmp('1txtAlamatPt').getValue(),
			'fs_tlp_pt': Ext.getCmp('1txtTlpPt').getValue(),
			'fs_kd_kat_usaha': Ext.getCmp('1cboKategoriUsaha').getValue(),
			'fn_kategori_pt': Ext.getCmp('1cboKategoriKtr').getValue(),
			'fn_kondisi_ktr': Ext.getCmp('1cboKondisiKtr').getValue(),
			'fs_kd_usaha': Ext.getCmp('1cboKerja').getValue(),
			'fs_ket_usaha': Ext.getCmp('1txtKetKerja').getValue(),
			'fd_kerja': Ext.Date.format(Ext.getCmp('1txtBlnKerja').getValue(), 'Ym'),
			'fn_pendapatan': Ext.getCmp('1txtGaji').getValue(),
			'fs_tempat_lahir': Ext.getCmp('1txtTempatLahir').getValue(),
			'fd_tgl_lahir': Ext.Date.format(Ext.getCmp('1txtTglLahir').getValue(), 'Ymd'),
			'fn_jns_kelamin': Ext.getCmp('1cboJK').getValue(),
			'fs_kd_agama': Ext.getCmp('1cboAgama').getValue(),
			'fs_nm_ibu': Ext.getCmp('1txtNamaIbu').getValue(),
			'fs_kd_pendidikan': Ext.getCmp('1cboPendidikan').getValue(),
			'fn_status_kawin': Ext.getCmp('1cboStatusKawin').getValue(),
			'fn_tanggungan': Ext.getCmp('1txtTanggungan').getValue(),
			'fs_status_rumah': Ext.getCmp('1cboStatusRmh').getValue(),
			'fd_tinggal': Ext.Date.format(Ext.getCmp('1txtBlnTinggal').getValue(), 'Ym'),
			'fn_biaya_bulan': Ext.getCmp('1txtBiaya').getValue(),
			'fs_alamat_surat': Ext.getCmp('1txtAlamatSurat').getValue(),
			'fs_kotakons': Ext.getCmp('1txtKota').getValue(),
			'fs_kd_poskons': Ext.getCmp('1txtKodePos').getValue(),
			'fb_garasi_rumah': Ext.getCmp('1cboGarasi').getValue(),
			'fn_jml_kendaraan': Ext.getCmp('1txtKend').getValue(),
			'fn_kali_kredit': Ext.getCmp('1cboKaliKredit').getValue(),
			'fn_kredit_ke': Ext.getCmp('1txtKreditKe').getValue(),
			'fn_kondisi_lingk': Ext.getCmp('1cboKondisiLingk').getValue(),
			
			'fs_kd_jns_kend': Ext.getCmp('2cboJenisKend').getValue(),
			'fs_rangka': Ext.getCmp('2txtRangka').getValue(),
			'fs_mesin': Ext.getCmp('2txtMesin').getValue(),
			'fn_silinder': Ext.getCmp('2txtSilinder').getValue(),
			'fn_tahun': Ext.getCmp('2txtThn').getValue(),
			'fs_warna': Ext.getCmp('2txtWarna').getValue(),
			'fb_komersial': Ext.getCmp('2cboKomersial').getValue(),
			'fb_nm_bpkb': Ext.getCmp('2cboNamaBpkb').getValue(),
			'fs_no_bpkb': Ext.getCmp('2txtBpkb').getValue(),
			'fs_nopol': Ext.getCmp('2txtNopol').getValue(),
			'fs_nm_bpkb': Ext.getCmp('2txtNamaBpkb').getValue(),
			'fs_alm_bpkb': Ext.getCmp('2txtAlamatBpkb').getValue(),
			'fs_kd_kota_bpkb': Ext.getCmp('2cboKota').getValue(),
			'fs_kd_lbg_keu': Ext.getCmp('2cboLembagaKeu').getValue(),
			'fs_kd_jns_ass': Ext.getCmp('2cboJenisAss').getValue(),
			'fs_kd_supplier': Ext.getCmp('2cboSupp').getValue(),
			'fs_kd_cabang': Ext.getCmp('2cboCab').getValue(),
			'fs_sales': Ext.getCmp('2txtSales').getValue()
		},
		success: function(response, opts) {
			var xtext = Ext.decode(response.responseText);
			
			if (xtext.sukses === true) {
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					icon: Ext.Msg.INFO,
					msg: xtext.hasil,
					title: 'ASTER'
				});
				Ext.getCmp('0cboRefno').setValue(trim(xtext.refno));
			}
			
			if (xtext.sukses === false) {
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					icon: Ext.Msg.INFO,
					msg: xtext.hasil,
					title: 'ASTER'
				});
			}
		},
		failure: function(response, opts) {
			var xtext = Ext.decode(response.responseText);
			Ext.Msg.show({
				buttons: Ext.MessageBox.OK,
				icon: Ext.Msg.INFO,
				msg: 'Saving Failed, Connection Failed',
				title: 'ASTER'
			});
		}
	});
}

var frmApkOrang = Ext.create('Ext.form.Panel', {
	border: false,
	frame: true,
	region: 'center',
	title: 'Form Aplikasi Pembiayaan Konsumen Perorangan / Wiraswasta',
	width: 1000,
	items: [{
		activeTab: 3,
		bodyStyle: 'padding: 5px;',
		id: 'tabapk',
		name: 'tabapk',
		plain: true,
		xtype: 'tabpanel',
		items: [{
			border: false,
			frame: true,
			title: 'APK',
			xtype: 'form',
			items: [{
				fieldDefaults: {
					labelAlign: 'right',
					labelSeparator: '',
					labelWidth: 80,
					msgTarget: 'side'
				},
				style: 'padding: 5px;',
				xtype: 'fieldset',
				items: [{
					anchor: '100%',
					layout: 'hbox',
					xtype: 'container',
					items: [{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [
							o0cboRefno,
							o0txtTgl,
						{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o0cboJenis,
									o0cboJenisPiutang,
									o0cboPolaTrs,
									o0cboJenisPaket
								]
							},{
								flex: 1.5,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o0txtJenis,
									o0txtJenisPiutang,
									o0txtPolaTrs,
									o0txtJenisPaket
								]
							}]
						}]
					},{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [
							o0txtNama,
						{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 2.7,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o0txtKtp
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o0txtTglKtp
								]
							}]
						},
							o0txtTlp,
							o0txtHp,
							o0txtAlamat,
							o0txtKel,
							o0txtKec,
						{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o0cboKab
								]
							},{
								flex: 2,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o0txtKab
								]
							}]
						},
							o0txtKota,
							o0txtKodePos,
						{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 2,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o0txtNpwp
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o0cboSpt
								]
							}]
						}]
					}]
				}]
			}]
		},{
			border: false,
			frame: true,
			title: 'Konsumen',
			xtype: 'form',
			items: [{
				fieldDefaults: {
					labelAlign: 'right',
					labelSeparator: '',
					labelWidth: 50,
					msgTarget: 'side'
				},
				style: 'padding: 5px;',
				title: 'Data Usaha',
				xtype: 'fieldset',
				items: [{
					anchor: '100%',
					layout: 'hbox',
					xtype: 'container',
					items: [{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [
							o1txtNamaPt,
							o1txtAlamatPt,
							o1txtTlpPt,
						{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1cboKategoriUsaha
								]
							},{
								flex: 2,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1txtKategoriUsaha
								]
							}]
						}]
					},{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1.1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1cboKategoriKtr
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1cboKondisiKtr
								]
							}]
						},{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1cboKerja
								]
							},{
								flex: 1.5,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1txtKerja
								]
							}]
						},
							o1txtKetKerja,
						{
							anchor: '80%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1txtBlnKerja
								]
							},{
								flex: 1.1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1txtGaji
								]
							}]
						}]
					}]
				}]
			},{
				fieldDefaults: {
					labelAlign: 'right',
					labelSeparator: '',
					labelWidth: 105,
					msgTarget: 'side'
				},
				style: 'padding: 5px;',
				title: 'Korespondensi',
				xtype: 'fieldset',
				items: [{
					anchor: '100%',
					layout: 'hbox',
					xtype: 'container',
					items: [{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 3.4,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1txtTempatLahir
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1txtTglLahir
								]
							}]
						},{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1.2,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1cboJK
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1cboAgama
								]
							}]
						},
							o1txtNamaIbu,
						{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1cboPendidikan
								]
							},{
								flex: 1.4,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1txtPendidikan
								]
							}]
						},{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 2.5,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1cboStatusKawin
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1txtTanggungan
								]
							}]
						},{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1cboStatusRmh
								]
							},{
								flex: 1.4,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1txtStatusRmh
								]
							}]
						}]
					},{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							anchor: '76%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1txtBlnTinggal
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1txtBiaya
								]
							}]
						},
							o1txtAlamatSurat,
						{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 2.5,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1txtKota
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1txtKodePos
								]
							}]
						},{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1.78,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1cboGarasi
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1txtKend
								]
							}]
						},{
							anchor: '80%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 5,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1cboKaliKredit
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o1txtKreditKe
								]
							}]
						}, o1cboKondisiLingk
						]
					}]
				}]
			}]
		},{
			border: false,
			frame: true,
			title: 'Kendaraan',
			xtype: 'form',
			items: [{
				fieldDefaults: {
					labelAlign: 'right',
					labelSeparator: '',
					labelWidth: 70,
					msgTarget: 'side'
				},
				style: 'padding: 5px;',
				title: 'Data Kendaraan',
				xtype: 'fieldset',
				items: [{
					anchor: '100%',
					layout: 'hbox',
					xtype: 'container',
					items: [{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [
							{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o2cboJenisKend
								]
							},{
								flex: 1.5,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o2txtJenisKend
								]
							}]
						},
							o2txtRangka,
							o2txtMesin
						]
					},{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							anchor: '55%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1.1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o2txtSilinder
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o2txtThn
								]
							}]
						},
							o2txtWarna,
							o2cboKomersial
						]
					}]
				}]
			},{
				fieldDefaults: {
					labelAlign: 'right',
					labelSeparator: '',
					labelWidth: 70,
					msgTarget: 'side'
				},
				style: 'padding: 5px;',
				title: 'Data BPKB',
				xtype: 'fieldset',
				items: [{
					anchor: '100%',
					layout: 'hbox',
					xtype: 'container',
					items: [{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [
							o2cboNamaBpkb,
							o2txtBpkb,
							o2txtNopol
						]
					},{
						flex: 1.5,
						layout: 'anchor',
						xtype: 'container',
						items: [
							o2txtNamaBpkb,
							o2txtAlamatBpkb,
						{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o2cboKota
								]
							},{
								flex: 1.5,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o2txtKota
								]
							}]
						}]
					}]
				}]
			},{
				fieldDefaults: {
					labelAlign: 'right',
					labelSeparator: '',
					labelWidth: 70,
					msgTarget: 'side'
				},
				style: 'padding: 5px;',
				title: '',
				xtype: 'fieldset',
				items: [{
					anchor: '100%',
					layout: 'hbox',
					xtype: 'container',
					items: [{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o2cboLembagaKeu
								]
							},{
								flex: 1.4,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o2txtLembagaKeu
								]
							}]
						},
							o2cboJenisAss
						]
					},{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o2cboSupp,
									o2cboCab
								]
							},{
								flex: 1.5,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o2txtSupp,
									o2txtCab
								]
							}]
						},
							o2txtSales
						]
					}]
				}]
			}]
		},{
			border: false,
			frame: true,
			title: 'Keuangan',
			xtype: 'form',
			items: [{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						fieldDefaults: {
							labelAlign: 'right',
							labelSeparator: '',
							labelWidth: 80,
							msgTarget: 'side'
						},
						style: 'padding: 5px;',
						title: '',
						xtype: 'fieldset',
						items: [
							o3cboPola,
							o3cboCaraBayar,
							o3txtTdkAng
						]
					},{
						fieldDefaults: {
							labelAlign: 'right',
							labelSeparator: '',
							labelWidth: 45,
							msgTarget: 'side'
						},
						style: 'padding: 5px;',
						title: 'Pembayaran Angsuran I',
						xtype: 'fieldset',
						items: [{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o3cboDiMuka
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o3cboKali
								]
							}]
						},
							o3cboPotongDana
						]
					},{
						fieldDefaults: {
							labelAlign: 'right',
							labelSeparator: '',
							labelWidth: 110,
							msgTarget: 'side'
						},
						style: 'padding: 5px;',
						title: '',
						xtype: 'fieldset',
						items: [
							o3cboAngsByr,
							o3txtBiaya,
							o3txtGros,
							o3txtNetto,
							o3txtDenda
						]
					}]
				},{
					flex: 0.02,
					layout: 'anchor',
					xtype: 'container'
				},{
					flex: 2,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						fieldDefaults: {
							labelAlign: 'right',
							labelSeparator: '',
							labelWidth: 150,
							msgTarget: 'side'
						},
						style: 'padding: 5px;',
						title: 'Perhitungan Angsuran',
						xtype: 'fieldset',
						items: [{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1.7,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o3txtOTRD
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o3txtOTRK
								]
							}]
						},{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1.7,
								layout: 'anchor',
								xtype: 'container',
								items: [{
									anchor: '100%',
									layout: 'hbox',
									xtype: 'container',
									items: [{
										flex: 1.2,
										layout: 'anchor',
										xtype: 'container',
										items: [
											o3txtUMPersenD
										]
									},{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											o3txtUMD
										]
									}]
								}]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [{
									anchor: '100%',
									layout: 'hbox',
									xtype: 'container',
									items: [{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											o3txtUMPersenK
										]
									},{
										flex: 3,
										layout: 'anchor',
										xtype: 'container',
										items: [
											o3txtUMK
										]
									}]
								}]
							}]
						},{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1.7,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o3txtAssD,
									o3txtJumlahD
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o3txtAssK,
									o3txtJumlahK
								]
							}]
						},{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1.7,
								layout: 'anchor',
								xtype: 'container',
								items: [{
									anchor: '100%',
									layout: 'hbox',
									xtype: 'container',
									items: [{
										flex: 2.3,
										layout: 'anchor',
										xtype: 'container',
										items: [
											o3txtBungaFD,
											o3txtMasaBlnD
										]
									},{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											o3txtBungaED,
											o3txtMasaKaliD
										]
									}]
								}]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [{
									anchor: '100%',
									layout: 'hbox',
									xtype: 'container',
									items: [{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											o3txtBungaFK,
											o3txtMasaBlnK
										]
									},{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											o3txtBungaEK,
											o3txtMasaKaliK
										]
									}]
								}]
							}]
						},{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1.7,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o3txtBungaD,
									o3txtNilaiKontrakD,
									o3txtAngsD,
									o3txtAngsTD
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o3txtBungaK,
									o3txtNilaiKontrakK,
									o3txtAngsK,
									o3txtAngsTK
								]
							}]
						}
							
						]
					}]
				}]
			}]
		},{
			border: false,
			frame: true,
			title: 'Detil Trs & Data Angsuran',
			xtype: 'form',
			items: [{
				fieldDefaults: {
					labelAlign: 'right',
					labelSeparator: '',
					labelWidth: 90,
					msgTarget: 'side'
				},
				style: 'padding: 5px;',
				title: 'Detil Transaksi',
				xtype: 'fieldset',
				items: [
					gridTrs
				]
			},{
				fieldDefaults: {
					labelAlign: 'right',
					labelSeparator: '',
					labelWidth: 90,
					msgTarget: 'side'
				},
				style: 'padding: 5px;',
				title: 'Data Angsuran Notariil',
				xtype: 'fieldset',
				items: [
					gridAngs
				]
			}]
		},{
			border: false,
			frame: true,
			title: 'Tambahan',
			xtype: 'form',
			items: [{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1.25,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						fieldDefaults: {
							labelAlign: 'right',
							labelSeparator: '',
							labelWidth: 100,
							msgTarget: 'side'
						},
						style: 'padding: 5px;',
						title: 'Data Tambahan: SUAMI / ISTRI',
						xtype: 'fieldset',
						items: [
							o5txtNama,
							o5txtAlamat, {xtype: 'splitter'}, {xtype: 'splitter'}, {xtype: 'splitter'},
							o5txtKota,
							o5txtTlp,
						{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o5cboKerja
								]
							},{
								flex: 1.2,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o5txtKerja
								]
							}]
						},
							o5txtKetKerja,
							o5txtAlamatPt,
							o5txtTlpPt,
							o5txtGaji,
							o5txtStatus
						]
					}]
				},{
					flex: 0.02,
					layout: 'anchor',
					xtype: 'container',
					items: []
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						fieldDefaults: {
							labelAlign: 'right',
							labelSeparator: '',
							labelWidth: 90,
							msgTarget: 'side'
						},
						style: 'padding: 5px;',
						title: 'Data Tambahan: PENJAMIN',
						xtype: 'fieldset',
						items: [
							o5txtNama2,
							o5txtAlamat2,
						{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 2.3,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o5txtKota2
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o5txtKodePos2
								]
							}]
						},
							o5txtTlp2,
						{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o5cboKerja2
								]
							},{
								flex: 2,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o5txtKerja2
								]
							}]
						},
							o5txtKetKerja2,
							o5txtAlamatPt2,
							o5txtTlpPt2,
							o5txtGaji2,
							o5txtStatus2
						]
					}]
				}]
			}]
		},{
			border: false,
			frame: true,
			title: 'Survey Keputusan Kredit',
			xtype: 'form',
			items: [{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						fieldDefaults: {
							labelAlign: 'right',
							labelSeparator: '',
							labelWidth: 100,
							msgTarget: 'side'
						},
						style: 'padding: 5px;',
						title: 'Rekening Bank Pribadi',
						xtype: 'fieldset',
						items: [
							o6txtSaldoRata,
							o6txtSaldoAwal,
							o6txtSaldoAkhir,
							o6txtJmlMasuk,
							o6txtJmlAmbil,
							o6txtJmlHari
						]
					}]
				},{
					flex: 0.02,
					layout: 'anchor',
					xtype: 'container'
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						fieldDefaults: {
							labelAlign: 'right',
							labelSeparator: '',
							labelWidth: 100,
							msgTarget: 'side'
						},
						style: 'padding: 5px;',
						title: 'Rekening Bank Perusahaan',
						xtype: 'fieldset',
						items: [
							o6txtSaldoRataPt,
							o6txtSaldoAwalPt,
							o6txtSaldoAkhirPt,
							o6txtJmlMasukPt,
							o6txtJmlAmbilPt,
							o6txtJmlHariPt
						]
					}]
				},{
					flex: 0.02,
					layout: 'anchor',
					xtype: 'container'
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						fieldDefaults: {
							labelAlign: 'right',
							labelSeparator: '',
							labelWidth: 45,
							msgTarget: 'side'
						},
						style: 'padding: 5px;',
						title: 'Survey',
						xtype: 'fieldset',
						items: [
							o6txtTglSurvei,
						{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o6cboPetugas
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o6txtPetugas
								]
							}]
						},
							o6txtLamaSurvei
						]
					}]
				}]
			},{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						fieldDefaults: {
							labelAlign: 'right',
							labelSeparator: '',
							labelWidth: 60,
							msgTarget: 'side'
						},
						style: 'padding: 5px;',
						title: 'Hasil Keputusan Kredit',
						xtype: 'fieldset',
						items: [
							o6cboKeputusan,
							o6txtTglKeputusan,
							o6txtSkor,
							o6txtGrade,
							o6txtKet
						]
					}]
				},{
					flex: 0.02,
					layout: 'anchor',
					xtype: 'container'
				},{
					flex: 1.4,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						fieldDefaults: {
							labelAlign: 'right',
							labelSeparator: '',
							labelWidth: 40,
							msgTarget: 'side'
						},
						style: 'padding: 5px;',
						title: 'Negative File',
						xtype: 'fieldset',
						items: [
							o6txtNama,
							o6txtAlamat,
							o6txtKota,
						{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o6txtJumlah
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									o6txtJumlah2
								]
							}]
						}]
					}]
				},{
					flex: 0.02,
					layout: 'anchor',
					xtype: 'container'
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						fieldDefaults: {
							labelAlign: 'right',
							labelSeparator: '',
							labelWidth: 90,
							msgTarget: 'side'
						},
						style: 'padding: 5px;',
						title: 'Hasil Analisa Credit Analyst',
						xtype: 'fieldset',
						items: [
							o6txtAnalisa
						]
					}]
				}]
			}]
		},{
			border: false,
			frame: true,
			title: 'Pencairan',
			xtype: 'form',
			items: [{
				fieldDefaults: {
					labelAlign: 'right',
					labelSeparator: '',
					labelWidth: 50,
					msgTarget: 'side'
				},
				style: 'padding: 5px;',
				title: 'Data PJJ',
				xtype: 'fieldset',
				items: [{
					anchor: '70%',
					layout: 'hbox',
					xtype: 'container',
					items: [{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [
							o7txtPJJ
						]
					},{
						flex: 0.8,
						layout: 'anchor',
						xtype: 'container',
						items: [
							o7txtTgl
						]
					},{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [
							o7txtTglAngs
						]
					},{
						flex: 0.6,
						layout: 'anchor',
						xtype: 'container',
						items: [
							o7txtTglAngsBln
						]
					}]
				}]
			},{
				fieldDefaults: {
					labelAlign: 'right',
					labelSeparator: '',
					labelWidth: 70,
					msgTarget: 'side'
				},
				style: 'padding: 5px;',
				title: 'Data Pencairan',
				xtype: 'fieldset',
				items: [{
					anchor: '80%',
					layout: 'hbox',
					xtype: 'container',
					items: [{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [
							o7cboCair
						]
					},{
						flex: 1.1,
						layout: 'anchor',
						xtype: 'container',
						items: [
							o7cboUM
						]
					},{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [
							o7cboPotongCair
						]
					}]
				},{
					anchor: '100%',
					layout: 'hbox',
					xtype: 'container',
					items: [{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [
							o7txtAN
						]
					},{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [
							o7txtBank
						]
					},{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [
							o7txtAC
						]
					}]
				}]
			}]
		}]
	}],
	buttons: [{
		text: 'Save',
		handler: fnCekSave
	},{
		text: 'Reset',
		handler: fnReset
	}]
});

var vMask = new Ext.LoadMask(frmApkOrang, {
	msg: "Please wait..."
});

Ext.onReady(function() {
    Ext.QuickTips.init();
	Ext.util.Format.thousandSeparator = ',';
	Ext.util.Format.decimalSeparator = '.';
	
	frmApkOrang.render(Ext.getBody());
	Ext.get('loading').destroy();
});
