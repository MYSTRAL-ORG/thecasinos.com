<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\CasinoDetail;
use App\Http\Requests\CasinoDetailRequest;

class CasinoDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $casinodetails= CasinoDetail::all();
        return view('casinodetails.index', ['casinodetails'=>$casinodetails]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('casinodetails.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CasinoDetailRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CasinoDetailRequest $request)
    {
        $casinodetail = new CasinoDetail;
		$casinodetail->title = $request->input('title');
		$casinodetail->description = $request->input('description');
		$casinodetail->sumup = $request->input('sumup');
		$casinodetail->games = $request->input('games');
		$casinodetail->fun_facts = $request->input('fun_facts');
		$casinodetail->resume_1_line = $request->input('resume_1_line');
		$casinodetail->resume_2_words = $request->input('resume_2_words');
        $casinodetail->save();

        return to_route('casinodetails.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $casinodetail = CasinoDetail::findOrFail($id);
        return view('casinodetails.show',['casinodetail'=>$casinodetail]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $casinodetail = CasinoDetail::findOrFail($id);
        return view('casinodetails.edit',['casinodetail'=>$casinodetail]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CasinoDetailRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CasinoDetailRequest $request, $id)
    {
        $casinodetail = CasinoDetail::findOrFail($id);
		$casinodetail->title = $request->input('title');
		$casinodetail->description = $request->input('description');
		$casinodetail->sumup = $request->input('sumup');
		$casinodetail->games = $request->input('games');
		$casinodetail->fun_facts = $request->input('fun_facts');
		$casinodetail->resume_1_line = $request->input('resume_1_line');
		$casinodetail->resume_2_words = $request->input('resume_2_words');
        $casinodetail->save();

        return to_route('casinodetails.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $casinodetail = CasinoDetail::findOrFail($id);
        $casinodetail->delete();

        return to_route('casinodetails.index');
    }
}
