<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\SecurityRequest;
use App\Models\Session;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;


class UserSettingsController extends Controller
{
    public function security() {
        return view('backend.pages.user.security');
    }

    public function security_store(SecurityRequest $request): RedirectResponse
    {
        $request->user()->update(['password' => Hash::make($request->input('new_password'))]);
        $this->alert('success', __('Вы успешно изменили пароль'));
        return back();
    }

    public function profile() {
        return view('backend.pages.user.profile');
    }

    public function profile_store(ProfileRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        if (isset($data['email']) && $data['email'] !== $user->email) {
            $user->email_verified_at = null;
        }

        $user->fill($data);

        $user->save();

        $this->alert('success', __('Вы успешно обновили информацию профиля'));

        return back();
    }

    public function activity() {
        $sessions = Session::latest('last_activity')->limit(20)->where('user_id', auth()->id())->get();
        return view('backend.pages.user.activity', compact('sessions'));
    }

    public function activity_destroy($id) {
        Session::where('user_id', auth()->id())->where('id', $id)->delete();
        return back();
    }
}
