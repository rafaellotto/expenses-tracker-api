<?php

return [
    'adminEmail' => env( 'MAIL_ADMIN', 'admin@example.com'),
    'senderEmail' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
    'senderName' => env('MAIL_FROM_NAME', 'Example.com mailer'),

    'paginationPageSize' => env('PAGINATION_PAGE_SIZE', 10),
];
