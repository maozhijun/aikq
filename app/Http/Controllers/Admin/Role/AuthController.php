<?php

namespace App\Http\Controllers\Admin\Role;

use App\Models\Admin\Account;
use App\Models\Admin\AdRole;
use App\Models\Admin\AdRoleAccount;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin_auth')->except(['index', 'logout']);
    }

    /**
     * 首页
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $method = $request->getMethod();
        if (strtolower($method) == "get") {//跳转到登录页面
            return view('admin.login');
        }

        $target = $request->input("target", '/admin/index');
        $account = $request->input("email", '');
        $password = $request->input("password");
        $remember = $request->input("remember", 0);

        $account = Account::query()->where("email", $account)->first();
        if (!isset($account)) {
            return back()->with(["error" => "账户或密码错误"]);
        }

        $salt = $account->salt;
        $pw = $account->password;
        //判断是否登录
        if ($pw != Account::shaPassword($salt, $password)) {
            return back()->with(["error" => "账户或密码错误"]);
        }

        $token = Account::generateToken();
        $account->token = $token;
        if ($remember == 1) {
            $account->expired_at = date_create('7 day');
        } else {
            $account->expired_at = date_create('30 min');
        }

        if ($account->save()) {
            session([Account::AIKQ_ADMIN_AUTH_SESSION => $account]);//登录信息保存在session
            if ($remember == 1) {
                $c = cookie(Account::AIKQ_ADMIN_AUTH_TOKEN, $token, 60 * 24 * 7, '/', 'aikq.cc', false, true);
                return response()->redirectTo($target)->withCookies([$c]);
            } else {
                return response()->redirectTo($target);
            }
        }
        return back()->with(["error" => "账户或密码错误"]);
    }

    public function accounts(Request $request)
    {
        $accounts = Account::query()->orderBy('created_at', 'desc')->get();
        $roles = AdRole::query()->get();
        return view('admin.auth.account_list', ['accounts' => $accounts, 'roles'=>$roles]);
    }

    public function put(Request $request)
    {
        $roles = $request->input("roles");//角色id 格式：id1,id2,id3,...,idn
        if (!empty($roles)) {
            $role_array = explode(",", $roles);
        } else {
            $role_array = [];
        }
        if ($request->has('id')) {
            $account = Account::query()->find($request->input('id', 0));
            if (empty($account)) {
                return back()->with('error', '无效的账户');
            }
            if ($request->has('account')) {
                $account->account = $request->input('account', '');
            }
            if ($request->has('email')) {
                $account->email = $request->input('email', '');
            }
            if ($request->has('name')) {
                $account->name = $request->input('name', '');
            }
            if ($request->has('status')) {
                $account->status = $request->input('status', 0);
            }
            if ($request->has('password') && $request->input('password', '') != '******') {
                $password = sha1($request->input('password', ''));
                $salt = $account->salt;
                $account->password = sha1($salt . $password);
            }
            $exception = DB::transaction(function () use ($account, $role_array) {
                $account->save();
                $account_id = $account->id;
                AdRoleAccount::query()->where("account_id", $account_id)->delete();//删除原来角色
                if (isset($role_array) && count($role_array) > 0) {
                    foreach ($role_array as $role_id) {
                        $ra = new AdRoleAccount();
                        $ra->role_id = $role_id;
                        $ra->account_id = $account_id;
                        $ra->save();
                    }
                }
            });
            if (!isset($exception)) {
                return back()->with('success', '更新账户成功');
            } else {
                return back()->with('error', '更新账户失败');
            }
        } else {
            if (!$request->has('account')) {
                return back()->with('error', '账户名不能为空');
            }
            if (!$request->has('name')) {
                return back()->with('error', '昵称不能为空');
            }
            if (!$request->has('email')) {
                return back()->with('error', '邮箱不能为空');
            }
            if (!$request->has('password')) {
                return back()->with('error', '密码不能为空');
            }
            if (!$request->has('status')) {
                return back()->with('error', '状态不能为空');
            }

            $account = Account::query()->where('account', $request->input('account', ''))->first();
            if (isset($account)) {
                return back()->with(['error' => '账号名已经存在'])->withInput(['password']);
            }
            $account = Account::query()->where('email', $request->input('email', ''))->first();
            if (isset($account)) {
                return back()->with(['error' => '邮箱已经存在'])->withInput(['password']);
            }

            $account = new Account();
            $password = sha1($request->input('password', ''));
            $salt = uniqid('mm:', true);
            $account->name = $request->input('name', '');
            $account->email = $request->input('email', '');
            $account->account = $request->input('account', '');
            $account->salt = $salt;
            $account->password = sha1($salt . $password);
            $account->status = $request->input('status', 1);

            $exception = DB::transaction(function () use ($account, $role_array) {
                $account->save();
                $account_id = $account->id;
                AdRoleAccount::query()->where("account_id", $account_id)->delete();//删除原来角色
                if (isset($role_array) && count($role_array) > 0) {
                    foreach ($role_array as $role_id) {
                        $ra = new AdRoleAccount();
                        $ra->role_id = $role_id;
                        $ra->account_id = $account_id;
                        $ra->save();
                    }
                }
            });
            if (!isset($exception)) {
                return back()->with('success', '账号创建成功');
            } else {
                return back()->with('error', '账号创建失败');
            }
        }
    }

    public function delete(Request $request)
    {
        $id = $request->input("id");
        if (!is_numeric($id)) {
            return back()->with(['error' => '参数错误']);
        }
        $account = Account::query()->find($id);
        if (!isset($account)) {
            return back()->with(['error' => '账号不存在']);
        }

        $exception = DB::transaction(function () use ($account) {
            $account->delete();
            AdRoleAccount::query()->where("account_id", $account->id)->delete();
        });
        if (isset($exception)) {
            return back()->with('error', '删除账号失败');
        }
        return back()->with('success', '删除账号成功');
    }

    public function logout(Request $request)
    {
        session()->forget(Account::AIKQ_ADMIN_AUTH_SESSION);
        setcookie(Account::AIKQ_ADMIN_AUTH_TOKEN, '', time() - 3600, '/', 'aikq.cc');
        return response()->redirectTo('/admin/login');
    }

}
