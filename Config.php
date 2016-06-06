<?php

defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));
defined('STDOUT') or define('STDOUT', fopen('php://STDOUT', 'w'));

class Config
{
    protected $config = [
        'Connection Class' => 'yii\db\Connection',
        'host' => '127.0.0.1',
        'port' => 3306,
        'username' => 'root',
        'password' => 'root',
        'charset' => 'UTF8',
        'dbName' => null,
        'tablePrefix' => '',
    ];

    public function __construct(&$config)
    {
        if (!isset($config['components']['db'])) {
            $this->say('You haven\'t configured your database yet.');
            $this->config();
            $this->confirm();
            $this->writeConfig();
            $this->say([
                'Configure file generated !',
            ]);
        }
    }

    protected function validate($attributes = null)
    {
        is_string($attributes) && $attributes = [$attributes];
        if (in_array('dbName', $attributes) && !preg_match('/\w+/', $this->config['dbName'])) {
            $this->config['dbName'] = $this->standardIn('Invalid database name(dbName), it should start with `a-z` or `_`, followed by such, or number, please enter again:');
            $this->validate('dbName');
        }
    }

    protected function writeConfig()
    {
        $handle = fopen(__APP__ . '/config/db.php', 'w');
        $configArray = [
            'class' => $this->config['Connection Class'],
            'dsn' => "mysql:host={$this->config['host']}:{$this->config['port']};dbname={$this->config['dbName']}",
            'username' => $this->config['username'],
            'password' => $this->config['password'],
            'tablePrefix' => $this->config['tablePrefix'],
            'charset' => $this->config['charset'],
        ];
        $config = "<?php\n\n";
        $config .= "return [\n";
        foreach ($configArray as $k => &$v) {
            $v = is_numeric($v) || is_bool($v) ? $v : "'$v'";
            $config .= "    '$k' => $v,\n";
        }
        $config .= "];\n";
        fwrite($handle, $config);
        fclose($handle);
    }

    public function config()
    {
        array_walk($this->config, function (&$v, $k) {
            $this->enter($k);
            $this->validate($k);
        });
    }

    protected function confirm()
    {
        $configString = "Your database configure are as follows:\n";
        foreach ($this->config as $k => &$v) {
            $configString .= "  $k\t= $v\n";
        };
        $this->say(['', $configString]);
        $this->radio();
    }

    protected function radio()
    {
        $confirmed = strtolower($this->standardIn('Confirmed ?[y/n/q]:'));
        if (in_array($confirmed, ['no', 'n'])) {
            $this->say(['', '', 'Please enter your database again.']);
            $this->config();
            $this->confirm();
        } else if (in_array($confirmed, ['quit', 'q'])) {
            $this->say('quit.');
        } else if (!in_array($confirmed, ['yes', 'y'])) {
            $this->say('Invalid input, Please choose again');
            $this->radio();
        }
    }

    protected function enter($property)
    {
        $defaultValue = $this->config[$property];
        $this->config[$property] = $this->standardIn("Please enter your database {$property}[$defaultValue]:") ?: $defaultValue;
    }

    protected function standardIn($lines = null)
    {
        $lines === null || $this->say($lines);
        return trim(fgets(STDIN));
    }

    protected function say($lines)
    {
        is_array($lines) || $lines = [$lines];
        array_walk($lines, function (&$v) {
            echo '> ';
            echo $v;
            echo substr($v, -1) === ':' ? ' ' : "\n";
        });
    }

}