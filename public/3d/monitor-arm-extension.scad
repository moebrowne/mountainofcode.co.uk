$fn=80;
length=60;
width=20;
height=33;

boltDiameter=9;

module boltHoles() {
    module boltHole() {
        translate([0,0,-(height+10)/2]) {
            cylinder(d=boltDiameter,h=height+10);
        }
    }

    translate([(length/2),0,0]) {
        boltHole();
    }
    translate([-(length/2),0,0]) {
        boltHole();
    }
}

module cornerRounder() {
    intersection() {
        cube([width,width,height], center=true);
        translate([width/2,0,-((height)/2)]) cylinder(d=width, h=height+10);
    }
}

difference() {
    union() {
        cube([length,width, height], center=true);
        translate([-((length/2)+width/2),0,0]) cornerRounder();
        rotate([0,0,180]) translate([-((length/2)+width/2),0,0]) cornerRounder();
    }
    boltHoles();
}
