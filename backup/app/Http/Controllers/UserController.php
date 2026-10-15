<?php

namespace App\Http\Controllers;

use App\Dao\Models\SystemRole;
use App\Dao\Models\User;
use App\Dao\Repositories\UserRepository;
use App\Http\Requests\GeneralRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Services\CreateService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateProfileService;
use App\Http\Services\UpdateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Plugins\Notes;
use Plugins\Response;
use Illuminate\Support\Facades\DB;

class UserController extends MasterController
{
    public function __construct(UserRepository $repository, SingleService $service)
    {
        self::$repository = self::$repository ?? $repository;
        self::$service = self::$service ?? $service;
    }

    protected function beforeForm()
    {
        $roles = SystemRole::getOptions();

        self::$share = [
            'roles' => $roles,
        ];
    }

    public function postCreate(UserRequest $request, CreateService $service)
    {
        $data = $service->save(self::$repository, $request);

        return Response::redirectBack($data);
    }

    public function postUpdate($code, UserRequest $request, UpdateService $service)
    {
        $data = $service->update(self::$repository, $request, $code);

        return Response::redirectBack($data);
    }

    public function changePassword()
    {
        if (request()->method() == 'POST') {

            User::find(auth()->user()->id)->update([
                'password' => bcrypt(request()->get('password')),
            ]);

            return redirect()->route('home');
        }

        return view('auth.change_password')->with($this->share());
    }

    public function postLoginApi(LoginRequest $request)
    {
        $user = User::where('username', $request->username)->first();

        if (! Hash::check($request->password, $user->password)) {
            return Notes::error([
                'password' => 'Password Tidak Di temukan',
            ], 'Login Gagal');
        }

        // if($user->tokens()){
        //     $user->tokens()->delete();
        // }

        $token = $user->createToken($user->name);
        $string_token = $token->plainTextToken;
        $user->api_token = $string_token;
        $user->save();


        $menu = DB::table('menu')->where('menu_role', $user->role)->get()->map(function($item){
            return $item->menu_name;
        });

        $data = $user->toArray();

        $data['menu'] = $menu;

        return Notes::token($data);
    }

    public function getProfile()
    {
        $user = Auth::user();
        return moduleView('pages.user.profile', $this->share([
            'model' => $user,
        ]));
    }

    public function postProfile(GeneralRequest $request, UpdateProfileService $service)
    {
        $data = $service->save($request);

        return Response::redirectBack($data);
    }
}
