$fn=110;
primaryOD=110;
secondardyOD=70;

module strap() {
    maxD=primaryOD+2;
    minD=primaryOD-2;
    module ring() {
        cylinder(d=primaryOD+6,h=25);
    }
    
    module boltHolders() {
        hull() {
            translate([18,(primaryOD-6)/2,25/2]) cube([2,2,10], center=true);
            translate([0,(primaryOD+2)/2,25/2]) cube([20,2,25], center=true);
            translate([0,(primaryOD+6)/2,25/2]) cube([20,25,15], center=true);
        }
        
        hull() {
            translate([18,-(primaryOD-6)/2,25/2]) cube([2,2,10], center=true);
            translate([0,-(primaryOD+2)/2,25/2]) cube([20,2,25], center=true);
            translate([0,-(primaryOD+6)/2,25/2]) cube([20,25,15], center=true);
        }
    }
    
    
    difference() {
        union() {
            ring();
            boltHolders();
        }
        //Splits
        translate([0,maxD/2,12]) cube([((maxD*PI)-(minD*PI))/2,50,30], center=true);
        translate([0,-maxD/2,12]) cube([((maxD*PI)-(minD*PI))/2,50,30], center=true);
        
        //Captive Nuts
        translate([43,(primaryOD/2)+8.5,25/2]) rotate([0,-90,0]) nut();
        translate([43,-((primaryOD/2)+8.5),25/2]) rotate([0,-90,0]) nut();
    }
}

module port() {
    cylinder(d=secondardyOD+4,h=25);
}

module flange() {
    intersection() {
        difference() {
            translate([(primaryOD-60)/2,0,0]) rotate([0,90,0]) cylinder(d=secondardyOD+30,h=60);
            translate([0,0,-(secondardyOD+50)/2]) cylinder(d=primaryOD,h=secondardyOD+50);
        }
        translate([0,0,-(secondardyOD+50)/2]) cylinder(d=primaryOD+6,h=secondardyOD+50);
    }
}

module nut() {
    cylinder($fn=6,d=11.5,h=4.5+30);
    translate([0,0,30]) cylinder(d=7,h=40);
}

intersection() {
    difference() {
        union() {
            translate([0,0,-25/2]) strap();
            translate([0,0,0]) flange();
            translate([45,0,0]) rotate([0,90,0]) port();
            translate([(primaryOD/2)-11,(primaryOD/2)-15]) rotate([0,0,-20]) cube([50,10,4], center=true);
            translate([(primaryOD/2)-11,-((primaryOD/2)-15)]) rotate([0,0,20]) cube([50,10,4], center=true);
        }

        translate([0,0,-100]) cylinder(d=primaryOD,h=200);
        rotate([0,90,0]) translate([0,0,-1])cylinder(d=secondardyOD,h=100);
    }
//    #translate([0,-100,-50]) cube([100,200,100]);
}