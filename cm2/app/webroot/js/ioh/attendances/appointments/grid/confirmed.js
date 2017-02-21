/**
 * 
 */

IOH.Attendances.Appointments.Grid.Confirmed = Ext.extend(IOH.Attendances.Appointments.Grid,
{
	title: 'Confirmed',
	
	initComponent: function () {
		this.createAttendanceAction = new Ext.Action({
			text: 'Book (Create Attendance)',
			handler: this.createAttendance,
			scope: this,
			cls: 'x-btn-text-icon',
			iconCls: 'page_add',
			disabled: true
		});
		
		
		var cfg = {
			store: IOH.APP.getEventStore('confirmed'),
			tbar: [this.createAttendanceAction]
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Attendances.Appointments.Grid.Confirmed.superclass.initComponent.apply(this, arguments);
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