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

	var grupProduk = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_product','fs_nm_product','fs_rangka',
			'fs_mesin','fn_cc','fs_kd_acno','fs_nm_acno',
			'fs_kd_acnox','fs_nm_acnox','fb_active','fs_status'
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
			url: 'produk/kodeproduk'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_product': Ext.getCmp('cboProduk').getValue(),
					'fs_nm_product': Ext.getCmp('cboProduk').getValue(),
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
		store: grupProduk,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Product Cd", dataIndex: 'fs_kd_product', menuDisabled: true, width: 100},
			{text: "Product", dataIndex: 'fs_nm_product', menuDisabled: true, width: 380}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Product Cd / Product',
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
					grupProduk.load();
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
			store: grupProduk,
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
				Ext.getCmp('cboProduk').setValue(record.get('fs_kd_product'));
				Ext.getCmp('txtProduk').setValue(record.get('fs_nm_product'));
				
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
				grupProduk.load();
				vMask.show();
			}
		}
	});

	var cboProduk = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Product',
		id: 'cboProduk',
		name: 'cboProduk',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtProduk').setValue('');
				Ext.getCmp('cboRangka').setValue('');
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

	var txtProduk = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Product Name',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtProduk',
		name: 'txtProduk',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupRangka = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_product','fs_nm_product','fs_rangka',
			'fs_mesin','fs_register','fs_cc',
			'fs_thn','fs_kd_warna','fs_nm_warna',
			'fs_kd_wh','fs_nm_wh'
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
			url: 'regstatus/rangka'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_product': Ext.getCmp('cboProduk').getValue(),
					'fs_rangka': Ext.getCmp('cboRangka').getValue(),
					'fs_mesin': Ext.getCmp('cboRangka').getValue(),
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
		store: grupRangka,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Code", dataIndex: 'fs_kd_product', menuDisabled: true, width: 100},
			{text: "Product", dataIndex: 'fs_nm_product', menuDisabled: true, width: 120},
			{text: "Chassis", dataIndex: 'fs_rangka', menuDisabled: true, width: 150},
			{text: "Machine", dataIndex: 'fs_mesin', menuDisabled: true, width: 110},
			{text: "Register", dataIndex: 'fs_register', menuDisabled: true, hidden: true},
			{text: "CC", dataIndex: 'fs_cc', menuDisabled: true, hidden: true},
			{text: "Year", dataIndex: 'fs_thn', menuDisabled: true, hidden: true},
			{text: "Code", dataIndex: 'fs_kd_warna', menuDisabled: true, hidden: true},
			{text: "Color", dataIndex: 'fs_nm_warna', menuDisabled: true, hidden: true},
			{text: "Code", dataIndex: 'fs_kd_wh', menuDisabled: true, hidden: true},
			{text: "Warehouse", dataIndex: 'fs_nm_wh', menuDisabled: true, hidden: true}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Code / Chassis / Machine',
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
					grupRangka.load();
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
			store: grupRangka,
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
				Ext.getCmp('cboProduk').setValue(record.get('fs_kd_product'));
				Ext.getCmp('txtProduk').setValue(record.get('fs_nm_product'));
				Ext.getCmp('cboRangka').setValue(record.get('fs_rangka'));
				Ext.getCmp('txtMesin').setValue(record.get('fs_mesin'));
				Ext.getCmp('txtReg').setValue(record.get('fs_register'));
				Ext.getCmp('txtCC').setValue(record.get('fs_cc'));
				Ext.getCmp('txtTahun').setValue(record.get('fs_thn'));
				Ext.getCmp('cboWarna').setValue(record.get('fs_kd_warna'));
				Ext.getCmp('txtWarna').setValue(record.get('fs_nm_warna'));
				Ext.getCmp('cboWH').setValue(record.get('fs_kd_wh'));
				Ext.getCmp('txtWH').setValue(record.get('fs_nm_wh'));
				Ext.getCmp('cboRangka2').setValue(record.get('fs_rangka'));
				Ext.getCmp('txtMesin2').setValue(record.get('fs_mesin'));
				Ext.getCmp('txtCC2').setValue(record.get('fs_cc'));
				Ext.getCmp('txtTahun2').setValue(record.get('fs_thn'));
				Ext.getCmp('cboWarna2').setValue(record.get('fs_kd_warna'));
				Ext.getCmp('txtWarna2').setValue(record.get('fs_nm_warna'));
				
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
				grupRangka.load();
				vMask.show();
			}
		}
	});
	
	var cboRangka = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Type a Chassis / Machine',
		fieldLabel: 'Chassis',
		id: 'cboRangka',
		name: 'cboRangka',
		xtype: 'textfield',
		listeners: {
			change: function() {
				Ext.getCmp('txtMesin').setValue('');
				Ext.getCmp('txtReg').setValue('');
				Ext.getCmp('txtCC').setValue(0);
				Ext.getCmp('txtTahun').setValue(Ext.Date.format(new Date(), 'Y'));
				Ext.getCmp('cboWarna').setValue('');
				Ext.getCmp('txtWarna').setValue('');
				Ext.getCmp('cboWH').setValue('');
				Ext.getCmp('txtWH').setValue('');
				Ext.getCmp('cboRangka2').setValue('');
				Ext.getCmp('txtMesin2').setValue('');
				Ext.getCmp('txtCC2').setValue(0);
				Ext.getCmp('txtTahun2').setValue(Ext.Date.format(new Date(), 'Y'));
				Ext.getCmp('cboWarna2').setValue('');
				Ext.getCmp('txtWarna2').setValue('');
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
					winCari2.show();
					winCari2.center();
				}
			}
		}
	};

	var txtMesin = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '80%',
		emptyText: 'Enter a Machine',
		fieldLabel: 'Machine',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtMesin',
		name: 'txtMesin',
		readOnly: true,
		xtype: 'textfield'
	};

	var txtReg = {
		anchor: '100%',
		emptyText: 'Enter a Register',
		fieldLabel: 'Register',
		fieldStyle: 'background-color: #eee; background-image: none;',
		hidden: true,
		id: 'txtReg',
		name: 'txtReg',
		readOnly: true,
		xtype: 'textfield'
	};

	var txtCC = Ext.create('Ext.ux.form.NumericField', {
		afterLabelTextTpl: required,
		allowBlank: false,
		alwaysDisplayDecimals: false,
		anchor: '98%',
		currencySymbol: null,
		decimalPrecision: 0,
		decimalSeparator: '.',
		fieldLabel: 'Cylinder',
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		hideTrigger: true,
		id: 'txtCC',
		keyNavEnabled: false,
		mouseWheelEnabled: false,
		name: 'txtCC',
		readOnly: true,
		thousandSeparator: ',',
		useThousandSeparator: false,
		value: 0,
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtCC').getValue())) {
					Ext.getCmp('txtCC').setValue(0);
				}
				else {
					return value;
				}
			}
		}
	});

	var txtTahun = Ext.create('Ext.ux.form.NumericField', {
		afterLabelTextTpl: required,
		allowBlank: false,
		alwaysDisplayDecimals: false,
		anchor: '100%',
		currencySymbol: null,
		decimalPrecision: 0,
		decimalSeparator: '.',
		fieldLabel: 'Year',
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		hideTrigger: true,
		id: 'txtTahun',
		keyNavEnabled: false,
		labelWidth: 45,
		mouseWheelEnabled: false,
		name: 'txtTahun',
		readOnly: true,
		thousandSeparator: ',',
		useThousandSeparator: false,
		value: Ext.Date.format(new Date(), 'Y'),
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtTahun').getValue())) {
					Ext.getCmp('txtTahun').setValue(0);
				}
				else {
					return value;
				}
			}
		}
	});

	var cboWarna = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Enter a',
		fieldLabel: 'Color',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'cboWarna',
		name: 'cboWarna',
		readOnly: true,
		xtype: 'textfield'
	};

	var txtWarna = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Color',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtWarna',
		name: 'txtWarna',
		readOnly: true,
		xtype: 'textfield'
	};

	var cboWH = {
		anchor: '98%',
		emptyText: 'Enter a',
		fieldLabel: 'WH',
		fieldStyle: 'background-color: #eee; background-image: none;',
		hidden: false,
		id: 'cboWH',
		name: 'cboWH',
		readOnly: true,
		xtype: 'textfield'
	};

	var txtWH = {
		anchor: '100%',
		emptyText: 'Warehouse',
		fieldStyle: 'background-color: #eee; background-image: none;',
		hidden: false,
		id: 'txtWH',
		name: 'txtWH',
		readOnly: true,
		xtype: 'textfield'
	};

	var cboRangka2 = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Enter a New Chassis',
		fieldLabel: 'Chassis',
		id: 'cboRangka2',
		name: 'cboRangka2',
		xtype: 'textfield'
	};
	
	var txtMesin2 = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Enter a New Machine',
		fieldLabel: 'Machine',
		id: 'txtMesin2',
		name: 'txtMesin2',
		xtype: 'textfield'
	};

	var txtCC2 = Ext.create('Ext.ux.form.NumericField', {
		afterLabelTextTpl: required,
		allowBlank: false,
		alwaysDisplayDecimals: false,
		anchor: '98%',
		currencySymbol: null,
		decimalPrecision: 0,
		decimalSeparator: '.',
		fieldLabel: 'Cylinder',
		fieldStyle: 'text-align: right;',
		hideTrigger: false,
		id: 'txtCC2',
		keyNavEnabled: false,
		mouseWheelEnabled: false,
		name: 'txtCC2',
		thousandSeparator: ',',
		useThousandSeparator: false,
		value: 0,
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtCC2').getValue())) {
					Ext.getCmp('txtCC2').setValue(0);
				}
				else {
					return value;
				}
			}
		}
	});

	var txtTahun2 = Ext.create('Ext.ux.form.NumericField', {
		afterLabelTextTpl: required,
		allowBlank: false,
		alwaysDisplayDecimals: false,
		anchor: '100%',
		currencySymbol: null,
		decimalPrecision: 0,
		decimalSeparator: '.',
		fieldLabel: 'Year',
		fieldStyle: 'text-align: right;',
		hideTrigger: false,
		id: 'txtTahun2',
		keyNavEnabled: false,
		labelWidth: 45,
		mouseWheelEnabled: false,
		name: 'txtTahun2',
		thousandSeparator: ',',
		useThousandSeparator: false,
		value: Ext.Date.format(new Date(), 'Y'),
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtTahun2').getValue())) {
					Ext.getCmp('txtTahun2').setValue(0);
				}
				else {
					return value;
				}
			}
		}
	});

	var grupWarna = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_vareable','fs_nm_vareable'
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
			url: 'beli/color'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_vareable': Ext.getCmp('cboWarna2').getValue(),
					'fs_nm_vareable': Ext.getCmp('cboWarna2').getValue(),
					'fs_cari': Ext.getCmp('txtCari3').getValue()
				});
			}
		}
	});


	var winGrid3 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupWarna,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Color Code", dataIndex: 'fs_kd_vareable', menuDisabled: true, width: 100},
			{text: "Color Name", dataIndex: 'fs_nm_vareable', menuDisabled: true, width: 380}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Color Code / Color Name',
				id: 'txtCari3',
				name: 'txtCari3',
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
					grupWarna.load();
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
			store: grupWarna,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari3.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboWarna2').setValue(record.get('fs_kd_vareable'));
				Ext.getCmp('txtWarna2').setValue(record.get('fs_nm_vareable'));
				
				winCari3.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
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
				grupWarna.load();
				vMask.show();
			}
		}
	});
	
	var cboWarna2 = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Color',
		id: 'cboWarna2',
		name: 'cboWarna2',
		xtype: 'textfield',
		listeners: {
			change: function() {
				Ext.getCmp('txtWarna2').setValue('');
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

	var txtWarna2 = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Color',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtWarna2',
		name: 'txtWarna2',
		readOnly: true,
		xtype: 'textfield'
	};

	function fnCekSave() {
		if (this.up('form').getForm().isValid()) {
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'produkedit/ceksave',
				params: {
					'fs_kd_product': Ext.getCmp('cboProduk').getValue(),
					'fs_rangka': Ext.getCmp('cboRangka').getValue(),
					'fs_mesin': Ext.getCmp('txtMesin').getValue(),
					'fs_rangka2': Ext.getCmp('cboRangka2').getValue(),
					'fs_mesin2': Ext.getCmp('txtMesin2').getValue()
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
			url: 'produkedit/save',
			params: {
				'fs_kd_product': Ext.getCmp('cboProduk').getValue(),
				'fs_rangka': Ext.getCmp('cboRangka').getValue(),
				'fs_mesin': Ext.getCmp('txtMesin').getValue(),
				'fs_register': Ext.getCmp('txtReg').getValue(),
				'fs_cc': Ext.getCmp('txtCC').getValue(),
				'fs_tahun': Ext.getCmp('txtTahun').getValue(),
				'fs_kd_warna': Ext.getCmp('cboWarna').getValue(),
				'fs_rangka2': Ext.getCmp('cboRangka2').getValue(),
				'fs_mesin2': Ext.getCmp('txtMesin2').getValue(),
				'fs_cc2': Ext.getCmp('txtCC2').getValue(),
				'fs_tahun2': Ext.getCmp('txtTahun2').getValue(),
				'fs_kd_warna2': Ext.getCmp('cboWarna2').getValue(),
				'fs_nm_warna2': Ext.getCmp('txtWarna2').getValue()
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
	
	var winFilter = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: false,
		frame: false,
		height: 100,
		layout: 'fit',
		minHeight: 100,
		maxHeight: 100,
		minWidth: 350,
		maxWidth: 350,
		title: 'Enter Date Filter',
		width: 350,
		items: [
			Ext.create('Ext.form.Panel', {
				bodyStyle: 'padding: 5px; background-color: '.concat(gBasePanel),
				fieldDefaults: {
					labelAlign: 'right',
					labelSeparator: '',
					msgTarget: 'side'
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
							afterLabelTextTpl: required,
							allowBlank: false,
							anchor: '95%',
							editable: true,
							fieldLabel: 'Date',
							format: 'd-m-Y',
							id: 'txtTgl',
							labelWidth: 40,
							maskRe: /[0-9-]/,
							minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
							name: 'txtTgl',
							value: new Date(),
							xtype: 'datefield'
						}]
					},{
						flex: 1.2,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							afterLabelTextTpl: required,
							allowBlank: false,
							anchor: '100%',
							editable: true,
							fieldLabel: 'Date Up To',
							format: 'd-m-Y',
							id: 'txtTgl2',
							labelWidth: 80,
							maskRe: /[0-9-]/,
							minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
							name: 'txtTgl2',
							value: new Date(),
							xtype: 'datefield'
						}]
					}]
				}]
			})
		],
		buttons: [{
			text: 'OK',
			handler: function() {
				vMask.hide();
				winFilter.hide();
				fnExportToExcel();
			}
		},{
			text: 'Cancel',
			handler: function() {
				vMask.hide();
				winFilter.hide();
			}
		}]
	});

	function fnExportToExcel() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 180000,
			url: 'produkedit/excel_histori',
			params: {
				'fd_refno': Ext.Date.format(Ext.getCmp('txtTgl').getValue(), 'Ymd'),
				'fd_refno2': Ext.Date.format(Ext.getCmp('txtTgl2').getValue(), 'Ymd'),
				'fd_tgl': Ext.Date.format(Ext.getCmp('txtTgl').getValue(), 'd-m-Y'),
				'fd_tgl2': Ext.Date.format(Ext.getCmp('txtTgl2').getValue(), 'd-m-Y')
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
		Ext.getCmp('cboProduk').setValue('');
		Ext.getCmp('txtProduk').setValue('');
		Ext.getCmp('cboRangka').setValue('');
		Ext.getCmp('txtMesin').setValue('');
		Ext.getCmp('txtReg').setValue('');
		Ext.getCmp('txtCC').setValue(0);
		Ext.getCmp('txtTahun').setValue(Ext.Date.format(new Date(), 'Y'));
		Ext.getCmp('cboWarna').setValue('');
		Ext.getCmp('txtWarna').setValue('');
		Ext.getCmp('cboWH').setValue('');
		Ext.getCmp('txtWH').setValue('');
		Ext.getCmp('cboRangka2').setValue('');
		Ext.getCmp('txtMesin2').setValue('');
		Ext.getCmp('txtCC2').setValue(0);
		Ext.getCmp('txtTahun2').setValue(Ext.Date.format(new Date(), 'Y'));
		Ext.getCmp('cboWarna2').setValue('');
		Ext.getCmp('txtWarna2').setValue('');
		Ext.getCmp('txtTgl').setValue(new Date());
		Ext.getCmp('txtTgl2').setValue(new Date());
	}

	var frmProdukEdit = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Editing Product Form',
		width: 800,
		fieldDefaults: {
			labelAlign: 'right',
			labelSeparator: '',
			labelWidth: 50,
			msgTarget: 'side'
		},
		items: [{
			style: 'padding: 5px;',
			xtype: 'fieldset',
			items: [{
				anchor: '60%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						cboProduk
					]
				},{
					flex: 1.5,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtProduk
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
					anchor: '98%',
					style: 'padding: 5px;',
					title: 'Old Value',
					xtype: 'fieldset',
					items: [
						cboRangka,
						txtMesin,
						txtReg,
					{
						anchor: '60%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtCC
							]
						},{
							flex: 0.9,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtTahun
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
								cboWarna,
								cboWH
							]
						},{
							flex: 1.6,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtWarna,
								txtWH
							]
						}]
					}]
				}]
			},{
				flex: 1,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					style: 'padding: 5px;',
					title: 'New Value',
					xtype: 'fieldset',
					items: [
						cboRangka2,
						txtMesin2,
					{
						anchor: '60%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtCC2
							]
						},{
							flex: 0.9,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtTahun2
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
								cboWarna2
							]
						},{
							flex: 1.6,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtWarna2
							]
						}]
					}
					]
				}]
			}]
		}],
		buttons: [{
			text: 'Save',
			handler: fnCekSave
		},{
			text: 'History',
			handler: function() {
				vMask.show();
				winFilter.show();
			}
		},{
			text: 'Reset',
			handler: fnReset
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmProdukEdit
	});
	
	function fnMaskShow() {
		frmProdukEdit.mask('Please wait...');
	}

	function fnMaskHide() {
		frmProdukEdit.unmask();
	}
	
	frmProdukEdit.render(Ext.getBody());
	Ext.get('loading').destroy();
});
