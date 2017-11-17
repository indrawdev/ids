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
			html: 'Double click on the Qty to display the chassis',
			target: view.el,
			trackMouse: true
		});
	}

	var grupRefno = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_refno','fd_refno','fs_doc','fd_doc','fs_descrp',
			'fs_refnot',
			'fs_dept','fs_nm_dept','fs_kd_dept','fs_count',
			'fs_kd_wh','fs_nm_wh',
			'fs_tdept','fs_nm_tdept','fs_kd_tdept','fs_tcount',
			'fs_kd_wht','fs_nm_wht','fs_status'
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
			url: 'mutasiin/refno'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboRefno').getValue(),
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
			{text: "Ref. Date", dataIndex: 'fd_refno', menuDisabled: true, width: 80},
			{text: "Doc. No", dataIndex: 'fs_doc', menuDisabled: true, hidden: true},
			{text: "Doc. Date", dataIndex: 'fd_doc', menuDisabled: true, hidden: true},
			{text: "Note", dataIndex: 'fs_descrp', menuDisabled: true, width: 150},
			{text: "Ref. Out", dataIndex: 'fs_refnot', menuDisabled: true, hidden: true},
			{text: "From Dept", dataIndex: 'fs_tdept', menuDisabled: true, hidden: true},
			{text: "From Dept", dataIndex: 'fs_nm_tdept', menuDisabled: true, width: 100},
			{text: "From Dept Code", dataIndex: 'fs_kd_tdept', menuDisabled: true, hidden: true},
			{text: "From Dept Count", dataIndex: 'fs_tcount', menuDisabled: true, hidden: true},
			{text: "From Warehouse Code", dataIndex: 'fs_kd_wht', menuDisabled: true, hidden: true},
			{text: "From Warehouse", dataIndex: 'fs_nm_wht', menuDisabled: true, width: 100},
			{text: "To Dept", dataIndex: 'fs_dept', menuDisabled: true, hidden: true},
			{text: "To Dept", dataIndex: 'fs_nm_dept', menuDisabled: true, width: 100},
			{text: "To Dept Code", dataIndex: 'fs_kd_dept', menuDisabled: true, hidden: true},
			{text: "To Dept Count", dataIndex: 'fs_count', menuDisabled: true, hidden: true},
			{text: "To Warehouse Code", dataIndex: 'fs_kd_wh', menuDisabled: true, hidden: true},
			{text: "To Warehouse", dataIndex: 'fs_nm_wh', menuDisabled: true, width: 100},
			{text: "Status", dataIndex: 'fs_status', menuDisabled: true, width: 100}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Ref. No',
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
				Ext.getCmp('txtDocno').setValue(record.get('fs_doc'));
				Ext.getCmp('txtDocnodt').setValue(tglDMY(record.get('fd_doc')));
				Ext.getCmp('txtNote').setValue(record.get('fs_descrp'));
				Ext.getCmp('cboOut').setValue(record.get('fs_refnot'));
				Ext.getCmp('cboDeptDr').setValue(record.get('fs_tdept'));
				Ext.getCmp('txtDeptDr').setValue(record.get('fs_nm_tdept'));
				Ext.getCmp('txtDeptCdDr').setValue(record.get('fs_kd_tdept'));
				Ext.getCmp('txtCountDr').setValue(record.get('fs_tcount'));
				Ext.getCmp('cboWHDr').setValue(record.get('fs_kd_wht'));
				Ext.getCmp('txtWHDr').setValue(record.get('fs_nm_wht'));
				Ext.getCmp('cboDeptKe').setValue(record.get('fs_dept'));
				Ext.getCmp('txtDeptKe').setValue(record.get('fs_nm_dept'));
				Ext.getCmp('txtDeptCdKe').setValue(record.get('fs_kd_dept'));
				Ext.getCmp('txtCountKe').setValue(record.get('fs_count'));
				Ext.getCmp('cboWHKe').setValue(record.get('fs_kd_wh'));
				Ext.getCmp('txtWHKe').setValue(record.get('fs_nm_wh'));
				
				grupGridReg.load();
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
		labelWidth: 65,
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
		name: 'txtRefnodt',
		value: new Date(),
		xtype: 'datefield'
	};

	var txtDocno = {
		anchor: '95%',
		emptyText: 'Enter a Document Number',
		fieldLabel: 'Doc. No',
		id: 'txtDocno',
		labelWidth: 50,
		name: 'txtDocno',
		xtype: 'textfield'
	};

	var txtDocnodt = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		editable: true,
		fieldLabel: 'Doc. Date',
		format: 'd-m-Y',
		id: 'txtDocnodt',
		labelWidth: 65,
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
		name: 'txtDocnodt',
		value: new Date(),
		xtype: 'datefield'
	};

	var txtNote = {
		anchor: '58.7%',
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

	var grupOut = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_refno','fd_refno','fs_doc','fd_doc','fs_descrp',
			'fs_dept','fs_nm_dept','fs_kd_dept','fs_count',
			'fs_kd_wh','fs_nm_wh',
			'fs_tdept','fs_nm_tdept','fs_kd_tdept','fs_tcount',
			'fs_kd_wht','fs_nm_wht','fs_status'
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
			url: 'mutasiin/refnoout'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboOut').getValue(),
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
		store: grupOut,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Ref. No", dataIndex: 'fs_refno', menuDisabled: true, width: 170},
			{text: "Ref. Date", dataIndex: 'fd_refno', menuDisabled: true, width: 80},
			{text: "Doc. No", dataIndex: 'fs_doc', menuDisabled: true, hidden: true},
			{text: "Doc. Date", dataIndex: 'fd_doc', menuDisabled: true, hidden: true},
			{text: "Note", dataIndex: 'fs_descrp', menuDisabled: true, width: 150},
			{text: "From Dept", dataIndex: 'fs_dept', menuDisabled: true, hidden: true},
			{text: "From Dept", dataIndex: 'fs_nm_dept', menuDisabled: true, width: 100},
			{text: "From Dept Code", dataIndex: 'fs_kd_dept', menuDisabled: true, hidden: true},
			{text: "From Dept Count", dataIndex: 'fs_count', menuDisabled: true, hidden: true},
			{text: "From Warehouse Code", dataIndex: 'fs_kd_wh', menuDisabled: true, hidden: true},
			{text: "From Warehouse", dataIndex: 'fs_nm_wh', menuDisabled: true, width: 100},
			{text: "To Dept", dataIndex: 'fs_tdept', menuDisabled: true, hidden: true},
			{text: "To Dept", dataIndex: 'fs_nm_tdept', menuDisabled: true, width: 100},
			{text: "To Dept Code", dataIndex: 'fs_kd_tdept', menuDisabled: true, hidden: true},
			{text: "To Dept Count", dataIndex: 'fs_tcount', menuDisabled: true, hidden: true},
			{text: "To Warehouse Code", dataIndex: 'fs_kd_wht', menuDisabled: true, hidden: true},
			{text: "To Warehouse", dataIndex: 'fs_nm_wht', menuDisabled: true, width: 100},
			{text: "Status", dataIndex: 'fs_status', menuDisabled: true, width: 100}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Ref. No',
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
					grupOut.load();
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
			store: grupOut,
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
				Ext.getCmp('cboOut').setValue(record.get('fs_refno'));
				Ext.getCmp('txtNote').setValue(record.get('fs_descrp'));
				Ext.getCmp('cboDeptDr').setValue(record.get('fs_dept'));
				Ext.getCmp('txtDeptDr').setValue(record.get('fs_nm_dept'));
				Ext.getCmp('txtDeptCdDr').setValue(record.get('fs_kd_dept'));
				Ext.getCmp('txtCountDr').setValue(record.get('fs_count'));
				Ext.getCmp('cboWHDr').setValue(record.get('fs_kd_wh'));
				Ext.getCmp('txtWHDr').setValue(record.get('fs_nm_wh'));
				Ext.getCmp('cboDeptKe').setValue(record.get('fs_tdept'));
				Ext.getCmp('txtDeptKe').setValue(record.get('fs_nm_tdept'));
				Ext.getCmp('txtDeptCdKe').setValue(record.get('fs_kd_tdept'));
				Ext.getCmp('txtCountKe').setValue(record.get('fs_tcount'));
				Ext.getCmp('cboWHKe').setValue(record.get('fs_kd_wht'));
				Ext.getCmp('txtWHKe').setValue(record.get('fs_nm_wht'));
				
				grupGridReg.removeAll();
				grupGridReg.load();
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
				grupOut.load();
				vMask.show();
			}
		}
	});

	var cboOut = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: "Select a Mutation Out",
		fieldLabel: 'Mutation Out',
		id: 'cboOut',
		labelWidth: 90,
		name: 'cboOut',
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

	var cboDeptDr = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Department',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'cboDeptDr',
		labelWidth: 80,
		name: 'cboDeptDr',
		readOnly: true,
		xtype: 'textfield'
	};

	var txtDeptDr = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'From Department',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtDeptDr',
		name: 'txtDeptDr',
		readOnly: true,
		value: '',
		xtype: 'textfield'
	};

	var txtDeptCdDr = {
		anchor: '100%',
		emptyText: '',
		fieldLabel: 'Dept Code',
		hidden: true,
		id: 'txtDeptCdDr',
		labelWidth: 80,
		name: 'txtDeptCdDr',
		value: '',
		xtype: 'textfield'
	};

	var txtCountDr = {
		anchor: '100%',
		emptyText: '',
		fieldLabel: 'Dept Count',
		hidden: true,
		id: 'txtCountDr',
		labelWidth: 80,
		name: 'txtCountDr',
		value: '',
		xtype: 'textfield'
	};

	var cboWHDr = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Warehouse',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'cboWHDr',
		labelWidth: 80,
		name: 'cboWHDr',
		readOnly: true,
		xtype: 'textfield'
	};

	var txtWHDr = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'From Warehouse',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtWHDr',
		name: 'txtWHDr',
		readOnly: true,
		xtype: 'textfield'
	};

	var cboDeptKe = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Department',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'cboDeptKe',
		labelWidth: 80,
		name: 'cboDeptKe',
		readOnly: true,
		xtype: 'textfield'
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
		fieldStyle: 'background-color: #eee; background-image: none;',
		hidden: true,
		id: 'txtDeptCdKe',
		labelWidth: 80,
		name: 'txtDeptCdKe',
		xtype: 'textfield'
	};

	var txtCountKe = {
		anchor: '100%',
		emptyText: '',
		fieldLabel: 'Dept Count',
		fieldStyle: 'background-color: #eee; background-image: none;',
		hidden: true,
		id: 'txtCountKe',
		labelWidth: 80,
		name: 'txtCountKe',
		xtype: 'textfield'
	};

	var cboWHKe = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Warehouse',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'cboWHKe',
		labelWidth: 80,
		name: 'cboWHKe',
		readOnly: true,
		xtype: 'textfield'
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
			url: 'mutasiin/grid_prod'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_dept': Ext.getCmp('txtDeptCdKe').getValue(),
					'fs_count': Ext.getCmp('txtCountKe').getValue(),
					'fs_refno': Ext.getCmp('cboRefno').getValue(),
					'fs_kd_tdept': Ext.getCmp('txtDeptCdDr').getValue(),
					'fs_tcount': Ext.getCmp('txtCountDr').getValue(),
					'fs_kd_out': Ext.getCmp('cboOut').getValue()
				});
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
			xtype: 'rownumberer'
		},{
			text: 'Product Code',
			dataIndex: 'fs_kd_product',
			flex: 1,
			menuDisabled: true
		},{
			text: 'Product Name',
			dataIndex: 'fs_nm_product',
			flex: 1.25,
			menuDisabled: true
		},{
			text: 'Qty',
			align: 'right',
			dataIndex: 'fn_qty',
			flex: 0.25,
			menuDisabled: true,
			editor: {
				editable: false,
				xtype: 'textfield',
				triggers: {
					cari: {
						cls: 'x-form-search-trigger',
						handler: function() {
							vMask.show();
							
							var recordgrid = gridProd.getSelectionModel().getSelection()[0];
							var xKdProd = recordgrid.get('fs_kd_product');
							var xNmProd = recordgrid.get('fs_nm_product');
							
							Ext.getCmp('txtProdAktif').setValue(xKdProd);
							Ext.getCmp('txtProdAktif2').setValue(xKdProd + ' - ' + xNmProd);
							
							grupGridReg.clearFilter();
							var xTotal = grupGridReg.getCount();
							if (xTotal > 0) {
								grupGridReg.filterBy(function(record) {
									if (record.get('fs_kd_product').trim() == xKdProd.trim()) {
										return true;
									}
									else {
										return false;
									}
								});
								gridReg.getView().refresh();
							}
							winReg.show();
							winReg.center();
						}
					}
				}
			}
		},{
			text: 'Unit',
			dataIndex: 'fs_kd_unit',
			flex: 0.5,
			menuDisabled: true
		},{
			text: 'Seqno',
			dataIndex: 'fs_seqno',
			flex: 0.5,
			hidden: true,
			menuDisabled: true
		}],
		bbar: [{
			anchor: '100%',
			layout: 'hbox',
			xtype: 'container',
			items: [{
				flex: 1,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					id: 'txtToolTip',
					labelWidth: 65,
					name: 'txtToolTip',
					value: '<*Double click on the Qty to display the chassis>',
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
				}]
			}]
		}],
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
		}
	});

	Ext.define('DataGridReg', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_rangka', type: 'string'},
			{name: 'fs_mesin', type: 'string'},
			{name: 'fd_thn', type: 'string'},
			{name: 'fs_nm_warna', type: 'string'},
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
			url: 'mutasiin/grid_reg'
		},
		listeners: {
			beforeload: function(store) {
				grupGridReg.removeAll();
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_dept': Ext.getCmp('txtDeptCdDr').getValue(),
					'fs_count': Ext.getCmp('txtCountDr').getValue(),
					'fs_kd_out': Ext.getCmp('cboOut').getValue(),
					'fs_kd_tdept': Ext.getCmp('txtDeptCdKe').getValue(),
					'fs_tcount': Ext.getCmp('txtCountKe').getValue(),
					'fs_refno': Ext.getCmp('cboRefno').getValue()
				});
			}
		}
	});
	
	var gridReg = Ext.create('Ext.ux.LiveSearchGridPanel', {
		anchor: '100%',
		defaultType: 'textfield',
		height: 270,
		sortableColumns: false,
		store: grupGridReg,
		bbar: [{
			id: 'txtProdAktif2',
			name: 'txtProdAktif2',
			value: '',
			xtype: 'displayfield'
		},{
			xtype: 'tbfill'
		}],
		columns: [{
			xtype: 'rownumberer',
			width: 35
		},{
			text: 'Chassis',
			dataIndex: 'fs_rangka',
			flex: 1,
			menuDisabled: true
		},{
			text: 'Machine',
			dataIndex: 'fs_mesin',
			flex: 1,
			menuDisabled: true
		},{
			text: 'Year',
			dataIndex: 'fd_thn',
			flex: 0.2,
			menuDisabled: true
		},{
			text: 'Color',
			dataIndex: 'fs_nm_warna',
			flex: 1,
			menuDisabled: true
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
		height: 500,
		layout: 'fit',
		resizable: false,
		title: 'Register',
		width: 700,
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
	
	function fnCekSave() {
		if (this.up('form').getForm().isValid()) {
			grupGridReg.clearFilter();
			
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
				url: 'mutasiout/mutasi_ceksave',
				params: {
					'fs_refno': Ext.getCmp('cboRefno').getValue(),
					'fs_kd_dept': Ext.getCmp('txtDeptCdDr').getValue(),
					'fs_count': Ext.getCmp('txtCountDr').getValue(),
					'fs_kd_trx': '3300',
					'fs_refnoout': Ext.getCmp('cboOut').getValue(),
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
		var xqty = '';
		var xunit = '';
		var xseqno = '';
		var xprodreg = '';
		var xrangka = '';
		var xmesin = '';
		var xseqnoreg = '';
		
		var store = gridProd.getStore();
		store.each(function(record, idx) {
			xprod = xprod +'|'+ record.get('fs_kd_product');
			xqty = xqty +'|'+ record.get('fn_qty');
			xunit = xunit +'|'+ record.get('fs_kd_unit');
			xseqno = xseqno +'|'+ record.get('fs_seqno');
		});
		
		store = gridReg.getStore();
		store.each(function(record, idx) {
			xprodreg = xprodreg +'|'+ record.get('fs_kd_product');
			xrangka = xrangka +'|'+ record.get('fs_rangka');
			xmesin = xmesin +'|'+ record.get('fs_mesin');
			xseqnoreg = xseqnoreg +'|'+ record.get('fs_seqno');
		});
		
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'mutasiout/mutasi_save',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue(),
				'fd_refno': Ext.Date.format(Ext.getCmp('txtRefnodt').getValue(), 'Ymd'),
				'fs_docno': Ext.getCmp('txtDocno').getValue(),
				'fd_docno': Ext.Date.format(Ext.getCmp('txtDocnodt').getValue(), 'Ymd'),
				'fs_note': Ext.getCmp('txtNote').getValue(),
				'fs_refnoout': Ext.getCmp('cboOut').getValue(),
				'fs_kd_dept': Ext.getCmp('txtDeptCdKe').getValue(),
				'fs_count': Ext.getCmp('txtCountKe').getValue(),
				'fs_kd_wh': Ext.getCmp('cboWHKe').getValue(),
				'fs_nm_wh': Ext.getCmp('txtWHKe').getValue(),
				'fs_kd_deptt': Ext.getCmp('txtDeptCdDr').getValue(),
				'fs_countt': Ext.getCmp('txtCountDr').getValue(),
				'fs_kd_wht': Ext.getCmp('cboWHDr').getValue(),
				'fs_nm_wht': Ext.getCmp('txtWHDr').getValue(),
				'fs_kd_trx': '3300',
				'fs_kd_product': xprod,
				'fn_qty': xqty,
				'fs_kd_unit': xunit,
				'fs_seqno': xseqno,
				'fs_kd_prodreg': xprodreg,
				'fs_rangka': xrangka,
				'fs_mesin': xmesin,
				'fs_seqnoreg': xseqnoreg
			},
			success: function(response) {
				var xtext = Ext.decode(response.responseText);
				
				if (xtext.sukses === true) {
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});
					fnReset();
				}
				
				if (xtext.sukses === false) {
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
			url: 'mutasiout/mutasi_cekremove',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue(),
				'fs_kd_dept': Ext.getCmp('txtDeptCdKe').getValue(),
				'fs_count': Ext.getCmp('txtCountKe').getValue(),
				'fs_rangka': xrangka,
				'fs_mesin': xmesin,
				'fs_kd_trx': '3300',
				'fs_refnoout': Ext.getCmp('cboOut').getValue()
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
		var xprodreg = '';
		var xrangka = '';
		var xmesin = '';
		var xseqnoreg = '';
		
		var store = gridReg.getStore();
		store.each(function(record, idx) {
			xprodreg = xprodreg +'|'+ record.get('fs_kd_product');
			xrangka = xrangka +'|'+ record.get('fs_rangka');
			xmesin = xmesin +'|'+ record.get('fs_mesin');
			xseqnoreg = xseqnoreg +'|'+ record.get('fs_seqno');
		});
		
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'mutasiout/mutasi_remove',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue(),
				'fs_kd_dept': Ext.getCmp('txtDeptCdKe').getValue(),
				'fs_count': Ext.getCmp('txtCountKe').getValue(),
				'fs_kd_wh': Ext.getCmp('cboWHKe').getValue(),
				'fs_nm_wh': Ext.getCmp('txtWHKe').getValue(),
				'fs_kd_trx': '3300',
				'fs_refnoout': Ext.getCmp('cboOut').getValue(),
				'fs_kd_prodreg': xprodreg,
				'fs_rangka': xrangka,
				'fs_mesin': xmesin,
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
			url: 'mutasiin/print_mutasiin',
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
						title: 'Mutation In',
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
		Ext.getCmp('txtDocno').setValue('');
		Ext.getCmp('txtDocnodt').setValue(new Date());
		Ext.getCmp('txtNote').setValue('');
		Ext.getCmp('cboOut').setValue('');
		Ext.getCmp('cboDeptDr').setValue('');
		Ext.getCmp('txtDeptDr').setValue('');
		Ext.getCmp('txtDeptCdDr').setValue('');
		Ext.getCmp('txtCountDr').setValue('');
		Ext.getCmp('cboWHDr').setValue('');
		Ext.getCmp('txtWHDr').setValue('');
		Ext.getCmp('cboDeptKe').setValue('');
		Ext.getCmp('txtDeptKe').setValue('');
		Ext.getCmp('txtDeptCdKe').setValue('');
		Ext.getCmp('txtCountKe').setValue('');
		Ext.getCmp('cboWHKe').setValue('');
		Ext.getCmp('txtWHKe').setValue('');
		Ext.getCmp('txtProdAktif').setValue('');
		
		grupGridProd.removeAll();
		gridProd.getView().refresh();
		grupGridReg.removeAll();
		gridReg.getView().refresh();
	}

	var frmMutasiIn = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Mutation In Form',
		width: 800,
		fieldDefaults: {
			labelAlign: 'right',
			labelSeparator: '',
			msgTarget: 'side'
		},
		items: [{
			style: 'padding: 5px;',
			xtype: 'fieldset',
			items: [{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1.5,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						anchor: '98%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1.75,
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
					}]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						cboOut
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
					style: 'padding: 5px;',
					title: 'FROM',
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
										cboDeptDr
									]
								},{
									flex: 1.5,
									layout: 'anchor',
									xtype: 'container',
									items: [
										txtDeptDr
									]
								}]
							},
								txtDeptCdDr,
								txtCountDr,
							{
								anchor: '100%',
								layout: 'hbox',
								xtype: 'container',
								items: [{
									flex: 1,
									layout: 'anchor',
									xtype: 'container',
									items: [
										cboWHDr
									]
								},{
									flex: 1.5,
									layout: 'anchor',
									xtype: 'container',
									items: [
										txtWHDr
									]
								}]
							}]
						}]
					}]
				}]
			},{
				flex: 1,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					style: 'padding: 5px;',
					title: 'TO',
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
								items: [{
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
										flex: 1.5,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtDeptKe
										]
									}]
								},
									txtDeptCdKe,
									txtCountKe,{
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
										flex: 1.5,
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
		target: frmMutasiIn
	});
	
	function fnMaskShow() {
		frmMutasiIn.mask('Please wait...');
	}

	function fnMaskHide() {
		frmMutasiIn.unmask();
	}
	
	frmMutasiIn.render(Ext.getBody());
	Ext.get('loading').destroy();
});
