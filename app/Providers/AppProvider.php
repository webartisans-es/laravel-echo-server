<?php

namespace App\Providers;

use BeyondCode\LaravelWebSockets\Apps\App;
use BeyondCode\LaravelWebSockets\Apps\AppProvider as IAppProvider;
use App\App as DatabaseApp;

class AppProvider implements IAppProvider
{
    public function all(): array
    {
        return DatabaseApp::where('active', true)->get()->map(function (DatabaseApp $app) {
            return $this->instantiate($app->toArray());
        })->toArray();
    }

    public function findById($appId): ?App
    {
        return $this->instantiate(DatabaseApp::find($appId)->toArray() ?? null);
    }

    public function findByKey(string $appKey): ?App
    {
        return $this->instantiate(DatabaseApp::where('key', $appKey)->firstOrFail()->toArray() ?? null);
    }

    public function findBySecret(string $appSecret): ?App
    {
        return $this->instantiate(DatabaseApp::where('secret', $appSecret)->first()->toArray() ?? null);
    }

    protected function instantiate(?array $appAttributes): ?App
    {
        if (! $appAttributes) {
            return null;
        }

        $app = new App($appAttributes['id'], $appAttributes['key'], $appAttributes['secret']);

        if (isset($appAttributes['name'])) {
            $app->setName($appAttributes['name']);
        }

        if (isset($appAttributes['host'])) {
            $app->setHost($appAttributes['host']);
        }

        $app->enableClientMessages($appAttributes['enable_client_messages'])
            ->enableStatistics($appAttributes['enable_statistics']);

        return $app;
    }
}
