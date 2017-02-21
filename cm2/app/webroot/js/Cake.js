Ext.ns('Cake');

Cake.serialize = function (data) {
	
	var result = {};
	
	function foo(key, val, prefix) {
		main(val, prefix + '[' + key + ']');
	}
	
	function serializeArray(a, prefix) {
		var result;
		
		Ext.each(a, function (item, index) {
			foo(index, item, prefix);
		});
		
		return result;
	}
	
	function serializeObject(obj, prefix) {
		var result;
		
		Ext.iterate(obj, function (key, val) {
			foo(key, val, prefix);
		});
		
		return result;
	}
	
	function main(data, prefix) {
		if (Ext.isArray(data)) {
			serializeArray(data, prefix);
		} else if (Ext.isObject(data)) {
			serializeObject(data, prefix);
		} else if (Ext.isPrimitive(data)) {
			result[prefix] = data;
		}
	}
	
	main(data, 'data');
	
	return result;
	
}

Cake.unflatten = function(obj) {
	var result = {};
	var tmp, p;
	var val;

	for (var prop in obj) {
		val  = obj[prop];
		prop = prop.split('.');
		tmp  = result;

		while(prop.length > 1) {
			p = prop.shift();
			if (typeof tmp[p] != 'object') {
				tmp[p] = {};
			}
			tmp = tmp[p];
		}
		
		p = prop.shift();
		if (!Ext.isObject(val) || !tmp[p]) {
			tmp[p] = val;
		} else {
			for(var i in val) {
				tmp[p][i] = val[i];
			}
		}
	}

	return result;
}

Cake.serializeForm = function (basicForm) {
	return Cake.serializeValues(basicForm.getValues());
}

Cake.serializeValues = function(values) {
	var result = {};
	Ext.iterate(values, function (name, value) {
		result['data[' + name.replace(/\./g, '][') + ']'] = value;
	});
	
	return result;
}
/*
Ext.lib.Ajax.serializeForm = function (form) {
	form = form.dom||form;
	var r = jQuery(form).serialize();

	if (Ext.get(form).hasClass('x-form') && !Ext.get(form).hasClass('hrt-standard-serialize')) {
		r = r.replace(/\+/g, ' ');
		r = Ext.urlDecode(r);
		r = Cake.serializeValues(r);
		r = Ext.urlEncode(r);
	}
	
	return r;
}
*/