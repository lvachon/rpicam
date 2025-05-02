What started out at a general purpose raspberry pi based camera quickly got specialized into a weatherproof(?) timelapse most-of-the-sky camera.

The party trick this one has over other timelapse cameras is it's "super" auto exposure.  Able to properly expose for the noonday sun and a moonless night smoothly.  

It's not a fancy algorithm, far from it, but I've found only one other timelapse camera that'll do that.  But now I want a better sensor and wifi too, so time to make my own rather than shell out ~$1000 for a commerical one.

BOM so far:
* Raspberry Pi 4 Model B (Any ram is fine, this is quite light so far)
* Raspberry Pi Camera Module v3 (or compatible)
* A tiny little 5v pc fan
* A tiny little aluminum heatsink
* A tiny little square of air filter material
* A waterproof enclosure with gasketed, clear, acrylic(?) lid
* 4 x Waterproof cable grommets
* A second waterproof enclosure for the power supply
* A tripod with 1/4-20 shoe
* A clamping phone mount for 1/4-20 tripods
* 2 x right angle usb C adapters
* A silicon jacketed, non-braided, 6 foot, USB A-C cable
* A 12 marine grade (ha!) USB power adapter thing
* Various bits of wire and some lever nuts
* 20 Lbs of lead (seriously)
* 3D printed mounting plate that secures camera to the back of the Raspberry Pi
* 4x 3D printed standoffs/legs to raise and level the camera assembly
* 12x M2.5 bolts (4xtiny, 4xshort, 4xlong)
* An old PC role-playing as a server to hold the many gigs of data this will produce.
* A reusable shopping bag

Assembly (note, most electrical and all software details are glossed over. a million other people can tell you how to get a raspi running with a camera):
* The raspi is physically prepared by painting over the indicator LEDs with several coats of black nail polish.  We're going to be exposing for ~50seconds at 16x gain if we're lucky, any stray light will kill the shot.
* The camera is secured and connected to the raspi via cable and adapter plate respectively, then mounted on the four standoffs.  Four bolts screw into the bottom of the standoffs to adjust each leg's height.  
* The waterproof enclosure with the clear lid is prepped with one hole for a waterproof cable grommet, four mounting holes for the tiny pc fan, and many intake and exhaust holes for the air the fan blows.  Try to place the intake near the heatsink, the fan should blow out (I think)
* The other enclosure is prepared with holes for two waterproof cable grommets.  This one will be performing it's actual job as a juction box.
* Feed the USB cable through one of the junction boxes grommets from the inside out, USB-C side first.
* BE SURE TO PUT THE GROMMET CAPS ON NOW, IN THE RIGHT ORIENTATION
* Feed the USB-C end into the camera enclosure through the waterproof grommet, leave the cap loose.
* Use the adapters to make everything fit and plug it into the raspi.
* Be sure to plug in the fan too (pins 4(+) and 6(-))
* Place the camera assembly into position in the enclosure, while feeding the slack through the grommet.  Once everything is level and square, tighten the grommet to hold things in place decently well.
* Be sure your camera lens isn't so high that it hits the enclosure lid, but get it as close as practical.
* Place your chosen power adapter into the junction box and plug in the cable.
* Feed your 12v wires (or whatever) into the other grommet of the junction box and wire to the power adapter.
* Secure the camera enclosure in the phone mount and attach that to the tripod.
* Tie/bungie/duck tape the junction box somewhere too (can you tell this is a place for improvement?)
* Place the whole thing somewhere with as much unobstructed sky as possible.  Level everything, pick a side of the camera sensor and point that north.  I like north to be on top personally.
* Fill a sturdy bag (that has handles) with about 20lbs of lead.  Hook that on the tripods anchor hook where the legs meet.  This will help recruit gravity in the fight against wind.
* Send external power into the junction box and and away we go!
* Tune into it's website to see live images, scroll through a days worth of history, and see a neat graph with some stats!
* Have a server you can store gigs of pictures on and do boring sysadmin stuff to move files and whatnot

  

Parts I've destroyed so far:
* 2 x Raspberry Pi 4 Model B (overvoltage to the USB port, both times.  The safety diode took the first hit spectacularly, magic smoke and everything.  The second board got properly killed all throughout.  The primary culprit was the USB controller roasting at 200F, but it turns out the SD card was also bricked.)
* 1 x 3 foot, _braided_, USB C to right-angle slimline USB C.  (This was killed when the wind over the tripod during a rainstorm.  This allowed water to wick through the braided cable, through the grommet, and into the power connector.  Where it them played galvanic rock-paper-scissors with all the metals involved, and in the end I lost.)
* 1 x 12v to USB power thing (rain damage, see above)
* 1 x Raspberry Pi camera module v3 (wide fov, noir) (Somehow smashed it's tiny little lens during all of this)
* 1 x Microcenter 64GB MicroSD card (my bin of them is running low...)
