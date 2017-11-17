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

	function gridTooltipSearch(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Double click on record to choose',
			target: view.el,
			trackMouse: true
		});
	}

	var cekTgl = {
		checked: true,
		fieldLabel : '',
		id: 'cekTgl',
		inputValue: '0',
		name: 'cekPilih',
		xtype: 'radiofield',
		listeners: {
			change: function(value) {
				if (Ext.getCmp('cekTgl').getValue() == '1') {
					Ext.getCmp('txtTgl').setReadOnly(false);
					Ext.getCmp('txtTgl').setFieldStyle('background-color: white; background-image: url('.concat(gBaseIMG,'text-bg.gif);'));
					Ext.getCmp('txtTgl2').setReadOnly(false);
					Ext.getCmp('txtTgl2').setFieldStyle('background-color: white; background-image: url('.concat(gBaseIMG,'text-bg.gif);'));
					Ext.getCmp('cekAllWH').setReadOnly(false);
					Ext.getCmp('cboCust').setReadOnly(true);
					Ext.getCmp('cboCust').setFieldStyle('background-color: #eee; background-image: none;');
					Ext.getCmp('cboRangka').setReadOnly(true);
					Ext.getCmp('cboRangka').setFieldStyle('background-color: #eee; background-image: none;');
				}
				else {
					return value;
				}
			}
		}
	};

	var txtTgl = {
		anchor: '98%',
		editable: true,
		fieldLabel: 'Date',
		format: 'd-m-Y',
		id: 'txtTgl',
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
		name: 'txtTgl',
		value: new Date(),
		xtype: 'datefield'
	};

	var txtTgl2 = {
		anchor: '100%',
		editable: true,
		fieldLabel: 'To',
		format: 'd-m-Y',
		id: 'txtTgl2',
		labelWidth: 20,
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
		name: 'txtTgl2',
		value: new Date(),
		xtype: 'datefield'
	};

	var cekAllWH = {
		boxLabel: 'All Department',
		checked: false,
		id: 'cekAllWH',
		name: 'cekAllWH',
		xtype: 'checkboxfield'
	};

	var cekCust = {
		fieldLabel : '',
		id: 'cekCust',
		inputValue: '1',
		name: 'cekPilih',
		xtype: 'radiofield',
		listeners: {
			change: function(value) {
				if (Ext.getCmp('cekCust').getValue() == '1') {
					Ext.getCmp('txtTgl').setReadOnly(true);
					Ext.getCmp('txtTgl').setFieldStyle('background-color: #eee; background-image: none;');
					Ext.getCmp('txtTgl2').setReadOnly(true);
					Ext.getCmp('txtTgl2').setFieldStyle('background-color: #eee; background-image: none;');
					Ext.getCmp('cekAllWH').setReadOnly(true);
					Ext.getCmp('cboCust').setReadOnly(false);
					Ext.getCmp('cboCust').setFieldStyle('background-color: white; background-image: url('.concat(gBaseIMG,'text-bg.gif);'));
					Ext.getCmp('cboRangka').setReadOnly(true);
					Ext.getCmp('cboRangka').setFieldStyle('background-color: #eee; background-image: none;');
				}
				else {
					return value;
				}
			}
		}
	};

	var grupCust = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_code','fs_kd_cust','fs_count','fs_nm_code','fs_idcard','fs_addr'],
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
			url: 'jual/cust_kode'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_code': Ext.getCmp('cboCust').getValue(),
					'fs_nm_code': Ext.getCmp('cboCust').getValue(),
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
		store: grupCust,
		columns: [	
			{xtype: 'rownumberer', width: 45},
			{text: "Code", dataIndex: 'fs_code', menuDisabled: true, width: 80},
			{text: "Code", dataIndex: 'fs_kd_cust', menuDisabled: true, hidden: true},
			{text: "Count", dataIndex: 'fs_count', menuDisabled: true, hidden: true},
			{text: "Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 200},
			{text: "IdCard", dataIndex: 'fs_idcard', menuDisabled: true, hidden: true},
			{text: "Address", dataIndex: 'fs_addr', menuDisabled: true, width: 200}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Code / Name',
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
					grupCust.load();
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
			store: grupCust,
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
				Ext.getCmp('cboCust').setValue(record.get('fs_code'));
				Ext.getCmp('txtCustCd').setValue(record.get('fs_kd_cust'));
				Ext.getCmp('txtCustCount').setValue(record.get('fs_count'));
				Ext.getCmp('txtCust').setValue(record.get('fs_nm_code'));
				
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
				grupCust.load();
				vMask.show();
			}
		}
	});

	var cboCust = {
		anchor: '98%',
		emptyText: "Select a",
		fieldLabel: 'Customer',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'cboCust',
		name: 'cboCust',
		readOnly: true,
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtCust').setValue('');
				Ext.getCmp('txtCustCd').setValue('');
				Ext.getCmp('txtCustCount').setValue('');
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

	var txtCustCd = {
		anchor: '98%',
		fieldLabel: 'Code',
		hidden: true,
		id: 'txtCustCd',
		name: 'txtCustCd',
		xtype: 'textfield'
	};

	var txtCustCount = {
		anchor: '98%',
		fieldLabel: 'Count',
		hidden: true,
		id: 'txtCustCount',
		name: 'txtCustCount',
		xtype: 'textfield'
	};

	var txtCust = {
		anchor: '100%',
		emptyText: "Customer",
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtCust',
		name: 'txtCust',
		readOnly: true,
		xtype: 'textfield'
	};

	var cekRangka = {
		fieldLabel : '',
		id: 'cekRangka',
		inputValue: '2',
		name: 'cekPilih',
		xtype: 'radiofield',
		listeners: {
			change: function(value) {
				if (Ext.getCmp('cekRangka').getValue() == '1') {
					Ext.getCmp('txtTgl').setReadOnly(true);
					Ext.getCmp('txtTgl').setFieldStyle('background-color: #eee; background-image: none;');
					Ext.getCmp('txtTgl2').setReadOnly(true);
					Ext.getCmp('txtTgl2').setFieldStyle('background-color: #eee; background-image: none;');
					Ext.getCmp('cekAllWH').setReadOnly(true);
					Ext.getCmp('cboCust').setReadOnly(true);
					Ext.getCmp('cboCust').setFieldStyle('background-color: #eee; background-image: none;');
					Ext.getCmp('cboRangka').setReadOnly(false);
					Ext.getCmp('cboRangka').setFieldStyle('background-color: white; background-image: url('.concat(gBaseIMG,'text-bg.gif);'));
				}
				else {
					return value;
				}
			}
		}
	};

	var grupRangka = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_rangka','fs_mesin','fs_thn',
			'fs_cc','fs_kd_warna','fs_nm_warna',
			'fs_register','fs_kd_product','fs_nm_product'
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
			url: 'infojual/rangka'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
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
		width: 750,
		sortableColumns: false,
		store: grupRangka,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Product Code", dataIndex: 'fs_kd_product', menuDisabled: true, width: 125},
			{text: "Product", dataIndex: 'fs_nm_product', menuDisabled: true, width: 125},
			{text: "Chassis", dataIndex: 'fs_rangka', menuDisabled: true, width: 150},
			{text: "Machine", dataIndex: 'fs_mesin', menuDisabled: true, width: 100},
			{text: "Year", dataIndex: 'fs_thn', align: 'center', menuDisabled: true, width: 50},
			{text: "Cylinder", dataIndex: 'fs_cc', align: 'center', menuDisabled: true, width: 50},
			{text: "Color Cd", dataIndex: 'fs_kd_warna', menuDisabled: true, hidden: true},
			{text: "Color", dataIndex: 'fs_nm_warna', menuDisabled: true, width: 80},
			{text: "Register", dataIndex: 'fs_register', menuDisabled: true, hidden: true}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Chassis / Machine',
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
				Ext.getCmp('cboRangka').setValue(record.get('fs_rangka'));
				
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
		anchor: '98%',
		emptyText: 'Select a Chassis Number',
		fieldLabel: 'Chassis',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'cboRangka',
		name: 'cboRangka',
		readOnly: true,
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
					winCari2.show();
					winCari2.center();
				}
			}
		}
	};

	var txtTgl3 = {
		anchor: '98%',
		editable: true,
		fieldLabel: 'Date',
		format: 'd-m-Y',
		id: 'txtTgl3',
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
		name: 'txtTgl3',
		value: new Date(),
		xtype: 'datefield'
	};

	var txtTgl4 = {
		anchor: '100%',
		editable: true,
		fieldLabel: 'To',
		format: 'd-m-Y',
		id: 'txtTgl4',
		labelWidth: 20,
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
		name: 'txtTgl4',
		value: new Date(),
		xtype: 'datefield'
	};

	var grupSales = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_code','fs_nm_code','fs_kd_sales','fs_count'],
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
			url: 'jual/sales'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_code': Ext.getCmp('cboSales').getValue(),
					'fs_nm_code': Ext.getCmp('cboSales').getValue(),
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
		store: grupSales,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Salesman Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Salesman Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 380},
			{text: "Code", dataIndex: 'fs_kd_sales', menuDisabled: true, hidden: true},
			{text: "Count", dataIndex: 'fs_count', menuDisabled: true, hidden: true}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Salesman Code / Salesman Name',
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
					grupSales.load();
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
			store: grupSales,
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
				Ext.getCmp('cboSales').setValue(record.get('fs_code'));
				Ext.getCmp('txtSales').setValue(record.get('fs_nm_code'));
				Ext.getCmp('txtSalesCd').setValue(record.get('fs_kd_sales'));
				Ext.getCmp('txtSalesCount').setValue(record.get('fs_count'));
				
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
				grupSales.load();
				vMask.show();
			}
		}
	});

	var cboSales = {
		anchor: '98%',
		emptyText: "Select a",
		fieldLabel: 'From Sales',
		id: 'cboSales',
		name: 'cboSales',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtSales').setValue('');
				Ext.getCmp('txtSalesCd').setValue('');
				Ext.getCmp('txtSalesCount').setValue('');
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

	var txtSalesCd = {
		anchor: '98%',
		fieldLabel: 'Code',
		hidden: true,
		id: 'txtSalesCd',
		name: 'txtSalesCd',
		xtype: 'textfield'
	};

	var txtSalesCount = {
		anchor: '98%',
		fieldLabel: 'Count',
		hidden: true,
		id: 'txtSalesCount',
		name: 'txtSalesCount',
		xtype: 'textfield'
	};

	var txtSales = {
		anchor: '100%',
		emptyText: "Salesman",
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtSales',
		name: 'txtSales',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupSales2 = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_code','fs_nm_code','fs_kd_sales','fs_count'],
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
			url: 'jual/sales'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_code': Ext.getCmp('cboSales2').getValue(),
					'fs_nm_code': Ext.getCmp('cboSales2').getValue(),
					'fs_cari': Ext.getCmp('txtCari4').getValue()
				});
			}
		}
	});


	var winGrid4 =  Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupSales2,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Salesman Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Salesman Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 380},
			{text: "Code", dataIndex: 'fs_kd_sales', menuDisabled: true, hidden: true},
			{text: "Count", dataIndex: 'fs_count', menuDisabled: true, hidden: true}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Salesman Code / Salesman Name',
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
					grupSales2.load();
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
			store: grupSales2,
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
				Ext.getCmp('cboSales2').setValue(record.get('fs_code'));
				Ext.getCmp('txtSales2').setValue(record.get('fs_nm_code'));
				Ext.getCmp('txtSalesCd2').setValue(record.get('fs_kd_sales'));
				Ext.getCmp('txtSalesCount2').setValue(record.get('fs_count'));
				
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
				grupSales2.load();
				vMask.show();
			}
		}
	});

	var cboSales2 = {
		anchor: '98%',
		emptyText: "Select a",
		fieldLabel: 'To Sales',
		id: 'cboSales2',
		name: 'cboSales2',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtSales2').setValue('');
				Ext.getCmp('txtSalesCd2').setValue('');
				Ext.getCmp('txtSalesCount2').setValue('');
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

	var txtSalesCd2 = {
		anchor: '98%',
		fieldLabel: 'Code',
		hidden: true,
		id: 'txtSalesCd2',
		name: 'txtSalesCd2',
		xtype: 'textfield'
	};

	var txtSalesCount2 = {
		anchor: '98%',
		fieldLabel: 'Count',
		hidden: true,
		id: 'txtSalesCount2',
		name: 'txtSalesCount2',
		xtype: 'textfield'
	};

	var txtSales2 = {
		anchor: '100%',
		emptyText: "Salesman",
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtSales2',
		name: 'txtSales2',
		readOnly: true,
		xtype: 'textfield'
	};

	var txtTgl5 = {
		anchor: '98%',
		editable: true,
		fieldLabel: 'Date',
		format: 'd-m-Y',
		id: 'txtTgl5',
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
		name: 'txtTgl5',
		value: new Date(),
		xtype: 'datefield'
	};

	var txtTgl6 = {
		anchor: '100%',
		editable: true,
		fieldLabel: 'To',
		format: 'd-m-Y',
		id: 'txtTgl6',
		labelWidth: 20,
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
		name: 'txtTgl6',
		value: new Date(),
		xtype: 'datefield'
	};

	function fnReset() {
		Ext.getCmp('txtTgl').setValue(new Date());
		Ext.getCmp('txtTgl2').setValue(new Date());
		Ext.getCmp('cboCust').setValue('');
		Ext.getCmp('txtCust').setValue('');
		Ext.getCmp('txtCustCd').setValue('');
		Ext.getCmp('txtCustCount').setValue('');
		Ext.getCmp('cboRangka').setValue('');
		Ext.getCmp('cekTgl').setValue('1');
		Ext.getCmp('cekAllWH').setValue('0');
		Ext.getCmp('cekCust').setValue('0');
		Ext.getCmp('cekRangka').setValue('0');
		pbar.hide();
	}

	function fnReset2() {
		Ext.getCmp('txtTgl3').setValue(new Date());
		Ext.getCmp('txtTgl4').setValue(new Date());
		Ext.getCmp('cboSales').setValue('');
		Ext.getCmp('txtSales').setValue('');
		Ext.getCmp('txtSalesCd').setValue('');
		Ext.getCmp('txtSalesCount').setValue('');
		Ext.getCmp('cboSales2').setValue('');
		Ext.getCmp('txtSales2').setValue('');
		Ext.getCmp('txtSalesCd2').setValue('');
		Ext.getCmp('txtSalesCount2').setValue('');
		pbar2.hide();
	}

	function fnReset3() {
		Ext.getCmp('txtTgl5').setValue(new Date());
		Ext.getCmp('txtTgl6').setValue(new Date());
		pbar3.hide();
	}

	var pbar = Ext.create('Ext.ProgressBar', {
		id: 'pbar',
		width: 600
	}).hide();

	function fnPB() {
		pbar.wait({
			duration: 60000,
			increment: 600,
			interval: 100,
			scope: this,
			fn: function() {
				pbar.hide();
				pbar.reset(true);
			}
		}).show();
	}

	var pbar2 = Ext.create('Ext.ProgressBar', {
		id: 'pbar2',
		width: 600
	}).hide();

	function fnPB2() {
		pbar2.wait({
			duration: 60000,
			increment: 600,
			interval: 100,
			scope: this,
			fn: function() {
				pbar2.hide();
				pbar2.reset(true);
			}
		}).show();
	}

	var pbar3 = Ext.create('Ext.ProgressBar', {
		id: 'pbar3',
		width: 600
	}).hide();

	function fnPB3() {
		pbar3.wait({
			duration: 60000,
			increment: 600,
			interval: 100,
			scope: this,
			fn: function() {
				pbar3.hide();
				pbar3.reset(true);
			}
		}).show();
	}

	function fnPrint() {
		var xPilih = '0';
		var xWH = '0';
		
		fnPB();
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		if (Ext.getCmp('cekTgl').getValue() == '1') {
			xPilih = 0;
		}
		
		if (Ext.getCmp('cekCust').getValue() == '1') {
			xPilih = 1;
		}
		
		if (Ext.getCmp('cekRangka').getValue() == '1') {
			xPilih = 2;
		}
		
		if (Ext.getCmp('cekAllWH').getValue() == '1') {
			xWH = 1;
		}
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'infojual/printinfo',
			params: {
				'fd_tgl': Ext.Date.format(Ext.getCmp('txtTgl').getValue(), 'Ymd'),
				'fd_tgl2': Ext.Date.format(Ext.getCmp('txtTgl2').getValue(), 'Ymd'),
				'fs_kd_wh': xWH,
				'fs_kd_cust': Ext.getCmp('txtCustCd').getValue(),
				'fs_custcount': Ext.getCmp('txtCustCount').getValue(),
				'fs_rangka': Ext.getCmp('cboRangka').getValue(),
				'fs_pilih': xPilih
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
					var xfilexls = xtext.nmfilexls.trim();
					
					vMask.show();
					var winpdf = Ext.create('Ext.window.Window', {
						closable: false,
						draggable: false,
						height: 500,
						layout: 'fit',
						title: 'Daily Sales',
						width: 900,
						items: {
							xtype: 'component',
							autoEl: {
								src: xfile,
								tag: 'iframe'
							}
						},
						buttons: [{
							href: xfilexls,
							hrefTarget: '_blank',
							text: 'Download Excel',
							xtype: 'button'
						},{
							text: 'Exit',
							handler: function() {
								vMask.hide();
								winpdf.hide();
							}
						}]
					}).show();
					pbar.hide();
					pbar.reset(true);
				}
				else {
					pbar.hide();
					pbar.reset(true);
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

	function fnPrint2() {
		fnPB2();
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'infojual/printinfo2',
			params: {
				'fd_tgl3': Ext.Date.format(Ext.getCmp('txtTgl3').getValue(), 'Ymd'),
				'fd_tgl4': Ext.Date.format(Ext.getCmp('txtTgl4').getValue(), 'Ymd'),
				'fs_kd_sales': Ext.getCmp('txtSalesCd').getValue(),
				'fs_salescount': Ext.getCmp('txtSalesCount').getValue(),
				'fs_kd_sales2': Ext.getCmp('txtSalesCd2').getValue(),
				'fs_salescount2': Ext.getCmp('txtSalesCount2').getValue(),
				'fs_nm_sales': Ext.getCmp('txtSales').getValue(),
				'fs_nm_sales2': Ext.getCmp('txtSales2').getValue()
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
					var xfilexls = xtext.nmfilexls.trim();
					
					vMask.show();
					var winpdf2 = Ext.create('Ext.window.Window', {
						closable: false,
						draggable: false,
						height: 500,
						layout: 'fit',
						title: 'Sales Per Salesman',
						width: 900,
						items: {
							xtype: 'component',
							autoEl: {
								src: xfile,
								tag: 'iframe'
							}
						},
						buttons: [{
							href: xfilexls,
							hrefTarget: '_blank',
							text: 'Download Excel',
							xtype: 'button'
						},{
							text: 'Exit',
							handler: function() {
								vMask.hide();
								winpdf2.hide();
							}
						}]
					}).show();
					pbar2.hide();
					pbar2.reset(true);
				}
				else {
					pbar2.hide();
					pbar2.reset(true);
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

	function fnPrint3() {
		fnPB3();
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'infojual/printinfo3',
			params: {
				'fd_tgl5': Ext.Date.format(Ext.getCmp('txtTgl5').getValue(), 'Ymd'),
				'fd_tgl6': Ext.Date.format(Ext.getCmp('txtTgl6').getValue(), 'Ymd')
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
					var xfilexls = xtext.nmfilexls.trim();
					
					vMask.show();
					var winpdf3 = Ext.create('Ext.window.Window', {
						closable: false,
						draggable: false,
						height: 500,
						layout: 'fit',
						title: 'Recap Per Type',
						width: 900,
						items: {
							xtype: 'component',
							autoEl: {
								src: xfile,
								tag: 'iframe'
							}
						},
						buttons: [{
							href: xfilexls,
							hrefTarget: '_blank',
							text: 'Download Excel',
							xtype: 'button'
						},{
							text: 'Exit',
							handler: function() {
								vMask.hide();
								winpdf3.hide();
							}
						}]
					}).show();
					pbar3.hide();
					pbar3.reset(true);
				}
				else {
					pbar3.hide();
					pbar3.reset(true);
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

	var frmInfoJual = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Sales Info Form',
		width: 645,
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
				title: 'Daily Sales',
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
							flex: 0.085,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cekTgl
							]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [{
								anchor: '80%',
								layout: 'hbox',
								xtype: 'container',
								items: [{
									flex: 1.27,
									layout: 'anchor',
									xtype: 'container',
									items: [
										txtTgl
									]
								},{
									flex: 1,
									layout: 'anchor',
									xtype: 'container',
									items: [
										txtTgl2
									]
								}]
							}]
						},{
							flex: 0.7,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cekAllWH
							]
						}]
					},{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 0.05,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cekCust
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
										cboCust,
										txtCustCd,
										txtCustCount
									]
								},{
									flex: 2,
									layout: 'anchor',
									xtype: 'container',
									items: [
										txtCust
									]
								}]
							}]
						}]
					},{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 0.05,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cekRangka
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
										cboRangka
									]
								},{
									flex: 1,
									layout: 'anchor',
									xtype: 'container'
								}]
							}]
						}]
					}]
				}],
				buttons: [{
					text: 'Print',
					handler: fnPrint
				},{
					text: 'Reset',
					handler: fnReset
				}],
				bbar: Ext.create('Ext.toolbar.Toolbar', {
					items: [pbar]
				})
			},{
				bodyStyle: 'background-color: '.concat(gBasePanel),
				border: false,
				frame: false,
				title: 'Per Salesman',
				items: [{
					fieldDefaults: {
						labelAlign: 'right',
						labelSeparator: '',
						labelWidth: 60,
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
							items: [{
								anchor: '98%',
								layout: 'hbox',
								xtype: 'container',
								items: [{
									flex: 1.35,
									layout: 'anchor',
									xtype: 'container',
									items: [
										txtTgl3
									]
								},{
									flex: 1,
									layout: 'anchor',
									xtype: 'container',
									items: [
										txtTgl4
									]
								}]
							}]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container'
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
								cboSales,
								txtSalesCd,
								txtSalesCount
							]
						},{
							flex: 2,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtSales
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
								cboSales2,
								txtSalesCd2,
								txtSalesCount2
							]
						},{
							flex: 2,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtSales2
							]
						}]
					}]
				}],
				buttons: [{
					text: 'Print',
					handler: fnPrint2
				},{
					text: 'Reset',
					handler: fnReset2
				}],
				bbar: Ext.create('Ext.toolbar.Toolbar', {
					items: [pbar2]
				})
			},{
				bodyStyle: 'background-color: '.concat(gBasePanel),
				border: false,
				frame: false,
				title: 'Recap Per Type',
				items: [{
					fieldDefaults: {
						labelAlign: 'right',
						labelSeparator: '',
						labelWidth: 60,
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
							items: [{
								anchor: '98%',
								layout: 'hbox',
								xtype: 'container',
								items: [{
									flex: 1.35,
									layout: 'anchor',
									xtype: 'container',
									items: [
										txtTgl5
									]
								},{
									flex: 1,
									layout: 'anchor',
									xtype: 'container',
									items: [
										txtTgl6
									]
								}]
							}]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container'
						}]
					}]
				}],
				buttons: [{
					text: 'Print',
					handler: fnPrint3
				},{
					text: 'Reset',
					handler: fnReset3
				}],
				bbar: Ext.create('Ext.toolbar.Toolbar', {
					items: [pbar3]
				})
			}]
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmInfoJual
	});
	
	function fnMaskShow() {
		frmInfoJual.mask('Please wait...');
	}

	function fnMaskHide() {
		frmInfoJual.unmask();
	}
	
	frmInfoJual.render(Ext.getBody());
	Ext.get('loading').destroy();
});
