<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Create User
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'bio' => 'nullable|string',
            'interests' => 'nullable|array',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = new User();
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->password = Hash::make($validatedData['password']);
        $user->bio = $validatedData['bio'] ?? '';
        $user->interests = json_encode($validatedData['interests'] ?? []);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        $user->save();

        return response()->json(['message' => 'User created successfully'], 201);
    }

    // Get User All Information
    public function show()
    {
        $user = User::all();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user, 200);
    }

    // Get User Detail Information
    public function detail($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user, 200);
    }

    // Edit User Information
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8',
            'bio' => 'nullable|string',
            'interests' => 'nullable|array',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if (isset($validatedData['name'])) {
            $user->name = $validatedData['name'];
        }

        if (isset($validatedData['email'])) {
            $user->email = $validatedData['email'];
        }

        if (isset($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->bio = $validatedData['bio'] ?? $user->bio;
        $user->interests = json_encode($validatedData['interests'] ?? json_decode($user->interests));

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        $user->save();

        return response()->json(['message' => 'User updated successfully'], 200);
    }

    // Delete User
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
