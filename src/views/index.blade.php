<form method="post" action="{{$url}}">
    <input type="hidden" name="payment_token" value="{{$token}}">
    <input type="hidden" name="url_ok" value="{{$url_ok}}">
    <input type="hidden" name="url_ko" value="{{$url_ko}}">
    <button>Payer</button>
</form>
