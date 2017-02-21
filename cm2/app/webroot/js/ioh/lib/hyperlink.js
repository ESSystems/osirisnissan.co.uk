IOH.Hyperlink = Ext.extend(Ext.BoxComponent, {
	initComponent: function() {
		Ext.apply(this, {
			html: '<a href="javascript:void(0)" ' + this.extra + '>' + this.text + '</a>',
			listeners: {
				render: function(c) {
					c.getEl().on('mousedown', function() {
						this.el.child('a', true).href = this.href;
					}, c, {
						stopEvent: false
					});
				}
			}
		});

		IOH.Hyperlink.superclass.initComponent.call(this);
	}
});

Ext.reg('IOH.Hyperlink', IOH.Hyperlink);