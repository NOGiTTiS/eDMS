<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="flex justify-center">
    <div class="w-full lg:w-3/4 p-8 rounded-lg glass-effect">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">ลงรับหนังสือใหม่</h1>
        
        <form action="<?php echo URLROOT; ?>/document/add" method="post" enctype="multipart/form-data">
            <!-- แถวที่ 1: วันที่ลงรับ, เลขทะเบียนรับ -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="registration_date" class="block text-sm font-medium text-gray-700">วันที่ลงรับ</label>
                    <input type="date" name="registration_date" id="registration_date" class="mt-1 block w-full input-field" value="<?php echo $data['registration_date']; ?>">
                </div>
                <div>
                    <label for="doc_registration_number" class="block text-sm font-medium text-gray-700">เลขทะเบียนรับ (อัตโนมัติ)</label>
                    <input type="text" name="doc_registration_number" id="doc_registration_number" class="mt-1 block w-full input-field bg-gray-200" value="<?php echo $data['doc_registration_number']; ?>" readonly>
                </div>
            </div>

            <!-- แถวที่ 2: เลขที่หนังสือ, ลงวันที่ -->
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="doc_incoming_number" class="block text-sm font-medium text-gray-700">ที่ (เลขหนังสือต้นทาง)</label>
                    <input type="text" name="doc_incoming_number" id="doc_incoming_number" class="mt-1 block w-full input-field" value="<?php echo $data['doc_incoming_number']; ?>">
                </div>
                <div>
                    <label for="doc_date" class="block text-sm font-medium text-gray-700">ลงวันที่ (ในหนังสือ)</label>
                    <input type="date" name="doc_date" id="doc_date" class="mt-1 block w-full input-field" value="<?php echo $data['doc_date']; ?>">
                </div>
            </div>

             <!-- แถวที่ 3: จาก, ถึง -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="doc_from" class="block text-sm font-medium text-gray-700">จาก</label>
                    <input type="text" name="doc_from" id="doc_from" class="mt-1 block w-full input-field" value="<?php echo $data['doc_from']; ?>">
                </div>
                <div>
                    <label for="doc_to" class="block text-sm font-medium text-gray-700">ถึง</label>
                    <input type="text" name="doc_to" id="doc_to" class="mt-1 block w-full input-field" value="<?php echo $data['doc_to']; ?>">
                </div>
            </div>

            <!-- แถวที่ 4: เรื่อง -->
            <div class="mt-4">
                <label for="doc_subject" class="block text-sm font-medium text-gray-700">เรื่อง *</label>
                <textarea name="doc_subject" id="doc_subject" rows="3" class="mt-1 block w-full input-field <?php echo (!empty($data['doc_subject_err'])) ? 'border-red-500' : 'border-gray-300'; ?>"><?php echo $data['doc_subject']; ?></textarea>
                <span class="text-red-500 text-sm"><?php echo $data['doc_subject_err']; ?></span>
            </div>

            <!-- แถวที่ 5: หมายเหตุ และ ไฟล์แนบ -->
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="remarks" class="block text-sm font-medium text-gray-700">หมายเหตุ</label>
                    <input type="text" name="remarks" id="remarks" class="mt-1 block w-full input-field" value="<?php echo $data['remarks']; ?>">
                </div>
                <div>
                    <label for="document_file" class="block text-sm font-medium text-gray-700">แนบไฟล์</label>
                    <input type="file" name="document_file" id="document_file" class="mt-1 block w-full text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100">
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <a href="<?php echo URLROOT; ?>/document" class="bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-full hover:bg-gray-400">ยกเลิก</a>
                <button type="submit" class="bg-pink-500 text-white font-bold py-2 px-4 rounded-full hover:bg-pink-600">บันทึก</button>
            </div>
        </form>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>