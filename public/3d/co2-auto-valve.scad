$fn=80;

include <BOSL2/std.scad> // https://github.com/BelfrySCAD/BOSL2
include <BOSL2/gears.scad>
include <servos/servo library.scad> // http://github.com/kartchnb/openscad-servo-library


module knob() {
    rotate([0,90,0]) cylinder(d=16.85,h=11.78);
}

module tee() {
    translate([-2.5,0,0]) rotate([0,90,0]) cylinder(d=14.2,h=23.5);
}

module regulatorBody() {
    translate([0,0,19]) cylinder(d=31.5, h=25.2);
    cylinder(d=40, h=19);
}

module pressureGauge() {
    rotate([90,0,0]) {
        translate([0,0,4.5]) cylinder(d=25,h=11);
        cylinder(d=10.25,h=4.5);
    }
}

module bubbler() {
    translate([9.5,0,0]) {
        cylinder(d=13.5,h=(19.3-13.5)+6);
        translate([0,0,(19.3-13.5)+6]) cylinder(d=16,h=14.5);
        translate([0,0,(19.3-13.5)+20.5]) cylinder(d=20.3,h=56.7);
    }
}


module valve() {
    translate([21,0,0]) knob();
    tee();
    bubbler();
}


module servo(scaleFactor=1.0) {
    scale(scaleFactor) ServoLib_GenerateServo("MG90S (Clone)");
    
    // wire
    translate([5,-(12.5/2),-29]) cube([12,12.5,3]);
}

module largeGear() {
    intersection() {
        rotate([0,90,0]) spur_gear(mod=1, teeth=90, thickness=8, shaft_diam=120);
        rotate([0,90,0]) rotate([0,0,70]) translate([0,0,-10]) rotate_extrude(angle=45) square([50,20]);
    }
}

module lever() {
    module spar() {
        $length=50;
        translate([($length/2)+(16.8/2),0,8/2]) cube([$length,4,8], center=true);
    }
    
    
    difference() {
        union() {
            cylinder(d=16.8+8,h=8);
            
            rotate([0,0,33]) translate([-10,10.35,0]) spar();
            rotate([0,0,16]) translate([-10,-10.35,0]) spar();
        }
        cylinder(d=16.9,h=20);
    }
}

module littleGear() {
    translate([0,38.5,-0.5]) rotate([2,0,0]) rotate([0,90,0]) spur_gear(mod=1, teeth=10, thickness=8, shaft_diam=4.7);
}


module body() {
    hull() {
        translate([0,0,19]) cylinder(d=40, h=17.5);
        translate([16.5,14,19]) cube([20,32.5,17.5]);
        translate([14,36.5,19]) cylinder(d=20,h=17.5);
        translate([31.5,-15,19]) cylinder(d=10,h=17.5);
    }
}

module servoMount() {
    difference() {
        body();
        fullRegulator(1.02);
        translate([48 + 0.44, 36.5, 25]) rotate([90, 0, 0]) rotate([0, 90, 0]) servo(1.02);

        //servo mount holes
        translate([28, 44.65, 25]) rotate([0, 90, 0]) cylinder(d = 2.3, h = 10);
        translate([28, 44.6 - 28.86, 25]) rotate([0, 90, 0]) cylinder(d = 2.3, h = 10);
    }
}

module fullRegulator(scaleFactor=1) {
    scale([scaleFactor,scaleFactor,1]) regulatorBody();
    translate([22,0,10.5]) scale([scaleFactor,scaleFactor,1]) valve();
    translate([0,-20,10.5]) scale([scaleFactor,scaleFactor,1]) pressureGauge();
}

module knobLeverAndGear() {
    translate([21,0,10.5]) rotate([-72,0,0]) {
        translate([23,0,0]) rotate([171,0,0]) rotate([0,90,0]) lever();

        rotate([103,0,0]) translate([27,0,0]) {
            largeGear();
            translate([0,0,-5.8]) littleGear();
        }
    }
}

rotate([0,0,-45]) {
    %fullRegulator();
    %translate([48,36.25,25]) rotate([90,0,0]) rotate([0,90,0]) servo();

    servoMount();
    knobLeverAndGear();
}

//Part: servoMount();
//Part: knobLeverAndGear();
