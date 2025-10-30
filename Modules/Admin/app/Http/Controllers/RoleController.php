<?php

namespace Modules\Admin\Http\Controllers;

use App\Helpers\BackendHelpers;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Http\Requests\RoleRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Role::whereNot('name', 'Super Admin')->when(
                BackendHelpers::isOrderColumnZero($request),
                fn($query) => $query->orderBy('id')
            );
            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('admin.roles.edit', base64_encode($row->id)) . '" class="btn btn-primary btn-sm mr-1" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit" style="margin-right: 3px;"><i class="fas fa-edit"></i></a>';
                    $btn .= '<form action="' . route('admin.roles.destroy', base64_encode($row->id))  . '" method="POST" style="display: inline-block;">' . csrf_field() . method_field('DELETE') . '<button type="button" class="btn btn-danger btn-sm role-delete-btn" data-delete-message-type="itemOnly" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete"><i class="fas fa-trash"></i></button></form>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin::roles.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::get();
        return view('admin::roles.form', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        DB::beginTransaction();

        try {
            $role = Role::create(['name' => $request->name, 'guard_name' => 'admin']);
            $role->syncPermissions($request->permissions);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Role created successfully.']);
        } catch (\Throwable $e) {
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
        $role = Role::findOrFail(base64_decode($id));
        $permissions = Permission::get();
        return view('admin::roles.form', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $role = Role::find(base64_decode($id));
            $role->update(['name' => $request->name]);
            $role->syncPermissions($request->permissions);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Role updated successfully.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json(['success' => false, 'message' => 'Something went wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $role = Role::findOrFail(base64_decode($id));
        $admins = Admin::whereHas('roles', fn($q) => $q->where('id', $role->id))->get();

        if ($admins->isNotEmpty()) {
            if ($request->boolean('cascade_delete')) {
                $admins->each(function ($admin) use ($role) {
                    $admin->removeRole($role);

                    if (!$admin->roles()->exists()) {
                        $admin->delete();
                    }
                });

                $role->delete();

                return response()->json(['success' => true, 'message' => 'The role and any admins without roles have been successfully deleted.']);
            }

            return response()->json([
                'success' => false,
                'message' => 'This role is assigned to admins. Confirm to remove the role from all admins and delete it.',
            ]);
        }

        $role->delete();

        return response()->json(['success' => true, 'message' => 'Role deleted successfully.']);
    }
}
