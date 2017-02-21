/**
 * 
 */
Ext.ns('IOH.Appointments');

IOH.Appointments.FollowupWindow = Ext.extend(Ext.Window,
{
	title: 'Create Followup Appointment',
	
	initComponent: function () {
		current_window = this;
		
		var cfg = {
			closeAction: 'hide',
			items: [new IOH.Appointments.Form({
				ref: 'form',
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
		
		IOH.Appointments.FollowupWindow.superclass.initComponent.apply(this, arguments);
	},
	
	onSave: function () {
		this.el.mask('Saving ...');
		
		var attendance_id = this.options, params = {};
		
		Ext.Ajax.request({
		   url: '/appointments/get_appointment_by_attendance_id/' + attendance_id,
		   success: function (result, request) {
			   var jsonData = Ext.util.JSON.decode(result.responseText);

			   if(jsonData.success && jsonData.data.Appointment.referral_id) {
				   params['Attendance.id'] = attendance_id;
				   params['Appointment.referral_id'] = jsonData.data.Appointment.referral_id;
				   params['Appointment.person_id'] = jsonData.data.Appointment.person_id;
				   params['Appointment.case_nature'] = jsonData.data.Referral.case_nature;
				   params['Appointment.referral_reason_id'] = jsonData.data.Referral.referral_reason_id;
				   params['Appointment.case_reference_number'] = jsonData.data.Referral.case_reference_number;
				   params['Appointment.referrer_type_id'] = jsonData.data.Appointment.referrer_type_id;
				   params['Appointment.referrer_name'] = jsonData.data.Appointment.referrer_name;
				   
				   this.submitForm(params);
			   } else {
				   if (!jsonData.success) {
					   Ext.Msg.alert('Error', 'Communication error');
				   } else {
					   Ext.Msg.alert('Error', 'Unable to add appointment - attendance does not have linked referral');
				   }
				   
				   this.el.unmask();
				   this.close();
			   }
		   },
		   
		   failure: function () {
			   this.el.unmask();
			   this.close();
			   IOH.APP.feedback("Appointment error", "There was a problem when contacting the server");
		   },
		   
		   scope: this
		});
	},
	
	submitForm: function(params) {
		this.form.getForm().submit({
			params: params,
			success: function (form) {
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
	   });
	}
});


IOH.Appointments.FollowupWindow.show = function (options, callback) {
	var w;
	
	if (!(w = Ext.WindowMgr.get('followup-appointments-window'))) {
		w = new IOH.Appointments.FollowupWindow({
			id: 'followup-appointments-window'
		});
	}
	
	w.options = options;
	w.callback = callback;
	w.show();
}