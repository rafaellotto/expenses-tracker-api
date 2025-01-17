<?php

namespace app\components;

class JwtService
{
    public static function generateToken($user_id): string
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone(\Yii::$app->timeZone));
        $token = \Yii::$app->jwt->getBuilder()
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('uid', $user_id)
            ->getToken(
                \Yii::$app->jwt->getConfiguration()->signer(),
                \Yii::$app->jwt->getConfiguration()->signingKey()
            );

        return $token->toString();
    }
}