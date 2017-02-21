Ext.ux.TreeCombo = Ext.extend(Ext.form.TriggerField, {

    triggerClass: 'x-form-tree-trigger',

    initComponent : function(){
        this.readOnly = true;
        Ext.ux.TreeCombo.superclass.initComponent.call(this);
        this.on('specialkey', function(f, e){
            if(e.getKey() == e.ENTER){
                this.onTriggerClick();
            }
        }, this);
        this.getTree();
    },

    onRender : function(ct, position){
        Ext.form.ComboBox.superclass.onRender.call(this, ct, position);
        
        if(this.hiddenName){
            this.hiddenField = this.el.insertSibling({tag:'input', type:'hidden', name: this.hiddenName,
                    id: (this.hiddenId||this.hiddenName)}, 'before', true);

            // prevent input submission
            this.el.dom.removeAttribute('name');
        }
    },
    
    onTriggerClick: function() {
        this.getTree().show();
        this.getTree().getEl().alignTo(this.wrap, 'tl-bl?');
    },

    getTree: function() {
        if (!this.treePanel) {
            if (!this.treeWidth) {
                this.treeWidth = Math.max(200, this.width || 200);
            }
            if (!this.treeHeight) {
                this.treeHeight = 200;
            }
            this.treePanel = new Ext.tree.TreePanel({
                renderTo: Ext.getBody(),
                loader: this.loader || new Ext.tree.TreeLoader({
                    preloadChildren: (typeof this.root == 'undefined'),
                    url: this.dataUrl || this.url
                }),
                root: this.root || new Ext.tree.AsyncTreeNode({children: this.children}),
                rootVisible: (typeof this.rootVisible != 'undefined') ? this.rootVisible : (this.root ? true : false),
                floating: true,
                autoScroll: true,
                minWidth: 200,
                minHeight: 200,
                useArrows:true,
                width: this.treeWidth,
                height: this.treeHeight,
                listeners: {
                    hide: this.onTreeHide,
                    show: this.onTreeShow,
                    click: this.onTreeNodeClick,
                    scope: this
                }
            });
            this.treePanel.show();
            this.treePanel.hide();
            this.relayEvents(this.treePanel.loader, ['beforeload', 'load', 'loadexception']);
            if(this.resizable){
                this.resizer = new Ext.Resizable(this.treePanel.getEl(),  {
                   pinned:true, handles:'se'
                });
                this.mon(this.resizer, 'resize', function(r, w, h){
                    this.treePanel.setSize(w, h);
                }, this);
            }
        }
        return this.treePanel;
    },

    onTreeShow: function() {
        Ext.getDoc().on('mousewheel', this.collapseIf, this);
        Ext.getDoc().on('mousedown', this.collapseIf, this);
    },

    onTreeHide: function() {
        Ext.getDoc().un('mousewheel', this.collapseIf, this);
        Ext.getDoc().un('mousedown', this.collapseIf, this);
    },

    collapseIf : function(e){
        if(!e.within(this.wrap) && !e.within(this.getTree().getEl())){
            this.collapse();
        }
    },

    collapse: function() {
        this.getTree().hide();
        if (this.resizer) {
        	this.resizer.resizeTo(this.treeWidth, this.treeHeight);
        }
    },

    // private
    validateBlur : function(){
        return !this.treePanel || !this.treePanel.isVisible();
    },

    setValue: function(v) {
    	if (typeof v == 'undefined') {
    		v = '';
    	}
        this.startValue = this.value = v;
        if(this.hiddenField){
            this.hiddenField.value = v;
        }
        if (this.treePanel) {
            var n = this.treePanel.getNodeById(v);
            if (n) {
                this.setRawValue(n.text);
            } else {
            	var child = false;
                Ext.each(this.treePanel.root.childNodes, function (node) {
                	child = this._findChild(node.attributes, v);
                	if (child) {
                		return false;
                	}
                }, this);
                
                if (child) {
                	this.setRawValue(child.text);
                } else {
                	this.setRawValue('');
                }
            }
        }
    },
    
    _findChild: function (parent, id) {
		var found = false;

		if (parent.children) {
    		Ext.each(parent.children, function (node) {
    			if (node.id == id) {
    				found = node;
    				return false;
    			} else {
    				found = this._findChild(node, id);
    				if (found) {
    					return false;
    				}
    			}
    		}, this);
    	}
		
		return found;
    },

    getValue: function() {
        return this.value;
    },

    onTreeNodeClick: function(node, e) {
        this.setValue(node.id);
        this.fireEvent('select', this, node);
        this.collapse();
    }
});

Ext.reg('treecombo', Ext.ux.TreeCombo);