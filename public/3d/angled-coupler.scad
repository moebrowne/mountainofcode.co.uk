$fn=80;


module corner(diameter, angle, bendRadius, legLengthA, legLengthB) {
    legLengthB=legLengthB-2.5;

    translate([-bendRadius,0,0]) rotate([0,0,-angle]) {
        rotate_extrude(angle=angle,convexity=10) {
            translate([bendRadius,0,0]) {
                circle(d=diameter);
            }
        }
        rotate([180+(90-angle)+0.05,90,0]) translate([0,-bendRadius,0]) cylinder(d=diameter,h=legLengthA);
        translate([bendRadius,0.05,0]) rotate([90,0,0]) cylinder(d=diameter,h=legLengthB);
    }
}

module maleSide(angle, outerDiameter, wall, bendRadius, length) {
    difference() {
        corner(outerDiameter,angle,bendRadius,10,length+10);
        corner(outerDiameter-(wall*2),angle,bendRadius,10+1,length+10+1);
    }
}

module femaleSide(wallThickness, innerDiameter, maleSideOuterDiameter, depth) {
    module flare() {
        difference() {
            cylinder(d2=innerDiameter+(wallThickness*2),d1=maleSideOuterDiameter,h=10);
            translate([0,0,-0.001]) cylinder(d2=innerDiameter,d1=maleSideOuterDiameter-(wallThickness*2),h=10+0.02);
        }
    }
    
    module engagement() {
        difference() {
            cylinder(d=innerDiameter+(wallThickness*2), h=depth);
            translate([0,0,-0.001]) cylinder(d=innerDiameter, h=depth+0.002);
        }
    }
    
    flare();
    translate([0,0,10]) engagement();
}

module coupler(maleSideOuterDiameter, maleSideLength, femaleSideInnerDiameter, femaleSideDepth, bendAngle, wallThickness, bendRadius) {
    maleSide(bendAngle,maleSideOuterDiameter,wallThickness, bendRadius, maleSideLength);
    translate([0,10,0]) rotate([-90,90,0]) femaleSide(wallThickness, femaleSideInnerDiameter, maleSideOuterDiameter, femaleSideDepth);
    
    %translate([0,80,0]) rotate([90,0,0]) cylinder(d=femaleSideInnerDiameter,h=40);
}


coupler(
    24.6, // male side OD
    25,   // male length
    25,   // female side ID
    20,   // female side depth
    90,   // angle
    2,    // wall thickness
    48,   // bend radius
);
