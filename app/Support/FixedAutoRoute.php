<?php

namespace App\Support;

use Buki\AutoRoute\Middleware\AjaxRequestMiddleware;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Router;
use ReflectionClass;
use ReflectionMethod;
use Livewire\Component;
use Livewire\Volt\Volt;

class FixedAutoRoute extends \Buki\AutoRoute\AutoRoute
{
    public function auto(string $prefix, string $controller, array $options = []): void
    {
        $only = $options['only'] ?? [];
        $except = $options['except'] ?? [];
        $patterns = $options['patterns'] ?? [];

        $routeName = trim($options['as'] ?? ($options['name'] ?? trim($prefix, '/')), '.') . '.';
        if ($routeName === '.') {
            $routeName = '';
        }

        $this->router->group(
            array_merge($options, ['prefix' => $prefix, 'as' => $routeName]),
            function () use ($controller, $only, $except, $patterns) {
                [$class, $className] = $this->resolveControllerNameFixed($controller);
                $classRef = new ReflectionClass($class);
                foreach ($classRef->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                    if (in_array($method->class, [BaseController::class, "{$this->namespace}\\Controller"])
                        || ($method->getDeclaringClass()->getParentClass() && $method->getDeclaringClass()->getParentClass()->getName() === BaseController::class)
                        || !$method->isPublic()
                        || str_starts_with($method->name, '__')) {
                        continue;
                    }

                    $methodName = $method->name;

                    if ((!empty($only) && !in_array($methodName, $only))
                        || (!empty($except) && in_array($methodName, $except))) {
                        continue;
                    }

                    [$httpMethods, $path, $middleware] = $this->getHttpMethodAndNameFixed($methodName);
                    [$endpoints, $routePatterns] = $this->getRouteValuesFixed($method, $patterns);

                    $endpoint = implode('/', $endpoints);
                    $handler = [$classRef->getName(), $method->name];
                    $routePath = ($path !== $this->mainMethod ? $path : '') . "/{$endpoint}";

                    if (str_starts_with($method->name, 'volt')) {
                        if (class_exists(Volt::class) && $method->getReturnType()?->getName() === 'string') {
                            Volt::route($routePath, $method->invoke(new ($classRef->getName()), ...$endpoints))
                                ->where($routePatterns)->name("{$method->name}")->middleware($middleware);
                        }
                        continue;
                    }

                    if (str_starts_with($method->name, 'wire')) {
                        if (!(class_exists(Component::class) && $method->getReturnType()?->getName() === 'string')) {
                            continue;
                        }
                        $handler = $method->invoke(new ($classRef->getName()), ...$endpoints);
                        if (!is_subclass_of($handler, Component::class)) {
                            continue;
                        }
                    }

                    $this->router
                        ->addRoute(array_map(fn ($m) => strtoupper($m), $httpMethods), $routePath, $handler)
                        ->where($routePatterns)->name("{$method->name}")->middleware($middleware);
                }
            }
        );
    }

    protected function resolveControllerNameFixed(string $controller): array
    {
        $trimmed = ltrim($controller, "\\");
        if (class_exists($trimmed)) {
            return [$trimmed, $trimmed];
        }
        $controller = str_replace(['.', $this->namespace], ['\\', ''], $controller);
        return [
            $this->namespace . "\\" . trim($controller, "\\"),
            $controller,
        ];
    }

    private function getHttpMethodAndNameFixed(string $controllerMethod): array
    {
        $httpMethods = $this->defaultHttpMethods;
        $middleware = null;
        foreach (array_merge($this->availableMethods, $this->customMethods) as $method) {
            $method = strtolower($method);
            if (stripos($controllerMethod, $method, 0) === 0) {
                if (in_array($method, ['volt', 'wire'])) {
                    $httpMethods = ['GET', 'HEAD'];
                } elseif ($method !== 'xany') {
                    $httpMethods = [ltrim($method, 'x')];
                }
                $middleware = str_starts_with($method, 'x') ? $this->ajaxMiddleware : null;
                $controllerMethod = lcfirst(preg_replace('/' . $method . '_?/i', '', $controllerMethod, 1));
                break;
            }
        }
        $controllerMethod = strtolower(preg_replace('%([a-z]|[0-9])([A-Z])%', '\1-\2', $controllerMethod));
        return [$httpMethods, $controllerMethod, $middleware];
    }

    private function getRouteValuesFixed(ReflectionMethod $method, array $patterns = []): array
    {
        $routePatterns = $endpoints = [];
        $patterns = array_merge($this->defaultPatterns, $patterns);
        foreach ($method->getParameters() as $param) {
            $paramName = $param->getName();
            $typeHint = $param->hasType() ? $param->getType()->getName() : null;
            if (!$this->isValidRouteParamFixed($typeHint)) {
                continue;
            }
            $routePatterns[$paramName] = $patterns[$paramName] ??
                ($this->defaultPatterns[":{$typeHint}"] ?? $this->defaultPatterns[':any']);
            $endpoints[] = $param->isOptional() ? "{{$paramName}?}" : "{{$paramName}}";
        }
        return [$endpoints, $routePatterns];
    }

    private function isValidRouteParamFixed(?string $type): bool
    {
        if (is_null($type) || in_array($type, ['int', 'float', 'string', 'bool', 'mixed'])) {
            return true;
        }
        if (class_exists($type) && is_subclass_of($type, Model::class)) {
            return true;
        }
        if (function_exists('enum_exists') && enum_exists($type)) {
            return true;
        }
        return false;
    }
}
