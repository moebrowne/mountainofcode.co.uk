$fn=80;

module pcTop() {
    cube([370,86,2]);
    
    translate([7,13.5,2]) cylinder(d=3.5,h=4);
    translate([7,13.5+60,2]) cylinder(d=3.5,h=4);
    
    translate([370-31,13.5,2]) cylinder(d=3.5,h=4);
    translate([370-31,13.5+60,2]) cylinder(d=3.5,h=4);
        
}

module tube() {
    translate([0,86/2,22+(25/2)]) rotate([0,90,0]) cylinder(d=25.1,h=345);
}

module handle() {
    module body() {
        hull() {
            cube([14,86,0.01]);
            translate([0,86/2,20]) rotate([0,90,0]) scale([1,0.8,1]) cylinder(d=55,h=14);
        }
    }
    
    module bodyFrame() {  
        cube([14,86,3]);
        
        translate([0,0,3]) difference() {
            body();
            translate([-0.01,10,5.5]) scale([1.01,0.76,0.76]) body();
        }
    }
    
    difference() {
        union() {
            bodyFrame();
            translate([0,86/2,22+(25/2)]) rotate([0,90,0]) cylinder(d=25+6,h=14);
        }
        
        translate([-0.001,0,0]) tube();
        
        translate([-10,-10,-10]) cube([80,80,10]); // flatten the bottom
        
        hull() translate([7,13.5,0]) cleat();
        hull() translate([7,13.5+60,0]) cleat();
    }
}

module cleat() {
    module cleatBody() {
        hull() {
            cube([14,6,0.001], center=true);
            translate([0,0,4]) cube([14,14,0.001], center=true);
        }
    }
    
    difference() {
        cleatBody();
        translate([0,0,-1.45]) screwClearance();
    }
}


module screwClearance() {
    cylinder(d=3.5+0.1,h=3.7);
    translate([0,0,3.7]) cylinder(d=7+0.1,h=1.6+0.2);
}

handle();
//cleat();

%tube();
%translate([0,0,-2]) pcTop();
%translate([332,0,0]) handle();

//part: handle();
//part: cleat();