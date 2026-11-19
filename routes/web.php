<?php

use App\Http\Controllers\Admin\AccountController as AdminAccountController;
use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExperienceController as AdminExperienceController;
use App\Http\Controllers\Admin\InvitationController as AdminInvitationController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\SkillController as AdminSkillController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\Admin\TechnologyController as AdminTechnologyController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Portfolio Routes (Phase 3)
|--------------------------------------------------------------------------
*/

Route::get('/', [PortfolioController::class, 'home'])->name('home');
Route::get('/about', [PortfolioController::class, 'about'])->name('about');
Route::get('/experience', [PortfolioController::class, 'experience'])->name('experience');
Route::get('/skills', [PortfolioController::class, 'skills'])->name('skills');
Route::get('/services', [PortfolioController::class, 'services'])->name('services');

// Projects list & case study detail
Route::get('/projects', [ProjectController::class, 'index'])->name('projects');
Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');

// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

Route::get('/resume', [PortfolioController::class, 'resume'])->name('resume.download');

Route::get('/feed.xml', [FeedController::class, 'index'])->name('feed');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');
Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/blog/tag/{tag:slug}', [BlogController::class, 'tag'])->name('blog.tag');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.post');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Portal Routes (Phase 2)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin', 'can:manage-portfolio'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('account', [AdminAccountController::class, 'edit'])->name('account.edit');

    // Projects CRUD
    Route::resource('projects', AdminProjectController::class)->except(['show']);

    // Experiences CRUD
    Route::get('experiences', [AdminExperienceController::class, 'index'])->name('experiences.index');
    Route::post('experiences', [AdminExperienceController::class, 'store'])->name('experiences.store');
    Route::put('experiences/{experience}', [AdminExperienceController::class, 'update'])->name('experiences.update');
    Route::delete('experiences/{experience}', [AdminExperienceController::class, 'destroy'])->name('experiences.destroy');

    // Skills & Categories
    Route::get('skills', [AdminSkillController::class, 'index'])->name('skills.index');
    Route::post('skills/categories', [AdminSkillController::class, 'storeCategory'])->name('skills.categories.store');
    Route::delete('skills/categories/{category}', [AdminSkillController::class, 'destroyCategory'])->name('skills.categories.destroy');
    Route::post('skills', [AdminSkillController::class, 'storeSkill'])->name('skills.store');
    Route::delete('skills/{skill}', [AdminSkillController::class, 'destroySkill'])->name('skills.destroy');

    Route::get('technologies', [AdminTechnologyController::class, 'index'])->name('technologies.index');
    Route::post('technologies', [AdminTechnologyController::class, 'store'])->name('technologies.store');
    Route::delete('technologies/{technology}', [AdminTechnologyController::class, 'destroy'])->name('technologies.destroy');

    // Services CRUD
    Route::get('services', [AdminServiceController::class, 'index'])->name('services.index');
    Route::post('services', [AdminServiceController::class, 'store'])->name('services.store');
    Route::put('services/{service}', [AdminServiceController::class, 'update'])->name('services.update');
    Route::delete('services/{service}', [AdminServiceController::class, 'destroy'])->name('services.destroy');

    Route::post('posts/preview', [AdminPostController::class, 'preview'])->name('posts.preview');
    Route::resource('posts', AdminPostController::class)->except(['show']);

    Route::get('tags', [AdminTagController::class, 'index'])->name('tags.index');
    Route::post('tags', [AdminTagController::class, 'store'])->name('tags.store');
    Route::delete('tags/{tag}', [AdminTagController::class, 'destroy'])->name('tags.destroy');

    Route::get('testimonials', [AdminTestimonialController::class, 'index'])->name('testimonials.index');
    Route::post('testimonials', [AdminTestimonialController::class, 'store'])->name('testimonials.store');
    Route::put('testimonials/{testimonial}', [AdminTestimonialController::class, 'update'])->name('testimonials.update');
    Route::delete('testimonials/{testimonial}', [AdminTestimonialController::class, 'destroy'])->name('testimonials.destroy');

    // Messages
    Route::get('messages', [AdminMessageController::class, 'index'])->name('messages.index');
    Route::post('messages/bulk', [AdminMessageController::class, 'bulk'])->name('messages.bulk');
    Route::patch('messages/read-all', [AdminMessageController::class, 'markAllAsRead'])->name('messages.read-all');
    Route::patch('messages/{message}/read', [AdminMessageController::class, 'markAsRead'])->name('messages.read');
    Route::patch('messages/{message}/handled', [AdminMessageController::class, 'markAsHandled'])->name('messages.handled');
    Route::patch('messages/{message}/archive', [AdminMessageController::class, 'archive'])->name('messages.archive');
    Route::patch('messages/{message}/unarchive', [AdminMessageController::class, 'unarchive'])->name('messages.unarchive');
    Route::post('messages/{message}/reply', [AdminMessageController::class, 'reply'])->name('messages.reply');
    Route::delete('messages/{message}', [AdminMessageController::class, 'destroy'])->name('messages.destroy');

    Route::patch('notifications/read-all', [AdminNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::patch('notifications/{notification}/read', [AdminNotificationController::class, 'markAsRead'])->name('notifications.read');

    // Superadmin only: Profile & Settings
    Route::middleware(['admin:superadmin', 'can:manage-site'])->group(function () {
        Route::get('profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::post('profile', [AdminProfileController::class, 'update'])->name('profile.update');

        Route::get('invitations', [AdminInvitationController::class, 'index'])->name('invitations.index');
        Route::post('invitations', [AdminInvitationController::class, 'store'])->name('invitations.store');
        Route::delete('invitations/{invitation}', [AdminInvitationController::class, 'destroy'])->name('invitations.destroy');

        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        Route::get('activity', [AdminActivityLogController::class, 'index'])->name('activity.index');
    });
});

require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
