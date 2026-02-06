$fn=80;

scaleFactor=1;

module solid() {
    translate([-18*scaleFactor,146*scaleFactor,0]) scale([scaleFactor,scaleFactor,scaleFactor]) import("reference/thingiverse-2999036.stl"); // https://www.thingiverse.com/thing:2999036
}

module base() {
    difference() {
        scale([1.065,1.065,1.065]) solid();
        translate([0,0,14]) translate([0,0,50]) cube([250,250,100], center=true);
        
        translate([0,0,0.75]) scale([1.04,1.04,1.04]) solid();
    }
}

module bumps() {

    module bumpsWhole() {
        translate([22*scaleFactor,0,0]) sphere(r=4);
        translate([0,22*scaleFactor,0]) sphere(r=4);
        translate([0,-22*scaleFactor,0]) sphere(r=4);
        translate([-22*scaleFactor,0,0]) sphere(r=4);
    }

    difference() {
        bumpsWhole();
        translate([0,0,-50]) cube([250,250,100], center=true);
    }
}

base();
bumps();