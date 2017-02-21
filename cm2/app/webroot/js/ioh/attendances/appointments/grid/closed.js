/**
 * 
 */

IOH.Attendances.Appointments.Grid.Closed = Ext.extend(IOH.Attendances.Appointments.Grid,
{
	title: 'Closed',
	
	initComponent: function () {
		var cfg = {
			store: IOH.APP.getEventStore('closed')
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Attendances.Appointments.Grid.Closed.superclass.initComponent.apply(this, arguments);
	}
});