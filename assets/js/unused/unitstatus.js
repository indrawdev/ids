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

	function trim(text) {
		return text.replace(/^\s+|\s+$/gm, '');
	}

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
		}
	});

	grupGudang.on('beforeload', function(store, operation, eOpts) {
		operation.params = {
			'fs_code': Ext.getCmp('cboWH').getValue(),
			'fs_nm_code': Ext.getCmp('cboWH').getValue()
		};
	}, this);

	var winGrid = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupGudang,
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
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Warehouse Code", dataIndex: 'fs_code', 'menuDisabled': true, hidden: true},
			{text: "Warehouse Name", dataIndex: 'fs_nm_code', 'menuDisabled': true, width: 500}
		],
		listeners: {
			itemclick: function(grid, record)
			{
				Ext.getCmp('cboWH').setValue(record.get('fs_code'));
				Ext.getCmp('txtWH').setValue(record.get('fs_nm_code'));
			},
			itemdblclick: function(grid, record)
			{
				winCari.hide();
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

	var winCari = Ext.create('Ext.menu.Menu', {
		bodyPadding: '5 0 5 0',
		plain: true
	});
	winCari.add(winGrid);

	Ext.define('Ext.ux.SearchWH', {
		alias: 'widget.searchWH',
		extend: 'Ext.form.field.Trigger',
		initComponent: function() {
			this.callParent(arguments);
			this.on('specialkey', function(f, e) {
				if (e.getKey() == e.ENTER) {
					this.onTriggerClick();
				}
			}, this);
		},
		onTriggerClick: function() {
			grupGudang.load();
			winGrid.show();
			winCari.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
		}
	});

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
		}
	});

	grupProduct.on('beforeload', function(store, operation, eOpts) {
		operation.params = {
			'fs_kd_product': Ext.getCmp('cboProduct').getValue(),
			'fs_nm_product': Ext.getCmp('cboProduct').getValue()
		};
	}, this);

	var winGrid2 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupProduct,
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
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Product Code", dataIndex: 'fs_kd_product', 'menuDisabled': true, width: 250},
			{text: "Product Name", dataIndex: 'fs_nm_product', 'menuDisabled': true, width: 350}
		],
		listeners: {
			itemclick: function(grid, record)
			{
				Ext.getCmp('cboProduct').setValue(record.get('fs_kd_product'));
				Ext.getCmp('txtProduct').setValue(record.get('fs_nm_product'));
			},
			itemdblclick: function(grid, record)
			{
				winCari2.hide();
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

	var winCari2 = Ext.create('Ext.menu.Menu', {
		bodyPadding: '5 0 5 0',
		plain: true
	});
	winCari2.add(winGrid2);

	Ext.define('Ext.ux.SearchProduct', {
		alias: 'widget.searchProduct',
		extend: 'Ext.form.field.Trigger',
		initComponent: function() {
			this.callParent(arguments);
			this.on('specialkey', function(f, e) {
				if (e.getKey() == e.ENTER) {
					this.onTriggerClick();
				}
			}, this);
		},
		onTriggerClick: function() {
			grupProduct.load();
			winGrid2.show();
			winCari2.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
		}
	});

	// color
	var grupColor = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_kd_warna','fs_nm_warna'],
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
			url: 'unitstatus/warna'
		}
	});

	grupColor.on('beforeload', function(store, operation, eOpts) {
		operation.params = {
			'fs_kd_warna': Ext.getCmp('cboColor').getValue(),
			'fs_nm_warna': Ext.getCmp('cboColor').getValue()
		};
	}, this);

	var winGrid3 = Ext.create('Ext.ux.LiveSearchGridPanel', {
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
					winCari3.hide();
				}
			}]
		}),
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Color Code", dataIndex: 'fs_kd_warna', 'menuDisabled': true, width: 250},
			{text: "Color Name", dataIndex: 'fs_nm_warna', 'menuDisabled': true, width: 350}
		],
		listeners: {
			itemclick: function(grid, record)
			{
				Ext.getCmp('cboColor').setValue(record.get('fs_kd_warna'));
				Ext.getCmp('txtColor').setValue(record.get('fs_nm_warna'));
			},
			itemdblclick: function(grid, record)
			{
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

	var winCari3 = Ext.create('Ext.menu.Menu', {
		bodyPadding: '5 0 5 0',
		plain: true
	});
	winCari3.add(winGrid3);

	Ext.define('Ext.ux.SearchColor', {
		alias: 'widget.searchColor',
		extend: 'Ext.form.field.Trigger',
		initComponent: function() {
			this.callParent(arguments);
			this.on('specialkey', function(f, e) {
				if (e.getKey() == e.ENTER) {
					this.onTriggerClick();
				}
			}, this);
		},
		onTriggerClick: function() {
			grupColor.load();
			winGrid3.show();
			winCari3.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
		}
	});
	// color

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
		}
	});

	grupGudang2.on('beforeload', function(store, operation, eOpts) {
		operation.params = {
			'fs_code': Ext.getCmp('cboWH2').getValue(),
			'fs_nm_code': Ext.getCmp('cboWH2').getValue()
		};
	}, this);

	var winGrid4 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupGudang2,
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
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Warehouse Code", dataIndex: 'fs_code', 'menuDisabled': true, hidden: true},
			{text: "Warehouse Name", dataIndex: 'fs_nm_code', 'menuDisabled': true, width: 500}
		],
		listeners: {
			itemclick: function(grid, record)
			{
				Ext.getCmp('cboWH2').setValue(record.get('fs_code'));
				Ext.getCmp('txtWH2').setValue(record.get('fs_nm_code'));
			},
			itemdblclick: function(grid, record)
			{
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

	var winCari4 = Ext.create('Ext.menu.Menu', {
		bodyPadding: '5 0 5 0',
		plain: true
	});
	winCari4.add(winGrid4);

	Ext.define('Ext.ux.SearchWH2', {
		alias: 'widget.searchWH2',
		extend: 'Ext.form.field.Trigger',
		initComponent: function() {
			this.callParent(arguments);
			this.on('specialkey', function(f, e) {
				if (e.getKey() == e.ENTER) {
					this.onTriggerClick();
				}
			}, this);
		},
		onTriggerClick: function() {
			grupGudang2.load();
			winGrid4.show();
			winCari4.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
		}
	});

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
		}
	});

	grupProduct2.on('beforeload', function(store, operation, eOpts) {
		operation.params = {
			'fs_kd_product': Ext.getCmp('cboProduct2').getValue(),
			'fs_nm_product': Ext.getCmp('cboProduct2').getValue()
		};
	}, this);

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
			{text: "Product Code", dataIndex: 'fs_kd_product', 'menuDisabled': true, width: 250},
			{text: "Product Name", dataIndex: 'fs_nm_product', 'menuDisabled': true, width: 350}
		],
		listeners: {
			itemclick: function(grid, record)
			{
				Ext.getCmp('cboProduct2').setValue(record.get('fs_kd_product'));
				Ext.getCmp('txtProduct2').setValue(record.get('fs_nm_product'));
			},
			itemdblclick: function(grid, record)
			{
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

	var winCari5 = Ext.create('Ext.menu.Menu', {
		bodyPadding: '5 0 5 0',
		plain: true
	});
	winCari5.add(winGrid5);

	Ext.define('Ext.ux.SearchProduct2', {
		alias: 'widget.searchProduct2',
		extend: 'Ext.form.field.Trigger',
		initComponent: function() {
			this.callParent(arguments);
			this.on('specialkey', function(f, e) {
				if (e.getKey() == e.ENTER) {
					this.onTriggerClick();
				}
			}, this);
		},
		onTriggerClick: function() {
			grupProduct2.load();
			winGrid5.show();
			winCari5.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
		}
	});

	Ext.define('Ext.ux.SearchCasis', {
		alias: 'widget.searchCasis',
		extend: 'Ext.form.field.Trigger',
		initComponent: function() {
			this.callParent(arguments);
			this.on('specialkey', function(f, e) {
				if (e.getKey() == e.ENTER) {
					this.onTriggerClick();
				}
			}, this);
		},
		onTriggerClick: function() {
			grupCasis.load();
			winGrid6.show();
			winCari6.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
		}
	});

	function fnReset() {
		Ext.getCmp('cboWH').setValue('');
		Ext.getCmp('txtWH').setValue('');
		Ext.getCmp('cboProduct').setValue('');
		Ext.getCmp('txtProduct').setValue('');
		Ext.getCmp('cboColor').setValue('');
		Ext.getCmp('txtColor').setValue('');
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
	}

	var pbar = Ext.create('Ext.ProgressBar', {
		id: 'pbar',
		width: 600
	}).hide();

	function fnPB() {
		pbar.wait({
			duration: 120000,
			increment: 600,
			interval: 200,
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

	function fnPrint() {
		fnPB();
		Ext.Ajax.on('beforerequest', vMask.show, vMask);
		Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
		Ext.Ajax.on('requestexception', vMask.hide, vMask);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 120000,
			url: 'unitstatus/printstatus',
			params: {
				'fs_kd_wh': Ext.getCmp('cboWH').getValue(),
				'fs_kd_product': Ext.getCmp('cboProduct').getValue(),
				'fs_kd_warna': Ext.getCmp('cboColor').getValue(),
				'fd_tgl': Ext.Date.format(new Date(), 'd-m-Y H:i:s')
				
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				
				if (xtext.sukses === true) {
					var xfile = trim(xtext.nmfile);
					
					/*var winpdf = Ext.create('Ext.window.Window', {
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
							text: 'Exit',
							handler: function() {
								winpdf.hide();
							}
						}]
					}).show();*/
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
					pbar.hide();
					pbar.reset(true);
				}
				else {
					pbar.hide();
					pbar.reset(true);
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.Msg.INFO,
						msg: xtext.hasil,
						title: 'ASTER'
					});				
				}
			},
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.Msg.INFO,
					msg: 'Print Failed, Connection Failed!!',
					title: 'ASTER'
				});
			}
		});
	}

	function fnPrint2() {
		fnPB2();
		Ext.Ajax.on('beforerequest', vMask.show, vMask);
		Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
		Ext.Ajax.on('requestexception', vMask.hide, vMask);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'unitstatus/printhistory',
			params: {
				'fs_kd_dept': Ext.getCmp('txtDeptCd').getValue(),
				'fs_count': Ext.getCmp('txtCount').getValue(),
				'fs_kd_wh': Ext.getCmp('cboWH2').getValue(),
				'fs_kd_product': Ext.getCmp('cboProduct2').getValue()
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				/*
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.Msg.INFO,
					msg: xtext.hasil,
					title: 'ASTER'
				});*/
				
				if (xtext.sukses === true) {
					var xfile = trim(xtext.nmfile);
					
					var winpdf2 = Ext.create('Ext.window.Window', {
						closable: false,
						draggable: false,
						height: 500,
						layout: 'fit',
						title: 'Unit Status History',
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
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.Msg.INFO,
						msg: xtext.hasil,
						title: 'ASTER'
					});				
				}
			},
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.Msg.INFO,
					msg: 'Print Failed, Connection Failed!!',
					title: 'ASTER'
				});
			}
		});
	}

	var frmUnitStatus = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Unit Status Form',
		width: 645,
		items: [{
			activeTab: 0,
			bodyStyle: 'padding: 5px;',
			plain: true,
			xtype: 'tabpanel',
			items: [{
				border: false,
				frame: true,
				title: 'Unit Status',
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
							items: [{
								anchor: '98%',
								emptyText: 'Select a Warehouse',
								fieldLabel: 'Warehouse',
								id: 'cboWH',
								name: 'cboWH',
								xtype: 'searchWH',
								listeners: {
									change: function(combo, value) {
										Ext.getCmp('txtWH').setValue('');
									}
								}
							},{
								anchor: '98%',
								emptyText: 'Select a Product',
								fieldLabel: 'Product',
								id: 'cboProduct',
								name: 'cboProduct',
								xtype: 'searchProduct',
								listeners: {
								change: function(combo, value) {
										Ext.getCmp('txtProduct').setValue('');
									}
								}
							},{
								anchor: '98%',
								emptyText: 'Select a Color',
								fieldLabel: 'Color',
								id: 'cboColor',
								name: 'cboColor',
								xtype: 'searchColor',
								listeners: {
								change: function(combo, value) {
										Ext.getCmp('txtColor').setValue('');
									}
								}
							}]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [{
								anchor: '100%',
								fieldStyle: 'background-color: #eee; background-image: none;',
								hidden: false,
								id: 'txtWH',
								name: 'txtWH',
								readOnly: true,
								xtype: 'textfield'
							},{
								anchor: '100%',
								fieldStyle: 'background-color: #eee; background-image: none;',
								hidden: false,
								id: 'txtProduct',
								name: 'txtProduct',
								readOnly: true,
								xtype: 'textfield'
							},{
								anchor: '100%',
								fieldStyle: 'background-color: #eee; background-image: none;',
								hidden: false,
								id: 'txtColor',
								name: 'txtColor',
								readOnly: true,
								xtype: 'textfield'
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
				border: false,
				frame: true,
				title: 'Unit Status History',
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
							items: [{
								anchor: '98%',
								emptyText: 'Select a Department',
								fieldLabel: 'Department',
								hidden: true,
								id: 'cboDept',
								name: 'cboDept',
								xtype: 'searchDept',
								listeners: {
									change: function(combo, value) {
										Ext.getCmp('txtDept').setValue('');
										Ext.getCmp('txtDeptCd').setValue('');
										Ext.getCmp('txtCount').setValue('');
									}
								}
							},{
								anchor: '98%',
								fieldLabel: 'Dept Code',
								hidden: true,
								id: 'txtDeptCd',
								name: 'txtDeptCd',
								xtype: 'textfield'
							},{
								anchor: '98%',
								fieldLabel: 'Dept Count',
								hidden: true,
								id: 'txtCount',
								name: 'txtCount',
								xtype: 'textfield'
							},{
								anchor: '98%',
								emptyText: 'Select a Warehouse',
								fieldLabel: 'Warehouse',
								id: 'cboWH2',
								name: 'cboWH2',
								xtype: 'searchWH2',
								listeners: {
									change: function(combo, value) {
										Ext.getCmp('txtWH2').setValue('');
									}
								}
							},{
								anchor: '98%',
								emptyText: 'Select a Product',
								fieldLabel: 'Product',
								id: 'cboProduct2',
								name: 'cboProduct2',
								xtype: 'searchProduct2',
								listeners: {
								change: function(combo, value) {
										Ext.getCmp('txtProduct2').setValue('');
									}
								}
							}]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [{
								anchor: '100%',
								fieldStyle: 'background-color: #eee; background-image: none;',
								hidden: true,
								id: 'txtDept',
								name: 'txtDept',
								readOnly: true,
								xtype: 'textfield'
							},{
								anchor: '100%',
								fieldStyle: 'background-color: #eee; background-image: none;',
								id: 'txtWH2',
								name: 'txtWH2',
								readOnly: true,
								xtype: 'textfield'
							},{
								anchor: '100%',
								fieldStyle: 'background-color: #eee; background-image: none;',
								id: 'txtProduct2',
								name: 'txtProduct2',
								readOnly: true,
								xtype: 'textfield'
							}]
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
			}]
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmUnitStatus
	});
	
	frmUnitStatus.render(Ext.getBody());
	Ext.get('loading').destroy();
});
