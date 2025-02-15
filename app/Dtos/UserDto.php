<?php

namespace App\Dtos;

use App\Interfaces\DtoInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

class UserDto implements DtoInterface
{
    private ?int $id;
    private string $name;
    private string $email;
    private string $phone_number;
    private string $password;
    private Carbon $created_at;
    private Carbon $updated_at;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phone_number;
    }

    /**
     * @param string $phone_number
     */
    public function setPhoneNumber(string $phone_number): void
    {
        $this->phone_number = $phone_number;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @param Carbon $created_at
     */
    public function setCreatedAt(Carbon $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @param Carbon $updated_at
     */
    public function setUpdatedAt(Carbon $updated_at): void
    {
        $this->updated_at = $updated_at;
    }


    public static function fromApiFormRequest(FormRequest $request): DtoInterface
    {

        $userDto = new UserDto();

        $userDto->setName($request->input('name'));
        $userDto->setEmail($request->input('email'));
        $userDto->setPhoneNumber($request->input('phone_number'));
        $userDto->setPassword($request->input('password'));
        return $userDto;
    }

    public static function fromModel(User|Model $model): DtoInterface
    {
        $userDto = new UserDto();
        $userDto->setId($model->id);
        $userDto->setName($model->name);
        $userDto->setEmail($model->email);
        $userDto->setPhoneNumber($model->phone_number);
        $userDto->setPassword($model->password);
        $userDto->setCreatedAt($model->created_at);
        $userDto->setUpdatedAt($model->updated_at);
        return $userDto;
    }


    public static function toArray(Model $model): array
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'email' => $model->email,
            'phone_number' => $model->phone_number,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ];
    }


}
