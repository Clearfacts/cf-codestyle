<?php

declare(strict_types=1);

namespace Clearfacts\Bundle\CodestyleBundle;

use Clearfacts\Bundle\CodestyleBundle\DependencyInjection\ServiceExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CodestyleBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new ServiceExtension();
    }
}
