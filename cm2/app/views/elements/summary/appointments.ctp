<?php $data = $this->requestAction('/appointments/summary/' . $person_id); ?>

<?php if (empty($data)) : ?>
<p>No appointments found.</p>
<?php return; endif; ?>


<table>
<thead>
<tr>
    <th>Type</th>
    <th>When</th>
    <th>Diary</th>
    <th>Notes</th>
</tr>
</thead>
<?php foreach ($data as $r) : ?>
<tr>
    <td><?php echo (!empty($r['Appointment']['new_or_review']) ? $r['Appointment']['new_or_review'] : 'new'); ?></td>
    
    <td>
        <?php echo $mytime->date($r['Appointment']['from_date']) ?>
        <?php echo $mytime->time($r['Appointment']['from_date']) ?> -
        <?php echo $mytime->time($r['Appointment']['to_date']) ?>
    </td>
    
    <td><?php echo $r['Diary']['name']?></td>
    
    <td>
        Status: 
        
        <?php 
            echo $r['Appointment']['state'] == 'new' ? 'pending' : $r['Appointment']['state'];
            if ($r['Appointment']['state'] == 'deleted') {
                echo ' on ' . $mytime->datetime($r['Appointment']['deleted_on']) . ' by '
                    . $r['Deleter']['full_name'];
                if (!empty ($r['Appointment']['deleted_reason'])) {
                    echo '<br />' . $r['Appointment']['deleted_reason'];
                }
            }
        ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php /* ?>
<pre>
<?php 
    print_r($data);
?>
</pre>
<?php */ ?>
