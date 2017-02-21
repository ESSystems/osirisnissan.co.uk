<?php $data = $this->requestAction('/attendances/summary/' . $person_id); ?>

<?php if (empty($data)) : ?>
<p>No attendances found.</p>
<?php return; endif; ?>

<table border="1">
<thead>
<tr>
    <th>Time</th>
    <th>Seen</th>
    <th>Reason</th>
    <th>Result</th>
</tr>
</thead>
<?php foreach ($data as $r) : ?>
    <tr>
        <td><?php echo $time->format('d/m/y H:i', $r['Attendance']['attendance_date_time'])?></td>
        <td>
            <?php if (!empty($r['Attendance']['seen_at_time'])) {
                echo $time->format('d/m/y H:i', $r['Attendance']['seen_at_time']);
            }?>
        </td>
        <td><?php echo $r['AttendanceReason']['description']?>
        <td><?php echo $r['AttendanceResult']['description']?>
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