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

	var grupGudang = Ext.create('Ext.data.Store', {
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
			url: 'regstatus/gudang'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_code': Ext.getCmp('cboWH').getValue(),
					'fs_nm_code': Ext.getCmp('cboWH').getValue(),
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
		store: grupGudang,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Warehouse Code", dataIndex: 'fs_code', menuDisabled: true, hidden: true},
			{text: "Warehouse Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 480}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Warehouse Code / Warehouse Name',
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
					grupGudang.load();
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
			store: grupGudang,
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
				Ext.getCmp('cboWH').setValue(record.get('fs_code'));
				Ext.getCmp('txtWH').setValue(record.get('fs_nm_code'));
				
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
				grupGudang.load();
				vMask.show();
			}
		}
	});

	var cboWH = {
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Warehouse',
		id: 'cboWH',
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
					winCari.show();
					winCari.center();
				}
			}
		}
	};
	
	var txtWH = {
		anchor: '100%',
		emptyText: 'Warehouse',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtWH',
		name: 'txtWH',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupProduct = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_kd_product','fs_nm_product'],
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
			url: 'regstatus/product'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_product': Ext.getCmp('cboProduct').getValue(),
					'fs_nm_product': Ext.getCmp('cboProduct').getValue(),
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
		store: grupProduct,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Product Code", dataIndex: 'fs_kd_product', menuDisabled: true, width: 150},
			{text: "Product Name", dataIndex: 'fs_nm_product', menuDisabled: true, width: 330}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Product Code / Product Name',
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
					grupProduct.load();
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
			store: grupProduct,
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
				Ext.getCmp('cboProduct').setValue(record.get('fs_kd_product'));
				Ext.getCmp('txtProduct').setValue(record.get('fs_nm_product'));
				
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
				grupProduct.load();
				vMask.show();
			}
		}
	});
	
	var cboProduct = {
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Product',
		id: 'cboProduct',
		name: 'cboProduct',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtProduct').setValue('');
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
	
	var txtProduct = {
		anchor: '100%',
		emptyText: 'Product',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtProduct',
		name: 'txtProduct',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupCasis = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_rangka','fs_mesin','fs_kd_product','fs_nm_product'],
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
					'fs_rangka': Ext.getCmp('cboCasis').getValue(),
					'fs_mesin': Ext.getCmp('cboCasis').getValue(),
					'fs_kd_product': Ext.getCmp('cboProduct').getValue(),
					'fs_cari': Ext.getCmp('txtCari9').getValue()
				});
			}
		}
	});


	var winGrid9 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 750,
		sortableColumns: false,
		store: grupCasis,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Chassis", dataIndex: 'fs_rangka', menuDisabled: true, width: 200},
			{text: "Machine", dataIndex: 'fs_mesin', menuDisabled: true, width: 150},
			{text: "Product Code", dataIndex: 'fs_kd_product', menuDisabled: true, width: 150},
			{text: "Product Name", dataIndex: 'fs_nm_product', menuDisabled: true, width: 180}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Chassis / Machine',
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
					grupCasis.load();
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
			store: grupCasis,
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
				Ext.getCmp('cboCasis').setValue(record.get('fs_rangka'));
				
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
				grupCasis.load();
				vMask.show();
			}
		}
	});
	
	var cboCasis = {
		anchor: '50%',
		emptyText: 'Select a Chassis',
		fieldLabel: 'Chassis',
		id: 'cboCasis',
		name: 'cboCasis',
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
					winCari9.show();
					winCari9.center();
				}
			}
		}
	};
	
	var cekSold = {
		boxLabel: 'With Sold',
		checked: false,
		id: 'cekSold',
		name: 'cekSold',
		xtype: 'checkboxfield'
	};
	
	var cekAll = {
		boxLabel: 'All Warehouse',
		checked: false,
		id: 'cekAll',
		name: 'cekAll',
		xtype: 'checkboxfield'
	};

	var grupDept = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_code','fs_count','fs_nm_code'],
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
			url: 'regstatus/dept'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_code': Ext.getCmp('cboDept').getValue(),
					'fs_nm_code': Ext.getCmp('cboDept').getValue(),
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
		store: grupDept,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Dept Code", dataIndex: 'fs_code', menuDisabled: true, hidden:true},
			{text: "Dept Count", dataIndex: 'fs_count', menuDisabled: true, hidden:true},
			{text: "Dept Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 480}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Dept Code / Dept Name',
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
					grupDept.load();
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
			store: grupDept,
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
				Ext.getCmp('cboDept').setValue(record.get('fs_code').concat(record.get('fs_count')));
				Ext.getCmp('txtDeptCd').setValue(record.get('fs_code'));
				Ext.getCmp('txtCount').setValue(record.get('fs_count'));
				Ext.getCmp('txtDept').setValue(record.get('fs_nm_code'));
				
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
				grupDept.load();
				vMask.show();
			}
		}
	});
	
	var cboDept = {
		anchor: '98%',
		emptyText: 'Select a Department',
		fieldLabel: 'Department',
		hidden: true,
		id: 'cboDept',
		name: 'cboDept',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtDept').setValue('');
				Ext.getCmp('txtDeptCd').setValue('');
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
					winCari3.show();
					winCari3.center();
				}
			}
		}
	};
	
	var txtDeptCd = {
		anchor: '98%',
		fieldLabel: 'Dept Code',
		hidden: true,
		id: 'txtDeptCd',
		name: 'txtDeptCd',
		xtype: 'textfield'
	};
	
	var txtCount = {
		anchor: '98%',
		fieldLabel: 'Dept Count',
		hidden: true,
		id: 'txtCount',
		name: 'txtCount',
		xtype: 'textfield'
	};
	
	var txtDept = {
		anchor: '100%',
		fieldStyle: 'background-color: #eee; background-image: none;',
		hidden: true,
		id: 'txtDept',
		name: 'txtDept',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupGudang2 = Ext.create('Ext.data.Store', {
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
			url: 'regstatus/gudang'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_code': Ext.getCmp('cboWH2').getValue(),
					'fs_nm_code': Ext.getCmp('cboWH2').getValue(),
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
		store: grupGudang2,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Warehouse Code", dataIndex: 'fs_code', menuDisabled: true, hidden: true},
			{text: "Warehouse Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 480}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Warehouse Code / Warehouse Name',
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
					grupGudang2.load();
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
			store: grupGudang2,
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
				Ext.getCmp('cboWH2').setValue(record.get('fs_code'));
				Ext.getCmp('txtWH2').setValue(record.get('fs_nm_code'));
				
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
				grupGudang2.load();
				vMask.show();
			}
		}
	});
	
	var cboWH2 = {
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Warehouse',
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
	
	var txtWH2 = {
		anchor: '100%',
		emptyText: 'Warehouse',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtWH2',
		name: 'txtWH2',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupProduct2 = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_kd_product','fs_nm_product'],
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
			url: 'regstatus/product2'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_product': Ext.getCmp('cboProduct2').getValue(),
					'fs_nm_product': Ext.getCmp('cboProduct2').getValue(),
					'fs_cari': Ext.getCmp('txtCari5').getValue()
				});
			}
		}
	});

	/*
	var winGrid5 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupProduct2,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupProduct2,
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
			{text: "Product Code", dataIndex: 'fs_kd_product', menuDisabled: true, width: 150},
			{text: "Product Name", dataIndex: 'fs_nm_product', menuDisabled: true, width: 330}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboProduct2').setValue(record.get('fs_kd_product'));
				Ext.getCmp('txtProduct2').setValue(record.get('fs_nm_product'));
				
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
	*/
	
	
	var winGrid5 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupProduct2,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Product Code", dataIndex: 'fs_kd_product', menuDisabled: true, width: 150},
			{text: "Product Name", dataIndex: 'fs_nm_product', menuDisabled: true, width: 330}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Product Code / Product Name',
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
					grupProduct2.load();
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
			store: grupProduct2,
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
				Ext.getCmp('cboProduct2').setValue(record.get('fs_kd_product'));
				Ext.getCmp('txtProduct2').setValue(record.get('fs_nm_product'));
				
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
				grupProduct2.load();
				vMask.show();
			}
		}
	});
	
	var cboProduct2 = {
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Product',
		id: 'cboProduct2',
		name: 'cboProduct2',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtProduct2').setValue('');
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
	
	var txtProduct2 = {
		anchor: '100%',
		emptyText: 'Product',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtProduct2',
		name: 'txtProduct2',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupCasis2 = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_rangka','fs_mesin','fs_kd_product','fs_nm_product'],
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
					'fs_rangka': Ext.getCmp('cboCasis2').getValue(),
					'fs_mesin': Ext.getCmp('cboCasis2').getValue(),
					'fs_kd_product': Ext.getCmp('cboProduct2').getValue(),
					'fs_cari': Ext.getCmp('txtCari6').getValue()
				});
			}
		}
	});


	var winGrid6 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 750,
		sortableColumns: false,
		store: grupCasis2,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Chassis", dataIndex: 'fs_rangka', menuDisabled: true, width: 200},
			{text: "Machine", dataIndex: 'fs_mesin', menuDisabled: true, width: 150},
			{text: "Product Code", dataIndex: 'fs_kd_product', menuDisabled: true, width: 150},
			{text: "Product Name", dataIndex: 'fs_nm_product', menuDisabled: true, width: 180}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Chassis / Machine',
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
					grupCasis2.load();
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
			store: grupCasis2,
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
				Ext.getCmp('cboCasis2').setValue(record.get('fs_rangka'));
				
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
				grupCasis2.load();
				vMask.show();
			}
		}
	});
	
	var cboCasis2 = {
		anchor: '50%',
		emptyText: 'Select a Chassis',
		fieldLabel: 'Chassis',
		id: 'cboCasis2',
		name: 'cboCasis2',
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
					winCari6.show();
					winCari6.center();
				}
			}
		}
	};
	
	var txtDate = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '29.7%',
		editable: true,
		fieldLabel: 'Date Up To',
		format: 'd-m-Y',
		id: 'txtDate',
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
		name: 'txtDate',
		value: new Date(),
		xtype: 'datefield'
	};

	var grupGudang3 = Ext.create('Ext.data.Store', {
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
			url: 'regstatus/gudang'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_code': Ext.getCmp('cboWH3').getValue(),
					'fs_nm_code': Ext.getCmp('cboWH3').getValue(),
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
		store: grupGudang3,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Warehouse Code", dataIndex: 'fs_code', menuDisabled: true, hidden: true},
			{text: "Warehouse Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 480}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Warehouse Code / Warehouse Name',
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
					grupGudang3.load();
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
			store: grupGudang3,
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
				Ext.getCmp('cboWH3').setValue(record.get('fs_code'));
				Ext.getCmp('txtWH3').setValue(record.get('fs_nm_code'));
				
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
				grupGudang3.load();
				vMask.show();
			}
		}
	});
	
	var cboWH3 = {
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Warehouse',
		id: 'cboWH3',
		name: 'cboWH3',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtWH3').setValue('');
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
	
	var txtWH3 = {
		anchor: '100%',
		emptyText: 'Warehouse',
		fieldStyle: 'background-color: #eee; background-image: none;',
		hidden: false,
		id: 'txtWH3',
		name: 'txtWH3',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupProduct3 = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_kd_product','fs_nm_product'],
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
			url: 'regstatus/product'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_product': Ext.getCmp('cboProduct3').getValue(),
					'fs_nm_product': Ext.getCmp('cboProduct3').getValue(),
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
		store: grupProduct3,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Product Code", dataIndex: 'fs_kd_product', menuDisabled: true, width: 150},
			{text: "Product Name", dataIndex: 'fs_nm_product', menuDisabled: true, width: 330}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Product Code / Product Name',
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
					grupProduct3.load();
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
			store: grupProduct3,
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
				Ext.getCmp('cboProduct3').setValue(record.get('fs_kd_product'));
				Ext.getCmp('txtProduct3').setValue(record.get('fs_nm_product'));
				
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
				grupProduct3.load();
				vMask.show();
			}
		}
	});
	
	var cboProduct3 = {
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Product',
		id: 'cboProduct3',
		name: 'cboProduct3',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtProduct3').setValue('');
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
	
	var txtProduct3 = {
		anchor: '100%',
		emptyText: 'Product',
		fieldStyle: 'background-color: #eee; background-image: none;',
		hidden: false,
		id: 'txtProduct3',
		name: 'txtProduct3',
		readOnly: true,
		xtype: 'textfield'
	};

	function fnReset() {
		Ext.getCmp('cboWH').setValue('');
		Ext.getCmp('txtWH').setValue('');
		Ext.getCmp('cboProduct').setValue('');
		Ext.getCmp('txtProduct').setValue('');
		Ext.getCmp('cboCasis').setValue('');
		Ext.getCmp('cekSold').setValue(false);
		Ext.getCmp('cekAll').setValue(false);
	}

	function fnReset2() {
		Ext.getCmp('cboDept').setValue('');
		Ext.getCmp('txtDeptCd').setValue('');
		Ext.getCmp('txtCount').setValue('');
		Ext.getCmp('txtDept').setValue('');
		Ext.getCmp('cboWH2').setValue('');
		Ext.getCmp('txtWH2').setValue('');
		Ext.getCmp('cboProduct2').setValue('');
		Ext.getCmp('txtProduct2').setValue('');
		Ext.getCmp('cboCasis2').setValue('');
		Ext.getCmp('txtDate').setValue(new Date());
	}

	function fnReset3() {
		Ext.getCmp('cboWH3').setValue('');
		Ext.getCmp('txtWH3').setValue('');
		Ext.getCmp('cboProduct3').setValue('');
		Ext.getCmp('txtProduct3').setValue('');
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
		fnPB();
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'regstatus/printreg',
			params: {
				'fs_kd_wh': Ext.getCmp('cboWH').getValue(),
				'fs_kd_product': Ext.getCmp('cboProduct').getValue(),
				'fs_rangka': Ext.getCmp('cboCasis').getValue(),
				'fb_jual': Ext.getCmp('cekSold').getValue(),
				'fb_all': Ext.getCmp('cekAll').getValue(),
				'fd_tgl': tglDMY(Ext.Date.format(new Date(), 'Ymd'))
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
						title: 'Regstatus',
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
			url: 'regstatus/printreg2',
			params: {
				'fs_kd_dept': Ext.getCmp('txtDeptCd').getValue(),
				'fs_count': Ext.getCmp('txtCount').getValue(),
				'fs_kd_wh': Ext.getCmp('cboWH2').getValue(),
				'fs_kd_product': Ext.getCmp('cboProduct2').getValue(),
				'fs_rangka': Ext.getCmp('cboCasis2').getValue(),
				'fd_tgl': tglDMY(Ext.Date.format(new Date(), 'Ymd')),
				'fd_tgl2': Ext.Date.format(Ext.getCmp('txtDate').getValue(), 'Ymd')
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
						title: 'Regstatus Per Date',
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
			url: 'regstatus/printsum',
			params: {
				'fs_kd_wh': Ext.getCmp('cboWH3').getValue(),
				'fs_kd_product': Ext.getCmp('cboProduct3').getValue()
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
						title: 'Regstatus',
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
	
	var frmRegStatus = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Register Status Form',
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
				title: 'Register Status',
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
								cboWH,
								cboProduct
							]
						},{
							flex: 2,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtWH,
								txtProduct
							]
						}]
					},
						cboCasis,
					{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 0.33,
							layout: 'anchor',
							xtype: 'container'
						},{
							flex: 2,
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
										cekSold
									]
								},{
									flex: 5,
									layout: 'anchor',
									xtype: 'container',
									items: [
										cekAll
									]
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
				title: 'Register Status Per Date',
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
								cboDept,
								txtDeptCd,
								txtCount,
								cboWH2,
								cboProduct2
							]
						},{
							flex: 2,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtDept,
								txtWH2,
								txtProduct2
							]
						}]
					},
						cboCasis2,
						txtDate
					]
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
				hidden: false,
				title: 'Register Status Summary',
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
								cboWH3,
								cboProduct3
							]
						},{
							flex: 2,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtWH3,
								txtProduct3
							]
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
		target: frmRegStatus
	});
	
	function fnMaskShow() {
		frmRegStatus.mask('Please wait...');
	}

	function fnMaskHide() {
		frmRegStatus.unmask();
	}
	
	frmRegStatus.render(Ext.getBody());
	Ext.get('loading').destroy();
});
