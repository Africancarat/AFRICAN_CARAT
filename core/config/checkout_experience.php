<?php

/**
 * Packaging, fulfilment fudge-factors / holiday hints for PDP + checkout reassurance.
 */
return [

    'processing_business_days' => [
        'min' => (int) env('CHECKOUT_PROCESSING_BIZ_MIN', 2),
        'max' => (int) env('CHECKOUT_PROCESSING_BIZ_MAX', 4),
    ],

    /*
     * Courier-in-transit business days AFTER dispatch (additive to processing band).
     * Override per market by env.
     */
    'shipping_business_days' => [
        'min' => (int) env('CHECKOUT_SHIPPING_BIZ_MIN', 2),
        'max' => (int) env('CHECKOUT_SHIPPING_BIZ_MAX', 5),
    ],

    /**
     * mm-dd keyed occasions for marketing copy (“arrives before …”).
     * Year is substituted at runtime — compare next upcoming occurrence vs delivery window start.
     */
    'celebratory_landmarks' => [
        'valentine' => ['month' => 2, 'day' => 14, 'label' => "Valentine's Day"],
        'christmas' => ['month' => 12, 'day' => 25, 'label' => 'Christmas'],
        'year_end' => ['month' => 12, 'day' => 31, 'label' => "New Year's Eve"],
    ],

    /* ₹ threshold to surface white-g concierge upsell checkbox copy */
    'concierge_suggest_inr' => (float) env('CONCIERGE_SUGGEST_INR_THRESHOLD', 1000000),

];
