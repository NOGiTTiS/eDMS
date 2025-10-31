<?php if ($data['totalPages'] > 1): ?>
<div class="mt-6 flex justify-between items-center">
    <div class="text-sm text-gray-600">
        หน้า <?php echo $data['currentPage']; ?> จาก <?php echo $data['totalPages']; ?>
    </div>
    <div class="flex items-center space-x-1">
        <!-- Previous Page Link -->
        <?php if ($data['currentPage'] > 1): ?>
            <a href="?page=<?php echo $data['currentPage'] - 1; ?>&search=<?php echo $data['searchTerm']; ?>" class="px-3 py-1 rounded-md bg-gray-200 hover:bg-gray-300">ก่อนหน้า</a>
        <?php endif; ?>

        <!-- Page Number Links (สามารถทำ Logic ให้ซับซ้อนขึ้นได้) -->
        <?php for ($i = 1; $i <= $data['totalPages']; $i++): ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo $data['searchTerm']; ?>" 
               class="px-3 py-1 rounded-md <?php echo ($i == $data['currentPage']) ? 'bg-pink-500 text-white' : 'bg-gray-200 hover:bg-gray-300'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <!-- Next Page Link -->
        <?php if ($data['currentPage'] < $data['totalPages']): ?>
            <a href="?page=<?php echo $data['currentPage'] + 1; ?>&search=<?php echo $data['searchTerm']; ?>" class="px-3 py-1 rounded-md bg-gray-200 hover:bg-gray-300">ถัดไป</a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>