<?php

namespace Modules\Admin\Http\Controllers\ModelSection;

use App\Http\Controllers\Controller;
use App\Models\ModelYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ModelYearBasicDetailsController extends Controller
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

            return view('admin::vehicle.' . $this->viewPath . '.basic-details/create', compact(
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

            if ($request->banner_type === 'image') {
                $update->update([
                    'banner_vid_thumbnail_alt' => null,
                    'banner_vid_thumbnail_alt_ar' => null,
                ]);
                $update->uploadMedia($request, 'banner_web');
                $update->uploadMedia($request, 'banner_mobile');
            } elseif ($request->banner_type === 'video') {
                $update->uploadMedia($request, 'banner_video');
                $update->uploadMedia($request, 'banner_video_thumbnail');

                $update->update([
                    'banner_web_alt' => null,
                    'banner_web_alt_ar' => null,
                    'banner_mobile_alt' => null,
                    'banner_mobile_alt_ar' => null,
                ]);
            }
            $update->uploadMedia($request, 'home_vid_left_vid');
            $update->uploadMedia($request, 'home_vid_left_vid_thumb');
            $update->uploadMedia($request, 'home_vid_right_vid');
            $update->uploadMedia($request, 'home_vid_right_vid_thumb');
            $update->uploadMedia($request, 'home_vid_right_icon');
            $update->uploadMedia($request, 'body_feature_web_banner');
            $update->uploadMedia($request, 'body_feature_mobile_banner');
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Section updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json(['success' => false, 'message' => 'Something went wrong.']);
        }
    }
}
