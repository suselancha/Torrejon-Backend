<?php

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Configuration\BankController;
use App\Http\Controllers\Configuration\ClientSegmentController;
use App\Http\Controllers\Configuration\EmployeeFunctionController;
use App\Http\Controllers\Configuration\RegionController;
use App\Http\Controllers\Configuration\ZonaController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Provider\ProviderController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\Stock\StockController;
use App\Http\Controllers\Subcategory\SubcategoryController;
use App\Http\Controllers\Sucursale\SucursaleController;
use App\Http\Controllers\Unit\UnitController;
use App\Http\Controllers\UserAccessController;
use App\Http\Controllers\Warehouse\WarehouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    //'middleware' => 'auth:api',
    'prefix' => 'auth'
 
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->name('me');
});

Route::group([
    'middleware' => 'auth:api'    
], function ($router) {
    Route::resource('roles', RolePermissionController::class);

    Route::get('users/config', [UserAccessController::class, 'config']);
    Route::resource('users', UserAccessController::class);

    Route::resource('employee_functions', EmployeeFunctionController::class);
    Route::resource('client_segments', ClientSegmentController::class);
    Route::get('zonas/config', [ZonaController::class, 'config']);
    Route::resource('zonas', ZonaController::class);

    Route::post('clients/index', [ClientController::class, 'index']);
    Route::post('clients/import', [ClientController::class, 'import_clients']);
    Route::get('clients/config', [ClientController::class, 'config']);
    Route::resource('clients', ClientController::class);

    Route::post('sucursales/index', [SucursaleController::class, 'index']);
    Route::get('sucursales/config', [SucursaleController::class, 'config']);
    Route::get('sucursales/search-clients', [SucursaleController::class, 'search_clients']);
    Route::get('sucursales/search-zonas', [SucursaleController::class, 'search_zonas']);
    Route::resource('sucursales', SucursaleController::class);

    Route::resource('providers', ProviderController::class);
    
    Route::post('accounts/filter', [AccountController::class, 'get_accounts']);
    Route::resource('accounts', AccountController::class);
    Route::resource('banks', BankController::class);

    Route::post('subcategories/filter', [SubcategoryController::class, 'get_subcategories']);
    Route::resource('subcategories', SubcategoryController::class);
    Route::resource('categories', CategoryController::class);
    Route::get('products/config', [ProductController::class, 'config']);
    Route::resource('products', ProductController::class);
    Route::get('regions/config', [RegionController::class, 'config']);
    Route::resource('regions', RegionController::class);
    Route::resource('warehouses', WarehouseController::class);
    Route::apiResource('units', UnitController::class);

    Route::get('stocks', [StockController::class, 'index']);
    Route::get('stocks/persons', [StockController::class, 'get_persons']);
    Route::get('stocks/details/{id}', [StockController::class, 'get_details']);
    Route::post('stocks/increase', [StockController::class, 'increase']);
    Route::post('stocks/decrease', [StockController::class, 'decrease']);
});
