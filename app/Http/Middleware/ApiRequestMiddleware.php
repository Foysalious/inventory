<?php namespace App\Http\Middleware;


use App\Interfaces\ApiRequestRepositoryInterface;
use Illuminate\Http\Request;


use Closure;

class ApiRequestMiddleware
{
    private ApiRequestRepositoryInterface $apiRequestRepository;

    public function __construct(ApiRequestRepositoryInterface $apiRequestRepository)
    {
        $this->apiRequestRepository = $apiRequestRepository;
    }

    public function handle(Request $request, Closure $next)
    {
        $api_request = $this->apiRequestRepository->create([
            'route' => $request->fullUrl(),
            'ip' => getIp(),
            'user_agent' => $request->header('User-Agent'),
            'portal' => $request->header('portal-name'),
            'portal_version' => $request->header('Version-Code'),
        ]);
        $request->merge(['api_request' => $api_request]);
        return $next($request);
    }
}
