$fn=70;

module holder() {
    
    module bottomRim() {
        difference() {
            cylinder(d=36.75, h=3);
            cylinder(d=34.5, h=3);
        }
    }

    module topRim() {
        translate([0,0,(59.5+3)]) {
            difference() {
                cylinder(d=34, h=1.5);
                translate([0,0,-0.5]) cylinder(d=32, h=2.5);
            }
        }
    }
    
    module topHole() {
        translate([0,0,59.5]) {
            cylinder(d=13.6, h=10);
        }
    }
    
    module detentRing() {
        detentCount = 19;
        
        module detents() {
            for(detentNum = [1:detentCount]) {
                rotate([0,0,(360/detentCount)*detentNum]) {
                    translate([(23.6/2),0,-0.75]) {
                        sphere(d=2.5);
                    }
                }
            }
        }
        
        module ring() {
            translate([0,0,1.4]) {
                rotate_extrude(convexity = 10) {
                    translate([(23.6/2), 0, 0]) {
                        circle(d = 3.5);
                    }
                }
            }
        }
        
        translate([0,0,(59.5+3)]) {
            detents();
            ring();
        }
    }
    
    module centerHole() {
        translate([0,0,-1]) {
            cylinder(d=34.5, h=61.25);
        }
    }
    
    module mainBody() {
        translate([0,0,3]) {
            hull() {
                cylinder(d=40, h=59.5);
                translate([12,0,30/2]) {
                    cylinder(d=25, h=59.5-30);
                }
            }
        }
        bottomRim();
    }

    module notch() {
        translate([(34.5/2)-0.75,0,(60/2)]) {
            difference() {
                translate([1/2,0,0]) {
                    cube([1.75,6.2,60], center=true);
                }
                cube([0.8,3.3,(60)], center=true);
            }
        }
    }
    
    module cutout() {
        hull() {
            translate([0,45/2,32]) {
                rotate([90,0,0]) {
                    cylinder(d=28.8, h=45);
                }
            }
            translate([-25,45/2,28.5]) {
                rotate([90,0,0]) {
                    cylinder(d=28, h=45);
                }
            }
        }
    }
    
    difference() {
        mainBody();
        centerHole();
        topHole();
        detentRing();
        cutout();
    }
    
    notch();
    topRim();
}

holder();
