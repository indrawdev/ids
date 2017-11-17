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

	function fnSisa() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'cbreceive/hitungsisa',
			params: {
				'fs_refno_jual': Ext.getCmp('cboJual').getValue()
			},
			success: function(response) {
				var xtext = Ext.decode(response.responseText);
				
				Ext.getCmp('txtSisa').setValue(0);
				Ext.getCmp('txtSisa2').setValue(0);
				if (xtext.sukses === true) {
					Ext.getCmp('txtSisa').setValue(xtext.jmlsisa);
					Ext.getCmp('txtSisa2').setValue(xtext.jmlsisa);
				}
			},
			failure: function(response) {
				var xtext = Ext.decode(response.responseText);
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Generate Sales Remaining Failed, Connection Failed!!',
					title: 'IDS'
				});
				fnMaskHide();
			}
		});
	}

	function fnTotal() {
		var xSisa = Ext.getCmp('txtSisa2').getValue();
		var xTotal = Ext.getCmp('txtTotal').getValue();
		
		Ext.getCmp('txtSisa').setValue(xSisa - xTotal);
	}
	
	var grupTrx = Ext.create('Ext.data.Store', {
		autoLoad: true,
		fields: [
			'fs_kd_strx','fs_nm_strx'
		],
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'cbreceive/cb_trs'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_strx': '5000'
				});
			}
		}
	});

	var cboTrs = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '65.3%',
		displayField: 'fs_nm_strx',
		editable: false,
		emptyText: 'Select a Trans Type',
		fieldLabel: 'Trans Type',
		id: 'cboTrs',
		name: 'cboTrs',
		store: grupTrx,
		valueField: 'fs_kd_strx',
		xtype: 'combobox',
		listeners: {
			change: function(combo, value) {
				fnReset();
			}
		}
	};

	var grupRefno = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_refno','fd_refno','fs_refno_jual',
			'fs_kd_cust','fs_cust_count','fs_nm_cust',
			'fn_sisa','fn_total','fs_ket','fb_edit'
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
			url: 'cbreceive/refno'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_strx': Ext.getCmp('cboTrs').getValue(),
					'fs_refno': Ext.getCmp('cboRefno').getValue(),
					'fs_nm_cust': Ext.getCmp('cboRefno').getValue(),
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
			{text: "Date", dataIndex: 'fd_refno', menuDisabled: true, width: 80},
			{text: "Refno Jual", dataIndex: 'fs_refno_jual', menuDisabled: true, hidden: true},
			{text: "Customer", dataIndex: 'fs_kd_cust', menuDisabled: true, hidden: true},
			{text: "Customer", dataIndex: 'fs_cust_count', menuDisabled: true, hidden: true},
			{text: "Customer", dataIndex: 'fs_nm_cust', menuDisabled: true, width: 150},
			{text: "Remain", dataIndex: 'fn_sisa', menuDisabled: true, hidden: true},
			{text: "Amount", dataIndex: 'fn_total', menuDisabled: true, hidden: true},
			{text: "Note", dataIndex: 'fs_ket', menuDisabled: true, width: 280},
			{text: "Boleh Edit", dataIndex: 'fb_edit', hidden: true}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Ref. No / Customer',
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
				Ext.getCmp('txtRefnodt').setValue(record.get('fd_refno'));
				Ext.getCmp('cboJual').setValue(record.get('fs_refno_jual'));
				Ext.getCmp('cboCust').setValue(record.get('fs_kd_cust'));
				Ext.getCmp('cboCustCount').setValue(record.get('fs_cust_count'));
				Ext.getCmp('txtCust').setValue(record.get('fs_nm_cust'));
				Ext.getCmp('txtSisa').setValue(record.get('fn_sisa'));
				Ext.getCmp('txtSisa2').setValue(record.get('fn_sisa'));
				Ext.getCmp('txtTotal').setValue(record.get('fn_total'));
				Ext.getCmp('txtKet').setValue(record.get('fs_ket'));
				
				var xEdit = record.get('fb_edit');
				if (xEdit === 0) {
					Ext.getCmp('btnSave').setDisabled(true);
					Ext.getCmp('btnRemove').setDisabled(true);
				}
				else {
					Ext.getCmp('btnSave').setDisabled(false);
					Ext.getCmp('btnRemove').setDisabled(false);
				}
				
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
		emptyText: 'AUTOMATIC',
		fieldLabel: 'Ref. No',
		id: 'cboRefno',
		name: 'cboRefno',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
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

	var grupRefnoJual = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_refno','fs_kd_cust','fs_count_cust',
			'fs_nm_cussup','fs_kd_product','fs_rangka',
			'fs_mesin','fs_kd_payment'
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
			url: 'cbreceive/refnojual'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboJual').getValue(),
					'fs_docno': Ext.getCmp('cboJual').getValue(),
					'fs_nm_cussup': Ext.getCmp('cboJual').getValue(),
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
		store: grupRefnoJual,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Refno", dataIndex: 'fs_refno', menuDisabled: true, width: 140},
			{text: "Customer", dataIndex: 'fs_kd_cust', menuDisabled: true, hidden: true},
			{text: "Customer", dataIndex: 'fs_count_cust', menuDisabled: true, hidden: true},
			{text: "Customer", dataIndex: 'fs_nm_cussup', menuDisabled: true, width: 340},
			{text: "Product", dataIndex: 'fs_kd_product', menuDisabled: true, hidden: true},
			{text: "Rangka", dataIndex: 'fs_rangka', menuDisabled: true, hidden: true},
			{text: "Mesin", dataIndex: 'fs_mesin', menuDisabled: true, hidden: true},
			{text: "Payment", dataIndex: 'fs_kd_payment', menuDisabled: true, hidden: true}			
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Refno / Customer',
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
					grupRefnoJual.load();
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
			store: grupRefnoJual,
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
				Ext.getCmp('cboJual').setValue(record.get('fs_refno'));
				Ext.getCmp('cboCust').setValue(record.get('fs_kd_cust'));
				Ext.getCmp('cboCustCount').setValue(record.get('fs_count_cust'));
				Ext.getCmp('txtCust').setValue(record.get('fs_nm_cussup'));
				
				if (Ext.getCmp('cboJual').getValue().trim() !== '') {
					fnSisa();
				}
				fnTotal();
				
				var xCust = record.get('fs_nm_cussup').trim();
				var xProd = record.get('fs_nm_product').trim();
				var xRangka = record.get('fs_rangka').trim();
				var xMesin = record.get('fs_mesin').trim();
				
				var xKet = 'CASH';
				if (record.get('fs_kd_payment').trim() === '1') {
					xKet = 'CASH TEMPO';
				}
				var xNote = 'Pembayaran '.concat(xKet,' 1 unit kendaraan ', xProd, '\n',
							'a/n: ', xCust, '\n',
							'No Rangka : ', xRangka, '\n',
							'No Mesin : ', xMesin
				);
				Ext.getCmp('txtKet').setValue(xNote);
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
				grupRefnoJual.load();
				vMask.show();
			}
		}
	});

	var cboJual = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '70%',
		emptyText: 'Type Customer Name Then Search',
		fieldLabel: 'Sales Ref. No',
		id: 'cboJual',
		name: 'cboJual',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('cboCust').setValue('');
				Ext.getCmp('cboCustCount').setValue('');
				Ext.getCmp('txtCust').setValue('');
				Ext.getCmp('txtSisa').setValue(0);
				Ext.getCmp('txtSisa2').setValue(0);
				Ext.getCmp('txtTotal').setValue(0);
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

	var cboCust = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		fieldLabel: 'Received From',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'cboCust',
		name: 'cboCust',
		readOnly: true,
		xtype: 'textfield'
	};

	var cboCustCount = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '95%',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'cboCustCount',
		name: 'cboCustCount',
		readOnly: true,
		xtype: 'textfield'
	};

	var txtCust = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtCust',
		name: 'txtCust',
		readOnly: true,
		xtype: 'textfield'
	};

	var txtSisa = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: true,
		anchor: '45%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		fieldLabel: 'Remaining',
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		hideTrigger: true,
		id: 'txtSisa',
		keyNavEnabled: false,
		mouseWheelEnabled: false,
		name: 'txtSisa',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0,
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtSisa').getValue())) {
					Ext.getCmp('txtSisa').setValue(0);
				}
			}
		}
	});

	var txtSisa2 = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: true,
		anchor: '45%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		fieldLabel: 'Remaining',
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		hidden: true,
		hideTrigger: true,
		id: 'txtSisa2',
		keyNavEnabled: false,
		mouseWheelEnabled: false,
		name: 'txtSisa2',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0,
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtSisa2').getValue())) {
					Ext.getCmp('txtSisa2').setValue(0);
				}
			}
		}
	});

	var txtTotal = Ext.create('Ext.ux.form.NumericField', {
		afterLabelTextTpl: required,
		allowBlank: false,
		alwaysDisplayDecimals: true,
		anchor: '98%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		fieldLabel: 'Amount',
		fieldStyle: 'text-align: right;',
		hideTrigger: true,
		id: 'txtTotal',
		keyNavEnabled: false,
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
				fnTotal();
			}
		}
	});

	var cmdRefresh = {
		anchor: '100%',
		iconCls: 'icon-refresh',
		id: 'cmdRefresh',
		name: 'cmdRefresh',
		text: '',
		xtype: 'button',
		handler: function() {
			Ext.getCmp('txtTotal').setValue(Ext.getCmp('txtSisa2').getValue());
			fnTotal();
		}
	};

	var txtKet = {
		anchor: '100%',
		emptyText: 'Enter The Reason for Payment',
		fieldLabel: 'Note',
		grow: true,
		growMin: 70,
		growMax: 70,
		id: 'txtKet',
		name: 'txtKet',
		xtype: 'textareafield'
	};

	function fnCekSave() {
		if (this.up('form').getForm().isValid()) {
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'cbreceive/ceksave',
				params: {
					'fs_kd_strx': Ext.getCmp('cboTrs').getValue(),
					'fs_refno': Ext.getCmp('cboRefno').getValue(),
					'fn_total': Ext.getCmp('txtTotal').getValue()
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
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'cbreceive/save',
			params: {
				'fs_kd_strx': Ext.getCmp('cboTrs').getValue(),
				'fs_refno': Ext.getCmp('cboRefno').getValue(),
				'fd_refno': Ext.Date.format(Ext.getCmp('txtRefnodt').getValue(), 'Ymd'),
				'fs_refno_jual': Ext.getCmp('cboJual').getValue(),
				'fs_kd_cust': Ext.getCmp('cboCust').getValue(),
				'fs_cust_count': Ext.getCmp('cboCustCount').getValue(),
				'fs_nm_cust': Ext.getCmp('txtCust').getValue(),
				'fs_ket': Ext.getCmp('txtKet').getValue(),
				'fn_total': Ext.getCmp('txtTotal').getValue()
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
					Ext.getCmp('cboRefno').setValue(xtext.refno.trim());
					
					Ext.getCmp('btnSave').setDisabled(true);
					Ext.getCmp('btnRemove').setDisabled(true);
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
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'cbreceive/cekremove',
			params: {
				'fs_kd_strx': '50000100',
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
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'cbreceive/remove',
			params: {
				'fs_kd_strx': '50000100',
				'fs_refno': Ext.getCmp('cboRefno').getValue()
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
			url: 'cbreceive/printkwitansi',
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
						title: 'Kwitansi',
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
							text: 'Download Excel',
							xtype: 'button'
						},*/{
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
		Ext.getCmp('cboTrs').setValue('50000100');
		Ext.getCmp('cboRefno').setValue('');
		Ext.getCmp('txtRefnodt').setValue(new Date());
		Ext.getCmp('cboCust').setValue('');
		Ext.getCmp('cboCustCount').setValue('');
		Ext.getCmp('txtTotal').setValue(0);
		Ext.getCmp('txtKet').setValue('');
		Ext.getCmp('cboJual').setValue('');
		Ext.getCmp('txtSisa').setValue(0);
		Ext.getCmp('txtSisa2').setValue(0);
		
		Ext.getCmp('btnSave').setDisabled(false);
		Ext.getCmp('btnRemove').setDisabled(false);
	}

	var frmCBReceive = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Cash Receipt Form',
		width: 550,
		fieldDefaults: {
			labelAlign: 'right',
			labelSeparator: '',
			labelWidth: 90,
			msgTarget: 'side'
		},
		items: [{
			style: 'padding: 5px;',
			xtype: 'fieldset',
			items: [
				cboTrs,
			{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 2.2,
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
			}
		]},{
			style: 'padding: 5px;',
			title: 'From Sales',
			xtype: 'fieldset',
			items: [
				cboJual,
			{
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
							flex: 2.1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cboCust
							]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cboCustCount
							]
						}]
					}]
				},{
					flex: 1.7,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtCust
					]
				}]
			},
				txtSisa2,
				txtSisa
		]},{
			style: 'padding: 5px;',
			title: 'Cash Receipt',
			xtype: 'fieldset',
			items: [{
				anchor: '50.7%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtTotal
					]
				},{
					flex: 0.1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						cmdRefresh
					]
				}]
			},
				txtKet
			]
		}],
		buttons: [{
			id: 'btnSave',
			name: 'btnSave',
			text: 'Save',
			handler: fnCekSave
		},{
			text: 'Print',
			handler: fnPrint
		},{
			text: 'Reset',
			handler: fnReset
		},{
			id: 'btnRemove',
			name: 'btnRemove',
			text: 'Remove',
			handler: fnCekRemove
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmCBReceive
	});
	
	function fnMaskShow() {
		frmCBReceive.mask('Please wait...');
	}

	function fnMaskHide() {
		frmCBReceive.unmask();
	}
	
	frmCBReceive.render(Ext.getBody());
	Ext.getCmp('cboTrs').setValue('50000100');
	Ext.get('loading').destroy();
});
