/**
 * 
 */
Ext.ns('IOH.AttendanceFeedback');

IOH.AttendanceFeedback.Form = Ext.extend(Ext.form.FormPanel,
{
	initComponent: function () {
		var cfg = {
			title: 'Report',
			labelAlign: 'top',
			border: false,
			padding: 30,
			defaults: {
				anchor: '-10px'
			},
			items: [{
				xtype: 'textarea',
				name: 'AttendanceFeedback.report',
				fieldLabel: 'Report',
				showLabel: false,
				height: 170
			}]
		};
		
		Ext.apply(this, cfg);
		//Ext.apply(this.initialConfig, cfg);
		
		IOH.AttendanceFeedback.Form.superclass.initComponent.apply(this, arguments);
	},
	
	loadAttendanceFeedback: function(attendanceId) {
		
		this.getForm().reset();
		this.load({
			url: '/attendance_feedback/load/' + attendanceId + '.json',
			waitMsg: 'Loading attendance feedback ...'
		});
	}
});