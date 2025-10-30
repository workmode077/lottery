<?php

namespace Modules\Admin\Http\Controllers;

use App\Helpers\BackendHelpers;
use App\Http\Controllers\Controller;
use App\Models\AdminSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Http\Requests\AdminSettingsRequest;
use Yajra\DataTables\Facades\DataTables;

class AdminSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = AdminSettings::when(
                BackendHelpers::isOrderColumnZero($request),
                fn($query) => $query->orderBy('id')
            );
            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->editColumn('key', fn($row) => $row->key_value)
                ->editColumn('value', function ($row) {
                    return $row->type == 2
                        ? '<img src="' . $row->formatted_value . '" alt="' . $row->key_value . '" class="table-thumbnail" style="background-color: #f2f2f2; padding: 4px; border-radius: 4px;">'
                        : ($row->type == 1 ? $row->formatted_value : null);
                })

                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('admin-settings.edit', base64_encode($row->id)) . '" class="btn btn-primary btn-sm mr-1" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit" style="margin-right: 3px;"><i class="fas fa-edit"></i></a>';
                    return $btn;
                })
                ->rawColumns(['value', 'action'])
                ->toJson();
        }
        return view('admin::admin-settings.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $adminSettings = AdminSettings::findOrFail(base64_decode($id));
        return view('admin::admin-settings.edit', compact('adminSettings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminSettingsRequest $request, $id)
    {
        $adminSettings = AdminSettings::findOrFail(base64_decode($id));

        DB::beginTransaction();

        try {
            $data = $request->all();

            if ($adminSettings->type == 2 && $request->hasFile('value')) {
                $adminSettings->uploadMedia($request, 'value');
            } elseif ($adminSettings->type == 1) {
                $data['value'] = $request->input('value');
            }

            // Capture old backend prefix before updating
            $oldPrefix = $adminSettings->key === 'backend-prefix' ? $adminSettings->value : null;

            $adminSettings->update($data);

            DB::commit();

            // If the backend prefix was updated, handle cookie removal and cache clearing
            if ($adminSettings->key === 'backend-prefix') {
                if ($oldPrefix) {
                    Cookie::queue(Cookie::forget($oldPrefix . '-email'));
                    Cookie::queue(Cookie::forget($oldPrefix . '-password'));
                    Cookie::queue(Cookie::forget($oldPrefix . '-remember'));
                }

                // Clear all caches efficiently
                Artisan::call('optimize:clear');

                return response()->json([
                    'success' => true,
                    'message' => 'Admin Settings updated successfully',
                    'redirect' => url($data['value'] . '/admin-settings')
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Admin Settings updated successfully.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json(['success' => false, 'message' => 'Something went wrong.']);
        }
    }

    /**
     * Updates the sort order of a specified model instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * The request is expected to contain:
     * - 'model': The name of the model to be updated (under the 'App\Models' namespace).
     * - 'id': The ID of the model instance (base64 encoded).
     * - 'value': The new sort order value to be set.
     */
    public function updateSortOrder(Request $request)
    {
        $modelClass = 'App\\Models\\' . $request->model;

        $data = $modelClass::find(base64_decode($request->id));

        if ($data) {
            $data->sort_order = $request->value;
            return response()->json(['success' => $data->save()]);
        }

        return response()->json([
            'success' => false,
            'message' => $data ? 'Data not found' : 'Invalid ID'
        ], $data ? 404 : 400);
    }

    /**
     * Updates the status of a specific column for a given model instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * The request is expected to contain:
     * - 'model': The name of the model to be updated (under the 'App\Models' namespace).
     * - 'id': The ID of the model instance (base64 encoded).
     * - 'column': The name of the column whose value needs to be updated.
     * - 'value': The new value to be set for the specified column.
     */
    public function updateToggleStatus(Request $request)
    {
        $modelClass = 'App\\Models\\' . $request->model;

        $data = $modelClass::find(base64_decode($request->id));

        if ($data) {
            $data->{$request->column} = $request->value;
            return response()->json(['success' => $data->save()]);
        }

        return response()->json([
            'success' => false,
            'message' => $data ? 'Invalid ID' : 'Data not found'
        ], $data ? 400 : 404);
    }

    /**
     * Check if a given slug is unique and generate a unique one if necessary.
     *
     * @param Request $request
     *      - id (optional, base64 encoded): The ID of the existing record (if editing).
     *      - model: The model name (e.g., "Blog").
     *      - slug: The slug to be checked.
     *
     * @return \Illuminate\Http\JsonResponse
     *      - exists: Whether the slug already exists.
     *      - uniqueSlug: The unique slug generated.
     */
    public function checkSlug(Request $request)
    {
        $id = $request->id ? base64_decode($request->id) : null;
        $model = app("App\\Models\\" . $request->model);
        $slug = $originalSlug = $request->slug;
        $counter = 1;

        while ($model::where('slug', $slug)->when($id, fn($q) => $q->where('id', '!=', $id))->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return response()->json([
            'exists' => $slug !== $originalSlug,
            'uniqueSlug' => $slug
        ]);
    }
}
