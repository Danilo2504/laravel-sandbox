<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="x-apple-disable-message-reformatting" />
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!--<![endif]-->
    <title>{{ $title ?? config('app.name') }}</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:AllowPNG/>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style type="text/css">
        /* Reset Styles */
        body {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        -webkit-text-size-adjust: 100%;
        -ms-text-size-adjust: 100%;
        }

        table {
        border-collapse: collapse !important;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
        }

        img {
        border: 0;
        height: auto;
        line-height: 100%;
        outline: none;
        text-decoration: none;
        -ms-interpolation-mode: bicubic;
        }

        a {
        color: #3490dc;
        text-decoration: none;
        }

        /* Responsive Styles */
        @media only screen and (max-width: 600px) {
        .email-container {
            width: 100% !important;
            max-width: 100% !important;
        }
        
        .email-header h1 {
            font-size: 24px !important;
        }
        
        .block-title h2 {
            font-size: 20px !important;
        }
        
        .block-text p {
            font-size: 14px !important;
        }
        
        .block-button a {
            width: 100% !important;
            display: block !important;
            box-sizing: border-box;
        }
        
        td[style*="padding: 40px 30px"] {
            padding: 20px 15px !important;
        }
        
        .email-header {
            padding: 20px 15px !important;
        }
        
        .email-footer {
            padding: 20px 15px !important;
        }
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
        body {
            background-color: #1a202c !important;
        }
        
        .email-container {
            background-color: #2d3748 !important;
        }
        
        .block-title h2 {
            color: #f7fafc !important;
        }
        
        .block-text p {
            color: #e2e8f0 !important;
        }
        
        .email-footer {
            background-color: #1a202c !important;
            border-top-color: #4a5568 !important;
        }
        }

        /* Outlook-Specific Styles */
        .ExternalClass {
        width: 100%;
        }

        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
        line-height: 100%;
        }

        /* Additional Utility Classes */
        .text-center {
        text-align: center !important;
        }

        .text-left {
        text-align: left !important;
        }

        .text-right {
        text-align: right !important;
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f7;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f4f7;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <!-- Main Container -->
                <table border="0" cellpadding="0" cellspacing="0" width="600" class="email-container" style="background-color: #ffffff; max-width: 600px;">
                    
                    <!-- Header -->
                    @include('mail.partials.header')
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <!-- Render Blocks -->
                                @if(isset($blocks) && is_array($blocks))
                                    @foreach($blocks as $block)
                                        @if(isset($block['type']))
                                            @include('mail.blocks.' . $block['type'], ['block' => $block])
                                        @endif
                                    @endforeach
                                @endif

                                <!-- Additional Content -->
                                @if(isset($content))
                                    <tr>
                                        <td style="padding: 0;">
                                            {!! $content !!}
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    @include('mail.partials.footer')
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
