<?php
/**
 * Plugin Name:     Books Manager
 * Plugin URI:      https://www.veronalabs.com
 * Plugin Prefix:   BOOKS_MANAGER
 * Description:     Manage book information with custom post types, taxonomies, and Rabbit Framework.
 * Author:          Hooman Helmi
 * Text Domain:     books-manager
 * Domain Path:     /languages
 * Version:         1.0
 */

defined('ABSPATH') || exit;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
}

use BooksManager\BookMangerBoot;
use BooksManager\Database\DatabaseHandler;
use BooksManager\Services\InfoListService;
use BooksManager\Services\MetaBoxService;
use BooksManager\Services\RegisterBookService;
use Rabbit\Application;
use Rabbit\Database\DatabaseServiceProvider;
use Rabbit\Logger\LoggerServiceProvider;
use Rabbit\Plugin;
use Rabbit\Redirects\AdminNotice;
use Rabbit\Templates\TemplatesServiceProvider;
use Rabbit\Utils\Singleton;

class BooksManagerInit extends Singleton {

    /**
     * Application instance.
     * 
     * @var Container
     */
    private $application;

    /**
     * BooksManagerInit constructor.
     * 
     * @since 1.0
     */
    public function __construct() {
        $this->application = Application::get()->loadPlugin(__DIR__, __FILE__, 'config');
        $this->init();
    }

    /**
     * Initialize the plugin.
     * 
     * @return void
     * @since 1.0
     */
    private function init() {
        try {
            $this->loadServiceProviders();

            $this->application->onActivation(function() {
                $this->activatePlugin();
            });
    
            $this->application->onDeactivation(function() {
                $this->deactivatePlugin();
            });
    
            $this->application->boot(function ( Plugin $plugin ) {
                BookMangerBoot::get();
                $plugin->loadPluginTextDomain();
            });
        } catch (Exception $e) {
             /**
             * Print the exception message to admin notice area
             */
            add_action('admin_notices', function () use ($e) {
                AdminNotice::permanent(['type' => 'error', 'message' => $e->getMessage()]);
            });

            /**
             * Log the exception to file
             */
            add_action('init', function () use ($e) {
                if ($this->application->has('logger')) {
                    $this->application->get('logger')->warning($e->getMessage());
                }
            });
        }
        
    }

    /**
     * Load service providers.
     * 
     * @return void
     * @since 1.0
     */
    private function loadServiceProviders() {
        $this->application->addServiceProvider(DatabaseServiceProvider::class);
        $this->application->addServiceProvider(TemplatesServiceProvider::class);
        $this->application->addServiceProvider(LoggerServiceProvider::class);

        $this->application->addServiceProvider( RegisterBookService::class );
        $this->application->addServiceProvider( MetaBoxService::class );
        $this->application->addServiceProvider( InfoListService::class );
    }

    /**
     * Runs tasks on plugin activation.
     * 
     * @return void
     * @since 1.0
     */
    public function activatePlugin() {
        DatabaseHandler::createInfoTable();
    }

    /**
     * Runs tasks on plugin deactivation.
     * 
     * @return void
     * @since 1.0
     * @todo This method is optional and can be removed if not needed.
     */
    public function deactivatePlugin() {
        // Optional: clear events, cache, or temporary data
    }

    /**
     * Get the application instance.
     * 
     * @return Container
     * @since 1.0
     */
    public function getApplication() {
        return $this->application;
    }
}

/**
 * Initialize the plugin.
 * 
 * @return BooksManagerInit
 */
function BooksManagerInit()
{
    return BooksManagerInit::get();
}

BooksManagerInit();