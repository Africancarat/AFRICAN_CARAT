<?php

namespace App\Helpers;

use App\Models\Currency;
use App\Models\PaymentSetting;
use Illuminate\Support\Facades\Session;

final class FinancingHelper
{
    public static function isRazorpayEnabled(): bool
    {
        return PaymentSetting::where('unique_keyword', 'razorpay')->where('status', 1)->exists();
    }

    public static function currentCurrencyCode(): string
    {
        $curr = Session::has('currency')
            ? Currency::find(Session::get('currency'))
            : Currency::where('is_default', 1)->first();

        return strtoupper(trim((string) ($curr->name ?? 'INR')));
    }

    /** Principal and EMI maths are only accurate for ₹ orders where Razorpay EMI exists. */
    public static function isInrCheckout(): bool
    {
        return self::currentCurrencyCode() === 'INR';
    }

    /** Reducing-balance EMI monthly instalment. */
    public static function emiMonthly(float $principalInr, float $annualNominalAprPercent, int $months): float
    {
        if ($months < 1 || $principalInr <= 0) {
            return 0.0;
        }
        $r = ($annualNominalAprPercent / 100) / 12;
        if ($r <= 1e-9) {
            return round($principalInr / $months, 2);
        }

        $pow = pow(1 + $r, $months);

        return round($principalInr * $r * $pow / ($pow - 1), 2);
    }

    /**
     * @return array<string, mixed>
     */
    public static function plansForPrincipalInr(float $principalStoreUnits): array
    {
        $principalInr = (float) PriceHelper::setConvertPrice($principalStoreUnits);
        $minInr = (float) config('financing.min_order_inr', 2500);

        $summary = [
            'eligible_display' => self::isRazorpayEnabled() && self::isInrCheckout() && $principalInr >= $minInr,
            'razorpay_live_emi_note' => self::isRazorpayEnabled()
                ? __('Eligible customers select issuing-bank EMI inside Razorpay checkout — indicative math below.')
                : null,
            'currency' => self::currentCurrencyCode(),
            'principal_inr' => $principalInr,
            'plans' => [],
        ];

        if (! $summary['eligible_display']) {
            return $summary;
        }

        foreach (config('financing.indicative_apr_by_months', []) as $m => $apr) {
            $summary['plans'][] = [
                'months' => (int) $m,
                'apr_percent' => (float) $apr,
                'emi_inr_rounded' => self::emiMonthly($principalInr, (float) $apr, (int) $m),
            ];
        }

        return $summary;
    }

    /**
     * Razorpay Standard Checkout overrides for JSON options.
     *
     * @return array<string, mixed>
     */
    public static function razorpayMethodBlock(): array
    {
        if (! self::isRazorpayEnabled() || ! self::isInrCheckout()) {
            return [];
        }

        return [
            'method' => config('financing.razorpay_checkout_methods', [
                'emi' => true,
                'card' => true,
                'netbanking' => true,
                'wallet' => true,
                'upi' => true,
            ]),
        ];
    }
}
