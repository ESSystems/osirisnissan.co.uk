<script type="text/javascript">
Ext.onReady(function () {
	var loginForm = new Ext.FormPanel({
		url: '<?=$html->url('/users/doLogin.json')?>',
	    labelWidth: 120,
	    labelAlign: 'right',
		layout: 'form',
	    autoHeight: true,
		bodyStyle: 'padding: 5px;',
		items: [
			{
            	xtype: 'combo',
            	store: new Ext.data.JsonStore({
                	url: '<?=$html->url('/users/combo/A.json')?>',
                	root: 'rows',
                	fields: [
                		'id', 'name'
                	]
            	}),
            	mode: 'remote',
            	forceSelection: true,
			    triggerAction: 'all',
	   	        loadingText: 'Loading ...',
                displayField: 'name',
                valueField: 'id',
            	fieldLabel: 'User',
            	hiddenName: 'User.id',
            	width: 200,
            	cls: 'user icon'
			}, {
				xtype: 'textfield',
				inputType: 'password',
				name: 'User.pass',
				fieldLabel: 'Password',
            	width: 200,
            	cls: 'key icon'
			}
		],
		
		buttonAlign: 'center',
		buttons: [
			{
				text: 'Login',
				handler: function () {
					loginForm.getForm().waitMsgTarget = 'login-form';
					loginForm.getForm().submit({
						success: function (form, action) {
							window.location = '<?=$html->url('/')?>';
						},
						failure: function (form, action) {
							alert(action.result.error);
						},
						waitMsg: 'Logging in ...'
					});
				}
			}
		]
		
	});
	
	new Ext.Panel({
		el: 'login-form',
		title: 'Login',
		layout: 'fit',
		items: loginForm,
	    hideBorders: true,
		autoHeight: true
	}).render();
});
</script>

<style>
	#login-form {
		width: 400px;
		margin: 0 auto;
		text-align: left;
	}
</style>

<div style="text-align: center;margin-top: 100px;">
	<div id="login-form"></div>
</div>