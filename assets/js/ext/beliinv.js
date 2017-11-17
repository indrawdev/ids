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
			html: 'Double click on the Price, Disc to edit and Qty to show detail',
			target: view.el,
			trackMouse: true
		});
	}

	function fnTotal() {
		var xTotalHarga = 0;
		var xTotalDisc = 0;
		var xTotalPPN = 0;
		
		var store = gridProd.getStore();
		store.each(function(record, idx) {
			xTotalHarga = xTotalHarga + (record.get('fn_qty') * record.get('fn_harga'));
			xTotalDisc = xTotalDisc + (record.get('fn_qty') * record.get('fn_diskon'));
		});
		
		var xTotal = xTotalHarga - xTotalDisc;
		
		Ext.getCmp('txtSumTotalHarga').setValue(xTotalHarga);
		Ext.getCmp('txtSumTotalDisc').setValue(xTotalDisc);
		Ext.getCmp('txtSumTotal').setValue(xTotal);
		
		Ext.getCmp('txtPPNKali').setValue(xTotal);
		Ext.getCmp('txtTaxKali').setValue(xTotal);
		
		if (Ext.getCmp('txtPPNTotal').getValue() == 10 / 100 * xTotal) {
			xTotalPPN = 10 / 100 * xTotal;
		}
		else {
			xTotalPPN = Ext.getCmp('txtPPNTotal').getValue();
		}
		Ext.getCmp('txtPPNTotal').setValue(xTotalPPN);
		
		var xTotalTax = Ext.getCmp('txtTaxPersen').getValue() / 100 * xTotal;
		Ext.getCmp('txtTaxTotal').setValue(xTotalTax);
		
		// var xGTotal = Math.round(xTotal + xTotalPPN + xTotalTax);
		var xGTotal = xTotal + xTotalPPN + xTotalTax;
		Ext.getCmp('txtGTotal').setValue(xGTotal);
	}

	var grupRefno = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_refno','fd_refno','fs_invno','fd_invno',
			'fs_kd_suppcount','fs_nm_supplier','fs_kd_supp','fs_kd_count',
			'fs_kd_term','fs_nm_term',
			'fn_total_price','fn_total_disc','fn_ppn',
			'fs_kd_otax','fs_nm_otax','fn_otax_persen'
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
			url: 'beliinv/refno'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboRefno').getValue(),
					'fs_invno': Ext.getCmp('cboRefno').getValue(),
					'fs_cari': Ext.getCmp('txtCari').getValue()
				});
			}
		}
	});

	/*
	var winGrid = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 750,
		store: grupRefno,
		sortableColumns: false,
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
			{text: "Inv. No", dataIndex: 'fs_invno', menuDisabled: true, width: 150},
			{text: "Inv. Date", dataIndex: 'fd_invno', menuDisabled: true, width: 80},
			{text: "Supp Cd", dataIndex: 'fs_kd_suppcount', menuDisabled: true, hidden: true},
			{text: "Supplier", dataIndex: 'fs_nm_supplier', menuDisabled: true, width: 220},
			{text: "Supp Cd", dataIndex: 'fs_kd_supp', menuDisabled: true, hidden: true},
			{text: "Supp Count", dataIndex: 'fs_kd_count', menuDisabled: true, hidden: true},
			{text: "Term Cd", dataIndex: 'fs_kd_term', menuDisabled: true, hidden: true},
			{text: "Term", dataIndex: 'fs_nm_term', menuDisabled: true, hidden: true},
			{text: "Total Harga", dataIndex: 'fn_total_price', menuDisabled: true, hidden: true},
			{text: "Total Diskon", dataIndex: 'fn_total_disc', menuDisabled: true, hidden: true},
			{text: "Total PPN", dataIndex: 'fn_ppn', menuDisabled: true, hidden: true},
			{text: "Tax Cd", dataIndex: 'fs_kd_otax', menuDisabled: true, hidden: true},
			{text: "Tax", dataIndex: 'fs_nm_otax', menuDisabled: true, hidden: true},
			{text: "Tax Persen", dataIndex: 'fn_otax_persen', menuDisabled: true, hidden: true}
		],
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
				Ext.getCmp('txtCount').setValue(record.get('fs_kd_count'));
				Ext.getCmp('cboTerm').setValue(record.get('fs_kd_term'));
				Ext.getCmp('txtTerm').setValue(record.get('fs_nm_term'));
				Ext.getCmp('txtSumTotalHarga').setValue(record.get('fn_total_price'));
				Ext.getCmp('txtSumTotalDisc').setValue(record.get('fn_total_disc'));
				Ext.getCmp('txtPPNTotal').setValue(record.get('fn_ppn'));
				Ext.getCmp('cboTax').setValue(record.get('fs_kd_otax'));
				Ext.getCmp('txtTax').setValue(record.get('fs_nm_otax'));
				Ext.getCmp('txtTaxPersen').setValue(record.get('fn_otax_persen'));
				
				grupGridDO.load();
				grupGridProd.load();
				grupGridReg.load();
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
	*/

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
			{text: "Inv. No", dataIndex: 'fs_invno', menuDisabled: true, width: 150},
			{text: "Inv. Date", dataIndex: 'fd_invno', menuDisabled: true, width: 80},
			{text: "Supp Cd", dataIndex: 'fs_kd_suppcount', menuDisabled: true, hidden: true},
			{text: "Supplier", dataIndex: 'fs_nm_supplier', menuDisabled: true, width: 220},
			{text: "Supp Cd", dataIndex: 'fs_kd_supp', menuDisabled: true, hidden: true},
			{text: "Supp Count", dataIndex: 'fs_kd_count', menuDisabled: true, hidden: true},
			{text: "Term Cd", dataIndex: 'fs_kd_term', menuDisabled: true, hidden: true},
			{text: "Term", dataIndex: 'fs_nm_term', menuDisabled: true, hidden: true},
			{text: "Total Harga", dataIndex: 'fn_total_price', menuDisabled: true, hidden: true},
			{text: "Total Diskon", dataIndex: 'fn_total_disc', menuDisabled: true, hidden: true},
			{text: "Total PPN", dataIndex: 'fn_ppn', menuDisabled: true, hidden: true},
			{text: "Tax Cd", dataIndex: 'fs_kd_otax', menuDisabled: true, hidden: true},
			{text: "Tax", dataIndex: 'fs_nm_otax', menuDisabled: true, hidden: true},
			{text: "Tax Persen", dataIndex: 'fn_otax_persen', menuDisabled: true, hidden: true}			
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Ref. No / Inv. No /  Supplier',
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
				Ext.getCmp('txtCount').setValue(record.get('fs_kd_count'));
				Ext.getCmp('cboTerm').setValue(record.get('fs_kd_term'));
				Ext.getCmp('txtTerm').setValue(record.get('fs_nm_term'));
				Ext.getCmp('txtSumTotalHarga').setValue(record.get('fn_total_price'));
				Ext.getCmp('txtSumTotalDisc').setValue(record.get('fn_total_disc'));
				Ext.getCmp('txtPPNTotal').setValue(record.get('fn_ppn'));
				Ext.getCmp('cboTax').setValue(record.get('fs_kd_otax'));
				Ext.getCmp('txtTax').setValue(record.get('fs_nm_otax'));
				Ext.getCmp('txtTaxPersen').setValue(record.get('fn_otax_persen'));
				
				grupGridDO.load();
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

	var txtDocno = {
		anchor: '95%',
		emptyText: 'Enter a Document Number',
		fieldLabel: 'Doc. No',
		id: 'txtDocno',
		name: 'txtDocno',
		xtype: 'textfield'
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

	var txtInvno = {
		anchor: '95%',
		emptyText: 'Enter an Invoice Number',
		fieldLabel: 'Inv. No',
		id: 'txtInvno',
		name: 'txtInvno',
		xtype: 'textfield'
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

	var grupDO = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_do','fs_docno'
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
			url: 'belido/refnodo'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboDO').getValue(),
					'fs_docno': Ext.getCmp('cboDO').getValue(),
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
		store: grupDO,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "DO No", dataIndex: 'fs_refno', menuDisabled: true, width: 200},
			{text: "Doc.No", dataIndex: 'fs_docno', menuDisabled: true, width: 280}			
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'DO No / Doc.No',
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
					grupDO.load();
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
			store: grupDO,
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
				Ext.getCmp('cboDO').setValue(record.get('fs_refno'));
				Ext.getCmp('txtDO').setValue(record.get('fs_docno'));
				
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
				grupDO.load();
				vMask.show();
			}
		}
	});

	var cellEditingDO = Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToEdit: 2
	});

	Ext.define('DataGridDO', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_do', type: 'string'},
			{name: 'fs_docno', type: 'string'}
		]
	});

	var grupGridDO = Ext.create('Ext.data.Store', {
		autoLoad: false,
		model: 'DataGridDO',
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'beliinv/grid_do'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboRefno').getValue()
				});
			},
			load: function() {
				grupGridDO.sort([ {
						property : 'fs_kd_do',
						direction: 'ASC'
					}
				]);
				gridDO.getView().refresh();
			}
		}
	});

	var gridDO = Ext.create('Ext.grid.Panel', {
		anchor: '99%',
		defaultType: 'textfield',
		height: 135,
		sortableColumns: false,
		store: grupGridDO,
		columns: [{
			xtype: 'rownumberer',
			width: 25
		},{
			dataIndex: 'fs_kd_do',
			text: 'DO',
			flex: 1,
			menuDisabled: true
		},{
			dataIndex: 'fs_docno',
			text: 'Doc.No',
			flex: 0.75,
			menuDisabled: true
		}],
		listeners: {
			selectionchange: function(view, records) {
				gridDO.down('#removeData').setDisabled(!records.length);
			}
		},
		plugins: [
			cellEditingDO
		],
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			listeners: {
				//render: gridTooltipProd
			},
			markDirty: false,
			stripeRows: true
		},
		tbar: [{
			flex: 1.5,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '97%',
				emptyText: 'Select a DO No',
				fieldLabel: 'DO',
				id: 'cboDO',
				labelWidth: 25,
				name: 'cboDO',
				xtype: 'textfield',
				listeners: {
					change: function(field, newValue) {
						Ext.getCmp('txtDO').setValue('');
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
				emptyText: 'Enter a Docno',
				hidden: true,
				id: 'txtDO',
				name: 'txtDO',
				xtype: 'textfield'
			}]
		},{
			xtype: 'buttongroup',
			defaults: {
				scale: 'small'
			},
			items: [{
				iconCls: 'icon-add',
				text: 'Add',
				handler: function() {
					var xtotal = grupGridDO.getCount();
					var xDO = Ext.getCmp('cboDO').getValue();
					var xdata = Ext.create('DataGridDO', {
						fs_kd_do: Ext.getCmp('cboDO').getValue(),
						fs_docno: Ext.getCmp('txtDO').getValue()
					});
					
					var store = gridDO.getStore();
					var xlanjut = true;
					store.each(function(record, idx) {
						var xtext = record.get('fs_kd_do').trim();
						
						if (xtext == xDO) {
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.OK,
								closable: false,
								icon: Ext.Msg.WARNING,
								msg: 'DO already exists, add DO cancel!!',
								title: 'IDS'
							});
							xlanjut = false;
						}
						
					});
					if (xlanjut === false) {
						return;
					}
					
					xDO = Ext.getCmp('cboDO').getValue();
					if (xDO.trim() === '') {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.Msg.WARNING,
							msg: 'DO is not in the list!',
							title: 'IDS'
						});
						return;
					}
					
					grupGridDO.insert(xtotal, xdata);
					grupGridDO.sort([ {
							property : 'fs_kd_do',
							direction: 'ASC'
						}
					]);
					
					xtotal = grupGridDO.getCount() - 1;
					gridDO.getSelectionModel().select(xtotal);
					
					grupGridProd2.load();
					grupGridReg2.load();
					
					Ext.getCmp('cboDO').setValue('');
					Ext.getCmp('txtDO').setValue('');
				}
			},{
				iconCls: 'icon-delete',
				itemId: 'removeData',
				text: 'Delete',
				handler: function() {
					var record = gridDO.getSelectionModel().getSelection()[0];
					var xtotal = 0;
					var xstore;
					var xxDO = '';
					
					var xDO = record.get('fs_kd_do');
					var arr_do = Array();
					
					arr_do = Array();
					xstore = gridReg.getStore();
					xstore.each(function(record, idx) {
						arr_do.push(record.get('fs_kd_do').trim());
					});
					
					xtotal = grupGridReg.getCount()-1;
					xxDO = '';
					for (i=xtotal;i>=0;i--) {
						xxDO = arr_do[i];
						
						if (xDO.trim() == xxDO.trim()) {
							grupGridReg.removeAt(i);
						}
					}
					gridReg.getView().refresh();
					
					arr_do = Array();
					xstore = gridProd.getStore();
					xstore.each(function(record, idx) {
						arr_do.push(record.get('fs_kd_do').trim());
					});
					
					xtotal = grupGridProd.getCount()-1;
					xxDO = '';
					for (i=xtotal;i>=0;i--) {
						xxDO = arr_do[i];
						
						if (xDO.trim() == xxDO.trim()) {
							grupGridProd.removeAt(i);
						}
					}
					gridProd.getView().refresh();
					
					var sm = gridDO.getSelectionModel();
					cellEditingDO.cancelEdit();
					grupGridDO.remove(sm.getSelection());
					gridDO.getView().refresh();
					if (grupGridDO.getCount() > 0) {
						sm.select(0);
					}
				},
				disabled: true
			}]
		}]
	});

	var cellEditingProd = Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToEdit: 2,
		listeners: {
			edit: function(editor, e) {
				fnTotal();
			}
		}
	});

	Ext.define('DataGridProd', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_do', type: 'string'},
			{name: 'fs_docno', type: 'string'},
			{name: 'fs_kd_product', type: 'string'},
			{name: 'fs_nm_product', type: 'string'},
			{name: 'fs_kd_warna', type: 'string'},
			{name: 'fs_nm_warna', type: 'string'},
			{name: 'fn_qty', type: 'float'},
			{name: 'fs_kd_unit', type: 'string'},
			{name: 'fn_harga', type: 'float'},
			{name: 'fn_diskon', type: 'float'}
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
			url: 'beliinv/grid_prod'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboRefno').getValue()
				});
			},
			load: function() {
				grupGridProd.sort([ {
						property : 'fs_kd_do',
						direction: 'ASC'
					},{
						property : 'fs_kd_product',
						direction: 'ASC'
					},{
						property : 'fs_nm_product',
						direction: 'ASC'
					},{
						property : 'fs_nm_warna',
						direction: 'ASC'
					}
				]);
				gridProd.getView().refresh();
				fnTotal();
			}
		}
	});

	var gridProd = Ext.create('Ext.grid.Panel', {
		anchor: '100%',
		defaultType: 'textfield',
		height: 195,
		sortableColumns: false,
		store: grupGridProd,
		columns: [{
			xtype: 'rownumberer',
			width: 30
		},{
			dataIndex: 'fs_kd_do',
			menuDisabled: true,
			text: 'DO',
			width: 140
		},{
			dataIndex: 'fs_docno',
			hidden: true,
			menuDisabled: true,
			text: 'Doc.No'
		},{
			dataIndex: 'fs_kd_product',
			menuDisabled: true,
			text: 'Code',
			width: 100
		},{
			dataIndex: 'fs_nm_product',
			text: 'Product Name',
			menuDisabled: true,
			width: 200
		},{
			dataIndex: 'fs_kd_warna',
			hidden: true,
			text: 'Color Code',
			menuDisabled: true
		},{
			dataIndex: 'fs_nm_warna',
			text: 'Color',
			menuDisabled: true,
			width: 150
		},{
			align: 'right',
			dataIndex: 'fn_qty',
			menuDisabled: true,
			text: 'Qty',
			width: 45,
			editor: {
				editable: false,
				xtype: 'textfield',
				triggers: {
					cari: {
						cls: 'x-form-search-trigger',
						handler: function() {
							var recordgrid = gridProd.getSelectionModel().getSelection()[0];
							var xKdDO = recordgrid.get('fs_kd_do');
							var xKdProd = recordgrid.get('fs_kd_product');
							var xNmProd = recordgrid.get('fs_nm_product');
							var xKdWarna = recordgrid.get('fs_kd_warna');
							var xNmWarna = recordgrid.get('fs_nm_warna');
							
							var xtext = xKdProd + ' - ' + xNmProd + ' - ' + xNmWarna;
							Ext.getCmp('txtProdAktif').setValue(xKdProd);
							Ext.getCmp('txtProdAktif2').setValue(xtext);
							
							grupGridReg.clearFilter();
							var xTotal = grupGridReg.getCount();
							if (xTotal > 0) {
								grupGridReg.filterBy(function(record) {
									if (record.get('fs_kd_do').trim() == xKdDO.trim() &&
										record.get('fs_kd_product').trim() == xKdProd.trim() &&
										record.get('fs_kd_warna').trim() == xKdWarna.trim()) {
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
			menuDisabled: true,
			text: 'Unit',
			width: 40
		},{
			align: 'right',
			dataIndex: 'fn_harga',
			format: '0,000.00',
			menuDisabled: true,
			text: 'Price',
			width: 120,
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
			format: '0,000.00',
			menuDisabled: true,
			text: 'Disc',
			width: 95,
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
		}],
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
				value: '<*Double click on the Price, Disc to edit and Qty to show detail>',
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
		}]
	});

	Ext.define('DataGridProd2', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_do', type: 'string'},
			{name: 'fs_docno', type: 'string'},
			{name: 'fs_kd_product', type: 'string'},
			{name: 'fs_nm_product', type: 'string'},
			{name: 'fs_kd_warna', type: 'string'},
			{name: 'fs_nm_warna', type: 'string'},
			{name: 'fn_qty', type: 'float'},
			{name: 'fs_kd_unit', type: 'string'}
		]
	});

	var grupGridProd2 = Ext.create('Ext.data.Store', {
		autoLoad: false,
		model: 'DataGridProd2',
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'beliinv/grid_prod2'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboDO').getValue()
				});
			},
			load: function() {
				var xArrDO = Array();
				var xArrDocno = Array();
				var xArrKdProd = Array();
				var xArrNmProd = Array();
				var xArrKdWarna = Array();
				var xArrNmWarna = Array();
				var xArrQty = Array();
				var xArrUnit = Array();
				
				var xStore = gridProd2.getStore();
				xStore.each(function(record) {
					xArrDO.push(record.get('fs_kd_do').trim());
					xArrDocno.push(record.get('fs_docno').trim());
					xArrKdProd.push(record.get('fs_kd_product').trim());
					xArrNmProd.push(record.get('fs_nm_product').trim());
					xArrKdWarna.push(record.get('fs_kd_warna').trim());
					xArrNmWarna.push(record.get('fs_nm_warna'));
					xArrQty.push(record.get('fn_qty'));
					xArrUnit.push(record.get('fs_kd_unit'));
				});
				
				var xTotal = grupGridProd2.getCount();
				if (xTotal > 0) {
					for (i=0;i<xTotal;i++) {
						
						xData = Ext.create('DataGridProd', {
							fs_kd_do: xArrDO[i],
							fs_docno: xArrDocno[i],
							fs_kd_product: xArrKdProd[i],
							fs_nm_product: xArrNmProd[i],
							fs_kd_warna: xArrKdWarna[i],
							fs_nm_warna: xArrNmWarna[i],
							fn_qty: xArrQty[i],
							fs_kd_unit: xArrUnit[i]
						});
						
						grupGridProd.insert(xTotal, xData);
					}
					grupGridProd.sort([ {
							property : 'fs_kd_do',
							direction: 'ASC'
						},{
							property : 'fs_kd_product',
							direction: 'ASC'
						},{
							property : 'fs_nm_product',
							direction: 'ASC'
						},{
							property : 'fs_nm_warna',
							direction: 'ASC'
						}
					]);
					gridProd.getView().refresh();
					
					grupGridProd2.removeAll();
				}
			}
		}
	});

	var gridProd2 = Ext.create('Ext.grid.Panel', {
		anchor: '100%',
		height: 225,
		hidden: true,
		sortableColumns: false,
		store: grupGridProd2,
		columns: [{
			xtype: 'rownumberer',
			width: 25
		},{
			dataIndex: 'fs_kd_do',
			menuDisabled: true,
			text: 'DO',
			width: 100
		},{
			dataIndex: 'fs_docno',
			menuDisabled: true,
			text: 'Doc.No',
			width: 100
		},{
			dataIndex: 'fs_kd_product',
			menuDisabled: true,
			text: 'Code',
			width: 100
		},{
			dataIndex: 'fs_nm_product',
			text: 'Product Name',
			menuDisabled: true,
			width: 200
		},{
			dataIndex: 'fs_kd_warna',
			hidden: true,
			text: 'Color Code',
			menuDisabled: true
		},{
			dataIndex: 'fs_nm_warna',
			text: 'Color',
			menuDisabled: true,
			width: 150
		},{
			align: 'right',
			dataIndex: 'fn_qty',
			menuDisabled: true,
			text: 'Qty',
			width: 40
		},{
			dataIndex: 'fs_kd_unit',
			menuDisabled: true,
			text: 'Unit',
			width: 60
		}],
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false,
			stripeRows: true
		}
	});

	Ext.define('DataGridReg', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_do', type: 'string'},
			{name: 'fs_kd_product', type: 'string'},
			{name: 'fs_kd_warna', type: 'string'},
			{name: 'fs_rangka', type: 'string'},
			{name: 'fs_mesin', type: 'string'},
			{name: 'fs_cc', type: 'float'},
			{name: 'fs_thn', type: 'float'},
			{name: 'fs_nm_wh', type: 'string'}
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
			url: 'beliinv/grid_reg'
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
		height: 350,
		sortableColumns: false,
		store: grupGridReg,
		columns: [{
			xtype: 'rownumberer',
			width: 30
		},{
			text: 'DO',
			dataIndex: 'fs_kd_do',
			hidden: true,
			menuDisabled: true
		},{
			text: 'Product',
			dataIndex: 'fs_kd_product',
			hidden: true,
			menuDisabled: true
		},{
			text: 'Color',
			dataIndex: 'fs_kd_warna',
			hidden: true,
			menuDisabled: true
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
			text: 'WH Name',
			dataIndex: 'fs_nm_wh',
			menuDisabled: true,
			width: 170
		}],
		bbar: [{
			fieldLabel: '',
			id: 'txtProdAktif2',
			labelAlign: 'left',
			labelSeparator: '',
			labelWidth: 90,
			name: 'txtProdAktif2',
			value: '',
			xtype: 'displayfield'
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
		width: 725,
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

	Ext.define('DataGridReg2', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_do', type: 'string'},
			{name: 'fs_kd_product', type: 'string'},
			{name: 'fs_kd_warna', type: 'string'},
			{name: 'fs_rangka', type: 'string'},
			{name: 'fs_mesin', type: 'string'},
			{name: 'fs_cc', type: 'float'},
			{name: 'fs_thn', type: 'float'},
			{name: 'fs_nm_wh', type: 'string'}
		]
	});

	var grupGridReg2 = Ext.create('Ext.data.Store', {
		autoLoad: false,
		model: 'DataGridReg2',
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'beliinv/grid_reg2'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboDO').getValue()
				});
			},
			load: function() {
				var xArrDO = Array();
				var xArrKdProd = Array();
				var xArrKdWarna = Array();
				var xArrRangka = Array();
				var xArrMesin = Array();
				var xArrCC = Array();
				var xArrThn = Array();
				var xArrNmWH = Array();
				
				var xStore = gridReg2.getStore();
				xStore.each(function(record) {
					xArrDO.push(record.get('fs_kd_do').trim());
					xArrKdProd.push(record.get('fs_kd_product').trim());
					xArrKdWarna.push(record.get('fs_kd_warna').trim());
					xArrRangka.push(record.get('fs_rangka').trim());
					xArrMesin.push(record.get('fs_mesin'));
					xArrCC.push(record.get('fs_cc'));
					xArrThn.push(record.get('fs_thn'));
					xArrNmWH.push(record.get('fs_nm_wh'));
				});
				
				var xTotal = grupGridReg2.getCount();
				if (xTotal > 0) {
					for (i=0;i<xTotal;i++) {
						
						xData = Ext.create('DataGridReg', {
							fs_kd_do: xArrDO[i],
							fs_kd_product: xArrKdProd[i],
							fs_kd_warna: xArrKdWarna[i],
							fs_rangka: xArrRangka[i],
							fs_mesin: xArrMesin[i],
							fs_cc: xArrCC[i],
							fs_thn: xArrThn[i],
							fs_nm_wh: xArrNmWH[i],
						});
						
						grupGridReg.insert(xTotal, xData);
					}
					grupGridReg.sort([ {
							property : 'fs_kd_do',
							direction: 'ASC'
						},{
							property : 'fs_kd_product',
							direction: 'ASC'
						},{
							property : 'fs_kd_warna',
							direction: 'ASC'
						}
					]);
					gridReg.getView().refresh();
					
					grupGridReg2.removeAll();
				}
			}
		}
	});

	var gridReg2 = Ext.create('Ext.grid.Panel', {
		height: 150,
		hidden: true,
		sortableColumns: false,
		store: grupGridReg2,
		columns: [{
			xtype: 'rownumberer',
			width: 30
		},{
			text: 'DO',
			dataIndex: 'fs_kd_do',
			menuDisabled: true,
			width: 100
		},{
			text: 'Product',
			dataIndex: 'fs_kd_product',
			menuDisabled: true,
			width: 100
		},{
			text: 'Color',
			dataIndex: 'fs_kd_warna',
			menuDisabled: true,
			width: 100
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
			align: 'right',
			text: 'Cylinder',
			dataIndex: 'fs_cc',
			menuDisabled: true,
			width: 60
		},{
			align: 'right',
			text: 'Year',
			dataIndex: 'fs_thn',
			menuDisabled: true,
			width: 50
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
		}],
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false,
			stripeRows: true
		}
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
		mouseWheelEnabled: false,
		name: 'txtSumTotalDisc',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0
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
					winCari5.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboTax').setValue(record.get('fs_kd_tax'));
				Ext.getCmp('txtTax').setValue(record.get('fs_nm_tax'));
				Ext.getCmp('txtTaxPersen').setValue(record.get('fn_persen'));
				
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
					winCari5.show();
					winCari5.center();
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
				url: 'beliinv/ceksave',
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
		var xdo = '';
		var xprod = '';
		var xnmprod = '';
		var xkdwarna = '';
		var xqty = '';
		var xunit = '';
		var xharga = '';
		var xdiskon = '';
		
		var store = gridProd.getStore();
		store.each(function(record, idx) {
			xdo = xdo +'|'+ record.get('fs_kd_do');
			xprod = xprod +'|'+ record.get('fs_kd_product');
			xnmprod = xnmprod +'|'+ record.get('fs_nm_product');
			xkdwarna = xkdwarna +'|'+ record.get('fs_kd_warna');
			xqty = xqty +'|'+ record.get('fn_qty');
			xunit = xunit +'|'+ record.get('fs_kd_unit');
			xharga = xharga +'|'+ record.get('fn_harga');
			xdiskon = xdiskon +'|'+ record.get('fn_diskon');
		});
		
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'beliinv/save',
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
				'fn_total': Ext.getCmp('txtSumTotal').getValue(),
				'fn_ppn': Ext.getCmp('txtPPNTotal').getValue(),
				'fs_kd_tax': Ext.getCmp('cboTax').getValue(),
				'fs_nm_tax': Ext.getCmp('txtTax').getValue(),
				'fn_taxpersen': Ext.getCmp('txtTaxPersen').getValue(),
				'fn_taxtotal': Ext.getCmp('txtTaxTotal').getValue(),
				'fn_gtotal': Ext.getCmp('txtGTotal').getValue(),
				'fs_kd_do': xdo,
				'fs_kd_product': xprod,
				'fs_nm_product': xnmprod,
				'fs_kd_warna': xkdwarna,
				'fn_qty': xqty,
				'fs_kd_unit': xunit,
				'fn_harga': xharga,
				'fn_disc': xdiskon
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
			url: 'beliinv/cekremove',
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
			url: 'beliinv/remove',
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
		Ext.getCmp('cboBM').setValue('00');
		Ext.getCmp('cboTerm').setValue('');
		Ext.getCmp('txtTerm').setValue('');
		Ext.getCmp('cboDO').setValue('');
		Ext.getCmp('txtDO').setValue('');
		Ext.getCmp('txtSumTotalHarga').setValue('0');
		Ext.getCmp('txtSumTotalDisc').setValue('0');
		Ext.getCmp('txtSumTotal').setValue('0');
		Ext.getCmp('txtPPNPersen').setValue('10');
		Ext.getCmp('txtPPNKali').setValue('0');
		Ext.getCmp('txtPPNTotal').setValue('0');
		Ext.getCmp('cboTax').setValue('');
		Ext.getCmp('txtTax').setValue('');
		Ext.getCmp('txtTaxPersen').setValue('0');
		Ext.getCmp('txtTaxKali').setValue('0');
		Ext.getCmp('txtTaxTotal').setValue('0');
		Ext.getCmp('txtGTotal').setValue('0');
		
		grupGridDO.removeAll();
		grupGridProd.load();
		grupGridProd2.removeAll();
		grupGridReg.removeAll();
		grupGridReg2.removeAll();
	}

	var frmBeliInv = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Invoice Form',
		width: 950,
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
					},{
						anchor: '95%',
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
							flex: 1.5,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtSupp
							]
						}]
					},{
						anchor: '60%',
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
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtTerm
							]
						}]
					},
						cboBM
					]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						gridDO
					]
				}]
			}]
		},
			gridProd,
			gridProd2,
			gridReg2,
			{xtype: 'splitter'},
		{
			style: 'padding: 5px;',
			xtype: 'fieldset',
			items: [{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1.4,
					layout: 'anchor',
					xtype: 'container'
				},{
					flex: 0.9,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtSumTotalHarga
					]
				},{
					flex: 0.8,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtSumTotalDisc
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
					anchor: '100%',
					layout: 'hbox',
					xtype: 'container',
					items: [{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: []
					},{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: []
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
						txtTaxTotal
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
		},/*{
			text: 'Print',
			handler: fnPrint
		},*/{
			text: 'Reset',
			handler: fnReset
		},{
			text: 'Remove',
			handler: fnCekRemove
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmBeliInv
	});
	
	function fnMaskShow() {
		frmBeliInv.mask('Please wait...');
	}

	function fnMaskHide() {
		frmBeliInv.unmask();
	}
	
	frmBeliInv.render(Ext.getBody());
	Ext.get('loading').destroy();
});
