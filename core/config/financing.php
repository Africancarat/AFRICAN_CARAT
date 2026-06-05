<?php

/**
 * PDP & checkout EMI / BNPL copy and indicative APR ladders.
 * Razorpay Standard Checkout exposes **live bank EMI choices** inside their modal — these numbers are illustrative only.
 */
return [
    'min_order_inr' => (float) env('FINANCING_MIN_INR', 2500),

    /* Key = months of tenure — value ≈ illustrative annual APR for reducing-balance EMI (%) */
    'indicative_apr_by_months' => [
        6 => 13.49,
        9 => 13.74,
        12 => 13.99,
        18 => 14.24,
        24 => 14.49,
        36 => 14.99,
    ],

    /* Enable EMI / wallets / UPI grouping in Razorpay Standard Checkout modal */
    'razorpay_checkout_methods' => [
        'emi' => (bool) env('RAZORPAY_ENABLE_EMI', true),
        'card' => true,
        'netbanking' => true,
        'wallet' => true,
        'upi' => true,
    ],
];
