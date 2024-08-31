@extends('Layouts.Admin')
@section('header', 'Produk')

@section('content')
<div class="container">
    <table width="100%">
        <tr>
            @foreach ($barcodes as $barcode)
                <td class="text-center" style="border: 1px solid #333;">
                    <p>{{ $barcode['product']->product_name }} - Rp. {{ ($barcode['product']->purchase_price) }}</p>
                    <div>{!! $barcode['barcode'] !!}
                    </div>
                    <br>
                    
                </td>
            @endforeach
        </tr>
    </table>
</div>
@endsection
