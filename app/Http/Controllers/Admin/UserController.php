<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::latest()->paginate();

        return response()->json($users);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', Password::defaults()],
        ]);
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);

        return response()->json($user, JsonResponse::HTTP_CREATED);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['sometimes', Password::defaults()],
        ]);

        if (! empty($request->input('password'))) {
            $data['password'] = bcrypt($request->input('password'));
        } else {
            $data['password'] = $user->password;
        }

        $user->update($data);

        return response()->json($user);
    }

    public function destroy(User $user): Response
    {
        $user->delete();

        return response()->noContent();
    }

    public function changeRole(Request $request, User $user): Response
    {
        $user->update([
            'role' => $request->input('role'),
        ]);

        return response()->noContent();
    }

    public function search(Request $request): JsonResponse
    {
        $searchQuery = $request->input('query');

        $users = User::where('name', 'like', "%{$searchQuery}%")
            ->paginate()
            ->withQueryString();

        return response()->json($users);
    }

    public function bulkDelete(Request $request): Response
    {
        User::whereIn('id', $request->input('ids'))->delete();

        return response()->noContent();
    }
}
