IOH.Absences.SearchResultsGrid = Ext.extend(Ext.grid.GridPanel, 
{
	initComponent: function () {
		var absencesStore = new Ext.data.JsonStore({
			url: '/absences/page.json',
			totalProperty: 'totalRows',
			successProperty: 'success',
			root: 'rows',
			id: 'id',
			fields: [
				'full_name',
				'start_date',
				'end_date',
				'returned_to_work_date',
				'sick_days',
				'department_name',
				'main_diagnosis',
				'person_id'
			],
			remoteSort: true,
			autoLoad: {params:{start:0, limit:25}}
		});
		
		absencesStore.on('beforeload', function (s, options) {
			options.params = Ext.apply(options.params, absencesStore.filterParams);
		});
		
	    var config = {
	        store: absencesStore,
	    	columns:[
		    	{
		           header: "Employee",
		           dataIndex: 'full_name',
		           sortable: true,
		           width: 60
		        },{
		           header: "From",
		           dataIndex: 'start_date',
		           sortable: true,
		           width: 30
		        },{
		           header: "To",
		           dataIndex: 'end_date',
		           sortable: true,
		           width: 30
		        },{
		           header: "Returned",
		           dataIndex: 'returned_to_work_date',
		           sortable: true,
		           width: 30
		        },{
		           header: "Sick Days",
		           dataIndex: 'sick_days',
		           sortable: true,
		           width: 30,
		           align: 'right'
		        },{
		           header: 'Department',
		           dataIndex: 'department_name',
		           width: 60
		        },{
		           header: 'Diagnosis',
		           dataIndex: 'main_diagnosis',
		           width: 160
		        }
			],
	        loadMask: true,
			viewConfig: {
	            forceFit:true,
	            autoFill: true,
	            emptyText: 'No absences found.'
	        },
			bbar: new Ext.PagingToolbar({
	            pageSize: 25,
	            store: absencesStore,
	            displayInfo: true,
	            displayMsg: 'Displaying absences {0} - {1} of {2}',
	            emptyMsg: "No absences found."
	        })
	    };
		
		Ext.apply(this, config);
		IOH.Absences.SearchResultsGrid.superclass.initComponent.apply(this, arguments);
	
		this.subscribe('sicknotesaved', function () {
			this.reload();
		}, this);
		this.subscribe('absencesaved', function () {
			this.getSelectionModel().clearSelections();
			this.reload();
		}, this);
	},
	
	search: function (filter) {
		filter = filter || {};
		for (var i in filter) {
			if (!filter[i]) {
				filter[i] = undefined;
			}
		}
		this.getStore().filterParams = filter;
		this.getStore().load({params:{start:0, limit:25}});
	},

	reload: function () {
    	this.getStore().reload();
    },
    
    reset: function () {
    	this.getSelectionModel().clearSelections();
    }
});
