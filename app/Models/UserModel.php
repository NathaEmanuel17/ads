<?php

namespace App\Models;

use CodeIgniter\Model;
use Faker\Generator;
use Fluent\Auth\Contracts\UserProviderInterface;
use App\Entities\User;
use Fluent\Auth\Traits\UserProviderTrait;

class UserModel extends Model implements UserProviderInterface
{
    use UserProviderTrait;

    /**
     * Name of database table
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The format that the results should be returned as.
     * Will be overridden if the as* methods are used.
     *
     * @var User
     */
    protected $returnType = User::class;

    /**
     * An array of field names that are allowed
     * to be set by the user in inserts/updates.
     *
     * @var array
     */
    protected $allowedFields = [
        'email',
        'username',
        'password',
        'email_verified_at',
        'remember_token',

        // Campos da gerencianet
        'name',
        'last_name',
        'cpf',
        'birth',
        'phone',
        'display_phone'
    ];

    /**
     * If true, will set created_at, and updated_at
     * values during insert and update routines.
     *
     * @var boolean
     */
    protected $useTimestamps = true;

    /**
     * Generate fake data.
     *
     * @return array
     */
    public function fake(Generator &$faker)
    {
        $faker->addProvider(new \Faker\Provider\pt_BR\Person($faker));
        $faker->addProvider(new \Faker\Provider\pt_BR\PhoneNumber($faker));

        return [
            'email'             => $faker->unique()->email,
            'username'          => $faker->unique()->userName,
            'password'          => '12345678',
            'name'              => $faker->name(),
            'last_name'         => $faker->lastName(),
            'email_verified_at' => date('Y-m-d H:i:s'), // está verificado
            'cpf'               => $faker->unique()->cpf,
            'phone'             => $faker->unique()->cellphoneNumber,
            'birth'             => $faker->date('Y-m-d H:i:s'),
            'display_phone'     => $faker->numberBetween(0, 1),

        ];
    }

    public function getSuperadmin()
    {
        return $this->join('superadmins', 'superadmins.user_id = users.id')->first();
    }

    public function deleteUserAccout()
    {
        
        try {
    
            $this->db->transStart();

            $this->delete(service('auth')->user()->id, purge: true);

            $this->db->transComplete();
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);

            die('Erro ao excluir a conta');
        }
    }

    public function getUserBycriteria(array $criteria = [])
    {
        return $this->select(['id', 'name', 'username', 'email'])->where($criteria)->first();
    }
}
