<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>SL NO</th>
            <th>CODE</th>
            <th>STORE NAME</th>
            <th>OLD NAME</th>

            <?php for ($i = 0; $i < $maxActivities; $i++): ?>
                <th><?= esc($activityHeaders[$i] ?? 'Activity ' . ($i + 1)) ?></th>
            <?php endfor; ?>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($excelRows as $row): ?>
            <tr>
                <td><?= $row['sl_no'] ?></td>
                <td><?= esc($row['code']) ?></td>
                <td><?= esc($row['store_name']) ?></td>
                <td><?= esc($row['old_name']) ?></td>

                <?php for ($i = 1; $i <= $maxActivities; $i++): ?>
                    <td><?= esc($row['activity_' . $i]) ?></td>
                <?php endfor; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
