@component('mail::message')
# Obrigado pelo seu pedido!

Olá {{ $order->client_name }},

Seu pedido **#{{ $order->id }}** foi criado com sucesso.

@component('mail::table')
| Produto | Quantidade | Valor |
|--------|:----------:|------:|
@foreach ($order->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | R$ {{ number_format($item->price, 2, ',', '.') }} |
@endforeach
@endcomponent

**Total: R$ {{ number_format($order->total, 2, ',', '.') }}**

@component('mail::button', ['url' => url('/orders/'.$order->id)])
Ver Pedido
@endcomponent

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
