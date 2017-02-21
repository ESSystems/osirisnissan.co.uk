(function() {
Ext.override(Ext.ensible.cal.DateRangeField, {
	// checkDates: function(type, startend) {
	// 	startField = Ext.getCmp(this.id + '-start-' + type);
	// 	endField = Ext.getCmp(this.id + '-end-' + type);

	// 	newStartDate = new Date();
	// 	newEndDate = new Date();

	// 	rec = this.record;

	// 	oldStartTime = rec.StartDate.getTime();
	// 	oldEndTime = rec.EndDate.getTime();

	// 	diff = oldEndTime - oldStartTime;

	// 	newStartTime = this.getDT('start').getTime();
	// 	newEndTime = this.getDT('end').getTime();

	// 	switch (startend) {
	// 		case 'start':
	// 			newStartDate.setTime(newStartTime);
	// 			newEndDate.setTime(newStartTime + diff);
	// 			break;
	// 		case 'end':
	// 			newStartDate.setTime(newEndTime - diff);
	// 			newEndDate.setTime(newEndTime);
	// 			break;
	// 	}

	// 	startField.setValue(newStartDate);
	// 	endField.setValue(newEndDate);
	// },

	onRender: function(ct, position) {
		if(!this.el){
            this.startDate = new Ext.form.DateField({
                id: this.id+'-start-date',
                format: this.dateFormat,
                width:100,
                listeners: {
                    'select': {
                        fn: function(){
                            this.onFieldChange('date', 'start');
                        },
                        scope: this
                    }
                }
            });
            this.startTime = new Ext.form.TimeField({
                id: this.id+'-start-time',
                hidden: this.showTimes === false,
                labelWidth: 0,
                hideLabel:true,
                width:90,
                listeners: {
                    'select': {
                        fn: function(){
                            this.onFieldChange('time', 'start');
                        },
                        scope: this
                    }
                }
            });
            this.endTime = new Ext.form.TimeField({
                id: this.id+'-end-time',
                hidden: this.showTimes === false,
                labelWidth: 0,
                hideLabel:true,
                width:90,
                listeners: {
                    'select': {
                        fn: function(){
                            this.onFieldChange('time', 'end');
                        },
                        scope: this
                    }
                }
            })
            this.endDate = new Ext.form.DateField({
                id: this.id+'-end-date',
                format: this.dateFormat,
                hideLabel:true,
                width:100,
                listeners: {
                    'select': {
                        fn: function(){
                            this.onFieldChange('date', 'end');
                        },
                        scope: this
                    }
                }
            });
            this.allDay = new Ext.form.Checkbox({
                id: this.id+'-allday',
                hidden: this.showTimes === false || this.showAllDay === false,
                boxLabel: this.allDayText,
                handler: function(chk, checked){
                    this.startTime.setVisible(!checked);
                    this.endTime.setVisible(!checked);
                },
                scope: this
            });
            this.toLabel = new Ext.form.Label({
                xtype: 'label',
                id: this.id+'-to-label',
                text: this.toText
            });
            
            var singleLine = this.singleLine;
            if(singleLine == 'auto'){
                var el, w = this.ownerCt.getWidth() - this.ownerCt.getEl().getPadding('lr');
                if(el = this.ownerCt.getEl().child('.x-panel-body')){
                    w -= el.getPadding('lr');
                }
                if(el = this.ownerCt.getEl().child('.x-form-item-label')){
                    w -= el.getWidth() - el.getPadding('lr');
                }
                singleLine = w <= this.singleLineMinWidth ? false : true;
            }
            
            this.fieldCt = new Ext.Container({
                autoEl: {id:this.id}, //make sure the container el has the field's id
                cls: 'ext-dt-range',
                renderTo: ct,
                layout: 'table',
                layoutConfig: {
                    columns: singleLine ? 6 : 3
                },
                defaults: {
                    hideParent: true
                },
                items:[
                    this.startDate,
                    this.startTime,
                    this.toLabel,
                    singleLine ? this.endTime : this.endDate,
                    singleLine ? this.endDate : this.endTime,
                    this.allDay
                ]
            });
            
            this.fieldCt.ownerCt = this;
            this.el = this.fieldCt.getEl();
            this.items = new Ext.util.MixedCollection();
            this.items.addAll([this.startDate, this.endDate, this.toLabel, this.startTime, this.endTime, this.allDay]);
        }
        
        Ext.ensible.cal.DateRangeField.superclass.onRender.call(this, ct, position);
        
        if(!singleLine){
            this.el.child('tr').addClass('ext-dt-range-row1');
        }
	},

	// setValue: function(v) {
	// 	this.record = v;
	// }.createSequence(Ext.ensible.cal.DateRangeField.prototype.setValue)
});
})();