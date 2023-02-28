<?php

return [
    'headers' => explode(',', $_ENV['headers']),
    'host'=>$_ENV['corsHost']
];