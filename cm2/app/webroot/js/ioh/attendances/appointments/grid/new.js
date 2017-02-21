/**
 * 
 */

IOH.Attendances.Appointments.Grid.New = Ext.extend(IOH.Attendances.Appointments.Grid,
{
	title: 'New',
	
	initComponent: function () {
		this.actionConfirm = new Ext.Action({
			text: 'Confirm (for tests only!)',
			handler: this.setState.createDelegate(this, ['confirmed']),
			scope: this,
			cls: 'x-btn-text-icon',
			iconCls: 'page_add',
			disabled1: true
		});
		
		var cfg = {
			store: IOH.APP.getEventStore('new'),
			tbar: ['->', this.actionConfirm]
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Attendances.Appointments.Grid.New.superclass.initComponent.apply(this, arguments);
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
		this.actionConfirm.setDisabled(sm.getSelections().length == 0);
	}
});