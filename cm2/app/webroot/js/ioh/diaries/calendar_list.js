/**
 * 
 */

Ext.override(Ext.ensible.cal.CalendarListMenu, 
{
    initComponent : function(){
        this.addEvents(
            'showcalendar',
            'hidecalendar',
            'radiocalendar',
            'colorchange'
        );
        
        var items = [];
        
        if (IOH.USER.belongsToGroup(['System supervisor', 'Admin'])) {
        	items.push({
            	text: 'Edit Diary',
            	handler: function () {
            		if (this.panel) {
            			this.panel.addDiary(this.calendarId);
            		}
            	},
            	scope: this
            },{
            	text: 'Non-patient Time',
            	handler: function () {
            		if (this.panel) {
            			this.panel.editRestrictions(this.calendarId);
            		}
            	},
            	scope: this
            });
        	
        	/*
        	items.push('-', {
                xtype: 'extensible.calendarcolorpalette',
                handler: this.handleColorSelect.createDelegate(this)
            });
            */
        }
        
        Ext.apply(this, {
            items: items 
        });
        
        Ext.ensible.cal.CalendarListMenu.superclass.initComponent.call(this);
    },
    
    // private
    afterRender: function(){
        Ext.ensible.cal.CalendarListMenu.superclass.afterRender.call(this);
        /*
        this.palette = this.findByType('extensible.calendarcolorpalette')[0];
        
        if(this.pallete && this.colorId){
            this.palette.select(this.colorId, true);
        }
        */
    },
    
    setCalendar: function(id, cid){
        this.calendarId = id;
        this.colorId = cid;
        
        return this;
    }
});

IOH.DiaryCombo = Ext.extend(Ext.ensible.cal.CalendarCombo,
{
	mode: 'local',
	
	editable: false,
	
	initComponent: function () {
		IOH.DiaryCombo.superclass.initComponent.apply(this, arguments);
	}
});

IOH.CalendarListPanel = Ext.extend(Ext.Panel, {
	initComponent: function () {
		var M = Ext.ensible.cal.CalendarMappings;
		
		var cfg = {
			layout: 'form',
			labelAlign: 'top',
			autoScroll: true,
			items: [new IOH.DiaryCombo({
				ref: 'diaryCombo',
				store: Ext.StoreMgr.get('RegularDiaries'),
				queryAction: 'all',
				lastQuery: '',
				labelAlign: 'top',
				fieldLabel: 'Diary',
				emptyText: 'Select a Diary ...',
				anchor: '0',
				listeners: {
					select: function (c, rec) {
						IOH.APP.calendarStore.each(function (r) {
							var show = (r.id == rec.id || r.id == Ext.num(rec.id) + 100000);
							r.set(M.IsHidden.name, !show);
						});
						
						this.editButton && this.editButton.setDisabled(!c.getValue());
						
						if (IOH.APP.getComponent('IOH.Diary')) {
							IOH.APP.getComponent('IOH.Diary').setDiary(c.getValue(), rec);
						}
					},
					scope: this
				}
			}), {
				xtype: 'button',
				ref: 'showAllBtn',
				text: 'Show All Diaries',
				handler: function () {
					Ext.StoreMgr.get('RegularDiaries').clearFilter();
				},
				disabled: true
			}]
		};

		
		if (IOH.USER.belongsToGroup(['System supervisor', 'Site supervisor'])) {
			cfg.tbar = [{
				xtype: 'button',
				text: 'Add Diary',
				handler: this.addDiary,
				scope: this
			}, {
				ref: '../editButton',
				disabled: true,
				xtype: 'button',
				text: 'Edit',
				menu: {
					items: [{
						text: 'Edit Diary',
						handler: function () {
							if (this.diaryCombo.getValue()) {
								this.addDiary(this.diaryCombo.getValue());
							}
						},
						scope: this
					},{
						text: 'Edit Non-Patient Time',
						handler: function () {
							if (this.diaryCombo.getValue()) {
								this.editRestrictions(this.diaryCombo.getValue());
							}
						},
						scope: this
					}]
				}
			}];
		}
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.CalendarListPanel.superclass.initComponent.apply(this, arguments);

		this.on('activate', function () { 
			IOH.APP.activate('IOH.Diary');
			
			var store = Ext.StoreMgr.get('RegularDiaries');
			var combo = this.diaryCombo;
			var rec;
			
			if (!store.data.key(combo.getValue())) {
				if (rec = store.data.item(0)) {
					combo.setValue(rec.id);
					combo.fireEvent('select', combo, rec, store.indexOf(rec));
				}
			}
			
		}, this);
		
		Ext.StoreMgr.get('RegularDiaries').on('datachanged', function (store) {
			this.showAllBtn.setDisabled(!store.isFiltered());
		}, this);
		
		// Global variable!!!
		IOH.APP.diaryCombo = this.diaryCombo;
	},
	
	addDiary: function (id) {
		if (!this.diaryWindow) {
			this.diaryWindow = new IOH.Diary.Window();
		}

		this.diaryWindow.calendarId = id;
		
		if (!this.diaryWindow.isVisible()) {
			this.diaryWindow.show();
		}
	},
	
	editRestrictions: function (id) {
		IOH.Diary.Restrictions.Window.show({
			calendarId: id
		});
	}
});

Ext.reg('IOH.CalendarListPanel', IOH.CalendarListPanel);