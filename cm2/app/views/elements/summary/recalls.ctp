<?php $data = $this->requestAction('/recall_list_item_events/summary/' . $person_id); ?>

<?php if (empty($data)) : ?>
<p>No recalls found.</p>
<?php return; endif; ?>

<table border="1">
<thead>
<tr>
    <th rowspan="2">List</th>
    <th rowspan="2">Last Test</th>
    <th rowspan="2">Due</th>
    <th colspan="2">Invited</th>
    <th rowspan="2">Attended</th>
    <th rowspan="2">Note / Comments</th>
</tr>
<tr>
    <th>On</th>
    <th>By</th>
</tr>
</thead>
<?php foreach ($data as $r) : ?>
<tr>
    <td><?php echo $r['RecallListItem']['RecallList']['title']?></td>
    <td><?php if (!empty($r['RecallListItem']['last_attended_date'])) { echo $r['RecallListItem']['last_attended_date']; } ?></td>
    <td><?php if (!empty($r['RecallListItemEvent']['due_date'])) { echo $mytime->date($r['RecallListItemEvent']['due_date']); } ?></td>
    <td><?php if (!empty($r['RecallListItemEvent']['invite_date'])) { echo $mytime->date($r['RecallListItemEvent']['invite_date']); } ?></td>
    <td>
        <?php echo $r['RecallListItemEvent']['contact_type']?>
        (<?php echo $r['Creator']['Person']['full_name']?>)
    </td>
    <td>
        <?php if (!empty($r['RecallListItemEvent']['attended_date'])) : ?>
            <?php echo $mytime->date($r['RecallListItemEvent']['attended_date'])?>
        <?php endif;?>
    </td>
    <td>
        <?php if (!empty($r['RecallListItemEvent']['note'])) : ?>
            <div><?php echo $mytime->date($r['RecallListItemEvent']['note'])?></div>
        <?php endif;?>
        <?php if (!empty($r['RecallListItemEvent']['comments'])) : ?>
            <div><?php echo $mytime->date($r['RecallListItemEvent']['comments'])?></div>
        <?php endif;?>
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
