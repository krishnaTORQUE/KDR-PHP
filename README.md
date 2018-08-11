![KDO](_default/favicon.png?raw=true "KDO")
# KDO



> **_Description_**

Next Version of VARPHP or Next Generation of VARPHP

Only PHP MVC (Model View Controller) nothing else. It is a nano framework.

Host Multiple Apps, Lightweight, Powerful, Most Secure & Super Fast



> **_Build_**
- Version: **1.0**
- Status: **Alpha**



> **_Tested_**
- PHP (7.0, 7.1, 7.2)
- Nginx (1.10, 1.12, 1.14)



> **_Note_**

- Enable Rewrite Module On.

- Example Server Configure Files are in **_`server-config`_** folder.

- No Library or Class or Functions Added. 
Use <a href="https://github.com/krishnaTORQUE/HelperClass" target="_blank">**HelperClass**</a> Instead.



> **_Setup_**

```php
# Create `_config.php` file in root directory.

$this->APP['NAME'] = 'My App';
$this->APP['ACTIVE'] = 'myapp';

## Using Plugins or Autoloaders ##
# Create `__PLUGS` folder in root directory and paste your plugins or autoloads.
$this->APP['PLUGS'] => ['file_name.php'];
```



> **_Update_**

**_Always check `CHANGELOG` before update._**
1. Delete **`__KDO`** folder completely from host.
2. Download new version of KDO and copy & paste **`__KDO`** folder.



> **_License (C) 2013 - 2018 under GNU GPL V2._**
