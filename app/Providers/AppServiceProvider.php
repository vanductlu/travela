<?php

namespace App\Providers;
use App\Models\admin\ContactModel;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
        $contactModel = new ContactModel();
        $data = $contactModel->countContactsUnread();

        $view->with('unreadCount', $data['countUnread']);
        $view->with('unreadContacts', $data['contacts']);
    });
    }
}
