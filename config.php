<?php
/**
 * API configuration.
 * API keys are kept server-side and never sent to the browser — the
 * front end only ever talks to the api/*.php proxies.
 */

return [

    'maptiler' => [
        // https://cloud.maptiler.com/account/keys/
        'api_key'       => 'qVf62VssZorkx8Bj16wu',
        'geocoding_url' => 'https://api.maptiler.com/geocoding/',
        // Restrict suggestions to Switzerland. Add more ISO codes
        // comma-separated (e.g. 'ch,de,fr') if needed later.
        'country'       => 'ch',
        'limit'         => 5,
    ],

    'swisspost' => [
        // Swiss Post Developer Portal (developer.post.ch) — OAuth2
        // client-credentials app, DCAPI products.
        'client_id'     => '67b916612df3ecf79ec9106be0c07f6d',
        'client_secret' => '0264792b3677bfc3edcf684cbdedf74b',
        'token_url'     => 'https://api.post.ch/OAuth/token',

        // Address validation (DCAPI_ADDRESS_VALIDATE)
        'validate_url'     => 'https://dcapi.apis.post.ch/address/v1/addresses/validation',
        'validate_scope'   => 'DCAPI_ADDRESS_VALIDATE',
        'valid_qualities'  => ['DOMICILE_CERTIFIED', 'CERTIFIED', 'USABLE'],

        // Collection / pickup order (DCAPI_DELIVERY_COLLECTION_ORDER)
        'collection_scope'          => 'DCAPI_DELIVERY_COLLECTION_ORDER',
        'collection_instructions_url' => 'https://dcapi.apis.post.ch/delivery/collection/order/v2/houses/{houseKey}/collection/instructions',
        'create_order_url'          => 'https://dcapi.apis.post.ch/delivery/collection/order/v2/orders',
        'approve_order_url'         => 'https://dcapi.apis.post.ch/delivery/collection/order/v2/orders/{orderKey}/approval',

        // Fixed business values — same idea as the WooCommerce
        // integration's `swiss_post_franking_licence` / mandator.
        'franking_licence' => '60156186',
        'mandator'         => 0,
        'time_window'      => 'FROM_7_30_AM',
        'product_code'     => 'ECO',
    ],

    // Gate for admin.php, the local log viewer. Change this.
    'admin' => [
        'password' => 'change-me',
    ],

    // Pushes the tracking link to the WooCommerce site (repan.ch) once
    // a pickup order is created, so it lands on the matching order.
    // api_key must match SWISSPOST_WIZARD_API_KEY in the WordPress
    // snippet on the WooCommerce side.
    'woocommerce_sync' => [
        'enabled'         => true, // flip to true once the WP-side routes are live
        'endpoint'        => 'https://repan.ch/wp-json/swisspost-wizard/v1/tracking',
        'cancel_endpoint' => 'https://repan.ch/wp-json/swisspost-wizard/v1/cancel',
        'api_key'         => 'ME-TO-A-LONG-RANDOM-STRING',
    ],

];
