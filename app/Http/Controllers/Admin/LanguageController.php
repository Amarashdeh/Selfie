<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class LanguageController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $languages = Language::select(['id','name','code','rtl','icon']);
            return DataTables::of($languages)
                ->addColumn('rtl_text', function($row){
                    return $row->rtl ? 'Yes' : 'No';
                })
                ->addColumn('icon_img', function($row){
                    if($row->icon){
                        return '<img src="'.asset("storage/".$row->icon).'" width="40">';
                    }
                    return '';
                })
                ->addColumn('action', function($row){
                    $dropdown = '
                    <div class="dropdown">
                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        &#8230;
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item btn-edit" href="'.route("admin.languages.edit",$row->id).'">Edit</a></li>
                        <li><a class="dropdown-item" href="'.route("admin.languages.editTerms",$row->id).'">Edit Terms</a></li>
                        <li><button class="dropdown-item btn-delete" data-id="'.$row->id.'">Delete</button></li>
                    </ul>
                    </div>';
                    return $dropdown;
                })
                ->rawColumns(['icon_img','action'])
                ->make(true);
        }

        return view('admin.languages.index');
    }

    public function create()
    {
        return view('admin.languages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:languages',
            'rtl' => 'required|boolean',
            'icon' => 'nullable|image|max:1024',
        ]);

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('languages', 'public');
        }

        $language = Language::create($data);

        // If AJAX request, return JSON
        if($request->ajax()){
            return response()->json([
                'success' => true,
                'language' => $language,
                'message' => 'Language added successfully.'
            ]);
        }

        return redirect()->route('admin.languages.index')->with('success', 'Language added successfully.');
    }

    public function edit(Language $language)
    {
        return view('admin.languages.edit', compact('language'));
    }

    public function update(Request $request, Language $language)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:languages,code,'.$language->id,
            'rtl' => 'required|boolean',
            'icon' => 'nullable|image|max:1024',
        ]);

        if ($request->hasFile('icon')) {
            if($language->icon){
                Storage::disk('public')->delete($language->icon);
            }
            $data['icon'] = $request->file('icon')->store('languages', 'public');
        }

        $language->update($data);

        return redirect()->route('admin.languages.index')->with('success', 'Language updated successfully.');
    }

    public function destroy(Language $language, Request $request)
    {
        if($language->icon){
            Storage::disk('public')->delete($language->icon);
        }
        $language->delete();

        if($request->ajax()){
            return response()->json([
                'success' => true,
                'message' => 'Language deleted successfully.'
            ]);
        }

        return redirect()->route('admin.languages.index')->with('success', 'Language deleted successfully.');
    }

}
