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
			html: 'Double click on the Qty to display the chassis',
			target: view.el,
			trackMouse: true
		});
	}

	function buatForm() {
		function fnCekUnit() {
			Ext.Ajax.request({
				method: 'POST',
				url: 'mutasiout/cek_unit',
				params: {
					'fs_kd_product': Ext.getCmp('cboProd').getValue()
				},
				success: function(response) {
					var xtext = Ext.decode(response.responseText);
					
					if (xtext.sukses === true) {/*
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.MessageBox.INFO,
							msg: xtext.hasil,
							title: 'IDS'
						});*/
						Ext.getCmp('cboUnit').setValue(xtext.satuan.trim());
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
				}
			});
		}

		var grupRefno = Ext.create('Ext.data.Store', {
			autoLoad: false,
			fields: [
				'fs_refno','fd_refno','fs_doc','fd_doc','fs_descrp',
				'fs_dept','fs_nm_dept','fs_kd_dept','fs_count',
				'fs_kd_wh','fs_nm_wh',
				'fs_tdept','fs_nm_tdept','fs_kd_tdept','fs_tcount',
				'fs_kd_wht','fs_nm_wht','fs_status'
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
				url: 'mutasiout/refno'
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_refno': Ext.getCmp('cboRefno').getValue(),
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
				{text: "Ref. Date", dataIndex: 'fd_refno', menuDisabled: true, width: 80},
				{text: "Doc. No", dataIndex: 'fs_doc', menuDisabled: true, hidden: true},
				{text: "Doc. Date", dataIndex: 'fd_doc', menuDisabled: true, hidden: true},
				{text: "Note", dataIndex: 'fs_descrp', menuDisabled: true, width: 150},
				{text: "From Dept", dataIndex: 'fs_dept', menuDisabled: true, hidden: true},
				{text: "From Dept", dataIndex: 'fs_nm_dept', menuDisabled: true, width: 100},
				{text: "From Dept Code", dataIndex: 'fs_kd_dept', menuDisabled: true, hidden: true},
				{text: "From Dept Count", dataIndex: 'fs_count', menuDisabled: true, hidden: true},
				{text: "From Warehouse Code", dataIndex: 'fs_kd_wh', menuDisabled: true, hidden: true},
				{text: "From Warehouse", dataIndex: 'fs_nm_wh', menuDisabled: true, width: 100},
				{text: "To Dept", dataIndex: 'fs_tdept', menuDisabled: true, hidden: true},
				{text: "To Dept", dataIndex: 'fs_nm_tdept', menuDisabled: true, width: 100},
				{text: "To Dept Code", dataIndex: 'fs_kd_tdept', menuDisabled: true, hidden: true},
				{text: "To Dept Count", dataIndex: 'fs_tcount', menuDisabled: true, hidden: true},
				{text: "To Warehouse Code", dataIndex: 'fs_kd_wht', menuDisabled: true, hidden: true},
				{text: "To Warehouse", dataIndex: 'fs_nm_wht', menuDisabled: true, width: 100},
				{text: "Status", dataIndex: 'fs_status', menuDisabled: true, width: 100}
			],
			tbar: [{
				flex: 1,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					anchor: '98%',
					emptyText: 'Ref. No',
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
					Ext.getCmp('txtDocno').setValue(record.get('fs_doc'));
					Ext.getCmp('txtDocnodt').setValue(tglDMY(record.get('fd_doc')));
					Ext.getCmp('txtNote').setValue(record.get('fs_descrp'));
					Ext.getCmp('cboDeptDr').setValue(record.get('fs_dept'));
					Ext.getCmp('txtDeptDr').setValue(record.get('fs_nm_dept'));
					Ext.getCmp('txtDeptCdDr').setValue(record.get('fs_kd_dept'));
					Ext.getCmp('txtCountDr').setValue(record.get('fs_count'));
					Ext.getCmp('cboWHDr').setValue(record.get('fs_kd_wh'));
					Ext.getCmp('txtWHDr').setValue(record.get('fs_nm_wh'));
					Ext.getCmp('cboDeptKe').setValue(record.get('fs_tdept'));
					Ext.getCmp('txtDeptKe').setValue(record.get('fs_nm_tdept'));
					Ext.getCmp('txtDeptCdKe').setValue(record.get('fs_kd_tdept'));
					Ext.getCmp('txtCountKe').setValue(record.get('fs_tcount'));
					Ext.getCmp('cboWHKe').setValue(record.get('fs_kd_wht'));
					Ext.getCmp('txtWHKe').setValue(record.get('fs_nm_wht'));
					
					grupGridProd.load();
					grupGridReg.removeAll();
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
			listeners: {
				change: function(field, newValue) {
					grupGridProd.removeAll();
					gridProd.getView().refresh();
					grupGridReg.removeAll();
					gridReg.getView().refresh();
					grupGridReg2.removeAll();
					gridReg2.getView().refresh();
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

		var txtDocno = {
			anchor: '95%',
			emptyText: 'Enter a Document Number',
			fieldLabel: 'Doc. No',
			id: 'txtDocno',
			labelWidth: 50,
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

		var txtNote = {
			anchor: '65%',
			emptyText: "Enter a Note",
			fieldLabel: 'Note',
			grow: true,
			growMin: 35,
			growMax: 35,
			id: 'txtNote',
			labelWidth: 50,
			name: 'txtNote',
			xtype: 'textareafield'
		};

		var grupDeptDr = Ext.create('Ext.data.Store', {
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
				url: 'regstatus/dept'
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_code': Ext.getCmp('cboDeptDr').getValue(),
						'fs_nm_code': Ext.getCmp('cboDeptDr').getValue(),
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
			store: grupDeptDr,
			columns: [
				{xtype: 'rownumberer', width: 45},
				{text: "Dept Code", dataIndex: 'fs_code', menuDisabled: true, hidden: true},
				{text: "Dept Count", dataIndex: 'fs_count', menuDisabled: true, hidden: true},
				{text: "Dept Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 480}
			],
			tbar: [{
				flex: 1,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					anchor: '98%',
					emptyText: 'Dept Code / Dept Name',
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
						grupDeptDr.load();
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
				store: grupDeptDr,
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
					Ext.getCmp('cboDeptDr').setValue(record.get('fs_code').concat(record.get('fs_count')));
					Ext.getCmp('txtDeptDr').setValue(record.get('fs_nm_code'));
					Ext.getCmp('txtDeptCdDr').setValue(record.get('fs_code'));
					Ext.getCmp('txtCountDr').setValue(record.get('fs_count'));
					
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
					grupDeptDr.load();
					vMask.show();
				}
			}
		});

		var cboDeptDr = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '98%',
			emptyText: 'Select a',
			fieldLabel: 'Department',
			fieldStyle: 'background-color: #eee; background-image: none;',
			id: 'cboDeptDr',
			labelWidth: 80,
			name: 'cboDeptDr',
			readOnly: true,
			xtype: 'textfield',
			listeners: {
				change: function(field, newValue) {
					Ext.getCmp('txtDeptDr').setValue('');
					Ext.getCmp('txtDeptCdDr').setValue('');
					Ext.getCmp('txtCountDr').setValue('');
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

		var txtDeptDr = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '100%',
			emptyText: 'From Department',
			fieldStyle: 'background-color: #eee; background-image: none;',
			id: 'txtDeptDr',
			name: 'txtDeptDr',
			readOnly: true,
			xtype: 'textfield'
		};

		var txtDeptCdDr = {
			anchor: '100%',
			emptyText: '',
			fieldLabel: 'Dept Code',
			hidden: true,
			id: 'txtDeptCdDr',
			labelWidth: 80,
			name: 'txtDeptCdDr',
			xtype: 'textfield'
		};

		var txtCountDr = {
			anchor: '100%',
			emptyText: '',
			fieldLabel: 'Dept Count',
			hidden: true,
			id: 'txtCountDr',
			labelWidth: 80,
			name: 'txtCountDr',
			xtype: 'textfield'
		};

		var grupGudangDr = Ext.create('Ext.data.Store', {
			autoLoad: false,
			fields: [
				'fs_code','fs_nm_code'
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
				url: 'regstatus/gudang'
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_kd_dept': Ext.getCmp('txtDeptCdDr').getValue(),
						'fs_count': Ext.getCmp('txtCountDr').getValue(),
						'fs_code': Ext.getCmp('cboWHDr').getValue(),
						'fs_nm_code': Ext.getCmp('cboWHDr').getValue(),
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
			store: grupGudangDr,
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
						grupGudangDr.load();
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
				store: grupGudangDr,
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
					Ext.getCmp('cboWHDr').setValue(record.get('fs_code'));
					Ext.getCmp('txtWHDr').setValue(record.get('fs_nm_code'));
					
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
					grupGudangDr.load();
					vMask.show();
				}
			}
		});

		var cboWHDr = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '98%',
			emptyText: 'Select a',
			fieldLabel: 'Warehouse',
			id: 'cboWHDr',
			labelWidth: 80,
			name: 'cboWHDr',
			xtype: 'textfield',
			listeners: {
				change: function(field, newValue) {
					Ext.getCmp('txtWHDr').setValue('');
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

		var txtWHDr = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '100%',
			emptyText: 'From Warehouse',
			fieldStyle: 'background-color: #eee; background-image: none;',
			id: 'txtWHDr',
			name: 'txtWHDr',
			readOnly: true,
			xtype: 'textfield'
		};

		var grupDeptKe = Ext.create('Ext.data.Store', {
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
				url: 'regstatus/dept'
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_code': Ext.getCmp('cboDeptKe').getValue(),
						'fs_nm_code': Ext.getCmp('cboDeptKe').getValue(),
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
			store: grupDeptKe,
			columns: [
				{xtype: 'rownumberer', width: 45},
				{text: "Code", dataIndex: 'fs_code', menuDisabled: true, width: 100},
				{text: "Count", dataIndex: 'fs_count', menuDisabled: true, width: 100},
				{text: "Dept Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 280}
			],
			tbar: [{
				flex: 1,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					anchor: '98%',
					emptyText: 'Code / Dept Name',
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
						grupDeptKe.load();
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
				store: grupDeptKe,
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
					Ext.getCmp('cboDeptKe').setValue(record.get('fs_code').concat(record.get('fs_count')));
					Ext.getCmp('txtDeptKe').setValue(record.get('fs_nm_code'));
					Ext.getCmp('txtDeptCdKe').setValue(record.get('fs_code'));
					Ext.getCmp('txtCountKe').setValue(record.get('fs_count'));
					
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
					grupDeptKe.load();
					vMask.show();
				}
			}
		});

		var cboDeptKe = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '98%',
			emptyText: 'Select a',
			fieldLabel: 'Department',
			id: 'cboDeptKe',
			labelWidth: 80,
			name: 'cboDeptKe',
			xtype: 'textfield',
			listeners: {
				change: function(field, newValue) {
					Ext.getCmp('txtDeptKe').setValue('');
					Ext.getCmp('txtDeptCdKe').setValue('');
					Ext.getCmp('txtCountKe').setValue('');
					Ext.getCmp('cboWHKe').setValue('');
					Ext.getCmp('txtWHKe').setValue('');
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

		var txtDeptKe = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '100%',
			emptyText: 'To Department',
			fieldStyle: 'background-color: #eee; background-image: none;',
			id: 'txtDeptKe',
			name: 'txtDeptKe',
			readOnly: true,
			xtype: 'textfield'
		};

		var txtDeptCdKe = {
			anchor: '100%',
			emptyText: '',
			fieldLabel: 'Dept Code',
			hidden: true,
			id: 'txtDeptCdKe',
			labelWidth: 80,
			name: 'txtDeptCdKe',
			xtype: 'textfield'
		};

		var txtCountKe = {
			anchor: '100%',
			emptyText: '',
			fieldLabel: 'Dept Count',
			hidden: true,
			id: 'txtCountKe',
			labelWidth: 80,
			name: 'txtCountKe',
			xtype: 'textfield'
		};

		var grupGudangKe = Ext.create('Ext.data.Store', {
			autoLoad: false,
			fields: [
				'fs_code','fs_nm_code'
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
				url: 'regstatus/gudang'
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_kd_dept': Ext.getCmp('txtDeptCdKe').getValue(),
						'fs_count': Ext.getCmp('txtCountKe').getValue(),
						'fs_code': Ext.getCmp('cboWHKe').getValue(),
						'fs_nm_code': Ext.getCmp('cboWHKe').getValue(),
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
			store: grupGudangKe,
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
						grupGudangKe.load();
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
				store: grupGudangKe,
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
					Ext.getCmp('cboWHKe').setValue(record.get('fs_code'));
					Ext.getCmp('txtWHKe').setValue(record.get('fs_nm_code'));
					
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
					grupGudangKe.load();
					vMask.show();
				}
			}
		});
		
		var cboWHKe = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '98%',
			emptyText: 'Select a',
			fieldLabel: 'Warehouse',
			id: 'cboWHKe',
			labelWidth: 80,
			name: 'cboWHKe',
			xtype: 'textfield',
			listeners: {
				change: function(field, newValue) {
					Ext.getCmp('txtWHKe').setValue('');
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
						var xNmDept = Ext.getCmp('txtDeptKe').getValue();
						var xLanjut = true;
						
						if (xNmDept.trim() === '') {
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.OK,
								closable: false,
								icon: Ext.MessageBox.INFO,
								msg: 'To Department is empty, pleas fill in advance!!',
								title: 'IDS'
							});
							xLanjut = false;
						}
						
						if (xLanjut === false) {
							return;
						}
						winCari5.show();
						winCari5.center();
					}
				}
			}
		};

		var txtWHKe = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '100%',
			emptyText: 'To Warehouse',
			fieldStyle: 'background-color: #eee; background-image: none;',
			id: 'txtWHKe',
			name: 'txtWHKe',
			readOnly: true,
			xtype: 'textfield'
		};

		var grupProd = Ext.create('Ext.data.Store', {
			autoLoad: false,
			fields: [
				'fs_kd_product','fs_nm_product',
				'fs_kd_unit','fs_nm_unit'
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
				url: 'mutasiout/product'
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
				{text: "Product Name", dataIndex: 'fs_nm_product', menuDisabled: true, width: 330},
				{text: "Unit Code", dataIndex: 'fs_kd_unit', menuDisabled: true, hidden: true},
				{text: "Unit Name", dataIndex: 'fs_nm_unit', menuDisabled: true, hidden: true}
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
					Ext.getCmp('cboUnit').setValue(record.get('fs_kd_unit'));
					
					fnCekUnit();
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
				url: 'mutasiout/unit'
			}
		});

		var cellEditingProd = Ext.create('Ext.grid.plugin.CellEditing', {
			clicksToEdit: 2
		});

		Ext.define('DataGridProd', {
			extend: 'Ext.data.Model',
			fields: [
				{name: 'fs_kd_product', type: 'string'},
				{name: 'fs_nm_product', type: 'string'},
				{name: 'fn_qty', type: 'float'},
				{name: 'fs_kd_unit', type: 'string'},
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
				url: 'mutasiout/grid_prod'
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_kd_dept': Ext.getCmp('txtDeptCdDr').getValue(),
						'fs_count': Ext.getCmp('txtCountDr').getValue(),
						'fs_refno': Ext.getCmp('cboRefno').getValue()
					});
				},
				load: function() {
					var xtotal = grupGridProd.getCount();
					
					if (xtotal > 0) {
						var store = gridProd.getStore();
						var xqty = 0;
						
						store.each(function(record, idx) {
							xqty = parseInt(record.get('fs_seqno'));
						});
						Ext.getCmp('txtUrutProd').setValue(xqty);
						
						grupGridReg2.load();
					}
				}
			}
		});

		var gridProd = Ext.create('Ext.grid.Panel', {
			anchor: '100%',
			defaultType: 'textfield',
			height: 250,
			sortableColumns: false,
			store: grupGridProd,
			columns: [{
				xtype: 'rownumberer',
				width: 25
			},{
				dataIndex: 'fs_kd_product',
				text: 'Product Code',
				flex: 1,
				menuDisabled: true
			},{
				dataIndex: 'fs_nm_product',
				text: 'Product Name',
				flex: 1.25,
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
								
								/* start edit */
								var xxxKdProd = recordgrid.get('fs_kd_product');
								var xxxKdWh = Ext.getCmp('cboWHDr').getValue();
								var xxxSeqNo = recordgrid.get('fs_seqno');
								var xxxRefNo = Ext.getCmp('cboRefno').getValue();

								Ext.getCmp('txtXKdProduct').setValue(xxxKdProd);
								Ext.getCmp('txtXKdWH').setValue(xxxKdWh);
								Ext.getCmp('txtXRefNo').setValue(xxxSeqNo);
								Ext.getCmp('txtXSeqNo').setValue(xxxRefNo);
								/* end edit */

								Ext.getCmp('txtProdAktif').setValue(xKdProd);
								Ext.getCmp('txtProdAktif2').setValue(xKdProd + ' - ' + xNmProd);
								
								grupGridReg.removeAll();
								grupGridReg.load({
									params: {
										// nothing load	
									}
								});
								
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
				flex: 0.5,
				menuDisabled: true
			},{
				dataIndex: 'fs_seqno',
				text: 'Seqno',
				flex: 0.5,
				hidden: true,
				menuDisabled: true
			}],
			listeners: {
				selectionchange: function(view, records) {
					gridProd.down('#removeData').setDisabled(!records.length);
				}
			},
			plugins: [
				cellEditingProd
			],
			bbar: [{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						id: 'txtToolTip',
						labelWidth: 65,
						name: 'txtToolTip',
						value: '<*Double click on the Qty to display the chassis>',
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
			}],
			tbar: [{
				flex: 1.5,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					anchor: '95%',
					emptyText: 'Select a Product',
					fieldLabel: 'Product',
					id: 'cboProd',
					labelWidth: 45,
					name: 'cboProd',
					xtype: 'textfield',
					listeners: {
						change: function(field, newValue) {
							Ext.getCmp('txtProd').setValue('');
							Ext.getCmp('cboUnit').setValue('UNIT');
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
				},{
					anchor: '95%',
					emptyText: 'Enter a Product Name',
					hidden: true,
					id: 'txtProd',
					name: 'txtProd',
					xtype: 'textfield'
				}]
			},{
				flex: 1,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					anchor: '95%',
					displayField: 'fs_nm_unit',
					editable: false,
					fieldLabel: 'Unit',
					fieldStyle: 'text-transform: uppercase;',
					id: 'cboUnit',
					labelWidth: 30,
					listConfig: {
						maxHeight: 110
					},
					name: 'cboUnit',
					store: grupUnit,
					value: 'UNIT',
					valueField: 'fs_kd_unit',
					xtype: 'combobox'
				},
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
				xtype: 'buttongroup',
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
							fs_seqno: zeroPad(xurut, 6)
						});
						
						var xwh = Ext.getCmp('txtWHDr').getValue();
						if (xwh.trim() === '') {
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.OK,
								closable: false,
								icon: Ext.Msg.WARNING,
								msg: 'From Warehouse is empty, please fill in advance!!',
								title: 'IDS'
							});
							return;
						}
						
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
						
						grupGridProd.insert(xtotal, xdata);
						Ext.getCmp('cboProd').setValue('');
						Ext.getCmp('txtProd').setValue('');
						Ext.getCmp('cboUnit').setValue('UNIT');
						Ext.getCmp('txtUrutProd').setValue(xurut);
					}
				},{
					iconCls: 'icon-delete',
					itemId: 'removeData',
					text: 'Delete',
					handler: function() {
						var record = gridProd.getSelectionModel().getSelection()[0];
						var xKdProd = record.get('fs_kd_product');
						
						grupGridReg2.clearFilter();
						var xTotal = grupGridReg2.getCount();
						if (xTotal > 0) {
							grupGridReg2.filterBy(function(record) {
								if (record.get('fs_kd_product').trim() == xKdProd.trim()) {
									return true;
								}
								else {
									return false;
								}
							});
							gridReg2.getView().refresh();
						}
						grupGridReg2.removeAll();
						
						grupGridReg2.clearFilter();
						gridReg2.getView().refresh();
						grupGridReg.removeAll();
						gridReg.getView().refresh();
						
						var sm = gridProd.getSelectionModel();
						cellEditingProd.cancelEdit();
						grupGridProd.remove(sm.getSelection());
						gridProd.getView().refresh();
						if (grupGridProd.getCount() > 0) {
							sm.select(0);
						}
						Ext.getCmp('txtProdAktif').setValue('');
					},
					disabled: true
				}]
			}],
			viewConfig: {
				getRowClass: function() {
					return 'rowwrap';
				},
				listeners: {
					render: gridTooltipProd
				},
				markDirty: false,
				stripeRows: true
			}
		});

		Ext.define('DataGridReg', {
			extend: 'Ext.data.Model',
			fields: [
				{name: 'fs_rangka', type: 'string'},
				{name: 'fs_mesin', type: 'string'},
				{name: 'fd_thn', type: 'string'},
				{name: 'fs_nm_warna', type: 'string'},
				{name: 'fb_cek', type: 'bool'},
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
				url: 'mutasiout/reg'
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_cari': Ext.getCmp('txtCari7').getValue(),
						'fs_kd_product': Ext.getCmp('txtXKdProduct').getValue(),
						'fs_kd_wh': Ext.getCmp('txtXKdWH').getValue(),
						'fs_refno': Ext.getCmp('txtXRefNo').getValue(),
						'fs_seqno': Ext.getCmp('txtXSeqNo').getValue()
					});
				},
				load: function() {
					fnInitData();
				}
			}
		});
		
		var gridReg = Ext.create('Ext.grid.Panel', {
			anchor: '100%',
			defaultType: 'textfield',
			height: 270,
			sortableColumns: false,
			store: grupGridReg,
			bbar: [{
				id: 'txtProdAktif2',
				name: 'txtProdAktif2',
				value: '',
				xtype: 'displayfield'
			},{
				xtype: 'tbfill'
			},{
				text: 'Check All',
				xtype: 'button',
				handler: function() {
					var storeReg = gridReg.getStore();
					storeReg.each(function(record, idx) {
						record.set('fb_cek','1');
					});
					
					var xKdProd = Ext.getCmp('txtProdAktif').getValue();
					
					grupGridReg2.clearFilter();
					var xTotal = grupGridReg2.getCount();
					if (xTotal > 0) {
						grupGridReg2.filterBy(function(record) {
							if (record.get('fs_kd_product').trim() == xKdProd.trim()) {
								return true;
							}
							else {
								return false;
							}
						});
						gridReg2.getView().refresh();
					}
					grupGridReg2.removeAll();
					
					grupGridReg2.clearFilter();
					storeReg = gridReg.getStore();
					storeReg.each(function(record, idx) {
						var xrangka = record.get('fs_rangka').trim();
						var xmesin = record.get('fs_mesin').trim();
						var xseqno = record.get('fs_seqno').trim();
						var xprod = record.get('fs_kd_product').trim();
						
						var xdata = Ext.create('DataGridReg', {
							fs_rangka: xrangka.trim(),
							fs_mesin: xmesin.trim(),
							fs_seqno: xseqno.trim(),
							fs_kd_product: xprod.trim()
						});
						
						xtotal = grupGridReg2.getCount();
						grupGridReg2.insert(xtotal, xdata);
					});
					grupGridReg2.sort([ {
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
					gridReg2.getView().refresh();
				}
			},{
				text: 'Uncheck All',
				xtype: 'button',
				handler: function() {
					var storeReg = gridReg.getStore();
					storeReg.each(function(record, idx) {
						record.set('fb_cek','0');
					});
					
					var xKdProd = Ext.getCmp('txtProdAktif').getValue();
					
					grupGridReg2.clearFilter();
					var xTotal = grupGridReg2.getCount();
					if (xTotal > 0) {
						grupGridReg2.filterBy(function(record) {
							if (record.get('fs_kd_product').trim() == xKdProd.trim()) {
								return true;
							}
							else {
								return false;
							}
						});
						gridReg2.getView().refresh();
					}
					grupGridReg2.removeAll();
					grupGridReg2.clearFilter();
				}
			}],
			columns: [{
				xtype: 'rownumberer', width: 30
			},{
				text: 'Chassis',
				dataIndex: 'fs_rangka',
				flex: 1,
				menuDisabled: true
			},{
				text: 'Machine',
				dataIndex: 'fs_mesin',
				flex: 1,
				menuDisabled: true
			},{
				text: 'Year',
				dataIndex: 'fd_thn',
				flex: 0.2,
				menuDisabled: true
			},{
				text: 'Color',
				dataIndex: 'fs_nm_warna',
				flex: 1,
				menuDisabled: true
			},{
				align: 'center',
				text: 'Add',
				dataIndex: 'fb_cek',
				flex: 0.25,
				menuDisabled: true,
				stopSelection: false,
				xtype: 'checkcolumn',
				listeners: {
					'checkchange': function(grid, rowIndex, checked) {
						var storeReg = gridReg.getStore();
						var record = storeReg.getAt(rowIndex);
						var storeReg2;
						var storeProd;
						
						var xrangka = record.get('fs_rangka').trim();
						var xmesin = record.get('fs_mesin').trim();
						var xseqno = record.get('fs_seqno').trim();
						var xprod = Ext.getCmp('txtProdAktif').getValue();
						var xcek = record.get('fb_cek');
						var xtotal = 0;
						
						grupGridReg2.clearFilter();
						if (xcek === true) {
							xtotal = grupGridReg2.getCount();
							var xdata = Ext.create('DataGridReg', {
								fs_rangka: xrangka.trim(),
								fs_mesin: xmesin.trim(),
								fs_seqno: xseqno.trim(),
								fs_kd_product: xprod.trim()
							});
							grupGridReg2.insert(xtotal, xdata);
						}
						else {
							var arr_rangka = Array();
							var arr_mesin = Array();
							var arr_seqno = Array();
							
							grupGridReg2.clearFilter();
							var xKdProd = Ext.getCmp('txtProdAktif').getValue();
							var xTotal = grupGridReg2.getCount();
							if (xTotal > 0) {
								grupGridReg2.filterBy(function(record) {
									if (record.get('fs_kd_product').trim() == xKdProd.trim()) {
										return true;
									}
									else {
										return false;
									}
								});
								gridReg2.getView().refresh();
							}
							
							xtotal = grupGridReg2.getCount();
							storeReg2 = gridReg2.getStore();
							storeReg2.each(function(record, idx) {
								arr_rangka.push(record.get('fs_rangka').trim());
								arr_mesin.push(record.get('fs_mesin').trim());
								arr_seqno.push(record.get('fs_seqno').trim());
							});
							
							xtotal = grupGridReg2.getCount()-1;
							
							for (i=xtotal;i>=0;i--) {
								var xxrangka = arr_rangka[i];
								var xxmesin = arr_mesin[i];
								var xxseqno = arr_seqno[i];
								
								if (xrangka.trim() == xxrangka.trim() &&
									xmesin.trim() == xxmesin.trim() &&
									xseqno.trim() == xxseqno.trim()) {
									
									grupGridReg2.removeAt(i);
								}
							}
						}
						grupGridReg2.clearFilter();
						grupGridReg2.sort([ {
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
						gridReg2.getView().refresh();
					}
				}
			},{
				text: 'Seqno',
				dataIndex: 'fs_seqno',
				flex: 0.5,
				hidden: true,
				menuDisabled: true
			},{
				text: 'Product',
				dataIndex: 'fs_kd_product',
				flex: 0.5,
				hidden: true,
				menuDisabled: true
			}],
			tbar: [{
				flex: 1,
				layout: 'anchor',
				xtype: 'container',
				items: [{
					anchor: '98%',
					emptyText: 'Chassis / Machine',
					id: 'txtCari7',
					name: 'txtCari7',
					xtype: 'textfield'
				},{
					id: 'txtXKdProduct',
					name: 'txtXKdProduct',
					xtype: 'textfield',
					hidden: true
				},{
					id: 'txtXKdWH',
					name: 'txtXKdWH',
					xtype: 'textfield',
					hidden: true
				},{
					id: 'txtXRefNo',
					name: 'txtXRefNo',
					xtype: 'textfield',
					hidden: true
				},{
					id: 'txtXSeqNo',
					name: 'txtXSeqNo',
					xtype: 'textfield',
					hidden: true
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
						grupGridReg.load();
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
		
		var winReg = Ext.create('Ext.window.Window', {
			border: false,
			closable: false,
			draggable: true,
			frame: false,
			height: 500,
			layout: 'fit',
			resizable: false,
			title: 'Register',
			width: 700,
			items: [
				gridReg
			],
			buttons: [{
				text: 'OK',
				handler: function() {
					var storeReg2;
					var storeProd = gridProd.getStore();
					storeProd.each(function(xrecord) {
						xKdProd = xrecord.get('fs_kd_product').trim();
						xrecord.set('fn_qty',0);
						
						grupGridReg2.clearFilter();
						xTotal = grupGridReg2.getCount();
						if (xTotal > 0) {
							grupGridReg2.filterBy(function(record) {
								if (record.get('fs_kd_product').trim() == xKdProd.trim()) {
									return true;
								}
								else {
									return false;
								}
							});
							gridReg2.getView().refresh();
						}
						
						xQty = 0;
						storeReg2 = gridReg2.getStore();
						storeReg2.each(function(xxrecord) {
							if (xxrecord.get('fs_kd_product').trim() === xKdProd.trim()) {
								xQty = xQty + 1;
							}
						});
						xrecord.set('fn_qty',xQty);
					});
					grupGridReg2.clearFilter();
					
					vMask.hide();
					winReg.hide();
				}
			}]
		});
		
		Ext.define('DataGridReg2', {
			extend: 'Ext.data.Model',
			fields: [
				{name: 'fs_rangka', type: 'string'},
				{name: 'fs_mesin', type: 'string'},
				{name: 'fs_seqno', type: 'string'},
				{name: 'fs_kd_product', type: 'string'}
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
				url: 'mutasiout/grid_reg'
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_kd_dept': Ext.getCmp('txtDeptCdDr').getValue(),
						'fs_count': Ext.getCmp('txtCountDr').getValue(),
						'fs_refno': Ext.getCmp('cboRefno').getValue()
					});
				},
				load: function() {
					grupGridReg2.clearFilter();
					grupGridReg2.sort([ {
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
					
					var xTotal = 0;
					var xKdProd = '';
					var xQty = 0;
					
					var storeReg2;
					var storeProd = gridProd.getStore();
					storeProd.each(function(xrecord) {
						xKdProd = xrecord.get('fs_kd_product').trim();
						xrecord.set('fn_qty',0);
						
						grupGridReg2.clearFilter();
						xTotal = grupGridReg2.getCount();
						if (xTotal > 0) {
							grupGridReg2.filterBy(function(record) {
								if (record.get('fs_kd_product').trim() == xKdProd.trim()) {
									return true;
								}
								else {
									return false;
								}
							});
							gridReg2.getView().refresh();
						}
						
						xQty = 0;
						storeReg2 = gridReg2.getStore();
						storeReg2.each(function(xxrecord) {
							if (xxrecord.get('fs_kd_product').trim() === xKdProd.trim()) {
								xQty = xQty + 1;
							}
						});
						xrecord.set('fn_qty',xQty);
					});
					grupGridReg2.clearFilter();
					gridReg2.getView().refresh();
				}
			}
		});
		
		var gridReg2 = Ext.create('Ext.grid.Panel', {
			anchor: '100%',
			defaultType: 'textfield',
			height: 100,
			hidden: true,
			sortableColumns: false,
			store: grupGridReg2,
			columns: [{
				xtype: 'rownumberer'
			},{
				text: 'Chassis',
				dataIndex: 'fs_rangka',
				flex: 1,
				menuDisabled: true
			},{
				text: 'Machine',
				dataIndex: 'fs_mesin',
				flex: 1,
				menuDisabled: true
			},{
				text: 'Seqno',
				dataIndex: 'fs_seqno',
				flex: 0.5,
				menuDisabled: true
			},{
				text: 'Product',
				dataIndex: 'fs_kd_product',
				flex: 0.5,
				menuDisabled: true
			}],
			viewConfig: {
				getRowClass: function() {
					return 'rowwrap';
				},
				markDirty: false,
				stripeRows: true
			}
		});

		function fnInitData() {
			var arr_rangka = Array();
			var arr_mesin = Array();
			var arr_seqno = Array();
			
			var xKdProd = Ext.getCmp('txtProdAktif').getValue();
			
			grupGridReg2.clearFilter();
			var xTotal = grupGridReg2.getCount();
			if (xTotal > 0) {
				grupGridReg2.filterBy(function(record) {
					if (record.get('fs_kd_product').trim() == xKdProd.trim()) {
						return true;
					}
					else {
						return false;
					}
				});
				gridReg2.getView().refresh();
			}
			
			var store = gridReg2.getStore();
			store.each(function(record, idx) {
				arr_rangka.push(record.get('fs_rangka').trim());
				arr_mesin.push(record.get('fs_mesin').trim());
				arr_seqno.push(record.get('fs_seqno').trim());
			});
			
			for (i=0;i<arr_rangka.length;i++) {
				var xrangka = arr_rangka[i].trim();
				var xmesin = arr_mesin[i].trim();
				var xseqno = arr_seqno[i].trim();
				
				store = gridReg.getStore();
				store.each(function(record, idx) {
					var xxrangka = record.get('fs_rangka').trim();
					var xxmesin = record.get('fs_mesin').trim();
					var xxseqno = record.get('fs_seqno').trim();
					
					if (xrangka.trim() == xxrangka.trim() &&
						xmesin.trim() == xxmesin.trim() &&
						xseqno.trim() == xxseqno.trim()) {
						
						record.set('fb_cek','1');
						return false;
					}
				});
				
			}
			grupGridReg2.clearFilter();
			gridReg2.getView().refresh();
		}

		function fnCekSave() {
			if (this.up('form').getForm().isValid()) {
				grupGridReg2.clearFilter();
				
				var xtotal = grupGridReg2.getCount();
				if (xtotal <= 0) {
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: 'Chassis List is empty, please fill in advance!!',
						title: 'IDS'
					});
					return;
				}
				
				var xrangka = "'";
				var lxrangka = 0;
				var xmesin = "'";
				var lxmesin = 0;
				
				store = gridReg2.getStore();
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
					url: 'mutasiout/mutasi_ceksave',
					params: {
						'fs_refno': Ext.getCmp('cboRefno').getValue(),
						'fs_kd_dept': Ext.getCmp('txtDeptCdDr').getValue(),
						'fs_count': Ext.getCmp('txtCountDr').getValue(),
						'fs_kd_trx': '3400',
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
			var xqty = '';
			var xunit = '';
			var xseqno = '';
			var xprodreg = '';
			var xrangka = '';
			var xmesin = '';
			var xseqnoreg = '';
			
			var store = gridProd.getStore();
			store.each(function(record, idx) {
				xprod = xprod +'|'+ record.get('fs_kd_product');
				xqty = xqty +'|'+ record.get('fn_qty');
				xunit = xunit +'|'+ record.get('fs_kd_unit');
				xseqno = xseqno +'|'+ record.get('fs_seqno');
			});
			
			store = gridReg2.getStore();
			store.each(function(record, idx) {
				xprodreg = xprodreg +'|'+ record.get('fs_kd_product');
				xrangka = xrangka +'|'+ record.get('fs_rangka');
				xmesin = xmesin +'|'+ record.get('fs_mesin');
				xseqnoreg = xseqnoreg +'|'+ record.get('fs_seqno');
			});
			
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'mutasiout/mutasi_save',
				params: {
					'fs_refno': Ext.getCmp('cboRefno').getValue(),
					'fd_refno': Ext.Date.format(Ext.getCmp('txtRefnodt').getValue(), 'Ymd'),
					'fs_docno': Ext.getCmp('txtDocno').getValue(),
					'fd_docno': Ext.Date.format(Ext.getCmp('txtDocnodt').getValue(), 'Ymd'),
					'fs_note': Ext.getCmp('txtNote').getValue(),
					'fs_kd_dept': Ext.getCmp('txtDeptCdDr').getValue(),
					'fs_count': Ext.getCmp('txtCountDr').getValue(),
					'fs_kd_wh': Ext.getCmp('cboWHDr').getValue(),
					'fs_nm_wh': Ext.getCmp('txtWHDr').getValue(),
					'fs_kd_deptt': Ext.getCmp('txtDeptCdKe').getValue(),
					'fs_countt': Ext.getCmp('txtCountKe').getValue(),
					'fs_kd_wht': Ext.getCmp('cboWHKe').getValue(),
					'fs_nm_wht': Ext.getCmp('txtWHKe').getValue(),
					'fs_kd_trx': '3400',
					'fs_kd_product': xprod,
					'fn_qty': xqty,
					'fs_kd_unit': xunit,
					'fs_seqno': xseqno,
					'fs_kd_prodreg': xprodreg,
					'fs_rangka': xrangka,
					'fs_mesin': xmesin,
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
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'mutasiout/mutasi_cekremove',
				params: {
					'fs_refno': Ext.getCmp('cboRefno').getValue(),
					'fs_kd_dept': Ext.getCmp('txtDeptCdDr').getValue(),
					'fs_count': Ext.getCmp('txtCountDr').getValue(),
					'fs_kd_trx': '3400'
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
				url: 'mutasiout/mutasi_remove',
				params: {
					'fs_refno': Ext.getCmp('cboRefno').getValue(),
					'fs_kd_dept': Ext.getCmp('txtDeptCdDr').getValue(),
					'fs_count': Ext.getCmp('txtCountDr').getValue(),
					'fs_kd_wh': Ext.getCmp('cboWHDr').getValue(),
					'fs_nm_wh': Ext.getCmp('txtWHDr').getValue(),
					'fs_kd_trx': '3400'
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
				url: 'mutasiout/print_mutasiout',
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
							title: 'Mutation Out',
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
			Ext.getCmp('cboRefno').setValue('');
			Ext.getCmp('txtRefnodt').setValue(new Date());
			Ext.getCmp('txtDocno').setValue('');
			Ext.getCmp('txtDocnodt').setValue(new Date());
			Ext.getCmp('txtNote').setValue('');
			Ext.getCmp('cboDeptDr').setValue('');
			Ext.getCmp('txtDeptDr').setValue('');
			Ext.getCmp('txtDeptCdDr').setValue('');
			Ext.getCmp('txtCountDr').setValue('');
			Ext.getCmp('cboWHDr').setValue('');
			Ext.getCmp('txtWHDr').setValue('');
			Ext.getCmp('cboDeptKe').setValue('');
			Ext.getCmp('txtDeptKe').setValue('');
			Ext.getCmp('txtDeptCdKe').setValue('');
			Ext.getCmp('txtCountKe').setValue('');
			Ext.getCmp('cboWHKe').setValue('');
			Ext.getCmp('txtWHKe').setValue('');
			Ext.getCmp('cboProd').setValue('');
			Ext.getCmp('txtProd').setValue('');
			Ext.getCmp('cboUnit').setValue('UNIT');
			Ext.getCmp('txtUrutProd').setValue('0');
			Ext.getCmp('txtProdAktif').setValue('');
			Ext.getCmp('txtProdAktif2').setValue('');
			
			grupGridProd.load();
			grupGridReg.removeAll();
			gridReg.getView().refresh();
			grupGridReg2.removeAll();
			gridReg2.getView().refresh();
			
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'alamat/nilaidefa',
				success: function(response) {
					var xtext = Ext.decode(response.responseText);
					
					if (xtext.sukses === true) {
						Ext.getCmp('cboDeptDr').setValue(xtext.kddept);
						Ext.getCmp('txtDeptCdDr').setValue(xtext.deptcd);
						Ext.getCmp('txtCountDr').setValue(xtext.count);
						Ext.getCmp('txtDeptDr').setValue(xtext.nmdept);
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
					fnMaskHide();
				}
			});
		}

		var frmMutasiOut = Ext.create('Ext.form.Panel', {
			border: false,
			frame: true,
			region: 'center',
			title: 'Mutation Out Form',
			width: 800,
			fieldDefaults: {
				labelAlign: 'right',
				labelSeparator: '',
				msgTarget: 'side'
			},
			items: [{
				style: 'padding: 5px;',
				xtype: 'fieldset',
				items: [{
					anchor: '65%',
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
				},
					txtNote
				]
			},{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						style: 'padding: 5px;',
						title: 'FROM',
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
									anchor: '100%',
									layout: 'hbox',
									xtype: 'container',
									items: [{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											cboDeptDr
										]
									},{
										flex: 1.2,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtDeptDr
										]
									}]
								},
									txtDeptCdDr,
									txtCountDr,
								{
									anchor: '100%',
									layout: 'hbox',
									xtype: 'container',
									items: [{
										flex: 1,
										layout: 'anchor',
										xtype: 'container',
										items: [
											cboWHDr
										]
									},{
										flex: 1.2,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtWHDr
										]
									}]
								}]
							}]
						}]
					}]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						style: 'padding: 5px;',
						title: 'TO',
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
												cboDeptKe
											]
										},{
											flex: 1.2,
											layout: 'anchor',
											xtype: 'container',
											items: [
												txtDeptKe
											]
										}]
									},
										txtDeptCdKe,
										txtCountKe,
									{
										anchor: '100%',
										layout: 'hbox',
										xtype: 'container',
										items: [{
											flex: 1,
											layout: 'anchor',
											xtype: 'container',
											items: [
												cboWHKe
											]
										},{
											flex: 1.2,
											layout: 'anchor',
											xtype: 'container',
											items: [
												txtWHKe
											]
										}]
									}]
								}]
							}]
						}]
					}]
				}]
			},
				gridProd,
				gridReg2
			],
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
			target: frmMutasiOut
		});
		
		function fnMaskShow() {
			frmMutasiOut.mask('Please wait...');
		}

		function fnMaskHide() {
			frmMutasiOut.unmask();
		}
		
		frmMutasiOut.render(Ext.getBody());
	}
	
	Ext.Ajax.request({
		method: 'POST',
		url: 'alamat/nilaidefa',
		success: function(response) {
			var xtext = Ext.decode(response.responseText);
			
			if (xtext.sukses === true) {
				buatForm();
				
				Ext.getCmp('cboDeptDr').setValue(xtext.kddept);
				Ext.getCmp('txtDeptCdDr').setValue(xtext.deptcd);
				Ext.getCmp('txtCountDr').setValue(xtext.count);
				Ext.getCmp('txtDeptDr').setValue(xtext.nmdept);
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
