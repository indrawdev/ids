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

	function gridTooltipProd(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Single click on the product item in the list to display the chassis <br/> <br/> Double click on the price, disc and lux tax to edit',
			target: view.el,
			trackMouse: true
		});
	}

	var grupTrs = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_strx','fs_kd_trx','fs_kd_sstrx','fs_nm_strx'
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
			url: 'cbreceive/cb_trs'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_strx': Ext.getCmp('cboTrs').getValue(),
					'fs_nm_strx': Ext.getCmp('cboTrs').getValue(),
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
		store: grupTrs,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Code", dataIndex: 'fs_kd_strx', menuDisabled: true, width: 100},
			{text: "Transaction Type", dataIndex: 'fs_nm_strx', menuDisabled: true, width: 380},
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Code / Transaction Type',
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
					grupTrs.load();
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
			store: grupTrs,
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
				Ext.getCmp('cboTrs').setValue(record.get('fs_kd_strx'));
				Ext.getCmp('cboTrx').setValue(record.get('fs_kd_trx'));
				Ext.getCmp('cboStrx').setValue(record.get('fs_kd_sstrx'));
				Ext.getCmp('txtTrs').setValue(record.get('fs_nm_strx'));
				
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
				grupTrs.load();
				vMask.show();
			}
		}
	});

	var cboTrs = {
		anchor: '98%',
		fieldLabel: 'Trans Type',
		id: 'cboTrs',
		name: 'cboTrs',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('cboTrx').setValue('');
				Ext.getCmp('cboStrx').setValue('');
				Ext.getCmp('txtTrs').setValue('');
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

	var cboTrx = {
		anchor: '98%',
		fieldLabel: 'Trx',
		hidden: true,
		id: 'cboTrx',
		name: 'cboTrx',
		xtype: 'textfield'
	};

	var cboStrx = {
		anchor: '98%',
		fieldLabel: 'sTrx',
		hidden: true,
		id: 'cboStrx',
		name: 'cboStrx',
		xtype: 'textfield'
	};

	var txtTrs = {
		anchor: '98%',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtTrs',
		name: 'txtTrs',
		readOnly: true,
		xtype: 'textfield'
	};

	var txtTgl = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '95%',
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
		afterLabelTextTpl: required,
		allowBlank: false,
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

	function fnPrint() {
		fnPB();
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 120000,
			url: 'infoterimakas/printkas',
			params: {
				'fs_kd_trx': Ext.getCmp('cboTrx').getValue(),
				'fs_kd_strx': Ext.getCmp('cboStrx').getValue(),
				'fd_refno': Ext.Date.format(Ext.getCmp('txtTgl').getValue(), 'Ymd'),
				'fd_refno2': Ext.Date.format(Ext.getCmp('txtTgl2').getValue(), 'Ymd'),
				'fd_tgl': Ext.Date.format(Ext.getCmp('txtTgl').getValue(), 'd-m-Y'),
				'fd_tgl2': Ext.Date.format(Ext.getCmp('txtTgl2').getValue(), 'd-m-Y')
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
					
					var winpdf = Ext.create('Ext.window.Window', {
						closable: false,
						draggable: false,
						height: 500,
						layout: 'fit',
						title: 'Cash Receipt Report',
						width: 900,
						items: {
							xtype: 'component',
							autoEl: {
								src: xfile,
								tag: 'iframe'
							}
						},
						buttons: [/*{
							href: xfilexls,
							hrefTarget: '_blank',
							text: 'Download Excel'
						},*/{
							text: 'Exit',
							handler: function() {
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

	function fnReset() {
		Ext.getCmp('cboTrs').setValue('');
		Ext.getCmp('cboTrx').setValue('');
		Ext.getCmp('cboStrx').setValue('');
		Ext.getCmp('txtTgl').setValue(new Date());
		Ext.getCmp('txtTgl2').setValue(new Date());
	}

	var frmInfoTerimaKas = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Cash Receipt Report Form',
		width: 630,
		fieldDefaults: {
			labelAlign: 'right',
			labelSeparator: '',
			labelWidth: 70,
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
						cboTrs,
						cboTrx,
						cboStrx
					]
				},{
					flex: 1.7,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtTrs
					]
				}]
			},{
				anchor: '50%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1.5,
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
		}],
		bbar: Ext.create('Ext.toolbar.Toolbar', {
			items: [pbar]
		}),
		buttons: [{
			text: 'Print',
			handler: fnPrint
		},{
			text: 'Reset',
			handler: fnReset
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmInfoTerimaKas
	});
	
	function fnMaskShow() {
		frmInfoTerimaKas.mask('Please wait...');
	}

	function fnMaskHide() {
		frmInfoTerimaKas.unmask();
	}
	
	frmInfoTerimaKas.render(Ext.getBody());
	Ext.get('loading').destroy();
});
