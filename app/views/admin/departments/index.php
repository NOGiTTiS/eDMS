<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="p-8 rounded-lg glass-effect">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">จัดการฝ่าย (Departments)</h1>
    <?php flash('dept_action_success'); ?>
    <?php flash('dept_action_fail'); ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- คอลัมน์ซ้าย: ฟอร์มเพิ่มฝ่าย -->
        <div class="md:col-span-1 p-6 bg-white bg-opacity-50 rounded-lg">
            <h2 class="text-xl font-semibold mb-4">เพิ่มฝ่ายใหม่</h2>
            <form action="<?php echo URLROOT; ?>/admin/addDepartment" method="post">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">ชื่อฝ่าย</label>
                    <input type="text" name="name" class="mt-1 block w-full input-field" required>
                </div>
                <div class="mt-4">
                    <button type="submit" class="w-full bg-[var(--theme-color)] text-white font-bold py-2 px-4 rounded-full hover:bg-[var(--theme-color-hover)]">บันทึก</button>
                </div>
            </form>
        </div>

        <!-- คอลัมน์ขวา: ตารางแสดงรายชื่อฝ่าย -->
        <div class="md:col-span-2">
             <div class="overflow-x-auto bg-white bg-opacity-75 rounded-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-200 bg-opacity-50">
                        <tr class="text-left">
                            <th class="p-3">#ID</th>
                            <th class="p-3">ชื่อฝ่าย</th>
                            <th class="p-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['departments'] as $dept): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="p-3"><?php echo $dept->id; ?></td>
                            <td class="p-3 font-semibold"><?php echo htmlspecialchars($dept->name); ?></td>
                            <td class="p-3">
                                <div class="flex items-center space-x-2 justify-end">
                                    <!-- ปุ่ม "แก้ไข" -->
                                    <a href="<?php echo URLROOT; ?>/admin/editDepartment/<?php echo $dept->id; ?>" 
                                       class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-semibold hover:bg-yellow-600 transition-colors">
                                        แก้ไข
                                    </a>
                                    
                                    <!-- ปุ่ม "ลบ" -->
                                    <form action="<?php echo URLROOT; ?>/admin/deleteDepartment/<?php echo $dept->id; ?>" method="post" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบฝ่ายนี้? (โปรดตรวจสอบให้แน่ใจว่าไม่มีผู้ใช้สังกัดฝ่ายนี้อยู่)');">
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
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>