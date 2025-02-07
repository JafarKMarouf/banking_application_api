<?php

namespace App\Services;

use App\Dtos\UserDto;
use App\Exceptions\InvaildPinException;
use App\Exceptions\InvalidPinLength;
use App\Exceptions\NotSetupPin;
use App\Exceptions\PinHasAlreadyBeenSet;
use App\Interfaces\UserServiceInterface;
use App\Jobs\SendEmailVerificationJob;
use App\Models\User;
use App\Traits\OtpTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService implements UserServiceInterface
{
    use OtpTrait;
    /**
     * @param \App\Dtos\UserDto $userDto
     * @param mixed $ipAddress
     * @return array{data: array<string|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model>, message: string}
     */
    public function createUser(UserDto $userDto, $ipAddress): array
    {
        $user =
            User::query()->create([
                'name' => $userDto->getName(),
                'email' => $userDto->getEmail(),
                'phone_number' => $userDto->getPhoneNumber(),
                'password' => $userDto->getPassword(),
            ]);

        $token = $user->createToken('token')->plainTextToken;
        $otp = $this->generateOtp($user->email, $ipAddress);

        dispatch(new SendEmailVerificationJob($user, $otp));

        $data['user'] = $user;
        $data['token'] = $token;

        $message = 'User Registeration Successfull!';

        return ['data' => $data, 'message' => $message];
    }

    /**
     * @param mixed $request
     * @return array{code: int, data: array, message: string}
     */
    public function loginUser($request): array
    {
        $field = filter_var(
            $request->identifier,
            FILTER_VALIDATE_EMAIL
        ) ? 'email' : 'phone_number';
        if (!Auth::attempt([
            $field => $request->identifier,
            'password' => $request->password
        ])) {
            $data = [];
            $message = "User $field & password does not match with our record.";
            $code = 401;
            return ['data' => $data, 'message' => $message, 'code' => $code];
        }
        $user = $request->user();
        $data['user'] = $user;
        $data['token'] = $user->createToken('token')->plainTextToken;

        $message = 'User Logged In Successfully!';
        $code = 200;

        return ['data' => $data, 'message' => $message, 'code' => $code];
    }
    /**
     * @inheritDoc
     */
    public function getUserById(int $userId): User
    {
        $user = User::query()
            ->where('id', $userId)
            ->first();
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function hasSetPin(User $user): bool
    {
        return $user->pin != null;
    }

    /**
     * @inheritDoc
     */
    public function setupPin(User $user, string $pin): void
    {
        if ($this->hasSetPin($user)) {
            throw new PinHasAlreadyBeenSet();
        }

        $user->pin = Hash::make($pin);
        $user->save();
    }

    /**
     * @inheritDoc
     */
    public function validatePin(int $userId, string $pin): bool
    {
        $user = $this->getUserById($userId);
        if (!$user) {
            throw new ModelNotFoundException();
        }
        if (!$this->hasSetPin($user)) {
            throw new NotSetupPin();
        }
        if (!Hash::check($pin, $user->pin)) {
            throw new InvaildPinException();
        }
        return true;
    }
}
