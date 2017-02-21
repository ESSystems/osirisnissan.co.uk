/**
 * 
 */
Ext.ns('IOH.Appointments');

IOH.Appointments.Form = Ext.extend(Ext.form.FormPanel,
{
	initComponent: function () {
		var cfg = {
			api: {
				submit: Server.Referrals.direct_accept
			},
			labelAlign: 'right',
			labelWidth: 70,
			border: false,
			padding: 10,
			defaults: {
				anchor: '-20px'
			},
			items: [{
				xtype: 'hidden',
				name: 'Appointment.id'
			},{
				hiddenName: 'Appointment.type',
				fieldLabel: 'Type',
				xtype: 'combo',
				mode: 'local',
				triggerAction: 'all',
				store: IOH.APP.attendanceReasonsStore,
				displayField: 'description',
				valueField: 'code',
				forceSelection: true,
				allowBlank: false
			},/*{
				hiddenName: 'Appointment.diary_id',
				xtype: 'extensible.calendarcombo',
				store: IOH.APP.calendarStore,
				fieldLabel: 'Diary'
			},*/{
				xtype: 'compositefield',
				fieldLabel: 'App. Length',
				items: [{
					name: 'Appointment.length',
					xtype: 'numberfield',
					flex: 1
				},{
					xtype: 'container',
					autoEl: 'span',
					html: 'min.'
				}]
			}/*,{
				xtype: 'compositefield',
				fieldLabel: 'When',
				items: [{
					name: 'Appointment.from_date',
					xtype: 'xdatetime',
					flex: 1
				},{
					xtype: 'button',
					text: 'Get Slot',
					disabled: true
				}]
			}*/]
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Appointments.Form.superclass.initComponent.apply(this, arguments);
	}
});