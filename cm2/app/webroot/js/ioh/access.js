IOH.Access = function ($user) {
	for (var i in $user.User) {
		this[i] = $user.User[i];
	}
	
	this.groups = [];
	
	for (var i = 0; i < $user.Group.length; i++) {
		this.groups.push($user.Group[i].group_name);
	}
	
	this.belongsToGroup = function (groups) {
		var result = false;
		
		if (this.groups.indexOf('Admin') != -1) {
			return true;
		}
		
		Ext.each(groups, function (group) {
			if (this.groups.indexOf(group) != -1) {
				result = true;
				return false;
			}
		}, this);
		
		return result;
	};
}