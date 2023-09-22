<?php

namespace App\Providers;

use App\Models\Memo;
use App\Models\Tag;
use Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
           $memo_model = new Memo();
           $memos = $memo_model->getMyMemo();
           
            $tags = Tag::where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->get();

            $view->with('memos', $memos)->with('tags', $tags);

        });
    }
}
