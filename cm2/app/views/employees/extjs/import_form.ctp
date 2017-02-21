<script type="text/javascript">
Ext.onReady(function () {
	var uploadForm = {
		id: 'import-employees-form',
		xtype: 'form',
		url: '<?=$html->url('/employees/import.json')?>',
		title: 'Import Employees from a CSV file',
		fileUpload: true,
		bodyStyle: 'padding: 5px',
		hideBorders: true,
		items: [
			{
				html: '<p>Please, select the file containing employees data and click "Import".</p>'
			}, 
			{
				xtype: 'field',
				inputType: 'file',
				fieldLabel: 'Employee CSV file',
				name: 'Employee.file'
			}
		],
		buttonAlign: 'left',
		buttons: [
			{
				text: 'Import',
				handler: function () {
					var e = Ext.get('import-employees-form');
					e.mask('Importing employees, please wait ...', 'x-mask-loading');
					Ext.getCmp('import-employees-form').getForm().submit({
						timeout: 10*60,
						success: function () {
							IOH.contentPanel.replace({
								title: 'Success',
								html: '<p>Employee data successfully imported.</p>',
								bodyStyle: 'padding: 5px;'
							});
						},
						
						failure: function () {
							e.unmask(true);
							alert('Failure.');
						}
					});
				}
			}
		]
	};
	
	IOH.contentPanel.replace(uploadForm);
	
});
</script>