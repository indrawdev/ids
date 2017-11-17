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

	function gridTooltipReg(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Double click on the item to edit',
			target: view.el,
			trackMouse: true
		});
	}

	var grupRefno = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_refno','fd_refno',
			'fs_nm_cust','fs_rangka','fs_mesin',
			'fs_docno','fd_docno',
			'fs_kd_agen','fs_nm_agen'
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
			url: 'agen/refno'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboRefno').getValue(),
					'fs_nm_cust': Ext.getCmp('cboRefno').getValue(),
					'fs_rangka': Ext.getCmp('cboRefno').getValue(),
					'fs_mesin': Ext.getCmp('cboRefno').getValue(),
					'fs_cari': Ext.getCmp('txtCari1').getValue()
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
			{text: "Ref. No", dataIndex: 'fs_refno', menuDisabled: true, width: 180},
			{text: "Ref. Date", dataIndex: 'fd_refno', menuDisabled: true, width: 80},
			{text: "Cust", dataIndex: 'fs_nm_cust', menuDisabled: true, width: 195},
			{text: "Chassis", dataIndex: 'fs_rangka', menuDisabled: true, width: 125},
			{text: "Machine", dataIndex: 'fs_mesin', menuDisabled: true, width: 100},
			{text: "Doc. No", dataIndex: 'fs_docno', menuDisabled: true, hidden: true},
			{text: "Doc. Date", dataIndex: 'fd_docno', menuDisabled: true, hidden: true},
			{text: "Agent Cd", dataIndex: 'fs_kd_agen', menuDisabled: true, hidden: true},
			{text: "Agent", dataIndex: 'fs_nm_agen', menuDisabled: true, hidden: true}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Ref. No / Cust / Chassis / Machine',
				id: 'txtCari1',
				name: 'txtCari1',
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
				Ext.getCmp('cboAgen').setValue(record.get('fs_kd_agen'));
				Ext.getCmp('txtAgen').setValue(record.get('fs_nm_agen'));
				
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

	var grupAgen = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_agen','fs_nm_agen'
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
			url: 'agen/kodeagen'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_agen': Ext.getCmp('cboAgen').getValue(),
					'fs_nm_agen': Ext.getCmp('cboAgen').getValue(),
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
		store: grupAgen,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Agent Cd", dataIndex: 'fs_kd_agen', menuDisabled: true, width: 100},
			{text: "Agen Name", dataIndex: 'fs_nm_agen', menuDisabled: true, width: 380}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Agent Cd / Agen Name',
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
					grupAgen.load();
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
			store: grupAgen,
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
				Ext.getCmp('cboAgen').setValue(record.get('fs_kd_agen'));
				Ext.getCmp('txtAgen').setValue(record.get('fs_nm_agen'));
				
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
				grupAgen.load();
				vMask.show();
			}
		}
	});

	var cboAgen = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Agent',
		id: 'cboAgen',
		labelWidth: 60,
		name: 'cboAgen',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtAgen').setValue('');
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

	var txtAgen = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Agent',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtAgen',
		name: 'txtAgen',
		readOnly: true,
		xtype: 'textfield'
	};

	var cellEditingReg = Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToEdit: 2
	});

	Ext.define('DataGridReg', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_refnojual', type: 'string'},
			{name: 'fs_kd_cust', type: 'string'},
			{name: 'fs_count', type: 'string'},
			{name: 'fs_nm_cust', type: 'string'},
			{name: 'fs_rangka', type: 'string'},
			{name: 'fs_mesin', type: 'string'},
			{name: 'fd_stnk', type: 'date', dateFormat: 'd-m-Y'},
			{name: 'fs_stnk', type: 'string'},
			{name: 'fs_nm_stnk_qq', type: 'string'},
			{name: 'fs_almt_stnk_qq', type: 'string'},
			{name: 'fd_bpkb', type: 'date', dateFormat: 'd-m-Y'},
			{name: 'fs_bpkb', type: 'string'},
			{name: 'fs_nm_bpkb_qq', type: 'string'},
			{name: 'fs_almt_bpkb_qq', type: 'string'},
			{name: 'fn_bbn', type: 'float'},
			{name: 'fn_servis', type: 'float'},
			{name: 'fs_note', type: 'string'}
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
			url: 'agen/grid_detail'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_refno': Ext.getCmp('cboRefno').getValue()
				});
			}
		}
	});

	var gridReg = Ext.create('Ext.grid.Panel', {
		defaultType: 'textfield',
		height: 180,
		sortableColumns: false,
		store: grupGridReg,
		columns: [{
			xtype: 'rownumberer'
		},{
			text: 'Sales',
			dataIndex: 'fs_refnojual',
			hidden: true,
			menuDisabled: true
		},{
			text: 'Cust Cd',
			dataIndex: 'fs_kd_cust',
			hidden: true,
			menuDisabled: true
		},{
			text: 'Cust Count',
			dataIndex: 'fs_count',
			hidden: true,
			menuDisabled: true
		},{
			text: 'Cust',
			dataIndex: 'fs_nm_cust',
			locked: true,
			menuDisabled: true,
			width: 200
		},{
			text: 'Chassis',
			dataIndex: 'fs_rangka',
			menuDisabled: true,
			width: 150
		},{
			text: 'Machine',
			dataIndex: 'fs_mesin',
			menuDisabled: true,
			width: 150
		},{
			text: 'STNK Receive',
			dataIndex: 'fd_stnk',
			format: 'd-m-Y',
			menuDisabled: true,
			renderer: Ext.util.Format.dateRenderer('d-m-Y'),
			width: 100,
			xtype: 'datecolumn',
			editor: {
				editable: true,
				format: 'd-m-Y',
				maskRe: /[0-9-]/,
				minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
				onExpand: function(value) {
					this.picker.setValue(Ext.isDate(value) ? value : new Date());
				},
				xtype: 'datefield',
				listeners: {
					change: function() {
						var recordgrid = gridReg.getSelectionModel().getSelection()[0];
						if (Ext.isDate(recordgrid.get('fd_stnk')) === false) {
							recordgrid.set('fd_stnk','01-01-3000');
						}
					}
				}
			}
		},{
			text: 'STNK Number',
			dataIndex: 'fs_stnk',
			menuDisabled: true,
			width: 150,
			editor: {
				xtype: 'textfield'
			}
		},{
			text: 'STNK QQ Name',
			dataIndex: 'fs_nm_stnk_qq',
			menuDisabled: true,
			width: 150,
			editor: {
				xtype: 'textfield'
			}
		},{
			text: 'STNK QQ Address',
			dataIndex: 'fs_almt_stnk_qq',
			menuDisabled: true,
			width: 150,
			editor: {
				grow: true,
				growMin: 70,
				growMax: 70,
				xtype: 'textareafield'
			}
		},{
			text: 'BPKB Receive',
			dataIndex: 'fd_bpkb',
			format: 'd-m-Y',
			menuDisabled: true,
			renderer: Ext.util.Format.dateRenderer('d-m-Y'),
			width: 100,
			xtype: 'datecolumn',
			editor: {
				editable: true,
				format: 'd-m-Y',
				maskRe: /[0-9-]/,
				minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
				onExpand: function(value) {
					this.picker.setValue(Ext.isDate(value) ? value : new Date());
				},
				xtype: 'datefield',
				listeners: {
					change: function() {
						var recordgrid = gridReg.getSelectionModel().getSelection()[0];
						if (Ext.isDate(recordgrid.get('fd_bpkb')) === false) {
							recordgrid.set('fd_bpkb','01-01-3000');
						}
					}
				}
			}
		},{
			text: 'BPKB Number',
			dataIndex: 'fs_bpkb',
			menuDisabled: true,
			width: 150,
			editor: {
				xtype: 'textfield'
			}
		},{
			text: 'BPKB QQ Name',
			dataIndex: 'fs_nm_bpkb_qq',
			menuDisabled: true,
			width: 150,
			editor: {
				xtype: 'textfield'
			}
		},{
			text: 'BPKB QQ Address',
			dataIndex: 'fs_almt_bpkb_qq',
			menuDisabled: true,
			width: 150,
			editor: {
				grow: true,
				growMin: 70,
				growMax: 70,
				xtype: 'textareafield'
			}
		},{
			align: 'right',
			text: 'BBN',
			dataIndex: 'fn_bbn',
			format: '0,000.00',
			menuDisabled: true,
			width: 100,
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
			text: 'Service',
			dataIndex: 'fn_servis',
			format: '0,000.00',
			menuDisabled: true,
			width: 100,
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
			text: 'Note',
			dataIndex: 'fs_note',
			menuDisabled: true,
			width: 100
		}],
		listeners: {
			selectionchange: function(view, records) {
				gridReg.down('#removeData').setDisabled(!records.length);
			}
		},
		plugins: [
			cellEditingReg
		],
		tbar: [{
			value: "<*Date '01-01-3000' is assumed empty>",
			xtype: 'displayfield'
		},{
			xtype: 'tbfill'
		},{
			xtype: 'buttongroup',
			columns: 1,
			defaults: {
				scale: 'small'
			},
			items: [{
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
				},
				disabled: true
			}]
		}],
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			listeners: {
				render: gridTooltipReg
			},
			markDirty: false,
			stripeRows: true
		}
	});

	var cellEditingReg2 = Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToEdit: 2
	});

	Ext.define('DataGridReg2', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fb_cek', type: 'bool'},
			{name: 'fs_refnojual', type: 'string'},
			{name: 'fd_refno', type: 'string'},
			{name: 'fs_kd_cust', type: 'string'},
			{name: 'fs_count', type: 'string'},
			{name: 'fs_nm_cust', type: 'string'},
			{name: 'fs_rangka', type: 'string'},
			{name: 'fs_mesin', type: 'string'},
			{name: 'fd_stnk', type: 'date', dateFormat: 'd-m-Y'},
			{name: 'fd_bpkb', type: 'date', dateFormat: 'd-m-Y'},
			{name: 'fs_note', type: 'string'}
		]
	});

	var grupGridReg2 = Ext.create('Ext.data.Store', {
		autoLoad: true,
		model: 'DataGridReg2',
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
			url: 'agen/grid_detail2'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_cari': Ext.getCmp('txtCari').getValue()
				});
			}
		}
	});

	var gridReg2 = Ext.create('Ext.grid.Panel', {
		defaultType: 'textfield',
		height: 191,
		sortableColumns: false,
		store: grupGridReg2,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupGridReg2
		}),
		columns: [{
			xtype: 'rownumberer',
			width: 45
		},{
			align: 'center',
			text: 'Add',
			dataIndex: 'fb_cek',
			menuDisabled: true,
			stopSelection: false,
			xtype: 'checkcolumn',
			width: 35,
			listeners: {
				checkchange: function(grid, rowIndex, checked) {
					var store = gridReg2.getStore();
					var record = store.getAt(rowIndex);
					
					var xcek = record.get('fb_cek');
					var xrefnojual = record.get('fs_refnojual').trim();
					var xkdcust = record.get('fs_kd_cust').trim();
					var xcount = record.get('fs_count').trim();
					var xnmcust = record.get('fs_nm_cust').trim();
					var xrangka = record.get('fs_rangka').trim();
					var xmesin = record.get('fs_mesin').trim();
					var xnote = record.get('fs_note').trim();
					
					if (xcek === true) {
						store = gridReg.getStore();
						var xlanjut = true;
						store.each(function(record, idx) {
							var xxrefnojual = record.get('fs_refnojual').trim();
							var xxrangka = record.get('fs_rangka').trim();
							var xxmesin = record.get('fs_mesin').trim();
							
							if (xrefnojual == xxrefnojual && xrangka == xxrangka && xmesin == xxmesin) {
								Ext.MessageBox.show({
									buttons: Ext.MessageBox.OK,
									closable: false,
									icon: Ext.Msg.WARNING,
									msg: 'Record already exists, add record cancel!!',
									title: 'IDS'
								});
								xlanjut = false;
							}
						});
						if (xlanjut === false) {
							return;
						}
						
						var xtotal = grupGridReg.getCount();
						var xdata = Ext.create('DataGridReg2', {
							fs_refnojual: xrefnojual.trim(),
							fs_kd_cust: xkdcust.trim(),
							fs_count: xcount.trim(),
							fs_nm_cust: xnmcust.trim(),
							fs_rangka: xrangka.trim(),
							fs_mesin: xmesin.trim(),
							fd_stnk: '01-01-3000',
							fs_stnk: '',
							fs_nm_stnk_qq: '',
							fs_almt_stnk_qq: '',
							fd_bpkb: '01-01-3000',
							fs_bpkb: '',
							fs_nm_bpkb_qq: '',
							fs_almt_bpkb_qq: '',
							fn_bbn: 0,
							fn_servis: 0,
							fs_note: xnote.trim()
						});
						grupGridReg.insert(xtotal, xdata);
						
						xtotal = grupGridReg.getCount() - 1;
						if (xtotal >= 0) {
							gridReg.getSelectionModel().select(xtotal);
						}
					}
				}
			}
		},{
			text: 'Sales',
			dataIndex: 'fs_refnojual',
			hidden: true,
			menuDisabled: true
		},{
			text: 'Cust Cd',
			dataIndex: 'fs_kd_cust',
			hidden: true,
			menuDisabled: true
		},{
			text: 'Cust Count',
			dataIndex: 'fs_count',
			hidden: true,
			menuDisabled: true
		},{
			text: 'Cust',
			dataIndex: 'fs_nm_cust',
			menuDisabled: true,
			width: 465
		},{
			text: 'Chassis',
			dataIndex: 'fs_rangka',
			menuDisabled: true,
			width: 150
		},{
			text: 'Machine',
			dataIndex: 'fs_mesin',
			menuDisabled: true,
			width: 125
		},{
			text: 'Note',
			dataIndex: 'fs_note',
			menuDisabled: true,
			width: 100
		}],
		plugins: [
			cellEditingReg2
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Type customer / chassis / machine / note',
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
					grupGridReg2.load();
				}
			}]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: []
		}],
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false,
			stripeRows: true
		}
	});

	function fnCekSave() {
		if (this.up('form').getForm().isValid()) {
			var xtotal = grupGridReg.getCount();
			if (xtotal <= 0) {
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Receiving List is empty, please fill in advance!!',
					title: 'IDS'
				});
				return;
			}
			
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'agen/ceksave',
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
					fnMaskHide();
				}
			});
		}
	}

	function fnSave() {
		var xrefnojual = '';
		var xkdcust = '';
		var xcount = '';
		var xrangka = '';
		var xmesin = '';
		var xtglstnk = '';
		var xtglstnk2 = '';
		var xstnk = '';
		var xnmstnkqq = '';
		var xalmtstnkqq = '';
		var xtglbpkb = '';
		var xtglbpkb2 = '';
		var xbpkb = '';
		var xnmbpkbqq = '';
		var xalmtbpkbqq = '';
		var xbbn = '';
		var xservis = '';
		
		var store = gridReg.getStore();
		store.each(function(record, idx) {
			if (record.get('fd_stnk') !== '' &&
				Ext.isDate(record.get('fd_stnk')) == true &&
				Ext.Date.format(record.get('fd_stnk'), 'd-m-Y') !== '01-01-3000') {
				
				xtglstnk2 = Ext.Date.format(record.get('fd_stnk'), 'Ymd');
			}
			else {
				xtglstnk2 = '';
			}
			
			if (record.get('fd_bpkb') !== '' &&
				Ext.isDate(record.get('fd_bpkb')) == true &&
				Ext.Date.format(record.get('fd_bpkb'), 'd-m-Y') !== '01-01-3000') {
				
				xtglbpkb2 = Ext.Date.format(record.get('fd_bpkb'), 'Ymd');
			}
			else {
				xtglbpkb2 = '';
			}
			
			xrefnojual = xrefnojual +'|'+ record.get('fs_refnojual');
			xkdcust = xkdcust +'|'+ record.get('fs_kd_cust');
			xcount = xcount +'|'+ record.get('fs_count');
			xrangka = xrangka +'|'+ record.get('fs_rangka');
			xmesin = xmesin +'|'+ record.get('fs_mesin');
			xtglstnk = xtglstnk +'|'+ xtglstnk2;
			xstnk = xstnk +'|'+ record.get('fs_stnk');
			xnmstnkqq = xnmstnkqq +'|'+ record.get('fs_nm_stnk_qq');
			xalmtstnkqq = xalmtstnkqq +'|'+ record.get('fs_almt_stnk_qq');
			xtglbpkb = xtglbpkb +'|'+ xtglbpkb2;
			xbpkb = xbpkb +'|'+ record.get('fs_bpkb');
			xnmbpkbqq = xnmbpkbqq +'|'+ record.get('fs_nm_bpkb_qq');
			xalmtbpkbqq = xalmtbpkbqq +'|'+ record.get('fs_almt_bpkb_qq');
			xbbn = xbbn +'|'+ record.get('fn_bbn');
			xservis = xservis +'|'+ record.get('fn_servis');
		});
		
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'agen/save',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue(),
				'fd_refno': Ext.Date.format(Ext.getCmp('txtRefnodt').getValue(), 'Ymd'),
				'fs_docno': Ext.getCmp('txtDocno').getValue(),
				'fd_docno': Ext.Date.format(Ext.getCmp('txtDocnodt').getValue(), 'Ymd'),
				'fs_kd_agen': Ext.getCmp('cboAgen').getValue(),
				'fs_refnojual': xrefnojual,
				'fs_kd_cust': xkdcust,
				'fs_count': xcount,
				'fs_rangka': xrangka,
				'fs_mesin': xmesin,
				'fd_stnk': xtglstnk,
				'fs_stnk': xstnk,
				'fs_nm_stnk_qq': xnmstnkqq,
				'fs_almt_stnk_qq': xalmtstnkqq,
				'fd_bpkb': xtglbpkb,
				'fs_bpkb': xbpkb,
				'fs_nm_bpkb_qq': xnmbpkbqq,
				'fs_almt_bpkb_qq': xalmtbpkbqq,
				'fn_bbn': xbbn,
				'fn_servis': xservis
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
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'agen/cekremove',
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
			url: 'agen/remove',
			params: {
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
					Ext.getCmp('cboRefno').setValue('');
					grupGridReg2.load();
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
			url: 'agen/print_agenservice',
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
						title: 'Biro Jasa',
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
		Ext.getCmp('txtDocno').setValue('');
		Ext.getCmp('txtDocnodt').setValue(new Date());
		Ext.getCmp('cboAgen').setValue('');
		Ext.getCmp('txtAgen').setValue('');
		
		grupGridReg.removeAll();
		gridReg.getView().refresh();
		grupGridReg2.load();
	}

	var frmAgen = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Service Agent Form',
		width: 950,
		fieldDefaults: {
			labelAlign: 'right',
			labelSeparator: '',
			labelWidth: 50,
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
					flex: 1.2,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 2,
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
					flex: 1.2,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 2,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtDocno
							]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtDocnodt
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
								cboAgen
							]
						},{
							flex: 1.6,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtAgen
							]
						}]
					}]
				}]
			}]
		},
			gridReg, {xtype: 'splitter'}, {xtype: 'splitter'},
			gridReg2
		],
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
		target: frmAgen
	});
	
	function fnMaskShow() {
		frmAgen.mask('Please wait...');
	}

	function fnMaskHide() {
		frmAgen.unmask();
	}
	
	frmAgen.render(Ext.getBody());
	Ext.get('loading').destroy();
});
