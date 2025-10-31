<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="flex justify-center">
    <div class="w-full lg:w-2/3 p-8 rounded-lg glass-effect">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">ตั้งค่าระบบ</h1>
        <?php flash('setting_success'); ?>

        <form action="<?php echo URLROOT; ?>/admin/settings" method="post" enctype="multipart/form-data">
            <!-- General Settings -->
            <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4">ตั้งค่าทั่วไป</h2>
            <div class="space-y-4">
                <div>
                    <label for="site_name" class="block text-sm font-medium text-gray-700">ชื่อระบบ</label>
                    <input type="text" name="site_name" class="mt-1 block w-full input-field" value="<?php echo htmlspecialchars($data['settings']['site_name']); ?>">
                </div>
                <div>
                    <label for="theme_color" class="block text-sm font-medium text-gray-700">สีธีมหลัก</label>
                    <input type="color" name="theme_color" class="mt-1 h-10 w-full input-field" value="<?php echo htmlspecialchars($data['settings']['theme_color']); ?>">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="bg_gradient_start" class="block text-sm font-medium text-gray-700">สีพื้นหลัง (เริ่มต้น)</label>
                        <input type="color" name="bg_gradient_start" class="mt-1 h-10 w-full input-field" value="<?php echo htmlspecialchars($data['settings']['bg_gradient_start']); ?>">
                    </div>
                     <div>
                        <label for="bg_gradient_end" class="block text-sm font-medium text-gray-700">สีพื้นหลัง (สิ้นสุด)</label>
                        <input type="color" name="bg_gradient_end" class="mt-1 h-10 w-full input-field" value="<?php echo htmlspecialchars($data['settings']['bg_gradient_end']); ?>">
                    </div>
                </div>
                <div>
                    <label for="site_logo" class="block text-sm font-medium text-gray-700">โลโก้ (ไฟล์ PNG พื้นหลังโปร่งใส, สูงไม่เกิน 40px)</label>
                    <input type="file" name="site_logo" class="mt-1 block w-full text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100">
                    <?php if (! empty($data['settings']['site_logo'])): ?>
                        <img src="<?php echo URLROOT . '/' . $data['settings']['site_logo']; ?>" class="h-8 mt-2" alt="Current Logo">
                    <?php endif; ?>
                </div>
                <div>
                    <label for="site_favicon" class="block text-sm font-medium text-gray-700">Favicon (ไฟล์ .ico หรือ .png ขนาด 32x32px)</label>
                    <input type="file" name="site_favicon" class="mt-1 block w-full text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100">
                </div>
            </div>

            <!-- Document Settings -->
            <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mt-8 mb-4">ตั้งค่าเอกสาร</h2>
            <div class="space-y-4">
                <div>
                    <label for="doc_number_format" class="block text-sm font-medium text-gray-700">รูปแบบเลขทะเบียนรับ</label>
                    <select name="doc_number_format" class="mt-1 block w-full input-field">
                        <option value="continuous"                                                                                                     <?php echo($data['settings']['doc_number_format'] == 'continuous') ? 'selected' : ''; ?>>รันต่อเนื่อง (เช่น 1, 2, 3)</option>
                        <option value="year_based"                                                                                                     <?php echo($data['settings']['doc_number_format'] == 'year_based') ? 'selected' : ''; ?>>รันต่อปี (เช่น 1/2568, 2/2568)</option>
                    </select>
                </div>
                 <div>
                    <label for="doc_registration_counter" class="block text-sm font-medium text-gray-700">เลขทะเบียนรับล่าสุด</label>
                    <input type="number" name="doc_registration_counter" class="mt-1 block w-full input-field" value="<?php echo (int) $data['settings']['doc_registration_counter']; ?>">
                    <p class="text-xs text-gray-500 mt-1">หากเลขล่าสุดในสมุดคือ 350 ให้กรอก 350 ที่นี่ เลขถัดไปจะเป็น 351</p>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-[var(--theme-color)] text-white font-bold py-2 px-4 rounded-full hover:bg-[var(--theme-color-hover)]">บันทึกการตั้งค่า</button>
            </div>
        </form>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>