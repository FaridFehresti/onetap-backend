<?php

use App\Http\Controllers\Api\ActionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\BrandlogoController;
use App\Http\Controllers\Api\CardController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Api\QrcodeController;
use App\Http\Controllers\Api\StripeController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\PlanFeatureController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FooterExploreController;
use App\Http\Controllers\FooterMediaController;
use App\Http\Controllers\FooterResourceController;
use App\Http\Controllers\GoGreenController;
use App\Http\Controllers\HeroController;
use App\Http\Controllers\MyCardController;
use App\Http\Controllers\MyCardLinkController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SecondFeatureController;
use App\Http\Controllers\SecretTokenController;
use App\Http\Controllers\StepController;
use App\Http\Controllers\Web\Backend\CMSController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/login_with_secret_token', [AuthController::class, 'loginWithSecretToken']);


// Cms Route
Route::post('/cms', [CMSController::class, 'index']);
Route::get('/cms/feature', [CMSController::class, 'feature']);
Route::get('cms/review', [CMSController::class, 'review']);
Route::get('cms/brand', [CMSController::class, 'brand']);
//Route::get('faq/all',[FaqController::class,'index']);
Route::post('contact_us',[ContactController::class,'store']);




Route::post('forget/password', [AuthController::class, 'forgetPassword']);
Route::post('/verify/otp', [AuthController::class, 'checkotp']);
Route::post('/password/update', [AuthController::class, 'passwordUpdate']);


// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/check', [AuthController::class, 'check']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/profile/update', [AuthController::class, 'profileUpdate']);
    Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);
    Route::post('/action/store', [ActionController::class, 'store']);
    Route::get('/action/show/{id}', [ActionController::class, 'show']);
    Route::post('/action/status', [ActionController::class, 'status']);
    Route::post('/action/delete', [ActionController::class, 'delete']);
    Route::post('/action/update', [ActionController::class, 'update']);
    Route::post('/action/edit', [ActionController::class, 'edit']);
    Route::get('/taps/ditails/{id}', [QrcodeController::class, 'tapsData']);
    Route::get('/contact/get/{id}', [ContactController::class, 'contactShow']);



    Route::get('/user/card', [UserController::class, 'index']);
    Route::post('/user/card/status', [UserController::class, 'status']);



    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/create', [CartController::class, 'store']);
        Route::post('/quantity', [CartController::class, 'quantity']);
        Route::post('/delete', [CartController::class, 'delete']);
    });

    Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('checkout.create');
    Route::post('/subscription/{type}', [SubscriptionController::class, 'createSubscriptionSession'])->name('checkout.subscriptions');


    Route::prefix('tokens')->group(function () {
        Route::post('/generate', [SecretTokenController::class, 'generateSecretToken']);
        Route::get('/', [SecretTokenController::class, 'getSecretToken']);
        Route::delete('/', [SecretTokenController::class, 'deleteSecretToken']);
        Route::put('/regenerate', [SecretTokenController::class, 'regenerateSecretToken']);
    });

    Route::prefix('heroes')->group(function () {
        Route::get('/', [HeroController::class, 'index'])->name('heroes.index');
        Route::post('/', [HeroController::class, 'store'])->name('heroes.store');
        Route::get('/{hero}', [HeroController::class, 'show'])->name('heroes.show');
        Route::post('/{hero}', [HeroController::class, 'update'])->name('heroes.update');
        Route::delete('/{hero}', [HeroController::class, 'destroy'])->name('heroes.destroy');

    });

    Route::prefix('second_features')->group(function () {
        Route::get('/', [SecondFeatureController::class, 'index'])->name('second_features.index');
        Route::post('/', [SecondFeatureController::class, 'store'])->name('second_features.store');
        Route::get('/{secondFeature}', [SecondFeatureController::class, 'show'])->name('second_features.show');
        Route::post('/{secondFeature}', [SecondFeatureController::class, 'update'])->name('second_features.update');
        Route::delete('/{secondFeature}', [SecondFeatureController::class, 'destroy'])->name('second_features.destroy');
    });

    Route::prefix('colors')->group(function () {
        Route::get('/', [ColorController::class, 'index'])->name('colors.index');
        Route::post('/', [ColorController::class, 'store'])->name('colors.store');
        Route::get('/{color}', [ColorController::class, 'show'])->name('colors.show');
        Route::post('/', [ColorController::class, 'update'])->name('colors.update');
        Route::delete('/{color}', [ColorController::class, 'destroy'])->name('colors.destroy');
    });



    Route::prefix('brand_logos')->name('brand_logos.')->group(function () {
        Route::get('/', [BrandlogoController::class, 'index'])->name('index');
        Route::post('/', [BrandlogoController::class, 'store'])->name('store');
        Route::get('/{brandlogo}', [BrandlogoController::class, 'show'])->name('show');
        Route::post('/{brandlogo}', [BrandlogoController::class, 'update'])->name('update');
        Route::delete('/{brandlogo}', [BrandlogoController::class, 'destroy'])->name('destroy');
        Route::post('/status/{brandlogo}', [BrandlogoController::class, 'status'])->name('status');
    });


    Route::prefix('plans')->name('plans.')->group(function () {
        Route::get('/', [PlanController::class, 'index'])->name('index');
        Route::post('/', [PlanController::class, 'store'])->name('store');
        Route::get('/{plan}', [PlanController::class, 'show'])->name('show');
        Route::post('/{plan}', [PlanController::class, 'update'])->name('update');
        Route::delete('/{plan}', [PlanController::class, 'destroy'])->name('destroy');
    });


    Route::prefix('plan_features')->name('plan_features.')->group(function () {
        Route::get('/', [PlanFeatureController::class, 'index'])->name('index');
        Route::post('/', [PlanFeatureController::class, 'store'])->name('store');
        Route::get('/{planFeature}', [PlanFeatureController::class, 'show'])->name('show');
        Route::post('/{planFeature}', [PlanFeatureController::class, 'update'])->name('update');
        Route::delete('/{planFeature}', [PlanFeatureController::class, 'destroy'])->name('destroy');
    });



    Route::prefix('go_green')->name('go_green.')->group(function () {
        Route::get('/', [GoGreenController::class, 'index'])->name('index');
        Route::post('/', [GoGreenController::class, 'store'])->name('store');
        Route::get('/{goGreen}', [GoGreenController::class, 'show'])->name('show');
        Route::post('/{goGreen}', [GoGreenController::class, 'update'])->name('update');
        Route::delete('/{goGreen}', [GoGreenController::class, 'destroy'])->name('destroy');
    });


    Route::prefix('steps')->name('steps.')->group(function () {
        Route::get('/', [StepController::class, 'index'])->name('index');
        Route::post('/', [StepController::class, 'store'])->name('store');
        Route::get('/{step}', [StepController::class, 'show'])->name('show');
        Route::post('/{step}', [StepController::class, 'update'])->name('update');
        Route::delete('/{step}', [StepController::class, 'destroy'])->name('destroy');
    });



    Route::prefix('footer_explores')->name('footer_explores.')->group(function () {
        Route::get('/', [FooterExploreController::class, 'index'])->name('index');
        Route::post('/', [FooterExploreController::class, 'store'])->name('store');
        Route::get('/{footerExplore}', [FooterExploreController::class, 'show'])->name('show');
        Route::post('/{footerExplore}', [FooterExploreController::class, 'update'])->name('update');
        Route::delete('/{footerExplore}', [FooterExploreController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('footer_resources')->name('footer_resources.')->group(function () {
        Route::get('/', [FooterResourceController::class, 'index'])->name('index');
        Route::post('/', [FooterResourceController::class, 'store'])->name('store');
        Route::get('/{footerResource}', [FooterResourceController::class, 'show'])->name('show');
        Route::post('/{footerResource}', [FooterResourceController::class, 'update'])->name('update');
        Route::delete('/{footerResource}', [FooterResourceController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('footer_media')->name('footer_media.')->group(function () {
        Route::get('/', [FooterMediaController::class, 'index'])->name('index');
        Route::post('/', [FooterMediaController::class, 'store'])->name('store');
        Route::get('/{footerMedia}', [FooterMediaController::class, 'show'])->name('show');
        Route::post('/{footerMedia}', [FooterMediaController::class, 'update'])->name('update');
        Route::delete('/{footerMedia}', [FooterMediaController::class, 'destroy'])->name('destroy');
    });


    Route::prefix('faqs')->name('faqs.')->group(function () {
        Route::get('/', [FaqController::class, 'index'])->name('index');
        Route::get('/all', [FaqController::class, 'activefaq'])->name('listAll');
        Route::post('/', [FaqController::class, 'store'])->name('store');
        Route::get('/{faq}', [FaqController::class, 'show'])->name('show');
        Route::post('/{faq}', [FaqController::class, 'update'])->name('update');
        Route::delete('/{faq}', [FaqController::class, 'destroy'])->name('destroy');
    });


    Route::prefix('contact_us')->name('contact_us.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::post('/', [ContactController::class, 'store'])->name('store');
        Route::get('/{contact}', [ContactController::class, 'show'])->name('show');
        Route::post('/{contact}', [ContactController::class, 'update'])->name('update');
        Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('destroy');
    });

    Route::post('/send_email', [EmailController::class, 'store'])->name('sendEmail');


    Route::prefix('my_cards')->name('my_cards.')->group(function () {
        Route::get('/', [MyCardController::class, 'index'])->name('index');
        Route::post('/', [MyCardController::class, 'store'])->name('store');
        Route::get('/{myCard}', [MyCardController::class, 'show'])->name('show');
        Route::post('/{myCard}', [MyCardController::class, 'update'])->name('update');
        Route::delete('/{myCard}', [MyCardController::class, 'destroy'])->name('destroy');
        Route::get('/active_link/{uuid}', [MyCardController::class, 'getActiveLinkByUuid'])->name('activeLink');
    });

    Route::prefix('my_card_links')->name('my_card_links.')->group(function () {
        Route::get('/', [MyCardLinkController::class, 'index'])->name('index');
        Route::post('/', [MyCardLinkController::class, 'store'])->name('store');
        Route::get('/{myCardLink}', [MyCardLinkController::class, 'show'])->name('show');
        Route::post('/{myCardLink}', [MyCardLinkController::class, 'update'])->name('update');
        Route::delete('/{myCardLink}', [MyCardLinkController::class, 'destroy'])->name('destroy');
    });


    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::post('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });




});

Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

// Route for canceled payment
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

Route::prefix('card')->group(function () {
    Route::get('/', [CardController::class, 'index']);
});

// CMS Route
Route::post('/contact/add', [ContactController::class, 'addContact'])->name('user.view');
Route::post('/paypal/add', [ContactController::class, 'addPaypal'])->name('user.paypal');
Route::get('/action/view/{code}', [QrcodeController::class, 'view'])->name('user.view');
Route::post('stripe/webhook', [StripeController::class, 'handle']);
