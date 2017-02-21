IOH.Absences.SicknotesGrid = Ext.extend(Ext.grid.GridPanel, 
{
	initComponent: function () {
		var sicknotesStore = new Ext.data.JsonStore({
			url: '/sicknotes/page.json',
			totalProperty: 'totalRows',
			successProperty: 'success',
			root: 'rows',
			id: 'id',
			fields: [
				'id',
				'type_code',
				'type_name',
				'start_date',
				'end_date',
				'symptoms_description',
				'comments',
				'created'
			],
			remoteSort: true
		});
		
		var config = {
			id: 'sicknotes-grid',
	    	xtype: 'grid',
	    	store: sicknotesStore,
	    	width: 300,
	    	height: 200,
	    	border: true,
	    	columns: [{
				header: 'ID',
				dataIndex: 'id',
				sortable: true,
				width: 50
	    	},{
				header: 'Type',
				dataIndex: 'type_name',
				sortable: true
	    	}, {
				header: 'From',
				dataIndex: 'start_date',
				sortable: true,
				width: 60
	    	}, {
				header: 'To',
				dataIndex: 'end_date',
				sortable: true,
				width: 60
	    	}],
	        bbar: [{
	    		text: 'Add',
	    		id: 'sicknote-add-button',
	    		disabled: false,
	    		handler: function () {
	    			var form = this._getForm();
	    			var options = {
	    				absenceId: form.findField('Absence.id').getValue(),
	    				personId: form.findField('Person.id').getValue()
	    			};
	    			
	    			if (!options.personId) {
	    				alert('Select an employee first.');
	    				return;
	    			}
	    			
	    			IOH.APP.showSicknotesWindow(options);
	    		},
	    		scope: this
	    	},{
	        	id: 'sicknote-edit-button',
	    		text: 'Edit',
	    		disabled: true,
	    		handler: function () {
	    			var options = {
						absenceId: this._getForm().findField('Absence.id').getValue()
	    			};
	    			var selected = this.getSelectionModel().getSelected();
	    			if (selected) {
	    				options.sicknoteId = selected.id;
	    			}
					IOH.APP.showSicknotesWindow(options);
	    		},
	    		scope: this
	    	}, {
	        id: 'sicknote-remove-button',
	    		text: 'Remove',
	    		disabled: true,
	    		handler: function () {
	    			var selected = this.getSelectionModel().getSelected();
						var form = this._getForm();
						if (!selected) {
							return;
						}
	    			if (this.getStore().getTotalCount() <= 1) {
	    				alert('You cannot remove all sick notes of an absence.');
	    				return;
	    			}
	    			if (!confirm('Are you sure you want to delete this sicknote?')) {
	    				return;
	    			}
	    			Ext.Ajax.request({
						url: '/sicknotes/delete.json',
						params: {
							'Sicknote.id': selected.id
						},
						success: function () {
							sicknotesStore.reload();
							form.publish('sicknotedeleted', form.findField('Absence.id').getValue(), true);
							// addAbsencesForm.reload();
							// absencesGrid.reload();
						},
						failure: function () {
							alert('Remove failed.');
						}
					});
	    		},
	    		scope: this
	    	}]
	    };
	    
		Ext.apply(this, config);
		IOH.Absences.SicknotesGrid.superclass.initComponent.apply(this, arguments);
	
	    this.getSelectionModel().on('selectionchange', function (selModel) {
	    	Ext.getCmp('sicknote-edit-button').setDisabled(false || !selModel.hasSelection());
	    	Ext.getCmp('sicknote-remove-button').setDisabled(false || !selModel.hasSelection());
	    });
	}, 
	
	_getForm: function () {
		return this.ownerCt.ownerCt.getForm();
	}
});
