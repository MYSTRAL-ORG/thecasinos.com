<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\CasinoOnline;
use Illuminate\Http\Request;
use function App\Http\Controllers\AppHttpControllers\str_random;

class AdminCasinoOnlineController extends Controller
{

    public static string $publicPath = 'img/casinos/online';


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
            $casinoonline = CasinoOnline::where('nom_casino', 'LIKE', "%$keyword%")
                ->orWhere('nom_casino_slug', 'LIKE', "%$keyword%")
                ->orWhere('sous_titre', 'LIKE', "%$keyword%")
                ->orWhere('key_feature', 'LIKE', "%$keyword%")
                ->orWhere('screenshot', 'LIKE', "%$keyword%")
                ->orWhere('logo', 'LIKE', "%$keyword%")
                ->orWhere('point_pour', 'LIKE', "%$keyword%")
                ->orWhere('point_contre', 'LIKE', "%$keyword%")
                ->orWhere('bonus', 'LIKE', "%$keyword%")
                ->orWhere('sumup_description', 'LIKE', "%$keyword%")
                ->orWhere('bonus_description', 'LIKE', "%$keyword%")
                ->orWhere('deposit_mehods_description', 'LIKE', "%$keyword%")
                ->orWhere('contact_information_description', 'LIKE', "%$keyword%")
                ->orWhere('contact_information', 'LIKE', "%$keyword%")
                ->orWhere('register_link', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%")
                ->orWhere('icone', 'LIKE', "%$keyword%")
                ->orWhere('actif', 'LIKE', "%$keyword%")
                ->orderBy('nom_casino')->paginate($perPage);
        } else {
            $casinoonline = CasinoOnline::orderBy('nom_casino')->paginate($perPage);
        }

        return view('admin.casino-online.index', compact('casinoonline'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.casino-online.create');
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
        if ($request->hasFile('screenshot')) {
            $file = $request->file('screenshot');

            $file->move(public_path($this::$publicPath), $file->getClientOriginalName());
            $requestData['screenshot'] =  '/' .$this::$publicPath.'/'. $file->getClientOriginalName();
        }
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $file->move(public_path($this::$publicPath), $file->getClientOriginalName());
            $requestData['logo'] =  '/' .$this::$publicPath.'/'. $file->getClientOriginalName();
        }
        if ($request->hasFile('icone')) {
            $file = $request->file('icone');
            $file->move(public_path($this::$publicPath), $file->getClientOriginalName());
            $requestData['icone'] = '/' .$this::$publicPath.'/' . $file->getClientOriginalName();
        }

        CasinoOnline::create($requestData);

        return redirect('admin/casino-online')->with('flash_message', 'CasinoOnline added!');
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
        $casinoonline = CasinoOnline::findOrFail($id);

        return view('admin.casino-online.show', compact('casinoonline'));
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
        $casinoonline = CasinoOnline::findOrFail($id);

        return view('admin.casino-online.edit', compact('casinoonline'));
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
                if ($request->hasFile('screenshot')) {
            $file = $request->file('screenshot');

            $file->move(public_path($this::$publicPath), $file->getClientOriginalName());
            $requestData['screenshot'] =  '/' .$this::$publicPath.'/'. $file->getClientOriginalName();
        }
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $file->move(public_path($this::$publicPath), $file->getClientOriginalName());
            $requestData['logo'] =  '/' .$this::$publicPath.'/'. $file->getClientOriginalName();
        }
        if ($request->hasFile('icone')) {
            $file = $request->file('icone');
            $file->move(public_path($this::$publicPath), $file->getClientOriginalName());
            $requestData['icone'] = '/' .$this::$publicPath.'/' . $file->getClientOriginalName();
        }

        $casinoonline = CasinoOnline::findOrFail($id);
        $casinoonline->update($requestData);

        return redirect('admin/casino-online')->with('flash_message', 'CasinoOnline updated!');
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
        CasinoOnline::destroy($id);

        return redirect('admin/casino-online')->with('flash_message', 'CasinoOnline deleted!');
    }
}
