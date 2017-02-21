/**
 * 
 */
Ext.ns('IOH.Appointments');

IOH.Appointments.Window = Ext.extend(Ext.Window,
{
	title: 'Create Appointment',
	
	initComponent: function () {
		var cfg = {
			closeAction: 'hide',
			items: [new IOH.Appointments.Form({
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
		
		IOH.Appointments.Window.superclass.initComponent.apply(this, arguments);
	},
	
	onSave: function () {
		this.el.mask('Saving ...');
		
		var rec = this.options, params = {};
		
		params['Appointment.referral_id'] = rec.id;
		params['Appointment.person_id'] = rec.get('Person.id');
		params['Appointment.case_nature'] = rec.get('Referral.case_nature');
		params['Appointment.referral_reason_id'] = rec.get('Referral.referral_reason_id');
		params['Appointment.case_reference_number'] = rec.get('Referral.case_reference_number');
		
		this.form.getForm().submit({
			params: params,
			success: function (form) {
				// Turn on the just selected calendar (diary) only.
				var selectedCalType = form.findField('Appointment.type').getValue();
				var CM = Ext.ensible.cal.CalendarMappings;
				
				// Show only diaries matching selected appointment type
				IOH.APP.regularDiariesStore.filter(CM.DefaultType.name, selectedCalType, false, true, true);

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


IOH.Appointments.Window.show = function (options, callback) {
	var w;
	
	if (!(w = Ext.WindowMgr.get('appointments-window'))) {
		w = new IOH.Appointments.Window({
			id: 'appointments-window'
		});
	}
	
	w.options = options;
	w.callback = callback;
	w.show();
}