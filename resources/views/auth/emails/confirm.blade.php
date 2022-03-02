<!DOCTYPE html>
<html>
<head>
    <title>Welcome to #EngageKilkenny</title>

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
                padding: 40px 15px 50px 15px !important;
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
    <!--[if gte mso 12]>
    <style type="text/css">
        .mso-right {
            padding-left: 20px;
        }
    </style>
    <![endif]-->
</head>
<body style="margin: 0 !important; padding: 0 !important;">

<!-- HIDDEN PREHEADER TEXT -->
<div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
    Engage with your historic town centre
</div>

<!-- HEADER -->
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="center" style="padding: 0px 15px 70px 15px;" class="section-padding">
            <table align="center" border="0" cellspacing="0" cellpadding="0" width="500">
                <tr>
                    <td align="center" valign="top" width="500">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 500px;" class="responsive-table">
                            <tr>
                                <td>
                                    <!-- HERO IMAGE -->
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="center" class="padding">
                                                <a href="https://www.engagekilkenny.com" target="_blank">
                                                    <img src="https://engagekilkenny.com/assets/ctchc_logo.png"
                                                         width="500"
                                                         border="0"
                                                         alt="CTCHC LOGO"
                                                         style="display: block; padding: 0; color: #266e9c; text-decoration: none; font-family: Helvetica, arial, sans-serif; font-size: 16px;"
                                                         class="img-max"
                                                    />
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <!-- COPY -->
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td align="center" style="font-size: 36px; font-family: Helvetica, Arial, sans-serif; color: #266e9c;" class="padding-copy"><strong>Engage with Kilkenny</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center" style="font-size: 18px; font-family: Helvetica, Arial, sans-serif; color: #266e9c; padding-top: 15px;" class="padding-copy">Verify your email below to get started</td>
                                                    </tr>
                                                </table>

                                                <div align="center" style="border-radius: 3px;margin-top: 50px;background-color: #2ecc71">
                                                    <a href="{{ route('confirm-email-token', $user->token) }}" target="_blank" style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #000000; text-decoration: none; border-radius: 3px; padding: 15px 25px; display: inline-block;" class="mobile-button">
                                                        Verify Your Email Address and Log In &rarr;
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="center" style="padding: 25px 15px 70px 15px;" class="section-padding">
            <table border="0" cellpadding="0" cellspacing="0" width="500" class="responsive-table">

                {{-- Copyright --}}
                <tr>
                    <td width="75%" align="center">
                        <br>
                        &copy; The Heritage Council, EngageKilkenny & Contributors 2022.
                        <br/>
                        <br/>
                    </td>
                </tr>

                {{-- Links to Social Media --}}

            </table>
        </td>
    </tr>
</table>
</body>
</html>
