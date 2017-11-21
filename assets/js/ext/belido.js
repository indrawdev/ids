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

	function tglDMY(text) {
		var x = '-';
		return text.substr(0,2).concat(x,text.substr(3,2),x,text.substr(6,4));
	}

	function zeroPad(num, places) {
		var zero = places - num.toString().length + 1;
		return Array(+(zero > 0 && zero)).join('0') + num;
	}

	function gridTooltipSearch(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Double click on record to choose',
			target: view.el,
			trackMouse: true
		});
	}

	function gridTooltipProd(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Double click on the Qty to edit',
			target: view.el,
			trackMouse: true
		});
	}

	function fnProduk() {
		Ext.Ajax.request({
			method: 'POST',
			url: 'produk/norangka',
			params: {
				'fs_kd_product': Ext.getCmp('txtProdAktif').getValue()
			},
			success: function(response) {
				var xtext = Ext.decode(response.responseText);
				
				if (xtext.sukses === true) {
					Ext.getCmp('txtRangka').setValue(xtext.kdrangka);
					Ext.getCmp('txtMesin').setValue(xtext.kdmesin);
					
					if (Ext.getCmp('txtCC').getValue() === 0) {
						Ext.getCmp('txtCC').setValue(xtext.cc);
					}
				}
				else {
					Ext.getCmp('txtRangka').setValue('');
					Ext.getCmp('txtMesin').setValue('');
					Ext.getCmp('txtCC').setValue(0);
				}
			},
			failure: function(response) {
				var xtext = Ext.decode(response.responseText);
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Load default Failed, Connection Failed!!',
					title: 'IDS'
				});
			}
		});
	}

	Ext.define('DataGridImport', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_product', type: 'string'},
			{name: 'fs_nm_product', type: 'string'},
			{name: 'fn_qty', type: 'string'},
			{name: 'ts_thn', type: 'string'},
			{name: 'fs_unit', type: 'string'},
			{name: 'fs_seqno', type: 'string'}
		]
	});

	Ext.define('DataGridDetail', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_rangka', type: 'string'},
			{name: 'fs_mesin', type: 'string'},
			{name: 'fs_cc', type: 'float'},
			{name: 'fs_thn', type: 'float'},
			{name: 'fs_kd_color', type: 'string'},
			{name: 'fs_nm_color', type: 'string'},
			{name: 'fs_kd_wh', type: 'string'},
			{name: 'fs_nm_wh', type: 'string'},
			{name: 'fs_seqno', type: 'string'},
			{name: 'fs_kd_product', type: 'string'}
		]
	});
	
	var grupListImport = Ext.create('Ext.data.Store', {
		autoLoad: false,
		model: 'DataGridImport',
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
			url: 'belido/gridimport'
		}
	});

	var grupListDetail = Ext.create('Ext.data.Store', {
		autoLoad: false,
		model: 'DataGridDetail',
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
			url: 'belido/griddetail'
		}
	});

	var gridListImport = Ext.create('Ext.grid.Panel', {
		anchor: '100%',
		autoDestroy: true,
		height: 343,
		sortableColumns: false,
		store: grupListImport,
		columns: [{
			width: 35,
			xtype: 'rownumberer'
		},{
			dataIndex: 'fs_kd_wh',
			menuDisabled: true,
			text: 'WH',
			flex: 1
		},{
			dataIndex: 'fs_kd_product',
			menuDisabled: true,
			text: 'Product Code',
			flex: 1
		},{
			dataIndex: 'fs_nm_product',
			menuDisabled: true,
			text: 'Product Name',
			flex: 3
		},{
			dataIndex: 'fs_thn',
			menuDisabled: true,
			text: 'Year',
			flex: 0.5
		},{
			dataIndex: 'fn_qty',
			menuDisabled: true,
			text: 'Qty',
			flex: 0.5
		},{
			dataIndex: 'fs_unit',
			menuDisabled: true,
			text: 'Unit',
			flex: 0.5
		},{
			dataIndex: 'fs_seqno',
			menuDisabled: true,
			hidden: true
		}],
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});

	var gridListDetail = Ext.create('Ext.grid.Panel', {
		height: 150,
		hidden: true,
		sortableColumns: false,
		store: grupListDetail,
		columns: [{
			xtype: 'rownumberer',
			width: 30
		},{
			text: 'Chassis',
			dataIndex: 'fs_rangka',
			menuDisabled: true,
			width: 200
		},{
			text: 'Machine',
			dataIndex: 'fs_mesin',
			menuDisabled: true,
			width: 180
		},{
			align: 'center',
			text: 'Cylinder',
			dataIndex: 'fs_cc',
			menuDisabled: true,
			width: 60
		},{
			align: 'center',
			text: 'Year',
			dataIndex: 'fs_thn',
			menuDisabled: true,
			width: 50
		},{
			text: 'Color Code',
			dataIndex: 'fs_kd_color',
			menuDisabled: true,
			width: 75
		},{
			text: 'Color Name',
			dataIndex: 'fs_nm_color',
			menuDisabled: true,
			width: 180
		},{
			text: 'WH Code',
			dataIndex: 'fs_kd_wh',
			menuDisabled: true,
			width: 75
		},{
			text: 'WH Name',
			dataIndex: 'fs_nm_wh',
			menuDisabled: true,
			width: 170
		},{
			text: 'Seqno',
			dataIndex: 'fs_seqno',
			menuDisabled: true
		},{
			text: 'Product',
			dataIndex: 'fs_kd_product',
			menuDisabled: true
		}],
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});

	var grupRefno = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_refno','fd_refno','fs_docno','fd_docno',
			'fs_kd_suppcount','fs_nm_supplier','fs_kd_supp','fs_kd_count'
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
			url: 'belido/refno'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboRefno').getValue(),
					'fs_docno': Ext.getCmp('cboRefno').getValue(),
					'fs_cari': Ext.getCmp('txtCari').getValue()
				});
			}
		}
	});

	var winGrid = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 750,
		sortableColumns: false,
		store: grupRefno,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Ref. No", dataIndex: 'fs_refno', menuDisabled: true, width: 150},
			{text: "Ref. Date", dataIndex: 'fd_refno', menuDisabled: true, width: 80},
			{text: "Doc. No", dataIndex: 'fs_docno', menuDisabled: true, width: 150},
			{text: "Doc. Date", dataIndex: 'fd_docno', menuDisabled: true, width: 80},
			{text: "Supp Cd", dataIndex: 'fs_kd_suppcount', menuDisabled: true, hidden: true},
			{text: "Supplier", dataIndex: 'fs_nm_supplier', menuDisabled: true, width: 220},
			{text: "Supp Cd", dataIndex: 'fs_kd_supp', menuDisabled: true, hidden: true},
			{text: "Supp Count", dataIndex: 'fs_kd_count', menuDisabled: true, hidden: true}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Ref. No / Doc. No',
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
					grupRefno.load();
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
			store: grupRefno,
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
				Ext.getCmp('cboRefno').setValue(record.get('fs_refno'));
				Ext.getCmp('txtRefnodt').setValue(tglDMY(record.get('fd_refno')));
				Ext.getCmp('txtDocno').setValue(record.get('fs_docno'));
				Ext.getCmp('txtDocnodt').setValue(tglDMY(record.get('fd_docno')));
				Ext.getCmp('cboSupp').setValue(record.get('fs_kd_suppcount'));
				Ext.getCmp('txtSupp').setValue(record.get('fs_nm_supplier'));
				Ext.getCmp('txtSuppCd').setValue(record.get('fs_kd_supp'));
				Ext.getCmp('txtCount').setValue(record.get('fs_kd_count'));
				
				grupGridProd.load();
				grupGridReg.load();
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
				grupRefno.load();
				vMask.show();
			}
		}
	});

	var cboRefno = {
		anchor: '95%',
		emptyText: "AUTOMATIC",
		fieldLabel: 'Ref. No',
		id: 'cboRefno',
		name: 'cboRefno',
		xtype: 'textfield',
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

	var txtDocno = {
		anchor: '95%',
		emptyText: 'Enter a Document Number',
		fieldLabel: 'Doc. No',
		id: 'txtDocno',
		name: 'txtDocno',
		xtype: 'textfield'
	};

	var txtRefnodt = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		editable: true,
		fieldLabel: 'Ref. Date',
		format: 'd-m-Y',
		id: 'txtRefnodt',
		labelWidth: 70,
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
		name: 'txtRefnodt',
		value: new Date(),
		xtype: 'datefield'
	};

	var txtDocnodt = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		editable: true,
		fieldLabel: 'Doc. Date',
		format: 'd-m-Y',
		id: 'txtDocnodt',
		labelWidth: 70,
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
		name: 'txtDocnodt',
		value: new Date(),
		xtype: 'datefield'
	};

	var grupSupp = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_code','fs_kd_supp','fs_count','fs_nm_code'],
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
			url: 'beli/supp_kode'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_code': Ext.getCmp('cboSupp').getValue(),
					'fs_nm_code': Ext.getCmp('cboSupp').getValue(),
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
		store: grupSupp,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Code", dataIndex: 'fs_kd_supp', menuDisabled: true, hidden: true},
			{text: "Count", dataIndex: 'fs_count', menuDisabled: true, hidden: true},
			{text: "Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 380}					
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Code / Name',
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
					grupSupp.load();
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
			store: grupSupp,
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
				Ext.getCmp('cboSupp').setValue(record.get('fs_code'));
				Ext.getCmp('txtSuppCd').setValue(record.get('fs_kd_supp'));
				Ext.getCmp('txtCount').setValue(record.get('fs_count'));
				Ext.getCmp('txtSupp').setValue(record.get('fs_nm_code'));
				
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
				grupSupp.load();
				vMask.show();
			}
		}
	});

	var cboSupp = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Supplier',
		id: 'cboSupp',
		name: 'cboSupp',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtSupp').setValue('');
				Ext.getCmp('txtSuppCd').setValue('');
				Ext.getCmp('txtCount').setValue('');
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

	var txtSuppCd = {
		anchor: '98%',
		fieldLabel: 'Code',
		hidden: true,
		id: 'txtSuppCd',
		labelWidth: 60,
		name: 'txtSuppCd',
		xtype: 'textfield'
	};

	var txtCount = {
		anchor: '98%',
		fieldLabel: 'Count',
		hidden: true,
		id: 'txtCount',
		labelWidth: 60,
		name: 'txtCount',
		xtype: 'textfield'
	};

	var txtSupp = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Supplier',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtSupp',
		name: 'txtSupp',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupMetode = Ext.create('Ext.data.Store', {
		autoLoad: true,
		fields: ['fs_kd_vareable','fs_nm_vareable'],
		pageSize: 25,
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'beli/metode'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_vareable': Ext.getCmp('cboBM').getValue(),
					'fs_nm_vareable': Ext.getCmp('cboBM').getValue()
				});
			}
		}
	});

	var cboBM = {
		anchor: '60%',
		displayField: 'fs_nm_vareable',
		editable: false,
		fieldLabel: 'Method',
		fieldStyle: 'text-transform: uppercase;',
		hidden: true,
		id: 'cboBM',
		labelWidth: 60,
		listConfig: {
			maxHeight: 110
		},
		name: 'cboBM',
		store: grupMetode,
		value: '00',
		valueField: 'fs_kd_vareable',
		xtype: 'combobox',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var grupProd = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_product','fs_nm_product',
			'fs_kd_unit','fs_nm_unit'
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
			url: 'beli/product'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_product': Ext.getCmp('cboProd').getValue(),
					'fs_nm_product': Ext.getCmp('cboProd').getValue(),
					'fs_cari': Ext.getCmp('txtCari3').getValue(),
					'fd_refno': Ext.Date.format(Ext.getCmp('txtRefnodt').getValue(), 'Ymd')
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
		store: grupProd,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Product Code", dataIndex: 'fs_kd_product', menuDisabled: true, width: 150},
			{text: "Product Name", dataIndex: 'fs_nm_product', menuDisabled: true, width: 330},
			{text: "Unit Code", dataIndex: 'fs_kd_unit', menuDisabled: true, hidden: true},
			{text: "Unit Name", dataIndex: 'fs_nm_unit', menuDisabled: true, hidden: true}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Product Code / Product Name',
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
					grupProd.load();
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
			store: grupProd,
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
				Ext.getCmp('cboProd').setValue(record.get('fs_kd_product'));
				Ext.getCmp('txtProd').setValue(record.get('fs_nm_product'));
				Ext.getCmp('cboUnit').setValue(record.get('fs_kd_unit'));
				
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
				grupProd.load();
				vMask.show();
			}
		}
	});
	
	var cboProd = {
		anchor: '98%',
		emptyText: 'Select a Product',
		fieldLabel: 'Product',
		id: 'cboProd',
		labelAlign: 'top',
		labelWidth: 45,
		name: 'cboProd',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtProd').setValue('');
				Ext.getCmp('cboUnit').setValue('UNIT');
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
	
	var txtProd = {
		anchor: '98%',
		emptyText: 'Enter a Product Name',
		hidden: true,
		id: 'txtProd',
		name: 'txtProd',
		xtype: 'textfield'
	};
	
	var grupUnit = Ext.create('Ext.data.Store', {
		autoLoad: true,
		fields: ['fs_kd_unit','fs_nm_unit'],
		pageSize: 25,
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'beli/unit'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_product': Ext.getCmp('cboProd').getValue(),
					'fs_kd_unit': Ext.getCmp('cboUnit').getValue(),
					'fs_nm_unit': Ext.getCmp('cboUnit').getValue()
				});
			}
		}
	});

	var cboUnit = {
		anchor: '95%',
		displayField: 'fs_nm_unit',
		editable: false,
		fieldLabel: 'Unit',
		fieldStyle: 'text-transform: uppercase;',
		id: 'cboUnit',
		labelAlign: 'top',
		labelWidth: 35,
		listConfig: {
			maxHeight: 110
		},
		name: 'cboUnit',
		store: grupUnit,
		value: 'UNIT',
		valueField: 'fs_kd_unit',
		xtype: 'combobox',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};
	
	var txtUrutProd = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: false,
		anchor: '95%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		hidden: true,
		hideTrigger: true,
		id: 'txtUrutProd',
		keyNavEnabled: false,
		mouseWheelEnabled: false,
		name: 'txtUrutProd',
		thousandSeparator: ',',
		useThousandSeparator: false,
		value: 0,
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtUrutProd').getValue())) {
					Ext.getCmp('txtUrutProd').setValue(0);
				}
				else {
					return value;
				}
			}
		}
	});
	
	var grupComp = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_kd_comp','fs_nm_comp','fs_nm_db'],
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
			url: 'belido/ambil_comp'
		}
	});

	var winGrid6 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupComp,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupComp,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari6.hide();
				}
			}]
		}),
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Comp Code", dataIndex: 'fs_kd_comp', menuDisabled: true, hidden: true},
			{text: "Company Name", dataIndex: 'fs_nm_comp', menuDisabled: true, width: 330},
			{text: "DB Name", dataIndex: 'fs_nm_db', menuDisabled: true, width: 150}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboComp').setValue(record.get('fs_nm_comp'));
				Ext.getCmp('txtComp').setValue(record.get('fs_kd_comp'));
				Ext.getCmp('txtDB').setValue(record.get('fs_nm_db'));
				
				winCari6.hide();
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
				grupComp.load();
				vMask.show();
			}
		}
	});

	var cboComp = {
		anchor: '98%',
		editable: false,
		emptyText : 'Select a From Company',
		fieldLabel: 'From Company',
		id: 'cboComp',
		labelAlign: 'top',
		labelWidth: 85,
		name: 'cboComp',
		store: grupComp,
		xtype: 'textfield',
		listeners: {
			change: function(combo, value) {
				Ext.getCmp('txtComp').setValue('');
				Ext.getCmp('txtDB').setValue('');
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
	
	var txtComp = {
		anchor: '98%',
		emptyText: 'Comp Code',
		hidden: true,
		id: 'txtComp',
		name: 'txtComp',
		xtype: 'textfield'
	};
	
	var txtDB = {
		anchor: '98%',
		emptyText: 'DB Name',
		hidden: true,
		id: 'txtDB',
		name: 'txtDB',
		xtype: 'textfield'
	};

	var cellEditingProd = Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToEdit: 2
	});

	Ext.define('DataGridProd', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_product', type: 'string'},
			{name: 'fs_nm_product', type: 'string'},
			{name: 'fn_qty', type: 'float'},
			{name: 'fs_kd_unit', type: 'string'},
			{name: 'fs_seqno', type: 'string'}
		]
	});

	var grupGridProd = Ext.create('Ext.data.Store', {
		autoLoad: false,
		model: 'DataGridProd',
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'beli/grid_prod'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboRefno').getValue()
				});
			},
			load: function() {
				var xtotal = grupGridProd.getCount();
				
				if (xtotal > 0) {
					var store = gridProd.getStore();
					var xqty = 0;
					
					store.each(function(record, idx) {
						xqty = parseInt(record.get('fs_seqno'));
					});
					Ext.getCmp('txtUrutProd').setValue(xqty);
					
					gridProd.getSelectionModel().select(0);
				}
			}
		}
	});

	var gridProd = Ext.create('Ext.grid.Panel', {
		anchor: '100%',
		defaultType: 'textfield',
		height: 270,
		sortableColumns: false,
		store: grupGridProd,
		columns: [{
			xtype: 'rownumberer',
			width: 25
		},{
			dataIndex: 'fs_kd_product',
			text: 'Product Code',
			flex: 0.5,
			menuDisabled: true
		},{
			dataIndex: 'fs_nm_product',
			text: 'Product Name',
			flex: 1.25,
			menuDisabled: true
		},{
			align: 'right',
			dataIndex: 'fn_qty',
			text: 'Qty',
			flex: 0.25,
			menuDisabled: true,
			editor: {
				editable: false,
				xtype: 'textfield',
				triggers: {
					cari: {
						cls: 'x-form-search-trigger',
						handler: function() {
							var recordgrid = gridProd.getSelectionModel().getSelection()[0];
							var xKdProd = recordgrid.get('fs_kd_product');
							var xNmProd = recordgrid.get('fs_nm_product');
							var xSeqno = recordgrid.get('fs_seqno');
							
							var xtext = xKdProd + ' - ' + xNmProd;
							Ext.getCmp('txtProdAktif').setValue(xKdProd);
							Ext.getCmp('txtProdAktif2').setValue(xtext);
							fnProduk();
							
							grupGridReg.clearFilter();
							var xTotal = grupGridReg.getCount();
							if (xTotal > 0) {
								grupGridReg.filterBy(function(record) {
									if (record.get('fs_kd_product').trim() == xKdProd.trim() &&
										record.get('fs_seqno').trim() == xSeqno.trim()) {
										return true;
									}
									else {
										return false;
									}
								});
								gridReg.getView().refresh();
							}
							vMask.show();
							winReg.show();
							winReg.center();
						}
					}
				}
			}
		},{
			dataIndex: 'fs_kd_unit',
			text: 'Unit',
			flex: 0.25,
			menuDisabled: true
		},{
			dataIndex: 'fs_seqno',
			text: 'Seqno',
			hidden: true,
			menuDisabled: true
		}],
		listeners: {
			selectionchange: function(view, records) {
				gridProd.down('#removeData').setDisabled(!records.length);
			}
		},
		plugins: [
			cellEditingProd
		],
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			listeners: {
				render: gridTooltipProd
			},
			markDirty: false,
			stripeRows: true
		},
		bbar: [{
			iconCls: 'icon-delete',
			itemId: 'removeData',
			text: 'Delete',
			handler: function() {
				var record = gridProd.getSelectionModel().getSelection()[0];
				
				var xseqno = record.get('fs_seqno');
				var arr_seqno = Array();
				
				grupGridReg.clearFilter();
				var store = gridReg.getStore();
				store.each(function(record, idx) {
					arr_seqno.push(record.get('fs_seqno').trim());
				});
				
				var xtotal = grupGridReg.getCount()-1;
				var xxseqno = '';
				for (i=xtotal;i>=0;i--) {
					xxseqno = arr_seqno[i];
					
					if (xseqno.trim() == xxseqno.trim()) {
						grupGridReg.removeAt(i);
					}
				}
				
				gridReg.getView().refresh();
				
				var sm = gridProd.getSelectionModel();
				cellEditingProd.cancelEdit();
				grupGridProd.remove(sm.getSelection());
				gridProd.getView().refresh();
				if (grupGridProd.getCount() > 0) {
					sm.select(0);
				}
				Ext.getCmp('txtProdAktif').setValue('');
				Ext.getCmp('txtProdAktif2').setValue('');
			},
			disabled: true
		},{
			xtype: 'tbfill',
		},{
			value: '<*Double click on the Qty to edit>',
			xtype: 'displayfield'
		},{
			value: '<*If any, add the "From company" first and then add the "Product">',
			xtype: 'displayfield'
		},{
			fieldLabel: 'Active Product',
			hidden: true,
			id: 'txtProdAktif',
			labelAlign: 'left',
			labelSeparator: ':',
			labelWidth: 90,
			name: 'txtProdAktif',
			value: '',
			xtype: 'displayfield'
		}],
		tbar: [{
			flex: 1.5,
			layout: 'anchor',
			xtype: 'container',
			items: [{
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
							cboProd,
							txtProd
						]
					},{
						flex: 0.25,
						layout: 'anchor',
						xtype: 'container',
						items: [
							cboUnit,
							txtUrutProd
						]
					},{
						flex: 0.25,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							id: 'txtLabel',
							name: 'txtLabel',
							xtype: 'displayfield'
						},{
							xtype: 'buttongroup',
							defaults: {
								scale: 'small'
							},
							items: [{
								iconCls: 'icon-add',
								text: 'Add',
								handler: function() {
									var xtotal = grupGridProd.getCount();
									var xurut = Ext.getCmp('txtUrutProd').getValue() + 1;
									var xprod = Ext.getCmp('cboProd').getValue();
									var xdata = Ext.create('DataGridProd', {
										fs_kd_product: Ext.getCmp('cboProd').getValue(),
										fs_nm_product: Ext.getCmp('txtProd').getValue(),
										fn_qty: '0',
										fs_kd_unit: Ext.getCmp('cboUnit').getValue(),
										fs_seqno: zeroPad(xurut, 6)
									});
									
									var store = gridProd.getStore();
									var xlanjut = true;
									store.each(function(record, idx) {
										var xtext = record.get('fs_kd_product').trim();
										
										if (xtext == xprod) {
											Ext.MessageBox.show({
												buttons: Ext.MessageBox.OK,
												closable: false,
												icon: Ext.Msg.WARNING,
												msg: 'Product already exists, add product cancel!!',
												title: 'IDS'
											});
											xlanjut = false;
										}
										
									});
									if (xlanjut === false) {
										return;
									}
									
									xprod = Ext.getCmp('txtProd').getValue();
									if (xprod.trim() === '') {
										Ext.MessageBox.show({
											buttons: Ext.MessageBox.OK,
											closable: false,
											icon: Ext.Msg.WARNING,
											msg: 'Product is not in the list!',
											title: 'IDS'
										});
										return;
									}
									
									var xunit = Ext.getCmp('cboUnit').getValue();
									if (xunit.trim() === '') {
										Ext.MessageBox.show({
											buttons: Ext.MessageBox.OK,
											closable: false,
											icon: Ext.Msg.WARNING,
											msg: 'Unit is not in the list!',
											title: 'IDS'
										});
										return;
									}
									
									grupGridProd.insert(xtotal, xdata);
									Ext.getCmp('cboProd').setValue('');
									Ext.getCmp('txtProd').setValue('');
									Ext.getCmp('cboUnit').setValue('UNIT');
									Ext.getCmp('txtUrutProd').setValue(xurut);
									
									xtotal = grupGridProd.getCount() - 1;
									gridProd.getSelectionModel().select(xtotal);
								}
							}]
						}]
					}]
				}]
			}]
		},{
			flex: 1.5,
			layout: 'anchor',
			xtype: 'container',
			items: [{
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
							cboComp,
							txtComp,
							txtDB
						]
					},{
						flex: 0.2,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							id: 'txtLabel2',
							name: 'txtLabel2',
							xtype: 'displayfield'
						},{
							xtype: 'buttongroup',
							defaults: {
								scale: 'small'
							},
							items: [{
								iconCls: 'icon-add',
								text: 'Add',
								handler: function() {
									xComp = Ext.getCmp('txtComp').getValue();
									xDB = Ext.getCmp('txtDB').getValue();
									if (xComp.trim() === '' || xDB.trim() === '') {
										Ext.MessageBox.show({
											buttons: Ext.MessageBox.OK,
											closable: false,
											icon: Ext.Msg.WARNING,
											msg: 'From Company is empty!',
											title: 'IDS'
										});
										return;
									}
									grupGridProd2.load();
								}
							}]
						}]
					}]
				}]
			}]
		}]
	});

	var grupGridProd2 = Ext.create('Ext.data.Store', {
		autoLoad: false,
		model: 'DataGridProd',
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'belido/grid_prod2'
		},
		listeners: {
			beforeload: function(store) {
				grupGridProd2.removeAll();
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_comp': Ext.getCmp('txtComp').getValue(),
					'fs_nm_db': Ext.getCmp('txtDB').getValue()
				});
			},
			load: function() {
				var xArrKdProd = Array();
				var xArrNmProd = Array();
				var xArrQty = Array();
				var xArrUnit = Array();
				var xArrSeqno = Array();
				
				grupGridProd.removeAll();
				
				var xStore = gridProd2.getStore();
				xStore.each(function(record) {
					xArrKdProd.push(record.get('fs_kd_product').trim());
					xArrNmProd.push(record.get('fs_nm_product').trim());
					xArrQty.push(record.get('fn_qty'));
					xArrUnit.push(record.get('fs_kd_unit'));
					xArrSeqno.push(record.get('fs_seqno').trim());
				});
				
				var xTotal = grupGridProd2.getCount();
				if (xTotal > 0) {
					for (i=0;i<xTotal;i++) {
						
						xData = Ext.create('DataGridProd', {
							fs_kd_product: xArrKdProd[i],
							fs_nm_product: xArrNmProd[i],
							fn_qty: xArrQty[i],
							fs_kd_unit: xArrUnit[i],
							fs_seqno: xArrSeqno[i]
						});
						
						grupGridProd.insert(xTotal, xData);
					}
					grupGridProd.sort([ {
							property : 'fs_seqno',
							direction: 'ASC'
						}
					]);
					gridProd.getView().refresh();
					xTotal = grupGridProd.getCount();
					Ext.getCmp('txtUrutProd').setValue(xTotal);
					
					grupGridProd2.removeAll();
					
					grupGridReg2.load();
				}
			}
		}
	});

	var gridProd2 = Ext.create('Ext.grid.Panel', {
		anchor: '100%',
		height: 150,
		hidden: true,
		sortableColumns: false,
		store: grupGridProd2,
		columns: [{
			xtype: 'rownumberer',
			width: 25
		},{
			dataIndex: 'fs_kd_product',
			text: 'Product Code',
			flex: 0.5,
			menuDisabled: true
		},{
			dataIndex: 'fs_nm_product',
			text: 'Product Name',
			flex: 1.25,
			menuDisabled: true
		},{
			align: 'right',
			dataIndex: 'fn_qty',
			text: 'Qty',
			flex: 0.25,
			menuDisabled: true
		},{
			dataIndex: 'fs_kd_unit',
			text: 'Unit',
			flex: 0.25,
			menuDisabled: true
		},{
			dataIndex: 'fs_seqno',
			text: 'Seqno',
			menuDisabled: true
		}],
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false,
			stripeRows: true
		}
	});

	var grupColor = Ext.create('Ext.data.Store', {
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
					'fs_kd_vareable': Ext.getCmp('cboColor').getValue(),
					'fs_nm_vareable': Ext.getCmp('cboColor').getValue()
				});
			}
		}
	});

	var winGrid4 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupColor,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupColor,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari4.hide();
				}
			}]
		}),
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Color Code", dataIndex: 'fs_kd_vareable', menuDisabled: true, width: 100},
			{text: "Color Name", dataIndex: 'fs_nm_vareable', menuDisabled: true, width: 380}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboColor').setValue(record.get('fs_kd_vareable'));
				Ext.getCmp('txtColor').setValue(record.get('fs_nm_vareable'));
				
				winCari4.hide();
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
				vMask2.hide();
			},
			beforeshow: function() {
				grupColor.load();
				vMask2.show();
			}
		}
	});

	var grupWH = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_code','fs_nm_code'],
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
			url: 'beli/wh'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_code': Ext.getCmp('cboWH').getValue(),
					'fs_nm_code': Ext.getCmp('cboWH').getValue()
				});
			}
		}
	});

	var winGrid5 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupWH,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupWH,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari5.hide();
				}
			}]
		}),
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Warehouse Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Warehouse Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 380}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboWH').setValue(record.get('fs_code'));
				Ext.getCmp('txtWH').setValue(record.get('fs_nm_code'));
				
				winCari5.hide();
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
				vMask2.hide();
			},
			beforeshow: function() {
				grupWH.load();
				vMask2.show();
			}
		}
	});

	// DUPLICATE WAREHOUSE
	var winGrid7 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupWH,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupWH,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari7.hide();
				}
			}]
		}),
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Warehouse Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Warehouse Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 380}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboWH2').setValue(record.get('fs_nm_code'));
				Ext.getCmp('txtWH2').setValue(record.get('fs_code'));
				
				winCari7.hide();
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
				vMask3.hide();
			},
			beforeshow: function() {
				grupWH.load();
				vMask3.show();
			}
		}
	});

	var cellEditingReg = Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToEdit: 2
	});

	Ext.define('DataGridReg', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_rangka', type: 'string'},
			{name: 'fs_mesin', type: 'string'},
			{name: 'fs_cc', type: 'float'},
			{name: 'fs_thn', type: 'float'},
			{name: 'fs_kd_color', type: 'string'},
			{name: 'fs_nm_color', type: 'string'},
			{name: 'fs_kd_wh', type: 'string'},
			{name: 'fs_nm_wh', type: 'string'},
			{name: 'fs_seqno', type: 'string'},
			{name: 'fs_kd_product', type: 'string'}
		]
	});

	var grupGridReg = Ext.create('Ext.data.Store', {
		autoLoad: false,
		model: 'DataGridReg',
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'beli/grid_reg'
		},
		listeners: {
			beforeload: function(store) {
				grupGridReg.clearFilter();
				grupGridReg.removeAll();
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboRefno').getValue()
				});
			}
		}
	});

	var gridReg = Ext.create('Ext.grid.Panel', {
		height: 350,
		sortableColumns: false,
		store: grupGridReg,
		columns: [{
			xtype: 'rownumberer',
			width: 30
		},{
			text: 'Chassis',
			dataIndex: 'fs_rangka',
			menuDisabled: true,
			width: 200
		},{
			text: 'Machine',
			dataIndex: 'fs_mesin',
			menuDisabled: true,
			width: 180
		},{
			align: 'center',
			text: 'Cylinder',
			dataIndex: 'fs_cc',
			menuDisabled: true,
			width: 60
		},{
			align: 'center',
			text: 'Year',
			dataIndex: 'fs_thn',
			menuDisabled: true,
			width: 50
		},{
			text: 'Color Code',
			dataIndex: 'fs_kd_color',
			hidden: true,
			menuDisabled: true,
			width: 75
		},{
			text: 'Color Name',
			dataIndex: 'fs_nm_color',
			menuDisabled: true,
			width: 180
		},{
			text: 'WH Code',
			dataIndex: 'fs_kd_wh',
			hidden: true,
			menuDisabled: true,
			width: 75
		},{
			text: 'WH Name',
			dataIndex: 'fs_nm_wh',
			menuDisabled: true,
			width: 170
		},{
			text: 'Seqno',
			dataIndex: 'fs_seqno',
			hidden: true,
			menuDisabled: true
		},{
			text: 'Product',
			dataIndex: 'fs_kd_product',
			hidden: true,
			menuDisabled: true
		}],
		listeners: {
			selectionchange: function(view, records) {
				gridReg.down('#removeData').setDisabled(!records.length);
			}
		},
		plugins: [
			cellEditingReg
		],
		bbar: [{
			iconCls: 'icon-delete',
			text: 'Delete All',
			xtype: 'button',
			handler: function() {
				grupGridReg.removeAll();
				
				var xprod = Ext.getCmp('txtProdAktif').getValue();
				var xQty = 0;
				store = gridReg.getStore();
				store.each(function(record, idx) {
					if (xprod.trim() == record.get('fs_kd_product').trim()) {
						xQty = xQty + 1;
					}
				});
				
				store = gridProd.getStore();
				store.each(function(record, idx) {
					if (xprod.trim() == record.get('fs_kd_product').trim()) {
						record.set('fn_qty',xQty);
					}
				});
				gridProd.getView().refresh();
			}
		},{
			xtype: 'tbfill'
		},{
			fieldLabel: '',
			id: 'txtProdAktif2',
			labelAlign: 'left',
			labelSeparator: '',
			labelWidth: 90,
			name: 'txtProdAktif2',
			value: '',
			xtype: 'displayfield'
		}],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '95%',
				emptyText: 'Enter a Chassis',
				fieldLabel: 'Chassis',
				labelAlign: 'top',
				labelSeparator: '',
				id: 'txtRangka',
				name: 'txtRangka',
				xtype: 'textfield'
			}]
		},{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '95%',
				emptyText: 'Enter a Machine',
				fieldLabel: 'Machine',
				labelAlign: 'top',
				labelSeparator: '',
				id: 'txtMesin',
				name: 'txtMesin',
				xtype: 'textfield'
			}]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: [
				Ext.create('Ext.ux.form.NumericField', {
					alwaysDisplayDecimals: false,
					anchor: '95%',
					currencySymbol: null,
					decimalPrecision: 2,
					decimalSeparator: '.',
					fieldLabel: 'Cylinder',
					fieldStyle: 'text-align: right;',
					hideTrigger: false,
					id: 'txtCC',
					keyNavEnabled: true,
					labelAlign: 'top',
					labelSeparator: '',
					maxLength: 4,
					mouseWheelEnabled: false,
					name: 'txtCC',
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
				})
			]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: [
				Ext.create('Ext.ux.form.NumericField', {
					alwaysDisplayDecimals: false,
					anchor: '95%',
					currencySymbol: null,
					decimalPrecision: 2,
					decimalSeparator: '.',
					fieldLabel: 'Year',
					fieldStyle: 'text-align: right;',
					hideTrigger: false,
					id: 'txtThn',
					keyNavEnabled: true,
					labelAlign: 'top',
					labelSeparator: '',
					maxLength: 4,
					mouseWheelEnabled: false,
					name: 'txtThn',
					thousandSeparator: ',',
					useThousandSeparator: false,
					value: Ext.Date.format(new Date(), 'Y'),
					listeners: {
						change: function(value) {
							if (Ext.isEmpty(Ext.getCmp('txtThn').getValue())) {
								Ext.getCmp('txtThn').setValue(Ext.Date.format(new Date(), 'Y'));
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
				emptyText: 'Select a Color',
				fieldLabel: 'Color',
				id: 'cboColor',
				labelAlign: 'top',
				labelSeparator: '',
				name: 'cboColor',
				xtype: 'textfield',
				listeners: {
					change: function(field, newValue) {
						Ext.getCmp('txtColor').setValue('');
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
			},{
				anchor: '95%',
				emptyText: 'Enter a Color Name',
				hidden: true,
				id: 'txtColor',
				name: 'txtColor',
				xtype: 'textfield'
			}]
		},{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '95%',
				emptyText: 'Select a Warehouse',
				fieldLabel: 'Warehouse',
				id: 'cboWH',
				labelAlign: 'top',
				labelSeparator: '',
				name: 'cboWH',
				xtype: 'textfield',
				listeners: {
					change: function(field, newValue) {
						Ext.getCmp('txtWH').setValue('');
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
			},{
				anchor: '95%',
				emptyText: 'Enter a Warehouse Name',
				hidden: true,
				id: 'txtWH',
				name: 'txtWH',
				xtype: 'textfield'
			}]
		},{
			xtype: 'buttongroup',
			columns: 1,
			defaults: {
				scale: 'small'
			},
			items: [{
				iconCls: 'icon-add',
				text: 'Add',
				handler: function() {
					var recordgrid = gridProd.getSelectionModel().getSelection()[0];
					var xprod = Ext.getCmp('txtProdAktif').getValue();
					if (recordgrid === undefined || xprod.trim() === '') {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.Msg.WARNING,
							msg: 'Please select a product from the list!',
							title: 'IDS'
						});
						return;
					}
					
					xprod = recordgrid.get('fs_kd_product');
					var xseqno = recordgrid.get('fs_seqno');
					
					var xtotal = grupGridReg.getCount();
					var xrangka = Ext.getCmp('txtRangka').getValue();
					var xmesin = Ext.getCmp('txtMesin').getValue();
					var xdata = Ext.create('DataGridReg', {
						fs_rangka: Ext.getCmp('txtRangka').getValue(),
						fs_mesin: Ext.getCmp('txtMesin').getValue(),
						fs_cc: Ext.getCmp('txtCC').getValue(),
						fs_thn: Ext.getCmp('txtThn').getValue(),
						fs_kd_color: Ext.getCmp('cboColor').getValue(),
						fs_nm_color: Ext.getCmp('txtColor').getValue(),
						fs_kd_wh: Ext.getCmp('cboWH').getValue(),
						fs_nm_wh: Ext.getCmp('txtWH').getValue(),
						fs_seqno: xseqno,
						fs_kd_product: xprod
					});
					
					var store = gridReg.getStore();
					var xlanjut = true;
					store.each(function(record, idx) {
						var xtext = record.get('fs_rangka').trim();
						var xtext2 = record.get('fs_mesin').trim();
						
						if (xtext == xrangka) {
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.OK,
								closable: false,
								icon: Ext.Msg.WARNING,
								msg: 'Chassis already exists, add chassis cancel!!',
								title: 'IDS'
							});
							xlanjut = false;
							return;
						}
						
						if (xtext2 == xmesin) {
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.OK,
								closable: false,
								icon: Ext.Msg.WARNING,
								msg: 'Machine already exists, add machine cancel!!',
								title: 'IDS'
							});
							xlanjut = false;
							return;
						}
					});
					if (xlanjut === false) {
						return;
					}
					
					grupGridReg.clearFilter();
					store = gridReg.getStore();
					xlanjut = true;
					store.each(function(record, idx) {
						var xtext = record.get('fs_rangka').trim();
						var xtext2 = record.get('fs_mesin').trim();
						var xtext3 = record.get('fs_seqno').trim();
						var xtext4 = record.get('fs_kd_product').trim();
						var xmsg = '';
						
						if (xtext == xrangka && xtext3 != xseqno) {
							xmsg = 'Chassis already exists in another product "'.concat(xtext4,'", add chassis cancel!!');
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.OK,
								closable: false,
								icon: Ext.Msg.WARNING,
								msg: xmsg,
								title: 'IDS'
							});
							xlanjut = false;
							return;
						}
						
						if (xtext2 == xmesin && xtext3 != xseqno) {
							xmsg = 'Machine already exists in another product "'.concat(xtext4,'", add machine cancel!!');
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.OK,
								closable: false,
								icon: Ext.Msg.WARNING,
								msg: xmsg,
								title: 'IDS'
							});
							xlanjut = false;
							return;
						}
					});
					if (xlanjut === false) {
						return;
					}
					
					var xTotal = grupGridReg.getCount();
					if (xTotal > 0) {
						grupGridReg.filterBy(function(record) {
							if (record.get('fs_kd_product').trim() == xprod.trim() &&
								record.get('fs_seqno').trim() == xseqno.trim()) {
								return true;
							}
							else {
								return false;
							}
						});
					}
					gridReg.getView().refresh();
					
					xrangka = Ext.getCmp('txtRangka').getValue();
					if (xrangka.trim() === '') {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.Msg.WARNING,
							msg: 'Chassis is empty, please fill in advance!',
							title: 'IDS'
						});
						return;
					}
					
					xmesin = Ext.getCmp('txtMesin').getValue();
					if (xmesin.trim() === '') {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.Msg.WARNING,
							msg: 'Machine is empty, please fill in advance!',
							title: 'IDS'
						});
						return;
					}
					
					var xcolor = Ext.getCmp('txtColor').getValue();
					if (xcolor.trim() === '') {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.Msg.WARNING,
							msg: 'Color is not in the list!',
							title: 'IDS'
						});
						return;
					}
					
					var xwh = Ext.getCmp('txtWH').getValue();
					if (xwh.trim() === '') {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.Msg.WARNING,
							msg: 'Warehouse is not in the list!',
							title: 'IDS'
						});
						return;
					}
					
					Ext.Ajax.on('beforerequest', fnMaskShow2);
					Ext.Ajax.on('requestcomplete', fnMaskHide2);
					Ext.Ajax.on('requestexception', fnMaskHide2);
					
					Ext.Ajax.request({
						method: 'POST',
						url: 'beli/cek_reg',
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
							else if (xtext.sukses === true) {
								
								grupGridReg.clearFilter();
								grupGridReg.insert(xtotal, xdata);
								grupGridReg.sort([ {
										property : 'fs_seqno',
										direction: 'ASC'
									},{
										property : 'fs_rangka',
										direction: 'ASC'
									},{
										property : 'fs_mesin',
										direction: 'ASC'
									}
								]);
								xTotal = grupGridReg.getCount();
								if (xTotal > 0) {
									grupGridReg.filterBy(function(record) {
										if (record.get('fs_kd_product').trim() == xprod.trim() &&
											record.get('fs_seqno').trim() == xseqno.trim()) {
											return true;
										}
										else {
											return false;
										}
									});
								}
								gridReg.getView().refresh();
								
								var xQty = 0;
								store = gridReg.getStore();
								store.each(function(record, idx) {
									if (xprod.trim() == record.get('fs_kd_product').trim()) {
										xQty = xQty + 1;
									}
								});
								
								store = gridProd.getStore();
								store.each(function(record, idx) {
									if (xprod.trim() == record.get('fs_kd_product').trim()) {
										record.set('fn_qty',xQty);
									}
								});
								gridProd.getView().refresh();
								
								fnProduk();
								Ext.getCmp('txtThn').setValue(Ext.Date.format(new Date(), 'Y'));
								Ext.getCmp('cboColor').setValue('');
								Ext.getCmp('txtColor').setValue('');
							}
						},
						failure: function(response) {
							var xtext = Ext.decode(response.responseText);
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.OK,
								closable: false,
								icon: Ext.MessageBox.INFO,
								msg: 'Check chassis and machine Failed, Connection Failed!!',
								title: 'IDS'
							});
							fnMaskHide2();
						}
					});
				}
			},{
				iconCls: 'icon-delete',
				itemId: 'removeData',
				text: 'Delete',
				handler: function() {
					var sm = gridReg.getSelectionModel();
					cellEditingReg.cancelEdit();
					grupGridReg.remove(sm.getSelection());
					gridReg.getView().refresh();
					if (grupGridReg.getCount() > 0) {
						sm.select(0);
					}
					
					var xprod = Ext.getCmp('txtProdAktif').getValue();
					var xQty = 0;
					store = gridReg.getStore();
					store.each(function(record, idx) {
						if (xprod.trim() == record.get('fs_kd_product').trim()) {
							xQty = xQty + 1;
						}
					});
					
					store = gridProd.getStore();
					store.each(function(record, idx) {
						if (xprod.trim() == record.get('fs_kd_product').trim()) {
							record.set('fn_qty',xQty);
						}
					});
					gridProd.getView().refresh();
				},
				disabled: true
			}]
		}],
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false,
			stripeRows: true
		}
	});
	
	var winReg = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		// height: 450,
		layout: 'fit',
		resizable: false,
		title: 'Register',
		width: 900,
		items: [
			gridReg
		],
		buttons: [{
			text: 'OK',
			handler: function() {
				vMask.hide();
				winReg.hide();
			}
		}]
	});

	var grupGridReg2 = Ext.create('Ext.data.Store', {
		autoLoad: false,
		model: 'DataGridReg',
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'belido/grid_reg2'
		},
		listeners: {
			beforeload: function(store) {
				var xprod = '';
				var xseqno = '';
				
				var xstore = gridProd.getStore();
				xstore.each(function(record) {
					xprod = xprod +'|'+ record.get('fs_kd_product');
					xseqno = xseqno +'|'+ record.get('fs_seqno');
				});
				
				grupGridReg2.clearFilter();
				grupGridReg2.removeAll();
				
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_comp': Ext.getCmp('txtComp').getValue(),
					'fs_nm_db': Ext.getCmp('txtDB').getValue(),
					'fs_kd_product': xprod,
					'fs_seqno': xseqno
				});
			},
			load: function() {
				var xArrKdProd = Array();
				var xArrRangka = Array();
				var xArrMesin = Array();
				var xArrCC = Array();
				var xArrThn = Array();
				var xArrKdWarna = Array();
				var xArrNmWarna = Array();
				var xArrKdWH = Array();
				var xArrNmWH = Array();
				var xArrSeqno = Array();
				
				grupGridReg.clearFilter();
				grupGridReg.removeAll();
				
				var xStore = gridReg2.getStore();
				xStore.each(function(record) {
					xArrKdProd.push(record.get('fs_kd_product').trim());
					xArrRangka.push(record.get('fs_rangka').trim());
					xArrMesin.push(record.get('fs_mesin').trim());
					xArrCC.push(record.get('fs_cc'));
					xArrThn.push(record.get('fs_thn'));
					xArrKdWarna.push(record.get('fs_kd_color').trim());
					xArrNmWarna.push(record.get('fs_nm_color').trim());
					xArrKdWH.push(record.get('fs_kd_wh').trim());
					xArrNmWH.push(record.get('fs_nm_wh').trim());
					xArrSeqno.push(record.get('fs_seqno').trim());
				});
				
				var xTotal = grupGridReg2.getCount();
				if (xTotal > 0) {
					for (i=0;i<xTotal;i++) {
						
						xData = Ext.create('DataGridReg', {
							fs_rangka: xArrRangka[i],
							fs_mesin: xArrMesin[i],
							fs_cc: xArrCC[i],
							fs_thn: xArrThn[i],
							fs_kd_color: xArrKdWarna[i],
							fs_nm_color: xArrNmWarna[i],
							fs_kd_wh: xArrKdWH[i],
							fs_nm_wh: xArrNmWH[i],
							fs_seqno: xArrSeqno[i],
							fs_kd_product: xArrKdProd[i]
						});
						
						grupGridReg.insert(xTotal, xData);
					}
					gridReg.getView().refresh();
					
					grupGridReg2.removeAll();
					
					Ext.getCmp('cboComp').setValue('');
					Ext.getCmp('txtComp').setValue('');
					Ext.getCmp('txtDB').setValue('');
				}
			}
		}
	});

	var gridReg2 = Ext.create('Ext.grid.Panel', {
		height: 150,
		hidden: true,
		sortableColumns: false,
		store: grupGridReg2,
		columns: [{
			xtype: 'rownumberer',
			width: 30
		},{
			text: 'Chassis',
			dataIndex: 'fs_rangka',
			menuDisabled: true,
			width: 200
		},{
			text: 'Machine',
			dataIndex: 'fs_mesin',
			menuDisabled: true,
			width: 180
		},{
			align: 'center',
			text: 'Cylinder',
			dataIndex: 'fs_cc',
			menuDisabled: true,
			width: 60
		},{
			align: 'center',
			text: 'Year',
			dataIndex: 'fs_thn',
			menuDisabled: true,
			width: 50
		},{
			text: 'Color Code',
			dataIndex: 'fs_kd_color',
			menuDisabled: true,
			width: 75
		},{
			text: 'Color Name',
			dataIndex: 'fs_nm_color',
			menuDisabled: true,
			width: 180
		},{
			text: 'WH Code',
			dataIndex: 'fs_kd_wh',
			menuDisabled: true,
			width: 75
		},{
			text: 'WH Name',
			dataIndex: 'fs_nm_wh',
			menuDisabled: true,
			width: 170
		},{
			text: 'Seqno',
			dataIndex: 'fs_seqno',
			menuDisabled: true
		},{
			text: 'Product',
			dataIndex: 'fs_kd_product',
			menuDisabled: true
		}],
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false,
			stripeRows: true
		}
	});

	var winUp = Ext.create('Ext.window.Window', {
		bodyStyle: 'padding: 5px; background-color: '.concat(gBasePanel),
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		resizable: false,
		title: 'Import Delivery Order',
		width: 700,
		items: [
			Ext.create('Ext.form.Panel', {
				bodyStyle: 'background-color: '.concat(gBasePanel),
				border: false,
				frame: false,
				id: 'frmWinUp',
				name: 'frmWinUp',
				items: [{
					anchor: '100%',
					layout: 'hbox',
					xtype: 'container',
					items: [{
						flex: 1.5,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							afterLabelTextTpl: required,
							allowBlank: false,
							anchor: '95%',
							emptyText: 'Select a Warehouse',
							id: 'cboWH2',
							name: 'cboWH2',
							xtype: 'textfield',
							listeners: {
								change: function(field, newValue) {
									Ext.getCmp('txtWH2').setValue('');
								}
							},
							triggers: {
								reset: {
									cls: 'x-form-clear-trigger',
									handler: function(field) {
										field.setValue('');
										Ext.getCmp('txtWH2').setValue('');
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
						},{
							anchor: '95%',
							emptyText: 'Enter a Warehouse Name',
							hidden: true,
							id: 'txtWH2',
							name: 'txtWH2',
							xtype: 'textfield'
						}]
					},{
						flex: 3,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							afterLabelTextTpl: required,
							allowBlank: false,
							anchor: '98%',
							buttonText: 'Pilih File...',
							emptyText: 'Pilih File Excel berisi Delivery Order',
							id: 'txtFileExcel',
							name: 'txtFileExcel',
							xtype: 'filefield'
						}]
					},{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							allowBlank: false,
							anchor: '98%',
							id: 'txtThnKend',
							name: 'txtThnKend',
							xtype: 'textfield',
							maxLength: 4,
							value: new Date().getFullYear(),
							enforceMaxLength: true
						}]
					},{
						flex: 0.5,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							anchor: '98%',
							text: 'Upload',
							id: 'btnUpload',
							name: 'btnUpload',
							xtype: 'button',
							handler: function() {
								var xForm = Ext.getCmp('frmWinUp').getForm();
								if (xForm.isValid()) {
									xForm.submit({
										url: 'belido/uploadfile',
										waitMsg: 'Mengunggah file anda...',
										success: function(form, action) {
											var result = action.result; 
											var data = result.data;
											var name = data.name;
											var message = Ext.String.format('<b>Message:</b> {0}<br>'+'<b>FileName:</b> {1}', result.msg, name);
											Ext.Msg.alert('Success', message);

											grupListImport.load();
											grupListDetail.load();
											vMask.hide();
										},
										failure: function(form, action) {
											Ext.Msg.alert('Failure', action.result.msg);
											vMask.hide();
										}
									});
								}
							}
						}]
					}]
				},{
					xtype: 'splitter'
				}, 
					gridListImport,
					gridListDetail
				],
				buttons: [{
					text: 'Proses Impor',
					handler: function() {
						fnCekImport();
					}
				},{
					text: 'Keluar',
					handler: function() {
						fnBatal();
						winUp.hide();
					}
				}]
			})
		]
	});
	
	function fnCekSave() {
		if (this.up('form').getForm().isValid()) {
			grupGridReg.clearFilter();
			var xprod = '';
			var xqty = '';
			var xrangka = "'";
			var lxrangka = 0;
			var xmesin = "'";
			var lxmesin = 0;
			
			var store = gridProd.getStore();
			store.each(function(record, idx) {
				xprod = xprod +'|'+ record.get('fs_kd_product');
				xqty = xqty +'|'+ record.get('fn_qty');
			});
			
			store = gridReg.getStore();
			store.each(function(record, idx) {
				xrangka = xrangka +"','"+ record.get('fs_rangka');
				xmesin = xmesin +"','"+ record.get('fs_mesin');
			});
			xrangka = xrangka + "'";
			xmesin = xmesin + "'";
			lxrangka = xrangka.length;
			lxmesin = xmesin.length;
			xrangka = xrangka.substr(3,lxrangka);
			xmesin = xmesin.substr(3,lxmesin);
			
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'belido/ceksave',
				params: {
					'fs_refno': Ext.getCmp('cboRefno').getValue(),
					'fs_kd_product': xprod,
					'fn_qty': xqty,
					'fs_rangka': xrangka,
					'fs_mesin': xmesin
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
		var xprod = '';
		var xnmprod = '';
		var xqty = '';
		var xunit = '';
		var xseqno = '';
		var xprodreg = '';
		var xrangka = '';
		var xmesin = '';
		var xcc = '';
		var xthn = '';
		var xwarna = '';
		var xkdwh = '';
		var xnmwh = '';
		var xseqnoreg = '';
		
		var store = gridProd.getStore();
		store.each(function(record, idx) {
			xprod = xprod +'|'+ record.get('fs_kd_product');
			xnmprod = xnmprod +'|'+ record.get('fs_nm_product');
			xqty = xqty +'|'+ record.get('fn_qty');
			xunit = xunit +'|'+ record.get('fs_kd_unit');
			xseqno = xseqno +'|'+ record.get('fs_seqno');
		});
		
		store = gridReg.getStore();
		store.each(function(record, idx) {
			xprodreg = xprodreg +'|'+ record.get('fs_kd_product');
			xrangka = xrangka +'|'+ record.get('fs_rangka');
			xmesin = xmesin +'|'+ record.get('fs_mesin');
			xcc = xcc +'|'+ record.get('fs_cc');
			xthn = xthn +'|'+ record.get('fs_thn');
			xwarna = xwarna +'|'+ record.get('fs_kd_color');
			xkdwh = xkdwh +'|'+ record.get('fs_kd_wh');
			xnmwh = xnmwh +'|'+ record.get('fs_nm_wh');
			xseqnoreg = xseqnoreg +'|'+ record.get('fs_seqno');
		});
		
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'belido/save',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue(),
				'fd_refno': Ext.Date.format(Ext.getCmp('txtRefnodt').getValue(), 'Ymd'),
				'fs_docno': Ext.getCmp('txtDocno').getValue(),
				'fd_docno': Ext.Date.format(Ext.getCmp('txtDocnodt').getValue(), 'Ymd'),
				'fs_kd_sup': Ext.getCmp('txtSuppCd').getValue(),
				'fs_count': Ext.getCmp('txtCount').getValue(),
				'fs_kd_belimtd': Ext.getCmp('cboBM').getValue(),
				'fs_nm_belimtd': Ext.getCmp('cboBM').getRawValue(),
				'fs_kd_product': xprod,
				'fs_nm_product': xnmprod,
				'fn_qty': xqty,
				'fs_kd_unit': xunit,
				'fs_seqno': xseqno,
				'fs_kd_prodreg': xprodreg,
				'fs_rangka': xrangka,
				'fs_mesin': xmesin,
				'fs_cc': xcc,
				'fs_thn': xthn,
				'fs_kd_color': xwarna,
				'fs_kd_wh': xkdwh,
				'fs_nm_wh': xnmwh,
				'fs_seqnoreg': xseqnoreg
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
		var xrangka = "'";
		var lxrangka = 0;
		var xmesin = "'";
		var lxmesin = 0;
		
		store = gridReg.getStore();
		store.each(function(record, idx) {
			xrangka = xrangka +"','"+ record.get('fs_rangka');
			xmesin = xmesin +"','"+ record.get('fs_mesin');
		});
		xrangka = xrangka + "'";
		xmesin = xmesin + "'";
		lxrangka = xrangka.length;
		lxmesin = xmesin.length;
		xrangka = xrangka.substr(3,lxrangka);
		xmesin = xmesin.substr(3,lxmesin);
		
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'belido/cekremove',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue(),
				'fs_rangka': xrangka,
				'fs_mesin': xmesin
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
			url: 'belido/remove',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue()
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

	function fnPrint() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'beli/print_beli',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue()
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
					
					vMask.show();
					var winpdf = Ext.create('Ext.window.Window', {
						closable: false,
						draggable: false,
						height: 500,
						layout: 'fit',
						title: 'Purchase',
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
								vMask.hide();
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
		Ext.getCmp('cboRefno').setValue('');
		Ext.getCmp('txtRefnodt').setValue(new Date());
		Ext.getCmp('txtDocno').setValue('');
		Ext.getCmp('txtDocnodt').setValue(new Date());
		Ext.getCmp('cboSupp').setValue('');
		Ext.getCmp('txtSupp').setValue('');
		Ext.getCmp('txtSuppCd').setValue('');
		Ext.getCmp('txtCount').setValue('');
		Ext.getCmp('cboBM').setValue('00');
		Ext.getCmp('cboProd').setValue('');
		Ext.getCmp('txtProd').setValue('');
		Ext.getCmp('cboUnit').setValue('UNIT');
		Ext.getCmp('txtUrutProd').setValue('0');
		Ext.getCmp('cboComp').setValue('');
		Ext.getCmp('txtComp').setValue('');
		Ext.getCmp('txtDB').setValue('');
		Ext.getCmp('txtRangka').setValue('');
		Ext.getCmp('txtMesin').setValue('');
		Ext.getCmp('txtCC').setValue('');
		Ext.getCmp('txtThn').setValue(Ext.Date.format(new Date(), 'Y'));
		Ext.getCmp('cboColor').setValue('');
		Ext.getCmp('txtColor').setValue('');
		Ext.getCmp('cboWH').setValue('');
		Ext.getCmp('txtWH').setValue('');
		
		grupGridProd.removeAll();
		grupGridProd2.removeAll();
		gridProd.getView().refresh();
		
		grupGridReg.removeAll();
		grupGridReg2.removeAll();
		gridReg.getView().refresh();
	}

	function fnBatal() {
		Ext.getCmp('txtFileExcel').reset();
		grupListImport.load();
		vMask.hide();
	}

	function fnCekImport() {
		var xTotal = 0;
		var xStore = gridListImport.getStore();
			
		xStore.each(function(record) {
			if (record.get('fn_qty') > 0) {
				xTotal = xTotal + record.get('fn_qty');
			}
		});
			
		if (xTotal <= 0) {
			Ext.MessageBox.show({
				buttons: Ext.MessageBox.OK,
				closable: false,
				icon: Ext.MessageBox.INFO,
				message: 'Data Kosong',
				title: 'IDS'
			});
			vMask2.hide();
			return;
		}

		fnImport();
		fnImportDetail();

		vMask.hide();
		winUp.hide();
	}
	
	function fnImport() {
		var xArrKdProd = Array();
		var xArrNmProd = Array();
		var xArrQty = Array();
		var xArrUnit = Array();
		var xArrSeqno = Array();
				
		grupGridProd.removeAll();
				
		var xStore = gridListImport.getStore();
			xStore.each(function(record) {
				xArrKdProd.push(record.get('fs_kd_product').trim());
				xArrNmProd.push(record.get('fs_nm_product').trim());
				xArrQty.push(record.get('fn_qty'));
				xArrUnit.push(record.get('fs_unit'));
				//xArrSeqno.push(record.get('fs_seqno').trim());
			});
				
		var xTotal = grupListImport.getCount();
		if (xTotal > 0) {
			for (i=0;i<xTotal;i++) {
				xData = Ext.create('DataGridProd', {
					fs_kd_product: xArrKdProd[i],
					fs_nm_product: xArrNmProd[i],
					fn_qty: xArrQty[i],
					fs_kd_unit: xArrUnit[i],
					//fs_seqno: xArrSeqno[i],
				});
						
				grupGridProd.insert(xTotal, xData);
			}

			grupGridProd.sort([ {
				property : 'fs_kd_product',
				direction: 'ASC'
				}
			]);
			
			gridProd.getView().refresh();
			xTotal = grupGridProd.getCount();
			Ext.getCmp('txtUrutProd').setValue(xTotal);
			grupListImport.removeAll();
		}
	}

	function fnImportDetail() {
		var xArrKdProd = Array();
		var xArrRangka = Array();
		var xArrMesin = Array();
		var xArrCC = Array();
		var xArrThn = Array();
		var xArrKdWarna = Array();
		var xArrNmWarna = Array();
		var xArrKdWH = Array();
		var xArrNmWH = Array();
		var xArrSeqno = Array();

		grupGridReg.clearFilter();
		grupGridReg.removeAll();

		var xStore = gridListDetail.getStore();
		xStore.each(function(record) {
			xArrKdProd.push(record.get('fs_kd_product').trim());
			xArrRangka.push(record.get('fs_rangka').trim());
			xArrMesin.push(record.get('fs_mesin').trim());
			xArrCC.push(record.get('fs_cc'));
			xArrThn.push(record.get('fs_thn'));
			xArrKdWarna.push(record.get('fs_kd_color').trim());
			xArrNmWarna.push(record.get('fs_nm_color').trim());
			xArrKdWH.push(record.get('fs_kd_wh').trim());
			xArrNmWH.push(record.get('fs_nm_wh').trim());
			xArrSeqno.push(record.get('fs_seqno').trim());
		});

		var xTotal = grupListDetail.getCount();
		if (xTotal > 0) {
			for (i=0;i<xTotal;i++) {
				xData = Ext.create('DataGridReg', {
					fs_kd_product: xArrKdProd[i],
					fs_rangka: xArrRangka[i],
					fs_mesin: xArrMesin[i],
					fs_cc: xArrCC[i],
					fs_thn: xArrThn[i],
					fs_kd_color: xArrKdWarna[i],
					fs_nm_color: xArrNmWarna[i],
					fs_kd_wh: xArrKdWH[i],
					fs_nm_wh: xArrNmWH[i],
					fs_seqno: xArrSeqno[i]
				});
						
				grupGridReg.insert(xTotal, xData);
			}
			
			gridReg.getView().refresh();

			grupListDetail.removeAll();

			Ext.getCmp('cboComp').setValue('');
			Ext.getCmp('txtComp').setValue('');
			Ext.getCmp('txtDB').setValue('');
		}
	}

	var frmBeliDO = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Delivery Order Form',
		width: 700,
		fieldDefaults: {
			labelAlign: 'right',
			labelSeparator: '',
			labelWidth: 60,
			msgTarget: 'side'
		},
		items: [{
			style: 'padding: 5px;',
			xtype: 'fieldset',
			items: [{
				anchor: '75%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 2,
					layout: 'anchor',
					xtype: 'container',
					items: [
						cboRefno,
						txtDocno
					]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtRefnodt,
						txtDocnodt
					]
				}]
			},{
				anchor: '75%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						cboSupp,
						txtSuppCd,
						txtCount,
						{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [{
									id: 'ImportExcel',
									name: 'ImportExcel',
									text: 'Import Excel',
									anchor: '95%',
									style: 'padding: 3px;',
									xtype: 'button',
									handler: function() {
										grupListImport.load();
										grupListDetail.load();
										//Ext.getCmp('cboTrs').setValue('');
										vMask.show();
										winUp.show();
									}
								}]
							}]
						}
					]
				},{
					flex: 1.7,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtSupp
					]
				}]
			},
				cboBM
			]
		},
			gridProd, {xtype: 'splitter'},
			gridProd2, {xtype: 'splitter'},
			gridReg2
		],
		buttons: [{
			text: 'Save',
			handler: fnCekSave
		},/*{
			text: 'Print',
			handler: fnPrint
		},*/{
			text: 'Reset',
			handler: fnReset
		},{
			text: 'Remove',
			handler: fnCekRemove
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmBeliDO
	});
	
	function fnMaskShow() {
		frmBeliDO.mask('Please wait...');
	}

	function fnMaskHide() {
		frmBeliDO.unmask();
	}

	var vMask2 = new Ext.LoadMask({
		msg: 'Please wait...',
		target: winReg
	});
	
	function fnMaskShow2() {
		winReg.mask('Please wait...');
	}

	function fnMaskHide2() {
		winReg.unmask();
	}

	var vMask3 = new Ext.LoadMask({
		msg: 'Please wait...',
		target: winUp
	});

	function fnMaskShow3() {
		winUp.mask('Please wait...');
	}

	function fnMaskHide3() {
		winUp.unmask();
	}
	
	frmBeliDO.render(Ext.getBody());
	Ext.get('loading').destroy();
});
