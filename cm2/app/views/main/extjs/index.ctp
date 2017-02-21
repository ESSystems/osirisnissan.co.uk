<script type="text/javascript">

Ext.namespace('IOH');

IOH.mainMenuItems = function() {
	var items = new Array();
	
	<?php if ($disableAttendances !== true) : ?>
		items.push(IOH.getAttendancesTree());
	<?php endif; ?>
	<?php if ($disableAbsences !== true) : ?>
		items.push(IOH.getAbsencesTree());
	<?php endif; ?>
	<?php if ($disableSettings !== true) : ?>
		items.push(IOH.getSettingsTree());
	<?php endif; ?>
	<?php if ($disableAdmin !== true) : ?>
		items.push(IOH.getAdminTree());
	<?php endif; ?>
	
	return items;
}

IOH.mainMenuItem = function (options) {
	options = Ext.apply(
		{
	        loader: new Ext.tree.TreeLoader(),
	        rootVisible:false,
	        lines:false,
	        autoScroll:true
		}, options); 
		
	var item = new Ext.tree.TreePanel(options);
    
    item.getSelectionModel().on('selectionchange', function (selModel, node) {
    	if (node) {
    		if (node.attributes.url) {
	    		IOH.load(node.attributes.url);
    		}
	    	node.unselect()
    	}
    });
    
    return item;
}

IOH.getAttendancesTree = function () {
	return new IOH.mainMenuItem({
        title: 'Attendances',
        root: new Ext.tree.AsyncTreeNode({
            children:[{
                text:'Add / Browse',
                leaf:true,
                url: '<?=$html->url('/attendances.extjs')?>'
            }]
        }),
        iconCls:'attendances'
	});
}

IOH.getAbsencesTree = function () {
	return new IOH.mainMenuItem({
        title: 'Absences',
        root: new Ext.tree.AsyncTreeNode({
            children:[{
                text:'Add / Browse',
                leaf:true,
                url: '<?=$html->url('/absences.extjs')?>'
            }, {
                text:'Daily entries',
                leaf:true,
                url: '<?=$html->url('/absences/daily.extjs')?>'
            }, {
                text:'Work related',
                leaf:true,
                url: '<?=$html->url('/absences/workRelated.extjs')?>'
            }]
        }),
        iconCls:'absences'
	});
}

IOH.getSettingsTree = function () {
	return new IOH.mainMenuItem({
        title: 'Settings',
        root: new Ext.tree.AsyncTreeNode({
            children:[{
                text:'Companies',
                leaf:true,
                url: '<?=$html->url('/organisations.extjs')?>'
            }]
        }),
        iconCls:'settings'
	});
}

IOH.getAdminTree = function () {
	return new IOH.mainMenuItem({
        title: 'Administration',
        root: new Ext.tree.AsyncTreeNode({
            children:[{
                text:'Users',
                leaf:true,
                url: '<?=$html->url('/admin/users.extjs')?>'
            },{
                text:'Groups',
                leaf:true,
                url: '<?=$html->url('/admin/groups.extjs')?>'
            },{
                text:'Functions',
                leaf:true,
                url: '<?=$html->url('/admin/funcs.extjs')?>'
            },
            {
            	text: '-----------',
            	leaf: true
            },
            {
            	text: 'Import Employees',
            	leaf: true,
            	url: '<?=$html->url('/employees/importForm.extjs')?>'
            }]
        }),
        iconCls:'admin'
	});
}


IOH.load = function (params) {
	if (typeof params == 'string') {
		params = {
			url: params
		}
	}
	if (!params.target) {
		params.target = 'content-wrapper';
	}
	
	params.scripts = true;
	
	try {
		return Ext.get(params.target).load(params);
	} catch (e) {
		alert('Error');
	}
}

Ext.onReady(function () {
	Ext.BLANK_IMAGE_URL = '/css/extjs/images/default/s.gif';
	
	Ext.QuickTips.init();
	Ext.form.Field.prototype.msgTarget = 'side';
	Ext.apply(Ext.UpdateManager.defaults, {
		loadScripts: true,
		disableCaching: true,
		showLoadIndicator: false
	});
	
	Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
   
    IOH.viewport = new Ext.Viewport({
        layout:'border',
        defaults: {
        	bodyBorder: false
        },
        items:[{ 
            	xtype: 'box', // raw
                region:'north',
                el: 'header',
                height:24
            }, {
		        defaults: {
		        	border: false,
                	bodyStyle: 'padding: 10px'
		        },
                region:'west',
                id:'west-panel',
                title:'Menu',
                split:true,
                width: 200,
                minSize: 175,
                maxSize: 400,
                collapsible: true,
                margins1:'0 0 0 5',
                layout:'accordion',
                layoutConfig:{
                    animate:true
                },
                items: IOH.mainMenuItems()
            }, {
   				id: 'content-panel',
                region:'center',
                layout: 'fit',
            	border: false,
                deferredRender:false,
                items: {
                	contentEl: 'content'
                },

                clear: function () {  
					this.items.each(function (i) {
						IOH.contentPanel.remove(i, true);
					});
				},
				
				replace: function (c) {
					Ext.TaskMgr.stopAll();
					this.clear();
					this.add(c);
				    this.doLayout();
				}
            }
         ]
    });
    
    IOH.contentPanel = Ext.ComponentMgr.get('content-panel');
    
	IOH.showPeopleWindow = function (options) {
    	if (!this.peopleWindow) {
        	this.load({
        		url: '<?=$html->url('/persons/window.extjs')?>',
        		callback: function () {
	            	this.peopleWindow = new IOH.People.Window();
	            	this.peopleWindow.options = options;
	            	this.peopleWindow.show();
        		},
        		scope: this
        	});
    	} else {
        	IOH.peopleWindow.options = options;
        	IOH.peopleWindow.show();
    	}
	}
	
	IOH.showDiagnosesWindow = function (options) {
    	if (!this.diagnosesWindow) {
        	this.load({
        		url: '<?=$html->url('/diagnoses/window.extjs')?>',
        		callback: function () {
	            	this.diagnosesWindow = new IOH.Diagnoses.Window();
	            	this.diagnosesWindow.options = options;
	            	this.diagnosesWindow.show();
        		},
        		scope: this
        	});
    	} else {
        	IOH.diagnosesWindow.options = options;
        	IOH.diagnosesWindow.show();
    	}
	}
	
	IOH.showSicknotesWindow = function (options) {
    	if (!this.sicknotesWindow) {
        	this.load({
        		url: '<?=$html->url('/sicknotes/window.extjs')?>',
        		callback: function () {
	            	this.sicknotesWindow = new IOH.Sicknote.Window();
	            	this.sicknotesWindow.options = options;
	            	this.sicknotesWindow.show();
        		},
        		scope: this
        	});
    	} else {
        	IOH.sicknotesWindow.options = options;
        	IOH.sicknotesWindow.show();
    	}
	}
	
});

</script>

<div id="content-wrapper">
	<div id="content">
		<h1>Welcome <?=$user['Person']['full_name']?></h1>
		
		<div style="overflow: auto; height: 300px;">
		<h2>Groups</h2>
		<ul>
		<?php foreach ($user['Group'] as $group) : ?>
			<li><?php echo $group['group_name'] ?></li>
		<?php endforeach; ?>
		</ul>
		</div>
	</div>
</div>