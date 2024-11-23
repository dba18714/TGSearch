<?php

namespace App\Services;

use App\Contracts\ContentModerationService;
use Illuminate\Support\Manager;
use OpenAI;
use OpenAI\Client;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Tms\V20201229\TmsClient;
class ContentModerationManager extends Manager
{
    public function createOpenaiDriver(): ContentModerationService
    {
        $config = $this->config->get('services.openai');
        return new OpenaiModerationService(
            OpenAI::client($config['api_key'])
        );
    }

    public function createTencentDriver(): ContentModerationService
    {
        $config = $this->config->get('services.tencent');
        
        $cred = new Credential(
            $config['secret_id'],
            $config['secret_key']
        );
        
        $httpProfile = new HttpProfile();
        $httpProfile->setEndpoint("tms.tencentcloudapi.com");
    
        $clientProfile = new ClientProfile();
        $clientProfile->setHttpProfile($httpProfile);
        
        $client = new TmsClient($cred, $config['region'], $clientProfile);
        
        return new TencentModerationService($client);
    }

    public function getDefaultDriver(): string
    {
        return $this->config->get('moderation.default', 'tencent');
    }

    public function driver($driver = null): ContentModerationService
    {
        return parent::driver($driver);
}
}