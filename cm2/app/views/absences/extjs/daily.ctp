<script type="text/javascript">
function onReady() {
	var absencesStore = new Ext.data.JsonStore({
		url: '<?=$html->url('/sicknotes/dailyPage.json')?>',
		totalProperty: 'totalRows',
		successProperty: 'success',
		root: 'rows',
		fields: [
			{name: 'Employee.salary_number', type: 'string'},
			'Person.first_name', 'Person.last_name',
			'Supervisor.first_name', 'Supervisor.last_name', 'Supervisor.extension',
			'Sicknote.type_code',
			{name: 'Sicknote.start_date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
			{name: 'Sicknote.end_date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
			'Sicknote.symptoms_description', 
			'Sicknote.comments',
			{name: 'Absence.returned_to_work_date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
			'Absence.sick_days', 
			{name: 'Absence.work_related_absence', type: 'boolean'},
			{name: 'Absence.accident_report_completed', type: 'boolean'}, 
			{name: 'Absence.discomfort_report_completed', type: 'boolean'}, 
			'Absence.department_code'
		],
		remoteSort: true,
		listeners: {
			beforeload: function (s, options) {
				options.params = Ext.apply(options.params, this.filterParams);
			}
		}
	});
	
	var absencesGrid = {
		id: 'absences-grid',
		xtype: 'grid',
    	title: 'Daily Sicknotes',
    	region: 'center',
        store: absencesStore,
    	columns:[
	    	{
	           header: "SN",
	           dataIndex: 'Employee.salary_number',
	           width: 65
	        },{
	           header: "Employee",
	           dataIndex: 'Person.first_name',
	           renderer: function (v, meta, r) {
	           		return r.json.Person.first_name + ' ' + r.json.Person.last_name;
	           },
	           width: 100
	        },{
	           header: "Dept",
	           dataIndex: 'Absence.department_code',
	           width: 40
	        },{
	           header: "Sicknote",
	           dataIndex: 'Sicknote.type_code',
	           width: 50
	        },{
	           header: "Start",
	           dataIndex: 'Sicknote.start_date',
	           renderer: Ext.util.Format.dateRenderer(Ext.form.DateField.prototype.format),
	           width: 50
	        },{
	           header: "End",
	           dataIndex: 'Sicknote.end_date',
	           renderer: Ext.util.Format.dateRenderer(Ext.form.DateField.prototype.format),
	           width: 50
	        },{
	           header: "RTW",
	           dataIndex: 'Absence.returned_to_work_date',
	           renderer: Ext.util.Format.dateRenderer(Ext.form.DateField.prototype.format),
	           width: 50
	        },{
	           header: "Days",
	           dataIndex: 'Absence.sick_days',
	           width: 35
	        },{
	           header: "Symptoms",
	           dataIndex: 'Sicknote.symptoms_description',
	           width: 60
	        },{
	           header: "Supervisor",
	           dataIndex: 'Supervisor.first_name',
	           renderer: function (v, meta, r) {
	           		return r.json.Supervisor.first_name + ' ' + r.json.Supervisor.last_name;
	           },
	           width: 100
	        },{
	           header: "Ext",
	           dataIndex: 'Supervisor.extension',
	           width: 30
	        },{
	           header: "WR",
	           dataIndex: 'Absence.work_related_absence',
	           renderer: booleanRenderer,
	           width: 25
	        },{
	           header: "AR",
	           dataIndex: 'Absence.accident_report_completed',
	           renderer: booleanRenderer,
	           width: 25
	        },{
	           header: "WD",
	           dataIndex: 'Absence.discomfort_report_completed',
	           renderer: booleanRenderer,
	           width: 25
	        },{
	           header: "Comments",
	           dataIndex: 'Sicknote.comments',
	           width: 140
	        }
		],
        loadMask: true,
        autoExpandColumn: 8,
        autoScroll: true,
		viewConfig: {
            forceFit1:true,
            autoFill1: true,
            emptyText: 'No absences found.'
        },
		bbar1: new Ext.PagingToolbar({
            pageSize: 25,
            store: absencesStore,
            displayInfo: true,
            displayMsg: 'Displaying absences {0} - {1} of {2}',
            emptyMsg: "No absences found."
        }),
		tbar: [{
			xtype: 'tbtext',
			text: 'Date'
		},{
			id: 'daily-date',
			xtype: 'datefield',
			name: 'date', 
			value: new Date()
		},{
			id: 'show-button',
			text: 'Show',
			handler: function () {
				var d = Ext.getCmp('daily-date').getRawValue();
				Ext.getCmp('absences-grid').load({
					'Sicknote.created': d
				});
			}
		},'-', {
			text: 'Print',
			handler: function () {
				var d = Ext.getCmp('daily-date').getRawValue();
				window.open('/sicknotes/dailyPage/' + d.replace(/\//g, '@'));
			}
		}],
		
		load: function (params) {
			this.store.filterParams = params;
			this.store.load({
				params: {start: 0, limit: 25}
			});
		}
	};
	
    IOH.contentPanel.replace({
    	layout: 'border',
    	hideBorders: true,
    	items: absencesGrid
    });
    
	Ext.getCmp('absences-grid').load({
		'Sicknote.created': Ext.getCmp('daily-date').getRawValue()
	});
}

Ext.onReady(function () {
	try {
		onReady();
	} catch (e) {
		alert(e.message);
	}
});
</script>