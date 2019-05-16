<?php
/**
 * Create by: Nguyen Linh Chan
 * Date: 13/5/2019
 * Place: Viet Vang Company
 */
namespace App\Http\Controllers;

use App\User;
use App\Http\Models\Admin;
use App\Http\Models\Gender;
use App\Http\Models\User_Type;
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
    protected $user_type;
    protected $gender;
    protected $admin;

    // Constructor User object
    public function __construct ()
    {
        $this->user = new User();
        $this->user_type = new User_Type();
        $this->gender = new Gender();
        $this->admin = new Admin();
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
            $user = $this->admin->findUserByToken(Session::get('token'));
            if($user){
                if($user->token == Session::get('token') && $user->role == 1)
                {
                    return redirect('admin/');
                }
            }
            
        }
        return view('login');
    }

    /**
     * Login
     * User login with email and password.
     */
    public function do_login(Request $request)
    {       
        Session::forget('signup_email');
        // Check all fields in form reset password with Validator method
        $validator = Validator::make($request->all(), 
            [
            'email' =>'required|email', // Check field email
            'password' => 'required|min:6', // Check field password
            ],
            [
                // Show notification
                'email.required' => 'Vui lòng nhập email', 
                'email.email' => 'Email không đúng định dạng',
                'password.required' => 'Vui lòng nhập mật khẩu',
                'password.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            try {
                $email = $request->input('email');
                $password = $request->input('password');
                // $data = $this->user->findUserByEmail($email);
                
                $data;
                $user = $this->user->findUserByEmail($email);
                $admin = $this->admin->findAdminByEmail($email);
                if (!$user && !$admin) {
                    return redirect()->back()->with('alert-error', 'Email không tồn tại!');
                }
                if($user)
                {
                    $data = $user;
                }
                if($admin)
                {
                    $data = $admin;
                }
                
                if ($data->activated == 0) {
                    $minutes = round((time() - strtotime($data->last_access)) / 60);
                    if ($minutes <= 30) {
                        $errors = new MessageBag(['error' => 'Tài khoản đã bị khóa']);
                        return redirect()->back()->withInput()->withErrors($errors);
                    } else {
                        if($data->role == 1){
                            $this->admin->updateAccount($email);
                        }
                        if($data->role != 1){
                            $this->user->updateAccount($email);
                        }
                        return redirect('/login')->with('alert-success', 'Tài khoản đã được kích hoạt, vui lòng đăng nhập lại!');
                    }
                } else {
                    if (Auth::guard('user')->attempt(['email' => $email, 'password' => $password]) || Auth::guard('admin')->attempt(['email' => $email, 'password' => $password])) 
                    {                      
                        if($data->role != 1){
                            $token = str_random(32);
                            $data->token = $token;
                            $data->token_expire = strtotime('1 days');
                            $data->save();
                            Session::put('name', $data['last_name']);
                            Session::put('id', $data->id);
                            Session::put('role', $data->role);
                            Session::put('token', $token);
                            Session::put('login', TRUE);
                            return redirect('/welcome');
                        }
                        if($data->role == 1){
                            $token = str_random(32);
                            $data->token = 
                            $data->token_expire = strtotime('1 days');
                            Admin::where('email', $email)
                            ->update([
                            'token' => $token,
                            ]);
                            Session::put('name', $data['last_name']);
                            Session::put('id', $data->id);
                            Session::put('email', $data->email);
                            Session::put('role', $data->role);
                            Session::put('token', $token);
                            Session::put('login', TRUE);
                            $this->admin->loginSuccess($email);
                            return redirect('/admin');
                        }
                    } else {
                        if($data->role != 1){
                            $this->user->updateAttemptLoginFail($email, $data->attempt);
                        }
                        if($data->role == 1){
                            $this->admin->updateAttemptLoginFail($email, $data->attempt);
                        }
                        if (($data->attempt) + 1 >= 3) {
                            if($data->role != 1){
                                $this->user->blockAccount($email);
                            }
                            if($data->role == 1){
                                $this->admin->blockAccount($email);
                            }                            
                            $errors = new MessageBag(['login' => 'Nhập sai quá số lần quy định, tài khoản đã bị khóa!']);
                            return redirect()->back()->withInput()->withErrors($errors);
                        }
                        $errors = new MessageBag(['login' => 'Email hoặc mật khẩu không đúng']);
                        return redirect()->back()->withInput()->withErrors($errors);
                    }
                }
            }catch (\Exception $exception)
            {
                dd($exception);
                return redirect()->back()->withInput()->withErrors($exception);
            }
        }
    }

    /**
     * Register
     * User register account
     */
    public function user_signup()
    {
        $genders = $this->gender->getAllGender();
        return view('client.signup', compact('genders'));
    }

     /**
     * Handle signup
     * Create account with name, email, password
     */
    public function user_store(Request $request){
        $validator = Validator::make(
            $request->all(),
            [
            'first_name' => 'required|min:2|max:30',
            'last_name' => 'required|min:2|max:30',
            'email' => 'required|min:15|max:50|email|unique:users|unique:admins',
            'password' => 'required|min:6|max:30|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'required|min:6|max:30',
            ],
            [
            'first_name.required' => 'Vui lòng nhập họ',
            'first_name.min' => 'Họ quá ngắn',
            'first_name.max' => 'Họ quá dài',
            'last_name.required' => 'Vui lòng nhập tên',
            'last_name.min' => 'Tên quá ngắn',
            'last_name.max' => 'Tên quá dài',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự',     
            'password.same' => 'Mật khẩu không chính xác',                        
            'password.required_with' => 'Mật khẩu không chính xác',            
            'password_confirmation.required' => 'Vui lòng nhập lại mật khẩu',
            'password_confirmation.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự',     
            'password_confirmation.same' => 'Mật khẩu không chính xác',            
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else 
        {
            $this->user->role = 2;
            $this->user->first_name = $request['first_name'];
            $this->user->last_name = $request['last_name'];
            $this->user->email = $request['email'];
            $this->user->password = bcrypt($request['password']);
            // $this->user->gender_id = $request['gender'];
            // $this->user->birthday = $request['birthday'];
            // if($request['postcode'] != null)
            // {
            //     $this->user->postcode = $request['postcode'];
            // }
            // if($request['phone'] != null)
            // {
            //     $this->user->phone = $request['phone'];
            // }
            // if($request['address'] != null)
            // {
            //     $this->user->address = $request['address'];
            // }
            $this->user->activated = 1;
            $this->user->last_access = date('Y-m-d H:i:s');
            $this->user->attempt = 0;
            $this->user->save();
            $email = $request['email'];
            // $user = $this->user->findUserByEmail($email);
            Session::put('signup_email', $email);
            $link = route('login');
            Mail::send('admin.emails.signup', array('name' => $request['last_name'],'signup_email' => $request['email'], 'link' => $link), function($message) use ($email){
                $message->to($email, 'User')->subject('Công Ty Việt Vang Xin Chào');
            });
            return redirect('login')->with('alert-success','Đăng ký tài khoản thành công. Vui lòng kiểm tra mail!');
        }
    }

    /**
     * Register
     * Admin register account
     */
    public function admin_signup()
    {
        $genders = $this->gender->getAllGender();
        return view('admin.signup', compact('genders'));
    }

    /**
    * Handle signup
    * Create account with name, email, password
    */
    public function admin_store(Request $request){
        $validator = Validator::make(
            $request->all(),
            [
            'first_name' => 'required|min:2|max:30',
            'last_name' => 'required|min:2|max:30',
            'email' => 'required|min:15|max:50|email|unique:users|unique:admins',
            'password' => 'required|min:6|max:30|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'required|min:6|max:30',
            ],
            [
            'first_name.required' => 'Vui lòng nhập họ',
            'first_name.min' => 'Họ quá ngắn',
            'first_name.max' => 'Họ quá dài',
            'last_name.required' => 'Vui lòng nhập tên',
            'last_name.min' => 'Tên quá ngắn',
            'last_name.max' => 'Tên quá dài',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'email.min' => 'Email không đúng định dạng',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự',            
            'password.same' => 'Mật khẩu không chính xác',            
            'password_confirmation.required' => 'Vui lòng nhập lại mật khẩu',
            'password_confirmation.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else 
        {          

            // Insert info in User table
            $this->admin->role = 1;
            $this->admin->first_name = $request['first_name'];
            $this->admin->last_name = $request['last_name'];
            $this->admin->email = $request['email'];
            $this->admin->password = bcrypt($request['password']);
            $this->admin->activated = 1;
            $this->admin->last_access = date('Y-m-d H:i:s');
            $this->admin->attempt = 0;
            $this->admin->save();
            $email = $request['email'];
            // $user = $this->user->findUserByEmail($email);
            Session::put('signup_email', $email);
            $link = route('login');
            Mail::send('admin.emails.signup', array('name' => $request['last_name'],'email' => $request['email'], 'link' => $link), function($message) use ($email){
                $message->to($email, 'User')->subject('Công Ty Việt Vang Xin Chào');
            });
            return redirect('login')->with('alert-success','Đăng ký tài khoản thành công. Vui lòng kiểm tra mail!');
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
        $admin = $this->admin->findAdminByEmail($email);        
        if (!$user && !$admin) {
            $error = new MessageBag(['error' => 'Email không tồn tại!']);
            return redirect()->back()->withInput()->withErrors($error);
        }
        $data;
        if($user) {
            $data = $user;
        }
        if($admin) {
            $data = $admin;
        }
        $data->reset_pass_token = $reset_pass_token;
        $data->save();
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
     * Send content mail to user mail define password change
     */
    public function reset_link($token, $email){
        if(Session::get('token_forget') == null)
        {
            $errors = new MessageBag(['error' => 'Thời gian chờ quá lâu, vui lòng kiểm tra lại!']);
            return redirect('/admin/404_page')->with('errors', $errors);
        }
        return view("admin.reset_form",compact('email'));
    }

    /**
     * Reset password
     * Check all fields in form reset password with Validator method
     * Find user
     */
    function do_reset(Request $request){
        
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
            'password' => 'required|min:6|max:30|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'required|min:6|max:30',
        ],[            
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự',            
            'password.same' => 'Mật khẩu không chính xác',            
            'password_confirmation.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự',
            'password_confirmation.same' => 'Mật khẩu không chính xác',
            
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->with('alert-success', 'Mật khẩu đã được thay đổi.!');
        }
        $email = $request->email;
        $password = $request->password;
        $user = User::where('email',$email)->first();
        $admin = Admin::where('email',$email)->first();
        $data;
        if($user)
        {
            $data = $user;
        }
        if($admin)
        {
            $data = $admin;
        }
        if($data->reset_pass_token == null)
        {
            return redirect()->back()->withErrors($validator)->with('alert-error', 'Url đã bị vô hiệu hóa!');
        }
        $data->password = bcrypt($password);
        $data->reset_pass_token = "";
        $data->save();
        Session::put('signup_email', $email);
        return redirect('/login');
    }

    /**
     * Admin logout
     * Redirect the user to the login screen
     * Set admin token by null whith adminLogout method
     */
    public function logout(){
        $this->admin->adminLogout(Session::get('token'));
        Auth::guard('admin')->logout(); 
        return Redirect::to('/login');
    }
}
