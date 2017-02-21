<?php $data = $this->requestAction('/absences/summary/' . $person_id); ?>

<?php if (empty($data)) : ?>
<p>No absences found.</p>
<?php return; endif; ?>


<table>
<thead>
<tr>
    <th>Type</th>
    <th>Start</th>
    <th>End</th>
    <th>Symptoms</th>
    <th>Comments</th>
</tr>
</thead>
<?php foreach ($data as $r) : ?>
<?php foreach ($r['Sicknote'] as $i=>$sr) : ?>
<tr>
        <td>
            <?php echo $sr['SicknoteType']['description']?>
        </td>
        <td>
            <?php echo $mytime->date($sr['start_date'])?>
        </td>
        <td>
            <?php echo $mytime->date($sr['end_date'])?>
        </td>
        <td>
            <?php echo $sr['symptoms_description']?>
        </td>
        <td>
            <?php echo $sr['comments']?>
        </td>
    </tr>
<?php endforeach;?>
<?php endforeach;?>
</table>

<?php /* ?>
<pre>
<?php 
    print_r($data);
?>
</pre>
<?php */ ?>