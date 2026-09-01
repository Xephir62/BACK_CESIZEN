<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SmokeTest extends KernelTestCase
{
    public function testLeKernelDemarre(): void
    {
        self::bootKernel();

        $this->assertSame('test', self::$kernel->getEnvironment());
        $this->assertNotNull(self::getContainer());
    }
}
