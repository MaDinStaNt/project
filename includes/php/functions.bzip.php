<?php
/**
 * @return bool
 * @param string $in
 * @param string $out
 * @desc compressing the file with the bzip2-extension
*/
function bzip2 ($in, $out)
{
    if (!file_exists ($in) || !is_readable ($in))
        return false;
    if ((!file_exists ($out) && !is_writeable (dirname ($out)) || (file_exists($out) && !is_writable($out)) ))
        return false;
   
    $in_file = fopen ($in, "rb");
    $out_file = bzopen ($out, "wb");
   
    while (!feof ($in_file)) {
        $buffer = fgets ($in_file, 4096);
         bzwrite ($out_file, $buffer, 4096);
    }

    fclose ($in_file);
    bzclose ($out_file);
   
    return true;
}

/**
 * @return bool
 * @param string $in
 * @param string $out
 * @desc uncompressing the file with the bzip2-extension
*/
function bunzip2 ($in, $out)
{
    if (!file_exists ($in) || !is_readable ($in))
        return false;
    if ((!file_exists ($out) && !is_writeable (dirname ($out)) || (file_exists($out) && !is_writable($out)) ))
        return false;

    $in_file = bzopen ($in, "rb");
    $out_file = fopen ($out, "wb");

    while ($buffer = bzread ($in_file, 4096)) {
        fwrite ($out_file, $buffer, 4096);
    }
 
    bzclose ($in_file);
    fclose ($out_file);
   
    return true;
}
?>
