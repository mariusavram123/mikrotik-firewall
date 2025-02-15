#!/usr/bin/php
<?php
/*
Parse a filenamed a.txt, take out the first address on the range
Convert the number of addresses in the last column of the file to subnet mask
Transform the network and subnet mask obtained into a address list command for mikrotik chr

Scope - block all russian IP addresses on mikrotik CHR
Add a ssh key in mikrotic CHR to bypass the need to enter the password every time
*/

$file = "iplist.txt";
$debug = true;
$list = "block-ru";
#login credentials for chr device
$user = "myuser";
$host = "my_ip";
$port = 22322;

function parsefile(){
    global $file, $debug;
    $handle = fopen($file,"r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            #$lines = explode(" ", $line);
            $lines = preg_split("/\t+/", $line);
            $address = $lines[0];
            $mask = $lines[2];
            if ($debug) {
                echo "Address is $address \n";
                echo "Mask is $mask \n";
            }
            //Take out the mask and extract the "," out of it, then convert it to integer
            if (str_contains($mask, ",")) {
                $position = strpos($mask, ",");
                $newmask = substr ($mask, 0, $position) . substr($mask, $position + 1);
                $maskint = intval($newmask);
                if ($debug) {
                    var_dump($maskint);
                }
                for ($i = 1; $i <= 32; $i ++) {
                    $result = pow(2,$i);
                    if ($debug) {
                        var_dump($result);
                    }
                    //Extract the subnet mask that match with the result of the 2^n operation, extract it from 32
                    if ($result == $maskint) {
                        $finalmask = 32 - $i;
                        if ($debug) {
                            var_dump($finalmask);
                            echo "Found match $i and finalmask $finalmask \n";
                        }
                        addrangeaddresslist($address,$finalmask);
                    }
                    //TODO: if cannot find any matching value - 2^n differ from the size of addresses, throw an error
                    // Later implement splitting it in multiple values that can make the total number
                }
            }
        }
    }   
}

# I need to connect to the mikrotik via ssh and execute the command

function addrangeaddresslist($address,$finalmask) {
    global $debug, $list, $user, $host, $port;
    echo "Adding $address range in address list";
    if ($debug) {
        echo "Address is $address and mask is $finalmask \n";
    }
    #echo "/ip/firewall/address-list add address=$address/$finalmask list=$list";
    $command = system("ssh $user@$host -p $port /ip/firewall/address-list add address=$address/$finalmask list=$list", $retval);
    if ($retval == 0) {
        echo "Added ip range $address/$finalmask to the address list";
    } else {
        die("Returned an error.");
    }
}

//call the function
parsefile();

?>
