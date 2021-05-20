<?php
$index = hash('md5', 'Indexkod: ' . (new DateTime('NOW', new DateTimeZone('Europe/Stockholm')))->format('Ymd'));
if(!isset($_POST[$index]))
	exit();
$code = hash('sha512', "Teckenpedagogerna Video Server säkerhetskod: " . (new DateTime('NOW', new DateTimeZone('Europe/Stockholm')))->format('"Y/H-d m"'));

function ip()
{
	foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key)
	{
		if (array_key_exists($key, $_SERVER) === true)
		{
			foreach (explode(',', $_SERVER[$key]) as $ip)
			{
				$ip = trim($ip);
				if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false)
				{
					return $ip;
				}
			}
		}
	}
}

if($code == $_POST[$index])
{
	$ip = ip();
	file_put_contents("/var/www/local/data/Text/DataServerIP.txt", $ip);
	file_put_contents("/var/www/local/data/Text/DataServerIP_NGINX.txt",
		"location /data/ { proxy_pass http://$ip/data/; }"
	);
}
