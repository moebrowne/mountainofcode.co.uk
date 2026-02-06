$fn=80;

angle=60; //deg

sparDiameter=4;
nodeHeight=sparDiameter;

module spars() {
    translate([0,0,0]) rotate([90,0,0]) cylinder(d=sparDiameter,h=100);
    rotate([0,0,angle]) translate([2,-4,0]) rotate([90,0,0]) cylinder(d=sparDiameter,h=100);
    rotate([0,0,-angle]) translate([-2,-4,0]) rotate([90,0,0]) cylinder(d=sparDiameter,h=100);
}

%spars();

module body() {
    hull() {
        translate([0,-1,-nodeHeight/2]) scale([1.2,0.8,1]) cylinder(d=14,h=nodeHeight);
        translate([-15,-6.4,0]) rotate([0,0,30]) cube([nodeHeight,nodeHeight,nodeHeight], center=true);
        translate([15,-6.4,0]) rotate([0,0,-30]) cube([nodeHeight,nodeHeight,nodeHeight], center=true);
        translate([0,-10,0]) cube([7,7,nodeHeight], center=true);
    }
}

module roundedBody() {
    minkowski() {
        body();
        sphere(d=2);
    }
}

difference() {
    roundedBody();
    spars();
    translate([0,-8,-20]) scale([1.3,0.6,1]) cylinder(d=6,h=40);
}