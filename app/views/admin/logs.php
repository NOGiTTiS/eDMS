<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="p-8 rounded-lg glass-effect">
<h1 class="text-3xl font-bold text-gray-800 mb-6">บันทึกการใช้งาน (Activity Logs)</h1>
    <div class="overflow-x-auto bg-white bg-opacity-75 rounded-lg">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-200 bg-opacity-50">
                <tr class="text-left">
                    <th class="p-3">วัน-เวลา</th>
                    <th class="p-3">ผู้ใช้งาน</th>
                    <th class="p-3">การกระทำ</th>
                    <th class="p-3">รายละเอียด</th>
                    <th class="p-3">IP Address</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($data['logs'] as $log): ?>
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="p-3 whitespace-nowrap"><?php echo date('d/m/Y H:i:s', strtotime($log->created_at)); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($log->username); ?></td>
                    <td class="p-3 font-semibold"><?php echo htmlspecialchars($log->action); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($log->details); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($log->ip_address); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>