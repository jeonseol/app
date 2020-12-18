<?php

declare(strict_types=1);

namespace App\Extensions;

use Slim\Http\ServerRequest;
use Twig\{
    Extension\AbstractExtension, TwigFunction
};

class BaseUrl extends AbstractExtension {

    private $base = "";
    private $origin;

    public function __construct(ServerRequest $request, string $basepath = "") {

        $basepath = preg_replace('/[\/]+/', '', "/$basepath");
        $this->base = preg_replace('/\/$/', '', $basepath);

        $uri = $request->getUri();
        $uri = $uri->withHost(preg_replace('/:\d+$/', '', $uri->getHost()));
        $origin = $uri->getScheme();
        $origin .= '://' . $uri->getAuthority();
        $this->origin = $origin;
    }

    public function getFunctions() {
        return [
            new TwigFunction('base_url', [$this, 'getBaseUrl']),
            new TwigFunction('base_path', [$this, 'getBasePath']),
            new TwigFunction('origin', [$this, 'getOrigin']),
        ];
    }

    public function getOrigin(): string {
        return $this->origin;
    }

    public function getBaseUrl(): string {
        return $this->origin . $this->base;
    }

    public function getBasePath(): string {
        return $this->base;
    }

}
