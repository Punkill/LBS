<?php

return [
    "settings" => [
        "displayErrorDetails" => true,
        "secrets" => '81051bcc2cf1bedf378224b0a93e2877',
        "cors" =>[
            "methods" => 'GET,POST,PUT,DELETE,OPTIONS',
            "headers" => 'Origin,Authorization,Content-Type,Accept,WWW-Authenticate',
            "maxAge" => 3600
        ]
    ]
];
