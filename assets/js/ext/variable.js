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

	var grupKey = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_key','fs_nm_key'
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
			url: 'variable/kodekey'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_key': Ext.getCmp('cboKey').getValue(),
					'fs_nm_key': Ext.getCmp('cboKey').getValue(),
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
		store: grupKey,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Key Cd", dataIndex: 'fs_kd_key', menuDisabled: true, width: 100},
			{text: "Key", dataIndex: 'fs_nm_key', menuDisabled: true, width: 380}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Key Cd / Key',
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
					grupKey.load();
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
			store: grupKey,
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
				Ext.getCmp('cboKey').setValue(record.get('fs_kd_key'));
				Ext.getCmp('txtKey').setValue(record.get('fs_nm_key'));
				
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
				grupKey.load();
				vMask.show();
			}
		}
	});

	var cboKey = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		emptyText: 'Select a',
		fieldLabel: 'Key',
		id: 'cboKey',
		name: 'cboKey',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtKey').setValue('');
				Ext.getCmp('cboVar').setValue('');
				Ext.getCmp('txtVar').setValue('');
				Ext.getCmp('cboAcno').setValue('');
				Ext.getCmp('txtAcno').setValue('');
				Ext.getCmp('txtSubsidi').setValue(0);
				
				if (field.getValue() !== '' && field.getValue() === '17')
				{
					Ext.getCmp('cboAcno').setReadOnly(false);
					Ext.getCmp('cboAcno').setFieldStyle('background-color: white; background-image: url('.concat(gBaseIMG,'text-bg.gif);'));
				}
				else {
					Ext.getCmp('cboAcno').setValue('');
					Ext.getCmp('txtAcno').setValue('');
					Ext.getCmp('cboAcno').setReadOnly(true);
					Ext.getCmp('cboAcno').setFieldStyle('background-color: #eee; background-image: none;');
				}
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

	var txtKey = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Key',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtKey',
		name: 'txtKey',
		readOnly: true,
		xtype: 'textfield'
	};

	var grupVar = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_var','fs_nm_var',
			'fs_kd_acno','fs_nm_acno','fn_subsidi'
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
			url: 'variable/kodevar'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_key': Ext.getCmp('cboKey').getValue(),
					'fs_kd_var': Ext.getCmp('cboVar').getValue(),
					'fs_nm_var': Ext.getCmp('cboVar').getValue(),
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
		store: grupVar,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Variable Cd", dataIndex: 'fs_kd_var', menuDisabled: true, width: 100},
			{text: "Variable", dataIndex: 'fs_nm_var', menuDisabled: true, width: 380},
			{text: "Acno Cd", dataIndex: 'fs_kd_acno', menuDisabled: true, hidden: true},
			{text: "Acno", dataIndex: 'fs_nm_acno', menuDisabled: true, hidden: true},
			{text: "Subsidy", dataIndex: 'fn_subsidi', menuDisabled: true, hidden: true}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Variable Cd / Variable',
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
					grupVar.load();
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
			store: grupVar,
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
				Ext.getCmp('cboVar').setValue(record.get('fs_kd_var'));
				Ext.getCmp('txtVar').setValue(record.get('fs_nm_var'));
				Ext.getCmp('cboAcno').setValue(record.get('fs_kd_acno'));
				Ext.getCmp('txtAcno').setValue(record.get('fs_nm_acno'));
				Ext.getCmp('txtSubsidi').setValue(record.get('fn_subsidi'));
				
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
				grupVar.load();
				vMask.show();
			}
		}
	});

	var cboVar = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '50%',
		emptyText: 'Select / Enter a Code',
		fieldLabel: 'Code',
		id: 'cboVar',
		name: 'cboVar',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtVar').setValue('');
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

	var txtVar = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Enter a Name',
		fieldLabel: 'Name',
		id: 'txtVar',
		name: 'txtVar',
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
					'fs_kd_acno': Ext.getCmp('cboAcno').getValue(),
					'fs_nm_acno': Ext.getCmp('cboAcno').getValue(),
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
		store: grupAcno,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Acc. No", dataIndex: 'fs_kd_acno', menuDisabled: true, width: 100},
			{text: "Account", dataIndex: 'fs_nm_acno', menuDisabled: true, width: 380}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Acc. No / Account',
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
					winCari3.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboAcno').setValue(record.get('fs_kd_acno'));
				Ext.getCmp('txtAcno').setValue(record.get('fs_nm_acno'));
				
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
				grupAcno.load();
				vMask.show();
			}
		}
	});

	var cboAcno = {
		anchor: '98%',
		emptyText: 'Select an',
		fieldLabel: 'Acc. No',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'cboAcno',
		name: 'cboAcno',
		readOnly: true,
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
					winCari3.show();
					winCari3.center();
				}
			}
		}
	};

	var txtAcno = {
		anchor: '100%',
		emptyText: 'Account Number',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtAcno',
		name: 'txtAcno',
		readOnly: true,
		xtype: 'textfield'
	};

	var txtSubsidi = Ext.create('Ext.ux.form.NumericField', {
		afterLabelTextTpl: required,
		allowBlank: false,
		alwaysDisplayDecimals: true,
		anchor: '45%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		fieldLabel: 'Subsidy',
		hidden: true,
		hideTrigger: true,
		id: 'txtSubsidi',
		keyNavEnabled: false,
		mouseWheelEnabled: false,
		name: 'txtSubsidi',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0,
		listeners: {
			change: function(field, value) {
				if (Ext.isEmpty(field.getValue())) {
					field.setValue(0);
				}
			}
		}
	});

	function fnCekSave() {
		if (this.up('form').getForm().isValid()) {
			xKdKey = Ext.getCmp('cboKey').getValue('');
			xKdAcno = Ext.getCmp('cboAcno').getValue('');
			xNmAcno = Ext.getCmp('txtAcno').getValue('');
			
			if (xKdKey.trim() === '17') {
				if (xKdAcno.trim() === '' || xNmAcno.trim() === '') {
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						message: 'Account Number is empty, please fill in advance!!',
						title: 'IDS'
					});
					return;
				}
			}
			
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'variable/ceksave',
				params: {
					'fs_kd_key': Ext.getCmp('cboKey').getValue(),
					'fs_kd_var': Ext.getCmp('cboVar').getValue()
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
			url: 'variable/save',
			params: {
				'fs_kd_key': Ext.getCmp('cboKey').getValue(),
				'fs_kd_var': Ext.getCmp('cboVar').getValue(),
				'fs_nm_var': Ext.getCmp('txtVar').getValue(),
				'fs_kd_acno': Ext.getCmp('cboAcno').getValue(),
				'fn_subsidi': Ext.getCmp('txtSubsidi').getValue()
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

	function fnReset() {
		Ext.getCmp('cboKey').setValue('');
		Ext.getCmp('txtKey').setValue('');
		Ext.getCmp('cboVar').setValue('');
		Ext.getCmp('txtVar').setValue('');
		Ext.getCmp('cboAcno').setValue('');
		Ext.getCmp('txtAcno').setValue('');
		Ext.getCmp('txtSubsidi').setValue(0);
	}

	var frmVariable = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Variable Master Form',
		width: 550,
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
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						cboKey
					]
				},{
					flex: 1.7,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtKey
					]
				}]
			},
				cboVar,
				txtVar,
			{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						cboAcno
					]
				},{
					flex: 1.6,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtAcno
					]
				}]
			},
					txtSubsidi
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
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmVariable
	});
	
	function fnMaskShow() {
		frmVariable.mask('Please wait...');
	}

	function fnMaskHide() {
		frmVariable.unmask();
	}
	
	frmVariable.render(Ext.getBody());
	Ext.get('loading').destroy();
});
