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

	function gridTooltipBeli(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Double click on the price to edit',
			target: view.el,
			trackMouse: true
		});
	}
	function gridTooltipJual(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Double click on the price, discount and bbn to edit',
			target: view.el,
			trackMouse: true
		});
	}

	function buatForm() {
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
				{text: "Product", dataIndex: 'fs_nm_product', menuDisabled: true, width: 280},
				{text: "Rangka", dataIndex: 'fs_rangka', menuDisabled: true, hidden: true},
				{text: "Mesin", dataIndex: 'fs_mesin', menuDisabled: true, hidden: true},
				{text: "CC", dataIndex: 'fn_cc', menuDisabled: true, hidden: true},
				{text: "Acno Cd", dataIndex: 'fs_kd_acno', menuDisabled: true, hidden: true},
				{text: "Acno", dataIndex: 'fs_nm_acno', menuDisabled: true, hidden: true},
				{text: "Acnox Cd", dataIndex: 'fs_kd_acnox', menuDisabled: true, hidden: true},
				{text: "Acnox", dataIndex: 'fs_nm_acnox', menuDisabled: true, hidden: true},
				{text: "Active", dataIndex: 'fb_active', menuDisabled: true, hidden: true},
				{text: "Status", dataIndex: 'fs_status', menuDisabled: true, width: 100}
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
					Ext.getCmp('cekAktif').setValue(record.get('fb_active'));
					Ext.getCmp('txtProduk').setValue(record.get('fs_nm_product'));
					Ext.getCmp('txtRangka').setValue(record.get('fs_rangka'));
					Ext.getCmp('txtMesin').setValue(record.get('fs_mesin'));
					Ext.getCmp('txtCC').setValue(record.get('fn_cc'));
					Ext.getCmp('cboAcno').setValue(record.get('fs_kd_acno'));
					Ext.getCmp('txtAcno').setValue(record.get('fs_nm_acno'));
					Ext.getCmp('cboAcnox').setValue(record.get('fs_kd_acnox'));
					Ext.getCmp('txtAcnox').setValue(record.get('fs_nm_acnox'));
					
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
			anchor: '95%',
			emptyText: 'Select / Enter a Product Code',
			fieldLabel: 'Code',
			id: 'cboProduk',
			name: 'cboProduk',
			xtype: 'textfield',
			listeners: {
				change: function(field, newValue) {
					Ext.getCmp('txtProduk').setValue('');
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

		var cekAktif = {
			boxLabel: 'Active',
			checked: false,
			id: 'cekAktif',
			name: 'cekAktif',
			xtype: 'checkboxfield'
		};

		var txtProduk = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '80%',
			emptyText: 'Enter a Product Name',
			fieldLabel: 'Name',
			id: 'txtProduk',
			name: 'txtProduk',
			xtype: 'textfield'
		};

		var txtRangka = {
			anchor: '50%',
			emptyText: 'Enter a Chassis No',
			fieldLabel: 'Chassis No',
			id: 'txtRangka',
			name: 'txtRangka',
			xtype: 'textfield'
		};

		var txtMesin = {
			anchor: '50%',
			emptyText: 'Enter a Machine No',
			fieldLabel: 'Machine No',
			id: 'txtMesin',
			name: 'txtMesin',
			xtype: 'textfield'
		};
		
		var txtCC = Ext.create('Ext.ux.form.NumericField', {
			afterLabelTextTpl: required,
			allowBlank: false,
			alwaysDisplayDecimals: false,
			anchor: '25%',
			currencySymbol: null,
			decimalPrecision: 2,
			decimalSeparator: '.',
			fieldLabel: 'Cylinder',
			fieldStyle: 'text-align: right;',
			hideTrigger: false,
			id: 'txtCC',
			keyNavEnabled: true,
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
		});

		var grupAcno = Ext.create('Ext.data.Store', {
			autoLoad: false,
			fields: [
				'fs_kd_acno','fs_nm_acno'
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
				url: 'accgl/acno'
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_kd_acno': Ext.getCmp('cboAcno').getValue(),
						'fs_nm_acno': Ext.getCmp('cboAcno').getValue(),
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
			store: grupAcno,
			columns: [
				{xtype: 'rownumberer', width: 45},
				{text: "Acc. No", dataIndex: 'fs_kd_acno', menuDisabled: true, width: 100},
				{text: "Account", dataIndex: 'fs_nm_acno', menuDisabled: true, width: 380}
			],
			tbar: [{
				flex: 1,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					anchor: '98%',
					emptyText: 'Acc. No / Account',
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
						grupAcno.load();
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
				store: grupAcno,
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
					Ext.getCmp('cboAcno').setValue(record.get('fs_kd_acno'));
					Ext.getCmp('txtAcno').setValue(record.get('fs_nm_acno'));
					
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
					grupAcno.load();
					vMask.show();
				}
			}
		});

		var cboAcno = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '98%',
			emptyText: 'Select an',
			fieldLabel: 'Acc. No',
			id: 'cboAcno',
			name: 'cboAcno',
			xtype: 'textfield',
			listeners: {
				change: function(field, newValue) {
					Ext.getCmp('txtAcno').setValue('');
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

		var txtAcno = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '100%',
			emptyText: 'Account Number',
			fieldStyle: 'background-color: #eee; background-image: none;',
			id: 'txtAcno',
			name: 'txtAcno',
			readOnly: true,
			xtype: 'textfield'
		};

		var grupAcnox = Ext.create('Ext.data.Store', {
			autoLoad: false,
			fields: [
				'fs_kd_acno','fs_nm_acno'
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
				url: 'accgl/acno'
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_kd_acno': Ext.getCmp('cboAcnox').getValue(),
						'fs_nm_acno': Ext.getCmp('cboAcnox').getValue(),
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
			store: grupAcnox,
			columns: [
				{xtype: 'rownumberer', width: 45},
				{text: "Acc. No", dataIndex: 'fs_kd_acno', menuDisabled: true, width: 100},
				{text: "Account", dataIndex: 'fs_nm_acno', menuDisabled: true, width: 380}
			],
			tbar: [{
				flex: 1,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					anchor: '98%',
					emptyText: 'Acc. No / Account',
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
						grupAcnox.load();
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
				store: grupAcnox,
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
					Ext.getCmp('cboAcnox').setValue(record.get('fs_kd_acno'));
					Ext.getCmp('txtAcnox').setValue(record.get('fs_nm_acno'));
					
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
					grupAcnox.load();
					vMask.show();
				}
			}
		});

		var cboAcnox = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '98%',
			emptyText: 'Select an',
			fieldLabel: 'Acc.X No',
			id: 'cboAcnox',
			name: 'cboAcnox',
			xtype: 'textfield',
			listeners: {
				change: function(field, newValue) {
					Ext.getCmp('txtAcnox').setValue('');
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

		var txtAcnox = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '100%',
			emptyText: 'AccountX Number',
			fieldStyle: 'background-color: #eee; background-image: none;',
			id: 'txtAcnox',
			name: 'txtAcnox',
			readOnly: true,
			xtype: 'textfield'
		};

		var cekDB = {
			boxLabel: 'Save to another companies',
			checked: true,
			id: 'cekDB',
			name: 'cekDB',
			xtype: 'checkboxfield',
			listeners: {
				change: function(field, newValue) {
					if (field.getValue() == true) {
						gridComp.setDisabled(false);
					}
					else {
						gridComp.setDisabled(true);
					}
				}
			}
		};

		Ext.define('DataGridComp', {
			extend: 'Ext.data.Model',
			fields: [
				{name: 'fs_kd_comp', type: 'string'},
				{name: 'fs_nm_comp', type: 'string'},
				{name: 'fs_nm_db', type: 'string'}
			]
		});

		var grupGridComp = Ext.create('Ext.data.Store', {
			autoLoad: true,
			model: 'DataGridComp',
			proxy: {
				actionMethods: {
					read: 'POST'
				},
				reader: {
					type: 'json'
				},
				type: 'ajax',
				url: 'mutasioutdb/ambil_comp2'
			}
		});
		
		var gridComp = Ext.create('Ext.grid.Panel', {
			anchor: '100%',
			autoDestroy: true,
			height: 160,
			sortableColumns: false,
			store: grupGridComp,
			columns: [{
				xtype: 'rownumberer',
				width: 35
			},{
				flex: 0.5,
				dataIndex: 'fs_kd_comp',
				hidden: true,
				menuDisabled: true,
				text: 'Comp Code'
			},{
				flex: 2,
				dataIndex: 'fs_nm_comp',
				menuDisabled: true,
				text: 'Company'
			},{
				flex: 1,
				dataIndex: 'fs_nm_db',
				menuDisabled: true,
				text: 'DB Name'
			}],
			viewConfig: {
				getRowClass: function() {
					return 'rowwrap';
				},
				markDirty: false,
				stripeRows: true
			}
		});

		Ext.define('DataGridDetil', {
			extend: 'Ext.data.Model',
			fields: [
				{name: 'fs_kd_product', type: 'string'},
				{name: 'fs_nm_product', type: 'string'},
				{name: 'fs_rangka', type: 'string'},
				{name: 'fs_mesin', type: 'string'},
				{name: 'fn_cc', type: 'string'},
				{name: 'fs_kd_acno', type: 'string'},
				{name: 'fs_kd_acnox', type: 'string'},
				{name: 'fb_active', type: 'bool'}
			]
		});

		var grupGridDetil = Ext.create('Ext.data.Store', {
			autoLoad: true,
			model: 'DataGridDetil',
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
				url: 'produk/griddetil'
			}
		});
		
		var gridDetil = Ext.create('Ext.grid.Panel', {
			anchor: '100%',
			autoDestroy: true,
			height: 450,
			sortableColumns: false,
			store: grupGridDetil,
			columns: [{
				xtype: 'rownumberer',
				width: 35
			},{
				dataIndex: 'fs_kd_product',
				locked: true,
				menuDisabled: true,
				text: 'Code',
				width: 125
			},{
				dataIndex: 'fs_nm_product',
				menuDisabled: true,
				text: 'Product',
				width: 170
			},{
				dataIndex: 'fs_rangka',
				menuDisabled: true,
				text: 'Chassis No',
				width: 125
			},{
				dataIndex: 'fs_mesin',
				menuDisabled: true,
				text: 'Machine No',
				width: 125
			},{
				align: 'center',
				dataIndex: 'fn_cc',
				menuDisabled: true,
				text: 'Cylinder',
				width: 50
			},{
				dataIndex: 'fs_kd_acno',
				menuDisabled: true,
				text: 'Acc. No',
				width: 125
			},{
				dataIndex: 'fs_kd_acnox',
				menuDisabled: true,
				text: 'Acc.X No',
				width: 100
			},{
				align: 'center',
				dataIndex: 'fb_active',
				menuDisabled: true,
				stopSelection: false,
				text: 'Active',
				width: 55,
				xtype: 'checkcolumn'
			}],
			bbar: Ext.create('Ext.PagingToolbar', {
				displayInfo: true,
				pageSize: 25,
				plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
				store: grupGridDetil
			}),
			viewConfig: {
				getRowClass: function() {
					return 'rowwrap';
				},
				markDirty: false,
				stripeRows: true
			}
		});

		var grupDeptBeli = Ext.create('Ext.data.Store', {
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
						'fs_code': Ext.getCmp('cboDeptBeli').getValue(),
						'fs_nm_code': Ext.getCmp('cboDeptBeli').getValue(),
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
			store: grupDeptBeli,
			columns: [
				{xtype: 'rownumberer', width: 45},
				{text: "Dept Code", dataIndex: 'fs_code', menuDisabled: true, hidden:true},
				{text: "Dept Count", dataIndex: 'fs_count', menuDisabled: true, hidden:true},
				{text: "Dept Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 500}
			],
			tbar: [{
				flex: 1,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					anchor: '98%',
					emptyText: 'Dept Code / Dept Name',
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
						grupDeptBeli.load();
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
				store: grupDeptBeli,
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
					Ext.getCmp('cboDeptBeli').setValue(record.get('fs_code').concat(record.get('fs_count')));
					Ext.getCmp('txtDeptCdBeli').setValue(record.get('fs_code'));
					Ext.getCmp('txtCountBeli').setValue(record.get('fs_count'));
					Ext.getCmp('txtDeptBeli').setValue(record.get('fs_nm_code'));
					
					var xKdDept = Ext.getCmp('txtDeptCdBeli').getValue();
					var xKdCount = Ext.getCmp('txtCountBeli').getValue();
					if (xKdDept.trim() !== '' && xKdCount.trim() !== '') {
						grupGridBeli.clearFilter();
						var xTotal = grupGridBeli.getCount();
						if (xTotal > 0) {
							grupGridBeli.filterBy(function(record) {
								if (record.get('fs_kd_dept').trim() == xKdDept.trim() &&
									record.get('fs_count').trim() == xKdCount.trim()) {
									return true;
								}
								else {
									return false;
								}
							});
							gridBeli.getView().refresh();
						}
					}
					
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
					grupDeptBeli.load();
					vMask.show();
				}
			}
		});
		
		var cboDeptBeli = {
			anchor: '98%',
			emptyText: 'Select a',
			fieldLabel: 'Department',
			id: 'cboDeptBeli',
			name: 'cboDeptBeli',
			xtype: 'textfield',
			listeners: {
				change: function(field, newValue) {
					Ext.getCmp('txtDeptBeli').setValue('');
					Ext.getCmp('txtDeptCdBeli').setValue('');
					Ext.getCmp('txtCountBeli').setValue('');
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
		
		var txtDeptCdBeli = {
			anchor: '98%',
			fieldLabel: 'Dept Code',
			hidden: true,
			id: 'txtDeptCdBeli',
			name: 'txtDeptCdBeli',
			xtype: 'textfield'
		};
		
		var txtCountBeli = {
			anchor: '98%',
			fieldLabel: 'Dept Count',
			hidden: true,
			id: 'txtCountBeli',
			name: 'txtCountBeli',
			xtype: 'textfield'
		};
		
		var txtDeptBeli = {
			anchor: '100%',
			emptyText: 'Department',
			fieldStyle: 'background-color: #eee; background-image: none;',
			id: 'txtDeptBeli',
			name: 'txtDeptBeli',
			readOnly: true,
			xtype: 'textfield'
		};

		Ext.define('DataGridBeli', {
			extend: 'Ext.data.Model',
			fields: [
				{name: 'fs_kd_dept', type: 'string'},
				{name: 'fs_count', type: 'string'},
				{name: 'fs_kd_product', type: 'string'},
				{name: 'fs_nm_product', type: 'string'},
				{name: 'fn_harga', type: 'float'},
				{name: 'fn_diskon', type: 'float'},
				{name: 'fn_deposit', type: 'float'}
			]
		});

		var grupGridBeli = Ext.create('Ext.data.Store', {
			autoLoad: true,
			model: 'DataGridBeli',
			proxy: {
				actionMethods: {
					read: 'POST'
				},
				reader: {
					type: 'json'
				},
				type: 'ajax',
				url: 'produk/gridbeli'
			},
			listeners: {
				load: function() {
					var xKdDept = Ext.getCmp('txtDeptCdBeli').getValue();
					var xKdCount = Ext.getCmp('txtCountBeli').getValue();
					
					grupGridBeli.clearFilter();
					if (xKdDept.trim() !== '' && xKdCount.trim() !== '') {
						var xTotal = grupGridBeli.getCount();
						if (xTotal > 0) {
							grupGridBeli.filterBy(function(record) {
								if (record.get('fs_kd_dept').trim() == xKdDept.trim() &&
									record.get('fs_count').trim() == xKdCount.trim()) {
									return true;
								}
								else {
									return false;
								}
							});
							gridBeli.getView().refresh();
						}
					}
				}
			}
		});
		
		var cellEditingBeli = Ext.create('Ext.grid.plugin.CellEditing', {
			clicksToEdit: 2
		});
		
		var gridBeli = Ext.create('Ext.grid.Panel', {
			anchor: '100%',
			autoDestroy: true,
			height: 372,
			sortableColumns: false,
			store: grupGridBeli,
			columns: [{
				xtype: 'rownumberer',
				width: 35
			},{
				dataIndex: 'fs_kd_dept',
				hidden: true,
				menuDisabled: true,
				text: 'Dept'
			},{
				dataIndex: 'fs_count',
				hidden: true,
				menuDisabled: true,
				text: 'Count'
			},{
				dataIndex: 'fs_kd_product',
				menuDisabled: true,
				text: 'Code',
				width: 125
			},{
				dataIndex: 'fs_nm_product',
				menuDisabled: true,
				text: 'Product',
				width: 175
			},{
				align: 'right',
				dataIndex: 'fn_harga',
				format: '0,000',
				menuDisabled: true,
				text: 'Price',
				width: 100,
				xtype: 'numbercolumn',
				editor: [
					Ext.create('Ext.ux.form.NumericField', {
						alwaysDisplayDecimals: false,
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
				dataIndex: 'fn_diskon',
				format: '0,000',
				menuDisabled: true,
				text: 'Discount',
				width: 80,
				xtype: 'numbercolumn',
				editor: [
					Ext.create('Ext.ux.form.NumericField', {
						alwaysDisplayDecimals: false,
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
				dataIndex: 'fn_deposit',
				format: '0,000',
				menuDisabled: true,
				text: 'Deposit',
				width: 95,
				xtype: 'numbercolumn',
				editor: [
					Ext.create('Ext.ux.form.NumericField', {
						alwaysDisplayDecimals: false,
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
			plugins: [
				cellEditingBeli
			],
			viewConfig: {
				getRowClass: function() {
					return 'rowwrap';
				},
				listeners: {
					render: gridTooltipBeli
				},
				markDirty: false,
				stripeRows: true
			}
		});

		var grupDeptJual = Ext.create('Ext.data.Store', {
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
						'fs_code': Ext.getCmp('cboDeptJual').getValue(),
						'fs_nm_code': Ext.getCmp('cboDeptJual').getValue(),
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
			store: grupDeptJual,
			columns: [
				{xtype: 'rownumberer', width: 45},
				{text: "Dept Code", dataIndex: 'fs_code', menuDisabled: true, hidden:true},
				{text: "Dept Count", dataIndex: 'fs_count', menuDisabled: true, hidden:true},
				{text: "Dept Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 500}	
			],
			tbar: [{
				flex: 1,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					anchor: '98%',
					emptyText: 'Dept Code / Dept Name',
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
						grupDeptJual.load();
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
				store: grupDeptJual,
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
					Ext.getCmp('cboDeptJual').setValue(record.get('fs_code').concat(record.get('fs_count')));
					Ext.getCmp('txtDeptCdJual').setValue(record.get('fs_code'));
					Ext.getCmp('txtCountJual').setValue(record.get('fs_count'));
					Ext.getCmp('txtDeptJual').setValue(record.get('fs_nm_code'));
					
					var xKdDept = Ext.getCmp('txtDeptCdJual').getValue();
					var xKdCount = Ext.getCmp('txtCountJual').getValue();
					
					if (xKdDept.trim() !== '' && xKdCount.trim() !== '') {
						grupGridJual.clearFilter();
						var xTotal = grupGridJual.getCount();
						if (xTotal > 0) {
							grupGridJual.filterBy(function(record) {
								if (record.get('fs_kd_dept').trim() == xKdDept.trim() &&
									record.get('fs_count').trim() == xKdCount.trim()) {
									return true;
								}
								else {
									return false;
								}
							});
							gridJual.getView().refresh();
						}
					}
					
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
					grupDeptJual.load();
					vMask.show();
				}
			}
		});
			
		var cboDeptJual = {
			anchor: '98%',
			emptyText: 'Select a',
			fieldLabel: 'Department',
			id: 'cboDeptJual',
			name: 'cboDeptJual',
			xtype: 'textfield',
			listeners: {
				change: function(field, newValue) {
					Ext.getCmp('txtDeptJual').setValue('');
					Ext.getCmp('txtDeptCdJual').setValue('');
					Ext.getCmp('txtCountJual').setValue('');
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
		
		var txtDeptCdJual = {
			anchor: '98%',
			fieldLabel: 'Dept Code',
			hidden: true,
			id: 'txtDeptCdJual',
			name: 'txtDeptCdJual',
			xtype: 'textfield'
		};
		
		var txtCountJual = {
			anchor: '98%',
			fieldLabel: 'Dept Count',
			hidden: true,
			id: 'txtCountJual',
			name: 'txtCountJual',
			xtype: 'textfield'
		};
		
		var txtDeptJual = {
			anchor: '100%',
			emptyText: 'Department',
			fieldStyle: 'background-color: #eee; background-image: none;',
			id: 'txtDeptJual',
			name: 'txtDeptJual',
			readOnly: true,
			xtype: 'textfield'
		};

		Ext.define('DataGridJual', {
			extend: 'Ext.data.Model',
			fields: [
				{name: 'fs_kd_dept', type: 'string'},
				{name: 'fs_count', type: 'string'},
				{name: 'fs_kd_product', type: 'string'},
				{name: 'fs_nm_product', type: 'string'},
				{name: 'fn_harga', type: 'float'},
				{name: 'fn_diskon', type: 'float'},
				{name: 'fn_bbn', type: 'float'},
				{name: 'fn_dp', type: 'float'}
			]
		});

		var grupGridJual = Ext.create('Ext.data.Store', {
			autoLoad: true,
			model: 'DataGridJual',
			proxy: {
				actionMethods: {
					read: 'POST'
				},
				reader: {
					type: 'json'
				},
				type: 'ajax',
				url: 'produk/gridjual'
			},
			listeners: {
				load: function() {
					var xKdDept = Ext.getCmp('txtDeptCdJual').getValue();
					var xKdCount = Ext.getCmp('txtCountJual').getValue();
					
					grupGridJual.clearFilter();
					if (xKdDept.trim() !== '' && xKdCount.trim() !== '') {
						var xTotal = grupGridJual.getCount();
						if (xTotal > 0) {
							grupGridJual.filterBy(function(record) {
								if (record.get('fs_kd_dept').trim() == xKdDept.trim() &&
									record.get('fs_count').trim() == xKdCount.trim()) {
									return true;
								}
								else {
									return false;
								}
							});
							gridJual.getView().refresh();
						}
					}
				}
			}
		});

		var cellEditingJual = Ext.create('Ext.grid.plugin.CellEditing', {
			clicksToEdit: 2
		});
		
		var gridJual = Ext.create('Ext.grid.Panel', {
			anchor: '100%',
			autoDestroy: true,
			height: 372,
			sortableColumns: false,
			store: grupGridJual,
			columns: [{
				xtype: 'rownumberer',
				width: 35
			},{
				dataIndex: 'fs_kd_dept',
				hidden: true,
				menuDisabled: true,
				text: 'Dept'
			},{
				dataIndex: 'fs_count',
				hidden: true,
				menuDisabled: true,
				text: 'Count'
			},{
				dataIndex: 'fs_kd_product',
				menuDisabled: true,
				text: 'Code',
				width: 125
			},{
				dataIndex: 'fs_nm_product',
				menuDisabled: true,
				text: 'Product',
				width: 110
			},{
				align: 'right',
				dataIndex: 'fn_harga',
				format: '0,000',
				menuDisabled: true,
				text: 'Price',
				width: 100,
				xtype: 'numbercolumn',
				editor: [
					Ext.create('Ext.ux.form.NumericField', {
						alwaysDisplayDecimals: false,
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
				dataIndex: 'fn_diskon',
				format: '0,000',
				menuDisabled: true,
				text: 'Discount',
				width: 80,
				xtype: 'numbercolumn',
				editor: [
					Ext.create('Ext.ux.form.NumericField', {
						alwaysDisplayDecimals: false,
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
				dataIndex: 'fn_bbn',
				format: '0,000',
				menuDisabled: true,
				text: 'BBN',
				width: 80,
				xtype: 'numbercolumn',
				editor: [
					Ext.create('Ext.ux.form.NumericField', {
						alwaysDisplayDecimals: false,
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
				dataIndex: 'fn_dp',
				format: '0,000',
				menuDisabled: true,
				text: 'DP Max',
				width: 80,
				xtype: 'numbercolumn',
				editor: [
					Ext.create('Ext.ux.form.NumericField', {
						alwaysDisplayDecimals: false,
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
			plugins: [
				cellEditingJual
			],
			viewConfig: {
				getRowClass: function() {
					return 'rowwrap';
				},
				listeners: {
					render: gridTooltipJual
				},
				markDirty: false,
				stripeRows: true
			}
		});

		function fnCekSave() {
			if (this.up('form').getForm().isValid()) {
				Ext.Ajax.request({
					method: 'POST',
					url: 'produk/ceksave',
					params: {
						'fs_kd_product': Ext.getCmp('cboProduk').getValue()
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
						vMask.hide();
					}
				});
			}
		}

		function fnSave() {
			var xkdcomp = '';
			var xnmcomp = '';
			var xnmdb = '';
			
			var xstore = gridComp.getStore();
			xstore.each(function(record, idx) {
				if (record.get('fn_harga') !== 0) {
					xkdcomp = xkdcomp +'|'+ record.get('fs_kd_comp');
					xnmcomp = xnmcomp +'|'+ record.get('fs_nm_comp');
					xnmdb = xnmdb +'|'+ record.get('fs_nm_db');
				}
			});
			
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'produk/save',
				params: {
					'fs_kd_product': Ext.getCmp('cboProduk').getValue(),
					'fs_nm_product': Ext.getCmp('txtProduk').getValue(),
					'fs_rangka': Ext.getCmp('txtRangka').getValue(),
					'fs_mesin': Ext.getCmp('txtMesin').getValue(),
					'fn_cc': Ext.getCmp('txtCC').getValue(),
					'fs_kd_acno': Ext.getCmp('cboAcno').getValue(),
					'fs_kd_acnox': Ext.getCmp('cboAcnox').getValue(),
					'fb_active': Ext.getCmp('cekAktif').getValue(),
					'fb_db': Ext.getCmp('cekDB').getValue(),
					'fs_kd_comp': xkdcomp,
					'fs_nm_comp': xnmcomp,
					'fs_nm_db': xnmdb
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

		function fnCekSave2() {
			if (this.up('form').getForm().isValid()) {
				Ext.Ajax.request({
					method: 'POST',
					url: 'produk/ceksave2',
					params: {
						'fs_kd_dept': Ext.getCmp('txtDeptCdBeli').getValue(),
						'fs_count': Ext.getCmp('txtCountBeli').getValue()
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
								fnSave2();
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
											fnSave2();
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
						vMask.hide();
					}
				});
			}
		}

		function fnSave2() {
			var xprod = '';
			var xharga = '';
			var xdiskon = '';
			var xdeposit = '';
			
			var xstore = gridBeli.getStore();
			xstore.each(function(record, idx) {
				if (record.get('fn_harga') !== 0) {
					xprod = xprod +'|'+ record.get('fs_kd_product');
					xharga = xharga +'|'+ record.get('fn_harga');
					xdiskon = xdiskon +'|'+ record.get('fn_diskon');
					xdeposit = xdeposit +'|'+ record.get('fn_deposit');
				}
			});
			
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'produk/save2',
				params: {
					'fs_kd_dept': Ext.getCmp('txtDeptCdBeli').getValue(),
					'fs_count': Ext.getCmp('txtCountBeli').getValue(),
					'fs_kd_product': xprod,
					'fn_harga': xharga,
					'fn_diskon': xdiskon,
					'fn_deposit': xdeposit
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
						grupGridBeli.load();
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

		function fnCekSave3() {
			if (this.up('form').getForm().isValid()) {
				Ext.Ajax.request({
					method: 'POST',
					url: 'produk/ceksave3',
					params: {
						'fs_kd_dept': Ext.getCmp('txtDeptCdJual').getValue(),
						'fs_count': Ext.getCmp('txtCountJual').getValue()
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
								fnSave3();
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
											fnSave3();
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
						vMask.hide();
					}
				});
			}
		}

		function fnSave3() {
			var xprod = '';
			var xharga = '';
			var xdiskon = '';
			var xbbn = '';
			var xdp = '';
			
			var xstore = gridJual.getStore();
			xstore.each(function(record, idx) {
				if (record.get('fn_harga') !== 0) {
					xprod = xprod +'|'+ record.get('fs_kd_product');
					xharga = xharga +'|'+ record.get('fn_harga');
					xdiskon = xdiskon +'|'+ record.get('fn_diskon');
					xbbn = xbbn +'|'+ record.get('fn_bbn');
					xdp = xdp +'|'+ record.get('fn_dp');
				}
			});
			
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'produk/save3',
				params: {
					'fs_kd_dept': Ext.getCmp('txtDeptCdJual').getValue(),
					'fs_count': Ext.getCmp('txtCountJual').getValue(),
					'fs_kd_product': xprod,
					'fn_harga': xharga,
					'fn_diskon': xdiskon,
					'fn_bbn': xbbn,
					'fn_dp': xdp
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
						grupGridJual.load();
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

		function fnReset() {
			Ext.getCmp('cboProduk').setValue('');
			Ext.getCmp('cekAktif').setValue(true);
			Ext.getCmp('txtProduk').setValue('');
			Ext.getCmp('txtRangka').setValue('');
			Ext.getCmp('txtMesin').setValue('');
			Ext.getCmp('txtCC').setValue(0);
			Ext.getCmp('cboAcno').setValue('');
			Ext.getCmp('txtAcno').setValue('');
			Ext.getCmp('cboAcnox').setValue('');
			Ext.getCmp('txtAcnox').setValue('');
			Ext.getCmp('cekDB').setValue(true);
			
			grupGridComp.load();
			grupGridDetil.load();
		}
		
		function fnReset2() {
			Ext.getCmp('cboDeptBeli').setValue('');
			Ext.getCmp('txtDeptCdBeli').setValue('');
			Ext.getCmp('txtCountBeli').setValue('');
			Ext.getCmp('txtDeptBeli').setValue('');
			
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'alamat/nilaidefa',
				success: function(response) {
					var xtext = Ext.decode(response.responseText);
					
					if (xtext.sukses === true) {
						Ext.getCmp('cboDeptBeli').setValue(xtext.kddept);
						Ext.getCmp('txtDeptCdBeli').setValue(xtext.deptcd);
						Ext.getCmp('txtCountBeli').setValue(xtext.count);
						Ext.getCmp('txtDeptBeli').setValue(xtext.nmdept);
						
						grupGridBeli.load();
					}
				},
				failure: function(response) {
					var xText = Ext.decode(response.responseText);
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						message: 'Load default value Failed, Connection Failed!!',
						title: 'IDS'
					});
					fnMaskHide();
				}
			});
		}
		
		function fnReset3() {
			Ext.getCmp('cboDeptJual').setValue('');
			Ext.getCmp('txtDeptCdJual').setValue('');
			Ext.getCmp('txtCountJual').setValue('');
			Ext.getCmp('txtDeptJual').setValue('');
			
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'alamat/nilaidefa',
				success: function(response) {
					var xtext = Ext.decode(response.responseText);
					
					if (xtext.sukses === true) {
						Ext.getCmp('cboDeptJual').setValue(xtext.kddept);
						Ext.getCmp('txtDeptCdJual').setValue(xtext.deptcd);
						Ext.getCmp('txtCountJual').setValue(xtext.count);
						Ext.getCmp('txtDeptJual').setValue(xtext.nmdept);
						
						grupGridJual.load();
					}
				},
				failure: function(response) {
					var xText = Ext.decode(response.responseText);
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						message: 'Load default value Failed, Connection Failed!!',
						title: 'IDS'
					});
					fnMaskHide();
				}
			});
		}
		
		var frmProduk = Ext.create('Ext.form.Panel', {
			border: false,
			frame: true,
			region: 'center',
			title: 'Unit Product Master Form',
			width: 650,
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
					title: 'Product Entry',
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
								flex: 1.5,
								layout: 'anchor',
								xtype: 'container',
								items: [
									cboProduk
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									cekAktif
								]
							}]
						},
							txtProduk,
							txtRangka,
							txtMesin,
							txtCC,
						{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									cboAcno,
									cboAcnox
								]
							},{
								flex: 1.6,
								layout: 'anchor',
								xtype: 'container',
								items: [
									txtAcno,
									txtAcnox
								]
							}]
						}]
					},
						cekDB,
						gridComp
					],
					buttons: [{
						text: 'Save',
						handler: fnCekSave
					},{
						text: 'Reset',
						handler: fnReset
					}]
				},{
					bodyStyle: 'background-color: '.concat(gBasePanel),
					border: false,
					frame: false,
					title: 'Product List',
					xtype: 'form',
					items: [
						gridDetil
					]
				},{
					bodyStyle: 'background-color: '.concat(gBasePanel),
					border: false,
					frame: false,
					title: 'Default Price Purchase',
					xtype: 'form',
					items: [{
						fieldDefaults: {
							labelAlign: 'right',
							labelSeparator: '',
							labelWidth: 70,
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
									cboDeptBeli,
									txtDeptCdBeli,
									txtCountBeli
								]
							},{
								flex: 1.6,
								layout: 'anchor',
								xtype: 'container',
								items: [
									txtDeptBeli
								]
							}]
						}]
					},
						gridBeli
					],
					buttons: [{
						text: 'Save',
						handler: fnCekSave2
					},{
						text: 'Reset',
						handler: fnReset2
					}]
				},{
					bodyStyle: 'background-color: '.concat(gBasePanel),
					border: false,
					frame: false,
					title: 'Default Price Sales',
					xtype: 'form',
					items: [{
						fieldDefaults: {
							labelAlign: 'right',
							labelSeparator: '',
							labelWidth: 70,
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
									cboDeptJual,
									txtDeptCdJual,
									txtCountJual
								]
							},{
								flex: 1.6,
								layout: 'anchor',
								xtype: 'container',
								items: [
									txtDeptJual
								]
							}]
						}]
					},
						gridJual
					],
					buttons: [{
						text: 'Save',
						handler: fnCekSave3
					},{
						text: 'Reset',
						handler: fnReset3
					}]
				}]
			}]
		});

		var vMask = new Ext.LoadMask({
			msg: 'Please wait...',
			target: frmProduk
		});
		
		function fnMaskShow() {
			frmProduk.mask('Please wait...');
		}

		function fnMaskHide() {
			frmProduk.unmask();
		}
		
		frmProduk.render(Ext.getBody());
	}
	
	Ext.Ajax.request({
		method: 'POST',
		url: 'alamat/nilaidefa',
		success: function(response) {
			var xtext = Ext.decode(response.responseText);
			
			if (xtext.sukses === true) {
				buatForm();
				
				Ext.getCmp('cboDeptBeli').setValue(xtext.kddept);
				Ext.getCmp('txtDeptCdBeli').setValue(xtext.deptcd);
				Ext.getCmp('txtCountBeli').setValue(xtext.count);
				Ext.getCmp('txtDeptBeli').setValue(xtext.nmdept);
				
				Ext.getCmp('cboDeptJual').setValue(xtext.kddept);
				Ext.getCmp('txtDeptCdJual').setValue(xtext.deptcd);
				Ext.getCmp('txtCountJual').setValue(xtext.count);
				Ext.getCmp('txtDeptJual').setValue(xtext.nmdept);
			}
		},
		failure: function(response) {
			var xText = Ext.decode(response.responseText);
			Ext.MessageBox.show({
				buttons: Ext.MessageBox.OK,
				closable: false,
				icon: Ext.MessageBox.INFO,
				message: 'Load default value Failed, Connection Failed!!',
				title: 'IDS'
			});
		}
	});
	
	Ext.get('loading').destroy();
});
