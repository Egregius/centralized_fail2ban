<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width,height=device-height, user-scalable=yes, minimal-ui" />
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<link href="terminals/style.php" rel="stylesheet" type="text/css"/>
		<link rel="stylesheet" type="text/css" href="/css/style.css"/>
		<style>
			table.pretty{clear:both;font:100%/100% verdana, Helvetica, sans-serif;}
		</style>
		<title>Blocked IP addressess</title>
		<script type="text/javascript" language="javascript" src="/js/jQuery.js"></script>
		<script type="text/javascript" language="javascript" src="/js/jQuery.dataTables.min.js"></script>
		<script type="text/javascript" language="javascript" src="/js/jQuery.dataTables.columnFilter.js"></script>
		<script type="text/javascript" charset="utf-8">
			var asInitVals=new Array();
			$(document).ready(function(){
				$('#table').dataTable({
					"bPaginate":false,
					"bStateSave":true,
					"bSort":true,
					"order": [[ 1, "desc" ]]
				});
			});
			
		</script>
	</head>
	<body>
		<div id="content" style="min-width:400px;margin:0 auto;">	
<?php
$dbuser='mysqluser';
$dbpass='3SiJ67PgGSg6BUjbgtajy3N2mv3Jpt4KqfHSpNVA8PQLPVwVkJ';
$db=new PDO("mysql:host=localhost",$dbuser,$dbpass);
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
$total=0;
$totalcost=0;
$x=0;
if (isset($_POST['unblock'])) {
	$array=explode('.', $_POST['unblock']);
	$ip1=$array[0];
	$ip2=$array[1];
	$ip3=$array[2];
	$ip4=$array[3];
	$stmt=$db->query("DELETE FROM `fail2ban`.`fail2ban` WHERE `1` = $ip1 AND `2` = $ip2 AND `3` = $ip3 AND `4` = $ip4");
	$stmt->execute();
	create_txt();
}
	$stmt=$db->query("SELECT `1`, `2`, `3`, `4`, `stamp` FROM `fail2ban`.`fail2ban` ORDER BY 1, 2, 3, 4");
	while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) $data[]=$row;
	echo '
	<div class="centerdiv center bg-grayWhite rounded drop-shadow" style="padding:1rem;width:1000px;">
		<table class="big pretty" id="table">
		<thead>
			<tr>
				<th width="200px">IP Address</th>
				<th width="200px">Stamp</th>
				<th width="150px">Unblock</th>
			</tr>
		</thead>
		<tbody>';
	if (!empty($data)) {
		foreach ($data as $c) {
				echo '
				<tr>
					<td nowrap>'.$c['1'].'.'.$c['2'].'.'.$c['3'].'.'.$c['4'].'</td>
					<td nowrap>'.$c['stamp'].'</td>
					<td><form method="POST"><button type="submit" name="unblock" value="'.$c['1'].'.'.$c['2'].'.'.$c['3'].'.'.$c['4'].'">unblock</button></form></td>
				</tr>';
			$x++;
		}
	}
	echo '
			
		</tbody>
		<tfoot>
			<tr>
				<td>Count</td>
				<td>'.$x.'</td>
			</tr>
		</tfoot>
	</table>';
function create_txt() {
	global $db;
	$html=null;
	$stmt=$db->query("SELECT `1`, `2`, `3`, `4` from fail2ban order by `1`, `2`, `3`, `4`");
	while ($i=$stmt->fetch(PDO::FETCH_ASSOC)) {
		$html.=$i['1'].'.'.$i['2'].'.'.$i['3'].'.'.$i['4'].'/32
';	
	}
	header('Content-Type:text/plain');
	//header('Content-Length: ' . strlen($html));
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	file_put_contents('/temp/badips.txt', $html);
	shell_exec('/usr/bin/aggregate </temp/badips.txt >/var/www/mydomain.be/badips.txt');
        $URI = $_SERVER['REQUEST_URI'];
        header("location:$URI");
}
