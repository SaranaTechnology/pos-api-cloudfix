<?php

namespace Domain\Staff\Actions\Auth;

use Illuminate\Support\Facades\Hash;
use Infra\Shared\Foundations\Action;
use Infra\Staff\Models\Staff;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class StaffLoginAction extends Action
{
    public function execute($data)
    {
        $staff = Staff::where('nip', $data['nip'])->first();

        if (!$staff) {
            throw new BadRequestException('NIP tidak ditemukan');
        }

        if (!$staff->password) {
            throw new BadRequestException('Staff belum memiliki password, silahkan hubungi admin');
        }

        if (!Hash::check($data['password'], $staff->password)) {
            throw new BadRequestException('Password salah');
        }

        $tokenResult = $staff->createToken('Cashier-POS-Staff');

        return [
            'token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'staff' => $staff,
        ];
    }
}
