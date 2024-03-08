<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryCity;
use Illuminate\Http\Request;

class CategoryCityController extends Controller
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
            $categorycity = CategoryCity::where('city_title', 'LIKE', "%$keyword%")
                ->orWhere('city_name', 'LIKE', "%$keyword%")
                ->orWhere('header_text', 'LIKE', "%$keyword%")
                ->orWhere('footer_text', 'LIKE', "%$keyword%")
                ->orderBy('city_name')->paginate($perPage);
        } else {
            $categorycity = CategoryCity::orderBy('city_name')->paginate($perPage);
        }

        return view('admin.category-city.index', compact('categorycity'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.category-city.create');
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

        CategoryCity::create($requestData);

        return redirect('category-city')->with('flash_message', 'CategoryCity added!');
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
        $categorycity = CategoryCity::findOrFail($id);

        return view('admin.category-city.show', compact('categorycity'));
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
        $categorycity = CategoryCity::findOrFail($id);

        return view('admin.category-city.edit', compact('categorycity'));
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

        $categorycity = CategoryCity::findOrFail($id);
        $categorycity->update($requestData);

        return redirect('category-city')->with('flash_message', 'CategoryCity updated!');
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
        CategoryCity::destroy($id);

        return redirect('category-city')->with('flash_message', 'CategoryCity deleted!');
    }
}
