<script type="text/javascript">

Ext.namespace('IOH.People');

IOH.People.Form = Ext.extend(Ext.FormPanel, {
	initComponent: function () {
		var personalDetailsPanel = {
			title: 'Personal Details',
			xtype: 'container',
			autoScroll: true,
			layout: 'column',
			border: false,
			autoScroll: true,
			defaults: {
				columnWidth: 0.50,
				layout: 'form',
				xtype: 'container',
		        defaultType: 'textfield',
				border: false
			},
			items: [{
				items: [{
					fieldLabel: 'Title',
					name: 'Person.title'
				},{
					xtype: 'datefield',
					fieldLabel: 'Date of Birth',
					name: 'Person.date_of_birth',
          width: 125
				},{
					fieldLabel: 'Address 1',
					name: 'Person.address1'	
				},{
					fieldLabel: 'Address 2',
					name: 'Person.address2'	
				},{
					fieldLabel: 'Address 3',
					name: 'Person.address3'	
				},{
					fieldLabel: 'Email',
					name: 'Person.email_address'	
				}]
			},{
				items: [{
					fieldLabel: 'Gender',
					name: 'Person.gender'	
				},{
					fieldLabel: 'Tel. Number',
					name: 'Person.telephone_number'	
				},{
					fieldLabel: 'County',
					name: 'Person.county'	
				},{
					fieldLabel: 'Postcode',
					name: 'Person.post_code'	
				},{
					fieldLabel: 'Area Code',
					name: 'Person.area_code'	
				}]
			}]
	    };	
	    
	    var employeeDetailsPanel = {
	    	id: 'employee-details-tab',
	    	xtype: 'container',
	    	title: 'Employee Details',		
	    	autoScroll: true,
			layout: 'column',
			border: false,
			defaults: {
				columnWidth: 0.50,
				layout: 'form',
				xtype: 'container',
		        defaultType: 'textfield',
				border: false
			},
			items: [{
				items: [{
					xtype: 'hidden',
					name: 'Employee.person_id'	
				},{
					fieldLabel: 'Salary No',
					name: 'Employee.salary_number'	
				},{
					fieldLabel: 'SAP No',
					name: 'Employee.sap_number'	
				},{
					fieldLabel: 'Department',
					name: 'Employee.Department.DepartmentDescription'	
				},{
					fieldLabel: 'Supervisor',
					name: 'Employee.Supervisor.full_name'
				},{
					fieldLabel: 'Supervisor\'s email',
					name: 'Employee.Supervisor.email_address'
				}]
			},{
				items: [{
					fieldLabel: 'Occupational Class',
					name: 'Employee.JobClass.JobClassDescription'	
				},{
					fieldLabel: 'Extension',
					name: 'Person.extension'	
				}]
			}]
	    };
	    
	    var tabPanel = {
	    	xtype: 'tabpanel',
	    	activeTab: 0,
	    	height: 250,
	    	border: false,
	    	deferredRender: false,
	    	labelAlign: 'right',
	    	items: [personalDetailsPanel, employeeDetailsPanel],
	    	bodyStyle: 'padding: 5px;'
	    };

	    var cfg = {
    	    frame1:false,
    	    bodyBorder: false,
    	    labelWidth: 70,
    		layout: 'form',
    		items: [{
    			xtype: 'hidden',
    			name: 'Person.id',
    			value: ''
    		},{
    			xtype: 'hidden',
    			name: 'Person.full_name',
    			readOnly: true
    		},{
    			layout: 'column',
    			labelAlign: 'top',
    			border: false,
    			defaults: {
    				columnWidth: 0.33,
    				border: false,
    				layout: 'form',
    				xtype: 'container'
    			},
    			items: [{
    				columnWidth: 0.34,
    				items: {
    	                xtype:'textfield',
    	                fieldLabel: 'First Name',
    	                name: 'Person.first_name',
    	                anchor: '-20px'
    	            }
    			}, {
    				items:{
    	                xtype:'textfield',
    	                fieldLabel: 'Middle Name',
    	                name: 'Person.middle_name',
    	                anchor: '-20px'
    	            }
    			}, {
    				items:{
    	                xtype:'textfield',
    	                fieldLabel: 'Last Name',
    	                name: 'Person.last_name',
    	                anchor: '-20px'
    				}
    			}]
    		}, {
    			layout: 'form',
    			labelAlign: 'top',
    			xtype: 'container',
    			items: {
    				name: 'Patient.ResponsibleOrganisationID',
                    fieldLabel: 'Organisation',
                    anchor: '-20px',
                	xtype: 'combo',
                	mode: 'remote',
    			    editable: false,
    			    forceSelection: true,
    			    triggerAction: 'all',
    	   	        loadingText: 'Loading ...',
                    store: new Ext.data.JsonStore({
                    	url: '<?=$html->url('/organisations.json')?>',
	                    root: 'rows',
	                    fields: [
	                    	'id', 'name'
	                    ],
	                    autoLoad: true
                    }),
                   	displayField: 'name',
                    valueField: 'id',
            		hiddenName: 'Patient.ResponsibleOrganisationID',
                    allowBlank:false
    			}
    		},
	    	tabPanel],
    		bodyStyle: 'padding: 10px',
    		buttons: [{
    			id: 'save-button',
    			text: 'Save',
    	        handler: function () {
    	        	this.getForm().submit({
    	        		url: '<?=$html->url('/persons/save.json')?>',
    	        		success: function (f, action) {
    	        			var result = action.result;
    	        			f.findField('Person.id').setValue(result.id);
    	        			this.store.reload({'Person.id':result.id});
    	        		},
    	        		scope: this
    	        	});
    	        },
    	        scope: this
    		}, {
    			text: 'Reset',
    			handler: function () {
    				this.resetForm();
    			},
    			scope: this
    		}, new Ext.Toolbar.Fill(), {
    			id: 'find-button',
    			text: 'Find',
    			handler: function () {
    				var vals = this.getForm().getValues();
    				if (typeof this.options.skipLeavers != 'undefined') {
        				vals['Option.skip_leavers'] = (this.options.skipLeavers?'1':'0');
    				}
    				if (typeof this.options.employeesOnly != 'undefined') {
        				vals['Option.employees_only'] = (this.options.employeesOnly?'1':'0');
    				}
    				
    				this.store.reload(vals);
    			},
    			scope: this
    		}]
		};
	
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
	
		IOH.People.Form.superclass.initComponent.apply(this, arguments);
	},
	
	resetForm: function () {
		this.getForm().reset();
		this.store.removeAll();
		this.updateMode();
	},
	
	loadPerson: function (personId) {
		this.getForm().reset();
		this.getForm().waitMsgTarget = this.getEl();

		this.load({
			url: '<?=$html->url('/persons/load')?>/' + personId + '.json',
			scripts: false,
			waitMsg: 'Loading person data ...',
			success: function (form, action) {
				this.updateMode();
			},
			scope: this
		});
	},
	
	updateMode: function () {
		// Modes are:
		// 1. not empty person id, empty employee id -> Edit Mode;
		// 2. not empty person id, not empty employee id -> ReadOnly Mode
		// 3. empty person id -> Add/Search Mode

		// Mode affects:
		// 1. Form title
		// 2. Save/Find buttons enabled state;
		// 3. Form fields readOnly property

		if(!this.getForm().findField('Person.id')) {
			return;
		}
		
		var personId = this.getForm().findField('Person.id').getValue();
		var readOnly = false;
		
		var saveButton = this.buttons[0];
		var findButton = this.buttons[2];
		
		if (personId) {
			// Edit or ReadOnly mode
			var employeeId = this.getForm().findField('Employee.person_id').getValue();
			findButton.enable();
			if (employeeId) {
				readOnly = true;
				this.findById('employee-details-tab').enable();
				saveButton.disable();
				this.setTitle('Select Person');
			} else {
				readOnly = false;
				this.findById('employee-details-tab').disable();
				saveButton.enable();
				this.setTitle('Edit Person');
			}
		} else {
			// Add/Search Mode
			this.findById('employee-details-tab').enable();
			saveButton.enable();
			findButton.enable();
			this.setTitle('Select / Add Person');
			this.findById('employee-details-tab').enable();
			readOnly = false;
		}
		
//		this.getForm().items.each(function(f){
//			f.el.dom.readOnly = readOnly;
//		});
	}
});

</script>