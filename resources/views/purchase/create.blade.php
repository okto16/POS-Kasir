@extends('Layouts.Admin')
@section('header', 'Pembelian Barang')
@section('content')
    <div class="container">

        <form id="purchaseForm" method="POST" action="{{ route('purchases.store') }}">
            @csrf

            <div class="mb-3">
                <label for="supplier_id" class="form-label">Supplier</label>
                <select id="supplier_id" name="supplier_id" class="form-control">
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="product" class="form-label">Pilih Produk</label>
                <select id="product" class="form-control">
                    <option value="" selected disabled>Pilih produk...</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->purchase_price }}">
                            {{ $product->product_name }} - Rp {{ number_format($product->purchase_price, 2) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="quantity" class="form-label">Jumlah</label>
                <input type="number" id="quantity" class="form-control" min="1" value="1">
            </div>

            <button type="button" class="btn btn-primary" onclick="addProduct()">Tambahkan Produk</button>

            <h2 class="mt-4">Daftar Produk yang Dibeli</h2>
            <table class="table table-bordered" id="cartTable">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga Beli</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <h3>Total: Rp <span id="totalPrice">0</span></h3>

            <button type="submit" class="btn btn-success">Proses Pembelian</button>
        </form>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script> <!-- Added Bootstrap JS -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script>
        let cart = [];
        let totalPrice = 0;

        function addProduct() {
            const productSelect = document.getElementById('product');
            const quantityInput = document.getElementById('quantity');

            const productId = productSelect.value;
            const productName = productSelect.options[productSelect.selectedIndex].text;
            const purchasePrice = parseFloat(productSelect.options[productSelect.selectedIndex].dataset.price);
            const quantity = parseInt(quantityInput.value);

            if (!productId || quantity < 1) {
                alert('Pilih produk dan masukkan jumlah yang valid.');
                return;
            }

            const subtotal = purchasePrice * quantity;
            totalPrice += subtotal;

            // Menambahkan produk ke keranjang
            cart.push({
                id: productId,
                name: productName,
                purchase_price: purchasePrice,
                quantity: quantity,
                subtotal: subtotal
            });

            // Menambahkan baris baru ke tabel keranjang
            updateCartTable();

            // Update total harga
            document.getElementById('totalPrice').textContent = `Rp ${totalPrice.toFixed(2)}`;

            // Clear the selection and quantity
            productSelect.value = '';
            quantityInput.value = 1;
        }

        function editData(id, event) {
            event.preventDefault();

            // Fetch data for the selected purchase
            fetch(`/purchases/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    // Fill the form with fetched data
                    document.getElementById('supplier_id').value = data.supplier_id;

                    // Clear the current cart and total price
                    cart = [];
                    totalPrice = 0;

                    // Populate the cart with existing products
                    data.products.forEach(item => {
                        cart.push({
                            id: item.product_id,
                            name: item.product_name,
                            purchase_price: item.purchase_price,
                            quantity: item.quantity,
                            subtotal: item.purchase_price * item.quantity
                        });
                        totalPrice += item.purchase_price * item.quantity;
                    });

                    // Update the cart table and total price display
                    updateCartTable();
                    document.getElementById('totalPrice').textContent = `Rp ${totalPrice.toFixed(2)}`;

                    // Set the form action to update route
                    document.getElementById('purchaseForm').action = `/purchases/${id}`;
                    document.getElementById('purchaseForm').method = 'POST';

                    // Add _method input to spoof PUT request
                    document.getElementById('purchaseForm').insertAdjacentHTML('beforeend',
                        '<input type="hidden" name="_method" value="PUT">');
                })
                .catch(error => console.error('Error:', error));
        }

        function updateCartTable() {
            const cartTableBody = document.getElementById('cartTable').querySelector('tbody');
            cartTableBody.innerHTML = '';

            cart.forEach((item, index) => {
                const newRow = cartTableBody.insertRow();
                newRow.innerHTML = `
                <td>${item.name}</td>
                <td>Rp ${item.purchase_price.toFixed(2)}</td>
                <td>${item.quantity}</td>
                <td>Rp ${item.subtotal.toFixed(2)}</td>
                <td><button type="button" class="class="btn btn-danger"" onclick="removeProduct(${index})">Hapus</button></td>
            `;
            });
        }

        function removeProduct(index) {
            // Mengurangi total harga
            totalPrice -= cart[index].subtotal;

            // Menghapus produk dari keranjang
            cart.splice(index, 1);

            // Update tabel dan total harga
            updateCartTable();
            document.getElementById('totalPrice').textContent = `Rp ${totalPrice.toFixed(2)}`;
        }

        document.getElementById('purchaseForm').addEventListener('submit', function(event) {
            event.preventDefault();

            if (cart.length === 0) {
                alert('Tambahkan produk terlebih dahulu.');
                return;
            }

            const form = this;
            const products = cart.map(item => ({
                product_id: item.id,
                quantity: item.quantity,
                purchase_price: item.purchase_price
            }));

            fetch(form.action, {
                    method: form.method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        supplier_id: document.getElementById('supplier_id').value,
                        products
                    })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    form.reset();
                    document.getElementById('cartTable').querySelector('tbody').innerHTML = '';
                    document.getElementById('totalPrice').textContent = '0';
                    cart = [];
                    totalPrice = 0;
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
@endsection
