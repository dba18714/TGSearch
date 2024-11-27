<?php

namespace App\Providers;

use App\Contracts\ContentModerationService;
use App\Services\ContentModerationManager;
use Illuminate\Support\ServiceProvider;
use App\Services\GoogleCustomSearchService;
use Illuminate\Http\Client\Factory as HttpFactory;
use OpenAI;
use OpenAI\Client;
use App\Services\OpenaiModerationService;
use App\Services\TencentModerationService;
use Illuminate\Database\Eloquent\Relations\Relation;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Tms\V20201229\TmsClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Relation::enforceMorphMap([
            'chat' => 'App\Models\Chat',
            'message' => 'App\Models\Message',
        ]);

        // $this->app->singleton('moderation', function ($app) {
        //     return new ContentModerationManager($app);
        // });

        // $this->app->singleton(ContentModerationService::class, function ($app) {
        //     return $app->make('moderation')->driver();
        // });

        // $this->app->singleton(TmsClient::class, function () {
        //     $cred = new Credential(
        //         config('services.tencent.secret_id'),
        //         config('services.tencent.secret_key')
        //     );
            
        //     $httpProfile = new HttpProfile();
        //     $httpProfile->setEndpoint("tms.tencentcloudapi.com");
        
        //     $clientProfile = new ClientProfile();
        //     $clientProfile->setHttpProfile($httpProfile);
            
        //     return new TmsClient($cred, config('services.tencent.region'), $clientProfile);
        // });
        
        // $this->app->singleton(TencentCloudModerationService::class, function ($app) {
        //     return new TencentCloudModerationService($app->make(TmsClient::class));
        // });

        $this->app->singleton(GoogleCustomSearchService::class, function ($app) {
            return new GoogleCustomSearchService(
                $app->make(HttpFactory::class),
                config('services.google.search_api_key'),
                config('services.google.search_engine_id')
            );
        });

        // // 注册 OpenAI 客户端
        // $this->app->singleton(Client::class, function () {
        //     return OpenAI::client(config('services.openai.api_key'));
        // });

        // // 注册审核服务
        // $this->app->singleton(OpenAiModerationService::class, function ($app) {
        //     return new OpenAiModerationService($app->make(Client::class));
        // });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
