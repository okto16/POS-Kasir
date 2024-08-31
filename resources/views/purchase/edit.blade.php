@extends('Layouts.Admin')
@section('header', 'Edit Pembelian Barang')
@section('content')
    <div class="container">

        <form id="purchaseForm" method="POST" action="{{ route('purchases.update', $purchase->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="supplier_id" class="form-label">Supplier</label>
                <select id="supplier_id" name="supplier_id" class="form-control">
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
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
                <tbody>
                    @foreach ($purchase->details as $detail)
                        <tr>
                            <td>{{ $detail->product->product_name }}</td>
                            <td>Rp {{ number_format($detail->purchase_price, 2) }}</td>
                            <td>{{ $detail->quantity }}</td>
                            <td>Rp {{ number_format($detail->subtotal, 2) }}</td>
                            <td><button type="button" class="btn btn-danger"
                                    onclick="removeProduct({{ $loop->index }})">Hapus</button></td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

            <h3>Total: Rp <span id="totalPrice">{{ number_format($purchase->total_price, 2) }}</span></h3>

            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        </form>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
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
        // Pastikan cart adalah array
        let cart = @json($purchase->details) || [];
        let totalPrice = {{ $purchase->total_price }};

        function addProduct() {
            const productSelect = document.getElementById('product');
            const quantityInput = document.getElementById('quantity');

            const productId = productSelect.value;
            const productName = productSelect.options[productSelect.selectedIndex].text.split(' - ')[
                0]; // Hanya ambil nama produk
            const purchasePrice = parseFloat(productSelect.options[productSelect.selectedIndex].dataset.price);
            const quantity = parseInt(quantityInput.value);

            if (!productId || quantity < 1) {
                alert('Pilih produk dan masukkan jumlah yang valid.');
                return;
            }

            // Cek apakah produk sudah ada di keranjang
            const existingProductIndex = cart.findIndex(item => item.id == productId);
            if (existingProductIndex !== -1) {
                // Produk sudah ada di keranjang, perbarui jumlah dan subtotal
                cart[existingProductIndex].quantity += quantity;
                cart[existingProductIndex].subtotal = cart[existingProductIndex].quantity * cart[existingProductIndex]
                    .purchase_price;
            } else {
                // Produk belum ada di keranjang, tambahkan sebagai produk baru
                const subtotal = purchasePrice * quantity;
                cart.push({
                    id: productId,
                    name: productName,
                    purchase_price: purchasePrice,
                    quantity: quantity,
                    subtotal: subtotal
                });
            }

            // Update total harga
            totalPrice = cart.reduce((total, item) => total + item.subtotal, 0);
            document.getElementById('totalPrice').textContent = `Rp ${totalPrice.toFixed(2)}`;

            // Update tabel keranjang
            updateCartTable();

            // Clear the selection and quantity
            productSelect.value = '';
            quantityInput.value = 1;
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
                    <td><button type="button" class="btn btn-danger" onclick="removeProduct(${index})">Hapus</button></td>
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
                    method: 'PUT',
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
                    window.location.href = '{{ route('purchases.index') }}'; // Redirect after update
                })
                .catch(error => console.error('Error:', error));

        });
    </script>

@endsection
