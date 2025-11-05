<?php

return [

 
    'api' => [
        'create_transaction'   => 'https://sandboxtest.space/en/purchase/create-transaction',
        'get_deposit_details'  => 'https://sandboxtest.space/en/purchase/get-deposit-details',
        'validate_transaction' => 'https://sandboxtest.space/api/v1/validate-transaction',
    ],

    // 🔑 Merchant Keys (use .env for production)
    'merchant' => [
        'primary'  => '$2y$10$VqOdRRE21p6/mGJLjyglk.J7zFM7tusWPQRcVtG7fbCjV9lsSTREO',
        'validate' => '$2y$10$VqOdRRE21p6/mGJLjyglk.J7zFM7tusWPQRcVtG7fbCjV9lsSTREO',
    ],

    // 💳 Default Invoice Settings
    'invoice' => [
        'description' => 'Deposit',
        'currency'    => 'INR',
        'domain'      => 'sandboxtest.space',
        'user_id'     => 'demoUser',
        'price'       => 10,
    ],
];
