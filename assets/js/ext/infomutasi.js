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
					'fs_kd_product': '',
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
					winCari.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboCasis').setValue(record.get('fs_rangka'));
				
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
					winCari.show();
					winCari.center();
				}
			}
		}
	};

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
			fnPB();
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				timeout: 60000,
				url: 'infomutasi/printmutasi',
				params: {
					'fs_rangka': Ext.getCmp('cboCasis').getValue()
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
							title: 'Info Mutasi',
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
		Ext.getCmp('cboCasis').setValue('');
	}

	var frmInfoMutasi = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Mutation Info Form',
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
			items: [
				cboCasis
			]
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
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmInfoMutasi
	});
	
	function fnMaskShow() {
		frmInfoMutasi.mask('Please wait...');
	}

	function fnMaskHide() {
		frmInfoMutasi.unmask();
	}
	
	frmInfoMutasi.render(Ext.getBody());
	Ext.get('loading').destroy();
});