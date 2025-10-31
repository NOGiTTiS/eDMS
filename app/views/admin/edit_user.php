<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="flex justify-center">
    <div class="w-full lg:w-2/3 p-8 rounded-lg glass-effect">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">แก้ไขข้อมูลผู้ใช้:                                                                                                                                                                                                                             <?php echo htmlspecialchars($data['full_name']); ?></h1>
        <form action="<?php echo URLROOT; ?>/admin/editUser/<?php echo $data['id']; ?>" method="post">
            <?php require_once '_form.php'; ?>
            <div class="mt-6 flex justify-end space-x-4">
                <a href="<?php echo URLROOT; ?>/admin" class="bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-full hover:bg-gray-400">ยกเลิก</a>
                <button type="submit" class="bg-[var(--theme-color)] text-white font-bold py-2 px-4 rounded-full hover:bg-[var(--theme-color-hover)]">บันทึกการเปลี่ยนแปลง</button>
            </div>
        </form>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>