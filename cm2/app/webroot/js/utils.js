function parseDate(dateString) {
	var m = (new String(dateString)).match(/0*(\d{1,2})\/0*(\d{1,2})\/0*(\d{1,2})/);
	if (m.length != 4) {
		return null;
	}
	var day   = parseInt(m[1]);
	var month = parseInt(m[2]);
	var year  = parseInt(m[3]);

	if (day > 31 || month > 12) {
		return null;
	}
	if (year < 80) {
		year = 2000 + year;
	} else {
		year = 1900 + year;
	}
	

	var result = new Date();

	result.setDate(day);
	result.setMonth(month-1);
	result.setYear(year);
	
	return result;
}

function daysBetween(from, to) {
	return Math.abs(Math.round((from-to)/86400000))	
}

function weekStart(date) {
	var ws;
	if (date) {
		ws = new Date(date);
	} else {
		ws = new Date();
	}
	var wday = (ws.getDay() + 6) % 7;
	var day  = ws.getDate();

	ws.setDate(day-wday);
	
	return ws;
}

function weekEnd(date) {
	var we = weekStart(date);
	
	we.setDate(we.getDate() + 4);
	
	return we;
}

function booleanRenderer(v, meta) {
	if (v) {
		v = 'Y';
		meta.attr = 'style="background-color: green; color: #fff;"';
	} else {
		v = 'N';
		meta.attr = 'style="background-color: #eee"';
	}
	return v;
}