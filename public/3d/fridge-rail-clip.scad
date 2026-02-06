$fn=70;

module angledBottom() {
    hull() {
        translate([0,0,-1.5/2]) {
            cube([30,0.01,1.5], center=true);
        }

        translate([0,10,-3.5/2]) {
            cube([22.5,0.01,3.5], center=true);
        }

        translate([0,10,-1.5/2]) {
            cube([30,0.01,1.5], center=true);
        }
    }
}

module curvedInterface() {
    difference() {
        translate([0,5,4.5/2]) {
            cube([30,10,4.5], center=true);
        }
        
        hull() {
            translate([-40/2,6.5-3.5,3.5]) {
                rotate([0,90,0]) {
                    cylinder(r=3.5, h=40);
                }
            }
            
            translate([0,-10,3.5]) {
                cube([40,10,7], center=true);
            }
            
            translate([0,1.5,10]) {
                cube([40,10,7], center=true);
            }
        }
    }
}

module  clip() {
    hull() {
        translate([0,6.5,20.5]) {
            rotate([0,90,90]) {
                cylinder(d=10, h=2);
            }
        }
        
        translate([0,6.5+(3.5/2),4-1]) {
            cube([18,3.5,1], center=true);
        }
    }
    
    module knub() {
        module shaft() {
            rotate([0,90,90]) {
                cylinder(d=5, h=3.5);
                translate([0,0,-2.5]) cylinder(d2=5, d1=2, h=2.5);
            }
        }
        
        module relief() {
            translate([0,-2,0]) cube([10,10,0.8], center=true);
        }
        
        translate([0,3,20.5]) {
            difference() {
                shaft();
                relief();
            }
        }
    }
    
    knub();
}

module rail() {
    module side() {
        translate([0,0,-0.5]) {
            translate([0,20,1.5]) {
                cube([6.6,20,2], center=true);
            }
            translate([-1,10,-3/2]) {
                cube([2.2,16,5.25]);
            }
        }
    }
    
    module centre() {
        translate([0,20,1]) {
            cube([9.8,20,2], center=true);
        }
    }
    
    translate([10,0,0]) side();
    centre();
    translate([-10,0,0]) side();
}

rail();

//%translate([0,0,-3.5]) cube([20,20,3.5]);


angledBottom();
curvedInterface();
clip();