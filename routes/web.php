<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
// ! Dependency injection
use Illuminate\http\Request;
// ! Route model binding
use App\Models\User;
use App\Models\Post;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ! route  automatically loaded by RouteServiceProvider
// ! route method

Route::get('/greeting', function () {
    return 'hello';
});
// * nav to Controller
Route::get('toController', [Controller::class, 'index']);

// * available Route Methods[get, post, put, patch, delete, options]

// * response to mutliple HTTP verbs

Route::match(['get', 'post'], '/match', function () {
    //logic
});
Route::any('/any', function(Request $request) {
 // logic
});

// * CSRF protection verbs[Post, put, patch ,delete]

// * redirecte return 302 default
Route::redirect('/here', '/there', 301); //default 302
Route::permanentRedirect('/here', '/there'); //get 301 code

// * view Route
Route::view('/view1', 'view1', ['parameter1' => '1']); // third argument is option

// !Required Parameters
Route::get('/user/{id}', function($id) {
    return 'user'.$id;
}); // will inject in callback function or Controllers

// * optional parameters ?
Route::get('/user{name?}', function($name = 'John') {
    return $name;
});

// * regular expression constraints
Route::get('/user{name?}', function($name = 'John') {
    return $name;
})->where('name', '[A-Za-z]+');

// * multiple
Route::get('/user{name?}/{id}', function($name = 'John') {
    return $name;
})->where(['name'=>'[A-Za-z]+', 'id'=>'[0-9]+']);

// * multiple convinence
Route::get('/user{name?}/{id}', function($name = 'John') {
    return $name;
})->whereAlpha('name')->whereNumber('id'); // does not match 404 will return

// * global constraints
// define at RouteServiceProvider

// * Encode forward slash
Route::get('/search{search}', function($name = 'John') {
    //logic
})->where('search', '.*'); // allow forward slash

// ! Named Routes

Route::get('/greeting1', function () {

})->name('greeting1'); //name should be allways unique

// * generate url
$url = route('greeting1'); //url to the named route
$url = route('greeting1',['id'=>'1']); //if named route has parameters
// return redirect()->route('greeting1');
// check if request route by named route will see middleware


// ! group

// * Middleware

Route::middleware(['first', 'second'])->group(function() {
    Route::get('/group',function() {
        //logic
    });
    //other Routes use the first, second middleware
});

// * Controller

Route::controller(Controller::class)->group(function() {
    Route::get('orders/{id}', 'show');
    Route::post('orders/{id}', 'store');
});

// * subdomain

Route::domain('{account}.example.com')->group(function() {
    Route::get('orders/{id}', function($account, $id) {
        // Routes
    });
});

// * prefix
Route::prefix('admin')->group(function() {
    //ROutes
});

// * Route name.prefix

Route::name('admin.')->group(function(){
    Route::get('/ther',function() {

    })->name('user'); //name is 'admin.user'
});

// ! Route model binding


// * implicit Binding

Route::get('users/{user}', function (User $user) {
    return $user->email; //User->email
    // automaticly math {id} in Model $user when user matches variable name $user, if not found will return 404
})->withTrashed();// withTrashed soft deleted model

Route::get('user/{user}',[Controller::class, 'show']);
// public function show(User $user) { //logic }

// * Customizing the key
Route::get('user/{user:name}', function(User $user){
    return $user;
}); //if alaways use columns other than id , overwrite getRouteKeyName in Eloquent model

// * scoping by custom key
Route::get('/users/{user}/posts/{post:name}', function(User $user, Post $post) {
    return $post;
}); //will automaticlly ....

// * scoping by scopeBindings()

Route::get('/users/{user}/posts/{post}', function(User $user, Post $post) {
    return $post;
})->scopeBindings(); //

Route::scopeBindings()->group(function () {
    Route::get('/users/{user}/posts/{post}', function(User $user, Post $post) {
        return $post;
    }); //scopeBinding
    // other Routes
});

// * customizing missing model behavior
// ->missing(function() { // missing logic});

// * explicit binding

// define in RouteServiceProvider boot() { }
Route::model('user', User::class);

Route::model('users{User}', function (User $user) {
    // Route
}); //default is mathing id

// * Customizing the resolution rule

// define in RouteServiceProvider boot() { }
Route::bind('user', function($value) { //$value is the value of URL segment
    //return the instance class that should inject in route
    return User::where('name', $value)->firstOrFail();
});

// overwrite resolveRouteBinding() function in on EloquentModel

public function resolveRouteBinding($value, $fields = null) { //$value is the value of URL segment
    return $this->where('name', $value)->firstOrFial();
}

// ? implict binding scope

// ! fallback route
Route::fallback(function() {
//
});


// ! Rate limiting