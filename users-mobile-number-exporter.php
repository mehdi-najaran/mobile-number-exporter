<?php

/**
 * Plugin Name: دریافت خروجی از شماره کاربران
 * Plugin URI: https://tarahimo.com/
 * Description: این پلاگین به شما کمک میکند تا به راحتی یک خروجی از کاربران سایتتون دریافت کنید 
 * Version: 1.1
 * Author: مهدی نجاران
 * Author URI: https://mehdi-najaran.ir/
 */

function users_mobile_number_exporter_add_admin_menu()
{
    add_submenu_page(
        'users.php',
        'بانک شماره',
        'بانک شماره',
        'manage_options',
        'users_mobile_number_exporter_admin_page',
        'users_mobile_number_exporter_admin_page'

    );
}

add_action('admin_menu', 'users_mobile_number_exporter_add_admin_menu');

function users_mobile_number_exporter_admin_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }
    $users = get_users();
    $phone_numbers = array();
    foreach ($users as $user) {
        $phone_number = get_user_meta($user->ID, 'eh_user_phone', true);
        if (!empty($phone_number)) {
            array_push($phone_numbers, $phone_number);
        }
    }
    $phone_numbers_text = implode("\n", $phone_numbers);
?>
    <div class="wrap">
        <h1>بانک شماره</h1>
        <p>شمار در این صفحه می توانید لیست کاملی از شماره های کاربران خود را دریافت کنید</p>
        <textarea rows='10' cols='50'><?php echo $phone_numbers_text; ?></textarea>
        <br>
        <a href="<?php echo esc_url(add_query_arg(array('download' => 'true'))); ?>" class="button button-primary">دانلود فایل</a>
    </div>
<?php
}

function users_mobile_number_exporter_download_file()
{
    if (isset($_GET['download']) && $_GET['download'] == 'true') {
        $filename = 'users_phone_numbers.txt';
        $users = get_users();
        $phone_numbers = array();
        foreach ($users as $user) {
            $phone_number = get_user_meta($user->ID, 'eh_user_phone', true);
            if (!empty($phone_number)) {
                array_push($phone_numbers, $phone_number);
            }
        }
        $phone_numbers_text = implode("\n", $phone_numbers);
        header('Content-Type: application/txt');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Pragma: no-cache');
        echo $phone_numbers_text;
        exit();
    }
}

add_action('admin_init', 'users_mobile_number_exporter_download_file');
