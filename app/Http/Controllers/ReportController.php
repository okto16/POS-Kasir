<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Milon\Barcode\DNS2D;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    public function index()
    {
        // if ($request->ajax()) {
        //     $startDate = $request->input('start_date');
        //     $endDate = $request->input('end_date');

        //     $query = Purchase::with('product', 'user');

        //     if ($startDate && $endDate) {
        //         $query->whereBetween('purchase_date', [$startDate, $endDate]);
        //     }

        //     return DataTables::of($query)
        //         ->editColumn('purchase_date', function ($row) {
        //             return $row->purchase_date->format('Y-m-d');
        //         })
        //         ->make(true);
        // }

        return view('admin.report');
    }
    public function api()
    {
        $report = Purchase::all();
        $datatables = datatables()->of($report)->addIndexColumn();
        return $datatables->make(true);
    }
    public function cetakMember(Request $request)
    {
        $ids = $request->input('ids', []);

    // Validasi jika tidak ada ID yang dipilih
    if (empty($ids)) {
        return redirect()->back()->withErrors('Tidak ada member yang dipilih.');
    }

    // Dapatkan data member berdasarkan ID yang diberikan
    $members = Purchase::whereIn('id', $ids)->get();

    // Buat instance DNS1D atau DNS2D untuk generate QR code
    $qrCodeGenerator = new DNS2D();

    // Generate QR code untuk setiap member
    $kartuMembers = $members->map(function ($member) use ($qrCodeGenerator) {
        return [
            'member' => $member,
            'qrcode' => $qrCodeGenerator->getBarcodeSVG($member->member_code, 'QRCODE'),
        ];
    });

    $pdf = Pdf::loadView('member.cetak', compact('kartuMembers'));

    // Set ukuran kertas dan orientasi
    $pdf->setPaper('A4', 'portrait'); 
    return view('member.cetak', compact('kartuMembers'));
    }
}
