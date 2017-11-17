Ext.Loader.setConfig({
	enabled: true
});

Ext.Loader.setPath('Ext.ux', gBaseUX);

Ext.require([
	'Ext.ux.LiveSearchGridPanel',
	'Ext.ux.ProgressBarPager'
]);

Ext.onReady(function() {
    Ext.QuickTips.init();

	var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';

	function gridTooltipSearch(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Double click on record to choose',
			target: view.el,
			trackMouse: true
		});
	}

	var grupComp = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_kd_comp','fs_nm_comp','fs_nm_db'],
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
			url: 'login/ambil_comp'
		}
	});

	var winGrid = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupComp,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupComp,
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
			{text: "Comp Code", dataIndex: 'fs_kd_comp', menuDisabled: true, hidden: true},
			{text: "Company Name", dataIndex: 'fs_nm_comp', menuDisabled: true, width: 330},
			{text: "DB Name", dataIndex: 'fs_nm_db', menuDisabled: true, width: 150}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboComp').setValue(record.get('fs_nm_comp'));
				Ext.getCmp('txtComp').setValue(record.get('fs_kd_comp'));
				Ext.getCmp('txtDB').setValue(record.get('fs_nm_db'));
				
				grupDept.load();
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
				grupComp.load();
				vMask.show();
			}
		}
	});

	var cboComp = {
		afterLabelTextTpl: required,
		allowBlank: false,
		editable: false,
		emptyText : 'Select a Company',
		fieldLabel: 'Company',
		id: 'cboComp',
		name: 'cboComp',
		width: 370,
		xtype: 'textfield',
		listeners: {
			change: function(combo, value) {
				Ext.getCmp('txtComp').setValue('');
				Ext.getCmp('txtDB').setValue('');
				Ext.getCmp('cboDept').setValue('');
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
	
	var txtComp = {
		emptyText: 'Comp Code',
		fieldLabel: 'Comp Code',
		hidden: true,
		id: 'txtComp',
		name: 'txtComp',
		width: 270,
		xtype: 'textfield'
	};
	
	var txtDB = {
		emptyText: 'DB Name',
		fieldLabel: 'DB Name',
		hidden: true,
		id: 'txtDB',
		name: 'txtDB',
		width: 270,
		xtype: 'textfield'
	};
	
	var grupDept = Ext.create('Ext.data.Store', {
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
			url: 'login/change_comp'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_nm_db': Ext.getCmp('txtDB').getValue(),
					'fs_kd_comp': Ext.getCmp('txtComp').getValue()
				});
			}
		}
	});
	
	var winGrid2 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
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
					winCari2.hide();
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
				Ext.getCmp('cboDept').setValue(record.get('fs_nm_code'));
				Ext.getCmp('txtDept').setValue(record.get('fs_code'));
				Ext.getCmp('txtCount').setValue(record.get('fs_count'));
				
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
				grupDept.load();
				vMask.show();
			}
		}
	});
	
	var cboDept = {
		afterLabelTextTpl: required,
		allowBlank: false,
		displayField: 'fs_nm_code',
		editable: false,
		emptyText: 'Select a Department',
		fieldLabel: 'Department',
		id: 'cboDept',
		name: 'cboDept',
		valueField: 'fs_code',
		width: 370,
		listeners: {
			change: function(combo, value) {
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
					winCari2.show();
					winCari2.center();
				}
			}
		}
	};
	
	var txtDept = {
		allowBlank: true,
		emptyText: 'Dept Code',
		fieldLabel: 'Dept Code',
		hidden: true,
		id: 'txtDept',
		name: 'txtDept',
		width: 270
	};
	
	var txtCount = {
		allowBlank: true,
		emptyText: 'Dept Count',
		fieldLabel: 'Dept Count',
		hidden: true,
		id: 'txtCount',
		name: 'txtCount',
		width: 270
	};
	
	var txtUserName = {
		afterLabelTextTpl: required,
		allowBlank: false,
		emptyText: 'User Code',
		fieldLabel: 'User Code',
		id: 'txtUserName',
		name: 'txtUserName',
		value: '',
		width: 270
	};
	
	var txtUserPass = {
		afterLabelTextTpl: required,
		allowBlank: false,
		emptyText: 'Password',
		fieldLabel: 'Password',
		id: 'txtUserPass',
		inputType: 'password',
		name: 'txtUserPass',
		value: '',
		width: 270
	};
	
	var txtCaptcha = {
		afterLabelTextTpl: required,
		allowBlank: false,
		fieldLabel: 'Type Captcha',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtCaptcha',
		labelWidth: 90,
		name: 'txtCaptcha',
		width: 270,
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};
	
	Ext.define('Image', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'src', type: 'string'}
		]
	});

	var dataImg = Ext.create('Ext.data.Store', {
		autoLoad: true,
		id: 'imagesStore',
		model: 'Image',
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'login/buat_captcha'
		}
	});

	var imageTpl = new Ext.XTemplate(
		'<tpl for=".">',
			'<div style="margin-bottom: 10px;" class="thumb-wrap">',
				'<img src="{src}" />',
				'<br/><span>{caption}</span>',
			'</div>',
		'</tpl>'
	);
	
	var gambar = Ext.create('Ext.view.View', {
		itemSelector: 'div.thumb-wrap',
		store: dataImg,
		tpl: imageTpl
	});

	var cmdRefresh = {
		iconCls: 'icon-refresh',
		id: 'cmdRefresh',
		name: 'cmdRefresh',
		text: 'Refresh Captcha',
		xtype: 'button',
		handler: function() {
			dataImg.load();
		}
	};
	
	function fnReset() {
		Ext.getCmp('txtUserName').setValue('');
		Ext.getCmp('txtUserPass').setValue('');
	}

	function fnLogin() {
		var xForm = Ext.getCmp('frmLogin').getForm();
		if (xForm.isValid()) {
			xForm.submit({
			waitTitle: 'Connecting',
			waitMsg: 'Validate User and Password...',
			success: function() {
					window.location.href = 'mainmenu';
				},
			failure: function(form, action) {
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						// icon: Ext.Msg.QUESTION,
						// icon: Ext.Msg.ERROR,
						// icon: Ext.Msg.WARNING,
						msg: 'Login Failed, ' + action.response.responseText,
						title: 'IDS'
					});
					winLogin.unmask();
				}
			});
		}
	}

	var winLogin = Ext.create('Ext.window.Window', {
        closable: false,
        draggable: true,
        height: 320,
		id: 'winLogin',
        layout: 'border',
		name: 'winLogin',
        plain: true,
        resizable: false,
        title: 'IDS Login Form',
        width: 450,
        items: [
			Ext.create('Ext.form.Panel', {
				bodyStyle: 'padding:15px 35px;',
				border: false,
				defaultType: 'textfield',
				fieldDefaults: {
					msgTarget: 'side',
					labelAlign: 'right',
					labelSeparator: '',
					labelWidth: 90
				},
				frame: false,
				id: 'frmLogin',
				method: 'POST',
				region: 'center',
				url: 'login/ceklogin',
				items:[
					cboComp,
					txtComp,
					txtDB,
					cboDept,
					txtDept,
					txtCount,
					txtUserName,
					txtUserPass,
					txtCaptcha,
					gambar,
					cmdRefresh
				],
				buttons: [{
					text: 'Login',
					handler: fnLogin
				},{
					text: 'Reset',
					handler: fnReset
				}]
			})
		]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: winLogin
	});
	
	function fnMaskShow() {
		winLogin.mask('Please wait...');
	}

	function fnMaskHide() {
		winLogin.unmask();
	}
	
	winLogin.show();
	Ext.get('loading').destroy();
});
