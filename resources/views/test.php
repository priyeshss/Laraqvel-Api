//Laravel Documentation 

composer create-project laravel/laravel api-project

composer require laravel/sanctum

php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

php artisan migrate

//Add Sanctum Trait in User Model Open: app/Models/User.php
//Add:
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
}

//Create Auth Controller Run:
php artisan make:controller Api/AuthController

//Register API
//Inside AuthController

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

public function register(Request $request)
{
    $user = User::create([
        'name'=>$request->name,
        'email'=>$request->email,
        'password'=>Hash::make($request->password)
    ]);

    return response()->json([
        "message"=>"User Registered"
    ]);
}

//Login API
use Illuminate\Support\Facades\Auth;

public function login(Request $request)
{
    if(!Auth::attempt($request->only('email','password'))){
        return response()->json([
            "message"=>"Invalid credentials"
        ],401);
    }

    $user = Auth::user();

    $token = $user->createToken('apiToken')->plainTextToken;

    return response()->json([
        "token"=>$token
    ]);
}

//Now login will generate Sanctum token.

//API Routes 
//open routes/api.php 

use App\Http\Controllers\Api\AuthController;

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

// Protect Routes Using Sanctum
//Protected routes:

Route::middleware('auth:sanctum')->group(function(){
});

//Example:

Route::middleware('auth:sanctum')->group(function(){
Route::post('/category',[CategoryController::class,'store']);
Route::post('/product',[ProductController::class,'store']);
});

//Meaning:
//User must send token to access these routes
// Test Register API
//POST

http://127.0.0.1:8000/api/register

Body (JSON):

{
"name":"Priyesh",
"email":"priyesh@test.com",
"password":"123456"
}
🔟 Test Login API

POST

/api/login

Response:

{
"token":"1|asdjhsadjsahd..."
}

//Copy this token.
// Use Token in Postman
//Header:
//Authorization: Bearer TOKEN
//Now protected APIs will work.


