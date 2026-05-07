@props([
    'url',
    'color' => 'primary',
    'align' => 'center',
])

<table class="action" align="{{ $align }}" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<a href="{{ $url }}" class="button button-{{ $color }}" target="_blank" rel="noopener"
    style="
        background-color: #020617;
        border-radius: 18px;
        color: #ffffff;
        display: inline-block;
        font-size: 15px;
        font-weight: 800;
        line-height: 1;
        padding: 17px 30px;
        text-decoration: none;
        box-shadow: 0 16px 35px rgba(15, 23, 42, 0.22);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    ">
    {{ $slot }}
</a>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>