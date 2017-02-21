IOH.FollowUpsStore = Ext.extend(Ext.data.JsonStore, 
{
	constructor: function (config) {
		IOH.FollowUpsStore.superclass.constructor.call(this, Ext.apply({
			url: '/followups/index.json',
			totalProperty: 'total',
			successProperty: 'success',
	        root: 'rows',
	        id: 'Followup.id',
	        fields: [
 	            {name: 'Person.full_name'},
	            {name: 'Patient.Organisation.OrganisationName', mapping: 'Person.Patient.Organisation.OrganisationName'},
	            {name: 'Followup.date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
	            {name: 'Followup.type'},
	            {name: 'Followup.result_attendance_id', type: 'int'}
	        ],
			remoteSort: true,
			listeners: {
				load: {
					fn: function () {
						this._dirty = false;
					},
					scope: this
				}
			}
		}, config));
		
		this._dirty = true;
	},
	
	isDirty: function () {
		return this._dirty;
	},
	
	setDirty: function () {
		this._dirty = true;
	}
});


IOH.FollowUps = Ext.extend(Ext.grid.GridPanel, 
{
	initComponent: function () {
		var store =  new IOH.FollowUpsStore({});
		
		var pagingToolbar = new Ext.PagingToolbar({
			pageSize: 50,
			store: store,
			displayInfo: true,
			displayMsg: 'Displaying follow ups {0} - {1} of {2}',
			emptyMsg: "No follow ups found."
		});
		
		var config = {
			title: 'Follow Ups',
			store: store,
			columns: [{
				id: 'person',
				header: 'Person',
				dataIndex: 'Person.full_name'
			},{
				header: 'Client',
				dataIndex: 'Patient.Organisation.OrganisationName',
				width: 50
			},{
				header: 'Type'
				,dataIndex: 'Followup.type'
				,width: 20
				,sortable: true
			},{
				header: 'Date',
				dataIndex: 'Followup.date',
				sortable: true,
				renderer: Ext.util.Format.dateRenderer('m/d/Y'),
				width: 20
			}
			],
			autoExpandColumn: 'person',
			viewConfig: {
				forceFit: true
			},
			sm: new Ext.grid.RowSelectionModel({
				listeners: {
					selectionchange: {
						fn:this._onSelectionChanged,
						scope: this
					}
				}
			}),
			tbar: [{
				text: 'Make Attendance',
				handler: this._onMakeAttendance,
				scope: this,
				disabled: true,
				cls: 'x-btn-text-icon',
				iconCls: 'arrow_rotate_clockwise'
			}],
			bbar: pagingToolbar
		};
		
		Ext.apply(this, config);
		
		IOH.FollowUps.superclass.initComponent.apply(this, arguments);
		
		this.on('beforeshow', this._onBeforeShow, this);
		this.subscribe('Attendance.saved', this._reload, this);
	},
	
	_onBeforeShow: function () {
		if (this.getStore().isDirty()) {
			this.getStore().reload();
		}
	},
	
	_reload: function () {
		this.getStore().setDirty(true);
		if (this.isVisible()) {
			this.getStore().reload();
		}
	},
	
	_getButton: function (i) {
		return this.getTopToolbar().items.get(i);
	},
	
	_onSelectionChanged: function (sm) {
		var selCount = sm.getCount(); 
		
		this._getButton(0).setDisabled(selCount < 1);
	},
	
	/**
	 * Send request to create attendance records for all selected followup records
	 */
	_onMakeAttendance: function () {
		var sm   = this.getSelectionModel();
		
		if (sm.getCount() < 1) {
			return;
		}
		
		var selectedRecords = sm.getSelections(); // An array of records being selected
		
		Ext.each(selectedRecords, this._makeAttendance, this);
	},
	
	/**
	 * Send request to create attendance record for a given followup record.
	 * 
	 * @param Record rec
	 */
	_makeAttendance: function (rec) {
		Ext.Ajax.request({
			url: '/followups/makeAttendance.json',
			params: {
				id: rec.id
			},
			success: function (response) {
				this.getStore().reload();
				IOH.APP.feedback('Attendance Created', 'Attendance Record has been created successfully.');
			},
			scope: this
		});
	}
});

Ext.reg('IOH.FollowUps', IOH.FollowUps);