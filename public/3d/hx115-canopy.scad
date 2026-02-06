$fn=80;

module babyTurtle() {
    module body() {
        hull() {
            cube([14,14,0.01], center=true);
            translate([0,0,9.5]) cube([12.5,12.5,0.01], center=true);
        }
        translate([0,0,9.5]) cylinder(d=10.5,h=9);
    }
    
    module mountHole() {
        translate([-14/2,0,6.5]) rotate([0,90,0]) {
            difference() {
                cylinder(d=5,h=14);
                translate([0,0,-0.5]) cylinder(d=2,h=15);
            }
            %translate([0,0,-5.5]) cylinder(d=2,h=25);
        }
    }

    translate([0,0,-6.5]) {
        body();
        mountHole();
    }
}

module vtx() {
    module body() {
        hull() {
            cube([3.3,29,1], center=true);
            translate([-16.5,0,0]) cube([0.01,8.2,1], center=true);
        }
    }
    
    module mountHoles() {
        translate([0.75-(2.5/2),(29/2)-1-(2.5/2),-2]) cylinder(d=2.5,h=4);
        translate([0.75-(2.5/2),-((29/2)-1-(2.5/2)),-2]) cylinder(d=2.5,h=4);
    }
    
    module connector() {
        translate([-4,5.5,2]) rotate([0,0,-55]) cube([4.5,7.5,3.1], center=true);
    }
    
    module antenna() {
        translate([-22.5,0,0]) rotate([0,90,0]) cylinder(d=4.3,h=6);
    }
    
    difference() {
        body();
        mountHoles();
    }
    
    connector();
    antenna();
}


module propClearnce() {
    rotate([0,0,45]) translate([115/2,0,0]) cylinder(d=72,h=6);
    rotate([0,0,45+90]) translate([115/2,0,0]) cylinder(d=72,h=6);
    rotate([0,0,45+180]) translate([115/2,0,0]) cylinder(d=72,h=6);
    rotate([0,0,45+270]) translate([115/2,0,0]) cylinder(d=72,h=6);
}


module frameMountHoles() {
    translate([47.8/2,0,0]) cylinder(d=2,h=1);
    translate([-47.8/2,0,0]) cylinder(d=2,h=1);
    translate([0,47.8/2,0]) cylinder(d=2,h=1);
    translate([0,-47.8/2,0]) cylinder(d=2,h=1);
}


module cameraClearance() {
    translate([0,-10,10]) {
        hull () {
            rotate([90-15,0,0]) babyTurtle();
            rotate([90-25,0,0]) babyTurtle();
            rotate([90-35,0,0]) babyTurtle();
            rotate([90-55,0,0]) babyTurtle();
            rotate([90-65,0,0]) babyTurtle();
        }
        
        %rotate([90-30,0,0]) babyTurtle();
    }
}


module canopy() {
    module cameraShroud() {
        hull() {
            translate([0,-47.8/2,-1]) cylinder(d=4,h=3);
            translate([0,-6,27]) cube([8,5,5], center=true);
            translate([0,-14,20]) cube([20,5,5], center=true);
            translate([0,-10,15]) cube([23,5,5], center=true);
            translate([0,-12,0]) cube([22,1,2], center=true);
            translate([0,-1,14]) cube([19,5,2], center=true);
        }
    }
    
    module cameraShrouldHollow() {
        hull() {
            translate([0,(-47.8/2)+2,-2]) cylinder(d=4,h=3);
            translate([0,-4,25]) cube([6,5,5], center=true);
            translate([0,-12,20]) cube([18,5,3], center=true);
            translate([0,-10,15]) cube([20,5,5], center=true);
            translate([0,-12,0]) cube([20,3,2], center=true);
            translate([0,-1,14]) cube([17,5,2], center=true);
        }
    }
    
    module rear() {
        hull() {
            translate([0,-6,27]) cube([8,5,5], center=true);
            translate([0,11,20]) cube([4,5,5], center=true);
            translate([0,25,-0.5]) cube([1,1,1], center=true);
            translate([0,-1,14]) cube([19,5,2], center=true);
        }
    }
    
    module rearHollow() {
        hull() {
            translate([0,-8,25]) cube([8,5,5], center=true);
            translate([0,9,18]) cube([4,5,5], center=true);
            translate([0,23,-0.5]) cube([1,1,1], center=true);
            translate([0,-3,14]) cube([17,5,2], center=true);
        }
    }
    
    module underside() {
        hull() {
            translate([0,0,0]) cube([48,5,2], center=true);
              translate([47.8/2,0,-1]) cylinder(d=4,h=1);
              translate([-47.8/2,0,-1]) cylinder(d=4,h=1);
            translate([0,25,-0.5]) cube([1,1,1], center=true);
            translate([0,-10,0]) cube([22,5,2], center=true);
            translate([0,-10,12]) cube([22,5,1], center=true);
            translate([0,-1,14]) cube([19,5,2], center=true);
        }
    }
        
    module underSideHollow() {
        hull() {
            translate([0,0,-1]) cube([44,4,2], center=true);
            translate([0,24,-1]) cube([1,1,1], center=true);
            translate([0,-8,-1]) cube([22,5,2], center=true);
            translate([0,-8,10]) cube([22,5,1], center=true);
            translate([0,-1,12]) cube([18,5,2], center=true);
        }
    }

    module hollow() {
        cameraShrouldHollow();
        rearHollow();
        underSideHollow();
    }
    
    difference() {
        union() {
            cameraShroud();
            rear();
            underside();
        }
        translate([0,0,5]) cameraClearance();
        hollow();
    }
}

    
module stackCover() {
    module frame() {
        difference() {
            linear_extrude(height=9) projection(cut=true) canopy();
            stack();
        }
    }
    
    module standoffs() {
        module standoff() {
            cylinder(d=4,h=11);
        }
        translate([47.8/2,0,0]) standoff();
        translate([-47.8/2,0,0]) standoff();
        translate([0,-47.8/2,0]) standoff();
    }
    
    module standoffHoles() {
        module standoffHole() {
            translate([0,0,-0.5]) cylinder(d=2,h=12);
        }
        translate([47.8/2,0,0]) standoffHole();
        translate([-47.8/2,0,0]) standoffHole();
        translate([0,-47.8/2,0]) standoffHole();
    }
    
    difference() {
        union() {
            frame();
            translate([0,0,-2]) standoffs();
        }
        standoffHoles();
    }
}


//%translate([0,0,-9]) stackCover();

canopy();

module stack() {
    translate([0,0,11/2]) rotate([0,0,45]) cube([32,32,11], center=true);
}

//%translate([0,0,-12]) stack();
//%translate([0,0,-12]) frameMountHoles();


//%translate([0,8,1]) rotate([0,90,0]) vtx();
//%translate([0,-3,1]) rotate([-40,0,0]) rotate([-90,90,0]) vtx();

//%translate([0,0,5]) cameraClearance();
//%propClearnce();