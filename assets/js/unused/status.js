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

	Ext.Ajax.request({
		method: 'POST',
		url: 'status/addfield',
		success: function(response, opts) {
			var xtext = Ext.decode(response.responseText);
		},
		failure: function(response, opts) {
			var xtext = Ext.decode(response.responseText);
			Ext.Msg.show({
				buttons: Ext.MessageBox.OK,
				closable: false,
				icon: Ext.Msg.INFO,
				msg: 'Load default Failed, Connection Failed!!',
				title: 'ASTER'
			});
		}
	});

	var grupRefno = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_refno','fd_refno',
			'fs_docno','fd_docno'
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
			url: 'status/refno'
		}
	});

	grupRefno.on('beforeload', function(store, operation, eOpts) {
		operation.params = {
			'fs_refno': Ext.getCmp('cboRefno').getValue()
		};
	}, this);

	var winGrid = Ext.create('Ext.ux.LiveSearchGridPanel', {
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
					winCari.hide();
				}
			}]
		}),
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Ref. No", dataIndex: 'fs_refno', menuDisabled: true, width: 180},
			{text: "Ref. Date", dataIndex: 'fd_refno', menuDisabled: true, width: 80},
			{text: "Doc. No", dataIndex: 'fs_docno', menuDisabled: true, width: 150},
			{text: "Doc. Date", dataIndex: 'fd_docno', menuDisabled: true, width: 80}
		],
		listeners: {
			itemclick: function(grid, record)
			{
				Ext.getCmp('cboRefno').setValue(record.get('fs_refno'));
				Ext.getCmp('txtRefnodt').setValue(tglDMY(record.get('fd_refno')));
				Ext.getCmp('txtDocno').setValue(record.get('fs_docno'));
				Ext.getCmp('txtDocnodt').setValue(tglDMY(record.get('fd_docno')));
				
				grupGridStatus.load();
			},
			itemdblclick: function(grid, record)
			{
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
			winGrid.show();
			winCari.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
		}
	});

	var cboRefno = {
		anchor: '95%',
		emptyText: "AUTOMATIC",
		fieldLabel: 'Ref. No',
		id: 'cboRefno',
		name: 'cboRefno',
		xtype: 'searchRefno'
	};

	var txtDocno = {
		anchor: '95%',
		emptyText: 'Enter a Document Number',
		fieldLabel: 'Doc. No',
		id: 'txtDocno',
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

	var grupStatus = Ext.create('Ext.data.ArrayStore', {
		autoLoad: false,
		data: [['0', 'READY'], ['1', 'MAINTENANCE'], ['2', 'REPAIR']],
		fields: ['fs_kd_status', 'fs_nm_status']
	});

	var cellEditingStatus = Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToEdit: 2
	});

	cellEditingStatus.on({
		scope: this
	});

	Ext.define('DataGridStatus', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_product', type: 'string'},
			{name: 'fs_nm_product', type: 'string'},
			{name: 'fs_rangka', type: 'string'},
			{name: 'fs_mesin', type: 'string'},
			{name: 'fn_status', type: 'string'},
			{name: 'fd_tgl_pelihara', type: 'date', dateFormat: 'd-m-Y'},
			{name: 'fs_ket', type: 'string'}
		]
	});

	var grupGridStatus = Ext.create('Ext.data.Store', {
		autoLoad: false,
		model: 'DataGridStatus',
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'status/grid_detail'
		}
	});

	grupGridStatus.on('beforeload', function(store, operation, eOpts) {
		operation.params = {
			'fs_refno': Ext.getCmp('cboRefno').getValue()
		};
	}, this);

	var gridStatus = Ext.create('Ext.grid.Panel', {
		defaultType: 'textfield',
		height: 180,
		sortableColumns: false,
		store: grupGridStatus,
		columns: [{
			xtype: 'rownumberer',
			width: 45
		},{
			text: 'Product Code',
			dataIndex: 'fs_kd_product',
			menuDisabled: true,
			width: 160
		},{
			text: 'Product Name',
			dataIndex: 'fs_nm_product',
			menuDisabled: true,
			width: 180
		},{
			text: 'Chassis',
			dataIndex: 'fs_rangka',
			menuDisabled: true,
			width: 155
		},{
			text: 'Machine',
			dataIndex: 'fs_mesin',
			menuDisabled: true,
			width: 155
		},{
			text: 'Status',
			dataIndex: 'fn_status',
			menuDisabled: true,
			width: 125,
			editor: {
				editable: false,
				displayField: 'fs_nm_status',
				store: grupStatus,
				value: '0',
				valueField: 'fs_kd_status',
				xtype: 'combobox'
			},
			renderer: function(val) {
				index = grupStatus.findExact('fs_kd_status',val); 
				if (index != -1){
					rs = grupStatus.getAt(index).data; 
					return rs.fs_nm_status; 
				}
			}
		},{
			text: 'Maintenance Date',
			dataIndex: 'fd_tgl_pelihara',
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
				xtype: 'datefield'
			}
		},{
			text: 'Note',
			dataIndex: 'fs_ket',
			menuDisabled: true,
			width: 200,
			editor: {
				xtype: 'textfield'
			}
		}],
		listeners: {
			selectionchange: function(view, records) {
				gridStatus.down('#removeData').setDisabled(!records.length);
			}
		},
		plugins: [
			cellEditingStatus
		],
		tbar: ['->',{
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
					var sm = gridStatus.getSelectionModel();
					cellEditingStatus.cancelEdit();
					grupGridStatus.remove(sm.getSelection());
					gridStatus.getView().refresh();
					if (grupGridStatus.getCount() > 0) {
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

	var cellEditingStatus2 = Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToEdit: 2
	});

	cellEditingStatus2.on({
		scope: this
	});

	Ext.define('DataGridStatus2', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_product', type: 'string'},
			{name: 'fs_nm_product', type: 'string'},
			{name: 'fs_rangka', type: 'string'},
			{name: 'fs_mesin', type: 'string'},
			{name: 'fs_nm_wh', type: 'string'},
			{name: 'fn_status', type: 'string'},
			{name: 'fd_tgl_pelihara', type: 'string'},
			{name: 'fs_ket', type: 'string'}
		]
	});

	var grupGridStatus2 = Ext.create('Ext.data.Store', {
		autoLoad: true,
		model: 'DataGridStatus2',
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
			url: 'status/grid_detail2'
		}
	});

	grupGridStatus2.on('beforeload', function(store, operation, eOpts) {
		operation.params = {
			'fs_cari': Ext.getCmp('txtCari').getValue()
		};
	}, this);

	var gridStatus2 = Ext.create('Ext.grid.Panel', {
		defaultType: 'textfield',
		height: 191,
		sortableColumns: false,
		store: grupGridStatus2,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupGridStatus2
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
			width: 30,
			listeners: {
				checkchange: function(grid, rowIndex, checked) {
					var store = gridStatus2.getStore();
					var record = store.getAt(rowIndex);
					
					var xcek = record.get('fb_cek');
					var xprodcd = trim(record.get('fs_kd_product'));
					var xprod = trim(record.get('fs_nm_product'));
					var xrangka = trim(record.get('fs_rangka'));
					var xmesin = trim(record.get('fs_mesin'));
					var xstatus = record.get('fn_status');
					var xtglpelihara = trim(record.get('fd_tgl_pelihara'));
					var xket = record.get('fs_ket');
					
					if (xcek === true) {
						store = gridStatus.getStore();
						var xlanjut = true;
						store.each(function(record, idx) {
							var xxprodcd = trim(record.get('fs_kd_product'));
							var xxrangka = trim(record.get('fs_rangka'));
							var xxmesin = trim(record.get('fs_mesin'));
							
							if (xprodcd == xxprodcd && xrangka == xxrangka && xmesin == xxmesin) {
								Ext.Msg.show({
									buttons: Ext.MessageBox.OK,
									closable: false,
									icon: Ext.Msg.WARNING,
									msg: 'Record already exists, add record cancel!!',
									title: 'ASTER'
								});
								xlanjut = false;
							}
						});
						if (xlanjut === false) {
							return;
						}
						
						var xtotal = grupGridStatus.getCount();
						var xdata = Ext.create('DataGridStatus2', {
							fs_kd_product: trim(xprodcd),
							fs_nm_product: trim(xprod),
							fs_rangka: trim(xrangka),
							fs_mesin: trim(xmesin),
							fn_status: xstatus,
							fd_tgl_pelihara: trim(xtglpelihara),
							fs_ket: trim(xket)
						});
						grupGridStatus.insert(xtotal, xdata);
						
						xtotal = grupGridStatus.getCount() - 1;
						if (xtotal >= 0) {
							gridStatus.getSelectionModel().select(xtotal);
						}
					}
				}
			}
		},{
			text: 'Product Code',
			dataIndex: 'fs_kd_product',
			menuDisabled: true,
			width: 180
		},{
			text: 'Product Name',
			dataIndex: 'fs_nm_product',
			menuDisabled: true,
			width: 210
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
			text: 'Warehouse',
			dataIndex: 'fs_nm_wh',
			menuDisabled: true,
			width: 140
		},{
			text: 'Status',
			dataIndex: 'fn_status',
			hidden: true,
			menuDisabled: true
		},{
			text: 'Maintenance Date',
			dataIndex: 'fd_tgl_pelihara',
			hidden: true,
			menuDisabled: true
		},{
			text: 'Note',
			dataIndex: 'fs_ket',
			hidden: true,
			menuDisabled: true
		}],
		plugins: [
			cellEditingStatus2
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Type product code / product name / chassis / machine / warehouse',
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
					grupGridStatus2.load();
				}
			}]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container'
		}],
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false,
			stripeRows: true
		}
	});

	var cmdCek = {
		anchor: '95%',
		id: 'cmdCek',
		name: 'cmdCek',
		text: 'Check All',
		xtype: 'button',
		handler: function() {
			var store = gridStatus2.getStore();
			
			store.each(function(record, idx) {
				record.set('fb_cek',true);
				
				var xcek = record.get('fb_cek');
				var xprodcd = trim(record.get('fs_kd_product'));
				var xprod = trim(record.get('fs_nm_product'));
				var xrangka = trim(record.get('fs_rangka'));
				var xmesin = trim(record.get('fs_mesin'));
				var xstatus = record.get('fn_status');
				var xtglpelihara = trim(record.get('fd_tgl_pelihara'));
				
				// if (xcek === true) {
					store1 = gridStatus.getStore();
					var xlanjut = true;
					store1.each(function(record, idx) {
						var xxprodcd = trim(record.get('fs_kd_product'));
						var xxrangka = trim(record.get('fs_rangka'));
						var xxmesin = trim(record.get('fs_mesin'));
						
						if (xprodcd == xxprodcd && xrangka == xxrangka && xmesin == xxmesin) {
							// Ext.Msg.show({
								// buttons: Ext.MessageBox.OK,
								// closable: false,
								// icon: Ext.Msg.WARNING,
								// msg: 'Record already exists, add record cancel!!',
								// title: 'ASTER'
							// });
							xlanjut = false;
						}
					});
					if (xlanjut === false) {
						return;
					}
					
					var xtotal = grupGridStatus.getCount();
					var xdata = Ext.create('DataGridStatus2', {
						fs_kd_product: trim(xprodcd),
						fs_nm_product: trim(xprod),
						fs_rangka: trim(xrangka),
						fs_mesin: trim(xmesin),
						fn_status: xstatus,
						fd_tgl_pelihara: trim(xtglpelihara)
					});
					grupGridStatus.insert(xtotal, xdata);
					
					xtotal = grupGridStatus.getCount() - 1;
					if (xtotal >= 0) {
						gridStatus.getSelectionModel().select(xtotal);
					}
				// }
			});
		}
	};

	var cmdUnCek = {
		anchor: '95%',
		id: 'cmdUnCek',
		name: 'cmdUnCek',
		text: 'Uncheck All',
		xtype: 'button',
		handler: function() {
			var store = gridStatus2.getStore();
			store.each(function(record, idx) {
				record.set('fb_cek',false);
			});
		}
	};

	function fnCekSave() {
		if (this.up('form').getForm().isValid()) {
			var xtotal = grupGridStatus.getCount();
			if (xtotal <= 0) {
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.Msg.INFO,
					msg: 'Status List is empty, please fill in advance!!',
					title: 'ASTER'
				});
				return;
			}
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'status/ceksave',
				params: {
					'fs_refno': Ext.getCmp('cboRefno').getValue()
				},
				success: function(response, opts) {
					var xtext = Ext.decode(response.responseText);
					
					if (xtext.sukses === false) {
						Ext.Msg.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.Msg.INFO,
							msg: xtext.hasil,
							title: 'ASTER'
						});
					}
					else {
						if (xtext.sukses === true && xtext.hasil == 'lanjut') {
							fnSave();
						}
						else {
							Ext.Msg.show({
								buttons: Ext.MessageBox.YESNO,
								closable: false,
								icon: Ext.Msg.QUESTION,
								msg: xtext.hasil,
								title: 'ASTER',
								fn: function(btn) {
									if (btn == 'yes') {
										fnSave();
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
						icon: Ext.Msg.INFO,
						msg: 'Saving Failed, Connection Failed!!',
						title: 'ASTER'
					});
					vMask.hide();
				}
			});
		}
	}

	function fnSave() {
		var xprod = '';
		var xrangka = '';
		var xmesin = '';
		var xstatus = '';
		var xtglpelihara = '';
		var xket = '';
		
		var store = gridStatus.getStore();
		store.each(function(record, idx) {
			if (record.get('fd_tgl_pelihara') !== '') {
				xtglpelihara2 = Ext.Date.format(record.get('fd_tgl_pelihara'), 'Ymd');
			}
			else {
				xtglpelihara2 = '';
			}
			
			xprod = xprod +'|'+ record.get('fs_kd_product');
			xrangka = xrangka +'|'+ record.get('fs_rangka');
			xmesin = xmesin +'|'+ record.get('fs_mesin');
			xstatus = xstatus +'|'+ record.get('fn_status');
			xtglpelihara = xtglpelihara +'|'+ xtglpelihara2;
			xket = xket +'|'+ record.get('fs_ket');
		});
		
		Ext.Ajax.on('beforerequest', vMask.show, vMask);
		Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
		Ext.Ajax.on('requestexception', vMask.hide, vMask);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'status/save',
			params: {
				'fs_refno': Ext.getCmp('cboRefno').getValue(),
				'fd_refno': Ext.Date.format(Ext.getCmp('txtRefnodt').getValue(), 'Ymd'),
				'fs_docno': Ext.getCmp('txtDocno').getValue(),
				'fd_docno': Ext.Date.format(Ext.getCmp('txtDocnodt').getValue(), 'Ymd'),
				'fs_kd_product': xprod,
				'fs_rangka': xrangka,
				'fs_mesin': xmesin,
				'fn_status': xstatus,
				'fd_tgl_pelihara': xtglpelihara,
				'fs_ket': xket
			},
			success: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				
				if (xtext.sukses === true) {
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.Msg.INFO,
						msg: xtext.hasil,
						title: 'ASTER'
					});
					Ext.getCmp('cboRefno').setValue(trim(xtext.refno));
				}
				
				if (xtext.sukses === false) {
					Ext.Msg.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.Msg.INFO,
						msg: xtext.hasil,
						title: 'ASTER'
					});
				}
			},
			failure: function(response, opts) {
				var xtext = Ext.decode(response.responseText);
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.Msg.INFO,
					msg: 'Saving Failed, Connection Failed!!',
					title: 'ASTER'
				});
				vMask.hide();
			}
		});
	}

	function fnReset() {
		Ext.getCmp('cboRefno').setValue('');
		Ext.getCmp('txtRefnodt').setValue(new Date());
		Ext.getCmp('txtDocno').setValue('');
		Ext.getCmp('txtDocnodt').setValue(new Date());
		
		grupGridStatus.removeAll();
		gridStatus.getView().refresh();
		grupGridStatus2.load();
	}

	var frmStatus = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Product Maintenance Status Form',
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
				anchor: '60%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 2,
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
			}]
		},{
			style: 'padding: 5px;',
			xtype: 'fieldset',
			items: [
				gridStatus
			]
		},{
			style: 'padding: 5px;',
			xtype: 'fieldset',
			items: [
				gridStatus2, {xtype: 'splitter'},
			{
				anchor: '25%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						cmdCek
					]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						cmdUnCek
					]
				}]
			}]
		}],
		buttons: [{
			text: 'Save',
			handler: fnCekSave
		},{
			text: 'Reset',
			handler: fnReset
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmStatus
	});
	
	frmStatus.render(Ext.getBody());
	Ext.get('loading').destroy();
});
