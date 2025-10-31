<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="flex justify-center">
    <div class="w-full lg:w-2/3 p-8 rounded-lg glass-effect">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">โปรไฟล์และการตั้งค่า</h1>
        <?php flash('profile_action_success'); ?>
        
        <!-- ฟอร์มแก้ไขข้อมูลส่วนตัว -->
        <h2 class="text-xl font-semibold text-gray-700">ข้อมูลผู้ใช้</h2>
        <form action="<?php echo URLROOT; ?>/profile/updateProfile" method="post" class="mt-4 space-y-4">
            <div>
                <label for="full_name" class="block text-sm font-medium text-gray-700">ชื่อ-สกุล</label>
                <input type="text" name="full_name" class="mt-1 w-full input-field <?php echo (!empty($data['full_name_err'])) ? 'border-red-500' : ''; ?>" value="<?php echo $_SESSION['user_full_name']; ?>">
                <span class="text-red-500 text-sm"><?php echo $data['full_name_err'] ?? ''; ?></span>
            </div>
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" class="mt-1 w-full input-field <?php echo (!empty($data['username_err'])) ? 'border-red-500' : ''; ?>" value="<?php echo $_SESSION['user_username']; ?>">
                <span class="text-red-500 text-sm"><?php echo $data['username_err'] ?? ''; ?></span>
            </div>
             <div>
                <label for="telegram_chat_id" class="block text-sm font-medium text-gray-700">Telegram Chat ID</label>
                <?php
                    // ดึงข้อมูลล่าสุดจาก DB มาแสดง เผื่อ Admin แก้ไขให้
                    $this->userModel = $this->model('User');
                    $currentUser = $this->userModel->getUserById($_SESSION['user_id']);
                ?>
                <input type="text" name="telegram_chat_id" class="mt-1 w-full input-field" value="<?php echo $currentUser->telegram_chat_id; ?>">
            </div>
             <div>
                <label class="block text-sm font-medium text-gray-700">บทบาท</label>
                <input type="text" class="mt-1 w-full input-field bg-gray-200" value="<?php echo translateRoleToThai($_SESSION['user_role']); ?>" readonly>
            </div>
             <div class="text-right">
                <button type="submit" class="bg-[var(--theme-color)] text-white font-bold py-2 px-4 rounded-full hover:bg-[var(--theme-color-hover)]">บันทึกข้อมูลส่วนตัว</button>
            </div>
        </form>

        <div class="border-t my-6"></div>

        <h2 class="text-xl font-semibold text-gray-700">เปลี่ยนรหัสผ่าน</h2>
        <form action="<?php echo URLROOT; ?>/profile/changePassword" method="post" class="mt-4 space-y-4">
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">รหัสผ่านปัจจุบัน</label>
                <input type="password" name="current_password" class="mt-1 w-full input-field                                                                                                                                                                                           <?php echo(! empty($data['current_password_err'])) ? 'border-red-500' : ''; ?>">
                <span class="text-red-500 text-sm"><?php echo $data['current_password_err'] ?? ''; ?></span>
            </div>
             <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700">รหัสผ่านใหม่</label>
                <input type="password" name="new_password" class="mt-1 w-full input-field                                                                                                                                                                                   <?php echo(! empty($data['new_password_err'])) ? 'border-red-500' : ''; ?>">
                <span class="text-red-500 text-sm"><?php echo $data['new_password_err'] ?? ''; ?></span>
            </div>
             <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">ยืนยันรหัสผ่านใหม่</label>
                <input type="password" name="confirm_password" class="mt-1 w-full input-field                                                                                                                                                                                           <?php echo(! empty($data['confirm_password_err'])) ? 'border-red-500' : ''; ?>">
                <span class="text-red-500 text-sm"><?php echo $data['confirm_password_err'] ?? ''; ?></span>
            </div>
            <div class="text-right">
                <button type="submit" class="bg-[var(--theme-color)] text-white font-bold py-2 px-4 rounded-full hover:bg-[var(--theme-color-hover)]">บันทึกรหัสผ่านใหม่</button>
            </div>
        </form>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>