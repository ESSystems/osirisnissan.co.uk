/**
 * 
 */
Ext.ns('IOH.Diary.Restrictions');

IOH.Diary.Restrictions.Form = Ext.extend(Ext.form.FormPanel, 
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
				load: Server.DiaryRestrictions.direct_load,
				submit: Server.DiaryRestrictions.direct_save
			},
			
			paramOrder: ['id'],
				
			items: [{
				name: 'DiaryRestriction.id',
				xtype: 'hidden'
			},{
				name: 'DiaryRestriction.diary_id',
				xtype: 'hidden'
			},{
				xtype: 'container',
				layout: 'form',
				labelAlign1: 'top',
				items: {
					xtype: 'textfield',
					name: 'DiaryRestriction.title',
					fieldLabel: 'Title',
					anchor: '-20px'
				}
			},{
				xtype: 'radiogroup',
				items: [{
					xtype: 'radio',
					name: 'DiaryRestriction.type',
					inputValue: '1',
					checked: true,
					boxLabel: 'Patient Time'
				},{
					xtype: 'radio',
					name: 'DiaryRestriction.type',
					inputValue: '0',
					boxLabel: 'Non-Patient Time'
				}]
			},{
				xtype: 'compositefield',
				fieldLabel: 'Date From / To',
				items: [{
					xtype: 'xdatefield',
					name: 'DiaryRestriction.from_date',
					width: 100
				},{
					xtype: 'xdatefield',
					name: 'DiaryRestriction.to_date',
					width: 100
				},{
					xtype: 'checkbox',
					boxLabel: 'Any&nbsp;Date',
					flex: 1
				}],
				anchor: '-20px'
					
			},{
				xtype: 'compositefield',
				fieldLabel: 'Time From / To',
				items: [{
					xtype: 'timefield',
					name: 'DiaryRestriction.from_time',
					width: 100
				},{
					xtype: 'timefield',
					name: 'DiaryRestriction.to_time',
					width: 100
				},{
					xtype: 'checkbox',
					boxLabel: 'Any&nbsp;Time',
					flex: 1
				}],
				anchor: '-20px'
			},{
				xtype: 'checkboxgroup',
				fieldLabel: 'Week Days',
				columns: 4,
				items: [{
					name: 'DiaryRestriction.week_day[0]',
					boxLabel: Date.dayNames[1]
				},{
					name: 'DiaryRestriction.week_day[1]',
					boxLabel: Date.dayNames[2]
				},{
					name: 'DiaryRestriction.week_day[2]',
					boxLabel: Date.dayNames[3]
				},{
					name: 'DiaryRestriction.week_day[3]',
					boxLabel: Date.dayNames[4]
				},{
					name: 'DiaryRestriction.week_day[4]',
					boxLabel: Date.dayNames[5]
				},{
					name: 'DiaryRestriction.week_day[5]',
					boxLabel: Date.dayNames[6]
				},{
					name: 'DiaryRestriction.week_day[6]',
					boxLabel: Date.dayNames[0]
				}]
			},{
				xtype: 'textfield',
				name: 'DiaryRestriction.month_day_str',
				fieldLabel: 'Month Days',
				anchor: '-20px'
			},{
				xtype: 'checkboxgroup',
				fieldLabel: 'Months',
				columns: 4,
				items: [{
					name: 'DiaryRestriction.month[0]',
					boxLabel: Date.monthNames[0]
				},{
					name: 'DiaryRestriction.month[1]',
					boxLabel: Date.monthNames[1]
				},{
					name: 'DiaryRestriction.month[2]',
					boxLabel: Date.monthNames[2]
				},{
					name: 'DiaryRestriction.month[3]',
					boxLabel: Date.monthNames[3]
				},{
					name: 'DiaryRestriction.month[4]',
					boxLabel: Date.monthNames[4]
				},{
					name: 'DiaryRestriction.month[5]',
					boxLabel: Date.monthNames[5]
				},{
					name: 'DiaryRestriction.month[6]',
					boxLabel: Date.monthNames[6]
				},{
					name: 'DiaryRestriction.month[7]',
					boxLabel: Date.monthNames[7]
				},{
					name: 'DiaryRestriction.month[8]',
					boxLabel: Date.monthNames[8]
				},{
					name: 'DiaryRestriction.month[9]',
					boxLabel: Date.monthNames[9]
				},{
					name: 'DiaryRestriction.month[10]',
					boxLabel: Date.monthNames[10]
				},{
					name: 'DiaryRestriction.month[11]',
					boxLabel: Date.monthNames[11]
				}]
			}]
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Diary.Restrictions.Form.superclass.initComponent.apply(this, arguments);
		
		this.on('show', function () {
			if (this.options.calendarId) {
				this.getForm().findField('DiaryRestriction.diary_id').setValue(this.options.calendarId);
			}
			if (this.options.id) {
				this.load(this.options.id);
			}
		}, this);
		
		this.on('save', this.onSave, this);
		
		this.getForm().on('beforesetvalues', function (values) {
			var m;
			Ext.iterate(values, function (name, value, all) {
				if (m = name.match(/(DiaryRestriction\.(week_day|month))\.(\d+)/)) {
					values[m[1] + '[' + m[3] + ']'] = value;
				}
			});
		}, this);
	},
	
	onSave: function () {
		this.getForm().submit({
			success: function (form, action) {
				this.fireEvent('saved');
				IOH.APP.feedback('Saved', 'Saved');
			},
			scope: this
		});
	},
	
	load: function (id) {
		this.getForm().reset();
		this.getForm().load({
			params: {
				id: id
			},
			scope: this
		});
	}
});

IOH.Diary.Restrictions.WindowForm = Ext.extend(Ext.Window,
{
	initComponent: function () {
		var cfg = {
			title: 'Diary Restrictions',
			closeAction: 'hide',
			layout: 'fit',
			width: 500,
			items: new IOH.Diary.Restrictions.Form({
				ref: 'form',
				bodyStyle: 'padding: 10px'
			}),
			autoHeight: true,
			modal: true,
			buttons: [{
				text: 'Save',
				handler: function () {
					this.fireEvent('save');
				},
				scope: this
			},{
				text: 'Close',
				handler: function () {
					this.hide();
				},
				scope: this
			}]
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Diary.Restrictions.WindowForm.superclass.initComponent.apply(this, arguments);
		
		this.on('show', function () {
			var crec = IOH.APP.calendarStore.getById(this.options.calendarId);
			this.setTitle(crec.get(Ext.ensible.cal.CalendarMappings.Title.name) + ' - Edit Rule')
			this.form.options = this.options; 
		}, this);
		
		this.on('saved', function () { 
			this.hide();
			if (this.callback && Ext.isFunction(this.callback.success)) {
				this.callback.success.call(this.callback.scope || this)
			}
		}, this);

		this.form.relayEvents(this, ['show', 'save']);
		this.relayEvents(this.form, ['saved']);
		
	}
});

IOH.Diary.Restrictions.WindowForm.show = function (options, callback) {
	var w;
	
	if (!(w = Ext.WindowMgr.get('diary-restrictions-window-form'))) {
		w = new IOH.Diary.Restrictions.WindowForm({
			id: 'diary-restrictions-window-form'
		});
	}
	
	w.options = options;
	w.callback = callback;
	w.show();
}