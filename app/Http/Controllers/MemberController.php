<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewMemberRequest;
use App\Models\Member;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Milon\Barcode\DNS2D;

class MemberController extends Controller
{
    public function index(){
        return view('member.member');
    }
    public function api()
    {
        $members = Member::all();
        $datatables = datatables()->of($members)->addIndexColumn();
        return $datatables->make(true);
    }

    public function store(NewMemberRequest $request)
    {
        $member = Member::latest()->first() ?? new Member();
        // Generate kode produk
        $memberCode = tambah_nol_didepan((int)$member->id +1, 5);

        // Tambahkan kode produk ke dalam request data
        $request->merge(['member_code' => $memberCode]);

        // Lanjutkan dengan menyimpan produk
        $member = Member::create($request->all());

        return redirect('members');
    }

    public function update(NewMemberRequest $request, $id)
    {
        $category = Member::findOrFail($id);
        $category->fill($request->all());
        $category->save();

        return redirect('');
    }

    public function destroy($id)
    {
        Member::destroy($id);
        return redirect('');
    }
    public function cetakMember(Request $request)
    {
        $ids = $request->input('ids', []);

    // Validasi jika tidak ada ID yang dipilih
    if (empty($ids)) {
        return redirect()->back()->withErrors('Tidak ada member yang dipilih.');
    }

    // Dapatkan data member berdasarkan ID yang diberikan
    $members = Member::whereIn('id', $ids)->get();

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
