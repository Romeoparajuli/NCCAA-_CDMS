<!-- dashboard_cards.php -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
    <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold">New Reports</h3>
        <p class="text-2xl"><?php echo $new_reports; ?></p>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold">Total Cadets</h3>
        <p class="text-2xl"><?php echo $total_cadets; ?></p>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold">Notices</h3>
        <p class="text-2xl"><?php echo $notices; ?></p>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold">Notifications</h3>
        <p class="text-2xl"><?php echo $notifications; ?></p>
    </div>
</div>
