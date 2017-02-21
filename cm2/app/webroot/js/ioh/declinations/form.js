/**
 * 
 */
Ext.ns('IOH.Declinations');

IOH.Declinations.Form = Ext.extend(Ext.form.FormPanel,
{
	initComponent: function () {
		var cfg = {
			api: {
				submit: Server.Declinations.direct_save
			},
			labelAlign: 'top',
			border: false,
			padding: 10,
			defaults: {
				anchor: '-20px'
			},
			items: [{
				xtype: 'textarea',
				name: 'Declination.reason',
				fieldLabel: 'Reason',
				height: 200
			}]
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Declinations.Form.superclass.initComponent.apply(this, arguments);
	}
});