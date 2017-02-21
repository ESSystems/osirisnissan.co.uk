IOH.UsersGrid = Ext.extend(Ext.grid.GridPanel, 
{
	initComponent: function () {
		var usersStore = new Ext.data.JsonStore({
			url: '/admin/users/page.json',
			totalProperty: 'totalRows',
			successProperty: 'success',
			root: 'rows',
			id: 'User.id',
			fields: [
				'Person.full_name',
				'Person.first_name',
				'Person.last_name',
				'User.diary_id',
				'User.clinic_department_id',
				'Status.status_description'
			],
			remoteSort: true
		});
		
		var config = {
			store: usersStore,
			sm: new Ext.grid.RowSelectionModel({singleSelect: true}),
			columns:[
				{
					header: "Full Name",
					dataIndex: 'Person.full_name',
					renderer: function (v, meta, r) {
							return r.json.Person.first_name + ' ' + r.json.Person.last_name;
					},
					sortable: true
				},/*{
					header: "Diary ID?",
					dataIndex: 'User.diary_id',
					sortable: true
				},{
					header: "Clinic Department ID",
					dataIndex: 'User.clinic_department_id',
					sortable: true
				},*/{
					header: "Status",
					dataIndex: 'Status.status_description',
					sortable: true
				}
			],
			loadMask: true,
			viewConfig: {
				forceFit:true
			},
			bbar: new Ext.PagingToolbar({
				pageSize: 25,
				store: usersStore,
				displayInfo: true,
				displayMsg: 'Displaying users {0} - {1} of {2}',
				emptyMsg: "No users"
			})
		};

		Ext.apply(this, config);
		IOH.UsersGrid.superclass.initComponent.apply(this, arguments);
		
		this.getSelectionModel().on('rowselect', function(model, rowIndex, record) {
			this.ownerCt.loadUser(record.id);
		}, this);
		
		usersStore.on('beforeload', function (s, options) {
			options.params = Ext.apply(options.params, usersStore.filterParams);
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