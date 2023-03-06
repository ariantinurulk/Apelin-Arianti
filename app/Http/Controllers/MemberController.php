<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\LogActivity;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $members = Member::when($search, function ($query, $search){
            return $query->where('nama', 'like', "%{$search}%")
                ->orWhere('tlp', 'like', "%{$search}%");
        })->paginate();

        if ($search) {
            $members->appends(['search' => $search]);
        }

        return view('member.index', [
            'members' => $members,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        LogActivity::add('Berhasil Membuat Member');
        return view('member.create');
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
            'nama' => 'required|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|max:250',
            'tlp' => 'required|numeric'
        ], [],[
            'tlp' => 'Telepon'
        ]);

        Member::create($request->all());

        return redirect()->route('member.index')
            ->with('message', 'success store');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function show(Member $member)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function edit(Member $member)
    {
        return view('member.edit',[
            'member'=>$member
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Member $member)
    {
        LogActivity::add('Berhasil Mengubah Member');
        $request->validate([
            'nama' => 'required|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|max:250',
            'tlp' => 'required|numeric'
        ], [], [
            'tlp' => 'Telepon'
        ]);

        $member->update($request->all());

        return redirect()->route('member.index')
            ->with('message', 'success update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function destroy(Member $member)
    {
        LogActivity::add('Berhasil Mengapus Member');
        $member->delete();
        return back()->with('message', 'success delete');
    }
}
