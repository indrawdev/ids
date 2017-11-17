Ext.Loader.setConfig({
	enabled: true
});

Ext.Loader.setPath('Ext.ux', gBaseUX);

Ext.require([
	'Ext.ux.form.NumericField',
	'Ext.ux.LiveSearchGridPanel',
	'Ext.ux.ProgressBarPager',
]);

Ext.onReady(function() {
	Ext.QuickTips.init();

	var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';
	
	function trim(text) {
		return text.replace(/^\s+|\s+$/gm, '');
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

		var grupID = Ext.create('Ext.data.Store', {
			autoLoad: false,
			fields: [
				'fs_kd_crud', 'fs_fname', 'fs_lname', 'fs_address',
				'fn_city_id', 'fb_active'
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
				url: 'crud/kodecrud',
			},
			listeners: {
				beforeload: function(store) {
					Ext.apply(store.getProxy().extraParams, {
						'fs_kd_crud': Ext.getCmp('cboCrud').getValue()
					});
				},

			}
		});

		var winGrid = Ext.create('Ext.ux.LiveSearchGridPanel', {
			autoDestroy: true,
			height: 450,
			width: 550,
			sortableColumns: false,
			store: grupID,
			bbar: Ext.create('Ext.PagingToolbar', {
				displayInfo: true,
				pageSize: 25,
				plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
				store: grupID,
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
				{text: "Crud Kode", dataIndex: 'fs_kd_crud', menuDisabled: true, width: 100},
				{text: "First Name", dataIndex: 'fs_fname', menuDisabled: true, width: 200},
				{text: "Last Name", dataIndex: 'fs_lname', menuDisabled: true, width: 200}
			],
			listeners: {
				itemdblclick: function(grid, record)
				{
					Ext.getCmp('cboCrud').setValue(record.get('fs_kd_crud'));
					Ext.getCmp('txtFname').setValue(record.get('fs_fname'));
					Ext.getCmp('txtLname').setValue(record.get('fs_lname'));
					Ext.getCmp('txtAddress').setValue(record.get('fs_address')); 
					Ext.getCmp('cekAktif').setValue(record.get('fb_active'));
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
					grupID.load();
					vMask.show();
				}	
			}
		});

		var cboCrud = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '95%',
			emptyText: 'Select / Enter a CRUD Code',
			fieldLabel: 'Code',
			id: 'cboCrud',
			name: 'cboCrud',
			xtype: 'textfield',
			listeners: {
				change: function(field, newValue) {
					//Ext.getCmp('txtCrud').setValue('');
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
	    
	    var txtCrud = {
	    	allowBlank: true,
	    	emptyText: '',
	    	fieldLabel: '',
	    	xtype: 'textfield',
	    	hidden: true,
	    	id: 'txtCrud',
	    	name: 'txtCrud',
	    };

		var cekAktif = {
			boxLabel: 'Active',
			checked: false,
			id: 'cekAktif',
			name: 'cekAktif',
			xtype: 'checkboxfield'
		};

		var txtFname = {
			afterLabelTextTpl: required,
			fieldLabel: 'First Name',
			anchor: '80%',
	        name: 'txtFname',
	        id: 'txtFname',
	        xtype: 'textfield',
	        emptyText : 'Input Nama Depan',
	        allowBlank: false
	    };

	    var txtLname = {
	    	afterLabelTextTpl: required,
	    	fieldLabel: 'Last Name',
	    	anchor: '80%',
	        name: 'txtLname',
	        id: 'txtLname',
	        xtype: 'textfield',
	        emptyText : 'Input Nama Akhir',
	        allowBlank: false
	    };

	    var dataJekel = Ext.create('Ext.data.Store', {
	    	fields: ['id', 'name'],
	    	data: [
	    		{"id":"1", "name":"Laki-Laki"},
	    		{"id":"2", "name":"Perempuan"}
	    	]
	    });
	    
	    var grupCity = Ext.create('Ext.data.Store', {
	    	autoLoad: false,
	    	fields : ['fn_city_id', 'fs_city_name'],
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
	    		url: 'crud/ambil_kota'
	    	}
	    });

	    var txtDob = {
	    	afterLabelTextTpl: required,
	    	xtype: 'datefield',
	    	anchor: '100%',
	    	fieldLabel: 'Tanggal lahir',
	    	id: 'txtDob',
	    	name: 'txtDob',
	    	format: 'd-m-Y',
	    	value: '02-04-1978'
	    };

	    var comGender = Ext.create('Ext.form.ComboBox', {
	    	afterLabelTextTpl: required,
	    	fieldLabel: 'Kelamin',
	    	anchor: '80%',
	    	store: dataJekel,
	    	queryMode: 'local',
	    	displayField: 'name',
	    	id: 'comGender',
	    	name: 'comGender',
	    	valueField: 'id',
	    	emptyText : 'Pilih Jenis Kelamin',
	    });

	    var winGrid2 = Ext.create('Ext.ux.LiveSearchGridPanel', {
	    	autoDestroy: true,
	    	height: 450,
	    	width: 550,
	    	sortableColumns: true,
	    	store: grupCity,
	    	bbar: Ext.create('Ext.PagingToolbar', {
	    		displayInfo: true,
	    		pageSize: 25,
	    		plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
	    		store: grupCity,
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
	    		{text: "Nomor", dataIndex: 'fn_city_id', menuDisabled: true, width: 100, hidden: true},
	    		{text: "Nama Kota", dataIndex: 'fs_city_name', menuDisabled: true, width: 330},
	    	],
	    	listeners: {
	    		itemdblclick: function(grid, record)
	    		{
	    			Ext.getCmp('cboCity').setValue(record.get('fs_city_name'));
	    			Ext.getCmp('txtCity').setValue(record.get('fn_city_id'));
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
	    			grupCity.load();
	    			vMask.show();
	    		}
	    	}
	    });

	    var cboCity = {
	    	afterLabelTextTpl: required,
	    	allowBlank: false,
	    	anchor: '80%',
	    	xtype: 'textfield',
	    	displayField: 'fn_city_id',
	    	editable: false,
	    	emptyText : 'Pilih Kota',
	    	fieldLabel: 'Kota',
	    	id: 'cboCity',
	    	name: 'cboCity',
	    	queryMode: 'remote',
	    	triggerAction: 'all',
	    	store: grupCity,
	    	valueField: 'fn_city_id',
	    	listeners: {
	    		change: function(field, newValue) {
	    			Ext.getCmp('txtCity').setValue('');
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

	    	},
	    };

	    var txtCity = {
	    	allowBlank: true,
	    	emptyText: 'Nama Kota',
	    	fieldLabel: 'Kota',
	    	xtype: 'textfield',
	    	hidden: true,
	    	id: 'txtCity',
	    	name: 'txtCity',
	    };

	    var txtAddress = {
	    	afterLabelTextTpl: required,
	    	fieldLabel: 'Address',
	    	xtype: 'textareafield',
	    	anchor: '90%',
	    	grow: true,
	    	name: 'txtAddress',
	    	id: 'txtAddress',
	    	emptyText : 'Input Alamat Lengkap',
	    	allowBlank: false
	    };


		Ext.define('DataGridDetil', {
			extend: 'Ext.data.Model',
			fields: [
				{name: 'fs_kd_product', type: 'string'},
				{name: 'fs_fname', type: 'string'},
				{name: 'fs_lname', type: 'string'},
				{name: 'fb_jekel', type: 'string'},
				{name: 'fn_city_id', type: 'string'},
				{name: 'fs_address', type: 'string'},
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
				url: 'crud/griddetil'
			},
		});
	    
		var gridDetil = Ext.create('Ext.grid.Panel', {
			anchor: '100%',
			autoDestroy: true,
			height: 450,
			sortableColumns: false,
			store: grupGridDetil,
			columns: [{
				xtype: 'rownumberer',
				width: 35
			},{
				dataIndex: 'fs_kd_crud',
				menuDisabled: true,
				text: 'Code',
				width: 50

			},{
				dataIndex: 'fs_fname',
				menuDisabled: true,
				text: 'First Name',
				width: 100
			},{
				dataIndex: 'fs_lname',
				menuDisabled: true,
				text: 'Last Name',
				width: 100
			},{
				dataIndex: 'fs_city_name',
				menuDisabled: true,
				text: 'Kota',
				width: 80
			},{		
				dataIndex: 'fs_address',
				menuDisabled: true,
				text: 'Alamat',
				width: 200
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

		function fnCekSave() {
			if (this.up('form').getForm().isValid()) {
				Ext.Ajax.request({
					method: 'POST',
					url: 'crud/ceksave',
					params: {
						'fs_kd_crud': Ext.getCmp('cboCrud').getValue()
					},
					success: function(response, opts) {
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
					failure: function(response, opts) {
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
			Ext.Ajax.on('beforerequest', vMask.show, vMask);
			Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
			Ext.Ajax.on('requestexception', vMask.hide, vMask);
			Ext.Ajax.request({
				method: 'POST',
				url: 'crud/save',
				params: {
					'fs_kd_crud': Ext.getCmp('cboCrud').getValue(),
					'fs_fname': Ext.getCmp('txtFname').getValue(),
					'fs_lname': Ext.getCmp('txtLname').getValue(),
					'fn_city_id': Ext.getCmp('txtCity').getValue(),
					'fs_address': Ext.getCmp('txtAddress').getValue(),
					'fb_jekel': Ext.getCmp('comGender').getValue(),
					'fb_active': Ext.getCmp('cekAktif').getValue(),
				},
				success: function(response, opts) {
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
				failure: function(response, opts) {
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
		
		function fnReset() {
			Ext.getCmp('cboCrud').setValue('');
			Ext.getCmp('txtFname').setValue('');
			Ext.getCmp('txtLname').setValue('');
			Ext.getCmp('cboCity').setValue('');
			Ext.getCmp('txtCity').setValue('');
			Ext.getCmp('comGender').setValue('');
			Ext.getCmp('cekAktif').setValue(true);
			Ext.getCmp('txtAddress').setValue('');
			grupGridDetil.load();
		}

		var frmCrud = Ext.create('Ext.form.Panel', {
			border: false,
			frame: true,
			title: 'Unit Master CRUD Form',
			region: 'center',
			width: 800,
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
					title: 'CRUD Entry',
					xtype: 'form',
					items: [{
						fieldDefaults: {
							labelAlign: 'right',
							labelSeparator: '',
							labelWidth: 80,
							msgTarget: 'side'
						},
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
								items: [
									cboCrud,
									txtFname,
									txtLname,
									cboCity,
									txtCity,
									comGender
								]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [						
									cekAktif,
									txtAddress
								]
							}]
						}],
					}],
					buttons: [
							{
								text: 'Submit',
								handler: fnCekSave
							},{
								text: 'Reset',
								handler: fnReset
					}]
				},{
					bodyStyle: 'background-color: '.concat(gBasePanel),
					border: false,
					frame: false,
					title: 'CRUD List',
					xtype: 'form',
					items: [
						gridDetil
					]
				}]
			}]
		});

		var vMask = new Ext.LoadMask({
			msg: 'Silakan tunggu...',
			target: frmCrud
		});

		frmCrud.render(Ext.getBody());
	}

	buatForm();

	Ext.get('loading').destroy();
});