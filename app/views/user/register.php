<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md">
        <div class="p-8 space-y-6 rounded-lg glass-effect">
            <h2 class="text-2xl font-bold text-center text-gray-800">สร้างบัญชีใหม่</h2>
            <form action="<?php echo URLROOT; ?>/user/register" method="post">
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">ชื่อ-สกุล</label>
                    <input type="text" name="full_name" id="full_name" class="mt-1 block w-full input-field <?php echo (!empty($data['full_name_err'])) ? 'border-red-500' : ''; ?>" value="<?php echo $data['full_name']; ?>">
                    <span class="text-red-500 text-sm"><?php echo $data['full_name_err']; ?></span>
                </div>
                
                <div class="mt-4">
                    <label for="username" class="block text-sm font-medium text-gray-700">ชื่อผู้ใช้ (Username)</label>
                    <input type="text" name="username" id="username" class="mt-1 block w-full input-field <?php echo (!empty($data['username_err'])) ? 'border-red-500' : ''; ?>" value="<?php echo $data['username']; ?>">
                    <span class="text-red-500 text-sm"><?php echo $data['username_err']; ?></span>
                </div>
                
                <div class="mt-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">รหัสผ่าน</label>
                    <input type="password" name="password" id="password" class="mt-1 block w-full input-field <?php echo (!empty($data['password_err'])) ? 'border-red-500' : ''; ?>" value="<?php echo $data['password']; ?>">
                    <span class="text-red-500 text-sm"><?php echo $data['password_err']; ?></span>
                </div>
                
                <div class="mt-4">
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">ยืนยันรหัสผ่าน</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="mt-1 block w-full input-field <?php echo (!empty($data['confirm_password_err'])) ? 'border-red-500' : ''; ?>" value="<?php echo $data['confirm_password']; ?>">
                    <span class="text-red-500 text-sm"><?php echo $data['confirm_password_err']; ?></span>
                </div>

                <!-- ===== เพิ่ม Dropdown เลือกฝ่าย ===== -->
                <div class="mt-4">
                    <label for="department_id" class="block text-sm font-medium text-gray-700">สังกัดฝ่าย</label>
                    <select name="department_id" id="department_id" class="mt-1 block w-full input-field <?php echo (!empty($data['department_id_err'])) ? 'border-red-500' : ''; ?>">
                        <option value="">-- กรุณาเลือกฝ่าย --</option>
                        <?php foreach($data['departments'] as $dept): ?>
                            <option value="<?php echo $dept->id; ?>" <?php echo ($data['department_id'] == $dept->id) ? 'selected' : ''; ?>>
                                <?php echo $dept->name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="text-red-500 text-sm"><?php echo $data['department_id_err']; ?></span>
                </div>

                <!-- ===== เพิ่ม Input สำหรับ Telegram ===== -->
                <div class="mt-4">
                    <label for="telegram_chat_id" class="block text-sm font-medium text-gray-700">Telegram Chat ID (ถ้ามี)</label>
                    <input type="text" name="telegram_chat_id" id="telegram_chat_id" class="mt-1 block w-full input-field" value="<?php echo $data['telegram_chat_id']; ?>">
                </div>
                
                <div class="flex items-center justify-between mt-6">
                    <button type="submit" class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[var(--theme-color)] hover:bg-[var(--theme-color-hover)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--theme-color)]">
                        สมัครสมาชิก
                    </button>
                </div>

                <div class="text-center mt-4">
                    <a href="<?php echo URLROOT; ?>/user/login" class="font-medium text-blue-600 hover:text-blue-500">
                        มีบัญชีอยู่แล้ว? เข้าสู่ระบบ
                    </a>
                </div>
            </form>
        </div>
        <!-- ===== เพิ่มส่วน Copyright ===== -->
        <div class="text-center mt-4">
            <p class="text-sm text-gray-600"><?php echo get_setting('site_copyright'); ?></p>
        </div>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>