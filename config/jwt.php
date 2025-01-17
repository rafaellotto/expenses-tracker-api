<?php

return [
    'class' => \bizley\jwt\Jwt::class,
    'signer' => \bizley\jwt\Jwt::HS256,
    'signingKey' => [
        'key' => env('JWT_SECRET_KEY'),
        'method' => \bizley\jwt\Jwt::METHOD_BASE64,
    ],
    'validationConstraints' => function (\bizley\jwt\Jwt $jwt) {
        $config = $jwt->getConfiguration();
        return [
            new \Lcobucci\JWT\Validation\Constraint\SignedWith(
                $config->signer(),
                $config->verificationKey()
            ),
        ];
    }
];