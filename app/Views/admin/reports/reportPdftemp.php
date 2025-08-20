<!DOCTYPE html>
<html>
<head>
    <title>Activity Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 6px; text-align: left; }
    </style>
</head>
<body>
    <h2>Task Report</h2>
    <table>
        <thead>
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participates</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity Tasks</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($activities as $row): 
            $totalTasks = isset($row['total_activities']) ? $row['total_activities'] : 0;
            $completedTasks = isset($row['completed_activities']) ? $row['completed_activities'] : 0;

            $percent = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0; 
            $color = ($percent < 50) ? 'red' : (($percent > 80) ? 'green' : '#FACC15'); 

            ?>
            <tr>
                <td><?= esc($row['title']) ?></td>
                <td><?= esc($row['total_task_staff'] ?? 0) ?></td>
                <td><?= esc($row['total_activities'] ?? 0) ?></td>
                <td><?= esc(str_replace('_',' ',$row['master_task_status'])) ?></td>
                 <td>
                    <div style="width:500px; height:25px; border-radius:4px;">
                        <div style="width:<?= $percent ?>%; height:25px; background:<?= $color ?>; border-radius:4px;"></div>
                    </div>
                    <span style="font-size:12px; color:<?=$color;?>">
                        <?= $completedTasks ?? 0 ?> / <?= $totalTasks ?? 0 ?> (<?= $percent ?>%)
                    </span>
                </td>

                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
