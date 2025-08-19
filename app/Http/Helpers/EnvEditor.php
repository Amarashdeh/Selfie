<?php

namespace App\Helpers;

class EnvEditor
{
    public static function setEnvValue($key, $value)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $escaped = preg_quote('='.$_ENV[$key], '/');
            $content = file_get_contents($path);

            if(str_contains($content, $key.'=')){
                $content = preg_replace("/^{$key}{$escaped}/m", $key.'='.$value, $content);
                $content = preg_replace("/{$key}=.*/", $key.'='.$value, $content);
            } else {
                $content .= "\n{$key}={$value}";
            }

            file_put_contents($path, $content);
        }
    }
}
