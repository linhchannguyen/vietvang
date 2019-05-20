<?php
/**
 * Create by: Nguyen Linh Chan
 * Date: 13/5/2019
 * Place: Viet Vang Company
 */
namespace App\Http\Controllers;

use Config;
use Excel;
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

    public function export()
    {
        $users = $this->user->getAllUser()->toArray();
        $data_arr[] = array(
            'First Name',
            'Last Name',	
            ' Gender ID',
            'Birthday',
            'Phone Number',
            'Postcode',	
            'Email',	
            'Address',
            'Activated',
            'Role');
        foreach($users as $value)
        {
            $data_arr[] = array(
                'First Name'    => $value['first_name'],
                'Last Name'     => $value['last_name'],
                'Gender ID'     => $value['gender_id'],
                'Birthday'      => date('Y-m-d', strtotime($value['birthday'])),
                'Phone Number'  => $value['phone'],
                'Postcode'      => $value['postcode'],
                'Email'         => $value['email'],
                'Password'      => $value['password'],
                'Address'       => $value['address'],
                'Address'       => $value['address'],
                'Activated'     => $value['activated'],
                'Role'          => $value['role']);
        }
        Excel::create('Users', function($excel) use ($data_arr){
            $excel->setTitle('Users');
            $excel->sheet('Users', function($sheet) use ($data_arr){
                $sheet->fromArray($data_arr, null, 'A1', false, false);
            });
        })->download('xls');
    }

    /**
     * Import excel file
     * Update or Create user with data into excel file
     */
    public function import(Request $request)
    {        
        $validator = Validator::make($request->all(), 
            [
                'user_file' =>'required'
            ],
            [
                'user_file.required' => 'Vui lòng chọn file trước khi hành động',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        else {
            $header_arr = array(
                "first_name",
                "last_name",
                "gender_id",
                "birthday",
                "phone_number",
                "postcode",
                "email",
                "password",
                "address",
                "activated",
                "role");
            
            $extension_arr = array(
                'csv',
                'txt',
                'xls'
            );
            // Check extension file: xls, text, vcs
            $file = $request->file('user_file');
            $extension = $file->getClientOriginalExtension();
            $count_ex = 0;
            foreach($extension_arr as $ex) {
                if($ex == $extension)
                {
                    $count_ex++;
                }
            }
            if($count_ex == 0)
            {
                return back()->with('error', 'File phải là: xls, vcs, txt');  
            }
            $path = $request->file('user_file')->getRealPath();
            // Get header file
            $results = Excel::selectSheetsByIndex(0)->load($path)->get();
            $c = 0;
            foreach($results as $res) {           
                foreach($res as $key => $x){
                    if($header_arr[$c] != $key || $key == null)
                    {
                        return back()->with('error', 'Xem lại định dạng cột ' . $header_arr[$c] . '.');  
                    }
                    $c++;
                }
                break;
            }
            $data = Excel::load($path)->get();
            if($data->count() > 0)
            {
                $co = 2;
                foreach($data->toArray() as $value)
                {     
                    foreach($value as $key_val => $val)
                    {
                        if($val == null) {
                            return back()->with('error', 'Dữ liệu cột ' . $key_val . ' trống tại dòng ' . $co);
                        }
                    }
                    $co++;
                    $user_insert[] = array(
                        'role' => $value['role'],
                        'first_name' => $value['first_name'],
                        'last_name' => $value['last_name'],
                        'email' => $value['email'],
                        'password' => $value['password'],
                        'gender_id' => $value['gender_id'],
                        'birthday' => date('Y-m-d', strtotime($value['birthday'])),
                        'postcode' => $value['postcode'],
                        'phone' => $value['phone_number'],
                        'address' => $value['address'],
                        'activated' => $value['activated'],
                    );            
                }
                foreach($user_insert as $value)
                {
                    $user = $this->user->where('email', $value['email'])->first();
                    if($user)
                    {
                        if($value['password'] != null)
                        {
                            if($length = strlen($value['password']) >= 6)
                            {
                                $this->user->where('email', $value['email'])
                                ->update([
                                'role' => $value['role'],
                                'first_name' => $value['first_name'],
                                'last_name' => $value['last_name'],
                                'email' => $value['email'],
                                'password' => bcrypt($value['password']),
                                'gender_id' => $value['gender_id'],
                                'birthday' => date('Y-m-d', strtotime($value['birthday'])),
                                'postcode' => $value['postcode'],
                                'phone' => $value['phone'],
                                'address' => $value['address'],
                                'activated' => $value['activated'],
                                ]);
                            }
                            else {
                                return back()->with('error', 'Mật khẩu user: ' . $user["email"] . ' không hợp lệ.');
                            }
                        }
                        $this->user->where('email', $value['email'])
                        ->update([
                        'role' => $value['role'],
                        'first_name' => $value['first_name'],
                        'last_name' => $value['last_name'],
                        'email' => $value['email'],
                        'gender_id' => $value['gender_id'],
                        'birthday' => date('Y-m-d', strtotime($value['birthday'])),
                        'postcode' => $value['postcode'],
                        'phone' => $value['phone'],
                        'address' => $value['address'],
                        'activated' => $value['activated'],
                        ]);
                    }
                    else {
                        if($value['password'] == null)
                        {
                            return back()->with('error', 'Mật khẩu user: ' . $value["email"] . ' không thể bỏ trống.');

                        }
                        if($value['password'] != null)
                        {
                            if($length = strlen($value['password']) >= 6)
                            {
                                $this->user->role = $value['role'];
                                $this->user->first_name = $value['first_name'];
                                $this->user->last_name = $value['last_name'];
                                $this->user->email = $value['email'];
                                $this->user->password = bcrypt($value['password']);
                                $this->user->gender_id = $value['gender_id'];
                                $this->user->birthday = date('Y-m-d', strtotime($value['birthday']));
                                $this->user->postcode = $value['postcode'];
                                $this->user->phone = $value['phone'];
                                $this->user->address = $value['address'];
                                $this->user->activated = $value['activated'];
                                $this->user->save();
                            }
                            else {
                                return back()->with('error', 'Mật khẩu user: ' . $value["email"] . ' không hợp lệ.');
                            }
                        }
                    }
                }
            }
            return back()->with('success', 'Excel Data Imported successfully.');
        }
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
     * Check all fields in form reset password with Validator method
     */
    public function do_login(Request $request)
    {       
        Session::forget('signup_email');
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
            $this->user->activated = 1;
            $this->user->last_access = date('Y-m-d H:i:s');
            $this->user->attempt = 0;
            $this->user->save();
            $email = $request['email'];
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
        Auth::guard('admin')->logout(); //Admin class extends Authenticatable class
        return Redirect::to('/login');
    }
}
