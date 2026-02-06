$fn=80;

module cooler() {
    difference() {
        union() {
            translate([0,0,4.5/2]) {
                cube([60,45,4.5], center=true);
            
                translate([0,0,(3.1/2)+(4.5/2)]) {
                    cube([42.2,45,3.1], center=true);
                    translate([0,0,7.8/2]) cube([40,41.5,7.8], center=true);
                }
            }
            //wires
            translate([-18,30,(7+4.5)/2]) cube([4,25,(7+4.5)], center=true);
            translate([18,30,(7+4.5)/2]) cube([4,25,(7+4.5)], center=true);
            
            // junction jumper
            translate([0,20,(7+4.5)/2]) cube([12,10,(7+4.5)], center=true);
            
            // heat pipes
            translate([0,24,(2+4.5)/2]) cube([40,60,(2+4.5)], center=true);
            translate([0,-24,(2+4.5)/2]) cube([40,60,(2+4.5)], center=true);
        }
        translate([24,35/2,-1]) cylinder(d=3.5,h=100);
        translate([-24,35/2,-1]) cylinder(d=3.5,h=100);
        translate([-24,-35/2,-1]) cylinder(d=3.5,h=100);
        translate([24,-35/2,-1]) cylinder(d=3.5,h=100);
    }
}

//%cooler();

difference() {
    translate([0,0,4.6]) cylinder(d=75,h=9.3);
    scale(1.02) cooler();
    
    translate([24,35/2,-1]) cylinder(d=3.5,h=100);
    translate([-24,35/2,-1]) cylinder(d=3.5,h=100);
    translate([-24,-35/2,-1]) cylinder(d=3.5,h=100);
    translate([24,-35/2,-1]) cylinder(d=3.5,h=100);
}

