<?php

namespace App\Services;

use App\Dtos\UserDto;
use App\Events\RegisterUserEvent;
use App\Exceptions\InvalidPinException;
use App\Exceptions\NotSetupPin;
use App\Exceptions\PinHasAlreadyBeenSet;
use App\Interfaces\UserServiceInterface;
use App\Jobs\SendEmailVerificationJob;
use App\Models\User;
use App\Notifications\SendEmailVerificationNotification;
use App\Traits\OtpTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService implements UserServiceInterface
{
    use OtpTrait;

    /**
     * @param UserDto $userDto
     * @param mixed $ipAddress
     * @return array{data: array<string|Builder|Model>, message: string}
     * @throws \Exception
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

        /** @var User $user*/
        $token = $user->createToken('token')->plainTextToken;
        $otp = $this->generateOtp($user->email, $ipAddress);
        dispatch(new SendEmailVerificationJob($user, $otp));

        $data['user'] = $user;
        $data['token'] = $token;

        $message = 'User Registration Successfully!';

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
    public function getUserById(int $userId): User
    {
        /** @var User $user */
        $user = User::query()
            ->where('id', $userId)
            ->first();
        return $user;
    }

    public function hasSetPin(User $user): bool
    {
        return $user->pin != null;
    }

    /**
     * @throws PinHasAlreadyBeenSet
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
     * @throws InvalidPinException
     * @throws NotSetupPin
     */
    public function validatePin(int $userId, string $pin): bool
    {
        $user = $this->getUserById($userId);
        if (!$this->getUserById($userId)) {
            throw new ModelNotFoundException();
        }
        if (!$this->hasSetPin($user)) {
            throw new NotSetupPin();
        }
        if (!Hash::check($pin, $user->pin)) {
            throw new InvalidPinException();
        }
        return true;
    }
}
