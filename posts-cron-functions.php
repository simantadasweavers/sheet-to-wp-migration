<?php
// Hook for migration cron job
// add_action('posts_migration_cron_job', 'handle_posts_migration');

// function handle_posts_migration() {
//     // Add your migration logic here
//     // For example, processing posts or other tasks.
//     error_log('Migration process executed');
// }

// Function to schedule the migration cron job
// function schedule_posts_migration_cron() {
//     if (!wp_next_scheduled('posts_migration_cron_job')) {
//         // Schedule the event every 10 seconds (in reality, WP-Cron minimum interval is 1 minute, so you'll need to adjust this)
//         wp_schedule_event(time(), 'ten_seconds', 'posts_migration_cron_job');
//     }
// }

// // Function to unschedule the migration cron job (optional, if you need to stop it)
// function unschedule_posts_migration_cron() {
//     $timestamp = wp_next_scheduled('posts_migration_cron_job');
//     if ($timestamp) {
//         wp_unschedule_event($timestamp, 'posts_migration_cron_job');
//     }
// }

// add_filter('cron_schedules', 'custom_cron_schedules');
// function custom_cron_schedules($schedules) {
//     $schedules['ten_seconds'] = array(
//         'interval' => 10,  // Interval in seconds
//         'display'  => esc_html__('Every 10 Seconds'),
//     );
//     return $schedules;
// }


// // Schedule the cron job if it's not already scheduled
// function schedule_custom_cron_job() {
//     if (!wp_next_scheduled('custom_ten_second_event')) {
//         wp_schedule_event(time(), 'ten_seconds', 'custom_ten_second_event');
//     }
// }
// add_action('wp', 'schedule_custom_cron_job');


// // Hook our custom action to the scheduled event
// add_action('custom_ten_second_event', 'echo_message_every_ten_seconds');

// // Define the function to output the message
// function echo_message_every_ten_seconds() {
//     error_log('I am fine');  // Logs the message in the debug log
//     echo 'I am fine';  // Echoes the message on the frontend
// }


?>