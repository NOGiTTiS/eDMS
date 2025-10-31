<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="flex justify-center">
    <div class="w-full lg:w-2/3 p-8 rounded-lg glass-effect">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">โปรไฟล์และการตั้งค่า</h1>
        <?php flash('profile_action_success'); ?>
        
        <h2 class="text-xl font-semibold text-gray-700">ข้อมูลผู้ใช้</h2>
        <div class="mt-4 space-y-2">
            <p><strong>ชื่อ-สกุล:</strong> <?php echo $_SESSION['user_full_name']; ?></p>
            <p><strong>Username:</strong> <?php echo $_SESSION['user_username']; ?></p>
            <p><strong>บทบาท:</strong> <?php echo translateRoleToThai($_SESSION['user_role']); ?></p>
        </div>

        <div class="border-t my-6"></div>

        <h2 class="text-xl font-semibold text-gray-700">เปลี่ยนรหัสผ่าน</h2>
        <form action="<?php echo URLROOT; ?>/profile/changePassword" method="post" class="mt-4 space-y-4">
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">รหัสผ่านปัจจุบัน</label>
                <input type="password" name="current_password" class="mt-1 w-full input-field <?php echo (!empty($data['current_password_err'])) ? 'border-red-500' : ''; ?>">
                <span class="text-red-500 text-sm"><?php echo $data['current_password_err'] ?? ''; ?></span>
            </div>
             <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700">รหัสผ่านใหม่</label>
                <input type="password" name="new_password" class="mt-1 w-full input-field <?php echo (!empty($data['new_password_err'])) ? 'border-red-500' : ''; ?>">
                <span class="text-red-500 text-sm"><?php echo $data['new_password_err'] ?? ''; ?></span>
            </div>
             <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">ยืนยันรหัสผ่านใหม่</label>
                <input type="password" name="confirm_password" class="mt-1 w-full input-field <?php echo (!empty($data['confirm_password_err'])) ? 'border-red-500' : ''; ?>">
                <span class="text-red-500 text-sm"><?php echo $data['confirm_password_err'] ?? ''; ?></span>
            </div>
            <div class="text-right">
                <button type="submit" class="bg-pink-500 text-white font-bold py-2 px-4 rounded-full hover:bg-pink-600">บันทึกรหัสผ่านใหม่</button>
            </div>
        </form>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>