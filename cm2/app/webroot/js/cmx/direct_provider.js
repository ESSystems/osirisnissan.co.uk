Ext.Direct.addProvider({
    actions:{"Cmx": [
                     {"name":"direct_diaries_index","len":1,"formHandler":false},
                     {"name":"direct_index","len":1,"formHandler":false},
                     {"name":"direct_save","len":1,"formHandler":false}
                     ]
	},
	type: 'remoting',
	namespace: 'Server',
	enableBuffer: 100,
	url: '/extjs/router'
});