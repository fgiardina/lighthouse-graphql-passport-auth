<?php

namespace Joselfonseca\LighthouseGraphQLPassport\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Password;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPassword
{

    use SendsPasswordResetEmails;
    /**
     * @param $rootValue
     * @param array $args
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext|null $context
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolve($rootValue, array $args, GraphQLContext $context = null, ResolveInfo $resolveInfo)
    {

        $user = null;
        if ( $args['data']['provider'] == "api") {
            $user = \App\User::where('email' , $args['data']['email'])->first();
        }

        if ($args['data']['provider'] == "admin-api") {
            $user = \App\Admin::where('email', $args['data']['email'])->first();
        }

        if (!$user) {
            return [
                'status' => 'EMAIL_NOT_SENT',
                'message' => $args['data']['email'] . " not found - provider: " . $args['data']['provider']
            ];
        }

        try {
            $this->broker()->sendResetLink(['email' => $args['data']['email']]);
        } catch (\Throwable $th) {}

        return [
            'status' => 'EMAIL_SENT',
            'message' => $args['data']['email']
        ];
    }

}
