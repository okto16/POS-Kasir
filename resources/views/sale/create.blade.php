@extends('Layouts.Admin')
@section('header', 'Penjualan')
@section('content')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endSection

@section('content')
<input type="text" id="user-id" hidden value=" {{ Auth::user()->id }}">
    <div class="mb-3">
        <label for="member-select">Pilih Member:</label>
        <select id="member-select" class="form-control">
            <option value="">Pilih Member</option>
            @foreach ($members as $member)
                <option value="{{ $member->id }}">{{ $member->name }} - {{ $member->email }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="product-select">Pilih Produk:</label>
        <select id="product-select" class="form-control">
            <option value="">Pilih Produk</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" data-price="{{ $product->sell_price }}"
                    data-discount="{{ $product->discount }}">
                    {{ $product->product_name }} - {{ $product->merk }}
                </option>
            @endforeach
        </select>
    </div>

    <table id="selected-products-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Discount</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Remove</th>
            </tr>
        </thead>
        <tbody>
            <!-- Rows will be dynamically added here -->
        </tbody>
    </table>

    <h2>Total: <span id="total">0</span></h2>
    <button id="complete-sale" class="btn btn-primary">Complete Sale</button>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function calculateTotal() {
            let total = 0;
            $('#selected-products-table tbody tr').each(function() {
                let price = parseFloat($(this).data('price'));
                let discount = parseFloat($(this).data('discount'));
                let quantity = parseInt($(this).find('.quantity').val());
                let subtotal = (price - discount) * quantity;
                $(this).find('.subtotal').text(subtotal);
                total += subtotal;
            });
            $('#total').text(total);
        }

        $('#product-select').on('change', function() {
            let selectedOption = $(this).find('option:selected');
            let productId = selectedOption.val();
            let productName = selectedOption.text();
            let price = selectedOption.data('price');
            let discount = selectedOption.data('discount');

            if (productId) {
                let rowHtml = `
                    <tr data-id="${productId}" data-price="${price}" data-discount="${discount}">
                        <td>${productName}</td>
                        <td>${price}</td>
                        <td>${discount}</td>
                        <td><input type="number" class="quantity" min="1" value="1"></td>
                        <td class="subtotal">${price - discount}</td>
                        <td><button class="remove-product btn btn-danger">Remove</button></td>
                    </tr>
                `;
                $('#selected-products-table tbody').append(rowHtml);
                calculateTotal();
            }
        });
        $(document).on('click', '.remove-product', function() {
            $(this).closest('tr').remove();
            calculateTotal();
        });

        $(document).on('change', '.quantity', function() {
            calculateTotal();
        });

        $('#complete-sale').on('click', function() {
            let member_id = $('#member-select').val();
            let user_id = $('#user-id').val();

            if (!member_id) {
                alert("Pilih member terlebih dahulu!");
                return;
            }
            let products = [];
            $('#selected-products-table tbody tr').each(function() {
                products.push({
                    id: $(this).data('id'),
                    quantity: $(this).find('.quantity').val()
                });
            });

            $.ajax({
                url: '{{ route('sales.store') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    member_id: member_id,
                    discount: 0,
                    pay: $('#total').text(),
                    received: $('#total').text(),
                    user_id: user_id,
                    products: products
                },
                success: function(response) {
                    alert(response.message);
                    location.reload();
                }
            });
        });
    </script>
@endsection
