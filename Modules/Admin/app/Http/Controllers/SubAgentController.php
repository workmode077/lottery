<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\BackendHelpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Http\Requests\SubAgentPostRequest;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;

class SubAgentController extends Controller
{
    protected $modelClass = 'App\Models\User';
    protected $viewPath = 'sub-agent';


    /* ============ DATATABEL ============= */
    public function index(Request $request)
    {
        $baseRouteName  = $this->getBaseRouteName();
        if ($request->ajax()) {
            $data = $this->modelClass::with('agent.superAgent')->when(
                BackendHelpers::isOrderColumnZero($request),
                fn($query) => $query->orderByDesc('id')
            )->where('user_type', 'sub_agent');

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
               ->addColumn('action', function ($row) use ($baseRouteName) {
                    $editUrl = route($baseRouteName . '.edit', base64_encode($row->id));
                    $deleteRoute = route($baseRouteName . '.destroy', base64_encode($row->id));
                    $btn = '<a href="' . $editUrl . '" class="btn btn-primary btn-sm mr-1" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit" style="margin-right: 3px;"><i class="fas fa-edit"></i></i></a>';
                    return $btn;
                })

                ->rawColumns(['status', 'action'])
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
             $parentAgent = User::where('user_type', 'agent')->active()->get();
            return view('admin::' . $this->viewPath . '.create', compact('baseRouteName','parentAgent'));
        } catch (\Exception $e) {
            return response()->view('admin::errors.500', [], 500);
        }
    }

    /* ============ STORE FUNCTION  ============= */
    public function store(SubAgentPostRequest $request)
    {
        DB::beginTransaction();
        try {
             $fillableData = $request->only([
                'username',
                'parent_id',
                'plain_password',
                'status'
            ]);
            // Ensure prefix
            if (!str_starts_with($fillableData['username'], 'SUB-')) {
                $fillableData['username'] = 'SUB-' . $fillableData['username'];
            }

            $fillableData['user_type'] = 'sub_agent';
            $this->modelClass::create($fillableData);
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
            $parentAgent = User::where('user_type', 'agent')->active()->get();
            return view('admin::' . $this->viewPath . '.create', compact('data', 'baseRouteName','parentAgent'));
        } catch (\Exception $e) {
            return response()->view('admin::errors.500', [], 500);
        }
    }

    /* ============ UPDATE FUNCTION  ============= */
    public function update(SubAgentPostRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $update = $this->modelClass::findOrFail(base64_decode($id));
            $fillableData = $request->only([
                'username',
                'parent_id',
                'plain_password', 
                'status'
            ]);
            // Ensure prefix
            if (!str_starts_with($fillableData['username'], 'SUB-')) {
                $fillableData['username'] = 'SUB-' . $fillableData['username'];
            }

            $update->update($fillableData);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data updated successfully']);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /* ============ DELETE FUNCTION  ============= */
    public function destroy($id)
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
