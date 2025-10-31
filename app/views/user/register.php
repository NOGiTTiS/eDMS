<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md">
        <div class="p-8 space-y-6 rounded-lg glass-effect">
            <h2 class="text-2xl font-bold text-center text-gray-800">สร้างบัญชีใหม่</h2>
            <form action="<?php echo URLROOT; ?>/user/register" method="post">
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">ชื่อ-สกุล</label>
                    <input type="text" name="full_name" id="full_name" class="mt-1 block w-full px-3 py-2 bg-white bg-opacity-50 border                                                                                                                                                                                                                                                                                                                                                                                                          <?php echo(! empty($data['full_name_err'])) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md shadow-sm focus:outline-none focus:ring-[var(--theme-color)] focus:border-[var(--theme-color)]" value="<?php echo $data['full_name']; ?>">
                    <span class="text-red-500 text-sm"><?php echo $data['full_name_err']; ?></span>
                </div>
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">ชื่อผู้ใช้ (Username)</label>
                    <input type="text" name="username" id="username" class="mt-1 block w-full px-3 py-2 bg-white bg-opacity-50 border                                                                                                                                                                                                                                                                                                                                                                                                    <?php echo(! empty($data['username_err'])) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md shadow-sm focus:outline-none focus:ring-[var(--theme-color)] focus:border-[var(--theme-color)]" value="<?php echo $data['username']; ?>">
                    <span class="text-red-500 text-sm"><?php echo $data['username_err']; ?></span>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">รหัสผ่าน</label>
                    <input type="password" name="password" id="password" class="mt-1 block w-full px-3 py-2 bg-white bg-opacity-50 border                                                                                                                                                                                                                                                                                                                                                                                                                <?php echo(! empty($data['password_err'])) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md shadow-sm focus:outline-none focus:ring-[var(--theme-color)] focus:border-[var(--theme-color)]" value="<?php echo $data['password']; ?>">
                    <span class="text-red-500 text-sm"><?php echo $data['password_err']; ?></span>
                </div>
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">ยืนยันรหัสผ่าน</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="mt-1 block w-full px-3 py-2 bg-white bg-opacity-50 border                                                                                                                                                                                                                                                                                                                                                                                                                                                                <?php echo(! empty($data['confirm_password_err'])) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md shadow-sm focus:outline-none focus:ring-[var(--theme-color)] focus:border-[var(--theme-color)]" value="<?php echo $data['confirm_password']; ?>">
                    <span class="text-red-500 text-sm"><?php echo $data['confirm_password_err']; ?></span>
                </div>
                <div class="flex items-center justify-between mt-4">
                    <button type="submit" class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[var(--theme-color-hover)] hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--theme-color)]">
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