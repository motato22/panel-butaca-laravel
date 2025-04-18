<?php

namespace App\Http\Controllers\Api\Mobile\Firebase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use \stdClass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Kreait\Firebase;

class FirebaseController extends Controller {
    protected $firebase;

    public function __construct(Firebase $firebase) {
        $this->firebase = $firebase;
    }

    public function deleteUser($uid) {
        try {
            $this->firebase->getAuth()->deleteUser($uid);
            // Manejo de éxito
        } catch (\Throwable $e) {
            // Manejo de errores
        }
    }
}