<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="p-8 rounded-lg glass-effect">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">ทะเบียนหนังสือรับ</h1>
        <div class="flex items-center space-x-2">
            <!-- ===== เพิ่มปุ่ม Export ===== -->
            <a href="<?php echo URLROOT; ?>/report/exportRegister?search=<?php echo urlencode($data['searchTerm']); ?>" 
               class="bg-green-500 text-white font-bold py-2 px-4 rounded-full hover:bg-green-600 transition-colors">
                Export to Excel
            </a>

            <?php if ($_SESSION['user_role'] == 'central_admin'): ?>
                <a href="<?php echo URLROOT; ?>/document/add" class="bg-[var(--theme-color)] text-white font-bold py-2 px-4 rounded-full hover:bg-[var(--theme-color-hover)] transition duration-300">
                    ลงรับหนังสือใหม่
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php flash('doc_action_success'); ?>
    <?php flash('login_success'); ?>
    <?php flash('doc_action_fail'); ?>

    <div class="mb-4">
        <form action="<?php echo URLROOT; ?>/document" method="GET">
            <div class="relative">
                <input type="text" name="search" placeholder="ค้นหาตามเรื่อง, เลขทะเบียนรับ, เลขที่หนังสือ..."
                       class="w-full input-field pr-10"
                       value="<?php echo htmlspecialchars($data['searchTerm']); ?>">
                <button type="submit" class="absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 hover:text-pink-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto bg-white bg-opacity-75 rounded-lg">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-200 bg-opacity-50">
                <tr class="text-left">
                    <th class="p-3">วันที่ลงรับ</th>
                    <th class="p-3">เลขทะเบียนรับ</th>
                    <th class="p-3">ที่</th>
                    <th class="p-3">ลงวันที่</th>
                    <th class="p-3">จาก</th>
                    <th class="p-3">ถึง</th>
                    <th class="p-3">เรื่อง</th>
                    <th class="p-3">การปฏิบัติ</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['documents'] as $doc): ?>
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="p-3"><?php echo ! is_null($doc->registration_date) ? date('d/m/Y', strtotime($doc->registration_date)) : ''; ?></td>
                    <td class="p-3 font-semibold"><?php echo htmlspecialchars($doc->doc_registration_number); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($doc->doc_incoming_number); ?></td>
                    <td class="p-3"><?php echo ! is_null($doc->doc_date) ? date('d/m/Y', strtotime($doc->doc_date)) : ''; ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($doc->doc_from); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($doc->doc_to); ?></td>
                    <td class="p-3 w-1/4"><?php echo htmlspecialchars($doc->doc_subject); ?></td>
                    <td class="p-3"><?php echo formatStatus($doc->status); ?></td>
                    <td class="p-3">
                        <!-- ใช้ Flexbox เพื่อจัดเรียงปุ่ม -->
                        <div class="flex items-center space-x-2">
                            <!-- ปุ่ม "ดูรายละเอียด" -->
                            <a href="<?php echo URLROOT; ?>/document/show/<?php echo $doc->documentId; ?>"
                               class="bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-semibold hover:bg-blue-600 transition-colors">
                                ดู
                            </a>

                            <!-- ปุ่ม "แก้ไข" (จะแสดงเมื่อตรงตามเงื่อนไข) -->
                            <?php if ($doc->created_by_id == $_SESSION['user_id'] && $doc->status == 'received'): ?>
                                <a href="<?php echo URLROOT; ?>/document/edit/<?php echo $doc->documentId; ?>"
                                   class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-semibold hover:bg-yellow-600 transition-colors">
                                    แก้ไข
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php require APPROOT . '/views/inc/_pagination.php'; ?>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>