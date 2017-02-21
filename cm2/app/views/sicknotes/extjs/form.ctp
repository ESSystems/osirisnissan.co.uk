<?php
	$jsonSicknoteTypesList = array();
	foreach ($sicknoteTypes as $code=>$desc) {
		$jsonSicknoteTypesList[] = array($code, $desc);
	}
?>

<script type="text/javascript">

Ext.namespace('IOH.Sicknote');

IOH.Sicknote.Form = Ext.extend(Ext.form.FormPanel, 
{
	initComponent: function () {
		var t = this;
		var self = this;

		var sicknotePanel = {
			autoScroll: true,
			layout: 'column',
			labelAlign: 'top',
			bodyStyle: 'padding: 5px;',
			border: false,
			autoScroll: true,
			items: [{
					xtype: 'hidden',
					name: 'Sicknote.absence_id'
				},{
					xtype: 'hidden',
					name: 'Sicknote.id'
				},{
					xtype: 'hidden',
					name: 'Absence.person_id'
				},{
					width: 275,
					layout: 'form',
					border: false,
					hideBorders: true,
					items: [
						{
							xtype: 'combo',
							fieldLabel: 'Type',
							hiddenName: 'Sicknote.type_code',
						    mode: 'local',
						    width: 250,
						    triggerAction: 'all',
						    editable: false,
							store: new Ext.data.SimpleStore({
								fields: ['code', 'description'],
								data: <?=$javascript->object($jsonSicknoteTypesList)?>
							}),
							displayField: 'description',
							valueField: 'code'
						},{
							layout: 'column',
							width: 300,
							xtype: 'panel',
							hideBorders: true,
							items: [{
									columnWidth: 0.50,
									layout: 'form',
									items:[{
										xtype: 'datefield',
										fieldLabel: 'From Date',
										name: 'Sicknote.start_date',
										width: 100,
										listeners: {
											change: function () {
												t.updateSickDays();
											}
										}
									}]
								}, {
									columnWidth: 0.50,
									layout: 'form',
									items:[{
										xtype: 'datefield',
										fieldLabel: 'To Date',
										name: 'Sicknote.end_date',
										width: 100,
										listeners: {
											change: function () {
												t.updateSickDays();
											}
										}
									}]
								}
							]
						},{
							xtype: 'textfield',
							fieldLabel: 'Sick days',
							name: 'Sicknote.sick_days',
							width: 250
						},{
							xtype: 'textarea',
							fieldLabel: 'Symptoms',
							name: 'Sicknote.symptoms_description',
							height: 77,
							width: 250
						}
					]
				},{
					width: 275,
					layout: 'form',
		            defaultType: 'textfield',
					border: false,
					hideBorders: true,
					items: [{
							xtype: 'multiselect',
							legend: 'Diagnoses',
							hideLabel: true,
							name:'Sicknote.diagnoses',
							store: new Ext.data.JsonStore({
								url:'<?=$html->url('/sicknotes/diagnoses.json')?>',
								successProperty: 'success',
								totalProperty: 'totalRows',
								root: 'rows',
								id: 'id',
								fields: [
									'id',
									'description'
								]
							}),
							valueField:'id',
							displayField:"description",
							width: 250,
							height:130,
							allowBlank:true,
							tbar: [{
								text: 'Add',
								handler: function () {
									IOH.APP.showDiagnosesWindow({
										onSelect: function (selection) {
											
									    	var form = self.getForm();
									    	var diagnoses = form.findField('Sicknote.diagnoses');
											diagnoses.store.add([new Ext.data.Record({id: selection.id, description: selection.name})]);
										},
										maxId: 74
									});
								}
							},{
								text: 'Remove',
								handler: function () {
							    	var form = self.getForm();
							    	var diagnoses = form.findField('Sicknote.diagnoses');
							    	var records   = diagnoses.view.getRecords(diagnoses.view.getSelectedNodes());
							    	
							    	for (var i = 0; i < records.length; i++) {
							    		diagnoses.store.remove(records[i]);
							    	}
								}
							}]
						},{
							xtype: 'textarea',
							fieldLabel: 'Comments',
							name: 'Sicknote.comments',
							height: 77,
							width: 250
						}
					]
				}
			]
	    };	

		var config = {
		    frame:false,
		    border: false,
		    bodyBorder: false,
		    hideBorders: true,
		    labelWidth: 70,
			layout: 'form',
			items: sicknotePanel,
			bodyStyle: 'padding: 5px'
		};

		Ext.apply(this, config);

		IOH.Sicknote.Form.superclass.initComponent.apply(this, arguments);

	},
	
	updateSickDays: function () {
		var form      = this.getForm();
		var startDate = form.findField('Sicknote.start_date').getValue();
		var endDate   = form.findField('Sicknote.end_date').getValue();
		
		if (!startDate || !endDate) {
			return;
		}
		
		var sickDays = daysBetween(startDate, endDate) + 1;
		
		form.findField('Sicknote.sick_days').setValue(sickDays);
	},

	loadSicknote: function (id) {
		this.getForm().waitMsgTarget = this.getEl();

		this.load({
			url: '<?=$html->url('/sicknotes/load')?>/' + id + '.json',
			scripts: false,
			waitMsg: 'Loading sicknote data ...',
			success: function (form, action) {
				form.findField('Sicknote.diagnoses').store.load({params: {id: id}});
			},
			failure: function (form, action) {
				alert('failure');
			},
			scope: this
		});
	},
	
	resetForm: function () {
		this.getForm().findField('Sicknote.diagnoses').store.removeAll();
		this.getForm().reset();
	}
});

</script>