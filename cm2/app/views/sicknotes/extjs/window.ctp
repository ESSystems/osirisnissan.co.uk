<?=$this->requestAction('/sicknotes/form.extjs', array('return'))?>

<script type="text/javascript">

Ext.namespace('IOH.Sicknote');

IOH.Sicknote.Window = Ext.extend(Ext.Window, 
{
	constructor: function () {
		Ext.apply(this, {
	        listeners: {
	        	show: function (w) {
	        		if (w.options.sicknoteId) {
	        			w.sicknoteForm.loadSicknote(w.options.sicknoteId);
	        		} else {
	        			w.sicknoteForm.resetForm();
		        		w.sicknoteForm.getForm().findField('Sicknote.absence_id').setValue(w.options.absenceId);
		        		w.sicknoteForm.getForm().findField('Absence.person_id').setValue(w.options.personId);
	        		}
	        	}
	        }
		});
		
		IOH.Sicknote.Window.superclass.constructor.apply(this, arguments);
	},
	
	initComponent: function () {
		this.sicknoteForm = new IOH.Sicknote.Form();

		var config = {
	    	title: 'Sicknote',
	        layout: 'border',
	        width:600,
	        height:330,
	        closeAction:'hide',
	        plain: false,
	        hideBorders: true,
	        items: [{
        		region: 'center',
        		layout: 'fit',
        		items: this.sicknoteForm
	        }],
	        buttons: [{
				id: 'save-button',
				text: 'Save',
		        handler: function () {
		        	var store  = this.sicknoteForm.getForm().findField('Sicknote.diagnoses').store;
		        	var params = {};
		        	var count  = store.getCount();
		        	var rec;
		        	
		        	for (var i = 0; i < count; i++) {
		        		params['data[Diagnosis][Diagnosis][' + i + ']'] = store.getAt(i).data.id;
		        	}
		        	this.sicknoteForm.getForm().submit({
		        		url: '<?=$html->url('/sicknotes/save.json')?>',
		        		params: params,
		        		success: function (f, action) {
		        			this.publish('sicknotesaved', action.result.absence_id, true);
		        			this.hide();
		        		},
		        		scope: this
		        	});
		        },
		        scope: this
			}, {
	            text: 'Close',
	            handler: function () {
	            	this.hide();
	            },
	            scope: this
	        }]
		};

		Ext.apply(this, config);
	
		IOH.Sicknote.Window.superclass.initComponent.apply(this, arguments);
	},
	
	onSelect: function () {
	}
});
</script>