<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="p-2 md:p-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-4">Dashboard</h1>
    <h2 class="text-xl text-gray-600 mb-6">สวัสดี, <?php echo $_SESSION['user_full_name']; ?></h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Card: งานที่รออนุมัติ -->
        <div class="p-6 rounded-lg glass-effect text-center">
            <div class="text-5xl font-bold text-pink-600"><?php echo $data['pendingCount']; ?></div>
            <div class="text-lg text-gray-700 mt-2">เอกสารรออนุมัติ/ดำเนินการ</div>
            <a href="<?php echo URLROOT; ?>/document" class="mt-4 inline-block bg-pink-500 text-white font-bold py-2 px-4 rounded-full hover:bg-pink-600 transition duration-300 text-sm">
                ไปที่กล่องงาน
            </a>
        </div>

        <!-- Card: กราฟสรุปสถานะ -->
        <div class="md:col-span-2 p-6 rounded-lg glass-effect">
            <h3 class="text-xl font-bold text-gray-800 mb-4">ภาพรวมสถานะเอกสารทั้งหมด</h3>
            <div class="h-64 md:h-80">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>

<!-- JavaScript สำหรับ Chart.js (ฉบับแก้ไข) -->
<script>
    // 1. รับข้อมูลจาก PHP
    const chartDataFromPHP = <?php echo json_encode($data['chartData']); ?>;

    // 2. เตรียมข้อมูลสำหรับ Chart.js
    const statusTranslations = {
        'received': 'ลงรับแล้ว',
        'pending_director': 'รอ ผอ. เกษียณ',
        'approved_director': 'ผอ. เกษียณแล้ว',
        'forwarded_to_dept': 'ส่งฝ่ายแล้ว',
        'pending_deputy_approval': 'รอ รองฯ เกษียณ',
        'pending_dept_admin_action': 'รอธุรการฝ่าย',
        'pending_section_head_action': 'รอหัวหน้างาน',
        'completed': 'เสร็จสิ้น'
    };
    
    const labels = chartDataFromPHP.map(item => statusTranslations[item.status] || item.status);
    const dataValues = chartDataFromPHP.map(item => item.count);

    // 3. สร้าง Chart
    const ctx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                label: 'จำนวนเอกสาร',
                data: dataValues,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',  // Blue
                    'rgba(255, 206, 86, 0.8)', // Yellow
                    'rgba(75, 192, 192, 0.8)',  // Green
                    'rgba(153, 102, 255, 0.8)',// Purple
                    'rgba(255, 159, 64, 0.8)', // Orange
                    'rgba(236, 72, 153, 0.8)', // Pink
                    'rgba(139, 92, 246, 0.8)', // Violet
                    'rgba(156, 163, 175, 0.8)' // Gray
                ],
                borderColor: 'rgba(255, 255, 255, 0.5)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        color: '#374151'
                    }
                },
                // ===== เพิ่มการตั้งค่าปลั๊กอิน Datalabels ตรงนี้ =====
                datalabels: {
                    formatter: (value, ctx) => {
                        // ไม่ต้องแสดงเลข 0
                        if (value === 0) {
                            return null;
                        }
                        return value;
                    },
                    color: '#fff', // สีตัวอักษร
                    font: {
                        weight: 'bold',
                        size: 16,
                    }
                }
            }
        },
        // ลงทะเบียนปลั๊กอินกับกราฟนี้
        plugins: [ChartDataLabels]
    });
</script>