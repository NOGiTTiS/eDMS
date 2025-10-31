<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="p-8 rounded-lg glass-effect">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">จัดการผู้ใช้งาน</h1>
        <a href="<?php echo URLROOT; ?>/admin/addUser" class="bg-[var(--theme-color)] text-white font-bold py-2 px-4 rounded-full hover:bg-[var(--theme-color-hover)]">เพิ่มผู้ใช้ใหม่</a>
    </div>
    <?php flash('user_action_success'); ?>
    <div class="overflow-x-auto bg-white bg-opacity-75 rounded-lg">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-200 bg-opacity-50">
                <tr class="text-left">
                    <th class="p-3">ชื่อ-สกุล</th>
                    <th class="p-3">Username</th>
                    <th class="p-3">บทบาท</th>
                    <th class="p-3">ฝ่าย</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['users'] as $user): ?>
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="p-3"><?php echo htmlspecialchars($user->full_name); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($user->username); ?></td>
                    <td class="p-3"><?php echo translateRoleToThai($user->role); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($user->department_name ?? 'N/A'); ?></td>
                    <td class="p-3">
                        <div class="flex items-center space-x-2">
                            <!-- ปุ่ม "แก้ไข" -->
                            <a href="<?php echo URLROOT; ?>/admin/editUser/<?php echo $user->id; ?>"
                               class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-semibold hover:bg-yellow-600 transition-colors">
                                แก้ไข
                            </a>

                            <!-- ปุ่ม "ลบ" -->
                            <form action="<?php echo URLROOT; ?>/admin/deleteUser/<?php echo $user->id; ?>" method="post" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้นี้?');">
                                <button type="submit"
                                        class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold hover:bg-red-600 transition-colors">
                                    ลบ
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>