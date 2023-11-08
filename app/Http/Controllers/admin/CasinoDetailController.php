<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\CasinoDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CasinoDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $casinodetail =  DB::table('casino_details')
                ->join('casino', 'casino.id', '=', 'casino_details.id_casino')
                ->select('casino_details.*', 'casino.name', 'casino.country_name','casino.city_name')
                ->whereRaw('LOWER(unaccent(casino.name)) LIKE LOWER(unaccent(?))', ["%{$keyword}%"])
                ->orderBy('casino.name')->paginate($perPage);
        } else {
            $casinodetail =     DB::table('casino_details')
                ->join('casino', 'casino.id', '=', 'casino_details.id_casino')
                ->select('casino_details.*', 'casino.name', 'casino.country_name','casino.city_name')->orderBy('id_casino')->paginate($perPage);
        }

        return view('admin/casino-details.index', compact('casinodetail'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin/casino-details.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {

        $requestData = $request->all();
        $requestData['actif']=0;
        if ($request->has('actif')) {

            $requestData['actif']=1;
        }
        CasinoDetail::create($requestData);

        return redirect('admin/casino-details')->with('flash_message', 'CasinoDetail added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {

        $casinodetail =  DB::table('casino_details')
            ->join('casino', 'casino.id', '=', 'casino_details.id_casino')
            ->select('casino_details.*', 'casino.name', 'casino.country_name','casino.city_name')->where('casino_details.id', $id)->get()->first();


        return view('admin/casino-details.show', compact('casinodetail'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $casinodetail =  DB::table('casino_details')
            ->join('casino', 'casino.id', '=', 'casino_details.id_casino')
            ->select('casino_details.*', 'casino.name', 'casino.country_name','casino.city_name')->where('casino_details.id', $id)->get()->first();

        return view('admin/casino-details.edit', compact('casinodetail'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {

        $requestData = $request->all();
        $requestData['actif']=0;
        if ($request->has('actif')) {

            $requestData['actif']=1;
        }
        $casinodetail = CasinoDetail::findOrFail($id);
        $casinodetail->update($requestData);

        return redirect('admin/casino-details')->with('flash_message', 'CasinoDetail updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        CasinoDetail::destroy($id);

        return redirect('admin/casino-details')->with('flash_message', 'CasinoDetail deleted!');
    }
}
