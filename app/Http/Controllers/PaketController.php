<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use App\Models\Outlet;
use Illuminate\Http\Request;
use App\Models\LogActivity;

class PaketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $pakets = Paket::join('outlets', 'outlets.id', 'pakets.outlet_id')
        ->when($search, function ($query, $search) {
        return $query->where('nama_paket', 'like', "%{$search}%")
        ;
        })
        ->select(
            'pakets.id as id',
            'nama_paket',
            'harga',
            'jenis',
            'diskon',
            'harga_akhir',
            'outlets.nama as outlet'
        )
        ->paginate();

        if ($search){
            $pakets->appends(['search' => $search]);
        }

        $jenis=[
            'kiloan'=>'Kiloan',
            'kaos'=>'T-Shirt/Kaos',
            'bed_cover'=>'Bed Cover',
            'selimut'=>'selimut',
            'lain'=>'Lainnya',
        ];

        $pakets->map(function($row) use ($jenis){
            $row->jenis = $jenis[$row->jenis];
            $row->harga = number_format($row->harga,0,',','.');
            $row->diskon = number_format($row->diskon,0,',','.');
            $row->harga_akhir = number_format($row->harga_akhir,0,',','.');
            return $row;
        });

        return view('paket.index',[
            'pakets'=>$pakets,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $outlets = Outlet::select('id as value','nama as option')->get();
        return view('paket.create',[
            'outlets'=>$outlets
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_paket' => 'required|max:100',
            'harga' => 'required|numeric',
            'jenis' => 'required|in:kiloan,bed_cover,kaos,selimut,lain',
            'diskon'=> 'nullable|numeric|min:0|',
            'harga_akhir'=> 'required|numeric|min:0|',
            'outlet_id' => 'required|exists:outlets,id',
            
        ],[],[
            'outlet_id' => 'Outlet'
            ]);

            Paket::create($request->all());
            LogActivity::add('berhasil membuat Paket ');
            return redirect()->route('paket.index')

            ->with('message','success store');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\paket  $paket
     * @return \Illuminate\Http\Response
     */
    public function show(paket $paket)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\paket  $paket
     * @return \Illuminate\Http\Response
     */
    public function edit(paket $paket)
    {
        $outlets = Outlet::select('id as value','nama as option')->get();
        return view('paket.edit',[
            'paket'=>$paket,
            'outlets'=>$outlets
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\paket  $paket
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, paket $paket)
    {
        $request->validate([
            'nama_paket' => 'required|max:100',
            'harga' => 'required|numeric',
            'jenis' => 'required|in:kiloan,bed_cover,kaos,selimut,lain',
            'diskon'=> 'nullable|numeric|min:0|',
            'harga_akhir'=> 'required|numeric|min:0|',
            'outlet_id' => 'required|exists:outlets,id',
            
        ],[],[
            'outlet_id' =>'Outlet'
            ]);

            $paket->update($request->all());
            LogActivity::add('berhasil update paket ');
            return redirect()->route('paket.index')
            ->with('message','success update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\paket  $paket
     * @return \Illuminate\Http\Response
     */
    public function destroy(Paket $paket)
    {
        $paket->delete();
        LogActivity::add('berhasil delete paket ');
        return back()->with ('message','success delete');
    }
}