/**
 * 
 */

IOH.Attendances.Appointments.Grid.Scheduled = Ext.extend(IOH.Attendances.Appointments.Grid,
{
	title: 'Scheduled Appointments',
	
	border: false,
	
	initComponent: function () {
		this.createAttendanceAction = new Ext.Action({
			text: 'Book (Create Attendance)',
			handler: this.createAttendance,
			scope: this,
			cls: 'x-btn-text-icon',
			iconCls: 'page_add',
			disabled: true
		});
		
		fields = [{
			name: 'Appointment.id', type: 'int'
		},{
			name: 'Person.full_name'
		},{
			name: 'Person.id', type: 'int'
		},{
			name: 'Referral.case_nature'
		},{
			name: 'PatientStatus.status'
		},{
			name: 'ReferralReason.reason'
		},{
			name: 'Referral.referral_reason_id'
		},{
			name: 'Referral.case_reference_number'
		}];
		
		var cfg = {
			store: IOH.APP.getEventStore('scheduled'),
			tbar: [this.createAttendanceAction]
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Attendances.Appointments.Grid.Scheduled.superclass.initComponent.apply(this, arguments);
		this.store.load();
	},
	
	createAttendance: function () {
		var sel = this.getSelectionModel().getSelections();
		
		var ids = [];
		
		for (var i = 0; i < sel.length; i++) {
			ids.push(sel[i].id);
		}
		
		if (!ids.length) {
			return;
		}
		
		
		Server.Appointments.direct_make_attendance(ids, function (result) {
			if (result.success) {
				IOH.APP.feedback('Attendance Created', 'Attendance Record has been created.');
				this.store.reload();
			}
		}, this);
	},
	
	selectionChange: function (sm) {
		this.createAttendanceAction.setDisabled(sm.getSelections().length == 0);
	}
});