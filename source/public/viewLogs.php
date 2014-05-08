<?php 
//runApp = false;
defined('CONSOLE') || define('CONSOLE', TRUE);
include_once realpath(dirname(__FILE__) . '/index.php');

function getPrefixPos($list){    
    $maxPrefix = strlen($list[0]);
    for($i = 1; $i < count($list); $i++)
    {
        $pos = 0;
        for(; $pos < $maxPrefix && $pos < strlen($list[$i]) 
                && $list[0][$pos] == $list[$i][$pos]; $pos++);
        $maxPrefix = $pos;
    }

    return $maxPrefix;
}

function getFiles($dirs, $pattern, $param){
    $files = array();
    $f=scandir($dirs[0]);    
    foreach($dirs as $dir){
        foreach(scandir($dir) as $file){
            if($file!='.' && $file!='..'){
                if(preg_match($pattern, $file)){
                    $files[] = realpath($dir.'/'.$file);
                }
            }
        }
    }    
    $fileNames = array();
    $pos = getPrefixPos($files);        
    foreach($files as $file){
        $fileNames[] = array(
            'name' => substr($file,$pos),
            'path' => $file,
            'link' => basename(__FILE__).'?'.$param.'='.$file
        );
    }    
    return $fileNames;
}

/*******************************************************************************
 *                                     LOGS 
 ******************************************************************************/
 
$logDirs = array(        
    APPLICATION_PATH.'/../var/log/'
);

// echo APPLICATION_PATH.'/../var/log/';exit;

$op = 'show-log';

echo "<h1>Logs: </h1>".PHP_EOL;
echo "<ul>".PHP_EOL;

foreach(getFiles($logDirs, '/.log$/', $op) as $file){
    echo "<li><a href=\"{$file['link']}\">{$file['name']}</a></li>";
}
echo "</ul>".PHP_EOL;

if(isset($_GET[$op])){
    $file = $_GET[$op];
    echo "<hr><pre>".file_get_contents($file)."</pre>";
    
    echo "<hr>";
    $output = array();
    exec("cat $file | wc -l",$output);
    echo "<b>Lineas:</b> ".($output[0]+1) . ' | ';
    exec("du -hs $file | awk '{print $1}'",$output);
    echo "<b>Size:</b> ".($output[1]) . ' | ';
    echo "<a href=\"".basename(__FILE__)."?clear-log=$file\">Borrar</a> | ";
    echo $file;
}

$op = 'clear-log';
if(isset($_GET[$op])){
    $file = $_GET[$op];
    file_put_contents($_GET[$op],'');
    //exec("echo '' > $file");
    //$fa=fopen($file,"w+");fwrite($fa,"");fclose($fa);
    echo "<hr>Log borrado";
}

