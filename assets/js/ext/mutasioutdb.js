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

	var grupRefno = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_refno','fd_refno','fs_ket',
			'fs_kd_custcount','fs_kd_cust','fs_count_cust',
			'fs_nm_cust','fs_kd_tcomp','fs_nm_tdb',
			'fs_nm_tcomp','fs_kd_tdeptcount','fs_kd_tdept',
			'fs_tcount','fs_nm_tdept','fs_kd_twh','fs_nm_twh'
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
			url: 'mutasioutdb/refno'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboRefno').getValue(),
					'fs_nm_cust': Ext.getCmp('cboRefno').getValue(),
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
			{text: "Ref. No", dataIndex: 'fs_refno', menuDisabled: true, width: 170},
			{text: "Ref. Date", dataIndex: 'fd_refno', menuDisabled: true, width: 70},
			{text: "Note", dataIndex: 'fs_ket', menuDisabled: true, width: 140},
			{text: "Cust", dataIndex: 'fs_kd_custcount', menuDisabled: true, hidden: true},
			{text: "Cust", dataIndex: 'fs_kd_cust', menuDisabled: true, hidden: true},
			{text: "Cust", dataIndex: 'fs_count_cust', menuDisabled: true, hidden: true},
			{text: "Customer", dataIndex: 'fs_nm_cust', menuDisabled: true, width: 100},
			{text: "To Company", dataIndex: 'fs_nm_tcomp', menuDisabled: true, width: 100},
			{text: "Comp", dataIndex: 'fs_kd_tcomp', menuDisabled: true, hidden: true},
			{text: "Comp", dataIndex: 'fs_nm_tdb', menuDisabled: true, hidden: true},
			{text: "Dept", dataIndex: 'fs_kd_tdeptcount', menuDisabled: true, hidden: true},
			{text: "Dept", dataIndex: 'fs_kd_tdept', menuDisabled: true, hidden: true},
			{text: "Dept", dataIndex: 'fs_tcount', menuDisabled: true, hidden: true},
			{text: "To Dept", dataIndex: 'fs_nm_tdept', menuDisabled: true, width: 100},
			{text: "Warehouse", dataIndex: 'fs_kd_twh', menuDisabled: true, hidden: true},
			{text: "Warehouse", dataIndex: 'fs_nm_twh', menuDisabled: true, hidden: true}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Ref. No / Cust',
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
				Ext.getCmp('txtNote').setValue(record.get('fs_ket'));
				Ext.getCmp('cboCust').setValue(record.get('fs_kd_custcount'));
				Ext.getCmp('txtCust').setValue(record.get('fs_nm_cust'));
				Ext.getCmp('txtCustCd').setValue(record.get('fs_kd_cust'));
				Ext.getCmp('txtCount').setValue(record.get('fs_count_cust'));
				Ext.getCmp('cboComp').setValue(record.get('fs_nm_tcomp'));
				Ext.getCmp('txtComp').setValue(record.get('fs_kd_tcomp'));
				Ext.getCmp('txtDB').setValue(record.get('fs_nm_tdb'));
				Ext.getCmp('cboDeptKe').setValue(record.get('fs_kd_tdeptcount'));
				Ext.getCmp('txtDeptKe').setValue(record.get('fs_nm_tdept'));
				Ext.getCmp('txtDeptCdKe').setValue(record.get('fs_kd_tdept'));
				Ext.getCmp('txtCountKe').setValue(record.get('fs_tcount'));
				Ext.getCmp('cboWHKe').setValue(record.get('fs_kd_twh'));
				Ext.getCmp('txtWHKe').setValue(record.get('fs_nm_twh'));
				
				grupGridProd.load();
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
		labelWidth: 50,
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

	var txtNote = {
		anchor: '65%',
		emptyText: "Enter a Note",
		fieldLabel: 'Note',
		grow: true,
		growMin: 35,
		growMax: 35,
		id: 'txtNote',
		labelWidth: 50,
		name: 'txtNote',
		xtype: 'textareafield'
	};

	var grupCust = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_code','fs_kd_cust','fs_count','fs_nm_code','fs_idcard','fs_addr','fs_tlp'],
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
		store: grupCust,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Code", dataIndex: 'fs_kd_cust', menuDisabled: true, hidden: true},
			{text: "Count", dataIndex: 'fs_count', menuDisabled: true, hidden: true},
			{text: "Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 250},
			{text: "Phone", dataIndex: 'fs_tlp', menuDisabled: true, width: 100},
			{text: "IdCard", dataIndex: 'fs_idcard', menuDisabled: true, hidden: true},
			{text: "Address", dataIndex: 'fs_addr', menuDisabled: true, width: 230}
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
					winCari2.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboCust').setValue(record.get('fs_code'));
				Ext.getCmp('txtCustCd').setValue(record.get('fs_kd_cust'));
				Ext.getCmp('txtCount').setValue(record.get('fs_count'));
				Ext.getCmp('txtCust').setValue(record.get('fs_nm_code'));
				
				grupGridProd.load();
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
				grupCust.load();
				vMask.show();
			}
		}
	});

	var cboCust = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: "Select a",
		fieldLabel: 'Customer',
		id: 'cboCust',
		labelWidth: 70,
		name: 'cboCust',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtCust').setValue('');
				Ext.getCmp('txtCustCd').setValue('');
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

	var txtCustCd = {
		anchor: '100%',
		fieldLabel: 'Cust Code',
		hidden: true,
		id: 'txtCustCd',
		labelWidth: 70,
		name: 'txtCustCd',
		xtype: 'textfield'
	};

	var txtCount = {
		anchor: '100%',
		fieldLabel: 'Cust Count',
		hidden: true,
		id: 'txtCount',
		labelWidth: 70,
		name: 'txtCount',
		xtype: 'textfield'
	};

	var txtCust = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: "Customer",
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtCust',
		name: 'txtCust',
		readOnly: true,
		xtype: 'textfield'
	};

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
			url: 'mutasioutdb/ambil_comp'
		}
	});

	var winGrid3 = Ext.create('Ext.ux.LiveSearchGridPanel', {
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
					winCari3.hide();
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
				
				grupDeptKe.load();
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
				grupComp.load();
				vMask.show();
			}
		}
	});

	var cboComp = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		editable: false,
		emptyText : 'Select a To Company',
		fieldLabel: 'Company',
		id: 'cboComp',
		name: 'cboComp',
		store: grupComp,
		xtype: 'textfield',
		listeners: {
			change: function(combo, value) {
				Ext.getCmp('txtComp').setValue('');
				Ext.getCmp('txtDB').setValue('');
				Ext.getCmp('cboDeptKe').setValue('');
				Ext.getCmp('txtDeptCdKe').setValue('');
				Ext.getCmp('txtDeptKe').setValue('');
				Ext.getCmp('txtCountKe').setValue('');
				Ext.getCmp('cboWHKe').setValue('');
				Ext.getCmp('txtWHKe').setValue('');
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
	
	var txtComp = {
		anchor: '100%',
		emptyText: 'Comp Code',
		fieldLabel: 'Comp Code',
		hidden: true,
		id: 'txtComp',
		name: 'txtComp',
		xtype: 'textfield'
	};
	
	var txtDB = {
		anchor: '100%',
		emptyText: 'DB Name',
		fieldLabel: 'DB Name',
		hidden: true,
		id: 'txtDB',
		name: 'txtDB',
		xtype: 'textfield'
	};

	var grupDeptKe = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_code','fs_count','fs_nm_code'
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
			url: 'mutasioutdb/kodedeptke'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_nm_db': Ext.getCmp('txtDB').getValue(),
					'fs_kd_comp': Ext.getCmp('txtComp').getValue(),
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
		store: grupDeptKe,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Count", dataIndex: 'fs_count', menuDisabled: true, width: 100},
			{text: "Dept Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 280}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Code / Dept Name',
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
					grupDeptKe.load();
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
			store: grupDeptKe,
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
				Ext.getCmp('cboDeptKe').setValue(record.get('fs_code').concat(record.get('fs_count')));
				Ext.getCmp('txtDeptKe').setValue(record.get('fs_nm_code'));
				Ext.getCmp('txtDeptCdKe').setValue(record.get('fs_code'));
				Ext.getCmp('txtCountKe').setValue(record.get('fs_count'));
				
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
				grupDeptKe.load();
				vMask.show();
			}
		}
	});

	var cboDeptKe = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Department',
		id: 'cboDeptKe',
		name: 'cboDeptKe',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtDeptKe').setValue('');
				Ext.getCmp('txtDeptCdKe').setValue('');
				Ext.getCmp('txtCountKe').setValue('');
				Ext.getCmp('cboWHKe').setValue('');
				Ext.getCmp('txtWHKe').setValue('');
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

	var txtDeptKe = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'To Department',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtDeptKe',
		name: 'txtDeptKe',
		readOnly: true,
		xtype: 'textfield'
	};

	var txtDeptCdKe = {
		anchor: '100%',
		emptyText: '',
		fieldLabel: 'Dept Code',
		hidden: true,
		id: 'txtDeptCdKe',
		name: 'txtDeptCdKe',
		xtype: 'textfield'
	};

	var txtCountKe = {
		anchor: '100%',
		emptyText: '',
		fieldLabel: 'Dept Count',
		hidden: true,
		id: 'txtCountKe',
		name: 'txtCountKe',
		xtype: 'textfield'
	};

	var grupGudangKe = Ext.create('Ext.data.Store', {
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
			url: 'mutasioutdb/gudang'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_nm_db': Ext.getCmp('txtDB').getValue(),
					'fs_kd_comp': Ext.getCmp('txtComp').getValue(),
					'fs_kd_dept': Ext.getCmp('txtDeptCdKe').getValue(),
					'fs_count': Ext.getCmp('txtCountKe').getValue(),
					'fs_code': Ext.getCmp('cboWHKe').getValue(),
					'fs_nm_code': Ext.getCmp('cboWHKe').getValue(),
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
		store: grupGudangKe,
		columns: [	
			{xtype: 'rownumberer', width: 45},
			{text: "Warehouse Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Warehouse Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 380}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Warehouse Code / Warehouse Name',
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
					grupGudangKe.load();
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
			store: grupGudangKe,
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
				Ext.getCmp('cboWHKe').setValue(record.get('fs_code'));
				Ext.getCmp('txtWHKe').setValue(record.get('fs_nm_code'));
				
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
				grupGudangKe.load();
				vMask.show();
			}
		}
	});
	
	var cboWHKe = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Warehouse',
		id: 'cboWHKe',
		name: 'cboWHKe',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtWHKe').setValue('');
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
					var xNmDept = Ext.getCmp('txtDeptKe').getValue();
					var xLanjut = true;
					
					if (xNmDept.trim() === '') {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.MessageBox.INFO,
							msg: 'To Department is empty, pleas fill in advance!!',
							title: 'IDS'
						});
						xLanjut = false;
					}
					
					if (xLanjut === false) {
						return;
					}
					winCari5.show();
					winCari5.center();
				}
			}
		}
	};

	var txtWHKe = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'To Warehouse',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtWHKe',
		name: 'txtWHKe',
		readOnly: true,
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
			{name: 'fs_rangka', type: 'string'},
			{name: 'fs_mesin', type: 'string'},
			{name: 'fs_cc', type: 'string'},
			{name: 'fs_thn', type: 'string'},
			{name: 'fs_kd_warna', type: 'string'},
			{name: 'fs_nm_warna', type: 'string'}
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
			url: 'mutasioutdb/grid_prod'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboRefno').getValue(),
					'fs_kd_cust': Ext.getCmp('cboCust').getValue()
				});
			},
			load: function() {
				grupGridProd.sort([ {
						property : 'fs_kd_product',
						direction: 'ASC'
					},{
						property : 'fs_rangka',
						direction: 'ASC'
					},{
						property : 'fs_mesin',
						direction: 'ASC'
					}
				]);
				gridProd.getView().refresh();
			}
		}
	});

	var gridProd = Ext.create('Ext.grid.Panel', {
		anchor: '100%',
		defaultType: 'textfield',
		height: 250,
		sortableColumns: false,
		store: grupGridProd,
		columns: [{
			xtype: 'rownumberer',
			width: 25
		},{
			dataIndex: 'fs_kd_product',
			menuDisabled: true,
			text: 'Code',
			width: 115
		},{
			dataIndex: 'fs_nm_product',
			menuDisabled: true,
			text: 'Product',
			width: 170
		},{
			dataIndex: 'fs_rangka',
			menuDisabled: true,
			text: 'Chasis',
			width: 150
		},{
			dataIndex: 'fs_mesin',
			menuDisabled: true,
			text: 'Machine',
			width: 125
		},{
			align: 'center',
			dataIndex: 'fs_cc',
			menuDisabled: true,
			text: 'Cylinder',
			width: 50
		},{
			align: 'center',
			dataIndex: 'fs_thn',
			menuDisabled: true,
			text: 'Year',
			width: 40
		},{
			dataIndex: 'fs_kd_warna',
			hidden: true,
			menuDisabled: true,
			text: 'Color Code',
			width: 100
		},{
			dataIndex: 'fs_nm_warna',
			menuDisabled: true,
			text: 'Color',
			width: 145
		}],
		listeners: {
			selectionchange: function(view, records) {
				gridProd.down('#removeData').setDisabled(!records.length);
			}
		},
		plugins: [
			cellEditingProd
		],
		tbar: ['->',{
			xtype: 'buttongroup',
			defaults: {
				scale: 'small'
			},
			items: [{
				iconCls: 'icon-delete',
				itemId: 'removeData',
				text: 'Delete',
				handler: function() {
					var sm = gridProd.getSelectionModel();
					cellEditingProd.cancelEdit();
					grupGridProd.remove(sm.getSelection());
					gridProd.getView().refresh();
					if (grupGridProd.getCount() > 0) {
						sm.select(0);
					}
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

	function fnCekSave() {
		if (this.up('form').getForm().isValid()) {
			var xrangka = "'";
			var lxrangka = 0;
			var xmesin = "'";
			var lxmesin = 0;
			
			store = gridProd.getStore();
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
			
			var xtotal = grupGridProd.getCount();
			if (xtotal <= 0) {
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Chassis List is empty, please fill in advance!!',
					title: 'IDS'
				});
				return;
			}
			
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'mutasioutdb/ceksave',
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
		var xrangka = '';
		var xmesin = '';
		var xcc = '';
		var xthn = '';
		var xkdwarna = '';
		var xnmwarna = '';
		
		var store = gridProd.getStore();
		store.each(function(record, idx) {
			xprod = xprod +'|'+ record.get('fs_kd_product');
			xrangka = xrangka +'|'+ record.get('fs_rangka');
			xmesin = xmesin +'|'+ record.get('fs_mesin');
			xcc = xcc +'|'+ record.get('fs_cc');
			xthn = xthn +'|'+ record.get('fs_thn');
			xkdwarna = xkdwarna +'|'+ record.get('fs_kd_warna');
			xnmwarna = xnmwarna +'|'+ record.get('fs_nm_warna');
		});
		
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'mutasioutdb/save',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue(),
				'fd_refno': Ext.Date.format(Ext.getCmp('txtRefnodt').getValue(), 'Ymd'),
				'fs_ket': Ext.getCmp('txtNote').getValue(),
				'fs_kd_cust': Ext.getCmp('txtCustCd').getValue(),
				'fs_count_cust': Ext.getCmp('txtCount').getValue(),
				'fs_kd_tcomp': Ext.getCmp('txtComp').getValue(),
				'fs_nm_tdb': Ext.getCmp('txtDB').getValue(),
				'fs_nm_tcomp': Ext.getCmp('cboComp').getValue(),
				'fs_kd_tdept': Ext.getCmp('txtDeptCdKe').getValue(),
				'fs_tcount': Ext.getCmp('txtCountKe').getValue(),
				'fs_nm_tdept': Ext.getCmp('txtDeptKe').getValue(),
				'fs_kd_twh': Ext.getCmp('cboWHKe').getValue(),
				'fs_nm_twh': Ext.getCmp('txtWHKe').getValue(),
				'fs_kd_product': xprod,
				'fs_rangka': xrangka,
				'fs_mesin': xmesin,
				'fs_cc': xcc,
				'fs_thn': xthn,
				'fs_kd_warna': xkdwarna,
				'fs_nm_warna': xnmwarna
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
					Ext.getCmp('cboRefno').setValue(xtext.refno.trim());
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
		
		store = gridProd.getStore();
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
			url: 'mutasioutdb/cekremove',
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
			url: 'mutasioutdb/remove',
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
			url: 'mutasioutdb/print_mutasioutdb',
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
					var xfilexls = xtext.nmfilexls.trim();
					
					vMask.show();
					var winpdf = Ext.create('Ext.window.Window', {
						closable: false,
						draggable: false,
						height: 500,
						layout: 'fit',
						title: 'Mutation Out',
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
		Ext.getCmp('txtNote').setValue('');
		Ext.getCmp('cboCust').setValue('');
		Ext.getCmp('txtCustCd').setValue('');
		Ext.getCmp('txtCount').setValue('');
		Ext.getCmp('txtCust').setValue('');
		Ext.getCmp('cboComp').setValue('');
		Ext.getCmp('txtComp').setValue('');
		Ext.getCmp('txtDB').setValue('');
		Ext.getCmp('cboDeptKe').setValue('');
		Ext.getCmp('txtDeptKe').setValue('');
		Ext.getCmp('txtDeptCdKe').setValue('');
		Ext.getCmp('txtCountKe').setValue('');
		Ext.getCmp('cboWHKe').setValue('');
		Ext.getCmp('txtWHKe').setValue('');
		
		grupGridProd.removeAll();
	}

	var frmMutasiOutDB = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Mutation Out Between Companies Form',
		width: 850,
		fieldDefaults: {
			labelAlign: 'right',
			labelSeparator: '',
			labelWidth: 80,
			msgTarget: 'side'
		},
		items: [{
			style: 'padding: 5px;',
			xtype: 'fieldset',
			items: [{
				anchor: '65%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 2,
					layout: 'anchor',
					xtype: 'container',
					items: [
						cboRefno
					]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtRefnodt
					]
				}]
			},
				txtNote
			]
		},{
			anchor: '100%',
			layout: 'hbox',
			xtype: 'container',
			items: [{
				flex: 1,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					anchor: '99%',
					style: 'padding: 5px;',
					title: 'FROM SALES',
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
								cboCust
							]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtCust
							]
						}]
					},
						txtCustCd,
						txtCount
					]
				}]
			},{
				flex: 1,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					style: 'padding: 5px;',
					title: 'TO COMPANY',
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
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									cboComp,
									txtComp,
									txtDB,
								{
									anchor: '100%',
									layout: 'hbox',
									xtype: 'container',
									items: [{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											cboDeptKe
										]
									},{
										flex: 1.2,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtDeptKe
										]
									}]
								},
									txtDeptCdKe,
									txtCountKe,
								{
									anchor: '100%',
									layout: 'hbox',
									xtype: 'container',
									items: [{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											cboWHKe
										]
									},{
										flex: 1.2,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtWHKe
										]
									}]
								}]
							}]
						}]
					}]
				}]
			}]
		},
			gridProd
		],
		buttons: [{
			text: 'Save',
			handler: fnCekSave
		},{
			text: 'Print',
			handler: fnPrint
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
		target: frmMutasiOutDB
	});
	
	function fnMaskShow() {
		frmMutasiOutDB.mask('Please wait...');
	}

	function fnMaskHide() {
		frmMutasiOutDB.unmask();
	}
	
	frmMutasiOutDB.render(Ext.getBody());
	Ext.get('loading').destroy();
});
