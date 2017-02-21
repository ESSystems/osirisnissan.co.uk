/**
 * 
 */

IOH.Attendances.Appointments.Grid.Booked = Ext.extend(IOH.Attendances.Appointments.Grid,
{
	title: 'Booked',
	
	initComponent: function () {
		var cfg = {
			store: IOH.APP.getEventStore('booked'),
			tbar1: [this.createAttendanceAction]
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Attendances.Appointments.Grid.Booked.superclass.initComponent.apply(this, arguments);
	}
});