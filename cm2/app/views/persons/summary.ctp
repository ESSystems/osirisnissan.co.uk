<?php $html->css('summary', null, array(), false); ?>
<?php $html->css('print_preview', null, array(), false); ?>

<div class="summary">
<h1><?php echo $person['Person']['full_name']?></h1>
<h3>Electronic Record Summary (<?php echo $time->format('d/m/Y H:i', time())?>)</h3>

<div class="employee">
    <h2>Employee Information</h2>

    <ul class="summary">
        <?php if(isset($person['Employee']['sap_number'])) { ?>
            <li><span>SAP Number:</span> <?php echo $person['Employee']['sap_number'] ?></li>
        <?php } ?>
        <?php if(isset($person['Employee']['salary_number'])) { ?>
            <li><span>Salary Number:</span> <?php echo $person['Employee']['salary_number'] ?></li>
        <?php } ?>
        <li><span>Date of birth:</span> <?php echo $time->format('d/m/Y', $person['Person']['date_of_birth'])?></li>
        <?php if ($person['Employee']['employment_end_date'] != '') { ?>
        <li><span>Employment status:</span> The employee left the organisation on <?php echo $time->format('d/m/Y', $person['Employee']['employment_end_date'])?></li>
        <?php } ?>
    </ul>
    <br />
</div>

<div class="attendances">
    <h2>Attendances</h2>

    <?php echo $this->element('summary/attendances', array('person_id'=>$person['Person']['id']))?>
</div>

<div class="appointments">
    <h2>Appointments</h2>

    <?php echo $this->element('summary/appointments', array('person_id'=>$person['Person']['id']))?>
</div>

<div class="recalls">
    <h2>Recalls</h2>

    <?php echo $this->element('summary/recalls', array('person_id'=>$person['Person']['id']))?>
</div>

<div class="absences">
    <h2>Absences</h2>

    <?php echo $this->element('summary/absences', array('person_id'=>$person['Person']['id']))?>
</div>

</div>