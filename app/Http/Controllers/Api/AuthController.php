<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    // Register Job Seeker
    public function registerJobSeeker(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'job_seeker',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    // Register Employer (HRD)
    public function registerEmployer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'website' => 'nullable|url',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'employer',
        ]);

        Company::create([
            'user_id' => $user->id,
            'company_name' => $validated['company_name'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'website' => $validated['website'] ?? null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    // Login (untuk kedua role)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'role' => $user->role,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function profile(Request $request)
    {
        $user = $request->user()->load('company'); // eager load relasi company
        return response()->json($user);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email,' . $user->id,
            'phone'             => 'nullable|string|max:20',
            'location'          => 'nullable|string|max:255',   // untuk user
            'bio'               => 'nullable|string',
            'jabatan'           => 'nullable|string|max:100',
            // data perusahaan (hanya employer)
            'company_name'      => 'nullable|string|max:255',
            'description'       => 'nullable|string',
            'industry'          => 'nullable|string|max:100',
            'company_location'  => 'nullable|string|max:255',
            'employee_count'    => 'nullable|string|max:100',
            'website'           => 'nullable|url|max:255',
        ]);

        // Update user
        $user->update([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? $user->phone,
            'location' => $validated['location'] ?? $user->location,
            'bio'      => $validated['bio'] ?? $user->bio,
            'jabatan'  => $validated['jabatan'] ?? $user->jabatan,
        ]);

        // Update perusahaan (jika employer)
        if ($user->role === 'employer' && $user->company) {
            $user->company->update([
                'company_name'   => $validated['company_name'] ?? $user->company->company_name,
                'description'    => $validated['description'] ?? $user->company->description,
                'industry'       => $validated['industry'] ?? $user->company->industry,
                'location'       => $validated['company_location'] ?? $user->company->location,
                'employee_count' => $validated['employee_count'] ?? $user->company->employee_count,
                'website'        => $validated['website'] ?? $user->company->website,
            ]);
        }

        return response()->json(['message' => 'Profil berhasil diperbarui.']);
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();

        // Hapus avatar lama jika ada
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        if ($user->role === 'employer' && $user->company) {
            $user->company->logo = $path;
            $user->company->save();
        }

        return response()->json([
            'message' => 'Avatar berhasil diperbarui.',
            'avatar_url' => asset('storage/' . $path)
        ]);
    }

    public function deleteAvatar(Request $request)
    {
        $user = $request->user();

        if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
        }

        $user->avatar = null;
        $user->save();

        if ($user->role === 'employer' && $user->company) {
            $user->company->logo = null;
            $user->company->save();
        }

        return response()->json([
            'message' => 'Avatar berhasil dihapus.'
        ]);
    }

    public function updateCv(Request $request)
    {
        $request->validate([
            'cv' => 'required|mimes:pdf,jpg,jpeg,png|max:5120', // max 5MB
        ]);

        $user = $request->user();

        // Hapus CV lama jika ada
        if ($user->cv_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->cv_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->cv_path);
        }

        // Simpan CV baru
        $path = $request->file('cv')->store('cv', 'public');
        $user->cv_path = $path;
        $user->save();

        return response()->json([
            'message' => 'CV berhasil diperbarui.',
            'cv_url' => asset('storage/' . $path)
        ]);
    }

    public function deleteCv(Request $request)
    {
        $user = $request->user();

        if ($user->cv_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->cv_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->cv_path);
        }

        $user->cv_path = null;
        $user->save();

        return response()->json([
            'message' => 'CV berhasil dihapus.'
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        // Hapus avatar jika ada
        if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
        }

        // Hapus cv jika ada
        if ($user->cv_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->cv_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->cv_path);
        }

        // Hapus token yang sedang aktif
        $user->tokens()->delete();
        
        // Hapus user (akan cascade ke tabel yang berelasi)
        $user->delete();

        return response()->json(['message' => 'Akun berhasil dihapus permanen.']);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Kata sandi saat ini salah.'], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Kata sandi berhasil diubah.']);
    }

    // Cek apakah email terdaftar (untuk Lupa Password)
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email tidak terdaftar di sistem kami.'
        ]);

        return response()->json(['message' => 'Email valid.']);
    }

    // Reset password langsung (untuk Lupa Password tanpa login)
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'new_password' => 'required|min:8',
        ]);

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Kata sandi berhasil direset.']);
    }
}