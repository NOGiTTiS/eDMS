<div>
    <label for="full_name" class="block text-sm font-medium text-gray-700">ชื่อ-สกุล *</label>
    <input type="text" name="full_name" class="mt-1 block w-full input-field <?php echo (!empty($data['full_name_err'])) ? 'border-red-500' : ''; ?>" value="<?php echo $data['full_name']; ?>">
    <span class="text-red-500 text-sm"><?php echo $data['full_name_err']; ?></span>
</div>
<div class="mt-4">
    <label for="username" class="block text-sm font-medium text-gray-700">Username *</label>
    <input type="text" name="username" class="mt-1 block w-full input-field <?php echo (!empty($data['username_err'])) ? 'border-red-500' : ''; ?>" value="<?php echo $data['username']; ?>">
    <span class="text-red-500 text-sm"><?php echo $data['username_err']; ?></span>
</div>
<div class="mt-4">
    <label for="password" class="block text-sm font-medium text-gray-700">รหัสผ่าน <?php echo (strpos($_SERVER['REQUEST_URI'], 'editUser') !== false) ? '(เว้นว่างไว้หากไม่ต้องการเปลี่ยน)' : '*'; ?></label>
    <input type="password" name="password" class="mt-1 block w-full input-field <?php echo (!empty($data['password_err'])) ? 'border-red-500' : ''; ?>">
    <span class="text-red-500 text-sm"><?php echo $data['password_err']; ?></span>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
    <div>
        <label for="role" class="block text-sm font-medium text-gray-700">บทบาท (Role)</label>
        <select name="role" class="mt-1 block w-full input-field">
            <?php $roles = getRoleListThai(); // <-- เรียกใช้ Helper ?>
            <?php foreach($roles as $role_key => $role_name): // <-- วนลูปแบบ Key => Value ?>
                <option value="<?php echo $role_key; ?>" <?php echo ($data['role'] == $role_key) ? 'selected' : ''; ?>>
                    <?php echo $role_name; // <-- แสดงชื่อภาษาไทย ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label for="department_id" class="block text-sm font-medium text-gray-700">ฝ่าย (Department)</label>
        <select name="department_id" class="mt-1 block w-full input-field">
            <option value="">-- ไม่ระบุ --</option>
            <?php foreach($data['departments'] as $dept): ?>
                <option value="<?php echo $dept->id; ?>" <?php echo ($data['department_id'] == $dept->id) ? 'selected' : ''; ?>><?php echo $dept->name; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<div class="mt-4">
    <label for="telegram_chat_id" class="block text-sm font-medium text-gray-700">Telegram Chat ID</label>
    <input type="text" name="telegram_chat_id" class="mt-1 block w-full input-field" value="<?php echo $data['telegram_chat_id']; ?>">
</div>