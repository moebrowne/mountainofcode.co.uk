$fn=80;

module corner(diameter, angle, bendRadius, legLengthA, legLengthB) {
    
    legLengthB=legLengthB-2.5;
    
    module bend() {
        rotate_extrude(angle=angle,convexity=10) {
            translate([bendRadius,0,0]) {
                difference() {
                    circle(d=diameter);
                    translate([0,-diameter/4,0]) square(diameter/2);
                }
            }
        }
    }
    
    rotate([0,0,-angle]) union() {
        bend();
        rotate([180+(90-angle)+0.05,90,0]) translate([0,-bendRadius,0]) cylinder(d=diameter,h=legLengthA);
        translate([bendRadius,0.05,0]) rotate([90,0,0]) cylinder(d=diameter,h=legLengthB);
    }
}

module support(angle, outerDiameter, wall, radius) {
    difference() {
        corner(outerDiameter,angle,radius,0,0);
        corner(outerDiameter-(wall*2),angle,radius,0.01,0.01);
    }
    
    module leg() {
        difference() {
            rotate([-90,0,0]) difference() {
                cylinder(d=outerDiameter, h=20);
                cylinder(d=outerDiameter-(wall*2), h=20);
            }
            translate([outerDiameter/2,0,0]) cube([outerDiameter/2,100,outerDiameter/2], center=true);
        }
    }
    
    translate([radius,0,0]) leg();
    translate([-radius,0,0]) rotate([0,180,0]) leg();
}

support(180,19.7,2,28);
