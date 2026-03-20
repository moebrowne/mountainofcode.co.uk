

module printerSide() {
    difference() {
        cube([150, 2.15, 47+(26*2)], center=true);
        translate([0,0.01,0]) cube([80, 10, 46.5], center=true);
    }
}

%printerSide();

difference() {
    union() {
        cube([25, 2.15, 46.5], center=true);
        translate([0,-2.15,0]) cube([25, 2.15, 70], center=true);
        
        hull() {
            translate([0,-3.30+0.1,26]) cube([25,0.001,18], center=true);
            translate([9,-25,26.22]) rotate([0,0,25]) rotate([60,0,0]) cube([25,0.001,35], center=true);
        }
    }
    translate([0,0,-22]) rotate([10,0,0]) cube([17,3,60], center=true); // cable slot
}
