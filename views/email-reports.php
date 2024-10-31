<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<!DOCTYPE html>
<html>
<head>
<title>Your report is ready</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<style type="text/css">
    /* CLIENT-SPECIFIC STYLES */
    body, table, td, a{-webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;} /* Prevent WebKit and Windows mobile changing default text sizes */
    table, td{mso-table-lspace: 0pt; mso-table-rspace: 0pt;} /* Remove spacing between tables in Outlook 2007 and up */
    img{-ms-interpolation-mode: bicubic;} /* Allow smoother rendering of resized image in Internet Explorer */

    /* RESET STYLES */
    img{border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none;}
    table{border-collapse: collapse !important;}
    body{height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important;}

    /* iOS BLUE LINKS */
    a[x-apple-data-detectors] {
        color: inherit !important;
        text-decoration: none !important;
        font-size: inherit !important;
        font-family: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
    }

    /* MOBILE STYLES */
    @media screen and (max-width: 525px) {

        /* ALLOWS FOR FLUID TABLES */
        .wrapper {
            width: 100% !important;
                max-width: 100% !important;
        }

        /* ADJUSTS LAYOUT OF LOGO IMAGE */
        .logo img {
            margin: 0 auto !important;
        }

        /* USE THESE CLASSES TO HIDE CONTENT ON MOBILE */
        .mobile-hide {
            display: none !important;
        }

        .img-max {
            max-width: 100% !important;
            width: 100% !important;
            height: auto !important;
        }

        /* FULL-WIDTH TABLES */
        .responsive-table {
            width: 100% !important;
        }

        /* UTILITY CLASSES FOR ADJUSTING PADDING ON MOBILE */
        .padding {
            padding: 10px 5% 15px 5% !important;
        }

        .padding-meta {
            padding: 30px 5% 0px 5% !important;
            text-align: center;
        }

        .padding-copy {
                 padding: 10px 5% 10px 5% !important;
            text-align: center;
        }

        .no-padding {
            padding: 0 !important;
        }

        .section-padding {
            padding: 50px 15px 50px 15px !important;
        }

        /* ADJUST BUTTONS ON MOBILE */
        .mobile-button-container {
                margin: 0 auto;
                width: 100% !important;
        }

        .mobile-button {
                padding: 15px !important;
                border: 0 !important;
                font-size: 16px !important;
                display: block !important;
        }

    }

    /* ANDROID CENTER FIX */
    div[style*="margin: 16px 0;"] { margin: 0 !important; }
</style>
</head>
<body style="margin: 0 !important; padding: 0 !important;">

<!-- ONE COLUMN SECTION -->
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td bgcolor="#ffffff" align="center" style="padding: 15px;" class="section-padding">
            <!--[if (gte mso 9)|(IE)]>
            <table align="center" border="0" cellspacing="0" cellpadding="0" width="500">
            <tr>
            <td align="center" valign="top" width="500">
            <![endif]-->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 500px;" class="responsive-table">
                <tr>
                    <td>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy"></td>
                                        </tr>
                                        <tr>
                                            <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">
                                                Hi there,<br><br>
                                                <?php echo nl2br(stripslashes(sanitize_textarea_field($message))); ?>
                                                <br>
                                                <!-- BULLETPROOF BUTTON -->
                                                <table class="expandmobile" width="220px" border="0" cellspacing="0" cellpadding="0">
                                                    <tbody><tr>
                                                        <td align="center" style="padding-top: 25px;" class="padding">
                                                            <table border="0" cellspacing="0" cellpadding="0" class="mobile-button-container">
                                                                <tbody><tr>
                                                                    <td align="center" style="border-radius: 3px;" bgcolor="#EFA64D"><a href="<?php echo $report_link; ?>" target="_blank" style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; border-radius: 3px; padding: 9px 20px; border: 1px solid #EFA64D; display: inline-block;" class="mobile-button">View Report →</a></td>
                                                                </tr>
                                                            </tbody></table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                </table>

                                                <br>
                                                Report analyzes <?php echo preg_replace('/http[s]*:\/\//i', '', $blogUrl); ?>
                                                <br><br>

                                                <?php echo date("F j, Y"); ?>
                                                <br>

                                                <br><br>
                                                Cheers,<br>
                                                The Pagezii Team
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!--[if (gte mso 9)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </td>
    </tr>
</table>

</body>
</html>
