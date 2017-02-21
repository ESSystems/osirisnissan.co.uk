/**
 * 
 */

Ext.ns('IOH.Diary');

IOH.Diary.Window = Ext.extend(Ext.Window, 
{
	initComponent: function () {
		this.form = new IOH.Diary.Form({
			bodyStyle: 'padding: 10px'
		});
		
		var cfg = {
			title: 'Diary',
			closeAction: 'hide',
			layout: 'fit',
			width: 500,
			items: this.form,
			autoHeight: true,
			modal: true,
			buttons: [{
				text: 'Save',
				handler: function () {
					this.fireEvent('save');
				},
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
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Diary.Window.superclass.initComponent.apply(this, arguments);
		
		this.form.relayEvents(this, ['save']);
		this.relayEvents(this.form, ['saved']);
		
		this.on('saved', this.hide, this);
		this.on('activate', this._onActivate, this);
	},
	
	_onActivate: function () {
		if (this.calendarId) {
			this.form.load(this.calendarId);
		}
	}
});