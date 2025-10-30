<?php

namespace Modules\Admin\Http\Controllers;

use App\Helpers\BackendHelpers;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Http\Requests\AdminPasswordRequest;
use Modules\Admin\Http\Requests\AdminRequest;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{
    protected $superAdminId;

    public function __construct()
    {
        $this->superAdminId = Admin::min('id');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Admin::when(
                auth('admin')->id() !== $this->superAdminId,
                fn($query) => $query->whereKeyNot($this->superAdminId)
            )
                ->when(
                    BackendHelpers::isOrderColumnZero($request),
                    fn($query) => $query->orderBy('id')
                );
            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('roles', function ($row) {
                    return $row->formatted_roles;
                })
                ->editColumn('status', function ($row) {
                    if (collect([$this->superAdminId, auth('admin')->id()])->contains($row->id)) {
                        return '<span class="badge bg-secondary">N/A</span>';
                    }

                    $fieldValue = $row->status ? 'checked' : '';
                    $fieldLabel = $row->status ? 'Enabled' : 'Disabled';

                    return '<div class="form-check form-switch">
                        <input type="checkbox" id="status' . base64_encode($row->id) . '" class="form-check-input toggle-switch" data-name="Status" data-labels="Enabled;Disabled" data-column="status" data-model="Admin"
                            value="' . $row->status . '"
                            data-id="' . base64_encode($row->id) . '" name="status"
                            ' . $fieldValue . '>
                        <label class="custom-control-label" for="status' . base64_encode($row->id) . '">' . $fieldLabel . '</label>
                    </div>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('admin.edit', base64_encode($row->id)) . '" class="btn btn-primary btn-sm mr-1" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit" style="margin-right: 3px;"><i class="fas fa-edit"></i></a>';
                    $btn .= '<a href="' . route('admin.change-password.edit', base64_encode($row->id)) . '" class="btn btn-primary btn-sm mr-1" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Change Password" style="margin-right: 3px;"><i class="fas fa-key"></i></a>';
                    if (collect([$this->superAdminId, auth('admin')->id()])->doesntContain($row->id)) {
                        $btn .= '<form action="' . route('admin.destroy', base64_encode($row->id))  . '" method="POST" style="display: inline-block;">' . csrf_field() . method_field('DELETE') . '<button type="button" class="btn btn-danger btn-sm delete-btn" data-delete-message-type="itemOnly" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete"><i class="fas fa-trash"></i></button></form>';
                    }
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->toJson();
        }
        return view('admin::admin.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::select('id', 'name')->whereNot('name', 'Super Admin')->get();
        return view('admin::admin.form', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminRequest $request)
    {
        DB::beginTransaction();

        try {
            $admin = Admin::create($request->except('role_id'));

            if ($roles = $request->input('role_id')) {
                $admin->assignRole(Role::whereIn('id', $roles)->pluck('name')->toArray());
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Admin created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json(['success' => false, 'message' => 'Something went wrong.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $superAdminId = $this->superAdminId;
        $admin = Admin::find(base64_decode($id));
        $roles = Role::select('id', 'name')->whereNot('name', 'Super Admin')->get();
        return view('admin::admin.form', compact('superAdminId', 'admin', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $admin = Admin::find(base64_decode($id));
            $admin->update($request->except('role_id'));

            if ($admin->id !== $this->superAdminId && $roles = $request->input('role_id')) {
                $admin->syncRoles(Role::whereIn('id', $roles)->pluck('name')->toArray());
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Admin updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json(['success' => false, 'message' => 'Something went wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $admin = Admin::findOrFail(base64_decode($id));
            $admin->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Admin deleted successfully.']);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'The requested Admin does not exist.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json(['success' => false, 'message' => 'Something went wrong.']);
        }
    }

    /**
     * Show the form for editing the admin's password.
     */
    public function editPassword($id)
    {
        $admin = Admin::find(base64_decode($id));
        return view('admin::admin.change-password', compact('admin'));
    }

    /**
     * Update the admin's password in storage.
     */
    public function updatePassword(AdminPasswordRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $admin = Admin::find(base64_decode($id));
            $admin->update($request->all());

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Admin Password updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json(['success' => false, 'message' => 'Something went wrong.']);
        }
    }

    /**
     * Log out the admin.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.show');
    }
}
