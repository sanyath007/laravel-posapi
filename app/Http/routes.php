<?php

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\User;
use App\Product;
use App\Category;
use App\Reservation;
use App\Vehicle;
use App\Driver;
use App\Drape;
use App\DrapeType;
use App\Set;
use App\Bring;
use App\BringDetail;
use App\Department;
use App\Staff;

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

/** ============= CORS ============= */
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Accept, Authorization, Content-Type, Origin, X-Requested-With, X-Auth-Token, X-Xsrf-Token');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 3600');
header('Content-Type: application/json;charset=utf-8');
/** ============= CORS ============= */
/** ============= HOME PAGE =============
 * we dont need to use Laravel Blade
 * we will return a PHP file that will hold all of our Angular content
 * see the "Where to Place Angular Files" below to see ideas on how to structure your app return.
 */
Route::get('/', function () {
    return view('home');
});

/**
 * ============= web group =============
 */
Route::group(['middleware' => 'web'], function() {
    /**
     * ============= Authentication =============
     */
    Route::get('/auth/login', function () {
        return view('auth.login');
    });

    Route::get('/auth/register', function () {
        return view('auth.register');
    });

    /**
     * ============= pos =============
     */
    Route::get('/pos', function () {
        return view('sales.pos');
    });

    Route::get('/po', function () {
        return view('purchases.po');
    });

    Route::get('/products', function () {
        return view('products._list', [
            'products' => Product::all()
        ]);
    });

    Route::get('/categories', function () {
        return view('categories._list', [
            'categories' => Category::all()
        ]);
    });
});

/**
 * ============= api group =============
 */
Route::group(['prefix' => 'api'], function() {

    Route::post('/auth', 'Api\ApiController@auth');

    Route::post('/signup', 'Api\ApiController@signup');

    Route::get('/users', 'Api\ApiController@index');

    Route::get('/useronline/{id}', function($id) {
        return User::with('department')->where(['person_id' => $id])->first();
    });

    Route::get('/user/{email}', 'Api\ApiController@online');

    Route::get('/departments', function() {
        return Department::all();
    });

    Route::get('/products/{pagesize}/{offset}', function($pagesize, $offset) {
        if ($pagesize > 0 || $offset > 0) {
            return Product::limit($pagesize)->offset($offset)->get();
        } else {
            return Product::all();
        }
    });

    Route::get('/product/{id}', function($id) {
        return Product::find($id);
    });

    Route::post('/product', function(Request $req) {
        $p = new Product();
        $p->name_th = $req['name_th'];
        $p->description = $req['description'];
        $p->cost = $req['cost'];
        $p->retail_price = $req['retail_price'];
        $p->stock_amount = $req['stock_amount'];
        if ($p->save()) {
            return [
                'msg' => true,
                'status' => '200',
                'text' => 'Add product successfully !!!',
                'data' => Product::all()
            ];
        } else {
            return [
                'msg' => false,
                'status' => '500',
                'text' => 'Can not add product !!!',
                'data' => ''
            ];
        }
    });

    /** Vehicle Reservation System */
    Route::get('/vehicles', function() {
        return Vehicle::all();
    });

    Route::get('/drivers', function() {
        return Driver::all();
    });

    Route::get('/reservations', function() {
        return Reservation::all();
    });

    Route::get('/reservation/{id}', function($id) {
        return Reservation::where(['reserve_id' => $id])->get();
    });

    /** Laundry System */
    Route::get('/drapes', function() {
        if (empty(Input::get('name'))) {
            return Drape::paginate(10);
        } else {
            return Drape::where('name', 'like', '%' . Input::get('name') . '%')->paginate(10);
        }
    });

    Route::get('/drape/{id}', function($id) {
        return Drape::find($id);
    });

    Route::post('/drape', function(Request $request) {
        $drape = new Drape();
        $drape->name = $request['name'];
        $drape->drape_type = $request['drapeType'];
        $drape->size = $request['size'];
        $drape->cost = $request['cost'];
        $drape->amount = $request['amount'];
        $drape->min_amt = $request['minAmt'];
        $drape->stock_amt = $request['stockAmt'];
        $drape->description = $request['description'];

        if ($drape->save()) {
            return [
                'msg' => true,
                'status' => '200',
                'text' => 'Add new drape successfully !!!',
                'data' => Drape::all()
            ];
        } else {
            return [
                'msg' => false,
                'status' => '500',
                'text' => 'Can not add new drape !!!',
                'data' => ''
            ];
        }
    });

    Route::put('/drape/{id}', function(Request $request, $id) {
        $drape = Drape::find($id);
        $drape->name = $request['name'];
        $drape->drape_type = $request['drape_type'];
        $drape->size = $request['size'];
        $drape->cost = $request['cost'];
        $drape->amount = $request['amount'];
        $drape->min_amt = $request['min_amt'];
        $drape->stock_amt = $request['stock_amt'];
        $drape->description = $request['description'];
        if ($drape->save()) {
            return [
                'msg' => true,
                'status' => '200',
                'text' => 'Update successfully !!!',
                'data' => Product::all()
            ];
        } else {
            return [
                'msg' => false,
                'status' => '500',
                'text' => 'Can not update !!!',
                'data' => ''
            ];
        }
    });

    Route::delete('/drape/{id}', function($id) {
        $drape = Drape::find($id);

        if ($drape && $drape->delete()) {
            return [
                'msg' => true,
                'status' => '200',
                'text' => 'Successfully !!!',
                'data' => Drape::all()
            ];
        } else {
            return [
                'msg' => false,
                'status' => '500',
                'text' => 'Can not delete drape !!!',
                'data' => ''
            ];
        }
    });

    Route::get('/drapetypes', function() {
        return DrapeType::all();
    });

    Route::get('/sets', function() {
        if (empty(Input::get('name'))) {
            return Set::paginate(10);
        } else {
            return Set::where('set_name', 'like', '%' . Input::get('name') . '%')->paginate(10);
        }
    });

    Route::get('/set/{id}', function($id) {
        return Set::where(['set_id' => $id])->get();
    });

    Route::post('/set', function(Request $request) {
        $set = new Set();
        $set->set_name = $request['name'];
        $set->set_desc = $request['description'];
        if ($set->save()) {
            return [
                'msg' => true,
                'status' => '200',
                'text' => 'Add new drape successfully !!!',
                'data' => Set::all()
            ];
        } else {
            return [
                'msg' => false,
                'status' => '500',
                'text' => 'Can not add new drape !!!',
                'data' => ''
            ];
        }
    });

    Route::put('/set/{id}', function(Request $request, $id) {
        $set = Set::find($id);
        $set->set_name = $request['name'];
        $set->set_desc = $request['description'];
        if ($set->save()) {
            return [
                'msg' => true,
                'status' => '200',
                'text' => 'Update successfully !!!',
                'data' => Set::all()
            ];
        } else {
            return [
                'msg' => false,
                'status' => '500',
                'text' => 'Can not update !!!',
                'data' => ''
            ];
        }
    });

    Route::delete('/set/{id}', function($id) {
        $set = Set::find($id);

        if ($set && $set->delete()) {
            return [
                'msg' => true,
                'status' => '200',
                'text' => 'Successfully !!!',
                'data' => Set::all()
            ];
        } else {
            return [
                'msg' => false,
                'status' => '500',
                'text' => 'Can not delete drape !!!',
                'data' => ''
            ];
        }
    });

    Route::get('/brings', function() {
        // if(empty(Input::get('name'))) {
        return Bring::with('department')->with('bringDetail')->paginate(10);
        // } else {
        //   return Bring::where('set_name', 'like', '%' . Input::get('name') . '%')->paginate(10);
        // }
    });

    Route::post('/bring', function(Request $request) {
        $bring = new Bring();
        $bring->bring_date = $request['bring']['bring_date'];
        $bring->user_id = $request['bring']['user_id'];
        $bring->depart_id = $request['bring']['depart_id'];
        $bring->bring_status = $request['bring']['bring_status'];

        if ($bring->save()) {
            for ($i = 0; $i < count($request['details']); $i++) {
                $detail = new BringDetail();
                $detail->bring_id = $bring->id;
                $detail->item_id = $request['details'][$i]['item_id'];
                $detail->item_type = $request['details'][$i]['item_type'];
                $detail->bring_amt = $request['details'][$i]['bring_amt'];
                $detail->save();
            }

            return [
                'msg' => true,
                'status' => '200',
                'text' => 'Add new drape successfully !!!',
                'data' => Set::all()
            ];
        } else {
            return [
                'msg' => false,
                'status' => '500',
                'text' => 'Can not add new drape !!!',
                'data' => ''
            ];
        }
    });

    Route::get('/staffs', function() {
//        if(empty(Input::get('name'))) {
        return Staff::paginate(10);
//        } else {
//            return Bring::where('set_name', 'like', '%' . Input::get('name') . '%')->paginate(10);
//        }
    });
});

/** ============= CATCH ALL ROUTE =============
 * all routes that are not home or api will be redirected to the frontend
 * this allows angular to route them
 */
// App::missing(function($exception) {
//   return View::make('home');
// });
