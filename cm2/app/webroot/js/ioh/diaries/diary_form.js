/**
 * 
 */
Ext.ns('IOH.Diary');

IOH.Diary.Form = Ext.extend(Ext.form.FormPanel, 
{
	labelAlign: 'right',
	labelWidth: 80,
	border: false,
	autoHeight: true,
	autoScroll: true,
	modal: true,
	
	initComponent: function () {
		var cfg = {
			api: {
				load: Server.Diaries.direct_load,
				submit: Server.Diaries.direct_save
			},
			
			paramOrder: ['id'],
				
			items: [{
				name: 'Diary.id',
				xtype: 'hidden'
			},{
				name: 'Diary.color_id',
				xtype: 'hidden'
			},{
				name: 'Diary.name',
				xtype: 'textfield',
				fieldLabel: 'Name',
				anchor: '-20px'
			},{
				xtype: 'compositefield',
				defaults: {
					xtype: 'container',
					layout: 'form'
				},
				fieldLabel: 'Appt. Type',
				items: [{
					hiddenName: 'Diary.default_appointment_type',
					flex: 2,
					xtype: 'combo',
					mode: 'local',
					triggerAction: 'all',
					store: IOH.APP.attendanceReasonsStore,
					displayField: 'description',
					valueField: 'code',
					forceSelection: true,
					listeners: {
						beforerender: function (combo) {
							combo.store.load();
						}
					}
				},{
					labelWidth: 40,
					items: {
						xtype: 'numberfield',
						name: 'Diary.appointment_length',
						fieldLabel: 'Length',
						width: 50
					}
				}],
				anchor: '-20px'
			},{
				xtype: 'extensible.calendarcolorpalette',
				fieldLabel: 'Color',
				handler: function (p, c) {
					this.getForm().findField('Diary.color_id').setValue(c);
				},
				scope: this
			}]
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Diary.Form.superclass.initComponent.apply(this, arguments);
		
		this.on('save', this.onSave, this);
	},
	
	onSave: function () {
		this.getForm().submit({
			success: function (form, action) {
				this.fireEvent('saved');
				IOH.APP.feedback('Saved', 'Saved');
				IOH.APP.calendarStore.reload();
			},
			scope: this
		});
	},
	
	load: function (id) {
		if (!Ext.num(id)) {
			return;
		}
		
		this.getForm().load({
			params: {
				id: id
			},
			success: function (form, action) {
				this.findByType('extensible.calendarcolorpalette')[0].select(action.result.data['Diary.color_id'], true);
			},
			scope: this
		});
	}
});