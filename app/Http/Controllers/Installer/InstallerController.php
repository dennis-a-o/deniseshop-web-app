<?php

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Database\SQLiteConnection;
use Symfony\Component\Console\Output\BufferedOutput;
use Exception;

class InstallerController extends Controller
{
    /**
     * Minimun PHP versions
     * 
     * @var $minPhpVersion
    */
    private $minPhpVersion = '8.0.0';

    /**
     * @var string
     */
    private $envPath;

    /**
     * @var string
     */
    private $envExamplePath;

    /**
     * Set the .env and .env.example paths.
     */
    public function __construct()
    {
        $this->envPath = base_path('.env');
        $this->envExamplePath = base_path('.env.example');
    }

    public function index()
    {
        return view('installer.index');
    }

    public function requirements()
    {
        $phpVersion = $this->checkPhpVersion(config('installer.core.minPhpVersion'));

        $requirements = $this->checkRequirements(config('installer.requirements'));

        return view('installer.requirements', compact('phpVersion', 'requirements'));
    }

    public function permissions()
    {
        $permissions = $this->checkPermission(config('installer.permissions'));

        return view('installer.permissions',compact('permissions'));
    }

    public function environment()
    {
        return view('installer.environment');
    }

    public function environmentWizard()
    {
        return view('installer.environment-wizard');
    }

    public function setEnvironmentWizard(Request $request)
    {
        $valideted = $request->validate([
            'app_name'              => 'required|string|max:50',
            'app_environment'       => 'required|string|max:50',
            'app_debug'             => 'required|string',
            'app_log_level'         => 'required|string|max:50',
            'app_url'               => 'required|url',
            'database_connection'   => 'required|string|max:50',
            'database_hostname'     => 'required|string|max:50',
            'database_port'         => 'required|numeric',
            'database_name'         => 'required|string|max:50',
            'database_username'     => 'required|string|max:50',
            'database_password'     => 'nullable|string|max:50',
            'broadcast_driver'      => 'required|string|max:50',
            'cache_driver'          => 'required|string|max:50',
            'session_driver'        => 'required|string|max:50',
            'queue_driver'          => 'required|string|max:50',
            'redis_hostname'        => 'required|string|max:50',
            'redis_password'        => 'required|string|max:50',
            'redis_port'            => 'required|numeric',
            'mail_driver'           => 'required|string|max:50',
            'mail_host'             => 'required|string|max:50',
            'mail_port'             => 'required|string|max:50',
            'mail_username'         => 'required|string|max:50',
            'mail_password'         => 'required|string|max:50',
            'mail_encryption'       => 'required|string|max:50',
            'pusher_app_id'         => 'max:50',
            'pusher_app_key'        => 'max:50',
            'pusher_app_secret'     => 'max:50',
        ]);


        if (!$this->checkDatabaseConnection($request)) {
            return back()->withErrors("Could not connect to the database.");
        }

        $envFileData =
        'APP_NAME=\''.$request->app_name."'\n".
        'APP_ENV='.$request->app_environment."\n".
        'APP_KEY='.'base64:'.base64_encode(Str::random(32))."\n".
        'APP_DEBUG='.$request->app_debug."\n".
        'APP_LOG_LEVEL='.$request->app_log_level."\n".
        'APP_URL='.$request->app_url."\n\n".
        'DB_CONNECTION='.$request->database_connection."\n".
        'DB_HOST='.$request->database_hostname."\n".
        'DB_PORT='.$request->database_port."\n".
        'DB_DATABASE='.$request->database_name."\n".
        'DB_USERNAME='.$request->database_username."\n".
        'DB_PASSWORD='.$request->database_password."\n\n".
        'BROADCAST_DRIVER='.$request->broadcast_driver."\n".
        'CACHE_DRIVER='.$request->cache_driver."\n".
        'SESSION_DRIVER='.$request->session_driver."\n".
        'QUEUE_DRIVER='.$request->queue_driver."\n\n".
        'REDIS_HOST='.$request->redis_hostname."\n".
        'REDIS_PASSWORD='.$request->redis_password."\n".
        'REDIS_PORT='.$request->redis_port."\n\n".
        'MAIL_DRIVER='.$request->mail_driver."\n".
        'MAIL_HOST='.$request->mail_host."\n".
        'MAIL_PORT='.$request->mail_port."\n".
        'MAIL_USERNAME='.$request->mail_username."\n".
        'MAIL_PASSWORD='.$request->mail_password."\n".
        'MAIL_ENCRYPTION='.$request->mail_encryption."\n\n".
        'PUSHER_APP_ID='.$request->pusher_app_id."\n".
        'PUSHER_APP_KEY='.$request->pusher_app_key."\n".
        'PUSHER_APP_SECRET='.$request->pusher_app_secret;

        try {
            file_put_contents($this->envPath, $envFileData);
        } catch (Exception $e) {
            $results = "Unable to save the .env file, Please create it manually.";
        }

        $results = "Your .env file settings have been saved.";

        return redirect('/install/database')->with('results', $results);
    }

    public function environmentEditor()
    {
        return view('installer.environment-editor');
    }


    public function setEnvironmentEditor(Request $request)
    {
        
        return redirect('/install/database');
    }

    public function database()
    {
         $response = $this->migrateAndSeed();

        return redirect('/install/final')->with(['message' => $response]);
    }

    public function final()
    {
        $finalMessages = $this->runFinal();
        $finalStatusMessage = $this->update();
        $finalEnvFile = $this->getEnvContent();

        return view('installer.final', compact('finalMessages', 'finalStatusMessage', 'finalEnvFile'));
    }

    /**
     *  Generate New Application Key.
     * 
     * @return string
     */
    private function runFinal()
    {
        $outputLog = new BufferedOutput;

        try {
            Artisan::call('key:generate', ['--force'=> true], $outputLog);
            Artisan::call('jwt:secret', ['--force'=> true], $outputLog);
        } catch (Exception $e) {
            return static::response($e->getMessage(), $outputLog);
        }
    
        return $outputLog->fetch();
    }

     /**
     * Create installed file.
     *
     * @return string
     */
    private function update()
    {
        $installedLogFile = storage_path('installed');

        $dateStamp = date('Y/m/d h:i:sa');

        if (! file_exists($installedLogFile)) {
            $message = "Laravel Installer successfully INSTALLED on ".$dateStamp."\n";

            file_put_contents($installedLogFile, $message);
        } else {
            $message = "Laravel Installer successfully UPDATED on ".$dateStamp;

            file_put_contents($installedLogFile, $message.PHP_EOL, FILE_APPEND | LOCK_EX);
        }

        return $message;
    }

    /**
     * Get content of the .env file
     * 
     * @return string
     */
    private function getEnvContent()
    {
        if (! file_exists($this->envPath)) {
            if (file_exists($this->envExamplePath)) {
                copy($this->envExamplePath, $this->envPath);
            } else {
                touch($this->envPath);
            }
        }

        return file_get_contents($this->envPath);
    }

    /**
     * Migrate and seeds database
     * @return array
     */
    private function migrateAndSeed()
    {
        $outputLog = new BufferedOutput;

        $this->sqlite($outputLog);

        return $this->migrate($outputLog);
    }

    /**
     * Run the migration and call the seeder.
     *
     * @param \Symfony\Component\Console\Output\BufferedOutput $outputLog
     * @return array
     */
    private function migrate(BufferedOutput $outputLog)
    {
        try {
            Artisan::call('migrate', ['--force'=> true], $outputLog);
        } catch (Exception $e) {
            return $this->response($e->getMessage(), 'error', $outputLog);
        }

        return $this->seed($outputLog);
    }

    /**
     * Seed the database.
     *
     * @param \Symfony\Component\Console\Output\BufferedOutput $outputLog
     * @return array
     */
    private function seed(BufferedOutput $outputLog)
    {
         try {
            Artisan::call('db:seed', ['--force' => true], $outputLog);
        } catch (Exception $e) {
            return $this->response($e->getMessage(), 'error', $outputLog);
        }

        return $this->response('Application has been successfully installed.', 'success', $outputLog);
    }

    /**
     * Return a formatted error messages.
     *
     * @param string $message
     * @param string $status
     * @param \Symfony\Component\Console\Output\BufferedOutput $outputLog
     * @return array
     */
    private function response($message, $status, BufferedOutput $outputLog)
    {
        return [
            'status' => $status,
            'message' => $message,
            'dbOutputLog' => $outputLog->fetch(),
        ];
    }

    /**
     * Check database type. If SQLite, then create the database file.
     *
     * @param \Symfony\Component\Console\Output\BufferedOutput $outputLog
     */
    private function sqlite(BufferedOutput $outputLog)
    {
        if (DB::connection() instanceof SQLiteConnection) {
            $database = DB::connection()->getDatabaseName();
            if (! file_exists($database)) {
                touch($database);
                DB::reconnect(Config::get('database.default'));
            }
            $outputLog->write('Using SqlLite database: '.$database, 1);
        }
    }

    /**
     * Checks database connectivity
     * 
     * @param Request $request
     * @return bool
     */
    private function checkDatabaseConnection(Request $request)
    {
        $connection = $request->input('database_connection');

        $settings = config("database.connections.$connection");

        config([
            'database' => [
                'default' => $connection,
                'connections' => [
                    $connection => array_merge($settings, [
                        'driver' => $connection,
                        'host' => $request->input('database_hostname'),
                        'port' => $request->input('database_port'),
                        'database' => $request->input('database_name'),
                        'username' => $request->input('database_username'),
                        'password' => $request->input('database_password'),
                    ]),
                ],
            ],
        ]);

        DB::purge();

        try {
            DB::connection()->getPdo();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check file permissions
     * 
     *  @param array $files
     * @return array
     * */
    private function checkPermission($files)
    {
        $results = [
            'permissions' => [],
            'errors' => false
        ];

        foreach ($files as $folder => $permission) {
            if (!substr(sprintf('%o', fileperms(base_path($folder))), -4)) {
                array_push($results['permissions'], [
                    'folder' => $folder,
                    'permission' => $permission,
                    'isSet' => false
                ]);
                $results['errors'] = true;
            }else{
                array_push($results['permissions'], [
                    'folder' => $folder,
                    'permission' => $permission,
                    'isSet' => true
                ]);
            }
        }

        return $results;
    }

    /**
     * Check php version 
     * 
     * @return array
     */
    private function checkPhpVersion($_minPhpVersion = null)
    {
        $minPhpVersion = $_minPhpVersion;
        $currentPhp = PHP_VERSION;
        $supported = false;

        if ($minPhpVersion == null) {
            $minPhpVersion = $this->getMinPhpVersion();
        }

        if(version_compare($currentPhp, $minPhpVersion) >= 0){
            $supported = true;
        }

        $phpStatus = [
            'minimum' => $minPhpVersion,
            'current' => $currentPhp,
            'supported' => $supported
        ];

        return $phpStatus;
    }

    /**
     * Check for server requirements
     * 
     * @param array  $requirements
     * @return array 
     */
    private function checkRequirements($requirements)
    {
        $results = [];

        foreach ($requirements as $type => $requirement) {
            switch ($type) {
                case 'php':
                    foreach($requirements[$type] as $requirement){
                        $results['requirements'][$type][$requirement] = true;

                        if (!extension_loaded($requirement)) {
                             $results['requirements'][$type][$requirement] = false;
                             $results['errors'] = true;
                        }
                    }
                    break;
                case 'apache':
                    foreach ($requirements[$type] as $requirement) {
                        // if function doesn't exist we can't check apache modules
                        if (function_exists('apache_get_modules')) {
                            $results['requirements'][$type][$requirement] = true;

                            if (! in_array($requirement, apache_get_modules())) {
                                $results['requirements'][$type][$requirement] = false;

                                $results['errors'] = true;
                            }
                        }
                    }
                    break;
            }
        }

        return $results;
    }

    /**
     * Get minimum PHP version ID.
     *
     * @return string minPhpVersion
     */
    protected function getMinPhpVersion()
    {
        return $this->minPhpVersion;
    }
}
