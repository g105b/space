A universal coordinate system for hierarchical location of any celestial object.

Just a little toy I'm playing with... it's currently in development and may never get finished.

Generation Parameters
---------------------

+ Multiverse ID.
+ Universal Sector Coordinates - USC scale: -inf:+inf universal sectors - p0:p0 has the Milky Way in the centre. 1 universal sector is 12,000 kpc square.
+ Lightyear Grid Coordinates - LGC scale: -100:+100 - each cell is 60 kpc square (195,695 light years). The Milky Way is 170,000 light years in diameter, so is just contained within the centre cell. 1 kiloparsec is equal to 3,261.6 light years (3,261.6 * 60 = 195,696). Selecting an LGC of p0:p0 within USC p0:p0 will clearly show the spirals of the Milky Way.
+ Galactic Grid Coordinates - GGC scale: -30:+30 kpc - each GGC cell is 1 kiloparsec square, which is equal to 3,261 lightyears.
+ Sub-galactic Grid Coordinates - SGC scale: -100:+100 Mau - each SGC cell is 1 Mau square. The Origin Solar System is 100,000 au (0.1 Mau) in diameter.
+ Solar System Coordinates - +0:+1031324 au,-180:+180 deg. Measured in Distance, Longitude (au, degrees) from the central point (star, or average position of multiple stars). There is no latitude measurement as this simulation reduces space into single 2d plane representation. Maximum distance from centre is 1,031,324au (just over 1 Mau). Systems larger than this become distributed systems and physically require multiple stars, otherwise they become black holes.
+ Orbital distance (planets only). When the SSC is aligned with a planet's orbit, the orbital distance plays a role in visiting/interacting with satellites of planets. Measured in km, an OD of 0km enters the global positioning system of the planet. An OD of the exact distance of a satellite enters the global positioning system of the satellite. 
+ Global Positioning System - alt,lon,lat. Measured in metres, degrees, degrees. Locates any item on the surface of a planet/satellite at a particular altitude.
+ Local Positioning System - alt,x,y. Scale: 0:500,000 m,-10,000:10,000m, -10,000:10,000m. Measured in metres. Used to locate and position items within a local grid system on or above the surface of a planet/satellite, within a cell of 20 square km. 

Example call:

```bash
./space-gen \
	--multiverse=ungabi \
	--usc=n100:p123 \ 
	--lgc=n90:p55 \
	--ggc=p30:p25 \
	--sgc=n100:n57 \
	--ssc=p13.515:n11.05 \
	--ods=p100 \
	--gps=p1000:n11.05:p51.03 \
	--lps=p0:n128.2:n20.11
```
