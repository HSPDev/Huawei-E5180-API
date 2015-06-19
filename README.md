#Huawei E5180 API

This project will let you interface with your Huawei E5180 Cube router easily.  
This router is deployed by 3 DK, as "home routers" for their 4G connections. 
You can get it for free if you order a 100GB or above package from 3.
(Link where I bought mine: [https://www.3.dk/mobiler-tablets/modems-routere/huawei/huawei-e5180-cube](https://www.3.dk/mobiler-tablets/modems-routere/huawei/huawei-e5180-cube))  

No advertisement or affiliation or anything, I wouldn't even really recommend the router. It got great speeds, but the WiFi is not the greatest, 
and I've seen it overload multiple times when you have lots of clients on your (W)LAN. The 4G link holds, but the LAN/WiFi/Router portion is really bad, so I personally use it with a D-link DIR-810l as router, with it's WAN port connected to the Huawei routers single LAN port, so it can focus on just delivering 4G Internet.

##What can it do?

 - Send SMS through the mobile network
 - Receive SMS (you need to pool for this periodically)
 - Delete SMS (to avoid filling it up)
 - Query router status (lots of info)
 - Query traffic statistics (decent amount of info)
 - Get the current PLMN (which network you are on)
 - Get craddle status (hardware info, battery and such if yours have one)
 - Get WLAN clients
 - Check the device for notifications (Including SMS and updates)
 - Query LED status (the big blue one on top)
 - Turn the LED on or off. 
 
What could you use this for? Do you want a free inbound SMS gateway for your home? Now you got it, if you have this router. I know 3.dk currently charges me 0.20 DKK for outbound SMS, as of 19-06-2015, but be sure to check with your own provider before sending SMS. I could also easily imaging your provider being mad at you if you suddenly used this as a commercial gateway, sending and receiving thousands of SMS messages. 

If you like me, live in the country without good internet through wire, and want a 4G router like this, you could also make your own interface to keep your bandwidth bills in check. 

Also, the blue LED is quite fun to play with. You can tap the top, to turn it on and off, so I already have plans for letting me know if I have new emails by turning the LED blue.

##Documentation
How do I use this library?
Simply include the composer autoloader into your project after installation, as you are used to, and proceed to make a Router object. Set the address of your router and login. Now every other function listed here SHOULD work, at least on E5180. I can't talk about any other routers compliance.

```
<?php
require_once 'vendor/autoload.php';

//The router class is the main entry point for interaction.
$router = new HSPDev\HuaweiApi\Router;

//If specified without http or https, assumes http://
$router->setAddress('192.168.8.1');

//Username and password. 
//Username is always admin as far as I can tell.
$router->login('admin', 'your-password');

var_dump($router->getLedStatus());

```
This will get the current status off the blue LED on top, either true or false, for on/off. If this seems to work, try the following line instead:

```
<?php
var_dump($router->setLedOn(!$router->getLedStatus()));

```
Every time you run the script now, it should turn the LED on or off, depending on it's current state.

**Now let's try something else. **

```
var_dump($router->getNetwork());

```
Which should return something like the following, which shows that I'm currently on the "3 DK"" network. You can look up PLMN lists to get the numeric codes.

```
object(SimpleXMLElement)#8 (5) {
  ["State"]=>
  string(1) "0"
  ["FullName"]=>
  string(4) "3 DK"
  ["ShortName"]=>
  string(4) "3 DK"
  ["Numeric"]=>
  string(5) "23806"
  ["Rat"]=>
  string(1) "2"
}

```

**What about some SMS?**

```
var_dump($router->getInbox());

```
In my case it returned this, meaning I have no new messages.

```
object(SimpleXMLElement)#6 (2) {
  ["Count"]=>
  string(1) "0"
  ["Messages"]=>
  object(SimpleXMLElement)#4 (0) {
  }
}

```
That can't be true. Let's send some to our router. You can probably find the phone number for your router on your bills, sometimes in the web interface or maybe by simple logging into the web interface and sending yourself a message. After sending my router a SMS I got this result instead:

```
object(SimpleXMLElement)#6 (2) {
  ["Count"]=>
  string(1) "1"
  ["Messages"]=>
  object(SimpleXMLElement)#4 (1) {
    ["Message"]=>
    object(SimpleXMLElement)#8 (9) {
      ["Smstat"]=>
      string(1) "0"
      ["Index"]=>
      string(5) "40000"
      ["Phone"]=>
      string(11) "(my phone number)"
      ["Content"]=>
      string(3) "Lol"
      ["Date"]=>
      string(19) "2015-06-19 15:27:15"
      ["Sca"]=>
      object(SimpleXMLElement)#9 (0) {
      }
      ["SaveType"]=>
      string(1) "4"
      ["Priority"]=>
      string(1) "0"
      ["SmsType"]=>
      string(1) "1"
    }
  }
}

```

Have a look inside the Router.php class to find out what methods you can use, it's very well documented, but I will throw a list here anyway.

 - login($username, $password) username is almost always "admin". "password" is the one for the web interface.
 - getStatus() gives info about the routers status.
 - getTrafficStats() gives traffic info 
 - getMonthStats() does the same for the current month (if you have setup limits)
 - getNetwork() gives info about the current network. Can't find the bars anywhere tho. 
 - getCraddleStatus() lots of more info. I suspect you can get battery status here, if your device has one.
 - getSmsCount() DOES NOT RETURN AN integer, but also an XML object.
 - getWlanClients() gets a list of WlanClients, if they have IP 0.0.0.0 they are disconnected. 
 - getNotifications() does what it says.
 - setLedOn(boolean $on) call with "true" to turn on, and "false" for off.
 - getLedStatus() true/false for LED status.
 - isLoggedIn() true/false to check if logged in.
 - getInbox($page = 1, $count = 20, $unreadPreferred = false) defaults are fine for most tinkering. page/count for pagination.
 - deleteSms($index) provide with SMS index for deleting. Returns true if not found also.
 - sendSms($receiver, $message) Pretty self explanatory. Might return true and not send anyway. There is an API to query for send status, but I didn't worry about it.

I don't promise that these will work like advertised or at all, just have fun. It should get you started.


##Huawei Router API Error codes
Sometimes if you are experimenting with the Huawei API and trying to talk with it, you will get a random error code back. This sucks, to say it politely, as there is absolutely no information on what is going wrong. Therefore, I've gotten hold of this list of error codes for the Huawei API, which I know is true for the E5180 and probably other devices too. So if you just googled "Huawei Router API Error" or something like that, congratulations, today is your lucky day.
Please note that not all codes are in here, but most of them are.

```
ERROR_BUSY = 100004
ERROR_CHECK_SIM_CARD_CAN_UNUSEABLE = 101004
ERROR_CHECK_SIM_CARD_PIN_LOCK = 101002
ERROR_CHECK_SIM_CARD_PUN_LOCK = 101003
ERROR_COMPRESS_LOG_FILE_FAILED = 103102
ERROR_CRADLE_CODING_FAILED = 118005
ERROR_CRADLE_GET_CRURRENT_CONNECTED_USER_IP_FAILED = 118001
ERROR_CRADLE_GET_CRURRENT_CONNECTED_USER_MAC_FAILED = 118002
ERROR_CRADLE_GET_WAN_INFORMATION_FAILED = 118004
ERROR_CRADLE_SET_MAC_FAILED = 118003
ERROR_CRADLE_UPDATE_PROFILE_FAILED = 118006
ERROR_DEFAULT = -1
ERROR_DEVICE_AT_EXECUTE_FAILED = 103001
ERROR_DEVICE_COMPRESS_LOG_FILE_FAILED = 103015
ERROR_DEVICE_GET_API_VERSION_FAILED = 103006
ERROR_DEVICE_GET_AUTORUN_VERSION_FAILED = 103005
ERROR_DEVICE_GET_LOG_INFORMATON_LEVEL_FAILED = 103014
ERROR_DEVICE_GET_PC_AISSST_INFORMATION_FAILED = 103012
ERROR_DEVICE_GET_PRODUCT_INFORMATON_FAILED = 103007
ERROR_DEVICE_NOT_SUPPORT_REMOTE_OPERATE = 103010
ERROR_DEVICE_PIN_MODIFFY_FAILED = 103003
ERROR_DEVICE_PIN_VALIDATE_FAILED = 103002
ERROR_DEVICE_PUK_DEAD_LOCK = 103011
ERROR_DEVICE_PUK_MODIFFY_FAILED = 103004
ERROR_DEVICE_RESTORE_FILE_DECRYPT_FAILED = 103016
ERROR_DEVICE_RESTORE_FILE_FAILED = 103018
ERROR_DEVICE_RESTORE_FILE_VERSION_MATCH_FAILED = 103017
ERROR_DEVICE_SET_LOG_INFORMATON_LEVEL_FAILED = 103013
ERROR_DEVICE_SET_TIME_FAILED = 103101
ERROR_DEVICE_SIM_CARD_BUSY = 103008
ERROR_DEVICE_SIM_LOCK_INPUT_ERROR = 103009
ERROR_DHCP_ERROR = 104001
ERROR_DIALUP_ADD_PRORILE_ERROR = 107724
ERROR_DIALUP_DIALUP_MANAGMENT_PARSE_ERROR = 107722
ERROR_DIALUP_GET_AUTO_APN_MATCH_ERROR = 107728
ERROR_DIALUP_GET_CONNECT_FILE_ERROR = 107720
ERROR_DIALUP_GET_PRORILE_LIST_ERROR = 107727
ERROR_DIALUP_MODIFY_PRORILE_ERROR = 107725
ERROR_DIALUP_SET_AUTO_APN_MATCH_ERROR = 107729
ERROR_DIALUP_SET_CONNECT_FILE_ERROR = 107721
ERROR_DIALUP_SET_DEFAULT_PRORILE_ERROR = 107726
ERROR_DISABLE_AUTO_PIN_FAILED = 101008
ERROR_DISABLE_PIN_FAILED = 101006
ERROR_ENABLE_AUTO_PIN_FAILED = 101009
ERROR_ENABLE_PIN_FAILED = 101005
ERROR_FIRST_SEND = 1
ERROR_FORMAT_ERROR = 100005
ERROR_GET_CONFIG_FILE_ERROR = 100008
ERROR_GET_CONNECT_STATUS_FAILED = 102004
ERROR_GET_NET_TYPE_FAILED = 102001
ERROR_GET_ROAM_STATUS_FAILED = 102003
ERROR_GET_SERVICE_STATUS_FAILED = 102002
ERROR_LANGUAGE_GET_FAILED = 109001
ERROR_LANGUAGE_SET_FAILED = 109002
ERROR_LOGIN_ALREADY_LOGINED = 108003
ERROR_LOGIN_MODIFY_PASSWORD_FAILED = 108004
ERROR_LOGIN_NO_EXIST_USER = 108001
ERROR_LOGIN_PASSWORD_ERROR = 108002
ERROR_LOGIN_TOO_MANY_TIMES = 108007
ERROR_LOGIN_TOO_MANY_USERS_LOGINED = 108005
ERROR_LOGIN_USERNAME_OR_PASSWORD_ERROR = 108006
ERROR_NET_CURRENT_NET_MODE_NOT_SUPPORT = 112007
ERROR_NET_MEMORY_ALLOC_FAILED = 112009
ERROR_NET_NET_CONNECTED_ORDER_NOT_MATCH = 112006
ERROR_NET_REGISTER_NET_FAILED = 112005
ERROR_NET_SIM_CARD_NOT_READY_STATUS = 112008
ERROR_NOT_SUPPORT = 100002
ERROR_NO_DEVICE = -2
ERROR_NO_RIGHT = 100003
ERROR_NO_SIM_CARD_OR_INVALID_SIM_CARD = 101001
ERROR_ONLINE_UPDATE_ALREADY_BOOTED = 110002
ERROR_ONLINE_UPDATE_CANCEL_DOWNLODING = 110007
ERROR_ONLINE_UPDATE_CONNECT_ERROR = 110009
ERROR_ONLINE_UPDATE_GET_DEVICE_INFORMATION_FAILED = 110003
ERROR_ONLINE_UPDATE_GET_LOCAL_GROUP_COMMPONENT_INFORMATION_FAILED = 110004
ERROR_ONLINE_UPDATE_INVALID_URL_LIST = 110021
ERROR_ONLINE_UPDATE_LOW_BATTERY = 110024
ERROR_ONLINE_UPDATE_NEED_RECONNECT_SERVER = 110006
ERROR_ONLINE_UPDATE_NOT_BOOT = 110023
ERROR_ONLINE_UPDATE_NOT_FIND_FILE_ON_SERVER = 110005
ERROR_ONLINE_UPDATE_NOT_SUPPORT_URL_LIST = 110022
ERROR_ONLINE_UPDATE_SAME_FILE_LIST = 110008
ERROR_ONLINE_UPDATE_SERVER_NOT_ACCESSED = 110001
ERROR_PARAMETER_ERROR = 100006
ERROR_PB_CALL_SYSTEM_FUCNTION_ERROR = 115003
ERROR_PB_LOCAL_TELEPHONE_FULL_ERROR = 115199
ERROR_PB_NULL_ARGUMENT_OR_ILLEGAL_ARGUMENT = 115001
ERROR_PB_OVERTIME = 115002
ERROR_PB_READ_FILE_ERROR = 115005
ERROR_PB_WRITE_FILE_ERROR = 115004
ERROR_SAFE_ERROR = 106001
ERROR_SAVE_CONFIG_FILE_ERROR = 100007
ERROR_SD_DIRECTORY_EXIST = 114002
ERROR_SD_FILE_EXIST = 114001
ERROR_SD_FILE_IS_UPLOADING = 114007
ERROR_SD_FILE_NAME_TOO_LONG = 114005
ERROR_SD_FILE_OR_DIRECTORY_NOT_EXIST = 114004
ERROR_SD_IS_OPERTED_BY_OTHER_USER = 114004
ERROR_SD_NO_RIGHT = 114006
ERROR_SET_NET_MODE_AND_BAND_FAILED = 112003
ERROR_SET_NET_MODE_AND_BAND_WHEN_DAILUP_FAILED = 112001
ERROR_SET_NET_SEARCH_MODE_FAILED = 112004
ERROR_SET_NET_SEARCH_MODE_WHEN_DAILUP_FAILED = 112002
ERROR_SMS_DELETE_SMS_FAILED = 113036
ERROR_SMS_LOCAL_SPACE_NOT_ENOUGH = 113053
ERROR_SMS_NULL_ARGUMENT_OR_ILLEGAL_ARGUMENT = 113017
ERROR_SMS_OVERTIME = 113018
ERROR_SMS_QUERY_SMS_INDEX_LIST_ERROR = 113020
ERROR_SMS_SAVE_CONFIG_FILE_FAILED = 113047
ERROR_SMS_SET_SMS_CENTER_NUMBER_FAILED = 113031
ERROR_SMS_TELEPHONE_NUMBER_TOO_LONG = 113054
ERROR_STK_CALL_SYSTEM_FUCNTION_ERROR = 116003
ERROR_STK_NULL_ARGUMENT_OR_ILLEGAL_ARGUMENT = 116001
ERROR_STK_OVERTIME = 116002
ERROR_STK_READ_FILE_ERROR = 116005
ERROR_STK_WRITE_FILE_ERROR = 116004
ERROR_UNKNOWN = 100001
ERROR_UNLOCK_PIN_FAILED = 101007
ERROR_USSD_AT_SEND_FAILED = 111018
ERROR_USSD_CODING_ERROR = 111017
ERROR_USSD_EMPTY_COMMAND = 111016
ERROR_USSD_ERROR = 111001
ERROR_USSD_FUCNTION_RETURN_ERROR = 111012
ERROR_USSD_IN_USSD_SESSION = 111013
ERROR_USSD_NET_NOT_SUPPORT_USSD = 111022
ERROR_USSD_NET_NO_RETURN = 11019
ERROR_USSD_NET_OVERTIME = 111020
ERROR_USSD_TOO_LONG_CONTENT = 111014
ERROR_USSD_XML_SPECIAL_CHARACTER_TRANSFER_FAILED = 111021
ERROR_WIFI_PBC_CONNECT_FAILED = 117003
ERROR_WIFI_STATION_CONNECT_AP_PASSWORD_ERROR = 117001
ERROR_WIFI_STATION_CONNECT_AP_WISPR_PASSWORD_ERROR = 117004
ERROR_WIFI_WEB_PASSWORD_OR_DHCP_OVERTIME_ERROR = 117002

//My own guess, don't trust this completely.
Unknown URLs are 100002

```