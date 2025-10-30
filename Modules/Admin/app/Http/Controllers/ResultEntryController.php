<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\BackendHelpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Http\Requests\ResultEntryRequest;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Game;

class ResultEntryController extends Controller
{
    protected $modelClass = 'App\Models\ResultEntry';
    protected $viewPath = 'result-entry';


    /* ============ DATATABEL ============= */
    public function index(Request $request)
    {
        $baseRouteName  = $this->getBaseRouteName();
        if ($request->ajax()) {
            $data = $this->modelClass::when(
                BackendHelpers::isOrderColumnZero($request),
                fn($query) => $query->orderByDesc('id')
            )->with('game');

            return DataTables::eloquent($data)
                ->addIndexColumn()
               ->addColumn('action', function ($row) use ($baseRouteName) {
                    $editUrl = route($baseRouteName . '.edit', base64_encode($row->id));
                    $btn = '<a href="' . $editUrl . '" class="btn btn-primary btn-sm mr-1" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit" style="margin-right: 3px;"><i class="fas fa-edit"></i></i></a>';
                    return $btn;
                })

                ->rawColumns(['action'])
                ->toJson();
        }

        return view('admin::' . $this->viewPath . '.index', compact('baseRouteName'));
    }

    /* ============ CREATE PAGE ============= */
    public function create()
    {
        try {
            $baseRouteName  = $this->getBaseRouteName();
            if (!$baseRouteName) {
                return abort(404);
            }
            $games = Game::where('status', true)->get();
            return view('admin::' . $this->viewPath . '.create', compact('baseRouteName','games'));
        } catch (\Exception $e) {
            return response()->view('admin::errors.500', [], 500);
        }
    }

    /* ============ STORE FUNCTION  ============= */
    public function store(ResultEntryRequest $request)
    {
        DB::beginTransaction();
        try {
            $store = $this->modelClass::create($request->all());
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /* ============ EDIT PAGE  ============= */
    public function edit($id)
    {
        try {
            $data = $this->modelClass::findOrFail(base64_decode($id));
            $baseRouteName  = $this->getBaseRouteName();
            if (!$data && !$baseRouteName) {
                return abort(404);
            }
            $games = Game::get();
            return view('admin::' . $this->viewPath . '.create', compact('data', 'baseRouteName','games'));
        } catch (\Exception $e) {
            return response()->view('admin::errors.500', [], 500);
        }
    }

    /* ============ UPDATE FUNCTION  ============= */
    public function update(ResultEntryRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $update = $this->modelClass::findOrFail(base64_decode($id));
            $update->update($request->all());
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data updated successfully']);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
