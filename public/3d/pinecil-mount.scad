$fn=80;

module base() {
    translate([-7.6,6.6,0]) intersection() {
        cube([50,50,33], center=true);
        translate([0,0,-0.125]) rotate([90,0,0]) import(file="reference/printables-181461.stl"); // https://www.printables.com/model/181461-soldering-helping-hands-base/files
    }

    cylinder(d=8, h=16.5);
}

difference() {
    base();
    translate([0,0,3]) cylinder(d=2.2, h=20);
}