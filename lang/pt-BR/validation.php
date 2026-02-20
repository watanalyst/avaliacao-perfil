<?php

return [
    'required'  => 'O campo :attribute é obrigatório.',
    'email'     => 'O campo :attribute deve ser um e-mail válido.',
    'confirmed' => 'A confirmação do campo :attribute não confere.',

    'min' => [
        'string' => 'O campo :attribute deve ter no mínimo :min caracteres.',
    ],

    'attributes' => [
        'name'     => 'nome',
        'email'    => 'e-mail',
        'password' => 'senha',
    ],
];
