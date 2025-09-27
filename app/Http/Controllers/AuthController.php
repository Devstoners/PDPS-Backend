<?php

namespace App\Http\Controllers;
use App\Repositories\Repository;
use Illuminate\Http\Request;
use App\Models\User;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{

    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * @OA\Post(
     *     path="/register",
     *     tags={"Authentication"},
     *     summary="Register a new user",
     *     description="Register a new user in the system",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"requesttype"},
     *             @OA\Property(property="requesttype", type="integer", example=1, description="Request type: 1 for Admin"),
     *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="status", type="string", example="active"),
     *             @OA\Property(property="type", type="string", example="admin"),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="nic", type="string", example="123456789V"),
     *             @OA\Property(property="mobileNo", type="string", example="+94771234567")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function register(Request $request){

        define("Admin", 1);
        define("GS", 2);

        $fields = $request->validate([
            'requesttype' => 'required'
        ]);

        if($fields['requesttype'] == Admin){

            $fields = $request->validate([
                'email' => 'required|string|unique:users,email',
                'password' => 'string|confirmed',
                'status' => 'required',
                'type' => 'required',
                'name' => 'required',
                'nic' => 'required',
                'mobileNo' => 'required'
            ]);
            $response = $this->repository->registerNew($fields);
          //  $response = $this->teacherRepository->updatePassword($id, $fields, $request);
            return response($response, 201);

        }
    }
    /**
     * @OA\Post(
     *     path="/login",
     *     tags={"Authentication"},
     *     summary="User login",
     *     description="Authenticate user and return access token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="token", type="string", example="1|abcdef123456"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     )
     * )
     */
    public function login(Request $request){
      //  return $request;
        $response = $this->repository->login($request);
        return response($response, 201);
    }
}
