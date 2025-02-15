# mikrotik-firewall
Scripts for Mikrotik firewalls

In this case I am using a .php script to block all the allocated IP addresses of Russia in ipv4.

Requirements:

- Add an user in Mikrotik
- Add a ssh key for that user in order to be able to ssh on the Mikrotik without the need for password
- php package installed on the machine where running the script

Usage:

```
./add-address-list.php
```
Expectations:
- Add all russian IP addresses in an addresss list.
After adding the address list you need to add the block rule in order to activate it.
I did not activated it to not block something essential.
Command to activate the address list on Mikrotik:
```
/ip firewall filter add action=drop chain=input src-address-list=block-ru
```
Source for IP list: https://lite.ip2location.com/russian-federation-ip-address-ranges
