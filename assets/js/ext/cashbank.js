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

	function gridTooltip(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Double click an item to edit',
			target: view.el,
			trackMouse: true
		});
	}

	function buatForm() {
		var cellEditingIn = Ext.create('Ext.grid.plugin.CellEditing', {
			clicksToEdit: 2,
			listeners: {
				edit: function(editor, e) {
					totalDCin();
				}
			}
		});

		function totalDCin() {
			var xtotaldebit = 0;
			var xtotalcredit = 0;
			var store = gridDetailIn.getStore();
			store.each(function(record, idx) {
				xtotaldebit = xtotaldebit + record.get('fn_debet');
				xtotalcredit = xtotalcredit + record.get('fn_credit');
			});
			Ext.getCmp('txtTotalDebitIn').setValue(xtotaldebit);
			Ext.getCmp('txtTotalCreditIn').setValue(xtotalcredit);
		}

		var grupDeptIn = Ext.create('Ext.data.Store', {
			autoLoad: false,
			fields: ['fs_code','fs_count','fs_nm_code'],
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
			}
		});

		var winGrid = Ext.create('Ext.ux.LiveSearchGridPanel', {
			autoDestroy: true,
			height: 450,
			width: 550,
			sortableColumns: false,
			store: grupDeptIn,
			bbar: Ext.create('Ext.PagingToolbar', {
				displayInfo: true,
				pageSize: 25,
				plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
				store: grupDeptIn,
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
				{text: "Count", dataIndex: 'fs_count', menuDisabled: true, width: 100},
				{text: "Dept Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 280}
			],
			listeners: {
				itemdblclick: function(grid, record)
				{
					Ext.getCmp('cboDeptIn').setValue(record.get('fs_nm_code'));
					Ext.getCmp('txtDeptIn').setValue(record.get('fs_code'));
					Ext.getCmp('txtCountIn').setValue(record.get('fs_count'));
					
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
					grupDeptIn.load();
					vMask.show();
				}
			}
		});

		var cboDeptIn = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '50%',
			displayField: 'fs_nm_code',
			editable: false,
			emptyText: 'Select a Department',
			fieldLabel: 'Department',
			id: 'cboDeptIn',
			name: 'cboDeptIn',
			valueField: 'fs_code',
			xtype: 'textfield',
			listeners: {
				change: function(field, newValue) {
					Ext.getCmp('txtDeptIn').setValue('');
					Ext.getCmp('txtCountIn').setValue('');
					fnResetIn1();
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

		var txtDeptIn = {
			anchor: '49.9%',
			fieldLabel: 'Dept Code',
			hidden: true,
			id: 'txtDeptIn',
			name: 'txtDeptIn',
			xtype: 'textfield'
		};

		var txtCountIn = {
			anchor: '49.9%',
			fieldLabel: 'Dept Count',
			hidden: true,
			id: 'txtCountIn',
			name: 'txtCountIn',
			xtype: 'textfield'
		};

		var grupsTrxIn = Ext.create('Ext.data.Store', {
			autoLoad: false,
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
				url: 'cashbank/cbin_trs'
			}
		});

		var cboTrsIn = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '50%',
			displayField: 'fs_nm_strx',
			editable: false,
			emptyText: 'Select a Trans Type',
			fieldLabel: 'Trans Type',
			id: 'cboTrsIn',
			name: 'cboTrsIn',
			store: grupsTrxIn,
			valueField: 'fs_kd_strx',
			xtype: 'combobox',
			listeners: {
				change: function(combo, value) {
					fnResetIn1();
				}
			}
		};

		var grupStatusIn = Ext.create('Ext.data.ArrayStore', {
			autoLoad: false,
			data: [['0', 'APPROVED'], ['1', 'DRAFT']],
			fields: ['fs_kd_status', 'fs_nm_status']
		});

		var cboStatusIn = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '50%',
			displayField: 'fs_nm_status',
			editable: false,
			fieldLabel: 'Status',
			id: 'cboStatusIn',
			name: 'cboStatusIn',
			value: '1',
			valueField: 'fs_kd_status',
			xtype: 'combobox',
			store: grupStatusIn
		};

		var grupRefnoIn = Ext.create('Ext.data.Store', {
			autoLoad: false,
			fields: [
				'fs_kd_dept','fs_count','fs_nm_dept','fs_kd_strx',
				'fs_nm_strx','fs_refno','fd_refno',
				'fs_doc','fd_doc','fs_descrp',
				'fs_kd_status','fs_nm_status','fs_cekgiro'
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
				url: 'cashbank/cbin_refno'
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_kd_dept': Ext.getCmp('txtDeptIn').getValue(),
						'fs_count': Ext.getCmp('txtCountIn').getValue(),
						'fs_kd_strx': Ext.getCmp('cboTrsIn').getValue(),
						'fs_refno': Ext.getCmp('cboRefnoIn').getValue()
					});
				}
			}
		});

		var winGrid2 = Ext.create('Ext.ux.LiveSearchGridPanel', {
			autoDestroy: true,
			height: 450,
			width: 750,
			sortableColumns: false,
			store: grupRefnoIn,
			bbar: Ext.create('Ext.PagingToolbar', {
				displayInfo: true,
				pageSize: 25,
				plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
				store: grupRefnoIn,
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
				{text: "Dept Cd", dataIndex: 'fs_kd_dept', menuDisabled: true, hidden: true},
				{text: "Dept Count", dataIndex: 'fs_count', menuDisabled: true, hidden: true},
				{text: "Dept Name", dataIndex: 'fs_nm_dept', menuDisabled: true, hidden: true},
				{text: "sTrx Cd", dataIndex: 'fs_kd_strx', menuDisabled: true, hidden: true},
				{text: "sTrx", dataIndex: 'fs_nm_strx', menuDisabled: true, hidden: true},
				{text: "Ref No", dataIndex: 'fs_refno', menuDisabled: true, width: 200},
				{text: "Ref Date", dataIndex: 'fd_refno', menuDisabled: true, width: 80},
				{text: "Doc No", dataIndex: 'fs_doc', menuDisabled: true, width: 100},
				{text: "Doc Date", dataIndex: 'fd_doc', menuDisabled: true, hidden: true},
				{text: "Descrp", dataIndex: 'fs_descrp', menuDisabled: true, width: 220},
				{text: "Status Cd", dataIndex: 'fs_kd_status', menuDisabled: true, hidden: true},
				{text: "Status", dataIndex: 'fs_nm_status', menuDisabled: true, width: 80},
				{text: "Cek Giro", dataIndex: 'fs_cekgiro', menuDisabled: true, hidden: true}
			],
			listeners: {
				itemdblclick: function(grid, record)
				{
					Ext.getCmp('cboDeptIn').setValue(record.get('fs_nm_dept'));
					Ext.getCmp('txtDeptIn').setValue(record.get('fs_kd_dept'));
					Ext.getCmp('txtCountIn').setValue(record.get('fs_count'));
					Ext.getCmp('cboTrsIn').setValue(record.get('fs_kd_strx'));
					Ext.getCmp('cboStatusIn').setValue(record.get('fs_kd_status'));
					Ext.getCmp('cboRefnoIn').setValue(record.get('fs_refno'));
					Ext.getCmp('txtDocnoIn').setValue(record.get('fs_doc'));
					Ext.getCmp('txtNoteIn').setValue(record.get('fs_descrp'));
					Ext.getCmp('txtRefnodtIn').setValue(tglDMY(record.get('fd_refno')));
					Ext.getCmp('txtDocnodtIn').setValue(tglDMY(record.get('fd_doc')));
					
					grupGridIn.load();
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
					grupRefnoIn.load();
					vMask.show();
				}
			}
		});

		var cboRefnoIn = {
			anchor: '90%',
			editable: true,
			emptyText: 'AUTOMATIC',
			fieldLabel: 'Ref. No',
			id: 'cboRefnoIn',
			name: 'cboRefnoIn',
			valueField: 'fs_refno',
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
						winCari2.show();
						winCari2.center();
					}
				}
			}
		};

		var txtRefnodtIn = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '50%',
			editable: true,
			fieldLabel: 'Ref. Date',
			format: 'd-m-Y',
			id: 'txtRefnodtIn',
			maskRe: /[0-9-]/,
			minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
			name: 'txtRefnodtIn',
			value: new Date(),
			xtype: 'datefield'
		};

		var txtDocnoIn = {
			anchor: '90%',
			fieldLabel: 'Doc. No',
			id: 'txtDocnoIn',
			name: 'txtDocnoIn',
			xtype: 'textfield'
		};

		var txtDocnodtIn = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '50%',
			editable: true,
			fieldLabel: 'Doc. Date',
			format: 'd-m-Y',
			id: 'txtDocnodtIn',
			maskRe: /[0-9-]/,
			minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
			name: 'txtDocnodtIn',
			value: new Date(),
			xtype: 'datefield'
		};

		var txtNoteIn = {
			anchor: '77.8%',
			fieldLabel: 'Note',
			grow: true,
			growMin: 35,
			growMax: 35,
			id: 'txtNoteIn',
			name: 'txtNoteIn',
			xtype: 'textareafield'
		};

		var grupAcnoIn = Ext.create('Ext.data.Store', {
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
				url: 'cashbank/cbin_acno'
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_kd_acno': Ext.getCmp('cboAcnoIn').getValue(),
						'fs_nm_acno': Ext.getCmp('cboAcnoIn').getValue()
					});
				}
			}
		});

		var winGrid3 = Ext.create('Ext.ux.LiveSearchGridPanel', {
			autoDestroy: true,
			height: 450,
			width: 550,
			sortableColumns: false,
			store: grupAcnoIn,
			bbar: Ext.create('Ext.PagingToolbar', {
				displayInfo: true,
				pageSize: 25,
				plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
				store: grupAcnoIn,
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
				{text: "Acc. No", dataIndex: 'fs_kd_acno', menuDisabled: true, width: 150},
				{text: "Acc. Name", dataIndex: 'fs_nm_acno', menuDisabled: true, width: 330}
			],
			listeners: {
				itemdblclick: function(grid, record)
				{
					Ext.getCmp('cboAcnoIn').setValue(record.get('fs_kd_acno'));
					Ext.getCmp('txtAcnoIn').setValue(record.get('fs_nm_acno'));
					
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
					grupAcnoIn.load();
					vMask.show();
				}
			}
		});

		var grupGridAcnoIn = Ext.create('Ext.data.Store', {
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
				url: 'cashbank/cbin_acno'
			}
		});

		var winGrid4 = Ext.create('Ext.ux.LiveSearchGridPanel', {
			autoDestroy: true,
			height: 450,
			width: 550,
			sortableColumns: false,
			store: grupGridAcnoIn,
			bbar: Ext.create('Ext.PagingToolbar', {
				displayInfo: true,
				pageSize: 25,
				plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
				store: grupGridAcnoIn,
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
				{text: "Acc. No", dataIndex: 'fs_kd_acno', menuDisabled: true, width: 150},
				{text: "Acc. Name", dataIndex: 'fs_nm_acno', menuDisabled: true, width: 330}
			],
			listeners: {
				itemdblclick: function(grid, record)
				{
					var recordgrid = gridDetailIn.getSelectionModel().getSelection()[0];
					recordgrid.set('fs_kd_acno',record.get('fs_kd_acno'));
					recordgrid.set('fs_nm_acno',record.get('fs_nm_acno'));
					
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
					grupGridAcnoIn.load();
					vMask.show();
				}
			}
		});

		Ext.define('DataGrid', {
			extend: 'Ext.data.Model',
			fields: [
				{name: 'fs_kd_acno', type: 'string'},
				{name: 'fs_nm_acno', type: 'string'},
				{name: 'fs_note', type: 'string'},
				{name: 'fn_debet', type: 'float'},
				{name: 'fn_credit', type: 'float'},
				{name: 'fs_seqno', type: 'string'}
			]
		});

		var grupGridIn = Ext.create('Ext.data.Store', {
			autoLoad: false,
			model: 'DataGrid',
			proxy: {
				actionMethods: {
					read: 'POST'
				},
				reader: {
					type: 'json'
				},
				type: 'ajax',
				url: 'cashbank/cbin_isigrid'
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_kd_dept': Ext.getCmp('txtDeptIn').getValue(),
						'fs_count': Ext.getCmp('txtCountIn').getValue(),
						'fs_kd_strx': Ext.getCmp('cboTrsIn').getValue(),
						'fs_refno': Ext.getCmp('cboRefnoIn').getValue()
					});
				},
				load: function() {
					totalDCin();
				}
			}
		});

		var gridDetailIn = Ext.create('Ext.grid.Panel', {
			defaultType: 'textfield',
			height: 248,
			sortableColumns: false,
			store: grupGridIn,
			columns: [{
				xtype: 'rownumberer'
			},{
				text: 'Acc. No',
				dataIndex: 'fs_kd_acno',
				flex: 0.75,
				menuDisabled: true,
				editor: {
					editable: false,
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
								winCari4.show();
								winCari4.center();
							}
						}
					}
				}
			},{
				text: 'Acc. Name',
				dataIndex: 'fs_nm_acno',
				flex: 1,
				menuDisabled: true
			},{
				text: 'Note',
				dataIndex: 'fs_note',
				flex: 1.5,
				menuDisabled: true,
				editor: {
					xtype: 'textfield'
				}
			},{
				align: 'right',
				dataIndex: 'fn_debet',
				flex: 0.75,
				format: '0,000.00',
				menuDisabled: true,
				text: 'Debit',
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
				dataIndex: 'fn_credit',
				flex: 0.75,
				format: '0,000.00',
				menuDisabled: true,
				text: 'Credit',
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
				text: 'Seqno',
				dataIndex: 'fs_seqno',
				flex: 0.35,
				menuDisabled: true,
				hidden: true,
				renderer: function(grid, value, record, rowIndex, colIndex) {
					return zeroPad(rowIndex + 1, 6);
				}
			}],
			listeners: {
				selectionchange: function(view, records) {
					gridDetailIn.down('#removeData').setDisabled(!records.length);
				}
			},
			plugins: [
				cellEditingIn
			],
			bbar: ['->',{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						Ext.create('Ext.ux.form.NumericField', {
							alwaysDisplayDecimals: true,
							anchor: '75%',
							currencySymbol: null,
							decimalPrecision: 2,
							decimalSeparator: '.',
							editable: false,
							fieldLabel: 'Total Debit',
							fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
							hideTrigger: true,
							labelAlign: 'right',
							id: 'txtTotalDebitIn',
							keyNavEnabled: false,
							mouseWheelEnabled: false,
							name: 'txtTotalDebitIn',
							thousandSeparator: ',',
							useThousandSeparator: true,
							value: 0
						})
					]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						Ext.create('Ext.ux.form.NumericField', {
							alwaysDisplayDecimals: true,
							anchor: '75%',
							currencySymbol: null,
							decimalPrecision: 2,
							decimalSeparator: '.',
							editable: false,
							fieldLabel: 'Total Credit',
							fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
							hideTrigger: true,
							labelAlign: 'right',
							id: 'txtTotalCreditIn',
							keyNavEnabled: false,
							mouseWheelEnabled: false,
							name: 'txtTotalCreditIn',
							thousandSeparator: ',',
							useThousandSeparator: true,
							value: 0
						})
					]
				}]
			}],
			tbar: [{
				flex: 1.5,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					anchor: '95%',
					emptyText: 'Select an Account',
					fieldLabel: 'Account No',
					id: 'cboAcnoIn',
					labelAlign: 'top',
					name: 'cboAcnoIn',
					xtype: 'textfield',
					listeners: {
						change: function(field, newValue) {
							Ext.getCmp('txtAcnoIn').setValue('');
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
				},{
					anchor: '95%',
					emptyText: 'Enter an Acc No',
					hidden: true,
					id: 'txtAcnoIn',
					name: 'txtAcnoIn',
					xtype: 'textfield'
				}]
			},{
				flex: 1.75,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					anchor: '95%',
					emptyText: 'Enter a Description',
					fieldLabel: 'Description',
					id: 'txtDescIn',
					labelAlign: 'top',
					name: 'txtDescIn',
					xtype: 'textfield'
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
						fieldLabel: 'Debit',
						fieldStyle: 'text-align: right;',
						hideTrigger: true,
						id: 'txtDebitIn',
						keyNavEnabled: false,
						labelAlign: 'top',
						mouseWheelEnabled: false,
						name: 'txtDebitIn',
						thousandSeparator: ',',
						useThousandSeparator: true,
						value: 0,
						listeners: {
							change: function(value) {
								if (Ext.isEmpty(Ext.getCmp('txtDebitIn').getValue())) {
									Ext.getCmp('txtDebitIn').setValue(0);
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
						alwaysDisplayDecimals: true,
						anchor: '95%',
						currencySymbol: null,
						decimalPrecision: 2,
						decimalSeparator: '.',
						fieldLabel: 'Credit',
						fieldStyle: 'text-align: right;',
						hideTrigger: true,
						id: 'txtCreditIn',
						keyNavEnabled: false,
						labelAlign: 'top',
						mouseWheelEnabled: false,
						name: 'txtCreditIn',
						thousandSeparator: ',',
						useThousandSeparator: true,
						value: 0,
						listeners: {
							change: function(value) {
								if (Ext.isEmpty(Ext.getCmp('txtCreditIn').getValue())) {
									Ext.getCmp('txtCreditIn').setValue(0);
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
						var xtotal = grupGridIn.getCount();
						var xdata = Ext.create('DataGrid', {
							fs_kd_acno: Ext.getCmp('cboAcnoIn').getValue(),
							fs_nm_acno: Ext.getCmp('txtAcnoIn').getValue(),
							fs_note: Ext.getCmp('txtDescIn').getValue(),
							fn_debet: Ext.getCmp('txtDebitIn').getValue(),
							fn_credit: Ext.getCmp('txtCreditIn').getValue(),
							fs_seqno: ''
						});
						
						var xacno = Ext.getCmp('txtAcnoIn').getValue();
						var xdebit = Ext.getCmp('txtDebitIn').getValue();
						var xcredit = Ext.getCmp('txtCreditIn').getValue();
						if (xacno.trim() === '') {
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.OK,
								closable: false,
								icon: Ext.Msg.WARNING,
								msg: 'Account is not in the list!',
								title: 'IDS'
							});
							return;
						}
						
						if ((xdebit === 0 && xcredit === 0) || (xdebit > 0 && xcredit > 0)) {
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.OK,
								closable: false,
								icon: Ext.Msg.WARNING,
								msg: 'One of debit or credit should be zero!',
								title: 'IDS'
							});
							return;
						}
						
						grupGridIn.insert(xtotal, xdata);
						Ext.getCmp('cboAcnoIn').setValue('');
						Ext.getCmp('txtAcnoIn').setValue('');
						Ext.getCmp('txtDescIn').setValue('');
						Ext.getCmp('txtDebitIn').setValue(0);
						Ext.getCmp('txtCreditIn').setValue(0);
						totalDCin();
					}
				},{
					iconCls: 'icon-delete',
					itemId: 'removeData',
					text: 'Delete',
					handler: function() {
						var sm = gridDetailIn.getSelectionModel();
						cellEditingIn.cancelEdit();
						grupGridIn.remove(sm.getSelection());
						gridDetailIn.getView().refresh();
						if (grupGridIn.getCount() > 0) {
							sm.select(0);
						}
						totalDCin();
					},
					disabled: true
				}]
			}],
			viewConfig: {
				getRowClass: function() {
					return 'rowwrap';
				},
				listeners: {
					render: gridTooltip
				},
				markDirty: false,
				stripeRows: true
			}
		});

		function fnResetIn(){
			Ext.getCmp('cboDeptIn').setValue('');
			Ext.getCmp('txtDeptIn').setValue('');
			Ext.getCmp('txtCountIn').setValue('');
			Ext.getCmp('cboStatusIn').setValue('1');
			Ext.getCmp('cboRefnoIn').setValue('');
			Ext.getCmp('txtRefnodtIn').setValue(new Date());
			Ext.getCmp('txtDocnoIn').setValue('');
			Ext.getCmp('txtDocnodtIn').setValue(new Date());
			Ext.getCmp('txtNoteIn').setValue('');
			Ext.getCmp('cboAcnoIn').setValue('');
			Ext.getCmp('txtAcnoIn').setValue('');
			Ext.getCmp('txtDescIn').setValue('');
			Ext.getCmp('txtDebitIn').setValue('0');
			Ext.getCmp('txtCreditIn').setValue('0');
			Ext.getCmp('txtTotalDebitIn').setValue('0');
			Ext.getCmp('txtTotalCreditIn').setValue('0');
			
			grupGridIn.removeAll();
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'alamat/nilaidefa',
				success: function(response) {
					var xtext = Ext.decode(response.responseText);
					
					if (xtext.sukses === true) {
						Ext.getCmp('cboDeptIn').setValue(xtext.nmdept);
						Ext.getCmp('txtDeptIn').setValue(xtext.deptcd);
						Ext.getCmp('txtCountIn').setValue(xtext.count);
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
		}

		function fnResetIn1(){
			Ext.getCmp('cboStatusIn').setValue('1');
			Ext.getCmp('cboRefnoIn').setValue('');
			Ext.getCmp('txtRefnodtIn').setValue(new Date());
			Ext.getCmp('txtDocnoIn').setValue('');
			Ext.getCmp('txtDocnodtIn').setValue(new Date());
			Ext.getCmp('txtNoteIn').setValue('');
			Ext.getCmp('cboAcnoIn').setValue('');
			Ext.getCmp('txtAcnoIn').setValue('');
			Ext.getCmp('txtDescIn').setValue('');
			Ext.getCmp('txtDebitIn').setValue('0');
			Ext.getCmp('txtCreditIn').setValue('0');
			Ext.getCmp('txtTotalDebitIn').setValue('0');
			Ext.getCmp('txtTotalCreditIn').setValue('0');
			
			grupGridIn.removeAll();
		};
		
		function fnCBInCekSave() {
			if (this.up('form').getForm().isValid()) {
				var xdebit = Ext.getCmp('txtTotalDebitIn').getValue();
				var xcredit = Ext.getCmp('txtTotalCreditIn').getValue();
				
				if (xdebit == xcredit) {
					Ext.Ajax.request({
						method: 'POST',
						url: 'cashbank/cbin_ceksave',
						params: {
							'fs_kd_dept': Ext.getCmp('txtDeptIn').getValue(),
							'fs_count': Ext.getCmp('txtCountIn').getValue(),
							'fs_kd_strx': Ext.getCmp('cboTrsIn').getValue(),
							'fs_refno': Ext.getCmp('cboRefnoIn').getValue()
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
									fnCBInSave();
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
												fnCBInSave();
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
				else {
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: 'Saving Failed, total debits and credits are not balanced!!',
						title: 'IDS'
					});
				}
			}
		}

		function fnCBInSave() {
			var xkdacno = '';
			var xnote = '';
			var xamt = '';
			var xseqno = '';
			var xdc = '';
			var xdc2 = '';
			var store = gridDetailIn.getStore();
			
			store.each(function(record, idx) {
				xkdacno = xkdacno +'|'+ record.get('fs_kd_acno');
				xnote = xnote +'|'+ record.get('fs_note');
				xseqno = xseqno +'|'+ record.get('fs_seqno');
				if (record.get('fn_debet') !== 0) {
					xamt = xamt +'|'+ record.get('fn_debet');
					xdc2 = 'D';
				}
				else {
					xamt = xamt +'|'+ record.get('fn_credit');
					xdc2 = 'C';
				}
				xdc = xdc +'|'+ xdc2;
			});
			
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'cashbank/cbin_save',
				params: {
					'fs_kd_dept': Ext.getCmp('txtDeptIn').getValue(),
					'fs_count': Ext.getCmp('txtCountIn').getValue(),
					'fs_kd_strx': Ext.getCmp('cboTrsIn').getValue(),
					'fs_kd_status': Ext.getCmp('cboStatusIn').getValue(),
					'fs_refno': Ext.getCmp('cboRefnoIn').getValue(),
					'fs_refnodt': Ext.Date.format(Ext.getCmp('txtRefnodtIn').getValue(), 'Ymd'),
					'fs_docno': Ext.getCmp('txtDocnoIn').getValue(),
					'fs_docnodt': Ext.Date.format(Ext.getCmp('txtDocnodtIn').getValue(), 'Ymd'),
					'fs_note': Ext.getCmp('txtNoteIn').getValue(),
					'fs_total': Ext.getCmp('txtTotalDebitIn').getValue(),
					'fs_cekgiro': '',
					'fs_kd_acno': xkdacno,
					'fs_notedetil': xnote,
					'fn_amt': xamt,
					'fs_seqno': xseqno,
					'fs_dc': xdc
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
						Ext.getCmp('cboRefnoIn').setValue(xtext.refno.trim());
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

		function fnCBInCekRemove() {
			Ext.Ajax.request({
				method: 'POST',
				url: 'cashbank/cbin_cekremove',
				params: {
					'fs_kd_dept': Ext.getCmp('txtDeptIn').getValue(),
					'fs_count': Ext.getCmp('txtCountIn').getValue(),
					'fs_kd_strx': Ext.getCmp('cboTrsIn').getValue(),
					'fs_refno': Ext.getCmp('cboRefnoIn').getValue()
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
										fnCBInRemove();
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
						msg: 'Remove Failed, Connection Failed!!',
						title: 'IDS'
					});
					vMask.hide();
				}
			});
		}

		function fnCBInRemove() {
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'cashbank/cbin_remove',
				params: {
					'fs_kd_dept': Ext.getCmp('txtDeptIn').getValue(),
					'fs_count': Ext.getCmp('txtCountIn').getValue(),
					'fs_kd_strx': Ext.getCmp('cboTrsIn').getValue(),
					'fs_refno': Ext.getCmp('cboRefnoIn').getValue()
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
						fnResetIn();
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

		function fnPrintCashBankIn() {
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				timeout: 60000,
				url: 'cashbank/printcb',
				params: {
					
					'fs_refno': Ext.getCmp('cboRefnoIn').getValue(),
					'fs_kd_dept': Ext.getCmp('txtDeptIn').getValue(),
					'fs_count': Ext.getCmp('txtCountIn').getValue(),
					'fs_kd_strx': Ext.getCmp('cboTrsIn').getValue()
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
							title: 'Cash Bank In',
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



		var cellEditingOut = Ext.create('Ext.grid.plugin.CellEditing', {
			clicksToEdit: 2,
			listeners: {
				edit: function(editor, e) {
					totalDCout();
				}
			}
		});

		function totalDCout() {
			var xtotaldebit = 0;
			var xtotalcredit = 0;
			var store = gridDetailOut.getStore();
			store.each(function(record, idx) {
				xtotaldebit = xtotaldebit + record.get('fn_debet');
				xtotalcredit = xtotalcredit + record.get('fn_credit');
			});
			Ext.getCmp('txtTotalDebitOut').setValue(xtotaldebit);
			Ext.getCmp('txtTotalCreditOut').setValue(xtotalcredit);
		}

		var grupDeptOut = Ext.create('Ext.data.Store', {
			autoLoad: false,
			fields: ['fs_code','fs_count','fs_nm_code'],
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
				url: 'cashbank/cbout_dept'
			}
		});

		var winGrid5 = Ext.create('Ext.ux.LiveSearchGridPanel', {
			autoDestroy: true,
			height: 450,
			width: 550,
			sortableColumns: false,
			store: grupDeptOut,
			bbar: Ext.create('Ext.PagingToolbar', {
				displayInfo: true,
				pageSize: 25,
				plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
				store: grupDeptOut,
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
				{text: "Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
				{text: "Count", dataIndex: 'fs_count', menuDisabled: true, width: 100},
				{text: "Dept Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 280}
			],
			listeners: {
				itemdblclick: function(grid, record)
				{
					Ext.getCmp('cboDeptOut').setValue(record.get('fs_nm_code'));
					Ext.getCmp('txtDeptOut').setValue(record.get('fs_code'));
					Ext.getCmp('txtCountOut').setValue(record.get('fs_count'));
					
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
					grupDeptOut.load();
					vMask.show();
				}
			}
		});

		var cboDeptOut = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '90%',
			displayField: 'fs_nm_code',
			editable: false,
			emptyText: 'Select a Department',
			fieldLabel: 'Department',
			id: 'cboDeptOut',
			name: 'cboDeptOut',
			valueField: 'fs_code',
			xtype: 'textfield',
			listeners: {
				change: function(field, newValue) {
					Ext.getCmp('txtDeptOut').setValue('');
					Ext.getCmp('txtCountOut').setValue('');
					fnResetOut1();
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

		var txtDeptOut = {
			anchor: '90%',
			fieldLabel: 'Dept Code',
			hidden: true,
			id: 'txtDeptOut',
			name: 'txtDeptOut',
			xtype: 'textfield'
		};

		var txtCountOut = {
			anchor: '90%',
			fieldLabel: 'Dept Count',
			hidden: true,
			id: 'txtCountOut',
			name: 'txtCountOut',
			xtype: 'textfield'
		};

		var txtCekOut = {
			anchor: '100%',
			fieldLabel: 'Cheque / Giro No',
			id: 'txtCekOut',
			labelWidth: 100,
			name: 'txtCekOut',
			xtype: 'textfield'
		};

		var grupsTrxOut = Ext.create('Ext.data.Store', {
			autoLoad: false,
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
				url: 'cashbank/cbout_trs'
			}
		});

		var cboTrsOut = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '50%',
			displayField: 'fs_nm_strx',
			editable: false,
			emptyText: 'Select a Trans Type',
			fieldLabel: 'Trans Type',
			id: 'cboTrsOut',
			name: 'cboTrsOut',
			store: grupsTrxOut,
			valueField: 'fs_kd_strx',
			xtype: 'combobox',
			listeners: {
				change: function(combo, value) {
					fnResetOut1();
				}
			}
		};

		var grupStatusOut = Ext.create('Ext.data.ArrayStore', {
			autoLoad: false,
			data: [['0', 'APPROVED'], ['1', 'DRAFT']],
			fields: ['fs_kd_status', 'fs_nm_status']
		});

		var cboStatusOut = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '50%',
			displayField: 'fs_nm_status',
			editable: false,
			fieldLabel: 'Status',
			id: 'cboStatusOut',
			name: 'cboStatusOut',
			value: '1',
			valueField: 'fs_kd_status',
			xtype: 'combobox',
			store: grupStatusOut
		};

		var grupRefnoOut = Ext.create('Ext.data.Store', {
			autoLoad: false,
			fields: [
				'fs_kd_dept','fs_count','fs_nm_dept','fs_kd_strx',
				'fs_nm_strx','fs_refno','fd_refno',
				'fs_doc','fd_doc','fs_descrp',
				'fs_kd_status','fs_nm_status','fs_cekgiro'
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
				url: 'cashbank/cbout_refno'
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_kd_dept': Ext.getCmp('txtDeptOut').getValue(),
						'fs_count': Ext.getCmp('txtCountOut').getValue(),
						'fs_kd_strx': Ext.getCmp('cboTrsOut').getValue(),
						'fs_refno': Ext.getCmp('cboRefnoOut').getValue()
					});
				}
			}
		});

		var winGrid6 = Ext.create('Ext.ux.LiveSearchGridPanel', {
			autoDestroy: true,
			height: 450,
			width: 750,
			sortableColumns: false,
			store: grupRefnoOut,
			bbar: Ext.create('Ext.PagingToolbar', {
				displayInfo: true,
				pageSize: 25,
				plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
				store: grupRefnoOut,
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
				{text: "Dept Cd", dataIndex: 'fs_kd_dept', menuDisabled: true, hidden: true},
				{text: "Dept Count", dataIndex: 'fs_count', menuDisabled: true, hidden: true},
				{text: "Dept Name", dataIndex: 'fs_nm_dept', menuDisabled: true, hidden: true},
				{text: "sTrx Cd", dataIndex: 'fs_kd_strx', menuDisabled: true, hidden: true},
				{text: "sTrx", dataIndex: 'fs_nm_strx', menuDisabled: true, hidden: true},
				{text: "Ref No", dataIndex: 'fs_refno', menuDisabled: true, width: 200},
				{text: "Ref Date", dataIndex: 'fd_refno', menuDisabled: true, width: 80},
				{text: "Doc No", dataIndex: 'fs_doc', menuDisabled: true, width: 100},
				{text: "Doc Date", dataIndex: 'fd_doc', menuDisabled: true, hidden: true},
				{text: "Descrp", dataIndex: 'fs_descrp', menuDisabled: true, width: 220},
				{text: "Status Cd", dataIndex: 'fs_kd_status', menuDisabled: true, hidden: true},
				{text: "Status", dataIndex: 'fs_nm_status', menuDisabled: true, width: 80},
				{text: "Cek Giro", dataIndex: 'fs_cekgiro', menuDisabled: true, hidden: true}
			],
			listeners: {
				itemdblclick: function(grid, record)
				{
					Ext.getCmp('cboDeptOut').setValue(record.get('fs_nm_dept'));
					Ext.getCmp('txtDeptOut').setValue(record.get('fs_kd_dept'));
					Ext.getCmp('txtCountOut').setValue(record.get('fs_count'));
					Ext.getCmp('cboTrsOut').setValue(record.get('fs_kd_strx'));
					Ext.getCmp('cboStatusOut').setValue(record.get('fs_kd_status'));
					Ext.getCmp('cboRefnoOut').setValue(record.get('fs_refno'));
					Ext.getCmp('txtDocnoOut').setValue(record.get('fs_doc'));
					Ext.getCmp('txtNoteOut').setValue(record.get('fs_descrp'));
					Ext.getCmp('txtRefnodtOut').setValue(tglDMY(record.get('fd_refno')));
					Ext.getCmp('txtDocnodtOut').setValue(tglDMY(record.get('fd_doc')));
					Ext.getCmp('txtCekOut').setValue(record.get('fs_cekgiro'));
					
					grupGridOut.load();
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
					grupRefnoOut.load();
					vMask.show();
				}
			}
		});

		var cboRefnoOut = {
			anchor: '90%',
			editable: true,
			emptyText: 'AUTOMATIC',
			fieldLabel: 'Ref. No',
			id: 'cboRefnoOut',
			name: 'cboRefnoOut',
			valueField: 'fs_refno',
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
						winCari6.show();
						winCari6.center();
					}
				}
			}
		};

		var txtRefnodtOut = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '50%',
			editable: true,
			fieldLabel: 'Ref. Date',
			format: 'd-m-Y',
			id: 'txtRefnodtOut',
			maskRe: /[0-9-]/,
			minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
			name: 'txtRefnodtOut',
			value: new Date(),
			xtype: 'datefield'
		};

		var txtDocnoOut = {
			anchor: '90%',
			fieldLabel: 'Doc. No',
			id: 'txtDocnoOut',
			name: 'txtDocnoOut',
			xtype: 'textfield'
		};

		var txtDocnodtOut = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '50%',
			editable: true,
			fieldLabel: 'Doc. Date',
			format: 'd-m-Y',
			id: 'txtDocnodtOut',
			maskRe: /[0-9-]/,
			minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
			name: 'txtDocnodtOut',
			value: new Date(),
			xtype: 'datefield'
		};

		var txtNoteOut = {
			anchor: '77.8%',
			fieldLabel: 'Note',
			grow: true,
			growMin: 35,
			growMax: 35,
			id: 'txtNoteOut',
			name: 'txtNoteOut',
			xtype: 'textareafield'
		};

		var grupAcnoOut = Ext.create('Ext.data.Store', {
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
				url: 'cashbank/cbout_acno'
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_kd_acno': Ext.getCmp('cboAcnoOut').getValue(),
						'fs_nm_acno': Ext.getCmp('cboAcnoOut').getValue()
					});
				}
			}
		});

		var winGrid7 = Ext.create('Ext.ux.LiveSearchGridPanel', {
			autoDestroy: true,
			height: 450,
			width: 550,
			sortableColumns: false,
			store: grupAcnoOut,
			bbar: Ext.create('Ext.PagingToolbar', {
				displayInfo: true,
				pageSize: 25,
				plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
				store: grupAcnoOut,
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
				{text: "Acc. No", dataIndex: 'fs_kd_acno', menuDisabled: true, width: 150},
				{text: "Acc. Name", dataIndex: 'fs_nm_acno', menuDisabled: true, width: 330}
			],
			listeners: {
				itemdblclick: function(grid, record)
				{
					Ext.getCmp('cboAcnoOut').setValue(record.get('fs_kd_acno'));
					Ext.getCmp('txtAcnoOut').setValue(record.get('fs_nm_acno'));
					
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
					grupAcnoOut.load();
					vMask.show();
				}
			}
		});

		var grupGridAcnoOut = Ext.create('Ext.data.Store', {
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
				url: 'cashbank/cbout_acno'
			}
		});

		var winGrid8 = Ext.create('Ext.ux.LiveSearchGridPanel', {
			autoDestroy: true,
			height: 450,
			width: 550,
			sortableColumns: false,
			store: grupGridAcnoOut,
			bbar: Ext.create('Ext.PagingToolbar', {
				displayInfo: true,
				pageSize: 25,
				plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
				store: grupGridAcnoOut,
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
				{text: "Acc. No", dataIndex: 'fs_kd_acno', menuDisabled: true, width: 150},
				{text: "Acc. Name", dataIndex: 'fs_nm_acno', menuDisabled: true, width: 330}
			],
			listeners: {
				itemdblclick: function(grid, record)
				{
					var recordgrid = gridDetailOut.getSelectionModel().getSelection()[0];
					recordgrid.set('fs_kd_acno',record.get('fs_kd_acno'));
					recordgrid.set('fs_nm_acno',record.get('fs_nm_acno'));
					
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
					grupGridAcnoOut.load();
					vMask.show();
				}
			}
		});

		var grupGridOut = Ext.create('Ext.data.Store', {
			autoLoad: false,
			model: 'DataGrid',
			proxy: {
				actionMethods: {
					read: 'POST'
				},
				reader: {
					type: 'json'
				},
				type: 'ajax',
				url: 'cashbank/cbout_isigrid'
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_kd_dept': Ext.getCmp('txtDeptOut').getValue(),
						'fs_count': Ext.getCmp('txtCountOut').getValue(),
						'fs_kd_strx': Ext.getCmp('cboTrsOut').getValue(),
						'fs_refno': Ext.getCmp('cboRefnoOut').getValue()
					});
				},
				load: function() {
					totalDCout();
				}
			}
		});

		var gridDetailOut = Ext.create('Ext.grid.Panel', {
			defaultType: 'textfield',
			height: 248,
			sortableColumns: false,
			store: grupGridOut,
			columns: [{
				xtype: 'rownumberer'
			},{
				text: 'Acc. No',
				dataIndex: 'fs_kd_acno',
				flex: 0.75,
				menuDisabled: true,
				editor: {
					editable: false,
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
								winCari8.show();
								winCari8.center();
							}
						}
					}
				}
			},{
				text: 'Acc. Name',
				dataIndex: 'fs_nm_acno',
				flex: 1,
				menuDisabled: true
			},{
				text: 'Note',
				dataIndex: 'fs_note',
				flex: 1.5,
				menuDisabled: true,
				editor: {
					xtype: 'textfield'
				}
			},{
				align: 'right',
				dataIndex: 'fn_debet',
				flex: 0.75,
				format: '0,000.00',
				menuDisabled: true,
				text: 'Debit',
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
				dataIndex: 'fn_credit',
				flex: 0.75,
				format: '0,000.00',
				menuDisabled: true,
				text: 'Credit',
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
				text: 'Seqno',
				dataIndex: 'fs_seqno',
				flex: 0.35,
				hidden: true,
				menuDisabled: true,
				renderer: function(grid, value, record, rowIndex, colIndex) {
					return zeroPad(rowIndex + 1, 6);
				}
			}],
			listeners: {
				selectionchange: function(view, records) {
					gridDetailOut.down('#removeData').setDisabled(!records.length);
				}
			},
			plugins: [
				cellEditingOut
			],
			bbar: ['->',{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						Ext.create('Ext.ux.form.NumericField', {
							alwaysDisplayDecimals: true,
							anchor: '75%',
							currencySymbol: null,
							decimalPrecision: 2,
							decimalSeparator: '.',
							editable: false,
							fieldLabel: 'Total Debit',
							fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
							hideTrigger: true,
							labelAlign: 'right',
							id: 'txtTotalDebitOut',
							keyNavEnabled: false,
							mouseWheelEnabled: false,
							name: 'txtTotalDebitOut',
							thousandSeparator: ',',
							useThousandSeparator: true,
							value: 0
						})
					]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						Ext.create('Ext.ux.form.NumericField', {
							alwaysDisplayDecimals: true,
							anchor: '75%',
							currencySymbol: null,
							decimalPrecision: 2,
							decimalSeparator: '.',
							editable: false,
							fieldLabel: 'Total Credit',
							fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
							hideTrigger: true,
							labelAlign: 'right',
							id: 'txtTotalCreditOut',
							keyNavEnabled: false,
							mouseWheelEnabled: false,
							name: 'txtTotalCreditOut',
							thousandSeparator: ',',
							useThousandSeparator: true,
							value: 0
						})
					]
				}]
			}],
			tbar: [{
				flex: 1.5,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					anchor: '95%',
					emptyText: 'Select an Account',
					fieldLabel: 'Account No',
					id: 'cboAcnoOut',
					labelAlign: 'top',
					name: 'cboAcnoOut',
					xtype: 'textfield',
					listeners: {
						change: function(field, newValue) {
							Ext.getCmp('txtAcnoOut').setValue('');
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
					emptyText: 'Enter an Acc No',
					hidden: true,
					id: 'txtAcnoOut',
					name: 'txtAcnoOut',
					xtype: 'textfield'
				}]
			},{
				flex: 1.75,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					anchor: '95%',
					emptyText: 'Enter a Description',
					fieldLabel: 'Description',
					id: 'txtDescOut',
					labelAlign: 'top',
					name: 'txtDescOut',
					xtype: 'textfield'
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
						fieldLabel: 'Debit',
						fieldStyle: 'text-align: right;',
						hideTrigger: true,
						id: 'txtDebitOut',
						keyNavEnabled: false,
						labelAlign: 'top',
						mouseWheelEnabled: false,
						name: 'txtDebitOut',
						thousandSeparator: ',',
						useThousandSeparator: true,
						value: 0,
						listeners: {
							change: function(value) {
								if (Ext.isEmpty(Ext.getCmp('txtDebitOut').getValue()))
								{
									Ext.getCmp('txtDebitOut').setValue(0);
								}
								else
								{
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
						alwaysDisplayDecimals: true,
						anchor: '95%',
						currencySymbol: null,
						decimalPrecision: 2,
						decimalSeparator: '.',
						fieldLabel: 'Credit',
						fieldStyle: 'text-align: right;',
						hideTrigger: true,
						id: 'txtCreditOut',
						keyNavEnabled: false,
						labelAlign: 'top',
						mouseWheelEnabled: false,
						name: 'txtCreditOut',
						thousandSeparator: ',',
						useThousandSeparator: true,
						value: 0,
						listeners: {
							change: function(value) {
								if (Ext.isEmpty(Ext.getCmp('txtCreditOut').getValue())) {
									Ext.getCmp('txtCreditOut').setValue(0);
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
						var xtotal = grupGridOut.getCount();
						var xdata = Ext.create('DataGrid', {
							fs_kd_acno: Ext.getCmp('cboAcnoOut').getValue(),
							fs_nm_acno: Ext.getCmp('txtAcnoOut').getValue(),
							fs_note: Ext.getCmp('txtDescOut').getValue(),
							fn_debet: Ext.getCmp('txtDebitOut').getValue(),
							fn_credit: Ext.getCmp('txtCreditOut').getValue(),
							fs_seqno: ''
						});
						
						var xacno = Ext.getCmp('txtAcnoOut').getValue();
						var xdebit = Ext.getCmp('txtDebitOut').getValue();
						var xcredit = Ext.getCmp('txtCreditOut').getValue();
						if (xacno.trim() === '') {
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.OK,
								closable: false,
								icon: Ext.Msg.WARNING,
								msg: 'Account is not in the list!',
								title: 'IDS'
							});
							return;
						}
						
						if ((xdebit === 0 && xcredit === 0) || (xdebit > 0 && xcredit > 0)) {
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.OK,
								closable: false,
								icon: Ext.Msg.WARNING,
								msg: 'One of debit or credit should be zero!',
								title: 'IDS'
							});
							return;
						}
						
						grupGridOut.insert(xtotal, xdata);
						Ext.getCmp('cboAcnoOut').setValue('');
						Ext.getCmp('txtAcnoOut').setValue('');
						Ext.getCmp('txtDescOut').setValue('');
						Ext.getCmp('txtDebitOut').setValue(0);
						Ext.getCmp('txtCreditOut').setValue(0);
						totalDCout();
					}
				},{
					iconCls: 'icon-delete',
					itemId: 'removeData',
					text: 'Delete',
					handler: function() {
						var sm = gridDetailOut.getSelectionModel();
						cellEditingOut.cancelEdit();
						grupGridOut.remove(sm.getSelection());
						gridDetailOut.getView().refresh();
						if (grupGridOut.getCount() > 0) {
							sm.select(0);
						}
						totalDCout();
					},
					disabled: true
				}]
			}],
			viewConfig: {
				getRowClass: function() {
					return 'rowwrap';
				},
				listeners: {
					render: gridTooltip
				},
				markDirty: false,
				stripeRows: true
			}
		});

		function fnResetOut(){
			Ext.getCmp('cboDeptOut').setValue('');
			Ext.getCmp('txtDeptOut').setValue('');
			Ext.getCmp('txtCountOut').setValue('');
			Ext.getCmp('txtCekOut').setValue('');
			Ext.getCmp('cboStatusOut').setValue('1');
			Ext.getCmp('cboRefnoOut').setValue('');
			Ext.getCmp('txtRefnodtOut').setValue(new Date());
			Ext.getCmp('txtDocnoOut').setValue('');
			Ext.getCmp('txtDocnodtOut').setValue(new Date());
			Ext.getCmp('txtNoteOut').setValue('');
			Ext.getCmp('cboAcnoOut').setValue('');
			Ext.getCmp('txtAcnoOut').setValue('');
			Ext.getCmp('txtDescOut').setValue('');
			Ext.getCmp('txtDebitOut').setValue('0');
			Ext.getCmp('txtCreditOut').setValue('0');
			Ext.getCmp('txtTotalDebitOut').setValue('0');
			Ext.getCmp('txtTotalCreditOut').setValue('0');
			
			grupGridOut.removeAll();
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'alamat/nilaidefa',
				success: function(response) {
					var xtext = Ext.decode(response.responseText);
					
					if (xtext.sukses === true) {
						Ext.getCmp('cboDeptOut').setValue(xtext.nmdept);
						Ext.getCmp('txtDeptOut').setValue(xtext.deptcd);
						Ext.getCmp('txtCountOut').setValue(xtext.count);
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
		}

		function fnResetOut1(){
			Ext.getCmp('txtCekOut').setValue('');
			Ext.getCmp('cboStatusOut').setValue('1');
			Ext.getCmp('cboRefnoOut').setValue('');
			Ext.getCmp('txtRefnodtOut').setValue(new Date());
			Ext.getCmp('txtDocnoOut').setValue('');
			Ext.getCmp('txtDocnodtOut').setValue(new Date());
			Ext.getCmp('txtNoteOut').setValue('');
			Ext.getCmp('cboAcnoOut').setValue('');
			Ext.getCmp('txtAcnoOut').setValue('');
			Ext.getCmp('txtDescOut').setValue('');
			Ext.getCmp('txtDebitOut').setValue('0');
			Ext.getCmp('txtCreditOut').setValue('0');
			Ext.getCmp('txtTotalDebitOut').setValue('0');
			Ext.getCmp('txtTotalCreditOut').setValue('0');
			
			grupGridOut.removeAll();
		};
		
		function fnCBOutCekSave() {
			if (this.up('form').getForm().isValid()) {
				var xdebit = Ext.getCmp('txtTotalDebitOut').getValue();
				var xcredit = Ext.getCmp('txtTotalCreditOut').getValue();
				
				if (xdebit == xcredit) {
					Ext.Ajax.request({
						method: 'POST',
						url: 'cashbank/cbin_ceksave',
						params: {
							'fs_kd_dept': Ext.getCmp('txtDeptOut').getValue(),
							'fs_count': Ext.getCmp('txtCountOut').getValue(),
							'fs_kd_strx': Ext.getCmp('cboTrsOut').getValue(),
							'fs_refno': Ext.getCmp('cboRefnoOut').getValue()
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
									fnCBOutSave();
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
												fnCBOutSave();
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
				else {
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: 'Saving Failed, total debits and credits are not balanced!!',
						title: 'IDS'
					});
				}
			}
		}

		function fnCBOutSave() {
			var xkdacno = '';
			var xnote = '';
			var xamt = '';
			var xseqno = '';
			var xdc = '';
			var xdc2 = '';
			var store = gridDetailOut.getStore();
			
			store.each(function(record, idx) {
				xkdacno = xkdacno +'|'+ record.get('fs_kd_acno');
				xnote = xnote +'|'+ record.get('fs_note');
				xseqno = xseqno +'|'+ record.get('fs_seqno');
				if (record.get('fn_debet') !== 0) {
					xamt = xamt +'|'+ record.get('fn_debet');
					xdc2 = 'D';
				}
				else {
					xamt = xamt +'|'+ record.get('fn_credit');
					xdc2 = 'C';
				}
				xdc = xdc +'|'+ xdc2;
			});
			
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'cashbank/cbin_save',
				params: {
					'fs_kd_dept': Ext.getCmp('txtDeptOut').getValue(),
					'fs_count': Ext.getCmp('txtCountOut').getValue(),
					'fs_kd_strx': Ext.getCmp('cboTrsOut').getValue(),
					'fs_kd_status': Ext.getCmp('cboStatusOut').getValue(),
					'fs_refno': Ext.getCmp('cboRefnoOut').getValue(),
					'fs_refnodt': Ext.Date.format(Ext.getCmp('txtRefnodtOut').getValue(), 'Ymd'),
					'fs_docno': Ext.getCmp('txtDocnoOut').getValue(),
					'fs_docnodt': Ext.Date.format(Ext.getCmp('txtDocnodtOut').getValue(), 'Ymd'),
					'fs_note': Ext.getCmp('txtNoteOut').getValue(),
					'fs_total': Ext.getCmp('txtTotalDebitOut').getValue(),
					'fs_cekgiro': Ext.getCmp('txtCekOut').getValue(),
					'fs_kd_acno': xkdacno,
					'fs_notedetil': xnote,
					'fn_amt': xamt,
					'fs_seqno': xseqno,
					'fs_dc': xdc
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
						Ext.getCmp('cboRefnoOut').setValue(xtext.refno.trim());
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

		function fnCBOutCekRemove() {
			Ext.Ajax.request({
				method: 'POST',
				url: 'cashbank/cbin_cekremove',
				params: {
					'fs_kd_dept': Ext.getCmp('txtDeptOut').getValue(),
					'fs_count': Ext.getCmp('txtCountOut').getValue(),
					'fs_kd_strx': Ext.getCmp('cboTrsOut').getValue(),
					'fs_refno': Ext.getCmp('cboRefnoOut').getValue()
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
										fnCBOutRemove();
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
						msg: 'Remove Failed, Connection Failed!!',
						title: 'IDS'
					});
					vMask.hide();
				}
			});
		}

		function fnCBOutRemove() {
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'cashbank/cbin_remove',
				params: {
					'fs_kd_dept': Ext.getCmp('txtDeptOut').getValue(),
					'fs_count': Ext.getCmp('txtCountOut').getValue(),
					'fs_kd_strx': Ext.getCmp('cboTrsOut').getValue(),
					'fs_refno': Ext.getCmp('cboRefnoOut').getValue()
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
						fnResetOut();
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

		function fnPrintCashBankOut() {
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				timeout: 60000,
				url: 'cashbank/printcb',
				params: {
					
					'fs_refno': Ext.getCmp('cboRefnoOut').getValue(),
					'fs_kd_dept': Ext.getCmp('txtDeptOut').getValue(),
					'fs_count': Ext.getCmp('txtCountOut').getValue(),
					'fs_kd_strx': Ext.getCmp('cboTrsOut').getValue()
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
							title: 'Cash Bank Out',
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

		var frmCashBank = Ext.create('Ext.form.Panel', {
			border: false,
			frame: true,
			region: 'center',
			title: 'Cash Bank Form',
			width: 950,
			fieldDefaults: {
				labelAlign: 'right',
				labelSeparator: '',
				msgTarget: 'side'
			},
			items: [{
				activeTab: 0,
				bodyStyle: 'padding: 5px; background-color: '.concat(gBasePanel),
				border: false,
				plain: true,
				xtype: 'tabpanel',
				items: [{
					bodyStyle: 'background-color: '.concat(gBasePanel),
					border: false,
					frame: false,
					title: 'Entry Cash Bank In',
					xtype: 'form',
					items: [{
						fieldDefaults: {
							labelAlign: 'right',
							labelSeparator: '',
							labelWidth: 80,
							msgTarget: 'side'
						},
						style: 'padding: 5px;',
						title: 'Header Cash Bank IN',
						xtype: 'fieldset',
						items: [
							cboDeptIn,
							txtDeptIn,
							txtCountIn,
						{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1.25,
								layout: 'anchor',
								xtype: 'container',
								items: [
									cboTrsIn,
									cboRefnoIn,
									txtDocnoIn
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									cboStatusIn,
									txtRefnodtIn,
									txtDocnodtIn
								]
							}]
						},
							txtNoteIn
						]
					},{
						style: 'padding: 5px;',
						title: 'Detail Cash Bank IN',
						xtype: 'fieldset',
						items: [
							gridDetailIn
						]
					}],
					buttons: [{
						text: 'Save',
						handler: fnCBInCekSave
					},{
						text: 'Print',
						handler: fnPrintCashBankIn
					},{
						text: 'Reset',
						handler: fnResetIn
					},{
						text: 'Remove',
						handler: fnCBInCekRemove
					}]
				},{
					bodyStyle: 'background-color: '.concat(gBasePanel),
					border: false,
					frame: false,
					title: 'Entry Cash Bank Out',
					xtype: 'form',
					items: [{
						fieldDefaults: {
							labelAlign: 'right',
							labelSeparator: '',
							labelWidth: 80,
							msgTarget: 'side'
						},
						style: 'padding: 5px;',
						title: 'Header Cash Bank OUT',
						xtype: 'fieldset',
						items: [{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 1.25,
								layout: 'anchor',
								xtype: 'container',
								items: [
									cboDeptOut,
									txtDeptOut,
									txtCountOut,
									cboTrsOut,
									cboRefnoOut,
									txtDocnoOut
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									txtCekOut,
									cboStatusOut,
									txtRefnodtOut,
									txtDocnodtOut
								]
							}]
						},
							txtNoteOut
						]
					},{
						style: 'padding: 5px;',
						title: 'Detail Cash Bank OUT',
						xtype: 'fieldset',
						items: [
							gridDetailOut
						]
					}],
					buttons: [{
						text: 'Save',
						handler: fnCBOutCekSave
					},{
						text: 'Print',
						handler: fnPrintCashBankOut
					},{
						text: 'Reset',
						handler: fnResetOut
					},{
						text: 'Remove',
						handler: fnCBOutCekRemove
					}]
				}]
			}]
		});

		var vMask = new Ext.LoadMask({
			msg: 'Please wait...',
			target: frmCashBank
		});
		
		function fnMaskShow() {
			frmCashBank.mask('Please wait...');
		}

		function fnMaskHide() {
			frmCashBank.unmask();
		}
		
		frmCashBank.render(Ext.getBody());
	}
	
	Ext.Ajax.request({
		method: 'POST',
		url: 'alamat/nilaidefa',
		success: function(response) {
			var xtext = Ext.decode(response.responseText);
			
			if (xtext.sukses === true) {
				buatForm();
				
				Ext.getCmp('cboDeptIn').setValue(xtext.nmdept);
				Ext.getCmp('txtDeptIn').setValue(xtext.deptcd);
				Ext.getCmp('txtCountIn').setValue(xtext.count);
				Ext.getCmp('cboDeptOut').setValue(xtext.nmdept);
				Ext.getCmp('txtDeptOut').setValue(xtext.deptcd);
				Ext.getCmp('txtCountOut').setValue(xtext.count);
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