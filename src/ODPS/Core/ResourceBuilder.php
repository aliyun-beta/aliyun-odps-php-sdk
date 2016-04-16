<?php
namespace ODPS\Core;

/**
 * Class ResourceBuilder
 *
 * @package ODPS\Core
 */
class ResourceBuilder
{
    const PROJECTS = "/projects";
    const TABLES = "/tables";
    const REGISTRATION = "/registration";
    const FUNCTIONS = "/functions";
    const EVENTS = "/events";
    const RESOURCES = "/resources";
    const INSTANCES = "/instances";
    const VOLUMES = "/volumes";
    const STREAMS = "/streams";
    const TOPOLOGIES = "/topologies";
    const XFLOWS = "/xflows";
    const STREAMJOBS = "/streamjobs";
    const MATRICES = "/matrices";
    const TUNNEL = "/tunnel";
    const OFFLINEMODELS = "/offlinemodels";

    public static function buildProjectsUrl()
    {
        return static::PROJECTS;
    }

    public static function buildProjectUrl($projectName)
    {
        return static::PROJECTS . "/" . static::encode($projectName);
    }

    public static function buildResourceUrl($projectName, $resourceName)
    {
        return static::PROJECTS . "/" . static::encode($projectName) . static::RESOURCES . "/" . static::encode($resourceName);
    }

    public static function buildResourcesUrl($projectName)
    {
        return static::PROJECTS . "/" . static::encode($projectName) . static::RESOURCES;
    }

    public static function buildTableUrl($projectName, $tableName)
    {
        return static::PROJECTS . "/" . static::encode($projectName) . static::TABLES . "/" . static::encode($tableName);
    }

    public static function buildTablesUrl($projectName)
    {
        return static::PROJECTS . "/" . static::encode($projectName) . static::TABLES;
    }

    public static function buildFunctionsUrl($projectName)
    {

        return static::PROJECTS . "/" . static::encode($projectName) . static::REGISTRATION . static::FUNCTIONS;
    }

    public static function buildTunnelUrl($projectName)
    {
        return static::PROJECTS . "/" . static::encode($projectName) . static::TUNNEL;
    }

    public static function buildFunctionUrl($projectName, $functionName)
    {
        return static::PROJECTS . "/" . static::encode($projectName) . static::REGISTRATION . static::FUNCTIONS . "/" . static::encode($functionName);
    }

    public static function buildInstanceUrl($projectName, $instanceName)
    {
        return static::PROJECTS . "/" . static::encode($projectName) . static::INSTANCES . "/" . static::encode($instanceName);
    }

    public static function buildInstancesUrl($projectName)
    {
        return static::PROJECTS . "/" . static::encode($projectName) . static::INSTANCES;
    }

    private static function encode($str)
    {
        if ($str === null || empty($str)) {
            return $str;
        }
        return str_replace("\\+", "%20", $str);
    }
}
