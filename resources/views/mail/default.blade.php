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
        {!! file_get_contents(public_path('css/mail.css')) !!}
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
