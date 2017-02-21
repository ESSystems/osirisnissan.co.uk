/**
 *
 */

Ext.ns('IOH.Triages.Referrals');

function getDeclinedAt(v, record) {
	if(record.Declination[0] != undefined) {
		date = new Date.parseDate(record.Declination[0].created,"Y-m-d H:i:s");
		return date;
	}
}

IOH.Triages.Referrals.Grid = Ext.extend(Ext.grid.GridPanel,
{
	border: true,

	initComponent: function () {

		var store = new Ext.data.DirectStore({
			storeId: this.type + 'ReferralsStore',
			directFn: Server.Referrals.direct_index,
			fields: [{
				name: 'Referral.id', type: 'int'
			},{
				name: 'Person.full_name'
			},{
				name: 'Person.id', type: 'int'
			},{
				name: 'Person.Employee.sap_number'
			},{
				name: 'Person.Employee.salary_number'
			},{
				name: 'Person.Employee.employment_start_date', type: 'date', dateFormat: 'Y-m-d'
			},{
				name: 'Referral.patient_status_id', type: 'int'
			},{
				name: 'Referral.case_nature'
			},{
				name: 'Referral.job_information'
			},{
				name: 'Referral.history'
			},{
				name: 'Referral.created_at', type: 'date', dateFormat: 'Y-m-d H:i:s'
			},{
				name: 'Referral.updated_at', type: 'date', dateFormat: 'Y-m-d H:i:s'
			},{
				name: 'Referral.private', type: 'bool'
			},{
				name: 'Referral.canceled_reason'
			},{
				name: 'Referral.canceled_on', type: 'date', dateFormat: 'Y-m-d H:i:s'
			},{
				name: 'Referral.state'
			},{
				name: 'Creator.full_name', type: 'string'
			},{
				name: 'Updater.full_name', type: 'string'
			},{
				name: 'Referral.referral_reason_id'
			},{
				name: 'Referral.case_reference_number'
			},{
				name: 'Referral.sickness_started', type: 'date', dateFormat: 'Y-m-d'
			},{
				name: 'Referral.sicknote_expires', type: 'date', dateFormat: 'Y-m-d'
			},{
				name: 'Referral.operational_property_id', type: 'int'
			},{
				name: 'Follower'
			},{
				name: 'PatientStatus.status'
			},{
				name: 'ReferralReason.reason'
			},{
				name: 'OperationalPriority.operational_priority'
			},{
				name: 'declined_at', convert: getDeclinedAt
			}],
			root: 'data',
			idProperty: 'Referral.id',
			baseParams: {
				'get_referrers': true,
				'get_followers': true,
				'type': this.type,
				'limit': 20
			},
			remoteSort: true,
			totalProperty: 'total',
			newestRecordCreated: null,
			listeners: {
				load: function (store, records) {
					if (store.baseParams.type != 'new') {
						return;
					}

					var newestRecordCreated = null;

					Ext.each(records, function (rec) {
						if (rec.get('Referral.created_at') > newestRecordCreated) {
							newestRecordCreated = rec.get('Referral.created_at');
						}
					});

					IOH.APP.resetNewReferrals(newestRecordCreated);
				}
			}
		});

		var pagingToolbar = new Ext.PagingToolbar({
			pageSize: 20,
			store: store,
			displayInfo: true,
			displayMsg: 'Displaying referrals {0} - {1} of {2}',
			emptyMsg: "No referrals found."
		});

		var grid_columns = new Array();
		switch(this.type) {
			case "accepted":
				grid_columns = this.new_columns.concat(this.accepted_columns);
				break;
			case "declined":
				grid_columns = this.new_columns.concat(this.declined_columns);
				break;
			default:
				grid_columns = this.new_columns;
		}

		var cfg = {
			store: store,

			colModel: new Ext.grid.ColumnModel({
				defaults: {
					sortable: true
				},

				columns: grid_columns
			}),

			viewConfig: {
				forceFit: true,
				autoExpandColumn: 'full-name'
			},

			loadMask: true,

			sm: new Ext.grid.RowSelectionModel({}),

			//tbar: [this.createAppointmentAction],

			bbar: pagingToolbar
		};

		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);

		IOH.Triages.Referrals.Grid.superclass.initComponent.apply(this, arguments);

		this.on('activate', this.store.load, this.store);
		this.getStore().on('load', this.onLoad, this);

		this.getSelectionModel().on('selectionchange', function (sm) {
			var count = sm.getCount();
			var selected = sm.getSelected();

			if (count == 1) {
				this.fireEvent('bind', selected.id, selected);
			} else {
				this.fireEvent('unbind');
			}
		}, this);
	},

	onLoad: function(store, records, options) {
		var sm = this.getSelectionModel();
		var count = sm.getCount();
		var selected = sm.getSelected();

		if(count == 1) {
			this.fireEvent('unbind');
			this.fireEvent('bind', selected.id, selected);
		}
	},

	new_columns: [{
		dataIndex: 'Referral.case_reference_number',
		header: 'Ref.No.',
		width: 50
	},{
		dataIndex: 'Referral.private',
		header: 'Private',
		width: 20,
		renderer: function (v, meta, r) {
			return r.json.Referral.private == 1 ? 'Yes' : 'No';
		}
	},{
		dataIndex: 'Person.full_name',
		header: 'Patient',
		id: 'full-name'
	},{
		dataIndex: 'PatientStatus.status',
		header: 'Status',
		width: 50
	},{
		dataIndex: 'ReferralReason.reason',
		header: 'Reason',
		width: 150
	},{
		dataIndex: 'OperationalPriority.operational_priority',
		header: 'Operational Priority',
		width: 90
	},{
		dataIndex: 'Referral.created_at',
		header: 'Created At',
		width: 50,
		renderer: Ext.util.Format.dateRenderer('d/m/Y H:i')
	}],

	accepted_columns: [{
		dataIndex: 'Referral.updated_at',
		header: 'Accepted At',
		width: 120,
		renderer: Ext.util.Format.dateRenderer('d/m/Y H:i')
	},{
		dataIndex: 'Updater.full_name',
		header: 'Accepted By',
		width: 120
	}],

	declined_columns: [{
		dataIndex: 'Referral.updated_at',
		header: 'Declined At',
		width: 120,
		renderer: Ext.util.Format.dateRenderer('d/m/Y H:i')
	},{
		dataIndex: 'Updater.full_name',
		header: 'Declined By',
		width: 120
	}]
});

