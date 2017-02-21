Ext.ns('IOH.Appointments.Referral');

IOH.Appointments.Referral.Window = Ext.extend(Ext.Window,
{
	title: 'Add Referral',
	modal: true,
	autoHeight: true,
	
	bodyStyle: 'padding: 10px;',
	
	initComponent: function () {
		var cfg = {
			closeAction: 'hide',
			layout: 'fit',
			width: 500,
			items: new IOH.Appointments.Referral.Form({
				ref: 'form',
				autoHeight: true,
				frame: false,
				unstyled: true
			}),
			buttons: [{
				text: 'Save',
				handler: function () {
					this.fireEvent('save');
				},
				scope: this
			},{
				text: 'Close',
				handler: function () {
					this.hide();
				},
				scope: this
			}]
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Appointments.Referral.Window.superclass.initComponent.apply(this, arguments);
		
		this.form.relayEvents(this, ['save']);
		this.relayEvents(this.form, ['saved']);
		
		this.on('saved', function (referralData) {
			this.hide();
			if (Ext.isFunction(this.options.callback)) {
				this.options.callback.call(this.options.scope || window, referralData)
			}
		}, this);
	}
});


IOH.Appointments.Referral.Window.show = function (options) {
	var w;
	
	if (!(w = Ext.WindowMgr.get('add-referral-window'))) {
		w = new IOH.Appointments.Referral.Window({
			id: 'add-referral-window'
		});
	}
	
	w.options = w.form.options = options;
	w.form.setPerson(options.Person);
	w.show();
}