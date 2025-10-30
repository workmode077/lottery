<?php

namespace Modules\Admin\Http\Controllers\ModelSection;

use App\Helpers\BackendHelpers;
use App\Http\Controllers\Controller;
use App\Models\ModelYear;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Http\Requests\ModelYearInteriorHighlightsPostRequest;
use Yajra\DataTables\Facades\DataTables;

class ModelYearInteriorHighlightsController extends Controller
{
    protected $modelClass = 'App\Models\InteriorExteriorHighlight';
    protected $viewPath = 'interior-highlights';
    /* ============ DATATABEL ============= */
    public function index(Request $request, $model_year)
    {
        $modelyearid = base64_decode($model_year);
        $baseRouteName  = $this->getBaseRouteNameSub();
        $modelyear = ModelYear::where('id', $modelyearid)->first();
        if ($request->ajax()) {
            $data = $this->modelClass::where('model_year_id', $modelyearid)->where('type', 'interior-highlight')->when(
                BackendHelpers::isOrderColumnZero($request),
                fn($query) => $query->orderByDesc('id')
            );
            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $fieldValue = $row->status ? 'checked' : '';
                    $fieldLabel = $row->status ? 'Enabled' : 'Disabled';
                    return '<div class="form-check form-switch">
                       <input type="checkbox" id="status' . base64_encode($row->id) . '" class="form-check-input toggle-switch" data-name="Status" data-labels="Enabled;Disabled" data-column="status" data-model="' . class_basename($this->modelClass) . '"
                           value="' . $row->status . '"
                           data-id="' . base64_encode($row->id) . '" name="status"
                           ' . $fieldValue . '>
                       <label class="custom-control-label" for="status' . base64_encode($row->id) . '">' . $fieldLabel . '</label>
                   </div>';
                })
                ->addColumn('action', function ($row) use ($baseRouteName, $modelyearid) {
                    $editUrl = route($baseRouteName . '.edit', [
                        'model_year' => base64_encode($modelyearid),
                        'interior_highlight' => base64_encode($row->id)
                    ]);

                    $deleteRoute = route($baseRouteName . '.destroy', [
                        'model_year' => base64_encode($modelyearid),
                        'interior_highlight' => base64_encode($row->id)
                    ]);

                    $btn = '<a href="' . $editUrl . '" class="btn btn-primary btn-sm m-1" data-bs-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '<form action="' . $deleteRoute . '" method="POST" style="display: inline-block;">'
                        . csrf_field()
                        . method_field('DELETE')
                        . '<button type="button" class="btn btn-danger btn-sm delete-btn" data-delete-message-type="itemOnly" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete"><i class="fas fa-trash"></i></button></form>';
                    return $btn;
                })


                ->rawColumns(['status', 'action'])
                ->toJson();
        }

        return view('admin::vehicle.model-years.' . $this->viewPath . '.index', compact('baseRouteName', 'modelyearid','modelyear'));
    }

    /* ============ CREATE PAGE ============= */
    public function create($model_year)
    {
        try {
            $modelyearid = base64_decode($model_year);
            $baseRouteName = $this->getBaseRouteNameSub();
            $sort_order =  $this->modelClass::max('sort_order') + 1;

            if (!$baseRouteName) {
                return abort(404);
            }

            return view('admin::vehicle.model-years.' . $this->viewPath . '.form', compact('baseRouteName', 'modelyearid', 'sort_order'));
        } catch (\Exception $e) {
            return response()->view('admin::errors.500', [], 500);
        }
    }


    /* ============ STORE FUNCTION  ============= */
    public function store(ModelYearInteriorHighlightsPostRequest $request, $model_year)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();

            $input['creator'] = auth('admin')->id();
            $input['editor'] = auth('admin')->id();
            $input['type'] = 'interior-highlight';
            $input['model_year_id'] = base64_decode($model_year);

            if ($input['int_ex_hglt_banner_type'] === 'image') {
                $input['int_ex_hglt_banner_vid_thumbnail_alt'] = null;
                $input['int_ex_hglt_banner_vid_thumbnail_alt_ar'] = null;
            } elseif ($input['int_ex_hglt_banner_type'] === 'video') {
                $input['int_ex_hglt_banner_web_alt'] = null;
                $input['int_ex_hglt_banner_web_alt_ar'] = null;
                $input['int_ex_hglt_banner_mobile_alt'] = null;
                $input['int_ex_hglt_banner_mobile_alt_ar'] = null;
            }
            $data = $this->modelClass::create($input);
            if ($input['int_ex_hglt_banner_type'] === 'image') {
                $data->uploadMedia($request, 'int_ex_hglt_banner_web');
                $data->uploadMedia($request, 'int_ex_hglt_banner_mobile');
            } elseif ($input['int_ex_hglt_banner_type'] === 'video') {
                $data->uploadMedia($request, 'int_ex_hglt_banner_vid');
                $data->uploadMedia($request, 'int_ex_hglt_banner_vid_thumbnail');
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }


    /* ============ EDIT PAGE  ============= */
    public function edit($model_year, $interior_highlight)
    {
        try {
            $modelyearid = base64_decode($model_year);
            $highlightId = base64_decode($interior_highlight);

            $data = $this->modelClass::findOrFail($highlightId);
            $baseRouteName = $this->getBaseRouteNameSub();

            if (!$data || !$baseRouteName) {
                return abort(404);
            }

            return view('admin::vehicle.model-years.' . $this->viewPath . '.form', compact('data', 'baseRouteName', 'modelyearid'));
        } catch (\Exception $e) {
            Log::error($e);
            return response()->view('admin::errors.500', [], 500);
        }
    }


    /* ============ UPDATE FUNCTION  ============= */
    public function update(ModelYearInteriorHighlightsPostRequest $request, $model_year, $id)
    {
        DB::beginTransaction();
        try {
            $data = $this->modelClass::findOrFail(base64_decode($id));
            $input = $request->all();

            $input['editor'] = auth('admin')->id();

            $previousType = $data->int_ex_hglt_banner_type;
            $newType = $input['int_ex_hglt_banner_type'];

            if ($previousType !== $newType) {
                if ($previousType === 'image') {
                    $data->clearMediaCollection('int_ex_hglt_banner_web');
                    $data->clearMediaCollection('int_ex_hglt_banner_mobile');
                } elseif ($previousType === 'video') {
                    $data->clearMediaCollection('int_ex_hglt_banner_vid');
                    $data->clearMediaCollection('int_ex_hglt_banner_vid_thumbnail');
                }
            }

            // Nullify non-applicable alt fields
            if ($newType === 'image') {
                $input['int_ex_hglt_banner_vid_thumbnail_alt'] = null;
                $input['int_ex_hglt_banner_vid_thumbnail_alt_ar'] = null;
            } elseif ($newType === 'video') {
                $input['int_ex_hglt_banner_web_alt'] = null;
                $input['int_ex_hglt_banner_web_alt_ar'] = null;
                $input['int_ex_hglt_banner_mobile_alt'] = null;
                $input['int_ex_hglt_banner_mobile_alt_ar'] = null;
            }

            $data->update($input);

            // Upload new media
            if ($newType === 'image') {
                $data->uploadMedia($request, 'int_ex_hglt_banner_web');
                $data->uploadMedia($request, 'int_ex_hglt_banner_mobile');
            } elseif ($newType === 'video') {
                $data->uploadMedia($request, 'int_ex_hglt_banner_vid');
                $data->uploadMedia($request, 'int_ex_hglt_banner_vid_thumbnail');
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data updated successfully']);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }




    /* ============ DELETE FUNCTION  ============= */
    public function destroy($model_year, $id)
    {
        try {
            DB::beginTransaction();

            $delete = $this->modelClass::findOrFail(base64_decode($id));
            $delete->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
