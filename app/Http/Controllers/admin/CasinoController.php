<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Casino;
use App\services\GenerateJsonService;
use Illuminate\Http\Request;
use function App\Http\Controllers\AppHttpControllers\str_random;

class CasinoController extends Controller
{


    public static string $publicPath = '/img/casino';



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
            $casino = Casino::where('name', 'LIKE', "%$keyword%")
                ->orWhere('img_url', 'LIKE', "%$keyword%")
                ->orderBy('name')->paginate($perPage);
        } else {
            $casino = Casino::orderBy('name')->paginate($perPage);
        }

        return view('admin.casino.index', compact('casino'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.casino.create');
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
        if ($request->hasFile('img_url')) {
            $file = $request->file('img_url');
            $file->move(public_path($this::$publicPath), $file->getClientOriginalName());

            $requestData['img_url'] =    $file->getClientOriginalName();
        }

        Casino::create($requestData);
        $serviceJson = new GenerateJsonService();
        $serviceJson->writeJson();
        return redirect('admin/casino')->with('flash_message', 'Casino added!');
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
        $casino = Casino::findOrFail($id);

        return view('admin.casino.show', compact('casino'));
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
        $casino = Casino::findOrFail($id);

        return view('admin.casino.edit', compact('casino'));
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
            if ($request->hasFile('img_url')) {
                $file = $request->file('img_url');
                $file->move(public_path($this::$publicPath), $file->getClientOriginalName());

                $requestData['img_url'] =    $file->getClientOriginalName();
            }

        $casino = Casino::findOrFail($id);
        $casino->update($requestData);
        $serviceJson = new GenerateJsonService();
        $serviceJson->writeJson();
        return redirect('admin/casino')->with('flash_message', 'Casino updated!');
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
        Casino::destroy($id);

        return redirect('admin/casino')->with('flash_message', 'Casino deleted!');
    }
}
