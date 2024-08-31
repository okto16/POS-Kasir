@extends('Layouts.Admin')
@section('header', 'Produk')
@section('content')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endSection

@section('content')
    <div id="controller">
        <div class="d-flex mb-3">
            <div class="flex-grow-1">
                <a class="btn btn-primary" @click="addData($event)" data-target="#modal-default" data-toggle="modal"> Create
                    New Product</a>
                <button class="btn btn-success ml-2" @click="cetakBarcode('{{ url('cetak/barcode') }}')">Cetak
                    Barcode</button>
            </div>
        </div>
        <div class="card-header">
            <h3 class="card-title">Data Table</h3>
        </div>
        <div class="card-body">
            <table id="datatable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th width="5%">
                            <input type="checkbox" name="select_all" id="select_all">
                        </th>
                        <th style="width: 10px">No</th>
                        <th>Kode Produk</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Merk</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Diskon</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>

        <div class="modal fade" id="modal-default">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form :action="actionUrl" method="post" autocomplete="off" @submit="submitForm($event, data.id)">
                        <div class="modal-header">
                            <h4 class="modal-title">Product</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="_method" value="PUT" v-if="editStatus">
                            <div class="form-group">
                                <label for="product_name">Name Product</label>
                                <input type="text" name="product_name" class="form-control" placeholder="Enter Name"
                                    :value="data.product_name" required="">
                                <label for="category_id">Categorie Id</label>
                                <select name="category_id" class="form-control">
                                    @foreach ($categories as $categorie)
                                        <option value="{{ $categorie->id }}">{{ $categorie->category_name }}</option>
                                    @endforeach
                                </select>
                                <label for="merk">Merk</label>
                                <input type="text" name="merk" class="form-control" placeholder="Enter Name"
                                    :value="data.merk" required="">
                                <label for="purchase_price">Harga Beli</label>
                                <input type="text" name="purchase_price" class="form-control" placeholder="Enter Name"
                                    :value="data.purchase_price" required="">
                                <label for="discount">Diskon</label>
                                <input type="text" name="discount" class="form-control" placeholder="Enter Name"
                                    :value="data.discount" required="">
                                <label for="sell_price">Harga Jual</label>
                                <input type="text" name="sell_price" class="form-control" placeholder="Enter Name"
                                    :value="data.sell_price" required="">
                                <label for="stock">Stok</label>
                                <input type="text" name="stock" class="form-control" placeholder="Enter Name"
                                    :value="data.stock" required="">
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
    <script type="text/javascript">
        var actionUrl = '{{ url('products') }}';
        var apiUrl = '{{ url('api/products') }}';
        var columns = [{
                data: 'checkbox',
                render: function(data, type, row, meta) {
                    return `<input type="checkbox" name="selected_product" value="${row.id}">`;
                },
                orderable: false,
                searchable: false,
                class: 'text-center'
            },
            {
                data: 'DT_RowIndex',
                class: 'text-center',
                orderable: true
            },
            {
                data: 'product_code',
                class: 'text-center',
                orderable: true
            },
            {
                data: 'product_name',
                class: 'text-center',
                orderable: true
            },
            {
                data: 'category_id',
                class: 'text-center',
                orderable: true
            },
            {
                data: 'merk',
                class: 'text-center',
                orderable: true
            },
            {
                data: 'purchase_price',
                class: 'text-center',
                orderable: true
            },
            {
                data: 'sell_price',
                class: 'text-center',
                orderable: true
            },
            {
                data: 'discount',
                class: 'text-center',
                orderable: true
            },
            {
                data: 'stock',
                class: 'text-center',
                orderable: true
            },
            {
                render: function(index, row, data, meta) {
                    return `
                <a href="#" class="btn btn-sm btn-warning" onclick="controller.editData(event, ${meta.row})">Edit</a>
                <a href="#" class="btn btn-sm btn-danger" onclick="controller.deleteData(event, ${data.id})">Delete</a>
            `;
                },
                orderable: false,
                width: '200px',
                class: 'text-center'
            }
        ];
    </script>
    <script src="{{ asset('js/data.js') }}"></script>
@endSection

@endsection
