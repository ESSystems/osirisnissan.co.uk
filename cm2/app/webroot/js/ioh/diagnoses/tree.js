Ext.namespace('IOH.Diagnoses');

IOH.Diagnoses.Tree = Ext.extend(Ext.tree.TreePanel, 
{
	border: false,
	
	initComponent: function () {
	    var selection = new Ext.Toolbar.Button({
					text: 'none'
				});
	    
	    var cfg = {
	        rootVisible:false,
	        autoScroll:true,
	        animate: false,
	        tbar: [
	        	'Selection:',
	        	selection
	        ],
	
	        loader: new Ext.tree.TreeLoader({
	            dataUrl: String.format('/diagnoses/view/{0}.json', this.maxId || '')
	        }),
	
	        root: new Ext.tree.AsyncTreeNode({
	            text:'Diagnoses',
	            id: '0'
	        })
	    };
	    
	    Ext.apply(this, cfg);
	    Ext.apply(this.initialConfig, cfg);
		IOH.Diagnoses.Tree.superclass.initComponent.apply(this, arguments);
	    
	    this.getSelectionModel().on('selectionchange', function (selModel, node) {
	    	selection.setText(node.getOwnerTree().getSelection(node).name);
	    });	
	},
	
	getSelection: function (node) {
		if (!node) {
			node = this.getSelectionModel().getSelectedNode();
		}
		
		var result = {
			name: '',
			id: ''
		};
		if (node) {
	    	if (node.parentNode.id != 0) {
	    		result.name += node.parentNode.text + ' > ';
	    	}
	    	result.name += node.text;
	    	result.id = node.id
		}
    	
		return result;
	}
});
