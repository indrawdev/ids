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

	var xDept = '';
	var xCount = '';
	var xTrx = '';
	var xsTrx = '';
	var xRefno = '';

	function gridTooltipSearch(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Double click on record to choose',
			target: view.el,
			trackMouse: true
		});
	}

	function buatForm() {
		var grupDept = Ext.create('Ext.data.Store', {
			autoLoad: false,
			fields: [
				'fs_code','fs_count','fs_nm_code'
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
				url: 'cashbank/cbin_dept'
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_cari': Ext.getCmp('txtCari').getValue()
					});
				}
			}
		});

		/*
		var winGrid = Ext.create('Ext.ux.LiveSearchGridPanel', {
			autoDestroy: true,
			height: 450,
			width: 550,
			store: grupDept,
			bbar: Ext.create('Ext.PagingToolbar', {
				displayInfo: true,
				pageSize: 25,
				plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
				store: grupDept,
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
				{text: "Code", dataIndex: 'fs_code', menuDisabled: true, sortable: false, width: 100},
				{text: "Count", dataIndex: 'fs_count', menuDisabled: true, sortable: false, width: 100},
				{text: "Dept Name", dataIndex: 'fs_nm_code', menuDisabled: true, sortable: false, width: 280}
			],
			listeners: {
				itemdblclick: function(grid, record)
				{
					Ext.getCmp('cboDept').setValue(record.get('fs_nm_code'));
					Ext.getCmp('txtDept').setValue(record.get('fs_code'));
					Ext.getCmp('txtCount').setValue(record.get('fs_count'));
					
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
			width: 550,
			sortableColumns: false,
			store: grupDept,
			columns: [
				{xtype: 'rownumberer', width: 45},
				{text: "Code", dataIndex: 'fs_code', menuDisabled: true, sortable: false, width: 100},
				{text: "Count", dataIndex: 'fs_count', menuDisabled: true, sortable: false, width: 100},
				{text: "Dept Name", dataIndex: 'fs_nm_code', menuDisabled: true, sortable: false, width: 280}
			],
			tbar: [{
				flex: 1,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					anchor: '98%',
					emptyText: 'Code / Dept Name',
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
						grupDept.load();
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
				store: grupDept,
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
					Ext.getCmp('cboDept').setValue(record.get('fs_nm_code'));
					Ext.getCmp('txtDept').setValue(record.get('fs_code'));
					Ext.getCmp('txtCount').setValue(record.get('fs_count'));
					
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
					grupDept.load();
					vMask.show();
				}
			}
		});

		var cboDept = {
			anchor: '35%',
			displayField: 'fs_nm_code',
			editable: false,
			emptyText: 'Select a Department',
			fieldLabel: 'Department',
			id: 'cboDept',
			name: 'cboDept',
			valueField: 'fs_code',
			xtype: 'textfield',
			listeners: {
				change: function(field, newValue) {
					Ext.getCmp('txtDept').setValue('');
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
		};

		var txtDept = {
			anchor: '35%',
			fieldLabel: 'Dept Code',
			hidden: true,
			id: 'txtDept',
			name: 'txtDept',
			xtype: 'textfield'
		};

		var txtCount = {
			anchor: '35%',
			fieldLabel: 'Dept Count',
			hidden: true,
			id: 'txtCount',
			name: 'txtCount',
			xtype: 'textfield'
		};

		var grupAcno = Ext.create('Ext.data.Store', {
			autoLoad: false,
			fields: [
				'fs_kd_acno','fs_nm_acno'
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
				url: 'accgl/acno'
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_kd_acno': Ext.getCmp('cboAcnoA').getValue(),
						'fs_nm_acno': Ext.getCmp('cboAcnoA').getValue(),
						'fs_cari': Ext.getCmp('txtCari2').getValue()
					});
				}
			}
		});

		/*
		var winGrid2 = Ext.create('Ext.ux.LiveSearchGridPanel', {
			autoDestroy: true,
			height: 450,
			width: 550,
			store: grupAcno,
			bbar: Ext.create('Ext.PagingToolbar', {
				displayInfo: true,
				pageSize: 25,
				plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
				store: grupAcno,
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
				{text: "Acc. No", dataIndex: 'fs_kd_acno', menuDisabled: true, sortable: false, width: 120},
				{text: "Acc. Name", dataIndex: 'fs_nm_acno', menuDisabled: true, sortable: false, width: 360}
			],
			listeners: {
				itemdblclick: function(grid, record)
				{
					Ext.getCmp('cboAcnoA').setValue(record.get('fs_kd_acno'));
					Ext.getCmp('txtAcnoA').setValue(record.get('fs_nm_acno'));
					
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
		*/

		var winGrid2 = Ext.create('Ext.grid.Panel',{
			anchor: '100%',
			autoDestroy: true,
			height: 450,
			width: 550,
			sortableColumns: false,
			store: grupAcno,
			columns: [
				{xtype: 'rownumberer', width: 45},
				{text: "Acc. No", dataIndex: 'fs_kd_acno', menuDisabled: true, sortable: false, width: 120},
				{text: "Acc. Name", dataIndex: 'fs_nm_acno', menuDisabled: true, sortable: false, width: 360}
			],
			tbar: [{
				flex: 1,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					anchor: '98%',
					emptyText: 'Acc. No / Acc. Name',
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
						grupAcno.load();
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
				store: grupAcno,
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
					Ext.getCmp('cboAcnoA').setValue(record.get('fs_kd_acno'));
					Ext.getCmp('txtAcnoA').setValue(record.get('fs_nm_acno'));
					
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
					grupAcno.load();
					vMask.show();
				}
			}
		});
		
		var cboAcnoA = {
			afterLabelTextTpl: required,
			anchor: '98%',
			emptyText: 'Select an',
			fieldLabel: 'Account No',
			id: 'cboAcnoA',
			name: 'cboAcnoA',
			value: '',
			xtype: 'textfield',
			listeners: {
				change: function(field, newValue) {
					Ext.getCmp('txtAcnoA').setValue('');
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

		var txtAcnoA = {
			anchor: '70%',
			editable: false,
			emptyText: 'Account',
			fieldStyle: 'background-color: #eee; background-image: none;',
			hidden: false,
			id: 'txtAcnoA',
			name: 'txtAcnoA',
			readOnly: true,
			xtype: 'textfield'
		};

		var grupAcno2 = Ext.create('Ext.data.Store', {
			autoLoad: false,
			fields: [
				'fs_kd_acno','fs_nm_acno'
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
				url: 'accgl/acno2'
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_kd_acno': Ext.getCmp('cboAcnoB').getValue(),
						'fs_nm_acno': Ext.getCmp('cboAcnoB').getValue(),
						'fs_cari': Ext.getCmp('txtCari3').getValue()
					});
				}
			}
		});

		/*
		var winGrid3 = Ext.create('Ext.ux.LiveSearchGridPanel', {
			autoDestroy: true,
			height: 450,
			width: 550,
			store: grupAcno2,
			bbar: Ext.create('Ext.PagingToolbar', {
				displayInfo: true,
				pageSize: 25,
				plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
				store: grupAcno2,
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
				{text: "Acc. No", dataIndex: 'fs_kd_acno', menuDisabled: true, sortable: false, width: 120},
				{text: "Acc. Name", dataIndex: 'fs_nm_acno', menuDisabled: true, sortable: false, width: 360}
			],
			listeners: {
				itemdblclick: function(grid, record)
				{
					Ext.getCmp('cboAcnoB').setValue(record.get('fs_kd_acno'));
					Ext.getCmp('txtAcnoB').setValue(record.get('fs_nm_acno'));
					
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
		*/

		var winGrid3 = Ext.create('Ext.grid.Panel',{
			anchor: '100%',
			autoDestroy: true,
			height: 450,
			width: 550,
			sortableColumns: false,
			store: grupAcno2,
			columns: [
				{xtype: 'rownumberer', width: 45},
				{text: "Acc. No", dataIndex: 'fs_kd_acno', menuDisabled: true, sortable: false, width: 120},
				{text: "Acc. Name", dataIndex: 'fs_nm_acno', menuDisabled: true, sortable: false, width: 360}
			],
			tbar: [{
				flex: 1,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					anchor: '98%',
					emptyText: 'Acc. No / Acc. Name',
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
						grupAcno2.load();
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
				store: grupAcno2,
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
					Ext.getCmp('cboAcnoB').setValue(record.get('fs_kd_acno'));
					Ext.getCmp('txtAcnoB').setValue(record.get('fs_nm_acno'));
					
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
					grupAcno2.load();
					vMask.show();
				}
			}
		});

		var cboAcnoB = {
			afterLabelTextTpl: required,
			anchor: '98%',
			emptyText: 'Select an',
			fieldLabel: 'To',
			id: 'cboAcnoB',
			labelWidth: 80,
			name: 'cboAcnoB',
			value: '',
			xtype: 'textfield',
			listeners: {
				change: function(field, newValue) {
					Ext.getCmp('txtAcnoB').setValue('');
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

		var txtAcnoB = {
			anchor: '70%',
			editable: false,
			emptyText: 'Account',
			fieldStyle: 'background-color: #eee; background-image: none;',
			hidden: false,
			id: 'txtAcnoB',
			name: 'txtAcnoB',
			readOnly: true,
			xtype: 'textfield'
		};

		var txtDateA = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '95%',
			editable: true,
			fieldLabel: 'Date',
			format: 'd-m-Y',
			id: 'txtDateA',
			maskRe: /[0-9-]/,
			minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
			name: 'txtDateA',
			value: new Date(),
			xtype: 'datefield'
		};

		var txtDateB = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '70%',
			editable: true,
			fieldLabel: 'To',
			format: 'd-m-Y',
			id: 'txtDateB',
			labelWidth: 30,
			maskRe: /[0-9-]/,
			minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
			name: 'txtDateB',
			value: new Date(),
			xtype: 'datefield'
		};

		Ext.define('DataGrid', {
			extend: 'Ext.data.Model',
			fields: [
				{name: 'fs_kd_dept', type: 'string'},
				{name: 'fs_count', type: 'string'},
				{name: 'DEPTNM', type: 'string'},
				{name: 'ACNO', type: 'string'},
				{name: 'ACNM', type: 'string'},
				{name: 'fs_kd_trx', type: 'string'},
				{name: 'fs_kd_strx', type: 'string'},
				{name: 'fs_refno', type: 'string'},
				{name: 'fd_refno', type: 'string'},
				{name: 'fs_Descrp', type: 'string'},
				{name: 'fs_acno', type: 'string'},
				{name: 'DEBET', type: 'float'},
				{name: 'CREDIT', type: 'float'},
				{name: 'BALANCE', type: 'float'}
			]
		});

		var grupGrid = Ext.create('Ext.data.Store', {
			autoLoad: false,
			model: 'DataGrid',
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
				url: 'accgl/grid_header'
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_kd_dept': Ext.getCmp('txtDept').getValue(),
						'fs_count': Ext.getCmp('txtCount').getValue(),
						'fs_kd_acnoa': Ext.getCmp('cboAcnoA').getValue(),
						'fs_kd_acnob': Ext.getCmp('cboAcnoB').getValue(),
						'fd_tgla': Ext.Date.format(Ext.getCmp('txtDateA').getValue(), 'Ymd'),
						'fd_tglb': Ext.Date.format(Ext.getCmp('txtDateB').getValue(), 'Ymd')
					});
				}
			}
		});

		var gridHeader = Ext.create('Ext.ux.LiveSearchGridPanel', {
			height: 217,
			sortableColumns: false,
			store: grupGrid,
			bbar: Ext.create('Ext.PagingToolbar', {
				displayInfo: true,
				pageSize: 25,
				plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
				store: grupGrid
			}),
			columns: [{
				xtype: 'rownumberer',
				width: 35,
			},{
				dataIndex: 'fs_kd_dept',
				hidden: true,
				locked: true,
				menuDisabled: true,
				text: "Dept Code"
			},{
				dataIndex: 'fs_count',
				hidden: true,
				locked: true,
				menuDisabled: true,
				text: "Dept Count"
			},{
				dataIndex: 'DEPTNM',
				menuDisabled: true,
				locked: true,
				text: "Dept Name",
				width: 150
			},{
				dataIndex: 'ACNO',
				hidden: true,
				menuDisabled: true,
				text: "Acc No"
			},{
				dataIndex: 'ACNM',
				hidden: true,
				menuDisabled: true,
				text: "Acc Name"
			},{
				dataIndex: 'fs_kd_trx',
				hidden: true,
				menuDisabled: true,
				text: "Trx"
			},{
				dataIndex: 'fs_kd_strx',
				hidden: true,
				menuDisabled: true,
				text: "sTrx"
			},{
				dataIndex: 'fs_refno',
				menuDisabled: true,
				text: "Reff No",
				width: 160
			},{
				dataIndex: 'fd_refno',
				menuDisabled: true,
				text: "Reff Date",
				width: 80
			},{
				dataIndex: 'fs_Descrp',
				menuDisabled: true,
				text: "Description",
				width: 270
			},{
				dataIndex: 'fs_acno',
				menuDisabled: true,
				text: "Account No",
				width: 100
			},{
				align: 'right',
				dataIndex: 'DEBET',
				format: '0,000.00',
				menuDisabled: true,
				text: "Debit",
				width: 100,
				xtype: 'numbercolumn'
			},{
				align: 'right',
				dataIndex: 'CREDIT',
				format: '0,000.00',
				menuDisabled: true,
				text: "Credit",
				width: 100,
				xtype: 'numbercolumn'
			},{
				align: 'right',
				dataIndex: 'BALANCE',
				format: '0,000.00',
				menuDisabled: true,
				text: "Balance",
				width: 110,
				xtype: 'numbercolumn'
			}],
			listeners: {
				itemclick: function(grid, record)
				{
					xDept = record.get('fs_kd_dept');
					xCount = record.get('fs_count');
					xTrx = record.get('fs_kd_trx');
					xsTrx = record.get('fs_kd_strx');
					xRefno = record.get('fs_refno');
					
					Ext.Ajax.on('beforerequest', fnMaskShow);
					Ext.Ajax.on('requestcomplete', fnMaskHide);
					Ext.Ajax.on('requestexception', fnMaskHide);
					
					Ext.Ajax.request({
						method: 'POST',
						url: 'accgl/cek_detail',
						params: {
							'fs_kd_dept': record.get('fs_kd_dept'),
							'fs_count': record.get('fs_count'),
							'fs_kd_trx': record.get('fs_kd_trx'),
							'fs_kd_strx': record.get('fs_kd_strx'),
							'fs_refno': record.get('fs_refno')
						},
						success: function(response) {
							var xtext = Ext.decode(response.responseText);
							
							if (xtext.sukses === true) {
								grupGrid2.load({
									params: {
										'fs_kd_dept': record.get('fs_kd_dept'),
										'fs_count': record.get('fs_count'),
										'fs_kd_trx': record.get('fs_kd_trx'),
										'fs_kd_strx': record.get('fs_kd_strx'),
										'fs_refno': record.get('fs_refno')
									}
								});
							}
							else {
								grupGrid2.load();
							}
						},
						failure: function(response) {
							var xtext = Ext.decode(response.responseText);
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.OK,
								closable: false,
								icon: Ext.MessageBox.INFO,
								msg: 'Show Failed, Connection Failed!!',
								title: 'IDS'
							});
							fnMaskHide();
						}
					});
				}
			},
			viewConfig: {
				getRowClass: function() {
					return 'rowwrap';
				},
				markDirty: false,
				stripeRows: true
			}
		});

		Ext.define('DataGrid2', {
			extend: 'Ext.data.Model',
			fields: [
				{name: 'fs_kd_acc', type: 'string'},
				{name: 'fs_nm_acc', type: 'string'},
				{name: 'fs_note', type: 'string'},
				{name: 'fn_debet', type: 'float'},
				{name: 'fn_credit', type: 'float'}
			]
		});

		var grupGrid2 = Ext.create('Ext.data.Store', {
			autoLoad: false,
			model: 'DataGrid2',
			proxy: {
				actionMethods: {
					read: 'POST'
				},
				reader: {
					type: 'json'
				},
				type: 'ajax',
				url: 'accgl/grid_detail'
			}
		});

		var gridDetail = Ext.create('Ext.grid.Panel', {
			height: 140,
			sortableColumns: false,
			store: grupGrid2,
			columns: [{
				xtype: 'rownumberer',
				width: 25,
			},{
				dataIndex: 'fs_kd_acc',
				menuDisabled: true,
				text: "Acc No",
				width: 100
			},{
				dataIndex: 'fs_nm_acc',
				menuDisabled: true,
				text: "Acc Name",
				width: 225
			},{
				dataIndex: 'fs_note',
				menuDisabled: true,
				text: "Note",
				width: 330
			},{
				align: 'right',
				dataIndex: 'fn_debet',
				format: '0,000.00',
				menuDisabled: true,
				text: "Debit",
				width: 120,
				xtype: 'numbercolumn'
			},{
				align: 'right',
				dataIndex: 'fn_credit',
				format: '0,000.00',
				menuDisabled: true,
				text: "Credit",
				width: 120,
				xtype: 'numbercolumn'
			}],
			viewConfig: {
				getRowClass: function() {
					return 'rowwrap';
				},
				markDirty: false,
				stripeRows: true
			}
		});

		function fnShow() {
			if (this.up('form').getForm().isValid()) {
				Ext.Ajax.on('beforerequest', fnMaskShow);
				Ext.Ajax.on('requestcomplete', fnMaskHide);
				Ext.Ajax.on('requestexception', fnMaskHide);
				
				Ext.Ajax.request({
					method: 'POST',
					url: 'accgl/cek_show',
					params: {
						'fs_kd_dept': Ext.getCmp('txtDept').getValue(),
						'fs_count': Ext.getCmp('txtCount').getValue(),
						'fs_kd_acnoa': Ext.getCmp('cboAcnoA').getValue(),
						'fs_kd_acnob': Ext.getCmp('cboAcnoB').getValue(),
						'fd_tgla': Ext.Date.format(Ext.getCmp('txtDateA').getValue(), 'Ymd'),
						'fd_tglb': Ext.Date.format(Ext.getCmp('txtDateB').getValue(), 'Ymd')
					},
					success: function(response) {
						var xtext = Ext.decode(response.responseText);
						
						grupGrid.removeAll();
						if (xtext.sukses === true) {
							grupGrid.load();
							grupGrid2.load();
						}
						else {
							grupGrid.load();
						}
					},
					failure: function(response) {
						var xtext = Ext.decode(response.responseText);
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.MessageBox.INFO,
							msg: 'Show Failed, Connection Failed!!',
							title: 'IDS'
						});
						fnMaskHide();
					}
				});
			}
		}

		function fnExportToExcel() {
			fnPB();
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				timeout: 180000,
				url: 'accgl/excel_header',
				params: {
					'fs_kd_dept': Ext.getCmp('txtDept').getValue(),
					'fs_count': Ext.getCmp('txtCount').getValue(),
					'fs_kd_acnoa': Ext.getCmp('cboAcnoA').getValue(),
					'fs_kd_acnob': Ext.getCmp('cboAcnoB').getValue(),
					'fs_nm_acnoa': Ext.getCmp('txtAcnoA').getValue(),
					'fs_nm_acnob': Ext.getCmp('txtAcnoB').getValue(),
					'fd_tgla': Ext.Date.format(Ext.getCmp('txtDateA').getValue(), 'Ymd'),
					'fd_tglb': Ext.Date.format(Ext.getCmp('txtDateB').getValue(), 'Ymd')
				},
				success: function(response) {
					var xtext = Ext.decode(response.responseText);
					
					vMask.show();
					if (xtext.sukses === true) {
						var xfile = xtext.nmfile.trim();
						
						var winxls = Ext.create('Ext.window.Window', {
							closable: false,
							draggable: false,
							height: 100,
							title: 'Download Excel Files',
							width: 180,
							defaults: {
								margin: 5
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
										anchor: '95%',
										href: xfile,
										hrefTarget: '_blank',
										iconCls: 'icon-save',
										text: 'Download',
										xtype: 'button'
									}]
								},{
									flex: 0.8,
									layout: 'anchor',
									xtype: 'container',
									items: [{
										anchor: '100%',
										iconCls: 'icon-exit',
										text: 'Exit',
										xtype: 'button',
										handler: function() {
											vMask.hide();
											winxls.hide();
										}
									}]
								}]
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
						vMask.hide();
					}
				},
				failure: function(response) {
					var xtext = Ext.decode(response.responseText);
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: 'Export Failed, Connection Failed!!',
						title: 'IDS'
					});
					fnMaskHide();
				}
			});
		}

		function fnExportToExcel2() {
			fnPB();
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				timeout: 180000,
				url: 'accgl/excel_detail',
				params: {
					'fs_kd_dept': xDept,
					'fs_count': xCount,
					'fs_kd_trx': xTrx,
					'fs_kd_strx': xsTrx,
					'fs_refno': xRefno
				},
				success: function(response) {
					var xtext = Ext.decode(response.responseText);
					
					vMask.show();
					if (xtext.sukses === true) {
						var xfile = xtext.nmfile.trim();
						
						var winxls = Ext.create('Ext.window.Window', {
							closable: false,
							draggable: false,
							height: 100,
							title: 'Download Excel Files',
							width: 180,
							defaults: {
								margin: 5
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
										anchor: '95%',
										href: xfile,
										hrefTarget: '_blank',
										iconCls: 'icon-save',
										text: 'Download',
										xtype: 'button'
									}]
								},{
									flex: 0.8,
									layout: 'anchor',
									xtype: 'container',
									items: [{
										anchor: '100%',
										iconCls: 'icon-exit',
										text: 'Exit',
										xtype: 'button',
										handler: function() {
											vMask.hide();
											winxls.hide();
										}
									}]
								}]
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
						vMask.hide();
					}
				},
				failure: function(response) {
					var xtext = Ext.decode(response.responseText);
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: 'Export Failed, Connection Failed!!',
						title: 'IDS'
					});
					fnMaskHide();
				}
			});
		}

		function fnReset() {
			Ext.getCmp('cboDept').setValue('');
			Ext.getCmp('txtDept').setValue('');
			Ext.getCmp('txtCount').setValue('');
			Ext.getCmp('cboAcnoA').setValue(),
			Ext.getCmp('cboAcnoB').setValue();
			Ext.getCmp('txtAcnoA').setValue();
			Ext.getCmp('txtAcnoB').setValue();
			Ext.getCmp('txtDateA').setValue(new Date());
			Ext.getCmp('txtDateB').setValue(new Date());
			
			grupGrid.removeAll();
			grupGrid2.removeAll();
			pbar.hide();
		}

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

		var frmAccGL = Ext.create('Ext.form.Panel', {
			border: false,
			frame: true,
			region: 'center',
			title: 'Account Track Form',
			width: 950,
			fieldDefaults: {
				labelAlign: 'right',
				labelSeparator: '',
				labelWidth: 80,
				msgTarget: 'side'
			},
			items: [{
				xtype: 'fieldset',
				style: 'padding: 5px;',
				items: [
					cboDept,
					txtDept,
					txtCount,
				{
					anchor: '80%',
					layout: 'hbox',
					xtype: 'container',
					items: [{
						flex: 0.5,
						layout: 'anchor',
						xtype: 'container',
						items: [
							cboAcnoA,
							cboAcnoB
						]
					},{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [
							txtAcnoA,
							txtAcnoB
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
							txtDateA
						]
					},{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [
							txtDateB
						]
					},{
						flex: 0.7,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							anchor: '95%',
							scale: 'medium',
							text: 'Show',
							width: 70,
							xtype: 'button',
							handler: fnShow
						}]
					},{
						flex: 0.7,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							anchor: '95%',
							scale: 'medium',
							text: 'Export Header To Excel',
							width: 70,
							xtype: 'button',
							handler: fnExportToExcel
						}]
					},{
						flex: 0.7,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							anchor: '95%',
							scale: 'medium',
							text: 'Export Detail To Excel',
							width: 70,
							xtype: 'button',
							handler: fnExportToExcel2
						}]
					},{
						flex: 0.7,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							anchor: '95%',
							scale: 'medium',
							text: 'Reset',
							width: 70,
							xtype: 'button',
							handler: fnReset
						}]
					}]
				}]
			},
				gridHeader, {xtype: 'splitter'},
				gridDetail
			]
		});

		var vMask = new Ext.LoadMask({
			msg: 'Please wait...',
			target: frmAccGL
		});
		
		function fnMaskShow() {
			frmAccGL.mask('Please wait...');
		}

		function fnMaskHide() {
			frmAccGL.unmask();
		}
		
		frmAccGL.render(Ext.getBody());
	}
	
	Ext.Ajax.request({
		method: 'POST',
		url: 'alamat/nilaidefa',
		success: function(response) {
			var xtext = Ext.decode(response.responseText);
			
			if (xtext.sukses === true) {
				buatForm();
				
				// Ext.getCmp('cboDept').setValue(xtext.nmdept);
				// Ext.getCmp('txtDept').setValue(xtext.deptcd);
				// Ext.getCmp('txtCount').setValue(xtext.count);
			}
		},
		failure: function(response) {
			var xText = Ext.decode(response.responseText);
			Ext.MessageBox.show({
				buttons: Ext.MessageBox.OK,
				closable: false,
				icon: Ext.MessageBox.INFO,
				message: 'Load default value Failed, Connection Failed!!',
				title: 'IDS'
			});
		}
	});
	
	Ext.get('loading').destroy();
});
