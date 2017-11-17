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

	function buatForm() {
		var cboDeptLama = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '98%',
			emptyText: 'Select a',
			fieldLabel: 'Old Department',
			fieldStyle: 'background-color: #eee; background-image: none;',
			id: 'cboDeptLama',
			name: 'cboDeptLama',
			readOnly: true,
			xtype: 'textfield'
		};

		var txtDeptLama = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '100%',
			emptyText: 'Old Department',
			fieldStyle: 'background-color: #eee; background-image: none;',
			id: 'txtDeptLama',
			name: 'txtDeptLama',
			readOnly: true,
			xtype: 'textfield'
		};

		var grupDeptBaru = Ext.create('Ext.data.Store', {
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
						'fs_code': Ext.getCmp('cboDeptBaru').getValue(),
						'fs_nm_code': Ext.getCmp('cboDeptBaru').getValue(),
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
			store: grupDeptBaru,
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
						grupDeptBaru.load();
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
				store: grupDeptBaru,
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
					Ext.getCmp('cboDeptBaru').setValue(record.get('fs_code').concat(record.get('fs_count')));
					Ext.getCmp('txtDeptBaru').setValue(record.get('fs_nm_code'));
					Ext.getCmp('txtDeptCdBaru').setValue(record.get('fs_code'));
					Ext.getCmp('txtCountBaru').setValue(record.get('fs_count'));
					
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
					grupDeptBaru.load();
					vMask.show();
				}
			}
		});

		var cboDeptBaru = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '98%',
			emptyText: 'Select a',
			fieldLabel: 'New Department',
			id: 'cboDeptBaru',
			name: 'cboDeptBaru',
			xtype: 'textfield',
			listeners: {
				change: function(field, newValue) {
					Ext.getCmp('txtDeptBaru').setValue('');
					Ext.getCmp('txtDeptCdBaru').setValue('');
					Ext.getCmp('txtCountBaru').setValue('');
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

		var txtDeptBaru = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '100%',
			emptyText: 'New Department',
			fieldStyle: 'background-color: #eee; background-image: none;',
			id: 'txtDeptBaru',
			name: 'txtDeptBaru',
			readOnly: true,
			xtype: 'textfield'
		};

		var txtDeptCdBaru = {
			anchor: '100%',
			emptyText: '',
			fieldLabel: 'Dept Code',
			hidden: true,
			id: 'txtDeptCdBaru',
			name: 'txtDeptCdBaru',
			xtype: 'textfield'
		};

		var txtCountBaru = {
			anchor: '100%',
			emptyText: '',
			fieldLabel: 'Dept Count',
			hidden: true,
			id: 'txtCountBaru',
			name: 'txtCountBaru',
			xtype: 'textfield'
		};

		function fnCekSave() {
			if (this.up('form').getForm().isValid()) {
				Ext.Ajax.request({
					method: 'POST',
					url: 'gantidept/ceksave',
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
						vMask.hide();
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
				url: 'gantidept/save',
				params: {
					'fs_kd_dept': Ext.getCmp('txtDeptCdBaru').getValue(),
					'fs_count': Ext.getCmp('txtCountBaru').getValue(),
					'fs_nm_dept': Ext.getCmp('txtDeptBaru').getValue()
				},
				success: function(response) {
					var xtext = Ext.decode(response.responseText);
					
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS',
						fn: function(btn) {
							if (btn == 'ok') {
								window.parent.location.href = 'mainmenu'
							}
						}
					});
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
			Ext.getCmp('cboDeptBaru').setValue('');
			Ext.getCmp('txtDeptBaru').setValue('');
			Ext.getCmp('txtDeptCdBaru').setValue('');
			Ext.getCmp('txtCountBaru').setValue('');
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'alamat/nilaidefa',
				success: function(response) {
					var xtext = Ext.decode(response.responseText);
					
					if (xtext.sukses === true) {
						Ext.getCmp('cboDeptLama').setValue(xtext.kddept);
						Ext.getCmp('txtDeptLama').setValue(xtext.nmdept);
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

		var frmGantiDept = Ext.create('Ext.form.Panel', {
			border: false,
			frame: true,
			region: 'center',
			title: 'Change Login Department Form',
			width: 550,
			fieldDefaults: {
				labelAlign: 'right',
				labelSeparator: '',
				labelWidth: 105,
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
							cboDeptLama
						]
					},{
						flex: 1.3,
						layout: 'anchor',
						xtype: 'container',
						items: [
							txtDeptLama
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
							cboDeptBaru,
							txtDeptCdBaru,
							txtCountBaru
						]
					},{
						flex: 1.3,
						layout: 'anchor',
						xtype: 'container',
						items: [
							txtDeptBaru
						]
					}]
				}]
			}],
			buttons: [{
				text: 'Change',
				handler: fnCekSave
			},{
				text: 'Reset',
				handler: fnReset
			}]
		});

		var vMask = new Ext.LoadMask({
			msg: 'Please wait...',
			target: frmGantiDept
		});
		
		function fnMaskShow() {
			frmGantiDept.mask('Please wait...');
		}

		function fnMaskHide() {
			frmGantiDept.unmask();
		}
		
		frmGantiDept.render(Ext.getBody());
	}
	
	Ext.Ajax.request({
		method: 'POST',
		url: 'alamat/nilaidefa',
		success: function(response) {
			var xtext = Ext.decode(response.responseText);
			
			if (xtext.sukses === true) {
				buatForm();
				
				Ext.getCmp('cboDeptLama').setValue(xtext.kddept);
				Ext.getCmp('txtDeptLama').setValue(xtext.nmdept);
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
