IOH.APP = {
	start: function () {
	    Ext.get('loading').remove();
	    Ext.get('loading-mask').remove();
	
		var self = this;
		
		Ext.form.DateField.prototype.format = 'd/m/y';
		Ext.ensible.cal.DateRangeField.prototype.dateFormat = 'd/m/y';
		Ext.form.DateField.prototype.altFormats = 'd/m/Y|Y-m-d|Y-m-d H:i:s';
		Ext.form.TimeField.prototype.format = 'H:i';
		Ext.form.TimeField.prototype.altFormats += '|H:i:s';

		Ext.QuickTips.init();

	    // turn on validation errors beside the field globally
	    Ext.form.Field.prototype.msgTarget = 'side';
	    
	    this._content = new IOH.Content();
	    
	    this.initStaticStores();
	    
	    this._newReferralBtn = new Ext.Button({
	    	prevTotal: 0,
			text: 'New Referral',
			hidden: true,
			handler: function () {
				IOH.APP.navigator().go('triage', 'IOH.Triages.Referrals');
			}
		});
	    
		this._viewport = new Ext.Viewport({
		    layout: 'border',
		    defaults: {
			},
		    items: [{
		        region: 'north',
				xtype: 'toolbar',
				height: 30,
				items: ['->',this._newReferralBtn,'-',{
					xtype:'tbtext',
					text: '<span style="font-size: 1.6em; font-weight: bold; color: #008; font-family: Georgia, serif;">OSIRIS</span>'
				}],
				margins: '0 0 5px 0'
		    }, {
		    	xtype: 'IOH.Navigator'
		    }, 
		    this._content, 
		    {
		        region: 'south',
				xtype: 'statusbar',
				ref: 'statusbar',
				id: 'my-status',
				height: 28,
	        
		        // defaults to use when the status is cleared:
		        defaultText: 'Default status text',
		        defaultIconCls: 'default-icon',
		        
		        // values to set initially:
		        text: 'Ready',
		        iconCls: 'ready-icon',
		        
		        // any standard Toolbar items:
		        items: [
					{
		            	cls: 'x-btn-text-icon',
		            	icon: '/css/img/exit.gif',
						xtype: 'tbbutton',
						text: 'Logout',
						handler: function () {
		            		window.location = '/users/logout';
		            	}
					}
				]
			}]
		});
		
		this.checkSession();
		
		// Initiate the poll for new referrals
		this.newestReferralDate = null;
		
		Ext.TaskMgr.start({
			run: function () {
				var date = null;
				if (this.newestReferralDate) {
					date = this.newestReferralDate.format('Y-m-d H:i:s');
				}
				Server.Referrals.direct_poll({date: date}, function (result) {
					if (!result.success) {
						return;
					}
					
					this._newReferralBtn.setVisible(result.total > 0);
					this._newReferralBtn.setText('New Referrals (' + result.total + ')');
					
					if (result.total > 0 && this._newReferralBtn.prevTotal != result.total) {
						IOH.APP.feedback('New Referrals', 'There are ' + (result.total - this._newReferralBtn.prevTotal) + ' new referral(s).');
					}
					
					this._newReferralBtn.prevTotal = result.total;
					
				}, this);
			},
			scope: this,
			interval: 1000 * 120 // 2 minutes
		});
	},
	
	resetNewReferrals: function (dt) {
		this.newestReferralDate = dt;
		this._newReferralBtn.hide();
		this._newReferralBtn.prevTotal = 0;
	},
	
	navigator: function () {
		return this._viewport.items.get(1);
	},
	
	activate: function (xtype) {
		var component = this.getComponent(xtype);
		var self = this;
		
		if (!component) {
			component = this._content.add({xtype: xtype});
			setTimeout(function () {
				self._content.doLayout();
			}, 1);
		}
		
		this._content.getLayout().setActiveItem(component);
	},
	
	getComponent: function (xtype) {
		var component = this._content.findByType(xtype);
		
		if (component.length) {
			component = component[0];
		} else {
			component = null;
		}
		
		return component;
	},
	
	showPeopleWindow: function (options) {
		if (!this.peopleWindow) {
			this.peopleWindow = new IOH.People.Window();
		}
		
		this.peopleWindow.options = options || {};
		this.peopleWindow.show();
	},
	
	showDiagnosesWindow: function (options) {
		(new IOH.Diagnoses.Window({options: options})).show();
	},
	
	showSicknotesWindow: function (options) {
    	if (!this.sicknotesWindow) {
        	this.sicknotesWindow = new IOH.Sicknote.Window();
    	}
    	
    	this.sicknotesWindow.options = options;
    	this.sicknotesWindow.show();
	},
	
	feedback: (function () {
	    var msgCt;

	    function createBox(t, s){
	        return ['<div class="msg">',
	                '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>',
	                '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc"><h3>', t, '</h3>', s, '</div></div></div>',
	                '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>',
	                '</div>'].join('');
	    }

	    return function(title, format){
            if(!msgCt){
                msgCt = Ext.DomHelper.insertFirst(document.body, {id:'msg-div'}, true);
            }
            msgCt.alignTo(document, 't-t');
            var s = String.format.apply(String, Array.prototype.slice.call(arguments, 1));
            var m = Ext.DomHelper.append(msgCt, {html:createBox(title, s)}, true);
            m.slideIn('t').pause(3).ghost("t", {remove:true});
	    }
	})(),
	
	initStaticStores: function () {
	    Ext.ensible.cal.CalendarMappings = {
    	    CalendarId:   {name:'ID', mapping: 'Diary.id', type: 'string'},
    	    Title:        {name:'CalTitle', mapping: 'Diary.name', type: 'string'},
    	    Description:  {name:'Desc', mapping: 'Diary.description', type: 'string'},
    	    ColorId:      {name:'Color', mapping: 'Diary.color_id', type: 'string'},
    	    IsHidden:     {name:'Hidden', mapping: 'Diary.is_hidden', type: 'boolean'},
    	    
    	    // We can also add some new fields that do not exist in the standard CalendarRecord:
    	    Owner:        {name: 'Owner', mapping: 'Diary.user_id'},
    	    DefaultType:	{name: 'DefaultType', mapping: 'Diary.default_appointment_type'},
    	    NPT:	{name: 'NPT', mapping: 'Diary.is_npt', type: 'boolean'},
    	    ApptLength: {name: 'ApptLength', mapping: 'Diary.appointment_length', type: 'int'}
    	};
    	// Don't forget to reconfigure!
    	Ext.ensible.cal.CalendarRecord.reconfigure();

    	this.calendarStore = new Ext.data.DirectStore({
    		storeId: 'DiariesStore',
	    	directFn: Server.Diaries.direct_index,
	    	root: 'data',
	    	idProperty: Ext.ensible.cal.CalendarMappings.CalendarId.mapping || 'Diary.id',
	    	fields: Ext.ensible.cal.CalendarRecord.prototype.fields.getRange(),
	    	autoLoad: true
	    });
    	
    	this.regularDiariesStore = new Ext.data.Store({ 
    		storeId: 'RegularDiaries',
		    recordType: this.calendarStore.recordType 
		});
    	
    	this.calendarStore.on('datachanged', function (s) {
    		var records = [];
    		var M = Ext.ensible.cal.CalendarMappings;
    		
    		s.each(function(r){
    			if (!r.data[M.NPT.name]) {
    				records.push(r.copy());
    			}
    		});
    		
    		this.removeAll();
    		this.add(records);
    	}, this.regularDiariesStore);
    	
    	this.attendanceReasonsStore = new Ext.data.DirectStore({
    		storeId: 'AttendanceReasonsStore',
			root: 'data',
			directFn: Server.AttendanceReasons.direct_get_diary_reasons,
			fields: [
        		{name: 'code', mapping: 'AttendanceReason.code'}, 
        		{name: 'description', mapping: 'AttendanceReason.description'}
        	],
			autoLoad: true
		});

		new Ext.data.DirectStore({
			storeId: 'ReferralReasonsStore',
	    	directFn: Server.ReferralReasons.direct_index,
	    	root: 'data',
	    	idProperty: 'ReferralReason.id',
	    	fields: [{
	    		name: 'id', mapping: 'ReferralReason.id', type: 'int',
	    	},{
	    		name: 'reason', mapping: 'ReferralReason.reason'
	    	}],
	    	autoLoad: true
	    });
		
		this.referrerTypesStore = new Ext.data.DirectStore({
			storeId: 'ReferrerTypesStore',
	    	directFn: Server.ReferrerTypes.direct_index,
	    	root: 'data',
	    	idProperty: 'ReferrerType.id',
	    	fields: [{
	    		name: 'id', mapping: 'ReferrerType.id', type: 'int',
	    	},{
	    		name: 'type', mapping: 'ReferrerType.type'
	    	}],
	    	autoLoad: true
	    });
		
		new Ext.data.DirectStore({
			storeId: 'ConflictAppointments',
			api: {
            	read: Server.Appointments.direct_conflicts,
            	create: Server.Appointments.direct_save,
            	update: Server.Appointments.direct_save,
            	destroy: Server.Appointments.direct_delete,
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
            }
		});
		
		new Ext.data.DirectStore({
			storeId: 'AttendanceOutcomesStore',
			directFn: Server.AttendanceOutcome.direct_index,
			fields: [{
				name: 'id', mapping: 'AttendanceOutcome.id', type: 'int'
			},{
				name: 'title', mapping: 'AttendanceOutcome.title'
			}],
			root: 'data',
			idProperty: 'id',
			autoLoad: true
		});
	},
	
	checkSession: function () {
        Server.Users.direct_peeksession(null, function (info) {
        	if (info.remainingTime == 0) {
        		setTimeout(function () { window.location.reload(); }, 1000);
        	}
        	
        	// schedule next peek 5 seconds before the session expiration.
        	var next = info.remainingTime - 5;
        	if (next <= 0) {
            	this._viewport.statusbar.setText(String.format('Session is expiring in {0} seconds!', info.remainingTime));
        		next = 1;
        	} else {
        		this._viewport.statusbar.setText('Ready');
        	}
        	setTimeout(this.checkSession.createDelegate(this), next*1000);
        }, this);
	},
	
	getEventStore: function (type) {
		type = type || '';

		var store, storeId = 'appointments-' + type;
		
		if (!(store = Ext.StoreMgr.get(storeId))) {
			store = new Ext.data.DirectStore({
				storeId: storeId,
				remoteSort: true,
	            api: {
	            	read: Server.Appointments.direct_index,
	            	create: Server.Appointments.direct_save,
	            	update: Server.Appointments.direct_save,
	            	destroy: Server.Appointments.direct_delete,
	            },
	            baseParams: {
	            	'Appointment.state' : type,
	            	start: 0,
	            	limit: 20
	            },
	            writer: new Ext.data.JsonWriter({
	                encode: false,
	                writeAllFields: true,
	                listful: false
	            }),
	            root: 'data',
		    	fields: Ext.ensible.cal.EventRecord.prototype.fields.getRange(),
	            idProperty: Ext.ensible.cal.EventMappings.EventId.mapping,
	            totalProperty: 'total',
	            listeners: {
	            	beforeload: function (store, options) {
	            		if (options.params.sort && Ext.ensible.cal.EventMappings[options.params.sort]) {
            				options.params.sort = Ext.ensible.cal.EventMappings[options.params.sort].mapping;
	            		}
	            	}
	            }
		    });
		}
		
		return store;
	}
};

/**
 * Helper function: Check if a component and all it owner components are visible.
 * 
 * @param cmp Component
 * @return bool
 */
function deepIsVisible(cmp) {
	if (!cmp.isVisible()) {
		return false;
	}
	
	if (!cmp.ownerCt) {
		return true;
	}
	
	return deepIsVisible(cmp.ownerCt);
}


/**
 * Create custom Ext.form.BasicForm event: beforesetvalues
 */
(function () {
	var setValuesOrig = Ext.form.BasicForm.prototype.setValues;
	
	Ext.override(Ext.form.BasicForm, {
		
		setValues : function (values) {
			this.fireEvent('beforesetvalues', values);
			setValuesOrig.apply(this, arguments);
	    }
	});
})();

(function () {
	var setValueOrig = Ext.form.ComboBox.prototype.setValue;
	
	Ext.override(Ext.form.ComboBox, {
		setValue: function () {
			setValueOrig.apply(this, arguments);
			this.fireEvent('valuechange', this.getValue());
		}
	});
})(); 

Ext.onReady(function () {
	setTimeout(function () {
		IOH.APP.start();
	}, 500);
});

Ext.PagingToolbar.prototype.doRefresh = function() {
    this.doLoad(this.cursor);
};

if (!console || !console.log) {
	var console = {
		log : function () {}
	};
}