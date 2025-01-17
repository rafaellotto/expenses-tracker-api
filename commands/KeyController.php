<?php

namespace app\commands;

use Random\RandomException;
use yii\console\Controller;
use yii\console\ExitCode;

class KeyController extends Controller
{
    public function actionGenerate(): int
    {
        $env_file_path = __DIR__ . '/../.env';

        if (! file_exists($env_file_path)) {
            echo "Error: .env file not found." . PHP_EOL;
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $env = file_get_contents($env_file_path);

        $key = null;

        do {
            try {
                $key = base64_encode(random_bytes(64));
            } catch (RandomException $e) {
                echo "Error when generating a key: " . $e->getMessage() . PHP_EOL;
            }
        } while (! $key);

        $env = preg_replace(
            '/^JWT_SECRET_KEY=.*$/m',
            "JWT_SECRET_KEY=$key",
            $env
        );

        if (! file_put_contents($env_file_path, $env)) {
            echo "Error: Failed to update the .env file." . PHP_EOL;
            return ExitCode::UNSPECIFIED_ERROR;
        }

        echo "JWT_SECRET updated successfully in .env file." . PHP_EOL;
        return ExitCode::OK;
    }
}
