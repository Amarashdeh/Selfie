<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Translation;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TranslationController extends Controller
{
    // Show the terms page with DataTable
    public function editTerms(Language $language, Request $request)
    {
        if ($request->ajax()) {
            $terms = Translation::where('language_id', $language->id)
                ->when($request->module, fn($q) => $q->where('module', $request->module))
                ->select(['id','key','value'])
                ->get();

            return datatables()->of($terms)
                ->addColumn('editable_fields', function($row) {
                    return '
                        <div id="view-'.$row->id.'">
                            <strong>'.$row->key.'</strong>: '.$row->value.'
                        </div>
                        <div id="edit-'.$row->id.'" class="d-none">
                            <input type="text" id="key-'.$row->id.'" value="'.$row->key.'" class="form-control mb-2">
                            <input type="text" id="value-'.$row->id.'" value="'.$row->value.'" class="form-control mb-2">
                            <button class="btn btn-sm btn-success" onclick="saveRow('.$row->id.')">Save</button>
                            <button class="btn btn-sm btn-secondary" onclick="cancelEdit('.$row->id.')">Cancel</button>
                        </div>
                    ';
                })
                ->addColumn('action', function($row) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="dropdown-item" onclick="editRow('.$row->id.')">Edit</a></li>
                                <li><a href="#" class="dropdown-item text-danger" onclick="deleteRow('.$row->id.')">Delete</a></li>
                            </ul>
                        </div>
                    ';
                })
                ->rawColumns(['editable_fields','action'])
                ->make(true);
        }

        return view('admin.languages.edit_terms', compact('language'));
    }

    // Store new translation
    public function storeTerm(Request $request, $languageId)
    {
        $request->validate([
            'key' => ['required','string','max:255','regex:/^[\p{Arabic}\p{L}\p{N}_\-\s]+$/u'],
            'value' => ['required','string','max:5000'],
            'module' => 'nullable|string|max:255'
        ], [
            'key.regex' => 'The key may only contain letters, numbers, dashes, and underscores (Arabic & English supported).'
        ]);

        $language = Language::findOrFail($languageId);

        $term = $language->translations()->create([
            'key' => $request->key,
            'value' => $request->value,
            'module' => $request->module,
        ]);

        return response()->json(['success' => true, 'data' => $term]);
    }

    // Update existing translation
    public function updateTerm(Request $request, Language $language, Translation $term)
    {
        $request->validate([
            'key' => ['required','string','max:255','regex:/^[\p{Arabic}\p{L}\p{N}_\-\s]+$/u'],
            'value' => ['required','string','max:5000'],
        ], [
            'key.regex' => 'The key may only contain letters, numbers, dashes, and underscores (Arabic & English supported).'
        ]);

        $term->update([
            'key' => $request->key,
            'value' => $request->value,
        ]);

        return response()->json(['success' => true, 'data' => $term]);
    }

    // Delete translation
    public function deleteTerm(Language $language, Translation $term)
    {
        $term->delete();
        return response()->json(['success' => true]);
    }
}
