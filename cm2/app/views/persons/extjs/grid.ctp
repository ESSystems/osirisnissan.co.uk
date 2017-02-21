<script type="text/javascript">

Ext.namespace('IOH.People');

IOH.People.Store = function () {
	IOH.People.Store.superclass.constructor.call(this, {
		url: '<?=$html->url('/persons/page.json')?>',
		totalProperty: 'totalRows',
		successProperty: 'success',
		root: 'rows',
		id: 'Person.id',
		fields: [{
			name: 'Person.full_name'
		},{
			name: 'Patient.Organisation.OrganisationName',
			mapping: 'Patient',
			convert: function (v) {
				if (v && v.Organisation && v.Organisation.OrganisationName) {
					return v.Organisation.OrganisationName;
				}
				return '-';
			}
		},{
			name: 'Employee.salary_number'
		},{
			name: 'Employee.leaver'
		}],
		remoteSort: true
	});
	
	this.on('beforeload', function (s, options) {
		options.params = Ext.apply(options.params, this.filterParams);
	}, this);
}

Ext.extend(IOH.People.Store, Ext.data.JsonStore, {
	reload : function (filter) {
		for (var i in filter) {
			if (!filter[i]) {
				filter[i] = undefined;
			}
		}
		this.filterParams = filter;
	    this.load({params:{start:0, limit:25}});
	}
});

IOH.People.Grid = Ext.extend(Ext.grid.GridPanel, {
	initComponent: function () {
		var cfg = {
			columns: [{
				header: 'Name',
				dataIndex: 'Person.full_name'
			},{
				header: 'Organisation',
				dataIndex: 'Patient.Organisation.OrganisationName'
			},{
				header: 'Leaver',
				dataIndex: 'Employee.leaver',
				width: 35
			}],
			bodyBorder: false,
			loadMask: true,
	        viewConfig: {
	            autoFill:true
	        },
	        bbar: new Ext.PagingToolbar({
	            pageSize: 25,
	            store: this.store,
	            displayInfo: true,
	            displayMsg: 'Displaying matches {0} - {1} of {2}',
	            emptyMsg: "No people found."
	        })			
		};

		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);

		IOH.People.Grid.superclass.initComponent.apply(this, arguments);
	}
});
</script>