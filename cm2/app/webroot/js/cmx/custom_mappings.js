Ext.ensible.cal.EventMappings = {
    EventId:     {name: 'EventId', mapping:'Appointment.id', type:'string'},
    CalendarId:  {name: 'CalendarId', mapping: 'Appointment.diary_id', type: 'int'},
    Title:       {
        name: 'Title',
        mapping: 'Appointment.title',
        type: 'string',
        convert: function(v, json) {
            if (!v) {
                v = json.Person.full_name;
            }

            return v;
		}
    },
    StartDate:   {name: 'StartDate', mapping: 'Appointment.from_date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    EndDate:     {name: 'EndDate', mapping: 'Appointment.to_date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    RRule:       {name: 'RecurRule', mapping: 'recur_rule'}, // not currently used
    Location:    {name: 'Location', mapping: 'loc', type: 'string'},
    Notes:       {name: 'Notes', mapping: 'Appointment.note', type: 'string'},
    Url:         {name: 'Url', mapping: 'url', type: 'string'},
    IsAllDay:    {name: 'IsAllDay', mapping: 'Appointment.is_all_day', type: 'boolean'},
    Reminder:    {name: 'Reminder', mapping: 'Appointment.remainder', type: 'string'},
    PersonId:	{name: 'PersonId', mapping: 'Appointment.person_id', type: 'int'},
    PersonName:	{name: 'PersonName', mapping: 'Person.full_name', type: 'string'},

    StartTime:	{name: 'StartTime', mapping: 'Appointment.start_time', type: 'string'},
    EndTime:	{name: 'EndTime', mapping: 'Appointment.end_time', type: 'string'},
    Period:		{name: 'Period', mapping: 'Appointment.period', type: 'string'},
    Type:		{name: 'Type', mapping: 'Appointment.type', type: 'string'},
    ReferralId: {name: 'ReferralId', mapping: 'Appointment.referral_id', type: 'int'},
    DiagnosisId:{name: 'DiagnosisId', mapping: 'Appointment.diagnosis_id', type: 'int'}
};

Ext.ensible.cal.EventRecord.reconfigure();

Ext.ensible.cal.CalendarMappings = {
    CalendarId:   {name:'ID', mapping: 'Diary.id', type: 'string'},
    Title:        {name:'CalTitle', mapping: 'Diary.name', type: 'string'},
    Description:  {name:'Desc', mapping: 'Diary.description', type: 'string'},
    ColorId:      {name:'Color', mapping: 'Diary.color_id', type: 'string'},
    IsHidden:     {name:'Hidden', mapping: 'Diary.is_hidden', type: 'boolean'},

    // We can also add some new fields that do not exist in the standard CalendarRecord:
    Owner:        {name: 'Owner', mapping: 'Diary.user_id'},
    DefaultType:	{name: 'DefaultType', mapping: 'Diary.default_appointment_type'},
    NPT:	{name: 'NPT', mapping: 'Diary.is_npt', type: 'boolean'}
};

Ext.ensible.cal.CalendarRecord.reconfigure();