<?php

/**
 * Plugin Name: دریافت خروجی از شماره کاربران
 * Description: این پلاگین به شما کمک میکند تا به راحتی یک خروجی از کاربران سایتتون دریافت کنید 
 * Version: 1.1.0
 * Author: مهدی نجاران
 * Author URI: https://mehdi-najaran.ir/
 */

// add admin menu
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

// add link style adnd script
function admin_enqueue_scripts()
{
    wp_enqueue_script('custom-js', plugin_dir_url(__FILE__) . 'js/app.js');
    wp_enqueue_style('style-css', plugin_dir_url(__FILE__) . 'css/style.css');
}
add_action('admin_enqueue_scripts', 'admin_enqueue_scripts');

// main page
function users_mobile_number_exporter_admin_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }
    $users = get_users();
    $phone_numbers = array();
    foreach ($users as $user) {
        $phone_number = get_user_meta($user->ID, 'billing_phone', true);
        if (!empty($phone_number)) {
            array_push($phone_numbers, $phone_number);
        }
    }
    $phone_numbers_text = implode("\n", $phone_numbers);
    $total_numbers = count($phone_numbers);

?>

    <div class="wrap">

        <div class="header">
            <div class="logo">
                <img src="<?php echo plugin_dir_url(__FILE__) . 'img/logo.png'; ?>" alt="logo">
            </div>
            <div class="title">
                <h1>بانک شماره</h1>
                <p>شمار در این صفحه می توانید لیست کاملی از شماره های کاربران خود را دریافت کنید</p>
            </div>
        </div>
        <div class="main">
            <div id="loader">
                <div id="loader-inner"></div>
            </div>
            <div class="col-right">
                <textarea rows='15' cols='50'><?php echo $phone_numbers_text; ?></textarea>
            </div>
            <div class="col-left">
                <p> شماره های در دسترس : <?php echo $total_numbers ?> عدد </p>
                <a href="<?php echo esc_url(add_query_arg(array('download' => 'true'))); ?>" class="button">دانلود فایل</a>

            </div>
        </div>
        <div class="footer">
            <p>© copyright by mehdi najaran</p>
        </div>
    </div>
<?php
}

// Configure exporter button
function users_mobile_number_exporter_download_file()
{
    if (isset($_GET['download']) && $_GET['download'] == 'true') {
        $filename = 'users_phone_numbers.txt';
        $users = get_users();
        $phone_numbers = array();
        foreach ($users as $user) {
            $phone_number = get_user_meta($user->ID, 'billing_phone', true);
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
