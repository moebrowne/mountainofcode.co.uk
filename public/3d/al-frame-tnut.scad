$fn=80;

module frame() {
    difference() {
        translate([0,-1,8/2]) cube([14,20,8], center=true);
        translate([0,-1,(8/2)]) cube([10,20.5,4], center=true);
        translate([0,-1,6]) cube([6.4,20.5,4.1], center=true);
    }
}

module nut() {
    intersection() {
        cylinder(d=10,h=4);
        translate([0,0,4/2]) cube([10,4,4], center=true);
    }
    
    translate([0,0,8/2]) {
        intersection() {
            cylinder(d=6.5,h=2);
            translate([0,0,2/2]) cube([10,4,2], center=true);
        }
    }
    
    translate([0,0,6/2]) {
        hull() {
            translate([0,0,(2+6)/2]) cube([10.05,4,2], center=true);
            translate([0,0,18]) translate([0,0,(2+6)/2]) cube([7.05,4,1], center=true);
            translate([0,0,18]) translate([0,0,(2+6)/2]) cube([9.05,0.5,1], center=true);
        }
    }
}

%frame();
translate([0,0,2]) nut();
