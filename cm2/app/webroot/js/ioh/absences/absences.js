IOH.Absences = Ext.extend(Ext.Container, 
{
	initComponent: function () {
		this.absencesGrid = new IOH.Absences.SearchResultsGrid({
	        region: 'center'
		});
		
		this.addAbsencesForm = new IOH.Absences.AbsenceForm({
	    border: true,
			region: 'north',
			split: true,
			autoHeight: true
    	});
		
		this.absencesGrid.getSelectionModel().on('rowselect', function(model, rowIndex, record) {
			this.addAbsencesForm.loadAbsence(record.id);
		}, this);
	
		var config = {
	    	layout: 'border',
	    	border: false,
				hideMode: 'offsets',
	    	items: [
		    	this.addAbsencesForm,
		    	this.absencesGrid
	    	]
		};
	
		Ext.apply(this, config);
		IOH.Absences.superclass.initComponent.apply(this, arguments);
	},
	
	search: function () {
		this.absencesGrid.search(this.addAbsencesForm.getForm().getValues());
	},
	
	merge: function () {
		var absencesGrid = this.absencesGrid;
		var absencesForm = this.addAbsencesForm;
		
		var selection = absencesGrid.getSelectionModel().getSelections();
		var params = {
			person_id: undefined
		};
		
		if (selection.length < 2) {
			alert('Please select two or more absences from the list.');
			return false;
		}
		for (var i = 0; i < selection.length; i++) {
			if (params.person_id == undefined) {
				params.person_id = selection[i].data.person_id;
			} else if (params.person_id != selection[i].data.person_id) {
				alert('Selected absences are not for a single employee.');
				return false;
			}
			params['id['+i+']'] = selection[i].id;
		}
		
		if (!confirm('Do you want to merge the selected ' + selection.length + ' absences?')) {
			return false;
		}
		
		new Ext.data.JsonStore({
			url: '/absences/merge.json',
			root: '',
			fields: [
				'success',
				'errors',
				'new_id'
			],
			listeners: {
				load: function (store) {
					var resp = store.reader.jsonData[0];
					if (!resp.success) {
						alert(resp.errors);
						return;
					}
					
					absencesForm.loadAbsence(resp.new_id);
					absencesGrid.reload();
				},
				loadexception: function () {
					alert('load exception');
				}
			}
		}).load({
    		params: params
		});
	},
	
	reset: function () {
		this.addAbsencesForm.reset();
		this.absencesGrid.reset();

		this.absencesGrid.search(this.addAbsencesForm.getForm().getValues());
	}
});

Ext.reg('IOH.Absences', IOH.Absences);