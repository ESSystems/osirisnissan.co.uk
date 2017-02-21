IOH.DiagnosesGrid = Ext.extend(IOH.Diagnoses.Tree, {
	border: true,
	maxId: -1,
	listeners: {
		checkchange: function(node, checked){
		    if(checked){
				Server.Diagnoses.direct_show([node.id], function (response) {
					if (response.success) {
						node.getUI().removeClass('obsolete');
						node.setText(node.text.replace(' (obsolete)', ''));
					}
				});
		    }else{
				Server.Diagnoses.direct_hide([node.id], function (response) {
					if (response.success) {
						node.getUI().addClass('obsolete');
						node.setText(node.text + ' (obsolete)');
					}
				});
		    }
		}
	}
});

Ext.reg('IOH.DiagnosesGrid', IOH.DiagnosesGrid);