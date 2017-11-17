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
		hidden: false,
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
		afterLabelTextTpl: required,
		allowBlank: false,
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
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Product',
		fieldStyle: 'background-color: #eee; background-image: none;',
		hidden: false,
		id: 'txtProduct',
		name: 'txtProduct',
		readOnly: true,
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				grupGridRangka.removeAll();
				if (field.getValue().trim() !== '') {
					grupGridRangka.load();
				}
			}
		},
	};

	Ext.define('DataGridRangka', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_rangka', type: 'string'},
			{name: 'fs_mesin', type: 'string'},
			{name: 'fb_tambah', type: 'boolean'}
		]
	});

	var grupGridRangka = Ext.create('Ext.data.Store', {
		autoLoad: false,
		model: 'DataGridRangka',
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'barcode/gridrangka'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_product': Ext.getCmp('cboProduct').getValue()
				});
			}
		}
	});
	
	var cellEditingBeli = Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToEdit: 2
	});
	
	var gridRangka = Ext.create('Ext.grid.Panel', {
		anchor: '100%',
		autoDestroy: true,
		height: 410,
		sortableColumns: false,
		store: grupGridRangka,
		bbar: [{
			xtype: 'tbfill'
		},{
			text: 'Check All',
			xtype: 'button',
			handler: function() {
				var storeReg = gridRangka.getStore();
				storeReg.each(function(record, idx) {
					record.set('fb_tambah','1');
				});
				gridRangka.getView().refresh();
			}
		},{
			text: 'Uncheck All',
			xtype: 'button',
			handler: function() {
				var storeReg = gridRangka.getStore();
				storeReg.each(function(record, idx) {
					record.set('fb_tambah','0');
				});
				gridRangka.getView().refresh();
			}
		}],
		columns: [{
			xtype: 'rownumberer',
			width: 35
		},{
			dataIndex: 'fs_rangka',
			flex: 1.25,
			menuDisabled: true,
			text: 'Chassis'
		},{
			dataIndex: 'fs_mesin',
			flex: 1,
			menuDisabled: true,
			text: 'Machine'
		},{
			align: 'center',
			text: 'Add',
			dataIndex: 'fb_tambah',
			flex: 0.25,
			menuDisabled: true,
			stopSelection: false,
			xtype: 'checkcolumn'
		}],
		plugins: [
			cellEditingBeli
		],
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false,
			stripeRows: true
		}
	});

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

	function fnPrint() {
		if (this.up('form').getForm().isValid()) {
			var xKdRangka = '';
			var xStore = gridRangka.getStore();
			
			xStore.each(function(record, idx) {
				if (record.get('fb_tambah') === true) {
					if (xKdRangka.trim() == '') {
						xKdRangka = "'"+ record.get('fs_rangka') + "'";
					}
					else {
						xKdRangka = xKdRangka +", '"+ record.get('fs_rangka') + "'";
					}
				}
			});
			
			fnPB();
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				timeout: 60000,
				url: 'barcode/printbarcode',
				params: {
					'fs_kd_wh': Ext.getCmp('cboWH').getValue(),
					'fs_kd_product': Ext.getCmp('cboProduct').getValue(),
					'fs_rangka': xKdRangka
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
	}

	function fnReset() {
		Ext.getCmp('cboWH').setValue('');
		Ext.getCmp('txtWH').setValue('');
		Ext.getCmp('cboProduct').setValue('');
		Ext.getCmp('txtProduct').setValue('');
	}

	var frmBarcode = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Barcode Form',
		width: 630,
		fieldDefaults: {
			labelAlign: 'right',
			labelSeparator: '',
			labelWidth: 65,
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
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						cboWH,
						cboProduct
					]
				},{
					flex: 1.8,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtWH,
						txtProduct
					]
				}]
			}]
		},
			gridRangka
		],
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
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmBarcode
	});
	
	function fnMaskShow() {
		frmBarcode.mask('Please wait...');
	}

	function fnMaskHide() {
		frmBarcode.unmask();
	}
	
	frmBarcode.render(Ext.getBody());
	Ext.get('loading').destroy();
});