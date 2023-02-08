<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Testing\Fluent\Concerns\Has;

class UserController extends Controller
{
    public function registration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|min:2',
            'last_name' => '',
            'email' => 'required|email|unique:users,email',
            'phone' => 'numeric',
            'password' => 'required|confirmed|min:5|max:255|alpha_num',
            'password_confirmation' => 'required',
        ]);
        if (!$validator->stopOnFirstFailure()->fails()) {
            $userdata = $request->all();
            $data['user'] = User::create([
                'first_name' => $userdata['first_name'],
                'last_name' => $userdata['last_name'],
                'email' => $userdata['email'],
                'phone' => $userdata['phone'],
                'password' => $userdata['password'],
            ]);
            $userdata = $request->only('email', 'password');
            $data['auth'] = Auth::attempt($userdata);
            $response = [
                'success' => true,
                'userdata' => $data,
                'error' => null,
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'success' => false,
                'userdata' => null,
                'error' => $validator->errors()->first(),
            ];
            return response()->json($response, 200);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|alpha_num',
        ]);
        $userdata = $request->only('email', 'password');
        if ($validator->stopOnFirstFailure()->fails()) {
            $response = [
                'success' => false,
                'userdata' => null,
                'error' => $validator->errors()->first(),
            ];
            return response()->json($response, 200);
        } elseif (Auth::attempt($userdata)) {
            $user = User::where('email', '=', $request->email)->first();
            $data['user'] = Auth::user();
            $data['token'] = $user->createToken($request->email)->plainTextToken;
            $response = [
                'success' => true,
                'userdata' => $data,
                'error' => null,
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'success' => false,
                'userdata' => null,
                'error' => 'Email or password are invalid.',
            ];
            return response()->json($response, 200);
        }
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['success' => true], 200);
    }

    public function getUserdataById($id)
    {
        $userdata = User::find($id);
        if ($userdata) {
            $userdata['isOnline'] = Cache::get('user-is-online-'.$id);
            $response = [
                'success' => true,
                'userdata' => $userdata,
                'error' => null,
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'success' => false,
                'userdata' => null,
                'error' => 'Invalid user id'
            ];
            return response()->json($response, 200);
        }
    }
    public function changeEmail(Request $request)
    {
        $validator = Validator::make($request->only('email'), [
            'email' => 'required|email|unique:users,email',
        ]);
        if (!$validator->stopOnFirstFailure()->fails()) {
            User::find($request->id)->update([
                'email' => $request->email,
            ]);
            $response = [
                'success' => true,
                'userdata' => null,
                'error' => null,
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'success' => false,
                'userdata' => null,
                'error' => $validator->errors()->first(),
            ];
            return response()->json($response, 200);
        }
    }
    public function changePhone(Request $request)
    {
        $validator = Validator::make($request->only('phone'), [
            'phone' => 'required|numeric',
        ]);
        if (!$validator->stopOnFirstFailure()->fails()) {
            User::find($request->id)->update([
                'phone' => $request->phone,
            ]);
            $response = [
                'success' => true,
                'userdata' => null,
                'error' => null,
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'success' => false,
                'userdata' => null,
                'error' => $validator->errors()->first(),
            ];
            return response()->json($response, 200);
        }
    }
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->only('newPassword', 'newPassword_confirmation'), [
            'newPassword' => 'required|confirmed|min:5|max:255|alpha_num',
            'newPassword_confirmation' => 'required',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            $response = [
                'success' => false,
                'userdata' => null,
                'error' => $validator->errors()->first(),
            ];
            return response()->json($response, 200);
        } else {
            User::where('id', '=', $request->id)->update([
                'password' => Hash::make($request->newPassword),
            ]);
            $response = [
                'success' => true,
                'userdata' => null,
                'error' => null,
            ];
            return response()->json($response, 200);
        }
    }

    public function userdata()
    {
        if (Auth::user()) {
            return response()->json(['success'=>true, 'user'=>Auth::user()], 200);
        } else {
            return response()->json(['success'=>false, 'user'=>null], 200);
        }

    }
}
