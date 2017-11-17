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

	function gridTooltipSearch(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Double click on record to choose',
			target: view.el,
			trackMouse: true
		});
	}

	var grupAcno = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_acno','fs_nm_acno','fb_aktif','fs_status'
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
			url: 'rekening/kodeacno'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_acno': Ext.getCmp('cboAcno').getValue(),
					'fs_nm_acno': Ext.getCmp('cboAcno').getValue(),
					'fs_cari': Ext.getCmp('txtCari').getValue()
				});
			}
		}
	});


	var winGrid = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupAcno,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Account Cd", dataIndex: 'fs_kd_acno', menuDisabled: true, width: 150},
			{text: "Account", dataIndex: 'fs_nm_acno', menuDisabled: true, width: 250},
			{text: "Active", dataIndex: 'fb_aktif', menuDisabled: true, hidden: true},
			{text: "Active", dataIndex: 'fs_status', menuDisabled: true, width: 80}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Account Cd / Account',
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
					winCari.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboAcno').setValue(record.get('fs_kd_acno'));
				Ext.getCmp('cekAktif').setValue(record.get('fb_aktif'));
				Ext.getCmp('txtAcno').setValue(record.get('fs_nm_acno'));
				
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
				grupAcno.load();
				vMask.show();
			}
		}
	});

	var cboAcno = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '95%',
		emptyText: 'Select / Enter an Acc Code',
		fieldLabel: 'Account Code',
		id: 'cboAcno',
		name: 'cboAcno',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
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
					winCari.show();
					winCari.center();
				}
			}
		}
	};

	var cekAktif = {
		boxLabel: 'Active',
		checked: true,
		id: 'cekAktif',
		name: 'cekAktif',
		xtype: 'checkboxfield'
	};

	var txtAcno = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Enter an Account Name',
		fieldLabel: 'Account Name',
		id: 'txtAcno',
		name: 'txtAcno',
		xtype: 'textfield'
	};

	Ext.define('DataGridDetil', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_acno', type: 'string'},
			{name: 'fs_nm_acno', type: 'string'},
			{name: 'fb_active', type: 'bool'}
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
			url: 'rekening/griddetil'
		}
	});
	
	var gridDetil = Ext.create('Ext.grid.Panel', {
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		sortableColumns: false,
		store: grupGridDetil,
		columns: [{
			xtype: 'rownumberer',
			width: 45
		},{
			dataIndex: 'fs_kd_acno',
			menuDisabled: true,
			text: 'Account Code',
			width: 150
		},{
			dataIndex: 'fs_nm_acno',
			menuDisabled: true,
			text: 'Account Name',
			width: 360
		},{
			align: 'center',
			dataIndex: 'fb_active',
			menuDisabled: true,
			stopSelection: false,
			text: 'Active',
			width: 55,
			xtype: 'checkcolumn'
		}],
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
			markDirty: false,
			stripeRows: true
		}
	});

	var grupKelAcno = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_group','fs_nm_group','fs_kd_acno','fs_nm_acno'
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
			url: 'rekening/kelacno'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_group': Ext.getCmp('cboKelAcno').getValue(),
					'fs_nm_group': Ext.getCmp('cboKelAcno').getValue(),
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
		store: grupKelAcno,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Group Acc Cd", dataIndex: 'fs_kd_group', menuDisabled: true, width: 150},
			{text: "Group Account", dataIndex: 'fs_nm_group', menuDisabled: true, width: 280},
			{text: "Account Cd", dataIndex: 'fs_kd_acno', menuDisabled: true, hidden: true},
			{text: "Account", dataIndex: 'fs_nm_acno', menuDisabled: true, width: 250}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Group Acc Cd / Group Account',
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
					grupKelAcno.load();
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
			store: grupKelAcno,
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
				Ext.getCmp('cboKelAcno').setValue(record.get('fs_kd_group'));
				Ext.getCmp('txtKelAcno').setValue(record.get('fs_nm_group'));
				Ext.getCmp('cboAcno2').setValue(record.get('fs_kd_acno'));
				Ext.getCmp('txtAcno2').setValue(record.get('fs_nm_acno'));
				
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
				grupKelAcno.load();
				vMask.show();
			}
		}
	});

	var cboKelAcno = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '55%',
		emptyText: 'Select / Enter a Group Acc Code',
		fieldLabel: 'Group Acc Code',
		id: 'cboKelAcno',
		name: 'cboKelAcno',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtKelAcno').setValue('');
				Ext.getCmp('cboAcno2').setValue('');
				Ext.getCmp('txtAcno2').setValue('');
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

	var txtKelAcno = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Enter a Group Account Name',
		fieldLabel: 'Group Acc Name',
		id: 'txtKelAcno',
		name: 'txtKelAcno',
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
			url: 'rekening/kodeacno'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_acno': Ext.getCmp('cboAcno2').getValue(),
					'fs_nm_acno': Ext.getCmp('cboAcno2').getValue(),
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
		store: grupAcno2,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Account Cd", dataIndex: 'fs_kd_acno', menuDisabled: true, width: 100},
			{text: "Account", dataIndex: 'fs_nm_acno', menuDisabled: true, width: 380}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Account Cd / Account',
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
				Ext.getCmp('cboAcno2').setValue(record.get('fs_kd_acno'));
				Ext.getCmp('txtAcno2').setValue(record.get('fs_nm_acno'));
				
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

	var cboAcno2 = {
		anchor: '98%',
		emptyText: 'Select an',
		fieldLabel: 'Account Code',
		id: 'cboAcno2',
		name: 'cboAcno2',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtAcno2').setValue('');
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

	var txtAcno2 = {
		anchor: '100%',
		emptyText: 'Account Name',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtAcno2',
		name: 'txtAcno2',
		xtype: 'textfield'
	};

	Ext.define('DataGridDetil2', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_group', type: 'string'},
			{name: 'fs_nm_group', type: 'string'},
			{name: 'fs_kd_acno', type: 'string'},
			{name: 'fs_nm_acno', type: 'string'}
		]
	});

	var grupGridDetil2 = Ext.create('Ext.data.Store', {
		autoLoad: true,
		model: 'DataGridDetil2',
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
			url: 'rekening/griddetil2'
		}
	});
	
	var gridDetil2 = Ext.create('Ext.grid.Panel', {
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		sortableColumns: false,
		store: grupGridDetil2,
		columns: [{
			xtype: 'rownumberer',
			width: 30
		},{
			dataIndex: 'fs_kd_group',
			menuDisabled: true,
			text: 'Group Code',
			width: 100
		},{
			dataIndex: 'fs_nm_group',
			menuDisabled: true,
			text: 'Group Acc Name',
			width: 230
		},{
			dataIndex: 'fs_kd_acno',
			menuDisabled: true,
			text: 'Account Code',
			width: 100
		},{
			dataIndex: 'fs_nm_acno',
			menuDisabled: true,
			text: 'Account Name',
			width: 230
		}],
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupGridDetil2
		}),
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
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'rekening/ceksave',
				params: {
					'fs_kd_acno': Ext.getCmp('cboAcno').getValue()
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
			url: 'rekening/save',
			params: {
				'fs_kd_acno': Ext.getCmp('cboAcno').getValue(),
				'fb_aktif': Ext.getCmp('cekAktif').getValue(),
				'fs_nm_acno': Ext.getCmp('txtAcno').getValue()
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
				fnReset();
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

	function fnCekSave2() {
		if (this.up('form').getForm().isValid()) {
			var xKdAcno = Ext.getCmp('cboAcno2').getValue();
			var xNmAcno = Ext.getCmp('txtAcno2').getValue();
			
			if (xKdAcno.trim() !== '' && xNmAcno.trim() === '') {
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					message: 'Account No. is not in the list!',
					title: 'IDS'
				});
				return;
			}
			
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'rekening/ceksave2',
				params: {
					'fs_kd_group': Ext.getCmp('cboKelAcno').getValue()
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
							fnSave2();
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
										fnSave2();
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

	function fnSave2() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'rekening/save2',
			params: {
				'fs_kd_group': Ext.getCmp('cboKelAcno').getValue(),
				'fs_nm_group': Ext.getCmp('txtKelAcno').getValue(),
				'fs_kd_acno': Ext.getCmp('cboAcno2').getValue()
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
				fnReset2();
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

	function fnPrint() {
		// Ext.Ajax.on('beforerequest', fnMaskShow);
		// Ext.Ajax.on('requestcomplete', fnMaskHide);
		// Ext.Ajax.on('requestexception', fnMaskHide);
		
		// Ext.Ajax.request({
			// method: 'POST',
			// timeout: 60000,
			// url: 'jual/printsrtpernyataan',
			// params: {
				// 'fs_refno': Ext.getCmp('txtRefno').getValue(),
				// 'fs_nama': Ext.getCmp('txtNama').getValue()
			// },
			// success: function(response) {
				// var xtext = Ext.decode(response.responseText);
				// /*
				// Ext.MessageBox.show({
					// buttons: Ext.MessageBox.OK,
					// closable: false,
					// icon: Ext.MessageBox.INFO,
					// msg: xtext.hasil,
					// title: 'IDS'
				// });*/
				
				// if (xtext.sukses === true) {
					// var xfile = xtext.nmfile.trim();
					
					// var winpdf = Ext.create('Ext.window.Window', {
						// closable: false,
						// draggable: false,
						// height: 500,
						// layout: 'fit',
						// title: 'Agreement Letter',
						// width: 900,
						// items: {
							// xtype: 'component',
							// autoEl: {
								// src: xfile,
								// tag: 'iframe'
							// }
						// },
						// buttons: [{
							// text: 'Exit',
							// handler: function() {
								// winpdf.hide();
							// }
						// }]
					// }).show();
				// }
				// else {
					// Ext.MessageBox.show({
						// buttons: Ext.MessageBox.OK,
						// closable: false,
						// icon: Ext.MessageBox.INFO,
						// msg: xtext.hasil,
						// title: 'IDS'
					// });				
				// }
			// },
			// failure: function(response) {
				// var xtext = Ext.decode(response.responseText);
				// Ext.MessageBox.show({
					// buttons: Ext.MessageBox.OK,
					// closable: false,
					// icon: Ext.MessageBox.INFO,
					// msg: 'Print Failed, Connection Failed!!',
					// title: 'IDS'
				// });
				// fnMaskHide();
			// }
		// });
	}

	function fnReset() {
		Ext.getCmp('cboAcno').setValue('');
		Ext.getCmp('cekAktif').setValue(true);
		Ext.getCmp('txtAcno').setValue('');
		
		grupGridDetil.load();
	}

	function fnReset2() {
		Ext.getCmp('cboKelAcno').setValue('');
		Ext.getCmp('txtKelAcno').setValue('');
		Ext.getCmp('cboAcno2').setValue('');
		Ext.getCmp('txtAcno2').setValue('');
		
		grupGridDetil2.load();
	}

	var frmRekening = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Account Form',
		width: 650,
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
				title: 'Account Entry',
				xtype: 'form',
				items: [{
					fieldDefaults: {
						labelAlign: 'right',
						labelSeparator: '',
						labelWidth: 90,
						msgTarget: 'side'
					},
					style: 'padding: 5px 50px;',
					xtype: 'fieldset',
					items: [{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1.4,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cboAcno
							]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cekAktif
							]
						}]
					},
						txtAcno
					]
				}],
				buttons: [{
					text: 'Save',
					handler: fnCekSave
				},/*{
					text: 'Print',
					handler: fnReset
				},*/{
					text: 'Reset',
					handler: fnReset
				}]
			},{
				bodyStyle: 'background-color: '.concat(gBasePanel),
				border: false,
				frame: false,
				title: 'Account List',
				xtype: 'form',
				items: [
					gridDetil
				]
			},{
				bodyStyle: 'background-color: '.concat(gBasePanel),
				border: false,
				frame: false,
				title: 'Group Account Entry',
				xtype: 'form',
				items: [{
					fieldDefaults: {
						labelAlign: 'right',
						labelSeparator: '',
						labelWidth: 110,
						msgTarget: 'side'
					},
					style: 'padding: 5px;',
					xtype: 'fieldset',
					items: [
						cboKelAcno,
						txtKelAcno,
					{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cboAcno2
							]
						},{
							flex: 1.3,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtAcno2
							]
						}]
					}]
				}],
				buttons: [{
					text: 'Save',
					handler: fnCekSave2
				},/*{
					text: 'Print',
					handler: fnReset
				},*/{
					text: 'Reset',
					handler: fnReset2
				}]
			},{
				bodyStyle: 'background-color: '.concat(gBasePanel),
				border: false,
				frame: false,
				title: 'Group Account List',
				xtype: 'form',
				items: [
					gridDetil2
				]
			}]
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmRekening
	});
	
	function fnMaskShow() {
		frmRekening.mask('Please wait...');
	}

	function fnMaskHide() {
		frmRekening.unmask();
	}
	
	frmRekening.render(Ext.getBody());
	Ext.get('loading').destroy();
});
