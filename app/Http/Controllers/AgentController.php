<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Traits\ResponseHandlerTrait;

class AgentController extends Controller
{
    use ResponseHandlerTrait;

    private function authorizeUser(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->user_type !== 'super_agent') {
            return null;
        }
        return $user;
    }

    // Applies parent_id filter automatically for super_agent from token
    private function scopeByAuth($query, $authUser)
    {
        if ($authUser->user_type === 'super_agent') {
            $query->where('parent_id', $authUser->id);
        }
        return $query;
    }

    public function index(Request $request)
    {
        $authUser = $this->authorizeUser($request);
      
        if (!$authUser) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized. Only super_agent can access this.",
                "errorCode" => 1,
                "data" => (object)[],
            ], 401);
        }

        $perPage = (int) $request->input('per_page', 15);

        $query = User::where('user_type', 'agent')
            ->select('id', 'username', 'user_type', 'parent_id', 'status', 'created_at');

        $this->scopeByAuth($query, $authUser);

        // Optional search by username
        if ($request->filled('search')) {
            $query->where('username', 'like', '%' . $request->search . '%');
        }

        // Optional status filter
        if ($request->filled('status')) {
            $query->where('status', $request->boolean('status'));
        }

        $agents = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            "message"       => "Success",
            "toast_message" => "Agent list fetched successfully",
            "errorCode"     => 0,
            "data"          => [
                "pagination" => [
                    "total"        => $agents->total(),
                    "per_page"     => $agents->perPage(),
                    "current_page" => $agents->currentPage(),
                    "last_page"    => $agents->lastPage(),
                    "from"         => $agents->firstItem(),
                    "to"           => $agents->lastItem(),
                ],
                "data" => $agents->items(),
            ]
        ], 200);
    }

    public function show(Request $request, $id)
    {
        $authUser = $this->authorizeUser($request);

        if (!$authUser) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized. Only super_agent can access this.",
                "errorCode" => 1,
                "data" => (object)[],
            ], 401);
        }

        $agent = $this->scopeByAuth(
                User::where('user_type', 'agent')
                    ->select('id', 'username', 'user_type', 'parent_id', 'password', 'plain_password', 'status'),
                $authUser
            )->find($id);

        if (!$agent) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Agent not found.",
                "errorCode" => 1,
                "data" => (object)[],
            ], 404);
        }

        return response()->json([
            "message" => "Success",
            "toast_message" => "Agent fetched successfully",
            "errorCode" => 0,
            "data" => $agent
        ], 200);
    }

    public function store(Request $request)
    {
        $authUser = $this->authorizeUser($request);

        if (!$authUser) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized. Only super_agent can access this.",
                "errorCode" => 1,
                "data" => (object)[],
            ], 401);
        }

        $rules = [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'status'   => 'sometimes|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);

        // Ensure username is unique per user_type
        $validator->after(function ($v) use ($request) {
            $exists = User::where('username', $request->username)
                ->where('user_type', 'agent')
                ->exists();
            if ($exists) {
                $v->errors()->add('username', 'This username already exists for agent type.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                "message" => "Error",
                "toast_message" => $validator->errors()->first(),
                "errorCode" => 1,
                "data" => (object)[],
            ], 200);
        }

        // parent_id always taken from token
        $parentId = $authUser->id;

        DB::beginTransaction();

        try {
            $plainPassword = $request->password;

            $agent = User::create([
                'username'       => $request->username,
                'user_type'      => 'agent',
                'parent_id'      => $parentId,
                'password'       => Hash::make($plainPassword),
                'plain_password' => $plainPassword,
                'status'         => $request->input('status', true),
            ]);

            DB::commit();

            return response()->json([
                "message" => "Success",
                "toast_message" => "Agent created successfully",
                "errorCode" => 0,
                "data" => $agent
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to create agent",
                "errorCode" => 1,
                "error" => $e->getMessage(),
                "data" => (object)[]
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $authUser = $this->authorizeUser($request);

        if (!$authUser) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized. Only super_agent can access this.",
                "errorCode" => 1,
                "data" => (object)[],
            ], 401);
        }

        $agent = $this->scopeByAuth(User::where('user_type', 'agent'), $authUser)->find($id);

        if (!$agent) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Agent not found.",
                "errorCode" => 1,
                "data" => (object)[],
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|min:6',
            'status'   => 'sometimes|boolean',
        ]);

        // Username uniqueness check (excluding current agent)
        $validator->after(function ($v) use ($request, $agent) {
            if ($request->filled('username')) {
                $exists = User::where('username', $request->username)
                    ->where('user_type', 'agent')
                    ->where('id', '!=', $agent->id)
                    ->exists();
                if ($exists) {
                    $v->errors()->add('username', 'This username already exists for agent type.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                "message" => "Error",
                "toast_message" => $validator->errors()->first(),
                "errorCode" => 1,
                "data" => (object)[],
            ], 200);
        }

        DB::beginTransaction();

        try {
            $fillable = $request->only(['username', 'status']);

            if ($request->filled('password')) {
                $fillable['password']       = Hash::make($request->password);
                $fillable['plain_password'] = $request->password;
            }

            $agent->update($fillable);

            DB::commit();

            return response()->json([
                "message" => "Success",
                "toast_message" => "Agent updated successfully",
                "errorCode" => 0,
                "data" => $agent->fresh()
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "message" => "Error",
                "toast_message" => "Failed to update agent",
                "errorCode" => 1,
                "error" => $e->getMessage(),
                "data" => (object)[]
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $authUser = $this->authorizeUser($request);

        if (!$authUser) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Unauthorized. Only super_agent can access this.",
                "errorCode" => 1,
                "data" => (object)[],
            ], 401);
        }

        $agent = $this->scopeByAuth(User::where('user_type', 'agent'), $authUser)->find($id);

        if (!$agent) {
            return response()->json([
                "message" => "Error",
                "toast_message" => "Agent not found.",
                "errorCode" => 1,
                "data" => (object)[],
            ], 404);
        }

        $agent->delete();

        return response()->json([
            "message" => "Success",
            "toast_message" => "Agent deleted successfully",
            "errorCode" => 0,
            "data" => (object)[]
        ], 200);
    }
}
