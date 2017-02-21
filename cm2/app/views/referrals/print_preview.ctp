<?php $html->css('print_preview', null, array(), false); ?>

<h1>Patient: <?php echo $referral['Person']['full_name'] . " (" . $referral['PatientStatus']['status'] . ")" ?></h1>
<table>
	<thead>
		<tr>
			<th>Date of Birth</th>
			<th>SAP / Salary Number</th>
			<th>Department at time of referral</th>
			<th>Supervisor</th>
		</tr>
  	</thead>
	<tbody>
		<tr>
	    	<td><?php echo date("d F Y", strtotime($referral['Person']['date_of_birth'])); ?></td>
	    	<td><?php if(isset($referral['Person']['Employee'])) { ?>
	    		<?php echo $referral['Person']['Employee']['sap_number'] ?> / <?php echo $referral['Person']['Employee']['salary_number'] ?>
	    	<?php } ?></td>
	    	<td><?php 
	    		if(isset($referral['Referral']['person_department_name']) && $referral['Referral']['person_department_name'] != '') {
	    			echo $referral['Referral']['person_department_name'];
	    		} else if(isset($referral['Person']['Employee']['Department'])) {
	    			echo $referral['Person']['Employee']['Department']['DepartmentDescription'];
	    		}
	    	?></td>
	    	<td><?php if(isset($referral['Person']['Employee']['Supervisor'])) { ?>
	    		<?php echo $referral['Person']['Employee']['Supervisor']['full_name'] ?>
	    	<?php } ?></td>
	  	</tr>
	</tbody>
</table>
<h2>Referral Details</h2>
<table>
	<thead>
		<tr>
			<th style="width:31%">Case details</th>
			<th style="width:23%">Case Nature</th>
			<th style="width:23%">Job Information</th>
			<th style="width:23%">History</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<p><b>Reason:</b> <?php echo $referral['ReferralReason']['reason']; ?></p>
				<p><b>Case Referrence Number:</b> <?php echo $referral['Referral']['case_reference_number'] ?></p>
				<p><b>Date sickness absence started:</b> <?php echo $referral['Referral']['sickness_started'] ?></p>
				<p><b>Date current sickness expires:</b> <?php echo $referral['Referral']['sicknote_expires'] ?></p>
				<p><b>Operational Priority:</b> <?php echo $referral['OperationalPriority']['operational_priority'] ?></p>
				<p><b>Created at:</b> <?php echo $referral['Referral']['created_at'] ?></p> 
			</td>
			<td><?php echo $referral['Referral']['case_nature'] ?></td>
			<td><?php echo $referral['Referral']['job_information'] ?></td>
			<td><?php echo $referral['Referral']['history'] ?></td>
		</tr>
	</tbody>
</table>
<h2>Referrer</h2>
<table>
	<thead>
		<tr>
			<th style="width:25%">Name</th>
			<th style="width:25%;">Source</th>
			<th style="width:25%">Organisation</th>
			<th>Email</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo $referral['Referrer']['Person']['full_name'] ?></td>
			<td><?php echo $referral['Referrer']['ReferrerType']['type'] ?></td>
			<td><?php echo $referral['Referrer']['Organisation']['OrganisationName'] ?></td>
			<td><?php echo $referral['Referrer']['email'] ?></td>
		</tr>
	</tbody>
</table>

<?php if(!empty($referral['Appointment'])) { ?>
<h2>Appointments</h2>
<table>
	<thead>
		<tr>
			<th>When</th>
			<th>Diary</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($referral['Appointment'] as $a) { ?>
		<tr>
			<td><?php echo $a['period'] ?></td>
			<td><?php echo $a['Diary']['name'] ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } // End appointments ?>

<?php if(!empty($referral['Declination'])) { ?>
<h2>Declinations</h2>
<table>
	<thead>
		<tr>
			<th style="width:25%">When</th>
			<th style="width:25%">By</th>
			<th>Reason</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($referral['Declination'] as $d) { ?>
		<tr>
			<td><?php echo $d['created'] ?></td>
			<td><?php echo $d['Person']['full_name'] ?></td>
			<td><?php echo $d['reason'] ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } // End declinations ?>