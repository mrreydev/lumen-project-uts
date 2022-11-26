<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Register Function
     */
    public function register(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $input = $request->all();
    
            $validationRules = [
                'first_name' => 'required|string|max:200',
                'last_name' => 'nullable|string|max:200',
                'address' => 'required|string',
                'gender' => 'required|in:male,female',
                'birth_date' => 'required|date',
                'phone' => 'required|min:7|max:15',
                'email' => 'required|unique:users|max:200',
                'password' => 'required|min:6'
            ];
    
            $validator = Validator::make($input, $validationRules);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
            }
            
            $user = new User();
            
            $user->first_name = $input['first_name'];
            $user->last_name = $input['last_name'] || null;
            $user->address = $input['address'];
            $user->gender = $input['gender'];
            $user->birth_date = $input['birth_date'];
            $user->phone = $input['phone'];
            $user->email = $input['email'];
            $plainPassword = $input['password'];
            $user->password = app('hash')->make($plainPassword);
    
            if ($user->save()) {

                if ($acceptHeader === 'application/json') {
                    $response = [
                        'message' => 'Register Success',
                        'status_code' => Response::HTTP_CREATED,
                        'data' => $user
                    ];
        
                    return response()->json($response, Response::HTTP_CREATED);
                } else {
                    $xml = new \SimpleXMLElement('<user/>');

                    $xml->addChild('id', $user->id);
                    $xml->addChild('first_name', $user->first_name);    
                    $xml->addChild('last_name', $user->last_name);    
                    $xml->addChild('address', $user->address);  
                    $xml->addChild('gender', $user->gender);  
                    $xml->addChild('birth_date', $user->birth_date);
                    $xml->addChild('phone', $user->phone);
                    $xml->addChild('email', $user->email);

                    return $xml->asXML();
                }
            }
    
            $response = [
                'message' => 'Register Failed',
                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
            ];
    
            return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        } else {
            $response = [
                'message' => 'Not Acceptable',
                'status_code' => Response::HTTP_NOT_ACCEPTABLE
            ];
    
            return response()->json($response, Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    /**
     * Login Function
     */
    public function login(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $input = $request->all();
    
            $validationRules = [
                'email' => 'required|string',
                'password' => 'required|string'
            ];
    
            $validator = Validator::make($input, $validationRules);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
            }
    
            $credentials = $request->only(['email', 'password']);
    
            if (!$token = Auth::attempt($credentials)) {
                $response = [
                    'message' => 'Unauthorized',
                    'status_code' => Response::HTTP_UNAUTHORIZED
                ];
    
                return response()->json($response, Response::HTTP_UNAUTHORIZED);
            }
    
            $user = Auth::user();
            $tokenArr = [
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => Auth::factory()->getTTL() * 60
            ];
    
            $arrUser = json_decode(json_encode($user), true);
            $data = array_merge($arrUser, $tokenArr);
            
            if ($acceptHeader === 'application/json') {
                $response = [
                    'message' => 'Login Success',
                    'status_code' => Response::HTTP_ACCEPTED,
                    'data' => $data
                ];
        
                return response()->json($response, Response::HTTP_ACCEPTED);
            } else {
                $xml = new \SimpleXMLElement('<user-login/>');

                $xml->addChild('id', $user->id);
                $xml->addChild('first_name', $user->first_name);    
                $xml->addChild('last_name', $user->last_name);    
                $xml->addChild('address', $user->address);  
                $xml->addChild('gender', $user->gender);  
                $xml->addChild('birth_date', $user->birth_date);
                $xml->addChild('phone', $user->phone);
                $xml->addChild('email', $user->email);
                $xml->addChild('token', $tokenArr['token']);
                $xml->addChild('token_type', $tokenArr['token_type']);
                $xml->addChild('expires_in', $tokenArr['expires_in']);

                return $xml->asXML();
            }
            
        } else {
            $response = [
                'message' => 'Not Acceptable',
                'status_code' => Response::HTTP_NOT_ACCEPTABLE
            ];
    
            return response()->json($response, Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    /**
     * Logout Function
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {

            if ($acceptHeader === 'application/json') {
                $response = [
                    'message' => 'Logout Success',
                    'status_code' => Response::HTTP_OK
                ];
        
                return response()->json($response, Response::HTTP_OK);
            } else {
                $xml = new \SimpleXMLElement('<user-logout/>');

                $xml->addChild('message', 'Logout Success');
                $xml->addChild('status_code', Response::HTTP_OK);    
            }
        } else {
            $response = [
                'message' => 'Not Acceptable',
                'status_code' => Response::HTTP_NOT_ACCEPTABLE
            ];
    
            return response()->json($response, Response::HTTP_NOT_ACCEPTABLE);
        }
    }
}
