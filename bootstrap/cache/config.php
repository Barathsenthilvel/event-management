<?php return array (
  'broadcasting' => 
  array (
    'default' => 'log',
    'connections' => 
    array (
      'reverb' => 
      array (
        'driver' => 'reverb',
        'key' => NULL,
        'secret' => NULL,
        'app_id' => NULL,
        'options' => 
        array (
          'host' => NULL,
          'port' => 443,
          'scheme' => 'https',
          'useTLS' => true,
        ),
        'client_options' => 
        array (
        ),
      ),
      'pusher' => 
      array (
        'driver' => 'pusher',
        'key' => NULL,
        'secret' => NULL,
        'app_id' => NULL,
        'options' => 
        array (
          'cluster' => NULL,
          'host' => 'api-mt1.pusher.com',
          'port' => 443,
          'scheme' => 'https',
          'encrypted' => true,
          'useTLS' => true,
        ),
        'client_options' => 
        array (
        ),
      ),
      'ably' => 
      array (
        'driver' => 'ably',
        'key' => NULL,
      ),
      'log' => 
      array (
        'driver' => 'log',
      ),
      'null' => 
      array (
        'driver' => 'null',
      ),
    ),
  ),
  'concurrency' => 
  array (
    'default' => 'process',
  ),
  'cors' => 
  array (
    'paths' => 
    array (
      0 => 'api/*',
      1 => 'sanctum/csrf-cookie',
    ),
    'allowed_methods' => 
    array (
      0 => '*',
    ),
    'allowed_origins' => 
    array (
      0 => '*',
    ),
    'allowed_origins_patterns' => 
    array (
    ),
    'allowed_headers' => 
    array (
      0 => '*',
    ),
    'exposed_headers' => 
    array (
    ),
    'max_age' => 0,
    'supports_credentials' => false,
  ),
  'hashing' => 
  array (
    'driver' => 'bcrypt',
    'bcrypt' => 
    array (
      'rounds' => '12',
      'verify' => true,
      'limit' => NULL,
    ),
    'argon' => 
    array (
      'memory' => 65536,
      'threads' => 1,
      'time' => 4,
      'verify' => true,
    ),
    'rehash_on_login' => true,
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => 'C:\\Users\\acer\\OneDrive\\Desktop\\projects\\event-management\\resources\\views',
    ),
    'compiled' => 'C:\\Users\\acer\\OneDrive\\Desktop\\projects\\event-management\\storage\\framework\\views',
  ),
  'app' => 
  array (
    'name' => 'Laravel',
    'env' => 'local',
    'debug' => true,
    'url' => 'http://localhost',
    'frontend_url' => 'http://localhost:3000',
    'asset_url' => NULL,
    'timezone' => 'Asia/Kolkata',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'cipher' => 'AES-256-CBC',
    'key' => 'base64:VPF+wIDVdyFMwBWZkIvO6HhGCuMJ7R+Yp1DmqhQGyHY=',
    'previous_keys' => 
    array (
    ),
    'maintenance' => 
    array (
      'driver' => 'file',
      'store' => 'database',
    ),
    'providers' => 
    array (
      0 => 'Illuminate\\Auth\\AuthServiceProvider',
      1 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
      2 => 'Illuminate\\Bus\\BusServiceProvider',
      3 => 'Illuminate\\Cache\\CacheServiceProvider',
      4 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
      5 => 'Illuminate\\Concurrency\\ConcurrencyServiceProvider',
      6 => 'Illuminate\\Cookie\\CookieServiceProvider',
      7 => 'Illuminate\\Database\\DatabaseServiceProvider',
      8 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
      9 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
      10 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
      11 => 'Illuminate\\Hashing\\HashServiceProvider',
      12 => 'Illuminate\\Mail\\MailServiceProvider',
      13 => 'Illuminate\\Notifications\\NotificationServiceProvider',
      14 => 'Illuminate\\Pagination\\PaginationServiceProvider',
      15 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
      16 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
      17 => 'Illuminate\\Queue\\QueueServiceProvider',
      18 => 'Illuminate\\Redis\\RedisServiceProvider',
      19 => 'Illuminate\\Session\\SessionServiceProvider',
      20 => 'Illuminate\\Translation\\TranslationServiceProvider',
      21 => 'Illuminate\\Validation\\ValidationServiceProvider',
      22 => 'Illuminate\\View\\ViewServiceProvider',
      23 => 'App\\Providers\\AppServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Illuminate\\Support\\Facades\\App',
      'Arr' => 'Illuminate\\Support\\Arr',
      'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
      'Auth' => 'Illuminate\\Support\\Facades\\Auth',
      'Benchmark' => 'Illuminate\\Support\\Benchmark',
      'Blade' => 'Illuminate\\Support\\Facades\\Blade',
      'Broadcast' => 'Illuminate\\Support\\Facades\\Broadcast',
      'Bus' => 'Illuminate\\Support\\Facades\\Bus',
      'Cache' => 'Illuminate\\Support\\Facades\\Cache',
      'Concurrency' => 'Illuminate\\Support\\Facades\\Concurrency',
      'Config' => 'Illuminate\\Support\\Facades\\Config',
      'Context' => 'Illuminate\\Support\\Facades\\Context',
      'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
      'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
      'Date' => 'Illuminate\\Support\\Facades\\Date',
      'DB' => 'Illuminate\\Support\\Facades\\DB',
      'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
      'Event' => 'Illuminate\\Support\\Facades\\Event',
      'File' => 'Illuminate\\Support\\Facades\\File',
      'Gate' => 'Illuminate\\Support\\Facades\\Gate',
      'Hash' => 'Illuminate\\Support\\Facades\\Hash',
      'Http' => 'Illuminate\\Support\\Facades\\Http',
      'Js' => 'Illuminate\\Support\\Js',
      'Lang' => 'Illuminate\\Support\\Facades\\Lang',
      'Log' => 'Illuminate\\Support\\Facades\\Log',
      'Mail' => 'Illuminate\\Support\\Facades\\Mail',
      'Notification' => 'Illuminate\\Support\\Facades\\Notification',
      'Number' => 'Illuminate\\Support\\Number',
      'Password' => 'Illuminate\\Support\\Facades\\Password',
      'Process' => 'Illuminate\\Support\\Facades\\Process',
      'Queue' => 'Illuminate\\Support\\Facades\\Queue',
      'RateLimiter' => 'Illuminate\\Support\\Facades\\RateLimiter',
      'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
      'Request' => 'Illuminate\\Support\\Facades\\Request',
      'Response' => 'Illuminate\\Support\\Facades\\Response',
      'Route' => 'Illuminate\\Support\\Facades\\Route',
      'Schedule' => 'Illuminate\\Support\\Facades\\Schedule',
      'Schema' => 'Illuminate\\Support\\Facades\\Schema',
      'Session' => 'Illuminate\\Support\\Facades\\Session',
      'Storage' => 'Illuminate\\Support\\Facades\\Storage',
      'Str' => 'Illuminate\\Support\\Str',
      'Uri' => 'Illuminate\\Support\\Uri',
      'URL' => 'Illuminate\\Support\\Facades\\URL',
      'Validator' => 'Illuminate\\Support\\Facades\\Validator',
      'View' => 'Illuminate\\Support\\Facades\\View',
      'Vite' => 'Illuminate\\Support\\Facades\\Vite',
    ),
  ),
  'auth' => 
  array (
    'defaults' => 
    array (
      'guard' => 'web',
      'passwords' => 'users',
    ),
    'guards' => 
    array (
      'web' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'admin' => 
      array (
        'driver' => 'session',
        'provider' => 'admins',
      ),
    ),
    'providers' => 
    array (
      'users' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Models\\User',
      ),
      'admins' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Models\\Admin',
      ),
    ),
    'passwords' => 
    array (
      'users' => 
      array (
        'provider' => 'users',
        'table' => 'password_reset_tokens',
        'expire' => 60,
        'throttle' => 60,
      ),
    ),
    'password_timeout' => 10800,
  ),
  'cache' => 
  array (
    'default' => 'database',
    'stores' => 
    array (
      'array' => 
      array (
        'driver' => 'array',
        'serialize' => false,
      ),
      'session' => 
      array (
        'driver' => 'session',
        'key' => '_cache',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'connection' => NULL,
        'table' => 'cache',
        'lock_connection' => NULL,
        'lock_table' => NULL,
      ),
      'file' => 
      array (
        'driver' => 'file',
        'path' => 'C:\\Users\\acer\\OneDrive\\Desktop\\projects\\event-management\\storage\\framework/cache/data',
        'lock_path' => 'C:\\Users\\acer\\OneDrive\\Desktop\\projects\\event-management\\storage\\framework/cache/data',
      ),
      'memcached' => 
      array (
        'driver' => 'memcached',
        'persistent_id' => NULL,
        'sasl' => 
        array (
          0 => NULL,
          1 => NULL,
        ),
        'options' => 
        array (
        ),
        'servers' => 
        array (
          0 => 
          array (
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
          ),
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
      ),
      'dynamodb' => 
      array (
        'driver' => 'dynamodb',
        'key' => '',
        'secret' => '',
        'region' => 'us-east-1',
        'table' => 'cache',
        'endpoint' => NULL,
      ),
      'octane' => 
      array (
        'driver' => 'octane',
      ),
      'failover' => 
      array (
        'driver' => 'failover',
        'stores' => 
        array (
          0 => 'database',
          1 => 'array',
        ),
      ),
    ),
    'prefix' => 'laravel-cache-',
  ),
  'countries_currencies' => 
  array (
    'countries' => 
    array (
      0 => 
      array (
        'code' => 'US',
        'name' => 'United States',
        'currency' => 'USD',
      ),
      1 => 
      array (
        'code' => 'GB',
        'name' => 'United Kingdom',
        'currency' => 'GBP',
      ),
      2 => 
      array (
        'code' => 'IN',
        'name' => 'India',
        'currency' => 'INR',
      ),
      3 => 
      array (
        'code' => 'AU',
        'name' => 'Australia',
        'currency' => 'AUD',
      ),
      4 => 
      array (
        'code' => 'CA',
        'name' => 'Canada',
        'currency' => 'CAD',
      ),
      5 => 
      array (
        'code' => 'DE',
        'name' => 'Germany',
        'currency' => 'EUR',
      ),
      6 => 
      array (
        'code' => 'FR',
        'name' => 'France',
        'currency' => 'EUR',
      ),
      7 => 
      array (
        'code' => 'IT',
        'name' => 'Italy',
        'currency' => 'EUR',
      ),
      8 => 
      array (
        'code' => 'ES',
        'name' => 'Spain',
        'currency' => 'EUR',
      ),
      9 => 
      array (
        'code' => 'NL',
        'name' => 'Netherlands',
        'currency' => 'EUR',
      ),
      10 => 
      array (
        'code' => 'JP',
        'name' => 'Japan',
        'currency' => 'JPY',
      ),
      11 => 
      array (
        'code' => 'CN',
        'name' => 'China',
        'currency' => 'CNY',
      ),
      12 => 
      array (
        'code' => 'SG',
        'name' => 'Singapore',
        'currency' => 'SGD',
      ),
      13 => 
      array (
        'code' => 'AE',
        'name' => 'United Arab Emirates',
        'currency' => 'AED',
      ),
      14 => 
      array (
        'code' => 'SA',
        'name' => 'Saudi Arabia',
        'currency' => 'SAR',
      ),
      15 => 
      array (
        'code' => 'BR',
        'name' => 'Brazil',
        'currency' => 'BRL',
      ),
      16 => 
      array (
        'code' => 'MX',
        'name' => 'Mexico',
        'currency' => 'MXN',
      ),
      17 => 
      array (
        'code' => 'ZA',
        'name' => 'South Africa',
        'currency' => 'ZAR',
      ),
      18 => 
      array (
        'code' => 'KR',
        'name' => 'South Korea',
        'currency' => 'KRW',
      ),
      19 => 
      array (
        'code' => 'HK',
        'name' => 'Hong Kong',
        'currency' => 'HKD',
      ),
      20 => 
      array (
        'code' => 'MY',
        'name' => 'Malaysia',
        'currency' => 'MYR',
      ),
      21 => 
      array (
        'code' => 'TH',
        'name' => 'Thailand',
        'currency' => 'THB',
      ),
      22 => 
      array (
        'code' => 'PK',
        'name' => 'Pakistan',
        'currency' => 'PKR',
      ),
      23 => 
      array (
        'code' => 'BD',
        'name' => 'Bangladesh',
        'currency' => 'BDT',
      ),
      24 => 
      array (
        'code' => 'EG',
        'name' => 'Egypt',
        'currency' => 'EGP',
      ),
      25 => 
      array (
        'code' => 'NG',
        'name' => 'Nigeria',
        'currency' => 'NGN',
      ),
      26 => 
      array (
        'code' => 'KE',
        'name' => 'Kenya',
        'currency' => 'KES',
      ),
      27 => 
      array (
        'code' => 'PH',
        'name' => 'Philippines',
        'currency' => 'PHP',
      ),
      28 => 
      array (
        'code' => 'ID',
        'name' => 'Indonesia',
        'currency' => 'IDR',
      ),
      29 => 
      array (
        'code' => 'VN',
        'name' => 'Vietnam',
        'currency' => 'VND',
      ),
      30 => 
      array (
        'code' => 'PL',
        'name' => 'Poland',
        'currency' => 'PLN',
      ),
      31 => 
      array (
        'code' => 'SE',
        'name' => 'Sweden',
        'currency' => 'SEK',
      ),
      32 => 
      array (
        'code' => 'CH',
        'name' => 'Switzerland',
        'currency' => 'CHF',
      ),
      33 => 
      array (
        'code' => 'RU',
        'name' => 'Russia',
        'currency' => 'RUB',
      ),
      34 => 
      array (
        'code' => 'TR',
        'name' => 'Turkey',
        'currency' => 'TRY',
      ),
      35 => 
      array (
        'code' => 'AR',
        'name' => 'Argentina',
        'currency' => 'ARS',
      ),
      36 => 
      array (
        'code' => 'CL',
        'name' => 'Chile',
        'currency' => 'CLP',
      ),
      37 => 
      array (
        'code' => 'CO',
        'name' => 'Colombia',
        'currency' => 'COP',
      ),
      38 => 
      array (
        'code' => 'NZ',
        'name' => 'New Zealand',
        'currency' => 'NZD',
      ),
    ),
    'currencies' => 
    array (
      'USD' => 'US Dollar',
      'GBP' => 'British Pound',
      'EUR' => 'Euro',
      'INR' => 'Indian Rupee',
      'AUD' => 'Australian Dollar',
      'CAD' => 'Canadian Dollar',
      'JPY' => 'Japanese Yen',
      'CNY' => 'Chinese Yuan',
      'SGD' => 'Singapore Dollar',
      'AED' => 'UAE Dirham',
      'SAR' => 'Saudi Riyal',
      'BRL' => 'Brazilian Real',
      'MXN' => 'Mexican Peso',
      'ZAR' => 'South African Rand',
      'KRW' => 'South Korean Won',
      'HKD' => 'Hong Kong Dollar',
      'MYR' => 'Malaysian Ringgit',
      'THB' => 'Thai Baht',
      'PKR' => 'Pakistani Rupee',
      'BDT' => 'Bangladeshi Taka',
      'EGP' => 'Egyptian Pound',
      'NGN' => 'Nigerian Naira',
      'KES' => 'Kenyan Shilling',
      'PHP' => 'Philippine Peso',
      'IDR' => 'Indonesian Rupiah',
      'VND' => 'Vietnamese Dong',
      'PLN' => 'Polish Zloty',
      'SEK' => 'Swedish Krona',
      'CHF' => 'Swiss Franc',
      'RUB' => 'Russian Ruble',
      'TRY' => 'Turkish Lira',
      'ARS' => 'Argentine Peso',
      'CLP' => 'Chilean Peso',
      'COP' => 'Colombian Peso',
      'NZD' => 'New Zealand Dollar',
    ),
    'time_formats' => 
    array (
      '24h' => '24-hour (e.g. 14:30)',
      '12h' => '12-hour AM/PM (e.g. 2:30 PM)',
      '24h_sec' => '24-hour with seconds (e.g. 14:30:00)',
      '12h_sec' => '12-hour with seconds (e.g. 2:30:00 PM)',
    ),
  ),
  'database' => 
  array (
    'default' => 'mysql',
    'connections' => 
    array (
      'sqlite' => 
      array (
        'driver' => 'sqlite',
        'url' => NULL,
        'database' => 'gnat',
        'prefix' => '',
        'foreign_key_constraints' => true,
        'busy_timeout' => NULL,
        'journal_mode' => NULL,
        'synchronous' => NULL,
        'transaction_mode' => 'DEFERRED',
      ),
      'mysql' => 
      array (
        'driver' => 'mysql',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'gnat',
        'username' => 'root',
        'password' => '',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => NULL,
        'options' => 
        array (
        ),
      ),
      'mariadb' => 
      array (
        'driver' => 'mariadb',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'gnat',
        'username' => 'root',
        'password' => '',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => NULL,
        'options' => 
        array (
        ),
      ),
      'pgsql' => 
      array (
        'driver' => 'pgsql',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'gnat',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => 'public',
        'sslmode' => 'prefer',
      ),
      'sqlsrv' => 
      array (
        'driver' => 'sqlsrv',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'gnat',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
      ),
    ),
    'migrations' => 
    array (
      'table' => 'migrations',
      'update_date_on_publish' => true,
    ),
    'redis' => 
    array (
      'client' => 'phpredis',
      'options' => 
      array (
        'cluster' => 'redis',
        'prefix' => 'laravel-database-',
        'persistent' => false,
      ),
      'default' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'username' => NULL,
        'password' => NULL,
        'port' => '6379',
        'database' => '0',
        'max_retries' => 3,
        'backoff_algorithm' => 'decorrelated_jitter',
        'backoff_base' => 100,
        'backoff_cap' => 1000,
      ),
      'cache' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'username' => NULL,
        'password' => NULL,
        'port' => '6379',
        'database' => '1',
        'max_retries' => 3,
        'backoff_algorithm' => 'decorrelated_jitter',
        'backoff_base' => 100,
        'backoff_cap' => 1000,
      ),
    ),
  ),
  'filesystems' => 
  array (
    'default' => 'local',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => 'C:\\Users\\acer\\OneDrive\\Desktop\\projects\\event-management\\storage\\app/private',
        'serve' => true,
        'throw' => false,
        'report' => false,
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => 'C:\\Users\\acer\\OneDrive\\Desktop\\projects\\event-management\\storage\\app/public',
        'url' => 'http://localhost/storage',
        'visibility' => 'public',
        'throw' => false,
        'report' => false,
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => '',
        'secret' => '',
        'region' => 'us-east-1',
        'bucket' => '',
        'url' => NULL,
        'endpoint' => NULL,
        'use_path_style_endpoint' => false,
        'throw' => false,
        'report' => false,
      ),
    ),
    'links' => 
    array (
      'C:\\Users\\acer\\OneDrive\\Desktop\\projects\\event-management\\public\\storage' => 'C:\\Users\\acer\\OneDrive\\Desktop\\projects\\event-management\\storage\\app/public',
    ),
  ),
  'homepage' => 
  array (
    'title' => 'GNAT Association — Give Hope, Support Communities',
    'logo' => 
    array (
      'src' => 'images/logo.png',
      'alt' => 'GNAT Association',
    ),
    'contact' => 
    array (
      'email' => 'info@gnatdonation.org',
      'address' => 'No. 36/76, Thiruveethi Amman Kovil 2nd Street, Aminjikarai, Chennai 600029',
      'phones' => 
      array (
        0 => 
        array (
          'tel' => '+918148510006',
          'label' => '+91 81485 10006',
        ),
        1 => 
        array (
          'tel' => '+919629319978',
          'label' => '+91 96293 19978',
        ),
      ),
      'maps_query' => 'No+36%2F76+Thiruveethi+Amman+Kovil+2nd+Street+Aminjikarai+Chennai+600029',
    ),
    'nav' => 
    array (
      0 => 
      array (
        'label' => 'Home',
        'href' => '#home',
      ),
      1 => 
      array (
        'label' => 'Activity',
        'href' => '#association-activity',
      ),
      2 => 
      array (
        'label' => 'About Us',
        'href' => '#about2',
      ),
      3 => 
      array (
        'label' => 'Events',
        'href' => '#events',
      ),
      4 => 
      array (
        'label' => 'Blog',
        'href' => '#blog',
      ),
      5 => 
      array (
        'label' => 'Gallery',
        'href' => '#gallery',
      ),
      6 => 
      array (
        'label' => 'Jobs',
        'href' => '#jobs',
      ),
      7 => 
      array (
        'label' => 'Contact Us',
        'href' => '#contact',
      ),
    ),
    'hero' => 
    array (
      'badge' => 'GNAT ASSOCIATION',
      'headline_line1' => 'Give Hope.',
      'headline_line2' => 'Grow Stronger Communities',
      'description_html' => '<strong class="text-white">GNAT Association</strong> brings people together to create meaningful impact across communities.',
      'registered_count' => 2603,
      'registered_label' => 'Peoples Registered',
      'avatar_image' => 'images/testimonials-images/thumb-10.2.webp',
    ),
    'volunteer_cards' => 
    array (
      0 => 
      array (
        'title' => 'Community Impact',
        'bullets' => 
        array (
          0 => 'Support real people with hands-on help.',
          1 => 'Build stronger neighborhoods through teamwork.',
          2 => 'Turn ideas into measurable outcomes.',
        ),
      ),
      1 => 
      array (
        'title' => 'Skill Growth',
        'bullets' => 
        array (
          0 => 'Learn through workshops and mentorship.',
          1 => 'Get hands-on experience with real tasks.',
          2 => 'Improve communication and leadership.',
        ),
      ),
      2 => 
      array (
        'title' => 'Sustainable Work',
        'bullets' => 
        array (
          0 => 'Help create eco-friendly initiatives.',
          1 => 'Use resources wisely and efficiently.',
          2 => 'Track impact and improve over time.',
        ),
      ),
      3 => 
      array (
        'title' => 'Team Support',
        'bullets' => 
        array (
          0 => 'Work with friendly, motivated teammates.',
          1 => 'Get clear guidance and quick feedback.',
          2 => 'Share ideas and improve together.',
        ),
      ),
      4 => 
      array (
        'title' => 'Leadership Path',
        'bullets' => 
        array (
          0 => 'Take responsibility in real projects.',
          1 => 'Build confidence through guided challenges.',
          2 => 'Grow from volunteer to mentor.',
        ),
      ),
    ),
    'banners' => 
    array (
      0 => 
      array (
        'href' => '#events',
        'src' => 'images/events/event-1-1.jpg',
        'alt' => 'Community education and outreach',
        'eyebrow' => 'EVENTS',
        'title' => 'Together we go further',
        'text' => 'Thank-you gatherings and impact stories from Chennai neighborhoods.',
      ),
      1 => 
      array (
        'href' => '#donate',
        'src' => 'images/events/event-1-2.jpg',
        'alt' => 'Programs and fundraising support',
        'eyebrow' => 'PROGRAMS',
        'title' => 'Support that reaches every family',
        'text' => 'Transparent campaigns and accountable giving across education, health, and community.',
      ),
      2 => 
      array (
        'href' => '#gallery',
        'src' => 'images/events/event-1-3.jpg',
        'alt' => 'Volunteers at community events',
        'eyebrow' => 'COMMUNITY',
        'title' => 'Moments that inspire action',
        'text' => 'Field photos from outreach, learning spaces, and celebrations with people we serve.',
      ),
    ),
    'testimonials_intro' => 
    array (
      'eyebrow' => 'What People Say',
      'title' => 'Trusted Voices & Stories That Matter',
      'text' => 'Hear from our partners and community members about the difference GNAT Association is making. Their experiences reflect real impact across communities.',
    ),
    'testimonials' => 
    array (
      0 => 
      array (
        'name' => 'Penelope Miller',
        'role' => 'Sr. Volunteer',
        'text' => 'GNAT Association made giving simple and transparent. We trusted every step and loved the impact updates.',
        'stars' => 5,
      ),
      1 => 
      array (
        'name' => 'David Johnson',
        'role' => 'Volunteer',
        'text' => 'Great communication, clear deliverables, and a smooth process from start to finish.',
        'stars' => 5,
      ),
      2 => 
      array (
        'name' => 'Sophia Brown',
        'role' => 'Volunteer',
        'text' => 'The team showed real expertise and delivered measurable improvements quickly.',
        'stars' => 5,
      ),
      3 => 
      array (
        'name' => 'Emma Wilson',
        'role' => 'Volunteer',
        'text' => 'From planning to delivery, everything was handled professionally and on time.',
        'stars' => 5,
      ),
      4 => 
      array (
        'name' => 'Michael Adams',
        'role' => 'Client',
        'text' => 'Reliable support and excellent results. The experience was easy and stress-free.',
        'stars' => 4,
      ),
      5 => 
      array (
        'name' => 'Olivia King',
        'role' => 'Client',
        'text' => 'Highly responsive, thoughtful, and proactive. We loved the final outcome.',
        'stars' => 5,
      ),
    ),
    'testimonial_profile_image' => 'images/testimonials-images/thumb-10.2.webp',
    'testimonial_stack_cards' => 
    array (
      0 => 
      array (
        'image' => 'images/testimonials-images/thumb-10.2.webp',
        'quote' => 'GNAT Association made giving simple and transparent. We saw exactly how our support helped the community. Highly recommended!',
        'name' => 'Penelope Miller (Arjon)',
        'role' => 'Sr. Volunteer',
        'rating' => '5.0',
        'play' => true,
      ),
      1 => 
      array (
        'image' => 'images/events/event-1-1.jpg',
        'quote' => 'Great communication, clear deliverables, and a smooth process from start to finish.',
        'name' => 'David Johnson (Brook)',
        'role' => 'Volunteer',
        'rating' => '5.0',
        'play' => false,
      ),
      2 => 
      array (
        'image' => 'images/events/event-1-3.jpg',
        'quote' => 'The team showed real expertise and delivered measurable improvements quickly.',
        'name' => 'Sophia Brown (Karis)',
        'role' => 'Volunteer',
        'rating' => '5.0',
        'play' => false,
      ),
      3 => 
      array (
        'image' => 'images/events/event-1-2.jpg',
        'quote' => 'From planning to delivery, everything was handled professionally and on time.',
        'name' => 'Emma Wilson (Nova)',
        'role' => 'Volunteer',
        'rating' => '5.0',
        'play' => false,
      ),
    ),
    'about' => 
    array (
      'main_image' => 'images/events/event-1-2.jpg',
      'accent_image' => 'images/events/event-1-1.jpg',
      'eyebrow' => 'ABOUT GNAT ASSOCIATION',
      'title_lines' => 
      array (
        0 => 'Worldwide, Our',
        1 => 'Community',
        2 => 'One Mission',
      ),
      'title_highlight' => 'Adopted',
      'text' => 'GNAT Association helps organizations run stronger CSR, grantmaking, and volunteer programs—so support reaches the people who need it most.',
    ),
    'events' => 
    array (
      0 => 
      array (
        'summary_date' => '03 Sep',
        'summary_title' => 'Let\'s Education For Children Get Good Life',
        'time' => '10:00 AM - 2:00 PM',
        'image' => 'images/events/event-1-1.jpg',
        'badge_day' => '03',
        'badge_month' => 'SEP',
        'badge_rounded' => 'rounded-full',
        'description' => 'Dicta Sunt Explicabo. Nemo Enim Ipsam Voluptatem Quia Voluptas Sit Aspernaturaut Odit Aut Fugit, Sed Quia Consequuntur.',
        'organizer' => 'Ashton Porter',
        'venue' => '350 5th Avenue, New York, NY 10118',
        'seat_mode' => 'limited',
        'seat_filled' => 12,
        'seat_limit' => 50,
      ),
      1 => 
      array (
        'summary_date' => '10 Sep',
        'summary_title' => 'Start A Fundraiser For Yourself In World',
        'time' => '10:00 AM - 2:00 PM',
        'image' => 'images/events/event-1-2.jpg',
        'badge_day' => '10',
        'badge_month' => 'SEP',
        'badge_rounded' => 'rounded-xl',
        'description' => 'Practical steps to start your fundraiser, keep momentum, and communicate impact clearly to your community.',
        'organizer' => 'Ashton Porter',
        'venue' => 'Virtual Session (Online)',
        'seat_mode' => 'unlimited',
      ),
      2 => 
      array (
        'summary_date' => '24 Sep',
        'summary_title' => 'Volunteer Training: Communication & Impact',
        'time' => '10:00 AM - 2:00 PM',
        'image' => 'images/events/event-1-3.jpg',
        'badge_day' => '24',
        'badge_month' => 'SEP',
        'badge_rounded' => 'rounded-full',
        'badge_bg' => 'bg-[#ffffff]',
        'description' => 'Learn communication techniques and how to turn volunteer actions into measurable impact.',
        'organizer' => 'Ashton Porter',
        'venue' => '350 5th Avenue, New York, NY 10118',
        'seat_mode' => 'limited',
        'seat_filled' => 8,
        'seat_limit' => 30,
      ),
    ),
    'donate' => 
    array (
      'intro_title' => 'Featured campaigns',
      'intro_kicker' => 'Association',
      'intro_text' => 'Explore active GNAT Association programs—swipe or use the arrows. Every project is designed for transparent, accountable community support.',
      'goal' => 500,
      'default_amount' => 100,
      'bar_percent_demo' => 52,
      'amounts' => 
      array (
        0 => 10,
        1 => 25,
        2 => 50,
        3 => 100,
        4 => 250,
      ),
      'campaigns' => 
      array (
        0 => 
        array (
          'image' => 'images/events/event-1-2.jpg',
          'alt' => 'Child health program',
          'title' => 'Promoting the rights of every child',
          'excerpt' => 'Healthcare, education, and safe spaces for children in our communities.',
        ),
        1 => 
        array (
          'image' => 'images/events/event-1-1.jpg',
          'alt' => 'Community children',
          'title' => 'There are many ways you can help today',
          'excerpt' => 'Join workshops, fundraisers, and outreach programs that change lives.',
        ),
        2 => 
        array (
          'image' => 'images/events/event-1-3.jpg',
          'alt' => 'Volunteer event',
          'title' => 'Clean water & nutrition programs',
          'excerpt' => 'Your gift funds sustainable access to essentials for families in need.',
        ),
        3 => 
        array (
          'image' => 'images/events/event-1-2.jpg',
          'alt' => 'Youth support',
          'title' => 'Youth mentorship & skills training',
          'excerpt' => 'Building brighter futures through coaching, courses, and community.',
        ),
      ),
    ),
    'services' => 
    array (
      0 => 
      array (
        'num' => '01',
        'label' => 'Quick Fundraising',
      ),
      1 => 
      array (
        'num' => '02',
        'label' => 'School & Education Support',
      ),
      2 => 
      array (
        'num' => '03',
        'label' => 'Medical Treatment',
      ),
      3 => 
      array (
        'num' => '04',
        'label' => 'Careers & opportunities',
        'href' => '#jobs',
      ),
      4 => 
      array (
        'num' => '05',
        'label' => 'Job openings & applications',
        'href' => '#jobs',
      ),
      5 => 
      array (
        'num' => '06',
        'label' => 'Fundraising Goals',
      ),
    ),
    'blog' => 
    array (
      'posts' => 
      array (
        0 => 
        array (
          'image' => 'images/events/event-1-2.jpg',
          'tag' => 'Forest',
          'day' => '09',
          'month' => 'Jan',
          'year' => '2026',
          'title' => 'Waste Management',
          'excerpt' => 'Energy consulting involves providing of advice and guidance on energy',
          'comments' => 367,
        ),
        1 => 
        array (
          'image' => 'images/events/event-1-3.jpg',
          'tag' => 'Recycle',
          'day' => '24',
          'month' => 'Feb',
          'year' => '2026',
          'title' => 'Waste Management',
          'excerpt' => 'Energy consulting involves providing of advice and guidance on energy',
          'comments' => 367,
        ),
        2 => 
        array (
          'image' => 'images/events/event-1-1.jpg',
          'tag' => 'Forest',
          'day' => '15',
          'month' => 'Mar',
          'year' => '2026',
          'title' => 'Waste Management',
          'excerpt' => 'Energy consulting involves providing of advice and guidance on energy',
          'comments' => 367,
        ),
        3 => 
        array (
          'image' => 'images/events/event-1-2.jpg',
          'tag' => 'Forest',
          'day' => '29',
          'month' => 'Apr',
          'year' => '2026',
          'title' => 'Waste Management',
          'excerpt' => 'Energy consulting involves providing of advice and guidance on energy',
          'comments' => 367,
        ),
      ),
    ),
    'gallery' => 
    array (
      'filters' => 
      array (
        0 => 
        array (
          'key' => 'all',
          'label' => 'All',
        ),
        1 => 
        array (
          'key' => 'programs',
          'label' => 'Programs',
        ),
        2 => 
        array (
          'key' => 'events',
          'label' => 'Events',
        ),
        3 => 
        array (
          'key' => 'community',
          'label' => 'Community',
        ),
      ),
      'items' => 
      array (
        0 => 
        array (
          'cat' => 'programs',
          'layout' => 'hero',
          'image' => 'images/events/event-1-1.jpg',
          'alt' => 'School and education support program',
          'eyebrow' => 'Programs',
          'title' => 'Learning & school support',
          'text' => 'Books, meals, and safe classrooms for children in Chennai.',
        ),
        1 => 
        array (
          'cat' => 'events',
          'layout' => 'wide',
          'image' => 'images/events/event-1-2.jpg',
          'alt' => 'Fundraising and outreach event',
          'eyebrow' => 'Events',
          'title' => 'Annual drive',
        ),
        2 => 
        array (
          'cat' => 'community',
          'layout' => 'cell',
          'image' => 'images/events/event-1-3.jpg',
          'alt' => 'Community volunteers together',
          'eyebrow' => 'Community',
          'title' => 'Volunteer day',
        ),
        3 => 
        array (
          'cat' => 'programs',
          'layout' => 'cell',
          'image' => 'images/events/event-1-2.jpg',
          'alt' => 'Health and wellness outreach',
          'eyebrow' => 'Programs',
          'title' => 'Health camp',
        ),
        4 => 
        array (
          'cat' => 'events',
          'layout' => 'banner',
          'image' => 'images/events/event-1-1.jpg',
          'alt' => 'Celebration at community event',
          'eyebrow' => 'Events',
          'title' => 'Together we go further',
          'text' => 'Thank-you gatherings and impact stories from Chennai neighborhoods.',
        ),
        5 => 
        array (
          'cat' => 'community',
          'layout' => 'cell',
          'image' => 'images/events/event-1-3.jpg',
          'alt' => 'Children at community program',
          'eyebrow' => 'Community',
          'title' => 'Youth circle',
        ),
        6 => 
        array (
          'cat' => 'programs',
          'layout' => 'cell',
          'image' => 'images/events/event-1-1.jpg',
          'alt' => 'Donation supplies distribution',
          'eyebrow' => 'Programs',
          'title' => 'Relief kits',
        ),
      ),
    ),
    'jobs' => 
    array (
      'eyebrow' => 'Careers',
      'title' => 'Build a Career with Purpose',
      'text' => 'Explore opportunities to work with a team dedicated to strengthening communities. Together, we create lasting impact. Email your résumé to',
    ),
  ),
  'logging' => 
  array (
    'default' => 'stack',
    'deprecations' => 
    array (
      'channel' => NULL,
      'trace' => false,
    ),
    'channels' => 
    array (
      'stack' => 
      array (
        'driver' => 'stack',
        'channels' => 
        array (
          0 => 'single',
        ),
        'ignore_exceptions' => false,
      ),
      'single' => 
      array (
        'driver' => 'single',
        'path' => 'C:\\Users\\acer\\OneDrive\\Desktop\\projects\\event-management\\storage\\logs/laravel.log',
        'level' => 'debug',
        'replace_placeholders' => true,
      ),
      'daily' => 
      array (
        'driver' => 'daily',
        'path' => 'C:\\Users\\acer\\OneDrive\\Desktop\\projects\\event-management\\storage\\logs/laravel.log',
        'level' => 'debug',
        'days' => 14,
        'replace_placeholders' => true,
      ),
      'slack' => 
      array (
        'driver' => 'slack',
        'url' => NULL,
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => 'debug',
        'replace_placeholders' => true,
      ),
      'papertrail' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\SyslogUdpHandler',
        'handler_with' => 
        array (
          'host' => NULL,
          'port' => NULL,
          'connectionString' => 'tls://:',
        ),
        'processors' => 
        array (
          0 => 'Monolog\\Processor\\PsrLogMessageProcessor',
        ),
      ),
      'stderr' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\StreamHandler',
        'handler_with' => 
        array (
          'stream' => 'php://stderr',
        ),
        'formatter' => NULL,
        'processors' => 
        array (
          0 => 'Monolog\\Processor\\PsrLogMessageProcessor',
        ),
      ),
      'syslog' => 
      array (
        'driver' => 'syslog',
        'level' => 'debug',
        'facility' => 8,
        'replace_placeholders' => true,
      ),
      'errorlog' => 
      array (
        'driver' => 'errorlog',
        'level' => 'debug',
        'replace_placeholders' => true,
      ),
      'null' => 
      array (
        'driver' => 'monolog',
        'handler' => 'Monolog\\Handler\\NullHandler',
      ),
      'emergency' => 
      array (
        'path' => 'C:\\Users\\acer\\OneDrive\\Desktop\\projects\\event-management\\storage\\logs/laravel.log',
      ),
    ),
  ),
  'mail' => 
  array (
    'default' => 'log',
    'mailers' => 
    array (
      'smtp' => 
      array (
        'transport' => 'smtp',
        'scheme' => NULL,
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '2525',
        'username' => NULL,
        'password' => NULL,
        'timeout' => NULL,
        'local_domain' => 'localhost',
      ),
      'ses' => 
      array (
        'transport' => 'ses',
      ),
      'postmark' => 
      array (
        'transport' => 'postmark',
      ),
      'resend' => 
      array (
        'transport' => 'resend',
      ),
      'sendmail' => 
      array (
        'transport' => 'sendmail',
        'path' => '/usr/sbin/sendmail -bs -i',
      ),
      'log' => 
      array (
        'transport' => 'log',
        'channel' => NULL,
      ),
      'array' => 
      array (
        'transport' => 'array',
      ),
      'failover' => 
      array (
        'transport' => 'failover',
        'mailers' => 
        array (
          0 => 'smtp',
          1 => 'log',
        ),
        'retry_after' => 60,
      ),
      'roundrobin' => 
      array (
        'transport' => 'roundrobin',
        'mailers' => 
        array (
          0 => 'ses',
          1 => 'postmark',
        ),
        'retry_after' => 60,
      ),
    ),
    'from' => 
    array (
      'address' => 'hello@example.com',
      'name' => 'Laravel',
    ),
    'markdown' => 
    array (
      'theme' => 'default',
      'paths' => 
      array (
        0 => 'C:\\Users\\acer\\OneDrive\\Desktop\\projects\\event-management\\resources\\views/vendor/mail',
      ),
    ),
  ),
  'queue' => 
  array (
    'default' => 'database',
    'connections' => 
    array (
      'sync' => 
      array (
        'driver' => 'sync',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'connection' => NULL,
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
        'after_commit' => false,
      ),
      'beanstalkd' => 
      array (
        'driver' => 'beanstalkd',
        'host' => 'localhost',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => 0,
        'after_commit' => false,
      ),
      'sqs' => 
      array (
        'driver' => 'sqs',
        'key' => '',
        'secret' => '',
        'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
        'queue' => 'default',
        'suffix' => NULL,
        'region' => 'us-east-1',
        'after_commit' => false,
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => NULL,
        'after_commit' => false,
      ),
      'deferred' => 
      array (
        'driver' => 'deferred',
      ),
      'failover' => 
      array (
        'driver' => 'failover',
        'connections' => 
        array (
          0 => 'database',
          1 => 'deferred',
        ),
      ),
      'background' => 
      array (
        'driver' => 'background',
      ),
    ),
    'batching' => 
    array (
      'database' => 'mysql',
      'table' => 'job_batches',
    ),
    'failed' => 
    array (
      'driver' => 'database-uuids',
      'database' => 'mysql',
      'table' => 'failed_jobs',
    ),
  ),
  'services' => 
  array (
    'postmark' => 
    array (
      'key' => NULL,
    ),
    'resend' => 
    array (
      'key' => NULL,
    ),
    'ses' => 
    array (
      'key' => '',
      'secret' => '',
      'region' => 'us-east-1',
    ),
    'slack' => 
    array (
      'notifications' => 
      array (
        'bot_user_oauth_token' => NULL,
        'channel' => NULL,
      ),
    ),
    'razorpay' => 
    array (
      'key' => 'rzp_test_SSZ0gpwvSlbdY5',
      'secret' => '6uDwKzT8cpgwa4TLwYa6l9gA',
    ),
  ),
  'session' => 
  array (
    'driver' => 'database',
    'lifetime' => 120,
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => 'C:\\Users\\acer\\OneDrive\\Desktop\\projects\\event-management\\storage\\framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'laravel-session',
    'path' => '/',
    'domain' => NULL,
    'secure' => NULL,
    'http_only' => true,
    'same_site' => 'lax',
    'partitioned' => false,
  ),
  'tinker' => 
  array (
    'commands' => 
    array (
    ),
    'alias' => 
    array (
    ),
    'dont_alias' => 
    array (
      0 => 'App\\Nova',
    ),
    'trust_project' => 'always',
  ),
);
