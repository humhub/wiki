<?php

namespace HumHubUtils;

class UpdatePhpVersion
{
    public static function increaseVersion($newMinPhpVersion)
    {
        $composerJson = json_decode(file_get_contents(getcwd() . '/composer.json'), true);
        if (isset($composerJson['config']['platform']['php']) && version_compare(
                $composerJson['config']['platform']['php'],
                $newMinPhpVersion,
                '<'
            )) {
            $composerJson['config']['platform']['php'] = $newMinPhpVersion;
            file_put_contents(
                getcwd() . '/composer.json',
                json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );
        }
    }
}