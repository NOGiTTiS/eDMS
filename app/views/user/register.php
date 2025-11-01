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

                <!-- ===== ส่วน Telegram Chat ID (ฉบับแก้ไข) ===== -->
                <div class="mt-4">
                    <div class="flex justify-between items-center">
                        <label for="telegram_chat_id" class="block text-sm font-medium text-gray-700">Telegram Chat ID (ถ้ามี)</label>
                        <button type="button" id="find-chat-id-btn" class="text-xs text-blue-600 hover:underline">หา Chat ID ของฉัน</button>
                    </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const findChatIdBtn = document.getElementById('find-chat-id-btn');
    if (findChatIdBtn) {
        const telegramChatIdInput = document.getElementById('telegram_chat_id');
        // ดึงค่า bot username และทำความสะอาด (trim)
        const botUsername = "<?php echo trim(get_setting('telegram_bot_username', '')); ?>";

        findChatIdBtn.addEventListener('click', function() {
            // ตรวจสอบก่อนว่ามี bot username หรือไม่
            if (!botUsername) {
                Swal.fire({
                    title: 'ข้อผิดพลาด',
                    text: 'ยังไม่ได้ตั้งค่า Telegram Bot Username ในระบบ',
                    icon: 'error'
                });
                return;
            }

            Swal.fire({
                title: 'วิธีหา Telegram Chat ID',
                html: `
                    <div class="text-left">
                        <p class="mb-2">1. คลิกปุ่มด้านล่างเพื่อเปิดแชทกับบอทของเรา แล้วกด <b>"Start"</b> หรือส่งข้อความอะไรก็ได้ 1 ข้อความ</p>
                        <a href="https://t.me/${botUsername}" target="_blank" class="inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded-full hover:bg-blue-600 mb-4">เปิดแชทกับ @${botUsername}</a>
                        <p class="mb-2">2. กลับมาที่หน้านี้ แล้วคลิกปุ่ม <b>"ดึง Chat ID ล่าสุด"</b></p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'ดึง Chat ID ล่าสุด',
                cancelButtonText: 'ปิด',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch('<?php echo URLROOT; ?>/user/getLatestChatId')
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(response.statusText);
                            }
                            return response.json();
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Request failed: ${error}`);
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value.success) {
                        telegramChatIdInput.value = result.value.chat_id;
                        Swal.fire({
                            title: 'สำเร็จ!',
                            text: 'Chat ID ของคุณถูกนำมาใส่ในฟอร์มแล้ว',
                            icon: 'success'
                        });
                    } else {
                        Swal.fire({
                            title: 'ไม่สำเร็จ',
                            text: result.value.message || 'ไม่พบข้อความล่าสุด กรุณาลองส่งข้อความหาบอทอีกครั้ง',
                            icon: 'error'
                        });
                    }
                }
            });
        });
    }
});
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>