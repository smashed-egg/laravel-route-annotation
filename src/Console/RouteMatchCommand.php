<?php

namespace SmashedEgg\RouteAnnotation\Console;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Symfony\Component\Routing\Matcher\TraceableUrlMatcher;
use Symfony\Component\Routing\RequestContext;

class RouteMatchCommand extends Command
{
    protected $signature = 'se:route:match
        {path_info : A path info}
        {--method= : Set the HTTP method}
        {--scheme= : Set the URI scheme (usually http or https)}
        {--host= : Set the URI host}
    ';

    public function __construct(protected Router $router)
    {
        parent::__construct();
    }

    public function handle()
    {
        $context = new RequestContext();
        //$context = $this->router->getContext();
        if (null !== $method = $this->option('method')) {
            $context->setMethod($method);
        }
        if (null !== $scheme = $this->option('scheme')) {
            $context->setScheme($scheme);
        }
        if (null !== $host = $this->option('host')) {
            $context->setHost($host);
        }

        $matcher = new TraceableUrlMatcher($this->router->getRoutes()->toSymfonyRouteCollection(), $context);
        /*foreach ($this->expressionLanguageProviders as $provider) {
            $matcher->addExpressionLanguageProvider($provider);
        }*/

        $traces = $matcher->getTraces($this->argument('path_info'));

        //var_dump($traces);
        $this->output->newLine();

        $matches = false;
        foreach ($traces as $trace) {
            if (TraceableUrlMatcher::ROUTE_ALMOST_MATCHES == $trace['level']) {
                $this->output->text(sprintf('Route <info>"%s"</> almost matches but %s', $trace['name'], lcfirst($trace['log'])));
            } elseif (TraceableUrlMatcher::ROUTE_MATCHES == $trace['level']) {
                $this->output->success(sprintf('Route "%s" matches', $trace['name']));

                //$routerDebugCommand = $this->getApplication()->find('debug:router');
                //$routerDebugCommand->run(new ArrayInput(['name' => $trace['name']]), $output);

                $matches = true;
            } elseif ($this->option('verbose')) {
                $this->output->text(sprintf('Route "%s" does not match: %s', $trace['name'], $trace['log']));
            }
        }

        if (!$matches) {
            $this->output->error(sprintf('None of the routes match the path "%s"', $this->argument('path_info')));

            return 1;
        }

        //$this->info($this->argument('path_info'));
        $this->info('Foo bar');

        return 0;
    }
}
