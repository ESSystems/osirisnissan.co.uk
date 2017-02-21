Ext.namespace('IOH.Diagnoses');

IOH.Diagnoses.Window = Ext.extend(Ext.Window, {
	initComponent: function () {
		this.diagnosesTree = new IOH.Diagnoses.Tree(this.options);
		
		var cfg = {
	    	title: 'Select Diagnosis',
	        layout: 'fit',
	        width:400,
	        height:450,
	        plain: false,
	        items: this.diagnosesTree,
	        modal: true,
	        buttons: [{
	            text:'Select',
	            handler: function () {
	            	this.onSelect();
	            },
	            scope: this
	        },{
	            text: 'Close',
	            handler: function () {
	            	this.hide();
	            },
	            scope: this
	        }],
	        listeners: {
	        	beforeshow: function (w) {
	        		if (w.options.diagnosisId) {
	        		} else {
	        		}
	        	}
	        }
		};
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Diagnoses.Window.superclass.initComponent.apply(this, arguments);
	},

	onSelect: function () {
		var selection = this.diagnosesTree.getSelection();
		
		var o = Ext.apply({
			nameField: 'Diagnosis.description',
			valueField: 'Attendance.diagnosis_id'
		}, this.options);
		
		if (o.onSelect) {
			o.onSelect(selection);
		} else if (o.targetForm) {
	    	var f = o.targetForm.getForm();
	    	
	    	f.findField(o.nameField).setValue(selection.name);
	    	f.findField(o.valueField).setValue(selection.id);
		}
    	this.hide();
	}
});