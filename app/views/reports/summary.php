<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="p-8 rounded-lg glass-effect">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">รายงานสรุปประสิทธิภาพ</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card: เวลาอนุมัติของ ผอ. -->
        <div class="p-6 rounded-lg bg-white bg-opacity-50 text-center">
            <div class="text-3xl font-bold text-blue-600"><?php echo $data['avgDirectorApprovalTime']; ?></div>
            <div class="text-lg text-gray-700 mt-2">ระยะเวลาเฉลี่ยในการอนุมัติ<br>(ผอ.)</div>
            <p class="text-xs text-gray-500 mt-1">(นับจากธุรการกลางส่งเรื่อง จนถึง ผอ. เกษียณ)</p>
        </div>

        <!-- Card: เวลาอนุมัติของ รองฯ ฝ่าย -->
        <div class="p-6 rounded-lg bg-white bg-opacity-50 text-center">
             <div class="text-3xl font-bold text-cyan-600"><?php echo $data['avgDeputyApprovalTime']; ?></div>
            <div class="text-lg text-gray-700 mt-2">ระยะเวลาเฉลี่ยในการอนุมัติ<br>(รอง ผอ. ฝ่าย)</div>
            <p class="text-xs text-gray-500 mt-1">(นับจากธุรการฝ่ายส่งเรื่อง จนถึง รองฯ เกษียณ)</p>
        </div>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>