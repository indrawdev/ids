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

	Ext.Ajax.request({
		method: 'POST',
		url: 'kasir/addfield',
		success: function(response) {
			var xtext = Ext.decode(response.responseText);
		},
		failure: function(response) {
			var xtext = Ext.decode(response.responseText);
			Ext.MessageBox.show({
				buttons: Ext.MessageBox.OK,
				closable: false,
				icon: Ext.MessageBox.INFO,
				msg: 'Load default Failed, Connection Failed!!',
				title: 'IDS'
			});
		}
	});

	function terBilang() {
		Ext.Ajax.request({
			method: 'POST',
			url: 'kasir/terbilang',
			params: {
				'fn_total': Ext.getCmp('txtTotal').getValue()
			},
			success: function(response) {
				var xtext = Ext.decode(response.responseText);
				
				if (xtext.sukses === true) {
					Ext.getCmp('txtBilang').setValue(xtext.word);
				}
			},
			failure: function(response) {
				var xtext = Ext.decode(response.responseText);
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Show calculated Failed, Connection Failed!!',
					title: 'IDS'
				});
			}
		});
	}

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
				Ext.getCmp('txtRefnodt').setValue(tglDMY(record.get('fd_refno')));
				Ext.getCmp('txtCust').setValue(record.get('fs_cust'));
				Ext.getCmp('txtTotal').setValue(record.get('fn_total'));
				Ext.getCmp('txtNote').setValue(record.get('fs_note'));
				Ext.getCmp('cekIn').setValue(record.get('fb_in'));
				Ext.getCmp('cekBank').setValue(record.get('fb_setor'));
				
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
		anchor: '95%',
		emptyText: "AUTOMATIC",
		fieldLabel: 'Ref. No',
		id: 'cboRefno',
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

	var txtCust = {
		anchor: '95%',
		emptyText: 'Enter a Customer',
		fieldLabel: 'Received From',
		id: 'txtCust',
		name: 'txtCust',
		xtype: 'textfield'
	};

	var txtTotal = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: true,
		anchor: '100%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		editable: true,
		fieldLabel: 'Amount of Money',
		hideTrigger: true,
		id: 'txtTotal',
		keyNavEnabled: false,
		labelWidth: 105,
		mouseWheelEnabled: false,
		name: 'txtTotal',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0,
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtTotal').getValue())) {
					Ext.getCmp('txtTotal').setValue(0);
				}
				else {
					terBilang();
					return value;
				}
			}
		}
	});

	var txtNote = {
		anchor: '100%',
		emptyText: 'Enter The Reason for Payment',
		fieldLabel: 'For Payment',
		grow: true,
		growMin: 35,
		growMax: 35,
		id: 'txtNote',
		name: 'txtNote',
		xtype: 'textareafield'
	};

	var txtBilang = {
		anchor: '100%',
		fieldLabel: 'Be Calculated',
		fieldStyle: 'background-color: #eee; background-image: none;',
		grow: true,
		growMin: 35,
		growMax: 35,
		id: 'txtBilang',
		name: 'txtBilang',
		xtype: 'textareafield'
	};

	var cekIn = {
		boxLabel: 'Money Status In',
		checked: true,
		id: 'cekIn',
		name: 'cekIn',
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
					Ext.getCmp('cekIn').setReadOnly(true);
				}
				else {
					Ext.getCmp('cekIn').setValue('1');
					Ext.getCmp('cekIn').setReadOnly(false);
				}
			}
		}
	};

	function fnCekSave() {
		if (this.up('form').getForm().isValid()) {
			Ext.Ajax.request({
				method: 'POST',
				url: 'kasir/kasir_ceksave',
				params: {
					'fs_refno': Ext.getCmp('cboRefno').getValue()
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
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'kasir/kasir_save',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue(),
				'fd_refno': Ext.Date.format(Ext.getCmp('txtRefnodt').getValue(), 'Ymd'),
				'fs_cust': Ext.getCmp('txtCust').getValue(),
				'fn_total': Ext.getCmp('txtTotal').getValue(),
				'fs_note': Ext.getCmp('txtNote').getValue(),
				'fs_terbilang': Ext.getCmp('txtBilang').getValue(),
				'fb_in': Ext.getCmp('cekIn').getValue(),
				'fb_setor': Ext.getCmp('cekBank').getValue()
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
					Ext.getCmp('cboRefno').setValue(xtext.refno.trim());
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

	function fnPrint() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'kasir/print_kuitansi',
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
					
					var winpdf = Ext.create('Ext.window.Window', {
						closable: false,
						draggable: false,
						height: 500,
						layout: 'fit',
						title: 'Cashier',
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
		Ext.getCmp('cboRefno').setValue('');
		Ext.getCmp('txtRefnodt').setValue(new Date());
		Ext.getCmp('txtCust').setValue('');
		Ext.getCmp('txtTotal').setValue('0');
		Ext.getCmp('txtNote').setValue('');
		Ext.getCmp('txtBilang').setValue('');
		Ext.getCmp('cekIn').setValue('1');
		Ext.getCmp('cekBank').setValue('0');
	}

	var frmKasir = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Cashier Form',
		width: 700,
		fieldDefaults: {
			labelAlign: 'right',
			labelSeparator: '',
			labelWidth: 85,
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
					flex: 2.5,
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
			},{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1.6,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtCust
					]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtTotal
					]
				}]
			},
				txtNote,
				txtBilang,
			{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 0.167,
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
							flex: 0.5,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cekIn
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
		buttons: [{
			text: 'Save',
			handler: fnCekSave
		},{
			text: 'Print',
			handler: fnPrint
		},{
			text: 'Reset',
			handler: fnReset
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmKasir
	});
	
	function fnMaskShow() {
		frmKasir.mask('Please wait...');
	}

	function fnMaskHide() {
		frmKasir.unmask();
	}
	
	frmKasir.render(Ext.getBody());
	Ext.get('loading').destroy();
});
