Ext.Loader.setConfig({
	enabled: true
});

Ext.Loader.setPath('Ext.ux', gBaseUX);

Ext.require([
	'Ext.ux.LiveSearchGridPanel',
	'Ext.ux.ProgressBarPager'
]);

Ext.onReady(function() {
    Ext.QuickTips.init();
	Ext.util.Format.thousandSeparator = ',';
	Ext.util.Format.decimalSeparator = '.';

	var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';

	function tglDMY(text) {
		var x = '-';
		return text.substr(6,2).concat(x,text.substr(4,2),x,text.substr(0,4));
	}

	function gridTooltipSearch(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Double click on record to choose',
			target: view.el,
			trackMouse: true
		});
	}

	function fnDetil() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'alamat/detil',
			params: {
				'fs_kd_tipe': Ext.getCmp('cboTipe').getValue(),
				'fs_code': Ext.getCmp('cboCode').getValue(),
				'fs_count': Ext.getCmp('cboCount').getValue()
			},
			success: function(response) {
				var xtext = Ext.decode(response.responseText);
				
				if (xtext.sukses === true) {
					var xkdtipe = Ext.getCmp('cboTipe').getValue();
					
					if (xkdtipe == '01') {
						Ext.getCmp('cboGL').setValue(xtext.kdacno);
						Ext.getCmp('txtGL').setValue(xtext.nmacno);
						Ext.getCmp('cekSupp').setValue(xtext.cek);
					}
					else if (xkdtipe == '02') {
						Ext.getCmp('cboTipeCust').setValue(xtext.kdtipe);
						Ext.getCmp('txtTipeCust').setValue(xtext.nmtipe);
						Ext.getCmp('cekCust').setValue(xtext.cek);
					}
				}
			},
			failure: function(response) {
				var xtext = Ext.decode(response.responseText);
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Generate Supplier Detail Failed, Connection Failed!!',
					title: 'IDS'
				});
				fnMaskHide();
			}
		});
	}

	function fnAmbilKode() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'alamat/ambilkode',
			params: {
				'fs_kd_tipe': Ext.getCmp('cboTipe').getValue()
			},
			success: function(response) {
				var xtext = Ext.decode(response.responseText);
				
				if (xtext.sukses === true) {
					Ext.getCmp('cboCode').setValue(xtext.txtcode);
					Ext.getCmp('cboCount').setValue(xtext.txtcount);
				}
			},
			failure: function(response) {
				var xtext = Ext.decode(response.responseText);
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Generate Code Failed, Connection Failed!!',
					title: 'IDS'
				});
				fnMaskHide();
			}
		});
	}

	var grupTipe = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_kd_tipe','fs_nm_tipe'],
		pageSize: 25,
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				rootProperty: 'hasil',
				totalProperty: 'total',
				type: 'json'
			},
			type: 'ajax',
			url: 'alamat/kodetipe'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_tipe': Ext.getCmp('cboTipe').getValue(),
					'fs_nm_tipe': Ext.getCmp('cboTipe').getValue(),
					'fs_cari': Ext.getCmp('txtCari').getValue()
				});
			}
		}
	});

	var winGrid = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupTipe,
		columns: [	
			{xtype: 'rownumberer', width: 45},
			{text: "Type Code", dataIndex: 'fs_kd_tipe', menuDisabled: true, width: 100},
			{text: "Type Name", dataIndex: 'fs_nm_tipe', menuDisabled: true, width: 380}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Type Code / Type Name',
				id: 'txtCari',
				name: 'txtCari',
				xtype: 'textfield'
			}]
		},{
			flex: 0.2,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '100%',
				text: 'Search',
				xtype: 'button',
				handler: function() {
					grupTipe.load();
				}
			}]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: []
		}],
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupTipe,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboTipe').setValue(record.get('fs_kd_tipe'));
				Ext.getCmp('txtTipe').setValue(record.get('fs_nm_tipe'));
				
				if (Ext.getCmp('txtTipe').getValue() !== '') {
					fnAmbilKode();
				}
				winCari.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});

	var winCari = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupTipe.load();
				vMask.show();
			}
		}
	});

	var cboTipe = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: "Select a",
		fieldLabel: 'Type',
		id: 'cboTipe',
		name: 'cboTipe',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtTipe').setValue('');
				Ext.getCmp('cboCode').setValue('');
				Ext.getCmp('cboCount').setValue('');
			}
		},
		triggers: {
			reset: {
				cls: 'x-form-clear-trigger',
				handler: function(field) {
					field.setValue('');
				}
			},
			cari: {
				cls: 'x-form-search-trigger',
				handler: function() {
					winCari.show();
					winCari.center();
				}
			}
		}
	};

	var txtTipe = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: "Type",
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtTipe',
		name: 'txtTipe',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupKode = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_code','fs_count','fs_nm_code',
			'fs_kd_tipe','fs_nm_tipe',
			'fs_alamat','fs_kd_kota','fs_nm_kota',
			'fs_kd_kab','fs_nm_kab','fs_kd_prop','fs_nm_prop',
			'fs_kd_neg','fs_nm_neg','fs_kd_wn','fs_nm_wn',
			'fs_kd_id','fd_tgl_id',
			'fs_tlp','fs_tlp2'
		],
		pageSize: 25,
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				rootProperty: 'hasil',
				totalProperty: 'total',
				type: 'json'
			},
			type: 'ajax',
			url: 'alamat/kodecode'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_tipe': Ext.getCmp('cboTipe').getValue(),
					'fs_code': Ext.getCmp('cboCode').getValue(),
					'fs_count': Ext.getCmp('cboCount').getValue(),
					'fs_nm_code': Ext.getCmp('cboCount').getValue(),
					'fs_cari': Ext.getCmp('txtCari2').getValue()
				});
			}
		}
	});


	var winGrid2 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupKode,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Code", dataIndex: 'fs_code', menuDisabled: true, width: 70},
			{text: "Count", dataIndex: 'fs_count', menuDisabled: true, width: 70},
			{text: "Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 340},
			{text: "Address", dataIndex: 'fs_alamat', menuDisabled: true, hidden: true},
			{text: "City Cd", dataIndex: 'fs_kd_kota', menuDisabled: true, hidden: true},
			{text: "City", dataIndex: 'fs_nm_kota', menuDisabled: true, hidden: true},
			{text: "District Cd", dataIndex: 'fs_kd_kab', menuDisabled: true, hidden: true},
			{text: "District", dataIndex: 'fs_nm_kab', menuDisabled: true, hidden: true},
			{text: "Province Cd", dataIndex: 'fs_kd_prop', menuDisabled: true, hidden: true},
			{text: "Province", dataIndex: 'fs_nm_prop', menuDisabled: true, hidden: true},
			{text: "Country Cd", dataIndex: 'fs_kd_neg', menuDisabled: true, hidden: true},
			{text: "Country", dataIndex: 'fs_nm_neg', menuDisabled: true, hidden: true},
			{text: "Citizen Cd", dataIndex: 'fs_kd_wn', menuDisabled: true, hidden: true},
			{text: "Citizen", dataIndex: 'fs_nm_wn', menuDisabled: true, hidden: true},
			{text: "ID Card", dataIndex: 'fs_kd_id', menuDisabled: true, hidden: true},
			{text: "ID Date", dataIndex: 'fd_tgl_id', menuDisabled: true, hidden: true},
			{text: "Phone 1", dataIndex: 'fs_tlp', menuDisabled: true, hidden: true},
			{text: "Phone 2", dataIndex: 'fs_tlp2', menuDisabled: true, hidden: true}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'City Cd / City Name',
				id: 'txtCari2',
				name: 'txtCari2',
				xtype: 'textfield'
			}]
		},{
			flex: 0.2,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '100%',
				text: 'Search',
				xtype: 'button',
				handler: function() {
					grupKode.load();
				}
			}]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: []
		}],
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupKode,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari2.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboCode').setValue(record.get('fs_code'));
				Ext.getCmp('cboCount').setValue(record.get('fs_count'));
				Ext.getCmp('txtNama').setValue(record.get('fs_nm_code'));
				Ext.getCmp('txtAlamat').setValue(record.get('fs_alamat'));
				Ext.getCmp('cboKota').setValue(record.get('fs_kd_kota'));
				Ext.getCmp('txtKota').setValue(record.get('fs_nm_kota'));
				Ext.getCmp('cboKab').setValue(record.get('fs_kd_kab'));
				Ext.getCmp('txtKab').setValue(record.get('fs_nm_kab'));
				Ext.getCmp('cboProp').setValue(record.get('fs_kd_prop'));
				Ext.getCmp('txtProp').setValue(record.get('fs_nm_prop'));
				Ext.getCmp('cboNeg').setValue(record.get('fs_kd_neg'));
				Ext.getCmp('txtNeg').setValue(record.get('fs_nm_neg'));
				Ext.getCmp('cboWN').setValue(record.get('fs_kd_wn'));
				Ext.getCmp('txtWN').setValue(record.get('fs_nm_wn'));
				Ext.getCmp('txtId').setValue(record.get('fs_kd_id'));
				Ext.getCmp('txtIddt').setValue(record.get('fd_tgl_id'));
				Ext.getCmp('txtTlp').setValue(record.get('fs_tlp'));
				Ext.getCmp('txtTlp2').setValue(record.get('fs_tlp2'));
				
				fnDetil();
				winCari2.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});

	var winCari2 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid2
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupKode.load();
				vMask.show();
			}
		}
	});

	var cboCode = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		fieldLabel: 'Code',
		id: 'cboCode',
		name: 'cboCode',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('cboCount').setValue('');
				Ext.getCmp('txtNama').setValue('');
				Ext.getCmp('txtAlamat').setValue('');
			}
		},
		triggers: {
			reset: {
				cls: 'x-form-clear-trigger',
				handler: function(field) {
					field.setValue('');
				}
			}/*,
			cari: {
				cls: 'x-form-search-trigger',
				handler: function() {
					winCari2.show();
					winCari2.center();
				}
			}*/
		}
	};

	var winGrid3 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		store: grupKode,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupKode,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari3.hide();
				}
			}]
		}),
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Code", dataIndex: 'fs_code', menuDisabled: true, width: 70},
			{text: "Count", dataIndex: 'fs_count', menuDisabled: true, width: 70},
			{text: "Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 340},
			{text: "Address", dataIndex: 'fs_alamat', menuDisabled: true, hidden: true},
			{text: "City Cd", dataIndex: 'fs_kd_kota', menuDisabled: true, hidden: true},
			{text: "City", dataIndex: 'fs_nm_kota', menuDisabled: true, hidden: true},
			{text: "District Cd", dataIndex: 'fs_kd_kab', menuDisabled: true, hidden: true},
			{text: "District", dataIndex: 'fs_nm_kab', menuDisabled: true, hidden: true},
			{text: "Province Cd", dataIndex: 'fs_kd_prop', menuDisabled: true, hidden: true},
			{text: "Province", dataIndex: 'fs_nm_prop', menuDisabled: true, hidden: true},
			{text: "Country Cd", dataIndex: 'fs_kd_neg', menuDisabled: true, hidden: true},
			{text: "Country", dataIndex: 'fs_nm_neg', menuDisabled: true, hidden: true},
			{text: "Citizen Cd", dataIndex: 'fs_kd_wn', menuDisabled: true, hidden: true},
			{text: "Citizen", dataIndex: 'fs_nm_wn', menuDisabled: true, hidden: true},
			{text: "ID Card", dataIndex: 'fs_kd_id', menuDisabled: true, hidden: true},
			{text: "ID Date", dataIndex: 'fd_tgl_id', menuDisabled: true, hidden: true},
			{text: "Phone 1", dataIndex: 'fs_tlp', menuDisabled: true, hidden: true},
			{text: "Phone 2", dataIndex: 'fs_tlp2', menuDisabled: true, hidden: true}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboCode').setValue(record.get('fs_code'));
				Ext.getCmp('cboCount').setValue(record.get('fs_count'));
				Ext.getCmp('txtNama').setValue(record.get('fs_nm_code'));
				Ext.getCmp('txtAlamat').setValue(record.get('fs_alamat'));
				Ext.getCmp('cboKota').setValue(record.get('fs_kd_kota'));
				Ext.getCmp('txtKota').setValue(record.get('fs_nm_kota'));
				Ext.getCmp('cboKab').setValue(record.get('fs_kd_kab'));
				Ext.getCmp('txtKab').setValue(record.get('fs_nm_kab'));
				Ext.getCmp('cboProp').setValue(record.get('fs_kd_prop'));
				Ext.getCmp('txtProp').setValue(record.get('fs_nm_prop'));
				Ext.getCmp('cboNeg').setValue(record.get('fs_kd_neg'));
				Ext.getCmp('txtNeg').setValue(record.get('fs_nm_neg'));
				Ext.getCmp('cboWN').setValue(record.get('fs_kd_wn'));
				Ext.getCmp('txtWN').setValue(record.get('fs_nm_wn'));
				Ext.getCmp('txtId').setValue(record.get('fs_kd_id'));
				Ext.getCmp('txtIddt').setValue(record.get('fd_tgl_id'));
				Ext.getCmp('txtTlp').setValue(record.get('fs_tlp'));
				Ext.getCmp('txtTlp2').setValue(record.get('fs_tlp2'));
				
				fnDetil();
				winCari3.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			listeners: {
				render: gridTooltipSearch
			}
		}
	});

	var winCari3 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid3
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupKode.load();
				vMask.show();
			}
		}
	});

	var cboCount = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		fieldLabel: 'Count',
		id: 'cboCount',
		labelWidth: 35,
		name: 'cboCount',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtNama').setValue('');
				Ext.getCmp('txtAlamat').setValue('');
			}
		},
		triggers: {
			reset: {
				cls: 'x-form-clear-trigger',
				handler: function(field) {
					field.setValue('');
				}
			},
			cari: {
				cls: 'x-form-search-trigger',
				handler: function() {
					winCari3.show();
					winCari3.center();
				}
			}
		}
	};

	var txtNama = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Enter a Name',
		fieldLabel: 'Name',
		id: 'txtNama',
		name: 'txtNama',
		xtype: 'textfield'
	};

	var txtAlamat = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Enter an Address',
		fieldLabel: 'Address',
		grow: true,
		growMin: 50,
		growMax: 50,
		id: 'txtAlamat',
		name: 'txtAlamat',
		xtype: 'textareafield'
	};

	var grupKota = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_kd_kota','fs_nm_kota'],
		pageSize: 25,
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				rootProperty: 'hasil',
				totalProperty: 'total',
				type: 'json'
			},
			type: 'ajax',
			url: 'alamat/kodekota'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_kota': Ext.getCmp('cboKota').getValue(),
					'fs_nm_kota': Ext.getCmp('cboKota').getValue(),
					'fs_cari': Ext.getCmp('txtCari4').getValue()
				});
			}
		}
	});


	var winGrid4 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupKota,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "City Code", dataIndex: 'fs_kd_kota', menuDisabled: true, width: 100},
			{text: "City Name", dataIndex: 'fs_nm_kota', menuDisabled: true, width: 380}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'City Code / City Name',
				id: 'txtCari4',
				name: 'txtCari4',
				xtype: 'textfield'
			}]
		},{
			flex: 0.2,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '100%',
				text: 'Search',
				xtype: 'button',
				handler: function() {
					grupKota.load();
				}
			}]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: []
		}],
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupKota,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari4.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboKota').setValue(record.get('fs_kd_kota'));
				Ext.getCmp('txtKota').setValue(record.get('fs_nm_kota'));
				
				winCari4.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});


	var winCari4 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid4
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupKota.load();
				vMask.show();
			}
		}
	});

	var cboKota = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'City',
		id: 'cboKota',
		name: 'cboKota',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtKota').setValue('');
			}
		},
		triggers: {
			reset: {
				cls: 'x-form-clear-trigger',
				handler: function(field) {
					field.setValue('');
				}
			},
			cari: {
				cls: 'x-form-search-trigger',
				handler: function() {
					winCari4.show();
					winCari4.center();
				}
			}
		}
	};

	var txtKota = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'City',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtKota',
		name: 'txtKota',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupKab = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_kd_kab','fs_nm_kab'],
		pageSize: 25,
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				rootProperty: 'hasil',
				totalProperty: 'total',
				type: 'json'
			},
			type: 'ajax',
			url: 'alamat/kodekab'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_kab': Ext.getCmp('cboKab').getValue(),
					'fs_nm_kab': Ext.getCmp('cboKab').getValue(),
					'fs_cari': Ext.getCmp('txtCari5').getValue()
				});
			}
		}
	});


	var winGrid5 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupKab,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "District Code", dataIndex: 'fs_kd_kab', menuDisabled: true, width: 100},
			{text: "District Name", dataIndex: 'fs_nm_kab', menuDisabled: true, width: 380}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'District Code / District Name',
				id: 'txtCari5',
				name: 'txtCari5',
				xtype: 'textfield'
			}]
		},{
			flex: 0.2,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '100%',
				text: 'Search',
				xtype: 'button',
				handler: function() {
					grupKab.load();
				}
			}]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: []
		}],
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupKab,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari5.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboKab').setValue(record.get('fs_kd_kab'));
				Ext.getCmp('txtKab').setValue(record.get('fs_nm_kab'));
				
				winCari5.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});


	var winCari5 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid5
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupKab.load();
				vMask.show();
			}
		}
	});

	var cboKab = {
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'District',
		id: 'cboKab',
		name: 'cboKab',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtKab').setValue('');
			}
		},
		triggers: {
			reset: {
				cls: 'x-form-clear-trigger',
				handler: function(field) {
					field.setValue('');
				}
			},
			cari: {
				cls: 'x-form-search-trigger',
				handler: function() {
					winCari5.show();
					winCari5.center();
				}
			}
		}
	};

	var txtKab = {
		anchor: '100%',
		emptyText: 'District',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtKab',
		name: 'txtKab',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupProp = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_kd_prop','fs_nm_prop'],
		pageSize: 25,
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				rootProperty: 'hasil',
				totalProperty: 'total',
				type: 'json'
			},
			type: 'ajax',
			url: 'alamat/kodeprop'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_prop': Ext.getCmp('cboProp').getValue(),
					'fs_nm_prop': Ext.getCmp('cboProp').getValue(),
					'fs_cari': Ext.getCmp('txtCari6').getValue()
				});
			}
		}
	});


	var winGrid6 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupProp,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Province Code", dataIndex: 'fs_kd_prop', menuDisabled: true, width: 100},
			{text: "Province Name", dataIndex: 'fs_nm_prop', menuDisabled: true, width: 380}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Province Code / Province Name',
				id: 'txtCari6',
				name: 'txtCari6',
				xtype: 'textfield'
			}]
		},{
			flex: 0.2,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '100%',
				text: 'Search',
				xtype: 'button',
				handler: function() {
					grupProp.load();
				}
			}]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: []
		}],
			bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupProp,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari6.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboProp').setValue(record.get('fs_kd_prop'));
				Ext.getCmp('txtProp').setValue(record.get('fs_nm_prop'));
				
				winCari6.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});


	var winCari6 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid6
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupProp.load();
				vMask.show();
			}
		}
	});

	var cboProp = {
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Province',
		id: 'cboProp',
		name: 'cboProp',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtProp').setValue('');
			}
		},
		triggers: {
			reset: {
				cls: 'x-form-clear-trigger',
				handler: function(field) {
					field.setValue('');
				}
			},
			cari: {
				cls: 'x-form-search-trigger',
				handler: function() {
					winCari6.show();
					winCari6.center();
				}
			}
		}
	};

	var txtProp = {
		anchor: '100%',
		emptyText: 'Province',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtProp',
		name: 'txtProp',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupNeg = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_kd_neg','fs_nm_neg'],
		pageSize: 25,
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				rootProperty: 'hasil',
				totalProperty: 'total',
				type: 'json'
			},
			type: 'ajax',
			url: 'alamat/kodeneg'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_neg': Ext.getCmp('cboNeg').getValue(),
					'fs_nm_neg': Ext.getCmp('cboNeg').getValue(),
					'fs_cari': Ext.getCmp('txtCari7').getValue()
				});
			}
		}
	});


	var winGrid7 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupNeg,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Country Code", dataIndex: 'fs_kd_neg', menuDisabled: true, width: 100},
			{text: "Country Name", dataIndex: 'fs_nm_neg', menuDisabled: true, width: 380}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Country Code / Country Name',
				id: 'txtCari7',
				name: 'txtCari7',
				xtype: 'textfield'
			}]
		},{
			flex: 0.2,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '100%',
				text: 'Search',
				xtype: 'button',
				handler: function() {
					grupNeg.load();
				}
			}]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: []
		}],
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupNeg,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari7.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboNeg').setValue(record.get('fs_kd_neg'));
				Ext.getCmp('txtNeg').setValue(record.get('fs_nm_neg'));
				
				winCari7.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});


	var winCari7 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid7
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupNeg.load();
				vMask.show();
			}
		}
	});

	var cboNeg = {
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Country',
		id: 'cboNeg',
		name: 'cboNeg',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtNeg').setValue('');
			}
		},
		triggers: {
			reset: {
				cls: 'x-form-clear-trigger',
				handler: function(field) {
					field.setValue('');
				}
			},
			cari: {
				cls: 'x-form-search-trigger',
				handler: function() {
					winCari7.show();
					winCari7.center();
				}
			}
		}
	};

	var txtNeg = {
		anchor: '100%',
		emptyText: 'Country',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtNeg',
		name: 'txtNeg',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupWN = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_kd_wn','fs_nm_wn'],
		pageSize: 25,
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				rootProperty: 'hasil',
				totalProperty: 'total',
				type: 'json'
			},
			type: 'ajax',
			url: 'alamat/kodewn'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_wn': Ext.getCmp('cboWN').getValue(),
					'fs_nm_wn': Ext.getCmp('cboWN').getValue(),
					'fs_cari': Ext.getCmp('txtCari8').getValue()
				});
			}
		}
	});


	var winGrid8 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupWN,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Citizen Code", dataIndex: 'fs_kd_wn', menuDisabled: true, width: 100},
			{text: "Citizen Name", dataIndex: 'fs_nm_wn', menuDisabled: true, width: 380}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Citizen Code / Citizen Name',
				id: 'txtCari8',
				name: 'txtCari8',
				xtype: 'textfield'
			}]
		},{
			flex: 0.2,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '100%',
				text: 'Search',
				xtype: 'button',
				handler: function() {
					grupWN.load();
				}
			}]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: []
		}],
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupWN,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari8.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboWN').setValue(record.get('fs_kd_wn'));
				Ext.getCmp('txtWN').setValue(record.get('fs_nm_wn'));
				
				winCari8.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});

	var winCari8 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid8
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupWN.load();
				vMask.show();
			}
		}
	});

	var cboWN = {
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Citizen',
		id: 'cboWN',
		name: 'cboWN',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtWN').setValue('');
			}
		},
		triggers: {
			reset: {
				cls: 'x-form-clear-trigger',
				handler: function(field) {
					field.setValue('');
				}
			},
			cari: {
				cls: 'x-form-search-trigger',
				handler: function() {
					winCari8.show();
					winCari8.center();
				}
			}
		}
	};

	var txtWN = {
		anchor: '100%',
		emptyText: 'Citizen',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtWN',
		name: 'txtWN',
		readOnly: true,
		xtype: 'textfield'
	};

	var txtId = {
		anchor: '98%',
		fieldLabel: 'ID Card',
		id: 'txtId',
		name: 'txtId',
		xtype: 'textfield'
	};

	var txtIddt = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		editable: true,
		fieldLabel: 'Up to',
		format: 'd-m-Y',
		id: 'txtIddt',
		labelWidth: 40,
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
		name: 'txtIddt',
		value: new Date(),
		xtype: 'datefield'
	};

	var txtTlp = {
		anchor: '100%',
		fieldLabel: 'Phone 1',
		id: 'txtTlp',
		name: 'txtTlp',
		xtype: 'textfield'
	};

	var txtTlp2 = {
		anchor: '100%',
		fieldLabel: 'Phone 2',
		id: 'txtTlp2',
		name: 'txtTlp2',
		xtype: 'textfield'
	};

	var grupGL = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_kd_acno','fs_nm_acno'],
		pageSize: 25,
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				rootProperty: 'hasil',
				totalProperty: 'total',
				type: 'json'
			},
			type: 'ajax',
			url: 'alamat/kodegl'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_acno': Ext.getCmp('cboGL').getValue(),
					'fs_nm_acno': Ext.getCmp('cboGL').getValue(),
					'fs_cari': Ext.getCmp('txtCari10').getValue()
				});
			}
		}
	});
	

	var winGrid10 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupGL,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Code", dataIndex: 'fs_kd_acno', menuDisabled: true, width: 120},
			{text: "Name", dataIndex: 'fs_nm_acno', menuDisabled: true, width: 360}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Code / Name',
				id: 'txtCari10',
				name: 'txtCari10',
				xtype: 'textfield'
			}]
		},{
			flex: 0.2,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '100%',
				text: 'Search',
				xtype: 'button',
				handler: function() {
					grupGL.load();
				}
			}]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: []
		}],
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupGL,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari10.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboGL').setValue(record.get('fs_kd_acno'));
				Ext.getCmp('txtGL').setValue(record.get('fs_nm_acno'));
				
				winCari10.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});

	var winCari10 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid10
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupGL.load();
				vMask.show();
			}
		}
	});

	var cboGL = {
		anchor: '98%',
		emptyText: "Select a",
		fieldLabel: 'GL Acc',
		id: 'cboGL',
		name: 'cboGL',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtGL').setValue('');
			}
		},
		triggers: {
			reset: {
				cls: 'x-form-clear-trigger',
				handler: function(field) {
					field.setValue('');
				}
			},
			cari: {
				cls: 'x-form-search-trigger',
				handler: function() {
					winCari10.show();
					winCari10.center();
				}
			}
		}
	};

	var txtGL = {
		anchor: '100%',
		emptyText: "GL Acc",
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtGL',
		name: 'txtGL',
		readOnly: true,
		xtype: 'textfield'
	};

	var cekSupp = {
		boxLabel: 'Active',
		boxLabelAlign: 'before',
		checked: true,
		hidden: true,
		id: 'cekSupp',
		name: 'cekSupp',
		xtype: 'checkboxfield'
	};

	var grupTipeCust = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_kd_tipe','fs_nm_tipe'],
		pageSize: 25,
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				rootProperty: 'hasil',
				totalProperty: 'total',
				type: 'json'
			},
			type: 'ajax',
			url: 'alamat/kodetipecust'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_tipe': Ext.getCmp('cboTipeCust').getValue(),
					'fs_nm_tipe': Ext.getCmp('txtTipeCust').getValue(),
					'fs_cari': Ext.getCmp('txtCari9').getValue()
				});
			}
		}
	});

	var winGrid9 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupTipeCust,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Code", dataIndex: 'fs_kd_tipe', menuDisabled: true, width: 110},
			{text: "Name", dataIndex: 'fs_nm_tipe', menuDisabled: true, width: 370}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Code / Name',
				id: 'txtCari9',
				name: 'txtCari9',
				xtype: 'textfield'
			}]
		},{
			flex: 0.2,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '100%',
				text: 'Search',
				xtype: 'button',
				handler: function() {
					grupTipeCust.load();
				}
			}]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: []
		}],
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupTipeCust,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari9.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboTipeCust').setValue(record.get('fs_kd_tipe'));
				Ext.getCmp('txtTipeCust').setValue(record.get('fs_nm_tipe'));
				
				winCari9.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});


	var winCari9 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid9
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupTipeCust.load();
				vMask.show();
			}
		}
	});

	var cboTipeCust = {
		anchor: '98%',
		emptyText: "Select a",
		fieldLabel: 'Type',
		id: 'cboTipeCust',
		name: 'cboTipeCust',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtTipeCust').setValue('');
			}
		},
		triggers: {
			reset: {
				cls: 'x-form-clear-trigger',
				handler: function(field) {
					field.setValue('');
				}
			},
			cari: {
				cls: 'x-form-search-trigger',
				handler: function() {
					winCari9.show();
					winCari9.center();
				}
			}
		}
	};

	var txtTipeCust = {
		anchor: '100%',
		emptyText: "Type",
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtTipeCust',
		name: 'txtTipeCust',
		readOnly: true,
		xtype: 'textfield'
	};

	var cekCust = {
		boxLabel: 'Active',
		boxLabelAlign: 'before',
		checked: true,
		hidden: true,
		id: 'cekCust',
		name: 'cekCust',
		xtype: 'checkboxfield'
	};

	function fnCekSave() {
		if (this.up('form').getForm().isValid()) {
			var xTipe = Ext.getCmp('cboTipe').getValue();
			var xKdKota = Ext.getCmp('cboKota').getValue();
			var xNmKota = Ext.getCmp('txtKota').getValue();
			var xID = Ext.getCmp('txtId').getValue();
			
			if (xNmKota.trim() === '') {
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					message: 'City is empty, please fill in advance!!',
					title: 'IDS'
				});
				return;
			}
			
			if (xTipe.trim() === '02' && xID.trim() === '' ) {
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					message: 'Customer ID Card is empty, please fill in advance!!',
					title: 'IDS'
				});
				return;
			}
			
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'alamat/ceksave',
				params: {
					'fs_kd_tipe': Ext.getCmp('cboTipe').getValue(),
					'fs_code': Ext.getCmp('cboCode').getValue(),
					'fs_count': Ext.getCmp('cboCount').getValue()
				},
				success: function(response) {
					var xtext = Ext.decode(response.responseText);
					
					if (xtext.sukses === false) {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.MessageBox.INFO,
							msg: xtext.hasil,
							title: 'IDS'
						});
					}
					else {
						if (xtext.sukses === true && xtext.hasil == 'lanjut') {
							fnSave();
						}
						else {
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.YESNO,
								closable: false,
								icon: Ext.Msg.QUESTION,
								msg: xtext.hasil,
								title: 'IDS',
								fn: function(btn) {
									if (btn == 'yes') {
										fnSave();
									}
								}
							});
						}
					}
				},
				failure: function(response) {
					var xtext = Ext.decode(response.responseText);
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: 'Saving Failed, Connection Failed!!',
						title: 'IDS'
					});
					fnMaskHide();
				}
			});
		}
	}

	function fnSave() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'alamat/save',
			params: {
				'fs_kd_tipe': Ext.getCmp('cboTipe').getValue(),
				'fs_code': Ext.getCmp('cboCode').getValue(),
				'fs_count': Ext.getCmp('cboCount').getValue(),
				'fs_nama': Ext.getCmp('txtNama').getValue(),
				'fs_alamat': Ext.getCmp('txtAlamat').getValue(),
				'fs_kd_kota': Ext.getCmp('cboKota').getValue(),
				'fs_nm_kota': Ext.getCmp('txtKota').getValue(),
				'fs_kd_kab': Ext.getCmp('cboKab').getValue(),
				'fs_kd_prop': Ext.getCmp('cboProp').getValue(),
				'fs_kd_neg': Ext.getCmp('cboNeg').getValue(),
				'fs_kd_wn': Ext.getCmp('cboWN').getValue(),
				'fs_kd_id': Ext.getCmp('txtId').getValue(),
				'fd_tgl_id': Ext.Date.format(Ext.getCmp('txtIddt').getValue(), 'Ymd'),
				'fs_tlp': Ext.getCmp('txtTlp').getValue(),
				'fs_tlp2': Ext.getCmp('txtTlp2').getValue(),
				'fs_kd_acno': Ext.getCmp('cboGL').getValue(),
				'fb_supp': Ext.getCmp('cekSupp').getValue(),
				'fs_kd_tipecust': Ext.getCmp('cboTipeCust').getValue(),
				'fb_cust': Ext.getCmp('cekCust').getValue()
			},
			success: function(response) {
				var xtext = Ext.decode(response.responseText);
				
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: xtext.hasil,
					title: 'IDS'
				});
				if (xtext.sukses === true) {
					fnReset();
				}
			},
			failure: function(response) {
				var xtext = Ext.decode(response.responseText);
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Saving Failed, Connection Failed!!',
					title: 'IDS'
				});
				fnMaskHide();
			}
		});
	}

	function fnExportToExcel() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 180000,
			url: 'alamat/excel_cust',
			params: {
			},
			success: function(response) {
				var xtext = Ext.decode(response.responseText);
				
				vMask.show();
				if (xtext.sukses === true) {
					var xfile = xtext.nmfile.trim();
					
					var winxls = Ext.create('Ext.window.Window', {
						closable: false,
						draggable: false,
						height: 100,
						title: 'Download Excel Files',
						width: 180,
						defaults: {
							margin: 5
						},
						items: [{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [{
									anchor: '95%',
									href: xfile,
									hrefTarget: '_blank',
									iconCls: 'icon-save',
									text: 'Download',
									xtype: 'button'
								}]
							},{
								flex: 0.8,
								layout: 'anchor',
								xtype: 'container',
								items: [{
									anchor: '100%',
									iconCls: 'icon-exit',
									text: 'Exit',
									xtype: 'button',
									handler: function() {
										vMask.hide();
										winxls.hide();
									}
								}]
							}]
						}]
					}).show();
				}
				else {
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});
					vMask.hide();
				}
			},
			failure: function(response) {
				var xtext = Ext.decode(response.responseText);
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Export Failed, Connection Failed!!',
					title: 'IDS'
				});
				fnMaskHide();
			}
		});
	}

	function fnReset() {
		Ext.getCmp('cboTipe').setValue('');
		Ext.getCmp('txtTipe').setValue('');
		Ext.getCmp('cboCode').setValue('');
		Ext.getCmp('cboCount').setValue('');
		Ext.getCmp('txtNama').setValue('');
		Ext.getCmp('txtAlamat').setValue('');
		Ext.getCmp('cboKota').setValue('');
		Ext.getCmp('txtKota').setValue('');
		Ext.getCmp('cboKab').setValue('');
		Ext.getCmp('txtKab').setValue('');
		Ext.getCmp('cboProp').setValue('');
		Ext.getCmp('txtProp').setValue('');
		Ext.getCmp('cboNeg').setValue('');
		Ext.getCmp('txtNeg').setValue('');
		Ext.getCmp('cboWN').setValue('');
		Ext.getCmp('txtWN').setValue('');
		Ext.getCmp('txtId').setValue('');
		Ext.getCmp('txtIddt').setValue(new Date());
		Ext.getCmp('txtTlp').setValue('');
		Ext.getCmp('txtTlp2').setValue('');
		Ext.getCmp('cboGL').setValue('');
		Ext.getCmp('txtGL').setValue('');
		Ext.getCmp('cekSupp').setValue('1');
		Ext.getCmp('cboTipeCust').setValue('');
		Ext.getCmp('txtTipeCust').setValue('');
		Ext.getCmp('cekCust').setValue('1');
	}

	var frmAlamat = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Address Maintenance Form',
		width: 550,
		items: [{
			activeTab: 0,
			bodyStyle: 'padding: 5px; background-color: '.concat(gBasePanel),
			border: false,
			plain: true,
			xtype: 'tabpanel',
			items: [{
				bodyStyle: 'background-color: '.concat(gBasePanel),
				border: false,
				frame: false,
				title: 'Entry Address',
				xtype: 'form',
				items: [{
					fieldDefaults: {
						labelAlign: 'right',
						labelSeparator: '',
						labelWidth: 50,
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
								cboTipe
							]
						},{
							flex: 2.5,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtTipe
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
								cboCode
							]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cboCount
							]
						}]
					},
						txtNama,
						txtAlamat,
					{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cboKota,
								cboKab,
								cboProp,
								cboNeg,
								cboWN
							]
						},{
							flex: 2.5,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtKota,
								txtKab,
								txtProp,
								txtNeg,
								txtWN
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
								txtId
							]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtIddt
							]
						}]
					},
						txtTlp,
						txtTlp2
					]
				}],
				buttons: [{
					text: 'Save',
					handler: fnCekSave
				},{
					text: 'Customer List',
					handler: fnExportToExcel
				},{
					text: 'Reset',
					handler: fnReset
				}]
			},{
				bodyStyle: 'background-color: '.concat(gBasePanel),
				border: false,
				frame: false,
				height: 389,
				title: 'Master Supplier',
				xtype: 'form',
				items: [{
					fieldDefaults: {
						labelAlign: 'right',
						labelSeparator: '',
						labelWidth: 50,
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
								cboGL
							]
						},{
							flex: 1.7,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtGL
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
								
							]
						},{
							flex: 27,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cekSupp
							]
						}]
					}]
				}]
			},{
				bodyStyle: 'background-color: '.concat(gBasePanel),
				border: false,
				frame: false,
				height: 389,
				hidden: true,
				title: 'Master Customer',
				xtype: 'form',
				items: [{
					fieldDefaults: {
						labelAlign: 'right',
						labelSeparator: '',
						labelWidth: 50,
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
								cboTipeCust
							]
						},{
							flex: 2.5,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtTipeCust
							]
						}]
					},{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1,
							layout: 'anchor',
							xtype: 'container'
						},{
							flex: 27,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cekCust
							]
						}]
					}]
				}]
			}]
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmAlamat
	});
	
	function fnMaskShow() {
		frmAlamat.mask('Please wait...');
	}

	function fnMaskHide() {
		frmAlamat.unmask();
	}
	
	frmAlamat.render(Ext.getBody());
	Ext.get('loading').destroy();
});
