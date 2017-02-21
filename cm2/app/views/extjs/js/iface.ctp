Ext.Direct.addProvider({
    actions:<?php echo $javascript->Object($server); ?>,
	type: 'remoting',
	namespace: 'Server',
	enableBuffer: 100,
	url: '/extjs/router'
});