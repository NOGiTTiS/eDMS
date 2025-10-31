<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="flex justify-between items-center mb-6">
    <!-- ปุ่มกลับ -->
    <a href="<?php echo URLROOT; ?>/document" class="text-blue-600 hover:text-blue-800"><i class="fas fa-chevron-left"></i> กลับไปหน้ารายการ</a>

    <!-- ===== เพิ่มส่วนปุ่ม Action (แก้ไข/ลบ) ===== -->
    <?php if(isset($data['document']) && $data['document']->created_by_id == $_SESSION['user_id'] && $data['document']->status == 'received'): ?>
    <div class="flex items-center space-x-4">
        <a href="<?php echo URLROOT; ?>/document/edit/<?php echo $data['document']->id; ?>" class="bg-yellow-500 text-white font-bold py-2 px-4 rounded-full hover:bg-yellow-600 transition-colors">แก้ไขเอกสาร</a>
        <form action="<?php echo URLROOT; ?>/document/delete/<?php echo $data['document']->id; ?>" method="post" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบเอกสารนี้? การกระทำนี้ไม่สามารถย้อนกลับได้');">
            <button type="submit" class="bg-red-500 text-white font-bold py-2 px-4 rounded-full hover:bg-red-600 transition-colors">ลบเอกสาร</button>
        </form>
    </div>
    <?php endif; ?>
    <!-- ===== จบส่วนปุ่ม Action ===== -->
</div>

<?php flash('doc_action_success'); ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- คอลัมน์ซ้าย: ข้อมูลหลักและไฟล์แนบ -->
    <div class="md:col-span-2">
        <div class="p-6 rounded-lg glass-effect">
            <h1 class="text-2xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($data['document']->doc_subject); ?></h1>
            <div><?php echo formatStatus($data['document']->status); ?></div>

            <div class="border-t border-gray-300 my-4"></div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><strong>เลขทะเบียนรับ:</strong>                                                                                                                                                             <?php echo htmlspecialchars($data['document']->doc_registration_number); ?></div>
                <div><strong>วันที่ลงรับ:</strong>                                                                                                                                                 <?php echo date('d/m/Y', strtotime($data['document']->registration_date)); ?></div>
                <div><strong>ที่:</strong>                                                                                                 <?php echo htmlspecialchars($data['document']->doc_incoming_number); ?></div>
                <div><strong>ลงวันที่:</strong>                                                                                                                               <?php echo date('d/m/Y', strtotime($data['document']->doc_date)); ?></div>
                <div><strong>จาก:</strong>                                                                                                 <?php echo htmlspecialchars($data['document']->doc_from); ?></div>
                <div><strong>ถึง:</strong>                                                                                                 <?php echo htmlspecialchars($data['document']->doc_to); ?></div>
                <div class="col-span-2"><strong>หมายเหตุ:</strong>                                                                                                                                                                     <?php echo htmlspecialchars($data['document']->remarks); ?></div>
            </div>

            <!-- ส่วนไฟล์แนบ -->
            <!-- ########## เพิ่มโค้ดส่วนนี้เข้ามาใหม่ทั้งหมด ########## -->
            <?php if (! empty($data['files'])): ?>
                <div class="mt-6 p-6 rounded-lg glass-effect">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">เอกสารแนบ</h2>
                    <?php foreach ($data['files'] as $file): ?>
                        <?php
                            // ดึงนามสกุลไฟล์ออกมาแล้วแปลงเป็นตัวพิมพ์เล็ก
                            $fileExtension = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));
                        ?>

                        <div class="mt-4">
                            <?php if ($fileExtension == 'pdf'): ?>
                                <!-- แสดงผลไฟล์ PDF ด้วย Iframe -->
                                <iframe src="<?php echo URLROOT . '/' . $file->file_path; ?>" class="w-full h-[800px] border rounded-md"></iframe>

                            <?php elseif (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                <!-- แสดงผลไฟล์รูปภาพ -->
                                <img src="<?php echo URLROOT . '/' . $file->file_path; ?>" alt="<?php echo htmlspecialchars($file->original_file_name); ?>" class="w-full rounded-md border">

                            <?php else: ?>
                                <!-- ถ้าไม่ใช่ PDF หรือรูปภาพ ให้แสดงเป็นลิงก์ดาวน์โหลด -->
                                <a href="<?php echo URLROOT . '/' . $file->file_path; ?>" target="_blank" class="inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded-full hover:bg-blue-600">
                                    ดาวน์โหลด:                                                                                                                                 <?php echo htmlspecialchars($file->original_file_name); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <!-- ###################################################### -->
        </div>

        <!-- ส่วนฟอร์ม Action (ปรับปรุงให้รองรับทุกบทบาท) -->

        <!-- ===== 1. ฟอร์มส่งต่อของธุรการกลาง ===== -->
        <?php if ($data['document']->status == 'received' && $_SESSION['user_role'] == 'central_admin'): ?>
        <div class="mt-6 p-6 rounded-lg glass-effect">
            <h2 class="text-xl font-bold text-gray-800 mb-4">ดำเนินการ (ส่งต่อให้ ผอ.)</h2>
            <form action="<?php echo URLROOT; ?>/document/forward/<?php echo $data['document']->id; ?>" method="post">
                <div>
                    <label for="forward_to_id" class="block text-sm font-medium text-gray-700">เรียน (ผอ.)</label>
                    <select name="forward_to_id" id="forward_to_id" class="mt-1 block w-full input-field">
                        <option value="">-- กรุณาเลือก --</option>
                        <?php foreach ($data['directors'] as $director): ?>
                            <option value="<?php echo $director->id; ?>"><?php echo $director->full_name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mt-4">
                    <label for="comment" class="block text-sm font-medium text-gray-700">ข้อสั่งการ / เกษียรหนังสือ</label>
                    <textarea name="comment" id="comment" rows="4" class="mt-1 block w-full input-field" placeholder="เช่น เพื่อโปรดทราบ, เพื่อโปรดพิจารณา"></textarea>
                </div>
                <div class="mt-4 flex justify-end">
                     <button type="submit" class="bg-[var(--theme-color)] text-white font-bold py-2 px-4 rounded-full hover:bg-[var(--theme-color-hover)]">ส่งเรื่อง</button>
                </div>
            </form>
        </div>
        <?php endif; ?>


        <!-- ===== 2. ฟอร์มเกษียณหนังสือของ ผอ. ===== -->
        <?php
            // เงื่อนไข: 1. สถานะต้องเป็น pending_director
            //         2. ผู้ที่ Login อยู่ ต้องเป็นคนเดียวกับที่เอกสารถูกส่งถึง
            if ($data['document']->status == 'pending_director' && $_SESSION['user_id'] == $data['forwarded_to_id']):
        ?>
        <div class="mt-6 p-6 rounded-lg glass-effect" id="approval-section">
            <h2 class="text-xl font-bold text-gray-800 mb-4">เกษียณหนังสือ (สำหรับ ผอ.)</h2>
            <form id="approval-form" action="<?php echo URLROOT; ?>/document/approve/<?php echo $data['document']->id; ?>" method="post">
                <!-- Hidden input สำหรับเก็บข้อมูลลายเซ็น -->
                <input type="hidden" name="signature" id="signature-data">

                <!-- Canvas สำหรับการลงนาม -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">ลงนามอิเล็กทรอนิกส์</label>
                    <div class="mt-1 relative w-full h-48 bg-gray-200 rounded-md">
                        <canvas id="signature-pad" class="absolute top-0 left-0 w-full h-full"></canvas>
                    </div>
                    <button type="button" id="clear-signature" class="text-sm text-blue-600 hover:underline mt-1">ล้างลายเซ็น</button>
                </div>

                <div class="mt-4">
                    <label for="comment" class="block text-sm font-medium text-gray-700">ข้อสั่งการ</label>
                    <textarea name="comment" id="comment" rows="3" class="mt-1 block w-full input-field" placeholder="เช่น ทราบ/อนุมัติ, เห็นควร..."></textarea>
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="bg-green-500 text-white font-bold py-2 px-4 rounded-full hover:bg-green-600">อนุมัติและลงนาม</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- ===== 3. ฟอร์มส่งต่อของธุรการกลาง (หลัง ผอ. อนุมัติ) ===== -->
        <?php if ($data['document']->status == 'approved_director' && $_SESSION['user_role'] == 'central_admin'): ?>
        <div class="mt-6 p-6 rounded-lg glass-effect">
            <h2 class="text-xl font-bold text-gray-800 mb-4">ดำเนินการ (ส่งต่อให้ฝ่าย)</h2>
            <form action="<?php echo URLROOT; ?>/document/forwardToDept/<?php echo $data['document']->id; ?>" method="post">
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700">ส่งถึง (ฝ่าย)</label>
                    <select name="department_id" id="department_id" class="mt-1 block w-full input-field">
                        <option value="">-- กรุณาเลือกฝ่าย --</option>
                        <?php foreach ($data['departments'] as $dept): ?>
                            <option value="<?php echo $dept->id; ?>"><?php echo $dept->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mt-4">
                    <label for="comment" class="block text-sm font-medium text-gray-700">ข้อความเพิ่มเติม</label>
                    <textarea name="comment" id="comment" rows="3" class="mt-1 block w-full input-field" placeholder="เช่น ส่งต่อเพื่อดำเนินการในส่วนที่เกี่ยวข้อง"></textarea>
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="bg-indigo-500 text-white font-bold py-2 px-4 rounded-full hover:bg-indigo-600">ส่งเรื่องต่อให้ฝ่าย</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- ===== 4. ฟอร์มส่งต่อของธุรการฝ่าย (ส่งให้ รอง ผอ. ฝ่าย) ===== -->
        <?php if ($data['document']->status == 'forwarded_to_dept' && $_SESSION['user_id'] == $data['forwarded_to_id']): ?>
        <div class="mt-6 p-6 rounded-lg glass-effect">
            <h2 class="text-xl font-bold text-gray-800 mb-4">ดำเนินการ (ส่งต่อให้ รอง ผอ. ฝ่าย)</h2>
            <form action="<?php echo URLROOT; ?>/document/forwardToDeputy/<?php echo $data['document']->id; ?>" method="post">
                <div>
                    <label for="forward_to_id" class="block text-sm font-medium text-gray-700">เรียน (รอง ผอ. ฝ่าย)</label>
                    <select name="forward_to_id" id="forward_to_id" class="mt-1 block w-full input-field">
                        <option value="">-- กรุณาเลือก --</option>
                        <?php foreach ($data['deputyDirectors'] as $deputy): ?>
                            <option value="<?php echo $deputy->id; ?>"><?php echo $deputy->full_name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mt-4">
                    <label for="comment" class="block text-sm font-medium text-gray-700">ข้อความเพิ่มเติม</label>
                    <textarea name="comment" id="comment" rows="3" class="mt-1 block w-full input-field"></textarea>
                </div>
                <div class="mt-4 flex justify-end">
                     <button type="submit" class="bg-cyan-500 text-white font-bold py-2 px-4 rounded-full hover:bg-cyan-600">ส่งเรื่องให้ รองฯ</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- ===== 5. ฟอร์มเกษียณของ รอง ผอ. ฝ่าย (ฉบับแก้ไขที่ถูกต้อง) ===== -->
        <?php if ($data['document']->status == 'pending_deputy_approval' && $_SESSION['user_id'] == $data['forwarded_to_id']): ?>
        <div class="mt-6 p-6 rounded-lg glass-effect" id="approval-section">
            <h2 class="text-xl font-bold text-gray-800 mb-4">เกษียณหนังสือ (สำหรับ รอง ผอ. ฝ่าย)</h2>
            <!-- แก้ไข action ให้เรียกเมธอดที่ถูกต้อง -->
            <form id="approval-form" action="<?php echo URLROOT; ?>/document/approveDeputy/<?php echo $data['document']->id; ?>" method="post">
                <input type="hidden" name="signature" id="signature-data">

                <!-- Canvas สำหรับการลงนาม -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">ลงนามอิเล็กทรอนิกส์</label>
                    <div class="mt-1 relative w-full h-48 bg-gray-200 rounded-md">
                        <canvas id="signature-pad" class="absolute top-0 left-0 w-full h-full"></canvas>
                    </div>
                    <button type="button" id="clear-signature" class="text-sm text-blue-600 hover:underline mt-1">ล้างลายเซ็น</button>
                </div>

                <div class="mt-4">
                    <label for="comment" class="block text-sm font-medium text-gray-700">ข้อสั่งการ</label>
                    <textarea name="comment" id="comment" rows="3" class="mt-1 block w-full input-field" placeholder="เช่น เห็นควรอนุมัติ, มอบหมาย..."></textarea>
                </div>

                <!-- (ไม่มี Dropdown เลือกหัวหน้างานแล้ว) -->

                <div class="mt-4 flex justify-end">
                     <button type="submit" class="bg-teal-500 text-white font-bold py-2 px-4 rounded-full hover:bg-teal-600">อนุมัติและส่งคืนธุรการฝ่าย</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- ===== 6. ฟอร์มดำเนินการของธุรการฝ่าย (หลัง รองฯ อนุมัติ) ===== -->
        <?php if ($data['document']->status == 'pending_dept_admin_action' && $_SESSION['user_role'] == 'dept_admin'): ?>
        <div class="mt-6 p-6 rounded-lg glass-effect">
            <h2 class="text-xl font-bold text-gray-800 mb-4">ดำเนินการขั้นสุดท้าย (สำหรับธุรการฝ่าย)</h2>
            <div class="border-t pt-4">
                <!-- ส่วนที่ 1: ส่งต่อให้หัวหน้างาน (ในระบบ) -->
                <form action="<?php echo URLROOT; ?>/document/assignToSectionHead/<?php echo $data['document']->id; ?>" method="post" class="mb-4">
                    <h3 class="font-semibold text-gray-700">1. มอบหมายงานให้หัวหน้างาน (ในระบบ)</h3>
                    <div class="mt-2">
                        <label for="forward_to_id" class="sr-only">หัวหน้างาน</label>
                        <select name="forward_to_id" class="w-full input-field">
                            <option value="">-- กรุณาเลือกหัวหน้างาน --</option>
                            <?php foreach ($data['sectionHeads'] as $head): ?>
                                <option value="<?php echo $head->id; ?>"><?php echo $head->full_name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mt-2">
                         <textarea name="comment" rows="2" class="w-full input-field" placeholder="ข้อความเพิ่มเติม (ถ้ามี)"></textarea>
                    </div>
                    <div class="mt-2 text-right">
                        <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded-full hover:bg-blue-600">มอบหมายงาน</button>
                    </div>
                </form>

                <!-- เส้นคั่น -->
                <div class="my-4 text-center text-gray-500">หรือ</div>

                <!-- ส่วนที่ 2: ปิดงาน (นอกระบบ) -->
                 <form action="<?php echo URLROOT; ?>/document/closeAndExport/<?php echo $data['document']->id; ?>" method="post">
                    <h3 class="font-semibold text-gray-700">2. สิ้นสุดเอกสาร (ส่งออก/ปฏิบัติเอง)</h3>
                    <div class="mt-2">
                        <textarea name="comment" rows="2" class="w-full input-field" placeholder="ระบุรายละเอียดการดำเนินการ เช่น 'ส่งอีเมลให้หัวหน้างานแล้ว' หรือ 'ดำเนินการจัดเก็บเข้าแฟ้ม'"></textarea>
                    </div>
                    <div class="mt-2 text-right">
                        <button type="submit" class="bg-green-500 text-white font-bold py-2 px-4 rounded-full hover:bg-green-600">ปิดงาน</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- ===== 7. ฟอร์มรับทราบของหัวหน้างาน ===== -->
        <?php if ($data['document']->status == 'pending_section_head_action' && $_SESSION['user_id'] == $data['forwarded_to_id']): ?>
        <div class="mt-6 p-6 rounded-lg glass-effect">
            <h2 class="text-xl font-bold text-gray-800 mb-4">ดำเนินการ (สำหรับหัวหน้างาน)</h2>
            <form action="<?php echo URLROOT; ?>/document/acknowledgeTask/<?php echo $data['document']->id; ?>" method="post">
                <div class="mt-2">
                    <label for="comment" class="block text-sm font-medium text-gray-700">บันทึกการปฏิบัติงาน (ถ้ามี)</label>
                    <textarea name="comment" id="comment" rows="3" class="w-full input-field" placeholder="เช่น รับทราบ, จะดำเนินการในวันที่..."></textarea>
                </div>
                <div class="mt-4 text-right">
                    <button type="submit" class="bg-emerald-500 text-white font-bold py-2 px-4 rounded-full hover:bg-emerald-600">รับทราบและดำเนินการ</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- จบส่วนฟอร์ม Action -->

    </div>

    <!-- คอลัมน์ขวา: ประวัติเอกสาร (Flow) -->
    <div class="p-6 rounded-lg glass-effect">
        <h2 class="text-xl font-bold text-gray-800 mb-4">เส้นทางเอกสาร</h2>
        <div>
            <ol class="relative border-l border-gray-400">
                <?php foreach ($data['flow'] as $flow_item): ?>
                <li class="mb-6 ml-4">
                    <div class="absolute w-3 h-3 bg-gray-500 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                    <time class="mb-1 text-sm font-normal leading-none text-gray-500"><?php echo date('d/m/Y H:i', strtotime($flow_item->created_at)); ?> น.</time>
                    <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($flow_item->full_name); ?></h3>

                    <?php
                        // Logic แสดงข้อความตาม Action
                        $actionText = '';
                        switch ($flow_item->action) {
                            case 'receive':
                                $actionText = 'ลงรับหนังสือเข้าระบบ';
                                break;
                            case 'forward_to_director':
                                $actionText = 'ส่งเรื่องต่อให้ ผอ.';
                                break;
                            case 'approve_by_director':
                                $actionText = 'เกษียณหนังสือ (ผอ.)';
                                break;
                            case 'forward_to_dept':
                                $actionText = 'ส่งเรื่องต่อให้ฝ่าย';
                                break;
                            case 'forward_to_deputy':
                                $actionText = 'ส่งเรื่องต่อให้ รองฯ ฝ่าย';
                                break;
                            case 'approve_by_deputy':
                                $actionText = 'เกษียณหนังสือ (รองฯ ฝ่าย)';
                                break;
                            case 'forward_to_section_head':
                                $actionText = 'มอบหมายงานให้หัวหน้างาน';
                                break;
                            case 'acknowledged_by_section_head':
                                $actionText = 'รับทราบและดำเนินการ';
                                break;
                            case 'complete_and_export':
                                $actionText = 'ปิดงาน/ส่งออก';
                                break;
                        }
                    ?>
                    <p class="text-sm font-semibold text-gray-700"><?php echo $actionText; ?></p>
                    <p class="text-base font-normal text-gray-600 mt-1"><?php echo htmlspecialchars($flow_item->comment); ?></p>

                    <?php if (! empty($flow_item->signature_path)): ?>
                        <div class="mt-2 p-2 border border-gray-300 rounded-md inline-block bg-white bg-opacity-50">
                            <img src="<?php echo URLROOT . '/' . $flow_item->signature_path; ?>" alt="ลายเซ็น" class="h-16">
                        </div>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>

<!-- ########## เพิ่ม JavaScript ท้ายไฟล์ ########## -->
<script>
    // ตรวจสอบว่ามี element ของ signature pad หรือไม่
    if (document.getElementById('signature-pad')) {
        const canvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)' // ทำให้พื้นหลังโปร่งใส
        });

        // ฟังก์ชันปรับขนาด canvas ให้พอดีกับ container
        function resizeCanvas() {
            const ratio =  Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear(); // เคลียร์ลายเซ็นเมื่อปรับขนาด
        }

        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();

        // ปุ่มล้างลายเซ็น
        const clearButton = document.getElementById('clear-signature');
        clearButton.addEventListener('click', function () {
            signaturePad.clear();
        });

        // ดักจับ Event ก่อน submit form
        const form = document.getElementById('approval-form');
        form.addEventListener('submit', function (event) {
            if (signaturePad.isEmpty()) {
                Swal.fire({
                    icon: 'error',
                    title: 'ข้อผิดพลาด',
                    text: 'กรุณาลงนามก่อนอนุมัติเอกสาร',
                });
                event.preventDefault(); // หยุดการ submit
            } else {
                // แปลงลายเซ็นเป็น Base64 แล้วยัดใส่ hidden input
                const signatureData = signaturePad.toDataURL('image/png');
                document.getElementById('signature-data').value = signatureData;
            }
        });
    }
</script>