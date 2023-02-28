<?php

return [
    'host' => $_ENV['smtpHost'],
    'port' => $_ENV['smtpPort'],
    'username' => $_ENV['smtpUsername'],
    'password' => $_ENV['smtpPassword'],
    'ssl' => [
        'verify_peer' => $_ENV['smtpVerifyPeer'],
        'verify_peer_name' => $_ENV['smtpVerifyPeerName'],
        'allow_self_signed' => $_ENV['smtpAllowSelfSigned'],
    ]
];