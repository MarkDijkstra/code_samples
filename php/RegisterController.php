<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo= '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $customMessages = [
            'firstname.required' => 'validation.required',
            'firstname.min' => 'validation.min',
            'lastname.required' => 'validation.required',
            'lastname.min' => 'validation.min',
            'email.required' => 'validation.required',
            'email.email' => 'validation.email',
            'email.unique' => 'validation.unique',
            'password.required' => 'validation.required',
            'password.min' => 'validation.min',
            'password.same' => 'validation.same',
            'password_confirmation.required' => 'validation.required',
            'terms.accepted' => 'validation.accept'
        ];
 
        $validator = Validator::make($data, [
            'firstname' => ['required', 'min:2', 'string', 'max:100'],
            'lastname' => ['required', 'min:2', 'string', 'max:100'],
            'email' => ['required', 'email', 'string', 'max:255', 'unique:users'],
            'password' => [
                'required_with:password_confirmation',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
                'same:password_confirmation'
            ],
            'password_confirmation' => ['required'],
            'terms' => ['accepted'],
        ], $customMessages);

        return $validator;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'firstname' => $data['firstname'],
            'lastname'  => $data['lastname'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
        ])->sendEmailVerificationNotification();
    }
    
    /**
     * Method register
     *
     * @param Request $request [explicite description]
     * @return void
     */
    public function register(Request $request) 
    {        
        if ($this->validator($request->all())->fails()) {
            $errors = $this->validator($request->all())->errors()->getMessages();            
            $clientErrors = array();
            foreach ($errors as $key => $value) {
                $clientErrors[$key] = $value[0];
            }
            $response = array(
                'status'        => 'error',
                'response_code' => 201,
                'errors'        => $clientErrors,
                'success'       => false,
            );            
        } else {
            $this->validator($request->all())->validate();
            $user = $this->create($request->all());
            $response = array(
                'status'        => 'success',
                'response_code' => 200,
                'success'       => true,
            );
        }

        return json_encode($response);
    }
}
