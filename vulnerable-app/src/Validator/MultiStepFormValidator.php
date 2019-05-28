<?php declare(strict_types=1);

namespace App\Validator;

use App\Data\MultiStepForm;
use App\Entity\User;

class MultiStepFormValidator
{
    public function validate(?User $user, MultiStepForm $form): array
    {
        $errors = [];
        if ($form->isVipDiscount()) {
            $roles = $user ? $user->getRoles() : [];

            if ($user && !in_array('ROLE_VIP', $roles, true)) {
                $errors[] = 'Only VIP users can apply for discounts.';
            }
        }

        return $errors;
    }
}