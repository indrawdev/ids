Ext.Loader.setConfig({
	enabled: true
});

Ext.Loader.setPath('Ext.ux', gBaseUX);

Ext.require([
	'Ext.ux.form.NumericField',
	'Ext.ux.LiveSearchGridPanel',
	'Ext.ux.ProgressBarPager'
]);

Ext.onReady(function() {
    Ext.QuickTips.init();
	Ext.util.Format.thousandSeparator = ',';
	Ext.util.Format.decimalSeparator = '.';

	var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';

	function gridTooltipSearch(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Double click on record to choose',
			target: view.el,
			trackMouse: true
		});
	}

	function gridTooltipCust(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Single click on the customer item in the list to display register',
			target: view.el,
			trackMouse: true
		});
	}

	Ext.define('DataGridCust', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_cust', type: 'string'},
			{name: 'fs_count', type: 'string'},
			{name: 'fs_nm_cust', type: 'string'},
			{name: 'fs_alamat', type: 'string'},
			{name: 'fs_nm_product', type: 'string'},
			{name: 'fs_rangka', type: 'string'},
			{name: 'fs_mesin', type: 'string'},
			{name: 'fs_stnk', type: 'string'},
			{name: 'fd_stnk', type: 'string'},
			{name: 'fs_bpkb', type: 'string'},
			{name: 'fd_bpkb', type: 'string'},
			{name: 'fs_nopol', type: 'string'},
			{name: 'fs_note', type: 'string'},
			{name: 'fs_nm_stnk_qq', type: 'string'},
			{name: 'fs_almt_stnk_qq', type: 'string'},
			{name: 'fs_nm_bpkb_qq', type: 'string'},
			{name: 'fs_almt_bpkb_qq', type: 'string'}
		]
	});

	var grupGridCust = Ext.create('Ext.data.Store', {
		autoLoad: true,
		model: 'DataGridCust',
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
			url: 'stnkbpkb/cust_list'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_cari': Ext.getCmp('txtCari').getValue()
				});
			}
		}
	});

	var gridCust = Ext.create('Ext.grid.Panel', {
		defaultType: 'textfield',
		height: 300,
		sortableColumns: false,
		store: grupGridCust,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupGridCust
		}),
		columns: [{
			xtype: 'rownumberer',
			width: 45
		},{
			text: 'Cust Cd',
			dataIndex: 'fs_kd_cust',
			hidden: true,
			locked: true,
			menuDisabled: true
		},{
			text: 'Count',
			dataIndex: 'fs_count',
			hidden: true,
			locked: true,
			menuDisabled: true
		},{
			text: 'Customer',
			dataIndex: 'fs_nm_cust',
			locked: true,
			menuDisabled: true,
			width: 200
		},{
			text: 'Address',
			dataIndex: 'fs_alamat',
			menuDisabled: true,
			width: 250
		},{
			text: 'Product',
			dataIndex: 'fs_nm_product',
			menuDisabled: true,
			width: 100
		},{
			text: 'Chassis',
			dataIndex: 'fs_rangka',
			menuDisabled: true,
			width: 125
		},{
			text: 'Machine',
			dataIndex: 'fs_mesin',
			menuDisabled: true,
			width: 100
		},{
			text: 'STNK Date',
			dataIndex: 'fd_stnk',
			menuDisabled: true,
			width: 80
		},{
			text: 'STNK No',
			dataIndex: 'fs_stnk',
			menuDisabled: true,
			width: 100
		},{
			text: 'STNK QQ Name',
			dataIndex: 'fs_nm_stnk_qq',
			menuDisabled: true,
			width: 150
		},{
			text: 'STNK QQ Address',
			dataIndex: 'fs_almt_stnk_qq',
			menuDisabled: true,
			width: 150
		},{
			text: 'BPKB Date',
			dataIndex: 'fd_bpkb',
			menuDisabled: true,
			width: 80
		},{
			text: 'BPKB No',
			dataIndex: 'fs_bpkb',
			menuDisabled: true,
			width: 100
		},{
			text: 'BPKB QQ Name',
			dataIndex: 'fs_nm_bpkb_qq',
			menuDisabled: true,
			width: 150
		},{
			text: 'BPKB QQ Address',
			dataIndex: 'fs_almt_bpkb_qq',
			menuDisabled: true,
			width: 150
		},{
			text: 'Police No',
			dataIndex: 'fs_nopol',
			menuDisabled: true,
			width: 100
		},{
			text: 'Note',
			dataIndex: 'fs_note',
			menuDisabled: true,
			width: 100
		}],
		listeners: {
			itemclick: function(grid, record) {
				var xstnk = '';
				var xtglstnk = '';
				var xtglstnk2 = '';
				var xbpkb = '';
				var xtglbpkb = '';
				var xtglbpkb2 = '';
				
				if (record.get('fd_stnk').trim() === '') {
					xstnk = 'Belum serah ke konsumen';
					xtglstnk = new Date();
					xtglstnk2 = '';
					Ext.getCmp('cekStnk').setValue('0');
					Ext.getCmp('txtStnkdt').setReadOnly(true);
					Ext.getCmp('txtStnkdt').setFieldStyle('background-color: #eee; background-image: none;');
				}
				else {
					xstnk = 'Sudah serah ke konsumen';
					xtglstnk = record.get('fd_stnk');
					xtglstnk2 = record.get('fd_stnk');
					Ext.getCmp('cekStnk').setValue('1');
					Ext.getCmp('txtStnkdt').setReadOnly(false);
					Ext.getCmp('txtStnkdt').setFieldStyle('background-color: white; background-image: url('.concat(gBaseIMG,'text-bg.gif);'));
				}
				
				if (record.get('fd_bpkb').trim() === '') {
					xbpkb = 'Belum serah ke konsumen';
					xtglbpkb = new Date();
					xtglbpkb2 = '';
					Ext.getCmp('cekBpkb').setValue('0');
					Ext.getCmp('txtBpkbdt').setReadOnly(true);
					Ext.getCmp('txtBpkbdt').setFieldStyle('background-color: #eee; background-image: none;');
				}
				else {
					xbpkb = 'Sudah serah ke konsumen';
					xtglbpkb = record.get('fd_bpkb');
					xtglbpkb2 = record.get('fd_bpkb');
					Ext.getCmp('cekBpkb').setValue('1');
					Ext.getCmp('txtBpkbdt').setReadOnly(false);
					Ext.getCmp('txtBpkbdt').setFieldStyle('background-color: white; background-image: url('.concat(gBaseIMG,'text-bg.gif);'));
				}
				
				Ext.getCmp('txtTglStnk').setValue(xtglstnk2);
				Ext.getCmp('txtStnk').setValue(record.get('fs_stnk'));
				Ext.getCmp('txtStnkdt').setValue(xtglstnk);
				Ext.getCmp('txtTglBpkb').setValue(xtglbpkb2);
				Ext.getCmp('txtBpkb').setValue(record.get('fs_bpkb'));
				Ext.getCmp('txtBpkbdt').setValue(xtglbpkb);
				Ext.getCmp('txtRangka').setValue(record.get('fs_rangka'));
				Ext.getCmp('txtMesin').setValue(record.get('fs_mesin'));
				Ext.getCmp('txtLabelStnk').setValue(xstnk);
				Ext.getCmp('txtLabelBpkb').setValue(xbpkb);
				Ext.getCmp('txtNopol').setValue(record.get('fs_nopol'));
			}
		},
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Type customer / chassis / machine',
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
					grupGridCust.load();
				}
			}]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: []
		}],
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			listeners: {
				render: gridTooltipCust
			},
			markDirty: false,
			stripeRows: true
		}
	});

	var txtTglStnk = {
		anchor: '95%',
		fieldLabel: 'STNK Dt',
		hidden: true,
		id: 'txtTglStnk',
		name: 'txtTglStnk',
		xtype: 'textfield'
	};
	
	var txtStnk = {
		anchor: '95%',
		fieldLabel: 'STNK No',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtStnk',
		name: 'txtStnk',
		readOnly: true,
		xtype: 'textfield'
	};
	
	var txtTglBpkb = {
		anchor: '95%',
		fieldLabel: 'BPKB Dt',
		hidden: true,
		id: 'txtTglBpkb',
		name: 'txtTglBpkb',
		xtype: 'textfield'
	};
	
	var txtBpkb = {
		anchor: '95%',
		fieldLabel: 'BPKB No',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtBpkb',
		name: 'txtBpkb',
		readOnly: true,
		xtype: 'textfield'
	};
	
	var txtRangka = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '95%',
		fieldLabel: 'Chassis',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtRangka',
		name: 'txtRangka',
		readOnly: true,
		xtype: 'textfield'
	};
	
	var txtMesin = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '95%',
		fieldLabel: 'Machine',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtMesin',
		name: 'txtMesin',
		readOnly: true,
		xtype: 'textfield'
	};
	
	var txtLabelStnk = {
		fieldLabel: '',
		id: 'txtLabelStnk',
		labelWidth: 90,
		name: 'txtLabelStnk',
		value: '',
		xtype: 'displayfield'
	};
	
	var txtLabelBpkb = {
		fieldLabel: '',
		id: 'txtLabelBpkb',
		labelWidth: 90,
		name: 'txtLabelBpkb',
		value: '',
		xtype: 'displayfield'
	};
	
	var txtStnkdt = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		editable: true,
		fieldLabel: 'STNK Handover',
		fieldStyle: 'background-color: #eee; background-image: none;',
		format: 'd-m-Y',
		id: 'txtStnkdt',
		labelWidth: 100,
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
		name: 'txtStnkdt',
		readOnly: true,
		value: new Date(),
		xtype: 'datefield'
	};
	
	var txtBpkbdt = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		editable: true,
		fieldLabel: 'BPKB Handover',
		fieldStyle: 'background-color: #eee; background-image: none;',
		format: 'd-m-Y',
		id: 'txtBpkbdt',
		labelWidth: 100,
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
		name: 'txtBpkbdt',
		readOnly: true,
		value: new Date(),
		xtype: 'datefield'
	};
	
	var cekStnk = {
		checked: false,
		id: 'cekStnk',
		labelAlign: 'left',
		name: 'cekStnk',
		xtype: 'checkboxfield',
		listeners: {
			change: function(value) {
				if (Ext.getCmp('cekStnk').getValue() == '1') {
					Ext.getCmp('txtStnkdt').setReadOnly(false);
					Ext.getCmp('txtStnkdt').setFieldStyle('background-color: white; background-image: url('.concat(gBaseIMG,'text-bg.gif);'));
					return value;
				}
				else {
					Ext.getCmp('txtStnkdt').setReadOnly(true);
					Ext.getCmp('txtStnkdt').setFieldStyle('background-color: #eee; background-image: none;');
					return value;
				}
			}
		}
	};
	
	var cekBpkb = {
		checked: false,
		id: 'cekBpkb',
		labelAlign: 'left',
		name: 'cekBpkb',
		xtype: 'checkboxfield',
		listeners: {
			change: function(value) {
				if (Ext.getCmp('cekBpkb').getValue() == '1') {
					Ext.getCmp('txtBpkbdt').setReadOnly(false);
					Ext.getCmp('txtBpkbdt').setFieldStyle('background-color: white; background-image: url('.concat(gBaseIMG,'text-bg.gif);'));
					return value;
				}
				else {
					Ext.getCmp('txtBpkbdt').setReadOnly(true);
					Ext.getCmp('txtBpkbdt').setFieldStyle('background-color: #eee; background-image: none;');
					return value;
				}
			}
		}
	};
	
	var txtNopol = {
		anchor: '100%',
		fieldLabel: 'Police Number',
		id: 'txtNopol',
		labelWidth: 100,
		name: 'txtNopol',
		xtype: 'textfield'
	};
	
	function fnCekSave() {
		if (this.up('form').getForm().isValid()) {
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'stnkbpkb/ceksave',
				params: {
					'fs_rangka': Ext.getCmp('txtRangka').getValue(),
					'fs_mesin': Ext.getCmp('txtMesin').getValue(),
					'fd_stnk': Ext.getCmp('txtTglStnk').getValue()
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
		var xtglstnk = '';
		var xtglbpkb = '';
		var xcekstnk = Ext.getCmp('cekStnk').getValue();
		var xcekbpkb = Ext.getCmp('cekBpkb').getValue();
		
		if (xcekstnk === true) {
			xtglstnk = Ext.Date.format(Ext.getCmp('txtStnkdt').getValue(), 'Ymd');
		}
		
		if (xcekbpkb === true) {
			xtglbpkb = Ext.Date.format(Ext.getCmp('txtBpkbdt').getValue(), 'Ymd');
		}
		
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'stnkbpkb/save',
			params: {
				'fs_rangka': Ext.getCmp('txtRangka').getValue(),
				'fs_mesin': Ext.getCmp('txtMesin').getValue(),
				'fd_stnk': xtglstnk,
				'fd_bpkb': xtglbpkb,
				'fs_nopol': Ext.getCmp('txtNopol').getValue()
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

	function fnCekRemove() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'stnkbpkb/cekremove',
			params: {
				'fs_rangka': Ext.getCmp('txtRangka').getValue(),
				'fs_mesin': Ext.getCmp('txtMesin').getValue()
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
						fnRemove();
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
									fnRemove();
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

	function fnRemove() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'stnkbpkb/remove',
			params: {
				'fs_rangka': Ext.getCmp('txtRangka').getValue(),
				'fs_mesin': Ext.getCmp('txtMesin').getValue()
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
					msg: 'Remove Failed, Connection Failed!!',
					title: 'IDS'
				});
				fnMaskHide();
			}
		});
	}

	function fnPrintSTNK() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'stnkbpkb/print_stnk',
			params: {
				'fs_rangka': Ext.getCmp('txtRangka').getValue(),
				'fs_mesin': Ext.getCmp('txtMesin').getValue()
			},
			success: function(response) {
				var xtext = Ext.decode(response.responseText);
				/*
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: xtext.hasil,
					title: 'IDS'
				});*/
				
				if (xtext.sukses === true) {
					var xfile = xtext.nmfile.trim();
					
					var winpdf = Ext.create('Ext.window.Window', {
						closable: false,
						draggable: false,
						height: 500,
						layout: 'fit',
						title: 'STNK / BPKB',
						width: 900,
						items: {
							xtype: 'component',
							autoEl: {
								src: xfile,
								tag: 'iframe'
							}
						},
						buttons: [{
							text: 'Exit',
							handler: function() {
								winpdf.hide();
							}
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
				}
			},
			failure: function(response) {
				var xtext = Ext.decode(response.responseText);
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Print Failed, Connection Failed!!',
					title: 'IDS'
				});
				fnMaskHide();
			}
		});
	}

	function fnPrintBPKB() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'stnkbpkb/print_bpkb',
			params: {
				'fs_rangka': Ext.getCmp('txtRangka').getValue(),
				'fs_mesin': Ext.getCmp('txtMesin').getValue()
			},
			success: function(response) {
				var xtext = Ext.decode(response.responseText);
				/*
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: xtext.hasil,
					title: 'IDS'
				});*/
				
				if (xtext.sukses === true) {
					var xfile = xtext.nmfile.trim();
					
					var winpdf = Ext.create('Ext.window.Window', {
						closable: false,
						draggable: false,
						height: 500,
						layout: 'fit',
						title: 'STNK / BPKB',
						width: 900,
						items: {
							xtype: 'component',
							autoEl: {
								src: xfile,
								tag: 'iframe'
							}
						},
						buttons: [{
							text: 'Exit',
							handler: function() {
								winpdf.hide();
							}
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
				}
			},
			failure: function(response) {
				var xtext = Ext.decode(response.responseText);
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Print Failed, Connection Failed!!',
					title: 'IDS'
				});
				fnMaskHide();
			}
		});
	}

	function fnReset() {
		Ext.getCmp('txtTglStnk').setValue('');
		Ext.getCmp('txtStnk').setValue('');
		Ext.getCmp('txtStnkdt').setValue(new Date());
		Ext.getCmp('txtTglBpkb').setValue('');
		Ext.getCmp('txtBpkb').setValue('');
		Ext.getCmp('txtBpkbdt').setValue(new Date());
		Ext.getCmp('txtRangka').setValue('');
		Ext.getCmp('txtMesin').setValue('');
		Ext.getCmp('txtLabelStnk').setValue('');
		Ext.getCmp('txtLabelBpkb').setValue('');
		Ext.getCmp('txtNopol').setValue('');
		Ext.getCmp('cekStnk').setValue('');
		Ext.getCmp('cekBpkb').setValue('');
		Ext.getCmp('txtStnkdt').setReadOnly(true);
		Ext.getCmp('txtStnkdt').setFieldStyle('background-color: #eee; background-image: none;');
		Ext.getCmp('txtBpkbdt').setReadOnly(true);
		Ext.getCmp('txtBpkbdt').setFieldStyle('background-color: #eee; background-image: none;');
		Ext.getCmp('cekStnk').setValue('0');
		Ext.getCmp('cekBpkb').setValue('0');
		
		grupGridCust.load();
	}

	var frmStnkBpkb = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'STNK-BPKB Form',
		width: 950,
		fieldDefaults: {
			labelAlign: 'right',
			labelSeparator: '',
			labelWidth: 60,
			msgTarget: 'side'
		},
		items: [{
			style: 'padding: 5px;',
			title: 'Customer List',
			xtype: 'fieldset',
			items: [
				gridCust
			]
		},{
			style: 'padding: 5px;',
			title: 'Register Details',
			xtype: 'fieldset',
			items: [{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 3,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						anchor: '95%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 2,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtTglStnk,
								txtStnk,
								txtTglBpkb,
								txtBpkb,
								txtRangka,
								txtMesin
							]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtLabelStnk,
								txtLabelBpkb
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
								txtStnkdt,
								txtBpkbdt
							]
						},{
							flex: 0.08,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cekStnk,
								cekBpkb
							]
						}]
					},
						txtNopol
					]
				}]
			}]
		}],
		buttons: [{
			text: 'Save',
			handler: fnCekSave
		},{
			text: 'Receipt STNK',
			handler: fnPrintSTNK
		},{
			text: 'Receipt BPKB',
			handler: fnPrintBPKB
		},{
			text: 'Reset',
			handler: fnReset
		},{
			text: 'Remove',
			handler: fnCekRemove
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmStnkBpkb
	});
	
	function fnMaskShow() {
		frmStnkBpkb.mask('Please wait...');
	}

	function fnMaskHide() {
		frmStnkBpkb.unmask();
	}
	
	frmStnkBpkb.render(Ext.getBody());
	Ext.get('loading').destroy();
});
