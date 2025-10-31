<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="p-8 rounded-lg glass-effect">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">รายงานสรุปประสิทธิภาพ</h1>

    <!-- ===== เพิ่มฟอร์มเลือกช่วงเวลา ===== -->
    <div class="p-4 mb-6 bg-white bg-opacity-50 rounded-lg">
        <form action="<?php echo URLROOT; ?>/report/summary" method="GET">
            <div class="flex items-end space-x-4">
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700">เลือกเดือน</label>
                    <select name="month" class="mt-1 block w-full input-field">
                        <option value="all">-- ทุกเดือน --</option>
                        <?php for ($m=1; $m<=12; $m++): ?>
                            <option value="<?php echo $m; ?>" <?php echo ($data['selectedMonth'] == $m) ? 'selected' : ''; ?>>
                                <?php echo date('F', mktime(0, 0, 0, $m, 10)); // แปลงเลขเป็นชื่อเดือน ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700">เลือกปี (พ.ศ.)</label>
                    <select name="year" class="mt-1 block w-full input-field">
                        <?php for ($y=date('Y')+543; $y>=date('Y')+543-5; $y--): // แสดง 5 ปีย้อนหลัง ?>
                            <option value="<?php echo $y; ?>" <?php echo ($data['selectedYear'] == $y) ? 'selected' : ''; ?>>
                                <?php echo $y; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <button type="submit" class="bg-[var(--theme-color)] text-white font-bold py-2 px-4 rounded-full hover:bg-[var(--theme-color-hover)]">ดูรายงาน</button>
                </div>
            </div>
        </form>
    </div>
    <!-- ===== จบฟอร์ม ===== -->

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card: จำนวนเอกสารทั้งหมดในช่วงเวลานี้ -->
        <div class="p-6 rounded-lg bg-white bg-opacity-50 text-center">
            <div class="text-3xl font-bold text-gray-800"><?php echo $data['totalDocuments']; ?></div>
            <div class="text-lg text-gray-700 mt-2">เอกสารทั้งหมด</div>
        </div>
        
        <!-- Card: เวลาอนุมัติของ ผอ. -->
        <div class="p-6 rounded-lg bg-white bg-opacity-50 text-center">
            <div class="text-3xl font-bold text-blue-600"><?php echo $data['avgDirectorApprovalTime']; ?></div>
            <div class="text-lg text-gray-700 mt-2">เวลาอนุมัติเฉลี่ย (ผอ.)</div>
        </div>

        <!-- Card: เวลาอนุมัติของ รองฯ ฝ่าย -->
        <div class="p-6 rounded-lg bg-white bg-opacity-50 text-center">
             <div class="text-3xl font-bold text-cyan-600"><?php echo $data['avgDeputyApprovalTime']; ?></div>
            <div class="text-lg text-gray-700 mt-2">เวลาอนุมัติเฉลี่ย (รองฯ)</div>
        </div>

         <!-- Card: จำนวนเอกสารที่เสร็จสิ้น -->
        <div class="p-6 rounded-lg bg-white bg-opacity-50 text-center">
            <div class="text-3xl font-bold text-green-600"><?php echo $data['completedDocuments']; ?></div>
            <div class="text-lg text-gray-700 mt-2">เอกสารที่เสร็จสิ้น</div>
        </div>
    </div>
    
    <!-- ส่วนของกราฟ (ตัวอย่าง) -->
    <div class="mt-8 p-6 bg-white bg-opacity-50 rounded-lg">
        <h2 class="text-xl font-semibold mb-4">กราฟสรุปจำนวนเอกสารรายวัน (ในเดือนที่เลือก)</h2>
        <div class="h-80"><canvas id="dailyChart"></canvas></div>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>

<script>
    // ดึงข้อมูลจาก Controller มาเตรียมสำหรับกราฟ
    const dailyData = <?php echo json_encode($data['dailyChartData'] ?? []); ?>;
    const daysInMonth = new Date(<?php echo $data['selectedYear']-543; ?>, <?php echo $data['selectedMonth']; ?>, 0).getDate();
    
    // สร้าง Array ของวันในเดือน (1, 2, 3, ..., 31)
    const labels = Array.from({length: daysInMonth}, (_, i) => i + 1);
    
    // สร้าง Array ของข้อมูล โดยที่ถ้าวันไหนไม่มีข้อมูลให้เป็น 0
    const dataValues = labels.map(day => {
        const found = dailyData.find(item => item.day == day);
        return found ? found.count : 0;
    });

    const ctx = document.getElementById('dailyChart').getContext('2d');
    const dailyChart = new Chart(ctx, {
        type: 'bar', // กราฟแท่ง
        data: {
            labels: labels,
            datasets: [{
                label: 'จำนวนเอกสารที่ลงรับ',
                data: dataValues,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
<!-- (JavaScript สำหรับกราฟจะอยู่ท้ายไฟล์) -->