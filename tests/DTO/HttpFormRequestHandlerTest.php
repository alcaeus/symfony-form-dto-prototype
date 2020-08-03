<?php

namespace App\Tests\DTO;

use App\DTO\EditAddress;
use App\DTO\HttpFormRequestHandler;
use App\Entity\Address;
use App\Entity\User;
use App\Metadata\DtoMetadataReader;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

final class HttpFormRequestHandlerTest extends KernelTestCase
{
    public function testHandleWithEmptyInitialData()
    {
        $data = [
            'address' => [
                'street' => '<street redacted>',
                'city' => '<city redacted>',
                'postalCode' => '<postal code redacted>',
                'country' => 'Germany',
            ],
        ];
        $request = Request::create('/', 'POST', $data);
        $user = new User('alcaeus');

        $reader = new DtoMetadataReader();
        $metadata = $reader->getMetadata(EditAddress::class);

        $dto = $metadata->createInstance(['type' => 'user']);
        $dto->initialize($user);

        self::bootKernel();
        $httpRequestHandler = self::$kernel->getContainer()->get(HttpFormRequestHandler::class);
        $this->assertInstanceOf(HttpFormRequestHandler::class, $httpRequestHandler);

        $this->assertTrue($httpRequestHandler->handle($metadata, $dto, $request));

        $dto->apply($user);
    }

    public function testHandle()
    {
        $data = [
            'address' => [
                'street' => '<street redacted>',
                'city' => '<city redacted>',
                'postalCode' => '<postal code redacted>',
                'country' => 'Germany',
            ],
        ];
        $request = Request::create('/', 'POST', $data);
        $user = new User('alcaeus');
        $user->updateAddress(new Address( '<street redacted>',  '<city redacted>',  '<postal code redacted>',  'Italy'));

        $reader = new DtoMetadataReader();
        $metadata = $reader->getMetadata(EditAddress::class);

        $dto = $metadata->createInstance(['type' => 'user']);
        $dto->initialize($user);

        self::bootKernel();
        $httpRequestHandler = self::$kernel->getContainer()->get(HttpFormRequestHandler::class);
        $this->assertInstanceOf(HttpFormRequestHandler::class, $httpRequestHandler);

        $this->assertTrue($httpRequestHandler->handle($metadata, $dto, $request));

        $dto->apply($user);

        $this->assertSame('Germany', $user->getAddress()->getCountry());
    }
}
