$fn=50;

$armWidth=6.1;
$armLength=49;
$armHeight=13;
$screwDiameter=1.8;
$screwInnerDistance=11;
$screwOuterDistance=47;

$batteryHeight=18.5;
$batteryWidth=12.5;
$batteryHeightPadding=1.5;
$batteryHeightTotal=$batteryHeight+$batteryHeightPadding;

%translate([2.55,-25,0]) cube([0.5,0.5,0.5]);
%translate([0,-35,0]) cube([$armWidth,0.5,0.5], center=true);

module arm() {

    module armBody() {
        translate([0,-$armLength+1.5,0]){
            hull() {
                cylinder(h=3, d=3);
                
                translate([0,10,0]) {
                    cylinder(h=$armHeight, d=$armWidth);
                    cylinder(h=1, d=$armWidth);
                }
                
                translate([0,$armLength-(1.5+($armWidth/2))-14,0]) {
                    cylinder(h=$armHeight, d=$armWidth);
                    cylinder(h=1, d=$armWidth);
                }
            }
        }
    }
    
    module armShell() {
        difference() {
            armBody();
            
            translate([0,-$armLength+1.5,-1]){
                hull() {
                    
                    translate([0,3,0]) {
                        cylinder(h=3, d=2);
                    }
                    
                    translate([0,10,0]) {
                        cylinder(h=$armHeight, d=$armWidth-1.1);
                        cylinder(h=1, d=$armWidth-1.5);
                    }
                    
                    translate([0,$armLength-(1.5+($armWidth/2))-14,0]) {
                        cylinder(h=$armHeight, d=$armWidth-1.1);
                        cylinder(h=1, d=$armWidth-1.5);
                    }
                }
            }
        }
    }
    
    module armScrewsBlocks() {
        translate([0,-$screwInnerDistance,0]) {
            cylinder(d1=$armWidth,h=4, d2=5);
        }
    }
    
    module batteryPlate() {
//        hull() {
//            #translate([0,-14,0]) cylinder(d=4,h=3);
//            #translate([14,0,0]) cube([1,1,3]);
//            translate([-14,0,0]) cube([1,1,3]);
//        }
        difference() {
//            intersection() {
//                translate([0,-19.8,0]) rotate([0,0,45]) cube([14,14,3]);
//                translate([-18/2,-20,0]) cube([18,20,3]);                
//            }
            union() {
                translate([0,-13,0]) rotate([0,0,45]) cube([9.5,9.5,3]);
                translate([0,-11,3/2]) cube([$armWidth,15,3], center=true);
                hull() {
                    translate([0,-15.5,2]) cylinder(d=$armWidth,h=1);
                    translate([0,-18,4]) cylinder(d=$armWidth,h=1);
                }
            }
            hull() {
                translate([0,-17,-0.5]) cylinder(d=5,h=8);
                translate([0,-20,-0.5]) cylinder(d=5,h=8);
            }
        }
    }
    
    difference() {
        union() {
            armShell();
//            #armScrewsBlocks();
            batteryPlate();
        }
    }
}

module armScrewsHoles() {

    module armScrewHole() {
        translate([0,-$screwOuterDistance,0]) {
            cylinder(h=3.5, d=$screwDiameter);
        }
        translate([0,-$screwInnerDistance,0]) {
            cylinder(h=4, d=$screwDiameter);
        }
    }
    
    armScrewHole();
    rotate([0,0,90]) armScrewHole();
    rotate([0,0,180]) armScrewHole();
    rotate([0,0,270]) armScrewHole();
}

module arms() {
    arm();
    rotate([0,0,90]) arm();
//    rotate([0,0,180]) arm();
//    rotate([0,0,270]) arm();
}

module batteryHolderCover() {
    rotate([0,0,45]) {
        translate([0,0,($batteryHeightTotal+1)/2]) {
            cube([$batteryWidth+2.5,25,$batteryHeightTotal+1], center=true);
        }
    }
}

module batteryHolderCoverSplit() {
    rotate([0,0,45]) {
        translate([0,6,($batteryHeightTotal+1)/2]) {
            cube([$batteryWidth+1.5,8,$batteryHeightTotal+1], center=true);
        }
        translate([0,-6,($batteryHeightTotal+1)/2]) {
            cube([$batteryWidth+1.5,8,$batteryHeightTotal+1], center=true);
        }
    }
}

module batteryHolderHollow() {
    rotate([0,0,45]) {
        translate([0,0,($batteryHeightTotal)/2]) {
            cube([$batteryWidth,25+1,$batteryHeightTotal], center=true);
        }
    }
}

module batteryHolderFillet() {
    rotate([0,0,45]) {
        translate([$batteryWidth/2,0,$batteryHeightTotal]) {
            rotate([0,45,0]) {
                cube([1,25,1], center=true);
            }
        }
        translate([-$batteryWidth/2,0,$batteryHeightTotal]) {
            rotate([0,45,0]) {
                cube([1,25,1], center=true);
            }
        }
    }
}

difference() {
    union() {
        arms();
//        batteryHolderCover();
    }
//    batteryHolderHollow();
    armScrewsHoles();
    
    rotate([0,0,45]) translate([0,0,1.5/2]) cube([40,10,1.5], center=true);
    rotate([0,0,-45]) translate([0,0,1.5/2]) cube([40,10,1.5], center=true);
}

//batteryHolderFillet();

//%rotate([0,0,45]) translate([0,0,-1]) cube([28,28,3], center=true);
