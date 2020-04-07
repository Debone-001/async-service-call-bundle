AsyncServiceCallBundle
========================

This bundle allows you to execute methods of your services asynchronously in a background process.

It is a fork on [krlove/async-service-call-bundle](https://github.com/krlove/async-service-call-bundle),
updated to run in Symfony 4 or 5.

Installation
------------
Install using composer:

    composer require krlove/async-service-call-bundle

It should enable the bundle at `config/bundles.php`

    return [
       ...
       new Krlove\AsyncServiceCallBundle\KrloveAsyncServiceCallBundle(),
    ]
    
If not, you now know what to do.
    
Configuration
-------------
Options:

- `console_path` - path to `console` script.
Can be absolute or relative to `kernel.project_dir` parameter's value.
Defaults to `bin/console` Symfony 4.* and Symfony 5.*.
- `php_path` - path to php executable. If no option provided in configuration, `Symfony\Component\Process\PhpExecutableFinder::find` will be used to set it up.

Example:

    # config/packages/krlove_async_service_call.yaml
    krlove_async_service_call:
        console_path: bin/console
        php_path: /usr/local/bin/php

Usage
-----
Define any service:

    <?php
        
    namespace App\Service;
        
    class AwesomeService
    {
        public function doSomething($int, $string, $array)
        {
            // do something heavy
            sleep(10);
        }
    }

Register service:

    # services.yml
    services:
        app.service.awesome:
            class: App\Service\AwesomeService
            public: true

> make sure your service is configured with `public: true`

Execute `doSomething` method asynchronously:

    $this->get('krlove.async')
         ->call('app.service.awesome', 'doSomething', [1, 'string', ['array']);

Line above will execute `App\Service\AwesomeService::doSomething` method by running `krlove:service:call` command on background.

You can follow it's execution by calling `top` on your console.

Original approach with [symfony process](https://symfony.com/doc/current/components/process.html) `Symfony\Component\Process\Process` was abandoned
in favor of a more traditional `exec` php function. The reason is this (that you can find in upper page):

```text
If a Response is sent before a child process had a chance to complete, the server process will be killed (depending on your OS). 
It means that your task will be stopped right away. 
Running an asynchronous process is not the same as running a process that survives its parent process.
```

 