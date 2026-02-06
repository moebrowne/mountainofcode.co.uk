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

module arm() {

    module armBody() {
        translate([0,-$armLength+1.5,0]){
            hull() {
                cylinder(h=3, d=3);
                
                translate([0,10,0]) {
                    cylinder(h=$armHeight, d1=$armWidth, d2=5.5);
                    cylinder(h=1, d=$armWidth);
                }
                
                translate([0,$armLength-(1.5+($armWidth/2))-3,0]) {
                    cylinder(h=$armHeight, d1=$armWidth, d2=5.5);
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
                        cylinder(h=$armHeight, d1=$armWidth-1.5, d2=4.25);
                        cylinder(h=1, d=$armWidth-1.5);
                    }
                    
                    translate([0,$armLength-(1.5+($armWidth/2))-3,0]) {
                        cylinder(h=$armHeight, d1=$armWidth-1.5, d2=4.25);
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
    
    difference() {
        union() {
            armShell();
            armScrewsBlocks();
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
    
    #armScrewHole();
    rotate([0,0,90]) armScrewHole();
    rotate([0,0,180]) armScrewHole();
    rotate([0,0,270]) armScrewHole();
}

module arms() {
    arm();
    rotate([0,0,90]) arm();
    rotate([0,0,180]) arm();
    rotate([0,0,270]) arm();
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
        batteryHolderCover();
    }
    batteryHolderHollow();
    armScrewsHoles();
}

batteryHolderFillet();
