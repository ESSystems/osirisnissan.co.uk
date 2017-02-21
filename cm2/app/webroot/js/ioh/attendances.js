IOH.Attendances = Ext.extend(Ext.Container, 
{
	initComponent: function () {
		
		var pendingGrid = new IOH.Attendances.PendingGrid();
		var grid        = new IOH.Attendances.SearchResultsGrid();
		var form        = new IOH.Attendances.AttendanceForm({
			defaults: {style:'padding:5px 5px 0;'},
			split: true,
			region: 'north',
			autoScroll: true,
			collapseMode: 'mini',
			hideBorders: true,
			autoHeight: true,
			title: false
		});
		
		grid.relayEvents(form, 'reset');
		
		grid.getSelectionModel().on('rowselect', function(model, rowIndex, record) {
			form.loadAttendance(record.id);
		});
		pendingGrid.getSelectionModel().on('rowselect', function(model, rowIndex, record) {
			form.loadAttendance(record.id);
		});
		
		pendingGrid.on('activate', pendingGrid.reload, pendingGrid);

		var tabs = [pendingGrid, grid];
		
		if (IOH.USER.belongsToGroup(['System supervisor','Admin'])) {
			var deletedGrid = new IOH.Attendances.DeletedGrid();
			deletedGrid.getSelectionModel().on('rowselect', function(model, rowIndex, record) {
				form.loadAttendance(record.id);
			});
			deletedGrid.on('activate', deletedGrid.store.reload, deletedGrid.store);
			tabs.push(deletedGrid);
		}
		
		Ext.each(tabs, function (g) {
			g.on('deactivate', function () {
				g.getSelectionModel().clearSelections();
			}, g);
		});
		
		this._gridsTab = new Ext.TabPanel({
			id: 'attendances-tab-panel',
			region: 'center',
	    	activeTab: 0,
			bodyBorder: true,
			minHeight: 200,
	    	items: tabs
		});
	
	
		var config = {
			id: 'attendances',
			layout: 'border',
			border: false,
			hideMode: 'offsets',
			items: [
				form,
				this._gridsTab
			]
		};
	
		Ext.apply(this, config);
		Ext.apply(this.initialConfig, config);
		
		IOH.Attendances.superclass.initComponent.apply(this, arguments);
		
		pendingGrid.relayEvents(this, ['show', 'hide']);
		
		this.relayEvents(pendingGrid, ['delete']);
		this.relayEvents(grid, ['delete']);
		this.on('delete', this._deleteAttendance, this);
	},
	
	search: function (filter) {
		this._gridsTab.activate(1);
		this._getSearchGrid().reload(filter);
	},
	
	_getSearchGrid: function () {
		return this._gridsTab.items.get(1);
	},
	
	_getPendingGrid: function () {
		return this._gridsTab.items.get(0);
	},
	
	_getAttendanceForm: function () {
		return this.items.get(0);
	},
	
	_deleteAttendance: function (grid) {
		var sel = grid.getSelectionModel().getSelections();
		if (sel.length == 0) {
			return;
		}
		
		if (!confirm('Are you sure you want do delete selected attendance record(s)?')) {
			return false;
		}
		
		var ids = [];
		
		Ext.each(sel, function (rec) {
			ids.push(rec.id);
		});
		
		Server.Attendances.direct_hide({
			data: {
				Attendance: { ids : ids }
			}
		}, function () {
			this.store.reload();
		}, grid);
	}
});

Ext.reg('IOH.Attendances', IOH.Attendances);