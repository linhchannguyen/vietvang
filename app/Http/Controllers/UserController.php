<?php
/**
 * Create by: Nguyen Linh Chan
 * Date: 13/5/2019
 * Place: Viet Vang Company
 */
namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Attribute user of User Model
    protected $user;

    // Constructor User object
    public function __construct ()
    {
        $this->user = new User();
    }

    /**
     * Home page of Admin
     * Redirect to home page of Admin
     */
    public function index()
    {
        return view('admin.home');
    }

    /**
     * User list
     * Show list of users table
     */
    public function user_list()
    {
        $users = $this->user->getAllUser();
        return view('admin.users.user_list', compact('users'));
    }

    /**
     * Login theme
     * Redirect to login theme
     */
    public function login()
    {
        if(Session::get('token'))
        {
            $user = $this->user->findUserByToken(Session::get('token'));
            // dd($user);
            if($user){
                if($user->token == Session::get('token') && $user->role == 1)
                {
                    // dd('vo 2');
                    return redirect('admin/');
                }
            }
            
        }
        return view('admin.login');
    }

    /**
     * Login
     * User login with email and password.
     */
    public function do_login(Request $request)
    {       
        // Check all fields in form reset password with Validator method
        $validator = Validator::make($request->all(), 
            [
            'email' =>'required|email', // Check field email
            'password' => 'required|min:6', // Check field password
            ],
            [
                // Show notification
                'email.required' => 'Email là trường bắt buộc', 
                'email.email' => 'Email không đúng định dạng',
                'password.required' => 'Mật khẩu là trường bắt buộc',
                'password.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            try {
                $email = $request->input('email');
                $password = $request->input('password');
                $data = $this->user->findUserByEmail($email);
                if ($data->activated == 0) {
                    $minutes = round((time() - strtotime($data->last_access)) / 60);
                    if ($minutes <= 30) {
                        $errors = new MessageBag(['error' => 'Tài khoản đã bị khóa']);
                        return redirect()->back()->withInput()->withErrors($errors);
                    } else {
                        $this->user->updateAccount($email);
                        return redirect('/admin/login')->with('alert-success', 'Tài khoản đã được kích hoạt, vui lòng đăng nhập lại!');
                    }
                } else {
                    if (Auth::attempt(['email' => $email, 'password' => $password])) {
                        $user = \auth()->user();
                        $user->token = str_random(32);
                        $user->token_expire = strtotime('1 days');
                        $user->save();
                        Session::put('name', $data->name);
                        Session::put('id', $data->id);
                        Session::put('token', $user->token);
                        Session::put('login', TRUE);
                        $this->user->loginSuccess($email);
                        if($data->role == 1)
                        {
                            return redirect('/admin');
                        }else 
                        {
                            return redirect('/welcome');
                        }
                    } else {
                        $this->user->updateAttemptLoginFail($email, $data->attempt);
                        if (($data->attempt) + 1 >= 3) {
                            $this->user->blockAccount($email);
                            $errors = new MessageBag(['login' => 'Nhập sai quá số lần quy định, tài khoản đã bị khóa!']);
                            return redirect()->back()->withInput()->withErrors($errors);
                        }
                        $errors = new MessageBag(['login' => 'Email hoặc mật khẩu không đúng']);
                        return redirect()->back()->withInput()->withErrors($errors);
                    }
                }
            }catch (\Exception $exception)
            {
                return redirect()->back()->withInput()->withErrors($exception);
            }
        }
    }

    /**
     * Register
     * User register account
     */
    public function signup()
    {
    return view('client.signup');
    }

     /**
     * Handle signup
     * Create account with name, email, password
     */
    public function store(Request $request){
        if($request['password'] != $request['repassword'])
        {
            $errors = new MessageBag(['signup' => 'Mật khẩu không phù hợp']);
            return redirect()->back()->withErrors($errors)->withInput();
        }else
        {
            $validator = Validator::make($request->all(),[
                'name' => 'required|min:4',
                'email' => 'required|min:4|email|unique:users',
                'password' => 'required|min:6',
                'repassword' => 'required|min:6',
            ],[
                'name.required' => 'Vui lòng nhập họ tên',
                'name.min' => 'Tên quá ngắn',
                'email.required' => 'Vui lòng nhập email',
                'email.email' => 'Email không đúng định dạng',
                'password.required' => 'Vui lòng nhập mật khẩu',
                'password.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự',            
                'repassword.required' => 'Vui lòng nhập lại mật khẩu',
                'repassword.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }else 
            {
                $this->user->role = 2;
                $this->user->name = $request['name'];
                $this->user->email = $request['email'];
                $this->user->password = bcrypt($request['password']);
                $this->user->activated = 1;
                $this->user->last_access = date('Y-m-d H:i:s');
                $this->user->attempt = 0;
                $this->user->save();
                $email = $request['email'];
                $user = $this->user->findUserByEmail($email);
                Mail::send('admin.emails.signup', array('name' => $request['name'],'email' => $request['email']), function($message) use ($email){
                    $message->to($email, 'User')->subject('Công Ty Việt Vang Xin Chào');
                });
                return redirect('admin/login')->with('alert-success','Đăng ký tài khoản thành công. Vui lòng kiểm tra mail!');
            }
        }
    }

     /**
     * Forget password
     * Redirect to forget password page
     */
    public function forget_password(){
        return view('admin.forget-password');
    }

    /**
     * Send mail
     * When user forget password, this is ask input email and submit. System will handle and send 
     */
    public function sendMail(Request $request){
        // Check all fields in form reset password with Validator method
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
        ],[
            'email.required' => 'Mật khẩu là trường bắt buộc.'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }
        $email = $request->email;
        $reset_pass_token = str_random(30);
        Session::put('token_forget', $reset_pass_token);
        $user = $this->user->findUserByEmail($email);
        if (!$user) {
            $error = new MessageBag(['error' => 'Email không hợp lệ!']);
            return redirect()->back()->withInput()->withErrors($error);
        }
        $user->reset_pass_token = $reset_pass_token;
        $user->save();
        $link = route('reset-link',['token'=>$reset_pass_token,'email'=>$email]);
        Mail::send('admin.emails.reset_email', array(
            'link'=> $link
        ), function($message) use ($email){
	        $message->to($email, 'User')->subject('Xin chào' . $email);
	    });
        return redirect()->back()->with('alert-success', 'Vui lòng kiểm tra mail để thay đổi mật khẩu mới!');
    }

    /**
     * Reset link
     */
    public function reset_link($token, $email){
        if(Session::get('token_forget') == null)
        {
            $errors = new MessageBag(['error' => 'Thời gian chờ quá lâu, vui lòng kiểm tra lại!']);
            return redirect('/admin/404_page')->with('errors', $errors);
        }
        $user = User::where('email',$email)->first();
        if ($user->reset_pass_token == $token) {
            return view("admin.reset_form",compact('email'));
        }
    }

    /**
     * Handle reset password
     */
    function do_reset(Request $request){
        // Check all fields in form reset password with Validator method
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required|min:6'
        ],[]);
        if ($validator->fails()) {
            $errors = new MessageBag(['error' => 'Reset mật khẩu thất bại, lỗi dữ liệu truyền vào ko đúng ràng buộc']);
            return redirect()->back()->withInput()->withErrors($errors);
        }
        $email = $request->email;
        $password = $request->password;
        $user = User::where('email',$email)->first();
        $user->password = bcrypt($password);
        $user->reset_pass_token = "";
        $user->save();
        return redirect('/admin/login');
    }

    /**
     * Login
     * User logout
     */
    public function logout(){
        Auth::logout();
        $this->user->userLogout(Session::get('token'));
        return Redirect::to('/admin/login'); // redirect the user to the login screen
    }
}
