$fn=60;

module runners() {
    translate([13.5,-1,0]) cube([7.5,40,5]);
    translate([33.5,-1,0]) cube([10,40,3]);
}

module angle() {
    translate([0,-1,13.5]) translate([21,25/2,(10/2)]) rotate([0,27.4,0]) cube([50,50,10], center=true);
}

module hole() {
    diameter=4.75;
    module counterSink() {
        cylinder(d=7.8,h=1.8);
        translate([0,0,-1.7]) cylinder(d2=7.8,d1=diameter,h=1.7);
    }
    
    translate([0,0,13-1.7]) counterSink();
    cylinder(d=diameter, h=13);
}

difference() {
    translate([0,0,0]) cube([40,30,13]);
    runners();
    angle();
    translate([9,30/2,0]) hole();
}

//%translate([20,0,13]) cube([0.5,50,0.5]);