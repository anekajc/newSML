<?php namespace App\Http\Controllers;

use Auth;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Model\User;

class AuthController extends Controller {

    /**
     * Handle an authentication attempt.
     *
     * @return Response
     */
    use AuthenticatesUsers;
    public function __construct()
    {
        $this->redirectTo = route('home');
    }
    protected $redirectTo = '/home';

    public function index() {
        return redirect('/login');
    }

    public function authenticate(Request $req) {
      if (Auth::attempt(['username' => $req->input('username'), 'password' => $req->input('password')])) {
        // return redirect('/home');
        return redirect('/');
      } else {
        // return redirect('/qweqwe');
        return back()->withErrors('1');
      }
    }

    public function updateIdle() {
      // $getIdle = DB::table('new_users')->get();
      // for ($i = 0; $i < count($getIdle); $i++) {
      //   DB::table('new_users')->where('id', $getIdle[$i]->id)->update(['status' => 0, 'hostid' => '', 'ipaddress' => '']);
      // }
    }

    public function username() {
      return filter_var($request->input(‘login’), FILTER_VALIDATE_EMAIL )
        ? ’email’
        : ‘username’;
    }

    public function logout() {
      User::where('id', Auth::id())->update(['status' => 0, 'hostid' => '', 'ipaddress' => '']);
      Auth::logout();
      return redirect('/');
    }
}
