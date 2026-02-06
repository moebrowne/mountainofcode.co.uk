$fn=80;

heatSinkWidth=90.2;
heatSinkHeight=40.2;
heatSinkLength=80;

fanWidth=60.7;
fanHeight=38;

module heatsink() {
    cube([heatSinkLength,heatSinkWidth,heatSinkHeight], center=true);
}

module fan() {
    cube([fanWidth,fanWidth,fanHeight], center=true);
}

%translate([0,0,(heatSinkLength/2)+fanHeight+heatSinkHeight]) rotate([0,90,0]) heatsink();
%translate([(fanWidth/2)-(heatSinkHeight/2),0,fanHeight/2])  fan();


module duct() {
    wallWidth=1;
    
    module right() {
        hull() {
            translate([(wallWidth/2),(heatSinkWidth/2)+(wallWidth/2),fanHeight+heatSinkHeight]) rotate([0,90,0]) cube([0.001,wallWidth,heatSinkHeight+wallWidth], center=true);
            
            translate([(fanWidth/2)-(heatSinkHeight/2)+(wallWidth/2),(fanWidth/2)+(wallWidth/2),fanHeight]) cube([fanWidth+wallWidth,wallWidth,0.001], center=true);
        }
    }
    
    module left() {
        hull() {
            translate([(wallWidth/2),(-heatSinkWidth/2)-(wallWidth/2),fanHeight+heatSinkHeight]) rotate([0,90,0]) cube([0.001,wallWidth,heatSinkHeight+wallWidth], center=true);
            
            translate([(fanWidth/2)-(heatSinkHeight/2)+(wallWidth/2),-(fanWidth/2)-(wallWidth/2),fanHeight]) cube([fanWidth+wallWidth,wallWidth,0.001], center=true);
        }
    }
    
    module front() {
        hull() {
            translate([(heatSinkHeight/2)+(wallWidth/2),0,fanHeight+heatSinkHeight]) rotate([0,90,0]) cube([0.001,heatSinkWidth+(wallWidth*2),wallWidth], center=true);
            
            translate([(fanWidth/2)-(heatSinkHeight/2)+(fanWidth/2)+(wallWidth/2),0,fanHeight]) cube([wallWidth,fanWidth+wallWidth,0.001], center=true);
        }
    }
    
    right();
    left();
    front();
}

module fanMountPlate() {
    module base() {
        hull() {
            cube([fanWidth,fanWidth,0.001], center=true);
            translate([0,0,2]) cube([fanWidth,fanWidth+2,0.001], center=true);
        }
    }
    
    translate([(fanWidth/2)-(heatSinkHeight/2),0,1]) difference() {
        base();
        translate([0,0,-5]) cylinder(d=60,h=10); // fan hole
        
        translate([25,25,-4.5]) cylinder(d=4,h=10);
        translate([25,-25,-4.5]) cylinder(d=4,h=10);
        translate([-25,25,-4.5]) cylinder(d=4,h=10);
        translate([-25,-25,-4.5]) cylinder(d=4,h=10);
    }
}

module back() {
    difference() {
        translate([-(heatSinkHeight/2)+(1/2),0,(40.2/2)+fanHeight]) cube([1,heatSinkWidth+(1*2),40.2], center=true);
    
        translate([-(heatSinkHeight/2)+(1/2),0,(40.2/2)+fanHeight]) translate([-5,-40,-15]) rotate([0,90,0]) cylinder(d=3,h=10);
        translate([-(heatSinkHeight/2)+(1/2),0,(40.2/2)+fanHeight]) translate([-5,40,-15]) rotate([0,90,0]) cylinder(d=3,h=10);
    }
}

back();

translate([0,0,37]) fanMountPlate();
duct();

