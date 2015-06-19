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

