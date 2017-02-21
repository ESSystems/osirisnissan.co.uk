/**
 *
 */
Ext.ns('IOH.referrers');

IOH.referrers.grid = Ext.extend(Ext.grid.GridPanel,
{
	title: 'Referrers',

	initComponent: function() {
		var referrers = new Ext.data.JsonStore({
			url: '/admin/referrers/page.json',
			totalProperty: 'totalRows',
			successProperty: 'success',
			root: 'rows',
			id: 'Referrer.id',
			fields: [
				'Person.full_name',
				'Referrer.email',
				'Referrer.username',
				'Referrer.track_referrals',
				'Organisation.OrganisationName'
			],
			remoteSort: true
		});

		var cfg = {
				store: referrers,
				sm: new Ext.grid.RowSelectionModel({singleSelect: true}),
				columns:[
					{
						header: "Full Name",
						dataIndex: 'Person.full_name',
						renderer: function (v, meta, r) {
							var person_full_name = '';
							if(r.json.Person.first_name !== null) {
								person_full_name += r.json.Person.first_name;
							}
							if(r.json.Person.last_name !== null) {
								person_full_name += r.json.Person.last_name;
							}

							return person_full_name;
						},
						sortable: true
					}, {
						header: "Permission to follow referrals",
						dataIndex: 'Referrer.track_referrals',
						renderer: function (v, meta, r) {
							if(r.json.Referrer.track_referrals !== null) {
								return r.json.Referrer.track_referrals.replace(/_/g, ' ');
							} else {
								return '';
							}
						},
						sortable: true
					}, {
						header: "Referrer username",
						dataIndex: 'Referrer.username',
						sortable: true
					}, {
						header: "Referrer email",
						dataIndex: 'Referrer.email',
						sortable: true
					}, {
						header: "Organisation",
						dataIndex: 'Organisation.OrganisationName',
						sortable: true
					}
				],
				loadMask: true,
				viewConfig: {
					forceFit:true
				},
				bbar: new Ext.PagingToolbar({
					pageSize: 25,
					store: referrers,
					displayInfo: true,
					displayMsg: 'Displaying users {0} - {1} of {2}',
					emptyMsg: "No users"
				})
		};

		Ext.apply(this, cfg);
		IOH.referrers.grid.superclass.initComponent.apply(this, arguments);

		this.getSelectionModel().on('rowselect', function(model, rowIndex, record) {
			this.ownerCt.loadReferrer(record.id);
		}, this);

		referrers.on('beforeload', function (s, options) {
			options.params = Ext.apply(options.params, referrers.filterParams);
		});
	},

	reload: function (filter) {
		if (filter != undefined) {
			for (var i in filter) {
				if (!filter[i]) {
					delete filter[i];
				}
			}
			this.getStore().filterParams = filter;
		}
		this.getStore().load({params:{start:0, limit:25}});
	}
});
