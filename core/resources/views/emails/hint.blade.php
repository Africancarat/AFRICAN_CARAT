<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>A Diamond Hint for You</title>
  <style>
    body { margin: 0; padding: 0; background: #FAF8F3; font-family: 'Georgia', serif; color: #0A0A0A; }
    .wrapper { max-width: 560px; margin: 40px auto; background: #fff; border: 1px solid #D4C5A9; }
    .header { background: #0A0A0A; padding: 32px 40px; text-align: center; }
    .header__wordmark { color: #D4C5A9; font-size: 11px; letter-spacing: 0.2em; text-transform: uppercase; margin: 0; }
    .header__title { color: #fff; font-size: 22px; font-weight: 400; letter-spacing: 0.04em; margin: 8px 0 0; }
    .body { padding: 40px; }
    .eyebrow { font-size: 10px; letter-spacing: 0.16em; text-transform: uppercase; color: #B8860B; margin: 0 0 16px; }
    .greeting { font-size: 20px; font-weight: 400; margin: 0 0 24px; line-height: 1.4; }
    .divider { border: none; border-top: 1px solid #D4C5A9; margin: 24px 0; }
    .occasion-badge { display: inline-block; border: 1px solid #D4C5A9; padding: 4px 14px; font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase; color: #888; margin-bottom: 20px; }
    .product-block { border: 1px solid #ece8e0; padding: 20px; margin: 20px 0; }
    .product-name { font-size: 17px; font-weight: 400; margin: 0 0 8px; }
    .product-price { font-size: 13px; letter-spacing: 0.08em; color: #555; margin: 0 0 16px; }
    .btn { display: inline-block; padding: 13px 32px; background: #0A0A0A; color: #fff; text-decoration: none; font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase; font-family: 'Arial', sans-serif; }
    .message-block { background: #FAF8F3; border-left: 2px solid #B8860B; padding: 16px 20px; margin: 24px 0; font-style: italic; font-size: 14px; line-height: 1.6; color: #333; }
    .footer { background: #0A0A0A; padding: 24px 40px; text-align: center; }
    .footer p { color: #888; font-size: 10px; letter-spacing: 0.08em; text-transform: uppercase; margin: 0; font-family: 'Arial', sans-serif; }
    .footer a { color: #D4C5A9; text-decoration: none; }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="header">
      <p class="header__wordmark">African Carat &bull; Monte Luca Diamonds</p>
      <h1 class="header__title">Someone is thinking of you ♥</h1>
    </div>

    <div class="body">
      <p class="eyebrow">{{ $data['occasion'] }}</p>
      <p class="greeting">Hello {{ $data['recipient_name'] }},<br>
        someone special left you a little hint.</p>

      <hr class="divider">

      <span class="occasion-badge">{{ $data['occasion'] }}</span>

      <div class="product-block">
        <p class="product-name">{{ $item->name }}</p>
        @if(isset($item->discount_price))
          <p class="product-price">
            {{ __('From') }} {{ \App\Helpers\PriceHelper::setCurrencyPrice($item->discount_price) }}
          </p>
        @endif
        <a href="{{ url(route('front.product', $item->slug)) }}" class="btn">{{ __('View this piece') }}</a>
      </div>

      @if(!empty($data['message']))
        <div class="message-block">
          &ldquo;{{ $data['message'] }}&rdquo;
        </div>
      @endif

      <hr class="divider">

      <p style="font-size:12px; color:#888; line-height:1.6; font-family:'Arial',sans-serif;">
        This hint was sent via the African Carat gifting tool. No purchase has been made.
        All diamonds are GIA certified and come with complimentary resizing within 60 days.
      </p>
    </div>

    <div class="footer">
      <p>
        &copy; {{ date('Y') }} African Carat &bull;
        <a href="{{ url('/') }}">monteluca.com</a>
      </p>
    </div>
  </div>
</body>
</html>
