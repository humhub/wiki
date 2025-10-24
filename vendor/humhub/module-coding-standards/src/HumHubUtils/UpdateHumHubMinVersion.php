<?php

namespace HumHubUtils;

class UpdateHumHubMinVersion
{
    public static function increaseVersion($minHumHubVersion)
    {
        $moduleJsonPath = getcwd() . '/module.json';
        if (file_exists($moduleJsonPath)) {
            $moduleJson = json_decode(file_get_contents($moduleJsonPath), true);
            if (version_compare($moduleJson['humhub']['minVersion'], $minHumHubVersion, '<')) {
                $moduleJson['humhub']['minVersion'] = $minHumHubVersion;
                file_put_contents(
                    $moduleJsonPath,
                    json_encode($moduleJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                );
            }
        } else {
            print "********** Module JSON not found!\n\n";
        }
    }
}