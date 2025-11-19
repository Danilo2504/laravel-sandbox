<tr>
    <td class="email-footer" style="background-color: #f8fafc; padding: 30px; text-align: center; border-top: 1px solid #e2e8f0;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td align="center" style="padding-bottom: 10px;">
                    <p style="margin: 0; color: #718096; font-size: 14px; font-family: Arial, sans-serif;">
                        © {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
                    </p>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <p style="margin: 0; color: #a0aec0; font-size: 12px; font-family: Arial, sans-serif;">
                        Si tienes alguna pregunta, contáctanos en <a href="mailto:{{ config('mail.from.address') }}" style="color: #3490dc; text-decoration: none;">{{ config('mail.from.address') }}</a>
                    </p>
                </td>
            </tr>
        </table>
    </td>
</tr>
