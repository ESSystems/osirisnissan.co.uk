/**
 * 
 */
Ext.ns('IOH.Declinations');

IOH.Declinations.Window = Ext.extend(Ext.Window,
{
	title: 'Decline Referral',
	
	initComponent: function () {
		var cfg = {
			closeAction: 'hide',
			items: [new IOH.Declinations.Form({
				ref: 'form'
			})],
			autoHeight: true,
			modal: true,
			width: 400,
			buttons: [{
				text: 'Save',
				handler: this.onSave,
				scope: this
			},{
				text: 'Cancel',
				handler: function () {
					this.hide();
				},
				scope: this
			}]
		};

		
		Ext.apply(this, cfg);
		
		IOH.Declinations.Window.superclass.initComponent.apply(this, arguments);
	},
	
	onSave: function () {
		this.el.mask('Saving ...');
		
		var rec = this.options, params = {};
		
		params['Declination.referral_id'] = rec.id;
		
		this.form.getForm().submit({
			params: params,
			success: function () {
				this.el.unmask();
				this.close();
				if (this.callback && Ext.isFunction(this.callback.success)) {
					this.callback.success.call(this.callback.scope || this)
				}
			},
			failure: function () {
				this.el.unmask();
			},
			scope: this
		})
	}
});


IOH.Declinations.Window.show = function (options, callback) {
	var w;
	
	if (!(w = Ext.WindowMgr.get('declinations-window'))) {
		w = new IOH.Declinations.Window({
			id: 'declinations-window'
		});
	}
	
	w.options = options;
	w.callback = callback;
	w.show();
}