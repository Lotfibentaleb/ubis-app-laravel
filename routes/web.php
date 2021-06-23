<?php
use App\Http\Controllers\CleanupController;

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

/*
Route::get('/', function () {
    return view('welcome');
});
*/
Route::get('/', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

/*
 * Users management
 * */
Route::prefix('/users')->group(function () {
    Route::get('', 'ClientsController@index');
    Route::get('{userId}', 'ClientsController@show');
    Route::post('create', 'ClientsController@create');
    Route::patch('{userId}', 'ClientsController@update');
    Route::post('destroy', 'ClientsController@destroyMass');
    Route::delete('{userId}/destroy', 'ClientsController@destroy');
});

/*
 * Current user
 * */
Route::prefix('/user')->group(function () {
    Route::get('', 'CurrentUserController@show');
    Route::patch('', 'CurrentUserController@update');
    Route::patch('/password', 'CurrentUserController@updatePassword');
});

/*
 * File upload (e.g. avatar)
 * */
Route::post('/files/store', 'FilesController@store');

Route::get('/test/{id}', test::class);

/*
 * Device registration management
 * */

Route::prefix('/registration')->group(function () {
    Route::get('', 'RegistrationController@index');
    Route::get('/articles', 'RegistrationController@articles');         // list several articles by name/art.nr.
    Route::get('/articles/{id}', 'RegistrationController@article');     // get back details per article
    Route::get('/product/{id}/articleNr/{articleNr?}', 'RegistrationController@showProduct');   // get back details per product
    Route::post('/product/{id}/articleNr/{articleNr?}', 'RegistrationController@createProduct');  // get back details per article
    Route::delete('/product/{id}/components/{componentId}', 'RegistrationController@deleteComponent');

});

Route::prefix('/productsearch')->group(function () {
    Route::get('', 'ProductSearchController@index');
    Route::get('/product/{pid}/productiondatadaisy/{did}', 'ProductSearchController@productiondatadaisy');
    Route::get('/product/{pid}/productiondatabacklight/{did}', 'ProductSearchController@productiondatabacklight');

});

Route::prefix('/productlist')->group(function () {
    Route::get('', 'ProductsListController@index');
    Route::get('/excel', 'ProductsListController@excel');
    Route::get('/enhancedExcel', 'ProductsListController@enhancedExcel');
    Route::get('/fullExcel', 'ProductsListController@fullExcel');
/*    Route::get('/articles', 'RegistrationController@articles');         // list several articles by name/art.nr.
    Route::get('/articles/{id}', 'RegistrationController@article');     // get back details per article
    Route::get('/product/{id}/articleNr/{articleNr?}', 'RegistrationController@showProduct');   // get back details per product
    Route::post('/product/{id}/articleNr/{articleNr?}', 'RegistrationController@createProduct');  // get back details per article
*/
    Route::put('/product/{id}', 'ProductsListController@updateProduct');
    Route::delete('/product/{id}', 'ProductsListController@destroy');

    Route::post('/bulkregister', 'ProductsListController@bulkRegister');
    Route::post('/bulkreverify', 'ProductsListController@bulkReVerify');
});

Route::prefix('/dashboard')->group(function () {
    Route::get('', 'DashboardController@index');
});


Route::get('/dashboardInfo', 'DashboardController@dashboardInfo');
Route::get('/homeDashboard', 'DashboardController@homeDashboard');

Route::get('/cleanup', [App\Http\Controllers\CleanupController::class, 'index']);

Route::get('production_flow/history', 'ProductionFlowController@indexHistory');
Route::apiResource('production_flow', 'ProductionFlowController');

Route::get('device_records', 'MeasurementController@index');
Route::get('device_records/{id}', 'MeasurementController@getMeasurement');

Route::get('production_section/history', 'ProductionSectionController@indexHistory');
Route::get('production_section/form_support', 'ProductionSectionController@showSupportValues');
Route::apiResource('production_section', 'ProductionSectionController');

Route::prefix('/tokens')->group(function () {
    Route::get('/createDeveloperToken', 'TokenController@createDeveloperAccessToken');
    Route::get('/resetDeveloperToken', 'TokenController@resetDeveloperAccessToken');
    Route::get('/getDeveloperToken', 'TokenController@getDeveloperAccessToken');
    Route::get('/createApiToken', 'TokenController@createApiAccessToken');
    Route::get('/getDeveloperTokens', 'TokenController@getDeveloperAccessTokens');
    Route::get('/getApiTokens', 'TokenController@getApiAccessTokens');
});