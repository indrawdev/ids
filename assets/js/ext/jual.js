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

	function formatNumb(val) {
		var format = "0,000.00";
		return Ext.util.Format.number(val, format);
	}

	function gridTooltipSearch(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Double click on record to choose',
			target: view.el,
			trackMouse: true
		});
	}

	function fnCekSaveCust() {
		Ext.Ajax.on('beforerequest', fnMaskShow2);
		Ext.Ajax.on('requestcomplete', fnMaskHide2);
		Ext.Ajax.on('requestexception', fnMaskHide2);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'jual/cust_ceksave',
			params: {
				'fs_nm_cust': Ext.getCmp('txtInputCust').getValue(),
				'fs_kd_id': Ext.getCmp('txtInputID').getValue(),
				'fs_addr': Ext.getCmp('txtInputAddr').getValue(),
				'fs_tlp': Ext.getCmp('txtInputTlp').getValue()
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
						fnSaveCust();
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
									fnSaveCust();
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
				fnMaskHide2();
			}
		});
	}

	function fnSaveCust() {
		Ext.Ajax.on('beforerequest', fnMaskShow2);
		Ext.Ajax.on('requestcomplete', fnMaskHide2);
		Ext.Ajax.on('requestexception', fnMaskHide2);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'jual/cust_save',
			params: {
				'fs_nm_cust': Ext.getCmp('txtInputCust').getValue(),
				'fs_kd_id': Ext.getCmp('txtInputID').getValue(),
				'fs_addr': Ext.getCmp('txtInputAddr').getValue(),
				'fs_tlp': Ext.getCmp('txtInputTlp').getValue()
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
					Ext.getCmp('cboCust').setValue(xtext.kdcust.trim().concat(xtext.kdcount.trim()));
					Ext.getCmp('txtCustCd').setValue(xtext.kdcust.trim());
					Ext.getCmp('txtCount').setValue(xtext.kdcount.trim());
					Ext.getCmp('txtCust').setValue(xtext.nmcust.trim());
					Ext.getCmp('txtID').setValue(xtext.kdid.trim());
					
					Ext.getCmp('txtAddr').setValue(xtext.almt.trim());
					Ext.getCmp('txtTlp').setValue(xtext.tlp.trim());
					Ext.getCmp('txtInputCust').setValue('');
					Ext.getCmp('txtInputID').setValue('');
					Ext.getCmp('txtInputAddr').setValue('');
					
					Ext.getCmp('txtInputTlp').setValue('');
					vMask2.hide();
					vMask.hide();
					winCust.hide();
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
				fnMaskHide2();
			}
		});
	}

	var winCust = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: false,
		frame: false,
		height: 450,
		layout: 'fit',
		minHeight: 250,
		maxHeight: 250,
		minWidth: 500,
		maxWidth: 500,
		title: 'Customer',
		width: 500,
		items: [
			Ext.create('Ext.form.Panel', {
				bodyStyle: 'padding: 5px; background-color: '.concat(gBasePanel),
				fieldDefaults: {
					labelAlign: 'right',
					labelSeparator: '',
					labelWidth: 80,
					msgTarget: 'side'
				},
				items: [{
					afterLabelTextTpl: required,
					allowBlank: false,
					anchor: '100%',
					fieldLabel: 'Name',
					id: 'txtInputCust',
					name: 'txtInputCust',
					value: '',
					xtype: 'textfield'
				},{
					afterLabelTextTpl: required,
					allowBlank: false,
					anchor: '100%',
					fieldLabel: 'ID Card',
					id: 'txtInputID',
					name: 'txtInputID',
					maskRe: /[0-9]/,
					value: '',
					xtype: 'textfield'
				},{
					afterLabelTextTpl: required,
					allowBlank: false,
					anchor: '100%',
					fieldLabel: 'Address',
					grow: true,
					growMin: 70,
					growMax: 70,
					id: 'txtInputAddr',
					name: 'txtInputAddr',
					value: '',
					xtype: 'textareafield'
				},{
					afterLabelTextTpl: required,
					allowBlank: false,
					anchor: '100%',
					fieldLabel: 'Phone',
					id: 'txtInputTlp',
					name: 'txtInputTlp',
					value: '',
					xtype: 'textfield'
				}]
			})
		],
		buttons: [{
			text: 'Save',
			handler: fnCekSaveCust
		},{
			text: 'Reset',
			handler: function() {
				Ext.getCmp('txtInputCust').setValue('');
				Ext.getCmp('txtInputID').setValue('');
				Ext.getCmp('txtInputAddr').setValue('');
				Ext.getCmp('txtInputTlp').setValue('');
			}
		},{
			text: 'Cancel',
			handler: function() {
				vMask.hide();
				winCust.hide();
			}
		}]
	});

	var grupTrsDP = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_refno','fd_refno','fs_kd_dept',
			'fs_nm_dept','fs_docno', 'fs_kd_cust',
			'fs_note','fn_amt'],
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
			url: 'cbreceive/dp_trs'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_cust': Ext.getCmp('cboInputCustDP').getValue(),
					'fs_cari': Ext.getCmp('txtCari12').getValue()
				});
			}
		}
	});

	var winGrid12 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupTrsDP,
		columns: [
			{xtype: 'rownumberer', width: 25},
			{text: "Ref No", dataIndex: 'fs_refno', menuDisabled: true, width: 170},
			{text: "Ref Date", dataIndex: 'fd_refno', menuDisabled: true, width: 80},
			{text: "Dept Cd", dataIndex: 'fs_kd_dept', menuDisabled: true, hidden: true},
			{text: "Dept", dataIndex: 'fs_nm_dept', menuDisabled: true, width: 100},
			{text: "Doc No", dataIndex: 'fs_docno', menuDisabled: true, width: 150},
			{text: "Cust Cd", dataIndex: 'fs_kd_cust', menuDisabled: true, hidden: true},
			{text: "Note", dataIndex: 'fs_note', menuDisabled: true, hidden: true},
			{text: "Amount", dataIndex: 'fn_amt', menuDisabled: true, hidden: true}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Ref No / Dept / Doc No',
				id: 'txtCari12',
				name: 'txtCari12',
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
					grupTrsDP.load();
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
			store: grupTrsDP,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari12.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboRefnoDP').setValue(record.get('fs_refno'));
				Ext.getCmp('txtInputDt').setValue(tglDMY(record.get('fd_refno')));
				Ext.getCmp('txtInputCustDP').setValue(record.get('fs_docno'));
				Ext.getCmp('cboInputCustDP').setValue(record.get('fs_kd_cust'));
				Ext.getCmp('txtInputNote').setValue(record.get('fs_note'));
				Ext.getCmp('txtInputDP').setValue(record.get('fn_amt'));
				
				winCari12.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});

	var winCari12 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid12
		],
		listeners: {
			beforehide: function() {
				vMask3.hide();
			},
			beforeshow: function() {
				grupTrsDP.load();
				vMask3.show();
			}
		}
	});

	Ext.define('DataGridDetil', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_cussup', type: 'string'},
			{name: 'fs_nm_cussup', type: 'string'},
			{name: 'fs_addr', type: 'string'},
			{name: 'fs_idcard', type: 'string'},
			{name: 'fs_tlp', type: 'string'},
			{name: 'fs_refno', type: 'string'},
			{name: 'fd_refno', type: 'string'},
			{name: 'fs_docno', type: 'string'},
			{name: 'fd_docno', type: 'string'},
			{name: 'fs_kd_sales', type: 'string'},
			{name: 'fs_nm_sales', type: 'string'},
			{name: 'fs_kd_surveyor', type: 'string'},
			{name: 'fs_nm_surveyor', type: 'string'},
			{name: 'fs_kd_product', type: 'string'},
			{name: 'fs_nm_product', type: 'string'},
			{name: 'fs_rangka', type: 'string'},
			{name: 'fs_mesin', type: 'string'},
			{name: 'fs_cc', type: 'string'},
			{name: 'fs_thn', type: 'string'},
			{name: 'fs_kd_warna', type: 'string'},
			{name: 'fs_nm_warna', type: 'string'},
			{name: 'fs_kd_wh', type: 'string'},
			{name: 'fs_nm_wh', type: 'string'},
			{name: 'fs_kd_salesmtd', type: 'string'},
			{name: 'fs_nm_salesmtd', type: 'string'},
			{name: 'fs_kd_payment', type: 'string'},
			{name: 'fs_nm_payment', type: 'string'},
			{name: 'fs_kd_acno', type: 'string'},
			{name: 'fs_nm_acno', type: 'string'},
			{name: 'fn_unitprice', type: 'string'},
			{name: 'fn_dpmax', type: 'string'},
			{name: 'fn_disc', type: 'string'},
			{name: 'fs_kd_dp', type: 'string'},
			{name: 'fs_nm_dp', type: 'string'},
			{name: 'fn_dp', type: 'string'},
			{name: 'fn_subsidi', type: 'string'},
			{name: 'fn_install', type: 'string'},
			{name: 'fn_total', type: 'string'},
			{name: 'fn_install', type: 'string'},
			{name: 'fs_register', type: 'string'},
			{name: 'fs_kd_cust', type: 'string'},
			{name: 'fs_count_cust', type: 'string'},
			{name: 'fs_nm_qq', type: 'string'},
			{name: 'fs_addr_qq', type: 'string'}
		]
	});

	var grupGridDetil = Ext.create('Ext.data.Store', {
		autoLoad: true,
		model: 'DataGridDetil',
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
			url: 'jual/griddetil'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_cari': Ext.getCmp('txtCari14').getValue()
				});
			}
		}
	});

	var gridDetil = Ext.create('Ext.grid.Panel', {
		anchor: '100%',
		autoDestroy: true,
		height: 500,
		sortableColumns: false,
		store: grupGridDetil,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Cust Cd", dataIndex: 'fs_kd_cussup', menuDisabled: true, hidden: true},
			{text: "Customer", dataIndex: 'fs_nm_cussup', menuDisabled: true, width: 200},
			{text: "Address", dataIndex: 'fs_addr', menuDisabled: true, hidden: true},
			{text: "ID Card", dataIndex: 'fs_idcard', menuDisabled: true, hidden: true},
			{text: "Phone", dataIndex: 'fs_tlp', menuDisabled: true, hidden: true},
			
			{text: "Ref. No", dataIndex: 'fs_refno', menuDisabled: true, width: 150},
			{text: "Ref. Date", dataIndex: 'fd_refno', menuDisabled: true, width: 80},
			{text: "Doc. No", dataIndex: 'fs_docno', menuDisabled: true, width: 170},
			{text: "Doc. Date", dataIndex: 'fd_docno', menuDisabled: true, width: 80},
			{text: "Sales Cd", dataIndex: 'fs_kd_sales', menuDisabled: true, hidden: true},
			
			{text: "Sales", dataIndex: 'fs_nm_sales', menuDisabled: true, hidden: true},
			{text: "Surveyor Cd", dataIndex: 'fs_kd_surveyor', hidden: true},
			{text: "Surveyor", dataIndex: 'fs_nm_surveyor', hidden: true},
			{text: "Term Cd", dataIndex: 'fs_kd_term', hidden: true},
			{text: "Term", dataIndex: 'fs_nm_term', hidden: true},
			
			{text: "Product Cd", dataIndex: 'fs_kd_product', hidden: true},
			{text: "Product", dataIndex: 'fs_nm_product', hidden: true},
			{text: "Chassis", dataIndex: 'fs_rangka', hidden: true},
			{text: "Machine", dataIndex: 'fs_mesin', hidden: true},
			{text: "Cylinder", dataIndex: 'fs_cc', hidden: true},
			
			{text: "Year", dataIndex: 'fs_thn', hidden: true},
			{text: "Color Cd", dataIndex: 'fs_kd_warna', hidden: true},
			{text: "Color", dataIndex: 'fs_nm_warna', hidden: true},
			{text: "WH Cd", dataIndex: 'fs_kd_wh', hidden: true},
			{text: "Warehouse", dataIndex: 'fs_nm_wh', hidden: true},
			
			{text: "Sales Methode Cd", dataIndex: 'fs_kd_salesmtd', hidden: true},
			{text: "Sales Methode", dataIndex: 'fs_nm_salesmtd', hidden: true},
			{text: "Payment Cd", dataIndex: 'fs_kd_payment', hidden: true},
			{text: "Payment", dataIndex: 'fs_nm_payment', hidden: true},
			{text: "Acc. No", dataIndex: 'fs_kd_acno', hidden: true},
			
			{text: "Acc. Name", dataIndex: 'fs_nm_acno', hidden: true},
			{text: "Unit Price", dataIndex: 'fn_unitprice', hidden: true},
			{text: "DP Max", dataIndex: 'fn_dpmax', hidden: true},
			{text: "Disc", dataIndex: 'fn_disc', hidden: true},
			{text: "DP Cd", dataIndex: 'fs_kd_dp', hidden: true},
			
			{text: "DP Name", dataIndex: 'fs_nm_dp', hidden: true},
			{text: "DP Value", dataIndex: 'fn_dp', hidden: true},
			{text: "Leasing Disc", dataIndex: 'fn_subsidi', hidden: true},
			{text: "Installment", dataIndex: 'fn_install', hidden: true},
			{text: "DPP", dataIndex: 'fn_dpp', hidden: true},
			{text: "DPP + Tax", dataIndex: 'fn_dpptax', hidden: true},
			{text: "Total", dataIndex: 'fn_total', hidden: true},
			
			{text: "Register", dataIndex: 'fs_register', hidden: true},
			{text: "Cust Cd", dataIndex: 'fs_kd_cust', hidden: true},
			
			{text: "Cust Count", dataIndex: 'fs_count_cust', hidden: true},
			{text: "QQ Name", dataIndex: 'fs_nm_qq', hidden: true},
			{text: "QQ Addr", dataIndex: 'fs_addr_qq', hidden: true},
			{text: "Boleh Edit", dataIndex: 'fb_edit', hidden: true},
			{
				xtype: 'actioncolumn',
				width: 20,
				items: [{
					iconCls: 'icon-detail',
					tooltip: 'Journal',
					handler: function(grid, rowIndex, colIndex, e) {

						var rec = grid.getStore().getAt(rowIndex);
						var xrefno = rec.get('fs_refno');
						var xcustomer = rec.get('fs_nm_cussup');

						
						var txtCustJurnal = {
							anchor: '90%',
							fieldLabel: 'Customer',
							fieldStyle: 'background-color: #eee; background-image: none;',
							readOnly: true,
							id: 'txtCustJurnal',
							name: 'txtCustJurnal',
							xtype: 'textfield',
							value: xcustomer
						};

						Ext.define('DataJurnal', {
							extend: 'Ext.data.Model',
							fields: [
								{name: 'fs_acno', type: 'string'},
								{name: 'fn_debit', type: 'string'},
								{name: 'fn_credit', type: 'string'}
							]
						});

						var grupJurnal = Ext.create('Ext.data.Store', {
							autoLoad: false,
							model: 'DataJurnal',
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
								url: 'jual/gridjurnal'
							},
							listeners: {
								beforeload: function(store) {
									Ext.apply(store.getProxy().extraParams, {
										'fs_refno': xrefno,
									});
								}
							}
						});

						var gridJurnal = Ext.create('Ext.grid.Panel', {
							defaultType: 'textfield',
							height: 200,
							sortableColumns: false,
							store: grupJurnal,
							columns: [{
								xtype: 'rownumberer',
								width: 45
							},{
								text: "Acc. No", 
								dataIndex: 'fs_acno', 
								menuDisabled: true, 
								width: 150
							},{
								text: "Debit", 
								dataIndex: 'fn_debit', 
								menuDisabled: true, 
								width: 120
							},{
								text: "Credit", 
								dataIndex: 'fn_credit', 
								menuDisabled: true, 
								width: 120
							}],
							listeners: {

							},
							viewConfig: {
								getRowClass: function() {
									return 'rowwrap';
								},
								markDirty: false,
								stripeRows: true
							}
						});

						var txtTotalD = {
							anchor: '100%',
							fieldLabel: 'DEBIT',
							fieldStyle: 'background-color: #eee; background-image: none;',
							readOnly: true,
							id: 'txtTotalD',
							name: 'txtTotalD',
							xtype: 'textfield'
						};

						var txtTotalC = {
							anchor: '100%',
							fieldLabel: 'CREDIT',
							fieldStyle: 'background-color: #eee; background-image: none;',
							readOnly: true,
							id: 'txtTotalC',
							name: 'txtTotalC',
							xtype: 'textfield'
						};

						var winJurnal = Ext.create('Ext.window.Window', {
							width: 500,
					        plain: true,
					        title: xrefno,
					        border: false,
					        autoScroll: true,
					        closable: false,
					        collapsible: false,
					        draggable: false,
		    				resizable: false,
							items: [{
								fieldDefaults: {
									labelAlign: 'right',
									labelSeparator: '',
									labelWidth: 80,
									msgTarget: 'side'
								},
								style: 'padding: 5px;',
								title: '',
								xtype: 'fieldset',
								items: [
									txtCustJurnal
								]
							},{
								fieldDefaults: {
									labelAlign: 'right',
									labelSeparator: '',
									labelWidth: 150,
									msgTarget: 'side'
								},
								style: 'padding: 5px;',
								title: '',
								xtype: 'fieldset',
								items: [
									gridJurnal
								]
							},{
								fieldDefaults: {
									labelAlign: 'right',
									labelSeparator: '',
									labelWidth: 50,
									msgTarget: 'side'
								},
								style: 'padding: 5px;',
								title: 'TOTAL',
								xtype: 'fieldset',
								items: [{
									anchor: '100%',
									layout: 'hbox',
									xtype: 'container',
									items: [{
										flex: 2,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtTotalD
										]
									},{
										flex: 2,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtTotalC
										]
									}]
								}]
							}],
							listeners: {
								beforehide: function() {
									vMask.hide();
								},
								beforeshow: function() {
									grupJurnal.load();
									vMask.show();
								}
							},
							buttons: [{
								text: 'OK',
								handler: function() {
									vMask.hide();
									winJurnal.doClose();
								}
							}]
						});
						
						Ext.Ajax.request({
							method: 'POST',
							url: 'jual/jurnaltotal',
							params : {
								'fs_refno' : xrefno
							},
							success: function(response) {
								var xtext = Ext.decode(response.responseText);
								if (xtext.sukses === true) {

									Ext.getCmp('txtTotalD').setValue(formatNumb(xtext.fn_total_debit));
									Ext.getCmp('txtTotalC').setValue(formatNumb(xtext.fn_total_credit));
									
								}
							},
							failure: function(response) {
								var xText = Ext.decode(response.responseText);
							}
						});

						winJurnal.show();
						winJurnal.center();
					},
					scope: this
				}]
			}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Customer / Ref. No / Doc. No',
				id: 'txtCari14',
				name: 'txtCari14',
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
					grupGridDetil.load();
				}
			}]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: []
		}],
		listeners: {
			celldblclick: function (grid, td, cellIndex, record, tr, rowIndex, e, eOpts) {

				var xcustomer = record.get('fs_nm_cussup');

				Ext.define('Detil', {
    				extend: 'Ext.data.Model',
    				fields: ['label', 'hasil']
				});

				var viewDetail = Ext.create('Ext.grid.Panel', {
					anchor: '100%',
					sortableColumns: false,
					store: {
					   	model: 'Detil',
					    data: [
					        {label: 'Customer', hasil: record.get('fs_nm_cussup')},
					        {label: 'Ref. No', hasil: record.get('fs_refno')},
					        {label: 'Doc. No', hasil: record.get('fs_docno')},
					        {label: 'Sales', hasil: record.get('fs_nm_sales')},
					        {label: 'Surveyor', hasil: record.get('fs_nm_surveyor')},
					        {label: 'Product', hasil: record.get('fs_nm_product')},
					        {label: 'Chassis', hasil: record.get('fs_rangka')},
					        {label: 'Machine', hasil: record.get('fs_mesin')},
					        {label: 'Cylinder', hasil: record.get('fs_cc')},
					        {label: 'Year', hasil: record.get('fs_thn')},
					        {label: 'Color', hasil: record.get('fs_nm_warna')},
					        {label: 'Warehouse', hasil: record.get('fs_nm_wh')},
					        {label: 'Sales Methode', hasil: record.get('fs_nm_salesmtd')},
					        {label: 'Payment', hasil: record.get('fs_nm_payment')},
					        {label: 'Acc. No', hasil: record.get('fs_kd_acno')},
					        {label: 'Acc. Name', hasil: record.get('fs_nm_acno')},
					        {label: 'Unit Price', hasil: formatNumb(record.get('fn_unitprice'))},
					        {label: 'DP Max', hasil: formatNumb(record.get('fn_dpmax'))},
					        {label: 'Disc', hasil: formatNumb(record.get('fn_disc'))},
					        {label: 'DP Name', hasil: record.get('fs_nm_dp')},
					        {label: 'DP Value', hasil: formatNumb(record.get('fn_dp'))},
					        {label: 'Leasing Disc', hasil: formatNumb(record.get('fn_subsidi'))},
					        {label: 'Total', hasil: formatNumb(record.get('fn_total'))},
					        {label: 'DPP', hasil: formatNumb(record.get('fn_dpp'))},
					        {label: 'DPP + Tax', hasil: formatNumb(record.get('fn_dpptax'))},
					        {label: 'Register', hasil: record.get('fs_register')}
					    ]},
					columns: [{
					    dataIndex: 'label',
					    text: 'Label',
					    locked: true,
					    width: 160
					}, {
					    dataIndex: 'hasil',
					    text: 'Hasil',
					    width: 380,
					    flex: 1
					}],
					viewConfig: {
						getRowClass: function() {
							return 'rowwrap';
						},
						markDirty: false
					}
				});

				var winDetail = Ext.create('Ext.window.Window', {
			        title: xcustomer,
			        width: 550,
			        height: 400,
			        plain: true,
			        autoScroll: true,
			        collapsible: false,
			        draggable: false,
    				resizable: false,
			        headerPosition: 'top',
			        layout: 'fit',
			        items: [
			        	viewDetail
			        ]
			    });

			    winDetail.show();
			    winDetail.center();
			}
		},
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupGridDetil
		}),
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});

	function fnCekSaveDP() {
		Ext.Ajax.on('beforerequest', fnMaskShow3);
		Ext.Ajax.on('requestcomplete', fnMaskHide3);
		Ext.Ajax.on('requestexception', fnMaskHide3);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'cbreceive/dp_ceksave',
			params: {
				'fs_refno_jual': Ext.getCmp('cboRefno').getValue(),
				'fs_refno': Ext.getCmp('cboRefnoDP').getValue(),
				'fn_dp': Ext.getCmp('txtInputDP').getValue()
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
						fnSaveDP();
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
									fnSaveDP();
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
				fnMaskHide3();
			}
		});
	}

	function fnSaveDP() {
		Ext.Ajax.on('beforerequest', fnMaskShow3);
		Ext.Ajax.on('requestcomplete', fnMaskHide3);
		Ext.Ajax.on('requestexception', fnMaskHide3);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'cbreceive/dp_save',
			params: {
				'fs_refno': Ext.getCmp('cboRefnoDP').getValue(),
				'fd_refno': Ext.Date.format(Ext.getCmp('txtInputDt').getValue(), 'Ymd'),
				'fs_kd_cust': Ext.getCmp('cboInputCustDP').getValue(),
				'fs_nm_cust': Ext.getCmp('txtInputCustDP').getValue(),
				'fs_note': Ext.getCmp('txtInputNote').getValue(),
				'fn_dp': Ext.getCmp('txtInputDP').getValue()
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
					Ext.getCmp('cboRefnoDP').setValue(xtext.refnodp.trim());
					Ext.getCmp('cboDP').setValue(xtext.kdcust.trim());
					Ext.getCmp('txtDP').setValue(xtext.nmcust.trim());
					Ext.getCmp('txtNilaiDP').setValue(xtext.nilaidp.trim());
					vMask3.hide();
					vMask.hide();
					winDP.hide();
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
				fnMaskHide3();
			}
		});
	}

	function fnCekRemoveDP() {
		Ext.Ajax.on('beforerequest', fnMaskShow3);
		Ext.Ajax.on('requestcomplete', fnMaskHide3);
		Ext.Ajax.on('requestexception', fnMaskHide3);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'cbreceive/dp_cekremove',
			params: {
				'fs_refno_jual': Ext.getCmp('cboRefno').getValue(),
				'fs_refno': Ext.getCmp('cboRefnoDP').getValue()
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
					if (xtext.sukses === true) {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.YESNO,
							closable: false,
							icon: Ext.Msg.QUESTION,
							msg: xtext.hasil,
							title: 'IDS',
							fn: function(btn) {
								if (btn == 'yes') {
									fnRemoveDP();
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
				fnMaskHide3();
			}
		});
	}

	function fnRemoveDP() {
		Ext.Ajax.on('beforerequest', fnMaskShow3);
		Ext.Ajax.on('requestcomplete', fnMaskHide3);
		Ext.Ajax.on('requestexception', fnMaskHide3);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'cbreceive/dp_remove',
			params: {
				'fs_refno': Ext.getCmp('cboRefnoDP').getValue()
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
					Ext.getCmp('cboDP').setValue('');
					Ext.getCmp('txtDP').setValue('');
					Ext.getCmp('txtNilaiDP').setValue('');
					vMask.hide();
					winDP.hide();
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
				fnMaskHide3();
			}
		});
	}

	var winDP = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: false,
		frame: false,
		height: 235,
		layout: 'fit',
		minHeight: 235,
		maxHeight: 235,
		minWidth: 500,
		maxWidth: 500,
		title: 'Customer DP',
		width: 500,
		items: [
			Ext.create('Ext.form.Panel', {
				bodyStyle: 'padding: 5px; background-color: '.concat(gBasePanel),
				fieldDefaults: {
					labelAlign: 'right',
					labelSeparator: '',
					labelWidth: 70,
					msgTarget: 'side'
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
							anchor: '98%',
							emptyText: 'AUTOMATIC',
							fieldLabel: 'Ref. No',
							id: 'cboRefnoDP',
							name: 'cboRefnoDP',
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
										winCari12.show();
										winCari12.center();
									}
								}
							}
						}]
					},{
						flex: 0.25,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							afterLabelTextTpl: required,
							allowBlank: false,
							anchor: '100%',
							editable: true,
							format: 'd-m-Y',
							id: 'txtInputDt',
							labelWidth: 70,
							maskRe: /[0-9-]/,
							minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
							name: 'txtInputDt',
							value: new Date(),
							xtype: 'datefield'
						}]
					}]
				},{
					afterLabelTextTpl: required,
					allowBlank: false,
					anchor: '100%',
					fieldLabel: 'Customer',
					id: 'txtInputCustDP',
					name: 'txtInputCustDP',
					readOnly: true,
					value: '',
					xtype: 'textfield'
				},{
					afterLabelTextTpl: required,
					allowBlank: false,
					anchor: '100%',
					fieldLabel: 'Cust Cd',
					hidden: true,
					id: 'cboInputCustDP',
					name: 'cboInputCustDP',
					readOnly: true,
					value: '',
					xtype: 'textfield'
				},{
					afterLabelTextTpl: required,
					allowBlank: false,
					anchor: '100%',
					fieldLabel: 'Note',
					grow: true,
					growMin: 70,
					growMax: 70,
					id: 'txtInputNote',
					name: 'txtInputNote',
					value: '',
					xtype: 'textareafield'
				},
					Ext.create('Ext.ux.form.NumericField', {
						afterLabelTextTpl: required,
						allowBlank: false,
						alwaysDisplayDecimals: true,
						anchor: '50%',
						currencySymbol: null,
						decimalPrecision: 2,
						decimalSeparator: '.',
						fieldLabel: 'DP Values',
						fieldStyle: 'text-align: right;',
						hideTrigger: true,
						id: 'txtInputDP',
						keyNavEnabled: false,
						mouseWheelEnabled: false,
						name: 'txtInputDP',
						thousandSeparator: ',',
						useThousandSeparator: true,
						value: 0,
						listeners: {
							change: function(value) {
								if (Ext.isEmpty(Ext.getCmp('txtInputDP').getValue())) {
									Ext.getCmp('txtInputDP').setValue(0);
								}
								else {
									return value;
								}
							}
						}
					})
				]
			})
		],
		buttons: [{
			text: 'Save',
			handler: fnCekSaveDP
		},{
			text: 'Remove',
			handler: fnCekRemoveDP
		},{
			text: 'Cancel',
			handler: function() {
				vMask.hide();
				winDP.hide();
			}
		}]
	});

	var grupCust = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_code','fs_kd_cust','fs_count','fs_nm_code','fs_idcard','fs_addr','fs_tlp'],
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
		width: 750,
		sortableColumns: false,
		store: grupCust,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Code", dataIndex: 'fs_kd_cust', menuDisabled: true, hidden: true},
			{text: "Count", dataIndex: 'fs_count', menuDisabled: true, hidden: true},
			{text: "Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 250},
			{text: "Phone", dataIndex: 'fs_tlp', menuDisabled: true, width: 100},
			{text: "IdCard", dataIndex: 'fs_idcard', menuDisabled: true, hidden: true},
			{text: "Address", dataIndex: 'fs_addr', menuDisabled: true, width: 230}
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
				Ext.getCmp('txtID').setValue(record.get('fs_idcard'));
				Ext.getCmp('txtAddr').setValue(record.get('fs_addr'));
				Ext.getCmp('txtTlp').setValue(record.get('fs_tlp'));
				
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

	var cboCust = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: "Select a",
		fieldLabel: 'Customer',
		id: 'cboCust',
		labelWidth: 70,
		name: 'cboCust',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtCust').setValue('');
				Ext.getCmp('txtCustCd').setValue('');
				Ext.getCmp('txtCount').setValue('');
				Ext.getCmp('txtID').setValue('');
				Ext.getCmp('txtAddr').setValue('');
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

	var txtCustCd = {
		anchor: '98%',
		fieldLabel: 'Cust Code',
		hidden: true,
		id: 'txtCustCd',
		labelWidth: 70,
		name: 'txtCustCd',
		xtype: 'textfield'
	};

	var txtCount = {
		anchor: '98%',
		fieldLabel: 'Cust Count',
		hidden: true,
		id: 'txtCount',
		labelWidth: 70,
		name: 'txtCount',
		xtype: 'textfield'
	};

	var txtCust = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '95%',
		emptyText: "Customer",
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtCust',
		name: 'txtCust',
		readOnly: true,
		xtype: 'textfield'
	};

	var txtID = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: "Customer's ID Card",
		fieldLabel: 'ID Card',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtID',
		labelWidth: 70,
		name: 'txtID',
		readOnly: true,
		xtype: 'textfield'
	};

	var txtAddr = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: "Customer's Address",
		fieldLabel: 'Address',
		fieldStyle: 'background-color: #eee; background-image: none;',
		grow: true,
		growMin: 50,
		growMax: 50,
		id: 'txtAddr',
		labelWidth: 70,
		name: 'txtAddr',
		readOnly: true,
		xtype: 'textareafield'
	};

	var txtTlp = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: "Customer's Phone",
		fieldLabel: 'Phone',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtTlp',
		labelWidth: 70,
		name: 'txtTlp',
		readOnly: true,
		xtype: 'textfield'
	};

	var txtQQ = {
		anchor: '100%',
		emptyText: "Enter a QQ Customer's Name",
		fieldLabel: 'QQ Name',
		hidden: true,
		id: 'txtQQ',
		labelWidth: 70,
		name: 'txtQQ',
		xtype: 'textfield'
	};

	var txtAddrQQ = {
		anchor: '100%',
		emptyText: "Enter a QQ Customer's Address",
		fieldLabel: 'QQ Address',
		grow: true,
		growMin: 35,
		growMax: 35,
		hidden: true,
		id: 'txtAddrQQ',
		labelWidth: 70,
		name: 'txtAddrQQ',
		xtype: 'textareafield'
	};

	var btnEditCust = {
		anchor: '98%',
		iconAlign: 'left',
		iconCls: 'icon-user-edit',
		id: 'btnEditCust',
		name: 'btnEditCust',
		scale: 'small',
		text: 'Edit Cust',
		xtype: 'button',
		handler: function() {
			if (Ext.getCmp('txtCust').getValue().trim() === '') {
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.Msg.WARNING,
					msg: 'Customer is empty, please fill in advance!!',
					title: 'IDS'
				});
				return;
			}
			else {
				Ext.getCmp('txtInputCust').setValue(Ext.getCmp('txtCust').getValue());
				Ext.getCmp('txtInputID').setValue(Ext.getCmp('txtID').getValue());
				Ext.getCmp('txtInputAddr').setValue(Ext.getCmp('txtAddr').getValue());
				Ext.getCmp('txtInputTlp').setValue(Ext.getCmp('txtTlp').getValue());
				
				vMask.show();
				winCust.show();
			}
		}
	};

	var btnCreateCust = {
		anchor: '100%',
		iconAlign: 'left',
		iconCls: 'icon-user-add',
		id: 'btnCreateCust',
		name: 'btnCreateCust',
		scale: 'small',
		text: 'Create Cust',
		xtype: 'button',
		handler: function() {
			vMask.show();
			winCust.show();
		}
	};

	var grupRefno = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_cussup','fs_nm_cussup','fs_addr','fs_idcard','fs_tlp','fs_refno',
			'fd_refno','fs_docno','fd_docno','fs_kd_sales','fs_nm_sales',
			'fs_kd_surveyor','fs_nm_surveyor','fs_kd_term','fs_nm_term','fs_kd_product',
			'fs_nm_product','fs_rangka','fs_mesin','fs_cc','fs_thn',
			'fs_kd_warna','fs_nm_warna','fs_kd_wh','fs_nm_wh','fs_kd_salesmtd',
			'fs_nm_salesmtd','fs_kd_payment','fs_nm_payment','fs_kd_acno','fs_nm_acno',
			'fn_unitprice','fn_dpmax','fn_disc','fs_kd_dp','fs_nm_dp',
			'fn_dp','fn_subsidi','fn_install','fn_total',
			'fs_register','fs_kd_cust','fs_count_cust', 'fs_nm_qq', 'fs_addr_qq','fb_edit'
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
			url: 'jual/refno'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboRefno').getValue(),
					'fs_docno': Ext.getCmp('cboRefno').getValue(),
					'fs_nm_cussup': Ext.getCmp('cboRefno').getValue(),
					'fs_cari': Ext.getCmp('txtCari2').getValue()
				});
			}
		}
	});

	var winGrid2 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 750,
		sortableColumns: false,
		store: grupRefno,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Cust Cd", dataIndex: 'fs_kd_cussup', menuDisabled: true, hidden: true},
			{text: "Customer", dataIndex: 'fs_nm_cussup', menuDisabled: true, width: 200},
			{text: "Address", dataIndex: 'fs_addr', menuDisabled: true, hidden: true},
			{text: "ID Card", dataIndex: 'fs_idcard', menuDisabled: true, hidden: true},
			{text: "Phone", dataIndex: 'fs_tlp', menuDisabled: true, hidden: true},
			
			{text: "Ref. No", dataIndex: 'fs_refno', menuDisabled: true, width: 150},
			{text: "Ref. Date", dataIndex: 'fd_refno', menuDisabled: true, width: 80},
			{text: "Doc. No", dataIndex: 'fs_docno', menuDisabled: true, width: 170},
			{text: "Doc. Date", dataIndex: 'fd_docno', menuDisabled: true, width: 80},
			{text: "Sales Cd", dataIndex: 'fs_kd_sales', menuDisabled: true, hidden: true},
			
			{text: "Sales", dataIndex: 'fs_nm_sales', menuDisabled: true, hidden: true},
			{text: "Surveyor Cd", dataIndex: 'fs_kd_surveyor', hidden: true},
			{text: "Surveyor", dataIndex: 'fs_nm_surveyor', hidden: true},
			{text: "Term Cd", dataIndex: 'fs_kd_term', hidden: true},
			{text: "Term", dataIndex: 'fs_nm_term', hidden: true},
			
			{text: "Product Cd", dataIndex: 'fs_kd_product', hidden: true},
			{text: "Product", dataIndex: 'fs_nm_product', hidden: true},
			{text: "Chassis", dataIndex: 'fs_rangka', hidden: true},
			{text: "Machine", dataIndex: 'fs_mesin', hidden: true},
			{text: "Cylinder", dataIndex: 'fs_cc', hidden: true},
			
			{text: "Year", dataIndex: 'fs_thn', hidden: true},
			{text: "Color Cd", dataIndex: 'fs_kd_warna', hidden: true},
			{text: "Color", dataIndex: 'fs_nm_warna', hidden: true},
			{text: "WH Cd", dataIndex: 'fs_kd_wh', hidden: true},
			{text: "Warehouse", dataIndex: 'fs_nm_wh', hidden: true},
			
			{text: "Sales Methode Cd", dataIndex: 'fs_kd_salesmtd', hidden: true},
			{text: "Sales Methode", dataIndex: 'fs_nm_salesmtd', hidden: true},
			{text: "Payment Cd", dataIndex: 'fs_kd_payment', hidden: true},
			{text: "Payment", dataIndex: 'fs_nm_payment', hidden: true},
			{text: "Acc. No", dataIndex: 'fs_kd_acno', hidden: true},
			
			{text: "Acc. Name", dataIndex: 'fs_nm_acno', hidden: true},
			{text: "Unit Price", dataIndex: 'fn_unitprice', hidden: true},
			{text: "DP Max", dataIndex: 'fn_dpmax', hidden: true},
			{text: "Disc", dataIndex: 'fn_disc', hidden: true},
			{text: "DP Cd", dataIndex: 'fs_kd_dp', hidden: true},
			
			{text: "DP Name", dataIndex: 'fs_nm_dp', hidden: true},
			{text: "DP Value", dataIndex: 'fn_dp', hidden: true},
			{text: "Leasing Disc", dataIndex: 'fn_subsidi', hidden: true},
			{text: "Installment", dataIndex: 'fn_install', hidden: true},
			{text: "Total", dataIndex: 'fn_total', hidden: true},
			
			{text: "Register", dataIndex: 'fs_register', hidden: true},
			{text: "Cust Cd", dataIndex: 'fs_kd_cust', hidden: true},
			
			{text: "Cust Count", dataIndex: 'fs_count_cust', hidden: true},
			{text: "QQ Name", dataIndex: 'fs_nm_qq', hidden: true},
			{text: "QQ Addr", dataIndex: 'fs_addr_qq', hidden: true},
			{text: "Boleh Edit", dataIndex: 'fb_edit', hidden: true}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Customer / Ref. No / Doc. No',
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
					winCari2.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboCust').setValue(record.get('fs_kd_cussup'));
				Ext.getCmp('txtCust').setValue(record.get('fs_nm_cussup'));
				Ext.getCmp('txtAddr').setValue(record.get('fs_addr'));
				Ext.getCmp('txtID').setValue(record.get('fs_idcard'));
				Ext.getCmp('txtTlp').setValue(record.get('fs_tlp'));
				
				Ext.getCmp('cboRefno').setValue(record.get('fs_refno'));
				Ext.getCmp('txtRefnodt').setValue(tglDMY(record.get('fd_refno')));
				Ext.getCmp('txtDocno').setValue(record.get('fs_docno'));
				Ext.getCmp('txtDocnodt').setValue(tglDMY(record.get('fd_docno')));
				Ext.getCmp('cboSales').setValue(record.get('fs_kd_sales'));
				
				Ext.getCmp('txtSales').setValue(record.get('fs_nm_sales'));
				Ext.getCmp('cboSurvey').setValue(record.get('fs_kd_surveyor'));
				Ext.getCmp('txtSurvey').setValue(record.get('fs_nm_surveyor'));
				Ext.getCmp('cboTerm').setValue(record.get('fs_kd_term'));
				Ext.getCmp('txtTerm').setValue(record.get('fs_nm_term'));
				
				Ext.getCmp('cboWH').setValue(record.get('fs_kd_wh'));//taruh diatas rangka spy ketika listener change di wh, tdk hapus rangka
				Ext.getCmp('txtWH').setValue(record.get('fs_nm_wh'));
				Ext.getCmp('cboProd').setValue(record.get('fs_kd_product'));
				Ext.getCmp('txtProd').setValue(record.get('fs_nm_product'));
				Ext.getCmp('cboRangka').setValue(record.get('fs_rangka'));
				
				Ext.getCmp('txtMesin').setValue(record.get('fs_mesin'));
				Ext.getCmp('txtCC').setValue(record.get('fs_cc'));
				Ext.getCmp('txtTahun').setValue(record.get('fs_thn'));
				Ext.getCmp('cboColor').setValue(record.get('fs_kd_warna'));
				Ext.getCmp('txtColor').setValue(record.get('fs_nm_warna'));
				
				Ext.getCmp('cboSM').setValue(record.get('fs_kd_salesmtd'));
				Ext.getCmp('txtSM').setValue(record.get('fs_nm_salesmtd'));
				Ext.getCmp('cboPM').setValue(record.get('fs_kd_payment'));
				Ext.getCmp('txtPM').setValue(record.get('fs_nm_payment'));
				Ext.getCmp('cboAcno').setValue(record.get('fs_kd_acno'));
				
				Ext.getCmp('txtAcno').setValue(record.get('fs_nm_acno'));
				Ext.getCmp('txtPrice').setValue(record.get('fn_unitprice'));
				Ext.getCmp('txtDPMax').setValue(record.get('fn_dpmax'));
				Ext.getCmp('txtDisc').setValue(record.get('fn_disc'));
				Ext.getCmp('cboRefnoDP').setValue(record.get('fs_refno_dp'));
				
				Ext.getCmp('cboDP').setValue(record.get('fs_kd_dp'));
				Ext.getCmp('txtDP').setValue(record.get('fs_nm_dp'));
				Ext.getCmp('txtNilaiDP').setValue(record.get('fn_dp'));
				Ext.getCmp('txtDiscL').setValue(record.get('fn_subsidi'));
				Ext.getCmp('txtInstal').setValue(record.get('fn_install'));
				
				Ext.getCmp('txtTotal').setValue(record.get('fn_total'));
				Ext.getCmp('txtReg').setValue(record.get('fs_register'));
				Ext.getCmp('txtCustCd').setValue(record.get('fs_kd_cust'));
				Ext.getCmp('txtCount').setValue(record.get('fs_count_cust'));
				// Ext.getCmp('txtQQ').setValue(record.get('fs_nm_qq'));
				// Ext.getCmp('txtAddrQQ').setValue(record.get('fs_addr_qq'));
				
				var xEdit = record.get('fb_edit');
				if (xEdit === 0) {
					Ext.getCmp('btnSave').setDisabled(true);
					Ext.getCmp('btnRemove').setDisabled(true);
					Ext.getCmp('btnCreateDP').setDisabled(true);
				}
				else {
					Ext.getCmp('btnSave').setDisabled(false);
					Ext.getCmp('btnRemove').setDisabled(false);
					Ext.getCmp('btnCreateDP').setDisabled(false);
				}
				
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
		labelWidth: 70,
		name: 'cboRefno',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				// Ext.getCmp('btnSave').setDisabled(false);
				// Ext.getCmp('btnRemove').setDisabled(false);
				// Ext.getCmp('btnCreateDP').setDisabled(false);
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

	var txtDocno = {
		anchor: '95%',
		emptyText: 'Enter a Document Number',
		fieldLabel: 'Doc. No',
		id: 'txtDocno',
		labelWidth: 70,
		name: 'txtDocno',
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

	var grupSales = Ext.create('Ext.data.Store', {
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
			url: 'jual/sales'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_code': Ext.getCmp('cboSales').getValue(),
					'fs_nm_code': Ext.getCmp('cboSales').getValue(),
					'fs_cari': Ext.getCmp('txtCari3').getValue()
				});
			}
		}
	});


	var winGrid3 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 750,
		sortableColumns: false,
		store: grupSales,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Salesman Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Salesman Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 380}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Salesman Code / Salesman Name',
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
					grupSales.load();
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
			store: grupSales,
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
				Ext.getCmp('cboSales').setValue(record.get('fs_code'));
				Ext.getCmp('txtSales').setValue(record.get('fs_nm_code'));
				
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
				grupSales.load();
				vMask.show();
			}
		}
	});

	var cboSales = {
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Salesman',
		id: 'cboSales',
		labelWidth: 70,
		name: 'cboSales',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtSales').setValue('');
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

	var txtSales = {
		anchor: '100%',
		emptyText: 'Salesman',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtSales',
		name: 'txtSales',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupSurvey = Ext.create('Ext.data.Store', {
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
			url: 'jual/survey'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_code': Ext.getCmp('cboSurvey').getValue(),
					'fs_nm_code': Ext.getCmp('cboSurvey').getValue(),
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
		store: grupSurvey,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Surveyor Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Surveyor Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 380}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Surveyor Code / Surveyor Name',
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
					grupSurvey.load();
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
			store: grupSurvey,
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
				Ext.getCmp('cboSurvey').setValue(record.get('fs_code'));
				Ext.getCmp('txtSurvey').setValue(record.get('fs_nm_code'));
				
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
				grupSurvey.load();
				vMask.show();
			}
		}
	});

	var cboSurvey = {
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Surveyor',
		id: 'cboSurvey',
		labelWidth: 70,
		name: 'cboSurvey',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtSurvey').setValue('');
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
	};

	var txtSurvey = {
		anchor: '100%',
		emptyText: 'Surveyor',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtSurvey',
		name: 'txtSurvey',
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
					winCari5.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboTerm').setValue(record.get('fs_kd_term'));
				Ext.getCmp('txtTerm').setValue(record.get('fs_nm_term'));
				
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
		labelWidth: 70,
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
					winCari5.show();
					winCari5.center();
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

	var grupProd = Ext.create('Ext.data.Store', {
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
			url: 'jual/product'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_product': Ext.getCmp('cboProd').getValue(),
					'fs_nm_product': Ext.getCmp('cboProd').getValue(),
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
		store: grupProd,
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
					winCari6.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboProd').setValue(record.get('fs_kd_product'));
				Ext.getCmp('txtProd').setValue(record.get('fs_nm_product'));
				
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
				grupProd.load();
				vMask.show();
			}
		}
	});

	var cboProd = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Product',
		id: 'cboProd',
		labelWidth: 70,
		name: 'cboProd',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtProd').setValue('');
				Ext.getCmp('cboRangka').setValue('');
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
	};

	var txtProd = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Product',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtProd',
		name: 'txtProd',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupRangka = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_rangka','fs_mesin','fs_thn',
			'fs_cc','fs_kd_warna','fs_nm_warna',
			'fs_register'
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
			url: 'jual/rangka'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboRefno').getValue(),
					'fs_rangka': Ext.getCmp('cboRangka').getValue(),
					'fs_mesin': Ext.getCmp('cboRangka').getValue(),
					'fs_kd_product': Ext.getCmp('cboProd').getValue(),
					'fs_kd_wh': Ext.getCmp('cboWH').getValue(),
					'fs_cari': Ext.getCmp('txtCari13').getValue()
				});
			}
		}
	});


	var winGrid13 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 750,
		sortableColumns: false,
		store: grupRangka,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Chassis", dataIndex: 'fs_rangka', menuDisabled: true, width: 200},
			{text: "Machine", dataIndex: 'fs_mesin', menuDisabled: true, width: 180},
			{text: "Year", dataIndex: 'fs_thn', align: 'center', menuDisabled: true, width: 50},
			{text: "Cylinder", dataIndex: 'fs_cc', align: 'center', menuDisabled: true, width: 50},
			{text: "Color Cd", dataIndex: 'fs_kd_warna', menuDisabled: true, hidden: true},
			{text: "Color", dataIndex: 'fs_nm_warna', menuDisabled: true, width: 200},
			{text: "Register", dataIndex: 'fs_register', hidden: true}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Chassis / Machine',
				id: 'txtCari13',
				name: 'txtCari13',
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
					grupRangka.load();
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
			store: grupRangka,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari13.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboRangka').setValue(record.get('fs_rangka'));
				Ext.getCmp('txtMesin').setValue(record.get('fs_mesin'));
				Ext.getCmp('txtTahun').setValue(record.get('fs_thn'));
				Ext.getCmp('txtCC').setValue(record.get('fs_cc'));
				Ext.getCmp('cboColor').setValue(record.get('fs_kd_warna'));
				Ext.getCmp('txtColor').setValue(record.get('fs_nm_warna'));
				Ext.getCmp('txtReg').setValue(record.get('fs_register'));
				
				winCari13.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});

	var winCari13 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid13
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupRangka.load();
				vMask.show();
			}
		}
	});

	var cboRangka = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '95%',
		emptyText: 'Select a Chassis Number',
		fieldLabel: 'Chassis',
		id: 'cboRangka',
		labelWidth: 70,
		name: 'cboRangka',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtReg').setValue('');
				Ext.getCmp('txtMesin').setValue('');
				Ext.getCmp('txtCC').setValue('0');
				Ext.getCmp('txtTahun').setValue(Ext.Date.format(new Date(), 'Y'));
				Ext.getCmp('cboColor').setValue('');
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
					var xprod = Ext.getCmp('txtProd').getValue('');
					var xwh = Ext.getCmp('txtWH').getValue('');
					if (xprod === '') {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.Msg.WARNING,
							msg: 'Product is empty, please fill in advance!!',
							title: 'IDS'
						});
						return;
					}
					
					if (xwh === '') {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.Msg.WARNING,
							msg: 'Warehouse is empty, please fill in advance!!',
							title: 'IDS'
						});
						return;
					}
					
					winCari13.show();
					winCari13.center();
				}
			}
		}
	};

	var txtMesin = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '95%',
		emptyText: 'Machine Number',
		fieldLabel: 'Machine',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtMesin',
		labelWidth: 70,
		name: 'txtMesin',
		readOnly: true,
		xtype: 'textfield'
	};

	var txtReg = {
		anchor: '95%',
		fieldLabel: 'Register',
		hidden: true,
		id: 'txtReg',
		labelWidth: 70,
		name: 'txtReg',
		xtype: 'textfield'
	};

	var txtCC = Ext.create('Ext.ux.form.NumericField', {
		afterLabelTextTpl: required,
		allowBlank: false,
		alwaysDisplayDecimals: false,
		anchor: '90%',
		currencySymbol: null,
		decimalPrecision: 0,
		decimalSeparator: '.',
		fieldLabel: 'Cylinder (CC)',
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		hideTrigger: true,
		id: 'txtCC',
		keyNavEnabled: false,
		labelWidth: 85,
		mouseWheelEnabled: false,
		name: 'txtCC',
		readOnly: true,
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
	});

	var txtTahun = Ext.create('Ext.ux.form.NumericField', {
		afterLabelTextTpl: required,
		allowBlank: false,
		alwaysDisplayDecimals: false,
		anchor: '100%',
		currencySymbol: null,
		decimalPrecision: 0,
		decimalSeparator: '.',
		fieldLabel: 'Year',
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		hideTrigger: true,
		id: 'txtTahun',
		keyNavEnabled: false,
		labelWidth: 50,
		mouseWheelEnabled: false,
		name: 'txtTahun',
		readOnly: true,
		thousandSeparator: ',',
		useThousandSeparator: false,
		value: Ext.Date.format(new Date(), 'Y'),
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtTahun').getValue())) {
					Ext.getCmp('txtTahun').setValue(0);
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
			url: 'jual/color'
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
				vMask.hide();
			},
			beforeshow: function() {
				grupColor.load();
				vMask.show();
			}
		}
	});

	var cboColor = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Color Code',
		fieldLabel: 'Color',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'cboColor',
		labelWidth: 85,
		name: 'cboColor',
		readOnly: true,
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
	};

	var txtColor = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Color Name',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtColor',
		name: 'txtColor',
		readOnly: true,
		xtype: 'textfield'
	};

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
			url: 'regstatus/gudang'
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
				vMask.hide();
			},
			beforeshow: function() {
				grupWH.load();
				vMask.show();
			}
		}
	});

	var cboWH = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Warehouse',
		id: 'cboWH',
		labelWidth: 85,
		name: 'cboWH',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtWH').setValue('');
				Ext.getCmp('cboRangka').setValue('');
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
	};

	var txtWH = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Warehouse',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtWH',
		name: 'txtWH',
		readOnly: true,
		xtype: 'textfield'
	};
	
	var grupSalesMtd = Ext.create('Ext.data.Store', {
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
			url: 'jual/sales_mtd'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_vareable': Ext.getCmp('cboSM').getValue(),
					'fs_nm_vareable': Ext.getCmp('cboSM').getValue(),
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
		store: grupSalesMtd,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Sales Methode Cd", dataIndex: 'fs_kd_vareable', menuDisabled: true, width: 100},
			{text: "Sales Methode Name", dataIndex: 'fs_nm_vareable', menuDisabled: true, width: 380}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Sales Methode Cd / Sales Methode Name',
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
					grupSalesMtd.load();
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
			store: grupSalesMtd,
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
				Ext.getCmp('cboSM').setValue(record.get('fs_kd_vareable'));
				Ext.getCmp('txtSM').setValue(record.get('fs_nm_vareable'));
				
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
				grupSalesMtd.load();
				vMask.show();
			}
		}
	});

	var cboSM = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Sales',
		id: 'cboSM',
		labelWidth: 70,
		name: 'cboSM',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtSM').setValue('');
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

	var txtSM = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Sales Methods',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtSM',
		name: 'txtSM',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupPM = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_code','fs_nm_code','fs_kd_acno','fs_nm_acno'],
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
			url: 'jual/payment'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_code': Ext.getCmp('cboPM').getValue(),
					'fs_nm_code': Ext.getCmp('cboPM').getValue(),
					'fs_cari': Ext.getCmp('txtCari10').getValue()
				});
			}
		}
	});

	
	var winGrid10 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupPM,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Payment Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Payment Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 380},
			{text: "Acc. No", dataIndex: 'fs_kd_acno', menuDisabled: true, hidden: true},
			{text: "Acc. Name", dataIndex: 'fs_nm_acno', menuDisabled: true, hidden: true}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Payment Code / Payment Name',
				id: 'txtCari10',
				name: 'txtCari10',
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
					grupPM.load();
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
			store: grupPM,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari10.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboPM').setValue(record.get('fs_code'));
				Ext.getCmp('txtPM').setValue(record.get('fs_nm_code'));
				Ext.getCmp('cboAcno').setValue(record.get('fs_kd_acno'));
				Ext.getCmp('txtAcno').setValue(record.get('fs_nm_acno'));
				
				winCari10.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});

	var winCari10 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid10
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupPM.load();
				vMask.show();
			}
		}
	});

	var cboPM = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Payment',
		id: 'cboPM',
		labelWidth: 70,
		name: 'cboPM',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtPM').setValue('');
				Ext.getCmp('cboAcno').setValue('');
				Ext.getCmp('txtAcno').setValue('');
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
					winCari10.show();
					winCari10.center();
				}
			}
		}
	};

	var txtPM = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Payment Methods',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtPM',
		name: 'txtPM',
		readOnly: true,
		xtype: 'textfield'
	};

	var cboAcno = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Account No',
		fieldLabel: 'Acc. No',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'cboAcno',
		labelWidth: 70,
		name: 'cboAcno',
		readOnly: true,
		xtype: 'textfield'
	};

	var txtAcno = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Account Name',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtAcno',
		name: 'txtAcno',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupDP = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_code','fs_nm_code','fn_dp','fs_nm_dept'],
		timeout: 60000,
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
			url: 'jual/kodedp'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					// 'fs_code': Ext.getCmp('cboDP').getValue(),
					'fs_cari': Ext.getCmp('txtCari11').getValue()
				});
			}
		}
	});


	var winGrid11 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupDP,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 280},
			{text: "Values", dataIndex: 'fn_dp', align: 'right', format: '0,000', menuDisabled: true, width: 100, xtype: 'numbercolumn'},
			{text: "Dept", dataIndex: 'fs_nm_dept', menuDisabled: true, hidden: true}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Code / Name',
				id: 'txtCari11',
				name: 'txtCari11',
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
					grupDP.load();
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
			store: grupDP,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari11.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboDP').setValue(record.get('fs_code'));
				Ext.getCmp('txtDP').setValue(record.get('fs_nm_code'));
				Ext.getCmp('txtNilaiDP').setValue(record.get('fn_dp'));
				
				winCari11.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});

	var winCari11 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid11
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupDP.load();
				vMask.show();
			}
		}
	});

	var cboDP = {
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Cust. DP',
		id: 'cboDP',
		labelWidth: 70,
		name: 'cboDP',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtDP').setValue('');
				Ext.getCmp('txtNilaiDP').setValue('0');
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
					winCari11.show();
					winCari11.center();
				}
			}
		}
	};

	var txtDP = {
		anchor: '100%',
		emptyText: 'Customer DP',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtDP',
		name: 'txtDP',
		readOnly: true,
		xtype: 'textfield'
	};

	var txtNilaiDP = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: true,
		anchor: '95%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		fieldLabel: 'DP Values',
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		hideTrigger: true,
		id: 'txtNilaiDP',
		keyNavEnabled: false,
		labelWidth: 85,
		mouseWheelEnabled: false,
		name: 'txtNilaiDP',
		readOnly: true,
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0,
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtNilaiDP').getValue())) {
					Ext.getCmp('txtNilaiDP').setValue(0);
				}
				
				var xharga = Ext.getCmp('txtPrice').getValue();
				var xdisc = Ext.getCmp('txtDisc').getValue();
				var xdp = Ext.getCmp('txtNilaiDP').getValue();
				var xtotal = xharga - xdisc - xdp;
				Ext.getCmp('txtTotal').setValue(xtotal);
			}
		}
	});

	var txtTotal = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: true,
		anchor: '100%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		editable: false,
		fieldLabel: 'Total',
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		hideTrigger: true,
		id: 'txtTotal',
		keyNavEnabled: false,
		labelWidth: 70,
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
					return value;
				}
			}
		}
	});

	var btnCreateDP = {
		anchor: '98%',
		id: 'btnCreateDP',
		name: 'btnCreateDP',
		scale: 'small',
		text: 'Create / Edit DP',
		xtype: 'button',
		handler: function() {
			var vCust = Ext.getCmp('txtCust').getValue();
			var vCustCd = Ext.getCmp('cboCust').getValue();
			var vProd = Ext.getCmp('txtProd').getValue();
			var vRangka = Ext.getCmp('cboRangka').getValue();
			var vMesin = Ext.getCmp('txtMesin').getValue();
			
			var vNote = 'Pembayaran DP 1 unit kendaraan '.concat(vProd, '\n',
						'a/n: ', vCust, '\n',
						'No Rangka : ', vRangka, '\n',
						'No Mesin : ', vMesin
			);
			
			Ext.getCmp('cboRefnoDP').setValue('');
			Ext.getCmp('txtInputDt').setValue(new Date());
			Ext.getCmp('txtInputCustDP').setValue(vCust);
			Ext.getCmp('cboInputCustDP').setValue(vCustCd);
			Ext.getCmp('txtInputNote').setValue(vNote);
			Ext.getCmp('txtInputDP').setValue('0');
			
			if (vCust.trim() === '') {
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.Msg.WARNING,
					msg: 'Customer is empty, please fill in advance!!',
					title: 'IDS'
				});
				return;
			}
			
			if (vProd.trim() === '' || vRangka.trim() === '' || vMesin.trim() === '') {
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.YESNO,
					closable: false,
					icon: Ext.Msg.QUESTION,
					msg: 'Product, chassis or machine is empty and will be empty in note!! </br> continue?',
					title: 'IDS',
					fn: function(btn) {
						if (btn == 'yes') {
							vMask.show();
							winDP.show();
						}
					}
				});
			}
			else {
				vMask.show();
				winDP.show();
			}
		}
	};

	var txtMan = {
		anchor: '100%',
		emptyText: "Enter Branch Manager Name",
		hidden: true,
		id: 'txtMan',
		name: 'txtMan',
		xtype: 'textfield'
	};

	var txtDPMax = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: true,
		anchor: '100%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		fieldLabel: 'DP Max',
		fieldStyle: 'text-align: right;',
		hidden: true,
		hideTrigger: true,
		id: 'txtDPMax',
		keyNavEnabled: false,
		labelWidth: 70,
		mouseWheelEnabled: false,
		name: 'txtDPMax',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0,
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtDPMax').getValue())) {
					Ext.getCmp('txtDPMax').setValue(0);
				}
				else {
					return value;
				}
			}
		}
	});

	var txtPrice = Ext.create('Ext.ux.form.NumericField', {
		afterLabelTextTpl: required,
		allowBlank: false,
		alwaysDisplayDecimals: true,
		anchor: '95%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		fieldLabel: 'Unit Price',
		fieldStyle: 'text-align: right;',
		hideTrigger: true,
		id: 'txtPrice',
		keyNavEnabled: false,
		labelWidth: 85,
		mouseWheelEnabled: false,
		name: 'txtPrice',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0,
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtPrice').getValue())) {
					Ext.getCmp('txtPrice').setValue(0);
				}
				
				var xharga = Ext.getCmp('txtPrice').getValue();
				var xdisc = Ext.getCmp('txtDisc').getValue();
				var xdp = Ext.getCmp('txtNilaiDP').getValue();
				var xtotal = xharga - xdisc - xdp;
				Ext.getCmp('txtTotal').setValue(xtotal);
			}
		}
	});

	var txtDisc = Ext.create('Ext.ux.form.NumericField', {
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
		labelWidth: 85,
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
				
				var xharga = Ext.getCmp('txtPrice').getValue();
				var xdisc = Ext.getCmp('txtDisc').getValue();
				var xdp = Ext.getCmp('txtNilaiDP').getValue();
				var xtotal = xharga - xdisc - xdp;
				Ext.getCmp('txtTotal').setValue(xtotal);
			}
		}
	});

	var txtDiscL = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: true,
		anchor: '100%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		fieldLabel: 'Leasing Disc',
		fieldStyle: 'text-align: right;',
		hideTrigger: true,
		id: 'txtDiscL',
		keyNavEnabled: false,
		labelWidth: 70,
		mouseWheelEnabled: false,
		name: 'txtDiscL',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0,
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtDiscL').getValue())) {
					Ext.getCmp('txtDiscL').setValue(0);
				}
				else {
					return value;
				}
			}
		}
	});

	var txtInstal = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: true,
		anchor: '100%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		fieldLabel: 'Installment',
		fieldStyle: 'text-align: right;',
		hideTrigger: true,
		id: 'txtInstal',
		keyNavEnabled: false,
		labelWidth: 70,
		mouseWheelEnabled: false,
		name: 'txtInstal',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0,
		listeners: {
			change: function(value) {
				if (Ext.isEmpty(Ext.getCmp('txtInstal').getValue())) {
					Ext.getCmp('txtInstal').setValue(0);
				}
				else {
					return value;
				}
			}
		}
	});
	
	var btnDP = {
		anchor: '98%',
		id: 'btnDP',
		name: 'btnDP',
		text: 'DP',
		xtype: 'button',
		handler: fnPrintKwitansiDP
	};
	
	var btnFidusia = {
		anchor: '98%',
		id: 'btnFidusia',
		name: 'btnFidusia',
		text: 'Fidusia',
		xtype: 'button',
		handler: fnPrintFidusia
	};
	
	var btnSI = {
		anchor: '98%',
		id: 'btnSI',
		name: 'btnSI',
		text: 'SI',
		xtype: 'button',
		handler: fnPrintSI
	};
	
	var btnAgreement = {
		anchor: '98%',
		id: 'btnAgreement',
		name: 'btnAgreement',
		text: 'Agreement',
		xtype: 'button',
		handler: function() {
			Ext.getCmp('txtInputMan').setValue(Ext.getCmp('txtMan').getValue());
			
			vMask.show();
			winMan.show();
		}
	};
	
	var btnReceipt = {
		anchor: '98%',
		id: 'btnReceipt',
		name: 'btnReceipt',
		text: 'Receipt',
		xtype: 'button',
		handler: fnPrintTT
	};
	
	var btnSave = {
		anchor: '98%',
		id: 'btnSave',
		name: 'btnSave',
		text: 'Save',
		xtype: 'button',
		handler: fnCekSave
	};
	
	var btnPrintSO = {
		anchor: '98%',
		id: 'btnPrintSO',
		name: 'btnPrintSO',
		text: 'Print SO',
		xtype: 'button',
		handler: fnPrintSO
	};
	
	var btnPrintDO = {
		anchor: '98%',
		id: 'btnPrintDO',
		name: 'btnPrintDO',
		text: 'Print DO',
		xtype: 'button',
		handler: fnPrintDO
	};
	
	var btnReset = {
		anchor: '98%',
		id: 'btnReset',
		name: 'btnReset',
		text: 'Reset',
		xtype: 'button',
		handler: fnReset
	};
	
	var btnRemove = {
		anchor: '98%',
		id: 'btnRemove',
		name: 'btnRemove',
		text: 'Remove',
		xtype: 'button',
		handler: fnCekRemove
	};
	
	var winMan = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: false,
		frame: false,
		height: 100,
		layout: 'fit',
		minHeight: 100,
		maxHeight: 100,
		minWidth: 500,
		maxWidth: 500,
		title: 'Enter Branch Manager Name',
		width: 500,
		items: [
			Ext.create('Ext.form.Panel', {
				bodyStyle: 'padding: 5px; background-color: '.concat(gBasePanel),
				fieldDefaults: {
					labelAlign: 'right',
					labelSeparator: '',
					labelWidth: 80,
					msgTarget: 'side'
				},
				items: [{
					anchor: '100%',
					id: 'txtInputMan',
					name: 'txtInputMan',
					value: '',
					xtype: 'textfield'
				}]
			})
		],
		buttons: [{
			text: 'OK',
			handler: function() {
				vMask.hide();
				winMan.hide();
				Ext.getCmp('txtMan').setValue(Ext.getCmp('txtInputMan').getValue());
				fnPrintAL();
			}
		},{
			text: 'Cancel',
			handler: function() {
				vMask.hide();
				winMan.hide();
			}
		}]
	});

	function fnCekSave() {
		if (this.up('form').getForm().isValid()) {
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'jual/jual_ceksave',
				params: {
					'fs_refno': Ext.getCmp('cboRefno').getValue(),
					'fs_rangka': Ext.getCmp('cboRangka').getValue(),
					'fs_mesin': Ext.getCmp('txtMesin').getValue(),
					'fs_nm_cust': Ext.getCmp('txtCust').getValue(),
					'fs_idcard': Ext.getCmp('txtID').getValue(),
					'fs_addr': Ext.getCmp('txtAddr').getValue(),
					'fs_tlp': Ext.getCmp('txtTlp').getValue()
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
							fnSaveJual();
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
										fnSaveJual();
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

	function fnSaveJual() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'jual/jual_save',
			params: {
				'fs_kd_cust': Ext.getCmp('txtCustCd').getValue(),
				'fs_count_cust': Ext.getCmp('txtCount').getValue(),
				'fs_nm_cust': Ext.getCmp('txtCust').getValue(),
				'fs_idcard': Ext.getCmp('txtID').getValue(),
				'fs_addr': Ext.getCmp('txtAddr').getValue(),
				'fs_refno': Ext.getCmp('cboRefno').getValue(),
				'fd_refno': Ext.Date.format(Ext.getCmp('txtRefnodt').getValue(), 'Ymd'),
				'fs_docno': Ext.getCmp('txtDocno').getValue(),
				'fd_docno': Ext.Date.format(Ext.getCmp('txtDocnodt').getValue(), 'Ymd'),
				'fs_kd_sales': Ext.getCmp('cboSales').getValue(),
				'fs_nm_sales': Ext.getCmp('txtSales').getValue(),
				'fs_kd_survey': Ext.getCmp('cboSurvey').getValue(),
				'fs_nm_survey': Ext.getCmp('txtSurvey').getValue(),
				'fs_kd_term': Ext.getCmp('cboTerm').getValue(),
				'fs_nm_term': Ext.getCmp('txtTerm').getValue(),
				'fs_kd_product': Ext.getCmp('cboProd').getValue(),
				'fs_nm_product': Ext.getCmp('txtProd').getValue(),
				'fs_register': Ext.getCmp('txtReg').getValue(),
				'fs_rangka': Ext.getCmp('cboRangka').getValue(),
				'fs_mesin': Ext.getCmp('txtMesin').getValue(),
				'fs_kd_salesmtd': Ext.getCmp('cboSM').getValue(),
				'fs_nm_salesmtd': Ext.getCmp('txtSM').getValue(),
				'fs_kd_payment': Ext.getCmp('cboPM').getValue(),
				'fs_nm_payment': Ext.getCmp('txtPM').getValue(),
				'fs_kd_acno': Ext.getCmp('cboAcno').getValue(),
				'fs_nm_acno': Ext.getCmp('txtAcno').getValue(),
				'fn_cc': Ext.getCmp('txtCC').getValue(),
				'fn_thn': Ext.getCmp('txtTahun').getValue(),
				'fs_kd_color': Ext.getCmp('cboColor').getValue(),
				'fs_nm_color': Ext.getCmp('txtColor').getValue(),
				'fs_kd_wh': Ext.getCmp('cboWH').getValue(),
				'fs_nm_wh': Ext.getCmp('txtWH').getValue(),
				'fn_harga': Ext.getCmp('txtPrice').getValue(),
				'fn_dpmax': Ext.getCmp('txtDPMax').getValue(),
				'fn_disc': Ext.getCmp('txtDisc').getValue(),
				'fs_refno_dp': Ext.getCmp('cboRefnoDP').getValue(),
				'fs_kd_dp': Ext.getCmp('cboDP').getValue(),
				'fs_nm_dp': Ext.getCmp('txtDP').getValue(),
				'fn_dp': Ext.getCmp('txtNilaiDP').getValue(),
				'fn_discl': Ext.getCmp('txtDiscL').getValue(),
				'fn_instal': Ext.getCmp('txtInstal').getValue(),
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
					
					Ext.getCmp('btnSave').setDisabled(false);
					Ext.getCmp('btnRemove').setDisabled(false);
					Ext.getCmp('btnCreateDP').setDisabled(false);
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
			url: 'jual/jual_cekremove',
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
						fnRemoveJual();
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
									fnRemoveJual();
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

	function fnRemoveJual() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'jual/jual_remove',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue(),
				'fs_rangka': Ext.getCmp('cboRangka').getValue(),
				'fs_mesin': Ext.getCmp('txtMesin').getValue()
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
					Ext.getCmp('cboRefno').setValue('');
					Ext.getCmp('txtReg').setValue('');
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

	function fnPrintSO() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'jual/printso',
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
						title: 'Sales Order',
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

	function fnPrintDO() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'jual/printdo',
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
						title: 'Delivery Order',
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

	function fnPrintInvoice() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'jual/printinvoice',
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
						title: 'Invoice',
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

	function fnPrintKwitansiDP() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'jual/printkwitansidp',
			params: {
				'fs_refno': Ext.getCmp('cboRefnoDP').getValue()
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
						title: 'Down Payment',
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

	function fnPrintFidusia() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'jual/printfidusia',
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
						title: 'Fidusia',
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

	function fnPrintSI() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'jual/printsi',
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
						title: 'Sales Invoice',
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

	function fnPrintAL() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'jual/printsrtpernyataan',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue(),
				'fs_nama': '',
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
					var xfilexls = xtext.nmfilexls.trim();
					
					vMask.show();
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
		Ext.getCmp('cboCust').setValue('');
		Ext.getCmp('txtCustCd').setValue('');
		Ext.getCmp('txtCount').setValue('');
		Ext.getCmp('txtCust').setValue('');
		Ext.getCmp('txtTlp').setValue('');
		Ext.getCmp('cboRefno').setValue('');
		Ext.getCmp('txtRefnodt').setValue(new Date());
		Ext.getCmp('txtDocno').setValue('');
		Ext.getCmp('txtDocnodt').setValue(new Date());
		Ext.getCmp('cboSales').setValue('');
		Ext.getCmp('txtSales').setValue('');
		Ext.getCmp('cboSurvey').setValue('');
		Ext.getCmp('txtSurvey').setValue('');
		Ext.getCmp('txtID').setValue('');
		Ext.getCmp('txtAddr').setValue('');
		Ext.getCmp('txtQQ').setValue('');
		Ext.getCmp('txtAddrQQ').setValue('');
		Ext.getCmp('cboTerm').setValue('');
		Ext.getCmp('txtTerm').setValue('');
		Ext.getCmp('cboProd').setValue('');
		Ext.getCmp('txtProd').setValue('');
		Ext.getCmp('txtReg').setValue('');
		Ext.getCmp('cboRangka').setValue('');
		Ext.getCmp('txtMesin').setValue('');
		Ext.getCmp('cboSM').setValue('');
		Ext.getCmp('txtSM').setValue('');
		Ext.getCmp('cboPM').setValue('');
		Ext.getCmp('txtPM').setValue('');
		Ext.getCmp('cboAcno').setValue('');
		Ext.getCmp('txtAcno').setValue('');
		Ext.getCmp('txtCC').setValue('0');
		Ext.getCmp('txtTahun').setValue(Ext.Date.format(new Date(), 'Y'));
		Ext.getCmp('cboColor').setValue('');
		Ext.getCmp('txtColor').setValue('');
		Ext.getCmp('cboWH').setValue('');
		Ext.getCmp('txtWH').setValue('');
		Ext.getCmp('txtPrice').setValue('0');
		Ext.getCmp('txtDPMax').setValue('0');
		Ext.getCmp('txtDisc').setValue('0');
		Ext.getCmp('cboDP').setValue('');
		Ext.getCmp('txtDP').setValue('');
		Ext.getCmp('txtNilaiDP').setValue('0');
		Ext.getCmp('txtDiscL').setValue('0');
		Ext.getCmp('txtTotal').setValue('0');
		Ext.getCmp('txtInstal').setValue('0');
		
		Ext.getCmp('txtInputCust').setValue('');
		Ext.getCmp('txtInputID').setValue('');
		Ext.getCmp('txtInputAddr').setValue('');
		Ext.getCmp('txtInputTlp').setValue('');
		
		Ext.getCmp('cboRefnoDP').setValue('');
		Ext.getCmp('txtInputDt').setValue(new Date());
		Ext.getCmp('cboInputCustDP').setValue('');
		Ext.getCmp('txtInputCustDP').setValue('');
		Ext.getCmp('txtInputNote').setValue('');
		Ext.getCmp('txtInputDP').setValue(0);
		
		Ext.getCmp('btnSave').setDisabled(false);
		Ext.getCmp('btnRemove').setDisabled(false);
		Ext.getCmp('btnCreateDP').setDisabled(false);
		Ext.getCmp('btnDP').setDisabled(false);
	}

	var frmSales = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Sales Form',
		width: 950,
		items: [{
			activeTab: 0,
			bodyStyle: 'padding: 5px; background-color: '.concat(gBasePanel),
			border: false,
			plain: true,
			xtype: 'tabpanel',
			items: [{
				id: 'tab1',
				bodyStyle: 'background-color: '.concat(gBasePanel),
				border: false,
				frame: false,
				title: 'Form',
				xtype: 'form',
				items: [{
					fieldDefaults: {
						labelAlign: 'right',
						labelSeparator: '',
						msgTarget: 'side'
					},
					style: 'padding: 2px;',
					xtype: 'fieldset',
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
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											cboCust,
											txtCustCd,
											txtCount
										]
									},{
										flex: 1.5,
										layout: 'anchor',
										xtype: 'container',
										items: [{
											anchor: '100%',
											layout: 'hbox',
											xtype: 'container',
											items: [{
												flex: 1.7,
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
													btnEditCust
												]
											},{
												flex: 1.2,
												layout: 'anchor',
												xtype: 'container',
												items: [
													btnCreateCust
												]
											}]
										}]
									}]
								},{
									anchor: '95%',
									layout: 'hbox',
									xtype: 'container',
									items: [{
										flex: 2.1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											cboRefno,
											txtDocno
										]
									},{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtRefnodt,
											txtDocnodt
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
											cboSales,
											cboSurvey
										]
									},{
										flex: 1.7,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtSales,
											txtSurvey
										]
									}]
								}]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									txtID,
									txtAddr,
									txtTlp,
									txtQQ,
									txtAddrQQ,
								{
									anchor: '80%',
									layout: 'hbox',
									xtype: 'container',
									items: [{
										flex: 1.3,
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
								}]
							}]
						}]
					},{
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
									anchor: '95%',
									layout: 'hbox',
									xtype: 'container',
									items: [{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											cboProd
										]
									},{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtProd
										]
									}]
								},
									txtReg,
									cboRangka,
									txtMesin
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [{
									anchor: '60%',
									layout: 'hbox',
									xtype: 'container',
									items: [{
										flex: 1.5,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtCC
										]
									},{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtTahun
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
											cboColor,
											cboWH
										]
									},{
										flex: 1.4,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtColor,
											txtWH
										]
									}]
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
									anchor: '95%',
									layout: 'hbox',
									xtype: 'container',
									items: [{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											cboSM
										]
									},{
										flex: 1.4,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtSM
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
										flex: 1.2,
										layout: 'anchor',
										xtype: 'container'
									},{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtDPMax
										]
									}]
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
									anchor: '95%',
									layout: 'hbox',
									xtype: 'container',
									items: [{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											cboPM,
											cboAcno
										]
									},{
										flex: 1.4,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtPM,
											txtAcno
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
										flex: 1.2,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtPrice,
											txtDisc
										]
									},{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtDiscL,
											txtInstal
										]
									}]
								}]
							}]
						}]
					},{
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
									anchor: '95%',
									layout: 'hbox',
									xtype: 'container',
									items: [{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											cboDP
										]
									},{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtDP
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
										flex: 1.2,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtNilaiDP
										]
									},{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtTotal
										]
									}]
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
									anchor: '95%',
									layout: 'hbox',
									xtype: 'container',
									items: [{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [{
											layout: 'anchor',
											xtype: 'container',
											items: [
												btnCreateDP
											]
										}]
									},{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtMan
										]
									}]
								}]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container'
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
									anchor: '95%',
									style: 'padding: 5px;',
									title: 'Leasing Document Print',
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
												btnDP
											]
										},{
											flex: 1,
											layout: 'anchor',
											xtype: 'container',
											items: [
												btnFidusia
											]
										},{
											flex: 1,
											layout: 'anchor',
											xtype: 'container',
											items: [
												btnSI
											]
										},{
											flex: 1,
											layout: 'anchor',
											xtype: 'container',
											items: [
												btnAgreement
											]
										},{
											flex: 1,
											layout: 'anchor',
											xtype: 'container',
											items: [
												btnReceipt
											]
										}]
									}]
								}]
							},{
								autoEl: {tag: 'center'},
								flex: 1.1,
								layout: 'anchor',
								xtype: 'container',
								items: [{
									anchor: '100%',
									style: 'padding: 5px;',
									title: 'Document Save & Print',
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
												btnSave
											]
										},{
											flex: 1,
											layout: 'anchor',
											xtype: 'container',
											items: [
												btnPrintSO
											]
										},{
											flex: 1,
											layout: 'anchor',
											xtype: 'container',
											items: [
												btnPrintDO
											]
										},{
											flex: 1,
											layout: 'anchor',
											xtype: 'container',
											items: [
												btnReset
											]
										},{
											flex: 1,
											layout: 'anchor',
											xtype: 'container',
											items: [
												btnRemove
											]
										}]
									}]
								}]
							}]
						}]
					}]
				}]
			},{
				id: 'tab2',
				bodyStyle: 'background-color: '.concat(gBasePanel),
				border: false,
				frame: false,
				title: 'Data',
				xtype: 'form',
				items: [
					gridDetil
				]
			}]
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmSales
	});
	
	function fnMaskShow() {
		frmSales.mask('Please wait...');
	}

	function fnMaskHide() {
		frmSales.unmask();
	}

	var vMask2 = new Ext.LoadMask({
		msg: 'Please wait...',
		target: winCust
	});
	
	function fnMaskShow2() {
		winCust.mask('Please wait...');
	}

	function fnMaskHide2() {
		winCust.unmask();
	}

	var vMask3 = new Ext.LoadMask({
		msg: 'Please wait...',
		target: winDP
	});
	
	function fnMaskShow3() {
		winDP.mask('Please wait...');
	}

	function fnMaskHide3() {
		winDP.unmask();
	}
	
	frmSales.render(Ext.getBody());
	Ext.get('loading').destroy();
});
