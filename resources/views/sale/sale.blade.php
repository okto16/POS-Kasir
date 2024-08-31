@extends('Layouts.Admin')
@section('header', 'Penjualan')
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
                <a href="{{ route('sales.create') }}" class="btn btn-primary"> Create
                    New Sale</a>
            </div>
        </div>
        <div class="card-header">
            <h3 class="card-title">Data Table</h3>
        </div>
        <div class="card-body">
            <table id="datatable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10px">No</th>
                        <th>Member</th>
                        <th>Total Item</th>
                        <th>Total Price</th>
                        <th>Discount</th>
                        <th>Payment</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
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
        var actionUrl = '{{ url('sales') }}';
        var apiUrl = '{{ url('api/sales') }}';
        var columns = [{
                data: 'DT_RowIndex',
                class: 'text-center',
                orderable: true
            },
            {
                data: 'member_id',
                class: 'text-center',
                name: 'member_id'
            },
            {
                data: 'total_item',
                class: 'text-center',
                name: 'total_item'
            },
            {
                data: 'total_price',
                class: 'text-center',
                name: 'total_price'
            },
            {
                data: 'discount',
                class: 'text-center',
                name: 'discount'
            },
            {
                data: 'pay',
                class: 'text-center',
                name: 'pay'
            },
            {
                data: 'created_at',
                class: 'text-center',
                name: 'created_at'
            },
            {
                render: function(index, row, data, meta) {
                    return `
                <a href="{{ url('sales/${data.id}/edit') }}" class="btn btn-sm btn-warning" @click="editData(id, $event)">Edit</a>
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
