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
			html: 'Single click on the product item in the list to display the chassis <br/> <br/> Double click on the price, disc and lux tax to edit',
			target: view.el,
			trackMouse: true
		});
	}

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

	var grupRefno = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_refno','fd_refno','fs_cust',
			'fn_total','fs_note', 'fb_in',
			'fs_status','fb_setor'
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
			url: 'kasir/refno'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboRefno').getValue(),
					'fs_cust': Ext.getCmp('cboRefno').getValue()
				});
			}
		}
	});

	var winGrid = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 750,
		sortableColumns: false,
		store: grupRefno,
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
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Ref. No", dataIndex: 'fs_refno', menuDisabled: true, width: 150},
			{text: "Ref. Date", dataIndex: 'fd_refno', menuDisabled: true, width: 80},
			{text: "Received From", dataIndex: 'fs_cust', menuDisabled: true, width: 100},
			{text: "Amount of Money", dataIndex: 'fn_total', menuDisabled: true, width: 100, align: 'right', format: '0,000.00', xtype: 'numbercolumn'},
			{text: "For Payment", dataIndex: 'fs_note', menuDisabled: true, width: 150},
			{text: "In", dataIndex: 'fb_in', menuDisabled: true, hidden: true},
			{text: "Money Status", dataIndex: 'fs_status', menuDisabled: true, width: 100},
			{text: "Bank", dataIndex: 'fb_setor', menuDisabled: true, hidden: true}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboRefno').setValue(record.get('fs_refno'));
				Ext.getCmp('txtTgl').setValue(tglDMY(record.get('fd_refno')));
				Ext.getCmp('txtTgl2').setValue(tglDMY(record.get('fd_refno')));
				
				var xcek = record.get('fb_in');
				var xcek2 = record.get('fb_setor');
				if (xcek == 'true') {
					Ext.getCmp('cekIn').setValue('1');
					Ext.getCmp('cekOut').setValue('0');
				}
				else {
					if (xcek2 == 'true') {
						Ext.getCmp('cekBank').setValue('1');
					}
					else {
						Ext.getCmp('cekBank').setValue('0');
						if (xcek == 'true') {
							Ext.getCmp('cekIn').setValue('1');
							Ext.getCmp('cekOut').setValue('0');
						}
						else {
							Ext.getCmp('cekIn').setValue('0');
							Ext.getCmp('cekOut').setValue('1');
						}
					}
				}
				
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
		anchor: '70%',
		fieldLabel: 'Ref. No',
		id: 'cboRefno',
		name: 'cboRefno',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				if (Ext.isEmpty(Ext.getCmp('cboRefno').getValue())) {
					Ext.getCmp('txtTgl').setReadOnly(false);
					Ext.getCmp('txtTgl2').setReadOnly(false);
					Ext.getCmp('cekIn').setReadOnly(false);
					Ext.getCmp('cekOut').setReadOnly(false);
					Ext.getCmp('cekBank').setReadOnly(false);
				}
				else {
					Ext.getCmp('txtTgl').setReadOnly(true);
					Ext.getCmp('txtTgl2').setReadOnly(true);
					Ext.getCmp('cekIn').setReadOnly(true);
					Ext.getCmp('cekOut').setReadOnly(true);
					Ext.getCmp('cekBank').setReadOnly(true);
				}
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

	var cekIn = {
		boxLabel: 'Money Status In',
		checked: true,
		id: 'cekIn',
		name: 'cekIn',
		xtype: 'checkboxfield'
	};

	var cekOut = {
		boxLabel: 'Money Status Out',
		checked: true,
		id: 'cekOut',
		name: 'cekOut',
		xtype: 'checkboxfield'
	};

	var cekBank = {
		boxLabel: 'Deposited Into Bank',
		checked: false,
		id: 'cekBank',
		name: 'cekBank',
		xtype: 'checkboxfield',
		listeners: {
			change: function(value) {
				if (Ext.getCmp('cekBank').getValue() == '1') {
					Ext.getCmp('cekIn').setValue('0');
					Ext.getCmp('cekOut').setValue('1');
					Ext.getCmp('cekIn').setReadOnly(true);
					Ext.getCmp('cekOut').setReadOnly(true);
				}
				else {
					Ext.getCmp('cekIn').setValue('1');
					Ext.getCmp('cekIn').setReadOnly(false);
					Ext.getCmp('cekOut').setReadOnly(false);
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
			url: 'infokasir/printkasir',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue(),
				'fd_tgl': Ext.Date.format(Ext.getCmp('txtTgl').getValue(), 'd-m-Y'),
				'fd_tgl2': Ext.Date.format(Ext.getCmp('txtTgl2').getValue(), 'd-m-Y'),
				'fd_refno': Ext.Date.format(Ext.getCmp('txtTgl').getValue(), 'Ymd'),
				'fd_refno2': Ext.Date.format(Ext.getCmp('txtTgl2').getValue(), 'Ymd'),
				'fb_cekin': Ext.getCmp('cekIn').getValue(),
				'fb_cekout': Ext.getCmp('cekOut').getValue(),
				'fb_setor': Ext.getCmp('cekBank').getValue()
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
						title: 'Cashier Report',
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
		Ext.getCmp('txtTgl').setValue(new Date());
		Ext.getCmp('txtTgl2').setValue(new Date());
		Ext.getCmp('cboRefno').setValue('');
		Ext.getCmp('cekIn').setValue('1');
		Ext.getCmp('cekOut').setValue('1');
		Ext.getCmp('cekBank').setValue('0');
	}

	var frmInfoKasir = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		height: 201,
		region: 'center',
		title: 'Cashier Report Form',
		width: 630,
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
				anchor: '55%',
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
			},
				cboRefno,
			{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 0.168,
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
							flex: 0.7,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cekIn
							]
						},{
							flex: 0.7,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cekOut
							]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cekBank
							]
						}]
					}]
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
		target: frmInfoKasir
	});
	
	frmInfoKasir.render(Ext.getBody());
	Ext.get('loading').destroy();
});
