<?php

namespace Modules\Admin\Http\Controllers\ModelSection;

use App\Http\Controllers\Controller;
use App\Models\ModelYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ModelYearDistanceController extends Controller
{
    protected $modelClass = 'App\Models\ModelYear';
    protected $viewPath = 'model-years';
    public function index($id)
    {
        try {
            $modelYearId = base64_decode($id, true);

            if (!$modelYearId || !is_numeric($modelYearId)) {
                return abort(404, 'Invalid ID');
            }

            $data = $this->modelClass::find($modelYearId);
            $vehicle = $data->vehicle_id;
            if (!$data) {
                return abort(404, 'Data not found');
            }

            $vehicle = base64_encode($data->vehicle_id ?? null);
            $baseRouteName = $this->getBaseRouteNameSub();

            return view('admin::vehicle.' . $this->viewPath . '.distance/create', compact(
                'data',
                'baseRouteName',
                'vehicle'
            ));
        } catch (\Exception $e) {
            report($e); // optional: logs the exception
            return response()->view('admin::errors.500', [], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $update = ModelYear::where('id', $request->model_year_id)->first();
            $update->update($request->all());
            $update->uploadMedia($request, 'distance_map_image');
            $update->uploadMedia($request, 'distance_right_image');
            $update->uploadMedia($request, 'distance_web_banner');
            $update->uploadMedia($request, 'distance_mobile_banner');
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json(['success' => false, 'message' => 'Something went wrong.']);
        }
    }
}
