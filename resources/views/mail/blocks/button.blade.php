<tr>
    <td class="block-button" style="padding: 0 0 20px 0;" align="{{ $block['align'] ?? 'left' }}">
        <!--[if mso]>
        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $block['url'] ?? '#' }}" style="height:44px;v-text-anchor:middle;width:200px;" arcsize="10%" strokecolor="{{ $block['color'] ?? '#3490dc' }}" fillcolor="{{ $block['color'] ?? '#3490dc' }}">
            <w:anchorlock/>
            <center style="color:#ffffff;font-family:Arial,sans-serif;font-size:16px;font-weight:bold;">{{ $block['text'] ?? 'Button' }}</center>
        </v:roundrect>
        <![endif]-->
        <!--[if !mso]><!-->
        <a href="{{ $block['url'] ?? '#' }}" style="background-color: {{ $block['color'] ?? '#3490dc' }}; color: #ffffff; display: inline-block; font-family: Arial, sans-serif; font-size: 16px; font-weight: bold; line-height: 44px; text-align: center; text-decoration: none; padding: 0 30px; border-radius: 4px; -webkit-text-size-adjust: none; mso-hide: all;">
            {{ $block['text'] ?? 'Button' }}
        </a>
        <!--<![endif]-->
    </td>
</tr>
