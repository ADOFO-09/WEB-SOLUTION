@foreach($paymentMethods as $pmValue => $pmLabel)
<option value="{{ $pmValue }}" {{ ($selected ?? '') == $pmValue ? 'selected' : '' }}>{{ $pmLabel }}</option>
@endforeach
