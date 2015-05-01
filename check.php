<?php

/**
 * 获得用户的真实IP地址
 *
 * @return  string
 */
function realIp()
{
	if (isset($_SERVER))
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

			/* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
			foreach ($arr AS $ip)
			{
				$ip = trim($ip);

				if ($ip != 'unknown')
				{
					$realip = $ip;

					break;
				}
			}
		}
		elseif (isset($_SERVER['HTTP_CLIENT_IP']))
		{
			$realip = $_SERVER['HTTP_CLIENT_IP'];
		}
		else
		{
			if (isset($_SERVER['REMOTE_ADDR']))
			{
				$realip = $_SERVER['REMOTE_ADDR'];
			}
			else
			{
				$realip = '0.0.0.0';
			}
		}
	}
	else
	{
		if (getenv('HTTP_X_FORWARDED_FOR'))
		{
			$realip = getenv('HTTP_X_FORWARDED_FOR');
		}
		elseif (getenv('HTTP_CLIENT_IP'))
		{
			$realip = getenv('HTTP_CLIENT_IP');
		}
		else
		{
			$realip = getenv('REMOTE_ADDR');
		}
	}

	preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
	$realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

	return $realip;
}

$start = microtime(true);

// 引入库
include("geoip.inc");

// 获得当前 IP
$ip = realIp();
 
// 打开本地数据库, 数据保存在 GeoIP 文件中.
$geoData = geoip_open('GeoIP.dat', GEOIP_STANDARD);
 
// 获取国家 Code
$countryCode = geoip_country_code_by_addr($geoData, $ip);
 
// 获取国家名称
$countryName = geoip_country_name_by_addr($geoData, $ip);
 
// 关闭本地数据库
geoip_close($geoData);

echo "Your Ip: \n";
echo $ip."\n";

echo "Your country: \n";
echo $countryCode."\n";
echo $countryName."\n";

$end = microtime(true);
echo "Waste time: ".($end-$start)." s\n";

?>
