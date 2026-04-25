
<?php
    $hp = config('homepage', []);
    $logo = $hp['logo'] ?? ['src' => 'logo.png', 'alt' => 'GNAT Association'];
    $nav = $hp['nav'] ?? [];
    $contact = $hp['contact'] ?? [
        'email' => '',
        'address' => '',
        'phones' => [],
    ];
?>
<?php echo $__env->make('home.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\member\partials\public-site-header.blade.php ENDPATH**/ ?>