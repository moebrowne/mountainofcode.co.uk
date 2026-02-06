$fn=80;

//cube([17,17,4], center=true);

//translate([0,0,0]) cube([5.75,5.75,8.2], center=true);
//
//translate([0,4.5/2,6/2]) {
//    rotate([90,0,0]) {
//        difference() {
//            cylinder(d=8,h=4.5);
//            cylinder(d=6.5,h=10);
//        }
//    }
//}

module bulkHead() {
    translate([0,1.25,5]) rotate([90,0,0]) {
        translate([0,0,-8]) cylinder(d=9.5,h=8,$fn=6);
        
        cylinder(d=9.5,h=1.9,$fn=6);
        translate([0,0,1.9]) cylinder(d=6.25,h=11);
        
        translate([0,0,5.8]) {
            cylinder(d=7.75,h=11.35);
            translate([0,0,2.65]) cylinder(d=9,h=5.8,$fn=6);
        }
    }
}

module basePlate() {
    translate([0,0,10/2]) cube([11,5.75,10], center=true);
}

module bottomPeg() {
    translate([0,0,-4.2/2]) cube([5.75,5.75,4.2], center=true);
}

module strapSpace() {
//    cylinder(d=17, h=2.75+1);
    translate([0,-1,0]) rotate([-45,0,90]) rotate([0,90,0]) {
        intersection () {
            minkowski() {
                size=8;
                cube([size-(size/2),size-(size/2),5.75], center=true);
                sphere(d=size+3);
            }
            cube([170,170,5.75], center=true);
        }
    }
}

module strapSpace2() {

    module rim() {
        difference() {
            scale([1,1,1.2]) translate([0,2,0]) rotate([90,0,0]) cylinder(d=18,h=5.75);
            scale([1,1,1.2]) translate([0,1.5,0]) rotate([90,0,0]) cylinder(d=20,h=4.75);
        }
    }
    
    module body() {
        hull() {
            scale([1,1,1.2]) translate([0,2,0]) rotate([90,0,0]) cylinder(d=17,h=5.75);
            translate([0,4,0]) cube([17,9,1], center=true);
        }
    }
    
    difference() {
        union() {
            body();
            rim();
        }
        translate([0,0,-50/2]) cube([50,50,50], center=true);
    }
}


difference() {
    strapSpace2();
    //basePlate();
    bulkHead();
}
bottomPeg();

%translate([0,0,-1/2]) cube([17,17,1], center=true);
%bulkHead();