/**
 * 
 */

Ext.ns('IOH.Attendances');

IOH.Attendances.Appointments = Ext.extend(Ext.Panel,
{
	initComponent: function () {
		var cfg = {
			//title: 'Appointments',
			layout: 'fit',
			items: [
				new IOH.Attendances.Appointments.Grid.Scheduled()
			]
		};
		
		Ext.apply(this, cfg);
		
		IOH.Attendances.Appointments.superclass.initComponent.apply(this, arguments);
	}
});

Ext.reg('IOH.Attendances.Appointments', IOH.Attendances.Appointments);