$fn=80;

module outerRing() {
    difference() {
        cylinder(d=80,h=3);
        translate([0,0,-0.1]) cylinder(d=80-4,h=3+0.2);
    }
}

module innerRing() {
    difference() {
        cylinder(d=29+4,h=3);
        translate([0,0,-0.1]) cylinder(d=29,h=3+0.2);
    }
}

//for(i = [1:8]) {
//    rotate([0,0,i*45]) translate([27,0,0]) scale([0.65,0.65,1]) innerRing();
//}


for(i = [1:24]) {
    rotate([0,0,i*(360/24)]) translate([15,0,0]) cube([2,36,3]);
}

innerRing();
outerRing();