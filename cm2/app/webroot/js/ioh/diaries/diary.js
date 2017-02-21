/**
 *
 */
Ext.ensible.cal.EventMappings = {
  EventId:     {name: 'EventId', mapping:'Appointment.id', type:'string'},
  CalendarId:  {name: 'CalendarId', mapping: 'Appointment.diary_id', type: 'int'},
  CalendarName:  {
		name: 'CalendarName',
		convert: function(v, json) {
			return json.Diary !== undefined ? json.Diary.name : '';
		}
	},
	Title:       {
		name: 'Title',
		mapping: 'Appointment.title',
		type: 'string',
		convert: function (v, json) {
			if (!v) {
				v = json.Person.full_name;
			}
			return v;
		}
  },
  StartDate:   {name: 'StartDate', mapping: 'Appointment.from_date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
  EndDate:     {name: 'EndDate', mapping: 'Appointment.to_date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
  RRule:       {name: 'RecurRule', mapping: 'recur_rule'}, // not currently used
  Location:    {name: 'Location', mapping: 'loc', type: 'string'},
  Notes:       {name: 'Notes', mapping: 'Appointment.note', type: 'string'},
  Url:         {name: 'Url', mapping: 'url', type: 'string'},
  IsAllDay:    {name: 'IsAllDay', mapping: 'Appointment.is_all_day', type: 'boolean'},
  Reminder:    {name: 'Reminder', mapping: 'Appointment.remainder', type: 'string'},
  PersonId:	{name: 'PersonId', mapping: 'Appointment.person_id', type: 'int'},
  PersonName:	{name: 'PersonName', mapping: 'Person.full_name', type: 'string'},

  StartTime:	{name: 'StartTime', mapping: 'Appointment.start_time', type: 'string'},
  EndTime:	{name: 'EndTime', mapping: 'Appointment.end_time', type: 'string'},
  Period:		{name: 'Period', mapping: 'Appointment.period', type: 'string'},
  Type:		{name: 'Type', mapping: 'Appointment.type', type: 'string'},
  ReferralId: {name: 'ReferralId', mapping: 'Appointment.referral_id', type: 'int'},
  DiagnosisId:{name: 'DiagnosisId', mapping: 'Appointment.diagnosis_id', type: 'int'},
  NewOrReview:{name: 'NewOrReview', mapping: 'Appointment.new_or_review', type: 'string'},
  ReferredByType:{name: 'ReferredByType', mapping: 'Appointment.referrer_type_id', type: 'int'},
  ReferredByName:{name: 'ReferredByName', mapping: 'Appointment.referrer_name', type: 'string'},
  DeletedReason:{name: 'DeletedReason', mapping: 'Appointment.deleted_reason', type: 'string'},
  PassesLateCancelationCondition:    {name: 'PassesLateCancelationCondition', mapping: 'Appointment.passes_late_cancelation_condition', type: 'boolean'},
  BlockedAppointment:    {name: 'BlockedAppointment', mapping: 'Appointment.blocked', type: 'boolean'}
};

Ext.ensible.cal.EventRecord.reconfigure();

Ext.override(Ext.data.DataReader,{
    extractData : function(root, returnRecords) {

        var rawName = (this instanceof Ext.data.JsonReader) ? 'json' : 'node';

        var rs = [];

        if (this.isData(root) && !(this instanceof Ext.data.XmlReader)) {
            root = [root];
        }
        var f       = this.recordType.prototype.fields,
            fi      = f.items,
            fl      = f.length,
            rs      = [];
        if (returnRecords === true) {
            var Record = this.recordType;
            for (var i = 0; i < root.length; i++) {
                var n = root[i];
                var record = new Record(this.extractValues(n, fi, fl), this.getId(n));
                record[rawName] = n;
                rs.push(record);
            }
        }
        else if (root) {
            for (var i = 0; i < root.length; i++) {
                var data = this.extractValues(root[i], fi, fl);

                // Override start
                var id   = this.getId(root[i]),
	            	idPath = this.meta.idProperty.split('.'),
	            	d = data, j;
	            for (j = 0; j < idPath.length-1; j++) {
	            	if (!Ext.isObject(d[idPath[j]])) {
	            		d[idPath[j]] = {};
	            	}
	            	d = d[idPath[j]];
	            }

	            d[idPath[j]] = this.getId(root[i]);
	            // Override end.

 //               data[this.meta.idProperty] = this.getId(root[i]);
                rs.push(data);
            }
        }
        return rs;
    }
});

IOH.CalendarPanel = Ext.extend(Ext.ensible.cal.CalendarPanel,
{
	initComponent: function () {
		var cfg = {
	    	activeItem: 1,
	    	enableEditDetails: false,
	    	minEventDisplayMinutes: 10,
	    	calendarStore: IOH.APP.calendarStore,
	    	viewConfig: {
	    	    getEventBodyMarkup : function(){
	    	        if(!this.eventBodyMarkup){
	    	            this.eventBodyMarkup = ['{Title}',
	    	                '<tpl if="_isReminder">',
	    	                    '<i class="ext-cal-ic ext-cal-ic-rem">&#160;</i>',
	    	                '</tpl>',
	    	                '<tpl if="_isRecurring">',
	    	                    '<i class="ext-cal-ic ext-cal-ic-rcr">&#160;</i>',
	    	                '</tpl>',
	    	                '<div class="cm2-cal-notes">{Notes}</div>'
//	    	                '<tpl if="spanLeft">',
//	    	                    '<i class="ext-cal-spl">&#160;</i>',
//	    	                '</tpl>',
//	    	                '<tpl if="spanRight">',
//	    	                    '<i class="ext-cal-spr">&#160;</i>',
//	    	                '</tpl>'
	    	            ].join('');
	    	        }
	    	        return this.eventBodyMarkup;
	    	    },
	    	    //iewStartHour: 8,
	    		//viewEndHour: 18,
	    		startDay: 1, // Monday
	    		dateParamStart: 'from',
	    		dateParamEnd: 'to'
	    	},
	    	monthViewCfg: {
                showHeader: true,
                showWeekLinks: true,
                showWeekNumbers: true
            },
            listeners: {
            	beforeeventmove: function(cal, rec, dt) {
					message = '';
					if(!rec.get(Ext.ensible.cal.EventMappings.PassesLateCancelationCondition.name)) {
						message += 'This appointment falls within the 48 hour notification.  Please ensure this late change of appointment has been recorded as a DNA or LC before proceeding. Are you sure you want to move this appointment now?';
					} else {
						message += 'Are you sure you want to move this appointment?';
					}
					Ext.MessageBox.confirm(
						'Appointment Update',
						message,
						function(c) {
							if(c == 'yes') {
        						var diff = dt.getTime() - rec.data[Ext.ensible.cal.EventMappings.StartDate.name].getTime();
        			            rec.beginEdit();
        			            rec.set(Ext.ensible.cal.EventMappings.StartDate.name, dt);
        			            rec.set(Ext.ensible.cal.EventMappings.EndDate.name, rec.data[Ext.ensible.cal.EventMappings.EndDate.name].add(Date.MILLI, diff));
        			            rec.endEdit();
        			            cal.save();
        					}
        				}
            		);

            		return false;
            	}
            }
	    };

		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);

		IOH.CalendarPanel.superclass.initComponent.apply(this, arguments);

		//this.on('render', this.store.load, this.store);
		this.getTopToolbar().add('-', this.printButton = new Ext.Button({
    		text: 'Print',
			cls: 'x-btn-text-icon',
			iconCls: 'printer',
			handler: function (btn) { this.fireEvent('diaryprint', btn.info); },
			scope: this
    	}));

		this.getTopToolbar().add('-', this.refreshButton = new Ext.Button({
			text: 'Refresh',
			cls: 'x-btn-text-icon',
			iconCls: 'refresh',
			handler: function (btn) { this.store.reload(); },
			scope: this
		}));

		this.on('viewchange', function (cal, v, info) {
			// When the view range is within a single day - enable print button
			cal.printButton.setDisabled(info.viewStart.format('Ymd') != info.viewEnd.format('Ymd'));
			cal.printButton.info = info;
		});
	},

	showEventEditor: function (availRec) {
		var dates = {},
			diariesStore = Ext.StoreMgr.get('DiariesStore'),
			diaryRec = diariesStore.getById(availRec.get('Diary.id'));

        dates[Ext.ensible.cal.EventMappings.StartDate.name] = availRec.get('Gap.avail_from');
		dates[Ext.ensible.cal.EventMappings.EndDate.name] =
			dates[Ext.ensible.cal.EventMappings.StartDate.name]
				.add(Date.MINUTE, diaryRec.get(Ext.ensible.cal.CalendarMappings.ApptLength.name));

		if (IOH.APP.getComponent('IOH.Diary').diaryId != diaryRec.id) {
			// Fire a global diary switch
			IOH.APP.diaryCombo.setValue(diaryRec.id);
			IOH.APP.diaryCombo.fireEvent('select',IOH.APP.diaryCombo, diaryRec);
		}

		this.getActiveView().showEventEditor(dates, null);
	},

	hideEventEditor: function () {
		this.getActiveView().dismissEventEditor();
	}
});

IOH.Diary = Ext.extend(Ext.TabPanel,
{
	initComponent: function () {

		var eventStore = this.getEventStore();

		var calendar = new IOH.CalendarPanel({
			//region: 'center',
			eventStore: eventStore,
			store: eventStore,
			title: 'Calendar',
			listeners: {
				diarychanged: function (diaryId) {
					if (!this.activeView) {
						//return;
					}
					var bounds = this.activeView.getViewBounds();
					this.store.loadDiary(diaryId, {from: bounds.start, to: bounds.end});
				},
				scope: calendar
			}
		});

		var appointmentsGrid = new IOH.AppointmentsGrid({
			store: Ext.StoreMgr.get('ConflictAppointments'),
			//region: 'center',
			title: 'Conflicts',
			ref: 'conflictAppointments',
			viewConfig: {
				forceFit: true,
				emptyText: 'No conflicts.'
			},
			calendar: calendar,
			listeners: {
				diarychanged: function (diaryId) {
					if (!this.calendar.activeView) {
						//return;
					}
					var bounds = this.calendar.activeView.getViewBounds();
					this.store.loadDiary(diaryId, {from: bounds.start, to: bounds.end});
				},
				scope: appointmentsGrid
			}
		});

		var nextAvailableGrid = new IOH.NextAvailableGrid({
			title: 'Availability',
			viewConfig: {
				forceFit: true,
				emptyText: 'Not available.'
			},
		});


		/*
		var appointmentRequests = new IOH.AppointmentRequests({
			region: 'east',
			split: true,
			width: '50%',
			title: 'Referrals',
			ref: 'referrals',
			showEventEditor: function (data) {
				var view = calendar.getActiveView();
				if(view.isXType('extensible.dayview')) {
					view = view.items.get(1); // body view
				}

				view.showEventEditor(data);
			}

		});
		*/

		var cfg = {
			items: [calendar, nextAvailableGrid, appointmentsGrid],
			activeTab: 0,
			disabled: true
		};

		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);

		IOH.Diary.superclass.initComponent.apply(this, arguments);

		this.on('diarychanged', function () {
			this.setDisabled(false);
		}, this);

		calendar.relayEvents(this, ['diarychanged']);
		appointmentsGrid.relayEvents(this, ['diarychanged']);
		nextAvailableGrid.relayEvents(this, ['diarychanged']);

		this.relayEvents(calendar, ['diaryprint']);

		calendar.store.on('save', function () {
			this.conflictAppointments.store.reload();
		}, this);

		this.on('diaryprint', this.onPrint, this);

		nextAvailableGrid.on('showeventeditor', function (availRec) {
			this.setActiveTab(calendar);
			calendar.showEventEditor(availRec);
		}, this);

		this.on('appointmentdeleted', function () {
			calendar.hideEventEditor();
		}, this);


//		appointmentsGrid.relayEvents(calendar, ['viewchange']);
//		appointmentRequests.relayEvents(calendar, ['eventadd']);
	},

	getEventStore: function () {
		if (!this.eventStore) {
			this.eventStore = new Ext.data.DirectStore({
				storeId: 'appointments',
	            api: {
	            	read: Server.Appointments.direct_index,
	            	create: Server.Appointments.direct_save,
	            	update: Server.Appointments.direct_save,
	            	destroy: Server.Appointments.direct_delete
	            },
	            writer: new Ext.data.JsonWriter({
	                encode: false,
	                writeAllFields: true,
	                listful: false
	            }),
	            root: 'data',
		    	fields: Ext.ensible.cal.EventRecord.prototype.fields.getRange(),
	            idProperty: Ext.ensible.cal.EventMappings.EventId.mapping,

		        loadDiary: function (diaryId, more) {
	            	this.setBaseParam('diary_id', diaryId);

	            	Ext.iterate(more, function (name, val) {
	            		this.setBaseParam(name, val);
	            	}, this);

	            	this.load();
	            },

	            listeners: {
	            	exception: function(dataProxy, type, action, options, response, arg) {
	            		if(response.success == false && (action == 'create' || action == 'update')) {
	            			msg = '';
	            			for (var k in response.errors) {
	            			    if (response.errors.hasOwnProperty(k)) {
	            			       msg += k + ' => ' + response.errors[k];
	            			    }
	            			}
	            			IOH.APP.feedback('Appointment Not available', msg);
	            		}
	            	},

	            	beforesave: function (store, data) {
	            		if (!data.destroy || !data.destroy.length) {
	            			return;
	            		}
	            		diary_bs = this
						if(!data.destroy[0].data.PassesLateCancelationCondition) {
							Ext.MessageBox.confirm(
								'Appointment Update',
								'This appointment falls within the 48 hour notification.  Please ensure this late change of appointment has been recorded as a DNA or LC before proceeding. Are you sure you want to delete this appointment now?',
								function(c) {
									if(c == 'yes') {
		        						diary_bs.promptAndDeleteAppointment(data.destroy[0].id);
		        					}
		        				}
		            		);
						} else {
							this.promptAndDeleteAppointment(data.destroy[0].id);
						}

	            		store.suspendEvents(false);
	            		store.rejectChanges();
	            		store.resumeEvents();

                		return false;
	            	},

	            	scope: this
	            }
		    });
		}

		return this.eventStore;
	},

	/**
	 * Ask user to confirm deleting an appointment specified by its primary key
	 */
	promptAndDeleteAppointment: function (id) {
		Ext.MessageBox.prompt(
			'Appointment Delete',
			'Please enter the reason why are you deleting this appointment',
			function (btn, reason) {
				if(btn == 'ok') {
					this.deleteAppointment(id, reason);
				}
			},
			this,
			true
		);
	},

	/**
	 * Send request to server to delete an appointment
	 */
	deleteAppointment: function (id, reason) {
		var store = this.getEventStore();

		store.api.destroy({id: id, reason: reason}, function (res) {
     	   if (res.success) {
     		   IOH.APP.feedback('Deleted', 'Appointment deleted successfully.');
     		   store.reload();
     		   this.fireEvent('appointmentdeleted', id);
     	   } else {
     		   IOH.APP.feedback('Error', 'Appointment NOT deteled!');
     	   }
        }, this);
	},

	setDiary: function (diaryId) {
		this.diaryId = diaryId;
		this.fireEvent('diarychanged', diaryId);
	},

	onPrint: function (info) {
		var url = String.format('/appointments/daily/{0}/{1}', this.diaryId, info.viewStart.format('Y-m-d'));
		window.open(url);
	}
});

Ext.reg('IOH.Diary', IOH.Diary);