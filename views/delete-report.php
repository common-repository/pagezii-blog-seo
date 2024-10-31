<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<div class="wrap">
<div style="padding: 10px 8px">
    <form method="post">
        <h1><?php echo $title; ?></h1>
        <p style="font-size: 19px;">Confirm deletion</p>
        <input type="hidden" name='postid' id='postid' value="<?php echo $_GET['delete']; ?>">
        <button class="button button-primary button-large" type="submit" style='margin-right: 10px;'>Delete</button>
        <a href="<?php echo $cancelLink; ?>" class="button button-large">Cancel</a>
        <?php wp_nonce_field( $nonce_field_name ); ?>
    </form>
</div>

</div>
