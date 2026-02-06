$fn=80;

module spars() {
    translate([-50,0,0]) rotate([0,0,90]) rotate([90,0,0]) cylinder(d=4, h=100);
    translate([0,-3,0]) rotate([90,0,0]) cylinder(d=2.5,h=70);
}

%spars();

module body() {
    hull() {
        cube([20,4.5,4.5], center=true);
        translate([0,-5,0]) cube([4,6,2], center=true);
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
}
