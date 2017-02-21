Ext.ns('IOH.AttendanceFeedback');

IOH.AttendanceFeedback.Followers = Ext.extend(Ext.Panel,
{
	attendance_id: null,

	initComponent: function () {
		this.store = new Ext.data.DirectStore({
			directFn:Server.AttendanceFeedback.followers,
			fields: [
				{name: 'PersonName', mapping: 'Person.full_name', type: 'string'},
				{name: 'ReferrerType', mapping: 'ReferrerType.type', type: 'string'},
				{name: 'OrganisationName', mapping: 'Organisation.OrganisationName', type: 'string'}
			],
			ref: 'followers-store',
            root:'data'
		});

		var tpl = new Ext.XTemplate(
			'<table class="details" style="width:100%;">',
				'<thead><tr>',
					'<th>Name</th>',
					'<th>Source</th>',
					'<th>Organisation</th>',
				'</tr></thead>',
				'<tpl for="."><tr>',
					'<td>{PersonName}</td>',
					'<td>{ReferrerType}</td>',
					'<td>{OrganisationName}</td>',
				'</tr></tpl>',
			'</table>'
		);

		this.dataView = new Ext.DataView({
				store: this.store,
				tpl: tpl,
				autoHeight:true,
				emptyText: 'No followers to display',
				ref:'dataview'
			});

		var cfg = {
			title: 'Followers',
			hideBorders: true,
			padding: 15,
			id: 'followers-panel',
			ref: 'followers',
			autoScroll: true,
			style: 'background-color: #FFFFFF',
			layout:'fit',
			items: this.dataView
		};

		Ext.apply(this, cfg);

		IOH.AttendanceFeedback.Followers.superclass.initComponent.apply(this, arguments);
	},

	loadFollowers: function(attendance_id) {
		this.attendance_id = attendance_id;
		this.store.setBaseParam('attendance_id', attendance_id);
		this.dataView.hide();
		this.store.reload();

		dataView = this.dataView;
		this.store.on('load', function() {
			dataView.show();
		});
	}
});