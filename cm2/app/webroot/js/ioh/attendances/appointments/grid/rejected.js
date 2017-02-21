/**
 * 
 */

IOH.Attendances.Appointments.Grid.Rejected = Ext.extend(IOH.Attendances.Appointments.Grid,
{
	title: 'Reject',
	
	initComponent: function () {
		var cfg = {
			store: IOH.APP.getEventStore('rejected')
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Attendances.Appointments.Grid.Rejected.superclass.initComponent.apply(this, arguments);
	}
});