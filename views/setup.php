<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<div class="wrap">
<h2>Plugin Setup</h2>

<?php
    if($_POST){
        echo '<div class="wp-core-ui"><div class="notice notice-success"><p style="font-size:16px;">Updated Settings</p></div></div>';
    }
?>

<form method="POST" enctype="multipart/form-data">
    <table class="form-table" id="pagezii-snapshot-form">
        <tbody>
            <tr class="form-field form-required">
                <th scope="row"><label for="to_email">Admin Email</label></th>
                <td>
                    <input type="text" name="to_email" id="to_email" value="<?php echo sanitize_email($pz_settings['to_email']); ?>" placeholder="jsmith@bestagency-acme.com">
                    <p class='text-muted'>The email address for emailing reports to.</p>
                </td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row"><label for="report_day">Report Day</label></th>
                <td>
                    <?php
                        $weekDays = [
                                '1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday',
                                '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday',
                                '7' => 'Sunday',
                        ];
                     ?>
                    <select name="report_day" id="report_day">
                        <?php
                            foreach($weekDays as $key => $value) {
                                $checked = $pz_settings['report_day'] == $key ? 'selected' : '';
                                echo "<option value='$key' $checked>$value</option>";
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row"><label for="pulse_reports">Pulse Report</label></th>
                <td>
                    <input type="checkbox" name="pulse_reports" id="pulse_reports" value="1" <?php echo $pz_settings['pulse_reports'] ? 'checked' : ''; ?> >
                    <p class='text-muted'><label for="pulse_reports">Turn on automatic pulse reports</label></p>
                </td>
            </tr>

            <tr class="form-field form-required">
                <th scope="row"><label for="author_reports">Author Report</label></th>
                <td>
                    <input type="checkbox" name="author_reports" id="author_reports" value="1" <?php echo $pz_settings['author_reports'] ? 'checked' : ''; ?> >
                    <p class='text-muted'><label for="author_reports">Turn on automatic author reports</label></p>
                </td>
            </tr>
        </tbody>
    </table>

    <?php wp_nonce_field( 'save-general-settings' ); ?>

    <p class="submit"><input type="submit" name="updatesettings" id="updatesettings" class="button button-primary" value="Update Settings"></p>
</form>
