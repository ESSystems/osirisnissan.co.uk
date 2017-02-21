CMX.APP = {

	initCalendar: function() {
		CMX.APP.Calendar = this.calendar = new Ext.ensible.cal.CalendarPanel({
			eventStore: this.eventStore,
			calendarStore: this.calendarStore,
			renderTo: 'calendar',
			height: 600,
			activeItem: 1,
			enableEditDetails: false,
			minEventDisplayMinutes: 10,
			viewConfig: {
				getEventBodyMarkup: function() {
					if (!this.eventBodyMarkup) {
						this.eventBodyMarkup = ['{Title}',
							'<tpl if="_isReminder">',
							'<i class="ext-cal-ic ext-cal-ic-rem">&#160;</i>',
							'</tpl>',
							'<tpl if="_isRecurring">',
							'<i class="ext-cal-ic ext-cal-ic-rcr">&#160;</i>',
							'</tpl>',
							'<div class="cm2-cal-notes">{Notes}</div>'].join('');
					}
					return this.eventBodyMarkup;
				},
				startDay: 1 // Monday
			},
			dayViewCfg: {},
			monthViewCfg: {
				showHeader: true,
				showWeekLinks: true,
				showWeekNumbers: true
			},
			listeners: {
				dayclick: function() {
					return false;
				},
				eventclick: function(cal, rec, el) {
					if (rec !== undefined) cal.showEventEditor(rec, el);
					return false;
				},
				beforeeventresize: function() {
					return false;
				},
				beforeeventmove: function(cal, rec, dt) {
					Ext.MessageBox.confirm(
						'Appointment Update',
						'Are you sure you want to move this appointment ?',

					function(c) {
						if (c == 'yes') {
							var diff = dt.getTime() - rec.data[Ext.ensible.cal.EventMappings.StartDate.name].getTime();
							rec.beginEdit();
							rec.set(Ext.ensible.cal.EventMappings.StartDate.name, dt);
							rec.set(Ext.ensible.cal.EventMappings.EndDate.name, rec.data[Ext.ensible.cal.EventMappings.EndDate.name].add(Date.MILLI, diff));
							rec.endEdit();
							cal.save();
						}
					});

					return false;
				}
			}
		});
	},

	initStores: function() {
		this.calendarStore = new Ext.data.DirectStore({
			storeId: 'DiariesStore',
			directFn: Server.Cmx.direct_diaries_index,
			root: 'data',
			idProperty: Ext.ensible.cal.CalendarMappings.CalendarId.mapping || 'Diary.id',
			fields: Ext.ensible.cal.CalendarRecord.prototype.fields.getRange(),
			autoLoad: true
		});

		this.eventStore = new Ext.data.DirectStore({
			storeId: 'appointments',
			api: {
				read: Server.Cmx.direct_index,
				update: Server.Cmx.direct_save
			},
			writer: new Ext.data.JsonWriter({
				encode: false,
				writeAllFields: true,
				listful: false
			}),
			root: 'data',
			fields: Ext.ensible.cal.EventRecord.prototype.fields.getRange(),
			idProperty: Ext.ensible.cal.EventMappings.EventId.mapping,
			listeners: {
				write: function(store, action, result, res, rs) {
					CMX.APP.Calendar.setStartDate(rs.get("StartDate"));
					this.parseResponse(action, res.result);
				},
				exception: function(dataProxy, type, action, options, response, arg) {
					this.parseResponse(action, response);
				}
			},
			parseResponse: function(action, response) {
				if (response.success === false && (action == 'create' || action == 'update')) {
					msg = '';
					for (var k in response.data.Appointment) {
						if (response.data.Appointment.hasOwnProperty(k)) {
							msg += response.data.Appointment[k];
						}
					}
					Ext.Msg.show({
						title: 'Appointment Not available',
						msg: msg,
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.ERROR
					});
				} else if (response.success === true) {
					Ext.Msg.show({
						title: 'Appointment Saved',
						msg: response.data.Appointment,
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
				}
			}
		});
	},

	start: function() {
		this.start_date = Ext.get("calendar").getAttribute('start-date');

		Ext.form.DateField.prototype.format = 'd/m/y';
		Ext.ensible.cal.DateRangeField.prototype.dateFormat = 'd/m/y';
		Ext.form.DateField.prototype.altFormats = 'd/m/Y|Y-m-d|Y-m-d H:i:s';
		Ext.form.TimeField.prototype.format = 'H:i';
		Ext.form.TimeField.prototype.altFormats += '|H:i:s';

		this.initStores();
		this.initCalendar();

		this.calendar.setStartDate(new Date.parseDate(this.start_date, "Y-m-d H:i:s"));
	}
};

Ext.onReady(function() {
	//	setTimeout(function () {

	CMX.APP.start();
	//	}, 500);

});