<style type="text/css">

#loading-mask{
    position:absolute;
    left:0;
    top:0;
    width:100%;
    height:100%;
    z-index:20000;
    background-color:white;
}
#loading{
    position:absolute;
    left:45%;
    top:40%;
    padding:2px;
    z-index:20001;
    height:auto;
}
#loading a {
    color:#225588;
}
#loading .loading-indicator{
    background:white;
    color:#444;
    font:bold 13px tahoma,arial,helvetica;
    padding:10px;
    margin:0;
    height:auto;
}
#loading-msg {
    font: normal 10px arial,tahoma,sans-serif;
}

#msg-div {
    position:absolute;
    left:35%;
    top:10px;
    width:250px;
    z-index:20000;
}

</style>

<script type="text/javascript">
function loadProgress(msg) {
	document.getElementById('loading-msg').innerHTML = msg;
}
</script>

<div id="loading-mask" style=""></div>
<div id="loading">
    <div class="loading-indicator">
    	Industrial and Organisational Health<br />
    	<span id="loading-msg">Loading styles and images...</span>
    </div>
</div>

<script type="text/javascript">
	loadProgress('Loading core libraries ...');
</script>

<?php 
	echo $html->css(
		array(
			"../js/{$EXT_BASE}/resources/css/ext-all",
			"../js/{$EXT_BASE}/resources/css/Multiselect",
			"../js/{$EXT_BASE}/resources/css/StatusBar",
			"../js/extensible-1.0.1/resources/css/extensible-all",
			'styles'
		), null, array(), false
	);
?>

<?php echo $javascript->link(
	array(
		"jquery-1.3.2.min",
//		"{$EXT_BASE}/adapter/jquery/jquery-plugins",
		"{$EXT_BASE}/adapter/jquery/ext-jquery-adapter",
		"{$EXT_BASE}/ext-all-debug",
		"{$EXT_BASE}/ux/Multiselect",
		"{$EXT_BASE}/ux/DDView",
		"{$EXT_BASE}/ux/datetime",
		"{$EXT_BASE}/ux/broadcast",
		"{$EXT_BASE}/ux/StatusBar",
//		"{$EXT_BASE}/ux/TreeCombo",
		"{$EXT_BASE}/ux/Ext.ux.TreeCombo",
		"{$EXT_BASE}/ux/xdatefield",
		"extensible-1.0.1/extensible-all-debug",
	)
)?>

<script type="text/javascript">
	Ext.BLANK_IMAGE_URL = '/img/s.gif';
	Ext.ns('IOH');
	loadProgress('Loading application code ...');
</script>

<?php echo $javascript->link('ioh/access'); ?>

<?php echo $javascript->link(
	array(
		'awesomeuploader_v1.3.1/Ext.ux.form.FileUploadField.js',
		'awesomeuploader_v1.3.1/Ext.ux.XHRUpload.js',
		'awesomeuploader_v1.3.1/swfupload.js',
		'awesomeuploader_v1.3.1/swfupload.swfobject.js',
		'awesomeuploader_v1.3.1/AwesomeUploader.js'
	)
); ?>
<?php 
	echo $html->css('../js/awesomeuploader_v1.3.1/Ext.ux.form.FileUploadField.css', null, array(), false);
?>

<script type="text/javascript">
	IOH.mainMenu = <?php echo $javascript->Object($this->requestAction('/users/main_menu')); ?>;
	IOH.USER = new IOH.Access(<?php echo $javascript->Object($session->read('user')); ?>);
	

</script>

<?php 
	echo $javascript->link(
		array(
			'/extjs/iface.js',
			'utils',
			'Cake',
			'ioh/admin/importCSV',
			'ioh/admin/duplicates',
			'ioh/people/combo',
			'ioh/people/summary',
			'ioh/people/Summary/form',
			'ioh/people/Summary/summary',
			'ioh/users/users_grid',
			'ioh/users/users_form',
			'ioh/users/users',
			'ioh/recall/followups',
			'ioh/recall/pending',
			'ioh/recall/history',
			'ioh/recall/index',
			'ioh/recall/list_members',
			'ioh/recall/recall',
			'ioh/recall/recall_invite_form',
			'ioh/organisations',
			'ioh/absences/work_related',
			'ioh/absences/daily',
			'ioh/absences/absences',
			'ioh/absences/search_results_grid',
			'ioh/absences/absence_form',
			'ioh/absences/sicknotes_grid',
			'ioh/absences/all_absences',
			'ioh/absences/all_sicknotes',
			'ioh/attendances',
			'ioh/attendances/pending_grid',
			'ioh/attendances/deleted_grid',
			'ioh/attendances/search_results_grid',
			'ioh/attendances/attendance_form',
			'ioh/attendances/dna_report',
		
			'ioh/diagnoses/tree',
			'ioh/diagnoses/window',
			'ioh/diagnoses/grid',
			'ioh/diaries/appointments_grid',
			'ioh/diaries/next_available_grid',
		    'ioh/diaries/appointment_requests',
			'ioh/diaries/diary',
			'ioh/diaries/appointment_form',
			'ioh/diaries/date_range_field',
			'ioh/diaries/calendar_list',
			'ioh/diaries/diary_form',
			'ioh/diaries/diary_window',
			'ioh/diaries/restrictions/form',
			'ioh/diaries/restrictions/grid',
			'ioh/diaries/restrictions/window',
			'ioh/diaries/referrals/window',
			'ioh/diaries/referrals/form',
		
			'ioh/attendances/appointments',
			'ioh/attendances/appointments/grid',
			'ioh/attendances/appointments/grid/scheduled',
			'ioh/attendances/appointments/form',
			'ioh/attendances/appointments/window',
			'ioh/attendances/appointments/followup_window',
		
			'ioh/attendances/attendance_feedback/form',
			'ioh/attendances/attendance_feedback/window',
			'ioh/attendances/attendance_feedback/followers',
		
			'ioh/notifications',

			'ioh/referrers/form',
			'ioh/referrers/grid',
			'ioh/referrers/page',
		
			'ioh/triages/referrals',
			'ioh/triages/referrals/grid',
			'ioh/triages/referrals/details/referral',
// 			'ioh/triages/referrals/details/new',
// 			'ioh/triages/referrals/details/declined',

			'ioh/declinations/form',
			'ioh/declinations/window',

			'ioh/dashboard',
			'ioh/content',
			'ioh/navigator',
			'ioh/access',
			'ioh/app',
		
			'ioh/lib/file_upload',
			'ioh/lib/hyperlink'
		)
	);
?>

<?php echo $this->requestAction('/persons/window.extjs', array('return'))?>
<?php echo $this->requestAction('/sicknotes/window.extjs', array('return'))?>

<script type="text/javascript">
	loadProgress('Ready!');
</script>