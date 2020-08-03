<?php

namespace App\Controller;

use App\DTO\EditAddress;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

final class EditAddressController extends AbstractController
{
    public function __invoke(Request $request, User $user)
    {
        return $this->handleDto(EditAddress::class, $user, $request, ['type' => 'user']);
    }
}
