<?php
    $themeColor = get_setting('theme_color', '#ec4899');
    // สร้างสีที่เข้มขึ้นสำหรับ hover state
    // (นี่เป็น Logic แบบง่ายๆ อาจต้องปรับปรุงถ้าต้องการความแม่นยำสูง)
    function adjustBrightness($hex, $steps)
    {
        $steps = max(-255, min(255, $steps));
        $hex   = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));
        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }
    $themeColorHover = adjustBrightness($themeColor, -20); // ทำให้สีเข้มขึ้นเล็กน้อย
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo get_setting('site_name', SITENAME); ?></title>
    <link rel="icon" href="<?php echo URLROOT . '/' . get_setting('site_favicon'); ?>" type="image/x-icon">

    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Font: Prompt -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Signature Pad JS -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <!-- Custom CSS -->
    <style>
        :root {
            --theme-color:                                                     <?php echo $themeColor; ?>;
            --theme-color-hover:                                                                 <?php echo $themeColorHover; ?>;
        }

        body {
            font-family: 'Prompt', sans-serif;
            background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);
        }
        /* Glassmorphism Effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        /* ########## เพิ่มโค้ดส่วนนี้เข้ามา ########## */
        .input-field {
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            width: 100%; /* เพิ่มความกว้างเต็ม 100% เป็นค่าเริ่มต้น */
        }
        .input-field:focus {
            outline: none;
            box-shadow: 0 0 0 2px #ec4899; /* pink-500 */
            border-color: #ec4899;
        }
        /* ########################################## */

    </style>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="relative z-30 p-4 glass-effect mb-6">
        <div class="container mx-auto flex justify-between items-center">
            <a href="<?php echo URLROOT; ?>/dashboard" class="flex items-center space-x-2 text-xl font-bold text-gray-800">
                <?php $logo = get_setting('site_logo');if (! empty($logo)): ?>
                    <img src="<?php echo URLROOT . '/' . $logo; ?>" class="h-8" alt="Logo">
                <?php endif; ?>
                <span><?php echo get_setting('site_name', SITENAME); ?></span>
            </a>
            <ul class="flex items-center space-x-6">
                <?php if (isLoggedIn()): ?>
                    <!-- ลิงก์หลักที่แสดงตลอด -->
                    <li><a href="<?php echo URLROOT; ?>/dashboard" class="text-blue-600 hover:text-blue-800 font-semibold">Dashboard</a></li>
                    <li><a href="<?php echo URLROOT; ?>/document" class="text-blue-600 hover:text-blue-800 font-semibold">ระบบเอกสาร</a></li>

                    <!-- เมนู Dropdown สำหรับผู้ใช้ -->
                    <li class="relative group">
                        <!-- ส่วนหัวของ Dropdown ที่แสดงชื่อผู้ใช้ (แก้ไขแล้ว) -->
                        <button class="flex items-center space-x-2 text-gray-700 cursor-pointer font-semibold">
                            <span><?php echo $_SESSION['user_full_name']; ?></span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <!-- เนื้อหาของ Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-48 bg-white bg-opacity-80 backdrop-blur-sm rounded-md shadow-lg py-1 z-20 opacity-0 group-hover:opacity-100 transition-all duration-300 transform scale-95 group-hover:scale-100 invisible group-hover:visible">
                            <a href="<?php echo URLROOT; ?>/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-pink-100">โปรไฟล์ส่วนตัว</a>

                            <!-- เมนูสำหรับ Admin เท่านั้น -->
                            <?php if ($_SESSION['user_role'] == 'central_admin'): ?>
                                <div class="border-t my-1"></div>
                                <a href="<?php echo URLROOT; ?>/admin" class="block px-4 py-2 text-sm text-purple-600 hover:bg-pink-100 font-semibold">จัดการผู้ใช้</a>
                                <a href="<?php echo URLROOT; ?>/admin/settings" class="block px-4 py-2 text-sm text-purple-600 hover:bg-pink-100 font-semibold">ตั้งค่าระบบ</a>
                                <div class="border-t my-1"></div>
                                <a href="<?php echo URLROOT; ?>/report/summary" class="block px-4 py-2 text-sm text-green-600 hover:bg-pink-100 font-semibold">รายงานสรุป</a>
                            <?php endif; ?>

                            <div class="border-t my-1"></div>
                            <a href="<?php echo URLROOT; ?>/user/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-pink-100">ออกจากระบบ</a>
                        </div>
                    </li>

                <?php else: ?>
                    <!-- เมนูสำหรับ Guest (ยังไม่ Login) -->
                    <li>
                        <a href="<?php echo URLROOT; ?>/user/login" class="text-blue-600 hover:text-blue-800">เข้าสู่ระบบ</a>
                    </li>
                    <li>
                        <a href="<?php echo URLROOT; ?>/user/register" class="bg-[var(--theme-color)] text-white font-bold py-2 px-4 rounded-full hover:bg-[var(--theme-color-hover)] transition duration-300">สมัครสมาชิก</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container mx-auto p-4">