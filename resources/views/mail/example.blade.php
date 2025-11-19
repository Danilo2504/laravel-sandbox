@component('mail.default', [
    'title' => 'Bienvenido a Nuestra Aplicación',
    'blocks' => [
        [
            'type' => 'title',
            'text' => '¡Hola Usuario!',
            'size' => 28
        ],
        [
            'type' => 'text',
            'content' => 'Gracias por registrarte en nuestra aplicación. Estamos emocionados de tenerte con nosotros.'
        ],
        [
            'type' => 'spacer',
            'height' => 30
        ],
        [
            'type' => 'button',
            'text' => 'Activar Cuenta',
            'url' => 'https://example.com/activate',
            'color' => '#3490dc',
            'align' => 'center'
        ],
        [
            'type' => 'spacer',
            'height' => 30
        ],
        [
            'type' => 'divider'
        ],
        [
            'type' => 'text',
            'content' => 'Si tienes alguna pregunta, no dudes en contactarnos.'
        ]
    ]
])
@endcomponent
