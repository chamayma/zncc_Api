<?php

declare(strict_types=1);

$container['userService'] = static function (Pimple\Container $container): App\Service\UserService {
    return new App\Service\UserService($container['db']);
};

$container['memberService'] = static function (Pimple\Container $container): App\Service\MemberService {
    return new App\Service\MemberService($container['db']);
};
