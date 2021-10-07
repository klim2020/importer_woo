<?php
function getConfig()
{
    return array(
        'address' => 'https://mojki.com.ua/',
        'key1' => 'ck_eb30cb1a5576274a5919cd91876f102b71ddfedc',
        'key2' => 'cs_5f7870f341a0f7c5810a96529e9545f02a87212f',
        'options' => [
            'wp_api' => true,
            'version' => 'wc/v3',
            'query_string_auth' => false,
        ]
    );
}