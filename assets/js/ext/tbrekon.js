Ext.Loader.setConfig({
	enabled: true
});

Ext.Loader.setPath('Ext.ux', gBaseUX);

Ext.require([
	'Ext.ux.form.NumericField',
	'Ext.ux.LiveSearchGridPanel',
	'Ext.ux.ProgressBarPager'
]);

var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';

var kolomdata;
var fielddata;
var fieldvalue;
var data;
var IsiGrid = '';
			
Ext.onReady(function() {

	Ext.QuickTips.init();
	Ext.util.Format.thousandSeparator = ',';
	Ext.util.Format.decimalSeparator = '.';

	Ext.Ajax.request({
		url: 'tbrekon/data_kolom',
		timeout: 120000,
		success: function(response) {
			data = Ext.decode(response.responseText);
			createGrid(data.columndata,data.fielddata,data.values);
			kolomdata = data.columndata;
			fielddata = data.fielddata;
			fieldvalue = data.values;
		}
	});
	
	var createGrid = function (columndata,fielddata,values) {
		var xBulan = [
			{"fn_bulan":"00"},
			{"fn_bulan":"01"},
			{"fn_bulan":"02"},
			{"fn_bulan":"03"},
			{"fn_bulan":"04"},
			{"fn_bulan":"05"},
			{"fn_bulan":"06"},
			{"fn_bulan":"07"},
			{"fn_bulan":"08"},
			{"fn_bulan":"09"},
			{"fn_bulan":"10"},
			{"fn_bulan":"11"},
			{"fn_bulan":"12"}
		];
		
		var ListBulan = Ext.create('Ext.data.Store', {
			autoLoad: false,
			fields: ['fn_bulan'],
			data: xBulan
		});
		
		var ListTahun = Ext.create('Ext.data.Store', {
			autoLoad: false,
			fields: ['fn_tahun'],
			data: (function() {
				var data = [],
				date = new Date(),
				thisYear = date.getFullYear();
				for (var i = thisYear - 10; i <= thisYear; i++) {
					data.push({
						fn_tahun: i
					});
				}
				return data;
			})()
		});

		var cboBulanAwal = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '98%',
			displayField: 'fn_bulan',
			editable: false,
			emptyText: 'Month',
			fieldLabel: 'Periode',
			id: 'cboBulanAwal',
			name: 'cboBulanAwal',
			store: ListBulan,
			value: '00',
			valueField: 'fn_bulan',
			xtype: 'combobox'
		};
		
		var cboTahunAwal = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '95%',
			displayField: 'fn_tahun',
			editable: false,
			emptyText: 'Year',
			id: 'cboTahunAwal',
			name: 'cboTahunAwal',
			store: ListTahun,
			value: Ext.Date.format(new Date(), 'Y'),
			valueField: 'fn_tahun',
			xtype: 'combobox'
		};
		
		var cboBulanAkhir = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '97%',
			displayField: 'fn_bulan',
			editable: false,
			emptyText: 'Month',
			fieldLabel: ' To',
			id: 'cboBulanAkhir',
			labelWidth: 30,
			name: 'cboBulanAkhir',
			store: ListBulan,
			value: '00',
			valueField: 'fn_bulan',
			xtype: 'combobox'
		};
		
		var cboTahunAkhir = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '80%',
			displayField: 'fn_tahun',
			editable: false,
			emptyText: 'Year',
			id: 'cboTahunAkhir',
			name: 'cboTahunAkhir',
			store: ListTahun,
			value: Ext.Date.format(new Date(), 'Y'),
			valueField: 'fn_tahun',
			xtype: 'combobox'
		};
		
		var grupLevel = Ext.create('Ext.data.ArrayStore', {
			autoLoad: false,
			data: [['0', '0'], ['1', '1'], ['2', '2'], ['3', '3'], ['4', '4']],
			fields: ['fs_kd_level', 'fs_nm_level']
		});

		var cboLevel = {
			afterLabelTextTpl: required,
			allowBlank: false,
			anchor: '90%',
			displayField: 'fs_nm_level',
			editable: false,
			fieldLabel: 'Level',
			id: 'cboLevel',
			labelWidth: 35,
			name: 'cboLevel',
			value: '0',
			valueField: 'fs_kd_level',
			xtype: 'combobox',
			store: grupLevel
		};
		
		var IsiGrid = Ext.create('Ext.data.ArrayStore', {
			autoLoad: false,
			autodestroy: true,
			storeId : 'IsiGrid',
			fields: fielddata,
			data: values
		});
		
		var gridReg2 = Ext.create('Ext.grid.Panel', {
			anchor: '100%',
			id : 'GridRekon',
			store: IsiGrid,
			height: 400,
			defaultType: 'textfield',
			columns: columndata
		});
		
		var frmTBRekon = Ext.create('Ext.form.Panel', {
			border: false,
			frame: true,
			region: 'center',
			title: 'Trial Balance (Rekon)',
			width: 1050,
			fieldDefaults: {
				labelAlign: 'right',
				labelSeparator: '',
				labelWidth: 60,
				msgTarget: 'side'
			}, 
			items: [{
				style: 'padding: 5px;',
				title: 'Periode',
				xtype: 'fieldset',
				items: [{
					anchor: '70%',
					layout: 'hbox',
					xtype: 'container',
					items: [{
						flex: 0.82,
						layout: 'anchor',
						xtype: 'container',
						items: [
							cboBulanAwal
						]
					},{
						flex: 0.5,
						layout: 'anchor',
						xtype: 'container',
						items: [
							cboTahunAwal
						]
					},{
						flex: 0.6,
						layout: 'anchor',
						xtype: 'container',
						items: [
							cboBulanAkhir
						]
					},{
						flex: 0.6,
						layout: 'anchor',
						xtype: 'container',
						items: [
							cboTahunAkhir
						]
					},{
						flex: 0.7,
						layout: 'anchor',
						xtype: 'container',
						items: [
							cboLevel
						]
					},{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							anchor: '95%',
							handler: fnGenerate,
							scale: 'medium',
							text: 'Generate',
							xtype: 'button'
						}]
					},{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [{
							anchor: '95%',
							handler: fnExportToExcel,
							scale: 'medium',
							text: 'Convert to Excel',
							xtype: 'button'
						}]
					}]
				}]
			},
				gridReg2
			]
		});

		var vMask = new Ext.LoadMask({
			msg: 'Please wait...',
			target: frmTBRekon
		});
		
		function fnMaskShow() {
			frmTBRekon.mask('Please wait...');
		}

		function fnMaskHide() {
			frmTBRekon.unmask();
		}
		
		frmTBRekon.render(Ext.getBody());
		
		function fnGenerate() {
			var xthn = Ext.getCmp('cboTahunAwal').getValue();
			var xthn2 = Ext.getCmp('cboTahunAkhir').getValue();
			var xbln = Ext.getCmp('cboBulanAwal').getValue();
			var xbln2 = Ext.getCmp('cboBulanAkhir').getValue();
			xthn = xthn.toString();
			xthn2 = xthn2.toString();
			xbln = xbln.toString();
			xbln2 = xbln2.toString();
			var xperiode = xthn.concat(xbln);
			var xperiode2 = xthn2.concat(xbln2);
			
			if (Ext.getCmp('cboTahunAwal').getValue() !== null && Ext.getCmp('cboBulanAwal').getValue().trim() !== '' && 
				Ext.getCmp('cboTahunAkhir').getValue() !== null && Ext.getCmp('cboBulanAkhir').getValue().trim() !== '') {
				
				Ext.Ajax.on('beforerequest', fnMaskShow);
				Ext.Ajax.on('requestcomplete', fnMaskHide);
				Ext.Ajax.on('requestexception', fnMaskHide);
				
				Ext.Ajax.request({
				url: 'tbrekon/data',
				params: {
					'fd_periodeawal': xperiode,
					'fd_periodeakhir': xperiode2,
					'fs_level': Ext.getCmp('cboLevel').getRawValue()
				},
				timeout: 180000,
				success: function(response) {
						data = Ext.decode(response.responseText);
						createStore(data.columndata,data.fielddata,data.values);
					},
					failure: function(response) {
						var xtext = Ext.decode(response.responseText);
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.MessageBox.INFO,
							msg: 'Periode Failed, Connection Failed!!',
							title: 'IDS'
						});
						fnMaskHide();
					}
				});
				
				var createStore = function(columndata,fielddata,values) {
					IsiGrid2 = Ext.create('Ext.data.ArrayStore', {
						autoLoad: false,
						column: columndata,
						fields: fielddata,
						data: values
					});
					gridReg2.reconfigure(IsiGrid2, columndata);
				};
			} 
			else {
				Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: 'Bulan atau Tahun Masih Kosong!',
						title: 'IDS'
					});
			}
		}
		
		function fnExportToExcel() {
			var xthn = Ext.getCmp('cboTahunAwal').getValue();
			var xthn2 = Ext.getCmp('cboTahunAkhir').getValue();
			var xbln = Ext.getCmp('cboBulanAwal').getValue();
			var xbln2 = Ext.getCmp('cboBulanAkhir').getValue();
			xthn = xthn.toString();
			xthn2 = xthn2.toString();
			xbln = xbln.toString();
			xbln2 = xbln2.toString();
			var xperiode = xthn.concat(xbln);
			var xperiode2 = xthn2.concat(xbln2);
			
			if (Ext.getCmp('cboTahunAwal').getValue() !== null && Ext.getCmp('cboBulanAwal').getValue().trim() !== '' && 
				Ext.getCmp('cboTahunAkhir').getValue() !== null && Ext.getCmp('cboBulanAkhir').getValue().trim() !== '') {
				
				Ext.Ajax.on('beforerequest', fnMaskShow);
				Ext.Ajax.on('requestcomplete', fnMaskHide);
				Ext.Ajax.on('requestexception', fnMaskHide);
				
				Ext.Ajax.request({
					method: 'POST',
					timeout: 180000,
					url: 'tbrekon/convert_toexcel',
					params: {
						'fd_periodeawal': xperiode,
						'fd_periodeakhir': xperiode2,
						'fs_level': Ext.getCmp('cboLevel').getRawValue()
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
			else {
				Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: 'Bulan atau Tahun Masih Kosong!',
						title: 'IDS'
					});
			}
		}
	};
	Ext.get('loading').destroy();
});
