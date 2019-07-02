<?php
namespace Tabula;

/**
 * Outlines a basic Tabula module
 * Extend this when creating modules
 * 
 * @author Skye
 */
abstract class Module{
    /**
     * Should upgrade the module to the latest version,
     * performing any required database upgrades,
     * when passed the currently installed version of itself.
     * If the passed value is an empty string, the 
     * module should perform its own initial setup.
     * Once this has been done, the module should
     * return its new version, as this will be passed 
     * back to the module next time it is loaded.
     */
    public function upgrade(string version): string;

    /**
     * Register all available routes for this module
     * with the passed Router. This will enable Tabula
     * to pass appropriate requests to this module.
     * Be aware these routes may be cached, and treat them
     * appropriately (i.e. please don't register parameters
     * where a query string would be more appropriate)
     * 
     * Please note request paramets are not yet implemented.
     */
    public function registerRoutes(Router router): boolean;

    /**
     * This will be called *if* a given request is one 
     * that should be routed to this module; or if
     * a previously called module requires this module.
     * If required, a reference to Tabula can be pulled here.
     */
    public function init(Tabula tabula): void;

    /**
     * Return your module name here. 
     * Please use a db-friendly name or things will break 😢
     */
    public function getName(): string;
}