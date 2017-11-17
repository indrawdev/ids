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
			html: 'Double click on the Qty, price, disc and lux code to edit',
			target: view.el,
			trackMouse: true
		});
	}

	var votax = '0';
	Ext.Ajax.request({
		method: 'POST',
		url: 'beli/otax',
		success: function(response) {
			var xtext = Ext.decode(response.responseText);
			
			if (xtext.sukses === true) {
				votax = xtext.tax;
			}
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

	function oTax() {
		Ext.Ajax.request({
			method: 'POST',
			url: 'beli/otax',
			success: function(response) {
				var xtext = Ext.decode(response.responseText);
				
				if (xtext.sukses === true) {
					votax = xtext.tax;
				}
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
	}

	function fnProduk() {
		Ext.Ajax.request({
			method: 'POST',
			url: 'produk/norangka',
			params: {
				'fs_kd_product': Ext.getCmp('txtProdAktif').getValue()
			},
			success: function(response) {
				var xtext = Ext.decode(response.responseText);
				
				if (xtext.sukses === true) {
					Ext.getCmp('txtRangka').setValue(xtext.kdrangka);
					Ext.getCmp('txtMesin').setValue(xtext.kdmesin);
					
					if (Ext.getCmp('txtCC').getValue() === 0) {
						Ext.getCmp('txtCC').setValue(xtext.cc);
					}
				}
				else {
					Ext.getCmp('txtRangka').setValue('');
					Ext.getCmp('txtMesin').setValue('');
					Ext.getCmp('txtCC').setValue(0);
				}
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
	}

	var grupRefno = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_refno','fd_refno','fs_docno','fd_docno','fs_invno',
			'fd_invno','fs_kd_suppcount','fs_nm_supplier','fs_kd_supp','fs_count_supp',
			'fs_kd_term','fs_nm_term','fs_kd_salesmtd','fs_nm_salesmtd','fn_total_price',
			'fn_total_disc','fn_disc','fn_total','fn_ppn','fs_kd_otax',
			'fs_nm_otax','fn_otax_persen','fn_otax_total','fn_gtotal'
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
			url: 'beli/refno'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboRefno').getValue(),
					'fs_docno': Ext.getCmp('cboRefno').getValue(),
					'fs_invno': Ext.getCmp('cboRefno').getValue(),
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
			{text: "Ref. No", dataIndex: 'fs_refno', menuDisabled: true, width: 150},
			{text: "Ref. Date", dataIndex: 'fd_refno', menuDisabled: true, width: 80},
			{text: "Doc. No", dataIndex: 'fs_docno', menuDisabled: true, width: 150},
			{text: "Doc. Date", dataIndex: 'fd_docno', menuDisabled: true, width: 80},
			{text: "Inv. No", dataIndex: 'fs_invno', menuDisabled: true, width: 150},
			{text: "Inv. Date", dataIndex: 'fd_invno', menuDisabled: true, width: 80},
			{text: "Supp Cd", dataIndex: 'fs_kd_suppcount', menuDisabled: true, hidden: true},
			{text: "Supplier", dataIndex: 'fs_nm_supplier', menuDisabled: true, width: 200},
			{text: "Supp Cd", dataIndex: 'fs_kd_supp', menuDisabled: true, hidden: true},
			{text: "Supp Count", dataIndex: 'fs_count_supp', menuDisabled: true, hidden: true},
			{text: "Term Cd", dataIndex: 'fs_kd_term', menuDisabled: true, hidden: true},
			{text: "Term", dataIndex: 'fs_nm_term', menuDisabled: true, hidden: true},
			{text: "Purchase Method Cd", dataIndex: 'fs_kd_salesmtd', menuDisabled: true, hidden: true},
			{text: "Purchase Method", dataIndex: 'fs_nm_salesmtd', menuDisabled: true, hidden: true},
			{text: "Total Price", dataIndex: 'fn_total_price', menuDisabled: true, hidden: true},
			{text: "Total Disc", dataIndex: 'fn_total_disc', menuDisabled: true, hidden: true},
			{text: "Disc", dataIndex: 'fn_disc', menuDisabled: true, hidden: true},
			{text: "Total", dataIndex: 'fn_total', menuDisabled: true, hidden: true},
			{text: "PPN", dataIndex: 'fn_ppn', menuDisabled: true, hidden: true},
			{text: "Tax Cd", dataIndex: 'fs_kd_otax', menuDisabled: true, hidden: true},
			{text: "Tax", dataIndex: 'fs_nm_otax', menuDisabled: true, hidden: true},
			{text: "Tax Percent", dataIndex: 'fn_otax_persen', menuDisabled: true, hidden: true},
			{text: "Tax Total", dataIndex: 'fn_otax_total', menuDisabled: true, hidden: true},
			{text: "Grand Total", dataIndex: 'fn_gtotal', menuDisabled: true, hidden: true}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Ref. No / Doc. No /  Inv. No',
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
				Ext.getCmp('txtDocno').setValue(record.get('fs_docno'));
				Ext.getCmp('txtDocnodt').setValue(tglDMY(record.get('fd_docno')));
				Ext.getCmp('txtInvno').setValue(record.get('fs_invno'));
				Ext.getCmp('txtInvnodt').setValue(tglDMY(record.get('fd_invno')));
				Ext.getCmp('cboSupp').setValue(record.get('fs_kd_suppcount'));
				Ext.getCmp('txtSupp').setValue(record.get('fs_nm_supplier'));
				Ext.getCmp('txtSuppCd').setValue(record.get('fs_kd_supp'));
				Ext.getCmp('txtCount').setValue(record.get('fs_count_supp'));
				Ext.getCmp('cboTerm').setValue(record.get('fs_kd_term'));
				Ext.getCmp('txtTerm').setValue(record.get('fs_nm_term'));
				Ext.getCmp('cboBM').setValue(record.get('fs_kd_salesmtd'));
				Ext.getCmp('txtSumTotalHarga').setValue(record.get('fn_total_price'));
				Ext.getCmp('txtSumTotalDisc').setValue(record.get('fn_total_disc'));
				Ext.getCmp('txtSumDisc').setValue(record.get('fn_disc'));
				Ext.getCmp('txtSumTotal').setValue(record.get('fn_total'));
				Ext.getCmp('txtPPNTotal').setValue(record.get('fn_ppn'));
				Ext.getCmp('cboTax').setValue(record.get('fs_kd_otax'));
				Ext.getCmp('txtTax').setValue(record.get('fs_nm_otax'));
				Ext.getCmp('txtTaxPersen').setValue(record.get('fn_otax_persen'));
				Ext.getCmp('txtTaxTotal').setValue(record.get('fn_otax_total'));
				Ext.getCmp('txtGTotal').setValue(record.get('fn_gtotal'));
				
				grupGridProd.load();
				grupGridReg.load();
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

	var txtDocno = {
		anchor: '95%',
		emptyText: 'Enter a Document Number',
		fieldLabel: 'Doc. No',
		id: 'txtDocno',
		labelWidth: 50,
		name: 'txtDocno',
		xtype: 'textfield'
	};

	var txtInvno = {
		anchor: '95%',
		emptyText: 'Enter an Invoice Number',
		fieldLabel: 'Inv. No',
		id: 'txtInvno',
		labelWidth: 50,
		name: 'txtInvno',
		xtype: 'textfield'
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

	var txtDocnodt = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		editable: true,
		fieldLabel: 'Doc. Date',
		format: 'd-m-Y',
		id: 'txtDocnodt',
		labelWidth: 70,
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
		name: 'txtDocnodt',
		value: new Date(),
		xtype: 'datefield'
	};

	var txtInvnodt = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		editable: true,
		fieldLabel: 'Inv. Date',
		format: 'd-m-Y',
		id: 'txtInvnodt',
		labelWidth: 70,
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
		name: 'txtInvnodt',
		value: new Date(),
		xtype: 'datefield'
	};

	var grupSupp = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_code','fs_kd_supp','fs_count','fs_nm_code'],
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
			url: 'beli/supp_kode'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_code': Ext.getCmp('cboSupp').getValue(),
					'fs_nm_code': Ext.getCmp('cboSupp').getValue(),
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
		store: grupSupp,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Code", dataIndex: 'fs_kd_supp', menuDisabled: true, hidden: true},
			{text: "Count", dataIndex: 'fs_count', menuDisabled: true, hidden: true},
			{text: "Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 380}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Code / Name',
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
					grupSupp.load();
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
			store: grupSupp,
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
				Ext.getCmp('cboSupp').setValue(record.get('fs_code'));
				Ext.getCmp('txtSuppCd').setValue(record.get('fs_kd_supp'));
				Ext.getCmp('txtCount').setValue(record.get('fs_count'));
				Ext.getCmp('txtSupp').setValue(record.get('fs_nm_code'));
				
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
				grupSupp.load();
				vMask.show();
			}
		}
	});

	var cboSupp = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Supplier',
		id: 'cboSupp',
		labelWidth: 60,
		name: 'cboSupp',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtSupp').setValue('');
				Ext.getCmp('txtSuppCd').setValue('');
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
					winCari2.show();
					winCari2.center();
				}
			}
		}
	};

	var txtSuppCd = {
		anchor: '98%',
		fieldLabel: 'Code',
		hidden: true,
		id: 'txtSuppCd',
		labelWidth: 60,
		name: 'txtSuppCd',
		xtype: 'textfield'
	};

	var txtCount = {
		anchor: '98%',
		fieldLabel: 'Count',
		hidden: true,
		id: 'txtCount',
		labelWidth: 60,
		name: 'txtCount',
		xtype: 'textfield'
	};

	var txtSupp = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Supplier',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtSupp',
		name: 'txtSupp',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupTerm = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_kd_term','fs_nm_term'],
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
			url: 'jual/term'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_term': Ext.getCmp('cboTerm').getValue(),
					'fs_nm_term': Ext.getCmp('cboTerm').getValue(),
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
		store: grupTerm,
		columns: [	
			{xtype: 'rownumberer', width: 45},
			{text: "Term Code", dataIndex: 'fs_kd_term', menuDisabled: true, width: 100},
			{text: "Term Name", dataIndex: 'fs_nm_term', menuDisabled: true, width: 380}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Term Code / Term Name',
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
					grupTerm.load();
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
			store: grupTerm,
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
				Ext.getCmp('cboTerm').setValue(record.get('fs_kd_term'));
				Ext.getCmp('txtTerm').setValue(record.get('fs_nm_term'));
				
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
				grupTerm.load();
				vMask.show();
			}
		}
	});

	var cboTerm = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Term',
		id: 'cboTerm',
		labelWidth: 60,
		name: 'cboTerm',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtTerm').setValue('');
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

	var txtTerm = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Term (Tenor)',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtTerm',
		name: 'txtTerm',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupMetode = Ext.create('Ext.data.Store', {
		autoLoad: true,
		fields: ['fs_kd_vareable','fs_nm_vareable'],
		pageSize: 25,
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'beli/metode'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_vareable': Ext.getCmp('cboBM').getValue(),
					'fs_nm_vareable': Ext.getCmp('cboBM').getValue()
				});
			}
		}
	});
	
	var cboBM = {
		anchor: '60%',
		displayField: 'fs_nm_vareable',
		editable: false,
		fieldLabel: 'Method',
		fieldStyle: 'text-transform: uppercase;',
		hidden: true,
		id: 'cboBM',
		labelWidth: 60,
		listConfig: {
			maxHeight: 110
		},
		name: 'cboBM',
		store: grupMetode,
		value: '00',
		valueField: 'fs_kd_vareable',
		xtype: 'combobox',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	function LuxTax() {
		var xharga = Ext.getCmp('txtHarga').getValue();
		var xdiskon = Ext.getCmp('txtDisc').getValue();
		var xlux = xharga - xdiskon;
		Ext.getCmp('txtLuxKali').setValue(xlux);
		
		var xpersen = Ext.getCmp('txtLuxPersen').getValue();
		var xharga2 = Ext.getCmp('txtLuxKali').getValue();
		var xtotal = (xpersen / 100) * xharga2;
		Ext.getCmp('txtLuxTotal').setValue(xtotal);
	}

	function EditLuxTax() {
		var record = gridProd.getSelectionModel().getSelection()[0];
		
		var xharga = record.get('fn_harga');
		var xdiskon = record.get('fn_diskon');
		var xpersen = record.get('fn_persen');
		
		var xtotallux = (xpersen / 100) * (xharga - xdiskon);
		record.set('fn_lux',xtotallux);
		fnTotal();
	}

	function fnTotal() {
		var xtotalharga = 0;
		var xtotaldisc = 0;
		var xtotallux = 0;
		var xppntotal = 0;
		
		var store = gridProd.getStore();
		store.each(function(record, idx) {
			xtotalharga = xtotalharga + (record.get('fn_qty') * record.get('fn_harga'));
			xtotaldisc = xtotaldisc + (record.get('fn_qty') * record.get('fn_diskon'));
			xtotallux = xtotallux + (record.get('fn_qty') * record.get('fn_lux'));
		});
		
		var xdisc = Ext.getCmp('txtSumDisc').getValue();
		var xtotal = xtotalharga - xtotaldisc + xtotallux - xdisc;
		var xotax = Ext.getCmp('txtTaxEx').getValue();
		
		// if (xotax === 0) {//include
			// xtotal2 = Math.round(xtotal / 1.1);
		// }
		// else {//exclude
			xtotal2 = xtotal;
		// }
		
		Ext.getCmp('txtSumTotalHarga').setValue(xtotalharga);
		Ext.getCmp('txtSumTotalDisc').setValue(xtotaldisc);
		Ext.getCmp('txtSumTotalLux').setValue(xtotallux);
		Ext.getCmp('txtSumTotal').setValue(xtotal);
		
		Ext.getCmp('txtPPNKali').setValue(xtotal2);
		
		
		if (Ext.getCmp('txtPPNTotal').getValue() == 10 / 100 * xtotal2) {
			xppntotal = 10 / 100 * xtotal2;
		}
		else {
			xppntotal = Ext.getCmp('txtPPNTotal').getValue();
		}
		Ext.getCmp('txtPPNTotal').setValue(xppntotal);
		
		var xtaxtotal = Ext.getCmp('txtTaxPersen').getValue() / 100 * xtotal2;
		Ext.getCmp('txtTaxKali').setValue(xtotal2);
		Ext.getCmp('txtTaxTotal').setValue(xtaxtotal);
		
		// xgtotal = Math.round(xtotal2 + xppntotal + xtaxtotal);
		xgtotal = xtotal2 + xppntotal + xtaxtotal;
		Ext.getCmp('txtGTotal').setValue(xgtotal);
	}

	var grupProd = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_product','fs_nm_product',
			'fs_kd_unit','fs_nm_unit',
			'fn_harga','fn_diskon'
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
			url: 'beli/product'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_product': Ext.getCmp('cboProd').getValue(),
					'fs_nm_product': Ext.getCmp('cboProd').getValue(),
					'fs_cari': Ext.getCmp('txtCari4').getValue(),
					'fd_refno': Ext.Date.format(Ext.getCmp('txtRefnodt').getValue(), 'Ymd')
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
		store: grupProd,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Product Code", dataIndex: 'fs_kd_product', menuDisabled: true, width: 150},
			{text: "Product Name", dataIndex: 'fs_nm_product', menuDisabled: true, width: 330},
			{text: "Unit Code", dataIndex: 'fs_kd_unit', menuDisabled: true, hidden: true},
			{text: "Unit Name", dataIndex: 'fs_nm_unit', menuDisabled: true, hidden: true},
			{text: "Price", dataIndex: 'fn_harga', menuDisabled: true, hidden: true},
			{text: "Disc", dataIndex: 'fn_diskon', menuDisabled: true, hidden: true}			
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Product Code / Product Name',
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
					winCari4.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboProd').setValue(record.get('fs_kd_product'));
				Ext.getCmp('txtProd').setValue(record.get('fs_nm_product'));
				Ext.getCmp('cboUnit').setValue(record.get('fs_kd_unit'));
				Ext.getCmp('txtHarga').setValue(record.get('fn_harga'));
				Ext.getCmp('txtDisc').setValue(record.get('fn_diskon'));
				
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
				grupProd.load();
				vMask.show();
			}
		}
	});
	
	var grupUnit = Ext.create('Ext.data.Store', {
		autoLoad: true,
		fields: ['fs_kd_unit','fs_nm_unit'],
		pageSize: 25,
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'beli/unit'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_product': Ext.getCmp('cboProd').getValue(),
					'fs_kd_unit': Ext.getCmp('cboUnit').getValue(),
					'fs_nm_unit': Ext.getCmp('cboUnit').getValue()
				});
			}
		}
	});

	var grupTax = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_kd_tax','fs_nm_tax','fn_persen'],
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
			url: 'beli/tax'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_tax': Ext.getCmp('cboLux').getValue(),
					'fs_nm_tax': Ext.getCmp('cboLux').getValue(),
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
		store: grupTax,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Lux Code", dataIndex: 'fs_kd_tax', menuDisabled: true, width: 100},
			{text: "Lux Name", dataIndex: 'fs_nm_tax', menuDisabled: true, width: 300},
			{text: "Percent", dataIndex: 'fn_persen', menuDisabled: true, width: 100}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Lux Name',
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
					grupTax.load();
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
			store: grupTax,
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
				Ext.getCmp('cboLux').setValue(record.get('fs_kd_tax'));
				Ext.getCmp('txtLux').setValue(record.get('fs_nm_tax'));
				Ext.getCmp('txtLuxPersen').setValue(record.get('fn_persen'));
				
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
				grupTax.load();
				vMask.show();
			}
		}
	});
	

	var grupSearchGridTax = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_tax','fs_nm_tax','fn_persen'
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
			url: 'beli/tax'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_cari': Ext.getCmp('txtCari6').getValue()
				});
			}
		}
	});


	var winGrid6 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupSearchGridTax,
		columns: [	
			{xtype: 'rownumberer', width: 45},
			{text: "Lux Code", dataIndex: 'fs_kd_tax', menuDisabled: true, width: 100},
			{text: "Lux Name", dataIndex: 'fs_nm_tax', menuDisabled: true, width: 300},
			{text: "Percent", dataIndex: 'fn_persen', menuDisabled: true, width: 80}		
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Lux Code / Lux Name',
				id: 'txtCari6',
				name: 'txtCari6',
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
					grupSearchGridTax.load();
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
			store: grupSearchGridTax,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari6.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				var recordgrid = gridProd.getSelectionModel().getSelection()[0];
				recordgrid.set('fs_kd_lux',record.get('fs_kd_tax'));
				recordgrid.set('fs_nm_lux',record.get('fs_nm_tax'));
				recordgrid.set('fn_persen',record.get('fn_persen'));
				
				EditLuxTax();
				winCari6.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});

	var winCari6 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid6
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupSearchGridTax.load();
				vMask.show();
			}
		}
	});

	var cellEditingProd = Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToEdit: 2,
		listeners: {
			edit: function(editor, e) {
				EditLuxTax();
			}
		}
	});

	Ext.define('DataGridProd', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_product', type: 'string'},
			{name: 'fs_nm_product', type: 'string'},
			{name: 'fn_qty', type: 'float'},
			{name: 'fs_kd_unit', type: 'string'},
			{name: 'fn_harga', type: 'float'},
			{name: 'fn_diskon', type: 'float'},
			{name: 'fs_kd_lux', type: 'string'},
			{name: 'fs_nm_lux', type: 'string'},
			{name: 'fn_persen', type: 'float'},
			{name: 'fn_lux', type: 'float'},
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
			url: 'beli/grid_prod'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboRefno').getValue()
				});
			},
			load: function() {
				fnTotal();
				var xtotal = grupGridProd.getCount();
				
				if (xtotal > 0) {
					var store = gridProd.getStore();
					var xqty = 0;
					
					store.each(function(record, idx) {
						xqty = parseInt(record.get('fs_seqno'));
					});
					Ext.getCmp('txtUrutProd').setValue(xqty);
					
					gridProd.getSelectionModel().select(0);
				}
			}
		}
	});

	var gridProd = Ext.create('Ext.grid.Panel', {
		anchor: '100%',
		defaultType: 'textfield',
		height: 225,
		sortableColumns: false,
		store: grupGridProd,
		columns: [{
			xtype: 'rownumberer',
			width: 25
		},{
			dataIndex: 'fs_kd_product',
			text: 'Product Code',
			flex: 0.8,
			menuDisabled: true
		},{
			dataIndex: 'fs_nm_product',
			text: 'Product Name',
			flex: 1.2,
			menuDisabled: true
		},{
			align: 'right',
			dataIndex: 'fn_qty',
			text: 'Qty',
			flex: 0.25,
			menuDisabled: true,
			editor: {
				editable: false,
				xtype: 'textfield',
				triggers: {
					cari: {
						cls: 'x-form-search-trigger',
						handler: function() {
							var recordgrid = gridProd.getSelectionModel().getSelection()[0];
							var xKdProd = recordgrid.get('fs_kd_product');
							var xNmProd = recordgrid.get('fs_nm_product');
							
							var xtext = xKdProd + ' - ' + xNmProd;
							Ext.getCmp('txtProdAktif').setValue(xKdProd);
							Ext.getCmp('txtProdAktif2').setValue(xtext);
							fnProduk();
							
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
							vMask.show();
							winReg.show();
							winReg.center();
						}
					}
				}
			}
		},{
			dataIndex: 'fs_kd_unit',
			text: 'Unit',
			flex: 0.25,
			menuDisabled: true
		},{
			align: 'right',
			dataIndex: 'fn_harga',
			flex: 0.75,
			format: '0,000.00',
			menuDisabled: true,
			text: 'Price',
			xtype: 'numbercolumn',
			editor: [
				Ext.create('Ext.ux.form.NumericField', {
					alwaysDisplayDecimals: true,
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
			flex: 0.65,
			format: '0,000.00',
			menuDisabled: true,
			text: 'Disc',
			xtype: 'numbercolumn',
			editor: [
				Ext.create('Ext.ux.form.NumericField', {
					alwaysDisplayDecimals: true,
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
			dataIndex: 'fs_kd_lux',
			hidden: true,
			text: 'Lux Code',
			flex: 0.25,
			menuDisabled: true
		},{
			dataIndex: 'fs_nm_lux',
			text: 'Lux Name',
			flex: 0.25,
			menuDisabled: true,
			editor: {
				editable: false,
				xtype: 'textfield',
				listeners: {
					change: function(field, newValue) {
						var recordgrid = gridProd.getSelectionModel().getSelection()[0];
						recordgrid.set('fs_kd_lux','');
						recordgrid.set('fn_persen',0);
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
							winCari6.show();
							winCari6.center();
						}
					}
				}
			}
		},{
			align: 'right',
			dataIndex: 'fn_persen',
			flex: 0.3,
			format: '0,000.00',
			menuDisabled: true,
			text: '%Lux',
			xtype: 'numbercolumn'
		},{
			align: 'right',
			dataIndex: 'fn_lux',
			flex: 0.5,
			format: '0,000.00',
			menuDisabled: true,
			text: 'Lux Value',
			xtype: 'numbercolumn'
		},{
			dataIndex: 'fs_seqno',
			text: 'Seqno',
			hidden: true,
			menuDisabled: true,
			flex: 0.35
		}],
		listeners: {
			selectionchange: function(view, records) {
				gridProd.down('#removeData').setDisabled(!records.length);
			}
		},
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
		},
		bbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				id: 'txtToolTip',
				labelWidth: 65,
				name: 'txtToolTip',
				value: '<*Double click on the Qty, price, disc and lux code to edit>',
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
		}],
		tbar: [{
			flex: 1.5,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '97%',
				emptyText: 'Select a Product',
				fieldLabel: 'Product',
				id: 'cboProd',
				labelAlign: 'top',
				name: 'cboProd',
				xtype: 'textfield',
				listeners: {
					change: function(field, newValue) {
						Ext.getCmp('txtProd').setValue('');
						Ext.getCmp('cboUnit').setValue('UNIT');
						Ext.getCmp('txtHarga').setValue('');
						Ext.getCmp('txtLuxKali').setValue('');
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
			},{
				anchor: '97%',
				emptyText: 'Enter a Product Name',
				hidden: true,
				id: 'txtProd',
				name: 'txtProd',
				xtype: 'textfield'
			}]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '95%',
				displayField: 'fs_nm_unit',
				editable: false,
				fieldLabel: 'Unit',
				fieldStyle: 'text-transform: uppercase;',
				id: 'cboUnit',
				labelAlign: 'top',
				listConfig: {
					maxHeight: 110
				},
				name: 'cboUnit',
				store: grupUnit,
				value: 'UNIT',
				valueField: 'fs_kd_unit',
				xtype: 'combobox',
				listeners: {
					change: function(field, newValue) {
						field.setValue(newValue.toUpperCase());
					}
				}
			}]
		},{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [
				Ext.create('Ext.ux.form.NumericField', {
					alwaysDisplayDecimals: true,
					anchor: '95%',
					currencySymbol: null,
					decimalPrecision: 2,
					decimalSeparator: '.',
					fieldLabel: 'Price',
					fieldStyle: 'text-align: right;',
					hideTrigger: true,
					id: 'txtHarga',
					keyNavEnabled: false,
					labelAlign: 'top',
					mouseWheelEnabled: false,
					name: 'txtHarga',
					thousandSeparator: ',',
					useThousandSeparator: true,
					value: 0,
					listeners: {
						change: function(value) {
							if (Ext.isEmpty(Ext.getCmp('txtHarga').getValue())) {
								Ext.getCmp('txtHarga').setValue(0);
							}
							else {
								LuxTax();
								return value;
							}
						}
					}
				})
			]
		},{
			flex: 0.825,
			layout: 'anchor',
			xtype: 'container',
			items: [
				Ext.create('Ext.ux.form.NumericField', {
					alwaysDisplayDecimals: true,
					anchor: '95%',
					currencySymbol: null,
					decimalPrecision: 2,
					decimalSeparator: '.',
					fieldLabel: 'Discount',
					fieldStyle: 'text-align: right;',
					hideTrigger: true,
					id: 'txtDisc',
					keyNavEnabled: false,
					labelAlign: 'top',
					mouseWheelEnabled: false,
					name: 'txtDisc',
					thousandSeparator: ',',
					useThousandSeparator: true,
					value: 0,
					listeners: {
						change: function(value) {
							if (Ext.isEmpty(Ext.getCmp('txtDisc').getValue())) {
								Ext.getCmp('txtDisc').setValue(0);
							}
							else {
								LuxTax();
								return value;
							}
						}
					}
				}),
				Ext.create('Ext.ux.form.NumericField', {
					alwaysDisplayDecimals: false,
					anchor: '95%',
					currencySymbol: null,
					decimalPrecision: 2,
					decimalSeparator: '.',
					hidden: true,
					hideTrigger: true,
					id: 'txtUrutProd',
					keyNavEnabled: false,
					mouseWheelEnabled: false,
					name: 'txtUrutProd',
					thousandSeparator: ',',
					useThousandSeparator: false,
					value: 0,
					listeners: {
						change: function(value) {
							if (Ext.isEmpty(Ext.getCmp('txtUrutProd').getValue())) {
								Ext.getCmp('txtUrutProd').setValue(0);
							}
							else {
								return value;
							}
						}
					}
				})
			]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '95%',
				emptyText: 'Select a Lux Tax',
				fieldLabel: 'Lux',
				id: 'cboLux',
				labelAlign: 'top',
				name: 'cboLux',
				xtype: 'textfield',
				listeners: {
					change: function(field, newValue) {
						Ext.getCmp('txtLux').setValue('');
						Ext.getCmp('txtLuxPersen').setValue(0);
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
			},{
				anchor: '95%',
				emptyText: 'Enter a Lux Name',
				hidden: true,
				id: 'txtLux',
				name: 'txtLux',
				xtype: 'textfield'
			}]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: [
				Ext.create('Ext.ux.form.NumericField', {
					alwaysDisplayDecimals: true,
					anchor: '95%',
					currencySymbol: null,
					decimalPrecision: 2,
					decimalSeparator: '.',
					editable: false,
					fieldLabel: 'Percent',
					fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
					hideTrigger: true,
					id: 'txtLuxPersen',
					keyNavEnabled: false,
					labelAlign: 'top',
					mouseWheelEnabled: false,
					name: 'txtLuxPersen',
					thousandSeparator: ',',
					useThousandSeparator: true,
					value: 0,
					listeners: {
						change: function(value) {
							if (Ext.isEmpty(Ext.getCmp('txtLuxPersen').getValue())) {
								Ext.getCmp('txtLuxPersen').setValue(0);
							}
							else {
								LuxTax();
								return value;
							}
						}
					}
				})
			]
		},{
			flex: 0.9,
			layout: 'anchor',
			xtype: 'container',
			items: [
				Ext.create('Ext.ux.form.NumericField', {
					alwaysDisplayDecimals: true,
					anchor: '95%',
					currencySymbol: null,
					decimalPrecision: 2,
					decimalSeparator: '.',
					editable: false,
					fieldLabel: 'x',
					fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
					hideTrigger: true,
					id: 'txtLuxKali',
					keyNavEnabled: false,
					labelAlign: 'top',
					mouseWheelEnabled: false,
					name: 'txtLuxKali',
					thousandSeparator: ',',
					useThousandSeparator: true,
					value: 0,
					listeners: {
						change: function(value) {
							if (Ext.isEmpty(Ext.getCmp('txtLuxKali').getValue())) {
								Ext.getCmp('txtLuxKali').setValue(0);
							}
							else {
								return value;
							}
						}
					}
				})
			]
		},{
			flex: 0.825,
			layout: 'anchor',
			xtype: 'container',
			items: [
				Ext.create('Ext.ux.form.NumericField', {
					alwaysDisplayDecimals: true,
					anchor: '95%',
					currencySymbol: null,
					decimalPrecision: 2,
					decimalSeparator: '.',
					editable: false,
					fieldLabel: '=',
					fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
					hideTrigger: true,
					id: 'txtLuxTotal',
					keyNavEnabled: false,
					labelAlign: 'top',
					mouseWheelEnabled: false,
					name: 'txtLuxTotal',
					thousandSeparator: ',',
					useThousandSeparator: true,
					value: 0,
					listeners: {
						change: function(value) {
							if (Ext.isEmpty(Ext.getCmp('txtLuxTotal').getValue())) {
								Ext.getCmp('txtLuxTotal').setValue(0);
							}
							else {
								return value;
							}
						}
					}
				})
			]
		},{
			xtype: 'buttongroup',
			columns: 1,
			defaults: {
				scale: 'small'
			},
			items: [{
				iconCls: 'icon-add',
				text: 'Add',
				handler: function() {
					var xtotal = grupGridProd.getCount();
					var xurut = Ext.getCmp('txtUrutProd').getValue() + 1;
					var xprod = Ext.getCmp('cboProd').getValue();
					var xdata = Ext.create('DataGridProd', {
						fs_kd_product: Ext.getCmp('cboProd').getValue(),
						fs_nm_product: Ext.getCmp('txtProd').getValue(),
						fn_qty: '0',
						fs_kd_unit: Ext.getCmp('cboUnit').getValue(),
						fn_harga: Ext.getCmp('txtHarga').getValue(),
						fn_diskon: Ext.getCmp('txtDisc').getValue(),
						fs_kd_lux: Ext.getCmp('cboLux').getValue(),
						fs_nm_lux: Ext.getCmp('txtLux').getValue(),
						fn_persen: Ext.getCmp('txtLuxPersen').getValue(),
						fn_lux: Ext.getCmp('txtLuxTotal').getValue(),
						fs_seqno: zeroPad(xurut, 6)
					});
					
					var store = gridProd.getStore();
					var xlanjut = true;
					store.each(function(record, idx) {
						var xtext = record.get('fs_kd_product').trim();
						
						if (xtext == xprod) {
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.OK,
								closable: false,
								icon: Ext.Msg.WARNING,
								msg: 'Product already exists, add product cancel!!',
								title: 'IDS'
							});
							xlanjut = false;
						}
						
					});
					if (xlanjut === false) {
						return;
					}
					
					xprod = Ext.getCmp('txtProd').getValue();
					if (xprod.trim() === '') {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.Msg.WARNING,
							msg: 'Product is not in the list!',
							title: 'IDS'
						});
						return;
					}
					
					var xunit = Ext.getCmp('cboUnit').getValue();
					if (xunit.trim() === '') {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.Msg.WARNING,
							msg: 'Unit is not in the list!',
							title: 'IDS'
						});
						return;
					}
					
					var xharga = Ext.getCmp('txtHarga').getValue();
					if (xharga === 0) {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.Msg.WARNING,
							msg: 'Price is zero, please fill in advance!',
							title: 'IDS'
						});
						return;
					}
					
					var xlux = Ext.getCmp('cboLux').getValue();
					var xlux2 = Ext.getCmp('txtLux').getValue();
					if ((xlux.trim() || '') && (xlux2.trim() === '')) {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.Msg.WARNING,
							msg: 'Lux Tax is not in the list!',
							title: 'IDS'
						});
						return;
					}
					
					grupGridProd.insert(xtotal, xdata);
					Ext.getCmp('cboProd').setValue('');
					Ext.getCmp('txtProd').setValue('');
					Ext.getCmp('cboUnit').setValue('UNIT');
					Ext.getCmp('txtHarga').setValue('');
					Ext.getCmp('txtDisc').setValue('');
					Ext.getCmp('cboLux').setValue('');
					Ext.getCmp('txtLux').setValue('');
					Ext.getCmp('txtLuxPersen').setValue('');
					Ext.getCmp('txtLuxKali').setValue('');
					Ext.getCmp('txtLuxTotal').setValue('');
					Ext.getCmp('txtUrutProd').setValue(xurut);
					
					xtotal = grupGridProd.getCount() - 1;
					gridProd.getSelectionModel().select(xtotal);
				}
			},{
				iconCls: 'icon-delete',
				itemId: 'removeData',
				text: 'Delete',
				handler: function() {
					var record = gridProd.getSelectionModel().getSelection()[0];
					
					var xseqno = record.get('fs_seqno');
					var arr_seqno = Array();
					
					grupGridReg.clearFilter();
					var store = gridReg.getStore();
					store.each(function(record, idx) {
						arr_seqno.push(record.get('fs_seqno').trim());
					});
					
					var xtotal = grupGridReg.getCount()-1;
					var xxseqno = '';
					for (i=xtotal;i>=0;i--) {
						xxseqno = arr_seqno[i];
						
						if (xseqno.trim() == xxseqno.trim()) {
							grupGridReg.removeAt(i);
						}
					}
					
					gridReg.getView().refresh();
					
					var sm = gridProd.getSelectionModel();
					cellEditingProd.cancelEdit();
					grupGridProd.remove(sm.getSelection());
					gridProd.getView().refresh();
					if (grupGridProd.getCount() > 0) {
						sm.select(0);
					}
					fnTotal();
					Ext.getCmp('txtProdAktif').setValue('');
					Ext.getCmp('txtProdAktif2').setValue('');
				},
				disabled: true
			}]
		}]
	});

	var txtSumTotalHarga = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: true,
		anchor: '95%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		editable: false,
		fieldLabel: 'Total Price',
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		hideTrigger: true,
		id: 'txtSumTotalHarga',
		keyNavEnabled: false,
		labelAlign: 'top',
		mouseWheelEnabled: false,
		name: 'txtSumTotalHarga',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0
	});

	var txtSumTotalDisc = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: true,
		anchor: '95%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		editable: false,
		fieldLabel: 'Total Disc',
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		hideTrigger: true,
		id: 'txtSumTotalDisc',
		keyNavEnabled: false,
		labelAlign: 'top',
		mouseWheelEnabled: false,
		name: 'txtSumTotalDisc',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0
	});

	var txtSumTotalLux = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: true,
		anchor: '95%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		editable: false,
		fieldLabel: 'Total Lux Tax',
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		hideTrigger: true,
		id: 'txtSumTotalLux',
		keyNavEnabled: false,
		labelAlign: 'top',
		mouseWheelEnabled: false,
		name: 'txtSumTotalLux',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0
	});

	var txtSumDisc = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: true,
		anchor: '95%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		fieldLabel: 'Discount',
		fieldStyle: 'text-align: right;',
		hideTrigger: true,
		id: 'txtSumDisc',
		keyNavEnabled: false,
		labelAlign: 'top',
		mouseWheelEnabled: false,
		name: 'txtSumDisc',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0,
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtSumDisc').getValue())) {
					Ext.getCmp('txtSumDisc').setValue(0);
				}
				else {
					fnTotal();
					return value;
				}
			}
		}
	});

	var txtSumTotal = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: true,
		anchor: '100%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		editable: false,
		fieldLabel: 'Total',
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		hideTrigger: true,
		id: 'txtSumTotal',
		keyNavEnabled: false,
		labelAlign: 'top',
		mouseWheelEnabled: false,
		name: 'txtSumTotal',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0
	});

	var lPPN = {
		id: 'lPPN',
		name: 'lPPN',
		value: 'PPN',
		xtype: 'displayfield'
	};

	var txtPPNPersen = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: false,
		anchor: '95%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		editable: false,
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		hideTrigger: true,
		id: 'txtPPNPersen',
		keyNavEnabled: false,
		mouseWheelEnabled: false,
		name: 'txtPPNPersen',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 10
	});

	var lPersen2 = {
		id: 'lPersen2',
		name: 'lPersen2',
		value: '(%)',
		xtype: 'displayfield'
	};

	var lX2 = {
		id: 'lX2',
		name: 'lX2',
		value: 'x',
		xtype: 'displayfield'
	};

	var txtPPNKali = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: true,
		anchor: '96%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		editable: false,
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		hideTrigger: true,
		id: 'txtPPNKali',
		keyNavEnabled: false,
		mouseWheelEnabled: false,
		name: 'txtPPNKali',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0
	});

	var lhsl2 = {
		id: 'lhsl2',
		name: 'lhsl2',
		value: '=',
		xtype: 'displayfield'
	};

	var txtPPNTotal = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: true,
		anchor: '98%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		fieldStyle: 'text-align: right;',
		hideTrigger: true,
		id: 'txtPPNTotal',
		keyNavEnabled: false,
		mouseWheelEnabled: false,
		name: 'txtPPNTotal',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0,
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtPPNTotal').getValue())) {
					Ext.getCmp('txtPPNTotal').setValue(0);
				}
				else {
					return value;
				}
			},
			blur: function() {
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
			var xKali = Ext.getCmp('txtPPNKali').getValue();
			Ext.getCmp('txtPPNTotal').setValue(10 / 100 * xKali);
			fnTotal();
		}
	};

	var grupTax2 = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_kd_tax','fs_nm_tax','fn_persen'],
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
			url: 'beli/tax'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_tax': Ext.getCmp('cboTax').getValue(),
					'fs_nm_tax': Ext.getCmp('cboTax').getValue(),
					'fs_cari': Ext.getCmp('txtCari9').getValue()
				});
			}
		}
	});

	var winGrid9 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupTax2,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Tax Code", dataIndex: 'fs_kd_tax', menuDisabled: true, width: 100},
			{text: "Tax Name", dataIndex: 'fs_nm_tax', menuDisabled: true, width: 300},
			{text: "Percent", dataIndex: 'fn_persen', menuDisabled: true, width: 80}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Tax Code / Tax Name',
				id: 'txtCari9',
				name: 'txtCari9',
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
					grupTax2.load();
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
			store: grupTax2,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari9.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboTax').setValue(record.get('fs_kd_tax'));
				Ext.getCmp('txtTax').setValue(record.get('fs_nm_tax'));
				Ext.getCmp('txtTaxPersen').setValue(record.get('fn_persen'));
				
				winCari9.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});

	var winCari9 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid9
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupTax2.load();
				vMask.show();
			}
		}
	});

	var cboTax = {
		anchor: '95%',
		emptyText: 'Select',
		fieldLabel: 'Tax',
		id: 'cboTax',
		labelWidth: 25,
		name: 'cboTax',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtTax').setValue('');
				Ext.getCmp('txtTaxPersen').setValue(0);
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
					winCari9.show();
					winCari9.center();
				}
			}
		}
	};

	var txtTax = {
		anchor: '91%',
		emptyText: 'a Tax',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtTax',
		name: 'txtTax',
		readOnly: true,
		xtype: 'textfield'
	};

	var txtTaxPersen = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: true,
		anchor: '95%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		editable: false,
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		hideTrigger: true,
		id: 'txtTaxPersen',
		keyNavEnabled: false,
		mouseWheelEnabled: false,
		name: 'txtTaxPersen',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0,
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtTaxPersen').getValue())) {
					Ext.getCmp('txtTaxPersen').setValue(0);
				}
				else {
					fnTotal();
					return value;
				}
			}
		}
	});

	var lPersen3 = {
		id: 'lPersen3',
		name: 'lPersen3',
		value: '(%)',
		xtype: 'displayfield'
	};

	var lX3 = {
		id: 'lX3',
		name: 'lX3',
		value: 'x',
		xtype: 'displayfield'
	};

	var txtTaxKali = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: true,
		anchor: '96%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		editable: false,
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		hideTrigger: true,
		id: 'txtTaxKali',
		keyNavEnabled: false,
		mouseWheelEnabled: false,
		name: 'txtTaxKali',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0,
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtTaxKali').getValue())) {
					Ext.getCmp('txtTaxKali').setValue(0);
				}
				else {
					return value;
				}
			}
		}
	});

	var lhsl3 = {
		id: 'lhsl3',
		name: 'lhsl3',
		value: '=',
		xtype: 'displayfield'
	};

	var txtTaxTotal = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: true,
		anchor: '100%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		editable: false,
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		hideTrigger: true,
		id: 'txtTaxTotal',
		keyNavEnabled: false,
		mouseWheelEnabled: false,
		name: 'txtTaxTotal',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0,
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtTaxTotal').getValue())) {
					Ext.getCmp('txtTaxTotal').setValue(0);
				}
				else {
					return value;
				}
			}
		}
	});

	var txtTaxEx = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: false,
		anchor: '100%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		editable: true,
		fieldLabel: 'Exclude Tax',
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		hidden: true,
		hideTrigger: true,
		id: 'txtTaxEx',
		keyNavEnabled: false,
		labelWidth: 70,
		mouseWheelEnabled: false,
		name: 'txtTaxEx',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: votax,
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtTaxEx').getValue())) {
					Ext.getCmp('txtTaxEx').setValue(0);
				}
				else {
					return value;
				}
			}
		}
	});

	var txtGTotal = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: true,
		anchor: '100%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		editable: false,
		fieldLabel: 'Grand Total',
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		hideTrigger: true,
		id: 'txtGTotal',
		keyNavEnabled: false,
		labelWidth: 68,
		mouseWheelEnabled: false,
		name: 'txtGTotal',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0,
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtGTotal').getValue())) {
					Ext.getCmp('txtGTotal').setValue(0);
				}
				else {
					return value;
				}
			}
		}
	});

	var grupColor = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_kd_vareable','fs_nm_vareable'],
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
			url: 'beli/color'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_vareable': Ext.getCmp('cboColor').getValue(),
					'fs_nm_vareable': Ext.getCmp('cboColor').getValue(),
					'fs_cari': Ext.getCmp('txtCari7').getValue()
				});
			}
		}
	});

	/*
	var winGrid7 = Ext.create('Ext.ux.LiveSearchGridPanel', {
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
					winCari7.hide();
				}
			}]
		}),
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Color Code", dataIndex: 'fs_kd_vareable', menuDisabled: true, width: 100},
			{text: "Color Name", dataIndex: 'fs_nm_vareable', menuDisabled: true, width: 380}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboColor').setValue(record.get('fs_kd_vareable'));
				Ext.getCmp('txtColor').setValue(record.get('fs_nm_vareable'));
				
				winCari7.hide();
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
	*/
	var winGrid7 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupColor,
		columns: [	
			{xtype: 'rownumberer', width: 45},
			{text: "Color Code", dataIndex: 'fs_kd_vareable', menuDisabled: true, width: 100},
			{text: "Color Name", dataIndex: 'fs_nm_vareable', menuDisabled: true, width: 380}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Color Code / Color Name',
				id: 'txtCari7',
				name: 'txtCari7',
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
					grupColor.load();
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
			store: grupColor,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari7.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboColor').setValue(record.get('fs_kd_vareable'));
				Ext.getCmp('txtColor').setValue(record.get('fs_nm_vareable'));
				
				winCari7.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});


	var winCari7 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid7
		],
		listeners: {
			beforehide: function() {
				vMask2.hide();
			},
			beforeshow: function() {
				grupColor.load();
				vMask2.show();
			}
		}
	});

	var grupWH = Ext.create('Ext.data.Store', {
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
			url: 'beli/wh'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_code': Ext.getCmp('cboWH').getValue(),
					'fs_nm_code': Ext.getCmp('cboWH').getValue(),
					'fs_cari': Ext.getCmp('txtCari8').getValue()
				});
			}
		}
	});

	/*
	var winGrid8 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupWH,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupWH,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari8.hide();
				}
			}]
		}),
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Warehouse Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Warehouse Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 380}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboWH').setValue(record.get('fs_code'));
				Ext.getCmp('txtWH').setValue(record.get('fs_nm_code'));
				
				winCari8.hide();
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
	*/

	var winGrid8 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupWH,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Warehouse Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Warehouse Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 380}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Warehouse Code / Warehouse Name',
				id: 'txtCari8',
				name: 'txtCari8',
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
					grupWH.load();
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
			store: grupWH,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari8.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboWH').setValue(record.get('fs_code'));
				Ext.getCmp('txtWH').setValue(record.get('fs_nm_code'));
				
				winCari8.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});


	var winCari8 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid8
		],
		listeners: {
			beforehide: function() {
				vMask2.hide();
			},
			beforeshow: function() {
				grupWH.load();
				vMask2.show();
			}
		}
	});

	var cellEditingReg = Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToEdit: 2
	});

	Ext.define('DataGridReg', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_rangka', type: 'string'},
			{name: 'fs_mesin', type: 'string'},
			{name: 'fs_cc', type: 'float'},
			{name: 'fs_thn', type: 'float'},
			{name: 'fs_kd_color', type: 'string'},
			{name: 'fs_nm_color', type: 'string'},
			{name: 'fs_kd_wh', type: 'string'},
			{name: 'fs_nm_wh', type: 'string'},
			{name: 'fn_hpp', type: 'float'},
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
			url: 'beli/grid_reg'
		},
		listeners: {
			beforeload: function(store) {
				grupGridReg.clearFilter();
				grupGridReg.removeAll();
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboRefno').getValue()
				});
			}
		}
	});

	var gridReg = Ext.create('Ext.grid.Panel', {
		defaultType: 'textfield',
		height: 350,
		sortableColumns: false,
		store: grupGridReg,
		columns: [{
			xtype: 'rownumberer',
			width: 30
		},{
			text: 'Chassis',
			dataIndex: 'fs_rangka',
			menuDisabled: true,
			width: 200
		},{
			text: 'Machine',
			dataIndex: 'fs_mesin',
			menuDisabled: true,
			width: 180
		},{
			align: 'center',
			text: 'Cylinder',
			dataIndex: 'fs_cc',
			menuDisabled: true,
			width: 60
		},{
			align: 'center',
			text: 'Year',
			dataIndex: 'fs_thn',
			menuDisabled: true,
			width: 50
		},{
			text: 'Color Code',
			dataIndex: 'fs_kd_color',
			hidden: true,
			menuDisabled: true,
			width: 75
		},{
			text: 'Color Name',
			dataIndex: 'fs_nm_color',
			menuDisabled: true,
			width: 180
		},{
			text: 'WH Code',
			dataIndex: 'fs_kd_wh',
			hidden: true,
			menuDisabled: true,
			width: 75
		},{
			text: 'WH Name',
			dataIndex: 'fs_nm_wh',
			menuDisabled: true,
			width: 170
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
		listeners: {
			selectionchange: function(view, records) {
				gridReg.down('#removeData').setDisabled(!records.length);
			}
		},
		plugins: [
			cellEditingReg
		],
		bbar: [{
			iconCls: 'icon-delete',
			text: 'Delete All',
			xtype: 'button',
			handler: function() {
				grupGridReg.removeAll();
				
				var xprod = Ext.getCmp('txtProdAktif').getValue();
				var xQty = 0;
				store = gridReg.getStore();
				store.each(function(record, idx) {
					if (xprod.trim() == record.get('fs_kd_product').trim()) {
						xQty = xQty + 1;
					}
				});
				
				store = gridProd.getStore();
				store.each(function(record, idx) {
					if (xprod.trim() == record.get('fs_kd_product').trim()) {
						record.set('fn_qty',xQty);
					}
				});
				gridProd.getView().refresh();
			}
		},{
			xtype: 'tbfill'
		},{
			fieldLabel: '',
			id: 'txtProdAktif2',
			labelAlign: 'left',
			labelSeparator: '',
			labelWidth: 90,
			name: 'txtProdAktif2',
			value: '',
			xtype: 'displayfield'
		}],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '95%',
				emptyText: 'Enter a Chassis',
				fieldLabel: 'Chassis',
				labelAlign: 'top',
				labelSeparator: '',
				id: 'txtRangka',
				name: 'txtRangka',
				xtype: 'textfield'
			}]
		},{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '95%',
				emptyText: 'Enter a Machine',
				fieldLabel: 'Machine',
				labelAlign: 'top',
				labelSeparator: '',
				id: 'txtMesin',
				name: 'txtMesin',
				xtype: 'textfield'
			}]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: [
				Ext.create('Ext.ux.form.NumericField', {
					alwaysDisplayDecimals: false,
					anchor: '95%',
					currencySymbol: null,
					decimalPrecision: 2,
					decimalSeparator: '.',
					fieldLabel: 'Cylinder',
					fieldStyle: 'text-align: right;',
					hideTrigger: false,
					id: 'txtCC',
					keyNavEnabled: true,
					labelAlign: 'top',
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
				})
			]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: [
				Ext.create('Ext.ux.form.NumericField', {
					alwaysDisplayDecimals: false,
					anchor: '95%',
					currencySymbol: null,
					decimalPrecision: 2,
					decimalSeparator: '.',
					fieldLabel: 'Year',
					fieldStyle: 'text-align: right;',
					hideTrigger: false,
					id: 'txtThn',
					keyNavEnabled: true,
					labelAlign: 'top',
					labelSeparator: '',
					maxLength: 4,
					mouseWheelEnabled: false,
					name: 'txtThn',
					thousandSeparator: ',',
					useThousandSeparator: false,
					value: Ext.Date.format(new Date(), 'Y'),
					listeners: {
						change: function(value) {
							if (Ext.isEmpty(Ext.getCmp('txtThn').getValue())) {
								Ext.getCmp('txtThn').setValue(Ext.Date.format(new Date(), 'Y'));
							}
							else {
								return value;
							}
						}
					}
				})
			]
		},{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '95%',
				emptyText: 'Select a Color',
				fieldLabel: 'Color',
				id: 'cboColor',
				labelAlign: 'top',
				labelSeparator: '',
				name: 'cboColor',
				xtype: 'textfield',
				listeners: {
					change: function(field, newValue) {
						Ext.getCmp('txtColor').setValue('');
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
							winCari7.show();
							winCari7.center();
						}
					}
				}
			},{
				anchor: '95%',
				emptyText: 'Enter a Color Name',
				hidden: true,
				id: 'txtColor',
				name: 'txtColor',
				xtype: 'textfield'
			}]
		},{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '95%',
				emptyText: 'Select a Warehouse',
				fieldLabel: 'Warehouse',
				id: 'cboWH',
				labelAlign: 'top',
				labelSeparator: '',
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
							winCari8.show();
							winCari8.center();
						}
					}
				}
			},{
				anchor: '95%',
				emptyText: 'Enter a Warehouse Name',
				hidden: true,
				id: 'txtWH',
				name: 'txtWH',
				xtype: 'textfield'
			}]
		},{
			xtype: 'buttongroup',
			columns: 1,
			defaults: {
				scale: 'small'
			},
			items: [{
				iconCls: 'icon-add',
				text: 'Add',
				handler: function() {
					var recordgrid = gridProd.getSelectionModel().getSelection()[0];
					var xprod = Ext.getCmp('txtProdAktif').getValue();
					if (recordgrid === undefined || xprod.trim() === '') {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.Msg.WARNING,
							msg: 'Please select a product from the list!',
							title: 'IDS'
						});
						return;
					}
					
					xprod = recordgrid.get('fs_kd_product');
					var xseqno = recordgrid.get('fs_seqno');
					
					var xtotal = grupGridReg.getCount();
					var xrangka = Ext.getCmp('txtRangka').getValue();
					var xmesin = Ext.getCmp('txtMesin').getValue();
					var xdata = Ext.create('DataGridReg', {
						fs_rangka: Ext.getCmp('txtRangka').getValue(),
						fs_mesin: Ext.getCmp('txtMesin').getValue(),
						fs_cc: Ext.getCmp('txtCC').getValue(),
						fs_thn: Ext.getCmp('txtThn').getValue(),
						fs_kd_color: Ext.getCmp('cboColor').getValue(),
						fs_nm_color: Ext.getCmp('txtColor').getValue(),
						fs_kd_wh: Ext.getCmp('cboWH').getValue(),
						fs_nm_wh: Ext.getCmp('txtWH').getValue(),
						fs_seqno: xseqno,
						fs_kd_product: xprod
					});
					
					var store = gridReg.getStore();
					var xlanjut = true;
					store.each(function(record, idx) {
						var xtext = record.get('fs_rangka').trim();
						var xtext2 = record.get('fs_mesin').trim();
						
						if (xtext == xrangka) {
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.OK,
								closable: false,
								icon: Ext.Msg.WARNING,
								msg: 'Chassis already exists, add chassis cancel!!',
								title: 'IDS'
							});
							xlanjut = false;
							return;
						}
						
						if (xtext2 == xmesin) {
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.OK,
								closable: false,
								icon: Ext.Msg.WARNING,
								msg: 'Machine already exists, add machine cancel!!',
								title: 'IDS'
							});
							xlanjut = false;
							return;
						}
					});
					if (xlanjut === false) {
						return;
					}
					
					grupGridReg.clearFilter();
					store = gridReg.getStore();
					xlanjut = true;
					store.each(function(record, idx) {
						var xtext = record.get('fs_rangka').trim();
						var xtext2 = record.get('fs_mesin').trim();
						var xtext3 = record.get('fs_seqno').trim();
						var xtext4 = record.get('fs_kd_product').trim();
						var xmsg = '';
						
						if (xtext == xrangka && xtext3 != xseqno) {
							xmsg = 'Chassis already exists in another product "'.concat(xtext4,'", add chassis cancel!!');
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.OK,
								closable: false,
								icon: Ext.Msg.WARNING,
								msg: xmsg,
								title: 'IDS'
							});
							xlanjut = false;
							return;
						}
						
						if (xtext2 == xmesin && xtext3 != xseqno) {
							xmsg = 'Machine already exists in another product "'.concat(xtext4,'", add machine cancel!!');
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.OK,
								closable: false,
								icon: Ext.Msg.WARNING,
								msg: xmsg,
								title: 'IDS'
							});
							xlanjut = false;
							return;
						}
					});
					if (xlanjut === false) {
						return;
					}
					
					var xTotal = grupGridReg.getCount();
					if (xTotal > 0) {
						grupGridReg.filterBy(function(record) {
							if (record.get('fs_kd_product').trim() == xprod.trim()) {
								return true;
							}
							else {
								return false;
							}
						});
					}
					gridReg.getView().refresh();
					
					xrangka = Ext.getCmp('txtRangka').getValue();
					if (xrangka.trim() === '') {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.Msg.WARNING,
							msg: 'Chassis is empty, please fill in advance!',
							title: 'IDS'
						});
						return;
					}
					
					xmesin = Ext.getCmp('txtMesin').getValue();
					if (xmesin.trim() === '') {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.Msg.WARNING,
							msg: 'Machine is empty, please fill in advance!',
							title: 'IDS'
						});
						return;
					}
					
					var xcolor = Ext.getCmp('txtColor').getValue();
					if (xcolor.trim() === '') {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.Msg.WARNING,
							msg: 'Color is not in the list!',
							title: 'IDS'
						});
						return;
					}
					
					var xwh = Ext.getCmp('txtWH').getValue();
					if (xwh.trim() === '') {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.Msg.WARNING,
							msg: 'Warehouse is not in the list!',
							title: 'IDS'
						});
						return;
					}
					
					Ext.Ajax.on('beforerequest', fnMaskShow2);
					Ext.Ajax.on('requestcomplete', fnMaskHide2);
					Ext.Ajax.on('requestexception', fnMaskHide2);
					
					Ext.Ajax.request({
						method: 'POST',
						url: 'beli/cek_reg',
						params: {
							'fs_rangka': Ext.getCmp('txtRangka').getValue(),
							'fs_mesin': Ext.getCmp('txtMesin').getValue()
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
							else if (xtext.sukses === true) {
								
								grupGridReg.clearFilter();
								grupGridReg.insert(xtotal, xdata);
								grupGridReg.sort([ {
										property : 'fs_seqno',
										direction: 'ASC'
									},{
										property : 'fs_rangka',
										direction: 'ASC'
									},{
										property : 'fs_mesin',
										direction: 'ASC'
									}
								]);
								xTotal = grupGridReg.getCount();
								if (xTotal > 0) {
									grupGridReg.filterBy(function(record) {
										if (record.get('fs_kd_product').trim() == xprod.trim()) {
											return true;
										}
										else {
											return false;
										}
									});
								}
								gridReg.getView().refresh();
								
								var xQty = 0;
								store = gridReg.getStore();
								store.each(function(record, idx) {
									if (xprod.trim() == record.get('fs_kd_product').trim()) {
										xQty = xQty + 1;
									}
								});
								
								store = gridProd.getStore();
								store.each(function(record, idx) {
									if (xprod.trim() == record.get('fs_kd_product').trim()) {
										record.set('fn_qty',xQty);
									}
								});
								gridProd.getView().refresh();
								fnTotal();
								
								fnProduk();
								Ext.getCmp('txtThn').setValue(Ext.Date.format(new Date(), 'Y'));
								Ext.getCmp('cboColor').setValue('');
								Ext.getCmp('txtColor').setValue('');
							}
						},
						failure: function(response) {
							var xtext = Ext.decode(response.responseText);
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.OK,
								closable: false,
								icon: Ext.MessageBox.INFO,
								msg: 'Check chassis and machine Failed, Connection Failed!!',
								title: 'IDS'
							});
							fnMaskHide2();
						}
					});
				}
			},{
				iconCls: 'icon-delete',
				itemId: 'removeData',
				text: 'Delete',
				handler: function() {
					var sm = gridReg.getSelectionModel();
					cellEditingReg.cancelEdit();
					grupGridReg.remove(sm.getSelection());
					gridReg.getView().refresh();
					if (grupGridReg.getCount() > 0) {
						sm.select(0);
					}
					
					var xprod = Ext.getCmp('txtProdAktif').getValue();
					var xQty = 0;
					store = gridReg.getStore();
					store.each(function(record, idx) {
						if (xprod.trim() == record.get('fs_kd_product').trim()) {
							xQty = xQty + 1;
						}
					});
					
					store = gridProd.getStore();
					store.each(function(record, idx) {
						if (xprod.trim() == record.get('fs_kd_product').trim()) {
							record.set('fn_qty',xQty);
						}
					});
					gridProd.getView().refresh();
					fnTotal();
				},
				disabled: true
			}]
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
		// height: 450,
		layout: 'fit',
		resizable: false,
		title: 'Register',
		width: 900,
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
			var xprod = '';
			var xqty = '';
			var xrangka = "'";
			var lxrangka = 0;
			var xmesin = "'";
			var lxmesin = 0;
			
			var store = gridProd.getStore();
			store.each(function(record, idx) {
				xprod = xprod +'|'+ record.get('fs_kd_product');
				xqty = xqty +'|'+ record.get('fn_qty');
			});
			
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
				url: 'beli/ceksave',
				params: {
					'fs_refno': Ext.getCmp('cboRefno').getValue(),
					'fs_kd_product': xprod,
					'fn_qty': xqty,
					'fs_rangka': xrangka,
					'fs_mesin': xmesin,
					'fn_ppn': Ext.getCmp('txtPPNTotal').getValue()
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
		var xnmprod = '';
		var xqty = '';
		var xunit = '';
		var xharga = '';
		var xdiskon = '';
		var xkdlux = '';
		var xnmlux = '';
		var xluxpersen = '';
		var xlux = '';
		var xseqno = '';
		var xprodreg = '';
		var xrangka = '';
		var xmesin = '';
		var xcc = '';
		var xthn = '';
		var xwarna = '';
		var xkdwh = '';
		var xnmwh = '';
		var xhpp = '';
		var xseqnoreg = '';
		
		var store = gridProd.getStore();
		store.each(function(record, idx) {
			xprod = xprod +'|'+ record.get('fs_kd_product');
			xnmprod = xnmprod +'|'+ record.get('fs_nm_product');
			xqty = xqty +'|'+ record.get('fn_qty');
			xunit = xunit +'|'+ record.get('fs_kd_unit');
			xharga = xharga +'|'+ record.get('fn_harga');
			xdiskon = xdiskon +'|'+ record.get('fn_diskon');
			xkdlux = xkdlux +'|'+ record.get('fs_kd_lux');
			xnmlux = xnmlux +'|'+ record.get('fs_nm_lux');
			xluxpersen = xluxpersen +'|'+ record.get('fn_persen');
			xlux = xlux +'|'+ record.get('fn_lux');
			xseqno = xseqno +'|'+ record.get('fs_seqno');
		});
		
		store = gridReg.getStore();
		store.each(function(record, idx) {
			xprodreg = xprodreg +'|'+ record.get('fs_kd_product');
			xrangka = xrangka +'|'+ record.get('fs_rangka');
			xmesin = xmesin +'|'+ record.get('fs_mesin');
			xcc = xcc +'|'+ record.get('fs_cc');
			xthn = xthn +'|'+ record.get('fs_thn');
			xwarna = xwarna +'|'+ record.get('fs_kd_color');
			xkdwh = xkdwh +'|'+ record.get('fs_kd_wh');
			xnmwh = xnmwh +'|'+ record.get('fs_nm_wh');
			xhpp = xhpp +'|'+ record.get('fn_hpp');
			xseqnoreg = xseqnoreg +'|'+ record.get('fs_seqno');
		});
		
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'beli/save',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue(),
				'fd_refno': Ext.Date.format(Ext.getCmp('txtRefnodt').getValue(), 'Ymd'),
				'fs_docno': Ext.getCmp('txtDocno').getValue(),
				'fd_docno': Ext.Date.format(Ext.getCmp('txtDocnodt').getValue(), 'Ymd'),
				'fs_invno': Ext.getCmp('txtInvno').getValue(),
				'fd_invno': Ext.Date.format(Ext.getCmp('txtInvnodt').getValue(), 'Ymd'),
				'fs_kd_sup': Ext.getCmp('txtSuppCd').getValue(),
				'fs_count': Ext.getCmp('txtCount').getValue(),
				'fs_kd_term': Ext.getCmp('cboTerm').getValue(),
				'fs_kd_belimtd': Ext.getCmp('cboBM').getValue(),
				'fs_nm_belimtd': Ext.getCmp('cboBM').getRawValue(),
				'fn_sumtotal_harga': Ext.getCmp('txtSumTotalHarga').getValue(),
				'fn_sumtotal_disc': Ext.getCmp('txtSumTotalDisc').getValue(),
				'fn_sumdisc': Ext.getCmp('txtSumDisc').getValue(),
				'fn_total': Ext.getCmp('txtSumTotal').getValue(),
				'fn_ppn': Ext.getCmp('txtPPNTotal').getValue(),
				'fs_kd_tax': Ext.getCmp('cboTax').getValue(),
				'fs_nm_tax': Ext.getCmp('txtTax').getValue(),
				'fn_taxpersen': Ext.getCmp('txtTaxPersen').getValue(),
				'fn_taxtotal': Ext.getCmp('txtTaxTotal').getValue(),
				'fn_gtotal': Ext.getCmp('txtGTotal').getValue(),
				'fs_kd_product': xprod,
				'fs_nm_product': xnmprod,
				'fn_qty': xqty,
				'fs_kd_unit': xunit,
				'fn_harga': xharga,
				'fn_disc': xdiskon,
				'fs_kd_lux': xkdlux,
				'fs_nm_lux': xnmlux,
				'fn_luxpersen': xluxpersen,
				'fn_lux': xlux,
				'fs_seqno': xseqno,
				'fs_kd_prodreg': xprodreg,
				'fs_rangka': xrangka,
				'fs_mesin': xmesin,
				'fs_cc': xcc,
				'fs_thn': xthn,
				'fs_kd_color': xwarna,
				'fs_kd_wh': xkdwh,
				'fs_nm_wh': xnmwh,
				'fn_hpp': xhpp,
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
			url: 'beli/cekremove',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue(),
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
			url: 'beli/remove',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue()
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
			url: 'beli/print_beli',
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
					
					vMask.show();
					var winpdf = Ext.create('Ext.window.Window', {
						closable: false,
						draggable: false,
						height: 500,
						layout: 'fit',
						title: 'Purchase',
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
		oTax();
		Ext.getCmp('cboRefno').setValue('');
		Ext.getCmp('txtRefnodt').setValue(new Date());
		Ext.getCmp('txtDocno').setValue('');
		Ext.getCmp('txtDocnodt').setValue(new Date());
		Ext.getCmp('txtInvno').setValue('');
		Ext.getCmp('txtInvnodt').setValue(new Date());
		Ext.getCmp('cboSupp').setValue('');
		Ext.getCmp('txtSupp').setValue('');
		Ext.getCmp('txtSuppCd').setValue('');
		Ext.getCmp('txtCount').setValue('');
		Ext.getCmp('cboTerm').setValue('');
		Ext.getCmp('txtTerm').setValue('');
		Ext.getCmp('cboBM').setValue('00');
		Ext.getCmp('cboProd').setValue('');
		Ext.getCmp('txtProd').setValue('');
		Ext.getCmp('cboUnit').setValue('UNIT');
		Ext.getCmp('txtHarga').setValue('0');
		Ext.getCmp('txtDisc').setValue('0');
		Ext.getCmp('txtUrutProd').setValue('0');
		Ext.getCmp('cboLux').setValue('');
		Ext.getCmp('txtLux').setValue('');
		Ext.getCmp('txtLuxPersen').setValue('0');
		Ext.getCmp('txtLuxKali').setValue('0');
		Ext.getCmp('txtLuxTotal').setValue('0');
		Ext.getCmp('txtSumTotalHarga').setValue('0');
		Ext.getCmp('txtSumTotalDisc').setValue('0');
		Ext.getCmp('txtSumTotalLux').setValue('0');
		Ext.getCmp('txtSumDisc').setValue('0');
		Ext.getCmp('txtSumTotal').setValue('0');
		Ext.getCmp('txtPPNPersen').setValue('10');
		Ext.getCmp('txtPPNKali').setValue('0');
		Ext.getCmp('txtPPNTotal').setValue('0');
		Ext.getCmp('cboTax').setValue('');
		Ext.getCmp('txtTax').setValue('');
		Ext.getCmp('txtTaxPersen').setValue('0');
		Ext.getCmp('txtTaxKali').setValue('0');
		Ext.getCmp('txtTaxTotal').setValue('0');
		Ext.getCmp('txtTaxEx').setValue(votax);
		Ext.getCmp('txtGTotal').setValue('0');
		Ext.getCmp('txtRangka').setValue('');
		Ext.getCmp('txtMesin').setValue('');
		Ext.getCmp('txtCC').setValue('');
		Ext.getCmp('txtThn').setValue(Ext.Date.format(new Date(), 'Y'));
		Ext.getCmp('cboColor').setValue('');
		Ext.getCmp('txtColor').setValue('');
		Ext.getCmp('cboWH').setValue('');
		Ext.getCmp('txtWH').setValue('');
		
		grupGridProd.load();
		grupGridReg.removeAll();
		gridReg.getView().refresh();
	}

	var frmBeli = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Purchase Form',
		width: 950,
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
						anchor: '95%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 2.1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cboRefno,
								txtDocno,
								txtInvno
							]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtRefnodt,
								txtDocnodt,
								txtInvnodt
							]
						}]
					}]
				},{
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
								cboSupp,
								txtSuppCd,
								txtCount
							]
						},{
							flex: 1.4,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtSupp
							]
						}]
					},{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cboTerm
							]
						},{
							flex: 1.4,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtTerm
							]
						}]
					},
						cboBM
					]
				}]
			}]
		},
			gridProd, {xtype: 'splitter'},
		{
			style: 'padding: 5px;',
			xtype: 'fieldset',
			items: [{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1.5,
					layout: 'anchor',
					xtype: 'container'
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtSumTotalHarga
					]
				},{
					flex: 0.75,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtSumTotalDisc
					]
				},{
					flex: 0.75,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtSumTotalLux
					]
				},{
					flex: 0.75,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtSumDisc
					]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtSumTotal
					]
				}]
			},{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 2.65,
					layout: 'anchor',
					xtype: 'container'
				},{
					flex: 0.2,
					layout: 'anchor',
					xtype: 'container',
					items: [
						lPPN
					]
				},{
					flex: 0.5,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtPPNPersen
					]
				},{
					flex: 0.23,
					layout: 'anchor',
					xtype: 'container',
					items: [
						lPersen2
					]
				},{
					flex: 0.1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						lX2
					]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtPPNKali
					]
				},{
					flex: 0.1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						lhsl2
					]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 5,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtPPNTotal
							]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cmdRefresh
							]
						}]
					}]
				}]
			},{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1.5,
					layout: 'anchor',
					xtype: 'container'
				},{
					flex: 0.6,
					layout: 'anchor',
					xtype: 'container',
					items: [
						cboTax
					]
				},{
					flex: 0.75,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtTax
					]
				},{
					flex: 0.5,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtTaxPersen
					]
				},{
					flex: 0.23,
					layout: 'anchor',
					xtype: 'container',
					items: [
						lPersen3
					]
				},{
					flex: 0.1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						lX3
					]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtTaxKali
					]
				},{
					flex: 0.1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						lhsl3
					]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtTaxTotal,
						txtTaxEx
					]
				}]
			},{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 2.93,
					layout: 'anchor',
					xtype: 'container'
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtGTotal
					]
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
		},{
			text: 'Remove',
			handler: fnCekRemove
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmBeli
	});
	
	function fnMaskShow() {
		frmBeli.mask('Please wait...');
	}

	function fnMaskHide() {
		frmBeli.unmask();
	}

	var vMask2 = new Ext.LoadMask({
		msg: 'Please wait...',
		target: winReg
	});
	
	function fnMaskShow2() {
		winReg.mask('Please wait...');
	}

	function fnMaskHide2() {
		winReg.unmask();
	}
	
	frmBeli.render(Ext.getBody());
	Ext.get('loading').destroy();
});
