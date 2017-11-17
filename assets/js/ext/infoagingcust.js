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

	function gridTooltipProd(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Single click on the product item in the list to display the chassis <br/> <br/> Double click on the price, disc and lux tax to edit',
			target: view.el,
			trackMouse: true
		});
	}

	var txtTgl = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '95%',
		editable: true,
		fieldLabel: 'Sales Date',
		format: 'd-m-Y',
		id: 'txtTgl',
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
		name: 'txtTgl',
		value: new Date(),
		xtype: 'datefield'
	};

	var txtTgl2 = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		editable: true,
		fieldLabel: 'To',
		format: 'd-m-Y',
		id: 'txtTgl2',
		labelWidth: 20,
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -50),
		name: 'txtTgl2',
		value: new Date(),
		xtype: 'datefield'
	};

	var pbar = Ext.create('Ext.ProgressBar', {
		id: 'pbar',
		width: 600
	}).hide();

	function fnPB() {
		pbar.wait({
			duration: 120000,
			increment: 600,
			interval: 200,
			scope: this,
			fn: function() {
				pbar.hide();
				pbar.reset(true);
			}
		}).show();
	}

	function fnPrint() {
		fnPB();
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 120000,
			url: 'infoagingcust/excel_aging',
			params: {
				'fd_refno': Ext.Date.format(Ext.getCmp('txtTgl').getValue(), 'Ymd'),
				'fd_refno2': Ext.Date.format(Ext.getCmp('txtTgl2').getValue(), 'Ymd'),
				'fd_tgl': Ext.Date.format(Ext.getCmp('txtTgl').getValue(), 'd-m-Y'),
				'fd_tgl2': Ext.Date.format(Ext.getCmp('txtTgl2').getValue(), 'd-m-Y')
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
					
					vMask.show();
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
					pbar.hide();
					pbar.reset(true);
				}
				else {
					pbar.hide();
					pbar.reset(true);
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
					msg: 'Print Failed, Connection Failed!!',
					title: 'IDS'
				});
				fnMaskHide();
			}
		});
	}

	function fnReset() {
		Ext.getCmp('txtTgl').setValue(new Date());
		Ext.getCmp('txtTgl2').setValue(new Date());
	}

	var frmInfoAgingCust = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Aging Customer Report Form',
		width: 630,
		fieldDefaults: {
			labelAlign: 'right',
			labelSeparator: '',
			labelWidth: 70,
			msgTarget: 'side'
		},
		items: [{
			style: 'padding: 5px;',
			xtype: 'fieldset',
			items: [{
				anchor: '50%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1.5,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtTgl
					]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtTgl2
					]
				}]
			}]
		}],
		bbar: Ext.create('Ext.toolbar.Toolbar', {
			items: [pbar]
		}),
		buttons: [{
			text: 'Print',
			handler: fnPrint
		},{
			text: 'Reset',
			handler: fnReset
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmInfoAgingCust
	});
	
	function fnMaskShow() {
		frmInfoAgingCust.mask('Please wait...');
	}

	function fnMaskHide() {
		frmInfoAgingCust.unmask();
	}
	
	frmInfoAgingCust.render(Ext.getBody());
	Ext.get('loading').destroy();
});
