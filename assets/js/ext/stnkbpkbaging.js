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
		anchor: '99%',
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
			{text: "Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Code", dataIndex: 'fs_kd_cust', menuDisabled: true, hidden: true},
			{text: "Count", dataIndex: 'fs_count', menuDisabled: true, hidden: true},
			{text: "Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 200},
			{text: "IdCard", dataIndex: 'fs_idcard', menuDisabled: true, hidden: true},
			{text: "Address", dataIndex: 'fs_addr', menuDisabled: true, width: 180}
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
		fieldLabel: 'From Customer',
		id: 'cboCust',
		name: 'cboCust',
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
	
	var grupCust2 = Ext.create('Ext.data.Store', {
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
					'fs_code': Ext.getCmp('cboCust2').getValue(),
					'fs_nm_code': Ext.getCmp('cboCust2').getValue(),
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
		store: grupCust2,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Code", dataIndex: 'fs_kd_cust', menuDisabled: true, hidden: true},
			{text: "Count", dataIndex: 'fs_count', menuDisabled: true, hidden: true},
			{text: "Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 200},
			{text: "IdCard", dataIndex: 'fs_idcard', menuDisabled: true, hidden: true},
			{text: "Address", dataIndex: 'fs_addr', menuDisabled: true, width: 180}
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
					grupCust2.load();
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
			store: grupCust2,
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
				Ext.getCmp('cboCust2').setValue(record.get('fs_code'));
				Ext.getCmp('txtCustCd2').setValue(record.get('fs_kd_cust'));
				Ext.getCmp('txtCustCount2').setValue(record.get('fs_count'));
				Ext.getCmp('txtCust2').setValue(record.get('fs_nm_code'));
				
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
				grupCust2.load();
				vMask.show();
			}
		}
	});

	var cboCust2 = {
		anchor: '98%',
		emptyText: "Select a",
		fieldLabel: 'To Customer',
		id: 'cboCust2',
		name: 'cboCust2',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtCust2').setValue('');
				Ext.getCmp('txtCustCd2').setValue('');
				Ext.getCmp('txtCustCount2').setValue('');
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
	
	var txtCustCd2 = {
		anchor: '98%',
		fieldLabel: 'Code',
		hidden: true,
		id: 'txtCustCd2',
		name: 'txtCustCd2',
		xtype: 'textfield'
	};
	
	var txtCustCount2 = {
		anchor: '98%',
		fieldLabel: 'Count',
		hidden: true,
		id: 'txtCustCount2',
		name: 'txtCustCount2',
		xtype: 'textfield'
	};
	
	var txtCust2 = {
		anchor: '100%',
		emptyText: "Customer",
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtCust2',
		name: 'txtCust2',
		readOnly: true,
		xtype: 'textfield'
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
		anchor: '99%',
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
	
	var grupCust3 = Ext.create('Ext.data.Store', {
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
					'fs_code': Ext.getCmp('cboCust3').getValue(),
					'fs_nm_code': Ext.getCmp('cboCust3').getValue(),
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
		store: grupCust3,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Code", dataIndex: 'fs_kd_cust', menuDisabled: true, hidden: true},
			{text: "Count", dataIndex: 'fs_count', menuDisabled: true, hidden: true},
			{text: "Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 200},
			{text: "IdCard", dataIndex: 'fs_idcard', menuDisabled: true, hidden: true},
			{text: "Address", dataIndex: 'fs_addr', menuDisabled: true, width: 180}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Code / Name',
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
					grupCust3.load();
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
			store: grupCust3,
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
				Ext.getCmp('cboCust3').setValue(record.get('fs_code'));
				Ext.getCmp('txtCustCd3').setValue(record.get('fs_kd_cust'));
				Ext.getCmp('txtCustCount3').setValue(record.get('fs_count'));
				Ext.getCmp('txtCust3').setValue(record.get('fs_nm_code'));
				
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
				grupCust3.load();
				vMask.show();
			}
		}
	});

	var cboCust3 = {
		anchor: '98%',
		emptyText: "Select a",
		fieldLabel: 'From Customer',
		id: 'cboCust3',
		name: 'cboCust3',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtCust3').setValue('');
				Ext.getCmp('txtCustCd3').setValue('');
				Ext.getCmp('txtCustCount3').setValue('');
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
	
	var txtCustCd3 = {
		anchor: '98%',
		fieldLabel: 'Code',
		hidden: true,
		id: 'txtCustCd3',
		name: 'txtCustCd3',
		xtype: 'textfield'
	};
	
	var txtCustCount3 = {
		anchor: '98%',
		fieldLabel: 'Count',
		hidden: true,
		id: 'txtCustCount3',
		name: 'txtCustCount3',
		xtype: 'textfield'
	};
	
	var txtCust3 = {
		anchor: '100%',
		emptyText: "Customer",
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtCust3',
		name: 'txtCust3',
		readOnly: true,
		xtype: 'textfield'
	};
	
	var grupCust4 = Ext.create('Ext.data.Store', {
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
					'fs_code': Ext.getCmp('cboCust4').getValue(),
					'fs_nm_code': Ext.getCmp('cboCust4').getValue(),
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
		store: grupCust4,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Code", dataIndex: 'fs_kd_cust', menuDisabled: true, hidden: true},
			{text: "Count", dataIndex: 'fs_count', menuDisabled: true, hidden: true},
			{text: "Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 200},
			{text: "IdCard", dataIndex: 'fs_idcard', menuDisabled: true, hidden: true},
			{text: "Address", dataIndex: 'fs_addr', menuDisabled: true, width: 180}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Code / Name',
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
					grupCust4.load();
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
			store: grupCust4,
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
				Ext.getCmp('cboCust4').setValue(record.get('fs_code'));
				Ext.getCmp('txtCustCd4').setValue(record.get('fs_kd_cust'));
				Ext.getCmp('txtCustCount4').setValue(record.get('fs_count'));
				Ext.getCmp('txtCust4').setValue(record.get('fs_nm_code'));
				
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
				grupCust4.load();
				vMask.show();
			}
		}
	});

	var cboCust4 = {
		anchor: '98%',
		emptyText: "Select a",
		fieldLabel: 'To Customer',
		id: 'cboCust4',
		name: 'cboCust4',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtCust4').setValue('');
				Ext.getCmp('txtCustCd4').setValue('');
				Ext.getCmp('txtCustCount4').setValue('');
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
	
	var txtCustCd4 = {
		anchor: '98%',
		fieldLabel: 'Code',
		hidden: true,
		id: 'txtCustCd4',
		name: 'txtCustCd4',
		xtype: 'textfield'
	};
	
	var txtCustCount4 = {
		anchor: '98%',
		fieldLabel: 'Count',
		hidden: true,
		id: 'txtCustCount4',
		name: 'txtCustCount4',
		xtype: 'textfield'
	};
	
	var txtCust4 = {
		anchor: '100%',
		emptyText: "Customer",
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtCust4',
		name: 'txtCust4',
		readOnly: true,
		xtype: 'textfield'
	};
	
	Ext.define('DataGrid', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_cust', type: 'string'},
			{name: 'fs_count', type: 'string'},
			{name: 'fs_nm_cust', type: 'string'},
			{name: 'fd_serah', type: 'string'},
			{name: 'fs_jumlah', type: 'string'},
			{name: '0-7', type: 'string'},
			{name: '8-14', type: 'string'},
			{name: '15-21', type: 'string'},
			{name: '22-28', type: 'string'},
			{name: '29-35', type: 'string'},
			{name: '36-42', type: 'string'},
			{name: '43-49', type: 'string'},
			{name: '50-56', type: 'string'},
			{name: '57-63', type: 'string'},
			{name: '>63', type: 'string'}
		]
	});

	var grupGrid = Ext.create('Ext.data.Store', {
		autoLoad: false,
		model: 'DataGrid',
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
			url: 'stnkbpkbaging/grid_detail'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fd_tgl': Ext.Date.format(Ext.getCmp('txtTgl').getValue(), 'Ymd'),
					'fd_tgl2': Ext.Date.format(Ext.getCmp('txtTgl2').getValue(), 'Ymd'),
					'fs_kd_cust': Ext.getCmp('txtCustCd').getValue(),
					'fs_count': Ext.getCmp('txtCustCount').getValue(),
					'fs_kd_cust2': Ext.getCmp('txtCustCd2').getValue(),
					'fs_count2': Ext.getCmp('txtCustCount2').getValue()
				});
			}
		}
	});

	var gridDetail = Ext.create('Ext.grid.Panel', {
		anchor: '100%',
		autoDestroy: true,
		height: 270,
		sortableColumns: false,
		store: grupGrid,
		columns: [{
			width: 45,
			xtype: 'rownumberer'
		},{
			dataIndex: 'fs_kd_cust',
			hidden: true,
			locked: true,
			menuDisabled: true,
			text: "Cust Cd",
			width: 50
		},{
			dataIndex: 'fs_count',
			hidden: true,
			locked: true,
			menuDisabled: true,
			text: "Count Cd",
			width: 50
		},{
			dataIndex: 'fs_nm_cust',
			locked: true,
			menuDisabled: true,
			text: "Cust",
			width: 170
		},{
			dataIndex: 'fd_serah',
			locked: true,
			menuDisabled: true,
			text: "Start Process",
			width: 80
		},{
			dataIndex: 'fs_jumlah',
			locked: true,
			menuDisabled: true,
			text: "Long Process",
			width: 80
		},{
			align: 'center',
			dataIndex: '0-7',
			menuDisabled: true,
			text: "0-7 day(s)",
			width: 80
		},{
			align: 'center',
			dataIndex: '8-14',
			menuDisabled: true,
			text: "8-14 day(s)",
			width: 80
		},{
			align: 'center',
			dataIndex: '15-21',
			menuDisabled: true,
			text: "15-21 day(s)",
			width: 80
		},{
			align: 'center',
			dataIndex: '22-28',
			menuDisabled: true,
			text: "22-28 day(s)",
			width: 80
		},{
			align: 'center',
			dataIndex: '29-35',
			menuDisabled: true,
			text: "29-35 day(s)",
			width: 80
		},{
			align: 'center',
			dataIndex: '36-42',
			menuDisabled: true,
			text: "36-42 day(s)",
			width: 80
		},{
			align: 'center',
			dataIndex: '43-49',
			menuDisabled: true,
			text: "43-49 day(s)",
			width: 80
		},{
			align: 'center',
			dataIndex: '50-56',
			menuDisabled: true,
			text: "50-56 day(s)",
			width: 80
		},{
			align: 'center',
			dataIndex: '57-63',
			menuDisabled: true,
			text: "57-63 day(s)",
			width: 80
		},{
			align: 'center',
			dataIndex: '>63',
			menuDisabled: true,
			text: ">63 day(s)",
			width: 80
		}],
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupGrid
		}),
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false,
			stripeRows: true
		}
	});

	Ext.define('DataGrid2', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_cust', type: 'string'},
			{name: 'fs_count', type: 'string'},
			{name: 'fs_nm_cust', type: 'string'},
			{name: 'fd_serah', type: 'string'},
			{name: 'fs_jumlah', type: 'string'},
			{name: '0-30', type: 'string'},
			{name: '31-60', type: 'string'},
			{name: '61-90', type: 'string'},
			{name: '90-120', type: 'string'},
			{name: '>120', type: 'string'}
		]
	});

	var grupGrid2 = Ext.create('Ext.data.Store', {
		autoLoad: false,
		model: 'DataGrid2',
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
			url: 'stnkbpkbaging/grid_detail2'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fd_tgl': Ext.Date.format(Ext.getCmp('txtTgl3').getValue(), 'Ymd'),
					'fd_tgl2': Ext.Date.format(Ext.getCmp('txtTgl4').getValue(), 'Ymd'),
					'fs_kd_cust': Ext.getCmp('txtCustCd3').getValue(),
					'fs_count': Ext.getCmp('txtCustCount3').getValue(),
					'fs_kd_cust2': Ext.getCmp('txtCustCd4').getValue(),
					'fs_count2': Ext.getCmp('txtCustCount4').getValue()
				});
			}
		}
	});

	var gridDetail2 = Ext.create('Ext.grid.Panel', {
		anchor: '100%',
		autoDestroy: true,
		height: 270,
		sortableColumns: false,
		store: grupGrid2,
		columns: [{
			width: 45,
			xtype: 'rownumberer'
		},{
			dataIndex: 'fs_kd_cust',
			hidden: true,
			menuDisabled: true,
			text: "Cust Cd",
			width: 50
		},{
			dataIndex: 'fs_count',
			hidden: true,
			menuDisabled: true,
			text: "Count Cd",
			width: 50
		},{
			dataIndex: 'fs_nm_cust',
			menuDisabled: true,
			text: "Cust",
			width: 295
		},{
			dataIndex: 'fd_serah',
			menuDisabled: true,
			text: "Start Process",
			width: 80
		},{
			dataIndex: 'fs_jumlah',
			menuDisabled: true,
			text: "Long Process",
			width: 80
		},{
			align: 'center',
			dataIndex: '0-30',
			menuDisabled: true,
			text: "0-30 day(s)",
			width: 80
		},{
			align: 'center',
			dataIndex: '31-60',
			menuDisabled: true,
			text: "31-60 day(s)",
			width: 80
		},{
			align: 'center',
			dataIndex: '61-90',
			menuDisabled: true,
			text: "61-90 day(s)",
			width: 80
		},{
			align: 'center',
			dataIndex: '91-120',
			menuDisabled: true,
			text: "91-120 day(s)",
			width: 90
		},{
			align: 'center',
			dataIndex: '>120',
			menuDisabled: true,
			text: ">120 day(s)",
			width: 80
		}],
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupGrid2
		}),
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false,
			stripeRows: true
		}
	});

	function fnExportToExcel() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 180000,
			url: 'stnkbpkbaging/excel_stnk',
			params: {
				'fd_tgl': Ext.Date.format(Ext.getCmp('txtTgl').getValue(), 'Ymd'),
				'fd_tgl2': Ext.Date.format(Ext.getCmp('txtTgl2').getValue(), 'Ymd'),
				'fs_kd_cust': Ext.getCmp('txtCustCd').getValue(),
				'fs_count': Ext.getCmp('txtCustCount').getValue(),
				'fs_kd_cust2': Ext.getCmp('txtCustCd2').getValue(),
				'fs_count2': Ext.getCmp('txtCustCount2').getValue()
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

	function fnExportToExcel2() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 180000,
			url: 'stnkbpkbaging/excel_bpkb',
			params: {
				'fd_tgl': Ext.Date.format(Ext.getCmp('txtTgl3').getValue(), 'Ymd'),
				'fd_tgl2': Ext.Date.format(Ext.getCmp('txtTgl4').getValue(), 'Ymd'),
				'fs_kd_cust': Ext.getCmp('txtCustCd3').getValue(),
				'fs_count': Ext.getCmp('txtCustCount3').getValue(),
				'fs_kd_cust2': Ext.getCmp('txtCustCd4').getValue(),
				'fs_count2': Ext.getCmp('txtCustCount4').getValue()
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
		Ext.getCmp('txtTgl').setValue(new Date());
		Ext.getCmp('txtTgl2').setValue(new Date());
		Ext.getCmp('cboCust').setValue('');
		Ext.getCmp('txtCust').setValue('');
		Ext.getCmp('txtCustCd').setValue('');
		Ext.getCmp('txtCustCount').setValue('');
		Ext.getCmp('cboCust2').setValue('');
		Ext.getCmp('txtCust2').setValue('');
		Ext.getCmp('txtCustCd2').setValue('');
		Ext.getCmp('txtCustCount2').setValue('');
		
		grupGrid.load();
	}

	function fnReset2() {
		Ext.getCmp('txtTgl3').setValue(new Date());
		Ext.getCmp('txtTgl3').setValue(new Date());
		Ext.getCmp('cboCust3').setValue('');
		Ext.getCmp('txtCust3').setValue('');
		Ext.getCmp('txtCustCd3').setValue('');
		Ext.getCmp('txtCustCount3').setValue('');
		Ext.getCmp('cboCust4').setValue('');
		Ext.getCmp('txtCust4').setValue('');
		Ext.getCmp('txtCustCd4').setValue('');
		Ext.getCmp('txtCustCount4').setValue('');
		
		grupGrid2.load();
	}

	var frmStnkBpkbAging = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'STNK-BPKB Aging Form',
		width: 950,
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
				title: 'STNK Aging',
				xtype: 'form',
				items: [{
					fieldDefaults: {
						labelAlign: 'right',
						labelSeparator: '',
						labelWidth: 90,
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
								anchor: '100%',
								layout: 'hbox',
								xtype: 'container',
								items: [{
									flex: 1.6,
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
							flex: 1.8,
							layout: 'anchor',
							xtype: 'container',
							items: []
						}]
					},{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1.6,
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
									flex: 1.1,
									layout: 'anchor',
									xtype: 'container',
									items: [
										txtCust
									]
								}]
							}]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: []
						}]
					},{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1.6,
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
										cboCust2,
										txtCustCd2,
										txtCustCount2
									]
								},{
									flex: 1.1,
									layout: 'anchor',
									xtype: 'container',
									items: [
										txtCust2
									]
								}]
							}]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: []
						}]
					}]
				},{
					anchor: '100%',
					layout: 'hbox',
					xtype: 'container',
					items: [{
						flex: 2.7,
						layout: 'anchor',
						xtype: 'container',
						items: []
					},{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 0.9,
								layout: 'anchor',
								xtype: 'container',
								items: [{
									anchor: '93%',
									text: 'Process',
									xtype: 'button',
									handler: function() {
										grupGrid.load();
									}
								}]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [{
									anchor: '93%',
									text: 'Export to Excel',
									xtype: 'button',
									handler: function() {
										grupGrid.load();
										fnExportToExcel();
									}
								}]
							},{
								flex: 0.9,
								layout: 'anchor',
								xtype: 'container',
								items: [{
									anchor: '93%',
									handler: fnReset,
									text: 'Reset',
									xtype: 'button'
								}]
							}]
						}]
					}]
				},{xtype: 'splitter'},{xtype: 'splitter'},
					gridDetail
				]
			},{
				bodyStyle: 'background-color: '.concat(gBasePanel),
				border: false,
				frame: false,
				title: 'BPKB Aging',
				xtype: 'form',
				items: [{
					fieldDefaults: {
						labelAlign: 'right',
						labelSeparator: '',
						labelWidth: 90,
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
								anchor: '100%',
								layout: 'hbox',
								xtype: 'container',
								items: [{
									flex: 1.6,
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
							flex: 1.8,
							layout: 'anchor',
							xtype: 'container',
							items: []
						}]
					},{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1.6,
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
										cboCust3,
										txtCustCd3,
										txtCustCount3
									]
								},{
									flex: 1.1,
									layout: 'anchor',
									xtype: 'container',
									items: [
										txtCust3
									]
								}]
							}]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: []
						}]
					},{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1.6,
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
										cboCust4,
										txtCustCd4,
										txtCustCount4
									]
								},{
									flex: 1.1,
									layout: 'anchor',
									xtype: 'container',
									items: [
										txtCust4
									]
								}]
							}]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: []
						}]
					}]
				},{
					anchor: '100%',
					layout: 'hbox',
					xtype: 'container',
					items: [{
						flex: 2.7,
						layout: 'anchor',
						xtype: 'container'
					},{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 0.9,
								layout: 'anchor',
								xtype: 'container',
								items: [{
									anchor: '93%',
									text: 'Process',
									xtype: 'button',
									handler: function() {
										grupGrid2.load();
									}
								}]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [{
									anchor: '93%',
									text: 'Export to Excel',
									xtype: 'button',
									handler: function() {
										grupGrid2.load();
										fnExportToExcel2();
									}
								}]
							},{
								flex: 0.9,
								layout: 'anchor',
								xtype: 'container',
								items: [{
									anchor: '93%',
									handler: fnReset2,
									text: 'Reset',
									xtype: 'button'
								}]
							}]
						}]
					}]
				},{xtype: 'splitter'},{xtype: 'splitter'},
					gridDetail2
				]
			}]
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmStnkBpkbAging
	});
	
	function fnMaskShow() {
		frmStnkBpkbAging.mask('Please wait...');
	}

	function fnMaskHide() {
		frmStnkBpkbAging.unmask();
	}
	
	frmStnkBpkbAging.render(Ext.getBody());
	Ext.get('loading').destroy();
});
