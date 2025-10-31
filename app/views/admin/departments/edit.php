<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="flex justify-center">
    <div class="w-full lg:w-1/2 p-8 rounded-lg glass-effect">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">แก้ไขชื่อฝ่าย</h1>
        <form action="<?php echo URLROOT; ?>/admin/editDepartment/<?php echo $data['department']->id; ?>" method="post">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">ชื่อฝ่าย</label>
                <input type="text" name="name" class="mt-1 block w-full input-field" value="<?php echo htmlspecialchars($data['department']->name); ?>" required>
            </div>
            <div class="mt-6 flex justify-end space-x-4">
                <a href="<?php echo URLROOT; ?>/admin/departments" class="bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-full hover:bg-gray-400">ยกเลิก</a>
                <button type="submit" class="bg-[var(--theme-color)] text-white font-bold py-2 px-4 rounded-full hover:bg-[var(--theme-color-hover)]">บันทึกการเปลี่ยนแปลง</button>
            </div>
        </form>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>