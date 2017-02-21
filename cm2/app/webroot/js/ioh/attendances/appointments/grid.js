/**
 * 
 */

Ext.ns('IOH.Attendances.Appointments');

IOH.Attendances.Appointments.Grid = Ext.extend(Ext.grid.GridPanel,
{
	initComponent: function () {
		var pagingToolbar = new Ext.PagingToolbar({
			store: this.store,
			pageSize: this.store.baseParams.limit || 20,
			displayInfo: true,
			displayMsg: 'Displaying appointments {0} - {1} of {2}',
			emptyMsg: "No appointments found."
		});
		
		var cfg = {
			columns: [{
				header: 'When',
				dataIndex: Ext.ensible.cal.EventMappings.StartDate.name,
				renderer: function (v, meta, rec) {
					return rec.get(Ext.ensible.cal.EventMappings.Period.name);
				},
				width: 100,
				sortable: true
			},{
				header: 'Patient',
				id: 'patient',
				dataIndex: Ext.ensible.cal.EventMappings.Title.name,
				sortable: true
			},{
				header: 'Diary Name',
				dataIndex: Ext.ensible.cal.EventMappings.CalendarId.name,
				renderer: function (v, meta, rec) {
					return rec.get(Ext.ensible.cal.EventMappings.CalendarName.name);
				},
				sortable: true
			}],
			autoExpandColumn: 'patient',
			viewConfig: {
				forceFit: true,
				getRowClass1: function (rec, idx, params, store) {
					var calId  = rec.get(Ext.ensible.cal.EventMappings.CalendarId.name);
					var calRec;
					var colorId = 0;
					
					if (calId > 0) {
						calRec = IOH.APP.calendarStore.getById(calId);
						colorId = calRec.get(Ext.ensible.cal.CalendarMappings.ColorId.name);
					}
					
					return String.format('ext-cal-evr x-cal-{0}-ad', colorId);
				}
			},
			loadMask: true,
			bbar: pagingToolbar,
			sm: new Ext.grid.RowSelectionModel(),
			cls1: 'x-calendar-list'
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Attendances.Appointments.Grid.superclass.initComponent.apply(this, arguments);
		
		this.getSelectionModel().on('selectionchange', this.selectionChange, this);
		this.on('activate', this.store.load, this.store);
	},
	
	setState: function (state) {
		var sel = this.getSelectionModel().getSelections();
		
		var ids = [];
		
		for (var i = 0; i < sel.length; i++) {
			ids.push(sel[i].id);
		}
		
		if (!ids.length) {
			return;
		}
		
		Server.Appointments['direct_'+state](ids, function (result) {
			if (result.success) {
				IOH.APP.feedback('Success', 'State has been changed to `' + state + '`.');
				this.store.reload();
			}
		}, this);
	},
	
	selectionChange: function () {
		
	}
	
});