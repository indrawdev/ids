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

	function trim(text) {
		return text.replace(/^\s+|\s+$/gm, '');
	}

	function tglDMY(text) {
		var x = '-';
		return text.substr(0,2).concat(x,text.substr(3,2),x,text.substr(6,4));
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
		Ext.Ajax.request({
			method: 'POST',
			url: 'jualunit/cust_ceksave',
			params: {
				'fs_nm_cust': Ext.getCmp('txtInputCust').getValue(),
				'fs_kd_id': Ext.getCmp('txtInputID').getValue(),
				'fs_addr': Ext.getCmp('txtInputAddr').getValue(),
				'fs_tlp': Ext.getCmp('txtInputTlp').getValue()
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				
				if (xtext.sukses === false) {
					Ext.Msg.show({
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
						Ext.Msg.show({
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
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Saving Failed, Connection Failed!!',
					title: 'IDS'
				});
			}
		});
	}

	function fnSaveCust() {
		Ext.Ajax.on('beforerequest', vMask2.show, vMask2);
		Ext.Ajax.on('requestcomplete', vMask2.hide, vMask2);
		Ext.Ajax.on('requestexception', vMask2.hide, vMask2);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'jualunit/cust_save',
			params: {
				'fs_nm_cust': Ext.getCmp('txtInputCust').getValue(),
				'fs_kd_id': Ext.getCmp('txtInputID').getValue(),
				'fs_addr': Ext.getCmp('txtInputAddr').getValue(),
				'fs_tlp': Ext.getCmp('txtInputTlp').getValue()
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: xtext.hasil,
					title: 'IDS'
				});
				
				if (xtext.sukses === true) {
					Ext.getCmp('cboCust').setValue(trim(xtext.kdcust).concat(trim(xtext.kdcount)));
					Ext.getCmp('txtCustCd').setValue(trim(xtext.kdcust));
					Ext.getCmp('txtCount').setValue(trim(xtext.kdcount));
					Ext.getCmp('txtCust').setValue(trim(xtext.nmcust));
					Ext.getCmp('txtID').setValue(trim(xtext.kdid));
					
					Ext.getCmp('txtAddr').setValue(trim(xtext.almt));
					Ext.getCmp('txtTlp').setValue(trim(xtext.tlp));
					Ext.getCmp('txtInputCust').setValue('');
					Ext.getCmp('txtInputID').setValue('');
					Ext.getCmp('txtInputAddr').setValue('');
					
					Ext.getCmp('txtInputTlp').setValue('');
					vMask.hide();
					winCust.hide();
				}
			},
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Saving Failed, Connection Failed!!',
					title: 'IDS'
				});
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
				bodyStyle: 'padding: 5px;',
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
					growMin: 35,
					growMax: 35,
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
			url: 'jualunit/dp_trs'
		}
	});

	grupTrsDP.on('beforeload', function(store, operation, eOpts) {
		operation.params = {
			'fs_kd_cust': Ext.getCmp('cboInputCustDP').getValue()
		};
	}, this);

	var winGrid12 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupTrsDP,
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
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Ref No", dataIndex: 'fs_refno', menuDisabled: true, width: 170},
			{text: "Ref Date", dataIndex: 'fd_refno', menuDisabled: true, width: 80},
			{text: "Dept Cd", dataIndex: 'fs_kd_dept', menuDisabled: true, hidden: true},
			{text: "Dept", dataIndex: 'fs_nm_dept', menuDisabled: true, width: 100},
			{text: "Doc No", dataIndex: 'fs_docno', menuDisabled: true, width: 150},
			{text: "Cust Cd", dataIndex: 'fs_kd_cust', menuDisabled: true, hidden: true},
			{text: "Note", dataIndex: 'fs_note', menuDisabled: true, hidden: true},
			{text: "Amount", dataIndex: 'fn_amt', menuDisabled: true, hidden: true}
		],
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
			listeners: {
				render: gridTooltipSearch
			}
		}
	});

	var winCari12 = Ext.create('Ext.menu.Menu', {
		bodyPadding: '5 0 5 0',
		plain: true
	});
	winCari12.add(winGrid12);

	Ext.define('Ext.ux.SearchTrsDP', {
		alias: 'widget.searchTrsDP',
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
			grupTrsDP.load();
			winGrid12.show();
			winCari12.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
		}
	});

	function fnCekSaveDP() {
		Ext.Ajax.request({
			method: 'POST',
			url: 'jualunit/dp_ceksave',
			params: {
				'fs_refno': Ext.getCmp('cboRefnoDP').getValue(),
				'fn_dp': Ext.getCmp('txtInputDP').getValue()
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				
				if (xtext.sukses === false) {
					Ext.Msg.show({
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
						Ext.Msg.show({
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
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Saving Failed, Connection Failed!!',
					title: 'IDS'
				});
			}
		});
	}

	function fnSaveDP() {
		Ext.Ajax.on('beforerequest', vMask3.show, vMask3);
		Ext.Ajax.on('requestcomplete', vMask3.hide, vMask3);
		Ext.Ajax.on('requestexception', vMask3.hide, vMask3);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'jualunit/dp_save',
			params: {
				'fs_refno': Ext.getCmp('cboRefnoDP').getValue(),
				'fd_refno': Ext.Date.format(Ext.getCmp('txtInputDt').getValue(), 'Ymd'),
				'fs_kd_cust': Ext.getCmp('cboInputCustDP').getValue(),
				'fs_nm_cust': Ext.getCmp('txtInputCustDP').getValue(),
				'fs_note': Ext.getCmp('txtInputNote').getValue(),
				'fn_dp': Ext.getCmp('txtInputDP').getValue()
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: xtext.hasil,
					title: 'IDS'
				});
				
				if (xtext.sukses === true) {
					Ext.getCmp('cboDP').setValue(trim(xtext.kdcust));
					Ext.getCmp('txtDP').setValue(trim(xtext.nmcust));
					Ext.getCmp('txtNilaiDP').setValue(trim(xtext.nilaidp));
					vMask.hide();
					winDP.hide();
				}
			},
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Saving Failed, Connection Failed!!',
					title: 'IDS'
				});
			}
		});
	}

	function fnCekRemoveDP() {
		Ext.Ajax.request({
			method: 'POST',
			url: 'jualunit/dp_cekremove',
			params: {
				'fs_refno': Ext.getCmp('cboRefnoDP').getValue()
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				
				if (xtext.sukses === false) {
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});
				}
				else {
					if (xtext.sukses === true) {
						Ext.Msg.show({
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
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Saving Failed, Connection Failed!!',
					title: 'IDS'
				});
			}
		});
	}

	function fnRemoveDP() {
		Ext.Ajax.on('beforerequest', vMask3.show, vMask3);
		Ext.Ajax.on('requestcomplete', vMask3.hide, vMask3);
		Ext.Ajax.on('requestexception', vMask3.hide, vMask3);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'jualunit/dp_remove',
			params: {
				'fs_refno': Ext.getCmp('cboRefnoDP').getValue()
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				
				Ext.Msg.show({
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
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Remove Failed, Connection Failed!!',
					title: 'IDS'
				});
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
				bodyStyle: 'padding: 5px;',
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
							xtype: 'searchTrsDP'
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
					growMin: 35,
					growMax: 35,
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
			url: 'jualunit/cust_kode'
		}
	});

	grupCust.on('beforeload', function(store, operation, eOpts) {
		operation.params = {
			'fs_code': Ext.getCmp('cboCust').getValue(),
			'fs_nm_code': Ext.getCmp('cboCust').getValue()
		};
	}, this);

	var winGrid = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupCust,
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
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Code", dataIndex: 'fs_kd_cust', menuDisabled: true, hidden: true},
			{text: "Count", dataIndex: 'fs_count', menuDisabled: true, hidden: true},
			{text: "Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 250},
			{text: "Phone", dataIndex: 'fs_tlp', menuDisabled: true, width: 100},
			{text: "IdCard", dataIndex: 'fs_idcard', menuDisabled: true, hidden: true},
			{text: "Address", dataIndex: 'fs_addr', menuDisabled: true, width: 500}
		],
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

	Ext.define('Ext.ux.SearchCust', {
		alias: 'widget.searchCust',
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
			grupCust.load();
			winGrid.show();
			winCari.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
		}
	});

	var grupRefno = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_cussup','fs_nm_cussup','fs_addr','fs_idcard','fs_refno',
			'fd_refno','fs_docno','fd_docno','fs_kd_sales','fs_nm_sales',
			'fs_kd_surveyor','fs_nm_surveyor','fs_kd_term','fs_nm_term','fs_kd_product',
			'fs_nm_product','fs_rangka','fs_mesin','fs_cc','fs_thn',
			'fs_kd_warna','fs_nm_warna','fs_kd_wh','fs_nm_wh','fs_kd_salesmtd',
			'fs_nm_salesmtd','fs_kd_payment','fs_nm_payment','fs_kd_acno','fs_nm_acno',
			'fn_unitprice','fn_dpmax','fn_disc','fs_kd_dp','fs_nm_dp',
			'fn_dp','fn_subsidi','fn_install','fn_total','fs_refnobeli',
			'fs_register','fs_kd_cust','fs_count_cust', 'fs_nm_qq', 'fs_addr_qq'
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
			url: 'jualunit/refno'
		}
	});

	grupRefno.on('beforeload', function(store, operation, eOpts) {
		operation.params = {
			'fs_refno': Ext.getCmp('cboRefno').getValue(),
			'fs_docno': Ext.getCmp('cboRefno').getValue(),
			'fs_nm_cussup': Ext.getCmp('cboRefno').getValue()
		};
	}, this);

	var winGrid2 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
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
					winCari2.hide();
				}
			}]
		}),
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Cust Cd", dataIndex: 'fs_kd_cussup', menuDisabled: true, hidden: true},
			{text: "Customer", dataIndex: 'fs_nm_cussup', menuDisabled: true, width: 150},
			{text: "Address", dataIndex: 'fs_addr', menuDisabled: true, hidden: true},
			{text: "ID Card", dataIndex: 'fs_idcard', menuDisabled: true, hidden: true},
			{text: "Ref. No", dataIndex: 'fs_refno', menuDisabled: true, width: 150},
			{text: "Ref. Date", dataIndex: 'fd_refno', menuDisabled: true, width: 80},
			{text: "Doc. No", dataIndex: 'fs_docno', menuDisabled: true, width: 150},
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
			{text: "Refno Purchase", dataIndex: 'fs_refnobeli', hidden: true},
			
			{text: "Register", dataIndex: 'fs_register', hidden: true},
			{text: "Cust Cd", dataIndex: 'fs_kd_cust', hidden: true},
			{text: "Cust Count", dataIndex: 'fs_count_cust', hidden: true},
			{text: "QQ Name", dataIndex: 'fs_nm_qq', hidden: true},
			{text: "QQ Addr", dataIndex: 'fs_addr_qq', hidden: true}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboCust').setValue(record.get('fs_kd_cussup'));
				Ext.getCmp('txtCust').setValue(record.get('fs_nm_cussup'));
				Ext.getCmp('txtAddr').setValue(record.get('fs_addr'));
				Ext.getCmp('txtID').setValue(record.get('fs_idcard'));
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
				Ext.getCmp('cboProd').setValue(record.get('fs_kd_product'));
				
				Ext.getCmp('txtProd').setValue(record.get('fs_nm_product'));
				Ext.getCmp('txtRangka').setValue(record.get('fs_rangka'));
				Ext.getCmp('txtMesin').setValue(record.get('fs_mesin'));
				Ext.getCmp('txtCC').setValue(record.get('fs_cc'));
				Ext.getCmp('txtYear').setValue(record.get('fs_thn'));
				
				Ext.getCmp('cboColor').setValue(record.get('fs_kd_warna'));
				Ext.getCmp('txtColor').setValue(record.get('fs_nm_warna'));
				Ext.getCmp('cboWH').setValue(record.get('fs_kd_wh'));
				Ext.getCmp('txtWH').setValue(record.get('fs_nm_wh'));
				Ext.getCmp('cboSM').setValue(record.get('fs_kd_salesmtd'));
				
				Ext.getCmp('txtSM').setValue(record.get('fs_nm_salesmtd'));
				Ext.getCmp('cboPM').setValue(record.get('fs_kd_payment'));
				Ext.getCmp('txtPM').setValue(record.get('fs_nm_payment'));
				Ext.getCmp('cboAcno').setValue(record.get('fs_kd_acno'));
				Ext.getCmp('txtAcno').setValue(record.get('fs_nm_acno'));
				
				Ext.getCmp('txtPrice').setValue(record.get('fn_unitprice'));
				Ext.getCmp('txtDPMax').setValue(record.get('fn_dpmax'));
				Ext.getCmp('txtDisc').setValue(record.get('fn_disc'));
				Ext.getCmp('cboDP').setValue(record.get('fs_kd_dp'));
				Ext.getCmp('txtDP').setValue(record.get('fs_nm_dp'));
				
				Ext.getCmp('txtNilaiDP').setValue(record.get('fn_dp'));
				Ext.getCmp('txtDiscL').setValue(record.get('fn_subsidi'));
				Ext.getCmp('txtInstal').setValue(record.get('fn_install'));
				Ext.getCmp('txtTotal').setValue(record.get('fn_total'));
				Ext.getCmp('txtRefnoBeli').setValue(record.get('fs_refnobeli'));
				
				Ext.getCmp('txtReg').setValue(record.get('fs_register'));
				Ext.getCmp('txtCustCd').setValue(record.get('fs_kd_cust'));
				Ext.getCmp('txtCount').setValue(record.get('fs_count_cust'));
				Ext.getCmp('txtQQ').setValue(record.get('fs_nm_qq'));
				Ext.getCmp('txtAddrQQ').setValue(record.get('fs_addr_qq'));
				
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

	Ext.define('Ext.ux.SearchRefno', {
		alias: 'widget.searchRefno',
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
			grupRefno.load();
			winGrid2.show();
			winCari2.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
		}
	});

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
			url: 'jualunit/sales'
		}
	});

	grupSales.on('beforeload', function(store, operation, eOpts) {
		operation.params = {
			'fs_code': Ext.getCmp('cboSales').getValue(),
			'fs_nm_code': Ext.getCmp('cboSales').getValue()
		};
	}, this);

	var winGrid3 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupSales,
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
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Salesman Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Salesman Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 500}
		],
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

	Ext.define('Ext.ux.SearchSales', {
		alias: 'widget.searchSales',
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
			grupSales.load();
			winGrid3.show();
			winCari3.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
		}
	});

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
			url: 'jualunit/survey'
		}
	});

	grupSurvey.on('beforeload', function(store, operation, eOpts) {
		operation.params = {
			'fs_code': Ext.getCmp('cboSurvey').getValue(),
			'fs_nm_code': Ext.getCmp('cboSurvey').getValue()
		};
	}, this);

	var winGrid4 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupSurvey,
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
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Surveyor Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Surveyor Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 500}
		],
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

	Ext.define('Ext.ux.SearchSurvey', {
		alias: 'widget.searchSurvey',
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
			grupSurvey.load();
			winGrid4.show();
			winCari4.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
		}
	});

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
			url: 'jualunit/term'
		}
	});

	grupTerm.on('beforeload', function(store, operation, eOpts) {
		operation.params = {
			'fs_kd_term': Ext.getCmp('cboTerm').getValue(),
			'fs_nm_term': Ext.getCmp('cboTerm').getValue()
		};
	}, this);

	var winGrid5 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupTerm,
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
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Term Code", dataIndex: 'fs_kd_term', menuDisabled: true, width: 100},
			{text: "Term Name", dataIndex: 'fs_nm_term', menuDisabled: true, width: 500}
		],
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

	Ext.define('Ext.ux.SearchTerm', {
		alias: 'widget.searchTerm',
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
			grupTerm.load();
			winGrid5.show();
			winCari5.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
		}
	});

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
			url: 'jualunit/product'
		}
	});

	grupProd.on('beforeload', function(store, operation, eOpts) {
		operation.params = {
			'fs_kd_product': Ext.getCmp('cboProd').getValue(),
			'fs_nm_product': Ext.getCmp('cboProd').getValue()
		};
	}, this);

	var winGrid6 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupProd,
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
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Product Code", dataIndex: 'fs_kd_product', menuDisabled: true, width: 100},
			{text: "Product Name", dataIndex: 'fs_nm_product', menuDisabled: true, width: 500}
		],
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
			listeners: {
				render: gridTooltipSearch
			}
		}
	});

	var winCari6 = Ext.create('Ext.menu.Menu', {
		bodyPadding: '5 0 5 0',
		plain: true
	});
	winCari6.add(winGrid6);

	Ext.define('Ext.ux.SearchProd', {
		alias: 'widget.searchProd',
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
			grupProd.load();
			winGrid6.show();
			winCari6.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
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
			url: 'jualunit/color'
		}
	});

	grupColor.on('beforeload', function(store, operation, eOpts) {
		operation.params = {
			'fs_kd_vareable': Ext.getCmp('cboColor').getValue(),
			'fs_nm_vareable': Ext.getCmp('cboColor').getValue()
		};
	}, this);

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
			{text: "Color Name", dataIndex: 'fs_nm_vareable', menuDisabled: true, width: 500}
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

	var winCari7 = Ext.create('Ext.menu.Menu', {
		bodyPadding: '5 0 5 0',
		plain: true
	});
	winCari7.add(winGrid7);

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
			winGrid7.show();
			winCari7.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
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
			url: 'jualunit/wh'
		}
	});

	grupWH.on('beforeload', function(store, operation, eOpts) {
		operation.params = {
			'fs_code': Ext.getCmp('cboWH').getValue(),
			'fs_nm_code': Ext.getCmp('cboWH').getValue()
		};
	}, this);

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
			{text: "Warehouse Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 500}
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

	var winCari8 = Ext.create('Ext.menu.Menu', {
		bodyPadding: '5 0 5 0',
		plain: true
	});
	winCari8.add(winGrid8);

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
			grupWH.load();
			winGrid8.show();
			winCari8.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
		}
	});

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
			url: 'jualunit/sales_mtd'
		}
	});

	grupSalesMtd.on('beforeload', function(store, operation, eOpts) {
		operation.params = {
			'fs_kd_vareable': Ext.getCmp('cboSM').getValue(),
			'fs_nm_vareable': Ext.getCmp('cboSM').getValue()
		};
	}, this);

	var winGrid9 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupSalesMtd,
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
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Product Code", dataIndex: 'fs_kd_vareable', menuDisabled: true, width: 100},
			{text: "Product Name", dataIndex: 'fs_nm_vareable', menuDisabled: true, width: 500}
		],
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
			listeners: {
				render: gridTooltipSearch
			}
		}
	});

	var winCari9 = Ext.create('Ext.menu.Menu', {
		bodyPadding: '5 0 5 0',
		plain: true
	});
	winCari9.add(winGrid9);

	Ext.define('Ext.ux.SearchSalesMtd', {
		alias: 'widget.searchSalesMtd',
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
			grupSalesMtd.load();
			winGrid9.show();
			winCari9.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
		}
	});

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
			url: 'jualunit/payment'
		}
	});

	grupPM.on('beforeload', function(store, operation, eOpts) {
		operation.params = {
			'fs_code': Ext.getCmp('cboPM').getValue(),
			'fs_nm_code': Ext.getCmp('cboPM').getValue()
		};
	}, this);

	var winGrid10 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupPM,
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
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Payment Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Payment Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 500},
			{text: "Acc. No", dataIndex: 'fs_kd_acno', menuDisabled: true, hidden: true},
			{text: "Acc. Name", dataIndex: 'fs_nm_acno', menuDisabled: true, hidden: true}
		],
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
			listeners: {
				render: gridTooltipSearch
			}
		}
	});

	var winCari10 = Ext.create('Ext.menu.Menu', {
		bodyPadding: '5 0 5 0',
		plain: true
	});
	winCari10.add(winGrid10);

	Ext.define('Ext.ux.SearchPM', {
		alias: 'widget.searchPM',
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
			grupPM.load();
			winGrid10.show();
			winCari10.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
		}
	});

	var grupDP = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_code','fs_nm_code','fn_dp','fs_nm_dept'],
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
			url: 'jualunit/dp'
		}
	});

	// grupDP.on('beforeload', function(store, operation, eOpts) {
		// operation.params = {
			// 'fs_code': Ext.getCmp('cboDP').getValue()
		// }
	// }, this);

	var winGrid11 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupDP,
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
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
			{text: "Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 500},
			{text: "Values", dataIndex: 'fn_dp', menuDisabled: true, width: 100},
			{text: "Dept", dataIndex: 'fs_nm_dept', menuDisabled: true, hidden: true}
		],
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
			listeners: {
				render: gridTooltipSearch
			}
		}
	});

	var winCari11 = Ext.create('Ext.menu.Menu', {
		bodyPadding: '5 0 5 0',
		plain: true
	});
	winCari11.add(winGrid11);

	Ext.define('Ext.ux.SearchDP', {
		alias: 'widget.searchDP',
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
			grupDP.load();
			winGrid11.show();
			winCari11.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
		}
	});

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
				bodyStyle: 'padding: 5px;',
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
			Ext.Ajax.request({
				method: 'POST',
				url: 'jualunit/jual_ceksave',
				params: {
					'fs_refno': Ext.getCmp('cboRefno').getValue(),// un remark klo udah bs simpan beli
					// 'fs_refno': Ext.getCmp('txtRefnoBeli').getValue(),
					'fs_rangka': Ext.getCmp('txtRangka').getValue(),
					'fs_mesin': Ext.getCmp('txtMesin').getValue()
				},
				success: function(response, opts) {
					var xtext = Ext.decode(response.responseText);
					
					if (xtext.sukses === false) {
						Ext.Msg.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.MessageBox.INFO,
							msg: xtext.hasil,
							title: 'IDS'
						});
					}
					else {
						if (xtext.sukses === true && xtext.hasil == 'lanjut') {
							fnSaveBeli();
						}
						else {
							Ext.Msg.show({
								buttons: Ext.MessageBox.YESNO,
								closable: false,
								icon: Ext.Msg.QUESTION,
								msg: xtext.hasil,
								title: 'IDS',
								fn: function(btn) {
									if (btn == 'yes') {
										fnSaveBeli();
									}
								}
							});
						}
					}
				},
				failure: function(response, opts) {
					var xtext = Ext.decode(response.responseText);
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: 'Saving Failed, Connection Failed!!',
						title: 'IDS'
					});
				}
			});
		}
	}

	function fnSaveBeli() {
		Ext.Ajax.on('beforerequest', vMask.show, vMask);
		Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
		Ext.Ajax.on('requestexception', vMask.hide, vMask);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'jualunit/beli_save',
			params: {
				'fs_kd_cust': Ext.getCmp('txtCustCd').getValue(),
				'fs_count_cust': Ext.getCmp('txtCount').getValue(),
				'fs_nm_cust': Ext.getCmp('txtCust').getValue(),
				'fs_idcard': Ext.getCmp('txtID').getValue(),
				'fs_addr': Ext.getCmp('txtAddr').getValue(),
				'fs_refno': Ext.getCmp('txtRefnoBeli').getValue(),//refno beli
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
				'fs_rangka': Ext.getCmp('txtRangka').getValue(),
				'fs_mesin': Ext.getCmp('txtMesin').getValue(),
				'fs_kd_salesmtd': Ext.getCmp('cboSM').getValue(),
				'fs_nm_salesmtd': Ext.getCmp('txtSM').getValue(),
				'fs_kd_payment': Ext.getCmp('cboPM').getValue(),
				'fs_nm_payment': Ext.getCmp('txtPM').getValue(),
				'fs_kd_acno': Ext.getCmp('cboAcno').getValue(),
				'fs_nm_acno': Ext.getCmp('txtAcno').getValue(),
				'fn_cc': Ext.getCmp('txtCC').getValue(),
				'fn_thn': Ext.getCmp('txtYear').getValue(),
				'fs_kd_color': Ext.getCmp('cboColor').getValue(),
				'fs_nm_color': Ext.getCmp('txtColor').getValue(),
				'fs_kd_wh': Ext.getCmp('cboWH').getValue(),
				'fs_nm_wh': Ext.getCmp('txtWH').getValue(),
				'fn_harga': Ext.getCmp('txtPrice').getValue(),
				'fn_dpmax': Ext.getCmp('txtDPMax').getValue(),
				'fn_disc': Ext.getCmp('txtDisc').getValue(),
				'fs_kd_dp': Ext.getCmp('cboDP').getValue(),
				'fs_nm_dp': Ext.getCmp('txtDP').getValue(),
				'fn_dp': Ext.getCmp('txtNilaiDP').getValue(),
				'fn_discl': Ext.getCmp('txtDiscL').getValue(),
				'fn_instal': Ext.getCmp('txtInstal').getValue(),
				'fn_total': Ext.getCmp('txtTotal').getValue()
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				
				if (xtext.sukses === true) {/*
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});*/
					Ext.getCmp('txtRefnoBeli').setValue(trim(xtext.refno));
					Ext.getCmp('txtReg').setValue(trim(xtext.reg));
					fnSaveJual();
				}
				
				if (xtext.sukses === false) {
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});
				}
			},
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Saving Failed, Connection Failed!!',
					title: 'IDS'
				});
			}
		});
	}

	function fnSaveJual() {
		Ext.Ajax.on('beforerequest', vMask.show, vMask);
		Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
		Ext.Ajax.on('requestexception', vMask.hide, vMask);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'jualunit/jual_save',
			params: {
				'fs_kd_cust': Ext.getCmp('txtCustCd').getValue(),
				'fs_count_cust': Ext.getCmp('txtCount').getValue(),
				'fs_nm_cust': Ext.getCmp('txtCust').getValue(),
				'fs_idcard': Ext.getCmp('txtID').getValue(),
				'fs_addr': Ext.getCmp('txtAddr').getValue(),
				'fs_nm_qq': Ext.getCmp('txtQQ').getValue(),
				'fs_addr_qq': Ext.getCmp('txtAddrQQ').getValue(),
				'fs_refno': Ext.getCmp('cboRefno').getValue(),//refno jual
				'fs_refnobeli': Ext.getCmp('txtRefnoBeli').getValue(),//refno beli
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
				'fs_rangka': Ext.getCmp('txtRangka').getValue(),
				'fs_mesin': Ext.getCmp('txtMesin').getValue(),
				'fs_kd_salesmtd': Ext.getCmp('cboSM').getValue(),
				'fs_nm_salesmtd': Ext.getCmp('txtSM').getValue(),
				'fs_kd_payment': Ext.getCmp('cboPM').getValue(),
				'fs_nm_payment': Ext.getCmp('txtPM').getValue(),
				'fs_kd_acno': Ext.getCmp('cboAcno').getValue(),
				'fs_nm_acno': Ext.getCmp('txtAcno').getValue(),
				'fn_cc': Ext.getCmp('txtCC').getValue(),
				'fn_thn': Ext.getCmp('txtYear').getValue(),
				'fs_kd_color': Ext.getCmp('cboColor').getValue(),
				'fs_nm_color': Ext.getCmp('txtColor').getValue(),
				'fs_kd_wh': Ext.getCmp('cboWH').getValue(),
				'fs_nm_wh': Ext.getCmp('txtWH').getValue(),
				'fn_harga': Ext.getCmp('txtPrice').getValue(),
				'fn_dpmax': Ext.getCmp('txtDPMax').getValue(),
				'fn_disc': Ext.getCmp('txtDisc').getValue(),
				'fs_kd_dp': Ext.getCmp('cboDP').getValue(),
				'fs_nm_dp': Ext.getCmp('txtDP').getValue(),
				'fn_dp': Ext.getCmp('txtNilaiDP').getValue(),
				'fn_discl': Ext.getCmp('txtDiscL').getValue(),
				'fn_instal': Ext.getCmp('txtInstal').getValue(),
				'fn_total': Ext.getCmp('txtTotal').getValue()
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				
				if (xtext.sukses === true) {
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});
					Ext.getCmp('cboRefno').setValue(trim(xtext.refno));
					Ext.getCmp('txtRefnoBeli').setValue(trim(xtext.refnobeli));
				}
				
				if (xtext.sukses === false) {
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});
				}
			},
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Saving Failed, Connection Failed!!',
					title: 'IDS'
				});
			}
		});
	}

	function fnCekRemove() {
		Ext.Ajax.request({
			method: 'POST',
			url: 'jualunit/jual_cekremove',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue()
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				
				if (xtext.sukses === false) {
					Ext.Msg.show({
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
						Ext.Msg.show({
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
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Saving Failed, Connection Failed!!',
					title: 'IDS'
				});
			}
		});
	}

	function fnRemoveJual() {
		Ext.Ajax.on('beforerequest', vMask.show, vMask);
		Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
		Ext.Ajax.on('requestexception', vMask.hide, vMask);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'jualunit/jual_remove',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue(),
				'fs_refnobeli': Ext.getCmp('txtRefnoBeli').getValue(),
				'fs_rangka': Ext.getCmp('txtRangka').getValue(),
				'fs_mesin': Ext.getCmp('txtMesin').getValue()
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				
				if (xtext.sukses === true) {
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});
					Ext.getCmp('cboRefno').setValue('');
					Ext.getCmp('txtRefnoBeli').setValue('');
					Ext.getCmp('txtReg').setValue('');
				}
				
				if (xtext.sukses === false) {
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});
				}
			},
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Remove Failed, Connection Failed!!',
					title: 'IDS'
				});
			}
		});
	}

	function fnPrintSO() {
		Ext.Ajax.on('beforerequest', vMask.show, vMask);
		Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
		Ext.Ajax.on('requestexception', vMask.hide, vMask);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'jualunit/printso',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue()
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				/*
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: xtext.hasil,
					title: 'IDS'
				});*/
				
				if (xtext.sukses === true) {
					var xfile = trim(xtext.nmfile);
					
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
						buttons: [{
							text: 'Exit',
							handler: function() {
								winpdf.hide();
							}
						}]
					}).show();
				}
				else {
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});				
				}
			},
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Print Failed, Connection Failed!!',
					title: 'IDS'
				});
			}
		});
	}

	function fnPrintDO() {
		Ext.Ajax.on('beforerequest', vMask.show, vMask);
		Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
		Ext.Ajax.on('requestexception', vMask.hide, vMask);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'jualunit/printdo',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue()
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				/*
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: xtext.hasil,
					title: 'IDS'
				});*/
				
				if (xtext.sukses === true) {
					var xfile = trim(xtext.nmfile);
					
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
						buttons: [{
							text: 'Exit',
							handler: function() {
								winpdf.hide();
							}
						}]
					}).show();
				}
				else {
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});				
				}
			},
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Print Failed, Connection Failed!!',
					title: 'IDS'
				});
			}
		});
	}

	function fnPrintInvoice() {
		Ext.Ajax.on('beforerequest', vMask.show, vMask);
		Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
		Ext.Ajax.on('requestexception', vMask.hide, vMask);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'jualunit/printinvoice',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue()
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				/*
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: xtext.hasil,
					title: 'IDS'
				});*/
				
				if (xtext.sukses === true) {
					var xfile = trim(xtext.nmfile);
					
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
						buttons: [{
							text: 'Exit',
							handler: function() {
								winpdf.hide();
							}
						}]
					}).show();
					
				}
				else {
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});				
				}
			},
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Print Failed, Connection Failed!!',
					title: 'IDS'
				});
			}
		});
	}

	function fnPrintKwitansiDP() {
		Ext.Ajax.on('beforerequest', vMask.show, vMask);
		Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
		Ext.Ajax.on('requestexception', vMask.hide, vMask);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'jualunit/printkwitansidp',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue()
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				/*
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: xtext.hasil,
					title: 'IDS'
				});*/
				
				if (xtext.sukses === true) {
					var xfile = trim(xtext.nmfile);
					
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
						buttons: [{
							text: 'Exit',
							handler: function() {
								winpdf.hide();
							}
						}]
					}).show();
				}
				else {
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});				
				}
			},
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Print Failed, Connection Failed!!',
					title: 'IDS'
				});
			}
		});
	}

	function fnPrintFidusia() {
		Ext.Ajax.on('beforerequest', vMask.show, vMask);
		Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
		Ext.Ajax.on('requestexception', vMask.hide, vMask);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'jualunit/printfidusia',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue()
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				/*
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: xtext.hasil,
					title: 'IDS'
				});*/
				
				if (xtext.sukses === true) {
					var xfile = trim(xtext.nmfile);
					
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
						buttons: [{
							text: 'Exit',
							handler: function() {
								winpdf.hide();
							}
						}]
					}).show();
				}
				else {
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});				
				}
			},
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Print Failed, Connection Failed!!',
					title: 'IDS'
				});
			}
		});
	}

	function fnPrintSI() {
		Ext.Ajax.on('beforerequest', vMask.show, vMask);
		Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
		Ext.Ajax.on('requestexception', vMask.hide, vMask);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'jualunit/printsi',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue()
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				/*
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: xtext.hasil,
					title: 'IDS'
				});*/
				
				if (xtext.sukses === true) {
					var xfile = trim(xtext.nmfile);
					
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
						buttons: [{
							text: 'Exit',
							handler: function() {
								winpdf.hide();
							}
						}]
					}).show();
				}
				else {
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});				
				}
			},
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Print Failed, Connection Failed!!',
					title: 'IDS'
				});
			}
		});
	}

	function fnPrintAL() {
		Ext.Ajax.on('beforerequest', vMask.show, vMask);
		Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
		Ext.Ajax.on('requestexception', vMask.hide, vMask);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'jualunit/printsrtpernyataan',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue(),
				'fs_nama': '',
				'fs_man': Ext.getCmp('txtMan').getValue()
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				/*
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: xtext.hasil,
					title: 'IDS'
				});*/
				
				if (xtext.sukses === true) {
					var xfile = trim(xtext.nmfile);
					
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
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});				
				}
			},
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Print Failed, Connection Failed!!',
					title: 'IDS'
				});
			}
		});
	}

	function fnPrintTT() {
		Ext.Ajax.on('beforerequest', vMask.show, vMask);
		Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
		Ext.Ajax.on('requestexception', vMask.hide, vMask);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'jualunit/printsrttandaterima',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue()
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				/*
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: xtext.hasil,
					title: 'IDS'
				});*/
				
				if (xtext.sukses === true) {
					var xfile = trim(xtext.nmfile);
					
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
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});				
				}
			},
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Print Failed, Connection Failed!!',
					title: 'IDS'
				});
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
		Ext.getCmp('txtRefnoBeli').setValue('');
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
		Ext.getCmp('txtRangka').setValue('');
		Ext.getCmp('txtMesin').setValue('');
		Ext.getCmp('cboSM').setValue('');
		Ext.getCmp('txtSM').setValue('');
		Ext.getCmp('cboPM').setValue('');
		Ext.getCmp('txtPM').setValue('');
		Ext.getCmp('cboAcno').setValue('');
		Ext.getCmp('txtAcno').setValue('');
		Ext.getCmp('txtCC').setValue('0');
		Ext.getCmp('txtYear').setValue(Ext.Date.format(new Date(), 'Y'));
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
	}

	var frmDirectSales = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Direct Sales Form',
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
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [{
								afterLabelTextTpl: required,
								allowBlank: false,
								anchor: '98%',
								emptyText: "Select a",
								fieldLabel: 'Customer',
								id: 'cboCust',
								labelWidth: 70,
								name: 'cboCust',
								xtype: 'searchCust',
								listeners: {
									change: function(combo, value) {
										Ext.getCmp('txtCust').setValue('');
										Ext.getCmp('txtCustCd').setValue('');
										Ext.getCmp('txtCount').setValue('');
										Ext.getCmp('txtID').setValue('');
										Ext.getCmp('txtAddr').setValue('');
									}
								}
							},{
								anchor: '98%',
								fieldLabel: 'Cust Code',
								hidden: true,
								id: 'txtCustCd',
								labelWidth: 70,
								name: 'txtCustCd',
								xtype: 'textfield'
							},{
								anchor: '98%',
								fieldLabel: 'Cust Count',
								hidden: true,
								id: 'txtCount',
								labelWidth: 70,
								name: 'txtCount',
								xtype: 'textfield'
							}]
						},{
							flex: 1.5,
							layout: 'anchor',
							xtype: 'container',
							items: [{
								anchor: '100%',
								layout: 'hbox',
								xtype: 'container',
								items: [{
									flex: 1.6,
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
								},{
									flex: 1,
									layout: 'anchor',
									xtype: 'container',
									items: [{
										anchor: '100%',
										iconAlign: 'left',
										iconCls: 'edit',
										scale: 'small',
										text: 'Edit Cust',
										xtype: 'button',
										handler: function() {
											if (trim(Ext.getCmp('txtCust').getValue()) === '') {
												Ext.Msg.show({
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
									}]
								},{
									flex: 1.2,
									layout: 'anchor',
									xtype: 'container',
									items: [{
										anchor: '100%',
										iconAlign: 'left',
										iconCls: 'add',
										scale: 'small',
										text: 'Create Cust',
										xtype: 'button',
										handler: function() {
											vMask.show();
											winCust.show();
										}
									}]
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
							items: [{
								anchor: '95%',
								emptyText: 'AUTOMATIC',
								fieldLabel: 'Ref. No',
								id: 'cboRefno',
								labelWidth: 70,
								name: 'cboRefno',
								xtype: 'searchRefno',
								listeners: {
								change: function(combo, value) {
										Ext.getCmp('txtRefnoBeli').setValue('');
									}
								}
							},{
								anchor: '95%',
								fieldLabel: 'Ref. Purch',
								hidden: true,
								id: 'txtRefnoBeli',
								labelWidth: 70,
								name: 'txtRefnoBeli',
								xtype: 'textfield'
							},{
								anchor: '95%',
								emptyText: 'Enter a Document Number',
								fieldLabel: 'Doc. No',
								id: 'txtDocno',
								labelWidth: 70,
								name: 'txtDocno',
								xtype: 'textfield'
							}]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [{
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
							},{
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
							}]
						}]
					},{
						anchor: '95%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [{
								anchor: '98%',
								emptyText: 'Select a',
								fieldLabel: 'Salesman',
								id: 'cboSales',
								labelWidth: 70,
								name: 'cboSales',
								xtype: 'searchSales',
								listeners: {
									change: function(combo, value) {
										Ext.getCmp('txtSales').setValue('');
									}
								}
							},{
								anchor: '98%',
								emptyText: 'Select a',
								fieldLabel: 'Surveyor',
								id: 'cboSurvey',
								labelWidth: 70,
								name: 'cboSurvey',
								xtype: 'searchSurvey',
								listeners: {
									change: function(combo, value) {
										Ext.getCmp('txtSurvey').setValue('');
									}
								}
							}]
						},{
							flex: 1.5,
							layout: 'anchor',
							xtype: 'container',
							items: [{
								anchor: '100%',
								emptyText: 'Salesman',
								fieldStyle: 'background-color: #eee; background-image: none;',
								id: 'txtSales',
								name: 'txtSales',
								readOnly: true,
								xtype: 'textfield'
							},{
								anchor: '100%',
								emptyText: 'Surveyor',
								fieldStyle: 'background-color: #eee; background-image: none;',
								id: 'txtSurvey',
								name: 'txtSurvey',
								readOnly: true,
								xtype: 'textfield'
							}]
						}]
					},{
						anchor: '95%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [{
								afterLabelTextTpl: required,
								allowBlank: false,
								anchor: '98%',
								emptyText: 'Select a',
								fieldLabel: 'Term',
								id: 'cboTerm',
								labelWidth: 70,
								name: 'cboTerm',
								xtype: 'searchTerm',
								listeners: {
									change: function(combo, value) {
										Ext.getCmp('txtTerm').setValue('');
									}
								}
							}]
						},{
							flex: 1.5,
							layout: 'anchor',
							xtype: 'container',
							items: [{
								afterLabelTextTpl: required,
								allowBlank: false,
								anchor: '100%',
								emptyText: 'Term (Tenor)',
								fieldStyle: 'background-color: #eee; background-image: none;',
								id: 'txtTerm',
								name: 'txtTerm',
								readOnly: true,
								xtype: 'textfield'
							}]
						}]
					}]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [{
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
					},{
						afterLabelTextTpl: required,
						allowBlank: false,
						anchor: '100%',
						emptyText: "Customer's Address",
						fieldLabel: 'Address',
						fieldStyle: 'background-color: #eee; background-image: none;',
						grow: true,
						growMin: 35,
						growMax: 35,
						id: 'txtAddr',
						labelWidth: 70,
						name: 'txtAddr',
						readOnly: true,
						xtype: 'textareafield'
					},{
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
					},{
						anchor: '100%',
						emptyText: "Enter a QQ Customer's Name",
						fieldLabel: 'QQ Name',
						id: 'txtQQ',
						labelWidth: 70,
						name: 'txtQQ',
						xtype: 'textfield'
					},{
						anchor: '100%',
						emptyText: "Enter a QQ Customer's Address",
						fieldLabel: 'QQ Address',
						grow: true,
						growMin: 35,
						growMax: 35,
						id: 'txtAddrQQ',
						labelWidth: 70,
						name: 'txtAddrQQ',
						xtype: 'textareafield'
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
							items: [{
								afterLabelTextTpl: required,
								allowBlank: false,
								anchor: '98%',
								emptyText: 'Select a',
								fieldLabel: 'Product',
								id: 'cboProd',
								labelWidth: 70,
								name: 'cboProd',
								xtype: 'searchProd',
								listeners: {
									change: function(combo, value) {
										Ext.getCmp('txtProd').setValue('');
									}
								}
							}]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [{
								afterLabelTextTpl: required,
								allowBlank: false,
								anchor: '100%',
								emptyText: 'Product',
								fieldStyle: 'background-color: #eee; background-image: none;',
								id: 'txtProd',
								name: 'txtProd',
								readOnly: true,
								xtype: 'textfield'
							}]
						}]
					},{
						anchor: '95%',
						fieldLabel: 'Register',
						hidden: true,
						id: 'txtReg',
						labelWidth: 70,
						name: 'txtReg',
						xtype: 'textfield'
					},{
						afterLabelTextTpl: required,
						allowBlank: false,
						anchor: '95%',
						emptyText: 'Enter a Chassis Number',
						fieldLabel: 'Chassis',
						id: 'txtRangka',
						labelWidth: 70,
						name: 'txtRangka',
						xtype: 'textfield'
					},{
						afterLabelTextTpl: required,
						allowBlank: false,
						anchor: '95%',
						emptyText: 'Enter a Machine Number',
						fieldLabel: 'Machine',
						id: 'txtMesin',
						labelWidth: 70,
						name: 'txtMesin',
						xtype: 'textfield'
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
								Ext.create('Ext.ux.form.NumericField', {
									afterLabelTextTpl: required,
									allowBlank: false,
									alwaysDisplayDecimals: false,
									anchor: '90%',
									currencySymbol: null,
									decimalPrecision: 0,
									decimalSeparator: '.',
									fieldLabel: 'Cylinder (CC)',
									hideTrigger: false,
									id: 'txtCC',
									keyNavEnabled: true,
									labelWidth: 85,
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
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								Ext.create('Ext.ux.form.NumericField', {
									afterLabelTextTpl: required,
									allowBlank: false,
									alwaysDisplayDecimals: false,
									anchor: '80%',
									currencySymbol: null,
									decimalPrecision: 0,
									decimalSeparator: '.',
									fieldLabel: 'Year',
									hideTrigger: false,
									id: 'txtYear',
									keyNavEnabled: true,
									labelWidth: 50,
									mouseWheelEnabled: false,
									name: 'txtYear',
									thousandSeparator: ',',
									useThousandSeparator: false,
									value: Ext.Date.format(new Date(), 'Y'),
									listeners: {
										change: function(value) {
											if (Ext.isEmpty(Ext.getCmp('txtYear').getValue())) {
												Ext.getCmp('txtYear').setValue(0);
											}
											else {
												return value;
											}
										}
									}
								})
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
							items: [{
								afterLabelTextTpl: required,
								allowBlank: false,
								anchor: '98%',
								emptyText: 'Select a',
								fieldLabel: 'Color',
								id: 'cboColor',
								labelWidth: 85,
								name: 'cboColor',
								xtype: 'searchColor',
								listeners: {
									change: function(combo, value) {
										Ext.getCmp('txtColor').setValue('');
									}
								}
							},{
								afterLabelTextTpl: required,
								allowBlank: false,
								anchor: '98%',
								emptyText: 'Select a',
								fieldLabel: 'Warehouse',
								id: 'cboWH',
								labelWidth: 85,
								name: 'cboWH',
								xtype: 'searchWH',
								listeners: {
									change: function(combo, value) {
										Ext.getCmp('txtWH').setValue('');
									}
								}
							}]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [{
								afterLabelTextTpl: required,
								allowBlank: false,
								anchor: '100%',
								emptyText: 'Color',
								fieldStyle: 'background-color: #eee; background-image: none;',
								id: 'txtColor',
								name: 'txtColor',
								readOnly: true,
								xtype: 'textfield'
							},{
								afterLabelTextTpl: required,
								allowBlank: false,
								anchor: '100%',
								emptyText: 'Warehouse',
								fieldStyle: 'background-color: #eee; background-image: none;',
								id: 'txtWH',
								name: 'txtWH',
								readOnly: true,
								xtype: 'textfield'
							}]
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
								afterLabelTextTpl: required,
								allowBlank: false,
								anchor: '98%',
								emptyText: 'Select a',
								fieldLabel: 'Sales',
								id: 'cboSM',
								labelWidth: 70,
								name: 'cboSM',
								xtype: 'searchSalesMtd',
								listeners: {
									change: function(combo, value) {
										Ext.getCmp('txtSM').setValue('');
									}
								}
							}]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [{
								afterLabelTextTpl: required,
								allowBlank: false,
								anchor: '100%',
								emptyText: 'Sales Methods',
								fieldStyle: 'background-color: #eee; background-image: none;',
								id: 'txtSM',
								name: 'txtSM',
								readOnly: true,
								xtype: 'textfield'
							}]
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
							items: []
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								Ext.create('Ext.ux.form.NumericField', {
									alwaysDisplayDecimals: true,
									anchor: '100%',
									currencySymbol: null,
									decimalPrecision: 2,
									decimalSeparator: '.',
									fieldLabel: 'DP Max',
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
								})
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
								afterLabelTextTpl: required,
								allowBlank: false,
								anchor: '98%',
								emptyText: 'Select a',
								fieldLabel: 'Payment',
								id: 'cboPM',
								labelWidth: 70,
								name: 'cboPM',
								xtype: 'searchPM',
								listeners: {
									change: function(combo, value) {
										Ext.getCmp('txtPM').setValue('');
										Ext.getCmp('cboAcno').setValue('');
										Ext.getCmp('txtAcno').setValue('');
									}
								}
							},{
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
							}]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [{
								afterLabelTextTpl: required,
								allowBlank: false,
								anchor: '100%',
								emptyText: 'Payment Methods',
								fieldStyle: 'background-color: #eee; background-image: none;',
								id: 'txtPM',
								name: 'txtPM',
								readOnly: true,
								xtype: 'textfield'
							},{
								afterLabelTextTpl: required,
								allowBlank: false,
								anchor: '100%',
								emptyText: 'Account Name',
								fieldStyle: 'background-color: #eee; background-image: none;',
								id: 'txtAcno',
								name: 'txtAcno',
								readOnly: true,
								xtype: 'textfield'
							}]
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
								Ext.create('Ext.ux.form.NumericField', {
									afterLabelTextTpl: required,
									allowBlank: false,
									alwaysDisplayDecimals: true,
									anchor: '95%',
									currencySymbol: null,
									decimalPrecision: 2,
									decimalSeparator: '.',
									fieldLabel: 'Unit Price',
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
								}),
								Ext.create('Ext.ux.form.NumericField', {
									alwaysDisplayDecimals: true,
									anchor: '95%',
									currencySymbol: null,
									decimalPrecision: 2,
									decimalSeparator: '.',
									fieldLabel: 'Discount',
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
								})
							]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								Ext.create('Ext.ux.form.NumericField', {
									alwaysDisplayDecimals: true,
									anchor: '100%',
									currencySymbol: null,
									decimalPrecision: 2,
									decimalSeparator: '.',
									fieldLabel: 'Leasing Disc',
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
								}),
								Ext.create('Ext.ux.form.NumericField', {
									alwaysDisplayDecimals: true,
									anchor: '100%',
									currencySymbol: null,
									decimalPrecision: 2,
									decimalSeparator: '.',
									fieldLabel: 'Installment',
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
								})
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
							items: [{
								anchor: '98%',
								emptyText: 'Select a',
								fieldLabel: 'Cust. DP',
								id: 'cboDP',
								labelWidth: 70,
								name: 'cboDP',
								xtype: 'searchDP',
								listeners: {
									change: function(combo, value) {
										Ext.getCmp('txtDP').setValue('');
										Ext.getCmp('txtNilaiDP').setValue('0');
									}
								}
							}]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [{
								anchor: '100%',
								emptyText: 'Customer DP',
								fieldStyle: 'background-color: #eee; background-image: none;',
								id: 'txtDP',
								name: 'txtDP',
								readOnly: true,
								xtype: 'textfield'
							}]
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
								Ext.create('Ext.ux.form.NumericField', {
									alwaysDisplayDecimals: true,
									anchor: '95%',
									currencySymbol: null,
									decimalPrecision: 2,
									decimalSeparator: '.',
									fieldLabel: 'DP Values',
									hideTrigger: true,
									id: 'txtNilaiDP',
									keyNavEnabled: false,
									labelWidth: 85,
									mouseWheelEnabled: false,
									name: 'txtNilaiDP',
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
								})
							]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								Ext.create('Ext.ux.form.NumericField', {
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
								})
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
								items: [{
									anchor: '98%',
									scale: 'small',
									text: 'Create DP',
									xtype: 'button',
									handler: function() {
										var vCust = Ext.getCmp('txtCust').getValue();
										var vCustCd = Ext.getCmp('cboCust').getValue();
										var vProd = Ext.getCmp('txtProd').getValue();
										var vRangka = Ext.getCmp('txtRangka').getValue();
										var vMesin = Ext.getCmp('txtMesin').getValue();
										
										var vNote = 'Pembayaran DP a/n '.concat(vCust, '\n',
													'Untuk 1 unit ', vProd, '\n',
													'No Rangka : ', vRangka, '\n',
													'No Mesin : ', vMesin);
										
										Ext.getCmp('cboRefnoDP').setValue('');
										Ext.getCmp('txtInputDt').setValue(new Date());
										Ext.getCmp('txtInputCustDP').setValue(vCust);
										Ext.getCmp('cboInputCustDP').setValue(vCustCd);
										Ext.getCmp('txtInputNote').setValue(vNote);
										Ext.getCmp('txtInputDP').setValue('0');
										
										if (trim(vCust) === '') {
											Ext.Msg.show({
												buttons: Ext.MessageBox.OK,
												closable: false,
												icon: Ext.Msg.WARNING,
												msg: 'Customer is empty, please fill in advance!!',
												title: 'IDS'
											});
											return;
										}
										
										if (trim(vProd) === '' || trim(vRangka) === '' || trim(vMesin) === '') {
											Ext.Msg.show({
												buttons: Ext.MessageBox.YESNO,
												closable: false,
												icon: Ext.Msg.QUESTION,
												msg: 'Product, chassis or machine is empty and will be empty in note!! <br> continue?',
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
								}]
							}]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [{
								anchor: '100%',
								emptyText: "Enter Branch Manager Name",
								hidden: true,
								id: 'txtMan',
								name: 'txtMan',
								xtype: 'textfield'
							}]
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
							items: []
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: []
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
						style: 'padding: 5px;',
						title: 'Leasing Document Print',
						xtype: 'fieldset',
						items: [{
							text: 'DP',
							width: 65,
							xtype: 'button',
							handler: fnPrintKwitansiDP
						},{
							text: 'Fidusia',
							width: 65,
							xtype: 'button',
							handler: fnPrintFidusia
						},{
							text: 'SI',
							width: 65,
							xtype: 'button',
							handler: fnPrintSI
						},{
							text: 'Agreement',
							width: 65,
							xtype: 'button',
							handler: function() {
								Ext.getCmp('txtInputMan').setValue(Ext.getCmp('txtMan').getValue());
								
								vMask.show();
								winMan.show();
							}
						},{
							text: 'Receipt',
							width: 65,
							xtype: 'button',
							handler: fnPrintTT
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
							text: 'Save',
							width: 65,
							xtype: 'button',
							handler: fnCekSave
						},{
							text: 'Print SO',
							width: 65,
							xtype: 'button',
							handler: fnPrintSO
						},{
							text: 'Print DO',
							width: 65,
							xtype: 'button',
							handler: fnPrintDO
						},{
							text: 'Print Inv',
							width: 65,
							xtype: 'button',
							handler: fnPrintInvoice
						},{
							text: 'Reset',
							width: 65,
							xtype: 'button',
							handler: fnReset
						},{
							text: 'Remove',
							width: 65,
							xtype: 'button',
							handler: fnCekRemove
						}]
					}]
				}]
			}]
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmSales
	});

	var vMask2 = new Ext.LoadMask({
		msg: 'Please wait...',
		target: winCust
	});

	var vMask3 = new Ext.LoadMask({
		msg: 'Please wait...',
		target: winDP
	});
	
	frmDirectSales.render(Ext.getBody());
	Ext.get('loading').destroy();
});
