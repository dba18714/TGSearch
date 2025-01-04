<?php

namespace App\ContentAudit;

use App\ContentAudit\Contracts\ContentAuditInterface;
use App\ContentAudit\Drivers\OpenaiDriver;
use App\ContentAudit\Drivers\TencentDriver;
use Illuminate\Support\Manager;
use OpenAI;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Tms\V20201229\TmsClient;

class ContentAuditManager extends Manager
{
    public function createOpenaiDriver(): ContentAuditInterface
    {
        $config = $this->config->get('services.openai');
        return new OpenaiDriver(
            OpenAI::factory()
                ->withBaseUri($config['base_uri'])  // https://api.guidaodeng.com/v1
                ->withApiKey($config['api_key'])
                ->make()
        );
    }

    public function createTencentDriver(): ContentAuditInterface
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

        return new TencentDriver($client);
    }

    public function getDefaultDriver(): string
    {
        return $this->config->get('content-audit.default', 'tencent');
    }
}
