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

	var grupCust = Ext.create('Ext.data.Store', {
		autoLoad: true,
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
				Ext.getCmp('txtCount').setValue(record.get('fs_count'));
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

	var grupProd = Ext.create('Ext.data.Store', {
		autoLoad: true,
		fields: ['fs_nm_product','fs_rangka','fs_nama','fs_refno'],
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
			url: 'surat/product'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_cust': Ext.getCmp('txtCustCd').getValue(),
					'fs_count': Ext.getCmp('txtCount').getValue(),
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
		store: grupProd,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Product Name", dataIndex: 'fs_nm_product', menuDisabled: true, width: 150},
			{text: "Chassis / Machine", dataIndex: 'fs_rangka', menuDisabled: true, width: 180},
			{text: "A/N", dataIndex: 'fs_nama', menuDisabled: true, width: 150},
			{text: "Ref No", dataIndex: 'fs_refno', hidden: true}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Product Name / Chassis / Machine / A/N',
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
					winCari2.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboProd').setValue(record.get('fs_nm_product'));
				Ext.getCmp('txtProd').setValue(record.get('fs_rangka'));
				Ext.getCmp('txtNama').setValue(record.get('fs_nama'));
				Ext.getCmp('txtRefno').setValue(record.get('fs_refno'));
				
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
				grupProd.load();
				vMask.show();
			}
		}
	});

	function fnPrintAL() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'jual/printsrtpernyataan',
			params: {
				'fs_refno': Ext.getCmp('txtRefno').getValue(),
				'fs_nama': Ext.getCmp('txtNama').getValue(),
				'fs_man': Ext.getCmp('txtMan').getValue()
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
					
					var winpdf = Ext.create('Ext.window.Window', {
						closable: false,
						draggable: false,
						height: 500,
						layout: 'fit',
						title: 'Agreement Letter',
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

	function fnPrintTT() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'jual/printsrttandaterima',
			params: {
				'fs_refno': Ext.getCmp('txtRefno').getValue()
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
					
					var winpdf = Ext.create('Ext.window.Window', {
						closable: false,
						draggable: false,
						height: 500,
						layout: 'fit',
						title: 'Receipt',
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
		Ext.getCmp('txtMan').setValue('');
		Ext.getCmp('cboCust').setValue('');
		Ext.getCmp('txtCust').setValue('');
		Ext.getCmp('txtCustCd').setValue('');
		Ext.getCmp('txtCount').setValue('');
		Ext.getCmp('cboProd').setValue('0');
		Ext.getCmp('txtProd').setValue('');
		Ext.getCmp('txtNama').setValue('');
	}

	var frmSurat = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Agreement Letter Form',
		width: 550,
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
				anchor: '100%',
				emptyText: "Enter Branch Manager Name",
				fieldLabel: 'Manager',
				id: 'txtMan',
				name: 'txtMan',
				xtype: 'textfield'
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
						emptyText: "Select a",
						fieldLabel: 'Customer',
						id: 'cboCust',
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
									winCari.show();
									winCari.center();
								}
							}
						}
					},{
						anchor: '98%',
						fieldLabel: 'Cust Code',
						hidden: true,
						id: 'txtCustCd',
						name: 'txtCustCd',
						xtype: 'textfield'
					},{
						anchor: '98%',
						fieldLabel: 'Count',
						hidden: true,
						id: 'txtCount',
						name: 'txtCount',
						xtype: 'textfield'
					}]
				},{
					flex: 1.25,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						afterLabelTextTpl: required,
						allowBlank: false,
						anchor: '100%',
						emptyText: "Customer",
						fieldStyle: 'background-color: #eee; background-image: none;',
						id: 'txtCust',
						name: 'txtCust',
						readOnly: true,
						xtype: 'textfield'
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
						emptyText: "Select a",
						fieldLabel: 'Product',
						id: 'cboProd',
						name: 'cboProd',
						xtype: 'textfield',
						listeners: {
							change: function(field, newValue) {
								Ext.getCmp('txtProd').setValue('');
								Ext.getCmp('txtNama').setValue('');
								Ext.getCmp('txtRefno').setValue('');
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
					}]
				},{
					flex: 1.25,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						afterLabelTextTpl: required,
						allowBlank: false,
						anchor: '100%',
						emptyText: "Product",
						fieldStyle: 'background-color: #eee; background-image: none;',
						id: 'txtProd',
						name: 'txtProd',
						readOnly: true,
						xtype: 'textfield'
					}]
				}]
			},{
				anchor: '100%',
				emptyText: "Enter STNK Name",
				fieldLabel: 'A/N',
				id: 'txtNama',
				name: 'txtNama',
				xtype: 'textfield'
			},{
				anchor: '100%',
				emptyText: "Enter Reference No",
				fieldLabel: 'Reff. No',
				hidden: true,
				id: 'txtRefno',
				name: 'txtRefno',
				xtype: 'textfield'
			}]
		}],
		buttons: [{
			text: 'Agreement',
			handler: fnPrintAL
		},{
			text: 'Receipt',
			handler: fnPrintTT
		},{
			text: 'Reset',
			handler: fnReset
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmSurat
	});
	
	function fnMaskShow() {
		frmSurat.mask('Please wait...');
	}

	function fnMaskHide() {
		frmSurat.unmask();
	}
	
	frmSurat.render(Ext.getBody());
	Ext.get('loading').destroy();
});
